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
        <h3><i class="fas fa-bell"></i> Notificaciones</h3>
        <div class="lista-notificacion">
            <div class="notificacion">
                <div class="notificacion-icono"><i class="fas fa-info-circle"></i></div>
                <div class="notificacion-contenido">
                    <p>Nueva convocatoria disponible</p>
                    <span class="notificacion-tiempo">Hace 2 horas</span>
                </div>
            </div>
            <div class="notificacion">
                <div class="notificacion-icono"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="notificacion-contenido">
                    <p>Recordatorio: Fecha límite de inscripción</p>
                    <span class="notificacion-tiempo">Hace 1 día</span>
                </div>
            </div>
            <div class="notificacion">
                <div class="notificacion-icono"><i class="fas fa-check-circle"></i></div>
                <div class="notificacion-contenido">
                    <p>Registro completado exitosamente</p>
                    <span class="notificacion-tiempo">Hace 3 días</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/calendario.js') }}"></script>
@endpush