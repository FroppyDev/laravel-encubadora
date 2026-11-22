@extends('layouts.navbar')
@section('styles')
    @parent
    <style>

        body{margin:0;background:#f5f7fb;color:#243553;font:16px/1.6 Arial,Helvetica,sans-serif}
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

        .cover-upload small {
            color: #64748B;
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

        <div class="create-layout">

            <!-- FORMULARIO -->
            <section class="form-column">

                <div class="form-card">
                    <h2>Información general</h2>

                    <div class="field">
                        <label for="title">Título del proyecto</label>
                        <input
                            id="title"
                            type="text"
                            maxlength="180"
                            placeholder="Ej. Plataforma de aprendizaje..."
                        >
                        <small>Máximo 180 caracteres</small>
                    </div>

                    <div class="field">
                        <label for="summary">Resumen</label>
                        <textarea
                            id="summary"
                            maxlength="500"
                            rows="3"
                            placeholder="Describe brevemente tu proyecto..."
                        ></textarea>
                        <small>Máximo 500 caracteres</small>
                    </div>

                    <div class="field">
                        <label for="category">Categoría</label>
                        <select id="category">
                            <option value="">Selecciona una categoría</option>
                        </select>
                    </div>
                </div>

                <div class="form-card">
                    <h2>Acerca del proyecto</h2>

                    <div class="field">
                        <label for="description">Descripción completa</label>
                        <textarea
                            id="description"
                            rows="10"
                            maxlength="20000"
                            placeholder="Explica tu proyecto, sus objetivos y lo que buscas conseguir..."
                        ></textarea>
                        <small>Máximo 20,000 caracteres</small>
                    </div>

                    <div class="field">
                        <label for="technologies">Tecnologías</label>
                        <select id="technologies" multiple>
                        </select>
                        <small>Puedes seleccionar varias tecnologías.</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary">
                        Guardar borrador
                    </button>

                    <button type="button" class="btn-primary">
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
                        <small>PNG, JPG o WEBP</small>
                    </label>

                    <input
                        type="file"
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

                    <div class="preview-image">
                        <span>Imagen del proyecto</span>
                    </div>

                    <h3>Nombre de tu proyecto</h3>

                    <p>
                        Aquí aparecerá el resumen de tu proyecto
                        cuando comiences a completar el formulario.
                    </p>

                    <div class="preview-tags">
                        <span>Categoría</span>
                    </div>

                    <hr>

                    <small>Tecnologías</small>

                    <div class="preview-tags">
                        <span>Sin tecnologías</span>
                    </div>
                </div>

            </aside>

        </div>

    </main>
@endsection