param([string]$BaseUrl = 'http://localhost:8000', [string]$MailpitUrl = 'http://localhost:8025')

# Development only: creates one local smoke account; never prints its password/token.
$ErrorActionPreference = 'Stop'
$health = Invoke-WebRequest -Uri "$BaseUrl/up"
$catalog = Invoke-RestMethod -Uri "$BaseUrl/api/v1/categories"
if ($catalog.total -lt 1) { throw 'Faltan los catálogos: ejecuta php artisan db:seed' }
$smokeEmail = 'smoke.' + [guid]::NewGuid().ToString('N').Substring(0,12) + '@info.uas.edu.mx'
$smokePassword = [guid]::NewGuid().ToString('N') + '!Aa1'
$payload = @{name='Prueba técnica local';email=$smokeEmail;password=$smokePassword;password_confirmation=$smokePassword} | ConvertTo-Json
$registered = Invoke-RestMethod -Uri "$BaseUrl/api/v1/register" -Method Post -ContentType 'application/json' -Body $payload
$mail = $null
for ($attempt = 0; $attempt -lt 10 -and !$mail; $attempt++) {
    $messages = Invoke-RestMethod -Uri "$MailpitUrl/api/v1/messages"
    $mail = $messages.messages | Where-Object { @($_.To | ForEach-Object { $_.Address }) -contains $smokeEmail } | Select-Object -First 1
    if (!$mail) { Start-Sleep -Milliseconds 300 }
}
if (!$mail) { throw 'No llegó correo a Mailpit' }
$message = Invoke-RestMethod -Uri ("$MailpitUrl/api/v1/message/" + $mail.ID)
$verifyUrl = [regex]::Match($message.Text, [regex]::Escape($BaseUrl) + '/api/v1/email/verify/[^\s]+').Value.TrimEnd(')')
if (!$verifyUrl) { throw 'No se encontró enlace de verificación' }
Invoke-RestMethod -Uri $verifyUrl | Out-Null
$login = Invoke-RestMethod -Uri "$BaseUrl/api/v1/login" -Method Post -ContentType 'application/json' -Body (@{email=$smokeEmail;password=$smokePassword;device_name='Smoke local'} | ConvertTo-Json)
$headers = @{Authorization=('Bearer ' + $login.token)}
$profile = Invoke-RestMethod -Uri "$BaseUrl/api/v1/user" -Headers $headers
if (!$profile.data.email_verified_at) { throw 'El correo no quedó verificado' }
Invoke-RestMethod -Uri "$BaseUrl/api/v1/logout" -Method Post -Headers $headers | Out-Null
[pscustomobject]@{Health=$health.StatusCode;Categories=$catalog.total;MailCaptured=[bool]$mail;Verified=[bool]$profile.data.email_verified_at;Logout='OK';LocalSmokeUserId=$registered.data.id} | ConvertTo-Json
