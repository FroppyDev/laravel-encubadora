from pathlib import Path
root=Path(r'C:/Users/potat/AndroidStudioProjects/EncubadoraFIC')
src=root/'app/src/main/java/com/fic/encubadorafic'
# Normalize accidental legacy encoding while preserving all existing content.
for p in src.rglob('*.kt'):
 s=p.read_text(encoding='utf-8-sig');original=s
 for char in 'áéíóúñÁÉÍÓÚÑ¿¡·…—–':
  try: s=s.replace(char.encode('utf-8').decode('cp1252'),char)
  except UnicodeDecodeError: pass
 if s!=original:p.write_text(s,encoding='utf-8')
p=root/'app/src/test/java/com/fic/encubadorafic/CreateProjectRepositoryTest.kt';s=p.read_text(encoding='utf-8').replace('null, listOf(999)','null, listOf(-1)');p.write_text(s,encoding='utf-8')
p=root/'app/src/test/java/com/fic/encubadorafic/MediaUrlsTest.kt';p.write_text('''package com.fic.encubadorafic
import com.fic.encubadorafic.data.auth.MediaUrls
import com.fic.encubadorafic.model.NewProject
import org.junit.Assert.*
import org.junit.Test
class MediaUrlsTest {
    @Test fun localhostMediaUsesTheDeviceBackendAddress() {
        assertEquals("http://10.0.2.2:8000/storage/users/avatar.png",
            MediaUrls.resolve("http://localhost:8000/storage/users/avatar.png", "http://10.0.2.2:8000/api/v1/", true))
    }
    @Test fun productionDoesNotRewriteHostsOrAcceptInsecureUrls() {
        assertNull(MediaUrls.resolve("http://example.com/a.png", "https://api.example.com/api/v1/", false))
        assertNull(MediaUrls.resolve("https://user:secret@example.com/a.png", "https://api.example.com/api/v1/", false))
        assertEquals("https://cdn.example.com/a.png", MediaUrls.resolve("https://cdn.example.com/a.png", "https://api.example.com/api/v1/", false))
    }
    @Test fun acceptsRealTechnologyIdsAndRejectsDuplicates() {
        val input = NewProject("Idea", "Resumen", "Descripción", 42, listOf(99))
        assertTrue(input.errors().isEmpty())
        assertTrue("technologies" in input.copy(technologyIds = listOf(99,99)).errors())
    }
}
''',encoding='utf-8')
p=root/'app/src/androidTest/java/com/fic/encubadorafic/data/auth/AuthIntegrationTest.kt';s=p.read_text(encoding='utf-8');i=s.rfind('}');s=s[:i]+'''
    @Test fun imageUploadUsesMultipartAndReturnsDefaultAndCustomUrls() {
        val user = JSONObject(account).put("avatar_url", "http://localhost:8000/storage/users/avatars/test.png")
            .put("cover_image_url", "http://localhost:8000/images/cover.jpe")
        val captured = exchange(200, JSONObject().put("data", user).toString()) {
            val result = it.uploadImage("image-token", "avatar", byteArrayOf(1,2,3), "image/png")
            assertTrue(result.avatarUrl!!.endsWith("/storage/users/avatars/test.png"))
            assertTrue(result.coverImageUrl!!.endsWith("/images/cover.jpe"))
        }
        assertTrue(captured.startsWith("POST /api/v1/user/images"))
        assertTrue(captured.contains("Authorization: Bearer image-token", true))
        assertTrue(captured.contains("multipart/form-data; boundary="))
        assertTrue(captured.contains("name=\\"avatar\\"; filename=\\"photo.png\\""))
        assertTrue(captured.contains("Content-Type: image/png"))
        assertFalse(captured.contains("name=\\"cover\\""))
    }

    @Test fun imageValidationErrorsCanBeDisplayedByField() {
        exchange(422, """{"errors":{"cover":["La portada supera el límite"]}}""") {
            try { it.uploadImage("token", "cover", byteArrayOf(1), "image/jpeg"); fail() }
            catch(e: AuthException) { assertEquals("La portada supera el límite", e.fields["cover"]) }
        }
    }
}
''';p.write_text(s,encoding='utf-8')
p=src/'ui/projects/ProjectDetailViewModel.kt';s=p.read_text(encoding='utf-8').replace('// Only demo state is mutated. Replace these callbacks with repository operations when integrating the API.','// Demo behavior remains available for previews/tests; real actions use the API.');p.write_text(s,encoding='utf-8')
