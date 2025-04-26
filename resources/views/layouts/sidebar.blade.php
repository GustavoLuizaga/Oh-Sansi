<!-- Sidebar -->
<div class="sidebar">
    <div class="menu">
        <div class="menu-seccion">
            <h4 class="titulo-menu"><i class="fas fa-home"></i> <span>INICIO</span></h4>
            <ul>
                @if($iusIds->contains(config('ius.DASHBOARD')))
                    <li><a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                @endif
                @if($iusIds->contains(config('ius.NOTIFICACIONES')))
                    <li><a href="#"><i class="fas fa-bell"></i> <span>Notificaciones</span></a></li>
                @endif
            </ul>
        </div>

        <div class="menu-seccion">
            <h4 class="titulo-menu"><i class="fas fa-tasks"></i> <span>GESTIÓN</span></h4>
            <ul>
                @if($iusIds->contains(config('ius.DELEGACIONES')))
                    <li><a href="{{ route('delegaciones') }}" class="{{ request()->is('delegaciones') ? 'active' : '' }}"><i class="fas fa-building"></i> <span>Delegaciones</span></a></li>
                @endif
                @if($iusIds->contains(config('ius.CONVOCATORIA')))
                    <li><a href="{{ route('convocatoria') }}" class="{{ request()->is('convocatoria') ? 'active' : '' }}"><i class="fas fa-bullhorn"></i> <span>Convocatoria</span></a></li>
                @endif
                @if($iusIds->contains(config('ius.REGISTRO')))
                    <li><a href="#" class="{{ request()->is('registro') ? 'active' : '' }}"><i class="fas fa-user-plus"></i> <span>Registro</span></a></li>
                @endif
                @if($iusIds->contains(config('ius.AREA_CATEGORIA')))
                    <li><a href="{{ route('areasCategorias') }}" class="{{ request()->is('area-categoria') ? 'active' : '' }}"><i class="fas fa-tags"></i> <span>Área y Categoría</span></a></li>
                @endif
                @if($iusIds->contains(config('ius.INSCRIPCION_ESTUDIANTE')))
                    <li><a href="{{ route('inscripcion.estudiante') }}" class="{{ request()->is('inscripcion/estudiante') ? 'active' : '' }}"><i class="fas fa-user-graduate"></i> <span>Inscripción Estudiante</span></a></li>
                @endif
                @if($iusIds->contains(config('ius.INSCRIPCION_TUTOR')))
                    <li><a href="{{ route('inscripcion.tutor') }}" class="{{ request()->is('inscripcion/tutor') ? 'active' : '' }}"><i class="fas fa-chalkboard-teacher"></i> <span>Registro</span></a></li>
                @endif
            </ul>
        </div>

        <div class="menu-seccion">
            <h4 class="titulo-menu"><i class="fas fa-cog"></i> <span>CONFIGURACIÓN</span></h4>
            <ul>
                @if($iusIds->contains(config('ius.PERFIL')))
                    <li><a href="#" class="{{ request()->is('perfil') ? 'active' : '' }}"><i class="fas fa-user-cog"></i> <span>Perfil</span></a></li>
                @endif
                @if($iusIds->contains(config('ius.SEGURIDAD')))
                    <li><a href="{{ route('servicios') }}" class="{{ request()->is('servicios') ? 'active' : '' }}"><i class="fas fa-shield-alt"></i> <span>Seguridad</span></a></li>
                @endif
            </ul>
        </div>
    </div>
</div>

