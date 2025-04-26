@push('styles')
    <link rel="stylesheet" href="{{ asset('css/inscripcion/inscripcionTutor.css') }}">
@endpush

<x-app-layout>

    <div class="tutor-container">
        <!-- Top Section: Token and Excel Upload -->
        <div class="top-section">
            <!-- Token Card -->
            <div class="card token-card">
                <h2><i class="fas fa-key"></i> Token de Inscripción</h2>
                <div class="token-display">
                    <input type="text" 
                           id="tokenInput" 
                           value="{{ $token ?? 'No hay token disponible' }}" 
                           readonly>
                    <button onclick="copyToken()" class="copy-button" {{ !$token ? 'disabled' : '' }}>
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>

            <!-- Excel Upload Card -->
            <div class="card excel-card">
                <h2><i class="fas fa-file-excel"></i> Inscripción Masiva</h2>
                <div class="excel-actions">
                    <input type="file" id="excelFile" accept=".xlsx, .xls" class="file-input">
                    <label for="excelFile" class="upload-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Seleccionar archivo</span>
                    </label>
                    <button class="upload-button">
                        <i class="fas fa-upload"></i> Subir
                    </button>
                    <a href="#" class="template-link">
                        <i class="fas fa-download"></i> Descargar plantilla
                    </a>
                </div>
            </div>
        </div>

        <!-- Bottom Section: Registration Form -->
        <div class="card form-card">
            <h2><i class="fas fa-user-plus"></i> Registro Manual de Estudiante</h2>
            <form class="registration-form">
                <div class="form-grid">
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3>Información Personal</h3>
                        <div class="input-group">
                            <label>Nombres</label>
                            <input type="text" name="nombres" required>
                        </div>
                        <div class="input-row">
                            <div class="input-group">
                                <label>Apellido Paterno</label>
                                <input type="text" name="apellidoPaterno" required>
                            </div>
                            <div class="input-group">
                                <label>Apellido Materno</label>
                                <input type="text" name="apellidoMaterno" required>
                            </div>
                        </div>
                        <div class="input-row">
                            <div class="input-group">
                                <label>CI</label>
                                <input type="text" name="ci" required>
                            </div>
                            <div class="input-group">
                                <label>Fecha de Nacimiento</label>
                                <input type="date" name="fechaNacimiento" required>
                            </div>
                        </div>
                    </div>

                    <!-- Contact and Academic Information -->
                    <div class="form-section">
                        <h3>Información de Contacto</h3>
                        <div class="input-row">
                            <div class="input-group">
                                <label>Email</label>
                                <input type="email" name="email" required>
                            </div>
                            <div class="input-group">
                                <label>Teléfono</label>
                                <input type="tel" name="telefono" required>
                            </div>
                        </div>

                        <h3 class="mt-4">Información Académica</h3>
                        <div class="input-row">
                            <div class="input-group">
                                <label>Área</label>
                                <select name="area" required>
                                    <option value="">Seleccione un área</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label>Categoría</label>
                                <select name="categoria" required>
                                    <option value="">Seleccione una categoría</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-button">
                        <i class="fas fa-save"></i> Registrar Estudiante
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<script>
function copyToken() {
    var tokenInput = document.getElementById('tokenInput');
    tokenInput.select();
    document.execCommand('copy');
    
    var copyButton = document.querySelector('.copy-button');
    var originalContent = copyButton.innerHTML;
    copyButton.innerHTML = '<i class="fas fa-check"></i>';
    
    setTimeout(function() {
        copyButton.innerHTML = originalContent;
    }, 2000);
}
</script>