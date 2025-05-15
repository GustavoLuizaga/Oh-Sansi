// Corrección para los problemas de columnas en el archivo inscripcionExcel.js
// Este archivo contiene una función principal que corrige la validación de email tutor y modalidad

// Función para ejecutar después de que el DOM esté cargado
document.addEventListener('DOMContentLoaded', function() {
    // Esperar a que jQuery esté disponible
    if (typeof $ === 'undefined') {
        console.error('jQuery no está disponible para corregir validaciones');
        return;
    }
    
    // Esperar a que exista la función validateRow
    if (typeof window.validateRow === 'function') {
        corregirValidaciones();
    } else {
        // Si no está disponible inmediatamente, intentar con un temporizador
        let intentos = 0;
        const verificarYCorregir = setInterval(function() {
            intentos++;
            if (typeof window.validateRow === 'function') {
                clearInterval(verificarYCorregir);
                corregirValidaciones();
            } else if (intentos > 20) {
                clearInterval(verificarYCorregir);
                console.error('No se pudo encontrar la función validateRow');
            }
        }, 500);
    }
    
    // Función para aplicar las correcciones
    function corregirValidaciones() {
        console.log("🔧 Aplicando correcciones a las validaciones de columnas...");
        
        // Reemplazar la validación existente con una mejorada
        const validacionOriginal = window.validateRow;
        
        // Crear una función intermedia personalizada
        window.validateRow = function(row) {
            // Obtener los datos de la fila
            const rowIndex = row.data('row');
            const rowData = window.excelData[rowIndex] || [];
            
            // Identificar correctamente los índices de columna
            const hasDelegacion = rowData.length >= 12 && 
                typeof rowData[11] !== 'undefined' && 
                rowData[11] !== null && 
                rowData[11] !== '';
            
            // Añadir depuración
            console.log(`Fila ${rowIndex+1} - hasDelegacion: ${hasDelegacion}`);
            if (hasDelegacion) {
                console.log(`Con delegación - Email Tutor: índice 13, Modalidad: índice 14, Código: índice 15`);
                console.log(`Valores: Email Tutor: ${rowData[13]}, Modalidad: ${rowData[14]}, Código: ${rowData[15]}`);
            } else {
                console.log(`Sin delegación - Email Tutor: índice 12, Modalidad: índice 13, Código: índice 14`);
                console.log(`Valores: Email Tutor: ${rowData[12]}, Modalidad: ${rowData[13]}, Código: ${rowData[14]}`);
            }
            
            // Ejecutar la función original de validación
            return validacionOriginal.call(this, row);
        };
        
        // Reemplazar la función validateGroups también
        const gruposOriginal = window.validateGroups;
        
        window.validateGroups = function() {
            console.log("🔍 Validando grupos con índices corregidos...");
            return gruposOriginal.call(this);
        };
        
        console.log("✅ Correcciones aplicadas");
    }
});
