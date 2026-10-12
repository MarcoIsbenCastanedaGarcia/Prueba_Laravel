<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Tareas y Evidencias')</title>
    
    <!-- Fuentes Google: Noto Serif & Quicksand -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,100..900;1,100..900&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    
    <!-- CSS del Proyecto -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    <div class="app-container">
        <!-- Sidebar Navigation Bar -->
        <aside class="sidebar">
            <div class="brand font-serif">
                🏫 Control Escolar
            </div>
            <nav>
                <ul class="nav-list">
                    @if(auth()->user()->esDocente())
                        <li class="nav-item {{ request()->routeIs('docente.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('docente.dashboard') }}">📊 Panel Principal</a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('docente.alumnos.*') ? 'active' : '' }}">
                            <a href="{{ route('docente.alumnos.index') }}">👥 Alumnos</a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('docente.tareas.*') ? 'active' : '' }}">
                            <a href="{{ route('docente.tareas.index') }}">📝 Tareas</a>
                        </li>
                    @else
                        <li class="nav-item {{ request()->routeIs('alumno.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('alumno.dashboard') }}">📌 Tareas Pendientes</a>
                        </li>
                        <li class="nav-item {{ request()->routeIs('alumno.historial') ? 'active' : '' }}">
                            <a href="{{ route('alumno.historial') }}">📚 Historial y Notas</a>
                        </li>
                    @endif
                </ul>
            </nav>
        </aside>

        <!-- Main Structure -->
        <div class="main-content">
            <!-- Header -->
            <header class="header">
                <div>
                    <h2>@yield('header-title', 'Panel de Control')</h2>
                    <small style="color: var(--text-muted)">Usuario: {{ auth()->user()->nombre }} ({{ ucfirst(auth()->user()->role) }})</small>
                </div>

                <div class="user-controls">
                    <!-- Botón Modo Oscuro/Claro -->
                    <button id="themeToggleBtn" class="theme-toggle-btn">
                        <span id="themeIcon">🌙</span>
                        <span id="themeText">Modo Oscuro</span>
                    </button>

                    <!-- Botón Cierre de Sesión -->
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="logout-btn">Cerrar Sesión</button>
                    </form>
                </div>
            </header>

            <!-- Main Article / Body -->
            <article class="content-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </article>

            <!-- Footer -->
            <footer class="footer">
                <p>&copy; {{ date('Y') }} Sistema de Tareas y Evidencias — Grupo Único | Desarrollado con Laravel 13</p>
            </footer>
        </div>
    </div>

    <!-- JS del Proyecto -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>