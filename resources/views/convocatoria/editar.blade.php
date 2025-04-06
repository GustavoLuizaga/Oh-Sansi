<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/convocatoria/editar.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="p-6">
        @if($convocatoria->estado == 'Cancelada')
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> Esta convocatoria ha sido cancelada y no puede ser editada.
            <a href="{{ route('convocatorias.ver', $convocatoria->idConvocatoria) }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Volver a Detalles
            </a>
        </div>
        @else
        <div class="convocatoria-form-container">
            <div class="form-header">
                <i class="fas fa-edit"></i> Editar Convocatoria
            </div>

            @if($convocatoria->estado == 'Publicada')
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Esta convocatoria está publicada. Solo puede editar la descripción y la información de contacto.
                <a href="{{ route('convocatorias.nuevaVersion', $convocatoria->idConvocatoria) }}" class="btn-nueva-version">
                    <i class="fas fa-copy"></i> Crear Nueva Versión
                </a>
            </div>
            @endif

            <form action="{{ route('convocatorias.update', $convocatoria->idConvocatoria) }}" method="POST" id="convocatoriaForm">
                @csrf
                @method('PUT')

                <!-- Información General -->
                <div class="form-section">
                    <h2 class="section-title">Información General</h2>

                    <div class="form-group">
                        <label for="nombre">Nombre de la Convocatoria</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" required minlength="5" maxlength="255"
                            value="{{ old('nombre', $convocatoria->nombre) }}"
                            {{ $convocatoria->estado == 'Publicada' ? 'readonly' : '' }}>
                        @error('nombre')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" required minlength="10" maxlength="1000">{{ old('descripcion', $convocatoria->descripcion) }}</textarea>
                        @error('descripcion')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="date-inputs">
                        <div class="form-group">
                            <label for="fechaInicio">Fecha de Inicio</label>
                            <input type="date" id="fechaInicio" name="fechaInicio" class="form-control" required
                                value="{{ old('fechaInicio', $convocatoria->fechaInicio) }}"
                                {{ $convocatoria->estado == 'Publicada' ? 'readonly' : '' }}>
                            @error('fechaInicio')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="fechaFin">Fecha de Fin</label>
                            <input type="date" id="fechaFin" name="fechaFin" class="form-control" required
                                value="{{ old('fechaFin', $convocatoria->fechaFin) }}"
                                {{ $convocatoria->estado == 'Publicada' ? 'readonly' : '' }}>
                            @error('fechaFin')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Método de Pago -->
                <div class="form-section">
                    <h2 class="section-title">Método de Pago</h2>

                    <div class="form-group">
                        <label for="metodoPago">Método de Pago</label>
                        <input type="text" id="metodoPago" name="metodoPago" class="form-control" required
                            value="{{ old('metodoPago', $convocatoria->metodoPago) }}"
                            {{ $convocatoria->estado == 'Publicada' ? 'readonly' : '' }}>
                        @error('metodoPago')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                @if($convocatoria->estado == 'Borrador')
                <!-- Áreas y Categorías - Solo visible en estado Borrador -->
                <div class="form-section">
                    <h2 class="section-title">Áreas y Categorías</h2>

                    <div id="areas-container">
                        @foreach($areasConCategorias as $index => $area)
                        <div class="area-container">
                            <div class="area-header">
                                <div class="area-title">
                                    <select name="areas[{{ $index }}][idArea]" class="form-control area-select" required>
                                        <option value="">Seleccione un área</option>
                                        @foreach($areas as $areaOption)
                                        <option value="{{ $areaOption->idArea }}" {{ $areaOption->idArea == $area->idArea ? 'selected' : '' }}>
                                            {{ $areaOption->nombre }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="area-actions">
                                    <button type="button" class="btn-remove" title="Eliminar área">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="categorias-container" id="categorias-{{ $index }}">
                                @foreach($area->categorias as $catIndex => $categoria)
                                <div class="category-container">
                                    <div class="category-header">
                                        <div class="category-title">
                                            <select name="areas[{{ $index }}][categorias][{{ $catIndex }}][idCategoria]" class="form-control categoria-select" required>
                                                <option value="">Seleccione una categoría</option>
                                                @foreach($categorias as $categoriaOption)
                                                <option value="{{ $categoriaOption->idCategoria }}" {{ $categoriaOption->idCategoria == $categoria->idCategoria ? 'selected' : '' }}>
                                                    {{ $categoriaOption->nombre }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="category-actions">
                                            <button type="button" class="btn-remove" title="Eliminar categoría">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grade-options">
                                        @foreach($gradosPorCategoria[$categoria->idCategoria] as $grado)
                                        <div class="grade-option">
                                            <input type="checkbox"
                                                name="areas[{{ $index }}][categorias][{{ $catIndex }}][grados][]"
                                                value="{{ $grado['idGrado'] }}"
                                                id="grado-{{ $index }}-{{ $catIndex }}-{{ $grado['idGrado'] }}"
                                                {{ in_array($grado['idGrado'], $categoria->grados->pluck('idGrado')->toArray()) ? 'checked' : '' }}>
                                            <label for="grado-{{ $index }}-{{ $catIndex }}-{{ $grado['idGrado'] }}">{{ $grado['grado'] }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <button type="button" class="btn-add mt-3 btn-add-categoria" data-area-id="{{ $index }}">
                                <i class="fas fa-plus-circle"></i> Agregar Categoría
                            </button>
                        </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn-add mt-3" id="btn-add-area">
                        <i class="fas fa-plus-circle"></i> Agregar Área
                    </button>
                </div>
                @endif

                <!-- Requisitos -->
                <div class="form-section">
                    <h2 class="section-title">Requisitos</h2>

                    <div class="form-group">
                        <label for="requisitos">Requisitos de participación</label>
                        <textarea id="requisitos" name="requisitos" class="form-control" required minlength="10" maxlength="300"
                            {{ $convocatoria->estado == 'Publicada' ? 'readonly' : '' }}>{{ old('requisitos', $convocatoria->requisitos) }}</textarea>
                        @error('requisitos')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Contacto de Soporte -->
                <div class="form-section">
                    <h2 class="section-title">Contacto de Soporte</h2>

                    <div class="form-group">
                        <label for="contacto">Información de Contacto</label>
                        <textarea id="contacto" name="contacto" class="form-control contact-info" required minlength="10" maxlength="255">{{ old('contacto', $convocatoria->contacto) }}</textarea>
                        @error('contacto')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="form-actions">
                    <a href="{{ route('convocatorias.ver', $convocatoria->idConvocatoria) }}" class="btn-cancel">Cancelar</a>
                    <button type="submit" class="btn-save">Guardar Cambios</button>
                </div>
            </form>
        </div>
        @endif
    </div>

    @if($convocatoria->estado != 'Cancelada')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var areasData = JSON.parse(`{!! json_encode($areas) !!}`);
            var categoriasData = JSON.parse(`{!! json_encode($categorias) !!}`);
            var gradosPorCategoriaData = JSON.parse(`{!! json_encode($gradosPorCategoria) !!}`);

            // Mantener un registro de áreas y categorías agregadas
            var areasAgregadas = new Set();
            var categoriasPorArea = {};

            // Inicializar áreas existentes
            var areasConCategoriasData = JSON.parse(`{!! json_encode($areasConCategorias) !!}`);
            
            // Recorrer las áreas y categorías con JavaScript en lugar de directivas Blade
            Object.entries(areasConCategoriasData).forEach(function([index, area]) {
                areasAgregadas.add(area.idArea);
                categoriasPorArea[index] = new Set();
                
                if (area.categorias) {
                    area.categorias.forEach(function(categoria) {
                        categoriasPorArea[index].add(categoria.idCategoria);
                    });
                }
            });

            // Validación de fechas
            const fechaInicioInput = document.getElementById('fechaInicio');
            if (fechaInicioInput && !fechaInicioInput.readOnly) {
                fechaInicioInput.addEventListener('change', function() {
                    const fechaInicio = new Date(this.value);
                    const fechaFin = new Date(document.getElementById('fechaFin').value);

                    // Establecer fecha mínima para fecha fin
                    document.getElementById('fechaFin').min = this.value;

                    // Si la fecha fin es anterior a la fecha inicio, resetearla
                    if (fechaFin < fechaInicio) {
                        document.getElementById('fechaFin').value = '';
                    }
                });

                // Establecer fecha mínima como hoy para la fecha de inicio
                const today = new Date();
                const formattedDate = today.toISOString().split('T')[0];
                fechaInicioInput.min = formattedDate;
            }

            // Validación para método de pago
            const metodoPagoInput = document.getElementById('metodoPago');
            if (metodoPagoInput && !metodoPagoInput.readOnly) {
                metodoPagoInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s,.]/g, '');

                    if (this.value.length > 100) {
                        this.value = this.value.substring(0, 100);
                    }
                });
            }

            // Verificar si la convocatoria está en estado borrador
            var convocatoriaEstado = '{{ $convocatoria->estado }}';
            if(convocatoriaEstado === 'Borrador') {
            // Función para agregar categoría
            function agregarCategoria(areaId) {
                const categoriasContainer = document.getElementById(`categorias-${areaId}`);
                const categoriaId = Date.now();

                const categoriaContainer = document.createElement('div');
                categoriaContainer.className = 'category-container';
                categoriaContainer.innerHTML = `
                <div class="category-header">
                <div class="category-title">
                <select name="areas[${areaId}][categorias][${categoriaId}][idCategoria]" class="form-control categoria-select" required>
                <option value="">Seleccione una categoría</option>
                ${categoriasData.map(categoria => `<option value="${categoria.idCategoria}">${categoria.nombre}</option>`).join('')}
                </select>
                </div>
                <div class="category-actions">
                <button type="button" class="btn-remove" title="Eliminar categoría">
                <i class="fas fa-times"></i>
                </button>
                </div>
                </div>

                <div class="grade-options">
                <!-- Los grados se agregarán dinámicamente -->
                </div>
                `;

                categoriasContainer.appendChild(categoriaContainer);

                // Agregar evento para eliminar categoría
                categoriaContainer.querySelector('.btn-remove').addEventListener('click', function() {
                const categoriaSelect = categoriaContainer.querySelector('.categoria-select');
                if (categoriaSelect.value) {
                categoriasPorArea[areaId].delete(categoriaSelect.value);
                }
                categoriasContainer.removeChild(categoriaContainer);
                });

                // Controlar selección de categoría
                const categoriaSelect = categoriaContainer.querySelector('.categoria-select');
                categoriaSelect.addEventListener('change', function() {
                const selectedCategoriaId = this.value;

                if (selectedCategoriaId && categoriasPorArea[areaId].has(selectedCategoriaId)) {
                alert('Esta categoría ya ha sido agregada para esta área.');
                this.value = '';
                return;
                }

                if (this.dataset.previousValue) {
                categoriasPorArea[areaId].delete(this.dataset.previousValue);
                }

                if (selectedCategoriaId) {
                categoriasPorArea[areaId].add(selectedCategoriaId);
                this.dataset.previousValue = selectedCategoriaId;

                // Cargar grados
                const gradeOptions = categoriaContainer.querySelector('.grade-options');
                gradeOptions.innerHTML = '';

                if (gradosPorCategoriaData[selectedCategoriaId]) {
                gradosPorCategoriaData[selectedCategoriaId].forEach(function(grado) {
                const gradeOption = document.createElement('div');
                gradeOption.className = 'grade-option';
                gradeOption.innerHTML = `
                <input type="checkbox" name="areas[${areaId}][categorias][${categoriaId}][grados][]" 
                value="${grado.idGrado}" id="grado-${areaId}-${categoriaId}-${grado.idGrado}">
                <label for="grado-${areaId}-${categoriaId}-${grado.idGrado}">${grado.nombre}</label>
                `;
                gradeOptions.appendChild(gradeOption);
                });
                }
                } else {
                categoriaContainer.querySelector('.grade-options').innerHTML = '';
                }
                });
            }

            // Agregar área
            document.getElementById('btn-add-area').addEventListener('click', function() {
                const areasContainer = document.getElementById('areas-container');
                const areaId = Date.now();

                const areaContainer = document.createElement('div');
                areaContainer.className = 'area-container';
                areaContainer.innerHTML = `
                    <div class="area-header">
                        <div class="area-title">
                            <select name="areas[${areaId}][idArea]" class="form-control area-select" required>
                                <option value="">Seleccione un área</option>
                                ${areasData.map(area => `<option value="${area.idArea}">${area.nombre}</option>`).join('')}
                            </select>
                        </div>
                        <div class="area-actions">
                            <button type="button" class="btn-remove" title="Eliminar área">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="categorias-container" id="categorias-${areaId}">
                        <!-- Las categorías se agregarán dinámicamente -->
                    </div>
                    
                    <button type="button" class="btn-add mt-3 btn-add-categoria" data-area-id="${areaId}">
                        <i class="fas fa-plus-circle"></i> Agregar Categoría
                    </button>
                `;

                areasContainer.appendChild(areaContainer);

                // Inicializar el registro de categorías para esta área
                categoriasPorArea[areaId] = new Set();

                // Agregar evento para eliminar área
                areaContainer.querySelector('.btn-remove').addEventListener('click', function() {
                    const areaSelect = areaContainer.querySelector('.area-select');
                    if (areaSelect.value) {
                        areasAgregadas.delete(areaSelect.value);
                    }
                    delete categoriasPorArea[areaId];
                    areasContainer.removeChild(areaContainer);
                });

                // Agregar evento para agregar categoría
                areaContainer.querySelector('.btn-add-categoria').addEventListener('click', function() {
                    agregarCategoria(this.getAttribute('data-area-id'));
                });

                // Controlar selección de área
                const areaSelect = areaContainer.querySelector('.area-select');
                areaSelect.addEventListener('change', function() {
                    const selectedAreaId = this.value;

                    if (selectedAreaId && areasAgregadas.has(selectedAreaId)) {
                        alert('Esta área ya ha sido agregada.');
                        this.value = '';
                        return;
                    }

                    if (this.dataset.previousValue) {
                        areasAgregadas.delete(this.dataset.previousValue);
                    }

                    if (selectedAreaId) {
                        areasAgregadas.add(selectedAreaId);
                        this.dataset.previousValue = selectedAreaId;
                    }

                    document.getElementById(`categorias-${areaId}`).innerHTML = '';
                    categoriasPorArea[areaId] = new Set();
                });
            });

            // Inicializar eventos para áreas existentes
            document.querySelectorAll('.area-container').forEach(function(areaContainer) {
                const areaId = areaContainer.querySelector('.btn-add-categoria').getAttribute('data-area-id');

                areaContainer.querySelector('.btn-remove').addEventListener('click', function() {
                    const areaSelect = areaContainer.querySelector('.area-select');
                    if (areaSelect.value) {
                        areasAgregadas.delete(areaSelect.value);
                    }
                    delete categoriasPorArea[areaId];
                    areaContainer.remove();
                });

                areaContainer.querySelector('.btn-add-categoria').addEventListener('click', function() {
                    agregarCategoria(this.getAttribute('data-area-id'));
                });

                areaContainer.querySelectorAll('.category-container').forEach(function(categoriaContainer) {
                    categoriaContainer.querySelector('.btn-remove').addEventListener('click', function() {
                        const categoriaSelect = categoriaContainer.querySelector('.categoria-select');
                        if (categoriaSelect.value) {
                            categoriasPorArea[areaId].delete(categoriaSelect.value);
                        }
                        categoriaContainer.remove();
                    });
                });
            });
            } // Cierre del if para estado Borrador

            // Validar el formulario antes de enviar
            document.getElementById('convocatoriaForm').addEventListener('submit', function(e) {
                let isValid = true;

                // Validar campos requeridos
                this.querySelectorAll('input[required], textarea[required], select[required]').forEach(function(field) {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                // Validar áreas y categorías solo si la convocatoria está en estado borrador
                if (convocatoriaEstado === 'Borrador') {
                    const areas = document.querySelectorAll('.area-container');
                    if (areas.length === 0) {
                        isValid = false;
                        alert('Debe agregar al menos un área.');
                    } else {
                        areas.forEach(function(area) {
                            const areaSelect = area.querySelector('.area-select');
                            const categorias = area.querySelectorAll('.category-container');

                            if (categorias.length === 0) {
                                isValid = false;
                                alert(`El área "${areaSelect.options[areaSelect.selectedIndex].text}" debe tener al menos una categoría.`);
                            } else {
                                categorias.forEach(function(categoria) {
                                    const categoriaSelect = categoria.querySelector('.categoria-select');
                                    const gradosSeleccionados = categoria.querySelectorAll('input[type="checkbox"]:checked').length;

                                    if (gradosSeleccionados === 0) {
                                        isValid = false;
                                        alert(`La categoría "${categoriaSelect.options[categoriaSelect.selectedIndex].text}" debe tener al menos un grado seleccionado.`);
                                    }
                                });
                            }
                        });
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    </script>
    @endif
</x-app-layout>