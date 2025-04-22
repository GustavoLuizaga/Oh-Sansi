<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/servicio.css') }}">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                        <!-- Botón de agregar deshabilitado por ahora -->
                        <button class="add-button" disabled style="opacity: 0.6;">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                    
                    <ul class="roles-list" id="roles-list">
                        @forelse($roles as $rol)
                            <li class="rol-item {{ $loop->first ? 'active' : '' }}" 
                                data-id="{{ $rol->idRol }}" 
                                onclick='seleccionarRol(this, "{{ $rol->idRol }}")'>
                                {{ $rol->nombre }}
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
                        <!-- Botón de agregar deshabilitado por ahora -->
                        <button class="add-button" disabled style="opacity: 0.6;">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                    
                    <ul class="permisos-list" id="permisos-list">
                        @forelse($funcionesDelRol as $funcion)
                            <li class="permiso-item">
                                {{ $funcion->nombre }}
                            </li>
                        @empty
                            <li class="permiso-item">No hay permisos asignados a este rol</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        function seleccionarRol(elemento, idRol) {
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
                            li.textContent = funcion.nombre;
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
    </script>
</x-app-layout>
