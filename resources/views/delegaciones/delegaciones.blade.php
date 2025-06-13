<x-app-layout>
    <link rel="stylesheet" href="/css/delegacion/delegacion.css">
    <link rel="stylesheet" href="/css/delegacion/modal.css">
    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success py-1 px-2 mb-1">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    @if(request()->has('deleted') && request()->deleted == 'true')
    <div class="alert alert-success py-1 px-2 mb-1">
        Colegio eliminado correctamente.
    </div>
    @endif

    <!-- Header Section -->
    <div class="delegaciones-header py-2">
        <h1><i class="fas fa-school"></i> {{ __('Administrar Colegios') }}</h1>
    </div>

    <!-- Search and Filter -->
    <form action="{{ route('delegaciones') }}" method="GET" id="filterForm">
        <!-- Actions Container (Add and Export buttons in the same row) -->
        <div class="actions-container mb-1">
            <a href="{{ route('delegaciones.agregar') }}" class="add-button py-1 px-2">
                <i class="fas fa-plus"></i> Agregar Colegio
            </a>
            <div class="search-filter-container mb-1">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Nombre o código SIE" value="{{ request('search') }}" class="py-1">
                    <button type="submit" class="search-button py-1 px-2">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
            <div class="export-buttons">
                <button type="button" class="export-button pdf py-1 px-2" id="exportPdf">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>

                <button type="button" class="export-button excel py-1 px-2" id="exportExcel">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
            </div>
        </div>

        <div class="filter-container mb-2 py-1 px-2">
            <div class="filter-group">
                <label for="dependencia" class="text-xs mb-1">Dependencia:</label>
                <select class="filter-select py-1" name="dependencia" id="dependencia">
                    <option value="">Todas</option>
                    <option value="Fiscal" {{ request('dependencia') == 'Fiscal' ? 'selected' : '' }}>Fiscal</option>
                    <option value="Convenio" {{ request('dependencia') == 'Convenio' ? 'selected' : '' }}>Convenio</option>
                    <option value="Privado" {{ request('dependencia') == 'Privado' ? 'selected' : '' }}>Privado</option>
                    <option value="Comunitaria" {{ request('dependencia') == 'Comunitaria' ? 'selected' : '' }}>Comunitaria</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="departamento" class="text-xs mb-1">Departamento:</label>
                <select class="filter-select py-1" name="departamento" id="departamento">
                    <option value="">Todos</option>
                    @foreach(['La Paz', 'Santa Cruz', 'Cochabamba', 'Oruro', 'Potosí', 'Tarija', 'Chuquisaca', 'Beni', 'Pando'] as $depto)
                        <option value="{{ $depto }}" {{ request('departamento') == $depto ? 'selected' : '' }}>{{ $depto }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="provincia" class="text-xs mb-1">Provincia:</label>
                <select class="filter-select py-1" name="provincia" id="provincia">
                    <option value="">Todas</option>
                    @if(request('provincia'))
                    <option value="{{ request('provincia') }}" selected>{{ request('provincia') }}</option>
                    @endif
                </select>
            </div>

            <div class="filter-group">
                <label for="municipio" class="text-xs mb-1">Municipio:</label>
                <select class="filter-select py-1" name="municipio" id="municipio">
                    <option value="">Todos</option>
                    @if(request('municipio'))
                    <option value="{{ request('municipio') }}" selected>{{ request('municipio') }}</option>
                    @endif
                </select>
            </div>
        </div>
    </form>

    <!-- Table -->
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
                    <div class="flex space-x-1">
                        <a href="{{ route('delegaciones.ver', $delegacion->codigo_sie) }}" class="action-button view w-5 h-5">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        <a href="{{ route('delegaciones.editar', $delegacion->codigo_sie) }}" class="action-button edit w-5 h-5">
                            <i class="fas fa-edit text-xs"></i>
                        </a>
                        <a href="#" class="action-button delete-button w-5 h-5" data-id="{{ $delegacion->codigo_sie }}" data-nombre="{{ $delegacion->nombre }}">
                            <i class="fas fa-trash text-xs"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No hay colegios registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="paginacion">
        <div class="pagination-container">
            @if($delegaciones->lastPage() > 1)
                <ul class="pagination-list">
                    <!-- Previous Page Link -->
                    @if($delegaciones->currentPage() > 1)
                        <li>
                            <a href="{{ $delegaciones->url(1) }}" class="pagination-link">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $delegaciones->url($delegaciones->currentPage() - 1) }}" class="pagination-link">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        </li>
                    @endif

                    <!-- Numbered Page Links -->
                    @for($i = max(1, $delegaciones->currentPage() - 2); $i <= min($delegaciones->lastPage(), $delegaciones->currentPage() + 2); $i++)
                        <li>
                            <a href="{{ $delegaciones->url($i) }}" 
                               class="pagination-link {{ $i == $delegaciones->currentPage() ? 'active' : '' }}">
                                {{ $i }}
                            </a>
                        </li>
                    @endfor

                    <!-- Next Page Link -->
                    @if($delegaciones->hasMorePages())
                        <li>
                            <a href="{{ $delegaciones->url($delegaciones->currentPage() + 1) }}" class="pagination-link">
                                <i class="fas fa-angle-right"></i>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $delegaciones->url($delegaciones->lastPage()) }}" class="pagination-link">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="pagination-info">
                    Mostrando {{ $delegaciones->firstItem() ?? 0 }} - {{ $delegaciones->lastItem() ?? 0 }} de {{ $delegaciones->total() }} registros
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const departamentoSelect = document.getElementById('departamento');
            const provinciaSelect = document.getElementById('provincia');
            const municipioSelect = document.getElementById('municipio');
            const dependenciaSelect = document.getElementById('dependencia');
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
                // La paz
                'Murillo': ['La Paz', 'El Alto', 'Palca', 'Mecapaca', 'Achocalla'],
                'Omasuyos': ['Achacachi', 'Ancoraimes', 'Huarina', 'Santiago de Huata', 'Huatajata'],
                'Pacajes': ['Coro Coro', 'Caquiaviri', 'Calacoto', 'Comanche', 'Charaña', 'Waldo Ballivián', 'Nazacara de Pacajes', 'Santiago de Callapa'],
                'Camacho': ['Puerto Acosta', 'Mocomoco', 'Puerto Carabuco', 'Humanata', 'Escoma'],
                'Muñecas': ['Chuma', 'Ayata', 'Aucapata'],
                'Larecaja': ['Sorata', 'Guanay', 'Tacacoma', 'Quiabaya', 'Combaya', 'Tipuani', 'Mapiri', 'Teoponte'],
                'Franz Tamayo': ['Apolo', 'Pelechuco'],
                'Ingavi': ['Viacha', 'Guaqui', 'Tiahuanacu', 'Desaguadero', 'San Andrés de Machaca', 'Jesús de Machaca', 'Taraco'],
                'Loayza': ['Luribay', 'Sapahaqui', 'Yaco', 'Malla', 'Cairoma'],
                'Inquisivi': ['Inquisivi', 'Quime', 'Cajuata', 'Colquiri', 'Ichoca', 'Villa Libertad Licoma'],
                'Sud Yungas': ['Chulumani', 'Irupana', 'Yanacachi', 'Palos Blancos', 'La Asunta'],
                'Los Andes': ['Pucarani', 'Laja', 'Batallas', 'Puerto Pérez'],
                'Aroma': ['Sica Sica', 'Umala', 'Ayo Ayo', 'Calamarca', 'Patacamaya', 'Colquencha', 'Collana'],
                'Nor Yungas': ['Coroico', 'Coripata'],
                'Abel Iturralde': ['Ixiamas', 'San Buenaventura'],
                'Bautista Saavedra': ['Charazani', 'Curva'],
                'Manco Kapac': ['Copacabana', 'San Pedro de Tiquina', 'Tito Yupanqui'],
                'Gualberto Villarroel': ['San Pedro de Curahuara', 'Papel Pampa', 'Chacarilla'],
                'José Manuel Pando': ['Santiago de Machaca', 'Catacora'],

                // Santa Cruz
                'Andrés Ibáñez': ['Santa Cruz de la Sierra', 'Cotoca', 'Porongo', 'La Guardia', 'El Torno'],
                'Ignacio Warnes': ['Warnes', 'Okinawa Uno'],
                'José Miguel de Velasco': ['San Ignacio', 'San Miguel', 'San Rafael'],
                'Ichilo': ['Buena Vista', 'San Carlos', 'Yapacaní', 'San Juan de Yapacaní'],
                'Chiquitos': ['San José', 'Pailón', 'Roboré', 'San José de Chiquitos'],
                'Sara': ['Portachuelo', 'Santa Rosa del Sara', 'Colpa Bélgica'],
                'Cordillera': ['Lagunillas', 'Charagua', 'Cabezas', 'Cuevo', 'Gutiérrez', 'Camiri', 'Boyuibe'],
                'Vallegrande': ['Vallegrande', 'Trigal', 'Moro Moro', 'Postrer Valle', 'Pucará'],
                'Florida': ['Samaipata', 'Pampa Grande', 'Mairana', 'Quirusillas'],
                'Obispo Santistevan': ['Montero', 'General Saavedra', 'Mineros', 'Fernández Alonso', 'San Pedro'],
                'Ñuflo de Chávez': ['Concepción', 'San Javier', 'San Ramón', 'San Julián', 'San Antonio de Lomerío', 'Cuatro Cañadas'],
                'Ángel Sandoval': ['San Matías'],
                'Manuel María Caballero': ['Comarapa', 'Saipina'],
                'Germán Busch': ['Puerto Suárez', 'Puerto Quijarro', 'El Carmen Rivero Tórrez'],
                'Guarayos': ['Ascensión de Guarayos', 'Urubichá', 'El Puente'],

                // Cochabamba
                'Cercado': ['Cochabamba'],
                'Campero': ['Aiquile', 'Pasorapa', 'Omereque'],
                'Ayopaya': ['Independencia', 'Morochata', 'Cocapata'],
                'Esteban Arce': ['Tarata', 'Anzaldo', 'Arbieto', 'Sacabamba'],
                'Arani': ['Arani', 'Vacas'],
                'Arque': ['Arque', 'Tacopaya'],
                'Capinota': ['Capinota', 'Santivañez', 'Sicaya'],
                'Germán Jordán': ['Cliza', 'Toco', 'Tolata'],
                'Quillacollo': ['Quillacollo', 'Sipe Sipe', 'Tiquipaya', 'Vinto', 'Colcapirhua'],
                'Chapare': ['Sacaba', 'Colomi', 'Villa Tunari'],
                'Tapacarí': ['Tapacarí'],
                'Carrasco': ['Totora', 'Pojo', 'Pocona', 'Chimoré', 'Puerto Villarroel', 'Entre Ríos'],
                'Mizque': ['Mizque', 'Vila Vila', 'Alalay'],
                'Punata': ['Punata', 'Villa Rivero', 'San Benito', 'Tacachi', 'Cuchumuela'],
                'Bolívar': ['Bolívar'],
                'Tiraque': ['Tiraque', 'Shinahota'],

                // Oruro
                'Cercado': ['Oruro', 'Caracollo', 'El Choro', 'Paria'],
                'Abaroa': ['Challapata', 'Santuario de Quillacas'],
                'Carangas': ['Corque', 'Choquecota'],
                'Sajama': ['Curahuara de Carangas', 'Turco'],
                'Litoral': ['Huachacalla', 'Escara', 'Cruz de Machacamarca', 'Yunguyo del Litoral', 'Esmeralda'],
                'Poopó': ['Poopó', 'Pazña', 'Antequera'],
                'Pantaleón Dalence': ['Huanuni', 'Machacamarca'],
                'Ladislao Cabrera': ['Salinas de Garcí Mendoza', 'Pampa Aullagas'],
                'Sabaya': ['Sabaya', 'Coipasa', 'Chipaya'],
                'Saucarí': ['Toledo'],
                'Tomás Barrón': ['Eucaliptus'],
                'Sud Carangas': ['Santiago de Andamarca', 'Belén de Andamarca'],
                'San Pedro de Totora': ['Totora'],
                'Sebastián Pagador': ['Santiago de Huari'],
                'Mejillones': ['La Rivera', 'Todos Santos', 'Carangas'],
                'Nor Carangas': ['Huayllamarca'],

                // Potosí
                'Tomás Frías': ['Potosí', 'Yocalla', 'Urmiri'],
                'Rafael Bustillo': ['Uncía', 'Chayanta', 'Llallagua', 'Chuquihuta'],
                'Cornelio Saavedra': ['Betanzos', 'Chaquí', 'Tacobamba'],
                'Chayanta': ['Colquechaca', 'Ravelo', 'Pocoata', 'Ocurí'],
                'Charcas': ['San Pedro de Buena Vista', 'Toro Toro'],
                'Nor Chichas': ['Cotagaita', 'Vitichi'],
                'Alonso de Ibáñez': ['Sacaca', 'Caripuyo'],
                'Sud Chichas': ['Tupiza', 'Atocha'],
                'Nor Lípez': ['Colcha K', 'San Pedro de Quemes'],
                'Sud Lípez': ['San Pablo de Lípez', 'Mojinete', 'San Antonio de Esmoruco'],
                'José María Linares': ['Puna', 'Caiza D', 'Ckochas'],
                'Antonio Quijarro': ['Uyuni', 'Tomave', 'Porco'],
                'Bernardino Bilbao': ['Arampampa', 'Acasio'],
                'Daniel Campos': ['Llica', 'Tahua'],
                'Modesto Omiste': ['Villazón'],
                'Enrique Baldivieso': ['San Agustín'],

                // Tarija
                'Cercado': ['Tarija'],
                'Aniceto Arce': ['Padcaya', 'Bermejo'],
                'Gran Chaco': ['Yacuiba', 'Caraparí', 'Villamontes'],
                'Avilés': ['Uriondo', 'Yunchará'],
                'Méndez': ['San Lorenzo', 'El Puente'],
                'Burnet O\'Connor': ['Entre Ríos'],

                // Chuquisaca
                'Oropeza': ['Sucre', 'Yotala', 'Poroma'],
                'Juana Azurduy de Padilla': ['Azurduy', 'Tarvita'],
                'Jaime Zudáñez': ['Zudáñez', 'Presto', 'Mojocoya', 'Icla'],
                'Tomina': ['Padilla', 'Tomina', 'Sopachuy', 'Villa Alcalá', 'El Villar'],
                'Hernando Siles': ['Monteagudo', 'Huacareta'],
                'Yamparáez': ['Tarabuco', 'Yamparáez'],
                'Nor Cinti': ['Camargo', 'San Lucas', 'Incahuasi', 'Villa Charcas'],
                'Sud Cinti': ['Camataqui', 'Culpina', 'Las Carreras'],
                'Belisario Boeto': ['Villa Serrano'],
                'Luis Calvo': ['Villa Vaca Guzmán', 'Huacaya', 'Macharetí'],

                // Beni
                'Cercado': ['Trinidad', 'San Javier'],
                'Vaca Díez': ['Riberalta', 'Guayaramerín'],
                'José Ballivián': ['Reyes', 'San Borja', 'Santa Rosa', 'Rurrenabaque'],
                'Yacuma': ['Santa Ana', 'Exaltación'],
                'Moxos': ['San Ignacio', 'San Lorenzo', 'San Francisco'],
                'Marbán': ['Loreto', 'San Andrés'],
                'Mamoré': ['San Joaquín', 'Puerto Siles', 'San Ramón'],
                'Iténez': ['Magdalena', 'Baures', 'Huacaraje'],

                // Pando
                'Nicolás Suárez': ['Cobija', 'Porvenir', 'Bolpebra', 'Bella Flor', 'Puerto Rico'],
                'Manuripi': ['Puerto Gonzalo Moreno', 'San Lorenzo', 'Sena', 'Ingavi'],
                'Madre de Dios': ['Puerto Gonzalo Moreno', 'San Lorenzo', 'Sena'],
                'Abuná': ['Santa Rosa del Abuná', 'Ingavi'],
                'Federico Román': ['Nueva Esperanza', 'Villa Nueva', 'Santos Mercado']
            };

            // Función para cargar las provincias según el departamento seleccionado
            function cargarProvincias() {
                const departamento = departamentoSelect.value;
                provinciaSelect.innerHTML = '<option value="">Todas</option>';
                municipioSelect.innerHTML = '<option value="">Todos</option>';

                if (departamento && provinciasPorDepartamento[departamento]) {
                    provinciasPorDepartamento[departamento].forEach(provincia => {
                        const option = document.createElement('option');
                        option.value = provincia;
                        option.textContent = provincia;
                        option.selected = provincia === "{{ request('provincia') }}";
                        provinciaSelect.appendChild(option);
                    });
                }
            }

            // Función para cargar los municipios según la provincia seleccionada
            function cargarMunicipios() {
                const provincia = provinciaSelect.value;
                municipioSelect.innerHTML = '<option value="">Todos</option>';

                if (provincia && municipiosPorProvincia[provincia]) {
                    municipiosPorProvincia[provincia].forEach(municipio => {
                        const option = document.createElement('option');
                        option.value = municipio;
                        option.textContent = municipio;
                        option.selected = municipio === "{{ request('municipio') }}";
                        municipioSelect.appendChild(option);
                    });
                }
            }

            // Event listeners
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

            dependenciaSelect.addEventListener('change', function() {
                filterForm.submit();
            });

            municipioSelect.addEventListener('change', function() {
                filterForm.submit();
            });

            dependenciaSelect.addEventListener('change', function() {
                filterForm.submit();
            });

            // Inicializar
            cargarProvincias();
            cargarMunicipios();

            // Export PDF button
            document.getElementById('exportPdf').addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = "{{ route('delegaciones.exportar.pdf') }}";
            });

            // Export Excel button
            document.getElementById('exportExcel').addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = "{{ route('delegaciones.exportar.excel') }}";
            });
        });
    </script>

    <!-- Modal de confirmación para eliminar -->
    @include('delegaciones.modal')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('deleteModal');
            const closeBtn = modal.querySelector('.close');
            const cancelBtn = document.getElementById('cancelDelete');
            const deleteForm = document.getElementById('deleteForm');
            const delegacionNombre = document.getElementById('delegacionNombre');
            const deleteButtons = document.querySelectorAll('.delete-button');

            // Función para abrir el modal
            function openModal(id, nombre) {
                deleteForm.action = `/delegaciones/${id}/eliminar`;
                delegacionNombre.textContent = nombre;
                modal.style.display = 'block';
            }

            // Función para cerrar el modal
            function closeModal() {
                modal.style.display = 'none';
            }
            
            // Event listeners para los botones
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.id;
                    const nombre = this.dataset.nombre;
                    openModal(id, nombre);
                });
            });
            
            // Cerrar modal con botón de cerrar y cancelar
            if (closeBtn) {
                closeBtn.addEventListener('click', closeModal);
            }
            
            if (cancelBtn) {
                cancelBtn.addEventListener('click', closeModal);
            }
        });
    </script>
</x-app-layout>