# Historias de usuario y plan de siete sprints

Proyecto: Incubadora FIC. Periodo propuesto: 21 de septiembre a 12 de diciembre de 2026.

## Supuestos y alcance

Se proponen **20 historias**, ordenadas por dependencias y valor para el MVP. La referencia es MVPEncubadora.pdf (alcance: pp. 6–8; API: pp. 13–15; pruebas y cierre: pp. 17–24), contrastada con el backend ya construido. Este documento es una propuesta de planificación, no evidencia de que se haya trabajado en fechas pasadas. Al 9 de octubre, los primeros dos periodos ya transcurrieron: el equipo debe registrar su avance real y replanificar pendientes sin inventar entregas.

El intervalo tiene 83 días naturales: no caben siete sprints completos de dos semanas. Se usan dos de 7 días, cuatro de 14 y uno final de 13. Las fechas son ventanas calendario, no días laborales ni una obligación de trabajar fines de semana. La duración variable responde al calendario académico solicitado. La capacidad y los puntos deberán estimarse con el equipo antes de comprometer el alcance.

Las historias de estudiante se entregan en web y Android. Administración de categorías, usuarios y moderación tendrá panel web; sus controles de servidor afectan ambos clientes. Cada historia incluye tareas identificadas y criterios de aceptación. El backend existente se reutiliza y verifica; una API funcionando no significa que la historia completa esté terminada.

## Decisiones propuestas para reconciliar PDF y código

- Comunidad privada: seguir el PDF y exigir cuenta verificada para consultar contenido. Actualmente hay consultas públicas; HU02 contempla cambiarlo y actualizar OpenAPI.
- Retirar publicaciones: mantener el archivo existente como retirada del catálogo, conservando datos. Si se exige eliminación lógica independiente, debe acordarse y estimarse como ajuste; no se presume implementada.
- Multimedia: acotar a una portada por proyecto (JPG/PNG/WebP, hasta 5 MB, valores propuestos). Galerías, video y archivos generales no se incluyen. Avatar puede quedar para una iteración posterior.
- Estados: conservar draft/published/archived para publicación. Los ejemplos idea/en desarrollo/pausado/finalizado del PDF son una dimensión distinta y no se agregan sin definirla.
- Mantener rutas efectivas de OpenAPI (por ejemplo PATCH /user y PUT /projects/{id}/reaction); los endpoints del PDF son iniciales. Documentar las nuevas rutas de archivos, avances y administración.
- No incluir pagos, chat, notificaciones push, iOS, analítica avanzada ni administración avanzada de equipos.

## Calendario y entregables

| Sprint | Fechas 2026 | Días naturales | Objetivo | Historias |
|---|---|---:|---|---|
| 1 | 21–27 de septiembre | 7 | Acceso institucional | HU01–HU03 |
| 2 | 28 de septiembre–4 de octubre | 7 | Cuenta y clasificación | HU04–HU06 |
| 3 | 5–18 de octubre | 14 | Publicación y gestión propia | HU07–HU09 |
| 4 | 19 de octubre–1 de noviembre | 14 | Descubrimiento | HU10–HU12 |
| 5 | 2–15 de noviembre | 14 | Retroalimentación y apoyo | HU13–HU14 |
| 6 | 16–29 de noviembre | 14 | Colaboración y seguimiento | HU15–HU17 |
| 7 | 30 de noviembre–12 de diciembre | 13 | Moderación y entrega | HU18–HU20 |

## Backlog ordenado

