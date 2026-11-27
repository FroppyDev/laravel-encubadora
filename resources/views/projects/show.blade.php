@extends('layouts.navbar')

@section('styles')
    @parent

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #f5f7fb;
            color: #243553;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
        }

        .project-page {
            max-width: 1150px;
            margin: auto;
            padding: 35px 24px 70px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #246BCE;
            font-weight: 600;
        }

        .project-hero {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            overflow: hidden;
        }

        .cover {
            width: 100%;
            max-height: 450px;
            background: #eaf0f8;
            overflow: hidden;
        }

        .cover img {
            width: 100%;
            max-height: 450px;
            object-fit: cover;
            display: block;
        }

        .hero-content {
            padding: 32px;
        }

        .category-tag {
            display: inline-block;
            background: #eff6ff;
            color: #246BCE;
            border-radius: 20px;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-tag {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-published {
            background: #dcfce7;
            color: #15803d;
        }

        .status-draft {
            background: #fef3c7;
            color: #92400e;
        }

        .status-archived {
            background: #e2e8f0;
            color: #475569;
        }

        .project-title {
            font-size: clamp(27px, 4vw, 40px);
            line-height: 1.2;
            margin: 16px 0;
            color: #142D4E;
        }

        .project-summary {
            font-size: 17px;
            color: #64748b;
        }

        .project-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            color: #64748b;
            font-size: 13px;
            margin-top: 20px;
        }

        .project-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 9px;
            border: 1px solid #dce3ec;
            text-decoration: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            background: white;
            color: #243553;
        }

        .btn-primary {
            background: #246BCE;
            border-color: #246BCE;
            color: white;
        }

        .btn-primary:hover {
            background: #1d56a7;
        }

        .btn-danger {
            background: #fff1f2;
            border-color: #fecdd3;
            color: #be123c;
        }

        .btn-success {
            background: #dcfce7;
            border-color: #bbf7d0;
            color: #15803d;
        }

        .btn:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .project-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 24px;
            margin-top: 24px;
            align-items: start;
        }

        .main-column,
        .side-column {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 25px;
        }

        .card h2 {
            font-size: 20px;
            margin: 0 0 20px;
            color: #142D4E;
        }

        .card h3 {
            font-size: 16px;
            color: #142D4E;
        }

        .description {
            white-space: pre-line;
            overflow-wrap: anywhere;
            margin: 0;
        }

        .technology-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .technology-tag {
            background: #eff6ff;
            color: #246BCE;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .reaction-form {
            display: inline;
        }

        .reaction-button {
            min-width: 100px;
            border-radius: 25px;
            font-size: 15px;
        }

        .reaction-button.active {
            background: #246BCE;
            color: white;
            border-color: #246BCE;
        }

        .section-divider {
            border: 0;
            border-top: 1px solid #e2e8f0;
            margin: 22px 0;
        }

        .comment-form textarea,
        .collaboration-form textarea,
        .update-form textarea,
        .update-form input,
        .comment-reply textarea {
            width: 100%;
            border: 1px solid #dce3ec;
            border-radius: 9px;
            padding: 12px;
            font: inherit;
            font-size: 14px;
            resize: vertical;
        }

        .comment-form textarea,
        .collaboration-form textarea,
        .update-form textarea,
        .comment-reply textarea {
            min-height: 100px;
        }

        .comment-form,
        .collaboration-form,
        .update-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .comment {
            padding: 16px 0;
            border-bottom: 1px solid #e9edf3;
        }

        .comment:last-child {
            border-bottom: 0;
        }

        .comment-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .comment-author {
            font-weight: 700;
            color: #142D4E;
        }

        .comment-date {
            font-size: 12px;
            color: #94a3b8;
        }

        .comment-body {
            white-space: pre-line;
            overflow-wrap: anywhere;
            margin: 10px 0;
        }

        .reply {
            margin: 15px 0 0 20px;
            padding: 14px;
            background: #f8fafc;
            border-radius: 10px;
        }

        .reply-toggle {
            color: #246BCE;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
        }

        .reply-form {
            margin-top: 12px;
        }

        .update-item {
            padding: 18px 0;
            border-bottom: 1px solid #e9edf3;
        }

        .update-item:last-child {
            border-bottom: 0;
        }

        .update-date {
            font-size: 12px;
            color: #94a3b8;
        }

        .update-body {
            white-space: pre-line;
            overflow-wrap: anywhere;
        }

        .request-item {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 12px;
        }

        .request-status {
            display: inline-block;
            border-radius: 20px;
            padding: 4px 9px;
            font-size: 11px;
            font-weight: 700;
        }

        .pending {
            background: #fef3c7;
            color: #92400e;
        }

        .accepted {
            background: #dcfce7;
            color: #15803d;
        }

        .rejected {
            background: #fee2e2;
            color: #b91c1c;
        }

        .cancelled {
            background: #e2e8f0;
            color: #475569;
        }

        .muted {
            color: #64748b;
            font-size: 13px;
        }

        .empty-state {
            color: #94a3b8;
            text-align: center;
            padding: 20px;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .owner-label {
            color: #246BCE;
            font-size: 12px;
            font-weight: 700;
        }

        @media(max-width: 850px) {
            .project-grid {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width: 600px) {
            .project-page {
                padding: 20px 14px 40px;
            }

            .hero-content,
            .card {
                padding: 20px;
            }

            .project-actions .btn {
                flex: 1;
            }

            .reply {
                margin-left: 8px;
            }
        }
    </style>
@endsection


@section('content-navbar')

<div class="project-page">

    <a href="{{ route('home') }}" class="back-link">
        ← Volver a explorar proyectos
    </a>

    {{-- MENSAJES --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <strong>Hay errores en el formulario:</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- ================================================= --}}
    {{-- ENCABEZADO DEL PROYECTO --}}
    {{-- ================================================= --}}

    <section class="project-hero">

        @if($project->image_url)
            <div class="cover">
                <img
                    src="{{ $project->image_url }}"
                    alt="Portada de {{ $project->title }}"
                >
            </div>
        @endif

        <div class="hero-content">

            <div style="display:flex; gap:10px; flex-wrap:wrap;">

                @if($project->category)
                    <span class="category-tag">
                        {{ $project->category->name }}
                    </span>
                @endif

                <span class="status-tag status-{{ $project->status }}">
                    {{ ucfirst($project->status) }}
                </span>

            </div>

            <h1 class="project-title">
                {{ $project->title }}
            </h1>

            <p class="project-summary">
                {{ $project->summary }}
            </p>

            <div class="project-meta">

                <span>
                    👤 {{ $project->user?->name ?? 'Usuario' }}
                </span>

                <span>·</span>

                <span>
                    📅 {{ $project->created_at->format('d/m/Y') }}
                </span>

                <span>·</span>

                <span>
                    ❤️ {{ $project->reactions_count }} reacciones
                </span>

                <span>·</span>

                <span>
                    💬 {{ $project->comments->count() }} comentarios
                </span>

            </div>


            {{-- ACCIONES --}}
            <div class="project-actions">

                @if($project->repository_url)
                    <a
                        href="{{ $project->repository_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn">
                        💻 Repositorio
                    </a>
                @endif

                @if($project->demo_url)
                    <a
                        href="{{ $project->demo_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-primary">
                        🚀 Ver demostración
                    </a>
                @endif

                @if($project->status === 'published')

                    <form
                        action="{{ route('projects.reaction.toggle', $project->slug) }}"
                        method="POST"
                        class="reaction-form">
                        @csrf

                        <button
                            type="submit"
                            class="btn reaction-button {{ $myReaction ? 'active' : '' }}">

                            {{ $myReaction ? '❤️ Te gusta' : '🤍 Me gusta' }}

                            ({{ $project->reactions_count }})
                        </button>
                    </form>

                @endif

            </div>

        </div>
    </section>


    {{-- ================================================= --}}
    {{-- CONTENIDO --}}
    {{-- ================================================= --}}

    <div class="project-grid">

        {{-- COLUMNA PRINCIPAL --}}
        <div class="main-column">


            {{-- DESCRIPCIÓN --}}
            <section class="card">

                <h2>Acerca del proyecto</h2>

                <div class="description">
                    {{ $project->description }}
                </div>

            </section>


            {{-- PROBLEMA --}}
            @if($project->problem_statement)

                <section class="card">

                    <h2>Problema que busca resolver</h2>

                    <div class="description">
                        {{ $project->problem_statement }}
                    </div>

                </section>

            @endif


            {{-- SOLUCIÓN --}}
            @if($project->proposed_solution)

                <section class="card">

                    <h2>Solución propuesta</h2>

                    <div class="description">
                        {{ $project->proposed_solution }}
                    </div>

                </section>

            @endif


            {{-- ================================================= --}}
            {{-- ACTUALIZACIONES --}}
            {{-- ================================================= --}}

            <section class="card">

                <h2>Actualizaciones del proyecto</h2>

                @if($project->user_id === auth()->id())

                    <form
                        action="{{ route('projects.updates.store', $project->slug) }}"
                        method="POST"
                        class="update-form">

                        @csrf

                        <input
                            type="text"
                            name="title"
                            maxlength="180"
                            placeholder="Título de la actualización"
                            value="{{ old('title') }}"
                            required
                        >

                        <textarea
                            name="body"
                            placeholder="¿Qué novedades hay en tu proyecto?"
                            required
                        >{{ old('body') }}</textarea>

                        <button class="btn btn-primary" type="submit">
                            Publicar actualización
                        </button>

                    </form>

                    <hr class="section-divider">

                @endif


                @forelse($project->updates as $update)

                    <article class="update-item">

                        <h3>{{ $update->title }}</h3>

                        <div class="update-date">
                            {{ $update->created_at->format('d/m/Y H:i') }}
                            · {{ $update->user?->name }}
                        </div>

                        <div class="update-body">
                            {{ $update->body }}
                        </div>

                        @if($update->user_id === auth()->id())

                            <form
                                action="{{ route('projects.updates.delete', $update->id) }}"
                                method="POST"
                                style="margin-top:12px;"
                                onsubmit="return confirm('¿Eliminar esta actualización?')">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger" type="submit">
                                    Eliminar actualización
                                </button>

                            </form>

                        @endif

                    </article>

                @empty

                    <div class="empty-state">
                        Todavía no hay actualizaciones.
                    </div>

                @endforelse

            </section>


            {{-- ================================================= --}}
            {{-- COMENTARIOS --}}
            {{-- ================================================= --}}

            @if($project->status === 'published')

                <section class="card" id="comments">

                    <h2>
                        Comentarios
                        ({{ $project->comments->count() }})
                    </h2>


                    {{-- NUEVO COMENTARIO --}}
                    <form
                        action="{{ route('projects.comments.store', $project->slug) }}"
                        method="POST"
                        class="comment-form">

                        @csrf

                        <textarea
                            name="body"
                            placeholder="Escribe tu comentario..."
                            required
                            maxlength="5000"
                        >{{ old('body') }}</textarea>

                        <button class="btn btn-primary" type="submit">
                            Publicar comentario
                        </button>

                    </form>


                    <hr class="section-divider">


                    {{-- LISTA --}}
                    @forelse($project->comments as $comment)

                        <article class="comment">

                            <div class="comment-header">

                                <span class="comment-author">
                                    {{ $comment->user?->name ?? 'Usuario' }}
                                </span>

                                <span class="comment-date">
                                    {{ $comment->created_at->format('d/m/Y H:i') }}
                                </span>

                            </div>

                            <div class="comment-body">
                                {{ $comment->body }}
                            </div>


                            {{-- ELIMINAR COMENTARIO --}}
                            @if($comment->user_id === auth()->id())

                                <form
                                    action="{{ route('projects.comments.delete', $comment->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('¿Eliminar comentario?')">

                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-danger" type="submit">
                                        Eliminar
                                    </button>

                                </form>

                            @endif


                            {{-- RESPONDER --}}
                            <details class="reply-form">

                                <summary class="reply-toggle">
                                    Responder
                                </summary>

                                <form
                                    action="{{ route('projects.comments.store', $project->slug) }}"
                                    method="POST"
                                    class="comment-form"
                                    style="margin-top:12px;">

                                    @csrf

                                    <input
                                        type="hidden"
                                        name="parent_id"
                                        value="{{ $comment->id }}"
                                    >

                                    <textarea
                                        name="body"
                                        placeholder="Escribe una respuesta..."
                                        required
                                        maxlength="5000"
                                    ></textarea>

                                    <button class="btn btn-primary" type="submit">
                                        Enviar respuesta
                                    </button>

                                </form>

                            </details>


                            {{-- RESPUESTAS --}}
                            @foreach($comment->replies as $reply)

                                <div class="reply">

                                    <div class="comment-header">

                                        <span class="comment-author">
                                            {{ $reply->user?->name ?? 'Usuario' }}
                                        </span>

                                        <span class="comment-date">
                                            {{ $reply->created_at->format('d/m/Y H:i') }}
                                        </span>

                                    </div>

                                    <div class="comment-body">
                                        {{ $reply->body }}
                                    </div>

                                    @if($reply->user_id === auth()->id())

                                        <form
                                            action="{{ route('projects.comments.delete', $reply->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('¿Eliminar respuesta?')">

                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-danger" type="submit">
                                                Eliminar
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            @endforeach

                        </article>

                    @empty

                        <div class="empty-state">
                            Sé la primera persona en comentar este proyecto.
                        </div>

                    @endforelse

                </section>

            @endif

        </div>


        {{-- ================================================= --}}
        {{-- COLUMNA LATERAL --}}
        {{-- ================================================= --}}

        <aside class="side-column">


            {{-- TECNOLOGÍAS --}}
            <section class="card">

                <h2>Tecnologías</h2>

                <div class="technology-list">

                    @forelse($project->technologies as $technology)

                        <span class="technology-tag">
                            {{ $technology->name }}
                        </span>

                    @empty

                        <span class="muted">
                            No se han especificado tecnologías.
                        </span>

                    @endforelse

                </div>

            </section>


            {{-- COLABORACIÓN --}}
            @if($project->status === 'published')

                <section class="card">

                    <h2>Colaborar</h2>

                    @if($project->user_id !== auth()->id())

                        @if(!$myCollaborationRequest)

                            <p class="muted">
                                ¿Te interesa participar en este proyecto?
                                Envía una solicitud al creador.
                            </p>

                            <form
                                action="{{ route('projects.collaboration.store', $project->slug) }}"
                                method="POST"
                                class="collaboration-form">

                                @csrf

                                <textarea
                                    name="message"
                                    maxlength="3000"
                                    placeholder="Cuéntale al creador cómo te gustaría colaborar..."
                                ></textarea>

                                <button class="btn btn-primary" type="submit">
                                    Solicitar colaboración
                                </button>

                            </form>

                        @else

                            <p>
                                Tu solicitud:
                            </p>

                            <span class="request-status {{ $myCollaborationRequest->status }}">
                                {{ ucfirst($myCollaborationRequest->status) }}
                            </span>

                            @if($myCollaborationRequest->status === 'pending')

                                <form
                                    action="{{ route('projects.collaboration.cancel', $myCollaborationRequest->id) }}"
                                    method="POST"
                                    style="margin-top:15px;">

                                    @csrf
                                    @method('PATCH')

                                    <button class="btn btn-danger" type="submit">
                                        Cancelar solicitud
                                    </button>

                                </form>

                            @elseif(in_array($myCollaborationRequest->status, ['rejected', 'cancelled']))

                                <p class="muted">
                                    Puedes enviar una nueva solicitud.
                                </p>

                                <form
                                    action="{{ route('projects.collaboration.store', $project->slug) }}"
                                    method="POST"
                                    class="collaboration-form">

                                    @csrf

                                    <textarea
                                        name="message"
                                        maxlength="3000"
                                        placeholder="Escribe un nuevo mensaje..."
                                    ></textarea>

                                    <button class="btn btn-primary" type="submit">
                                        Enviar nueva solicitud
                                    </button>

                                </form>

                            @endif

                        @endif

                    @else

                        <p class="muted">
                            Este es tu proyecto.
                        </p>

                    @endif

                </section>

            @endif


            {{-- SOLICITUDES RECIBIDAS DEL PROPIETARIO --}}
            @if($project->user_id === auth()->id())

                <section class="card">

                    <h2>
                        Solicitudes recibidas
                        ({{ $collaborationRequests->count() }})
                    </h2>

                    @forelse($collaborationRequests as $request)

                        <div class="request-item">

                            <strong>
                                {{ $request->user?->name }}
                            </strong>

                            <p class="muted">
                                {{ $request->created_at->format('d/m/Y') }}
                            </p>

                            <span class="request-status {{ $request->status }}">
                                {{ ucfirst($request->status) }}
                            </span>

                            @if($request->message)

                                <p class="description">
                                    {{ $request->message }}
                                </p>

                            @endif

                            @if($request->status === 'pending')

                                <div style="display:flex; gap:8px; flex-wrap:wrap;">

                                    <form
                                        action="{{ route('projects.collaboration.update', $request->id) }}"
                                        method="POST">

                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="accepted">

                                        <button class="btn btn-success" type="submit">
                                            Aceptar
                                        </button>

                                    </form>


                                    <form
                                        action="{{ route('projects.collaboration.update', $request->id) }}"
                                        method="POST">

                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="rejected">

                                        <button class="btn btn-danger" type="submit">
                                            Rechazar
                                        </button>

                                    </form>

                                </div>

                            @endif

                        </div>

                    @empty

                        <div class="empty-state">
                            No has recibido solicitudes.
                        </div>

                    @endforelse

                </section>

            @endif

        </aside>

    </div>

</div>

@endsection