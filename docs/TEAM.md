# Trabajo en equipo

## División sugerida

| Área | Archivos propios | Contrato compartido |
|---|---|---|
| Backend/base de datos | `app/`, `database/`, `routes/api.php`, pruebas | `docs/openapi.json` |
| Web | `resources/views/`, `resources/css/`, `resources/js/`, rutas web | Modelos/policies y Fortify |
| Android | Repositorio o carpeta `android/` futura | API `/api/v1`, modelos JSON, errores y paginación |

La web ya tiene pantallas mínimas de autenticación Blade con Fortify. La aplicación Android y las pantallas web de proyectos están pendientes. Android puede comenzar por registro/login, catálogo/listado/detalle y estados de carga/error usando OpenAPI.

La web puede usar controladores con sesiones y modelos, pero debe aplicar las mismas Policies y validaciones. Si se agregan operaciones web equivalentes a las de la API, extraer su lógica a acciones/servicios compartidos; no copiar reglas a JavaScript. Sanctum está configurado exclusivamente para Bearer en `/api`; una sesión web sola no da acceso a estas rutas.

## Flujo Git

1. Crear un repositorio remoto privado del equipo y subir este proyecto (incluyendo `composer.lock`).
2. Cada integrante clona el repositorio, copia `.env.example` y sigue el README. Cada clon tiene su base local; no es necesario compartir contraseñas ni un servidor PostgreSQL público.
3. Usar ramas cortas: `feature/android-login`, `feature/web-projects`, `feature/project-search`.
4. Abrir pull requests hacia `main`; ejecutar formato y pruebas PostgreSQL. El workflow incluido hace esas comprobaciones en GitHub.
5. Todo cambio de endpoint debe actualizar OpenAPI, documentación y pruebas en el mismo PR. Coordinar cambios incompatibles antes de fusionar; usar una nueva versión cuando corresponda.

Para que todos consuman **la misma API remota**, falta elegir hosting y desplegar un entorno de desarrollo con HTTPS y SMTP. Compartir este código permite trabajar localmente desde ahora; no crea automáticamente un servidor accesible por Internet.

## Acuerdos iniciales

- IDs numéricos en rutas; no cambiar a slugs sin coordinar Android/web.
- Publicación/archivo mediante `status`; DELETE archiva.
- Todos los clientes muestran aviso de verificación pendiente ante 403 correspondiente.
- Tratar campos de texto como texto plano.
- No versionar `.env`, tokens, contraseñas, logs, `vendor/` ni datos personales.
- Migraciones compartidas se corrigen con nuevas migraciones.
