<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verificar correo · Incubadora FIC</title>
    <style>
        *{box-sizing:border-box}body{margin:0;min-height:100svh;padding:32px;display:grid;place-items:center;font:15px/1.6 Arial,Helvetica,sans-serif;color:#243553;background:radial-gradient(ellipse at top left,#e4edff,transparent 60%),#f5f7fb}.card{display:grid;grid-template-columns:1fr 1.1fr;width:100%;max-width:1040px;min-height:660px;background:#fff;border:1px solid #e4e8f0;border-radius:24px;overflow:hidden;box-shadow:0 24px 80px #26344b12}.brand{padding:52px 40px;display:flex;flex-direction:column;justify-content:center;background:linear-gradient(150deg,#edf4ff,#f4edff)}.brand strong{font-size:30px;color:#1769d2}.brand h2{font-size:32px;line-height:1.25;margin:48px 0 16px}.brand p,p{color:#65738a}.content{padding:48px;align-self:center;min-width:0}h1{font-size:28px;line-height:1.3;margin:0 0 16px}p{overflow-wrap:anywhere}label{display:block;font-weight:700;margin:24px 0 8px}input{width:100%;padding:14px;border:1px solid #d5d9e0;border-radius:8px;font:24px monospace;letter-spacing:8px;text-align:center}button{width:100%;padding:14px;border:0;border-radius:9px;margin-top:20px;background:#a65bdd;color:#fff;font:700 15px Arial;cursor:pointer}.secondary{background:#eff3fa;color:#1769d2}.logout{background:transparent;color:#65738a;margin-top:8px}input:focus-visible,button:focus-visible{outline:2px solid #1975ef;outline-offset:3px}.status,.errors{padding:12px 16px;border-radius:8px;background:#eff6ff;color:#125875}.errors{background:#fff1f2;color:#a32020}.errors ul{margin:0;padding-left:18px}.hint{font-size:13px}@media(max-width:767px){body{padding:16px}.card{display:block;min-height:auto}.brand{padding:24px}.brand strong{font-size:24px}.brand h2,.brand p{display:none}.content{padding:28px 24px}h1{font-size:24px}}
    </style>
</head>
<body>
<main class="card">
    <aside class="brand"><strong>EncubadoraFIC</strong><h2>Un paso más para comenzar.</h2><p>Confirma tu correo institucional para acceder a la comunidad de la FIC.</p></aside>
    <section class="content" aria-labelledby="verify-title">
        <h1 id="verify-title">Verifica tu correo</h1>
        <p>Ingresa el código de seis dígitos enviado a <strong>{{ auth()->user()->email }}</strong>.</p>
        @if($deliveryError ?? null)<p class="errors" role="alert">{{ $deliveryError }}</p>@endif
        @if(session('status'))<p class="status" role="status">{{ session('status') }}</p>@endif
        @if($errors->any())<div class="errors" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ route('verification.code') }}">
            @csrf
            <label for="code">Código de verificación</label>
            <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" minlength="6" maxlength="6" required aria-describedby="code-help">
            <p class="hint" id="code-help">Vence en 10 minutos. Revisa también tu carpeta de correo no deseado.</p>
            <button type="submit">Verificar y entrar</button>
        </form>
        <form method="POST" action="{{ route('verification.code.resend') }}">@csrf<button class="secondary" type="submit">Reenviar código</button></form>
        <p class="hint">Puedes solicitar otro código después de un minuto.</p>
        <form method="POST" action="/logout">@csrf<button class="logout" type="submit">Cerrar sesión</button></form>
    </section>
</main>
</body>
</html>
