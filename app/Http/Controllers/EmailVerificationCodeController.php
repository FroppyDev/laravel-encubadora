<?php

namespace App\Http\Controllers;

use App\Services\EmailVerificationCodeService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class EmailVerificationCodeController extends Controller
{
    public function notice(Request $request, EmailVerificationCodeService $codes)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/home');
        }
        $deliveryError = null;
        try {
            $codes->send($request->user());
        } catch (TransportExceptionInterface $exception) {
            $deliveryError = 'No pudimos enviar el correo. Intenta reenviar el código en unos momentos.';
        } catch (ValidationException $exception) {
            $deliveryError = $exception->errors()['code'][0];
        }

        return view('auth.verify', ['deliveryError' => $deliveryError]);
    }

    public function resend(Request $request, EmailVerificationCodeService $codes)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/home');
        }
        try {
            $codes->send($request->user(), true);
        } catch (TransportExceptionInterface $exception) {
            return back()->withErrors(['code' => 'No pudimos enviar el correo. Inténtalo de nuevo más tarde.']);
        }

        return redirect('/email/verify')->with('status', 'Enviamos un código nuevo a tu correo.');
    }

    public function verify(Request $request, EmailVerificationCodeService $codes)
    {
        $data = $request->validate(['code' => ['required', 'string', 'regex:/^[0-9]{6}$/']], [
            'code.required' => 'Ingresa el código que recibiste por correo.',
            'code.regex' => 'El código debe tener seis dígitos.',
        ]);
        $codes->verify($request->user(), $data['code']);

        return redirect('/home');
    }
}
