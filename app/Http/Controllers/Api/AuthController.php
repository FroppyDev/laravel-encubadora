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
use Illuminate\Support\Facades\Storage;

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

    public function images(Request $request)
    {
        $request->validate([
            'avatar' => [
                'required_without:cover',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:max_width=4096,max_height=4096',
            ],
            'cover' => [
                'required_without:avatar',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                'dimensions:max_width=6000,max_height=6000',
            ],
        ]);

        $user = $request->user();

        $fields = [
            'avatar' => ['avatar_path', 'users/avatars'],
            'cover' => ['cover_image_path', 'users/covers'],
        ];

        $newPaths = [];
        $oldPaths = [];

        try {
            foreach ($fields as $input => [$column, $directory]) {
                if (! $request->hasFile($input)) {
                    continue;
                }

                $path = $request->file($input)->store($directory, 'public');

                if ($path === false) {
                    throw new \RuntimeException(
                        'No se pudo guardar la imagen.'
                    );
                }

                $newPaths[] = $path;

                // Solo considera archivos personalizados de esta carpeta.
                $previousPath = $user->{$column};

                if (
                    $previousPath &&
                    str_starts_with($previousPath, $directory . '/')
                ) {
                    $oldPaths[] = $previousPath;
                }

                $user->{$column} = $path;
            }

            $user->saveOrFail();
        } catch (\Throwable $exception) {
            // Si falla el guardado, limpia los archivos nuevos.
            // Conserva el error original si también falla la limpieza.
            try {
                Storage::disk('public')->delete($newPaths);
            } catch (\Throwable $cleanupException) {
                report($cleanupException);
            }

            throw $exception;
        }

        // La actualización ya se guardó. Un fallo de limpieza
        // no debe hacer que la petición parezca fallida.
        try {
            Storage::disk('public')->delete($oldPaths);
        } catch (\Throwable $cleanupException) {
            report($cleanupException);
        }

        return response()->json([
            'message' => 'Imágenes actualizadas correctamente.',
            'data' => $user->fresh(),
        ]);
    }
}
