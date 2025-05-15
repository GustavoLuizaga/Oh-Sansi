/**
 * excelUploadInfo.js - Maneja la funcionalidad para mostrar/ocultar la información de carga de Excel
 */
document.addEventListener('DOMContentLoaded', function() {
    // Elementos del colapsable
    const excelInfoHeader = document.getElementById('excelInfoHeader');
    const excelInfoContent = document.getElementById('excelInfoContent');

    // Si los elementos existen, añadir el listener para colapsar/expandir
    if (excelInfoHeader && excelInfoContent) {
        excelInfoHeader.addEventListener('click', function() {
            // Alternar la clase active en el header
            this.classList.toggle('active');
            
            // Alternar la clase open en el contenido
            excelInfoContent.classList.toggle('open');
        });
    }
});
