<header>
    <nav>
        <button class="menu-toggle" id="menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="logo">OH! <span>SANSI</span></div>

        <div class="nav-links" id="nav-links">
            <div class="menu-items">
                <a href="{{ url('/') }}">Inicio</a>
                <a href="{{ route('convocatoria.publica') }}">Convocatoria</a>
                <a href="#">Reglamento</a>
            </div>
            <div class="menu-footer">
                <button id="theme-toggle-mobile" class="theme-toggle mobile-only">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </div>
        <div class="auth-buttons">
            <button id="theme-toggle" class="theme-toggle desktop-only">
                <i class="fas fa-moon"></i>
            </button>
            @auth
            <a href="{{ url('/dashboard') }}" class="get-started">
                <i class="fas fa-user"></i> Dashboard
            </a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="join-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </button>
            </form>
            @else
            <a href="{{ route('login') }}" class="login-link">
                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
            </a>
            @endauth
        </div>
    </nav>
</header>