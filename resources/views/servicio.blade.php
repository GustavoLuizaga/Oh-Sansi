<x-app-layout>
    <link rel="stylesheet" href="/css/servicio.css">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensajes de alerta -->
            @if(session('success'))
            <div class="alert alert-success mb-4">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif
            
            @if(session('error'))
            <div class="alert alert-danger mb-4">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif
            
            <!-- Header Section -->
            <div class="servicios-header py-2">
                <h1><i class="fas fa-user-shield"></i> {{ __('Administrar Roles y Permisos') }}</h1>
            </div>
            
            <!-- Main Content -->
            <div class="roles-permisos-container">
                <!-- Roles Section -->
                <div class="roles-section">
                    <div class="section-header">
                        <span>Roles</span>
                        <button class="add-button" id="btn-agregar-rol">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                    
                    <ul class="roles-list" id="roles-list">
                        @forelse($roles as $rol)
                            <li class="rol-item {{ $loop->first ? 'active' : '' }}" 
                                data-id="{{ $rol->idRol }}" 
                                onclick='seleccionarRol(this, "{{ $rol->idRol }}")'>
                                {{ $rol->nombre }}
                                <div class="item-actions">
                                    <button class="action-button edit" onclick="event.stopPropagation(); editarRol('{{ $rol->idRol }}', '{{ $rol->nombre }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-button delete" onclick="event.stopPropagation(); confirmarEliminarRol('{{ $rol->idRol }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <!-- Removed the incorrect permission delete button from here -->
                                </div>
                            </li>
                        @empty
                            <li class="rol-item">No hay roles disponibles</li>
                        @endforelse
                    </ul>
                </div>
                
                <!-- Permisos Section -->
                <div class="permisos-section">
                    <div class="section-header">
                        <span>Permisos del Rol: <span id="rol-seleccionado">{{ $primerRol ? $primerRol->nombre : 'Ninguno' }}</span></span>
                        <button class="add-button" id="btn-agregar-permiso">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                    
                    <ul class="permisos-list" id="permisos-list">
                        @forelse($funcionesDelRol as $funcion)
                            <li class="permiso-item">
                                {{ $funcion->nombre }}
                                <button class="action-button delete" onclick="eliminarPermiso('{{ $primerRol ? $primerRol->idRol : 0 }}', '{{ $funcion->idFuncion }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </li>
                        @empty
                            <li class="permiso-item">No hay permisos asignados a este rol</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Rol -->
    <div id="modal-agregar-rol" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Agregar Nuevo Rol</h2>
                <span class="close" id="close-modal-rol">&times;</span>
            </div>
            <div class="modal-body">
                <form id="form-agregar-rol" method="POST" action="/servicios/agregar-rol">
                    @csrf
                    <div class="form-group">
                        <label for="nombre-rol">Nombre del Rol:</label>
                        <input type="text" id="nombre-rol" name="nombre" required class="form-control">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Rol -->
    <div id="modal-editar-rol" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Editar Rol</h2>
                <span class="close" id="close-modal-editar-rol">&times;</span>
            </div>
            <div class="modal-body">
                <form id="form-editar-rol" method="POST" action="/servicios/editar-rol">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="editar-rol-id" name="idRol">
                    <div class="form-group">
                        <label for="editar-nombre-rol">Nombre del Rol:</label>
                        <input type="text" id="editar-nombre-rol" name="nombre" required class="form-control">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Rol -->
    <div id="modal-eliminar-rol" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Eliminar Rol</h2>
                <span class="close" id="close-modal-eliminar-rol">&times;</span>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar este rol? Esta acción no se puede deshacer.</p>
                <form id="form-eliminar-rol" method="POST" action="/servicios/eliminar-rol">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" id="eliminar-rol-id" name="idRol">
                    <div class="form-actions">
                        <button type="button" id="cancelar-eliminar-rol" class="btn-cancel">Cancelar</button>
                        <button type="submit" class="btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Permiso -->
    <div id="modal-agregar-permiso" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Agregar Permisos al Rol</h2>
                <span class="close" id="close-modal-permiso">&times;</span>
            </div>
            <div class="modal-body">
                <form id="form-agregar-permiso" method="POST" action="/servicios/agregar-permiso">
                    @csrf
                    <input type="hidden" id="rol-id-permiso" name="idRol">
                    <div class="form-group">
                        <label for="permisos-disponibles">Permisos Disponibles:</label>
                        <select id="permisos-disponibles" name="permisos[]" multiple class="form-control" required>
                            <!-- Se llenará dinámicamente con JavaScript -->
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">Asignar Permisos</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let rolActualId = parseInt('{{ $primerRol ? $primerRol->idRol : 0 }}');
        
        // Función para seleccionar un rol
        function seleccionarRol(elemento, idRol) {
            // Actualizar variable global
            rolActualId = idRol;
            
            // Remover clase active de todos los roles
            document.querySelectorAll('.rol-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Agregar clase active al rol seleccionado
            elemento.classList.add('active');
            
            // Actualizar el nombre del rol seleccionado
            document.getElementById('rol-seleccionado').textContent = elemento.textContent.trim();
            
            // Obtener los permisos del rol seleccionado mediante AJAX
            fetch(`/servicios/obtener-funciones-rol/${idRol}`)
                .then(response => response.json())
                .then(data => {
                    const permisosList = document.getElementById('permisos-list');
                    permisosList.innerHTML = '';
                    
                    if (data.length > 0) {
                        data.forEach(funcion => {
                            const li = document.createElement('li');
                            li.className = 'permiso-item';
                            li.innerHTML = `
                                ${funcion.nombre}
                                <button class="action-button delete" onclick="eliminarPermiso(${idRol}, ${funcion.idFuncion})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            `;
                            permisosList.appendChild(li);
                        });
                    } else {
                        const li = document.createElement('li');
                        li.className = 'permiso-item';
                        li.textContent = 'No hay permisos asignados a este rol';
                        permisosList.appendChild(li);
                    }
                })
                .catch(error => {
                    console.error('Error al obtener los permisos:', error);
                });
        }
        
        // Funciones para manejar modales
        function abrirModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }
        
        function cerrarModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Funciones para roles
        function editarRol(idRol, nombreRol) {
            document.getElementById('editar-rol-id').value = idRol;
            document.getElementById('editar-nombre-rol').value = nombreRol;
            abrirModal('modal-editar-rol');
        }
        
        function confirmarEliminarRol(idRol) {
            document.getElementById('eliminar-rol-id').value = idRol;
            abrirModal('modal-eliminar-rol');
        }
        
        // Función para eliminar permiso
        function eliminarPermiso(idRol, idFuncion) {
            if (confirm('¿Está seguro que desea eliminar este permiso del rol?')) {
                try {
                    // Obtener el token CSRF de manera segura
                    const csrfToken = document.querySelector('meta[name="csrf-token"]');
                    
                    if (!csrfToken) {
                        console.error('No se encontró el token CSRF');
                        alert('Error: No se pudo encontrar el token CSRF. Por favor, recargue la página.');
                        return;
                    }
                    
                    fetch('/servicios/eliminar-permiso', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
                        },
                        body: JSON.stringify({ idRol, idFuncion })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('La respuesta del servidor no fue exitosa: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Recargar los permisos del rol
                            const rolActivo = document.querySelector('.rol-item.active');
                            if (rolActivo) {
                                seleccionarRol(rolActivo, idRol);
                            }
                        } else {
                            alert('Error al eliminar el permiso: ' + (data.message || 'Error desconocido'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al procesar la solicitud: ' + error.message);
                    });
                } catch (e) {
                    console.error('Error al iniciar la solicitud:', e);
                    alert('Error al iniciar la solicitud: ' + e.message);
                }
            }
        }
        
        // Cargar permisos disponibles para un rol
        function cargarPermisosDisponibles(idRol) {
            fetch(`/servicios/obtener-permisos-disponibles/${idRol}`)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('permisos-disponibles');
                    select.innerHTML = '';
                    
                    if (data.length > 0) {
                        data.forEach(funcion => {
                            const option = document.createElement('option');
                            option.value = funcion.idFuncion;
                            option.textContent = funcion.nombre;
                            select.appendChild(option);
                        });
                    } else {
                        const option = document.createElement('option');
                        option.disabled = true;
                        option.textContent = 'No hay permisos disponibles';
                        select.appendChild(option);
                    }
                })
                .catch(error => {
                    console.error('Error al obtener permisos disponibles:', error);
                });
        }
        
        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Botón agregar rol
            document.getElementById('btn-agregar-rol').addEventListener('click', function() {
                abrirModal('modal-agregar-rol');
            });
            
            // Botón agregar permiso
            document.getElementById('btn-agregar-permiso').addEventListener('click', function() {
                if (rolActualId > 0) {
                    document.getElementById('rol-id-permiso').value = rolActualId;
                    cargarPermisosDisponibles(rolActualId);
                    abrirModal('modal-agregar-permiso');
                } else {
                    alert('Por favor, seleccione un rol primero');
                }
            });
            
            // Cerrar modales
            document.getElementById('close-modal-rol').addEventListener('click', function() {
                cerrarModal('modal-agregar-rol');
            });
            
            document.getElementById('close-modal-editar-rol').addEventListener('click', function() {
                cerrarModal('modal-editar-rol');
            });
            
            document.getElementById('close-modal-eliminar-rol').addEventListener('click', function() {
                cerrarModal('modal-eliminar-rol');
            });
            
            document.getElementById('close-modal-permiso').addEventListener('click', function() {
                cerrarModal('modal-agregar-permiso');
            });
            
            document.getElementById('cancelar-eliminar-rol').addEventListener('click', function() {
                cerrarModal('modal-eliminar-rol');
            });
            
            // Cerrar modales al hacer clic fuera de ellos
            window.addEventListener('click', function(event) {
                const modales = document.getElementsByClassName('modal');
                for (let i = 0; i < modales.length; i++) {
                    if (event.target === modales[i]) {
                        modales[i].style.display = 'none';
                    }
                }
            });
        });
    </script>
</x-app-layout>
