# API v1 · Guía de integración

El contrato importable en Postman, Insomnia o Swagger Editor es [openapi.json](openapi.json). Prefijo: `/api/v1`. IDs numéricos; el slug es informativo y no reemplaza al ID en las rutas.

## Conexión

- Equipo local: `http://localhost:8000/api/v1`.
- Emulador Android: `http://10.0.2.2:8000/api/v1`.
- Teléfono físico: requiere una URL accesible, por ejemplo un backend de desarrollo desplegado con HTTPS. Docker publica solo en localhost por defecto.
- Encabezados: `Accept: application/json`, `Content-Type: application/json` y, para rutas privadas, `Authorization: Bearer <token>`.
- Los errores usan `{ "message": "...", "errors": { "campo": ["..."] } }`; `errors` aparece en validaciones. 401: iniciar sesión; 403: permisos o verificación; 404: recurso no disponible; 409: conflicto; 422: corregir campos; 429: esperar `Retry-After`.
- Los recursos individuales usan `{ "data": {...} }`. Listados usan paginación Laravel con `data`, `current_page`, `last_page`, `total`, `next_page_url` y `prev_page_url`. Proyectos e interacciones: 20 por página. Catálogos: 100. Usar `?page=2`.
- No todos los endpoints incluyen relaciones o contadores; están descritos como propiedades opcionales en OpenAPI. Nunca se expone el correo del autor en un recurso público.
- Texto plano: escapar al mostrarlo en Blade o HTML. No interpretar comentarios/descripciones como HTML confiable.

## Flujo de acceso

1. `POST /register`: `name`, `email`, `password`, `password_confirmation`. Mínimo 10 caracteres. Solo el dominio completo configurado (`info.uas.edu.mx`), sin distinguir mayúsculas. Devuelve 201, usuario sin verificar y **sin token**.
2. `POST /login`: `email`, `password`, `device_name`. Devuelve `token`, `token_type: Bearer` y `data`. El token dura 30 días. Puede iniciar sesión un usuario no verificado, pero no participar.
3. Abrir el enlace del correo. Es una URL firmada que vence en 60 minutos; no requiere iniciar sesión en el navegador. Volver a la aplicación y consultar `GET /user` para actualizar `email_verified_at`.
4. `POST /email/verification-notification` reenvía el enlace al usuario autenticado (máximo 6/min).
5. `POST /logout` revoca solo el token usado y responde 204. Otros dispositivos mantienen su sesión.

Login tiene límite de 5/min por correo/IP; registro 10/min por IP; API 120/min por usuario o IP. Guardar el token en almacenamiento seguro del dispositivo apoyado en Android Keystore. No guardarlo en código, logs o URLs. El permiso de HTTP para el emulador debe limitarse al build de desarrollo; producción usa HTTPS.

La recuperación de contraseña está disponible mediante las pantallas web `/forgot-password` y `/reset-password/{token}`. Android puede abrir ese flujo en navegador; no se incluyó un segundo flujo de recuperación en `/api/v1`. Restablecer la contraseña revoca todos los tokens personales.

## Proyectos

```http
POST /api/v1/projects
Authorization: Bearer <token>
Content-Type: application/json

{
  "title": "Huerto inteligente",
  "summary": "Monitoreo de humedad para el huerto universitario",
  "description": "Construiremos sensores y una aplicación de seguimiento.",
  "category_id": 1,
  "technology_ids": [1, 2],
  "status": "draft"
}
```

Consultar IDs reales en `/categories` y `/technologies`. El servidor asigna propietario y slug. Crear proyectos exige correo verificado, incluso borradores. `PATCH /projects/{id}` con `{"status":"published"}` publica; con `archived` archiva. `DELETE /projects/{id}` también archiva sin borrar filas. Solo el propietario edita; moderadores pueden archivar.

`GET /projects?search=huerto&category_id=1&technology_id=2` devuelve únicamente publicados. `GET /me/projects` devuelve todos los proyectos propios. El detalle, comentarios y avances de borradores/archivados requieren token del propietario o moderador. Si envías `technology_ids` en una edición, reemplaza toda la selección; `[]` la vacía.

## Interacciones

Todas las escrituras requieren autenticación y correo verificado. Comentarios, likes y solicitudes nuevas solo se crean en proyectos publicados.

| Acción | Método y ruta | Cuerpo / regla |
|---|---|---|
| Listar comentarios | GET `/projects/{id}/comments` | Lista plana, `parent_id` enlaza respuesta |
| Comentar | POST `/projects/{id}/comments` | `body`, `parent_id` opcional |
| Editar comentario | PATCH `/comments/{id}` | `body`; autor o moderador |
| Borrar comentario | DELETE `/comments/{id}` | Borra también sus respuestas |
| Reaccionar | PUT `/projects/{id}/reaction` | `{"type":"like"}`; idempotente |
| Quitar reacción | DELETE `/projects/{id}/reaction` | Solo la propia |
| Solicitar colaboración | POST `/projects/{id}/collaboration-requests` | `message` opcional |
| Ver solicitudes | GET `/projects/{id}/collaboration-requests` | Dueño ve todas; otros solo la propia |
| Resolver solicitud | PATCH `/collaboration-requests/{id}` | Dueño: `accepted`/`rejected`; solicitante: `cancelled` |
| Listar avances | GET `/projects/{id}/updates` | Misma visibilidad del proyecto |
| Publicar avance | POST `/projects/{id}/updates` | `title`, `body`; solo dueño |
| Reportar | POST `/reports` | `reason`, `details` opcional, exactamente uno de `project_id`/`comment_id` |
| Listar reportes | GET `/reports` | Solo moderator/admin |
| Resolver reporte | PATCH `/reports/{id}` | `status`: `reviewed` o `dismissed`; solo moderator/admin |

Respuestas de comentarios: máximo dos niveles, padre del mismo proyecto, sin cambios de padre posteriores. Solicitudes: una fila por usuario/proyecto; tras rechazo/cancelación un nuevo POST reutiliza esa fila y vuelve a pending. Accepted/pending bloquea duplicados (409). Solo pending admite resolución. Aceptar una solicitud registra la decisión; no incorpora gestión de tareas o una tabla de membresías.

No existe carga de avatar/portada en esta etapa. Los campos de almacenamiento están reservados en la base, pero no se aceptan rutas arbitrarias del cliente. Tampoco se permite editar correo, roles o estado de verificación desde el perfil.
