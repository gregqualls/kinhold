<?php

namespace Tests\Feature;

use App\Models\Family;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class FamilyCreationLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_log_warning_fires_when_self_hosted_creates_second_family(): void
    {
        config()->set('kinhold.self_hosted', true);
        Family::factory()->create();

        Log::spy();
        $second = Family::factory()->create();

        Log::shouldHaveReceived('warning')
            ->once()
            ->withArgs(function (string $message, array $context) use ($second) {
                return str_contains($message, 'additional family')
                    && $context['family_id'] === $second->id
                    && $context['family_count'] === 2
                    && $context['commercial_license_acknowledged'] === false;
            });
    }

    public function test_log_warning_does_not_fire_for_first_family_when_self_hosted(): void
    {
        config()->set('kinhold.self_hosted', true);

        Log::spy();
        Family::factory()->create();

        Log::shouldNotHaveReceived('warning');
    }

    public function test_log_warning_does_not_fire_when_not_self_hosted(): void
    {
        config()->set('kinhold.self_hosted', false);
        Family::factory()->create();

        Log::spy();
        Family::factory()->create();

        Log::shouldNotHaveReceived('warning');
    }

    public function test_log_context_includes_acknowledged_flag(): void
    {
        config()->set('kinhold.self_hosted', true);
        config()->set('kinhold.commercial_license_acknowledged', true);
        Family::factory()->create();

        Log::spy();
        Family::factory()->create();

        Log::shouldHaveReceived('warning')
            ->once()
            ->withArgs(function (string $message, array $context) {
                return $context['commercial_license_acknowledged'] === true;
            });
    }
}
