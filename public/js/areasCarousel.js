document.addEventListener('DOMContentLoaded', function() {
    const areasGrid = document.querySelector('.areas-grid');
    const prevBtn = document.querySelector('.scroll-left');
    const nextBtn = document.querySelector('.scroll-right');
    const scrollAmount = 300;

    prevBtn.addEventListener('click', () => {
        areasGrid.scrollBy({
            left: -scrollAmount,
            behavior: 'smooth'
        });
    });

    nextBtn.addEventListener('click', () => {
        areasGrid.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
    });

    // Hide/show buttons based on scroll position
    areasGrid.addEventListener('scroll', () => {
        prevBtn.style.opacity = areasGrid.scrollLeft <= 0 ? '0.5' : '1';
        prevBtn.style.cursor = areasGrid.scrollLeft <= 0 ? 'not-allowed' : 'pointer';
        
        const maxScroll = areasGrid.scrollWidth - areasGrid.clientWidth;
        nextBtn.style.opacity = areasGrid.scrollLeft >= maxScroll ? '0.5' : '1';
        nextBtn.style.cursor = areasGrid.scrollLeft >= maxScroll ? 'not-allowed' : 'pointer';
    });
});