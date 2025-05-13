// filepath: c:\xampp1\htdocs\LARAVEL2025\Oh-Sansi\public\js\dashboard.js
document.addEventListener('DOMContentLoaded', function() {
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
    new Chart(document.getElementById('areasChart'), {
        type: 'pie',
        data: {
            labels: ['Matemáticas', 'Física', 'Química', 'Biología', 'Informática'],
            datasets: [{
                data: [35, 20, 15, 18, 12],
                backgroundColor: [colors.primary, colors.success, colors.warning, colors.info, colors.purple]
            }]
        },
        options: {
            ...chartOptions,
            plugins: {
                ...chartOptions.plugins,
                title: {
                    display: true,
                    text: 'Distribución por Áreas',
                    padding: {
                        top: 10,
                        bottom: 20
                    }
                }
            }
        }
    });

    // Gráfico de Distribución por Tipo de Colegio
    new Chart(document.getElementById('tipoColegioChart'), {
        type: 'doughnut',
        data: {
            labels: ['Fiscal', 'Particular', 'Convenio'],
            datasets: [{
                data: [45, 35, 20],
                backgroundColor: [colors.primary, colors.success, colors.warning]
            }]
        },
        options: {
            ...chartOptions
        }
    });

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
                        label: function(context) {
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
                        callback: function(value) {
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