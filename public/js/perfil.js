document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const editBtn = document.getElementById('edit-profile-btn');
    const cancelBtn = document.getElementById('cancel-edit-btn');
    const saveBtn = document.getElementById('save-profile-btn');
    const profileForm = document.getElementById('profile-form');
    const passwordSection = document.querySelector('.password-section');
    const profileInputs = document.querySelectorAll('.profile-input');
    const changePasswordBtn = document.getElementById('change-password-btn');
    
    // Estado inicial
    let isEditing = false;
    let isPasswordVisible = false;
    
    // Función para habilitar la edición
    function enableEditing() {
        isEditing = true;
        
        // Habilitar todos los inputs del perfil
        profileInputs.forEach(input => {
            input.disabled = false;
        });
        
        // Mostrar botones de guardar y cancelar, ocultar botón de editar
        editBtn.classList.add('hidden');
        cancelBtn.classList.remove('hidden');
        saveBtn.classList.remove('hidden');
    }
    
    // Función para cancelar la edición
    function cancelEditing() {
        isEditing = false;
        
        // Deshabilitar todos los inputs del perfil
        profileInputs.forEach(input => {
            input.disabled = true;
        });
        
        // Mostrar botón de editar, ocultar botones de guardar y cancelar
        editBtn.classList.remove('hidden');
        cancelBtn.classList.add('hidden');
        saveBtn.classList.add('hidden');
        
        // Resetear el formulario a los valores originales
        profileForm.reset();
    }

    // Función para toggle sección de contraseña
    function togglePasswordSection() {
        isPasswordVisible = !isPasswordVisible;
        if (isPasswordVisible) {
            passwordSection.classList.remove('hidden');
        } else {
            passwordSection.classList.add('hidden');
        }
    }
    
    // Event listeners
    editBtn.addEventListener('click', enableEditing);
    cancelBtn.addEventListener('click', cancelEditing);
    changePasswordBtn.addEventListener('click', togglePasswordSection);
});