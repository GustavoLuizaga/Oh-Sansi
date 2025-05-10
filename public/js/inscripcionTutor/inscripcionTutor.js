
document.addEventListener('DOMContentLoaded', function () {
    const areaSelect = document.getElementById('areaSelect');
    const categoriaSelect = document.getElementById('categoriaSelect');
    const gradoSelect = document.getElementById('gradoSelect');

    const idConvocatoria = "{{ $idConvocatoriaResult ?? '' }}";

    areaSelect.addEventListener('change', function () {
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
    categoriaSelect.addEventListener('change', function () {
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