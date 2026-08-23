<?php
/**
 * Custom Editorial Theme functions and definitions
 *
 * @package Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'CUSTOM_THEME_VERSION', '1.0.0' );
define( 'CUSTOM_THEME_DIR', get_template_directory() );
define( 'CUSTOM_THEME_URI', get_template_directory_uri() );

if ( ! function_exists( 'custom_theme_setup' ) ) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     */
    function custom_theme_setup() {
        // Make theme available for translation.
        load_theme_textdomain( 'custom-theme', CUSTOM_THEME_DIR . '/languages' );

        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        // Let WordPress manage the document title.
        add_theme_support( 'title-tag' );

        // Enable support for Post Thumbnails on posts and pages.
        add_theme_support( 'post-thumbnails' );

        // Custom image sizes for editorial layouts
        add_image_size( 'custom-theme-featured', 1200, 675, true ); // 16:9 Hero
        add_image_size( 'custom-theme-card', 700, 465, true );      // 3:2 Grid Card
        add_image_size( 'custom-theme-compact', 320, 215, true );   // Magazine Secondary
        add_image_size( 'custom-theme-thumbnail', 160, 120, true );  // Sidebar & search thumbnails
        add_image_size( 'custom-theme-avatar', 96, 96, true );      // Author Avatar

        // Register navigation menus.
        register_nav_menus(
            array(
                'primary' => esc_html__( 'Primary Navigation', 'custom-theme' ),
                'footer'  => esc_html__( 'Footer Navigation', 'custom-theme' ),
            )
        );

        // Switch default core markup for search form, comment form, etc. to output valid HTML5.
        add_theme_support(
            'html5',
            array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'style',
                'script',
                'navigation-widgets',
            )
        );

        // Set up the WordPress core custom logo feature.
        add_theme_support(
            'custom-logo',
            array(
                'height'      => 80,
                'width'       => 260,
                'flex-width'  => true,
                'flex-height' => true,
            )
        );

        // Add theme support for selective refresh for widgets.
        add_theme_support( 'customize-selective-refresh-widgets' );

        // Add support for responsive embedded content.
        add_theme_support( 'responsive-embeds' );

        // Add support for full and wide align images in Gutenberg & Page Builders.
        add_theme_support( 'align-wide' );

        // Add support for custom line height controls in block editor.
        add_theme_support( 'custom-line-height' );

        // Add support for custom spacing (margin & padding) in block editor.
        add_theme_support( 'custom-spacing' );

        // Add support for appearance tools (border, color, spacing, typography).
        add_theme_support( 'appearance-tools' );

        // Add support for editor styles.
        add_theme_support( 'editor-styles' );
        add_editor_style( 'assets/css/editor-style.css' );
    }
endif;
add_action( 'after_setup_theme', 'custom_theme_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function custom_theme_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'custom_theme_content_width', 860 );
}
add_action( 'after_setup_theme', 'custom_theme_content_width', 0 );

/**
 * Enqueue scripts and styles.
 */
function custom_theme_scripts() {
    // Base WordPress theme stylesheet (core alignment, captions, accessibility, print).
    wp_enqueue_style(
        'custom-theme-style',
        get_stylesheet_uri(),
        array(),
        CUSTOM_THEME_VERSION
    );

    // Main Theme CSS (includes self-hosted @font-face for Inter & Lora)
    wp_enqueue_style(
        'custom-theme-main',
        CUSTOM_THEME_URI . '/assets/css/main.css',
        array( 'custom-theme-style' ),
        CUSTOM_THEME_VERSION
    );

    // Main Theme JavaScript
    wp_enqueue_script(
        'custom-theme-main',
        CUSTOM_THEME_URI . '/assets/js/main.js',
        array(),
        CUSTOM_THEME_VERSION,
        true
    );

    // Pass theme data & API settings to JS
    wp_localize_script(
        'custom-theme-main',
        'customThemeData',
        array(
            'darkModeEnabled'    => (bool) get_theme_mod( 'custom_theme_enable_dark_mode', false ),
            'darkModeDefault'    => get_theme_mod( 'custom_theme_dark_mode_default', 'light' ),
            'searchApiUrl'       => esc_url_raw( rest_url( 'custom-theme/v1/search' ) ),
            'homeUrl'            => esc_url_raw( home_url( '/' ) ),
            'copiedText'         => esc_html__( 'Link copied!', 'custom-theme' ),
            'copyErrorText'      => esc_html__( 'Unable to copy', 'custom-theme' ),
            'openMenu'           => esc_html__( 'Open navigation menu', 'custom-theme' ),
            'closeMenu'          => esc_html__( 'Close navigation menu', 'custom-theme' ),
            'searchingText'      => esc_html__( 'Searching articles...', 'custom-theme' ),
            'noResultsText'      => esc_html__( 'No articles found matching', 'custom-theme' ),
            'viewAllResultsText' => esc_html__( 'View all results for', 'custom-theme' ),
            'recentSearchesText' => esc_html__( 'Recent Searches', 'custom-theme' ),
            'clearRecentText'    => esc_html__( 'Clear', 'custom-theme' ),
        )
    );

    // Threaded comments script
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'custom_theme_scripts' );