| Orden | Historia | Prioridad | Sprint | Dependencias |
|---|---|---|---:|---|
| 1 | HU01 · Registro institucional | Alta | 1 | Ninguna |
| 2 | HU02 · Verificación y acceso institucional | Alta | 1 | HU01 |
| 3 | HU03 · Inicio y cierre de sesión | Alta | 1 | HU01 |
| 4 | HU04 · Recuperación de contraseña | Media | 2 | HU01, HU03 |
| 5 | HU05 · Perfil propio | Media | 2 | HU02, HU03 |
| 6 | HU06 · Administración de categorías | Media | 2 | HU03 y rol admin |
| 7 | HU07 · Creación y publicación de proyectos | Alta | 3 | HU02, HU05, HU06 |
| 8 | HU08 · Gestión de publicaciones propias | Alta | 3 | HU07 |
| 9 | HU09 · Imágenes del proyecto | Media | 3 | HU07, HU08 |
| 10 | HU10 · Catálogo de proyectos | Alta | 4 | HU02, HU07 |
| 11 | HU11 · Búsqueda y filtros | Alta | 4 | HU10 |
| 12 | HU12 · Detalle de proyecto | Alta | 4 | HU07, HU09, HU10 |
| 13 | HU13 · Comentarios y respuestas | Alta | 5 | HU02, HU12 |
| 14 | HU14 · Reacciones de apoyo | Media | 5 | HU02, HU12 |
| 15 | HU15 · Solicitud de colaboración | Alta | 6 | HU02, HU12 |
| 16 | HU16 · Resolución de solicitudes | Alta | 6 | HU15 |
| 17 | HU17 · Actualizaciones del proyecto | Media | 6 | HU08, HU12 |
| 18 | HU18 · Reporte de contenido | Alta | 7 | HU12, HU13 |
| 19 | HU19 · Moderación de contenido | Alta | 7 | HU18 |
| 20 | HU20 · Administración de cuentas y roles | Alta | 7 | HU03, HU19 para reutilizar auditoría |

Alta: núcleo funcional, acceso o control de la comunidad. Media: soporte relevante del MVP. El orden considera dependencias, por lo que una historia media puede preceder a una alta.

## Sprint 1: Acceso institucional

**Fechas:** 21–27 de septiembre de 2026.

**Trabajo técnico transversal:** Repositorio remoto y acuerdos; revisar Docker, migraciones y OpenAPI existentes; arrancar proyectos Web/Android.

### HU01 · Registro institucional

**Historia:** Como estudiante, quiero registrarme con mi correo institucional para pertenecer a la comunidad de la facultad.

**Prioridad:** Alta. **Dependencias:** Ninguna.

**Base existente / pendiente:** Registro API y web básico existentes; integrar Android y validar ambos clientes.

**Tareas:**

- HU01-T1 · Backend: reutilizar el registro, normalización de correo y validación del dominio exacto info.uas.edu.mx.
- HU01-T2 · Web/Android: implementar formularios con nombre, correo, contraseña y confirmación; mostrar errores por campo.
- HU01-T3 · QA: probar correo válido, dominio engañoso, duplicados y bloqueo de asignación de roles.

**Criterio de aceptación:** Un correo válido crea una cuenta student sin verificar; externos/duplicados se rechazan y nunca se acepta un rol enviado por el cliente.

### HU02 · Verificación y acceso institucional

**Historia:** Como estudiante, quiero verificar mi correo para acceder a las funciones reservadas a la comunidad.

**Prioridad:** Alta. **Dependencias:** HU01.

**Base existente / pendiente:** Enlaces y bloqueo de escrituras existentes; falta restringir consultas para ajustarse al PDF.

**Tareas:**

- HU02-T1 · Backend: reutilizar enlaces firmados, vencimiento y reenvío; exigir autenticación/verificación también al consultar el contenido de la comunidad.
- HU02-T2 · Web/Android: mostrar estado pendiente, reenviar correo y actualizar el perfil después de verificar.
- HU02-T3 · QA: probar firma alterada, enlace vencido, reenvíos limitados y acceso de invitados/no verificados.

**Criterio de aceptación:** Solo cuentas verificadas acceden al contenido privado; un enlace inválido no verifica la cuenta. Login y reenvío siguen disponibles para cuentas pendientes.

### HU03 · Inicio y cierre de sesión

**Historia:** Como estudiante, quiero iniciar y cerrar sesión para acceder de forma segura a mi cuenta.

**Prioridad:** Alta. **Dependencias:** HU01.

**Base existente / pendiente:** Sesiones web y tokens API existentes; integrar Android.

**Tareas:**

- HU03-T1 · Backend: validar sesiones Fortify y tokens Sanctum, expiración y límites de intentos.
- HU03-T2 · Web/Android: integrar ingreso, almacenamiento seguro del token y cierre de sesión.
- HU03-T3 · QA: comprobar credenciales incorrectas, rutas protegidas y revocación del token actual.

**Criterio de aceptación:** Credenciales válidas permiten entrar; logout invalida la credencial usada. Ningún cliente almacena contraseñas o tokens en logs.

## Sprint 2: Cuenta y clasificación

**Fechas:** 28 de septiembre–4 de octubre de 2026.

**Trabajo técnico transversal:** Integración temprana Web/Android; desplegar staging mínimo con HTTPS y correo de pruebas.

### HU04 · Recuperación de contraseña

**Historia:** Como estudiante, quiero restablecer mi contraseña para recuperar el acceso a mi cuenta.

