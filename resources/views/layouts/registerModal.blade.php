<div id="register-type-modal" class="register-modal-overlay">
    <div class="register-modal-container">
        <span class="register-modal-close">&times;</span>
        <h2 class="register-modal-title">Registro</h2>
        <div class="register-modal-options">
            <a href="{{ route('register') }}" class="register-modal-option">
                <i class="fas fa-user-graduate"></i>
                <span>Estudiante</span>
            </a>
            <a href="{{ route('register.tutor') }}" class="register-modal-option">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Delegado</span>
            </a>
        </div>
    </div>
</div>