from pathlib import Path
root=Path(r'C:/Users/potat/AndroidStudioProjects/EncubadoraFIC')
src=root/'app/src/main/java/com/fic/encubadorafic'
def edit(path,old,new):
 p=src/path;s=p.read_text(encoding='utf-8-sig')
 if old not in s: raise Exception(f'Missing {path}: {old[:80]}')
 p.write_text(s.replace(old,new),encoding='utf-8')
def put(path,s):
 p=src/path;p.parent.mkdir(parents=True,exist_ok=True);p.write_text(s,encoding='utf-8')
p=src/'ui/perfil/PerfilScreen.kt';s=p.read_text();a=s.index('                                /*Text(');b=s.index(')*/',a)+3;s=s[:a]+s[b:];p.write_text(s,encoding='utf-8')
edit('model/Project.kt','val deleted_at: String? = null','val deleted_at: String? = null,\n    val reactionsCount: Int = 0')
edit('data/repository/ApiProjectsRepository.kt','updated_at = json.optString("updated_at")','updated_at = json.optString("updated_at"), reactionsCount = json.optInt("reactions_count")')
edit('ui/main/ProyectItem.kt','    categoryId: Long = 1L,','    categoryId: Long = 1L,\n    coverUrl: String? = null,')
edit('ui/main/ProyectItem.kt','com.fic.encubadorafic.ui.projects.ProjectCover(categoryId, Modifier.height(150.dp))','''if (coverUrl != null) com.fic.encubadorafic.ui.perfil.RemoteImage(coverUrl, "Portada del proyecto", Modifier.fillMaxWidth().height(150.dp))
            else com.fic.encubadorafic.ui.projects.ProjectCover(categoryId, Modifier.height(150.dp))''')
p=src/'ui/main/MainScreen.kt';s=p.read_text();a=s.index('                when (HomeTabs.entries[page])');b=s.index('\n            }\n        }\n',a)
s=s[:a]+'''                val projects = when (HomeTabs.entries[page]) {
                    HomeTabs.Popular -> visibleProjects.sortedByDescending { it.reactionsCount }
                    HomeTabs.Reciente -> visibleProjects.sortedByDescending { it.created_at }
                    else -> visibleProjects
                }
                Column(Modifier.fillMaxSize()) {
                    androidx.compose.material3.TextButton(onClick = viewModel::cargarRutas, enabled = !state.cargando) { Text("Actualizar proyectos") }
                    if (state.cargando) androidx.compose.material3.LinearProgressIndicator(Modifier.fillMaxWidth())
                    state.error?.let { Text(it, Modifier.padding(16.dp), color = MaterialTheme.colorScheme.error) }
                    LazyColumn(contentPadding = androidx.compose.foundation.layout.PaddingValues(bottom = 88.dp)) {
                        if (!state.cargando && state.error == null && projects.isEmpty()) item { Text("No hay proyectos en esta categoría.", Modifier.padding(20.dp)) }
                        items(projects.size, key = { projects[it].id }) { index ->
                            val project = projects[index]
                            ProyectItem(name = project.title, description = project.summary,
                                category = categories.firstOrNull { it.id == project.category_id }?.name ?: "Categoría no disponible",
                                categoryId = project.category_id, coverUrl = project.cover_image,
                                onClick = { onNavigateToProject(project.id) })
                        }
                    }
                }'''+s[b:];p.write_text(s,encoding='utf-8')
# Avoid simultaneous duplicated loading on entry and init.
edit('ui/main/MainViewModel.kt','''    init {
        cargarRutas()
    }''','''    private var loadingJob: kotlinx.coroutines.Job? = null''')
edit('ui/main/MainViewModel.kt','''        viewModelScope.launch {''','''        loadingJob?.cancel()
        loadingJob = viewModelScope.launch {''')
