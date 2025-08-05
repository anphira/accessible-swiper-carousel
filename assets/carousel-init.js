document.addEventListener("DOMContentLoaded", function() {
    initializeAccessibleCarousels();
});

// Also initialize when Elementor editor loads
document.addEventListener("elementor/popup/show", function() {
    setTimeout(initializeAccessibleCarousels, 100);
});

function initializeAccessibleCarousels() {
    const carousels = document.querySelectorAll(".accessible-swiper-carousel");
    
    carousels.forEach(function(carousel) {
        // Skip if already initialized
        if (carousel.classList.contains('swiper-initialized')) {
            return;
        }
        
        const config = JSON.parse(carousel.dataset.swiperConfig);
        const widgetId = carousel.dataset.widgetId;
        const swiperElement = carousel.querySelector('.swiper');
        
        if (!swiperElement) return;
        
        const swiper = new Swiper(swiperElement, config);
        
        // Accessibility enhancements
        swiper.on('slideChange', function() {
            const activeSlide = swiper.slides[swiper.activeIndex];
            const totalSlides = swiper.slides.length;
            
            if (activeSlide) {
                activeSlide.setAttribute('aria-label', 
                    'Slide ' + (swiper.realIndex + 1) + ' of ' + totalSlides);
            }
            
            // Announce slide change to screen readers
            const announcement = document.createElement('div');
            announcement.setAttribute('aria-live', 'polite');
            announcement.setAttribute('aria-atomic', 'true');
            announcement.className = 'sr-only';
            announcement.textContent = 'Showing slide ' + (swiper.realIndex + 1) + ' of ' + totalSlides;
            
            document.body.appendChild(announcement);
            setTimeout(function() {
                document.body.removeChild(announcement);
            }, 1000);
        });
        
        // Pause on focus within carousel
        swiperElement.addEventListener('focusin', function() {
            if (swiper.autoplay && swiper.autoplay.running) {
                swiper.autoplay.stop();
            }
        });
        
        swiperElement.addEventListener('focusout', function() {
            if (swiper.autoplay && !swiper.autoplay.running && config.autoplay) {
                setTimeout(function() {
                    if (!swiperElement.contains(document.activeElement)) {
                        swiper.autoplay.start();
                    }
                }, 100);
            }
        });
        
        // Pause/Play functionality
        const pauseBtn = carousel.querySelector('.pause-play-btn');
        if (pauseBtn && config.autoplay) {
            let isPaused = false;
            
            pauseBtn.addEventListener('click', function() {
                if (isPaused) {
                    swiper.autoplay.start();
                    pauseBtn.querySelector('.pause-text').style.display = 'inline';
                    pauseBtn.querySelector('.play-text').style.display = 'none';
                    pauseBtn.setAttribute('aria-label', 'Pause carousel');
                } else {
                    swiper.autoplay.stop();
                    pauseBtn.querySelector('.pause-text').style.display = 'none';
                    pauseBtn.querySelector('.play-text').style.display = 'inline';
                    pauseBtn.setAttribute('aria-label', 'Play carousel');
                }
                isPaused = !isPaused;
            });
        }
        
        // Add keyboard support for pagination dots
        const paginationBullets = carousel.querySelectorAll('.swiper-pagination-bullet');
        paginationBullets.forEach(function(bullet, index) {
            bullet.setAttribute('role', 'tab');
            bullet.setAttribute('aria-label', 'Go to slide ' + (index + 1));
            
            bullet.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    swiper.slideTo(index);
                }
            });
        });
    });
}

// Screen reader only class
if (!document.querySelector('style[data-asc-sr-only]')) {
    const style = document.createElement('style');
    style.setAttribute('data-asc-sr-only', 'true');
    style.textContent = `
        .sr-only {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
    `;
    document.head.appendChild(style);
}