<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/usuarios/verUsuario.css') }}">

    <!-- Header Section -->
    <div class="ver-usuario-header py-2">
        <h1><i class="fas fa-user"></i> {{ __('Detalles del Usuario') }}</h1>
        <div class="header-actions">
            <a href="{{ route('usuarios') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="ver-usuario-container">
        <div class="ver-usuario-content">
            <!-- Información Personal -->
            <div class="detail-section">
                <div class="detail-section-title">Información Personal</div>
                <div class="detail-row">
                    <div class="detail-item">
                        <div class="detail-label">Nombre Completo</div>
                        <div class="detail-value">{{ $usuario->name }} {{ $usuario->apellidoPaterno }} {{ $usuario->apellidoMaterno }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">CI</div>
                        <div class="detail-value">{{ $usuario->ci }}</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-item">
                        <div class="detail-label">Fecha de Nacimiento</div>
                        <div class="detail-value">{{ date('d/m/Y', strtotime($usuario->fechaNacimiento)) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Género</div>
                        <div class="detail-value">{{ $usuario->genero == 'M' ? 'Masculino' : 'Femenino' }}</div>
                    </div>
                </div>
            </div>

            <!-- Información de Cuenta -->
            <div class="detail-section">
                <div class="detail-section-title">Información de Cuenta</div>
                <div class="detail-row">
                    <div class="detail-item">
                        <div class="detail-label">Correo Electrónico</div>
                        <div class="detail-value">{{ $usuario->email }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Estado de Verificación</div>
                        <div class="detail-value">
                            @if($usuario->email_verified_at)
                                <span class="badge bg-success text-white">Verificado</span>
                            @else
                                <span class="badge bg-warning text-dark">No Verificado</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-item">
                        <div class="detail-label">Fecha de Registro</div>
                        <div class="detail-value">{{ date('d/m/Y H:i', strtotime($usuario->created_at)) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Última Actualización</div>
                        <div class="detail-value">{{ date('d/m/Y H:i', strtotime($usuario->updated_at)) }}</div>
                    </div>
                </div>
            </div>

            <!-- Roles -->
            <div class="detail-section">
                <div class="detail-section-title">Roles Asignados</div>
                <div class="roles-list">
                    @forelse($usuario->roles as $rol)
                        <span class="role-badge">{{ $rol->nombre }}</span>
                    @empty
                        <p>No tiene roles asignados</p>
                    @endforelse
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="actions-bar">
                <a href="{{ route('usuarios.editar', $usuario->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro que desea eliminar este usuario?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>