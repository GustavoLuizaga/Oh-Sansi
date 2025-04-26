<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/delegacion/delegacion.css') }}">
    <link rel="stylesheet" href="{{ asset('css/delegado/delegado.css') }}">

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success py-1 px-2 mb-1">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <!-- Header Section -->
    <div class="delegaciones-header py-2">
        <h1><i class="fas fa-chalkboard-teacher"></i> {{ __('Administrar Tutores') }}</h1>
    </div>

    <!-- Actions Container (Search and Buttons) -->
    <div class="actions-container mb-1">
        <div class="search-filter-container mb-1">
            <form action="{{ route('delegado') }}" method="GET" id="searchForm">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Buscar por nombre o CI" value="{{ request('search') }}" class="py-1">
                    <button type="submit" class="search-button py-1 px-2">
                        <i class="fas fa-search"></i> Buscar
                    </button>
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
                    @foreach($tutor->delegaciones as $delegacion)
                        {{ $delegacion->nombre }}
                        @if(!$loop->last), @endif
                    @endforeach
                </td>
                <td class="actions">
                    <div class="flex space-x-1">
                        <a href="#" class="action-button view w-5 h-5">
                            <i class="fas fa-eye text-xs"></i>
                        </a>
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
    <div class="pagination">
        {{ $tutores->appends(request()->query())->links() }}
    </div>
    @endif


</x-app-layout>