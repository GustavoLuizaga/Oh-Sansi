
<!-- filepath: resources/views/auth/welcome-animation.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenida OH SANSI</title>
    <link rel="stylesheet" href="/css/loginAnimacion.css">
</head>
<body>
    <div id="welcome-animation" style="display:flex; position:fixed; inset:0; z-index:9999; background:#111;">
        <div class="welcome-container">
            <div class="circuit" id="circuit"></div>
            <div class="logo" id="logo">
                <span class="letter oh-letter">!</span>
                <span class="letter oh-letter">O</span>
                <span class="letter oh-letter">H</span>
                <span class="letter sansi-letter">S</span>
                <span class="letter sansi-letter">A</span>
                <span class="letter sansi-letter">N</span>
                <span class="letter sansi-letter">S</span>
                <span class="letter sansi-letter">I</span>
            </div>
            <div class="subtitle" id="subtitle">Inicializando sistema...</div>
            <div class="loading-bar" id="loadingBar"></div>
        </div>
    </div>
    <script src="/js/loginAnimacion.js"></script>
    <script>
        // Redirigir automáticamente después de la animación
        setTimeout(function() {
            window.location.href = "{{ $redirectTo }}";
        }, 5000); // Ajusta el tiempo según la duración de tu animación
    </script>
</body>
</html>