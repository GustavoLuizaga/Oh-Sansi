<!-- FOOTER HTML -->
<footer>
  <div class="contenedor footer-content">
    <div class="footer-logo">
      <div class="logo">OH! <span>SANSI</span></div>
      <p>Impulsando el talento científico y tecnológico en la nueva generación de estudiantes bolivianos.</p>
    </div>
    
    <div class="footer-links">
      <div class="footer-column">
        <h3 class="footer-toggle">Olimpiadas <i class="fas fa-chevron-down"></i></h3>
        <ul class="footer-collapse">
          <li><a href="#" onclick="abrirFooterModal('footerCategorias'); return false;">Categorías</a></li>
          <li><a href="#" onclick="abrirFooterModal('footerCalendario'); return false;">Calendario</a></li>
          <li><a href="#" onclick="abrirFooterModal('footerPremios'); return false;">Premios</a></li>
          <li><a href="#" onclick="abrirFooterModal('footerResultados'); return false;">Resultados</a></li>
        </ul>
      </div>
      
      <div class="footer-column">
        <h3 class="footer-toggle">Recursos <i class="fas fa-chevron-down"></i></h3>
        <ul class="footer-collapse">
          <li><a href="https://red.minedu.gob.bo/textosAprendizajeSecundaria" target="_blank" rel="noopener noreferrer">Material de Estudio</a></li>
        <li>
            <a href="#" onclick="abrirFooterModal('footerGuiaParticipante'); return false;">Guía del Participante</a>
        </li>

        <li>
            <a href="#" onclick="abrirModalFAQ(); return false;">Preguntas Frecuentes</a>
        </li>
        </ul>
        
      </div>

      <div class="footer-column">
        <h3 class="footer-toggle">Contacto <i class="fas fa-chevron-down"></i></h3>
        <ul class="footer-collapse contact-info">
            <li>
            <i class="fas fa-map-marker-alt"></i>
            <a href="https://maps.app.goo.gl/AP1AkVXmFFBsiW3f6" target="_blank" rel="noopener noreferrer">
                Campus UMSS, Cochabamba
            </a>
            </li>
            <li>
            <i class="fas fa-phone"></i>
            <a href="https://wa.me/591444525252" target="_blank">+591 4 4525252</a>
            </li>
            <li>
                <i class="fas fa-envelope"></i>
                <a href="mailto:olimpiadas@umss.edu">olimpiadas@umss.edu</a>
            </li>
        </ul>

        <div class="social-media">
        <a href="http://www.facebook.com/sharer.php?u=https%3A%2F%2Fwww.umss.edu.bo&t=Universidad%20Mayor%20de%20San%20Sim%C3%B3n" target="_blank" aria-label="Facebook">
             <i class="fab fa-facebook-f"></i>
        </a>

        <a href="http://twitter.com/share?text=Universidad%20Mayor%20de%20San%20Sim%C3%B3n&url=https%3A%2F%2Fwww.umss.edu.bo" target="_blank" aria-label="Twitter">
            <i class="fab fa-twitter"></i>
        </a>
          <a href="https://www.instagram.com/umss/" target="_blank" aria-label="Instagram">
             <i class="fab fa-instagram"></i>
        </a>

        <a href="http://www.linkedin.com/shareArticle?mini=true&url=https%3A%2F%2Fwww.umss.edu.bo&title=Universidad%20Mayor%20de%20San%20Sim%C3%B3n" target="_blank" aria-label="LinkedIn">
            <i class="fab fa-linkedin-in"></i>
        </a>

        <a href="https://t.me/share/url?url=https://www.umss.edu.bo/" target="_blank" aria-label="Telegram">
            <i class="fab fa-telegram"></i>
        </a>

        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="contenedor">
      <p>&copy; 2025 Olimpiadas Oh! SanSi - Universidad Mayor de San Simón</p>
    </div>
  </div>

<!-- MODAL CATEGORÍAS -->
<div id="footerCategorias" class="footer-modal-bg" style="display:none;">
    <div class="footer-modal-content">
        <div class="footer-modal-header">
        <span>Categorías activas</span>
        <button onclick="cerrarFooterModal('footerCategorias')" class="footer-modal-close">&times;</button>
        </div>
        <div class="footer-modal-body">
        @if($hayAreasDisponibles)
            <section class="about-olympiad">
            <h2>¿Áreas de competición?</h2>
            <div class="areas-container">
                <div class="areas-grid">
                @foreach($areas as $area)
                    <div class="area-card" 
                        style="cursor:pointer;" 
                        data-url="{{ route('convocatoria.redirigirPorArea', $area->idArea) }}" 
                        onclick="window.location = this.dataset.url">
                    <div class="area-icon">
                        <i class="{{ $area->icono }}"></i>
                    </div>
                    <h3>{{ $area->nombre }}</h3>
                    </div>
                @endforeach
                </div>
            </div>
            </section>
        @else
            <p>No hay áreas disponibles actualmente.</p>
        @endif
        </div>
    </div>
