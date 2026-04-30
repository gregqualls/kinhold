<?php

namespace Tests\Unit;

use App\Models\Family;
use App\Services\LicenseEnforcementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseEnforcementServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_warn_returns_false_when_not_self_hosted_even_with_many_families(): void
    {
        config()->set('kinhold.self_hosted', false);
        config()->set('kinhold.commercial_license_acknowledged', false);
        Family::factory()->count(3)->create();

        $this->assertFalse((new LicenseEnforcementService)->shouldWarn());
    }

    public function test_should_warn_returns_false_when_self_hosted_but_only_one_family(): void
    {
        config()->set('kinhold.self_hosted', true);
        config()->set('kinhold.commercial_license_acknowledged', false);
        Family::factory()->create();

        $this->assertFalse((new LicenseEnforcementService)->shouldWarn());
    }

    public function test_should_warn_returns_false_when_self_hosted_with_zero_families(): void
    {
        config()->set('kinhold.self_hosted', true);
        config()->set('kinhold.commercial_license_acknowledged', false);

        $this->assertFalse((new LicenseEnforcementService)->shouldWarn());
    }

    public function test_should_warn_returns_true_when_self_hosted_and_more_than_one_family(): void
    {
        config()->set('kinhold.self_hosted', true);
        config()->set('kinhold.commercial_license_acknowledged', false);
        Family::factory()->count(2)->create();

        $this->assertTrue((new LicenseEnforcementService)->shouldWarn());
    }

    public function test_should_warn_returns_false_when_commercial_license_acknowledged(): void
    {
        config()->set('kinhold.self_hosted', true);
        config()->set('kinhold.commercial_license_acknowledged', true);
        Family::factory()->count(5)->create();

        $this->assertFalse((new LicenseEnforcementService)->shouldWarn());
    }

    public function test_family_count_reflects_db_state(): void
    {
        Family::factory()->count(4)->create();

        $this->assertSame(4, (new LicenseEnforcementService)->familyCount());
    }

    public function test_family_count_is_cached_within_instance(): void
    {
        $service = new LicenseEnforcementService;
        Family::factory()->create();

        $first = $service->familyCount();
        Family::factory()->create();
        $second = $service->familyCount();

        $this->assertSame($first, $second, 'Cached count should not change within a request.');
    }

    public function test_acknowledged_reads_config(): void
    {
        config()->set('kinhold.commercial_license_acknowledged', true);
        $this->assertTrue((new LicenseEnforcementService)->acknowledged());

        config()->set('kinhold.commercial_license_acknowledged', false);
        $this->assertFalse((new LicenseEnforcementService)->acknowledged());
    }
}
