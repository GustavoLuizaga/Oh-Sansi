@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gestionAreas.css') }}">
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
                <input type="text" placeholder="Buscar área...">
            </div>
            <div class="filter-dropdown">
                <select>
                    <option>Ordenar por</option>
                    <option>Nombre (A-Z)</option>
                    <option>Nombre (Z-A)</option>
                    <option>Fecha de creación</option>
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
                {{-- @foreach($areas as $area)
                <tr>
                    <td>{{ $area->nombre }}</td>
                    <td class="action-cell">
                        <button class="btn-action btn-edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach --}}
            </tbody>
        </table>
    </div>

    <!-- Modal para Nueva Área -->
    <div class="modal fade" id="nuevaAreaModal" tabindex="-1" aria-labelledby="nuevaAreaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" >
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="nuevaAreaModalLabel">Nueva Área</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevaArea" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nombreArea" class="form-label">Nombre del Área</label>
                            <input type="text" class="form-control" id="nombreArea" name="nombre" required>
                            <div class="form-text">Mínimo 5 caracteres, maximo 20, sin números ni simbolos especiales</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer justify-content-start">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark" form="formNuevaArea" >Guardar</button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>