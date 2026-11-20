
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi perfil · EncubadoraFIC</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #F3F6FA;
            color: #243553;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
        }

        :root {
            --navy: #142D4E;
            --blue: #246BCE;
            --muted: #64748B;
            --border: #E4E8F0;
        }

        header {
            background: white;
            border-bottom: 1px solid var(--border);
        }

        .navigation {
            max-width: 1100px;
            margin: auto;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand {
            font-size: 22px;
            font-weight: 700;
            color: var(--blue);
            text-decoration: none;
        }

        .back-link {
            text-decoration: none;
            color: var(--navy);
            font-weight: 600;
            padding: 9px 15px;
            border: 1px solid var(--border);
            border-radius: 9px;
        }

        .back-link:hover {
            background: #F1F5FF;
        }

        main {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 24px 50px;
        }

        .page-heading {
            margin-bottom: 25px;
        }

        .page-heading h1 {
            color: var(--navy);
            font-size: 30px;
            margin: 0;
        }

        .page-heading p {
            color: var(--muted);
            margin: 5px 0 0;
        }

        .profile-card,
        .edit-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 25px;
        }

        /* Portada */

        .cover {
            height: 220px;
            position: relative;
            background:
                linear-gradient(120deg, #142D4E, #367DC8, #7EAFD3);
            overflow: hidden;
        }

        .cover::before {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(255,255,255,.08);
            top: -130px;
            right: 50px;
        }

        .cover::after {
            content: "";
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255,255,255,.07);
            bottom: -150px;
            left: 80px;
        }

        .cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            inset: 0;
        }

        /* Información del usuario */

        .profile-info {
            padding: 0 35px 30px;
        }

        .avatar-wrapper {
            margin-top: -55px;
            position: relative;
            width: 110px;
            height: 110px;
            border-radius: 50%;
            border: 5px solid white;
            background: #E6EFFA;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--blue);
            font-size: 38px;
            font-weight: bold;
        }

        .avatar-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-info h2 {
            color: var(--navy);
            font-size: 25px;
            margin: 12px 0 0;
            overflow-wrap: anywhere;
        }

        .email {
            color: var(--muted);
            font-size: 15px;
            margin: 3px 0 15px;
            overflow-wrap: anywhere;
        }

        .bio {
            color: var(--muted);
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            margin: 15px 0 0;
        }

        .divider {
            border: 0;
            border-top: 1px solid #EDF1F6;
            margin: 20px 0;
        }

        /* Formulario */

        .edit-card {
            padding: 30px 35px;
        }

        .edit-card h2 {
            color: var(--navy);
            font-size: 21px;
            margin: 0 0 5px;
        }

        .description {
            color: var(--muted);
            margin: 0 0 25px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            color: var(--navy);
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #D9DFEA;
            border-radius: 12px;
            font: inherit;
            font-size: 15px;
            color: #243553;
            background: white;
            transition: border .2s, box-shadow .2s;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 140px;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(36,107,206,.12);
        }

        .form-group small {
            display: block;
            color: var(--muted);
            font-size: 12px;
            margin-top: 6px;
        }

        .field-error {
            color: #B42318;
            font-size: 13px;
            margin-top: 5px;
        }

        .save-button {
            background: var(--blue);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px 25px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s;
        }

        .save-button:hover {
            background: #1957A9;
        }

        .save-button:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .feedback {
            padding: 13px 16px;
            background: #EAF7F0;
            color: #216B4A;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .errors {
            padding: 15px 35px;
            background: #FFF1F2;
            color: #A32020;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            flex-wrap: wrap;
        }

        .cancel-button {
            text-decoration: none;
            padding: 13px 22px;
            color: var(--navy);
            border: 1px solid var(--border);
            border-radius: 12px;
            font-weight: 600;
        }


        .image-upload {
            position: absolute;
            z-index: 2;
            right: 15px;
            bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .image-upload input,
        .avatar-upload input {
            display: none;
        }

        .image-button,
        .avatar-button {
            display: inline-block;
            padding: 9px 13px;
            border-radius: 9px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
        }

        .image-button {
            background: rgba(20,45,78,.8);
            color: white;
        }

        .avatar-upload {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 12px;
        }

        .avatar-button {
            background: #EAF2FF;
            color: #246BCE;
            border: 1px solid #D9E7FA;
        }

        .image-upload button,
        .avatar-upload button {
            padding: 9px 13px;
            border-radius: 9px;
            font-size: 13px;
        }

        @media(max-width:600px) {
            .image-upload {
                flex-wrap: wrap;
                justify-content: flex-end;
            }
        }


        @media(max-width: 600px) {
            .navigation {
                padding: 15px 16px;
            }

            main {
                margin-top: 25px;
                padding: 0 15px 30px;
            }

            .cover {
                height: 155px;
            }

            .profile-info {
                padding: 0 20px 25px;
            }

            .edit-card {
                padding: 24px 20px;
            }

            .page-heading h1 {
                font-size: 25px;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .form-actions button,
            .form-actions a {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>

<header>
    <nav class="navigation">
        <a href="{{ route('home') }}" class="brand">
            EncubadoraFIC
        </a>

        <a href="{{ route('home') }}" class="back-link">
            ← Volver al inicio
        </a>
    </nav>
</header>

<main>

    <div class="page-heading">
        <h1>Mi perfil</h1>
        <p>Administra tu información personal y personaliza tu perfil.</p>
    </div>

    @if(session('profile_status'))
        <div class="feedback" role="status">
            {{ session('profile_status') }}
        </div>
    @endif

    @if($errors->profile->any())
        <ul class="errors" role="alert">
            @foreach($errors->profile->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @php
        $user = auth()->user();

        $avatarUrl = $user->avatar_url;
        $coverUrl = $user->cover_image_url;

        $initial = mb_strtoupper(mb_substr($user->name, 0, 1));
    @endphp

    <!-- Tarjeta de presentación -->

    <section class="profile-card">

        
        <div class="cover">
            <img
                id="coverPreview"
                src="{{ $user->cover_image_url }}"
                alt="Portada del perfil"
            >

            <form
                action="{{ route('profile.images.update') }}"
                method="POST"
                enctype="multipart/form-data"
                class="image-upload cover-upload"
            >
                @csrf

                <label for="coverInput" class="image-button">
                    📷 Cambiar portada
                </label>

                <input
                    type="file"
                    id="coverInput"
                    name="cover"
                    accept="image/jpeg,image/png,image/webp"
                    required
                >

                <button type="submit">Guardar portada</button>
            </form>
        </div>


        <div class="profile-info">

            <div class="avatar-wrapper">
                @if($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="Foto de perfil">
                @else
                    {{ $initial }}
                @endif
            </div>


            <form
                action="{{ route('profile.images.update') }}"
                method="POST"
                enctype="multipart/form-data"
                class="avatar-upload"
            >
                @csrf

                <label for="avatarInput" class="avatar-button">
                    📷 Cambiar foto de perfil
                </label>

                <input
                    type="file"
                    id="avatarInput"
                    name="avatar"
                    accept="image/jpeg,image/png,image/webp"
                    required
                >

                <button type="submit" class="save-button">
                    Guardar fotografía
                </button>
            </form>


            <h2>{{ $user->name }}</h2>

            <p class="email">
                {{ $user->email }}
            </p>

            <hr class="divider">

            <p class="bio">
                {{ $user->bio ?: 'Comparte tus intereses y lo que te gustaría construir con la comunidad.' }}
            </p>

        </div>
    </section>

    <!-- Formulario de edición -->

    <section class="edit-card">

        <h2>Editar información</h2>

        <p class="description">
            Tu nombre y biografía ayudan a que otros estudiantes te conozcan.
        </p>

        <form
            id="profileForm"
            method="POST"
            action="{{ route('profile.update') }}"
        >
            @csrf
            @method('PATCH')

            <div class="form-group">

                <label for="name">Nombre</label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    maxlength="100"
                    autocomplete="name"
                    required
                >

                @error('name', 'profile')
                    <div class="field-error">{{ $message }}</div>
                @enderror

                <small id="nameCounter">
                    {{ mb_strlen(old('name', $user->name)) }}/100
                </small>

            </div>

            <div class="form-group">

                <label for="bio">Biografía</label>

                <textarea
                    id="bio"
                    name="bio"
                    maxlength="2000"
                    rows="5"
                >{{ old('bio', $user->bio) }}</textarea>

                @error('bio', 'profile')
                    <div class="field-error">{{ $message }}</div>
                @enderror

                <small id="bioCounter">
                    {{ mb_strlen(old('bio', $user->bio ?? '')) }}/2000
                </small>

            </div>

            <div class="form-actions">

                <a href="{{ route('home') }}" class="cancel-button">
                    Cancelar
                </a>

                <button type="submit" class="save-button" id="saveButton">
                    Guardar cambios
                </button>

            </div>

        </form>

    </section>

</main>

<script>
    const nameInput = document.getElementById('name');
    const bioInput = document.getElementById('bio');

    const nameCounter = document.getElementById('nameCounter');
    const bioCounter = document.getElementById('bioCounter');

    nameInput.addEventListener('input', () => {
        nameCounter.textContent = `${nameInput.value.length}/100`;
    });

    bioInput.addEventListener('input', () => {
        bioCounter.textContent = `${bioInput.value.length}/2000`;
    });

    document.getElementById('profileForm').addEventListener('submit', () => {
        const button = document.getElementById('saveButton');
        button.disabled = true;
        button.textContent = 'Guardando...';
    });


    const avatarInput = document.getElementById('avatarInput');
    const coverInput = document.getElementById('coverInput');

    avatarInput.addEventListener('change', function () {
        const file = this.files[0];

        if (file) {
            document.querySelector('.avatar-wrapper img').src =
                URL.createObjectURL(file);
        }
    });

    coverInput.addEventListener('change', function () {
        const file = this.files[0];

        if (file) {
            document.getElementById('coverPreview').src =
                URL.createObjectURL(file);
        }
    });

</script>

</body>
</html>
