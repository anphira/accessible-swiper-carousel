<?php
/**
 * Accessible Swiper Carousel Widget Class
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ASC_Accessible_Swiper_Carousel extends \Elementor\Widget_Base {

    public function get_name() {
        return 'accessible_swiper_carousel';
    }

    public function get_title() {
        return __('Accessible Carousel', 'accessible-swiper-carousel');
    }

    public function get_icon() {
        return 'eicon-slider-3d';
    }

    public function get_categories() {
        return ['general'];
    }

    public function get_keywords() {
        return ['carousel', 'slider', 'swiper', 'accessible'];
    }

    protected function register_controls() {
        
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Carousel Settings', 'accessible-swiper-carousel'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'carousel_label',
            [
                'label' => __('Carousel Label', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Featured content carousel', 'accessible-swiper-carousel'),
                'description' => __('Accessible label for screen readers', 'accessible-swiper-carousel'),
            ]
        );

        $this->add_control(
            'slides_per_view',
            [
                'label' => __('Slides Per View', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 1,
                'max' => 6,
            ]
        );

        $this->add_control(
            'space_between',
            [
                'label' => __('Space Between Slides', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 20,
                'min' => 0,
                'max' => 100,
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => __('Autoplay', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'no',
            ]
        );

        $this->add_control(
            'autoplay_delay',
            [
                'label' => __('Autoplay Delay (ms)', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3000,
                'condition' => [
                    'autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'loop',
            [
                'label' => __('Loop', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_navigation',
            [
                'label' => __('Show Navigation', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label' => __('Show Pagination', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Responsive Section
        $this->start_controls_section(
            'responsive_section',
            [
                'label' => __('Responsive Settings', 'accessible-swiper-carousel'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'tablet_slides',
            [
                'label' => __('Tablet Slides Per View', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 2,
                'min' => 1,
                'max' => 4,
            ]
        );

        $this->add_control(
            'slides_content',
            [
                'label' => __('Slides', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => [
                    [
                        'name' => 'slide_content',
                        'label' => __('Slide Content', 'accessible-swiper-carousel'),
                        'type' => \Elementor\Controls_Manager::WYSIWYG,
                        'default' => __('<h3>Slide Title</h3><p>Add your content here.</p>', 'accessible-swiper-carousel'),
                    ],
                ],
                'default' => [
                    [
                        'slide_content' => __('<h3>Slide 1</h3><p>Add your content here by editing this slide.</p>', 'accessible-swiper-carousel'),
                    ],
                    [
                        'slide_content' => __('<h3>Slide 2</h3><p>Add your content here by editing this slide.</p>', 'accessible-swiper-carousel'),
                    ],
                    [
                        'slide_content' => __('<h3>Slide 3</h3><p>Add your content here by editing this slide.</p>', 'accessible-swiper-carousel'),
                    ],
                ],
                'title_field' => __('Slide', 'accessible-swiper-carousel'),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        
        $swiper_config = [
            'slidesPerView' => $settings['slides_per_view'],
            'spaceBetween' => $settings['space_between'],
            'loop' => $settings['loop'] === 'yes',
            'keyboard' => ['enabled' => true],
            'a11y' => [
                'enabled' => true,
                'prevSlideMessage' => __('Previous slide', 'accessible-swiper-carousel'),
                'nextSlideMessage' => __('Next slide', 'accessible-swiper-carousel'),
            ],
            'breakpoints' => [
                768 => [
                    'slidesPerView' => $settings['tablet_slides']
                ],
                480 => [
                    'slidesPerView' => $settings['mobile_slides']
                ]
            ]
        ];

        if ($settings['autoplay'] === 'yes') {
            $swiper_config['autoplay'] = [
                'delay' => $settings['autoplay_delay'],
                'pauseOnMouseEnter' => true,
                'disableOnInteraction' => false
            ];
        }

        if ($settings['show_navigation'] === 'yes') {
            $swiper_config['navigation'] = [
                'nextEl' => '.swiper-button-next-' . $widget_id,
                'prevEl' => '.swiper-button-prev-' . $widget_id
            ];
        }

        if ($settings['show_pagination'] === 'yes') {
            $swiper_config['pagination'] = [
                'el' => '.swiper-pagination-' . $widget_id,
                'clickable' => true,
                'type' => 'bullets'
            ];
        }

        $this->add_render_attribute('carousel-wrapper', [
            'class' => 'accessible-swiper-carousel',
            'data-swiper-config' => wp_json_encode($swiper_config),
            'data-widget-id' => $widget_id
        ]);
        ?>

        <div <?php echo $this->get_render_attribute_string('carousel-wrapper'); ?>>
            <div class="swiper swiper-<?php echo $widget_id; ?>" 
                 role="region" 
                 aria-label="<?php echo esc_attr($settings['carousel_label']); ?>"
                 aria-live="polite">
                
                <div class="swiper-wrapper">
                    <?php
                    $slides = $settings['slides_content'];
                    if (!empty($slides)) {
                        foreach ($slides as $index => $slide) {
                            echo '<div class="swiper-slide" role="group" aria-label="' . sprintf(__('Slide %d', 'accessible-swiper-carousel'), $index + 1) . '">';
                            echo '<div class="slide-content">';
                            echo $slide['slide_content'];
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        // Fallback slides
                        for ($i = 1; $i <= 3; $i++) {
                            echo '<div class="swiper-slide" role="group" aria-label="' . sprintf(__('Slide %d', 'accessible-swiper-carousel'), $i) . '">';
                            echo '<div class="slide-content">';
                            echo '<h3>' . sprintf(__('Slide %d', 'accessible-swiper-carousel'), $i) . '</h3>';
                            echo '<p>' . __('Add your content here by editing this carousel widget.', 'accessible-swiper-carousel') . '</p>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>

                <?php if ($settings['show_pagination'] === 'yes'): ?>
                    <div class="swiper-pagination swiper-pagination-<?php echo $widget_id; ?>" 
                         role="tablist" 
                         aria-label="<?php echo esc_attr__('Carousel pagination', 'accessible-swiper-carousel'); ?>"></div>
                <?php endif; ?>

                <?php if ($settings['show_navigation'] === 'yes'): ?>
                    <button class="swiper-button-prev swiper-button-prev-<?php echo $widget_id; ?>" 
                            aria-label="<?php echo esc_attr__('Previous slide', 'accessible-swiper-carousel'); ?>" 
                            type="button">
                        <span aria-hidden="true">‹</span>
                    </button>
                    <button class="swiper-button-next swiper-button-next-<?php echo $widget_id; ?>" 
                            aria-label="<?php echo esc_attr__('Next slide', 'accessible-swiper-carousel'); ?>" 
                            type="button">
                        <span aria-hidden="true">›</span>
                    </button>
                <?php endif; ?>
            </div>

            <?php if ($settings['autoplay'] === 'yes'): ?>
                <div class="carousel-controls">
                    <button class="pause-play-btn" 
                            data-widget-id="<?php echo $widget_id; ?>"
                            aria-label="<?php echo esc_attr__('Pause carousel', 'accessible-swiper-carousel'); ?>">
                        <span class="pause-text"><?php echo __('Pause', 'accessible-swiper-carousel'); ?></span>
                        <span class="play-text" style="display: none;"><?php echo __('Play', 'accessible-swiper-carousel'); ?></span>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <?php
    }
}