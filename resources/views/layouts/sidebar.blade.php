<!-- resources\views\layouts\sidebar.blade.php -->
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    {{-- Botón de toggle para colapsar/expandir el sidebar --}}
    <div class="sidebar-inner">
        {{-- Botón de toggle dentro del contenedor --}}
        <button class="btn-toggle-sidebar" id="btn-toggle-sidebar">
            <i class="fas fa-angle-double-left" id="toggle-icon"></i>
        </button>


        <div class="menu">
            <div class="menu-seccion">
                <h4 class="titulo-menu" data-title="INICIO"><i class="fas fa-home"></i> <span class="menu-text">INICIO</span></h4>
                <ul>
                    @if($iusIds->contains(config('ius.DASHBOARD')))
                    <li data-title="Dashboard"><a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}" data-title="Dashboard"><i class="fas fa-tachometer-alt"></i> <span class="menu-text">Dashboard</span></a></li>
                    @endif
                    @if($iusIds->contains(config('ius.NOTIFICACIONES')))
                    <li data-title="Notificaciones"><a href="#" data-title="Notificaciones"><i class="fas fa-bell"></i> <span class="menu-text">Notificaciones</span></a></li>
                    @endif
                </ul>
            </div>

            <div class="menu-seccion">
                <h4 class="titulo-menu" data-title="GESTIÓN"><i class="fas fa-tasks"></i> <span class="menu-text">GESTIÓN</span></h4>
                <ul>
                    @if($iusIds->contains(config('ius.DELEGACIONES')))
                    <li data-title="Delegaciones"><a href="{{ route('delegaciones') }}" class="{{ request()->is('delegaciones') ? 'active' : '' }}" data-title="Delegaciones"><i class="fas fa-building"></i> <span class="menu-text">Delegaciones</span></a></li>
                    @endif
                    @if($iusIds->contains(config('ius.CONVOCATORIA')))
                    <li data-title="Convocatoria"><a href="{{ route('convocatoria') }}" class="{{ request()->is('convocatoria') ? 'active' : '' }}" data-title="Convocatoria"><i class="fas fa-bullhorn"></i> <span class="menu-text">Convocatoria</span></a></li>
                    @endif
                    @if($iusIds->contains(config('ius.REGISTRO')))
                    <li data-title="Registro"><a href="#" class="{{ request()->is('registro') ? 'active' : '' }}" data-title="Registro"><i class="fas fa-user-plus"></i> <span class="menu-text">Registro</span></a></li>
                    @endif
                    @if($iusIds->contains(config('ius.AREA_CATEGORIA')))
                    <li data-title="Área y Categoría"><a href="{{ route('areasCategorias') }}" class="{{ request()->is('area-categoria') ? 'active' : '' }}" data-title="Área y Categoría"><i class="fas fa-tags"></i> <span class="menu-text">Área y Categoría</span></a></li>
                    @endif
                    @if($iusIds->contains(config('ius.INSCRIPCION_ESTUDIANTE')))
                    <li data-title="Inscripción Estudiante"><a href="{{ route('inscripcion.estudiante') }}" class="{{ request()->is('inscripcion/estudiante') ? 'active' : '' }}" data-title="Inscripción Estudiante"><i class="fas fa-user-graduate"></i> <span class="menu-text">Inscripción Estudiante</span></a></li>
                    @endif
                    @if($iusIds->contains(config('ius.INSCRIPCION_ESTUDIANTE')))
                    <li data-title="Imprimir Formulario"><a href="{{ route('inscripcion.estudiante.imprimirFormularioInscripcion') }}" class="{{ request()->is('inscripcion/estudiante/imprimirFormularioInscripcion') ? 'active' : '' }}" data-title="Imprimir Formulario"><i class="fas fa-print"></i> <span class="menu-text">Imprimir Formulario</span></a></li>
                    @endif
                    @if($iusIds->contains(config('ius.INSCRIPCION_TUTOR')))
                    <li data-title="Registro Tutor"><a href="{{ route('inscripcion.tutor') }}" class="{{ request()->is('inscripcion/tutor') ? 'active' : '' }}" data-title="Registro Tutor"><i class="fas fa-chalkboard-teacher"></i> <span class="menu-text">Registro Tutor</span></a></li>
                    @endif
                    @if($iusIds->contains(config('ius.ESTUDIANTES')))
                    <li data-title="Estudiantes"><a href="{{ route('estudiantes.lista') }}" class="{{ request()->is('/estudiantes') ? 'active' : '' }}" data-title="Estudiantes"><i class="fas fa-users"></i> <span class="menu-text">Estudiantes</span></a></li>
                    @endif
                    @if($iusIds->contains(config('ius.DELEGADOS')))
                    <li data-title="Delegados"><a href="{{ route('delegado') }}" class="{{ request()->is('/delegado') ? 'active' : '' }}" data-title="Delegados"><i class="fas fa-user-tie"></i> <span class="menu-text">Delegados</span></a></li>
                    @endif
                    @if($iusIds->contains(config('ius.USUARIOS')))
                    <li data-title="Usuarios"><a href="{{ route('usuarios') }}" class="{{ request()->is('/usuarios') ? 'active' : '' }}" data-title="Usuarios"><i class="fas fa-user-friends"></i> <span class="menu-text">Usuarios</span></a></li>
                    @endif
                    @if($iusIds->contains(config('ius.VERIFICACIONMANUAL')))
                    <li data-title="Verificacion Manual"><a href="{{ route('verificacionManual.comprobanteDePago') }}" class="{{ request()->is('/VerificacionManual/ComprobanteDePago') ? 'active' : '' }}" data-title="VerificacionManual"><i class="fas fa-tasks"></i> <span class="menu-text">Verificacion Manual</span></a></li>
                    @endif
                    @if($iusIds->contains(config('ius.BACKUP')))
                    <li data-title="Respaldo y Logs"><a href="{{ route('backup') }}" class="{{ request()->is('/backup') ? 'active' : '' }}" data-title="Backup"><i class="fas fa-database"></i> <span class="menu-text">Respaldo y Logs</span></a></li>
                    @endif
                </ul>
            </div>

            <div class="menu-seccion">
                <h4 class="titulo-menu" data-title="CONFIGURACIÓN"><i class="fas fa-cog"></i> <span class="menu-text">CONFIGURACIÓN</span></h4>
                <ul>
                    @if($iusIds->contains(config('ius.PERFIL')))
                    <li data-title="Perfil"><a href="#" class="{{ request()->is('perfil') ? 'active' : '' }}" data-title="Perfil"><i class="fas fa-user-cog"></i> <span class="menu-text">Perfil</span></a></li>
                    @endif
                    @if($iusIds->contains(config('ius.SEGURIDAD')))
                    <li data-title="Roles y Permisos"><a href="{{ route('servicios') }}" class="{{ request()->is('servicios') ? 'active' : '' }}" data-title="Roles y Permisos"><i class="fas fa-shield-alt"></i> <span class="menu-text">Roles y Permisos</span></a></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>