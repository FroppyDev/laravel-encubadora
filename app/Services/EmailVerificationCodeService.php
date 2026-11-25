<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\EmailVerificationCode;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class EmailVerificationCodeService
{
    private function key(User $user): string
    {
        return 'email-verification-code:'.$user->getKey().':'.hash('sha256', $user->getEmailForVerification());
    }

    public function send(User $user, bool $resend = false): void
    {
        $key = $this->key($user);
        Cache::lock($key.':lock', 60)->block(5, function () use ($user, $key, $resend) {
            if ($user->fresh()->hasVerifiedEmail()) {
                return;
            }
            $current = Cache::get($key);
            if ($current && ! $resend) {
                return;
            }
            if (Cache::has($key.':cooldown')) {
                throw ValidationException::withMessages(['code' => 'Espera un minuto antes de solicitar otro código.']);
            }
            $code = (string) random_int(100000, 999999);
            // Persist only the hash; replace it only after the mail transport accepts the message.
            $user->notify(new EmailVerificationCode($code));
            Cache::put($key, ['hash' => Hash::make($code), 'attempts' => 0, 'expires_at' => now()->addMinutes(10)->timestamp], 600);
            Cache::put($key.':cooldown', true, 60);
        });
    }

    public function verify(User $user, string $code): void
    {
        $key = $this->key($user);
        Cache::lock($key.':lock', 60)->block(5, function () use ($user, $code, $key) {
            if ($user->fresh()->hasVerifiedEmail()) {
                return;
            }
            $record = Cache::get($key);
            if (! $record || $record['expires_at'] <= now()->timestamp) {
                throw ValidationException::withMessages(['code' => 'El código venció. Solicita uno nuevo.']);
            }
            if ($record['attempts'] >= 5) {
                throw ValidationException::withMessages(['code' => 'Se agotaron los intentos. Solicita un código nuevo.']);
            }
            if (! Hash::check($code, $record['hash'])) {
                $record['attempts']++;
                Cache::put($key, $record, max(1, $record['expires_at'] - now()->timestamp));
                throw ValidationException::withMessages(['code' => 'El código no es correcto.']);
            }
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }
            Cache::forget($key);
        });
    }
}
