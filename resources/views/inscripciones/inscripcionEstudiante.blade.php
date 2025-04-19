<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/inscripcion/inscripcionEstudiante.css') }}">

    <!-- Modal for No Active Convocatoria -->
    @if(!$convocatoriaActiva)
    <div id="noConvocatoriaModal" class="modal-overlay" style="display: flex;">
        <div class="modal-content">
            <i class="fas fa-exclamation-circle modal-icon"></i>
            <h3 class="modal-title">Convocatoria No Disponible</h3>
            <p class="modal-message">No hay convocatoria publicada en este momento. Por favor, intente más tarde.</p>
            <a href="{{ route('dashboard') }}" class="modal-button">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
        </div>
    </div>
    @endif

    <div class="inscription-container">
        <!-- Header -->
        <div class="inscription-header">
            <h1><i class="fas fa-user-plus"></i> Inscripción de Postulante</h1>
            @if($convocatoriaActiva)
            <p class="convocatoria-info">Convocatoria: <span>{{ $convocatoria->nombre }}</span></p>
            @endif
        </div>

        <!-- Main Form -->
        <form id="inscriptionForm" method="POST" action="{{ route('inscripcion.store') }}" class="inscription-form" onsubmit="return validateForm(event)">
            @csrf
            @if($convocatoriaActiva)
            <input type="hidden" name="idConvocatoria" value="{{ $convocatoria->idConvocatoria }}">
            @endif

            <!-- Instrucciones del Formulario -->
            <div class="form-instructions">
                <h2>Complete todos los campos del formulario</h2>
            </div>

            <div class="form-content">
                <!-- Información Personal -->
                <div class="formulario-seccion" id="personal-info">
                    <div class="seccion-card">
                        <div class="seccion-header">
                            <h2><i class="fas fa-user"></i> Información Personal</h2>
                        </div>
                        <div class="seccion-body">
                            <div class="info-group">
                                <label>Nombre Completo</label>
                                <div class="info-value">
                                    {{ auth()->user()->name }} {{ auth()->user()->apellidoPaterno }} {{ auth()->user()->apellidoMaterno }}
                                </div>
                            </div>
                            <!--      <div class="info-row">
                                <div class="info-group">
                                    <label>Apellido Paterno</label>
                                    <div class="info-value">{{ auth()->user()->apellidoPaterno }}</div>
                                </div>
                                <div class="info-group">
                                    <label>Apellido Materno</label>
                                    <div class="info-value">{{ auth()->user()->apellidoMaterno }}</div>
                                </div>
                            </div>-->
                            <div class="info-row">
                                <div class="info-group">
                                    <label>Cedula de identidad</label>
                                    <div class="info-value">{{ auth()->user()->ci }}</div>
                                </div>
                                <div class="info-group">
                                    <label>Fecha de Nacimiento</label>
                                    <div class="info-value">{{ auth()->user()->fechaNacimiento }}</div>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-group">
                                    <label>Correo electronico</label>
                                    <div class="info-value">{{ auth()->user()->email }}</div>
                                </div>
                                <div class="info-group">
                                    <label>Género</label>
                                    <div class="info-value">{{ auth()->user()->genero }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de Contacto -->
                <div class="formulario-seccion" id="contact-info">
                    <div class="seccion-card">
                        <div class="seccion-header">
                            <h2><i class="fas fa-phone"></i> Información de Contacto</h2>
                        </div>
                        <div class="seccion-body">
                            <div class="input-grupo">
                                <label for="numeroContacto">Número de Contacto</label>
                                <div class="input-with-icon">
                                    <input type="tel" id="numeroContacto" name="numeroContacto" required
                                        maxlength="8" pattern="[0-9]{8}"
                                        placeholder="Ej: 63772394">
                                </div>
                                @error('numeroContacto')
                                <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información de Tutores -->
                <div class="formulario-seccion" id="tutor-info">
                    <div class="seccion-card">
                        <div class="seccion-header">
                            <h2><i class="fas fa-chalkboard-teacher"></i> Información de Tutores</h2>
                            <p class="section-subtitle">Puede agregar hasta 2 tutores</p>
                        </div>
                        <div class="seccion-body">
                            <div id="tutorContainer">
                                <div class="tutor-block">
                                    <div class="tutor-header">
                                        <h3>Tutor 1</h3>
                                    </div>
                                    <div class="input-grupo">
                                        <label>Token del Tutor</label>
                                        <div class="input-with-icon token-verification-container">
                                            <input type="text" class="tutor-token" name="tutor_tokens[]"
                                                placeholder="Ingrese el token del tutor" required>
                                            <button type="button" class="btn-verificar-token">
                                                <i class="fas fa-check-circle"></i> Verificar
                                            </button>
                                        </div>
                                        <span class="token-status"></span>
                                    </div>
                                    <div class="tutor-info" style="display: none;">
                                        <div class="info-row">
                                            <div class="info-group">
                                                <label>Área</label>
                                                <div class="info-value tutor-area"></div>
                                                <input type="hidden" class="idArea-input" name="tutor_areas[]">
                                            </div>
                                            <div class="info-group">
                                                <label>Delegación</label>
                                                <div class="info-value tutor-delegacion"></div>
                                                <input type="hidden" class="idDelegacion-input" name="tutor_delegaciones[]">
                                            </div>
                                        </div>
                                        <div class="input-row">
                                            <div class="input-grupo">
                                                <label>Categoría</label>
                                                <select class="categoria-select" name="idCategoria" required>
                                                    <option value="">Seleccione una categoría</option>
                                                </select>
                                            </div>
                                            <div class="input-grupo">
                                                <label>Grado</label>
                                                <select class="grado-select" name="idGrado" required disabled>
                                                    <option value="">Seleccione un grado</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="button" id="addTutorBtn" class="btn-add-tutor">
                                <i class="fas fa-plus"></i> Agregar otro tutor
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón de Envío -->
            <div class="subir-formulario">
                <button type="submit" class="btn-subir">
                    <i class="fas fa-check"></i> Completar Inscripción
                </button>
            </div>
        </form>
    </div>
    <script src="{{ asset('js/inscripcionEstudiante.js') }}"></script>
</x-app-layout>

<script>
function validateForm(event) {
    const tutorBlocks = document.querySelectorAll('.tutor-block');
    const validTutors = Array.from(tutorBlocks).filter(block => {
        const tokenInput = block.querySelector('.tutor-token');
        const tutorInfo = block.querySelector('.tutor-info');
        return tokenInput.value.trim() !== '' && tutorInfo.style.display !== 'none';
    });

    if (validTutors.length === 0) {
        alert('Debe tener al menos un tutor válido para continuar');
        event.preventDefault();
        return false;
    }
    return true;
}
</script>