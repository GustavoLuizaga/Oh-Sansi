<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/inscripcion/listaEstudiantes.css') }}">

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success py-1 px-2 mb-1">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Header Section -->
    <div class="estudiantes-header py-2">
        <h1><i class="fas fa-clock"></i> {{ __('Estudiantes Pendientes de Inscripción') }}</h1>
    </div>

    <!-- Actions Container -->
    <div class="actions-container">
        <div class="button-group">
            <a href="{{ route('estudiantes.lista') }}" class="back-button">
                <i class="fas fa-arrow-left"></i>
                <span>Volver a Lista</span>
            </a>
        </div>

        <div class="search-filter-container">
            <form action="{{ route('estudiantes.pendientes') }}" method="GET" class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Buscar por nombre o CI..." value="{{ request('search') }}">
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                    <span>Buscar</span>
                </button>
            </form>
        </div>

        <div class="export-buttons">
            <button type="button" class="export-button payment" id="generarOrdenPago">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Generar Orden</span>
            </button>

            <button type="button" class="export-button upload" id="exportExcel">
                <i class="fas fa-receipt"></i>
                <span>Subir Comprobante</span>
            </button>
        </div>
    </div>

    <!-- Search and Filter -->
    <form action="{{ route('estudiantes.pendientes') }}" method="GET" id="filterForm">
        <div class="filter-container mb-2 py-1 px-2">
            @if(!$esTutor)
            <div class="filter-group">
                <label for="delegacion" class="text-xs mb-1">Colegio:</label>
                <select class="filter-select py-1" name="delegacion" id="delegacion">
                    <option value="">Todos</option>
                    @foreach($delegaciones as $delegacion)
                    <option value="{{ $delegacion->idDelegacion }}" {{ request('delegacion') == $delegacion->idDelegacion ? 'selected' : '' }}>
                        {{ $delegacion->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="filter-group">
                <label for="modalidad" class="text-xs mb-1">Modalidad:</label>
                <select class="filter-select py-1" name="modalidad" id="modalidad">
                    <option value="">Todas</option>
                    @foreach($modalidades as $modalidad)
                    <option value="{{ $modalidad }}" {{ request('modalidad') == $modalidad ? 'selected' : '' }}>
                        {{ ucfirst($modalidad) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="area" class="text-xs mb-1">Área:</label>
                <select class="filter-select py-1" name="area" id="area">
                    <option value="">Todas</option>
                    @foreach($areas as $area)
                    <option value="{{ $area->idArea }}" {{ request('area') == $area->idArea ? 'selected' : '' }}>
                        {{ $area->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="categoria" class="text-xs mb-1">Categoría:</label>
                <select class="filter-select py-1" name="categoria" id="categoria">
                    <option value="">Todas</option>
                    @foreach($categorias as $categoria)
                    <option value="{{ $categoria->idCategoria }}" {{ request('categoria') == $categoria->idCategoria ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <!-- Table -->
    <table class="estudiantes-table">
        <thead>
            <tr>
                <th>CI</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Estado</th>
                <th>Fecha de Registro</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($estudiantes as $estudiante)
            <tr>
                <td>{{ $estudiante->user->ci }}</td>
                <td>{{ $estudiante->user->name }}</td>
                <td>{{ $estudiante->user->apellidoPaterno }} {{ $estudiante->user->apellidoMaterno }}</td>
                <td>
                    <span class="status-badge pending">Pendiente</span>
                </td>
                <td>{{ $estudiante->user->created_at->format('d/m/Y') }}</td>
                <td class="actions">
                    <div class="action-buttons">
                        <!-- Change this line 
                        <a href="javascript:void(0)" onclick="verEstudiante({{ $estudiante->id }})" class="action-button view" title="Visualizar">
                        -->
                        <!-- To this -->                        <a href="#" onclick="verEstudiante('{{ $estudiante->id }}'); return false;" class="action-button view" title="Visualizar">
                            <i class="fas fa-eye"></i>
                        </a>                        <a href="#" onclick="editarEstudiante('{{ $estudiante->id }}'); return false;" class="action-button edit" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="#" onclick="return false;" class="action-button delete-button" title="Eliminar">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No hay estudiantes pendientes de inscripción</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Modal de Visualización -->
     @include('inscripciones.modalPendienteVer')
   

    <!-- Modal de Edición -->
    @include('inscripciones.modalPendienteEditar')

    <!-- Pagination -->
    <div class="pagination">
        {{ $estudiantes->appends(request()->query())->links() }}
    </div>

    @push('scripts')
    <script>
        // Add event listener for generarOrdenPago button
        document.getElementById('generarOrdenPago').addEventListener('click', function() {
            window.location.href = '{{ route("boleta") }}';
        });

        function verEstudiante(id) {
            fetch(`/estudiantes/ver/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const estudiante = data.estudiante;
                    document.getElementById('verCI').textContent = estudiante.ci;
                    document.getElementById('verNombre').textContent = estudiante.nombre;
                    document.getElementById('verApellidos').textContent = `${estudiante.apellidoPaterno} ${estudiante.apellidoMaterno}`;
                    document.getElementById('verFechaRegistro').textContent = estudiante.fechaNacimiento ? new Date(estudiante.fechaNacimiento).toLocaleDateString() : 'No disponible';
                    
                    // Actualizar información académica si existe
                    if (estudiante.area) {
                        document.getElementById('verArea').textContent = estudiante.area.nombre;
                    } else {
                        document.getElementById('verArea').textContent = 'No asignada';
                    }
                    
                    if (estudiante.categoria) {
                        document.getElementById('verCategoria').textContent = estudiante.categoria.nombre;
                    } else {
                        document.getElementById('verCategoria').textContent = 'No asignada';
                    }
                    
                    if (estudiante.delegacion) {
                        document.getElementById('verDelegacion').textContent = estudiante.delegacion.nombre;
                    } else {
                        document.getElementById('verDelegacion').textContent = 'No asignada';
                    }
                    
                    document.getElementById('verModalidad').textContent = estudiante.modalidad || 'No definida';

                    // Mostrar información del grupo si existe y la modalidad es duo o equipo
                    const infoGrupo = document.getElementById('infoGrupo');
                    if (estudiante.grupo && (estudiante.modalidad === 'duo' || estudiante.modalidad === 'equipo')) {
                        document.getElementById('verNombreGrupo').textContent = estudiante.grupo.nombre || 'Sin nombre';
                        document.getElementById('verCodigoGrupo').textContent = estudiante.grupo.codigo;
                        document.getElementById('verEstadoGrupo').textContent = estudiante.grupo.estado;
                        infoGrupo.style.display = 'block';
                    } else {
                        infoGrupo.style.display = 'none';
                    }

                    document.getElementById('modalVerEstudiante').style.display = 'flex';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos del estudiante');
            });
        }        // Variable global para almacenar el ID de delegación y modalidad actual
        let currentDelegacionId = null;
        let currentModalidad = null;
        
        function editarEstudiante(id) {
            fetch(`/estudiantes/ver/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const estudiante = data.estudiante;
                    document.getElementById('editEstudianteId').value = estudiante.id;
                    
                    // Guardar el ID de la delegación
                    if (estudiante.delegacion && estudiante.delegacion.id) {
                        currentDelegacionId = estudiante.delegacion.id;
                    } else if (estudiante.delegacion && estudiante.delegacion.idDelegacion) {
                        currentDelegacionId = estudiante.delegacion.idDelegacion;
                    }
                      // Llenar el formulario con los datos actuales
                    if (estudiante.area) {
                        document.getElementById('editArea').value = estudiante.area.id || estudiante.area.idArea;
                    }
                    if (estudiante.categoria) {
                        document.getElementById('editCategoria').value = estudiante.categoria.id || estudiante.categoria.idCategoria;
                    }                    if (estudiante.modalidad) {
                        document.getElementById('editModalidad').value = estudiante.modalidad;
                        currentModalidad = estudiante.modalidad;
                        
                        // Si es duo o equipo, mostrar el selector de grupos y cargarlos
                        if (estudiante.modalidad === 'duo' || estudiante.modalidad === 'equipo') {
                            const grupoContainer = document.getElementById('grupoContainer');
                            grupoContainer.style.display = 'block';
                            
                            // Cargar los grupos si tenemos el ID de la delegación
                            if (currentDelegacionId) {
                                cargarGrupos(currentDelegacionId, estudiante.modalidad);
                                
                                // Si el estudiante ya tiene un grupo, seleccionarlo después de un pequeño retraso
                                if (estudiante.grupo && estudiante.grupo.id) {
                                    setTimeout(() => {
                                        document.getElementById('editGrupo').value = estudiante.grupo.id;
                                    }, 500);
                                }
                            }
                        } else {
                            // Ocultar selector de grupos para modalidad individual
                            document.getElementById('grupoContainer').style.display = 'none';
                        }
                    }

                    document.getElementById('modalEditarEstudiante').style.display = 'flex';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos del estudiante');
            });
        }

        // Función para manejar el cambio de modalidad
        function handleModalidadChange() {
            const modalidadSelect = document.getElementById('editModalidad');
            const grupoContainer = document.getElementById('grupoContainer');
            const grupoSelect = document.getElementById('editGrupo');
            
            // Guardar la modalidad actual seleccionada
            currentModalidad = modalidadSelect.value;
            
            // Limpiar el selector de grupos
            grupoSelect.innerHTML = '<option value="">Seleccione un grupo</option>';
            
            // Mostrar u ocultar el selector de grupos según la modalidad
            if (modalidadSelect.value === 'duo' || modalidadSelect.value === 'equipo') {
                grupoContainer.style.display = 'block';
                
                // Cargar grupos si tenemos el ID de la delegación
                if (currentDelegacionId) {
                    cargarGrupos(currentDelegacionId, modalidadSelect.value);
                } else {
                    console.error('No se pudo obtener el ID de delegación');
                }
            } else {
                grupoContainer.style.display = 'none';
            }
        }
        
        // Función para cargar los grupos según delegación y modalidad
        function cargarGrupos(idDelegacion, modalidad) {
            fetch(`/estudiantes/grupos/${idDelegacion}/${modalidad}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const grupoSelect = document.getElementById('editGrupo');
                    
                    // Limpiar opciones actuales
                    grupoSelect.innerHTML = '<option value="">Seleccione un grupo</option>';
                      // Añadir los nuevos grupos
                    data.grupos.forEach(grupo => {
                        const option = document.createElement('option');
                        option.value = grupo.id;
                        option.textContent = `${grupo.nombreGrupo || 'Grupo'} (${grupo.codigoInvitacion})`;
                        grupoSelect.appendChild(option);
                    });
                } else {
                    console.error('Error al cargar grupos:', data.message);
                }
            })
            .catch(error => {
                console.error('Error al obtener los grupos:', error);
            });
        }

        function cerrarModalVer() {
            document.getElementById('modalVerEstudiante').style.display = 'none';
        }

        function cerrarModalEditar() {
            document.getElementById('modalEditarEstudiante').style.display = 'none';
        }

        document.getElementById('formEditarEstudiante').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('editEstudianteId').value;
            const formData = new FormData(this);            // Convertir FormData a un objeto para enviar como JSON            // Crear objeto para enviar como JSON
            const formObject = {
                area_id: formData.get('idArea'),
                categoria_id: formData.get('idCategoria'),
                modalidad: formData.get('modalidad')
            };
            
            // Si la modalidad es duo o equipo y hay un grupo seleccionado, incluirlo
            if ((formData.get('modalidad') === 'duo' || formData.get('modalidad') === 'equipo') && formData.get('idGrupoInscripcion')) {
                formObject.idGrupoInscripcion = formData.get('idGrupoInscripcion');
            }
            
            fetch(`/estudiantes/update/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formObject)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error al actualizar el estudiante');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el estudiante');
            });
        });

        // Cerrar modales al hacer clic fuera
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
    @endpush
</x-app-layout>