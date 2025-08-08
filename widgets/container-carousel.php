<?php
/**
 * Container-Style Accessible Swiper Carousel Widget Class
 * This version allows drag-and-drop content
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ASC_Container_Carousel extends \Elementor\Widget_Base {

    public function get_name() {
        return 'accessible_container_carousel';
    }

    public function get_title() {
        return __('Accessible Container Carousel', 'accessible-swiper-carousel');
    }

    public function get_icon() {
        return 'eicon-carousel';
    }

    public function get_categories() {
        return ['general'];
    }

    public function get_keywords() {
        return ['carousel', 'slider', 'swiper', 'accessible', 'container'];
    }

    // Enable content editing in the widget
    public function is_dynamic_content(): bool {
        return false;
    }

    protected function register_controls() {
        
        // Instructions Section
        $this->start_controls_section(
            'instructions_section',
            [
                'label' => __('Instructions', 'accessible-swiper-carousel'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'instructions',
            [
                'type' => \Elementor\Controls_Manager::RAW_HTML,
                'raw' => __('<div style="line-height:1.3;">
                    <strong>How to use this carousel:</strong><br>
                    1. Add slides using the "Slide Content" repeater below<br>
                    2. For each slide, use the WYSIWYG editor or add HTML<br>
                    3. You can include any HTML, shortcodes, or Elementor shortcodes<br>
                    4. Configure carousel settings in the sections below
                </div>', 'accessible-swiper-carousel'),
            ]
        );

        $this->end_controls_section();

        // Slides Content Section
        $this->start_controls_section(
            'slides_section',
            [
                'label' => __('Slide Content', 'accessible-swiper-carousel'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'slide_title',
            [
                'label' => __('Slide Title', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Slide Title', 'accessible-swiper-carousel'),
            ]
        );

        $repeater->add_control(
            'content_type',
            [
                'label' => __('Content Type', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'editor' => __('Rich Text Editor', 'accessible-swiper-carousel'),
                    'template' => __('Elementor Template', 'accessible-swiper-carousel'),
                    'html' => __('Custom HTML', 'accessible-swiper-carousel'),
                ],
                'default' => 'editor',
            ]
        );

        $repeater->add_control(
            'slide_content',
            [
                'label' => __('Content', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => __('<h3>Slide Title</h3><p>Add your content here. You can use any HTML, including images, buttons, and more.</p>', 'accessible-swiper-carousel'),
                'condition' => [
                    'content_type' => 'editor',
                ],
            ]
        );

        $repeater->add_control(
            'slide_template',
            [
                'label' => __('Choose Template', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_elementor_templates(),
                'condition' => [
                    'content_type' => 'template',
                ],
            ]
        );

        $repeater->add_control(
            'slide_html',
            [
                'label' => __('Custom HTML', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::CODE,
                'language' => 'html',
                'default' => '<div style="padding: 20px; text-align: center;">
    <h3>Custom HTML Slide</h3>
    <p>Add any HTML content here</p>
</div>',
                'condition' => [
                    'content_type' => 'html',
                ],
            ]
        );

        $this->add_control(
            'slides_list',
            [
                'label' => __('Slides', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'slide_title' => __('Slide 1', 'accessible-swiper-carousel'),
                        'content_type' => 'editor',
                        'slide_content' => __('<h3>Welcome to Slide 1</h3><p>This is your first slide. Edit this content and add more slides as needed.</p>', 'accessible-swiper-carousel'),
                    ],
                    [
                        'slide_title' => __('Slide 2', 'accessible-swiper-carousel'),
                        'content_type' => 'editor',
                        'slide_content' => __('<h3>This is Slide 2</h3><p>Add images, buttons, or any other content you need.</p>', 'accessible-swiper-carousel'),
                    ],
                    [
                        'slide_title' => __('Slide 3', 'accessible-swiper-carousel'),
                        'content_type' => 'editor',
                        'slide_content' => __('<h3>Final Slide</h3><p>You can add as many slides as you want using the repeater above.</p>', 'accessible-swiper-carousel'),
                    ],
                ],
                'title_field' => '{{{ slide_title }}}',
            ]
        );

        $this->end_controls_section();

        // Carousel Settings Section
        $this->start_controls_section(
            'carousel_settings_section',
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
            'mobile_slides',
            [
                'label' => __('Mobile Slides Per View', 'accessible-swiper-carousel'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 1,
                'min' => 1,
                'max' => 3,
            ]
        );

        $this->end_controls_section();
    }

    private function get_elementor_templates() {
        $templates = [];
        
        $posts = get_posts([
            'post_type' => 'elementor_library',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => '_elementor_template_type',
                    'value' => ['section', 'container'],
                    'compare' => 'IN',
                ],
            ],
        ]);

        foreach ($posts as $post) {
            $templates[$post->ID] = $post->post_title;
        }

        return $templates;
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
                 
                 aria-label="<?php echo esc_attr($settings['carousel_label']); ?>"
                 >
                
                <div class="swiper-wrapper">
                    <?php
                    $slides = $settings['slides_list'];
                    if (!empty($slides)) {
                        foreach ($slides as $index => $slide) {
                            echo '<div class="swiper-slide" aria-label="Slide ' . sprintf(__('Slide %d', 'accessible-swiper-carousel'), $index + 1) . '">';
                            echo '<div class="slide-content">';
                            
                            switch ($slide['content_type']) {
                                case 'template':
                                    if (!empty($slide['slide_template'])) {
                                        echo \Elementor\Plugin::instance()->frontend->get_builder_content_for_display($slide['slide_template']);
                                    }
                                    break;
                                case 'html':
                                    echo $slide['slide_html'];
                                    break;
                                default:
                                    echo $slide['slide_content'];
                                    break;
                            }
                            
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>

                <?php if ($settings['show_pagination'] === 'yes'): ?>
                    <div class="swiper-pagination swiper-pagination-<?php echo $widget_id; ?>" 
                         
                         aria-label="<?php echo esc_attr__('Carousel pagination', 'accessible-swiper-carousel'); ?>"></div>
                <?php endif; ?>

            </div>
            
            <?php if ($settings['show_navigation'] === 'yes'): ?>
                <button class="swiper-button-prev swiper-button-prev-<?php echo $widget_id; ?>" 
                        aria-label="<?php echo esc_attr__('Previous slide', 'accessible-swiper-carousel'); ?>" 
                        >
                    <span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M169.4 297.4C156.9 309.9 156.9 330.2 169.4 342.7L361.4 534.7C373.9 547.2 394.2 547.2 406.7 534.7C419.2 522.2 419.2 501.9 406.7 489.4L237.3 320L406.6 150.6C419.1 138.1 419.1 117.8 406.6 105.3C394.1 92.8 373.8 92.8 361.3 105.3L169.3 297.3z"/></svg></span>
                </button>
                <button class="swiper-button-next swiper-button-next-<?php echo $widget_id; ?>" 
                        aria-label="<?php echo esc_attr__('Next slide', 'accessible-swiper-carousel'); ?>" 
                        >
                    <span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M471.1 297.4C483.6 309.9 483.6 330.2 471.1 342.7L279.1 534.7C266.6 547.2 246.3 547.2 233.8 534.7C221.3 522.2 221.3 501.9 233.8 489.4L403.2 320L233.9 150.6C221.4 138.1 221.4 117.8 233.9 105.3C246.4 92.8 266.7 92.8 279.2 105.3L471.2 297.3z"/></svg></span>
                </button>
            <?php endif; ?>

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