<?php

namespace Tests\Feature;

use App\Models\Family;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseConfigEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_config_endpoint_includes_license_block(): void
    {
        $response = $this->getJson('/api/v1/config');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'license' => ['warn', 'family_count', 'commercial_license_acknowledged'],
        ]);
    }

    public function test_config_endpoint_is_public(): void
    {
        // Confirm the license block is exposed pre-auth — it gates a banner the
        // SPA needs to render before login finishes resolving.
        config()->set('kinhold.self_hosted', true);
        Family::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/config');

        $response->assertStatus(200);
        $response->assertJsonPath('license.warn', true);
        $response->assertJsonPath('license.family_count', 2);
    }

    public function test_license_warn_is_false_on_hosted_kinhold_with_many_families(): void
    {
        config()->set('kinhold.self_hosted', false);
        Family::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/config');

        $response->assertJsonPath('license.warn', false);
        // family_count is gated to warn-only — hosted Kinhold should not leak it.
        $response->assertJsonPath('license.family_count', null);
    }

    public function test_license_warn_is_false_when_commercial_license_acknowledged(): void
    {
        config()->set('kinhold.self_hosted', true);
        config()->set('kinhold.commercial_license_acknowledged', true);
        Family::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/config');

        $response->assertJsonPath('license.warn', false);
        $response->assertJsonPath('license.commercial_license_acknowledged', true);
        // Banner suppressed → family_count not exposed.
        $response->assertJsonPath('license.family_count', null);
    }

    public function test_license_warn_is_false_for_single_family_self_hosted(): void
    {
        config()->set('kinhold.self_hosted', true);
        Family::factory()->create();

        $response = $this->getJson('/api/v1/config');

        $response->assertJsonPath('license.warn', false);
        // No banner → no need to know the count.
        $response->assertJsonPath('license.family_count', null);
    }

    public function test_family_count_is_exposed_only_when_warn_is_true(): void
    {
        // Belt-and-suspenders for the gating: same DB state as test_config_endpoint_is_public,
        // but verifies the count comes through with the correct integer value.
        config()->set('kinhold.self_hosted', true);
        Family::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/config');

        $response->assertJsonPath('license.warn', true);
        $response->assertJsonPath('license.family_count', 3);
    }
}
