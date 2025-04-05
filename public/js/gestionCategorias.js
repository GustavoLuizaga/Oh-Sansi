/**
 * Gestiona la funcionalidad del modal de nueva categoría
 * Permite agregar y eliminar múltiples selecciones de grado
 * - Siempre mantiene al menos un select visible
 * - Solo muestra botón eliminar en selects agregados (no en el primero)
 */
document.addEventListener('DOMContentLoaded', function() {
    // Constantes y elementos del DOM
    const GRADOS_CONTAINER = document.getElementById('gradosContainer');
    const AGREGAR_GRADO_BTN = document.getElementById('agregarGradoBtn');
    const FORMULARIO_PRINCIPAL = document.getElementById('formNuevaCategoria');
    
    // Clonar el primer elemento como plantilla (sin botón de eliminar)
    const GRADO_TEMPLATE = GRADOS_CONTAINER.querySelector('.grado-item').cloneNode(true);
    
    /**
     * Agrega un nuevo campo de selección de grado
     * - Crea un clon de la plantilla
     * - Configura el nuevo elemento
     */
    function agregarGrado() {
        // Crear clon y configurar select
        const nuevoGrado = GRADO_TEMPLATE.cloneNode(true);
        const select = nuevoGrado.querySelector('select');
        select.value = '';
        select.required = true;
        
        // Crear y configurar botón de eliminar
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn-remove btn btn-outline-danger btn-sm ms-2';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.title = 'Eliminar este grado';
        
        // Evento para eliminar el grado
        removeBtn.addEventListener('click', function() {
            nuevoGrado.remove();
            actualizarEstados();
        });
        
        // Insertar botón después del select
        select.insertAdjacentElement('afterend', removeBtn);
        
        // Agregar al contenedor
        GRADOS_CONTAINER.appendChild(nuevoGrado);
        
        // Actualizar estados y enfocar
        actualizarEstados();
        select.focus();
    }
    
    /**
     * Actualiza los estados de los elementos
     * - Asegura que el primer select nunca tenga botón de eliminar
     * - Muestra botones en los demás elementos
     */
    function actualizarEstados() {
        const todosGrados = GRADOS_CONTAINER.querySelectorAll('.grado-item');
        
        todosGrados.forEach((grado, index) => {
            const removeBtn = grado.querySelector('.btn-remove');
            
            // Solo mostramos botón en elementos agregados (índice > 0)
            if (removeBtn) {
                removeBtn.style.display = index === 0 ? 'none' : 'block';
            }
        });
    }
    
    /**
     * Valida el formulario antes de enviar
     * - Verifica que al menos un grado esté seleccionado
     */
    function validarFormulario(e) {
        e.preventDefault();
        
        const gradosValidos = Array.from(document.querySelectorAll('select[name="grados[]"]'))
            .filter(select => select.value.trim() !== '');
        
        if (gradosValidos.length === 0) {
            mostrarError('Por favor selecciona al menos un grado');
            return;
        }
        
        // Validación exitosa - proceder con envío
        console.log('Datos a enviar:', {
            categoria: document.getElementById('nombreCategoria').value,
            grados: gradosValidos.map(select => select.value)
        });
        
        // this.submit(); // Descomentar para enviar realmente
    }
    
    /**
     * Muestra mensajes de error al usuario
     * @param {string} mensaje - Texto del error a mostrar
     */
    function mostrarError(mensaje) {
        // Implementación mejorable con Toast de Bootstrap
        alert(mensaje);
    }
    
    // Event listeners
    AGREGAR_GRADO_BTN.addEventListener('click', agregarGrado);
    FORMULARIO_PRINCIPAL.addEventListener('submit', validarFormulario);
    
    // Estado inicial
    actualizarEstados();
});