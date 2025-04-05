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
            
            <form action="{{ route('delegaciones') }}" method="GET" id="filterForm">
                <div class="delegaciones-search-bar">
                    <div class="search-input">
                        <input type="text" name="search" placeholder="Buscar por nombre o código SIE" value="{{ request('search') }}">
                    </div>
                    
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                    
                    <button type="button" class="export-button pdf" id="exportPdf">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                    
                    <button type="button" class="export-button excel" id="exportExcel">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                    
                    <select class="filter-select" name="departamento" id="departamento">
                        <option value="">Departamento</option>
                        <option value="La Paz" {{ request('departamento') == 'La Paz' ? 'selected' : '' }}>La Paz</option>
                        <option value="Santa Cruz" {{ request('departamento') == 'Santa Cruz' ? 'selected' : '' }}>Santa Cruz</option>
                        <option value="Cochabamba" {{ request('departamento') == 'Cochabamba' ? 'selected' : '' }}>Cochabamba</option>
                        <option value="Oruro" {{ request('departamento') == 'Oruro' ? 'selected' : '' }}>Oruro</option>
                        <option value="Potosí" {{ request('departamento') == 'Potosí' ? 'selected' : '' }}>Potosí</option>
                        <option value="Tarija" {{ request('departamento') == 'Tarija' ? 'selected' : '' }}>Tarija</option>
                        <option value="Chuquisaca" {{ request('departamento') == 'Chuquisaca' ? 'selected' : '' }}>Chuquisaca</option>
                        <option value="Beni" {{ request('departamento') == 'Beni' ? 'selected' : '' }}>Beni</option>
                        <option value="Pando" {{ request('departamento') == 'Pando' ? 'selected' : '' }}>Pando</option>
                    </select>
                    
                    <select class="filter-select" name="provincia" id="provincia">
                        <option value="">Provincia</option>
                        @if(request('provincia'))
                            <option value="{{ request('provincia') }}" selected>{{ request('provincia') }}</option>
                        @endif
                    </select>
                    
                    <select class="filter-select" name="municipio" id="municipio">
                        <option value="">Municipio</option>
                        @if(request('municipio'))
                            <option value="{{ request('municipio') }}" selected>{{ request('municipio') }}</option>
                        @endif
                    </select>
                </div>
            </form>
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
                        <a href="{{ route('delegaciones.ver', $delegacion->codigo_sie) }}" class="action-button view">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('delegaciones.editar', $delegacion->codigo_sie) }}" class="action-button edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" class="action-button delete" data-id="{{ $delegacion->codigo_sie }}">
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
            {{ $delegaciones->appends(request()->query())->links() }}
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const departamentoSelect = document.getElementById('departamento');
            const provinciaSelect = document.getElementById('provincia');
            const municipioSelect = document.getElementById('municipio');
            const filterForm = document.getElementById('filterForm');
            
            // Datos de provincias por departamento
            const provinciasPorDepartamento = {
                'La Paz': ['Murillo', 'Omasuyos', 'Pacajes', 'Camacho', 'Muñecas', 'Larecaja', 'Franz Tamayo', 'Ingavi', 'Loayza', 'Inquisivi', 'Sud Yungas', 'Los Andes', 'Aroma', 'Nor Yungas', 'Abel Iturralde', 'Bautista Saavedra', 'Manco Kapac', 'Gualberto Villarroel', 'José Manuel Pando'],
                'Santa Cruz': ['Andrés Ibáñez', 'Ignacio Warnes', 'José Miguel de Velasco', 'Ichilo', 'Chiquitos', 'Sara', 'Cordillera', 'Vallegrande', 'Florida', 'Obispo Santistevan', 'Ñuflo de Chávez', 'Ángel Sandoval', 'Manuel María Caballero', 'Germán Busch', 'Guarayos'],
                'Cochabamba': ['Cercado', 'Campero', 'Ayopaya', 'Esteban Arce', 'Arani', 'Arque', 'Capinota', 'Germán Jordán', 'Quillacollo', 'Chapare', 'Tapacarí', 'Carrasco', 'Mizque', 'Punata', 'Bolívar', 'Tiraque'],
                'Oruro': ['Cercado', 'Abaroa', 'Carangas', 'Sajama', 'Litoral', 'Poopó', 'Pantaleón Dalence', 'Ladislao Cabrera', 'Sabaya', 'Saucarí', 'Tomás Barrón', 'Sud Carangas', 'San Pedro de Totora', 'Sebastián Pagador', 'Mejillones', 'Nor Carangas'],
                'Potosí': ['Tomás Frías', 'Rafael Bustillo', 'Cornelio Saavedra', 'Chayanta', 'Charcas', 'Nor Chichas', 'Alonso de Ibáñez', 'Sud Chichas', 'Nor Lípez', 'Sud Lípez', 'José María Linares', 'Antonio Quijarro', 'Bernardino Bilbao', 'Daniel Campos', 'Modesto Omiste', 'Enrique Baldivieso'],
                'Tarija': ['Cercado', 'Aniceto Arce', 'Gran Chaco', 'Avilés', 'Méndez', 'Burnet O\'Connor'],
                'Chuquisaca': ['Oropeza', 'Juana Azurduy de Padilla', 'Jaime Zudáñez', 'Tomina', 'Hernando Siles', 'Yamparáez', 'Nor Cinti', 'Sud Cinti', 'Belisario Boeto', 'Luis Calvo'],
                'Beni': ['Cercado', 'Vaca Díez', 'José Ballivián', 'Yacuma', 'Moxos', 'Marbán', 'Mamoré', 'Iténez'],
                'Pando': ['Nicolás Suárez', 'Manuripi', 'Madre de Dios', 'Abuná', 'Federico Román']
            };
            
            // Datos de municipios por provincia
            const municipiosPorProvincia = {
                // La Paz
                'Murillo': ['La Paz', 'El Alto', 'Palca', 'Mecapaca', 'Achocalla'],
                'Omasuyos': ['Achacachi', 'Ancoraimes', 'Huarina', 'Santiago de Huata', 'Huatajata'],
                'Pacajes': ['Coro Coro', 'Caquiaviri', 'Calacoto', 'Comanche', 'Charaña', 'Waldo Ballivián', 'Nazacara de Pacajes', 'Santiago de Callapa'],
                // Add more municipalities as needed
                
                // Santa Cruz
                'Andrés Ibáñez': ['Santa Cruz de la Sierra', 'Cotoca', 'Porongo', 'La Guardia', 'El Torno'],
                'Ignacio Warnes': ['Warnes', 'Okinawa Uno'],
                
                // Cochabamba
                'Cercado': ['Cochabamba'],
                'Quillacollo': ['Quillacollo', 'Sipe Sipe', 'Tiquipaya', 'Vinto', 'Colcapirhua'],
                
                // Add more municipalities for other provinces
            };
            
            // Función para cargar las provincias según el departamento seleccionado
            function cargarProvincias() {
                const departamento = departamentoSelect.value;
                provinciaSelect.innerHTML = '<option value="">Provincia</option>';
                municipioSelect.innerHTML = '<option value="">Municipio</option>';
                
                if (departamento && provinciasPorDepartamento[departamento]) {
                    provinciasPorDepartamento[departamento].forEach(provincia => {
                        const option = document.createElement('option');
                        option.value = provincia;
                        option.textContent = provincia;
                        option.selected = provincia === "{{ request('provincia') }}";
                        provinciaSelect.appendChild(option);
                    });
                }
                
                // Remove automatic form submission
                // filterForm.submit();
            }
            
            // Función para cargar los municipios según la provincia seleccionada
            function cargarMunicipios() {
                const provincia = provinciaSelect.value;
                municipioSelect.innerHTML = '<option value="">Municipio</option>';
                
                if (provincia && municipiosPorProvincia[provincia]) {
                    municipiosPorProvincia[provincia].forEach(municipio => {
                        const option = document.createElement('option');
                        option.value = municipio;
                        option.textContent = municipio;
                        option.selected = municipio === "{{ request('municipio') }}";
                        municipioSelect.appendChild(option);
                    });
                }
                
                // Remove automatic form submission
                // filterForm.submit();
            }
            
            // Cargar provincias iniciales si hay un departamento seleccionado
            if (departamentoSelect.value) {
                cargarProvincias();
                
                // If province is selected, load municipalities
                if (provinciaSelect.value) {
                    cargarMunicipios();
                }
            }
            
            // Eventos para detectar cambios en los selects
            departamentoSelect.addEventListener('change', function() {
                cargarProvincias();
                filterForm.submit();
            });
            
            provinciaSelect.addEventListener('change', function() {
                cargarMunicipios();
                filterForm.submit();
            });
            
            municipioSelect.addEventListener('change', function() {
                filterForm.submit();
            });
            
            // Exportar a PDF
            document.getElementById('exportPdf').addEventListener('click', function() {
                window.location.href = "{{ route('delegaciones.exportar.pdf') }}?" + new URLSearchParams(new FormData(filterForm)).toString();
            });
            
            // Exportar a Excel
            document.getElementById('exportExcel').addEventListener('click', function() {
                window.location.href = "{{ route('delegaciones.exportar.excel') }}?" + new URLSearchParams(new FormData(filterForm)).toString();
            });
            
            // Eliminar delegación
            document.querySelectorAll('.delete').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('¿Está seguro que desea eliminar este colegio?')) {
                        const id = this.getAttribute('data-id');
                        fetch("{{ url('delegaciones') }}/" + id, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert('Error al eliminar el colegio');
                            }
                        });
                    }
                });
            });
        });
    </script>
</x-app-layout>
