document.addEventListener('DOMContentLoaded', function() {
    const toggles = document.querySelectorAll('.footer-toggle');
    
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            // Toggle la clase active en el botón
            this.classList.toggle('active');
            
            // Encuentra el siguiente elemento (el contenido colapsable)
            const content = this.nextElementSibling;
            if (content.classList.contains('show')) {
                content.classList.remove('show');
            } else {
                content.classList.add('show');
            }
        });
    });
});


// Mostrar Premios
function abrirModalPremios() {
    document.getElementById('footerPremios').style.display = 'block';
}

// Mostrar Resultados
function abrirModalResultados() {
    document.getElementById('footerResultados').style.display = 'block';
}

function abrirFooterModal(id) {
  document.getElementById(id).style.display = 'flex';
}
function cerrarFooterModal(id) {
  document.getElementById(id).style.display = 'none';
}
// Cerrar al hacer clic fuera del contenido
document.addEventListener('mousedown', function(e) {
  ['footerCategorias','footerCalendario','footerPremios','footerResultados'].forEach(id => {
    const modal = document.getElementById(id);
    if (modal && modal.style.display === 'flex') {
      const content = modal.querySelector('.footer-modal-content');
      if (content && !content.contains(e.target)) {
        modal.style.display = 'none';
      }
    }
  });
});
// Mostrar Guía del Participante
function abrirFooterModal(id) {
  document.getElementById(id).style.display = 'flex';
}
function cerrarFooterModal(id) {
  document.getElementById(id).style.display = 'none';
}
document.addEventListener('mousedown', function(e) {
  ['footerCategorias','footerCalendario','footerPremios','footerResultados','footerGuiaParticipante'].forEach(id => {
    const modal = document.getElementById(id);
    if (modal && modal.style.display === 'flex') {
      const content = modal.querySelector('.footer-modal-content');
      if (content && !content.contains(e.target)) {
        modal.style.display = 'none';
      }
    }
  });
});