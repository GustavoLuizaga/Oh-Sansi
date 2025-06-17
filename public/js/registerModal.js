document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('register-type-modal');
    const registerBtn = document.querySelector('.register-hero-btn');
    const closeBtn = document.querySelector('.register-modal-close');

    if (registerBtn) {
        registerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        });
    }

    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    });
});