**Prioridad:** Media. **Dependencias:** HU01, HU03.

**Base existente / pendiente:** Flujo web existente; Android puede abrirlo en navegador.

**Tareas:**

- HU04-T1 · Backend: reutilizar recuperación oficial, enlaces temporales y revocación de tokens al restablecer.
- HU04-T2 · Web/Android: añadir acceso al flujo desde login; Android abre el formulario web seguro.
- HU04-T3 · QA: probar enlace válido, vencido y reutilizado; comprobar que la contraseña anterior deja de funcionar.

**Criterio de aceptación:** El enlace válido permite cambiar la contraseña; los enlaces vencidos/reutilizados fallan y se revocan los tokens móviles anteriores.

### HU05 · Perfil propio

**Historia:** Como estudiante, quiero consultar y editar mi nombre y biografía para presentarme ante otros participantes.

**Prioridad:** Media. **Dependencias:** HU02, HU03.

**Base existente / pendiente:** GET/PATCH de perfil existentes; pantallas pendientes.

**Tareas:**

- HU05-T1 · Backend: reutilizar lectura/edición de perfil y aceptar únicamente los campos permitidos.
- HU05-T2 · Web/Android: crear vista de perfil y formulario con confirmación de guardado.
- HU05-T3 · QA: verificar persistencia entre clientes y rechazo de cambios de rol, correo o verificación.

**Criterio de aceptación:** Los cambios propios se conservan y aparecen en ambos clientes; no es posible editar permisos o perfiles ajenos.

### HU06 · Administración de categorías

**Historia:** Como administrador, quiero crear, editar y desactivar categorías para organizar las publicaciones.

**Prioridad:** Media. **Dependencias:** HU03 y rol admin.

**Base existente / pendiente:** Consulta y seeders existentes; administración y desactivación nuevas.

**Tareas:**

- HU06-T1 · Backend/BD: agregar administración exclusiva de admin y migración de categoría activa/inactiva sin romper proyectos existentes.
- HU06-T2 · Web: construir un panel básico; Web/Android deben excluir categorías inactivas de nuevas selecciones.
- HU06-T3 · QA: probar slug único, permisos y conservación de referencias al desactivar una categoría.

**Criterio de aceptación:** Solo admin modifica categorías; una categoría inactiva no acepta nuevas asignaciones y los proyectos previos siguen siendo consultables.

## Sprint 3: Publicación y gestión propia

**Fechas:** 5–18 de octubre de 2026.

**Trabajo técnico transversal:** Agregar almacenamiento de imágenes y respaldo; trabajar mediante migraciones nuevas.
S
### HU07 · Creación y publicación de proyectos

**Historia:** Como estudiante verificado, quiero registrar y publicar un proyecto para dar a conocer mi propuesta.

**Prioridad:** Alta. **Dependencias:** HU02, HU05, HU06.

**Base existente / pendiente:** API y esquema existentes; formularios y flujo completo pendientes.

**Tareas:**

- HU07-T1 · Backend: reutilizar creación con título, resumen, descripción, categoría y tecnologías; mantener propietario asignado por el servidor.
- HU07-T2 · Web/Android: construir formulario para guardar borrador y publicar; usar los catálogos reales.
- HU07-T3 · QA: probar campos obligatorios, tecnologías válidas, publicación y privacidad de borradores.

**Criterio de aceptación:** El proyecto se guarda con su creador correcto; solo los publicados se muestran en el catálogo de la comunidad.

### HU08 · Gestión de publicaciones propias

**Historia:** Como creador, quiero editar y retirar mis publicaciones para mantener actualizada la información que comparto.

**Prioridad:** Alta. **Dependencias:** HU07.

**Base existente / pendiente:** Edición y archivo existentes; falta experiencia de usuario.

**Tareas:**

- HU08-T1 · Backend: reutilizar edición/archivo y endpoint de proyectos propios; conservar las Policies de propiedad.
- HU08-T2 · Web/Android: implementar Mis proyectos, edición y confirmación para retirar una publicación.
- HU08-T3 · QA: comprobar que no se modifican proyectos ajenos y que archivar conserva comentarios y solicitudes.

**Criterio de aceptación:** El propietario puede editar y retirar su proyecto del catálogo. Para este plan, retirar significa archivar; no se implementa borrado físico.

### HU09 · Imágenes del proyecto

**Historia:** Como creador, quiero agregar una portada a mi proyecto para comunicar visualmente mi propuesta.

