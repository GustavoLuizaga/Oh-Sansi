<link rel="stylesheet" href="/css/backupAdmin/backup.css">

<x-app-layout>
    <!-- Título principal con franja azul -->
    <div class="page-header-blue py-2">
        <h1><i class="fas fa-clipboard-list"></i> Gestion de respaldos</h1>
    </div>

    <!-- Tabla de auditoría -->

    <table id="audit-table">
        <thead>
            <tr>
                <th>TABLA</th>
                <th>ACCIÓN</th>
                <th>DATOS ANTERIORES</th>
                <th>DATOS NUEVOS</th>
                <th>USUARIO</th>
                <th>FECHA</th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
            <tr class="audit-row" data-action="{{ $log->accion }}">
                <td>{{ $log->tabla }}</td>
                <td class="accion-cell">{{ $log->accion }}</td>
                <td class="json-cell">{{ json_encode($log->datos_anteriores, JSON_UNESCAPED_UNICODE) }}</td>
                <td class="json-cell">{{ json_encode($log->datos_nuevos, JSON_UNESCAPED_UNICODE) }}</td>
                <td>{{ $log->usuario_id }}</td>
                <td>{{ $log->fecha_cambio }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('.audit-row');

            rows.forEach(row => {
                const action = row.dataset.action.trim().toUpperCase();
                const actionCell = row.querySelector('.accion-cell');

                // Aplicar color según la acción
                switch (action) {
                    case 'INSERT':
                        
                        actionCell.style.color = '#059669';
                        
                        break;
                    case 'UPDATE':
                        
                        actionCell.style.color = '#2563eb';
                        
                        break;
                    case 'DELETE':
                       
                        actionCell.style.color = '#dc2626';
                        
                        break;
                }

                // Formatear JSON
                row.querySelectorAll('.json-cell').forEach(cell => {
                    try {
                        let content = cell.textContent.trim();
                        if (content.startsWith('"') && content.endsWith('"')) {
                            content = content.slice(1, -1);
                        }
                        content = content.replace(/\\"/g, '"');

                        const json = JSON.parse(content);
                        const pretty = JSON.stringify(json, null, 2);

                        const pre = document.createElement('pre');
                        pre.textContent = pretty;
                        cell.innerHTML = '';
                        cell.appendChild(pre);
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        cell.textContent = content.replace(/\\"/g, '"');
                    }
                });
            });
        });
    </script>
</x-app-layout>