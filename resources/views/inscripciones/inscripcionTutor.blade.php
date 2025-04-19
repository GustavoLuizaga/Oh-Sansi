@push('styles')
    <link rel="stylesheet" href="{{ asset('css/inscripcion/inscripcionTutor.css') }}">
@endpush

<x-app-layout>

    <div class="token-section">
        <div class="token-container">
            <h2>Token de Inscripción</h2>
            <p class="token-description">Este es tu token único para inscribir estudiantes:</p>
            
            <div class="token-display">
                <input type="text" 
                       id="tokenInput" 
                       value="{{ $token ?? 'No hay token disponible' }}" 
                       readonly>
                <button onclick="copyToken()" class="copy-button" {{ !$token ? 'disabled' : '' }}>
                    <i class="fas fa-copy"></i>
                    Copiar
                </button>
            </div>
            
            <p class="token-info">
                <i class="fas fa-info-circle"></i>
                Comparte este token con los estudiantes que desees inscribir.
            </p>
        </div>
    </div>
</x-app-layout>

<script>
function copyToken() {
    var tokenInput = document.getElementById('tokenInput');
    tokenInput.select();
    document.execCommand('copy');
    
    // Cambiar el texto del botón temporalmente
    var copyButton = document.querySelector('.copy-button');
    var originalContent = copyButton.innerHTML;
    copyButton.innerHTML = '<i class="fas fa-check"></i> Copiado!';
    
    setTimeout(function() {
        copyButton.innerHTML = originalContent;
    }, 2000);
}
</script>