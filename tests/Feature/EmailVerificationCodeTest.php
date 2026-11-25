<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\EmailVerificationCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationCodeTest extends TestCase
{
    use RefreshDatabase;

    private function pendingUser(string $email = 'pending@info.uas.edu.mx'): User
    {
        return User::factory()->unverified()->create(['email' => $email]);
    }

    private function sentCode(User $user): string
    {
        return Notification::sent($user, EmailVerificationCode::class)->last()->code;
    }

    public function test_notice_sends_once_and_valid_code_opens_home(): void
    {
        Notification::fake();
        $user = $this->pendingUser();
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/home');
        $this->get('/home')->assertRedirect('/email/verify');
        $this->get('/email/verify')->assertOk()->assertSee('Código de verificación');
        $this->get('/email/verify')->assertOk();
        Notification::assertSentToTimes($user, EmailVerificationCode::class, 1);
        $this->post('/email/verification-code', ['code' => $this->sentCode($user)])->assertRedirect('/home');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->get('/home')->assertOk();
    }

    public function test_expired_code_cannot_verify_account(): void
    {
        Notification::fake();
        $user = $this->pendingUser();
        $this->actingAs($user)->get('/email/verify')->assertOk();
        $code = $this->sentCode($user);
        $this->travel(11)->minutes();
        $this->post('/email/verification-code', ['code' => $code])->assertSessionHasErrors('code');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_five_failed_attempts_block_even_correct_code(): void
    {
        Notification::fake();
        $user = $this->pendingUser();
        $this->actingAs($user)->get('/email/verify')->assertOk();
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/email/verification-code', ['code' => '000000'])->assertSessionHasErrors('code');
        }
        $this->post('/email/verification-code', ['code' => $this->sentCode($user)])->assertSessionHasErrors('code');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_code_is_bound_to_the_authenticated_account(): void
    {
        Notification::fake();
        $first = $this->pendingUser();
        $second = $this->pendingUser('second@info.uas.edu.mx');
        $this->actingAs($first)->get('/email/verify')->assertOk();
        $this->actingAs($second)->post('/email/verification-code', ['code' => $this->sentCode($first)])
            ->assertSessionHasErrors('code');
        $this->assertFalse($second->fresh()->hasVerifiedEmail());
    }

    public function test_resend_has_cooldown_and_replaces_old_code(): void
    {
        Notification::fake();
        $user = $this->pendingUser();
        $this->actingAs($user)->get('/email/verify')->assertOk();
        $this->post('/email/verification-code/resend')->assertSessionHasErrors('code');
        Notification::assertSentToTimes($user, EmailVerificationCode::class, 1);
        $this->travel(61)->seconds();
        $this->post('/email/verification-code/resend')->assertRedirect('/email/verify');
        Notification::assertSentToTimes($user, EmailVerificationCode::class, 2);
        $this->post('/email/verification-code', ['code' => $this->sentCode($user)])->assertRedirect('/home');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_guests_cannot_request_or_submit_codes(): void
    {
        Notification::fake();
        $this->post('/email/verification-code', ['code' => '123456'])->assertRedirect('/login');
        $this->post('/email/verification-code/resend')->assertRedirect('/login');
        Notification::assertNothingSent();
    }

    public function test_verified_accounts_do_not_receive_codes(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'verified@info.uas.edu.mx']);
        $this->actingAs($user)->get('/email/verify')->assertRedirect('/home');
        $this->post('/email/verification-code/resend')->assertRedirect('/home');
        Notification::assertNothingSent();
    }

    public function test_delivery_failure_is_visible_and_does_not_verify_user(): void
    {
        Notification::shouldReceive('send')->once()->andThrow(
            new \Symfony\Component\Mailer\Exception\TransportException('SMTP unavailable')
        );
        $user = $this->pendingUser();
        $this->actingAs($user)->get('/email/verify')->assertOk()->assertSee('No pudimos enviar el correo');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
