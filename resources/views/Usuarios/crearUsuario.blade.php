<x-app-layout>
    <link rel="stylesheet" href="/css/usuarios/crearUsuario.css">
    <!-- Header Section -->
    <div class="crear-usuario-header py-2">
        <h1><i class="fas fa-user-plus"></i> {{ __('Crear Nuevo Usuario') }}</h1>
    </div>

    <div class="crear-usuario-container">
        <div class="crear-usuario-form">
            <form method="POST" action="{{ route('usuarios.store') }}" id="createUserForm">
                @csrf

                <!-- Información Personal -->
                <div class="form-section">
                    <div class="form-section-title">Información Personal</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name" class="required-label">Nombre</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="apellidoPaterno" class="required-label">Apellido Paterno</label>
                            <input type="text" class="form-control @error('apellidoPaterno') is-invalid @enderror" id="apellidoPaterno" name="apellidoPaterno" value="{{ old('apellidoPaterno') }}" required>
                            @error('apellidoPaterno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="apellidoMaterno">Apellido Materno</label>
                            <input type="text" class="form-control @error('apellidoMaterno') is-invalid @enderror" id="apellidoMaterno" name="apellidoMaterno" value="{{ old('apellidoMaterno') }}">
                            @error('apellidoMaterno')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="ci" class="required-label">CI</label>
                            <input type="number" class="form-control @error('ci') is-invalid @enderror" id="ci" name="ci" value="{{ old('ci') }}" required maxlength="7" oninput="if(this.value.length > 7) this.value = this.value.slice(0, 7)">
                            <small class="form-text text-muted">Máximo 7 dígitos</small>
                            @error('ci')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="fechaNacimiento" class="required-label">Fecha de Nacimiento</label>
                            <input type="date" class="form-control @error('fechaNacimiento') is-invalid @enderror" id="fechaNacimiento" name="fechaNacimiento" value="{{ old('fechaNacimiento') }}" required>
                            @error('fechaNacimiento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="genero" class="required-label">Género</label>
                            <select class="form-control @error('genero') is-invalid @enderror" id="genero" name="genero" required>
                                <option value="">Seleccione...</option>
                                <option value="M" {{ old('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
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
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email_verified_at">Verificación de Correo</label>
                            <div class="email-verification-toggle">
                                <input type="checkbox" id="email_verified_at" name="email_verified_at" value="1" {{ old('email_verified_at') ? 'checked' : '' }}>
                                <label for="email_verified_at" class="toggle-label">Marcar como verificado</label>
                            </div>
                            <small class="form-text text-muted">Si no se marca, se enviará un correo de verificación automáticamente</small>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password" class="required-label">Contraseña</label>
                            <div class="password-input-container">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                <button type="button" class="toggle-password-btn" data-target="password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation" class="required-label">Confirmar Contraseña</label>
                            <div class="password-input-container">
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                <button type="button" class="toggle-password-btn" data-target="password_confirmation">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
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
                            @if(is_array(old('roles')))
                                @foreach(old('roles') as $rolId)
                                    @php
                                        $rol = $roles->firstWhere('idRol', $rolId);
                                    @endphp
                                    @if($rol)
                                        <div class="selected-role-item">
                                            <span>{{ $rol->nombre }}</span>
                                            <input type="hidden" name="roles[]" value="{{ $rol->idRol }}">
                                            <button type="button" class="remove-role-btn">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
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
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Manejo de contraseñas visibles/ocultas
                    const togglePasswordBtns = document.querySelectorAll('.toggle-password-btn');
                    togglePasswordBtns.forEach(btn => {
                        btn.addEventListener('click', function() {
                            const targetId = this.getAttribute('data-target');
                            const passwordInput = document.getElementById(targetId);
                            const icon = this.querySelector('i');
                            
                            if (passwordInput.type === 'password') {
                                passwordInput.type = 'text';
                                icon.classList.remove('fa-eye');
                                icon.classList.add('fa-eye-slash');
                            } else {
                                passwordInput.type = 'password';
                                icon.classList.remove('fa-eye-slash');
                                icon.classList.add('fa-eye');
                            }
                        });
                    });
                    
                    // Manejo de roles
                    const roleSelector = document.getElementById('role-selector');
                    const addRoleBtn = document.getElementById('add-role-btn');
                    const selectedRolesList = document.getElementById('selected-roles-list');
                    const rolesError = document.getElementById('roles-error');
                    const createUserForm = document.getElementById('createUserForm');
                    
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
                    
                    // Agregar eventos de eliminación a roles existentes
                    const existingRemoveBtns = document.querySelectorAll('.remove-role-btn');
                    existingRemoveBtns.forEach(btn => {
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
                            rolesError.classList.add('d-block');
                            return false;
                        } else {
                            rolesError.classList.remove('d-block');
                            rolesError.classList.add('d-none');
                            return true;
                        }
                    }
                    
                    // Validar formulario antes de enviar
                    createUserForm.addEventListener('submit', function(e) {
                        if (!validateRoles()) {
                            e.preventDefault();
                            // Desplazarse a la sección de roles
                            document.querySelector('.form-section-title').scrollIntoView({ behavior: 'smooth' });
                        }
                    });
                    
                    // Validación inicial
                    validateRoles();
                });
            </script>
        </div>
    </div>
</x-app-layout>