</div>

  <!-- MODAL CONVOCATORIAS -->
  <div id="footerCalendario" class="footer-modal-bg" style="display:none;">
    <div class="footer-modal-content">
      <div class="footer-modal-header">
        <span>Convocatorias y Áreas</span>
        <button onclick="cerrarFooterModal('footerCalendario')" class="footer-modal-close">&times;</button>
      </div>
      <div class="footer-modal-body">
        @if(isset($convocatorias) && $convocatorias->count())
          <section class="convocatorias-footer">
            <h2>Convocatorias Activas</h2>
            <div class="convocatorias-container">
              @foreach($convocatorias as $convocatoria)
                <div class="convocatoria-card">
                  <b>{{ $convocatoria->nombre }}</b>
                  <div>
                    <i class="fas fa-calendar-alt"></i>
                    {{ $convocatoria->fechaInicio }} &rarr; {{ $convocatoria->fechaFin }}
                  </div>
                </div>
              @endforeach
            </div>
          </section>
        @else
          <p>No hay convocatorias activas.</p>
        @endif
      </div>
    </div>
  </div>

  <!-- MODAL PREMIOS -->
  <div id="footerPremios" class="footer-modal-bg" style="display:none;">
    <div class="footer-modal-content">
      <div class="footer-modal-header">
        <span>Premios</span>
        <button onclick="cerrarFooterModal('footerPremios')" class="footer-modal-close">&times;</button>
      </div>
      <div class="footer-modal-body">
        <ul>
          <li>Medallas de oro, plata y bronce</li>
          <li>Certificados de participación y excelencia</li>
          <li>Becas y menciones especiales</li>
          <li>Reconocimientos institucionales</li>
        </ul>
      </div>
    </div>
  </div>

  <!-- MODAL RESULTADOS -->
  <div id="footerResultados" class="footer-modal-bg" style="display:none;">
    <div class="footer-modal-content">
      <div class="footer-modal-header">
        <span>Resultados</span>
        <button onclick="cerrarFooterModal('footerResultados')" class="footer-modal-close">&times;</button>
      </div>
      <div class="footer-modal-body">
        <p>Consulta los resultados de convocatorias anteriores y conoce a los ganadores destacados.</p>
      </div>
    </div>
  </div>

<!-- MODAL GUÍA DEL PARTICIPANTE -->
<div id="footerGuiaParticipante" class="footer-modal-bg" style="display:none;">
    <div class="footer-modal-content">
        <div class="footer-modal-header">
            <span>Guía del Participante</span>
            <button onclick="cerrarFooterModal('footerGuiaParticipante')" class="footer-modal-close">&times;</button>
        </div>
        <div class="footer-modal-body">
            <section class="how-to-participate">
                <h2>¿Cómo participar?</h2>
                <ol class="participation-steps">
                    <li class="step-item" data-step="1">
                        <i class="fas fa-user-graduate fa-3x"></i>
                        <p>Registrarse como estudiante.</p>
                    </li>
                    <li class="step-item" data-step="2">
                        <i class="fas fa-file-alt fa-3x"></i>
                        <p>Completar el formulario.</p>
                    </li>
                    <li class="step-item" data-step="3">
                        <i class="fas fa-money-bill-wave fa-3x"></i>
                        <p>Realizar el pago en la Caja FCYT.</p>
                    </li>
                    <li class="step-item" data-step="4">
                        <i class="fas fa-upload fa-3x"></i>
                        <p>Subir el comprobante.</p>
                    </li>
                    <li class="step-item" data-step="5">
                        <i class="fas fa-check-circle fa-3x"></i>
                        <p>Recibir la confirmación.</p>
                    </li>
                </ol>
                <!-- @if (Route::has('register'))
                <a href="{{ route('register') }}" class="btn btn-primary">Registrarse</a>
                @endif   -->
            </section>
        </div>
    </div>
</div>
<!-- Este es para reutilizar el faq que creamos para las preguntas frecuentes en rigth-bar -->
@include('components.faq-chat')
</footer>
