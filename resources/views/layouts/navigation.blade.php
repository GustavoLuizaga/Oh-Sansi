<nav>
    <!-- Logo -->
    <div class="logo">
        <a href="{{ route('dashboard') }}">
            Oh! <span>Sansi</span>
        </a>
    </div>

    <!-- User Dropdown -->
    <div class="user-dropdown">
        <button type="button" class="user-menu-button" onclick="toggleDropdown()">
            <i class="fas fa-user"></i>
            {{ Auth::user()->name }}
            @if(Auth::user()->roles->first())
                <span>({{ Auth::user()->roles->first()->nombre }})</span>
            @endif
            <i class="fas fa-chevron-down"></i>
        </button>
        
        <div class="dropdown-menu" id="userDropdown">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item">
                    <i class="fas fa-sign-out-alt"></i>
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
</nav>

<style>
.user-dropdown {
    position: relative;
    display: inline-block;
}

.user-menu-button {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: none;
    border: none;
    color: var(--dark-color);
    cursor: pointer;
}

.user-menu-button:hover {
    color: var(--primary-color);
}

.dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    background: white;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    min-width: 200px;
    z-index: 1000;
}

.dropdown-menu.show {
    display: block;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 12px 16px;
    border: none;
    background: none;
    cursor: pointer;
    color: var(--dark-color);
}

.dropdown-item:hover {
    background-color: var(--complementary-light);
    color: var(--primary-color);
}
</style>

<script>
function toggleDropdown() {
    document.getElementById('userDropdown').classList.toggle('show');
}

// Cerrar el dropdown cuando se hace click fuera
window.onclick = function(event) {
    if (!event.target.matches('.user-menu-button') && !event.target.matches('.user-menu-button *')) {
        var dropdowns = document.getElementsByClassName('dropdown-menu');
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}
</script>
