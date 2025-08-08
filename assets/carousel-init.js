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
        
        // Function to manage slide visibility for screen readers
        function updateSlideVisibility() {
            const slides = swiper.slides;
            const activeIndex = swiper.activeIndex;
            const slidesPerView = swiper.params.slidesPerView === 'auto' ? 
                swiper.slidesPerViewDynamic() : swiper.params.slidesPerView;
            
            slides.forEach(function(slide, index) {
                // Determine if slide is currently visible
                const isVisible = index >= activeIndex && index < (activeIndex + slidesPerView);
                
                if (isVisible) {
                    // Make visible slides accessible
                    slide.removeAttribute('aria-hidden');
                    slide.removeAttribute('inert');
                    
                    // Restore focus to content within visible slides
                    const focusableElements = slide.querySelectorAll('a, button, input, textarea, select, [tabindex]');
                    focusableElements.forEach(function(el) {
                        const originalTabIndex = el.dataset.originalTabindex;
                        if (originalTabIndex !== undefined) {
                            el.setAttribute('tabindex', originalTabIndex);
                            delete el.dataset.originalTabindex;
                        }
                    });
                } else {
                    // Hide non-visible slides from screen readers
                    slide.setAttribute('aria-hidden', 'true');
                    
                    // Store original tabindex and remove from tab order
                    const focusableElements = slide.querySelectorAll('a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])');
                    focusableElements.forEach(function(el) {
                        if (!el.dataset.originalTabindex) {
                            el.dataset.originalTabindex = el.getAttribute('tabindex') || '0';
                        }
                        el.setAttribute('tabindex', '-1');
                    });
                }
            });
        }
        
        // Wait for Swiper to fully initialize before managing visibility
        swiper.on('init', function() {
            setTimeout(updateSlideVisibility, 100);
        });
        
        // Accessibility enhancements
        swiper.on('slideChange', function() {
            const totalSlides = swiper.slides.length;
            
            // Update slide visibility after slide change
            setTimeout(updateSlideVisibility, 50);
            
            // Announce slide change to screen readers
            const announcement = document.createElement('div');
            announcement.setAttribute('aria-live', 'polite');
            announcement.setAttribute('aria-atomic', 'true');
            announcement.className = 'sr-only';
            announcement.textContent = 'Slide ' + (swiper.activeIndex + 1) + ' of ' + totalSlides + ' visible';
            
            document.body.appendChild(announcement);
            setTimeout(function() {
                if (document.body.contains(announcement)) {
                    document.body.removeChild(announcement);
                }
            }, 1500);
        });
        
        // Update visibility when slides are updated
        swiper.on('slidesUpdated', function() {
            setTimeout(updateSlideVisibility, 50);
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