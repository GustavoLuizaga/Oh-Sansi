@push('styles')
    <link rel="stylesheet" href="{{ asset('css/gestionCategorias.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/gestionCategorias.js') }}"></script>
@endpush

<x-app-layout>
    <x-slot name="header">
        <h1><i class="fas fa-tags"></i> {{ __('Gestión de Categorías') }}</h1>
    </x-slot>

    <div class="area-container">
        <!-- Action Bar -->
        <div class="action-bar">
            <button class="btn-new-area" data-bs-toggle="modal" data-bs-target="#nuevaCategoriaModal">
                <i class="fas fa-plus-circle"></i> Nueva Categoría
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
                <input type="text" placeholder="Buscar categoría...">
            </div>
            <div class="filter-dropdown">
                <select>
                    <option>Ordenar por</option>
                    <option>Nivel (A-Z)</option>
                    <option>Nivel (Z-A)</option>
                    <option>Fecha de creación</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <table class="areas-table">
            <thead>
                <tr>
                    <th>NIVEL/CATEGORÍA</th>
                    <th>GRADOS</th>
                    <th style="text-align: right;">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Primaria</td>
                    <td>
                        <div class="grades-list">
                            <span class="grade-pill">4to Primaria</span>
                            <span class="grade-pill">5to Primaria</span>
                            <span class="grade-pill">6to Primaria</span>
                        </div>
                    </td>
                    <td class="action-cell">
                        <button class="btn-action btn-edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>Secundaria</td>
                    <td>
                        <div class="grades-list">
                            <span class="grade-pill">1ro Secundaria</span>
                            <span class="grade-pill">2do Secundaria</span>
                            <span class="grade-pill">3ro Secundaria</span>
                            <span class="grade-pill">4to Secundaria</span>
                            <span class="grade-pill">5to Secundaria</span>
                            <span class="grade-pill">6to Secundaria</span>
                        </div>
                    </td>
                    <td class="action-cell">
                        <button class="btn-action btn-edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-action btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Modal para Nueva Categoria -->
    <div class="modal fade" id="nuevaCategoriaModal" tabindex="-1" aria-labelledby="nuevaCategoriaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-4" id="nuevaCategoriaModalLabel">Nueva Categoría</h2>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Formulario principal para la categoría -->
                    <form id="formNuevaCategoria" class="mb-4">
                        <div class="mb-3">
                            <label for="nombreCategoria" class="form-label">Nombre del Nivel/Categoría</label>
                            <input type="text" class="form-control" id="nombreCategoria" title="Mínimo 5 caracteres, máximo 20, sin números ni símbolos especiales">
                            <div class="form-text">Mínimo 5 caracteres, máximo 20, sin números ni símbolos especiales</div>
                        </div>
                    </form>

                    <!-- Contenedor para los grados -->
                    <div id="gradosContainer" class="mb-3">
                        <!-- Primer grado (sin botón de eliminar) -->
                        <div class="grado-item mb-3 d-flex align-items-center gap-2">
                            <select name="grados[]" class="form-select flex-grow-1" required>
                                <option value="" disabled selected>Selecciona un grado</option>
                                <optgroup label="Primaria">
                                    <option value="1p">1ro de Primaria</option>
                                    <option value="2p">2do de Primaria</option>
                                    <option value="3p">3ro de Primaria</option>
                                    <option value="4p">4to de Primaria</option>
                                    <option value="5p">5to de Primaria</option>
                                    <option value="6p">6to de Primaria</option>
                                </optgroup>
                                <optgroup label="Secundaria">
                                    <option value="1s">1ro de Secundaria</option>
                                    <option value="2s">2do de Secundaria</option>
                                    <option value="3s">3ro de Secundaria</option>
                                    <option value="4s">4to de Secundaria</option>
                                    <option value="5s">5to de Secundaria</option>
                                    <option value="6s">6to de Secundaria</option>
                                </optgroup>
                            </select>
                            <!-- Botón de eliminar (oculto inicialmente) -->
                            <button type="button" class="btn-remove btn btn-outline-danger btn-sm" style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Botón para agregar nuevo grado -->
                    <button type="button" id="agregarGradoBtn" class="btn btn-outline-dark w-50 mb-3">
                        + Agregar Grado
                    </button>
                </div>
                
                <div class="modal-footer ">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark" form="formNuevaCategoria">Guardar</button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