/**
 * Customize excerpt length.
 *
 * @param int $length Excerpt word length.
 * @return int
 */
function custom_theme_excerpt_length( $length ) {
    return 24;
}
add_filter( 'excerpt_length', 'custom_theme_excerpt_length', 999 );

/**
 * Customize excerpt more string.
 *
 * @param string $more "Read more" string.
 * @return string
 */
function custom_theme_excerpt_more( $more ) {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'custom_theme_excerpt_more' );

/**
 * Load modular theme components
 */
require_once CUSTOM_THEME_DIR . '/inc/helpers.php';
require_once CUSTOM_THEME_DIR . '/inc/template-tags.php';
require_once CUSTOM_THEME_DIR . '/inc/template-functions.php';
require_once CUSTOM_THEME_DIR . '/inc/thumbnail-regenerator.php';

// Conditionally load widgets, customizer, and admin options if present
if ( file_exists( CUSTOM_THEME_DIR . '/inc/widgets.php' ) ) {
    require_once CUSTOM_THEME_DIR . '/inc/widgets.php';
}
if ( file_exists( CUSTOM_THEME_DIR . '/inc/customizer.php' ) ) {
    require_once CUSTOM_THEME_DIR . '/inc/customizer.php';
}
if ( is_admin() && file_exists( CUSTOM_THEME_DIR . '/inc/admin-options.php' ) ) {
    require_once CUSTOM_THEME_DIR . '/inc/admin-options.php';
}
if ( file_exists( CUSTOM_THEME_DIR . '/inc/elementor/class-elementor-integration.php' ) ) {
    require_once CUSTOM_THEME_DIR . '/inc/elementor/class-elementor-integration.php';
}

// Google Fonts preconnect removed — fonts are now self-hosted in assets/fonts/.

function custom_theme_script_loader_tag($tag, $handle) {
    if ('custom-theme-main' === $handle) {
        $tag = str_replace(' src=', ' defer src=', $tag);
    }
    return $tag;
}
add_filter('script_loader_tag', 'custom_theme_script_loader_tag', 10, 2);

