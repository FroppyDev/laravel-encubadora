<!doctype html>
<html lang="es"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Acceso · Incubadora FIC</title>
<style>body{font:17px system-ui;background:#f4f6fa;color:#183048;max-width:460px;margin:8vh auto;padding:24px}input,button{box-sizing:border-box;width:100%;padding:12px;margin:8px 0 20px}button{background:#163f58;color:white;border:0;border-radius:6px}a{color:#125875}li{color:#a32020}</style>
@if(in_array($mode, ['login', 'register', 'forgot-password', 'reset-password']))
@php
    $formTitles = ['login' => 'Iniciar sesión', 'register' => 'Crear cuenta', 'forgot-password' => 'Recuperar contraseña', 'reset-password' => 'Restablecer contraseña'];
    $formActions = ['login' => '/login', 'register' => '/register', 'forgot-password' => '/forgot-password', 'reset-password' => '/reset-password'];
@endphp
<style>
    *{box-sizing:border-box}
    body{max-width:none;margin:0;padding:24px 16px;min-height:100vh;min-height:100svh;display:grid;place-items:center;background:#eef0f4;font-family:Arial,Helvetica,sans-serif;color:#374151;font-size:14px}
    .auth-card{width:100%;max-width:360px;min-height:640px;padding:44px 24px 32px;background:#fff;border-radius:28px;box-shadow:0 8px 32px #26344b08}
    .brand{text-align:center;margin-bottom:60px}
    .brand-icon{display:grid;place-items:center;width:88px;height:88px;margin:0 auto 18px;background:#1975ef;border-radius:50%;color:#fff}
    .brand-icon svg{width:58px;height:58px}
    .brand h1{font-size:21px;line-height:1.3;margin:0;color:#ca85ff;font-weight:700}
    .brand p{font-size:13px;line-height:1.4;color:#737373;margin:8px 0 0}
    .auth-card label{display:block;font-size:13px;font-weight:700;line-height:1.4;margin-bottom:20px}
    .auth-card input{display:block;width:100%;min-height:46px;border:1px solid #d5d9e0;border-radius:7px;background:#fff;padding:12px 10px;margin:5px 0 0;font:14px Arial,Helvetica,sans-serif;color:#374151;box-shadow:0 1px 2px #18304805}
    .auth-card input::placeholder{color:#94a3b8;opacity:1}
    .auth-card input[type="hidden"]{display:none}
    .auth-card input:focus-visible{outline:2px solid #1975ef;outline-offset:2px}
    .auth-card button{width:100%;min-height:44px;padding:12px;margin:2px 0 0;border:0;border-radius:999px;background:#c885fa;color:#fff;font-size:14px;font-weight:700;cursor:pointer}
    .auth-card button:hover{filter:brightness(.95)}
    .auth-card a:focus-visible,.auth-card button:focus-visible{outline:2px solid #1975ef;outline-offset:4px}
    .auth-card a{color:#a855db;text-underline-offset:3px}
    .auth-nav{text-align:center;font-size:13px;line-height:1.6;margin-top:26px}
    .auth-nav .forgot-link{display:inline-block;font-weight:700;margin-bottom:30px;color:#bc75ef;text-decoration:none}
    .auth-nav p{margin:0;color:#737373}
    .auth-nav p a{color:inherit}
    .auth-feedback{padding:12px 16px;border-radius:7px;background:#fff1f2;font-size:13px;line-height:1.5;margin:0 0 20px;overflow-wrap:anywhere}
    ul.auth-feedback{padding-left:30px}
    .auth-feedback[role="status"]{background:#eff6ff;color:#125875}
    .auth-card--register{padding:40px 16px 24px;min-height:640px}
    .auth-card--register .brand{margin-bottom:44px}
    .auth-card--register .brand-icon{width:56px;height:56px;background:#0069ce;margin-bottom:16px}
    .auth-card--register .brand-icon svg{width:22px;height:22px}
    .auth-card--register .brand h1{color:#237af1}
    .auth-card h2{font-size:18px;margin:0 0 26px;line-height:1.4}
    .auth-card--register label{margin-bottom:16px}
    .auth-card--register input{min-height:40px;padding:10px 8px}
    .auth-card--register button{background:#0069ce;border-radius:5px;margin-top:18px}
    .auth-card--register .auth-nav{border-top:7px solid #d1d5db;padding-top:14px;margin-top:24px}
    .auth-card--register .auth-nav a{color:#237af1;text-decoration:none;font-weight:700}
    .brand-intro{display:none}
    .form-heading{display:none}
    .form-description{font-size:14px;line-height:1.7;color:#65738a;margin:0 0 28px}
    .auth-card--forgot-password .auth-content>h2,.auth-card--reset-password .auth-content>h2{margin-bottom:14px}
    .auth-card--forgot-password button,.auth-card--reset-password button{background:#a65bdd}
    .auth-card--forgot-password .auth-nav p a,.auth-card--reset-password .auth-nav p a{color:#9450c6;font-weight:700}
    @media(min-width:768px){
        body{padding:48px 32px;background:radial-gradient(ellipse at top left,#e4edff,transparent 60%),radial-gradient(ellipse at bottom right,#f1e8fc,transparent 60%),#f5f7fb}
        .auth-card,.auth-card--register{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.1fr);max-width:1040px;min-height:660px;padding:0;border:1px solid #e4e8f0;border-radius:24px;overflow:hidden;box-shadow:0 24px 80px #26344b12}
        .brand,.auth-card--register .brand{display:flex;flex-direction:column;align-items:flex-start;justify-content:center;text-align:left;margin:0;padding:56px 44px;background:linear-gradient(150deg,#edf4ff,#f4edff);border-right:1px solid #e7e9f2}
        .auth-card--register .brand{background:linear-gradient(150deg,#eaf3ff,#f3f8ff)}
        .brand-icon,.auth-card--register .brand-icon{width:80px;height:80px;margin:0 0 24px}
        .brand-icon svg,.auth-card--register .brand-icon svg{width:52px;height:52px}
        .brand h1,.auth-card--register .brand h1{font-size:30px;letter-spacing:-1px;color:#1769d2}
        .brand p{font-size:15px;line-height:1.7;color:#65738a;margin-top:14px}
        .brand p br{display:none}
        .brand-intro{display:block;margin-top:48px}
        .brand-intro h2{font-size:32px;line-height:1.25;letter-spacing:-.8px;color:#243553;margin:0 0 16px;max-width:300px}
        .brand-intro p{max-width:300px;margin:0}
        .auth-content{align-self:center;padding:56px 52px;min-width:0}
        .form-heading{display:block}
        .auth-content>h2{font-size:28px;letter-spacing:-.6px;color:#243553;margin-bottom:32px}
        .auth-card label,.auth-card--register label{font-size:14px;margin-bottom:22px}
        .auth-card input,.auth-card--register input{min-height:50px;padding:14px;margin-top:8px;border-radius:8px;font-size:15px}
        .auth-card button,.auth-card--register button{min-height:50px;font-size:15px;margin-top:8px;border-radius:9px}
        .auth-card--login button{background:#a65bdd}
        .auth-nav{font-size:14px;margin-top:24px}
        .auth-nav .forgot-link{color:#9450c6;margin-bottom:26px}
        .auth-card--register .auth-nav{border-top:1px solid #e4e8f0;padding-top:24px;margin-top:28px}
    }
    @media(min-width:768px) and (max-width:960px){.brand,.auth-card--register .brand{padding:40px 28px}.auth-content{padding:40px 32px}.brand h1,.auth-card--register .brand h1{font-size:26px}}
    @media(max-width:420px){body{padding:0;background:#fff}.auth-card{max-width:360px;min-height:100svh;box-shadow:none;border-radius:24px;padding-top:44px}.auth-card--register{padding-top:40px}}
</style>
<main class="auth-card auth-card--{{ $mode }}">
    <header class="brand">
        <span class="brand-icon" aria-hidden="true">
            <svg viewBox="0 0 64 64" fill="none"><circle cx="32" cy="32" r="23" stroke="currentColor" stroke-width="4"/><path d="m42 22-6 14-14 6 6-14 14-6Z" stroke="currentColor" stroke-width="4" stroke-linejoin="round"/><circle cx="32" cy="32" r="3" fill="currentColor"/></svg>
        </span>
        <h1>EncubadoraFIC</h1>
        @if($mode === 'login')<p>Acceso exclusivo para estudiantes de la FIC</p>@endif
        <div class="brand-intro">
            <h2>El comienzo de tu próxima gran idea.</h2>
            <p>Un espacio para conectar, compartir proyectos y crecer con la comunidad de la FIC.</p>
        </div>
    </header>
    <section class="auth-content" aria-label="{{ $formTitles[$mode] }}">
    @if($mode === 'login')<h2 class="form-heading">Iniciar sesión</h2>@endif
    @if($mode === 'register')<h2>Crear cuenta</h2>@endif
    @if($mode === 'forgot-password')
        <h2>¿Olvidaste tu contraseña?</h2>
        <p class="form-description">Ingresa tu correo institucional para solicitar un enlace y restablecer tu contraseña.</p>
    @endif
    @if($mode === 'reset-password')
        <h2>Restablecer contraseña</h2>
        <p class="form-description">Crea y confirma tu nueva contraseña para recuperar el acceso a tu cuenta.</p>
    @endif
    @if(session('status'))<p class="auth-feedback" role="status">Solicitud procesada. Revisa tu correo si solicitaste un enlace.</p>@endif
    @if($errors->any())<ul class="auth-feedback" role="alert">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
    <form method="POST" action="{{ $formActions[$mode] }}" aria-label="{{ $formTitles[$mode] }}">
        @csrf
        @if($mode === 'register')<label>Nombre completo<input name="name" value="{{ old('name') }}" maxlength="100" required autocomplete="name" placeholder="Escribe tu nombre completo"></label>@endif
        <label>Correo electrónico<input type="email" name="email" value="{{ old('email', isset($request) ? $request->email : '') }}" required autocomplete="email" placeholder="tu.correo@info.uas.edu.mx"></label>
        @if($mode !== 'forgot-password')<label>{{ $mode === 'reset-password' ? 'Nueva contraseña' : 'Contraseña' }}<input type="password" name="password" required autocomplete="{{ in_array($mode, ['register', 'reset-password']) ? 'new-password' : 'current-password' }}" placeholder="••••••••"></label>@endif
        @if(in_array($mode, ['register', 'reset-password']))<label>Confirmar contraseña<input type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••"></label>@endif
        @if($mode === 'reset-password')<input type="hidden" name="token" value="{{ $request->route('token') }}">@endif
        <button type="submit">{{ $mode === 'forgot-password' ? 'Enviar enlace de recuperación' : ($mode === 'register' ? 'Regístrate' : $formTitles[$mode]) }}</button>
    </form>
    <nav class="auth-nav" aria-label="Opciones de acceso">
        @if($mode === 'login')
            <a class="forgot-link" href="/forgot-password">¿Olvidaste tu contraseña?</a>
            <p>¿No tienes una cuenta? <a href="/register">Regístrate</a></p>
        @elseif(in_array($mode, ['forgot-password', 'reset-password']))
            <p><a href="/login">Volver a iniciar sesión</a></p>
        @else
            <p>¿Ya tienes una cuenta? <a href="/login">Iniciar sesión</a></p>
        @endif
    </nav>
    </section>
</main>
@else
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
</form><nav><a href="/login">Ingresar</a> · <a href="/register">Crear cuenta</a> · <a href="/forgot-password">Recuperar acceso</a></nav>
@endif
</html>
