// filepath: c:\xampp1\htdocs\LARAVEL2025\Oh-Sansi\public\js\dashboard.js
document.addEventListener('DOMContentLoaded', function () {

    const select = document.getElementById('convocatoriaFilter');
    const totalEstudiantes = document.getElementById('totalEstudiantes');
    const totalTutores = document.getElementById('totalTutores');
    let areasChart; // Variable para el gráfico de áreas
    let tipoColegioChart; 

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
    new Chart(document.getElementById('nivelesChart'), {
        type: 'pie',
        data: {
            labels: ['Primaria', 'Secundaria'],
            datasets: [{
                data: [40, 60],
                backgroundColor: [colors.info, colors.primary]
            }]
        },
        options: {
            ...chartOptions
        }
    });

    // Gráfico de Distribución por Género
    new Chart(document.getElementById('generoChart'), {
        type: 'doughnut',
        data: {
            labels: ['Masculino', 'Femenino'],
            datasets: [{
                data: [52, 48],
                backgroundColor: [colors.primary, colors.pink]
            }]
        },
        options: {
            ...chartOptions
        }
    });

    // Gráfico de Participación por Departamento
    new Chart(document.getElementById('departamentosChart'), {
        type: 'bar',
        data: {
            labels: [
                'La Paz',
                'Santa Cruz',
                'Cochabamba',
                'Potosí',
                'Chuquisaca',
                'Oruro',
                'Tarija',
                'Beni',
                'Pando'
            ],
            datasets: [{
                label: 'Participantes',
                data: [850, 920, 780, 450, 380, 320, 290, 180, 120],
                backgroundColor: [
                    'rgba(26, 54, 93, 0.8)',     // La Paz
                    'rgba(44, 82, 130, 0.8)',    // Santa Cruz
                    'rgba(72, 149, 239, 0.8)',   // Cochabamba
                    'rgba(32, 187, 255, 0.8)',   // Potosí
                    'rgba(0, 42, 76, 0.8)',      // Chuquisaca
                    'rgba(99, 26, 51, 0.8)',     // Oruro
                    'rgba(255, 158, 27, 0.8)',   // Tarija
                    'rgba(45, 211, 111, 0.8)',   // Beni
                    'rgba(108, 117, 125, 0.8)'   // Pando
                ],
                borderColor: [
                    'rgb(26, 54, 93)',
                    'rgb(44, 82, 130)',
                    'rgb(72, 149, 239)',
                    'rgb(32, 187, 255)',
                    'rgb(0, 42, 76)',
                    'rgb(99, 26, 51)',
                    'rgb(255, 158, 27)',
                    'rgb(45, 211, 111)',
                    'rgb(108, 117, 125)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Participación por Departamento',
                    color: 'var(--text-color)',
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y + ' estudiantes';
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    },
                    ticks: {
                        color: 'var(--text-color)',
                        callback: function (value) {
                            return value + ' est.';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: 'var(--text-color)'
                    }
                }
            }
        }
    });
});