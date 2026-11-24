@extends('layouts.navbar')

@section('styles')
    @parent
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f5f7fb;
            color: #243553;
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        header {
            background: white;
            border-bottom: 1px solid #e4e8f0;
            padding: 20px 32px;
        }

        .navigation {
            max-width: 1000px;
            margin: auto;
        }

        main {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 24px;
        }

        .back {
            color: #1769d2;
            text-decoration: none;
            font-weight: bold;
        }

        .project-header {
            background: linear-gradient(130deg, #edf4ff, #f4edff);
            padding: 48px;
            border-radius: 20px;
            margin-top: 24px;
        }

        .category {
            color: #1769d2;
            font-weight: bold;
        }

        h1 {
            font-size: 36px;
            margin: 12px 0;
        }

        .meta {
            color: #65738a;
            font-size: 14px;
        }

        .content {
            background: white;
            border: 1px solid #e4e8f0;
            border-radius: 16px;
            padding: 32px;
            margin-top: 24px;
        }

        .content h2 {
            margin-top: 0;
        }

        .description {
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .project-cover {
            width: 100%;
            max-height: 420px;
            margin-bottom: 28px;
            border-radius: 14px;
            overflow: hidden;
            background: #edf2f7;
        }

        .project-cover img {
            display: block;
            width: 100%;
            max-height: 420px;
            object-fit: cover;
            object-position: center;
        }

        @media(max-width: 600px) {
            .project-header {
                padding: 28px;
            }

            h1 {
                font-size: 28px;
            }
        }
    </style>
@endsection

@section('content-navbar')
    <a href="{{ route('home') }}" class="back">
            ← Volver a explorar proyectos
        </a>

        <section class="project-header">


            @if ($project->image_url)
                <div class="project-cover">
                    <img
                        src="{{ $project->image_url }}"
                        alt="Portada de {{ $project->title }}"
                    >
                </div>
            @endif

            <span class="category">
                {{ $project->category?->name }}
            </span>

            <h1>{{ $project->title }}</h1>

            <p>{{ $project->summary }}</p>

            <div class="meta">
                Por {{ $project->user?->name }}
                · {{ $project->created_at->format('d/m/Y') }}
                · {{ $project->reactions_count ?? 0 }} reacciones
            </div>

        </section>

        <section class="content">

            <h2>Acerca del proyecto</h2>

            <div class="description">
                {{ $project->description }}
            </div>

        </section>
@endsection