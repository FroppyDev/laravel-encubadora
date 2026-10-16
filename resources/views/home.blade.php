<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio · Incubadora FIC</title>
    <style>
        *{box-sizing:border-box}body{margin:0;background:#f5f7fb;color:#243553;font:16px/1.6 Arial,Helvetica,sans-serif}header{background:#fff;border-bottom:1px solid #e4e8f0}.navigation{max-width:1100px;margin:auto;padding:20px 32px;display:flex;justify-content:space-between;align-items:center;gap:20px}.brand{font-size:23px;font-weight:700;color:#1769d2}button{border:1px solid #d9dfea;border-radius:8px;background:#fff;padding:10px 18px;color:#374151;font:inherit;cursor:pointer}button:hover{background:#f1f5ff}button:focus-visible{outline:2px solid #1975ef;outline-offset:3px}main{max-width:1100px;margin:48px auto;padding:0 32px}.welcome{padding:48px;border:1px solid #e4e8f0;border-radius:24px;background:linear-gradient(130deg,#edf4ff,#f4edff);overflow-wrap:anywhere}.eyebrow{color:#1769d2;font-weight:700;margin:0 0 12px}h1{font-size:clamp(26px,4vw,40px);line-height:1.2;margin:0 0 20px}p{max-width:640px;color:#65738a}.account{margin-top:28px;padding:24px;background:#fff;border:1px solid #e4e8f0;border-radius:16px;overflow-wrap:anywhere}.account h2{font-size:18px;margin:0 0 8px}.account p{margin:0}.verified{display:inline-block;margin-top:12px;color:#216b4a;background:#eaf7f0;padding:4px 12px;border-radius:999px;font-size:14px}@media(max-width:600px){.navigation{padding:16px;flex-wrap:wrap}main{margin:24px auto;padding:0 16px}.welcome{padding:28px 24px}.brand{font-size:20px}}
        .feed-tabs{max-width:1100px;margin:0 auto;padding:0 32px;display:flex;gap:28px;overflow-x:auto}
        .feed-tab{flex-shrink:0;border:0;border-bottom:3px solid transparent;border-radius:0;padding:14px 4px;background:transparent;color:#65738a;font-weight:700;white-space:nowrap}
        .feed-tab:hover{background:transparent;color:#1769d2;border-bottom-color:#c9dcf8}
        .feed-tab[aria-current="page"]{color:#1769d2;border-bottom-color:#1769d2}
        .feed-tab{text-decoration:none}
        input,textarea{width:100%;padding:11px 12px;border:1px solid #d9dfea;border-radius:8px;color:#243553;background:#fff;font:inherit}
        input:focus-visible,textarea:focus-visible,a:focus-visible,summary:focus-visible{outline:2px solid #1975ef;outline-offset:3px}
        .search-form{display:flex;align-items:center;gap:8px;flex:1;max-width:540px}.search-form input{min-width:0}.search-form button,.save-profile{background:#1769d2;color:white;border-color:#1769d2}
        .profile-form label{display:block;font-size:14px;font-weight:700;margin:16px 0}.profile-form textarea{resize:vertical;min-height:120px}.profile-form small{font-weight:400;color:#65738a}.profile-form summary{cursor:pointer;font-weight:700;color:#1769d2}.profile-form{padding-bottom:24px}.feedback{padding:12px;border-radius:8px;background:#eaf7f0;color:#216b4a}.errors{color:#a32020;background:#fff1f2;padding:12px 12px 12px 28px}.menu-close{align-self:flex-end;margin:12px 16px 0}.bio{white-space:pre-wrap}
        .project-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}.project-card{padding:24px;background:#fff;border:1px solid #e4e8f0;border-radius:16px;overflow-wrap:anywhere}.project-card h3{margin:8px 0}.project-card p{margin:8px 0}.project-meta{font-size:14px;color:#65738a}.pagination{display:flex;gap:20px;align-items:center;justify-content:center;margin-top:24px}.pagination a{color:#1769d2}.feed-heading{margin-top:36px}.empty-state{grid-column:1/-1;padding:32px;background:#fff;border-radius:16px}
        @media(max-width:600px){.search-form{flex-basis:100%;max-width:none}.project-grid{grid-template-columns:1fr}}
        .feed-tab:focus-visible{outline-offset:-3px}
        @media(max-width:600px){.feed-tabs{padding:0 16px;gap:20px}.feed-tab{font-size:14px}}

        /* ===== Menú desplegable ===== */
        .nav-left{display:flex;align-items:center;gap:14px;min-width:0}
        .menu-toggle{display:inline-flex;align-items:center;justify-content:center;width:42px;height:42px;padding:0}
        .menu-toggle svg{width:22px;height:22px}
        .menu-backdrop{position:fixed;inset:0;background:rgba(20,30,50,.4);opacity:0;visibility:hidden;transition:opacity .2s,visibility .2s;z-index:40}
        .menu-backdrop.open{opacity:1;visibility:visible}
        .side-menu{position:fixed;top:0;left:0;bottom:0;width:min(320px,85vw);background:#fff;box-shadow:4px 0 24px rgba(20,30,50,.15);transform:translateX(-100%);transition:transform .25s ease;z-index:50;display:flex;flex-direction:column;overflow-y:auto;visibility:hidden}
        .side-menu.open{transform:translateX(0);visibility:visible}
        .menu-user{display:flex;align-items:center;gap:16px;padding:28px 24px 20px;border-bottom:1px solid #e4e8f0}
        .menu-avatar{flex-shrink:0;width:52px;height:52px;border-radius:50%;background:#34d27b;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:20px}
        .menu-username{font-weight:700;color:#1f2937;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .menu-user-info{min-width:0;flex:1}.menu-user-info .menu-username{display:block}
        .menu-bio{margin:6px 0 0;font-size:13px;line-height:1.5;color:#65738a;white-space:pre-wrap;overflow-wrap:anywhere}
        .menu-section{padding:16px 24px 0}
        .menu-title{margin:0 0 4px;max-width:none;color:#374151;font-size:15px}
        .menu-list{list-style:none;margin:0;padding:0}
        .menu-link{display:flex;align-items:center;gap:10px;padding:12px 8px;border-radius:8px;color:#4b5563;text-decoration:none}
        .menu-link svg{width:16px;height:16px;flex-shrink:0}
        .menu-link:hover{background:#f1f5ff;color:#1769d2}
        .menu-link:focus-visible{outline:2px solid #1975ef;outline-offset:-2px}
        .menu-footer{margin-top:auto;padding:16px 24px 24px;border-top:1px solid #e4e8f0}
        .menu-footer button{width:100%;text-align:left;border:0;padding:12px 8px;color:#4b5563}
        @media(prefers-reduced-motion:reduce){.side-menu,.menu-backdrop{transition:none}}
        .profile-dialog{width:min(460px,calc(100% - 32px));max-height:calc(100dvh - 32px);padding:28px;border:1px solid #e4e8f0;border-radius:20px;color:#243553;background:#fff;box-shadow:0 24px 80px #14203233;overflow-y:auto}
        .profile-dialog::backdrop{background:rgba(20,30,50,.45)}
        .profile-dialog h2{margin:0;font-size:24px}.profile-dialog p{margin:8px 0 20px;font-size:14px}
        .profile-dialog .profile-form{padding:0}.profile-actions{display:flex;justify-content:flex-end;gap:10px;margin-top:24px;flex-wrap:wrap}
        .edit-profile{width:100%;text-align:left;border:0;color:#1769d2;font-weight:700}
    </style>
</head>
<body>
    <header><nav class="navigation" aria-label="Navegación principal">
        <div class="nav-left">
            <button class="menu-toggle" id="menuToggle" type="button" aria-label="Abrir menú" aria-expanded="false" aria-controls="sideMenu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <span class="brand">EncubadoraFIC</span>
        </div>
        <form class="search-form" method="GET" action="{{ route('home') }}" role="search">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input type="search" name="search" value="{{ $search }}" maxlength="100" aria-label="Buscar proyectos" placeholder="Buscar proyectos…">
            <button type="submit">Buscar</button>
        </form>
        </nav>
    <nav class="feed-tabs" aria-label="Explorar publicaciones">
        @foreach(['explore' => 'Explorar', 'popular' => 'Popular', 'recent' => 'Lo más reciente'] as $key => $label)
            <a class="feed-tab" href="{{ route('home', ['tab' => $key, 'search' => $search]) }}#inicio" @if($tab === $key) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
    </nav>
    </header>

    {{-- Menú lateral desplegable --}}
    <div class="menu-backdrop" id="menuBackdrop"></div>
    <aside class="side-menu" id="sideMenu" aria-label="Menú de usuario" aria-hidden="true">
        <button class="menu-close" id="menuClose" type="button">Cerrar menú</button>
        <div class="menu-user">
            <div class="menu-avatar" aria-hidden="true">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="menu-user-info">
                <span class="menu-username" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</span>
                @if(auth()->user()->bio)<p class="menu-bio">{{ auth()->user()->bio }}</p>@endif
            </div>
        </div>

        <div class="menu-section">
            <button class="edit-profile" id="editProfile" type="button" aria-haspopup="dialog" aria-controls="profileDialog">Editar perfil</button>
        </div>
        <div class="menu-section">
            <p class="menu-title">Colecciones</p>
            <ul class="menu-list">
                <li><a class="menu-link" href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>Mis proyectos</a></li>
                <li><a class="menu-link" href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>Proyectos guardados</a></li>
            </ul>
        </div>

        <div class="menu-section">
            <p class="menu-title">Categorías</p>
            <ul class="menu-list">
                <li><a class="menu-link" href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>Todos los proyectos</a></li>
                <li><a class="menu-link" href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>Arte</a></li>
                <li><a class="menu-link" href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>Multimedia</a></li>
                <li><a class="menu-link" href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>Educación</a></li>
                <li><a class="menu-link" href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>Entretenimiento</a></li>
                <li><a class="menu-link" href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>Videojuegos</a></li>
            </ul>
        </div>

        <div class="menu-footer">
            <form method="POST" action="/logout">@csrf<button type="submit">Cerrar sesión</button></form>
        </div>
    </aside>

    <dialog class="profile-dialog" id="profileDialog" aria-labelledby="profileTitle" aria-describedby="profileDescription">
        <h2 id="profileTitle">Editar perfil</h2>
        <p id="profileDescription">Actualiza tu nombre y cuéntanos un poco sobre ti.</p>
        @if($errors->profile->any())<ul class="errors" role="alert">@foreach($errors->profile->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
        <form class="profile-form" method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')
            <label>Nombre<input name="name" value="{{ old('name', auth()->user()->name) }}" maxlength="100" autocomplete="name" required autofocus></label>
            <label>Biografía<textarea name="bio" maxlength="1000" rows="4" aria-describedby="bioHint">{{ old('bio', auth()->user()->bio) }}</textarea><small id="bioHint">Cuéntanos sobre ti. Máximo 1000 caracteres.</small></label>
            <div class="profile-actions">
                <button id="cancelProfile" type="button">Cancelar</button>
                <button class="save-profile" type="submit">Guardar cambios</button>
            </div>
        </form>
    </dialog>

    <main id="inicio">
        @if(session('profile_status'))<p class="feedback" role="status">{{ session('profile_status') }}</p>@endif
        @if($errors->getBag('default')->any())<ul class="errors" role="alert">@foreach($errors->getBag('default')->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
        <section class="welcome">
            <p class="eyebrow">Página principal</p>
            <h1>Hola, {{ auth()->user()->name }}</h1>
            <p>Te damos la bienvenida a la comunidad de la FIC.</p>
            <p>Un espacio para conectar, compartir proyectos y desarrollar tus ideas.</p>
        </section>
        <section aria-labelledby="projects-title">
            <h2 class="feed-heading" id="projects-title">{{ ['explore' => 'Explorar proyectos', 'popular' => 'Proyectos populares', 'recent' => 'Lo más reciente'][$tab] }}</h2>
            <p>{{ ['explore' => 'Descubre los proyectos de la comunidad en orden alfabético.', 'popular' => 'Los proyectos con más reacciones de la comunidad.', 'recent' => 'Los proyectos publicados, del más nuevo al más antiguo.'][$tab] }}</p>
            @if($search !== '')<p>Resultados para «{{ $search }}». <a href="{{ route('home', ['tab' => $tab]) }}#inicio">Limpiar búsqueda</a></p>@endif
            @if($showPlaceholders)<p role="status">Proyectos de ejemplo para explorar la plataforma. Se mostrarán proyectos reales cuando la comunidad publique los suyos.</p>@endif
            <div class="project-grid">
                @forelse($projects as $project)
                    <article class="project-card">
                        @if($project->is_placeholder)<span class="verified">Proyecto de ejemplo</span><br>@endif
                        <span class="eyebrow">{{ $project->category?->name }}</span>
                        <h3>{{ $project->title }}</h3>
                        <p>{{ $project->summary }}</p>
                        <p class="project-meta">{{ $project->user?->name }} · {{ $project->created_at->format('d/m/Y') }} · {{ $project->reactions_count }} reacciones</p>
                        <details><summary>Ver descripción</summary><p class="bio">{{ $project->description }}</p></details>
                    </article>
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
    </main>
    <script>
        // Menú lateral
        const menuToggle = document.getElementById('menuToggle');
        const sideMenu = document.getElementById('sideMenu');
        const menuBackdrop = document.getElementById('menuBackdrop');

        function setMenu(open) {
            sideMenu.classList.toggle('open', open);
            menuBackdrop.classList.toggle('open', open);
            menuToggle.setAttribute('aria-expanded', String(open));
            menuToggle.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
            sideMenu.setAttribute('aria-hidden', String(!open));
            document.body.style.overflow = open ? 'hidden' : '';
            if (open) document.getElementById('menuClose').focus(); else menuToggle.focus();
        }

        menuToggle.addEventListener('click', () => setMenu(!sideMenu.classList.contains('open')));
        menuBackdrop.addEventListener('click', () => setMenu(false));
        document.getElementById('menuClose').addEventListener('click', () => setMenu(false));
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && sideMenu.classList.contains('open')) setMenu(false);
            if (e.key === 'Tab' && sideMenu.classList.contains('open')) {
                const items = [...sideMenu.querySelectorAll('a, button, input:not([type="hidden"]), textarea, summary')].filter(item => item.getClientRects().length);
                const first = items[0], last = items[items.length - 1];
                if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
                else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
            }
        });
        const profileDialog = document.getElementById('profileDialog');
        function openProfile() {
            setMenu(false);
            profileDialog.showModal();
            document.body.style.overflow = 'hidden';
        }
        document.getElementById('editProfile').addEventListener('click', openProfile);
        document.getElementById('cancelProfile').addEventListener('click', () => profileDialog.close());
        profileDialog.addEventListener('click', event => {
            const bounds = profileDialog.getBoundingClientRect();
            if (event.target === profileDialog && (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom)) profileDialog.close();
        });
        profileDialog.addEventListener('close', () => {
            profileDialog.querySelector('form').reset();
            setMenu(true);
            document.getElementById('editProfile').focus();
        });
        @if($errors->profile->any()) openProfile(); @endif
    </script>
</body>
</html>

