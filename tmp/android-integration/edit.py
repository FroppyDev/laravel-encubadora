from pathlib import Path
root=Path(r'C:/Users/potat/AndroidStudioProjects/EncubadoraFIC')
src=root/'app/src/main/java/com/fic/encubadorafic'
def edit(path, old, new):
 p=src/path
 s=p.read_text(encoding='utf-8-sig')
 if old not in s: raise Exception(f'Missing anchor: {path}: {old[:80]}')
 p.write_text(s.replace(old,new),encoding='utf-8')
def put(path,s):
 p=src/path;p.parent.mkdir(parents=True,exist_ok=True);p.write_text(s,encoding='utf-8')

edit('data/auth/AuthRepository.kt','val bio: String? = null)','val bio: String? = null, val avatarUrl: String? = null, val coverImageUrl: String? = null)')
edit('data/auth/AuthRepository.kt','    fun resend(token: String)', '''    fun uploadImage(token: String, field: String, bytes: ByteArray, mime: String): AuthUser =
        throw UnsupportedOperationException()

    fun technologies(token: String): List<com.fic.encubadorafic.model.ProjectOption> = emptyList()

    fun resend(token: String)''')
edit('data/auth/AuthRepository.kt','    suspend fun resend()','''    suspend fun uploadImage(field: String, bytes: ByteArray, mime: String): AuthUser = withContext(Dispatchers.IO) {
        authenticated(requireToken()) { service.uploadImage(it, field, bytes, mime) }
    }

    suspend fun technologies() = withContext(Dispatchers.IO) {
        authenticated(requireToken()) { service.technologies(it) }
    }

    suspend fun resend()''')
edit('data/auth/HttpAuthService.kt','        if (json.isNull("bio")) null else json.getString("bio")','''        if (json.isNull("bio")) null else json.getString("bio"),
        MediaUrls.resolve(json.optString("avatar_url").takeUnless { it == "null" }, baseUrl, debug),
        MediaUrls.resolve(json.optString("cover_image_url").takeUnless { it == "null" }, baseUrl, debug)''')
edit('data/auth/HttpAuthService.kt','    private fun request(', '    fun request(')
edit('data/auth/HttpAuthService.kt','        token: String? = null,\n    ): JSONObject', '''        token: String? = null,
        upload: ByteArray? = null,
        contentType: String? = null,
    ): JSONObject''')
edit('data/auth/HttpAuthService.kt','            if (body != null) {','''            if (upload != null) {
                connection.doOutput = true
                connection.setRequestProperty("Content-Type", requireNotNull(contentType))
                connection.setFixedLengthStreamingMode(upload.size)
                connection.outputStream.use { it.write(upload) }
            } else if (body != null) {''')
edit('data/auth/HttpAuthService.kt','    override fun resend(token: String)', '''    override fun uploadImage(token: String, field: String, bytes: ByteArray, mime: String): AuthUser {
        require(field == "avatar" || field == "cover")
        require(mime in listOf("image/jpeg", "image/png", "image/webp"))
        val boundary = "Android-${java.util.UUID.randomUUID()}"
        val extension = when (mime) { "image/png" -> "png"; "image/webp" -> "webp"; else -> "jpg" }
        val output = java.io.ByteArrayOutputStream()
        output.write(("--$boundary\\r\\nContent-Disposition: form-data; name=\\"$field\\"; filename=\\"photo.$extension\\"\\r\\n" +
            "Content-Type: $mime\\r\\n\\r\\n").toByteArray())
        output.write(bytes)
        output.write("\\r\\n--$boundary--\\r\\n".toByteArray())
        return parseResponse { parseUser(request("user/images", "POST", token = token,
            upload = output.toByteArray(), contentType = "multipart/form-data; boundary=$boundary").getJSONObject("data")) }
    }

    override fun technologies(token: String): List<com.fic.encubadorafic.model.ProjectOption> {
        val result = mutableListOf<com.fic.encubadorafic.model.ProjectOption>()
        var page = 1
        do {
            val json = request("technologies?page=$page", "GET", token = token)
            val items = json.getJSONArray("data")
            for (i in 0 until items.length()) {
                val item = items.getJSONObject(i)
                result.add(com.fic.encubadorafic.model.ProjectOption(item.getLong("id"), item.getString("name")))
            }
            val more = page < json.optInt("last_page", 1)
            page++
        } while (more)
        return result.distinctBy { it.id }
    }

    override fun resend(token: String)''')
