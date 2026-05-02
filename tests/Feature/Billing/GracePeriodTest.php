<?php

namespace Tests\Feature\Billing;

use App\Models\Family;
use App\Models\User;
use App\Models\VaultCategory;
use App\Models\VaultEntry;
use App\Notifications\Billing\PaymentRetryNotification;
use App\Notifications\Billing\SubscriptionDowngradedNotification;
use App\Services\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Mockery;
use Tests\TestCase;

class GracePeriodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('kinhold.billing_enabled', true);
    }

    private function familyWithOwner(): array
    {
        $family = Family::factory()->create();
        $owner = User::factory()->parent()->create(['family_id' => $family->id]);
        $family->update(['billing_owner_id' => $owner->id]);

        return [$family->fresh(), $owner];
    }

    public function test_day_3_sends_retry_email_and_marks_as_sent(): void
    {
        Notification::fake();
        [$family, $owner] = $this->familyWithOwner();
        $family->forceFill(['payment_failed_at' => Carbon::now()->subDays(3)])->save();

        $this->artisan('kinhold:enforce-grace-period')->assertSuccessful();

        Notification::assertSentTo($owner, PaymentRetryNotification::class);
        $this->assertNotEmpty($family->fresh()->settings['grace_day_3_sent_at'] ?? null);
    }

    public function test_day_3_does_not_resend_when_already_marked(): void
    {
        Notification::fake();
        [$family, $owner] = $this->familyWithOwner();
        $family->forceFill([
            'payment_failed_at' => Carbon::now()->subDays(4),
            'settings' => ['grace_day_3_sent_at' => Carbon::now()->subDay()->toIso8601String()],
        ])->save();

        $this->artisan('kinhold:enforce-grace-period')->assertSuccessful();

        Notification::assertNotSentTo($owner, PaymentRetryNotification::class);
    }

    public function test_day_7_downgrades_and_notifies(): void
    {
        Notification::fake();
        [$family, $owner] = $this->familyWithOwner();
        $family->forceFill([
            'payment_failed_at' => Carbon::now()->subDays(7),
            'settings' => [
                'chatbot' => ['plan' => 'standard'],
                'ai_mode' => 'kinhold',
            ],
        ])->save();

        // Stub BillingService::selectAiTier so we don't hit Stripe.
        $mock = Mockery::mock(BillingService::class)->makePartial();
        $mock->shouldReceive('selectAiTier')->once()->with(Mockery::on(fn ($f) => $f->id === $family->id), 'off');
        $this->app->instance(BillingService::class, $mock);

        $this->artisan('kinhold:enforce-grace-period')->assertSuccessful();

        $fresh = $family->fresh();
        $this->assertNotEmpty($fresh->settings['downgraded_at'] ?? null);
        $this->assertSame('standard', $fresh->settings['ai_plan_before_downgrade'] ?? null);
        $this->assertTrue((bool) ($fresh->settings['storage_soft_capped'] ?? false));
        Notification::assertSentTo($owner, SubscriptionDowngradedNotification::class);
    }

    public function test_day_7_with_no_prior_ai_tier_still_marks_downgraded(): void
    {
        Notification::fake();
        [$family, $owner] = $this->familyWithOwner();
        $family->forceFill([
            'payment_failed_at' => Carbon::now()->subDays(8),
        ])->save();

        // No AI tier active — selectAiTier should NOT be called.
        $mock = Mockery::mock(BillingService::class)->makePartial();
        $mock->shouldNotReceive('selectAiTier');
        $this->app->instance(BillingService::class, $mock);

        $this->artisan('kinhold:enforce-grace-period')->assertSuccessful();

        $fresh = $family->fresh();
        $this->assertNotEmpty($fresh->settings['downgraded_at'] ?? null);
        $this->assertTrue((bool) ($fresh->settings['storage_soft_capped'] ?? false));
        Notification::assertSentTo($owner, SubscriptionDowngradedNotification::class);
    }

    public function test_skips_families_with_no_payment_failed_at(): void
    {
        Notification::fake();
        [, $owner] = $this->familyWithOwner();

        $this->artisan('kinhold:enforce-grace-period')->assertSuccessful();

        Notification::assertNothingSentTo($owner);
    }

    public function test_running_twice_in_one_day_does_not_double_send(): void
    {
        Notification::fake();
        [, $owner] = $this->familyWithOwner();
        Family::query()->update(['payment_failed_at' => Carbon::now()->subDays(3)]);

        $this->artisan('kinhold:enforce-grace-period')->assertSuccessful();
        $this->artisan('kinhold:enforce-grace-period')->assertSuccessful();

        Notification::assertSentToTimes($owner, PaymentRetryNotification::class, 1);
    }

    public function test_soft_capped_family_cannot_upload_new_vault_documents(): void
    {
        Storage::fake('private');
        [$family, $owner] = $this->familyWithOwner();
        $family->forceFill([
            'settings' => ['storage_soft_capped' => true],
        ])->save();

        $owner->update(['family_id' => $family->id]);
        Sanctum::actingAs($owner);

        $category = VaultCategory::create([
            'family_id' => $family->id,
            'name' => 'Test',
            'slug' => 'soft-cap-test',
            'icon' => 'file',
        ]);
        $entry = VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $category->id,
            'created_by' => $owner->id,
            'title' => 'Test Entry',
            'encrypted_data' => encrypt(json_encode(['data' => 'test'])),
        ]);

        $pdf = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->postJson("/api/v1/vault/entries/{$entry->id}/documents", ['file' => $pdf]);

        $response->assertStatus(402);
        $response->assertJsonStructure(['message']);
    }

    public function test_uncapped_family_can_still_upload(): void
    {
        Storage::fake('private');
        [$family, $owner] = $this->familyWithOwner();
        $owner->update(['family_id' => $family->id]);
        Sanctum::actingAs($owner);

        $category = VaultCategory::create([
            'family_id' => $family->id,
            'name' => 'Test',
            'slug' => 'no-cap-test',
            'icon' => 'file',
        ]);
        $entry = VaultEntry::create([
            'family_id' => $family->id,
            'vault_category_id' => $category->id,
            'created_by' => $owner->id,
            'title' => 'Test Entry',
            'encrypted_data' => encrypt(json_encode(['data' => 'test'])),
        ]);

        $pdf = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->postJson("/api/v1/vault/entries/{$entry->id}/documents", ['file' => $pdf]);

        $this->assertNotEquals(402, $response->status());
    }

    public function test_in_grace_period_helper_reflects_payment_failed_at(): void
    {
        [$family] = $this->familyWithOwner();

        $this->assertFalse($family->inGracePeriod());

        $family->forceFill(['payment_failed_at' => Carbon::now()->subDays(2)])->save();
        $this->assertTrue($family->fresh()->inGracePeriod());
        $this->assertGreaterThanOrEqual(2, $family->fresh()->gracePeriodDaysElapsed());
    }
}
