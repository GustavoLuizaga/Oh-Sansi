// Funciones para manejar los modales
function abrirModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
}

function cerrarModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Cerrar modal al hacer clic fuera del contenido
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Función para ver detalles del estudiante
function verEstudiante(id) {
    fetch(`/estudiantes/ver/${id}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const estudiante = data.estudiante;
            
            // Actualizar el contenido del modal
            document.getElementById('verCI').textContent = estudiante.ci || 'No disponible';
            document.getElementById('verNombre').textContent = estudiante.nombre || 'No disponible';
            document.getElementById('verApellidos').textContent = 
                `${estudiante.apellidoPaterno || ''} ${estudiante.apellidoMaterno || ''}`;
            
            // Actualizar fecha de registro si está disponible
            const fechaRegistro = document.getElementById('verFechaRegistro');
            if (fechaRegistro) {
                fechaRegistro.textContent = estudiante.fechaNacimiento ? 
                    new Date(estudiante.fechaNacimiento).toLocaleDateString() : 'No disponible';
            }
            
            // Actualizar información académica si existe
            if (estudiante.area && estudiante.area.nombre) {
                document.getElementById('verArea').textContent = estudiante.area.nombre;
            } else {
                document.getElementById('verArea').textContent = 'No asignada';
            }
            
            if (estudiante.categoria && estudiante.categoria.nombre) {
                document.getElementById('verCategoria').textContent = estudiante.categoria.nombre;
            } else {
                document.getElementById('verCategoria').textContent = 'No asignada';
            }
            
            if (estudiante.delegacion && estudiante.delegacion.nombre) {
                document.getElementById('verDelegacion').textContent = estudiante.delegacion.nombre;
            } else {
                document.getElementById('verDelegacion').textContent = 'No asignada';
            }
            
            document.getElementById('verModalidad').textContent = estudiante.modalidad || 'No definida';
            
            // Verificar si hay información de inscripción
            if (estudiante.inscripciones && estudiante.inscripciones.length > 0) {
                const inscripcion = estudiante.inscripciones[0];
                if (inscripcion.detalles && inscripcion.detalles.length > 0) {
                    const detalle = inscripcion.detalles[0];
                    
                    // Actualizar modalidad desde la inscripción si está disponible
                    if (detalle.modalidadInscripcion) {
                        document.getElementById('verModalidad').textContent = detalle.modalidadInscripcion;
                    }
                }
            }
            
            // Abrir el modal
            abrirModal('modalVerEstudiante');
        } else {
            alert('Error al cargar los detalles del estudiante');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar los detalles del estudiante');
    });
}

// Función para cargar datos en el modal de edición
function editarEstudiante(id) {
    fetch(`/estudiantes/ver/${id}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const estudiante = data.estudiante;
            
            // Llenar el formulario con los datos actuales
            if (estudiante.area) {
                document.getElementById('editArea').value = estudiante.area.id;
            }
            if (estudiante.categoria) {
                document.getElementById('editCategoria').value = estudiante.categoria.id;
            }
            if (estudiante.modalidad) {
                document.getElementById('editModalidad').value = estudiante.modalidad;
            }
            
            // Buscar la inscripción del estudiante si existe
            // Esto es necesario para cargar correctamente los datos de inscripción
            if (estudiante.inscripciones && estudiante.inscripciones.length > 0) {
                const inscripcion = estudiante.inscripciones[0];
                if (inscripcion.detalles && inscripcion.detalles.length > 0) {
                    const detalle = inscripcion.detalles[0];
                    
                    // Actualizar los campos de inscripción
                    if (detalle.idArea) {
                        document.getElementById('editArea').value = detalle.idArea;
                    }
                    if (detalle.idCategoria) {
                        document.getElementById('editCategoria').value = detalle.idCategoria;
                    }
                    if (detalle.modalidadInscripcion) {
                        document.getElementById('editModalidad').value = detalle.modalidadInscripcion;
                    }
                }
            }
            
            // Guardar el ID del estudiante para el envío del formulario
            document.getElementById('editEstudianteId').value = estudiante.id;
            
            // Abrir el modal
            abrirModal('modalEditarEstudiante');
        } else {
            alert('Error al cargar los datos del estudiante');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar los datos del estudiante');
    });
}

// Manejar el envío del formulario de edición
document.getElementById('formEditarEstudiante').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const estudianteId = document.getElementById('editEstudianteId').value;
    const formData = new FormData(this);
    
    fetch(`/estudiantes/update/${estudianteId}`, {
        method: 'PUT',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            area_id: formData.get('idArea'),
            categoria_id: formData.get('idCategoria'),
            modalidad: formData.get('modalidad')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            cerrarModal('modalEditarEstudiante');
            // Recargar la página para ver los cambios
            window.location.reload();
        } else {
            alert(data.message || 'Error al actualizar el estudiante');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el estudiante');
    });
});