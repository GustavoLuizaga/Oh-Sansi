<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/inscripcion/inscripcionEstudiante.css') }}">
    <!-- Modal for No Active Convocatoria -->
    <div id="noConvocatoriaModal" class="modal-overlay">
        <div class="modal-content">
            <i class="fas fa-exclamation-circle modal-icon"></i>
            <h3 class="modal-title">Convocatoria No Disponible</h3>
            <p class="modal-message">No hay convocatoria publicada en este momento. Por favor, intente más tarde.</p>
            <a href="{{ route('dashboard') }}" class="modal-button">
                Volver al Dashboard
            </a>
        </div>
    </div>
            <!-- Form Container -->
            <div class="form-container">
                <h2>Formulario de Inscripción</h2>

                <form id="inscriptionForm" method="POST" action="{{ route('inscripcion.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Academic Information -->
                    <div class="form-section">
                        <h3><i class="fas fa-book"></i> Información Académica</h3>
                        <div class="areas-container">
                            <div class="area-group" id="area-group-1">
                                <div class="form-row">
                                    <div class="input-group">
                                        <label for="colegio"><i class="fas fa-building"></i> Colegio</label>
                                        <div class="input-with-icon">
                                            <select id="colegio" name="colegio" class="select2-colegio" required>
                                                <option value="">Buscar colegio...</option>
                                                @foreach($colegios as $colegio)
                                                <option value="{{ $colegio->id }}" {{ old('colegio') == $colegio->id ? 'selected' : '' }}>
                                                    {{ $colegio->nombre }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('colegio')
                                            <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="input-group area-selection">
                                        <label for="area-1"><i class="fas fa-book"></i> Área</label>
                                        <div class="input-with-icon">
                                            <select id="area-1" name="areas[]" class="area-select" required>
                                                <option value="">Seleccionar Área</option>
                                                @foreach($areas as $area)
                                                <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                                                @endforeach
                                            </select>
                                            @error('areas.*')
                                            <span class="error-message">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <button type="button" class="btn-add-area" title="Agregar otra área">
                                            <i class="fas fa-plus-circle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="form-section">
                        <h3><i class="fas fa-address-card"></i> Información de Contacto</h3>
                        <div class="form-row">
                            <div class="input-group">
                                <label for="celular"><i class="fas fa-phone"></i> Celular</label>
                                <div class="input-with-icon">
                                    <input type="tel" id="celular" name="celular" value="{{ old('celular') }}"
                                        placeholder="70707070" maxlength="8" required>
                                    @error('celular')
                                    <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="input-group">
                                <label for="direccion"><i class="fas fa-map-marker-alt"></i> Dirección</label>
                                <div class="input-with-icon">
                                    <input type="text" id="direccion" name="direccion" value="{{ old('direccion') }}"
                                        placeholder="Mínimo 10 caracteres" minlength="10" required>
                                    @error('direccion')
                                    <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tutor Information -->
                    <div class="form-section" id="tutorSection">
                        <h3><i class="fas fa-users"></i> Información del Tutor</h3>
                        <div class="tutor-container">
                            <div class="form-row">
                                <div class="input-group">
                                    <label for="tokenTutor"><i class="fas fa-key"></i> Token del Tutor</label>
                                    <div class="input-with-icon">
                                        <input type="text" id="tokenTutor" name="token_tutor[]"
                                            placeholder="Ingrese el token del tutor" required>
                                        @error('token_tutor.*')
                                        <span class="error-message">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="addTutor" class="btn-add-tutor">
                            <i class="fas fa-plus"></i> Agregar otro tutor
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="button-group">
                        <a href="{{ route('dashboard') }}" class="btn-cancel">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-check"></i> Terminar Inscripción
                        </button>
                    </div>
                </form>
            </div>

    <x-slot name="scripts">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{ asset('js/inscripcion_Estudiante.js') }}"></script>
        <script>
            const idConvocatoria = JSON.parse(`{!! json_encode($idConvocatoria) !!}`);
            if (!idConvocatoria || typeof idConvocatoria === 'string') {
                document.getElementById('noConvocatoriaModal').style.display = 'flex';
                document.querySelector('.container').style.display = 'none';
            }
        </script>
    </x-slot>
</x-app-layout>