put('data/auth/MediaUrls.kt', '''package com.fic.encubadorafic.data.auth

import java.net.URI

object MediaUrls {
    // Laravel may advertise localhost even when the device reaches it through an emulator/USB address.
    fun resolve(value: String?, baseUrl: String, debug: Boolean): String? = runCatching {
        if (value.isNullOrBlank()) return null
        val base = URI(baseUrl)
        var uri = base.resolve(value)
        if (debug && uri.host in setOf("localhost", "127.0.0.1", "10.0.2.2", "::1")) {
            uri = URI(base.scheme, null, base.host, base.port, uri.path, uri.query, null)
        }
        require(uri.userInfo == null && uri.host != null)
        require(uri.scheme == "https" || (debug && uri.scheme == "http"))
        uri.toString()
    }.getOrNull()
}
''')
edit('ui/sessions/AuthViewModel.kt','    val categoriesLoaded: Boolean = false,','    val categoriesLoaded: Boolean = false,\n    val technologies: List<com.fic.encubadorafic.model.ProjectOption> = emptyList(),')
edit('ui/sessions/AuthViewModel.kt','        val categories = repository.categories()','        val categories = repository.categories()\n        val technologies = repository.technologies()')
edit('ui/sessions/AuthViewModel.kt','copy(categories = categories, categoriesLoaded = true)','copy(categories = categories, technologies = technologies, categoriesLoaded = true)')
edit('ui/sessions/AuthViewModel.kt','    fun loadCategories() = perform {','''    fun uploadImage(uri: android.net.Uri, field: String) = perform {
        val user = kotlinx.coroutines.withContext(kotlinx.coroutines.Dispatchers.IO) {
            val resolver = getApplication<Application>().contentResolver
            val mime = resolver.getType(uri)
            if (mime !in listOf("image/jpeg", "image/png", "image/webp"))
                throw AuthException(422, "Selecciona una imagen JPG, PNG o WebP.")
            val limit = (if (field == "avatar") 2 else 5) * 1024 * 1024
            val bytes = resolver.openInputStream(uri)?.use { stream ->
                val output = java.io.ByteArrayOutputStream()
                val buffer = ByteArray(8192)
                while (true) {
                    val count = stream.read(buffer)
                    if (count < 0) break
                    if (output.size() + count > limit) throw AuthException(422,
                        "La imagen supera el límite de ${limit / 1024 / 1024} MB.")
                    output.write(buffer, 0, count)
                }
                output.toByteArray()
            } ?: throw AuthException(422, "No se pudo leer la imagen seleccionada.")
            repository.uploadImage(field, bytes, requireNotNull(mime))
        }
        mutableState.value = mutableState.value.copy(user = user, message = "Imagen actualizada correctamente.")
    }

    fun loadCategories() = perform {''')
put('ui/perfil/RemoteImage.kt','''package com.fic.encubadorafic.ui.perfil

import android.graphics.BitmapFactory
import androidx.compose.foundation.Image
import androidx.compose.foundation.layout.*
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.ImageBitmap
import androidx.compose.ui.graphics.asImageBitmap
import androidx.compose.ui.layout.ContentScale
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import kotlinx.coroutines.CancellationException
import java.net.HttpURLConnection
import java.net.URL

@Composable
fun RemoteImage(url: String?, description: String, modifier: Modifier = Modifier) {
    var retry by remember(url) { mutableIntStateOf(0) }
    var bitmap by remember(url) { mutableStateOf<ImageBitmap?>(null) }
    var failed by remember(url) { mutableStateOf(false) }
    LaunchedEffect(url, retry) {
        failed = false
        if (url == null) { failed = true; return@LaunchedEffect }
        try {
            bitmap = withContext(Dispatchers.IO) {
                val connection = URL(url).openConnection() as HttpURLConnection
                try {
                    connection.connectTimeout = 15000
                    connection.readTimeout = 15000
                    connection.instanceFollowRedirects = false
                    check(connection.responseCode == 200)
                    val bytes = connection.inputStream.use { input ->
                        val output = java.io.ByteArrayOutputStream()
                        val buffer = ByteArray(8192)
                        while (true) {
                            val n = input.read(buffer)
                            if (n < 0) break
                            check(output.size() + n <= 12 * 1024 * 1024)
                            output.write(buffer, 0, n)
                        }
                        output.toByteArray()
                    }
                    val bounds = BitmapFactory.Options().apply { inJustDecodeBounds = true }
                    BitmapFactory.decodeByteArray(bytes, 0, bytes.size, bounds)
                    var sample = 1
                    while (maxOf(bounds.outWidth, bounds.outHeight) / sample > 1200) sample *= 2
                    requireNotNull(BitmapFactory.decodeByteArray(bytes, 0, bytes.size,
                        BitmapFactory.Options().apply { inSampleSize = sample })).asImageBitmap()
                } finally { connection.disconnect() }
            }
        } catch (e: CancellationException) { throw e }
        catch (_: Exception) { failed = true }
    }
    Box(modifier, contentAlignment = Alignment.Center) {
        val image = bitmap
        if (image != null) Image(image, description, Modifier.fillMaxSize(), contentScale = ContentScale.Crop)
        else if (failed) TextButton(onClick = { retry++ }) { Text("Reintentar foto", style = MaterialTheme.typography.labelSmall) }
        else CircularProgressIndicator(Modifier.size(androidx.compose.ui.unit.Dp(24f)))
    }
}
''')
edit('ui/perfil/PerfilScreen.kt','import androidx.activity.compose.BackHandler','''import androidx.activity.compose.BackHandler
import androidx.activity.compose.rememberLauncherForActivityResult
import androidx.activity.result.contract.ActivityResultContracts
import androidx.activity.result.PickVisualMediaRequest
import androidx.compose.foundation.clickable''')
edit('ui/perfil/PerfilScreen.kt','    onBack: () -> Unit,','    onBack: () -> Unit,\n    onUploadImage: (android.net.Uri, String) -> Unit = { _, _ -> },\n    projectsContent: @Composable () -> Unit = {},')
edit('ui/perfil/PerfilScreen.kt','    val user = state.user ?: return','''    val user = state.user ?: return
    val avatarPicker = rememberLauncherForActivityResult(ActivityResultContracts.PickVisualMedia()) { uri ->
        uri?.let { onUploadImage(it, "avatar") }
    }
    val coverPicker = rememberLauncherForActivityResult(ActivityResultContracts.PickVisualMedia()) { uri ->
        uri?.let { onUploadImage(it, "cover") }
    }''')
