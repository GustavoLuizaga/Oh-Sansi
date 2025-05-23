@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gestionAreas.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/gestionAreas.js') }}"></script>
@endpush
<x-app-layout>
    <x-slot name="header">
        <h1><i class="fas fa-th-large"></i> {{ __('Gestión de Áreas') }}</h1>
    </x-slot>

    <div class="area-container">
        <!-- Action Bar -->
        <div class="action-bar">
            <button class="btn-new-area" data-bs-toggle="modal" data-bs-target="#nuevaAreaModal">
                <i class="fas fa-plus-circle"></i> Nueva Área
            </button>
            <div class="export-buttons">
                <button class="btn-export">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </button>
                <button class="btn-export">
                    <i class="fas fa-file-excel"></i> Descargar Excel
                </button>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="search-filter">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" 
                        id="searchArea" 
                        name="search" 
                        placeholder="Buscar área..."
                        value="{{ request('search') }}">
            </div>
            <div class="filter-dropdown">
                <select id="orderBy" name="orderBy">
                <option value="todos" {{ request('orderBy') == 'todos' || !request('orderBy') ? 'selected' : '' }}>Todos</option>
                    <option value="nombre_asc" {{ request('orderBy') == 'nombre_asc' ? 'selected' : '' }}>Nombre (A-Z)</option>
                    <option value="nombre_desc" {{ request('orderBy') == 'nombre_desc' ? 'selected' : '' }}>Nombre (Z-A)</option>

                </select>
            </div>
        </div>

        <!-- Table -->
        <table class="areas-table">
            <thead>
                <tr>
                    <th>NOMBRE DEL ÁREA</th>
                    <th style="text-align: right;">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($areas) && count($areas) > 0)
                    @foreach($areas as $area)
                    <tr>
                        <td>{{ $area->nombre }}</td>
                        <td class="action-cell">
                            <button class="btn-action btn-edit" 
                                    data-id="{{ $area->idArea }}"
                                    data-nombre="{{ $area->nombre }}" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#EditarAreaModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action btn-delete" 
                                    data-id="{{ $area->idArea }}" 
                                    data-nombre="{{ $area->nombre }}"  
                                    data-bs-toggle="modal" 
                                    data-bs-target="#ConfirmarBorradoModal">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="2" class="text-center">No hay áreas registradas</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Modal para crear Nueva Área -->
    <div class="modal fade" id="nuevaAreaModal" tabindex="-1" aria-labelledby="nuevaAreaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" >
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="nuevaAreaModalLabel">Nueva Área</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevaArea" action="{{ route('areas.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nombreArea" class="form-label">Nombre del Área</label>
                            <input type="text" class="form-control" id="nombreArea" name="nombre" required minlength="5" >
                            <div class="form-text">Mínimo 5 caracteres, maximo 20, sin números ni simbolos especiales</div>
                        </div>

                        <div class="modal-footer justify-content-start">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-dark">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmacion de Eliminacion-->
    <div class="modal fade" id="ConfirmarBorradoModal" tabindex="-1" aria-labelledby="ConfirmarBorradoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="ConfirmarBorradoModalLabel">Eliminar Área</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de eliminar el área: <strong><span id="nombreAreaEliminar"></span></strong>?
                    Esta operación no se puede revertir.
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmarEliminar">Sí, estoy seguro</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para Editar Área -->
    <div class="modal fade" id="EditarAreaModal" tabindex="-1" aria-labelledby="EditarAreaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" >
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="EditarAreaModalLabel">Editar Área</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarArea" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nombreAreaEdit" class="form-label">Nombre del Área</label>
                            <input type="text" class="form-control" id="nombreAreaEdit" name="nombre" required minlength="5">
                            <div class="form-text">Mínimo 5 caracteres, maximo 20, sin números ni simbolos especiales</div>
                        </div>

                        <div class="modal-footer justify-content-start">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-dark">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

