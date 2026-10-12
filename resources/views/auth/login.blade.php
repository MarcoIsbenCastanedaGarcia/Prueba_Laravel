<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — Sistema Escolar</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@600;700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--cerulean), var(--tropical-teal));
        }
        .login-card {
            background-color: var(--bg-surface);
            border-radius: 16px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            position: relative;
        }
        .login-title {
            text-align: center;
            color: var(--cerulean);
            margin-bottom: 0.5rem;
        }
        .login-subtitle {
            text-align: center;
            color: var(--text-muted);
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div style="position: absolute; top: 1rem; right: 1rem;">
            <button id="themeToggleBtn" class="theme-toggle-btn">
                <span id="themeIcon">🌙</span>
            </button>
        </div>

        <h1 class="login-title font-serif">Bienvenido</h1>
        <p class="login-subtitle">Sistema de Tareas y Evidencias</p>

        @if($errors->any())
            <div class="alert alert-danger" style="font-size: 0.875rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="usuario@escuela.edu.mx">
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                Ingresar al Sistema
            </button>
        </form>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>