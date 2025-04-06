document.addEventListener('DOMContentLoaded', function() {
    // Export PDF button
    document.getElementById('exportPdf').addEventListener('click', function(e) {
        e.preventDefault();
        // Add PDF export functionality here
        alert('Exportando a PDF...');
    });
    
    // Export Excel button
    document.getElementById('exportExcel').addEventListener('click', function(e) {
        e.preventDefault();
        // Add Excel export functionality here
        alert('Exportando a Excel...');
    });

//Buscador de gestion de convocatorias y filtro por estado
    const searchInput = document.getElementById('searchConvocatoria');
    const tableRows = document.querySelectorAll('.convocatoria-table tbody tr');

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();

        tableRows.forEach(row => {
            const nombre = row.querySelector('td:first-child').textContent.toLowerCase();
            
            if (nombre.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });

        // Mostrar mensaje cuando no hay resultados
        const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
        const tbody = document.querySelector('.convocatoria-table tbody');
        const noResultsRow = tbody.querySelector('.no-results');

        if (visibleRows.length === 0) {
            if (!noResultsRow) {
                const tr = document.createElement('tr');
                tr.className = 'no-results';
                tr.innerHTML = '<td colspan="6" class="text-center text-danger">No se encontraron convocatorias con ese nombre</td>';
                tbody.appendChild(tr);
            }
        } else if (noResultsRow) {
            noResultsRow.remove();
        }
    });

    const estadoSelect = document.getElementById('estado');
    
    // Función para realizar la búsqueda
    function realizarBusqueda() {
        const searchTerm = searchInput.value;
        const estado = estadoSelect.value;
        
        // Construir la URL con los parámetros
        const urlParams = new URLSearchParams();
        if (searchTerm) urlParams.set('search', searchTerm);
        if (estado) urlParams.set('estado', estado);
        
        // Redirigir con los parámetros
        window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
    }

    // Manejar el evento input del buscador con debounce
    let timeoutId;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(realizarBusqueda, 500);
    });

    // Manejar el cambio de estado
    estadoSelect.addEventListener('change', realizarBusqueda);
});



