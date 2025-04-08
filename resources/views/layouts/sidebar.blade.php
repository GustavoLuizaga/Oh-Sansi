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
