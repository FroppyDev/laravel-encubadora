@extends('layouts.navbar')

@section('styles')
    @parent
    <style>
        body {
            margin: 0;
            background: #f5f7fb;
            color: #243553;
            font: 16px/1.6 Arial, Helvetica, sans-serif;
        }

        .create-project {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 24px 70px;
            color: #142D4E;
        }

        .create-header {
            margin-bottom: 32px;
        }

        .eyebrow {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #246BCE;
        }

        .create-header h1 {
            font-size: clamp(28px, 4vw, 38px);
            margin: 10px 0;
            font-weight: 750;
        }

        .create-header p {
            color: #64748B;
            max-width: 650px;
            line-height: 1.6;
        }

        .create-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) minmax(280px, 0.9fr);
            gap: 24px;
            align-items: start;
        }

        .form-column,
        .preview-column {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 3px 12px rgba(20, 45, 78, 0.035);
        }

        .form-card h2 {
            font-size: 17px;
            margin: 0 0 22px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 20px;
        }

        .field:last-child {
            margin-bottom: 0;
        }

        .field label {
            font-size: 13px;
            font-weight: 650;
        }

        .field input,
        .field textarea,
        .field select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #DCE3EC;
            border-radius: 9px;
            background: #FFFFFF;
            color: #142D4E;
            font: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .field input:focus,
        .field textarea:focus,
        .field select:focus {
            border-color: #246BCE;
            box-shadow: 0 0 0 3px rgba(36, 107, 206, .10);
        }

        .field textarea {
            resize: vertical;
            line-height: 1.6;
        }

        .field small {
            color: #94A3B8;
            font-size: 11px;
        }

        .cover-upload {
            min-height: 180px;
            border: 2px dashed #CBD5E1;
            border-radius: 12px;
            background: #F8FAFC;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: #142D4E;
            transition: .2s;
        }

        .cover-upload:hover {
            border-color: #246BCE;
            background: #F0F6FF;
        }

        .upload-icon {
            font-size: 32px;
            color: #246BCE;
        }

        .preview-column {
            position: sticky;
            top: 24px;
        }

        .preview-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .preview-heading h2 {
            margin-bottom: 16px;
        }

        .draft-label {
            font-size: 11px;
            background: #EFF6FF;
            color: #246BCE;
            padding: 5px 9px;
            border-radius: 20px;
            margin-bottom: 16px;
        }

        .preview-image {
            height: 160px;
            background: #F1F5F9;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94A3B8;
            margin-bottom: 18px;
            overflow: hidden;
        }

        .preview-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .preview-card h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .preview-card p {
            color: #64748B;
            font-size: 13px;
            line-height: 1.6;
        }

        .preview-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
            margin: 12px 0;
        }

        .preview-tags span {
            background: #EFF6FF;
            color: #246BCE;
            border-radius: 20px;
            padding: 6px 10px;
            font-size: 11px;
        }

        .preview-card hr {
            border: 0;
            border-top: 1px solid #E2E8F0;
            margin: 18px 0;
        }

        .preview-card > small {
            color: #64748B;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn-primary,
        .btn-secondary {
            padding: 12px 20px;
            border-radius: 9px;
            font-weight: 650;
            font-size: 13px;
            cursor: pointer;
        }

        .btn-primary {
            background: #246BCE;
            color: white;
            border: 1px solid #246BCE;
        }

        .btn-primary:hover {
            background: #1D56A7;
        }

        .btn-secondary {
            background: white;
            color: #142D4E;
            border: 1px solid #CBD5E1;
        }

        .btn-primary:disabled,
        .btn-secondary:disabled {
            opacity: .65;
            cursor: not-allowed;
        }

        /* Selector de tecnologías */

        .technology-selector {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .technology-search {
            position: relative;
        }

        .technology-search input {
            padding-left: 42px;
        }

        .technology-search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            pointer-events: none;
        }

        .selected-technologies {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            min-height: 8px;
        }

        .selected-tech {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            border: 1px solid #BBD5FA;
            border-radius: 20px;
            background: #EFF6FF;
            color: #1769D2;
            font-size: 12px;
            font-weight: 650;
        }

        .remove-tech {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            padding: 0;
            border: 0;
            border-radius: 50%;
            background: transparent;
            color: #1769D2;
            font-size: 16px;
            line-height: 1;
            cursor: pointer;
        }

        .remove-tech:hover {
            background: #DCEAFF;
        }

        .selected-empty {
            color: #94A3B8;
            font-size: 12px;
        }

        .technology-list {
            display: flex;
            flex-direction: column;
            gap: 5px;
            max-height: 240px;
            overflow-y: auto;
            padding: 5px;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            background: #FAFCFF;
        }

        .technology-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border: 1px solid transparent;
            border-radius: 8px;
            cursor: pointer;
            transition: background .15s, border-color .15s;
        }

        .technology-option:hover {
            background: #F1F6FF;
        }

        .technology-option.selected {
            background: #EFF6FF;
            border-color: #C7DDFB;
        }

        .technology-option input {
            width: 17px;
            height: 17px;
            margin: 0;
            accent-color: #246BCE;
            cursor: pointer;
            flex-shrink: 0;
        }

        .technology-option span {
            font-size: 13px;
            font-weight: 550;
            color: #334155;
        }

        .technology-option.selected span {
            color: #1769D2;
            font-weight: 700;
        }

        .technology-count {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #94A3B8;
            font-size: 11px;
        }

        .technology-empty {
            display: none;
            padding: 18px;
            text-align: center;
            color: #94A3B8;
            font-size: 13px;
        }

        .button-project {
            display: inline-block;
            padding: 12px 24px;
            background-color: #1769d2;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }

        .btn-delete {
            padding: 12px 20px;
            border-radius: 9px;
            font-weight: 650;
            font-size: 13px;
            cursor: pointer;
            background: #fff;
            color: #dc2626;
            border: 1px solid #fecaca;
            transition: .2s;
        }

        .btn-delete:hover {
            background: #fef2f2;
            border-color: #dc2626;
        }

        .btn-delete:disabled {
            opacity: .65;
            cursor: not-allowed;
        }

        @media (max-width: 850px) {
            .create-layout {
                grid-template-columns: 1fr;
            }

            .preview-column {
                position: static;
            }
        }

        @media (max-width: 520px) {
            .create-project {
                padding: 24px 15px 45px;
            }

            .form-card {
                padding: 18px;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .form-actions button {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content-navbar')

    @php
        $project = $project ?? null;

        $selectedTechnologyIds = old(
            'technologies',
            $project?->technologies?->pluck('id')->toArray() ?? []
        );
    @endphp

    <div class="create-project">

        <header class="create-header">
            <span class="eyebrow">COMPARTE TUS IDEAS</span>
            <h1>Publicar proyecto</h1>
            <p>
                Comparte tu proyecto con la comunidad, explica tu idea
                y muestra las tecnologías que utilizaste.
            </p>
        </header>

        @if ($errors->any())
            <div class="form-card" style="border-color: #ef4444; margin-bottom: 20px;">
                <strong style="color: #dc2626;">Hay algunos errores en el formulario:</strong>

                <ul style="color: #dc2626; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="form-card" style="border-color: #16a34a; margin-bottom: 20px;">
                <p style="color: #15803d; margin: 0;">
                    {{ session('success') }}
                </p>
            </div>
        @endif

        <form
            id="projectForm"
            action="{{ route('projects.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            @if($project)
                <input type="hidden" name="project_id" value="{{ $project->id }}">
            @endif

            <input type="hidden" name="action" id="projectAction" value="">

            <div class="create-layout">

                <!-- FORMULARIO -->
                <section class="form-column">

                    <div class="form-card">
                        <h2>Información general</h2>

                        <div class="field">
                            <label for="title">Título del proyecto</label>

                            <input
                                id="title"
                                name="title"
                                type="text"
                                maxlength="180"
                                value="{{ old('title', $project?->title) }}"
                                placeholder="Ej. Plataforma de aprendizaje..."
                                required
                            >

                            <small>Máximo 180 caracteres</small>
                        </div>

                        <div class="field">
                            <label for="summary">Resumen</label>

                            <textarea
                                id="summary"
                                name="summary"
                                maxlength="500"
                                rows="3"
                                placeholder="Describe brevemente tu proyecto..."
                                required
                            >{{ old('summary', $project?->summary) }}</textarea>

                            <small>Máximo 500 caracteres</small>
                        </div>

                        <div class="field">
                            <label for="category">Categoría</label>

                            <select id="category" name="category" required>
                                <option value="">Selecciona una categoría</option>

                                @foreach($categories as $category)
                                    <option
                                        value="{{ $category->id }}"
                                        @selected(old('category_id', $project?->category) == $category->id)
                                    >
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-card">
                        <h2>Acerca del proyecto</h2>

                        <div class="field">
                            <label for="description">Descripción completa</label>

                            <textarea
                                id="description"
                                name="description"
                                rows="10"
                                maxlength="20000"
                                placeholder="Explica tu proyecto, sus objetivos y lo que buscas conseguir..."
                                required
                            >{{ old('description', $project?->description) }}</textarea>

                            <small>Máximo 20,000 caracteres</small>
                        </div>

                        <!-- SELECTOR DE TECNOLOGÍAS -->
                        <div class="field">
                            <label for="technologySearch">Tecnologías utilizadas</label>

                            <div class="technology-selector">

                                <!-- Buscador -->
                                <div class="technology-search">
                                    <span class="technology-search-icon">⌕</span>

                                    <input
                                        type="search"
                                        id="technologySearch"
                                        placeholder="Buscar tecnologías..."
                                        autocomplete="off"
                                    >
                                </div>

                                <!-- Tecnologías seleccionadas -->
                                <div
                                    class="selected-technologies"
                                    id="selectedTechnologies"
                                >
                                    <span class="selected-empty">
                                        Todavía no has seleccionado tecnologías.
                                    </span>
                                </div>

                                <!-- Contador -->
                                <div class="technology-count">
                                    <span>Selecciona las tecnologías de tu proyecto</span>
                                    <span id="technologyCount">0 seleccionadas</span>
                                </div>

                                <!-- Lista de tecnologías -->
                                <div class="technology-list" id="technologyList">

                                    @foreach($technologies as $technology)
                                        <label class="technology-option">
                                            <input
                                                type="checkbox"
                                                name="technologies[]"
                                                value="{{ $technology->id }}"
                                                data-name="{{ $technology->name }}"
                                                @if(in_array($technology->id, $selectedTechnologyIds))
                                                    checked
                                                @endif
                                            >

                                            <span>{{ $technology->name }}</span>
                                        </label>
                                    @endforeach

                                    <div class="technology-empty" id="technologyEmpty">
                                        No se encontraron tecnologías.
                                    </div>

                                </div>

                                <small>
                                    Puedes seleccionar varias tecnologías y quitarlas cuando quieras.
                                </small>

                            </div>
                        </div>
                    </div>

                    <!-- BOTONES -->
                    <div class="form-actions">

                        @if (isset($project))
                            @if ($project->status === 'draft')
                                <button
                                    type="button"
                                    class="btn-delete"
                                    id="btnDelete">
                                    Eliminar borrador
                                </button>
                            @endif
                        @endif

                        <button
                            type="submit"
                            data-action="draft"
                            class="btn-secondary"
                            id="btnSave"
                        >
                            Guardar borrador
                        </button>

                        <button
                            type="submit"
                            data-action="published"
                            class="btn-primary"
                            id="btnPublish"
                        >
                            Publicar proyecto
                        </button>

                    </div>

                </section>

                <!-- COLUMNA LATERAL -->
                <aside class="preview-column">

                    <div class="form-card">
                        <h2>Imagen de portada</h2>

                        <label class="cover-upload" for="cover">
                            <span class="upload-icon">＋</span>
                            <strong>Subir imagen</strong>
                            <small>PNG, JPG o WEBP (máximo 5 MB)</small>
                        </label>

                        <input
                            type="file"
                            name="cover"
                            id="cover"
                            accept="image/png,image/jpeg,image/webp"
                            hidden
                        >
                    </div>

                    <div class="form-card preview-card">

                        <div class="preview-heading">
                            <h2>Vista previa</h2>
                            <span class="draft-label">Borrador</span>
                        </div>

                        <div class="preview-image" id="previewImage">
                            @if(!empty($project?->cover))
                                <img
                                    src="{{ asset('storage/' . $project->cover) }}"
                                    alt="Portada del proyecto"
                                >
                            @else
                                <span>Imagen del proyecto</span>
                            @endif
                        </div>

                        <h3 id="previewTitle">
                            {{ old('title', $project?->title) ?: 'Nombre de tu proyecto' }}
                        </h3>

                        <p id="previewSummary">
                            {{ old('summary', $project?->summary) ?: 'Aquí aparecerá el resumen de tu proyecto cuando comiences a completar el formulario.' }}
                        </p>

                        <div class="preview-tags" id="previewCategory">
                            <span>
                                {{ $categories->firstWhere('id', old('category', $project?->category_id))?->name ?? 'Categoría' }}
                            </span>
                        </div>

                        <hr>

                        <small>Tecnologías</small>

                        <div
                            class="preview-tags"
                            id="previewTechnologies"
                        >
                            <span>Sin tecnologías</span>
                        </div>

                    </div>

                </aside>

            </div>
        </form>

        @if ($project && $project->exists && $project->status === 'draft')
            <form
                id="deleteProjectForm"
                action="{{ route('projects.delete', $project) }}"
                method="POST"
                style="display: none;"
            >
                @csrf
                @method('DELETE')
            </form>
        @endif

    </div>

@endsection

@section('scripts')
    @parent

    <script>
        // ==========================================
        // ELEMENTOS DEL FORMULARIO
        // ==========================================

        const titleInput = document.getElementById('title');
        const summaryInput = document.getElementById('summary');
        const categorySelect = document.getElementById('category');

        const technologySearch = document.getElementById('technologySearch');
        const technologyList = document.getElementById('technologyList');
        const selectedTechnologies = document.getElementById('selectedTechnologies');

        const technologyCount = document.getElementById('technologyCount');
        const technologyEmpty = document.getElementById('technologyEmpty');

        const previewTitle = document.getElementById('previewTitle');
        const previewSummary = document.getElementById('previewSummary');
        const previewCategory = document.getElementById('previewCategory');
        const previewTechnologies = document.getElementById('previewTechnologies');

        const btnSave = document.getElementById('btnSave');
        const btnPublish = document.getElementById('btnPublish');
        const btnDelete = document.getElementById('btnDelete');

        const technologyCheckboxes = Array.from(
            technologyList.querySelectorAll('input[type="checkbox"]')
        );

        // ==========================================
        // VISTA PREVIA: TÍTULO
        // ==========================================

        titleInput.addEventListener('input', () => {
            previewTitle.textContent =
                titleInput.value.trim() || 'Nombre de tu proyecto';
        });

        // ==========================================
        // VISTA PREVIA: RESUMEN
        // ==========================================

        summaryInput.addEventListener('input', () => {
            previewSummary.textContent =
                summaryInput.value.trim() ||
                'Aquí aparecerá el resumen de tu proyecto cuando comiences a completar el formulario.';
        });

        // ==========================================
        // VISTA PREVIA: CATEGORÍA
        // ==========================================

        categorySelect.addEventListener('change', () => {
            const selectedOption =
                categorySelect.options[categorySelect.selectedIndex];

            previewCategory.innerHTML = '';

            const tag = document.createElement('span');

            tag.textContent = categorySelect.value
                ? selectedOption.text
                : 'Categoría';

            previewCategory.appendChild(tag);
        });

        // ==========================================
        // BUSCADOR DE TECNOLOGÍAS
        // ==========================================

        technologySearch.addEventListener('input', () => {

            const searchTerm = technologySearch.value
                .trim()
                .toLowerCase();

            let visibleCount = 0;

            technologyCheckboxes.forEach(checkbox => {

                const option = checkbox.closest('.technology-option');
                const technologyName = checkbox.dataset.name.toLowerCase();

                const matches = technologyName.includes(searchTerm);

                option.style.display = matches ? 'flex' : 'none';

                if (matches) {
                    visibleCount++;
                }
            });

            technologyEmpty.style.display =
                visibleCount === 0 ? 'block' : 'none';
        });

        // ==========================================
        // ACTUALIZAR TECNOLOGÍAS SELECCIONADAS
        // ==========================================

        function updateSelectedTechnologies() {

            const selected = technologyCheckboxes.filter(
                checkbox => checkbox.checked
            );

            // Actualizar apariencia de las opciones.
            technologyCheckboxes.forEach(checkbox => {

                const option = checkbox.closest('.technology-option');

                option.classList.toggle('selected', checkbox.checked);
            });

            // Actualizar contador.
            technologyCount.textContent =
                `${selected.length} seleccionadas`;

            // Actualizar etiquetas seleccionadas.
            selectedTechnologies.innerHTML = '';

            if (selected.length === 0) {

                const empty = document.createElement('span');

                empty.className = 'selected-empty';
                empty.textContent =
                    'Todavía no has seleccionado tecnologías.';

                selectedTechnologies.appendChild(empty);

            } else {

                selected.forEach(checkbox => {

                    const tag = document.createElement('span');
                    tag.className = 'selected-tech';

                    const name = document.createElement('span');
                    name.textContent = checkbox.dataset.name;

                    const removeButton = document.createElement('button');

                    removeButton.type = 'button';
                    removeButton.className = 'remove-tech';
                    removeButton.textContent = '×';

                    removeButton.setAttribute(
                        'aria-label',
                        `Quitar ${checkbox.dataset.name}`
                    );

                    removeButton.addEventListener('click', () => {

                        checkbox.checked = false;

                        updateSelectedTechnologies();
                    });

                    tag.appendChild(name);
                    tag.appendChild(removeButton);

                    selectedTechnologies.appendChild(tag);
                });
            }

            // Actualizar vista previa.
            previewTechnologies.innerHTML = '';

            if (selected.length === 0) {

                const emptyTag = document.createElement('span');
                emptyTag.textContent = 'Sin tecnologías';

                previewTechnologies.appendChild(emptyTag);

            } else {

                selected.forEach(checkbox => {

                    const tag = document.createElement('span');

                    tag.textContent = checkbox.dataset.name;

                    previewTechnologies.appendChild(tag);
                });
            }
        }

        // ==========================================
        // EVENTOS DE SELECCIÓN
        // ==========================================

        technologyCheckboxes.forEach(checkbox => {

            checkbox.addEventListener('change', () => {
                updateSelectedTechnologies();
            });

        });

        // ==========================================
        // VISTA PREVIA: IMAGEN DE PORTADA
        // ==========================================

        const coverInput = document.getElementById('cover');
        const previewImage = document.getElementById('previewImage');

        let coverPreviewUrl = null;

        coverInput.addEventListener('change', () => {

            const file = coverInput.files[0];

            if (!file) return;

            if (coverPreviewUrl) {
                URL.revokeObjectURL(coverPreviewUrl);
            }

            coverPreviewUrl = URL.createObjectURL(file);

            previewImage.innerHTML = '';

            const image = document.createElement('img');

            image.src = coverPreviewUrl;
            image.alt = 'Vista previa de la portada del proyecto';

            previewImage.appendChild(image);
        });

        // ==========================================
        // ENVÍO DEL FORMULARIO
        // ==========================================

        const projectForm = document.getElementById('projectForm');

        const draftLabel = document.querySelector('.draft-label');
        const projectAction = document.getElementById('projectAction');

        let formSubmitting = false;

        projectForm.addEventListener('submit', (event) => {

            const submitter = event.submitter;

            // Comprobar que se haya pulsado un botón válido.
            if (!submitter || !submitter.dataset.action) {
                event.preventDefault();
                return;
            }

            // Evitar envíos duplicados.
            if (formSubmitting) {
                event.preventDefault();
                return;
            }

            formSubmitting = true;

            // Guardar la acción en el campo oculto.
            const action = submitter.dataset.action;
            projectAction.value = action;

            // Actualizar la vista previa.
            if (action === 'draft') {
                draftLabel.textContent = 'Borrador';
                btnSave.textContent = 'Guardando...';
            } else {
                draftLabel.textContent = 'Publicado';
                btnPublish.textContent = 'Publicando...';
            }

            // Deshabilitar ambos botones.
            btnSave.disabled = true;
            btnPublish.disabled = true;
        });

        // ==========================================
        // ELIMINAR BORRADOR
        // ==========================================

        const deleteProjectForm = document.getElementById('deleteProjectForm');

        if (btnDelete && deleteProjectForm) {

            btnDelete.addEventListener('click', () => {

                const confirmation = confirm(
                    '¿Estás seguro de que deseas eliminar este borrador? Esta acción no se puede deshacer.'
                );

                if (!confirmation) return;

                // Enviar el formulario DELETE independiente
                deleteProjectForm.submit();
            });

        }

        // ==========================================
        // INICIALIZAR VISTA PREVIA
        // ==========================================

        titleInput.dispatchEvent(new Event('input'));
        summaryInput.dispatchEvent(new Event('input'));
        categorySelect.dispatchEvent(new Event('change'));

        updateSelectedTechnologies();

    </script>
@endsection