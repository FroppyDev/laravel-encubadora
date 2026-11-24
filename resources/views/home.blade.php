@extends('layouts.navbar')

@section('styles')
    @parent
    <style>

        .button-project {
            display: inline-block;
            padding: 12px 24px;
            background-color: #1769d2;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }

    </style>

@endsection
@section('navbar-buscador')

    <form class="search-form" method="GET" action="{{ route('home') }}" role="search">

        {{-- Conservar la pestaña seleccionada --}}
        <input type="hidden" name="tab" value="{{ $tab }}">

        {{-- Conservar la categoría seleccionada --}}
        <input type="hidden" name="category" value="{{ $categoryId }}">

        {{-- Búsqueda --}}
        <input
            type="search"
            name="search"
            value="{{ $search }}"
            maxlength="100"
            aria-label="Buscar proyectos"
            placeholder="Buscar proyectos…"
        >

        <button type="submit">Buscar</button>
    </form>

@endsection

@section('navbar-opciones')

    <nav class="feed-tabs" aria-label="Explorar publicaciones">

        @foreach([
            'explore' => 'Explorar',
            'popular' => 'Popular',
            'recent' => 'Lo más reciente'
        ] as $key => $label)

            <a
                class="feed-tab"
                href="{{ route('home', array_merge(request()->query(), ['tab' => $key])) }}#inicio"
                @if($tab === $key) aria-current="page" @endif
            >
                {{ $label }}
            </a>

        @endforeach

    </nav>

@endsection

@section('content-navbar')
        @if(session('profile_status'))<p class="feedback" role="status">{{ session('profile_status') }}</p>@endif
        @if($errors->getBag('default')->any())<ul class="errors" role="alert">@foreach($errors->getBag('default')->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
        <section class="welcome">
            <p class="eyebrow">Página principal</p>
            <h1>Hola, {{ auth()->user()->name }}</h1>
            <p>Te damos la bienvenida a la comunidad de la FIC.</p>
            <p>Un espacio para conectar, compartir proyectos y desarrollar tus ideas.</p>

            <div class="actions">
                <a href="{{ route('projects.create') }}" class="button-project">Publicar proyecto</a>
            </div>
        </section>
        <section aria-labelledby="projects-title">
            <h2 class="feed-heading" id="projects-title">{{ ['explore' => 'Explorar proyectos', 'popular' => 'Proyectos populares', 'recent' => 'Lo más reciente'][$tab] }}</h2>
            <p>{{ ['explore' => 'Descubre los proyectos de la comunidad en orden alfabético.', 'popular' => 'Los proyectos con más reacciones de la comunidad.', 'recent' => 'Los proyectos publicados, del más nuevo al más antiguo.'][$tab] }}</p>
            @if($search !== '')
                <p>
                    Resultados para «{{ $search }}».

                    <a href="{{ route('home', array_merge(request()->query(), ['search' => null])) }}#inicio">
                        Limpiar búsqueda
                    </a>
                </p>
            @endif            

            @if($showPlaceholders)<p role="status">Proyectos de ejemplo para explorar la plataforma. Se mostrarán proyectos reales cuando la comunidad publique los suyos.</p>@endif
            <div class="project-grid">
                @forelse($projects as $project)
                    <a href="{{ route('projects.show', $project->slug) }}" class="project-card">
                        @if($project->is_placeholder)<span class="verified">Proyecto de ejemplo</span><br>@endif
                        <span class="eyebrow">{{ $project->category?->name }}</span>
                        <h3>{{ $project->title }}</h3>
                        <p>{{ $project->summary }}</p>
                        <p class="project-meta">{{ $project->user?->name }} · {{ $project->created_at->format('d/m/Y') }} · {{ $project->reactions_count }} reacciones</p>
                    </a>
                @empty
                    <p class="empty-state">{{ $search !== '' ? 'No encontramos proyectos con esa búsqueda. Prueba con otras palabras.' : 'Todavía no hay proyectos publicados.' }}</p>
                @endforelse
            </div>
            @if($projects->hasPages())
                <nav class="pagination" aria-label="Páginas de proyectos">
                    @if($projects->previousPageUrl())<a href="{{ $projects->previousPageUrl() }}#inicio">Anterior</a>@endif
                    <span>Página {{ $projects->currentPage() }} de {{ $projects->lastPage() }}</span>
                    @if($projects->nextPageUrl())<a href="{{ $projects->nextPageUrl() }}#inicio">Siguiente</a>@endif
                </nav>
            @endif
        </section>

@endsection