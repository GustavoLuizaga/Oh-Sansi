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

                @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                @if(session('error_messages'))
                <div class="alert alert-danger">
                    <p>{{ session('message') }}</p>
                    <ul>
                        @foreach(session('error_messages') as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form method="POST" action="{{ route('register.lista.store') }}" enctype="multipart/form-data" class="excel-actions">
                    @csrf
                    <input type="file"
                        id="excelFile"
                        name="file"
                        accept=".xlsx, .xls"
                        class="file-input"
                        required>
                    <label for="excelFile" class="upload-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span id="fileName">Seleccionar archivo</span>
                    </label>
                    <div id="fileInfo" class="file-info" style="display: none;">
                        <i class="fas fa-file-excel"></i>
                        <span id="selectedFileName"></span>
                        <button type="button" class="remove-file" onclick="removeFile()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <button type="submit" class="upload-button">
                        <i class="fas fa-upload"></i> Subir
                    </button>
                    <a href="{{ asset('plantillasExel/plantilla_inscripcion.xlsx') }}" download class="template-link">
                        <i class="fas fa-download"></i> Descargar plantilla
                    </a>
                    <button onclick="cargarDatosConvocatoria()">
                        Ver información sobre la convocatoria
                    </button>

                </form>
                
                @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>

        <!-- Bottom Section: Registration Form -->
        <div class="card form-card">
            <h2><i class="fas fa-user-plus"></i> Registro Manual de Estudiante</h2>
            <form class="registration-form" method="POST" action="{{ route('inscripcion.estudiante.manual.store') }}">
                @csrf
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
                            <div class="input-group">
                                <label>Género</label>
                                <select name="genero" required>
                                    <option value="">Seleccione un Género</option>
                                    <option value="">F</option>
                                    <option value="">M</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label>Nombre Completo Tutor</label>
                                <input type="text" name="nombreCompletoTutor" required>
                            </div>
                            <div class="input-group">
                                <label>Correo Tutor</label>
                                <input type="text" name="correoTutor" required>
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
                                <select name="area" id="areaSelect" required>
                                    <option value="">Seleccione un área</option>
                                    @foreach($areas as $area)
                                    <option value="{{ $area->idArea }}">{{ $area->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group">
                                <label>Categoría</label>
                                <select name="categoria" id="categoriaSelect" required>
                                    <option value="">Seleccione una categoría</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label>Grado</label>
                                <select name="grado" id="gradoSelect" required>
                                    <option value="">Seleccione un grado</option>
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
        <!-- Modal -->
        <div id="modalDatos" class="modal">
            <div class="modal-contenido">
                <button onclick="cerrarModal()" class="modal-cerrar">✖</button>
                <div id="contenidoModal" class="modal-cuerpo">
                    Cargando datos...
                </div>
            </div>
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
    document.getElementById('excelFile').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const fileInfo = document.getElementById('fileInfo');
        const selectedFileName = document.getElementById('selectedFileName');
        const uploadLabel = document.getElementById('fileName');

        if (file) {
            fileInfo.style.display = 'flex';
            selectedFileName.textContent = file.name;
            uploadLabel.textContent = 'Cambiar archivo';
        } else {
            fileInfo.style.display = 'none';
            uploadLabel.textContent = 'Seleccionar archivo';
        }
    });

    function removeFile() {
        const input = document.getElementById('excelFile');
        const fileInfo = document.getElementById('fileInfo');
        const uploadLabel = document.getElementById('fileName');

        input.value = '';
        fileInfo.style.display = 'none';
        uploadLabel.textContent = 'Seleccionar archivo';
    }

    function cargarDatosConvocatoria() {
        fetch('/verDatosCovocatoria')
            .then(response => response.text())
            .then(html => {
                document.getElementById('contenidoModal').innerHTML = html;
                document.getElementById('modalDatos').style.display = 'flex';
            })
            .catch(error => {
                console.error(error);
                document.getElementById('contenidoModal').innerHTML = '<p>Error al cargar los datos.</p>';
                document.getElementById('modalDatos').style.display = 'flex';
            });
    }

    function cerrarModal() {
        document.getElementById('modalDatos').style.display = 'none';
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const areaSelect = document.getElementById('areaSelect');
        const categoriaSelect = document.getElementById('categoriaSelect');
        const gradoSelect = document.getElementById('gradoSelect');

        const idConvocatoria = "{{ $idConvocatoriaResult ?? '' }}";

        areaSelect.addEventListener('change', function() {
            const idArea = this.value;

            categoriaSelect.innerHTML = '<option value="">Seleccione una categoría</option>';
            gradoSelect.innerHTML = '<option value="">Seleccione un grado</option>';

            if (idArea) {
                fetch(`/obtener-categorias/${idConvocatoria}/${idArea}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(categoria => {
                            const option = document.createElement('option');
                            option.value = categoria.idCategoria;
                            option.textContent = categoria.nombre;
                            categoriaSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar categorías:', error);
                    });
            }
        });

        // NUEVO: Cuando el usuario cambie una CATEGORÍA
        categoriaSelect.addEventListener('change', function() {
            const idCategoria = this.value;
            gradoSelect.innerHTML = '<option value="">Seleccione un grado</option>';

            if (idCategoria) {
                fetch(`/obtener-grados/${idCategoria}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(grado => {
                            const option = document.createElement('option');
                            option.value = grado.idGrado;
                            option.textContent = grado.grado;
                            gradoSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar grados:', error);
                    });
            }
        });

    });
</script>