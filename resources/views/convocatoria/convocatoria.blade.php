<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/convocatoria/convocatoria.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
            <h1><i class="fas fa-clipboard-list"></i> Gestión de Convocatorias</h1>
        </div>

        <!-- Actions Container (Add and Export buttons in the same row) -->
        <!-- Update the Nueva Convocatoria link in the Actions Container section -->
        <div class="actions-container">
            <a href="{{ route('convocatorias.crear') }}" class="btn-nueva-convocatoria">
                <i class="fas fa-plus-circle"></i> Nueva Convocatoria
            </a>
            
            <div class="export-buttons">
                <a href="#" class="btn-export" id="exportPdf">
                    <i class="fas fa-file-pdf"></i> Descargar PDF
                </a>
                <a href="#" class="btn-export" id="exportExcel">
                    <i class="fas fa-file-excel"></i> Descargar Excel
                </a>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="search-filter-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar convocatoria...">
            </div>
            <div class="filter-dropdown">
                <label for="estado">Estado:</label>
                <select id="estado">
                    <option value="">Todos</option>
                    <option value="publicada">Publicada</option>
                    <option value="borrador">Borrador</option>
                    <option value="cancelada">Cancelada</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <table class="convocatoria-table">
            <thead>
                <tr>
                    <th>NOMBRE</th>
                    <th>DESCRIPCIÓN</th>
                    <th>FECHA INICIO</th>
                    <th>FECHA FIN</th>
                    <th>ESTADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Olimpiada Matemática 2024</td>
                    <td>Competencia nacional de matemáticas</td>
                    <td>01 May, 2024</td>
                    <td>30 Jun, 2024</td>
                    <td>
                        <span class="estado-badge estado-publicada">
                            <i class="fas fa-circle"></i> PUBLICADA
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="#" class="btn-action btn-details" title="Detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="btn-action btn-edit" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="btn-action btn-delete" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Olimpiada Física 2024</td>
                    <td>Competencia nacional de física</td>
                    <td>15 May, 2024</td>
                    <td>15 Jul, 2024</td>
                    <td>
                        <span class="estado-badge estado-borrador">
                            <i class="fas fa-circle"></i> BORRADOR
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="#" class="btn-action btn-details" title="Detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="btn-action btn-edit" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="btn-action btn-approve" title="Aprobar">
                                <i class="fas fa-check"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Olimpiada Química 2024</td>
                    <td>Competencia nacional de química</td>
                    <td>01 Jun, 2024</td>
                    <td>30 Jul, 2024</td>
                    <td>
                        <span class="estado-badge estado-cancelada">
                            <i class="fas fa-circle"></i> CANCELADA
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="#" class="btn-action btn-details" title="Detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="#" class="btn-action btn-edit" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="#" class="btn-action btn-delete" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination -->
        <ul class="pagination">
            <li><a href="#"><i class="fas fa-chevron-left"></i></a></li>
            <li><a href="#">1</a></li>
            <li class="active"><a href="#">2</a></li>
            <li><a href="#">3</a></li>
            <li><a href="#">4</a></li>
            <li><a href="#">5</a></li>
            <li><a href="#"><i class="fas fa-chevron-right"></i></a></li>
        </ul>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Export PDF button
            document.getElementById('exportPdf').addEventListener('click', function(e) {
                e.preventDefault();
                // Add PDF export functionality here
                alert('Exportando a PDF...');
            });
            
            // Export Excel button
            document.getElementById('exportExcel').addEventListener('click', function(e) {
                e.preventDefault();
                // Add Excel export functionality here
                alert('Exportando a Excel...');
            });
        });
    </script>
</x-app-layout>