**Prioridad:** Media. **Dependencias:** HU07, HU08.

**Base existente / pendiente:** Nueva funcionalidad; solo existe un campo reservado en la base.

**Tareas:**

- HU09-T1 · Backend: agregar carga/reemplazo de portada con formatos y tamaño máximo documentados; autorizar propietario y lectura según visibilidad.
- HU09-T2 · Web/Android: implementar selector de imagen, vista previa, carga y manejo de errores.
- HU09-T3 · QA: probar archivos inválidos, exceso de tamaño, reemplazo, acceso a imágenes privadas y almacenamiento.

**Criterio de aceptación:** Una imagen admitida se conserva y se muestra en ambos clientes; archivos inválidos y cargas sobre proyectos ajenos se rechazan. Alcance propuesto: una portada JPG/PNG/WebP, hasta 5 MB.

## Sprint 4: Descubrimiento

**Fechas:** 19 de octubre–1 de noviembre de 2026.

**Trabajo técnico transversal:** Revisión de experiencia de usuario, paginación, consultas y permisos de lectura.

### HU10 · Catálogo de proyectos

**Historia:** Como estudiante verificado, quiero consultar un catálogo para descubrir iniciativas de la facultad.

**Prioridad:** Alta. **Dependencias:** HU02, HU07.

**Base existente / pendiente:** Listado paginado existente; integración visual pendiente.

**Tareas:**

- HU10-T1 · Backend: reutilizar paginación y orden de proyectos publicados con acceso institucional.
- HU10-T2 · Web/Android: implementar tarjetas, navegación, carga, estado vacío y reintento.
- HU10-T3 · QA: comprobar paginación, ocultamiento de borradores/archivados y consistencia entre clientes.

**Criterio de aceptación:** Se muestran únicamente proyectos publicados a usuarios autorizados, con paginación y sin duplicados al navegar.

### HU11 · Búsqueda y filtros

**Historia:** Como estudiante, quiero buscar y filtrar proyectos para encontrar propuestas relacionadas con mis intereses.

**Prioridad:** Alta. **Dependencias:** HU10.

**Base existente / pendiente:** Filtros de API existentes; controles de interfaz pendientes.

**Tareas:**

- HU11-T1 · Backend: validar búsqueda por título/resumen y filtros combinables por categoría y tecnología.
- HU11-T2 · Web/Android: incorporar buscador, filtros, limpieza y conservación de criterios al paginar.
- HU11-T3 · QA: probar combinaciones, búsqueda sin resultados y exclusión de proyectos privados.

**Criterio de aceptación:** Los resultados satisfacen los filtros seleccionados; limpiar filtros restaura el catálogo y paginar conserva la búsqueda.

### HU12 · Detalle de proyecto

**Historia:** Como estudiante, quiero consultar los detalles de una iniciativa para decidir si deseo apoyarla o colaborar.

**Prioridad:** Alta. **Dependencias:** HU07, HU09, HU10.

**Base existente / pendiente:** Detalle API existente; pantallas pendientes.

**Tareas:**

- HU12-T1 · Backend: revisar respuesta con autor público, descripción, categoría, tecnologías, portada y enlaces permitidos.
- HU12-T2 · Web/Android: diseñar detalle y accesos a las interacciones que se incorporarán después.
- HU12-T3 · QA: verificar permisos por ID, enlaces válidos, textos escapados y ausencia de datos privados del autor.

**Criterio de aceptación:** El detalle muestra la información del proyecto sin revelar correo privado, contraseñas o tokens; se rechaza el acceso a recursos no autorizados.

## Sprint 5: Retroalimentación y apoyo

**Fechas:** 2–15 de noviembre de 2026.

**Trabajo técnico transversal:** Regresión de acceso/publicación; cerrar defectos de integración y accesibilidad.

### HU13 · Comentarios y respuestas

**Historia:** Como estudiante, quiero comentar y responder para aportar preguntas y retroalimentación al proyecto.

**Prioridad:** Alta. **Dependencias:** HU02, HU12.

**Base existente / pendiente:** API de comentarios existente; interfaces pendientes.

**Tareas:**

- HU13-T1 · Backend: reutilizar creación, edición, borrado y respuestas; mantener máximo dos niveles y padre del mismo proyecto.
- HU13-T2 · Web/Android: construir lista paginada y formularios para comentar, responder y gestionar comentarios propios.
- HU13-T3 · QA: probar autoría, respuestas inválidas, moderación, texto malicioso y límites de solicitudes.

