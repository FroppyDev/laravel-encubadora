# Correos de verificación con Brevo

Al abrir `/email/verify` con una sesión pendiente de verificación, la aplicación envía automáticamente un código de seis dígitos. Vence a los 10 minutos, admite cinco intentos y puede reenviarse después de 60 segundos. Un reenvío invalida el código anterior. El código se guarda como hash en la caché; no requiere migraciones. Al validarlo, se confirma el correo y se redirige a `/home`.

Los enlaces de verificación existentes y los clientes de la API siguen siendo compatibles. El registro puede enviar su enlace habitual, y la pantalla web envía el código al abrirse. Los usuarios ya verificados no reciben códigos nuevos.

## Activar entrega real

En Brevo, configura un remitente autorizado y obtén el usuario SMTP y una clave SMTP en **Configuración → SMTP y API → SMTP**. La clave SMTP no es la clave API ni la contraseña de tu cuenta.

En `api/laravel/.env`, configura estos valores (sustituye los marcadores localmente; no publiques la clave):

```dotenv
MAIL_MAILER=smtp
MAIL_SCHEME=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
DOCKER_MAIL_HOST=smtp-relay.brevo.com
DOCKER_MAIL_PORT=587
MAIL_USERNAME="TU_USUARIO_SMTP_DE_BREVO"
MAIL_PASSWORD="TU_CLAVE_SMTP_DE_BREVO"
MAIL_FROM_ADDRESS="TU_REMITENTE_AUTORIZADO_EN_BREVO"
MAIL_FROM_NAME="Incubadora FIC"
```

El transporte SMTP negocia STARTTLS en el puerto 587. Si tienes `MAIL_URL` configurado, elimínalo para que no sustituya estos valores.

Desde `api/laravel`, aplica los cambios:

```powershell
docker compose up -d app
docker compose exec app php artisan config:clear
docker compose restart app
docker compose exec app php artisan test --filter=EmailVerificationCodeTest
```

Para comprobar la entrega, inicia sesión con una cuenta institucional tuya sin verificar, abre la pantalla de verificación, revisa el correo y escribe el código. Puedes consultar el estado de entrega en los registros transaccionales de Brevo. No marques manualmente el correo como verificado para esta prueba.

Sin `DOCKER_MAIL_HOST` y `DOCKER_MAIL_PORT`, Docker conserva Mailpit: los mensajes se consultan en http://localhost:8025 y **no llegan al buzón real**. Las credenciales y el remitente de Brevo deben configurarse antes de activar el envío real.

Referencia oficial: https://help.brevo.com/hc/en-us/articles/7924908994450-Send-transactional-emails-using-Brevo-SMTP
