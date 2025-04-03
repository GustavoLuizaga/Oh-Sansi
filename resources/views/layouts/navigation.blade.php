<nav>
    <!-- Logo -->
    <div class="logo">
        <a href="{{ route('dashboard') }}">
            Oh! Sansi
        </a>
    </div>

    <!-- Enlaces de navegación -->
    <ul class="nav-links">
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
                🏠 Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('servicios') }}" class="{{ request()->is('servicios') ? 'active' : '' }}">
                ⚙️ Servicios
            </a>
        </li>
    </ul>

    <!-- Botón de Logout -->
    <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit">
            🚪 Log Out
        </button>
    </form>
</nav>
