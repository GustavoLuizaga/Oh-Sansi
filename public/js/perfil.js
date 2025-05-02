document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const editBtn = document.getElementById('edit-profile-btn');
    const cancelBtn = document.getElementById('cancel-edit-btn');
    const saveBtn = document.getElementById('save-profile-btn');
    const profileForm = document.getElementById('profile-form');
    const passwordSection = document.querySelector('.password-section');
    const profileInputs = document.querySelectorAll('.profile-input');
    
    // Estado inicial
    let isEditing = false;
    
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
        
        // Mostrar sección de cambio de contraseña
        passwordSection.classList.remove('hidden');
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
        
        // Ocultar sección de cambio de contraseña
        passwordSection.classList.add('hidden');
        
        // Resetear el formulario a los valores originales
        profileForm.reset();
    }
    
    // Event listeners
    editBtn.addEventListener('click', enableEditing);
    cancelBtn.addEventListener('click', cancelEditing);
});