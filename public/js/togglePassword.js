document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar todos los botones de toggle password
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');
    
    // Añadir evento de clic a cada botón
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Encontrar el campo de contraseña asociado (el input anterior al botón)
            const passwordInput = this.previousElementSibling;
            
            // Cambiar el tipo de input entre 'password' y 'text'
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            }
        });
    });
});