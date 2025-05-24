let currentSlide = 0;
const totalSlides = document.querySelectorAll('.slide').length;
let slideInterval;

function updateSlider() {
    const sliderWrapper = document.getElementById('sliderWrapper');
    const dots = document.querySelectorAll('.nav-dot');
    
    if (sliderWrapper) {
        const translateX = -(currentSlide * (100 / totalSlides));
        sliderWrapper.style.transform = `translateX(${translateX}%)`;
        
        // Update dots
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
    }
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    updateSlider();
    resetAutoSlide();
}

function previousSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    updateSlider();
    resetAutoSlide();
}

function goToSlide(index) {
    currentSlide = index;
    updateSlider();
    resetAutoSlide();
}

function startAutoSlide() {
    slideInterval = setInterval(nextSlide, 5000); // Auto slide every 5 seconds
}

function resetAutoSlide() {
    clearInterval(slideInterval);
    startAutoSlide();
}

// Initialize slider when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (totalSlides > 1) {
        updateSlider();
        startAutoSlide();
        
        // Pause auto-slide on hover
        const slider = document.querySelector('.main-story-featured');
        if (slider) {
            slider.addEventListener('mouseenter', () => clearInterval(slideInterval));
            slider.addEventListener('mouseleave', startAutoSlide);
        }
    }
});

// Add keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowLeft') {
        previousSlide();
    } else if (e.key === 'ArrowRight') {
        nextSlide();
    }
});