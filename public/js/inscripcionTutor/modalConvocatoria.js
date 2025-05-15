/**
 * modalConvocatoria.js - Maneja la visualización dinámica de información de convocatorias en el modal
 */
document.addEventListener('DOMContentLoaded', function() {
    const modalConvocatoriaDropdown = document.getElementById('modal-convocatoria-dropdown');
    const modalConvocatoriaDetails = document.getElementById('modal-convocatoria-details');

    // Si no existe el desplegable, no seguimos
    if (!modalConvocatoriaDropdown || !modalConvocatoriaDetails) {
        console.error('No se encontraron los elementos necesarios para el modal de convocatorias');
        return;
    }
    
    // Inicializar el modal con un estado vacío
    modalConvocatoriaDetails.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-list-alt"></i>
            <p>Seleccione una convocatoria para ver sus detalles</p>
        </div>
    `;

    console.log('Inicializando listeners para el modal de convocatorias');
    
    // Cuando se cambia la convocatoria seleccionada en el modal
    modalConvocatoriaDropdown.addEventListener('change', function() {
        const convocatoriaId = this.value;

        if (!convocatoriaId) {
            // Mostrar estado vacío si no hay convocatoria seleccionada
            modalConvocatoriaDetails.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-list-alt"></i>
                    <p>Seleccione una convocatoria para ver sus detalles</p>
                </div>
            `;
            return;
        }

        // Mostrar indicador de carga
        modalConvocatoriaDetails.innerHTML = `
            <div class="loading-state">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p>Cargando información de la convocatoria...</p>
            </div>
        `;

        // Obtener datos de la convocatoria
        fetchConvocatoriaDetails(convocatoriaId);
    });

    /**
     * Obtiene los detalles de la convocatoria seleccionada
     * @param {number} convocatoriaId - ID de la convocatoria
     */
    function fetchConvocatoriaDetails(convocatoriaId) {
        console.log('Solicitando datos para convocatoria:', convocatoriaId);
        
        // Obtener la URL base actual (para manejar subdirectorios en caso de que existan)
        const baseUrl = window.location.pathname.startsWith('/oh-sansi') ? '/oh-sansi' : '';
        
        // Construir la URL completa para la API
        const apiUrl = `${baseUrl}/api/convocatoria/${convocatoriaId}/areas-categorias-grados`;
        
        console.log('Realizando solicitud a:', apiUrl);
          fetch(apiUrl, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Respuesta recibida:', response.status);
            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            
            // Verificar si hay un error en la respuesta
            if (data.error) {
                throw new Error(data.message || 'Error en los datos recibidos');
            }
            
            // Verificar que la estructura de datos sea correcta
            if (!data.areas) {
                throw new Error('Formato de datos incorrecto');
            }
            
            renderConvocatoriaDetails(data);
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Intentar analizar el error para dar un mensaje más adecuado
            let errorMessage = 'Error de conexión. Verifique su conexión a internet.';
            
            if (error.message.includes('404')) {
                errorMessage = 'No se encontró la información de esta convocatoria.';
            } else if (error.message.includes('500')) {
                errorMessage = 'Error en el servidor. Por favor contacte al administrador.';
            } else if (error.message.includes('Formato')) {
                errorMessage = 'Los datos recibidos tienen un formato incorrecto.';
            }
            
            // Mostrar error al usuario
            modalConvocatoriaDetails.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-circle text-danger"></i>
                    <p>Error al cargar los datos</p>
                    <p class="text-muted">${errorMessage}</p>
                    <button class="btn btn-sm btn-outline-secondary mt-3" onclick="document.getElementById('modal-convocatoria-dropdown').dispatchEvent(new Event('change'))">
                        <i class="fas fa-redo"></i> Intentar nuevamente
                    </button>
                </div>
            `;
        });
    }
    
    /**
     * Renderiza los detalles de áreas, categorías y grados de la convocatoria
     * @param {Object} data - Datos de áreas, categorías y grados
     */    function renderConvocatoriaDetails(data) {
        console.log('Renderizando datos de convocatoria:', data);
        
        if (!data || !data.areas) {
            modalConvocatoriaDetails.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i>
                    <p>No se pudo obtener información para esta convocatoria</p>
                </div>
            `;
            return;
        }
        
        if (data.areas.length === 0) {
            modalConvocatoriaDetails.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-info-circle"></i>
                    <p>No hay áreas definidas para esta convocatoria</p>
                </div>
            `;
            return;
        }        let html = `
            <div class="convocatoria-details-container">
                <div class="convocatoria-summary">
                    <p><strong>Total de áreas:</strong> ${data.areas.length}</p>
                </div>
        `;

        // Generar HTML para cada área
        data.areas.forEach(area => {
            html += `
                <div class="card-area">
                    <h3 class="titulo-area">${area.nombre || 'Área sin nombre'}</h3>
            `;

            // Si hay categorías para esta área
            if (area.categorias && area.categorias.length > 0) {
                // Generar HTML para cada categoría
                area.categorias.forEach(categoria => {
                    html += `
                        <div class="card-categoria">
                            <h4 class="titulo-categoria">${categoria.nombre || 'Categoría sin nombre'}</h4>
                    `;
                    
                    // Si hay grados para esta categoría
                    if (categoria.grados && categoria.grados.length > 0) {
                        html += '<ul class="lista-grados">';
                        
                        categoria.grados.forEach(grado => {
                            // Usar nombre, grado o un valor por defecto, en ese orden
                            const nombreGrado = grado.nombre || grado.grado || 'Grado sin nombre';
                            html += `<li class="card-grado">${nombreGrado}</li>`;
                        });
                        
                        html += '</ul>';
                    } else {
                        html += '<p class="text-muted">No hay grados definidos para esta categoría.</p>';
                    }
                    
                    html += '</div>'; // Cierra card-categoria
                });
            } else {
                html += `
                    <div class="card-categoria">
                        <p class="text-muted">No hay categorías definidas para esta área.</p>
                    </div>
                `;
            }
            
            html += '</div>'; // Cierra card-area
        });
        
        html += '</div>'; // Cierra convocatoria-details-container
        
        // Actualizar el contenido
        modalConvocatoriaDetails.innerHTML = html;
    }

});

/**
 * Función para mostrar el modal de información de convocatoria
 */
function mostrarModal() {
    const modal = document.getElementById('modalDatos');
    if (modal) {
        modal.style.display = 'flex';
    }
}

/**
 * Función para cerrar el modal
 */
function cerrarModal() {
    const modal = document.getElementById('modalDatos');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Cerrar el modal al hacer clic fuera de su contenido
window.addEventListener('click', function(event) {
    const modal = document.getElementById('modalDatos');
    if (event.target === modal) {
        cerrarModal();
    }
});
window.addEventListener('click', function(event) {
    const modal = document.getElementById('modalDatos');
    if (event.target === modal) {
        cerrarModal();
    }
});
