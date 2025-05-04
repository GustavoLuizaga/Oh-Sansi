@push('styles')
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<link rel="stylesheet" href="{{ asset('css/convocatoria/convocatoria.css') }}">
<link rel="stylesheet" href="{{ asset('css/inscripcion/grupo.css') }}">
@endpush

<x-app-layout>
    <div class="p-6">
        <!-- Success Message -->
        @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        <!-- Header Section -->
        <div class="convocatoria-header">
            <h1><i class="fas fa-users"></i> Gestión de Grupos</h1>
        </div>

        <!-- Actions Container -->
        <div class="actions-container">
            <button type="button" class="btn-nueva-convocatoria" onclick="abrirModalCrearGrupo()">
                <i class="fas fa-plus-circle"></i> Nuevo Grupo
            </button>
        </div>

        <!-- Search and Filter -->
        <form action="{{ route('inscripcion.grupos') }}" method="GET" id="searchForm">
            <div class="search-filter-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Buscar grupo..." value="{{ request('search') }}">
                    <button type="submit" class="search-button py-1 px-2">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>

                <div class="filter-dropdown">
                    <label for="estado">Estado:</label>
                    <select id="estado" name="estado" onchange="document.getElementById('searchForm').submit();">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="incompleto" {{ request('estado') == 'incompleto' ? 'selected' : '' }}>Incompleto</option>
                        <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
            </div>
        </form>

        <!-- Table -->
        <table class="convocatoria-table">
            <thead>
                <tr>
                    <th>NOMBRE DEL GRUPO</th>
                    <th>MODALIDAD</th>
                    <th>DELEGACIÓN</th>
                    <th>CÓDIGO DE INVITACIÓN</th>
                    <th>ESTADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @forelse($grupos as $grupo)
                <tr>
                    <td>{{ $grupo->nombreGrupo }}</td>
                    <td>{{ ucfirst($grupo->modalidad) }}</td>
                    <td>{{ $grupo->delegacion->nombre }}</td>
                    <td><span class="codigo-invitacion">{{ $grupo->codigoInvitacion }}</span></td>
                    <td>
                        <span class="estado-badge estado-{{ strtolower($grupo->estado) }}">
                            <i class="fas fa-circle"></i> {{ strtoupper($grupo->estado) }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            @if($grupo->estado == 'incompleto')
                            <!-- Activar grupo -->
                            <a href="#" class="btn-action btn-approve" title="Activar"
                                onclick="event.preventDefault(); if(confirm('¿Está seguro de activar este grupo?')) document.getElementById('activate-form-{{ $grupo->id }}').submit();">
                                <i class="fas fa-check"></i>
                            </a>
                            <form id="activate-form-{{ $grupo->id }}" action="{{ route('inscripcion.grupos.update-status', $grupo->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="estado" value="activo">
                            </form>
                            
                            <!-- Eliminar grupo -->
                            <a href="#" class="btn-action btn-delete" title="Eliminar"
                                onclick="event.preventDefault(); if(confirm('¿Está seguro de eliminar este grupo?')) document.getElementById('delete-form-{{ $grupo->id }}').submit();">
                                <i class="fas fa-trash"></i>
                            </a>
                            <form id="delete-form-{{ $grupo->id }}" action="{{ route('inscripcion.grupos.destroy', $grupo->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            @elseif($grupo->estado == 'activo')
                            <!-- Cancelar grupo -->
                            <a href="#" class="btn-action btn-cancel" title="Cancelar"
                                onclick="event.preventDefault(); if(confirm('¿Está seguro de cancelar este grupo?')) document.getElementById('cancel-form-{{ $grupo->id }}').submit();">
                                <i class="fas fa-ban"></i>
                            </a>
                            <form id="cancel-form-{{ $grupo->id }}" action="{{ route('inscripcion.grupos.update-status', $grupo->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="estado" value="cancelado">
                            </form>
                            @elseif($grupo->estado == 'cancelado')
                            <!-- Reactivar grupo -->
                            <a href="#" class="btn-action btn-recover" title="Reactivar"
                                onclick="event.preventDefault(); if(confirm('¿Está seguro de reactivar este grupo?')) document.getElementById('reactivate-form-{{ $grupo->id }}').submit();">
                                <i class="fas fa-undo"></i>
                            </a>
                            <form id="reactivate-form-{{ $grupo->id }}" action="{{ route('inscripcion.grupos.update-status', $grupo->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="estado" value="activo">
                            </form>
                            
                            <!-- Eliminar grupo -->
                            <a href="#" class="btn-action btn-delete" title="Eliminar"
                                onclick="event.preventDefault(); if(confirm('¿Está seguro de eliminar este grupo?')) document.getElementById('delete-form-{{ $grupo->id }}').submit();">
                                <i class="fas fa-trash"></i>
                            </a>
                            <form id="delete-form-{{ $grupo->id }}" action="{{ route('inscripcion.grupos.destroy', $grupo->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No hay grupos disponibles</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="paginacion">
            <div class="pagination-container">
                {{ $grupos->links() }}
            </div>
        </div>
        
        <!-- Modal para crear grupo -->
        <div id="modalCrearGrupo" class="modal">
            <div class="modal-contenido">
                <button onclick="cerrarModalCrearGrupo()" class="modal-cerrar">✖</button>
                <h3 class="modal-titulo"><i class="fas fa-users"></i> Crear Nuevo Grupo</h3>
                
                <form id="formCrearGrupo" class="modal-form" method="POST" action="{{ route('inscripcion.grupos.store') }}">
                    @csrf
                    <div class="input-group">
                        <label for="nombreGrupo">Nombre del Grupo</label>
                        <input type="text" id="nombreGrupo" name="nombreGrupo" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="modalidad">Modalidad</label>
                        <select id="modalidad" name="modalidad" required>
                            <option value="">Seleccione una modalidad</option>
                            <option value="duo">Dúo</option>
                            <option value="equipo">Equipo</option>
                        </select>
                    </div>
                    
                    @if(!$esTutor)
                    <div class="input-group">
                        <label for="idDelegacion">Delegación</label>
                        <select id="idDelegacion" name="idDelegacion" required>
                            <option value="">Seleccione una delegación</option>
                            @foreach($delegaciones as $delegacion)
                            <option value="{{ $delegacion->idDelegacion }}">{{ $delegacion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <input type="hidden" name="esTutor" value="true">
                    @endif
                    
                    <div class="modal-actions">
                        <button type="button" class="btn-cancelar" onclick="cerrarModalCrearGrupo()">Cancelar</button>
                        <button type="submit" class="btn-guardar">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalCrearGrupo() {
            document.getElementById('modalCrearGrupo').style.display = 'flex';
        }
        
        function cerrarModalCrearGrupo() {
            document.getElementById('modalCrearGrupo').style.display = 'none';
        }
        
        // Cerrar modal al hacer clic fuera de él
        window.onclick = function(event) {
            const modal = document.getElementById('modalCrearGrupo');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</x-app-layout>