{{-- 

<!-- Sidebar con botón de colapso -->
<div class="sidebar" id="sidebar">
    <div class="toggle-btn" id="sidebarToggle">
        <i class="fas fa-circle-chevron-left" id="toggleIcon"></i>
    </div>
    
    <div class="menu">
        <div class="menu-seccion">
            <h4 class="titulo-menu"><i class="fas fa-home"></i> <span class="menu-text">INICIO</span></h4>
            <ul>
                @if($iusIds->contains(config('ius.DASHBOARD')))
                    <li><a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> <span class="menu-text">Dashboard</span></a></li>
                @endif
                @if($iusIds->contains(config('ius.NOTIFICACIONES')))
                    <li><a href="#"><i class="fas fa-bell"></i> <span class="menu-text">Notificaciones</span></a></li>
                @endif
            </ul>
        </div>

        <div class="menu-seccion">
            <h4 class="titulo-menu"><i class="fas fa-tasks"></i> <span class="menu-text">GESTIÓN</span></h4>
            <ul>
                @if($iusIds->contains(config('ius.DELEGACIONES')))
                    <li><a href="{{ route('delegaciones') }}" class="{{ request()->is('delegaciones') ? 'active' : '' }}"><i class="fas fa-building"></i> <span class="menu-text">Delegaciones</span></a></li>
                @endif
                @if($iusIds->contains(config('ius.CONVOCATORIA')))
                    <li><a href="{{ route('convocatoria') }}" class="{{ request()->is('convocatoria') ? 'active' : '' }}"><i class="fas fa-bullhorn"></i> <span class="menu-text">Convocatoria</span></a></li>
                @endif
                @if($iusIds->contains(config('ius.REGISTRO')))
                    <li><a href="#" class="{{ request()->is('registro') ? 'active' : '' }}"><i class="fas fa-user-plus"></i> <span class="menu-text">Registro</span></a></li>
                @endif
                @if($iusIds->contains(config('ius.AREA_CATEGORIA')))
                    <li><a href="{{ route('areasCategorias') }}" class="{{ request()->is('area-categoria') ? 'active' : '' }}"><i class="fas fa-tags"></i> <span class="menu-text">Área y Categoría</span></a></li>
                @endif
                @if($iusIds->contains(config('ius.INSCRIPCION_ESTUDIANTE')))
                    <li><a href="{{ route('inscripcion.estudiante') }}" class="{{ request()->is('inscripcion/estudiante') ? 'active' : '' }}"><i class="fas fa-user-graduate"></i> <span class="menu-text">Inscripción Estudiante</span></a></li>
                @endif
                @if($iusIds->contains(config('ius.INSCRIPCION_TUTOR')))
                    <li><a href="{{ route('inscripcion.tutor') }}" class="{{ request()->is('inscripcion/tutor') ? 'active' : '' }}"><i class="fas fa-chalkboard-teacher"></i> <span class="menu-text">Registro</span></a></li>
                @endif
            </ul>
        </div>

        <div class="menu-seccion">
            <h4 class="titulo-menu"><i class="fas fa-cog"></i> <span class="menu-text">CONFIGURACIÓN</span></h4>
            <ul>
                @if($iusIds->contains(config('ius.PERFIL')))
                    <li><a href="#" class="{{ request()->is('perfil') ? 'active' : '' }}"><i class="fas fa-user-cog"></i> <span class="menu-text">Perfil</span></a></li>
                @endif
                @if($iusIds->contains(config('ius.SEGURIDAD')))
                    <li><a href="{{ route('servicios') }}" class="{{ request()->is('servicios') ? 'active' : '' }}"><i class="fas fa-shield-alt"></i> <span class="menu-text">Seguridad</span></a></li>
                @endif
            </ul>
        </div>
    </div>
</div>

<style>
:root {
    --sidebar-width: 250px;
    --sidebar-collapsed-width: 70px;
}

/* Estilos para el botón de toggle */
.toggle-btn {
    position: absolute;
    right: -1px;
    top: 50%;
    transform: translateY(-50%);
    background-color: var(--primary-color);
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 100;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.toggle-btn:hover {
    transform: translateY(-50%) scale(1.1);
}

.sidebar {
    /* position: fixed;
    top: 0;
    left: 0;
    height: 100%; */
    width: var(--sidebar-width);
    background-color: var(--secondary-dark);
    overflow-x: hidden;
    transition: width 0.3s ease;
    z-index: 999;
}

.sidebar.collapsed {
    width: var(--sidebar-collapsed-width);
}

.sidebar.collapsed .menu-text {
    display: none;
}

.sidebar.collapsed .titulo-menu span {
    display: none;
}

.sidebar.collapsed .menu ul li {
    margin-right: 5px;
    justify-content: center;
}

.sidebar.collapsed .menu ul li a {
    justify-content: center;
}

/* Ajustar el contenido principal cuando se colapsa el sidebar */
.main-content {
    transition: margin-left 0.3s ease;
    margin-left: var(--sidebar-width);
}

body.sidebar-collapsed .main-content {
    margin-left: var(--sidebar-collapsed-width);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const toggleIcon = document.getElementById('toggleIcon');
    const body = document.body;
    
    // Verificar si hay una preferencia guardada en localStorage
    const sidebarState = localStorage.getItem('sidebarState');
    if (sidebarState === 'collapsed') {
        toggleSidebar();
    }
    
    sidebarToggle.addEventListener('click', function() {
        toggleSidebar();
    });
    
    function toggleSidebar() {
        sidebar.classList.toggle('collapsed');
        body.classList.toggle('sidebar-collapsed');
        
        // Cambiar el icono según el estado
        if (sidebar.classList.contains('collapsed')) {
            toggleIcon.classList.remove('fa-circle-chevron-left');
            toggleIcon.classList.add('fa-circle-chevron-right');
            localStorage.setItem('sidebarState', 'collapsed');
        } else {
            toggleIcon.classList.remove('fa-circle-chevron-right');
            toggleIcon.classList.add('fa-circle-chevron-left');
            localStorage.setItem('sidebarState', 'expanded');
        }
    }
});
</script> --}}