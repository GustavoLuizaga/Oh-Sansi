<x-guest-layout>
    <div class="registration-container">
        <div class="registration-card">
            <div class="registration-header">
                <h2><i class="fas fa-user-graduate"></i> Registro de Estudiante</h2>
            </div>

            <form method="POST" action="{{ route('register') }}" class="registration-form">
                @csrf

                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Nombre Completo*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Juan Carlos" required />
                        </div>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="apellidoPaterno">Apellido Paterno*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input id="apellidoPaterno" type="text" name="apellidoPaterno" value="{{ old('apellidoPaterno') }}" placeholder="Pérez" required />
                        </div>
                        @error('apellidoPaterno')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="apellidoMaterno">Apellido Materno*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input id="apellidoMaterno" type="text" name="apellidoMaterno" value="{{ old('apellidoMaterno') }}" placeholder="García" required />
                        </div>
                        @error('apellidoMaterno')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="ci">Carnet de Identidad*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-id-card"></i>
                            <input id="ci" type="text" name="ci" value="{{ old('ci') }}" placeholder="1234567" required />
                        </div>
                        @error('ci')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="fechaNacimiento">Fecha de Nacimiento*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-calendar"></i>
                            <input id="fechaNacimiento" type="date" name="fechaNacimiento" value="{{ old('fechaNacimiento') }}" required />
                        </div>
                        @error('fechaNacimiento')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
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
                        @error('genero')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Correo Electrónico*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="email@ejemplo.com" required />
                        </div>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input id="password" type="password" name="password" placeholder="********" required />
                            <i class="fas fa-eye toggle-password"></i>
                        </div>

                        <div class="progress-container">
                            <div class="progress">
                                <div id="password-strength-bar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="strength-labels">
                                <span>Débil</span>
                                <span>Media</span>
                                <span>Fuerte</span>
                            </div>
                        </div>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirmar Contraseña*</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input id="password_confirmation" type="password" name="password_confirmation" placeholder="********" required />
                            <i class="fas fa-eye toggle-password"></i>
                        </div>
                    </div>
                </div>

                <div class="terms-checkbox">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">Acepto los términos y condiciones</label>
                    @error('terms')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-footer">
                    <button type="submit" class="register-button">
                        Crear Cuenta
                    </button>
                    <p class="login">¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia Sesión aquí</a></p>
                </div>
            </form>
            <script src="/js/register-validation.js"></script>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordStrengthBar = document.getElementById('password-strength-bar');

        passwordInput.addEventListener('input', function () {
            const password = passwordInput.value;
            let strength = 0;

            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            let percent = 0;
            passwordStrengthBar.className = 'progress-bar';

            if (strength <= 1) {
                percent = 33;
                passwordStrengthBar.classList.add('strength-weak');
            } else if (strength <= 3) {
                percent = 66;
                passwordStrengthBar.classList.add('strength-medium');
            } else {
                percent = 100;
                passwordStrengthBar.classList.add('strength-strong');
            }

            passwordStrengthBar.style.width = percent + '%';
            passwordStrengthBar.setAttribute('aria-valuenow', percent);
        });
    </script>

    <style>
        .progress-container {
            margin-top: 10px;
        }

        .progress {
            height: 12px;
            border-radius: 8px;
            overflow: hidden;
            background-color: #e0e0e0;
            position: relative;
        }

        .progress-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease;
            border-radius: 8px;
        }

        .strength-weak {
            background-color: #f44336; /* rojo */
        }

        .strength-medium {
            background-color: #ffc107; /* amarillo */
        }

        .strength-strong {
            background-color: #4caf50; /* verde */
        }

        .strength-labels {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #555;
            padding-top: 4px;
        }

        .strength-labels span {
            flex: 1;
            text-align: center;
        }
    </style>
</x-guest-layout>
