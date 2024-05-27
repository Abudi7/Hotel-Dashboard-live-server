document.addEventListener('DOMContentLoaded', () => {
    const nextButton = document.querySelector('.next');
    const prevButton = document.querySelector('.prev');
    const slides = document.querySelectorAll('.slide');
    let currentSlide = 0;

    function moveSlide(direction) {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + direction + slides.length) % slides.length;
        slides[currentSlide].classList.add('active');
        updateSlides();
    }

    function updateSlides() {
        const slidesContainer = document.querySelector('.slides');
        slidesContainer.style.transform = `translateX(-${currentSlide * 100}%)`;
    }

    nextButton.addEventListener('click', () => moveSlide(1));
    prevButton.addEventListener('click', () => moveSlide(-1));

    slides[currentSlide].classList.add('active');
});
