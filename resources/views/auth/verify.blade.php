<!doctype html><html lang="es"><meta charset="utf-8"><title>Verificar correo</title>
<h1>Verifica tu correo institucional</h1><p>Abre el enlace que enviamos a tu correo y después vuelve a <a href="/home">tu cuenta</a>.</p>
@if(session('status'))<p>Enlace enviado.</p>@endif
<form method="POST" action="/email/verification-notification">@csrf<button>Reenviar enlace</button></form>
<form method="POST" action="/logout">@csrf<button>Cerrar sesión</button></form></html>
