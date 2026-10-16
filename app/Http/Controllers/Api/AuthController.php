<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Rules\InstitutionalEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->safe()->only(['name', 'email', 'password']))->refresh();
        event(new Registered($user));

        return response()->json(['data' => $user, 'message' => 'Cuenta creada. Revisa tu correo para verificarla e inicia sesión.'], 201);
    }

    public function login(Request $request)
    {
        if (is_string($request->email)) {
            $request->merge(['email' => strtolower(trim($request->email))]);
        }
        $data = $request->validate(['email' => ['required', 'email', new InstitutionalEmail], 'password' => 'required|string', 'device_name' => 'required|string|max:100']);
        $user = User::where('email', $data['email'])->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['Las credenciales no son correctas.']]);
        }

        return response()->json(['data' => $user, 'token' => $user->createToken($data['device_name'], ['*'], now()->addDays(30))->plainTextToken, 'token_type' => 'Bearer']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }

    public function user(Request $request)
    {
        return response()->json(['data' => $request->user()]);
    }

    public function profile(Request $request)
    {
        $data = $request->validate(['name' => 'sometimes|required|string|max:100', 'bio' => 'sometimes|nullable|string|max:2000']);
        $user = $request->user();
        foreach ($data as $key => $value) {
            $user->{$key} = $value;
        }
        $user->save();

        return response()->json(['data' => $user]);
    }

    public function resend(Request $request)
    {
        if (! $request->user()->hasVerifiedEmail()) {
            $request->user()->sendEmailVerificationNotification();
        }

        return response()->json(['message' => 'Si tu correo está pendiente, enviamos un enlace de verificación.']);
    }
}
