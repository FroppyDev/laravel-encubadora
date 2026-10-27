# Incubadora FIC · Base compartida

Backend de la plataforma de proyectos estudiantiles. Laravel **13.35.0**, PHP **8.3+**, Sanctum **4.3.3**, Fortify **1.41.0** y PostgreSQL **17**. Versiones de PHP fijadas para el entorno Docker en `Dockerfile`; dependencias exactas en `composer.lock`.

Incluye registro institucional, correo de verificación, autenticación web mínima con Blade, tokens para Android y API de proyectos, categorías, tecnologías, comentarios, likes, solicitudes, avances y moderación. El diseño completo de las pantallas web y la app Android son trabajo posterior.

## Inicio con Docker

Requisitos: Git y Docker Desktop funcionando con contenedores Linux. No necesitas instalar PHP, Composer, Node ni PostgreSQL en tu equipo para trabajar en la API.

Desde PowerShell, dentro del proyecto:

```powershell
Copy-Item .env.example .env
docker compose build app
docker compose up -d db mailpit
docker compose run --rm app composer install --no-interaction --prefer-dist
docker compose run --rm app php artisan key:generate
docker compose run --rm app php artisan migrate --seed
docker compose up -d app
```

Copiar `.env` y generar clave solo en la primera instalación. Nunca reemplazar `.env` de otro entorno ni regenerar claves de una aplicación en uso.

- Web: http://localhost:8000/login
- API: http://localhost:8000/api/v1/projects
- Buzón local Mailpit: http://localhost:8025
- PostgreSQL desde un cliente SQL local: host `127.0.0.1`, puerto `5433`; usuario/base/clave de tu `.env`.

La contraseña de `.env.example` es únicamente para desarrollo local. Los puertos se publican en localhost. Docker usa `db:5432` internamente y un volumen persistente para PostgreSQL; `vendor` vive en otro volumen. `docker compose down` detiene servicios conservando datos. **No uses `down -v` si quieres conservar la base.**

## Verificar correo

Registrar una dirección con dominio `@info.uas.edu.mx`, abrir Mailpit, entrar al correo y abrir el enlace firmado. Después volver a `/home` o consultar `GET /api/v1/user`. Mailpit captura mensajes localmente: no los entrega a cuentas reales y no acredita que seas dueño de la dirección en producción.

En producción configurar SMTP real (`MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_SCHEME`, `MAIL_FROM_ADDRESS`) y `APP_URL` con HTTPS. Si ejecutas PHP fuera de Docker, `.env.example` usa el mailer `log`: los enlaces aparecen en `storage/logs/laravel.log`; ese archivo es privado. Docker sobrescribe el mailer para usar Mailpit.

## Pruebas

Pruebas rápidas con SQLite en memoria:

```powershell
docker compose exec app php artisan test
```

Pruebas reales de PostgreSQL (crear la base de pruebas una sola vez):

```powershell
docker compose exec db createdb -U incubadora incubadora_test
docker compose exec app vendor/bin/phpunit -c phpunit.pgsql.xml
```

Si cambiaste `DB_USERNAME`, usa ese usuario en `createdb`. Si la base ya existe, omite el primer comando. `phpunit.pgsql.xml` fuerza `incubadora_test`; las pruebas reconstruyen esta base. **No usar esa base para información que quieras conservar.**

```powershell
docker compose exec app vendor/bin/pint --test
docker compose exec app php artisan route:list
```

El workflow `.github/workflows/tests.yml` ejecuta formato y pruebas sobre PostgreSQL al subir cambios o abrir PR. Ver [resultados de validación](docs/VALIDATION.md).

## Integración y equipo

- [Guía y ejemplos de la API](docs/API.md).
- [Contrato OpenAPI para importar en Postman/Insomnia](docs/openapi.json).
- [Esquema, relaciones y reglas de borrado](docs/DATABASE.md).
- [División de trabajo y flujo Git](docs/TEAM.md).

Android usa `http://10.0.2.2:8000/api/v1` desde el emulador. Los dispositivos físicos y equipos remotos requieren un backend accesible. No hay despliegue público ni repositorio remoto configurado todavía.

## Organización

`app/Models`: relaciones. `app/Policies`: permisos sobre contenido. `app/Http/Requests`: validaciones de registro/proyectos. `app/Http/Controllers/Api`: endpoints. `app/Actions/Fortify`: autenticación web oficial. `database/migrations`: esquema versionado. `database/seeders`: catálogos repetibles, sin cuentas precreadas. `resources/views/auth`: pantallas iniciales. `tests/Feature`: casos de integración y seguridad.

Fortify aporta el backend oficial de autenticación; se agregaron vistas Blade simples sin depender de Node. No se instaló un frontend completo ni se duplicó la autenticación de Laravel. No se implementan pagos, chat, archivos, gestión avanzada de tareas o membresías de equipo.

## Configuración adicional

`INSTITUTIONAL_EMAIL_DOMAIN` centraliza el dominio admitido. `CORS_ALLOWED_ORIGINS` acepta orígenes exactos separados por comas si se incorpora un frontend en otro origen. Blade en el mismo origen y Android nativo no requieren habilitar CORS. `APP_DEBUG=false` evita devolver detalles internos por defecto.

Si cambias configuración: `docker compose exec app php artisan config:clear` y `docker compose restart app`. El servidor utiliza `--no-reload` para conservar las variables de Docker. Los correos se envían de forma síncrona en desarrollo (`QUEUE_CONNECTION=sync`). Hay tablas de colas preparadas, pero si habilitas colas debes ejecutar también un worker.

## Problemas locales de certificados

Si Composer falla con `unable to get local issuer certificate`, revisa la inspección HTTPS de antivirus/proxy e instala su CA de confianza. No desactives TLS. En esta máquina se usó un PEM temporal con certificados de confianza de Windows y `COMPOSER_CAFILE`; ese archivo es específico del equipo y no forma parte del repositorio. Puede montarse solo durante la instalación:

```powershell
$certPath = 'C:\ruta\al\ca-confiable.pem'
docker compose run --rm -v "${certPath}:/tmp/dev-ca.pem:ro" -e COMPOSER_CAFILE=/tmp/dev-ca.pem app composer install --no-interaction --prefer-dist
```
