<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class EmailCrlfInjectionTest extends TestCase
{
    use RefreshDatabase;

    private const INJECTED_EMAIL = "\"foo\r\nBcc: victim@example.com\"@example.com";

    public function test_email_rule_rejects_addresses_with_line_breaks(): void
    {
        $this->assertTrue(Validator::make(['email' => self::INJECTED_EMAIL], ['email' => 'email'])->fails());
        $this->assertTrue(Validator::make(['email' => "foo\n@example.com"], ['email' => 'email:rfc'])->fails());
        $this->assertTrue(Validator::make(['email' => 'foo@example.com'], ['email' => 'email'])->passes());
    }

    public function test_registration_rejects_an_email_with_injected_headers(): void
    {
        Notification::fake();

        $this->postJson('/api/v1/register', [
            'name' => 'Attacker',
            'email' => self::INJECTED_EMAIL,
            'password' => 'Password1',
            'password_confirmation' => 'Password1',
            'family_name' => 'Attacker Family',
        ])->assertStatus(422)->assertJsonValidationErrors('email');

        $this->assertSame(0, User::count());
        Notification::assertNothingSent();
    }
}
