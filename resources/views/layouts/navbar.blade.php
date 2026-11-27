    @extends('layouts.base')
    
    @section('styles')
    @parent
    <style>
        *{box-sizing:border-box}
        body{margin:0;background:#f5f7fb;color:#243553;font:16px/1.6 Arial,Helvetica,sans-serif}
        header{background:#fff;border-bottom:1px solid #e4e8f0}.navigation{max-width:1100px;margin:auto;padding:20px 32px;display:flex;justify-content:space-between;align-items:center;gap:20px}.brand{font-size:23px;font-weight:700;color:#1769d2}button{border:1px solid #d9dfea;border-radius:8px;background:#fff;padding:10px 18px;color:#374151;font:inherit;cursor:pointer}button:hover{background:#f1f5ff}button:focus-visible{outline:2px solid #1975ef;outline-offset:3px}main{max-width:1100px;margin:48px auto;padding:0 32px}.welcome{padding:48px;border:1px solid #e4e8f0;border-radius:24px;background:linear-gradient(130deg,#edf4ff,#f4edff);overflow-wrap:anywhere}.eyebrow{color:#1769d2;font-weight:700;margin:0 0 12px}h1{font-size:clamp(26px,4vw,40px);line-height:1.2;margin:0 0 20px}p{max-width:640px;color:#65738a}.account{margin-top:28px;padding:24px;background:#fff;border:1px solid #e4e8f0;border-radius:16px;overflow-wrap:anywhere}.account h2{font-size:18px;margin:0 0 8px}.account p{margin:0}.verified{display:inline-block;margin-top:12px;color:#216b4a;background:#eaf7f0;padding:4px 12px;border-radius:999px;font-size:14px}@media(max-width:600px){.navigation{padding:16px;flex-wrap:wrap}main{margin:24px auto;padding:0 16px}.welcome{padding:28px 24px}.brand{font-size:20px}}
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
        
        .menu-link[aria-current="page"] {
            background: #eaf2ff;
            color: #1769d2;
            font-weight: 700;
            border-left: 3px solid #1769d2;
            padding-left: 5px;
        }

        .menu-link[aria-current="page"]:hover {
            background: #dceaff;
            color: #1258b5;
        }

        .menu-user {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 28px 24px 20px;
            border-bottom: 1px solid #e4e8f0;
        }

        .menu-user-link {
            text-decoration: none;
            cursor: pointer;
            transition: background .2s;
        }

        .menu-user-link:hover {
            background: #f1f5ff;
        }

        .menu-user-link:focus-visible {
            outline: 2px solid #1975ef;
            outline-offset: -3px;
        }

        .menu-avatar {
            flex-shrink: 0;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #34d27b;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 20px;
            overflow: hidden;
        }

        .menu-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .menu-username {
            font-weight: 700;
            color: #1f2937;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }


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
        .project-card {
            display: block;
            text-decoration: none;
            color: inherit;
            cursor: pointer;
        }
        
        
    </style>
    @endsection

    @section('header')
    <header><nav class="navigation" aria-label="Navegación principal">
            <div class="nav-left">
                <button class="menu-toggle" id="menuToggle" type="button" aria-label="Abrir menú" aria-expanded="false" aria-controls="sideMenu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <span class="brand">EncubadoraFIC</span>
            </div>
            @yield('navbar-buscador')
            </nav>
        @yield('navbar-opciones')
    </header>
    @endsection

    @section('content')

        
        {{-- Menú lateral desplegable --}}
        <div class="menu-backdrop" id="menuBackdrop"></div>
        <aside class="side-menu" id="sideMenu" aria-label="Menú de usuario" aria-hidden="true">
            <button class="menu-close" id="menuClose" type="button">Cerrar menú</button>
            
            <a href="{{ route('profile.profile') }}" class="menu-user menu-user-link">

                <div class="menu-avatar">
                    @if(auth()->user()->avatar_url)
                        <img
                            src="{{ auth()->user()->avatar_url }}"
                            alt=""
                        >
                    @else
                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>

                <div class="menu-user-info">
                    <span
                        class="menu-username"
                        title="{{ auth()->user()->name }}"
                    >
                        {{ auth()->user()->name }}
                    </span>
                </div>

            </a>

            <div class="menu-section">
                <p class="menu-title">Colecciones</p>
                <ul class="menu-list">
                    <li><a class="menu-link" href="{{ route('projects.myProjects') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>Mis proyectos</a></li>
                    <li><a class="menu-link" href="#"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>Proyectos guardados</a></li>
                </ul>
            </div>

            <div class="menu-section">
                <p class="menu-title">Categorías</p>

                <ul class="menu-list">

                    {{-- Todas las categorías --}}
                    <li>
                        <a
                            href="{{ route('home', array_merge(request()->query(), ['category' => null])) }}"
                            class="menu-link"
                            @if(!request('category')) aria-current="page" @endif
                        >
                            <svg viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 5l7 7-7 7"/>
                            </svg>

                            Todos los proyectos
                        </a>
                    </li>

                    {{-- Categorías disponibles --}}
                    @forelse($categories as $category)

                        <li>
                            <a
                                href="{{ route('home', array_merge(request()->query(), ['category' => $category->id])) }}"
                                class="menu-link"
                                @if(request('category') == $category->id) aria-current="page" @endif
                            >
                                <svg viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 5l7 7-7 7"/>
                                </svg>

                                {{ $category->name }}
                            </a>
                        </li>

                    @empty

                        <li class="menu-bio">No hay categorías activas.</li>

                    @endforelse

                </ul>
            </div>

            @if(auth()->user()->role === 'admin')
            <div class="menu-section"><a class="menu-link" href="{{ route('admin.dashboard') }}">Panel de administración</a></div>
            @endif
            <div class="menu-footer">
                <form method="POST" action="/logout">@csrf<button type="submit">Cerrar sesión</button></form>
            </div>
        </aside>

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
        </script>
    
        @yield('content-navbar')
    
    @endsection

