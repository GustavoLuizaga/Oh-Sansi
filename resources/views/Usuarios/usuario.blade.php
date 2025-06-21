<x-app-layout>
    <link rel="stylesheet" href="/css/usuarios/usuario.css">
    <!-- Messages -->
    @if(session('success'))
    <div class="alert alert-success py-1 px-2 mb-1">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger py-1 px-2 mb-1">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    <!-- Header Section -->
    <div class="usuarios-header py-2">
        <h1><i class="fas fa-users"></i> {{ __('Administrar Usuarios') }}</h1>
    </div>

    <!-- Actions Container (Add and Search in the same row) -->
    <div class="actions-container mb-1">
        <a href="{{ route('usuarios.agregar') }}" class="add-button py-1 px-2">
            <i class="fas fa-plus"></i> Agregar Usuario
        </a>
        <div class="search-filter-container mb-1">
            <form action="{{ route('usuarios') }}" method="GET" id="searchForm">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Buscar por nombre o correo" value="{{ request('search') }}" class="py-1">
                    <button type="submit" class="search-button py-1 px-2">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filter -->
    <form action="{{ route('usuarios') }}" method="GET" id="filterForm">
        <div class="filter-container mb-2 py-1 px-2">
            <div class="filter-group">
                <label for="rol" class="text-xs mb-1">Filtrar por Rol:</label>
                <select class="filter-select py-1" name="rol" id="rol" onchange="this.form.submit()">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->idRol }}" {{ request('rol') == $rol->idRol ? 'selected' : '' }}>{{ $rol->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
    </form>

    <!-- Table -->
    <table class="usuarios-table">
        <thead>
            <tr>
                <th>CI</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Género</th>
                <th>Roles</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usuarios as $usuario)
            <tr>
                <td>{{ $usuario->ci }}</td>
                <td>{{ $usuario->name }} {{ $usuario->apellidoPaterno }} {{ $usuario->apellidoMaterno }}</td>
                <td>{{ $usuario->email }}</td>
                <td>{{ $usuario->genero == 'M' ? 'Masculino' : 'Femenino' }}</td>
                <td>
                    @foreach($usuario->roles as $rol)
                        <span class="badge bg-primary text-white text-xs px-1 py-0.5 rounded">{{ $rol->nombre }}</span>
                    @endforeach
                </td>
                <td class="actions">
                    <div class="flex space-x-1">
                        <a href="{{ route('usuarios.ver', $usuario->id) }}" class="action-button view w-5 h-5">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
                        <a href="{{ route('usuarios.editar', $usuario->id) }}" class="action-button edit w-5 h-5">
                            <i class="fas fa-edit text-xs"></i>
                        </a>
                        <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro que desea eliminar este usuario?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-button delete-button w-5 h-5">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">No hay usuarios registrados</td>
            </tr>
            @endforelse
        </tbody>
    </table>    <!-- Pagination -->
    <div class="paginacion">
        <div class="pagination-container">
            @if($usuarios->lastPage() > 1)
                <ul class="pagination-list">
                    {{-- First Page --}}
                    <li class="{{ ($usuarios->currentPage() == 1) ? 'disabled' : '' }}">
                        <a href="{{ $usuarios->url(1) }}" class="pagination-link">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    </li>

                    {{-- Previous Page --}}
                    <li class="{{ ($usuarios->currentPage() == 1) ? 'disabled' : '' }}">
                        <a href="{{ $usuarios->url($usuarios->currentPage() - 1) }}" class="pagination-link">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    </li>

                    {{-- Numbered Pages --}}
                    @for($i = max(1, $usuarios->currentPage() - 2); $i <= min($usuarios->lastPage(), $usuarios->currentPage() + 2); $i++)
                        <li class="{{ ($usuarios->currentPage() == $i) ? 'active' : '' }}">
                            <a href="{{ $usuarios->url($i) }}" class="pagination-link">{{ $i }}</a>
                        </li>
                    @endfor

                    {{-- Next Page --}}
                    <li class="{{ ($usuarios->currentPage() == $usuarios->lastPage()) ? 'disabled' : '' }}">
                        <a href="{{ $usuarios->url($usuarios->currentPage() + 1) }}" class="pagination-link">
                            <i class="fas fa-angle-right"></i>
                        </a>
                    </li>

                    {{-- Last Page --}}
                    <li class="{{ ($usuarios->currentPage() == $usuarios->lastPage()) ? 'disabled' : '' }}">
                        <a href="{{ $usuarios->url($usuarios->lastPage()) }}" class="pagination-link">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                </ul>
                <div class="pagination-info">
                    Mostrando {{ $usuarios->firstItem() ?? 0 }} - {{ $usuarios->lastItem() ?? 0 }} de {{ $usuarios->total() }} usuarios
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
</x-app-layout>