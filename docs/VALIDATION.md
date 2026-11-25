# Validación de la base inicial

Fecha: 9 de octubre de 2026.

## Entorno comprobado

- Proyecto inicialmente vacío; no había autenticación, migraciones ni datos previos.
- PHP local: 8.3.33. PHP dentro de Docker: 8.3.35.
- Laravel 13.35.0, Sanctum 4.3.3, Fortify 1.41.0; dependencias exactas versionadas en `composer.lock`.
- PostgreSQL 17 en Docker, base de desarrollo `incubadora`, base separada `incubadora_test`.
- Servicios app, db y mailpit levantados localmente.

## Resultados

| Comprobación | Resultado |
|---|---|
| Construcción de Docker | Correcta |
| Composer install dentro de Docker | Correcto, con CA local para inspección HTTPS |
| Migraciones en PostgreSQL nuevo y seeders | 16 migraciones aplicadas; 4 categorías y 6 tecnologías |
| `vendor/bin/phpunit -c phpunit.pgsql.xml` | **20 pruebas, 138 assertions, todas correctas** |
| `vendor/bin/phpunit -c phpunit.xml` | 19 aprobadas, 1 omitida por ser exclusiva de PostgreSQL; 136 assertions |
| Formato Laravel Pint | Correcto |
| Registro HTTP y transporte SMTP a Mailpit | Correcto |
| Abrir enlace firmado, iniciar sesión y cerrar sesión por HTTP | Correcto |
| JSON OpenAPI | Parseado correctamente; contrato escrito para las rutas v1 |
| Exclusión de secretos en Git | `.env`, logs, SQLite local y vendor ignorados |

La suite prueba dominios permitidos/engañosos, normalización, rol protegido, correo sin verificar, tokens reales/revocación, separación sesión/Bearer, recuperación de contraseña, throttling, contenido privado, permisos, reacciones, comentarios/respuestas, solicitudes, avances, reportes y CHECK de PostgreSQL.

## Correcciones verificadas durante la integración

- El `serve` de Laravel filtra variables de entorno al recargar; Docker usa `--no-reload` para conservar DB_HOST y el transporte de correo del contenedor.
- PHPUnit necesita sobrescribir tanto `env` como `server` para que las variables de Docker no prevalezcan. Antes de corregirlo, una corrida utilizó la base local recién creada y borró sus catálogos iniciales. Se restauraron con el seeder. No había datos previos del usuario.
- Hay un bloqueo en `tests/TestCase.php` **antes** de ejecutar migraciones de pruebas: solo permite `:memory:` o `incubadora_test`. La prueba PostgreSQL comprueba además `current_database()`. Se verificó que las 4 categorías de desarrollo permanecen antes y después de ejecutar la suite.
- La opción `--configuration` pasada mediante Artisan generaba una advertencia por duplicación; documentación y CI usan PHPUnit directamente para la configuración PostgreSQL.

## Repetir

```powershell
docker compose exec app vendor/bin/phpunit -c phpunit.pgsql.xml
docker compose exec app vendor/bin/phpunit -c phpunit.xml
docker compose exec app vendor/bin/pint --test
docker compose exec app php artisan migrate:status
./scripts/smoke.ps1
```

`smoke.ps1` es solo para desarrollo: crea una cuenta sintética institucional por corrida, usa el buzón local y no imprime contraseña ni token. Las cuentas sintéticas quedan en la base local. El script no envía mensajes a destinatarios externos cuando usa este Compose.

## Pendiente de entorno/equipo

- GitHub Actions está configurado, pero no se ejecutó en GitHub porque aún no existe remoto configurado.
- No se probó entrega a correo institucional real: requiere SMTP real y entorno HTTPS.
- No se realizó despliegue público, integración con Android ni diseño final de las pantallas de proyectos.
- Las pantallas Blade se probaron por HTTP y pruebas funcionales; no se realizó revisión visual en navegador ni pruebas en dispositivo Android.
- La configuración Docker es de desarrollo (servidor PHP integrado). El despliegue de producción requiere servidor apropiado, secretos, HTTPS, backups y configuración de correo.
