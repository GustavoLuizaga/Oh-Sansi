<div class="card form-card">
    <h2><i class="fas fa-user-plus"></i> Registro Manual de Estudiante</h2>
    
    <!-- Selector de tipo de estudiante -->
    <div class="student-type-selector">
        <div class="selector-options">
            <label class="option">
                <input type="radio" name="studentType" value="existing" checked>
                <span>Estudiante Existente</span>
            </label>
            <label class="option">
                <input type="radio" name="studentType" value="new">
                <span>Nuevo Estudiante</span>
            </label>
        </div>
    </div>

    <!-- Buscador de CI (solo visible para estudiante existente) -->
    <div class="ci-search-container" id="ciSearchContainer">
        <div class="input-group">
            <div class="search-input">
                <input type="text" id="searchCI" placeholder="Ingrese CI del estudiante">
                <button type="button" id="searchButton">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </div>
        </div>
        <div id="searchResult" class="search-result"></div>
    </div>

    <form class="registration-form" method="POST" action="{{ route('inscripcion.estudiante.manual.store') }}">
        @csrf
        <div class="form-grid">
            <!-- Información del Usuario -->
            <div class="form-section user-info">
                <h3><i class="fas fa-user"></i> Información del Usuario</h3>
                <div class="input-row">
                    <div class="input-group">
                        <label>Nombres</label>
                        <input type="text" name="nombres" required>
                    </div>
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
                <div class="input-row">
                    <div class="input-group">
                        <label>Género</label>
                        <select name="genero" required>
                            <option value="">Seleccione un Género</option>
                            <option value="F">F</option>
                            <option value="M">M</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                </div>
            </div>

            <!-- Información Académica -->
            <div class="form-section academic-info">
                <h3><i class="fas fa-graduation-cap"></i> Información Académica</h3>
                
                

                <!-- Contenedor dinámico para áreas -->
                <div id="areasContainer">
                    <!-- Primera área (siempre visible) -->
                    <div class="area-section">
                        <div class="area-header">
                            <h4>Área de Participación 1</h4>
                            <button type="button" class="btn-remove-area" style="display: none;">
                                <i class="fas fa-minus-circle"></i> Quitar área
                            </button>
                        </div>
                        <div class="input-row">
                            <div class="input-group">
                                <label>Área</label>
                                <select name="areas[0][area]" class="area-select" required>
                                    <option value="">Seleccione un área</option>
                                    @foreach($areas as $area)
                                    <option value="{{ $area->idArea }}">{{ $area->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group">
                                <label>Categoría</label>
                                <select name="areas[0][categoria]" class="categoria-select" required>
                                    <option value="">Seleccione una categoría</option>
                                </select>
                            </div>
                        </div>
                        <div class="input-row">
                            <div class="input-group">
                                <label>Modalidad de participación</label>
                                <select name="areas[0][modalidad]" class="modalidad-select" required>
                                    <option value="individual">Individual</option>
                                    <option value="duo">Dúo</option>
                                    <option value="equipo">Equipo</option>
                                </select>
                            </div>
                            <div class="input-group grupo-container" style="display: none;">
                                <label>Grupo</label>
                                <select name="areas[0][grupo]" class="grupo-select">
                                    <option value="">Seleccione un grupo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botón para agregar más áreas -->
                <div class="add-area-container">
                    <button type="button" id="addAreaBtn" class="btn-add-area">
                        <i class="fas fa-plus-circle"></i> Agregar otra área
                    </button>
                </div>
                <!-- Grado (Común para todas las áreas) -->
                <div class="input-group grado-container">
                    <label>Grado</label>
                    <select name="grado" id="gradoSelect" required>
                        <option value="">Seleccione un grado</option>
                    </select>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="form-section additional-info">
                <h3><i class="fas fa-info-circle"></i> Información Adicional</h3>
                <div class="input-row">
                    <div class="input-group">
                        <label>Nombre Completo del Tutor</label>
                        <input type="text" name="nombreCompletoTutor" required>
                    </div>
                    <div class="input-group">
                        <label>Correo del Tutor</label>
                        <input type="email" name="correoTutor" required>
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