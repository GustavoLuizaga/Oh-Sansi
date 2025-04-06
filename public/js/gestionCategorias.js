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
        
        // Recopilar datos
        const formData = new FormData(FORMULARIO_PRINCIPAL);
        
        // Realizar la solicitud AJAX
        fetch('/gestionCategorias/', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('nuevaCategoriaModal'));
                modal.hide();
                window.location.reload();
                
                // Aquí puedes hacer lo que necesites después de crear la categoría
            } else {
                mostrarError('Hubo un error al crear la categoría');
            }
        })
        .catch(error => {
            mostrarError('Error en la conexión');
        });
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

document.addEventListener('DOMContentLoaded', function() {
    const confirmDeleteModal = document.getElementById('ConfirmarBorradoModal');
    let categoriaIdEliminar = null;

    // Manejar la apertura del modal y establecer el nombre de la categoría
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const categoriaNombre = this.getAttribute('data-categoria-nombre');
            categoriaIdEliminar = this.getAttribute('data-categoria-id');
            document.getElementById('nombreCategoriaEliminar').textContent = categoriaNombre;
        });
    });

    // Manejar la confirmación de eliminación
    document.getElementById('confirmarEliminar').addEventListener('click', function() {
        if (categoriaIdEliminar) {
            fetch(`/gestionCategorias/${categoriaIdEliminar}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cerrar el modal
                    const modalInstance = bootstrap.Modal.getInstance(confirmDeleteModal);
                    modalInstance.hide();

                    // Eliminar la fila de la tabla
                    document.querySelector(`tr[data-categoria-id="${categoriaIdEliminar}"]`).remove();
                } else {
                    alert('Hubo un error al eliminar la categoría.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al eliminar la categoría.');
            });
        }
    });
});

//Buscador de gestios de categotrias
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchCategoria');
    const tableRows = document.querySelectorAll('.areas-table tbody tr');

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();

        tableRows.forEach(row => {
            const categoriaName = row.querySelector('td:first-child').textContent.toLowerCase();
            
            if (categoriaName.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Mostrar mensaje cuando no hay resultados
        const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
        const tbody = document.querySelector('.areas-table tbody');
        const noResultsRow = tbody.querySelector('.no-results');

        if (visibleRows.length === 0) {
            if (!noResultsRow) {
                const tr = document.createElement('tr');
                tr.className = 'no-results';
                tr.innerHTML = '<td colspan="3" class="text-center text-danger">No se encontraron categorías con ese nombre</td>';
                tbody.appendChild(tr);
            }
        } else if (noResultsRow) {
            noResultsRow.remove();
        }
    });
});