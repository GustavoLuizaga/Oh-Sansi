// filepath: c:\xampp1\htdocs\LARAVEL2025\Oh-Sansi\public\js\dashboard.js
document.addEventListener('DOMContentLoaded', function () {

    const select = document.getElementById('convocatoriaFilter');
    const totalEstudiantes = document.getElementById('totalEstudiantes');
    const totalTutores = document.getElementById('totalTutores');
    let areasChart; // Variable para el gráfico de áreas
    let tipoColegioChart;
    let gradosChart;
    let generoChart;
    let departamentosChart;

    select.addEventListener('change', function () {
        const idConvocatoria = this.value;
        fetch(`/dashboard/datos/${idConvocatoria}`)
            .then(response => response.json())
            .then(data => {
                totalEstudiantes.textContent = data.totalEstudiantes;
                totalTutores.textContent = data.totalTutores;
                actualizarAreasChart(data.areasLabels, data.areasData);
            })
            .catch(error => {
                totalEstudiantes.textContent = '0';
                totalTutores.textContent = '0';

            });

        fetch(`/dashboard/tutores-delegaciones/${idConvocatoria}`)
            .then(response => response.json())
            .then(data => {
                // data.labelD y data.dataD contienen los labels y los datos para el gráfico
                actualizarTipoColegioChart(data.labelD, data.dataD);
            })
            .catch(error => {
                console.error('Error al cargar los datos de tutores por delegación:', error);
            });

        fetch(`/dashboard/grados-convocatoria/${idConvocatoria}`)
            .then(response => response.json())
            .then(data => {
                actualizarGradosChart(data.labels, data.data);
            });

        fetch(`/dashboard/genero-estudiantes/${idConvocatoria}`)
            .then(response => response.json())
            .then(data => {
                actualizarGeneroChart(data.masculino, data.femenino);
            })
            .catch(error => {
                console.error('Error al cargar los datos de género:', error);
                actualizarGeneroChart(0, 0); // Actualizar con valores por defecto si hay error
            });

        fetch(`/dashboard/top-delegaciones/${idConvocatoria}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('.stats-table tbody');
                tbody.innerHTML = '';
                if (data.top.length === 0) {
                    tbody.innerHTML = `<tr>
                <td colspan="3" style="text-align:center;">Sin información</td>
            </tr>`;
                } else {
                    data.top.forEach(item => {
                        tbody.innerHTML += `
                    <tr>
                        <td>${item.colegio}</td>
                        <td>${item.estudiantes}</td>
                        <td>${item.porcentaje}%</td>
                    </tr>
                `;
                    });
                }
            });

        fetch(`/dashboard/departamentos-convocatoria/${idConvocatoria}`)
            .then(response => response.json())
            .then(data => {
                actualizarDepartamentosChart(data.labels, data.data);
            })
            .catch(error => {
                console.error('Error al cargar los datos de departamentos:', error);
                actualizarDepartamentosChart([], []); // Actualizar con valores por defecto si hay error
            });

        fetch(`/dashboard/top-tutores/${idConvocatoria}`)
            .then(response => response.json())
            .then(data => {
                const rankingList = document.querySelector('.ranking-list');
                rankingList.innerHTML = '';
                if (data.top.length === 0) {
                    rankingList.innerHTML = `<div class="ranking-item"><span style="width:100%">Sin información</span></div>`;
                } else {
                    data.top.forEach((item, idx) => {
                        let posClass = '';
                        if (idx === 0) posClass = 'gold';
                        else if (idx === 1) posClass = 'silver';
                        else if (idx === 2) posClass = 'bronze';
                        rankingList.innerHTML += `
                    <div class="ranking-item">
                        <span class="ranking-position ${posClass}">${idx + 1}</span>
                        <div class="ranking-info">
                            <h4>${item.nombre}</h4>
                            <p>${item.estudiantes} estudiantes</p>
                            <div class="tags">
                                ${item.areas.map(area => `<span class="tag">${area}</span>`).join('')}
                            </div>
                        </div>
                        <span class="ranking-score">${item.porcentaje}%</span>
                    </div>
                `;
                    });
                }
            })
            .catch(error => {
                console.error('Error al cargar los datos de tutores:', error);
                const rankingList = document.querySelector('.ranking-list');
                rankingList.innerHTML = `<div class="ranking-item"><span style="width:100%">Sin información</span></div>`;
            });


    });

    // Opcional: cargar el valor inicial al cargar la página
    if (select.value) {
        fetch(`/dashboard/datos/${select.value}`)
            .then(response => response.json())
            .then(data => {
                totalEstudiantes.textContent = data.totalEstudiantes;
            });
    }





    // Configuración de colores
    const colors = {
        primary: '#1a365d',
        secondary: '#2c5282',
        success: '#0ca678',
        warning: '#f59f00',
        info: '#17a2b8',
        danger: '#dc3545',
        purple: '#6f42c1',
        pink: '#e83e8c'
    };

    const chartOptions = {
        responsive: true,
        animation: {
            duration: 2000,
            easing: 'easeOutQuart'
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0,0,0,0.8)',
                padding: 12,
                titleFont: {
                    size: 14
                },
                bodyFont: {
                    size: 13
                }
            }
        }
    };

    // Gráfico de Participación por Área
    function actualizarAreasChart(labels, data) {
        // Si no hay datos, muestra "Sin información"
        if (!labels || labels.length === 0) {
            labels = ['Sin información'];
            data = [1];
        }

        // Paleta de colores para las áreas (puedes agregar más si tienes más áreas)
        const areaColors = [
            colors.primary,
            colors.success,
            colors.warning,
            colors.info,
            colors.purple,
            colors.pink,
            colors.danger,
            colors.secondary
        ];

        // Si hay más áreas que colores, repetir la paleta
        const backgroundColors = labels.map((_, i) => areaColors[i % areaColors.length]);

        if (areasChart) {
            areasChart.data.labels = labels;
            areasChart.data.datasets[0].data = data;
            areasChart.data.datasets[0].backgroundColor = backgroundColors;
            areasChart.update();
        } else {
            const ctx = document.getElementById('areasChart').getContext('2d');
            areasChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColors
                    }]
                },
                options: {
                    ...chartOptions,
                    plugins: {
                        ...chartOptions.plugins,
                        title: {
                            display: true,
                            text: 'Distribución por Áreas',
                            padding: { top: 10, bottom: 20 }
                        }
                    }
                }
            });
        }
    }

    // Gráfico de Distribución por Tipo de Colegio
    function actualizarTipoColegioChart(labels, data) {
        // Si no hay datos, muestra "Sin información"
        if (!labels || labels.length === 0) {
            labels = ['Sin información'];
            data = [1];
        }

        // Paleta de colores para los tipos de colegio
        const colegioColors = [
            colors.primary,
            colors.success,
            colors.warning,
            colors.info,
            colors.purple,
            colors.pink,
            colors.danger,
            colors.secondary
        ];

        // Si hay más labels que colores, repetir la paleta
        const backgroundColors = labels.map((_, i) => colegioColors[i % colegioColors.length]);

        if (tipoColegioChart) {
            tipoColegioChart.data.labels = labels;
            tipoColegioChart.data.datasets[0].data = data;
            tipoColegioChart.data.datasets[0].backgroundColor = backgroundColors;
            tipoColegioChart.update();
        } else {
            const ctx = document.getElementById('tipoColegioChart').getContext('2d');
            tipoColegioChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColors
                    }]
                },
                options: {
                    ...chartOptions
                }
            });
        }
    }

    // Gráfico de Niveles de Participación
    function actualizarGradosChart(labels, data) {
        // Si no hay datos, muestra "Sin información"
        if (!labels || labels.length === 0) {
            labels = ['Sin información'];
            data = [1];
        }

        // Paleta de colores para los grados (puedes agregar más si tienes más grados)
        const gradoColors = [
            colors.info,
            colors.primary,
            colors.success,
            colors.warning,
            colors.purple,
            colors.pink,
            colors.danger,
            colors.secondary
        ];
        const backgroundColors = labels.map((_, i) => gradoColors[i % gradoColors.length]);

        if (gradosChart) {
            gradosChart.data.labels = labels;
            gradosChart.data.datasets[0].data = data;
            gradosChart.data.datasets[0].backgroundColor = backgroundColors;
            gradosChart.update();
        } else {
            const ctx = document.getElementById('nivelesChart').getContext('2d');
            gradosChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColors
                    }]
                },
                options: {
                    ...chartOptions
                }
            });
        }
    }

    // Gráfico de Distribución por Género
    function actualizarGeneroChart(masculino, femenino) {
        let labels, data, backgroundColors;

        // Si ambos valores son 0 o no hay datos, muestra "Sin información"
        if ((!masculino && !femenino) || (masculino === 0 && femenino === 0)) {
            labels = ['Sin información'];
            data = [1];
            backgroundColors = [colors.secondary];
        } else {
            labels = ['Masculino', 'Femenino'];
            data = [masculino, femenino];
            backgroundColors = [colors.primary, colors.pink];
        }

        if (generoChart) {
            generoChart.data.labels = labels;
            generoChart.data.datasets[0].data = data;
            generoChart.data.datasets[0].backgroundColor = backgroundColors;
            generoChart.update();
        } else {
            const ctx = document.getElementById('generoChart').getContext('2d');
            generoChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColors
                    }]
                },
                options: {
                    ...chartOptions
                }
            });
        }
    }

    // Gráfico de Participación por Departamento
    function actualizarDepartamentosChart(labels, data) {
        const backgroundColors = [
            'rgba(26, 54, 93, 0.8)',     // La Paz
            'rgba(44, 82, 130, 0.8)',    // Santa Cruz
            'rgba(72, 149, 239, 0.8)',   // Cochabamba
            'rgba(32, 187, 255, 0.8)',   // Potosí
            'rgba(0, 42, 76, 0.8)',      // Chuquisaca
            'rgba(99, 26, 51, 0.8)',     // Oruro
            'rgba(255, 158, 27, 0.8)',   // Tarija
            'rgba(45, 211, 111, 0.8)',   // Beni
            'rgba(108, 117, 125, 0.8)'   // Pando
        ];
        const borderColors = [
            'rgb(26, 54, 93)',
            'rgb(44, 82, 130)',
            'rgb(72, 149, 239)',
            'rgb(32, 187, 255)',
            'rgb(0, 42, 76)',
            'rgb(99, 26, 51)',
            'rgb(255, 158, 27)',
            'rgb(45, 211, 111)',
            'rgb(108, 117, 125)'
        ];

        if (departamentosChart) {
            departamentosChart.data.labels = labels;
            departamentosChart.data.datasets[0].data = data;
            departamentosChart.update();
        } else {
            const ctx = document.getElementById('departamentosChart').getContext('2d');
            departamentosChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Participantes',
                        data: data,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    // ...tus opciones de chartOptions...
                }
            });
        }
    }
    if (select.value) {
        select.dispatchEvent(new Event('change'));
    }
});