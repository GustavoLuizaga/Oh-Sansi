@push('styles')
    <link rel="stylesheet" href="{{ asset('css/inscripcion/FormularioDatosInscripcionEst.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/FormularioDatosInscripcionEst.js') }}"></script>
@endpush
<x-app-layout>
    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success py-1 px-2 mb-1">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Header Section -->
    <div class="estudiantes-header py-2">
        <h1><i class="fas fa-user-plus"></i> Datos de Inscripción del Postulante</h1>
    </div>

    <!-- Actions Container -->
    <div class="actions-container mb-1">
        <div class="button-group">
            <a href="{{ route('inscripcion.estudiante') }}" class="add-button py-1 px-2">
                <i class="fas fa-arrow-left"></i> Volver Formulario de Inscripción
            </a>
        </div>
        
        <div class="export-buttons">
            <button type="button" class="export-button pdf py-1 px-2" id="exportPdf">
                <i class="fas fa-file-pdf"></i> Generar orden de pago
            </button>
            
            <button type="button" class="export-button excel py-1 px-2" id="exportExcel">
                <i class="fas fa-file-excel"></i> Subir comprobante de pago
            </button>
        </div>
    </div>

        <!-- IDs ocultos para uso en JS -->
        <div id="data-ids" 
        data-estudiante-id="{{ $ids['estudiante_id'] }}"
        data-tutor-id="{{ $ids['tutor_id'] }}"
        data-inscripcion-id="{{ $ids['inscripcion_id'] }}"
        data-convocatoria-id="{{ $ids['convocatoria_id'] }}"
        data-delegacion-id="{{ $ids['delegacion_id'] }}"
        data-grado-id="{{ $ids['grado_id'] }}"
        style="display: none;">
    </div>

    <form id="inscriptionForm" method="POST" action="http://127.0.0.1:8000/inscripcion/estudiante/store" class="inscription-form" onsubmit="return validateForm(event)">
        <input type="hidden" name="_token" value="NOkvTK5h6JxZcDobnv8gIZjpQn2DZRiQ2G32a4Cr" wfd-id="id1">                        <input type="hidden" name="idConvocatoria" value="3" wfd-id="id2">
        
        <!-- Instrucciones del Formulario -->
        <div class="form-instructions">
            <h2>EN AQUI SE DEBE MODIFICAR INSCRIPCION, CAMBIAR TUTORES, AREAS, CATEGORIAS</h2>
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
                                orlandoestudainte aaa bbb
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-group">
                                <label>Cedula de identidad</label>
                                <div class="info-value">12345678</div>
                            </div>
                            <div class="info-group">
                                <label>Fecha de Nacimiento</label>
                                <div class="info-value">2008-04-11</div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-group">
                                <label>Correo electronico</label>
                                <div class="info-value">orlando123@gmail.com</div>
                            </div>
                            <div class="info-group">
                                <label>Género</label>
                                <div class="info-value">F</div>
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
                                <input type="tel" id="numeroContacto" name="numeroContacto" required="" maxlength="8" pattern="[0-9]{8}" placeholder="Ej: 63772394" wfd-id="id3">
                            </div>
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
                            <div class="tutor-block" data-used-token="LIJFniOgO3M0qMAl1GeLn3jioCMHy318uctHzpVU">
                                <div class="tutor-header">
                                    <h3>Tutor 1</h3>
                                <button type="button" class="btn-eliminar-tutor" title="Eliminar tutor"><i class="fas fa-trash"></i></button></div>
                                <div class="input-grupo">
                                    <label>Token del Tutor</label>
                                    <div class="input-with-icon token-verification-container">
                                        <input type="text" class="tutor-token" name="tutor_tokens[]" placeholder="Ingrese el token del tutor" required="" wfd-id="id4">
                                        <button type="button" class="btn-verificar-token"><i class="fas fa-check-circle"></i> Verificar</button>
                                    </div>
                                    <span class="token-status valid">Token válido</span>
                                </div>
                                <div class="tutor-info" style="display: block;">
                                    <div class="info-row">
                                        <div class="info-group">
                                            <label>Delegación</label>
                                            <div class="info-value tutor-delegacion">Unidad Educativa San Martín</div>
                                            <input type="hidden" class="idDelegacion-input" name="tutor_delegaciones[]" wfd-id="id5" value="1">
                                        </div>
                                    </div>
                                    
                                    <!-- Áreas y Categorías -->
                                    <div class="areas-container">
                                        <div class="area-block">
                                            <div class="info-row">
                                                <div class="info-group">
                                                    <label>Área</label>
                                                    <select class="area-select" name="tutor_areas_1_1" required="">
                                                        <option value="">Seleccione un área</option><option value="1">Fisica</option><option value="2">Quimica</option><option value="3">Matematicas</option><option value="4">Informatica</option><option value="5">Robotica</option><option value="6">Biologia</option><option value="7">Astronomia</option></select>
                                                    <input type="hidden" class="tutor-area-hidden" value="Fisica" wfd-id="id6">
                                                </div>
                                                <div class="input-grupo">
                                                    <label>Categoría</label>
                                                    <select class="categoria-select" name="tutor_categorias_1_1" required=""><option value="">Seleccione una categoría</option><option value="14">Jucumari</option><option value="18">Builders S</option></select>
                                                </div>
                                            <button type="button" class="btn-eliminar-area" title="Eliminar área"><i class="fas fa-trash"></i></button></div>
                                        </div>
                                        <button type="button" class="btn-add-area">
                                            <i class="fas fa-plus-circle"></i> Agregar otra área
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <div class="tutor-block" data-used-token="UYH6Cry73UFK6LOyIe8mLrC9QMJVwmD9KQtFkwxX">
                                <div class="tutor-header">
                                    <h3>Tutor 2</h3>
                                <button type="button" class="btn-eliminar-tutor" title="Eliminar tutor"><i class="fas fa-trash"></i></button></div>
                                <div class="input-grupo">
                                    <label>Token del Tutor</label>
                                    <div class="input-with-icon token-verification-container">
                                        <input type="text" class="tutor-token" name="tutor_tokens[]" placeholder="Ingrese el token del tutor" required="" wfd-id="id4">
                                        <button type="button" class="btn-verificar-token"><i class="fas fa-check-circle"></i> Verificar</button>
                                    </div>
                                    <span class="token-status valid">Token válido</span>
                                </div>
                                <div class="tutor-info" style="display: block;">
                                    <div class="info-row">
                                        <div class="info-group">
                                            <label>Delegación</label>
                                            <div class="info-value tutor-delegacion">Unidad Educativa Santa Cruz de la Sierra</div>
                                            <input type="hidden" class="idDelegacion-input" name="tutor_delegaciones[]" wfd-id="id5" value="3">
                                        </div>
                                    </div>
                                    
                                    <!-- Áreas y Categorías -->
                                    <div class="areas-container">
                                        <div class="area-block">
                                            <div class="info-row">
                                                <div class="info-group">
                                                    <label>Área</label>
                                                    <select class="area-select" name="tutor_areas_2_1" required="">
                                                        <option value="">Seleccione un área</option><option value="1">Fisica</option><option value="2">Quimica</option><option value="3">Matematicas</option><option value="4">Informatica</option><option value="5">Robotica</option><option value="6">Biologia</option><option value="7">Astronomia</option></select>
                                                    <input type="hidden" class="tutor-area-hidden" value="Fisica" wfd-id="id6">
                                                </div>
                                                <div class="input-grupo">
                                                    <label>Categoría</label>
                                                    <select class="categoria-select" name="tutor_categorias_2_1" required=""><option value="">Seleccione una categoría</option><option value="1">3p</option><option value="2">4p</option><option value="3">5P</option><option value="4">6P</option><option value="20">Lego S</option></select>
                                                </div>
                                            <button type="button" class="btn-eliminar-area" title="Eliminar área"><i class="fas fa-trash"></i></button></div>
                                        </div>
                                        <button type="button" class="btn-add-area">
                                            <i class="fas fa-plus-circle"></i> Agregar otra área
                                        </button>
                                    </div>
                                </div>
                            </div></div>

                        <button type="button" id="addTutorBtn" class="btn-add-tutor" style="display: none;">
                            <i class="fas fa-plus"></i> Agregar otro tutor
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de Grado Común -->
        <div class="formulario-seccion" id="grado-info">
            <div class="seccion-card">
                <div class="seccion-header">
                    <h2><i class="fas fa-graduation-cap"></i> Selección de Grado</h2>
                    <p class="section-subtitle">El grado se cargará automáticamente según las categorías seleccionadas</p>
                </div>
                <div class="seccion-body">
                    <div class="input-grupo">
                        <label for="idGrado">Grado</label>
                        <select id="idGrado" name="idGrado" class="grado-select-common" required=""><option value="">Seleccione un grado</option><option value="7">1ro de Secundaria</option><option value="8">2do de Secundaria</option><option value="9">3ro de Secundaria</option><option value="10">4to de Secundaria</option><option value="11">5to de Secundaria</option><option value="12">6to de Secundaria</option><option value="5">5to de Primaria</option></select>
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


</x-app-layout>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Export PDF button
        document.getElementById('exportPdf').addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = "{{ route('inscripcionEstudiante.exportar.pdf') }}";
        });
        
    });
</script>
