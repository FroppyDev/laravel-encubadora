# Panel de administración (HU06)

Acceso: /admin o «Panel de administración» en el menú de /home. Requiere sesión, correo verificado y role=admin. No se crean cuentas ni se cambian roles automáticamente.

Aplicar la migración: `docker compose exec app php artisan migrate`.
Pruebas: `docker compose exec app php artisan test`.

El panel permite buscar, filtrar, crear, editar, desactivar y reactivar categorías. El slug es único; no se eliminan categorías ni sus referencias.

GET /api/v1/categories devuelve únicamente categorías activas para selectores web/Android. Cada categoría incluye is_active (boolean). POST /api/v1/projects y PUT/PATCH /api/v1/projects/{project} rechazan nuevas asignaciones inactivas (422). Se permite conservar la categoría inactiva actual al editar un proyecto. La consulta de proyectos conserva su categoría independientemente del estado.

Rutas web con sesión y CSRF: GET /admin, GET/POST /admin/categories, PUT /admin/categories/{category}, PATCH /admin/categories/{category}/status (is_active boolean obligatorio). Toda modificación requiere rol admin en el servidor; no hay endpoint de eliminación.

Comprobación manual: ingresar con una cuenta admin verificada, crear y editar una categoría, desactivarla y comprobar que desaparece de GET /api/v1/categories y que los proyectos anteriores siguen visibles. Repetir el acceso como estudiante/moderador (403).