edit('ui/main/MainViewModel.kt','error = "No se pudieron cargar los proyectos"','error = (e as? com.fic.encubadorafic.data.auth.AuthException)?.message ?: "No se pudieron cargar los proyectos"')
put('ui/search/SearchScreen.kt','''package com.fic.encubadorafic.ui.search

import androidx.compose.runtime.*
import androidx.compose.runtime.saveable.rememberSaveable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.material3.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.lifecycle.viewmodel.compose.hiltViewModel
import com.fic.encubadorafic.ui.main.MainViewModel
import com.fic.encubadorafic.ui.main.ProyectItem
import com.fic.encubadorafic.data.auth.Category

@Composable
fun SearchScren(onBack: () -> Unit = {}, onOpen: (Long) -> Unit = {}, categories: List<Category> = emptyList()) {
    val vm: MainViewModel = hiltViewModel()
    val state by vm.uiState.collectAsState()
    var query by rememberSaveable { mutableStateOf("") }
    var category by rememberSaveable { mutableStateOf<Long?>(null) }
    var expanded by remember { mutableStateOf(false) }
    LaunchedEffect(Unit) { vm.cargarRutas() }
    val results = state.proyectos.filter {
        (category == null || category == it.category_id) &&
            (it.title.contains(query.trim(), true) || it.summary.contains(query.trim(), true))
    }
    Column(Modifier.fillMaxSize().safeDrawingPadding().padding(16.dp)) {
        TextButton(onClick = onBack) { Text("Volver") }
        OutlinedTextField(query, { query = it }, Modifier.fillMaxWidth(), label = { Text("Buscar proyectos") }, singleLine = true)
        Box {
            TextButton(onClick = { expanded = true }) { Text(categories.firstOrNull { it.id == category }?.name ?: "Todas las categorías") }
            DropdownMenu(expanded, onDismissRequest = { expanded = false }) {
                DropdownMenuItem(text = { Text("Todas las categorías") }, onClick = { category = null; expanded = false })
                categories.forEach { item -> DropdownMenuItem(text = { Text(item.name) }, onClick = { category = item.id; expanded = false }) }
            }
        }
        if (state.cargando) LinearProgressIndicator(Modifier.fillMaxWidth())
        else if (state.error != null) {
            Text(state.error.orEmpty(), color = MaterialTheme.colorScheme.error)
            TextButton(onClick = vm::cargarRutas) { Text("Reintentar") }
        } else Text("${results.size} resultados encontrados")
        LazyColumn {
            items(results.size, key = { results[it].id }) { index ->
                val project = results[index]
                ProyectItem(name = project.title, description = project.summary,
                    category = categories.firstOrNull { it.id == project.category_id }?.name ?: "Categoría no disponible",
                    categoryId = project.category_id, coverUrl = project.cover_image, onClick = { onOpen(project.id) })
            }
        }
    }
}
''')
edit('navegation/Navigation.kt','entry<Routes.Search> { SearchScren() }','entry<Routes.Search> { SearchScren(onBack = { backStack.removeLastOrNull() }, onOpen = { backStack.add(Routes.ProjectDetail(it)) }, categories = state.categories) }')
edit('ui/projects/ProjectDetailViewModel.kt','    val error: String? = null,','    val error: String? = null,\n    val busy: Boolean = false,')
edit('ui/projects/ProjectDetailViewModel.kt','''        if (!detail.demo || detail.project.status != "published") return''','''        if (detail.project.status != "published" || mutable.value.busy) return
        if (!detail.demo) {
            val api = repository as? com.fic.encubadorafic.data.repository.ApiProjectsRepository ?: return
            mutate { detail.copy(supported = true, supports = api.support(detail.project.id, true)) }
            return
        }''')
