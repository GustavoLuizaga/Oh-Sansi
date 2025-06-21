<x-app-layout>
    <link rel="stylesheet" href="/css/delegacion/delegacion.css">
    <link rel="stylesheet" href="/css/delegado/delegado.css">

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success py-1 px-2 mb-1">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Header Section -->
    <div class="delegaciones-header py-2">
        <h1><i class="fas fa-chalkboard-teacher"></i> {{ __('Administrar Delegados') }}</h1>
    </div>

    <!-- Actions Container (Search and Buttons) -->
    <!-- Actions Container (Add and Search in the same row) -->
    <div class="actions-container mb-1">
        <a href="{{ route('delegado.agregar') }}" class="btn-nuevo-tutor">
            <i class="fas fa-plus-circle"></i> Agregar Tutor
        </a>
        <div class="search-filter-container">
            <form action="{{ route('delegado') }}" method="GET" id="searchForm" class="search-form">
                <div class="search-box">
                    <div class="search-input-group">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Buscar por nombre o CI" value="{{ request('search') }}" class="py-1">
                        <button type="submit" class="search-button py-1 px-2">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                    <div class="filter-group">
                        <select name="colegio" class="filter-select" onchange="this.form.submit()">
                            <option value="">Todos los colegios</option>
                            @foreach($colegios as $colegio)
                                <option value="{{ $colegio->id }}" {{ request('colegio') == $colegio->id ? 'selected' : '' }}>
                                    {{ $colegio->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <a href="{{ route('delegado.solicitudes') }}" class="add-button py-1 px-2">
            <i class="fas fa-clipboard-list"></i> Solicitudes
        </a>
    </div>

    <!-- Table -->
    <table class="delegaciones-table">
        <thead>
            <tr>
                <th>
                    CI
                    <a href="{{ route('delegado', array_merge(request()->query(), ['sort' => 'ci', 'direction' => 'asc'])) }}" class="sort-link">
                        <i class="fas fa-sort-up {{ (request('sort') == 'ci' && request('direction') == 'asc') ? 'text-primary-color' : 'text-gray-400' }}"></i>
                    </a>
                    <a href="{{ route('delegado', array_merge(request()->query(), ['sort' => 'ci', 'direction' => 'desc'])) }}" class="sort-link">
                        <i class="fas fa-sort-down {{ (request('sort') == 'ci' && request('direction') == 'desc') ? 'text-primary-color' : 'text-gray-400' }}"></i>
                    </a>
                </th>
                <th>
                    Nombre
                    <a href="{{ route('delegado', array_merge(request()->query(), ['sort' => 'name', 'direction' => 'asc'])) }}" class="sort-link">
                        <i class="fas fa-sort-up {{ (request('sort') == 'name' && request('direction') == 'asc') ? 'text-primary-color' : 'text-gray-400' }}"></i>
                    </a>
                    <a href="{{ route('delegado', array_merge(request()->query(), ['sort' => 'name', 'direction' => 'desc'])) }}" class="sort-link">
                        <i class="fas fa-sort-down {{ (request('sort') == 'name' && request('direction') == 'desc') ? 'text-primary-color' : 'text-gray-400' }}"></i>
                    </a>
                </th>
                <th>
                    Colegio
                    <a href="{{ route('delegado', array_merge(request()->query(), ['sort' => 'colegio', 'direction' => 'asc'])) }}" class="sort-link">
                        <i class="fas fa-sort-up {{ (request('sort') == 'colegio' && request('direction') == 'asc') ? 'text-primary-color' : 'text-gray-400' }}"></i>
                    </a>
                    <a href="{{ route('delegado', array_merge(request()->query(), ['sort' => 'colegio', 'direction' => 'desc'])) }}" class="sort-link">
                        <i class="fas fa-sort-down {{ (request('sort') == 'colegio' && request('direction') == 'desc') ? 'text-primary-color' : 'text-gray-400' }}"></i>
                    </a>
                </th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tutores ?? [] as $tutor)
            <tr>
                <td>{{ $tutor->user->ci }}</td>
                <td>{{ $tutor->user->name }} {{ $tutor->user->apellidoPaterno }} {{ $tutor->user->apellidoMaterno }}</td>
                <td>
                    @php
                        $colegiosUnicos = $tutor->delegaciones->unique('nombre');
                    @endphp
                    @foreach($colegiosUnicos as $delegacion)
                        {{ $delegacion->nombre }}
                        @if(!$loop->last), @endif
                    @endforeach
                </td>
                <td class="actions">
                    <div class="flex space-x-1">
                        <a href="{{ route('delegado.ver', ['id' => $tutor->user->id]) }}" class="action-button view" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('delegado.editar', ['id' => $tutor->user->id]) }}" class="action-button edit" title="Editar tutor">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" 
                                data-tutor-id="{{ $tutor->user->id }}" 
                                class="action-button delete btn-eliminar" 
                                title="Eliminar tutor">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No hay tutores registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if(isset($tutores) && $tutores->count() > 0)
    <div class="paginacion">
        <div class="pagination-container">
            @if($tutores->lastPage() > 1)
                <ul class="pagination-list">
                    {{-- First Page --}}
                    <li class="{{ ($tutores->currentPage() == 1) ? 'disabled' : '' }}">
                        <a href="{{ $tutores->url(1) }}" class="pagination-link">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    </li>
                    {{-- Previous Page --}}
                    <li class="{{ ($tutores->currentPage() == 1) ? 'disabled' : '' }}">
                        <a href="{{ $tutores->url($tutores->currentPage() - 1) }}" class="pagination-link">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    </li>
                    {{-- Numbered Pages --}}
                    @for($i = max(1, $tutores->currentPage() - 2); $i <= min($tutores->lastPage(), $tutores->currentPage() + 2); $i++)
                        <li class="{{ ($tutores->currentPage() == $i) ? 'active' : '' }}">
                            <a href="{{ $tutores->url($i) }}" class="pagination-link">{{ $i }}</a>
                        </li>
                    @endfor
                    {{-- Next Page --}}
                    <li class="{{ ($tutores->currentPage() == $tutores->lastPage()) ? 'disabled' : '' }}">
                        <a href="{{ $tutores->url($tutores->currentPage() + 1) }}" class="pagination-link">
                            <i class="fas fa-angle-right"></i>
                        </a>
                    </li>
                    {{-- Last Page --}}
                    <li class="{{ ($tutores->currentPage() == $tutores->lastPage()) ? 'disabled' : '' }}">
                        <a href="{{ $tutores->url($tutores->lastPage()) }}" class="pagination-link">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                </ul>
                <div class="pagination-info">
                    Mostrando {{ $tutores->firstItem() ?? 0 }} - {{ $tutores->lastItem() ?? 0 }} de {{ $tutores->total() }} tutores
                </div>
            @endif
        </div>
    </div>
    <style>
    .paginacion {
        margin-top: 1rem;
        padding: 1rem;
    }
    .pagination-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }
    .pagination-list {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        gap: 0.5rem;
    }
    .pagination-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        height: 2rem;
        padding: 0.5rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        color: #4a5568;
        text-decoration: none;
        transition: all 0.2s;
    }
    .pagination-list li.active .pagination-link {
        background-color: #4299e1;
        border-color: #4299e1;
        color: white;
    }
    .pagination-list li.disabled .pagination-link {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }
    .pagination-link:hover:not(.disabled) {
        background-color: #ebf4ff;
        border-color: #4299e1;
    }
    .pagination-info {
        color: #4a5568;
        font-size: 0.875rem;
    }
    </style>
    @endif


</x-app-layout>

<!-- Add this at the bottom of the file -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <h3>Confirmar Eliminación</h3>
        <p>¿Está seguro que desea eliminar este tutor?</p>
        <div class="modal-actions">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="button" class="cancel-button" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="delete-button">Eliminar</button>
            </form>
        </div>
    </div>
</div>

<!-- Update the JavaScript at the bottom of the file -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-eliminar');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tutorId = this.getAttribute('data-tutor-id');
            confirmarEliminacion(tutorId);
        });
    });
});

function confirmarEliminacion(tutorId) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    form.action = `/delegado/eliminar/${tutorId}`;
    modal.style.display = 'flex';
}

function cerrarModal() {
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target == modal) {
        cerrarModal();
    }
}
</script>