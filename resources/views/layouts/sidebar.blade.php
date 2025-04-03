<!-- Sidebar -->
<div class="sidebar">
    <div class="menu">
        <div class="menu-seccion">
            <h4 class="titulo-menu"><i class="fas fa-home"></i> <span>INICIO</span></h4>
            <ul>
                <li><a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
                <li><a href="#"><i class="fas fa-bell"></i> <span>Notificaciones</span></a></li>
            </ul>
        </div>
        <div class="menu-seccion">
            <h4 class="titulo-menu"><i class="fas fa-tasks"></i> <span>GESTIÓN</span></h4>
            <ul>
                <li><a href="#" class="{{ request()->is('delegaciones') ? 'active' : '' }}"><i class="fas fa-building"></i> <span>Delegaciones</span></a></li>
                <li><a href="#" class="{{ request()->is('convocatoria') ? 'active' : '' }}"><i class="fas fa-bullhorn"></i> <span>Convocatoria</span></a></li>
                <li><a href="#" class="{{ request()->is('registro') ? 'active' : '' }}"><i class="fas fa-user-plus"></i> <span>Registro</span></a></li>
                <li><a href="#" class="{{ request()->is('area-categoria') ? 'active' : '' }}"><i class="fas fa-tags"></i> <span>Área y Categoría</span></a></li>
            </ul>
        </div>
        <div class="menu-seccion">
            <h4 class="titulo-menu"><i class="fas fa-cog"></i> <span>CONFIGURACIÓN</span></h4>
            <ul>
                <li><a href="#" class="{{ request()->is('perfil') ? 'active' : '' }}"><i class="fas fa-user-cog"></i> <span>Perfil</span></a></li>
                <li><a href="{{ route('servicios') }}" class="{{ request()->is('servicios') ? 'active' : '' }}" class="{{ request()->is('seguridad') ? 'active' : '' }}"><i class="fas fa-shield-alt"></i> <span>Seguridad</span></a></li>
            </ul>
        </div>
    </div>
</div>