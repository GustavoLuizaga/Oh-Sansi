<div id="modalFAQ" class="modal-faq">
    <div class="modal-faq-content">
        <div class="faq-header">
            <span class="faq-title">
                <i class="fas fa-robot"></i> Asistente Virtual FAQ
            </span>
            <button class="cerrar-modal-faq" onclick="cerrarModalFAQ()" title="Cerrar">&times;</button>
        </div>
        <div class="faq-chat-container">
            <div class="chat-messages custom-scroll" id="chatMessagesFAQ">
                <div class="message bot-message">
                    ¡Hola! Soy el asistente virtual de Ohi Sansi. ¿En qué puedo ayudarte hoy?
                </div>
            </div>
            <div class="quick-questions">
                <div class="quick-options">
                    <div class="quick-option" onclick="askQuestionFAQ('¿Cómo me inscribo en un área de competencia?')">¿Cómo me inscribo en un área?</div>
                    <div class="quick-option" onclick="askQuestionFAQ('¿Cuándo se publica la convocatoria?')">¿Cuándo se publica las convocatorias?</div>
                    <div class="quick-option" onclick="askQuestionFAQ('¿Cómo inscribir a varios estudiantes a la vez?')">Inscripción múltiple</div>
                    <div class="quick-option" onclick="askQuestionFAQ('¿Convocatorias activas?')">¿Cuales son las convocatorias activas?</div>
                    <div class="quick-option" onclick="askQuestionFAQ('¿Cómo me inscribo?')">Soy nuevo</div>
                </div>
            </div>
            <div class="chat-input-container">
                <input type="text" id="userInputFAQ" placeholder="Escribe tu pregunta aquí..." autocomplete="off">
                <button id="sendButtonFAQ" title="Enviar"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de contactos -->
<div id="modalContactos" class="modal-contactos" style="display:none;">
    <div class="modal-contactos-content">

        <div class="contactos-header">
            <span class="contactos-titulo">
                <i class="fas fa-address-book"></i> Contactos Clave
            </span>
            <button class="cerrar-modal-contactos" onclick="cerrarModalContactos()">&times;</button>
        </div>

        <!-- Administrador -->
        <div class="contacto-item">
            <div class="contacto-titulo">
                <i class="fas fa-user-shield"></i> Administrador
            </div>
            <div class="contacto-dato">
                <i class="fas fa-envelope"></i> admin@onisansi.org
            </div>
            <div class="contacto-dato">
                <i class="fas fa-phone"></i> +591 77910962
            </div>
        </div>

        <!-- Responsable de Inscripciones -->
        <div class="contacto-item">
            <div class="contacto-titulo">
                <i class="fas fa-user-edit"></i> Responsable de Inscripciones
            </div>
            <div class="contacto-dato">
                <i class="fas fa-envelope"></i> inscripciones@onisansi.org
            </div>
            <div class="contacto-dato">
                <i class="fas fa-phone"></i> +591 67508670
            </div>
            <div class="contacto-dato">
                <i class="fas fa-clock"></i> Atención: Lunes a Viernes 8:00 - 16:00
            </div>
        </div>

        <!-- Departamento de Cajas -->
        <div class="contacto-item">
            <div class="contacto-titulo">
                <i class="fas fa-cash-register"></i> Departamento de Cajas
            </div>
            <div class="contacto-dato">
                <i class="fas fa-envelope"></i> cajas@onisansi.org
            </div>
            <div class="contacto-dato">
                <i class="fas fa-phone"></i> +591 63887464
            </div>
            <div class="contacto-dato">
                <i class="fas fa-map-marker-alt"></i> Oficina 12, Edificio Principal-Vicerectorado
            </div>
        </div>

    </div>
</div>
<script>
    const URL_CONVOCATORIAS = "{{ url('/convocatorias') }}";
    const URL_DELEGACIONES = "{{ url('/delegaciones') }}";
    const URL_WELCOME = "{{ url('/') }}";

</script>
