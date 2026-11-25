<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use App\Rules\InstitutionalEmail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::authenticateUsing(function (Request $request) {
            if (is_string($request->input('email'))) {
                $request->merge(['email' => Str::lower(trim($request->input('email')))]);
            }
            $data = $request->validate([
                'email' => ['required', 'email', new InstitutionalEmail],
                'password' => ['required', 'string'],
            ]);
            $user = User::where('email', $data['email'])->first();

            return $user && Hash::check($data['password'], $user->password) ? $user : null;
        });

        Fortify::loginView(fn () => view('auth.form', ['mode' => 'login']));
        Fortify::registerView(fn () => view('auth.form', ['mode' => 'register']));
        Fortify::requestPasswordResetLinkView(fn () => view('auth.form', ['mode' => 'forgot-password']));
        Fortify::resetPasswordView(fn (Request $request) => view('auth.form', ['mode' => 'reset-password', 'request' => $request]));
        Fortify::verifyEmailView(fn (Request $request) => app(\App\Http\Controllers\EmailVerificationCodeController::class)->notice($request, app(\App\Services\EmailVerificationCodeService::class)));
        Fortify::confirmPasswordView(fn () => view('auth.form', ['mode' => 'confirm-password']));
        RateLimiter::for('registration', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));
        VerifyEmail::createUrlUsing(function ($user) {
            // Mobile clients can reach the same API through emulator/USB aliases.
            // Email links must use the configured browser-accessible origin.
            $urls = clone URL::getFacadeRoot();
            $urls->useOrigin(config('app.url'));
            $urls->forceScheme(parse_url(config('app.url'), PHP_URL_SCHEME));

            return $urls->temporarySignedRoute('api.verification.verify', now()->addMinutes(60), ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]);
        });
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip()
            );
        });
    }
}