**Criterio de aceptación:** Un usuario verificado comenta proyectos publicados; solo autor o moderador autorizado modifica/elimina comentarios y no hay respuestas cruzadas.

### HU14 · Reacciones de apoyo

**Historia:** Como estudiante, quiero dar o retirar un like para expresar apoyo a un proyecto.

**Prioridad:** Media. **Dependencias:** HU02, HU12.

**Base existente / pendiente:** API idempotente existente; controles y contador pendientes.

**Tareas:**

- HU14-T1 · Backend: reutilizar PUT/DELETE de reacción y restricción única; agregar al detalle un indicador de si el usuario actual ya reaccionó y documentarlo en OpenAPI.
- HU14-T2 · Web/Android: implementar botón de apoyo, estado propio y contador actualizado.
- HU14-T3 · QA: probar clics repetidos, reintentos de red y retiro de reacción sin afectar otros usuarios.

**Criterio de aceptación:** Un usuario aporta como máximo un like por proyecto; repetir la petición no duplica registros y retirarlo actualiza el contador.

## Sprint 6: Colaboración y seguimiento

**Fechas:** 16–29 de noviembre de 2026.

**Trabajo técnico transversal:** Probar un flujo completo entre dos cuentas usando clientes distintos; preparar datos y guion de aceptación.

### HU15 · Solicitud de colaboración

**Historia:** Como estudiante, quiero solicitar colaborar y consultar mi solicitud para expresar mi interés en un proyecto.

**Prioridad:** Alta. **Dependencias:** HU02, HU12.

**Base existente / pendiente:** API existente; flujo de interfaz pendiente.

**Tareas:**

- HU15-T1 · Backend: reutilizar solicitud, consulta propia y cancelación; impedir solicitudes al propio proyecto y duplicados activos.
- HU15-T2 · Web/Android: crear formulario con mensaje, estado de solicitud y acción de cancelar.
- HU15-T3 · QA: comprobar privacidad, duplicados y reenvío permitido después de rechazo/cancelación.

**Criterio de aceptación:** El solicitante ve y cancela solo su solicitud pendiente; no puede solicitar en su propio proyecto ni mantener solicitudes activas duplicadas.

### HU16 · Resolución de solicitudes

**Historia:** Como creador, quiero aceptar o rechazar solicitudes para decidir quién puede colaborar en mi proyecto.

**Prioridad:** Alta. **Dependencias:** HU15.

**Base existente / pendiente:** Transiciones y permisos API existentes; bandeja pendiente.

**Tareas:**

- HU16-T1 · Backend: reutilizar listado del propietario y transiciones transaccionales pending/accepted/rejected.
- HU16-T2 · Web/Android: construir bandeja por proyecto y acciones de aceptar/rechazar con confirmación.
- HU16-T3 · QA: probar acceso ajeno, decisiones concurrentes y actualización visible para el solicitante.

**Criterio de aceptación:** Solo el propietario resuelve solicitudes pendientes. La decisión persiste y aparece al solicitante; no incluye chat ni gestión avanzada de equipos.

### HU17 · Actualizaciones del proyecto

**Historia:** Como creador, quiero publicar, corregir y retirar avances para informar sobre la evolución de mi proyecto.

**Prioridad:** Media. **Dependencias:** HU08, HU12.

**Base existente / pendiente:** Crear/listar existentes; editar/eliminar pendientes según el PDF.

**Tareas:**

- HU17-T1 · Backend: reutilizar crear/listar y agregar edición/eliminación de avances con validaciones y autorización del propietario.
- HU17-T2 · Web/Android: construir historial y formularios de publicación/edición, con confirmación de eliminación.
- HU17-T3 · QA: probar autoría, orden del historial, persistencia y visibilidad ligada al proyecto.

**Criterio de aceptación:** Solo el propietario gestiona los avances de su proyecto; otros usuarios autorizados los consultan y las modificaciones se reflejan en ambos clientes.

## Sprint 7: Moderación y entrega

**Fechas:** 30 de noviembre–12 de diciembre de 2026.

**Trabajo técnico transversal:** Reservar 10–12 de diciembre para aceptación, regresión, restauración de respaldo, documentación, APK y demostración; no incorporar funciones nuevas en ese cierre.

### HU18 · Reporte de contenido

**Historia:** Como estudiante, quiero reportar un proyecto o comentario para señalar contenido inapropiado.

