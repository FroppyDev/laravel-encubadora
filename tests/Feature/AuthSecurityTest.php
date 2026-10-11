<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_reset_revokes_existing_mobile_tokens(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'reset@info.uas.edu.mx']);
        $user->createToken('Android');
        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHasNoErrors();
        $resetToken = null;
        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use (&$resetToken) {
            $resetToken = $notification->token;

            return true;
        });
        $this->get('/reset-password/'.$resetToken.'?email='.$user->email)->assertOk();
        $this->post('/reset-password', ['token' => $resetToken, 'email' => $user->email, 'password' => 'NewPassword123!', 'password_confirmation' => 'NewPassword123!'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_login_and_registration_are_throttled(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/login', ['email' => 'missing@info.uas.edu.mx', 'password' => 'wrong', 'device_name' => 'Test'])->assertUnprocessable();
        }
        $this->postJson('/api/v1/login', ['email' => 'missing@info.uas.edu.mx', 'password' => 'wrong', 'device_name' => 'Test'])->assertStatus(429);
        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/v1/register', [])->assertUnprocessable();
        }
        $this->postJson('/api/v1/register', [])->assertStatus(429);
    }

    public function test_cookie_session_does_not_authenticate_mobile_api(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web')->getJson('/api/v1/user')->assertUnauthorized();
    }

    public function test_api_returns_json_without_accept_header(): void
    {
        $this->get('/api/v1/user')->assertUnauthorized()->assertHeader('Content-Type', 'application/json');
        $this->withToken('invalid')->get('/api/v1/projects')->assertUnauthorized();
    }
}
