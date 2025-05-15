// Este archivo de depuración puede ayudarnos a verificar los índices correctos
// Añadir esto al archivo inscripcionExcel.js para depurar la asignación de columnas

// Ejemplo de uso:
// Dentro de la función validateRow, justo antes de return isValid, agregar:
// console.log('Información de columnas para fila ' + rowIndex);
// console.log('hasDelegacion:', hasDelegacion);
// console.log('Indices - Email Tutor:', tutorEmailIndex, 'Modalidad:', modalidadIndex, 'Código:', codigoIndex);
// console.log('Valores - Email Tutor:', rowData[tutorEmailIndex], 'Modalidad:', rowData[modalidadIndex], 'Código:', rowData[codigoIndex]);
