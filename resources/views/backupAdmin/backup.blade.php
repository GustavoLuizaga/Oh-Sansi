<x-app-layout>
    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold mb-4">Registros de Auditoría</h2>
        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">ID</th>
                    <th class="border px-4 py-2">Tabla</th>
                    <th class="border px-4 py-2">Acción</th>
                    <th class="border px-4 py-2">Datos Anteriores</th>
                    <th class="border px-4 py-2">Datos Nuevos</th>
                    <th class="border px-4 py-2">Usuario ID</th>
                    <th class="border px-4 py-2">Fecha Cambio</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td class="border px-4 py-2">{{ $log->id }}</td>
                        <td class="border px-4 py-2">{{ $log->tabla }}</td>
                        <td class="border px-4 py-2">{{ $log->accion }}</td>
                        <td class="border px-4 py-2">
                            <pre>{{ json_encode($log->datos_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </td>
                        <td class="border px-4 py-2">
                            <pre>{{ json_encode($log->datos_nuevos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </td>
                        <td class="border px-4 py-2">{{ $log->usuario_id }}</td>
                        <td class="border px-4 py-2">{{ $log->fecha_cambio }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
