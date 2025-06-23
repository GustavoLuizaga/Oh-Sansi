document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.login-form');
    form.addEventListener('submit', function(e) {
        // Mostrar animación y ocultar el formulario
        document.querySelector('.login-container').style.display = 'none';
        document.getElementById('welcome-animation').style.display = 'flex';
        // El formulario se enviará normalmente, la animación se verá mientras se procesa el login
    });
});


document.addEventListener('DOMContentLoaded', () => {
    const letters = document.querySelectorAll('.letter');
    const subtitle = document.getElementById('subtitle');
    const loadingBar = document.getElementById('loadingBar');
    const circuit = document.getElementById('circuit');

    // Crear efecto de circuito
    createCircuit();

    // Animación letra por letra
    letters.forEach((letter, index) => {
        setTimeout(() => {
            letter.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
            letter.style.opacity = '1';
            letter.style.transform = 'scale(1)';
            
            // Efecto de "salto" para cada letra
            setTimeout(() => {
                letter.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    letter.style.transform = 'translateY(0)';
                }, 150);
            }, 500);
            
            // Cuando la última letra aparece
            if(index === letters.length - 1) {
                setTimeout(() => {
                    // Animación de pulso para todo el logo
                    document.getElementById('logo').style.animation = 'pulse 2s ease-in-out infinite';
                    
                    // Mostrar subtítulo
                    subtitle.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
                    subtitle.style.opacity = '0.8';
                    subtitle.style.transform = 'translateY(0)';
                    
                    // Mostrar barra de carga
                    loadingBar.style.transition = 'opacity 0.5s ease-out, width 2.5s ease-out';
                    loadingBar.style.opacity = '1';
                    loadingBar.style.width = '100%';
                    
                    // Cambiar texto del subtítulo cuando la barra está completa
                    setTimeout(() => {
                        subtitle.textContent = '¡Sistema listo!';
                        // Mostrar "BIENVENIDO" después de 0.7 segundos
                        setTimeout(() => {
                            subtitle.textContent = 'BIENVENIDO';
                            // Simular redirección o cierre después de 0.7 segundos más
                            setTimeout(() => {
                                document.querySelector('.welcome-container').style.opacity = '0';
                                document.querySelector('.welcome-container').style.transition = 'opacity 0.5s ease-out';
                            }, 700);
                        }, 700);
                    }, 1200); // <-- tiempo más corto
                }, 300);
            }
        }, index * 200); // Retraso entre cada letra
    });

    function createCircuit() {
        const circuitContainer = document.getElementById('circuit');
        const lineCount = 20;
        for(let i = 0; i < lineCount; i++) {
            const line = document.createElement('div');
            line.classList.add('circuit-line');
            const startX = Math.random() * 100;
            const startY = Math.random() * 100;
            const length = Math.random() * 30 + 10;
            const angle = Math.random() * 360;
            line.style.width = `${length}%`;
            line.style.left = `${startX}%`;
            line.style.top = `${startY}%`;
            line.style.transform = `rotate(${angle}deg) scaleX(0)`;
            circuitContainer.appendChild(line);
            setTimeout(() => {
                line.style.transition = `transform ${Math.random() * 2 + 1}s ease-in-out`;
                line.style.transform = `rotate(${angle}deg) scaleX(1)`;
                setTimeout(() => {
                    line.style.opacity = '0';
                    line.style.transition = 'opacity 0.5s ease-out';
                }, 2000);
            }, Math.random() * 2000);
        }
        setInterval(createCircuit, 1000);
    }
});