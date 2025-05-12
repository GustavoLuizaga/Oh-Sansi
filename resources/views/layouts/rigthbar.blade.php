<!-- Right Sidebar -->
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

</div>

@push('scripts')
<script src="{{ asset('js/calendario.js') }}"></script>
@endpush

<style>
    .notificacion-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding: 0 5px;
    }

    .notificacion-header h3 {
        display: flex;
        align-items: center;
        margin: 0;
        font-size: 1.1em;
    }

    .notificacion-header h3 i {
        margin-right: 8px;
    }

    .btn-historial {
        background: none;
        border: none;
        color: #0A2A4D;
        cursor: pointer;
        padding: 8px;
        font-size: 1.1em;
        transition: color 0.3s ease;
        display: flex;
        align-items: center;
        height: 100%;
        margin-left: 10px;
    }

    .btn-historial:hover {
        color: #2980b9;
    }

    /* Estilos adicionales para mejorar el alineamiento vertical */
    .sidebar-derecho-notificacion {
        padding: 15px;
    }
</style>

<script>
    function cargarNotificaciones() {
        fetch('/notificaciones/nuevas')
            .then(response => response.json())
            .then(data => {
                const contenedor = document.getElementById('notificaciones');

                // Limpia notificaciones viejas antes de insertar
                contenedor.innerHTML = '';

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
                // Aquí puedes implementar la lógica para mostrar el historial
                console.log('Historial:', data);
            })
            .catch(error => console.error('Error al cargar el historial:', error));
    }
</script>