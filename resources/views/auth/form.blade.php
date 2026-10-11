<!doctype html>
<html lang="es"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Acceso · Incubadora FIC</title>
<style>body{font:17px system-ui;background:#f4f6fa;color:#183048;max-width:460px;margin:8vh auto;padding:24px}input,button{box-sizing:border-box;width:100%;padding:12px;margin:8px 0 20px}button{background:#163f58;color:white;border:0;border-radius:6px}a{color:#125875}li{color:#a32020}</style>
<h1>Incubadora FIC</h1>
@php
    $titles = ['login'=>'Iniciar sesión','register'=>'Crear cuenta','forgot-password'=>'Recuperar contraseña','reset-password'=>'Restablecer contraseña','confirm-password'=>'Confirmar contraseña'];
    $actions = ['login'=>'/login','register'=>'/register','forgot-password'=>'/forgot-password','reset-password'=>'/reset-password','confirm-password'=>'/user/confirm-password'];
@endphp
<h2>{{ $titles[$mode] }}</h2>
@if(session('status'))<p role="status">Solicitud procesada. Revisa tu correo si solicitaste un enlace.</p>@endif
@if($errors->any())<ul role="alert">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
<form method="POST" action="{{ $actions[$mode] }}">@csrf
@if($mode === 'register')<label>Nombre<input name="name" value="{{ old('name') }}" maxlength="100" required autocomplete="name"></label>@endif
@if($mode !== 'confirm-password')<label>Correo institucional<input type="email" name="email" value="{{ old('email', isset($request) ? $request->email : '') }}" required autocomplete="email"></label>@endif
@if($mode !== 'forgot-password')<label>Contraseña<input type="password" name="password" required autocomplete="{{ $mode === 'register' || $mode === 'reset-password' ? 'new-password' : 'current-password' }}"></label>@endif
@if(in_array($mode, ['register','reset-password']))<label>Confirmar contraseña<input type="password" name="password_confirmation" required autocomplete="new-password"></label>@endif
@if($mode === 'reset-password')<input type="hidden" name="token" value="{{ $request->route('token') }}">@endif
<button type="submit">{{ $titles[$mode] }}</button>
</form><nav><a href="/login">Ingresar</a> · <a href="/register">Crear cuenta</a> · <a href="/forgot-password">Recuperar acceso</a></nav></html>
