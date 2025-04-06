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
                @forelse($convocatorias as $convocatoria)
                <tr>
                    <td>{{ $convocatoria->nombre }}</td>
                    <td>{{ Str::limit($convocatoria->descripcion, 50) }}</td>
                    <td>{{ \Carbon\Carbon::parse($convocatoria->fechaInicio)->format('d M, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($convocatoria->fechaFin)->format('d M, Y') }}</td>
                    <td>
                        <span class="estado-badge estado-{{ strtolower($convocatoria->estado) }}">
                            <i class="fas fa-circle"></i> {{ strtoupper($convocatoria->estado) }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('convocatorias.ver', $convocatoria->idConvocatoria) }}" class="btn-action btn-details" title="Detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('convocatorias.editar', $convocatoria->idConvocatoria) }}" class="btn-action btn-edit" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($convocatoria->estado == 'Borrador')
                            <a href="#" class="btn-action btn-approve" title="Aprobar">
                                <i class="fas fa-check"></i>
                            </a>
                            @else
                            <a href="#" class="btn-action btn-delete" title="Eliminar" 
                               onclick="event.preventDefault(); if(confirm('¿Está seguro de eliminar esta convocatoria?')) document.getElementById('delete-form-{{ $convocatoria->idConvocatoria }}').submit();">
                                <i class="fas fa-trash"></i>
                            </a>
                            <form id="delete-form-{{ $convocatoria->idConvocatoria }}" action="{{ route('convocatorias.eliminar', $convocatoria->idConvocatoria) }}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No hay convocatorias disponibles</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination - only show when more than 10 convocatorias -->
        @if(count($convocatorias) > 10)
        <ul class="pagination">
            <li><a href="#"><i class="fas fa-chevron-left"></i></a></li>
            <li><a href="#">1</a></li>
            <li class="active"><a href="#">2</a></li>
            <li><a href="#">3</a></li>
            <li><a href="#">4</a></li>
            <li><a href="#">5</a></li>
            <li><a href="#"><i class="fas fa-chevron-right"></i></a></li>
        </ul>
        @endif
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
