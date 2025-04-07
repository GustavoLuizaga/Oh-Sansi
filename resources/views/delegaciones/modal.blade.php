<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar Colegio</h5>
                <button type="button" class="btn-close" id="closeModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de eliminar el colegio: <strong><span id="colegio-nombre"></span></strong>?</p>
                <p class="text-muted">Esta operación no se puede revertir.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancelar" id="cancelDelete">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn-confirmar" id="confirmDelete">
                    <i class="fas fa-trash"></i> Sí, eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('deleteModal');
        const closeBtn = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelDelete');
        const confirmBtn = document.getElementById('confirmDelete');
        const colegioNombre = document.getElementById('colegio-nombre');
        let colegioId = '';

        // Función para abrir el modal
        function openModal(id, nombre) {
            colegioId = id;
            colegioNombre.textContent = nombre;
            modal.classList.add('show');
            document.body.classList.add('modal-open');
        }

        // Función para cerrar el modal
        function closeModal() {
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
        }

        // Event listeners para los botones de eliminar
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');
                openModal(id, nombre);
            });
        });

        // Event listeners para cerrar el modal
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // Event listener para confirmar eliminación
        confirmBtn.addEventListener('click', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/delegaciones/${colegioId}/eliminar`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                closeModal();
                if (data.success) {
                    window.location.href = "{{ route('delegaciones') }}?deleted=true";
                } else {
                    alert('No se pudo eliminar el colegio.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un error al eliminar el colegio.');
            });
        });

        // Cerrar modal al hacer clic fuera
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    });
</script>