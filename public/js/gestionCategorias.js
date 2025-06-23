// Example starter JavaScript for disabling form submissions if there are invalid fields
(() => {
    'use strict'
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    const forms = document.querySelectorAll('.needs-validation')
    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
        }
        form.classList.add('was-validated')
        }, false)
    })
})()

/**
 * GESTIÓN COMPLETA DE CATEGORÍAS
 * Todos los event listeners en un solo bloque para evitar conflictos
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // ==================== CREAR NUEVA CATEGORÍA ====================
    const GRADOS_CONTAINER = document.getElementById('gradosContainer');
    const AGREGAR_GRADO_BTN = document.getElementById('agregarGradoBtn');
    const FORMULARIO_PRINCIPAL = document.getElementById('formNuevaCategoria');
    
    if (GRADOS_CONTAINER && AGREGAR_GRADO_BTN && FORMULARIO_PRINCIPAL) {
        // Clonar el primer elemento como plantilla
        const GRADO_TEMPLATE = GRADOS_CONTAINER.querySelector('.grado-item').cloneNode(true);
        
        function agregarGrado() {
            const nuevoGrado = GRADO_TEMPLATE.cloneNode(true);
            const select = nuevoGrado.querySelector('select');
            select.value = '';
            select.required = true;
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn-remove btn btn-outline-danger btn-sm ms-2';
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.title = 'Eliminar este grado';
            
            removeBtn.addEventListener('click', function() {
                nuevoGrado.remove();
                actualizarEstados();
            });
            
            select.insertAdjacentElement('afterend', removeBtn);
            GRADOS_CONTAINER.appendChild(nuevoGrado);
            actualizarEstados();
            select.focus();
        }
        
        function actualizarEstados() {
            const todosGrados = GRADOS_CONTAINER.querySelectorAll('.grado-item');
            todosGrados.forEach((grado, index) => {
                const removeBtn = grado.querySelector('.btn-remove');
                if (removeBtn) {
                    removeBtn.style.display = index === 0 ? 'none' : 'block';
                }
            });
        }
        
        
        function validarFormulario(e) {
            e.preventDefault();
            
            const gradosValidos = Array.from(document.querySelectorAll('#formNuevaCategoria select[name="grados[]"]'))
                .filter(select => select.value.trim() !== '');
            
            if (gradosValidos.length === 0) {
                alert('Debe seleccionar al menos un grado');
                return;
            }
            
            const formData = new FormData(FORMULARIO_PRINCIPAL);
            
            // Debug: Mostrar qué se está enviando
            console.log('Datos a enviar:');
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }
            
            // Usar la URL directa basada en tu action del formulario
            const url = '/gestionCategorias'; // URL directa sin helper de Laravel
            console.log('URL de envío:', url);
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Status:', response.status);
                console.log('Headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Respuesta:', data);
                if (data.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('nuevaCategoriaModal'));
                    modal.hide();
                    setTimeout(() => {
                        window.location.reload();
                    }, 300);
                } else {
                    alert(data.message || 'Error al crear la categoría');
                }
            })
            .catch(error => {
                console.error('Error completo:', error);
                alert('Error de conexión: ' + error.message);
            });
        }
        
        // Event listeners para crear categoría
        AGREGAR_GRADO_BTN.addEventListener('click', agregarGrado);
        FORMULARIO_PRINCIPAL.addEventListener('submit', validarFormulario);
        actualizarEstados();
    }
    
    // ==================== ELIMINAR CATEGORÍA ====================
    const confirmDeleteModal = document.getElementById('ConfirmarBorradoModal');
    let categoriaIdEliminar = null;

    if (confirmDeleteModal) {
        // Manejar botones de eliminar
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-delete')) {
                const button = e.target.closest('.btn-delete');
                const categoriaNombre = button.getAttribute('data-categoria-nombre');
                categoriaIdEliminar = button.getAttribute('data-categoria-id');
                document.getElementById('nombreCategoriaEliminar').textContent = categoriaNombre;
            }
        });

        // Confirmar eliminación
        const confirmarEliminarBtn = document.getElementById('confirmarEliminar');
        if (confirmarEliminarBtn) {
            confirmarEliminarBtn.addEventListener('click', function() {
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
                            const modalInstance = bootstrap.Modal.getInstance(confirmDeleteModal);
                            modalInstance.hide();
                            document.querySelector(`tr[data-categoria-id="${categoriaIdEliminar}"]`).remove();
                        }
                        // Si hay error, simplemente no hacer nada
                    })
                    .catch(error => {
                        // Silenciar errores
                        console.log('Error:', error);
                    });
                }
            });
        }
    }
    
    // ==================== EDITAR CATEGORÍA ====================
    const editarModal = document.getElementById('EditarCategoriaModal');
    
    if (editarModal) {
        let formSubmitHandler = null; // Para guardar referencia del handler
        
        editarModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const categoriaId = button.getAttribute('data-categoria-id');
            const categoriaNombre = button.getAttribute('data-categoria-nombre');
            const gradosData = JSON.parse(button.getAttribute('data-grados'));
            
            const form = editarModal.querySelector('form');
            const nombreInput = editarModal.querySelector('input[name="nombreCategoria"]');
            const gradosContainer = editarModal.querySelector('#gradosContainer');
            
            nombreInput.value = categoriaNombre;
            gradosContainer.innerHTML = '';
            
            // Agregar cada grado
            gradosData.forEach((grado, index) => {
                const gradoItem = document.createElement('div');
                gradoItem.className = 'grado-item mb-3 d-flex align-items-center gap-2';
                
                const select = document.createElement('select');
                select.className = 'form-select flex-grow-1';
                select.name = 'grados[]';
                select.required = true;
                
                const selectOriginal = document.querySelector('#nuevaCategoriaModal select[name="grados[]"]');
                if (selectOriginal) {
                    select.innerHTML = selectOriginal.innerHTML;
                }
                
                // Seleccionar el grado correcto
                for (let i = 0; i < select.options.length; i++) {
                    if (select.options[i].value == grado.id) {
                        select.options[i].selected = true;
                        break;
                    }
                }
                
                gradoItem.appendChild(select);
                
                const btnRemove = document.createElement('button');
                btnRemove.type = 'button';
                btnRemove.className = 'btn-remove btn btn-outline-danger btn-sm';
                btnRemove.innerHTML = '<i class="fas fa-times"></i>';
                btnRemove.style.display = index === 0 ? 'none' : 'block';
                
                btnRemove.addEventListener('click', function() {
                    gradoItem.remove();
                    const remainingGrados = gradosContainer.querySelectorAll('.grado-item');
                    if (remainingGrados.length === 1) {
                        remainingGrados[0].querySelector('.btn-remove').style.display = 'none';
                    }
                });
                
                gradoItem.appendChild(btnRemove);
                gradosContainer.appendChild(gradoItem);
            });
            
            form.action = `/gestionCategorias/${categoriaId}`;
            
            let methodField = form.querySelector('input[name="_method"]');
            if (!methodField) {
                methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                form.appendChild(methodField);
            }
            methodField.value = 'PUT';
            
            // IMPORTANTE: Remover event listener anterior si existe
            if (formSubmitHandler) {
                form.removeEventListener('submit', formSubmitHandler);
            }
            
            // Crear nuevo handler
            formSubmitHandler = function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const modal = bootstrap.Modal.getInstance(editarModal);
                        modal.hide();
                        location.reload();
                    }
                    // Si hay error, simplemente no hacer nada
                })
                .catch(error => {
                    // Silenciar errores
                    console.log('Error:', error);
                });
            };
            
            // Agregar el nuevo handler
            form.addEventListener('submit', formSubmitHandler);
        });
        
        // Botón agregar grado en modal de edición
        const agregarGradoEditarBtn = editarModal.querySelector('#agregarGradoBtn');
        if (agregarGradoEditarBtn) {
            agregarGradoEditarBtn.addEventListener('click', function() {
                const gradosContainer = editarModal.querySelector('#gradosContainer');
                
                const gradoItem = document.createElement('div');
                gradoItem.className = 'grado-item mb-3 d-flex align-items-center gap-2';
                
                const selectOriginal = document.querySelector('#nuevaCategoriaModal select[name="grados[]"]');
                const select = selectOriginal.cloneNode(true);
                select.value = '';
                
                gradoItem.appendChild(select);
                
                const btnRemove = document.createElement('button');
                btnRemove.type = 'button';
                btnRemove.className = 'btn-remove btn btn-outline-danger btn-sm';
                btnRemove.innerHTML = '<i class="fas fa-times"></i>';
                btnRemove.style.display = 'block';
                
                btnRemove.addEventListener('click', function() {
                    gradoItem.remove();
                });
                
                gradoItem.appendChild(btnRemove);
                gradosContainer.appendChild(gradoItem);
                select.focus();
            });
        }
    }
}); 

document.addEventListener('DOMContentLoaded', function() {
    // Variables de paginación
    let currentPage = 1;
    const itemsPerPage = 10;
    let filteredRows = [];
    let allRows = [];

    // Elementos del DOM
    const searchInput = document.querySelector('.search-box input');
    const orderSelect = document.querySelector('.filter-dropdown select');
    const tableBody = document.querySelector('.areas-table tbody');
    
    // Inicializar paginación
    initializePagination();

    function initializePagination() {
        // Obtener todas las filas (excluyendo la fila de "no hay categorías")
        allRows = Array.from(tableBody.querySelectorAll('tr')).filter(row => {
            return !row.querySelector('td[colspan]') || row.classList.contains('no-results-row');
        });
        
        filteredRows = [...allRows];
        
        // Crear contenedor de paginación
        createPaginationContainer();
        
        // Asegurar que todas las filas estén visibles inicialmente
       

            // Aplicar estilos de alineación a todas las filas antes de mostrar la primera página
        allRows.forEach(row => {
            const actionCell = row.querySelector('.action-cell');
            if (actionCell) {
                actionCell.style.textAlign = 'right';
                actionCell.style.display = 'flex';
                actionCell.style.justifyContent = 'flex-end';
                
                const publishedMessage = actionCell.querySelector('.published-area-message');
                if (publishedMessage) {
                    publishedMessage.style.display = 'flex';
                    publishedMessage.style.justifyContent = 'flex-end';
                    publishedMessage.style.width = '100%';
                    publishedMessage.style.marginLeft = '0';
                }
            }
        });
        
        // Mostrar primera página
        showPage(1);
        
        // Event listeners
        setupEventListeners();
    }

    function createPaginationContainer() {
        // Verificar si ya existe el contenedor
        if (document.querySelector('.pagination-container')) {
            return;
        }

        const container = document.querySelector('.area-container');
        const paginationHTML = `
            <div class="pagination-container">
                <div class="pagination-controls">
                    <button id="prevPage" class="pagination-btn" disabled>
                        <i class="fas fa-chevron-left"></i> Anterior
                    </button>
                    <div id="pageNumbers" class="page-numbers"></div>
                    <button id="nextPage" class="pagination-btn" disabled>
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="pagination-info">
                    <span id="paginationInfo">Mostrando 0 de 0 elementos</span>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', paginationHTML);
    }

    function setupEventListeners() {
        // Búsqueda
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                filterAndSort();
                currentPage = 1;
                showPage(currentPage);
            });
        }

        // Ordenamiento
        if (orderSelect) {
            orderSelect.addEventListener('change', function() {
                filterAndSort();
                currentPage = 1;
                showPage(currentPage);
            });
        }

        // Botones de paginación
        document.getElementById('prevPage').addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
            }
        });

        document.getElementById('nextPage').addEventListener('click', function() {
            const totalPages = Math.ceil(filteredRows.length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                showPage(currentPage);
            }
        });
    }

    function filterAndSort() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const orderBy = orderSelect ? orderSelect.value : '';

        // Filtrar por nombre de categoría
        filteredRows = allRows.filter(row => {
            const categoryName = row.querySelector('td:first-child').textContent.toLowerCase();
            return categoryName.includes(searchTerm);
        });

        // Ordenar según la opción seleccionada
        if (orderBy === 'Nivel (A-Z)') {
            filteredRows.sort((a, b) => {
                const nameA = a.querySelector('td:first-child').textContent.toLowerCase();
                const nameB = b.querySelector('td:first-child').textContent.toLowerCase();
                return nameA.localeCompare(nameB);
            });
        } else if (orderBy === 'Categoria (Z-A)') {
            filteredRows.sort((a, b) => {
                const nameA = a.querySelector('td:first-child').textContent.toLowerCase();
                const nameB = b.querySelector('td:first-child').textContent.toLowerCase();
                return nameB.localeCompare(nameA);
            });
        } else if (orderBy === 'Fecha de creación') {
            // Para fecha de creación, usaremos el data-categoria-id como proxy del orden de creación
            filteredRows.sort((a, b) => {
                const idA = parseInt(a.getAttribute('data-categoria-id'));
                const idB = parseInt(b.getAttribute('data-categoria-id'));
                return idA - idB;
            });
        }
    }

    function showPage(page) {
    const startIndex = (page - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const totalPages = Math.ceil(filteredRows.length / itemsPerPage);

    // Obtener las filas que deben mostrarse en esta página
    const pageRows = filteredRows.slice(startIndex, endIndex);
    
    // Primero: Restaurar estilos de todas las filas
    allRows.forEach(row => {
        row.style.display = 'none';
        
        // Aplicar estilos de alineación a todas las filas, no solo a las visibles
        const actionCell = row.querySelector('.action-cell');
        if (actionCell) {
            actionCell.style.textAlign = 'right';
            actionCell.style.display = 'flex';
            actionCell.style.justifyContent = 'flex-end';
            
            const publishedMessage = actionCell.querySelector('.published-area-message');
            if (publishedMessage) {
                publishedMessage.style.display = 'flex';
                publishedMessage.style.justifyContent = 'flex-end';
                publishedMessage.style.width = '100%';
                publishedMessage.style.marginLeft = '0';
            }
        }
    });
    
    // Segundo: Mostrar solo las filas de la página actual
    pageRows.forEach(row => {
        row.style.display = '';
    });

    // Manejar mensaje de "no hay resultados"
    let noDataRow = tableBody.querySelector('.no-results-row');
    
    if (filteredRows.length === 0) {
        if (!noDataRow) {
            noDataRow = document.createElement('tr');
            noDataRow.innerHTML = '<td colspan="3" class="text-center">No se encontraron categorías</td>';
            noDataRow.classList.add('no-results-row');
            tableBody.appendChild(noDataRow);
        }
        noDataRow.style.display = '';
    } else if (noDataRow) {
        noDataRow.style.display = 'none';
    }

    // Actualizar información de paginación
    updatePaginationInfo(startIndex, endIndex, filteredRows.length);
    
    // Actualizar controles de paginación
    updatePaginationControls(page, totalPages);
    
    // Actualizar números de página
    updatePageNumbers(page, totalPages);
}

    function updatePaginationInfo(startIndex, endIndex, total) {
        const paginationInfo = document.getElementById('paginationInfo');
        if (total === 0) {
            paginationInfo.textContent = 'No hay elementos para mostrar';
        } else {
            const showing = Math.min(endIndex, total);
            paginationInfo.textContent = `Mostrando ${startIndex + 1}-${showing} de ${total} elementos`;
        }
    }

    function updatePaginationControls(page, totalPages) {
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        
        prevBtn.disabled = page <= 1;
        nextBtn.disabled = page >= totalPages || totalPages === 0;
        
        // Actualizar clases para estilos
        prevBtn.classList.toggle('disabled', page <= 1);
        nextBtn.classList.toggle('disabled', page >= totalPages || totalPages === 0);
    }

    function updatePageNumbers(currentPage, totalPages) {
        const pageNumbersContainer = document.getElementById('pageNumbers');
        pageNumbersContainer.innerHTML = '';

        if (totalPages <= 1) return;

        // Determinar rango de páginas a mostrar
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);

        // Ajustar si estamos cerca del inicio o final
        if (currentPage <= 3) {
            endPage = Math.min(5, totalPages);
        }
        if (currentPage >= totalPages - 2) {
            startPage = Math.max(1, totalPages - 4);
        }

        // Botón primera página
        if (startPage > 1) {
            createPageButton(1, currentPage, pageNumbersContainer);
            if (startPage > 2) {
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                ellipsis.className = 'page-ellipsis';
                pageNumbersContainer.appendChild(ellipsis);
            }
        }

        // Botones de páginas
        for (let i = startPage; i <= endPage; i++) {
            createPageButton(i, currentPage, pageNumbersContainer);
        }

        // Botón última página
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                ellipsis.className = 'page-ellipsis';
                pageNumbersContainer.appendChild(ellipsis);
            }
            createPageButton(totalPages, currentPage, pageNumbersContainer);
        }
    }

    function createPageButton(pageNum, currentPage, container) {
        const button = document.createElement('button');
        button.textContent = pageNum;
        button.className = `page-btn ${pageNum === currentPage ? 'active' : ''}`;
        button.addEventListener('click', function() {
            currentPage = pageNum;
            showPage(currentPage);
        });
        container.appendChild(button);
    }
});