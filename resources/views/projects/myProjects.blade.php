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

@section('content-navbar')
        <section aria-labelledby="projects-title">
            <p role="status">Mis proyectos</p>
            <div class="project-grid">
                @forelse($projects as $project)
                    <a href="{{ $project->status === 'published'
                        ? route('projects.show', $project->slug)
                        : route('projects.create', ['slug' => $project->slug]) }}"
                        class="project-card">

                        <h3>{{ $project->title }}</h3>

                        <p>{{ $project->summary }}</p>

                        <p class="project-meta">
                            {{ $project->user?->name }}
                            · {{ $project->created_at->format('d/m/Y') }}
                            · {{ $project->reactions_count }} reacciones
                        </p>

                    </a>
                @empty
                    <p class="empty-state">{{ $search !== '' ? 'No encontramos proyectos con esa búsqueda. Prueba con otras palabras.' : 'Todavía no hay proyectos publicados.' }}</p>
                @endforelse
            </div>
        </section>

@endsection