# requestCollaboration got the same replacement; correct its branch.
p=src/'ui/projects/ProjectDetailViewModel.kt';s=p.read_text();a=s.index('    fun requestCollaboration()');b=s.index('    private fun update',a);s=s[:a]+'''    fun requestCollaboration() {
        val detail = mutable.value.detail ?: return
        if (mutable.value.busy || detail.project.status != "published") return
        if (detail.demo) { update(detail.copy(collaborationPending = true)); return }
        val api = repository as? com.fic.encubadorafic.data.repository.ApiProjectsRepository ?: return
        mutate { api.collaborate(detail.project.id); detail.copy(collaborationPending = true) }
    }

    fun removeSupport() {
        val detail = mutable.value.detail ?: return
        if (mutable.value.busy) return
        val api = repository as? com.fic.encubadorafic.data.repository.ApiProjectsRepository ?: return
        mutate { detail.copy(supported = false, supports = api.support(detail.project.id, false)) }
    }

    private fun mutate(action: suspend () -> ProjectDetail) {
        mutable.value = mutable.value.copy(busy = true, error = null)
        viewModelScope.launch {
            try { mutable.value = ProjectDetailState(loading = false, detail = action()) }
            catch (e: CancellationException) { throw e }
            catch (e: Exception) { mutable.value = mutable.value.copy(busy = false, error = e.message ?: "No se pudo completar la operación.") }
        }
    }

'''+s[b:];p.write_text(s,encoding='utf-8')
edit('ui/projects/ProjectDetailScreen.kt','        viewModel::requestCollaboration','        viewModel::requestCollaboration,\n        viewModel::removeSupport')
edit('ui/projects/ProjectDetailScreen.kt','    onRetry: () -> Unit, onSupport: () -> Unit, onCollaborate: () -> Unit,','    onRetry: () -> Unit, onSupport: () -> Unit, onCollaborate: () -> Unit,\n    onRemoveSupport: () -> Unit = {},')
edit('ui/projects/ProjectDetailScreen.kt','enabled = detail.demo,','enabled = !state.busy,')
edit('ui/projects/ProjectDetailScreen.kt','enabled = detail.demo && !detail.collaborationPending,','enabled = !state.busy && !detail.collaborationPending,')
edit('ui/projects/ProjectDetailScreen.kt','Solicitud pendiente','Solicitud enviada')
edit('ui/projects/ProjectDetailScreen.kt','''                ProjectCover(detail.project.category_id, Modifier.height(210.dp))''','''                if (detail.project.cover_image != null) com.fic.encubadorafic.ui.perfil.RemoteImage(detail.project.cover_image, "Portada del proyecto", Modifier.fillMaxWidth().height(210.dp))
                else ProjectCover(detail.project.category_id, Modifier.height(210.dp))
                state.error?.let { Text(it, Modifier.padding(16.dp), color = MaterialTheme.colorScheme.error) }
                if (!detail.demo && detail.project.status == "published") Row {
                    TextButton(onClick = onSupport, enabled = !state.busy) { Text("Apoyar") }
                    TextButton(onClick = onRemoveSupport, enabled = !state.busy) { Text("Quitar mi apoyo") }
                }''')
# API returns total reactions, not whether this viewer liked; avoid implying an unverified personal state.
edit('ui/projects/ProjectDetailScreen.kt','if (detail.supported) Icons.Filled.Favorite else Icons.Outlined.FavoriteBorder','if (detail.demo && detail.supported) Icons.Filled.Favorite else Icons.Outlined.FavoriteBorder')
edit('ui/projects/ProjectDetailScreen.kt','if (detail.supported) "Quitar apoyo" else "Apoyar proyecto"','if (detail.demo && detail.supported) "Quitar apoyo" else "Apoyar proyecto"')
edit('ui/projects/ProjectDetailScreen.kt','Esta demostración marcará una solicitud como pendiente en la app. No se enviará al creador.','Se enviará una solicitud al creador del proyecto para que pueda revisarla.')
edit('ui/projects/ProjectDetailScreen.kt','Probar solicitud','Enviar solicitud')
edit('ui/projects/CreateProjectViewModel.kt','''            } catch (_: Exception) {''','''            } catch (e: com.fic.encubadorafic.data.auth.AuthException) {
                mutable.value = CreateProjectState(error = e.fields.values.joinToString("\\n").ifBlank { e.message })
            } catch (_: Exception) {''')
p=root/'app/src/androidTest/java/com/fic/encubadorafic/CreateProjectScreenTest.kt';s=p.read_text();s=s.replace('categories = listOf(com.fic.encubadorafic.model.ProjectOption(42, "Sustentabilidad"))','technologyOptions = listOf(com.fic.encubadorafic.model.ProjectOption(44, "Arduino")), categories = listOf(com.fic.encubadorafic.model.ProjectOption(42, "Sustentabilidad"))');s=s.replace('listOf(4L)','listOf(44L)');p.write_text(s,encoding='utf-8')