edit('ui/perfil/PerfilScreen.kt','''                        Surface(
                            Modifier
                                .align(Alignment.BottomEnd)''','''                        RemoteImage(user.coverImageUrl, "Portada", Modifier.matchParentSize())
                        Surface(
                            Modifier
                                .clickable(enabled = !state.busy) { coverPicker.launch(PickVisualMediaRequest(ActivityResultContracts.PickVisualMedia.ImageOnly)) }
                                .align(Alignment.BottomEnd)''')
edit('ui/perfil/PerfilScreen.kt','''                                Text(
                                    user.name.trim().split''','''                                Text(
                                    user.name.trim().split''')
edit('ui/perfil/PerfilScreen.kt','''                            ) {
                                Text(
                                    user.name.trim()''','''                            ) {
                                RemoteImage(user.avatarUrl, "Foto de perfil", Modifier.matchParentSize())
                                /*Text(
                                    user.name.trim()''')
edit('ui/perfil/PerfilScreen.kt','''                                    style = MaterialTheme.typography.headlineMedium
                                )''','''                                    style = MaterialTheme.typography.headlineMedium
                                )*/''')
edit('ui/perfil/PerfilScreen.kt','''                        Text(
                            user.name,''','''                        TextButton(enabled = !state.busy, onClick = {
                            avatarPicker.launch(PickVisualMediaRequest(ActivityResultContracts.PickVisualMedia.ImageOnly))
                        }) { Text("Cambiar foto de perfil") }
                        Text("Perfil: hasta 2 MB · Portada: hasta 5 MB", style = MaterialTheme.typography.bodySmall)
                        if (state.busy) LinearProgressIndicator(Modifier.fillMaxWidth())
                        state.fields["avatar"]?.let { Text(it, color = MaterialTheme.colorScheme.error) }
                        state.fields["cover"]?.let { Text(it, color = MaterialTheme.colorScheme.error) }
                        Text(
                            user.name,''')
# Replace the placeholder collaboration card with actual owned projects; API has no membership listing.
p=src/'ui/perfil/PerfilScreen.kt';s=p.read_text();start=s.index('            Surface(shape = RoundedCornerShape(20.dp)',s.index('user.bio?.takeIf'));end=s.index('            Surface(shape = RoundedCornerShape(20.dp)',start+20);s=s[:start]+'            projectsContent()\n\n'+s[end:];p.write_text(s,encoding='utf-8')
edit('navegation/Navigation.kt','''                            onBack = { auth.clearMessages(); backStack.removeLastOrNull() })
                    }
                    entry<Routes.Categories>''','''                            onBack = { auth.clearMessages(); backStack.removeLastOrNull() },
                            onUploadImage = auth::uploadImage,
                            projectsContent = { com.fic.encubadorafic.ui.perfil.OwnProjects { id -> backStack.add(Routes.ProjectDetail(id)) } })
                    }
                    entry<Routes.Categories>''')
