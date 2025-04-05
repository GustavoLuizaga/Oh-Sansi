<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/delegacion/delegacion.css') }}">
    
    <div class="delegaciones-container">
        <div class="delegaciones-header">
            <div class="header-top-row">
                <h1>Lista de Colegios</h1>
                <a href="{{ route('delegaciones.agregar') }}" class="add-button">
                    <i class="fas fa-plus"></i> Agregar Colegio
                </a>
            </div>
            
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            
            <div class="delegaciones-search-bar">
                <div class="search-input">
                    <input type="text" placeholder="Buscar por nombre o código">
                </div>
                
                <button class="search-button">
                    <i class="fas fa-search"></i>
                </button>
                
                <button class="export-button pdf">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                
                <button class="export-button excel">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
                
                <select class="filter-select">
                    <option value="">Departamento</option>
                    <option value="La Paz">La Paz</option>
                    <option value="Santa Cruz">Santa Cruz</option>
                    <option value="Cochabamba">Cochabamba</option>
                    <option value="Oruro">Oruro</option>
                    <option value="Potosí">Potosí</option>
                    <option value="Tarija">Tarija</option>
                    <option value="Chuquisaca">Chuquisaca</option>
                    <option value="Beni">Beni</option>
                    <option value="Pando">Pando</option>
                </select>
                
                <select class="filter-select">
                    <option value="">Provincia</option>
                    <!-- Se cargaría dinámicamente -->
                </select>
                
                <select class="filter-select">
                    <option value="">Municipio</option>
                    <!-- Se cargaría dinámicamente -->
                </select>
            </div>
        </div>
        
        <table class="delegaciones-table">
            <thead>
                <tr>
                    <th>Código SIE</th>
                    <th>Nombre de Colegio</th>
                    <th>Departamento</th>
                    <th>Provincia</th>
                    <th>Municipio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($delegaciones as $delegacion)
                <tr>
                    <td>{{ $delegacion->codigo_sie }}</td>
                    <td>{{ $delegacion->nombre }}</td>
                    <td>{{ $delegacion->departamento }}</td>
                    <td>{{ $delegacion->provincia }}</td>
                    <td>{{ $delegacion->municipio }}</td>
                    <td class="actions">
                        <a href="#" class="action-button view">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="#" class="action-button edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" class="action-button delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No hay colegios registrados</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="pagination">
            <a href="#" class="pagination-arrow">
                <i class="fas fa-chevron-left"></i>
            </a>
            <a href="#" class="pagination-item active">1</a>
            <a href="#" class="pagination-item">2</a>
            <a href="#" class="pagination-item">3</a>
            <a href="#" class="pagination-item">10</a>
            <a href="#" class="pagination-arrow">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>
</x-app-layout>
