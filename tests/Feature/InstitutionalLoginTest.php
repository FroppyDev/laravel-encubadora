<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstitutionalLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_institutional_user_logs_in_and_reaches_home(): void
    {
        $user = User::factory()->create(['email' => 'student@info.uas.edu.mx']);

        $this->post('/login', ['email' => ' STUDENT@INFO.UAS.EDU.MX ', 'password' => 'password'])
            ->assertSessionHasNoErrors()->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
        $this->get('/home')->assertOk()->assertSee($user->name);
    }

    public function test_other_domains_are_rejected_even_with_correct_password(): void
    {
        foreach (['student@example.com', 'student@info.uas.edu.mx.example.com', 'student@sub.info.uas.edu.mx'] as $email) {
            $user = User::factory()->create(['email' => $email]);
            $this->post('/login', ['email' => $email, 'password' => 'password'])
                ->assertSessionHasErrors('email');
            $this->assertGuest();
            $this->postJson('/api/v1/login', ['email' => $email, 'password' => 'password', 'device_name' => 'Test'])
                ->assertUnprocessable()->assertJsonValidationErrors('email');
            $this->assertSame(0, $user->tokens()->count());
        }
    }

    public function test_institutional_domain_does_not_replace_password_validation(): void
    {
        User::factory()->create(['email' => 'student@info.uas.edu.mx']);
        foreach (['student@info.uas.edu.mx', 'missing@info.uas.edu.mx'] as $email) {
            $this->post('/login', ['email' => $email, 'password' => 'incorrect'])
                ->assertSessionHasErrors('email');
            $this->assertGuest();
        }
    }

    public function test_unverified_user_must_verify_email_before_accessing_home(): void
    {
        User::factory()->unverified()->create(['email' => 'pending@info.uas.edu.mx']);
        $this->post('/login', ['email' => 'pending@info.uas.edu.mx', 'password' => 'password'])
            ->assertRedirect('/home');
        $this->get('/home')->assertRedirect('/email/verify');
    }

    public function test_home_requires_authentication(): void
    {
        $this->get('/home')->assertRedirect('/login');
    }

    public function test_institutional_api_login_still_returns_a_token(): void
    {
        User::factory()->create(['email' => 'mobile@info.uas.edu.mx']);
        $this->postJson('/api/v1/login', ['email' => 'mobile@info.uas.edu.mx', 'password' => 'password', 'device_name' => 'Test'])
            ->assertOk()->assertJsonStructure(['token', 'token_type']);
    }
}
