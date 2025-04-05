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
                </div>
                
                <div class="delegaciones-filters">
                    <select class="filter-select" name="dependencia" id="dependencia">
                        <option value="">Dependencia</option>
                        <option value="Fiscal" {{ request('dependencia') == 'Fiscal' ? 'selected' : '' }}>Fiscal</option>
                        <option value="Convenio" {{ request('dependencia') == 'Convenio' ? 'selected' : '' }}>Convenio</option>
                        <option value="Privado" {{ request('dependencia') == 'Privado' ? 'selected' : '' }}>Privado</option>
                        <option value="Comunitaria" {{ request('dependencia') == 'Comunitaria' ? 'selected' : '' }}>Comunitaria</option>
                    </select>


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
            
            dependenciaSelect.addEventListener('change', function() {
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
