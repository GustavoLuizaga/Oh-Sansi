<x-guest-layout>
    <div class="registration-container">
        <div class="registration-card">
            <div class="registration-header">
                <h2><i class="fas fa-chalkboard-teacher"></i> Registro de Tutor</h2>
            </div>

            <form method="POST" action="{{ route('register.tutor.store') }}" class="registration-form" enctype="multipart/form-data">
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Nombre Completo*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Juan Carlos" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="apellidoPaterno">Apellido Paterno*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input id="apellidoPaterno" type="text" name="apellidoPaterno" value="{{ old('apellidoPaterno') }}" placeholder="Pérez" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="apellidoMaterno">Apellido Materno*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input id="apellidoMaterno" type="text" name="apellidoMaterno" value="{{ old('apellidoMaterno') }}" placeholder="García" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="ci">Carnet de Identidad*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-id-card"></i>
                            <input id="ci" type="text" name="ci" value="{{ old('ci') }}" placeholder="1234567" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fechaNacimiento">Fecha de Nacimiento*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-calendar"></i>
                            <input id="fechaNacimiento" type="date" name="fechaNacimiento" value="{{ old('fechaNacimiento') }}" placeholder="dd/mm/aaaa" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="genero">Género*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-venus-mars"></i>
                            <select id="genero" name="genero" required>
                                <option value="">Seleccionar</option>
                                <option value="M" {{ old('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                                <option value="F" {{ old('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="telefono">Teléfono*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-phone"></i>
                            <input id="telefono" type="tel" name="telefono" value="{{ old('telefono') }}" placeholder="70707070" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="profesion">Profesión*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-graduation-cap"></i>
                            <input id="profesion" type="text" name="profesion" value="{{ old('profesion') }}" placeholder="Ingeniero en Sistemas" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo Electrónico*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="email@ejemplo.com" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="delegacion_tutoria">Delegacion Tutoria*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-school"></i>
                            <select id="delegacion_tutoria" name="delegacion_tutoria" required>
                                @if(isset($unidades) && $unidades->count() > 0)
                                    <option value="">Seleccionar Unidad Educativa</option>
                                    @foreach($unidades as $unidad)
                                        <option value="{{ $unidad->idDelegacion }}" {{ old('delegacion_tutoria') == $unidad->idDelegacion ? 'selected' : '' }}>
                                            {{ $unidad->nombre }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">No hay unidades educativas disponibles</option>
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="area_tutoria">Áreas de Tutoría*</label>
                        <div class="areas-search">
                            <i class="fas fa-search"></i>
                            <input type="text" id="areas_search" placeholder="Buscar áreas..." />
                        </div>
                        <div class="areas-container">
                            @if(isset($areas) && $areas->count() > 0)
                                @foreach($areas as $area)
                                    <div class="area-option" data-area-name="{{ strtolower($area->nombre) }}">
                                        <input type="checkbox" 
                                            id="area_{{ $area->idArea }}" 
                                            name="area_tutoria[]" 
                                            value="{{ $area->idArea }}" 
                                            {{ (is_array(old('area_tutoria')) && in_array($area->idArea, old('area_tutoria'))) ? 'checked' : '' }}
                                        />
                                        <label for="area_{{ $area->idArea }}">{{ $area->nombre }}</label>
                                    </div>
                                @endforeach
                            @else
                                <div class="no-areas">No hay áreas de tutoría disponibles</div>
                            @endif
                        </div>
                        <div class="areas-actions">
                            <div class="select-all-option">
                                <input type="checkbox" id="select_all_areas" name="select_all_areas">
                                <label for="select_all_areas">Seleccionar todas las áreas</label>
                            </div>
                            <div class="selected-count">0 áreas seleccionadas</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input id="password" type="password" name="password" placeholder="********" required />
                            <i class="fas fa-eye toggle-password"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirmar Contraseña*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input id="password_confirmation" type="password" name="password_confirmation" placeholder="********" required />
                            <i class="fas fa-eye toggle-password"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cv">Validar ser tutor(PDF)*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-file-pdf"></i>
                            <input 
                                id="cv" 
                                type="file" 
                                name="cv" 
                                accept=".pdf"
                                required 
                                class="file-input" 
                            />
                        </div>
                        @error('cv')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="terms-checkbox">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">Acepto los términos y condiciones</label>
                </div>

                <div class="form-footer">
                    <button type="submit" class="register-button">
                        Crear Cuenta de Tutor
                    </button>
                    <p class="login">¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia Sesión aquí</a></p>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const selectAllCheckbox = document.getElementById('select_all_areas');
                        const areaCheckboxes = document.querySelectorAll('.area-option input[type="checkbox"]');
                        const areaOptions = document.querySelectorAll('.area-option');
                        const searchInput = document.getElementById('areas_search');
                        const selectedCountElement = document.querySelector('.selected-count');
                        
                        // Función para actualizar el contador de áreas seleccionadas
                        function updateSelectedCount() {
                            const selectedCount = document.querySelectorAll('.area-option input[type="checkbox"]:checked').length;
                            selectedCountElement.textContent = selectedCount + ' áreas seleccionadas';
                        }
                        
                        // Inicializar el contador
                        updateSelectedCount();
                        
                        // Función para seleccionar o deseleccionar todas las áreas
                        selectAllCheckbox.addEventListener('change', function() {
                            areaCheckboxes.forEach(checkbox => {
                                // Solo cambiar si el área es visible (no está filtrada)
                                if (checkbox.closest('.area-option').style.display !== 'none') {
                                    checkbox.checked = selectAllCheckbox.checked;
                                    
                                    // Actualizar clase selected
                                    if (selectAllCheckbox.checked) {
                                        checkbox.closest('.area-option').classList.add('selected');
                                    } else {
                                        checkbox.closest('.area-option').classList.remove('selected');
                                    }
                                }
                            });
                            updateSelectedCount();
                        });
                        
                        // Actualizar el estado del checkbox cuando se seleccionan/deseleccionan áreas manualmente
                        areaCheckboxes.forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                // Actualizar clase selected
                                if (this.checked) {
                                    this.closest('.area-option').classList.add('selected');
                                } else {
                                    this.closest('.area-option').classList.remove('selected');
                                }
                                
                                // Verificar si todas las áreas visibles están seleccionadas
                                let allVisibleSelected = true;
                                areaCheckboxes.forEach(cb => {
                                    if (cb.closest('.area-option').style.display !== 'none' && !cb.checked) {
                                        allVisibleSelected = false;
                                    }
                                });
                                
                                selectAllCheckbox.checked = allVisibleSelected;
                                updateSelectedCount();
                            });
                        });
                        
                        // Funcionalidad de búsqueda
                        searchInput.addEventListener('input', function() {
                            const searchTerm = this.value.toLowerCase().trim();
                            
                            areaOptions.forEach(option => {
                                const areaName = option.getAttribute('data-area-name');
                                if (areaName.includes(searchTerm)) {
                                    option.style.display = 'flex';
                                } else {
                                    option.style.display = 'none';
                                }
                            });
                            
                            // Actualizar estado del checkbox "seleccionar todos"
                            let allVisibleSelected = true;
                            let visibleCount = 0;
                            
                            areaCheckboxes.forEach(cb => {
                                if (cb.closest('.area-option').style.display !== 'none') {
                                    visibleCount++;
                                    if (!cb.checked) {
                                        allVisibleSelected = false;
                                    }
                                }
                            });
                            
                            selectAllCheckbox.checked = allVisibleSelected && visibleCount > 0;
                        });
                        
                        // Hacer que al hacer clic en la etiqueta también se active/desactive el checkbox
                        areaOptions.forEach(option => {
                            option.addEventListener('click', function(e) {
                                // Evitar que se active dos veces cuando se hace clic directamente en el checkbox o la etiqueta
                                if (e.target !== this) return;
                                
                                const checkbox = this.querySelector('input[type="checkbox"]');
                                checkbox.checked = !checkbox.checked;
                                
                                // Disparar el evento change manualmente
                                const event = new Event('change');
                                checkbox.dispatchEvent(event);
                            });
                        });
                    });
                </script>
            </form>
        </div>
    </div>
</x-guest-layout>