**Prioridad:** Alta. **Dependencias:** HU12, HU13.

**Base existente / pendiente:** API y restricciones existentes; interfaces pendientes.

**Tareas:**

- HU18-T1 · Backend: reutilizar reporte con un único objetivo, motivo y detalles; limitar repeticiones.
- HU18-T2 · Web/Android: incorporar Reportar, selección de motivo y confirmación de envío.
- HU18-T3 · QA: probar objetivo inexistente/doble, duplicados y que reportar no borra contenido automáticamente.

**Criterio de aceptación:** El reporte apunta exactamente a un objetivo visible, queda pendiente y no se duplica para el mismo reportante y contenido.

### HU19 · Moderación de contenido

**Historia:** Como moderador, quiero revisar y resolver reportes para mantener una comunidad respetuosa.

**Prioridad:** Alta. **Dependencias:** HU18.

**Base existente / pendiente:** API de revisión y permisos existentes; panel y auditoría pendientes.

**Tareas:**

- HU19-T1 · Backend/BD: reutilizar revisión de reportes y acciones de moderación; registrar actor, fecha, objetivo y acción sin secretos.
- HU19-T2 · Web: crear panel de reportes con detalle, revisión/desestimación y retiro autorizado de contenido.
- HU19-T3 · QA: comprobar acceso por rol, trazabilidad y efectos de archivar un proyecto o eliminar un comentario.

**Criterio de aceptación:** Solo moderator/admin resuelve reportes; la decisión registra quién y cuándo. La acción sobre el contenido es explícita y queda auditada.

### HU20 · Administración de cuentas y roles

**Historia:** Como administrador, quiero activar o suspender cuentas y asignar roles para controlar el acceso a la plataforma.

**Prioridad:** Alta. **Dependencias:** HU03, HU19 para reutilizar auditoría.

**Base existente / pendiente:** Nueva administración; el campo role ya existe.

**Tareas:**

- HU20-T1 · Backend/BD: agregar estado activo y endpoints exclusivos de admin; suspensión bloquea login, sesiones y tokens existentes.
- HU20-T2 · Web: construir lista de usuarios y controles de estado/rol; impedir dejar al sistema sin administrador activo.
- HU20-T3 · QA: probar escalamiento de privilegios, suspensión/reactivación, revocación y auditoría.

**Criterio de aceptación:** Solo admin cambia roles/estado; una cuenta suspendida no accede ni con credenciales previas, y cada cambio queda registrado.

## Organización sugerida del equipo

El PDF identifica cinco integrantes. Sin asignar nombres ni suponer disponibilidad, se sugieren cinco frentes: backend/BD; web; Android; integración y archivos; QA/documentación/despliegue. QA y revisiones son responsabilidad compartida, no de una sola persona. Las tareas Web/Android pueden separarse en dos tarjetas manteniendo el mismo ID de historia.

No dividir sprints por tecnología: cada sprint debe terminar con funciones integradas. En Sprint 1 revisar la base ya creada; no reconstruir autenticación o tablas. En Sprint 2 disponer de staging para integrar temprano. Preparar pruebas, auditoría y despliegue durante todo el periodo, no únicamente al final.

## Definición de terminado para cada historia

- Criterio de aceptación demostrado y validación del responsable del producto.
- Backend, base de datos y clientes correspondientes integrados, con datos persistentes y consistentes.
- Autorización comprobada en servidor; pruebas positivas, negativas y de acceso ajeno.
- Pruebas automáticas pertinentes aprobadas y comprobación manual en web/Android cuando corresponda.
- Estados de carga, vacío y error implementados; interfaz utilizable y textos claros.
- OpenAPI, migraciones e instrucciones actualizadas; revisión de código y cambios integrados en la rama compartida.
- Demostración en staging. Si solo está lista la API, la historia queda parcial, no terminada.

## Cierre del sprint 7

Antes del 10 de diciembre deben estar integradas HU18–HU20. Del 10 al 12 se reserva tiempo para aceptación del flujo registro → verificación → publicación → descubrimiento → comentario/like → solicitud → resolución → avance → reporte/moderación; revisión de cuentas suspendidas; pruebas de seguridad; respaldo/restauración; entrega del APK de pruebas y acceso web; manual breve y demostración.

Esta reserva no garantiza capacidad: si la estimación del equipo no permite cerrar administración/moderación antes del 10, adelantar tareas a sprints previos o renegociar alcance, sin eliminar controles de acceso ni trasladar todas las pruebas al último día.
