<?php
/**
 * Plugin Name: Accessible Swiper Carousel for Elementor
 * Plugin URI: https://easya11yguide.com
 * Description: Adds an accessible Swiper carousel widget to Elementor that allows any content inside.
 * Version: 1.0.14
 * Author: Anphira, LLC
 * Text Domain: accessible-swiper-carousel
 * Requires at least: 5.0
 * Tested up to: 6.3
 * Requires PHP: 7.4
 * Elementor tested up to: 3.15
 * Elementor Pro tested up to: 3.15
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('ASC_VERSION', '1.0.14');
define('ASC_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ASC_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Main Plugin Class
 */
final class Accessible_Swiper_Carousel_Plugin {
    
    private static $_instance = null;
    
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function __construct() {
        add_action('plugins_loaded', [$this, 'init']);
    }
    
    public function init() {
        // Check if Elementor is active
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
            return;
        }
        
        // Check Elementor version
        if (!version_compare(ELEMENTOR_VERSION, '3.0.0', '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_version']);
            return;
        }
        
        // Check PHP version
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return;
        }
        
        // Register widget
        add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
        
        // Enqueue scripts
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('elementor/preview/enqueue_styles', [$this, 'enqueue_scripts']);
    }
    
    public function admin_notice_missing_elementor() {
        if (isset($_GET['activate'])) unset($_GET['activate']);
        
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'accessible-swiper-carousel'),
            '<strong>' . esc_html__('Accessible Swiper Carousel', 'accessible-swiper-carousel') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'accessible-swiper-carousel') . '</strong>'
        );
        
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    public function admin_notice_minimum_elementor_version() {
        if (isset($_GET['activate'])) unset($_GET['activate']);
        
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'accessible-swiper-carousel'),
            '<strong>' . esc_html__('Accessible Swiper Carousel', 'accessible-swiper-carousel') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'accessible-swiper-carousel') . '</strong>',
            '3.0.0'
        );
        
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    public function admin_notice_minimum_php_version() {
        if (isset($_GET['activate'])) unset($_GET['activate']);
        
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'accessible-swiper-carousel'),
            '<strong>' . esc_html__('Accessible Swiper Carousel', 'accessible-swiper-carousel') . '</strong>',
            '<strong>' . esc_html__('PHP', 'accessible-swiper-carousel') . '</strong>',
            '7.4'
        );
        
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    
    public function register_widgets() {
        require_once ASC_PLUGIN_PATH . 'widgets/accessible-swiper-carousel.php';
        require_once ASC_PLUGIN_PATH . 'widgets/container-carousel.php';
        
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \ASC_Accessible_Swiper_Carousel());
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \ASC_Container_Carousel());
    }
    
    public function enqueue_scripts() {
        wp_enqueue_script('swiper-js', ASC_PLUGIN_URL . 'assets/swiper-bundle.min.js', [], '10.3.1', true);
        wp_enqueue_style('swiper-css', ASC_PLUGIN_URL . 'assets/swiper-bundle.min.css', [], '10.3.1');
        
        wp_enqueue_script('accessible-carousel-init', ASC_PLUGIN_URL . 'assets/carousel-init.js', ['swiper-js'], ASC_VERSION, true);
        wp_enqueue_style('accessible-carousel-style', ASC_PLUGIN_URL . 'assets/carousel-style.css', ['swiper-css'], ASC_VERSION);
    }
}

Accessible_Swiper_Carousel_Plugin::instance();