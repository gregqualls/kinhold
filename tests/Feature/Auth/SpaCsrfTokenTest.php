<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;
use Illuminate\Cookie\CookieValuePrefix;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SpaCsrfTokenTest extends TestCase
{
    use RefreshDatabase;

    private const SESSION_TOKEN = 'live-session-token';

    protected function setUp(): void
    {
        parent::setUp();

        // The framework skips CSRF verification under unit tests; this suite exists to exercise it.
        $this->app->bind(VerifyCsrfToken::class, fn ($app) => new class($app, $app['encrypter']) extends VerifyCsrfToken
        {
            protected function runningUnitTests()
            {
                return false;
            }
        });

        Sanctum::actingAs(User::factory()->create());
    }

    public function test_spa_write_succeeds_with_xsrf_cookie_token(): void
    {
        $this->spaWrite(['X-XSRF-TOKEN' => $this->xsrfHeader(self::SESSION_TOKEN)])
            ->assertOk();
    }

    public function test_spa_write_without_any_token_is_rejected(): void
    {
        $this->spaWrite([])->assertStatus(419);
    }

    public function test_stale_x_csrf_token_header_overrides_a_valid_xsrf_token(): void
    {
        $this->spaWrite([
            'X-CSRF-TOKEN' => 'stale-token-from-page-load',
            'X-XSRF-TOKEN' => $this->xsrfHeader(self::SESSION_TOKEN),
        ])->assertStatus(419);
    }

    public function test_same_origin_browser_write_succeeds_without_a_token(): void
    {
        $this->spaWrite(['Sec-Fetch-Site' => 'same-origin'])->assertOk();
    }

    public function test_cross_site_browser_write_without_a_token_is_rejected(): void
    {
        $this->spaWrite(['Sec-Fetch-Site' => 'cross-site'])->assertStatus(419);
    }

    private function spaWrite(array $headers)
    {
        return $this->withSession(['_token' => self::SESSION_TOKEN])
            ->withHeaders(['Origin' => 'http://localhost', ...$headers])
            ->putJson('/api/v1/settings/notification-preferences', [
                'email' => ['task_assigned' => false],
            ]);
    }

    private function xsrfHeader(string $token): string
    {
        $encrypter = $this->app['encrypter'];

        return $encrypter->encrypt(CookieValuePrefix::create('XSRF-TOKEN', $encrypter->getKey()).$token, false);
    }
}
