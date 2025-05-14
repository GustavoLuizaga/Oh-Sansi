<!-- Right Sidebar -->

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<div class="sidebar-derecho">
    <div class="sidebar-derecho-calendario">
        <h3><i class="fas fa-calendar-alt"></i> Calendario</h3>
        <div class="calendario">
            <div class="calendar-header">
                <span>Abril 2025</span>
                <div class="calendar-nav">
                    <button><i class="fas fa-chevron-left"></i></button>
                    <button><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
            <div class="calendar-days">
                <div class="day-name">Lu</div>
                <div class="day-name">Ma</div>
                <div class="day-name">Mi</div>
                <div class="day-name">Ju</div>
                <div class="day-name">Vi</div>
                <div class="day-name">Sa</div>
                <div class="day-name">Do</div>
            </div>
            <div class="calendar-dates">
                <!-- Calendar dates will be generated dynamically -->
            </div>
        </div>
    </div>

    <div class="sidebar-derecho-links">
        <h3><i class="fas fa-link"></i> Enlaces</h3>
        <ul class="lista-links">
            <li><a href="#"><i class="fas fa-external-link-alt"></i> Ministerio de Educación</a></li>
            <li><a href="#"><i class="fas fa-external-link-alt"></i> Contactos</a></li>
            <li><a href="#"><i class="fas fa-external-link-alt"></i> Preguntas Frecuentes</a></li>
            <li><a href="#"><i class="fas fa-external-link-alt"></i> Convocatoria</a></li>
        </ul>
    </div>

    <div class="sidebar-derecho-notificacion">
        <div class="notificacion-header">
            <h3><i class="fas fa-bell"></i> Notificaciones</h3>
            <button onclick="verHistorial()" class="btn-historial" title="Ver historial">
                <i class="fas fa-history"></i>
            </button>
        </div>
        <div class="lista-notificacion" id="notificaciones">
            <!-- Aquí se insertarán dinámicamente desde JS -->
        </div>
    </div>

    <div id="modalHistorial" class="modal-notificaciones" style="display:none;">
        <div class="modal-contenido">
            <span class="cerrar-modal" onclick="cerrarModalHistorial()">&times;</span>
            <h2>Historial de Notificaciones</h2>
            <div id="historialNotificaciones"></div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/calendario.js') }}"></script>
    @endpush

    <script>
        function cargarNotificaciones() {
            fetch('/notificaciones/nuevas')
                .then(response => response.json())
                .then(data => {
                    const contenedor = document.getElementById('notificaciones');
                    contenedor.innerHTML = '';

                    if (!data || data.length === 0) {
                        const mensajeVacio = `
                    <div class="notificacion-vacia">
                        <i class="fas fa-bell-slash"></i>
                        <p>No tienes notificaciones nuevas</p>
                    </div>
                `;
                        contenedor.innerHTML = mensajeVacio;
                    } else {
                        data.forEach(notificacion => {
                            const nuevaNotificacion = `
                        <div class="notificacion">
                            <div class="notificacion-icono"><i class="fas fa-info-circle"></i></div>
                            <div class="notificacion-contenido">
                                <p>${notificacion.mensaje}</p>
                                <span class="notificacion-tiempo">${notificacion.tiempo}</span>
                            </div>
                        </div>
                    `;
                            contenedor.insertAdjacentHTML('beforeend', nuevaNotificacion);
                        });
                    }
                })
                .catch(error => console.error('Error al cargar notificaciones:', error));
        }

        // Llamar al cargar la página
        cargarNotificaciones();

        // Repetir cada 10 segundos
        setInterval(cargarNotificaciones, 10000);

        function verHistorial() {
            fetch('/notificaciones/todas')
                .then(response => response.json())
                .then(data => {
                    const contenedor = document.getElementById('historialNotificaciones');
                    contenedor.innerHTML = '';

                    if (!data || data.length === 0) {
                        contenedor.innerHTML = `
                    <div class="notificacion-vacia">
                        <i class="fas fa-bell-slash"></i>
                        <p>No tienes historial de notificaciones</p>
                    </div>
                `;
                    } else {
                        data.forEach(notificacion => {
                            // Verificar que el ID existe y convertirlo a string
                            const notificacionId = notificacion.id || notificacion.idNotificacion;

                            if (!notificacionId) {
                                console.error('Notificación sin ID:', notificacion);
                                return;
                            }

                            const nuevaNotificacion = `
                        <div class="notificacion" style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center;">
                                <div class="notificacion-icono"><i class="fas fa-info-circle"></i></div>
                                <div class="notificacion-contenido">
                                    <p>${notificacion.mensaje}</p>
                                    <span class="notificacion-tiempo">${notificacion.tiempo}</span>
                                </div>
                            </div>
                            <button class="btn-borrar-notificacion" 
                                title="Eliminar" 
                                data-id="${notificacionId}"
                                onclick="borrarNotificacion('${notificacionId}')">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    `;
                            contenedor.insertAdjacentHTML('beforeend', nuevaNotificacion);
                        });
                    }
                    document.getElementById('modalHistorial').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error al cargar el historial:', error);
                    alert('Error al cargar el historial de notificaciones');
                });
        }

        function cerrarModalHistorial() {
            document.getElementById('modalHistorial').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera del contenido
        window.onclick = function(event) {
            const modal = document.getElementById('modalHistorial');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        function borrarNotificacion(id) {
            if (confirm('¿Seguro que deseas eliminar esta notificación?')) {
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(`/notificaciones/borrar/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Recargar solo si la eliminación fue exitosa
                            verHistorial();
                            cargarNotificaciones(); // También actualizar las notificaciones nuevas
                        } else {
                            alert(data.message || 'No se pudo eliminar la notificación');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al eliminar la notificación');
                    });
            }
        }
    </script>