put('data/repository/ApiProjectsRepository.kt','''package com.fic.encubadorafic.data.repository

import android.content.Context
import com.fic.encubadorafic.BuildConfig
import com.fic.encubadorafic.data.auth.*
import com.fic.encubadorafic.model.*
import dagger.hilt.android.qualifiers.ApplicationContext
import javax.inject.Inject
import javax.inject.Singleton
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import org.json.JSONObject
import org.json.JSONArray

@Singleton
class ApiProjectsRepository @Inject constructor(@ApplicationContext context: Context) : ProjectsRepository {
    private val api = HttpAuthService(BuildConfig.API_BASE_URL, BuildConfig.DEBUG)
    private val tokens = KeystoreTokenStore(context, BuildConfig.API_BASE_URL)
    private fun call(path: String, method: String = "GET", body: JSONObject? = null): JSONObject {
        val token = tokens.read() ?: throw AuthException(401, "Tu sesión expiró. Vuelve a iniciar sesión.")
        return api.request(path, method, body, token)
    }
    private fun pages(path: String): List<JSONObject> {
        val items = mutableListOf<JSONObject>()
        var page = 1
        do {
            val json = call("$path?page=$page")
            val data = json.getJSONArray("data")
            for (i in 0 until data.length()) items.add(data.getJSONObject(i))
            val more = page < json.optInt("last_page", 1)
            page++
        } while (more)
        return items
    }
    override suspend fun obtenerProjectos(): List<Project> = withContext(Dispatchers.IO) {
        pages("projects").map(::project).distinctBy { it.id }
    }
    suspend fun mine(): List<Project> = withContext(Dispatchers.IO) {
        pages("me/projects").map(::project).distinctBy { it.id }
    }
    override suspend fun obtenerDetalle(id: Long): ProjectDetail? = withContext(Dispatchers.IO) {
        try {
            val json = call("projects/$id").getJSONObject("data")
            val updates = pages("projects/$id/updates").map {
                ProjectUpdate(it.getLong("id"), it.getString("title"), it.getString("body"), it.optString("created_at").take(10))
            }
            val technologies = json.optJSONArray("technologies") ?: JSONArray()
            val requests = pages("projects/$id/collaboration-requests")
            val me = api.user(requireNotNull(tokens.read())).id
            ProjectDetail(project(json), json.optJSONObject("user")?.optString("name") ?: "Autor",
                json.optJSONObject("category")?.optString("name") ?: "Sin categoría",
                (0 until technologies.length()).map { technologies.getJSONObject(it).getString("name") },
                updates, json.optInt("reactions_count"),
                collaborationPending = requests.any { it.optLong("user_id") == me && it.optString("status") in listOf("pending", "accepted") })
        } catch (e: AuthException) { if (e.status == 404) null else throw e }
    }
    override suspend fun crearProyecto(input: NewProject, published: Boolean, authorId: Long, authorName: String): ProjectDetail = withContext(Dispatchers.IO) {
        val body = JSONObject().put("title", input.title.trim()).put("summary", input.summary.trim())
            .put("description", input.description.trim()).put("category_id", input.categoryId)
            .put("technology_ids", JSONArray(input.technologyIds)).put("status", if (published) "published" else "draft")
        val json = call("projects", "POST", body).getJSONObject("data")
        // Do not perform a second request after creation: failure could encourage duplicate submissions.
        ProjectDetail(project(json), authorName, json.optJSONObject("category")?.optString("name") ?: input.categoryName)
    }
    suspend fun support(id: Long, enabled: Boolean) = withContext(Dispatchers.IO) {
        call("projects/$id/reaction", if (enabled) "PUT" else "DELETE", if (enabled) JSONObject().put("type", "like") else null)
        call("projects/$id").getJSONObject("data").optInt("reactions_count")
    }
    suspend fun collaborate(id: Long) = withContext(Dispatchers.IO) {
        call("projects/$id/collaboration-requests", "POST", JSONObject())
        Unit
    }
    private fun project(json: JSONObject) = Project(
        id = json.getLong("id"), user_id = json.getLong("user_id"), category_id = json.getLong("category_id"),
        title = json.getString("title"), slug = json.getString("slug"), summary = json.getString("summary"),
        description = json.getString("description"),
        cover_image = json.optString("cover_image_path").takeIf { it.isNotBlank() && it != "null" }?.let {
            MediaUrls.resolve("/storage/$it", BuildConfig.API_BASE_URL, BuildConfig.DEBUG)
        },
        status = json.getString("status"), visibility = if (json.getString("status") == "published") "public" else "private",
        created_at = json.optString("created_at"), updated_at = json.optString("updated_at")
    )
}
''')
edit('data/repository/ProjectsRepositoryModule.kt','repository: FakeProjectsRepository','repository: ApiProjectsRepository')
edit('model/NewProject.kt','technologyIds.any { id -> DemoProjectOptions.technologies.none { it.id == id } }','technologyIds.any { it <= 0 }')
edit('ui/projects/CreateProjectScreen.kt','    vm: CreateProjectViewModel = hiltViewModel(),','    technologyOptions: List<ProjectOption> = emptyList(),\n    vm: CreateProjectViewModel = hiltViewModel(),')
edit('ui/projects/CreateProjectScreen.kt','        categories = categories, categoriesLoading = categoriesLoading,','        categories = categories, categoriesLoading = categoriesLoading, technologyOptions = technologyOptions,')
edit('ui/projects/CreateProjectScreen.kt','    onReloadCategories: () -> Unit = {},','    onReloadCategories: () -> Unit = {},\n    technologyOptions: List<ProjectOption> = emptyList(),')
edit('ui/projects/CreateProjectScreen.kt','DemoProjectOptions.technologies.forEach','technologyOptions.forEach')
edit('ui/projects/CreateProjectScreen.kt','selectedCategory?.id, technologies,','selectedCategory?.id, technologies.filter { id -> technologyOptions.any { it.id == id } },')
edit('ui/projects/CreateProjectScreen.kt','catálogo de prueba','catálogo de proyectos')
edit('ui/projects/CreateProjectScreen.kt','Aparecerá en Explorar dentro de esta sesión de prueba.','El proyecto se guardará en tu cuenta y aparecerá en Explorar.')
edit('navegation/Navigation.kt','                            categories = state.categories.map','                            technologyOptions = state.technologies,\n                            categories = state.categories.map')
edit('ui/projects/ProjectDetailScreen.kt','?: if (detail.project.id >= 100) detail.categoryName else "Categoría no disponible"','?: detail.categoryName')
put('ui/perfil/OwnProjects.kt','''package com.fic.encubadorafic.ui.perfil

import androidx.compose.runtime.*
import androidx.compose.foundation.layout.*
import androidx.compose.material3.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.lifecycle.viewmodel.compose.hiltViewModel
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import dagger.hilt.android.lifecycle.HiltViewModel
import javax.inject.Inject
import com.fic.encubadorafic.data.repository.ApiProjectsRepository
import com.fic.encubadorafic.model.Project
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import kotlinx.coroutines.CancellationException

data class OwnProjectsState(val loading: Boolean = true, val items: List<Project> = emptyList(), val error: String? = null)
@HiltViewModel
class OwnProjectsViewModel @Inject constructor(private val repository: ApiProjectsRepository): ViewModel() {
    private val mutable = MutableStateFlow(OwnProjectsState())
    val state = mutable.asStateFlow()
    fun load() { viewModelScope.launch {
        mutable.value = OwnProjectsState()
        try { mutable.value = OwnProjectsState(false, repository.mine()) }
        catch(e: CancellationException) { throw e }
        catch(e: Exception) { mutable.value = OwnProjectsState(false, error = e.message ?: "No se pudieron cargar tus proyectos.") }
    } }
}
@Composable
fun OwnProjects(onOpen: (Long) -> Unit) {
    val vm: OwnProjectsViewModel = hiltViewModel()
    val state by vm.state.collectAsState()
    LaunchedEffect(Unit) { vm.load() }
    Card(Modifier.fillMaxWidth()) {
        Column(Modifier.padding(20.dp), verticalArrangement = Arrangement.spacedBy(8.dp)) {
            Text("Mis proyectos", style = MaterialTheme.typography.titleMedium)
            if(state.loading) LinearProgressIndicator(Modifier.fillMaxWidth())
            state.error?.let { Text(it, color = MaterialTheme.colorScheme.error) }
            if(!state.loading && state.error == null && state.items.isEmpty()) Text("Todavía no has creado proyectos.")
            state.items.forEach { project ->
                TextButton(onClick = { onOpen(project.id) }) {
                    val status = when(project.status) { "draft" -> "Borrador"; "archived" -> "Archivado"; else -> "Publicado" }
                    Text("${project.title} · $status")
                }
            }
            TextButton(onClick = vm::load, enabled = !state.loading) { Text("Actualizar proyectos") }
        }
    }
}
''')
