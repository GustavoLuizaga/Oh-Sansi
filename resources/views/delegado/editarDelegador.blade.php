<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/delegado/editar-delegador.css') }}">

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success py-1 px-2 mb-1">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Error Messages -->
    @if ($errors->any())
    <div class="alert alert-danger py-1 px-2 mb-1">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Header Section -->
    <div class="delegado-header py-2">
        <h1><i class="fas fa-user-edit"></i> {{ __('Editar Tutor') }}</h1>
    </div>

    <form action="{{ route('delegado.actualizar', $tutor->id) }}" method="POST" class="form-container">
        @csrf
        @method('PUT')
        
        <!-- Información Personal -->
        <div class="form-section">
            <h2 class="section-title"><i class="fas fa-user mr-2"></i>Información Personal</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="ci" class="form-label">CI:</label>
                    <input type="text" class="form-control" id="ci" name="ci" value="{{ $tutor->user->ci }}" readonly>
                </div>
                
                <div class="form-group">
                    <label for="name" class="form-label required-label">Nombre:</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $tutor->user->name }}" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="apellidoPaterno" class="form-label required-label">Apellido Paterno:</label>
                    <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" value="{{ $tutor->user->apellidoPaterno }}" required>
                </div>
                
                <div class="form-group">
                    <label for="apellidoMaterno" class="form-label">Apellido Materno:</label>
                    <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno" value="{{ $tutor->user->apellidoMaterno }}">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="email" class="form-label required-label">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $tutor->user->email }}" required>
                </div>
                
                <div class="form-group">
                    <label for="telefono" class="form-label required-label">Teléfono:</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" value="{{ $tutor->telefono }}" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="profesion" class="form-label required-label">Profesión:</label>
                    <input type="text" class="form-control" id="profesion" name="profesion" value="{{ $tutor->profesion }}" required>
                </div>
                
                <div class="form-group">
                    <label for="genero" class="form-label required-label">Género:</label>
                    <select class="form-control" id="genero" name="genero" required>
                        <option value="M" {{ $tutor->user->genero == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ $tutor->user->genero == 'F' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="fechaNacimiento" class="form-label required-label">Fecha de Nacimiento:</label>
                    <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" value="{{ $tutor->user->fechaNacimiento }}" required>
                </div>
            </div>
        </div>
        
        <!-- Información Académica -->
        <div class="form-section">
            <h2 class="section-title"><i class="fas fa-graduation-cap mr-2"></i>Información Académica</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Colegio(s):</label>
                    <div class="multi-select-container">
                        @foreach($colegios as $colegio)
                            <div class="multi-select-item">
                                <input type="checkbox" 
                                       id="colegio_{{ $colegio->idDelegacion }}" 
                                       name="colegios[]" 
                                       value="{{ $colegio->idDelegacion }}" 
                                       class="colegio-checkbox"
                                       data-colegio-id="{{ $colegio->idDelegacion }}"
                                       {{ $tutor->delegaciones->contains('idDelegacion', $colegio->idDelegacion) ? 'checked' : '' }}>
                                <label for="colegio_{{ $colegio->idDelegacion }}">{{ $colegio->nombre }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="form-row mt-4">
                <div class="form-group w-full">
                    <label class="form-label">Áreas de Tutoría por Colegio:</label>
                    
                    @foreach($colegios as $colegio)
                        @php
                            $isColegioSelected = $tutor->delegaciones()->where('delegacion.idDelegacion', $colegio->idDelegacion)->exists();
                        @endphp
                        <div id="areas_colegio_{{ $colegio->idDelegacion }}" 
                             class="areas-por-colegio mt-2 {{ $isColegioSelected ? 'show-area' : 'hide-area' }}">
                            <div class="colegio-title mb-2">
                                <i class="fas fa-school mr-1"></i> <strong>{{ $colegio->nombre }}</strong>
                            </div>
                            <div class="multi-select-container ml-4">
                                @foreach($areas as $area)
                                    @php
                                        $areaAsignadaEnColegio = $tutor->areas()
                                            ->wherePivot('idDelegacion', $colegio->idDelegacion)
                                            ->wherePivot('idArea', $area->idArea)
                                            ->exists();
                                    @endphp
                                    <div class="multi-select-item">
                                        <input type="checkbox" 
                                               id="area_{{ $colegio->idDelegacion }}_{{ $area->idArea }}" 
                                               name="areas_colegio[{{ $colegio->idDelegacion }}][]" 
                                               value="{{ $area->idArea }}" 
                                               {{ $areaAsignadaEnColegio ? 'checked' : '' }}>
                                        <label for="area_{{ $colegio->idDelegacion }}_{{ $area->idArea }}">{{ $area->nombre }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Opciones de Director -->
        <div class="form-section">
            <h2 class="section-title"><i class="fas fa-user-tie mr-2"></i>Rol de Director</h2>
            <div class="checkbox-container">
                <input type="checkbox" id="es_director" name="es_director" {{ $tutor->es_director ? 'checked' : '' }}>
                <label for="es_director">¿Es Director?</label>
            </div>
            <p class="text-sm text-gray-500 mt-2">
                <i class="fas fa-info-circle mr-1"></i> Los directores tienen permisos adicionales para gestionar tutores y recursos.
            </p>
        </div>
        
        <!-- Botones de Acción -->
        <div class="action-buttons">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-2"></i> Guardar Cambios
            </button>
            
            <a href="{{ route('delegado.ver', ['id' => $tutor->user->id]) }}" class="btn btn-secondary">
                <i class="fas fa-times mr-2"></i> Cancelar
            </a>
        </div>
    </form>
    <!-- Add styles for showing/hiding areas -->
    <style>
        .show-area {
            display: block;
        }
        .hide-area {
            display: none;
        }
    </style>

    <!-- JavaScript to handle area visibility -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const colegioCheckboxes = document.querySelectorAll('.colegio-checkbox');
            
            colegioCheckboxes.forEach(checkbox => {
                const colegioId = checkbox.getAttribute('data-colegio-id');
                const areasContainer = document.getElementById(`areas_colegio_${colegioId}`);
                
                if (checkbox.checked) {
                    areasContainer.classList.add('show-area');
                    areasContainer.classList.remove('hide-area');
                } else {
                    areasContainer.classList.add('hide-area');
                    areasContainer.classList.remove('show-area');
                }
                
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        areasContainer.classList.add('show-area');
                        areasContainer.classList.remove('hide-area');
                    } else {
                        areasContainer.classList.add('hide-area');
                        areasContainer.classList.remove('show-area');
                        
                        const areaCheckboxes = areasContainer.querySelectorAll('input[type="checkbox"]');
                        areaCheckboxes.forEach(areaCheckbox => {
                            areaCheckbox.checked = false;
                        });
                    }
                });
            });
        });
    </script>
</x-app-layout>
