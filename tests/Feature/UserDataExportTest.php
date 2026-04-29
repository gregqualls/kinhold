<?php

namespace Tests\Feature;

use App\Models\CalendarConnection;
use App\Models\Family;
use App\Models\Task;
use App\Models\User;
use App\Models\VaultCategory;
use App\Models\VaultEntry;
use App\Models\VaultPermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use ZipArchive;

class UserDataExportTest extends TestCase
{
    use RefreshDatabase;

    private Family $familyA;

    private Family $familyB;

    private User $parentA;

    private User $parentB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->familyA = Family::factory()->create();
        $this->familyB = Family::factory()->create();

        $this->parentA = User::factory()->parent()->create(['family_id' => $this->familyA->id]);
        $this->parentB = User::factory()->parent()->create(['family_id' => $this->familyB->id]);
    }

    public function test_unauthenticated_export_returns_401(): void
    {
        $response = $this->postJson('/api/v1/settings/account/data-export');

        $response->assertStatus(401);
    }

    public function test_export_returns_zip_with_expected_files(): void
    {
        Sanctum::actingAs($this->parentA);

        $response = $this->post('/api/v1/settings/account/data-export');

        $response->assertStatus(200);
        $this->assertSame('application/zip', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));

        $files = $this->extractZipFiles($response->getContent());

        $this->assertContains('manifest.json', $files);
        $this->assertContains('user.json', $files);
        $this->assertContains('tasks.json', $files);
        $this->assertContains('vault.json', $files);
        $this->assertContains('points.json', $files);
        $this->assertContains('badges.json', $files);
        $this->assertContains('chat.json', $files);
        $this->assertContains('calendar.json', $files);
        $this->assertContains('food.json', $files);
    }

    public function test_export_scoping_isolates_users(): void
    {
        $categoryA = VaultCategory::create([
            'family_id' => $this->familyA->id,
            'name' => 'Medical',
            'slug' => 'medical',
            'icon' => 'heart',
        ]);

        $entryA = VaultEntry::create([
            'family_id' => $this->familyA->id,
            'vault_category_id' => $categoryA->id,
            'created_by' => $this->parentA->id,
            'title' => 'Family A SSN',
            'encrypted_data' => encrypt(json_encode(['ssn' => 'family-a-secret'])),
        ]);

        Task::create([
            'family_id' => $this->familyA->id,
            'created_by' => $this->parentA->id,
            'title' => 'Family A Chore',
        ]);

        Sanctum::actingAs($this->parentB);
        $response = $this->post('/api/v1/settings/account/data-export');

        $vault = $this->readZipJson($response->getContent(), 'vault.json');
        $tasks = $this->readZipJson($response->getContent(), 'tasks.json');

        $this->assertCount(0, $vault, 'Parent B should see none of Parent A\'s vault entries');
        $this->assertCount(0, $tasks, 'Parent B should see none of Parent A\'s tasks');

        $this->assertStringNotContainsString($entryA->id, $response->getContent());
        $this->assertStringNotContainsString('family-a-secret', $response->getContent());
    }

    public function test_vault_encrypted_data_is_decrypted_in_export(): void
    {
        $category = VaultCategory::create([
            'family_id' => $this->familyA->id,
            'name' => 'Pantry',
            'slug' => 'pantry',
            'icon' => 'lemon',
        ]);

        VaultEntry::create([
            'family_id' => $this->familyA->id,
            'vault_category_id' => $category->id,
            'created_by' => $this->parentA->id,
            'title' => 'Lemon storage',
            'encrypted_data' => Crypt::encryptString(json_encode(['secret' => 'lemons'])),
        ]);

        Sanctum::actingAs($this->parentA);
        $response = $this->post('/api/v1/settings/account/data-export');

        $vault = $this->readZipJson($response->getContent(), 'vault.json');

        $this->assertCount(1, $vault);
        $this->assertSame('lemons', $vault[0]['data']['secret']);
        $this->assertNull($vault[0]['encrypted_data']);
    }

    public function test_calendar_tokens_are_redacted(): void
    {
        CalendarConnection::create([
            'user_id' => $this->parentA->id,
            'provider' => 'google',
            'access_token' => 'super-secret-access-token-xyz',
            'refresh_token' => 'super-secret-refresh-token-abc',
            'calendar_id' => 'cal-123',
            'calendar_name' => 'Family Calendar',
            'is_active' => true,
        ]);

        Sanctum::actingAs($this->parentA);
        $response = $this->post('/api/v1/settings/account/data-export');

        $body = $response->getContent();

        $this->assertStringNotContainsString('super-secret-access-token-xyz', $body);
        $this->assertStringNotContainsString('super-secret-refresh-token-abc', $body);

        $calendar = $this->readZipJson($body, 'calendar.json');
        $this->assertCount(1, $calendar);
        $this->assertSame('Family Calendar', $calendar[0]['calendar_name']);
        $this->assertArrayNotHasKey('access_token', $calendar[0]);
        $this->assertArrayNotHasKey('refresh_token', $calendar[0]);
    }

    public function test_shared_vault_entry_appears_in_both_owner_and_grantee_exports(): void
    {
        $sibling = User::factory()->parent()->create(['family_id' => $this->familyA->id]);

        $category = VaultCategory::create([
            'family_id' => $this->familyA->id,
            'name' => 'Shared',
            'slug' => 'shared',
            'icon' => 'share',
        ]);

        $entry = VaultEntry::create([
            'family_id' => $this->familyA->id,
            'vault_category_id' => $category->id,
            'created_by' => $this->parentA->id,
            'title' => 'Wifi password',
            'encrypted_data' => Crypt::encryptString(json_encode(['password' => 'shared-wifi-secret'])),
        ]);

        VaultPermission::create([
            'vault_entry_id' => $entry->id,
            'user_id' => $sibling->id,
            'permission_level' => 'view',
        ]);

        Sanctum::actingAs($this->parentA);
        $ownerVault = $this->readZipJson(
            $this->post('/api/v1/settings/account/data-export')->getContent(),
            'vault.json'
        );

        Sanctum::actingAs($sibling);
        $granteeVault = $this->readZipJson(
            $this->post('/api/v1/settings/account/data-export')->getContent(),
            'vault.json'
        );

        $this->assertCount(1, $ownerVault);
        $this->assertCount(1, $granteeVault);
        $this->assertSame($entry->id, $ownerVault[0]['id']);
        $this->assertSame($entry->id, $granteeVault[0]['id']);
        $this->assertSame('shared-wifi-secret', $granteeVault[0]['data']['password']);
    }

    private function extractZipFiles(string $bytes): array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'kinhold-test-');
        file_put_contents($tmp, $bytes);

        $zip = new ZipArchive;
        $zip->open($tmp);
        $files = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $files[] = $zip->getNameIndex($i);
        }
        $zip->close();
        @unlink($tmp);

        return $files;
    }

    private function readZipJson(string $bytes, string $filename): array
    {
        $tmp = tempnam(sys_get_temp_dir(), 'kinhold-test-');
        file_put_contents($tmp, $bytes);

        $zip = new ZipArchive;
        $zip->open($tmp);
        $contents = $zip->getFromName($filename);
        $zip->close();
        @unlink($tmp);

        return json_decode($contents, true) ?? [];
    }
}
