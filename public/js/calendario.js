/**
 * Calendario de Convocatorias
 * Este script maneja la visualización de convocatorias en el calendario del sidebar derecho
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el calendario cuando el DOM esté completamente cargado
    initCalendar();
});

/**
 * Inicializa el calendario y carga las convocatorias publicadas
 */
function initCalendar() {
    // Obtener el contenedor del calendario
    const calendarContainer = document.querySelector('.calendario');
    if (!calendarContainer) return;

    // Obtener fecha actual
    const currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();

    // Cargar convocatorias publicadas
    fetchConvocatoriasPublicadas().then(convocatorias => {
        // Renderizar el calendario con las convocatorias
        renderCalendar(currentMonth, currentYear, convocatorias);

        // Configurar los botones de navegación
        setupNavigation(currentMonth, currentYear, convocatorias);
    });
}

/**
 * Configura los botones de navegación del calendario
 */
function setupNavigation(month, year, convocatorias) {
    const prevButton = document.querySelector('.calendar-nav button:first-child');
    const nextButton = document.querySelector('.calendar-nav button:last-child');
    
    if (!prevButton || !nextButton) return;

    // Botón anterior
    prevButton.addEventListener('click', function() {
        month--;
        if (month < 0) {
            month = 11;
            year--;
        }
        renderCalendar(month, year, convocatorias);
    });

    // Botón siguiente
    nextButton.addEventListener('click', function() {
        month++;
        if (month > 11) {
            month = 0;
            year++;
        }
        renderCalendar(month, year, convocatorias);
    });
}

/**
 * Renderiza el calendario para el mes y año especificados
 */
function renderCalendar(month, year, convocatorias) {
    // Nombres de los meses en español
    const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    
    // Actualizar el encabezado del calendario
    const calendarHeader = document.querySelector('.calendar-header span');
    if (calendarHeader) {
        calendarHeader.textContent = `${monthNames[month]} ${year}`;
    }

    // Obtener el primer día del mes y el número de días
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    
    // Ajustar el primer día para que la semana comience en lunes (0 = lunes, 6 = domingo)
    const firstDayAdjusted = firstDay === 0 ? 6 : firstDay - 1;
    
    // Obtener el último día del mes anterior
    const prevMonthLastDay = new Date(year, month, 0).getDate();
    
    // Limpiar el contenedor de fechas
    const datesContainer = document.querySelector('.calendar-dates');
    if (!datesContainer) return;
    
    datesContainer.innerHTML = '';

    // Días del mes anterior
    for (let i = firstDayAdjusted; i > 0; i--) {
        const dayElement = document.createElement('div');
        dayElement.className = 'date prev-month';
        dayElement.textContent = prevMonthLastDay - i + 1;
        datesContainer.appendChild(dayElement);
    }

    // Días del mes actual
    const today = new Date();
    for (let i = 1; i <= daysInMonth; i++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'date';
        dayElement.textContent = i;

        // Marcar el día actual
        if (today.getDate() === i && today.getMonth() === month && today.getFullYear() === year) {
            dayElement.classList.add('current-date');
        }

        // Verificar si este día tiene convocatorias
        const currentDate = new Date(year, month, i);
        const hasConvocatoria = checkConvocatoriaForDate(currentDate, convocatorias);
        
        if (hasConvocatoria) {
            dayElement.classList.add('event');
            dayElement.setAttribute('data-toggle', 'tooltip');
            dayElement.setAttribute('title', 'Convocatoria activa');
            
            // Agregar información sobre la convocatoria al hacer hover
            const convocatoriasForDate = getConvocatoriasForDate(currentDate, convocatorias);
            if (convocatoriasForDate.length > 0) {
                let tooltipContent = 'Convocatorias activas:<br>';
                convocatoriasForDate.forEach(conv => {
                    tooltipContent += `- ${conv.nombre}<br>`;
                });
                dayElement.setAttribute('data-bs-html', 'true');
                dayElement.setAttribute('title', tooltipContent);
            }
        }

        datesContainer.appendChild(dayElement);
    }

    // Días del mes siguiente
    const totalCells = 42; // 6 filas x 7 días
    const nextMonthDays = totalCells - (firstDayAdjusted + daysInMonth);
    
    for (let i = 1; i <= nextMonthDays; i++) {
        const dayElement = document.createElement('div');
        dayElement.className = 'date next-month';
        dayElement.textContent = i;
        datesContainer.appendChild(dayElement);
    }

    // Inicializar tooltips de Bootstrap
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

/**
 * Verifica si una fecha específica tiene convocatorias activas
 */
function checkConvocatoriaForDate(date, convocatorias) {
    return convocatorias.some(conv => {
        const fechaInicio = new Date(conv.fechaInicio);
        const fechaFin = new Date(conv.fechaFin);
        return date >= fechaInicio && date <= fechaFin;
    });
}

/**
 * Obtiene las convocatorias para una fecha específica
 */
function getConvocatoriasForDate(date, convocatorias) {
    return convocatorias.filter(conv => {
        const fechaInicio = new Date(conv.fechaInicio);
        const fechaFin = new Date(conv.fechaFin);
        return date >= fechaInicio && date <= fechaFin;
    });
}

/**
 * Obtiene las convocatorias publicadas desde el servidor
 */
async function fetchConvocatoriasPublicadas() {
    try {
        const response = await fetch('/api/convocatorias-publicadas');
        if (!response.ok) {
            throw new Error('Error al obtener convocatorias');
        }
        const data = await response.json();
        return data.convocatorias || [];
    } catch (error) {
        console.error('Error al cargar convocatorias:', error);
        return [];
    }
}