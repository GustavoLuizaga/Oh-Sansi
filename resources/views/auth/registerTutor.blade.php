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
                            <select id="delegacion_tutoria" name="delegacion_tutoria" >
                                @if(isset($unidades) && $unidades->count() > 0)
                                    <option value="">Seleccionar Unidad Educativa</option>
                                    @foreach($unidades as $unidad)
                                        <option value="{{ $unidad->codigo }}" {{ old('delegacion_tutoria') == $unidad->codigo ? 'selected' : '' }}>
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
                        <label for="area_tutoria">Área de Tutoría*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-book"></i>
                            <select id="area_tutoria" name="area_tutoria" >
                                @if(isset($areas) && $areas->count() > 0)
                                    <option value="">Seleccionar área</option>
                                    @foreach($areas as $area)
                                        <option value="{{ $area->codigo }}" {{ old('area_tutoria') == $area->codigo ? 'selected' : '' }}>
                                            {{ $area->nombre }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">No hay áreas de tutoría disponibles</option>
                                @endif
                            </select>
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
            </form>
        </div>
    </div>
</x-guest-layout>