<!-- Modal de confirmación para eliminar colegio -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar eliminación</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar el colegio <span id="colegio-nombre"></span>? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancelar" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-confirmar" id="confirmDelete">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener todos los botones de eliminar
        const deleteButtons = document.querySelectorAll('.delete-button');
        let colegioId = '';
        
        // Verificar si hay botones
        if (deleteButtons.length > 0) {
            // Agregar evento click a cada botón de eliminar
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Obtener el ID y nombre del colegio
                    colegioId = this.getAttribute('data-id');
                    const colegioNombre = this.getAttribute('data-nombre');
                    
                    // Actualizar el modal con la información del colegio
                    document.getElementById('colegio-nombre').textContent = colegioNombre;
                    
                    // Mostrar el modal usando jQuery
                    $('#deleteModal').modal('show');
                });
            });
        }
        
        // Manejar el evento de confirmación de eliminación
        document.getElementById('confirmDelete').addEventListener('click', function() {
            // Crear el token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Realizar la solicitud AJAX para eliminar
            fetch("{{ url('delegaciones') }}/" + colegioId, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Ocultar el modal
                $('#deleteModal').modal('hide');
                
                if (data.success) {
                    // Redireccionar a la página de delegaciones
                    window.location.href = "{{ route('delegaciones') }}?deleted=true";
                } else {
                    alert('Hubo un error al eliminar el colegio. Por favor, inténtelo de nuevo.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al eliminar el colegio. Por favor, inténtelo de nuevo.');
            });
        });
    });
</script>