<?php
/**
 * Elementor Integration Coordinator
 *
 * Bootstraps custom Elementor widgets and category registration for the Custom Editorial theme.
 *
 * @package Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Main Elementor Integration Class
 */
class Custom_Theme_Elementor_Integration {

    /**
     * Singleton instance
     *
     * @var Custom_Theme_Elementor_Integration|null
     */
    private static $instance = null;

    /**
     * Get singleton instance
     *
     * @return Custom_Theme_Elementor_Integration
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        // Register custom category
        add_action( 'elementor/elements/categories_registered', array( $this, 'register_widget_categories' ), 1 );
        
        // Register widgets (Elementor 3.5.0+)
        add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
        
        // Backward compatibility for older Elementor versions (<3.5)
        add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets_legacy' ) );
    }

    /**
     * Register Lucidia Widget Category
     *
     * @param \Elementor\Elements_Manager $elements_manager
     */
    public function register_widget_categories( $elements_manager ) {
        $elements_manager->add_category(
            'custom-editorial',
            array(
                'title' => esc_html__( 'Lucidia', 'custom-theme' ),
                'icon'  => 'eicon-posts-ticker',
            )
        );
    }

    /**
     * Register Custom Widgets (Modern Elementor 3.5+)
     *
     * @param \Elementor\Widgets_Manager $widgets_manager
     */
    public function register_widgets( $widgets_manager ) {
        // Widget 1: Hero Featured Story
        $hero_widget_file = CUSTOM_THEME_DIR . '/inc/elementor/widgets/class-widget-hero-post.php';
        if ( file_exists( $hero_widget_file ) ) {
            require_once $hero_widget_file;
            $widgets_manager->register( new \CustomTheme\Elementor\Widgets\Hero_Post() );
        }

        // Widget 2: Editorial Posts Grid
        $grid_widget_file = CUSTOM_THEME_DIR . '/inc/elementor/widgets/class-widget-post-grid.php';
        if ( file_exists( $grid_widget_file ) ) {
            require_once $grid_widget_file;
            $widgets_manager->register( new \CustomTheme\Elementor\Widgets\Post_Grid() );
        }

        // Widget 3: Horizontal Post List
        $list_widget_file = CUSTOM_THEME_DIR . '/inc/elementor/widgets/class-widget-post-list.php';
        if ( file_exists( $list_widget_file ) ) {
            require_once $list_widget_file;
            $widgets_manager->register( new \CustomTheme\Elementor\Widgets\Post_List() );
        }

        // Widget 4: Magazine Compact Spotlight
        $spotlight_widget_file = CUSTOM_THEME_DIR . '/inc/elementor/widgets/class-widget-compact-spotlight.php';
        if ( file_exists( $spotlight_widget_file ) ) {
            require_once $spotlight_widget_file;
            $widgets_manager->register( new \CustomTheme\Elementor\Widgets\Compact_Spotlight() );
        }
    }

    /**
     * Register Custom Widgets (Legacy Elementor <3.5 fallback)
     *
     * @param \Elementor\Widgets_Manager $widgets_manager
     */
    public function register_widgets_legacy( $widgets_manager ) {
        if ( did_action( 'elementor/widgets/register' ) ) {
            return;
        }
        $hero_widget_file = CUSTOM_THEME_DIR . '/inc/elementor/widgets/class-widget-hero-post.php';
        if ( file_exists( $hero_widget_file ) ) {
            require_once $hero_widget_file;
            $widgets_manager->register_widget_type( new \CustomTheme\Elementor\Widgets\Hero_Post() );
        }

        $grid_widget_file = CUSTOM_THEME_DIR . '/inc/elementor/widgets/class-widget-post-grid.php';
        if ( file_exists( $grid_widget_file ) ) {
            require_once $grid_widget_file;
            $widgets_manager->register_widget_type( new \CustomTheme\Elementor\Widgets\Post_Grid() );
        }

        $list_widget_file = CUSTOM_THEME_DIR . '/inc/elementor/widgets/class-widget-post-list.php';
        if ( file_exists( $list_widget_file ) ) {
            require_once $list_widget_file;
            $widgets_manager->register_widget_type( new \CustomTheme\Elementor\Widgets\Post_List() );
        }

        $spotlight_widget_file = CUSTOM_THEME_DIR . '/inc/elementor/widgets/class-widget-compact-spotlight.php';
        if ( file_exists( $spotlight_widget_file ) ) {
            require_once $spotlight_widget_file;
            $widgets_manager->register_widget_type( new \CustomTheme\Elementor\Widgets\Compact_Spotlight() );
        }
    }
}

/**
 * Initialize integration during theme setup.
 * By the time functions.php runs, plugins_loaded has already executed and Elementor is loaded.
 */
function custom_theme_init_elementor_integration() {
    if ( did_action( 'elementor/loaded' ) ) {
        Custom_Theme_Elementor_Integration::get_instance();
    }
}
add_action( 'init', 'custom_theme_init_elementor_integration', 0 );
custom_theme_init_elementor_integration();
