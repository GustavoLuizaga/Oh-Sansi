<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/usuarios/editarUsuario.css') }}">

    <!-- Header Section -->
    <div class="editar-usuario-header py-2">
        <h1><i class="fas fa-user-edit"></i> {{ __('Editar Usuario') }}</h1>
    </div>

    <div class="editar-usuario-container">
        <div class="editar-usuario-form">
            <form method="POST" action="{{ route('usuarios.update', $usuario->id) }}" id="editUserForm">
                @csrf
                @method('PUT')

                <!-- Información Personal -->
                <div class="form-section">
                    <div class="form-section-title">Información Personal</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name" class="required-label">Nombre</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $usuario->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="apellidoPaterno" class="required-label">Apellido Paterno</label>
                            <input type="text" class="form-control @error('apellidoPaterno') is-invalid @enderror" id="apellidoPaterno" name="apellidoPaterno" value="{{ old('apellidoPaterno', $usuario->apellidoPaterno) }}" required>
                            @error('apellidoPaterno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="apellidoMaterno">Apellido Materno</label>
                            <input type="text" class="form-control @error('apellidoMaterno') is-invalid @enderror" id="apellidoMaterno" name="apellidoMaterno" value="{{ old('apellidoMaterno', $usuario->apellidoMaterno) }}">
                            @error('apellidoMaterno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="ci" class="required-label">CI</label>
                            <input type="number" class="form-control @error('ci') is-invalid @enderror" id="ci" name="ci" value="{{ old('ci', $usuario->ci) }}" required>
                            @error('ci')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="fechaNacimiento" class="required-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control @error('fechaNacimiento') is-invalid @enderror" id="fechaNacimiento" name="fechaNacimiento" value="{{ old('fechaNacimiento', $usuario->fechaNacimiento) }}" required>
                            @error('fechaNacimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="genero" class="required-label">Género</label>
                            <select class="form-control @error('genero') is-invalid @enderror" id="genero" name="genero" required>
                                <option value="">Seleccione...</option>
                                <option value="M" {{ old('genero', $usuario->genero) == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('genero', $usuario->genero) == 'F' ? 'selected' : '' }}>Femenino</option>
                            </select>
                            @error('genero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Información de Cuenta -->
                <div class="form-section">
                    <div class="form-section-title">Información de Cuenta</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email" class="required-label">Correo Electrónico</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $usuario->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Nueva Contraseña</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                            <small class="form-text text-muted">Dejar en blanco para mantener la contraseña actual</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirmar Nueva Contraseña</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>
                </div>

                <!-- Roles -->
                <div class="form-section">
                    <div class="form-section-title">Roles</div>
                    <div class="roles-container">
                        <div class="roles-selection">
                            <select id="role-selector" class="form-control">
                                <option value="">Seleccione un rol...</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->idRol }}" data-nombre="{{ $rol->nombre }}">{{ $rol->nombre }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="add-role-btn" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Agregar Rol
                            </button>
                        </div>
                        <div class="selected-roles-list" id="selected-roles-list">
                            @foreach($usuario->roles as $rol)
                                <div class="selected-role-item">
                                    <span>{{ $rol->nombre }}</span>
                                    <input type="hidden" name="roles[]" value="{{ $rol->idRol }}">
                                    <button type="button" class="remove-role-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <div id="roles-error" class="invalid-feedback d-none">Debe seleccionar al menos un rol</div>
                        @error('roles')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="form-actions">
                    <a href="{{ route('usuarios') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </form>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Manejo de roles
                    const roleSelector = document.getElementById('role-selector');
                    const addRoleBtn = document.getElementById('add-role-btn');
                    const selectedRolesList = document.getElementById('selected-roles-list');
                    const rolesError = document.getElementById('roles-error');
                    const editUserForm = document.getElementById('editUserForm');
                    
                    // Función para agregar un rol
                    addRoleBtn.addEventListener('click', function() {
                        if (roleSelector.value) {
                            const rolId = roleSelector.value;
                            const rolNombre = roleSelector.options[roleSelector.selectedIndex].dataset.nombre;
                            
                            // Verificar si el rol ya está seleccionado
                            const existingRoles = document.querySelectorAll('input[name="roles[]"]');
                            for (let i = 0; i < existingRoles.length; i++) {
                                if (existingRoles[i].value === rolId) {
                                    return; // El rol ya está seleccionado
                                }
                            }
                            
                            // Crear elemento de rol seleccionado
                            const roleItem = document.createElement('div');
                            roleItem.className = 'selected-role-item';
                            roleItem.innerHTML = `
                                <span>${rolNombre}</span>
                                <input type="hidden" name="roles[]" value="${rolId}">
                                <button type="button" class="remove-role-btn">
                                    <i class="fas fa-times"></i>
                                </button>
                            `;
                            
                            // Agregar evento para eliminar el rol
                            const removeBtn = roleItem.querySelector('.remove-role-btn');
                            removeBtn.addEventListener('click', function() {
                                roleItem.remove();
                                validateRoles();
                            });
                            
                            // Agregar a la lista
                            selectedRolesList.appendChild(roleItem);
                            
                            // Resetear selector
                            roleSelector.value = '';
                            
                            // Validar roles
                            validateRoles();
                        }
                    });
                    
                    // Agregar evento a los botones de eliminar existentes
                    const existingRemoveBtns = document.querySelectorAll('.remove-role-btn');
                    existingRemoveBtns.forEach(function(btn) {
                        btn.addEventListener('click', function() {
                            btn.closest('.selected-role-item').remove();
                            validateRoles();
                        });
                    });
                    
                    // Función para validar roles
                    function validateRoles() {
                        const selectedRoles = document.querySelectorAll('input[name="roles[]"]');
                        if (selectedRoles.length === 0) {
                            rolesError.classList.remove('d-none');
                            return false;
                        } else {
                            rolesError.classList.add('d-none');
                            return true;
                        }
                    }
                    
                    // Validar formulario antes de enviar
                    editUserForm.addEventListener('submit', function(e) {
                        if (!validateRoles()) {
                            e.preventDefault();
                        }
                    });
                });
            </script>
        </div>
    </div>
</x-app-layout>