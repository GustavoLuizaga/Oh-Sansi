<x-guest-layout>
    <div class="centered-logo">
        <img src="{{ asset('img/images/logoOhSansi.png') }}" alt="SanSi Logo">
    </div>

    <div class="forgot-password-container">
        <div class="forgot-password-text">
            ¿Olvidaste tu contraseña? No hay problema. Simplemente háganós saber su dirección de correo electrónico y le enviaremos un enlace de restablecimiento de contraseña que le permitirá elegir una nueva.
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="success-message" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="error-message" :errors="$errors" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="input-wrapper">
                <label class="input-label">Correo Electrónico</label>
                <div class="input-icon-group">
                    <i class="fas fa-envelope"></i>
                    <input 
                        type="email" 
                        name="email" 
                        class="email-input"
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                        placeholder="ohsansi@gmail.com"
                    >
                </div>
            </div>
            <button class="reset-button">
            ENLACE DE RESTABLECIMIENTO DE CONTRASEÑA DE CORREO ELECTRÓNICO
            </button>
            <div class="back-to-login">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i> Volver a inicio de sesión
                </a>
            </div>
        </form>
    </div>
</x-guest-layout>
