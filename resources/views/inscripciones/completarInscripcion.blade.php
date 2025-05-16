<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/inscripcion/listaEstudiantes.css') }}">

    <!-- Header Section -->
    <div class="estudiantes-header py-2">
        <h1><i class="fas fa-user-check"></i> {{ __('Completar Inscripción de Estudiante') }}</h1>
    </div>

    <!-- Form Container -->
    <div class="bg-white p-4 rounded-lg shadow-md">
        <form action="{{ route('estudiantes.completarInscripcion.store', $estudiante->id) }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <h2 class="text-lg font-semibold mb-2">Datos del Estudiante</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre:</label>
                        <p class="bg-gray-100 p-2 rounded">{{ $estudiante->user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos:</label>
                        <p class="bg-gray-100 p-2 rounded">{{ $estudiante->user->apellidoPaterno }} {{ $estudiante->user->apellidoMaterno }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CI:</label>
                        <p class="bg-gray-100 p-2 rounded">{{ $estudiante->user->ci }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <h2 class="text-lg font-semibold mb-2">Datos de Inscripción</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="idConvocatoria" class="block text-sm font-medium text-gray-700 mb-1">Convocatoria:</label>
                        <select name="idConvocatoria" id="idConvocatoria" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            <option value="">Seleccione una convocatoria</option>
                            @foreach($convocatorias as $convocatoria)
                                <option value="{{ $convocatoria->idConvocatoria }}">{{ $convocatoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="idArea" class="block text-sm font-medium text-gray-700 mb-1">Área:</label>
                        <select name="idArea" id="idArea" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            <option value="">Seleccione un área</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->idArea }}">{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="idCategoria" class="block text-sm font-medium text-gray-700 mb-1">Categoría:</label>
                        <select name="idCategoria" id="idCategoria" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            <option value="">Seleccione una categoría</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->idCategoria }}">{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="idGrado" class="block text-sm font-medium text-gray-700 mb-1">Grado:</label>
                        <select name="idGrado" id="idGrado" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            <option value="">Seleccione un grado</option>
                            @foreach($grados as $grado)
                                <option value="{{ $grado->idGrado }}">{{ $grado->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="idDelegacion" class="block text-sm font-medium text-gray-700 mb-1">Colegio:</label>
                        <select name="idDelegacion" id="idDelegacion" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            <option value="">Seleccione un colegio</option>
                            @foreach($delegaciones as $delegacion)
                                <option value="{{ $delegacion->idDelegacion }}">{{ $delegacion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="numeroContacto" class="block text-sm font-medium text-gray-700 mb-1">Número de Contacto:</label>
                        <input type="text" name="numeroContacto" id="numeroContacto" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required maxlength="8" placeholder="Ej: 70123456">
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('estudiantes.pendientes') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Completar Inscripción
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const convocatoriaSelect = document.getElementById('idConvocatoria');
            const areaSelect = document.getElementById('idArea');
            const categoriaSelect = document.getElementById('idCategoria');
            
            // Aquí se puede agregar lógica para cargar dinámicamente las áreas según la convocatoria seleccionada
            // y las categorías según el área seleccionada
            
            convocatoriaSelect.addEventListener('change', function() {
                // Lógica para actualizar áreas según la convocatoria seleccionada
            });
            
            areaSelect.addEventListener('change', function() {
                // Lógica para actualizar categorías según el área seleccionada
            });
        });
    </script>
</x-app-layout>