function custom_theme_cleanup_head() {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('after_setup_theme', 'custom_theme_cleanup_head');

function custom_theme_clear_category_transients() {
    delete_transient('custom_theme_nav_categories');
}
add_action('created_category', 'custom_theme_clear_category_transients');
add_action('edited_category', 'custom_theme_clear_category_transients');
add_action('delete_category', 'custom_theme_clear_category_transients');

/**
 * Register custom block pattern category.
 */
function custom_theme_register_pattern_category() {
    register_block_pattern_category(
        'custom-theme',
        array(
            'label'       => esc_html__( 'Lucidia', 'custom-theme' ),
            'description' => esc_html__( 'Ready-made editorial content sections for your blog.', 'custom-theme' ),
        )
    );
}
add_action( 'init', 'custom_theme_register_pattern_category' );

/**
 * Per-Post Layout Override Meta Box
 */
function custom_theme_layout_meta_box() {
    add_meta_box(
        'custom_theme_layout_options',
        esc_html__( 'Layout Options', 'custom-theme' ),
        'custom_theme_layout_meta_box_render',
        array( 'post', 'page' ),
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'custom_theme_layout_meta_box' );

function custom_theme_layout_meta_box_render( $post ) {
    wp_nonce_field( 'custom_theme_layout_nonce', 'custom_theme_layout_nonce' );

    $single_template = get_post_meta( $post->ID, '_custom_theme_single_template', true );
    $sidebar         = get_post_meta( $post->ID, '_custom_theme_sidebar_override', true );
    $width           = get_post_meta( $post->ID, '_custom_theme_width_override', true );
    $header          = get_post_meta( $post->ID, '_custom_theme_header_override', true );
    $footer          = get_post_meta( $post->ID, '_custom_theme_footer_override', true );

    $single_template = $single_template ? $single_template : 'inherit';
    $sidebar         = $sidebar ? $sidebar : 'inherit';
    $width           = $width ? $width : 'inherit';
    $header          = $header ? $header : 'inherit';
    $footer          = $footer ? $footer : 'inherit';
    ?>
    <style>
        .custom-theme-layout-options label {
            display: block;
            margin-bottom: 4px;
            font-size: 13px;
        }
        .custom-theme-layout-options select {
            width: 100%;
            margin-bottom: 12px;
        }
    </style>
    <div class="custom-theme-layout-options">
        <?php if ( 'post' === $post->post_type ) : ?>
        <p>
            <label for="custom_theme_single_template"><?php esc_html_e( 'Single Post Template', 'custom-theme' ); ?></label>
            <select name="custom_theme_single_template" id="custom_theme_single_template">
                <option value="inherit" <?php selected( $single_template, 'inherit' ); ?>><?php esc_html_e( 'Inherit Global Default', 'custom-theme' ); ?></option>
                <option value="classic" <?php selected( $single_template, 'classic' ); ?>><?php esc_html_e( 'Classic Editorial', 'custom-theme' ); ?></option>
                <option value="magazine" <?php selected( $single_template, 'magazine' ); ?>><?php esc_html_e( 'Magazine Hero', 'custom-theme' ); ?></option>
                <option value="minimal" <?php selected( $single_template, 'minimal' ); ?>><?php esc_html_e( 'Minimal Clean', 'custom-theme' ); ?></option>
            </select>
        </p>
        <?php endif; ?>

        <p>
            <label for="custom_theme_sidebar_override"><?php esc_html_e( 'Sidebar Display', 'custom-theme' ); ?></label>
            <select name="custom_theme_sidebar_override" id="custom_theme_sidebar_override">
                <option value="inherit" <?php selected( $sidebar, 'inherit' ); ?>><?php esc_html_e( 'Inherit Global Setting', 'custom-theme' ); ?></option>
                <option value="show" <?php selected( $sidebar, 'show' ); ?>><?php esc_html_e( 'Show Sidebar', 'custom-theme' ); ?></option>
                <option value="hide" <?php selected( $sidebar, 'hide' ); ?>><?php esc_html_e( 'Hide Sidebar', 'custom-theme' ); ?></option>
            </select>
        </p>

        <p>
            <label for="custom_theme_width_override"><?php esc_html_e( 'Content Width (when sidebar hidden)', 'custom-theme' ); ?></label>
            <select name="custom_theme_width_override" id="custom_theme_width_override">
                <option value="inherit" <?php selected( $width, 'inherit' ); ?>><?php esc_html_e( 'Inherit Global Setting', 'custom-theme' ); ?></option>
                <option value="contained" <?php selected( $width, 'contained' ); ?>><?php esc_html_e( 'Standard 920px', 'custom-theme' ); ?></option>
                <option value="narrow" <?php selected( $width, 'narrow' ); ?>><?php esc_html_e( 'Narrow 780px', 'custom-theme' ); ?></option>
                <option value="full" <?php selected( $width, 'full' ); ?>><?php esc_html_e( 'Wide 1240px', 'custom-theme' ); ?></option>
            </select>
        </p>

        <p>
            <label for="custom_theme_header_override"><?php esc_html_e( 'Header Display', 'custom-theme' ); ?></label>
            <select name="custom_theme_header_override" id="custom_theme_header_override">
                <option value="inherit" <?php selected( $header, 'inherit' ); ?>><?php esc_html_e( 'Inherit Global Setting', 'custom-theme' ); ?></option>
                <option value="show" <?php selected( $header, 'show' ); ?>><?php esc_html_e( 'Show Header', 'custom-theme' ); ?></option>
                <option value="hide" <?php selected( $header, 'hide' ); ?>><?php esc_html_e( 'Hide Header', 'custom-theme' ); ?></option>
            </select>
        </p>

        <p>
            <label for="custom_theme_footer_override"><?php esc_html_e( 'Footer Display', 'custom-theme' ); ?></label>
            <select name="custom_theme_footer_override" id="custom_theme_footer_override">
                <option value="inherit" <?php selected( $footer, 'inherit' ); ?>><?php esc_html_e( 'Inherit Global Setting', 'custom-theme' ); ?></option>
                <option value="show" <?php selected( $footer, 'show' ); ?>><?php esc_html_e( 'Show Footer', 'custom-theme' ); ?></option>
                <option value="hide" <?php selected( $footer, 'hide' ); ?>><?php esc_html_e( 'Hide Footer', 'custom-theme' ); ?></option>
            </select>
        </p>
    </div>
    <?php
}

function custom_theme_layout_meta_box_save( $post_id ) {
    if ( ! isset( $_POST['custom_theme_layout_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['custom_theme_layout_nonce'] ), 'custom_theme_layout_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
        return;
    }
    if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return;
        }
    } else {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    $fields = array(
        'custom_theme_single_template'  => '_custom_theme_single_template',
        'custom_theme_sidebar_override' => '_custom_theme_sidebar_override',
        'custom_theme_width_override'   => '_custom_theme_width_override',
        'custom_theme_header_override'  => '_custom_theme_header_override',
        'custom_theme_footer_override'  => '_custom_theme_footer_override',
    );

    foreach ( $fields as $post_key => $meta_key ) {
        if ( isset( $_POST[ $post_key ] ) ) {
            $value = sanitize_text_field( wp_unslash( $_POST[ $post_key ] ) );
            if ( 'inherit' === $value ) {
                delete_post_meta( $post_id, $meta_key );
            } else {
                update_post_meta( $post_id, $meta_key, $value );
            }
        }
    }
}
add_action( 'save_post', 'custom_theme_layout_meta_box_save' );
