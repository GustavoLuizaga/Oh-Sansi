<nav>
    <!-- Logo -->
    <div class="logo">
        <a href="{{ route('dashboard') }}">
            Oh! Sansi
        </a>
    </div>

    <!-- Botón de Logout -->
    <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit">
            🚪 Log Out
        </button>
    </form>
</nav>
