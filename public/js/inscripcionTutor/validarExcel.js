// Función principal de validación
window.validarExcel = async function(datos, idConvocatoria) {
    console.log('Iniciando validación de Excel con datos:', datos);
    const errores = [];
    
    // Validar que haya datos
    if (!datos || !Array.isArray(datos) || datos.length === 0 || Object.keys(datos[0] || {}).length === 0) {
        return {
            valido: false,
            errores: ['No hay datos para validar. Por favor, asegúrese de que el archivo Excel contiene información.']
        };
    }

    // Validar cada fila
    for (let i = 0; i < datos.length; i++) {
        const fila = datos[i];
        if (!fila) continue; // Saltar filas vacías
        
        const erroresFila = await validarFila(fila, i + 1, idConvocatoria);
        if (erroresFila.length > 0) {
            errores.push(...erroresFila.map(error => `Fila ${i + 1}: ${error}`));
        }
    }

    return {
        valido: errores.length === 0,
        errores: errores
    };
};

// Función para validar una fila individual
async function validarFila(fila, numeroFila, idConvocatoria) {
    const errores = [];

    // Validar campos requeridos
    if (!fila.nombre) {
        errores.push('Falta el nombre');
    }
    if (!fila.apellidoPaterno) {
        errores.push('Falta el apellido paterno');
    }
    if (!fila.ci) {
        errores.push('Falta el CI');
    }
    if (!fila.email) {
        errores.push('Falta el email');
    }
    if (!fila.fechaNacimiento) {
        errores.push('Falta la fecha de nacimiento');
    }
    if (!fila.genero) {
        errores.push('Falta el género');
    }
    if (!fila.area) {
        errores.push('Falta el área');
    }
    if (!fila.categoria) {
        errores.push('Falta la categoría');
    }
    if (!fila.grado) {
        errores.push('Falta el grado');
    }

    // Validar formato de campos
    if (fila.nombre && !/^[A-Za-záéíóúÁÉÍÓÚüÜñÑ\s]+$/.test(fila.nombre)) {
        errores.push('El nombre debe contener solo letras');
    }
    if (fila.apellidoPaterno && !/^[A-Za-záéíóúÁÉÍÓÚüÜñÑ\s]+$/.test(fila.apellidoPaterno)) {
        errores.push('El apellido paterno debe contener solo letras');
    }
    if (fila.apellidoMaterno && !/^[A-Za-záéíóúÁÉÍÓÚüÜñÑ\s]+$/.test(fila.apellidoMaterno)) {
        errores.push('El apellido materno debe contener solo letras');
    }
    if (fila.ci && !/^\d{7}$/.test(fila.ci)) {
        errores.push('El CI debe tener exactamente 7 dígitos');
    }
    if (fila.email && !/^[a-zA-Z0-9._%+-]+@gmail\.com$/.test(fila.email)) {
        errores.push('El email debe tener formato válido y ser @gmail.com');
    }
    if (fila.genero && !['M', 'F'].includes(fila.genero.toUpperCase())) {
        errores.push('El género debe ser "M" o "F"');
    }    if (!fila.numeroContacto) {
        errores.push('Falta el número de contacto');
    } else if (!/^\d{8}$/.test(fila.numeroContacto)) {
        errores.push('El número de contacto debe tener exactamente 8 dígitos');
    }

    // Validar modalidad y código de grupo
    if (fila.modalidad) {
        const modalidad = fila.modalidad.toString().toLowerCase();
        if (!['individual', 'duo', 'equipo'].includes(modalidad)) {
            errores.push('La modalidad debe ser "Individual", "Duo" o "Equipo"');
        } else if ((modalidad === 'duo' || modalidad === 'equipo') && !fila.codigoGrupo) {
            errores.push('Falta el código de invitación para modalidad ' + fila.modalidad);
        }
    }    // Si hay errores básicos, no continuar con la validación del servidor
    if (errores.length > 0) {
        return errores;
    }

    // Validar área y categoría con el backend
    try {
        if (!idConvocatoria) {
            throw new Error('No se ha especificado el ID de la convocatoria');
        }
        
        const token = document.querySelector('meta[name="csrf-token"]');
        if (!token) {
            throw new Error('Token CSRF no encontrado');
        }

        const response = await fetch('/validar-configuracion-inscripcion', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token.content
            },
            body: JSON.stringify({
                area: fila.area,
                categoria: fila.categoria,
                grado: fila.grado,
                idConvocatoria: idConvocatoria
            })
        });

        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }        const result = await response.json();
        console.log('Respuesta de validación:', result);
        if (!result.valido) {
            errores.push(result.mensaje || 'Error de validación no especificado');
        }
    } catch (error) {
        console.error('Error al validar área y categoría:', error);
        errores.push('Error al validar área y categoría: ' + error.message);
    }

    return errores;
}

// Exportar funciones
window.validarFila = validarFila;
