<?php
/**
 * Theme Customization Admin Dashboard & Options Page
 *
 * Provides a dedicated top-level WordPress Admin Menu page for managing
 * theme settings, synchronized with WordPress theme_mods and Customizer.
 *
 * @package Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Register Top-Level Theme Customization Admin Menu
 */
function custom_theme_add_admin_menu() {
    // Main Top-Level Menu
    add_menu_page(
        esc_html__( 'Theme Customization', 'custom-theme' ),
        esc_html__( 'Theme Customization', 'custom-theme' ),
        'edit_theme_options',
        'custom-theme-options',
        'custom_theme_render_options_page',
        'dashicons-admin-customizer',
        59
    );

    // Submenu: Theme Options (Dashboard)
    add_submenu_page(
        'custom-theme-options',
        esc_html__( 'Theme Options Dashboard', 'custom-theme' ),
        esc_html__( 'Theme Options', 'custom-theme' ),
        'edit_theme_options',
        'custom-theme-options',
        'custom_theme_render_options_page'
    );

    // Submenu: Direct Live Customizer Link
    add_submenu_page(
        'custom-theme-options',
        esc_html__( 'Live Customizer Preview', 'custom-theme' ),
        esc_html__( 'Live Customizer ↗', 'custom-theme' ),
        'edit_theme_options',
        'customize.php'
    );
}
add_action( 'admin_menu', 'custom_theme_add_admin_menu' );

/**
 * Enqueue Admin Scripts & Styles for Theme Customization Page
 */
function custom_theme_admin_options_assets( $hook_suffix ) {
    if ( 'toplevel_page_custom-theme-options' !== $hook_suffix ) {
        return;
    }

    // WordPress Core Color Picker & Media Uploader
    wp_enqueue_media();
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );

    // Admin Custom Styling & Script
    wp_enqueue_style(
        'custom-theme-admin-options',
        CUSTOM_THEME_URI . '/assets/css/admin-options.css',
        array( 'wp-color-picker' ),
        CUSTOM_THEME_VERSION
    );

    wp_enqueue_script(
        'custom-theme-admin-options',
        CUSTOM_THEME_URI . '/assets/js/admin-options.js',
        array( 'jquery', 'wp-color-picker' ),
        CUSTOM_THEME_VERSION,
        true
    );

    wp_localize_script(
        'custom-theme-admin-options',
        'customThemeAdminData',
        array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'custom_theme_regenerate_nonce' ),
            'strings' => array(
                'confirmRegenerate' => esc_html__( 'Are you sure you want to regenerate thumbnails for all media attachments? This will generate all 5 theme image sizes.', 'custom-theme' ),
                'processing'        => esc_html__( 'Processing...', 'custom-theme' ),
                'finished'          => esc_html__( 'All thumbnails regenerated successfully!', 'custom-theme' ),
                'stopping'          => esc_html__( 'Stopping...', 'custom-theme' ),
                'stopped'           => esc_html__( 'Regeneration paused.', 'custom-theme' ),
                'noImages'          => esc_html__( 'No image attachments found in the Media Library.', 'custom-theme' ),
                'error'             => esc_html__( 'An error occurred during regeneration.', 'custom-theme' ),
            ),
        )
    );
}
add_action( 'admin_enqueue_scripts', 'custom_theme_admin_options_assets' );

/**
 * Save Theme Options Handler
 */
function custom_theme_save_options() {
    if ( ! isset( $_POST['custom_theme_options_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['custom_theme_options_nonce'] ) ), 'custom_theme_save_options_action' ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_theme_options' ) ) {
        wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'custom-theme' ) );
    }

    // 1. General Colors
    if ( isset( $_POST['custom_theme_accent_color'] ) ) {
        set_theme_mod( 'custom_theme_accent_color', sanitize_hex_color( wp_unslash( $_POST['custom_theme_accent_color'] ) ) );
    }
    if ( isset( $_POST['custom_theme_accent_hover_color'] ) ) {
        set_theme_mod( 'custom_theme_accent_hover_color', sanitize_hex_color( wp_unslash( $_POST['custom_theme_accent_hover_color'] ) ) );
    }

    set_theme_mod( 'custom_theme_show_breadcrumbs', isset( $_POST['custom_theme_show_breadcrumbs'] ) );

    // Blog & Single Post Layout Settings
    if ( isset( $_POST['custom_theme_archive_layout'] ) ) {
        set_theme_mod( 'custom_theme_archive_layout', sanitize_text_field( wp_unslash( $_POST['custom_theme_archive_layout'] ) ) );
    }
    if ( isset( $_POST['custom_theme_pagination_type'] ) ) {
        $pag_type = sanitize_text_field( wp_unslash( $_POST['custom_theme_pagination_type'] ) );
        $valid_pag = array( 'numbered', 'load_more', 'infinite' );
        set_theme_mod( 'custom_theme_pagination_type', in_array( $pag_type, $valid_pag, true ) ? $pag_type : 'numbered' );
    }
    if ( isset( $_POST['custom_theme_default_single_template'] ) ) {
        $single_tmpl = sanitize_text_field( wp_unslash( $_POST['custom_theme_default_single_template'] ) );
        $valid_tmpls = array( 'classic', 'magazine', 'minimal' );
        set_theme_mod( 'custom_theme_default_single_template', in_array( $single_tmpl, $valid_tmpls, true ) ? $single_tmpl : 'classic' );
    }
    set_theme_mod( 'custom_theme_single_show_sidebar', isset( $_POST['custom_theme_single_show_sidebar'] ) );
    if ( isset( $_POST['custom_theme_single_content_width'] ) ) {
        $single_w = sanitize_text_field( wp_unslash( $_POST['custom_theme_single_content_width'] ) );
        $valid_single_w = array( 'contained', 'narrow', 'full' );
        set_theme_mod( 'custom_theme_single_content_width', in_array( $single_w, $valid_single_w, true ) ? $single_w : 'contained' );
    }
    set_theme_mod( 'custom_theme_single_show_featured_image', isset( $_POST['custom_theme_single_show_featured_image'] ) );
    set_theme_mod( 'custom_theme_single_show_toc', isset( $_POST['custom_theme_single_show_toc'] ) );
    set_theme_mod( 'custom_theme_toc_sticky', isset( $_POST['custom_theme_toc_sticky'] ) );
    set_theme_mod( 'custom_theme_enable_reading_mode', isset( $_POST['custom_theme_enable_reading_mode'] ) );
    set_theme_mod( 'custom_theme_single_show_social_share', isset( $_POST['custom_theme_single_show_social_share'] ) );
    set_theme_mod( 'custom_theme_single_show_author_box', isset( $_POST['custom_theme_single_show_author_box'] ) );
    set_theme_mod( 'custom_theme_single_show_post_nav', isset( $_POST['custom_theme_single_show_post_nav'] ) );
    set_theme_mod( 'custom_theme_single_show_related_posts', isset( $_POST['custom_theme_single_show_related_posts'] ) );
    set_theme_mod( 'custom_theme_single_show_comments', isset( $_POST['custom_theme_single_show_comments'] ) );
    set_theme_mod( 'custom_theme_archive_show_sidebar', isset( $_POST['custom_theme_archive_show_sidebar'] ) );

    // 2. Header & Layout
    set_theme_mod( 'custom_theme_display_header', isset( $_POST['custom_theme_display_header'] ) );
    if ( isset( $_POST['custom_theme_header_layout'] ) ) {
        $layout = sanitize_text_field( wp_unslash( $_POST['custom_theme_header_layout'] ) );
        $valid_layouts = array( 'default', 'centered', 'split', 'minimal' );
        set_theme_mod( 'custom_theme_header_layout', in_array( $layout, $valid_layouts, true ) ? $layout : 'default' );
    }
    if ( isset( $_POST['custom_theme_header_width'] ) ) {
        $width = sanitize_text_field( wp_unslash( $_POST['custom_theme_header_width'] ) );
        set_theme_mod( 'custom_theme_header_width', in_array( $width, array( 'contained', 'full-width' ), true ) ? $width : 'contained' );
    }
    if ( isset( $_POST['custom_theme_header_height'] ) ) {
        set_theme_mod( 'custom_theme_header_height', absint( $_POST['custom_theme_header_height'] ) );
    }
    if ( isset( $_POST['custom_theme_header_bg_color'] ) ) {
        set_theme_mod( 'custom_theme_header_bg_color', sanitize_hex_color( wp_unslash( $_POST['custom_theme_header_bg_color'] ) ) );
    }
    set_theme_mod( 'custom_theme_header_border_bottom', isset( $_POST['custom_theme_header_border_bottom'] ) );

    // 3. Top Bar
    set_theme_mod( 'custom_theme_enable_topbar', isset( $_POST['custom_theme_enable_topbar'] ) );
    if ( isset( $_POST['custom_theme_topbar_text'] ) ) {
        set_theme_mod( 'custom_theme_topbar_text', wp_kses_post( wp_unslash( $_POST['custom_theme_topbar_text'] ) ) );
    }
    set_theme_mod( 'custom_theme_topbar_show_date', isset( $_POST['custom_theme_topbar_show_date'] ) );
    set_theme_mod( 'custom_theme_topbar_show_social', isset( $_POST['custom_theme_topbar_show_social'] ) );
    if ( isset( $_POST['custom_theme_topbar_bg_color'] ) ) {
        set_theme_mod( 'custom_theme_topbar_bg_color', sanitize_hex_color( wp_unslash( $_POST['custom_theme_topbar_bg_color'] ) ) );
    }
    if ( isset( $_POST['custom_theme_topbar_text_color'] ) ) {
        set_theme_mod( 'custom_theme_topbar_text_color', sanitize_hex_color( wp_unslash( $_POST['custom_theme_topbar_text_color'] ) ) );
    }

    // 4. Branding & Logo
    if ( isset( $_POST['custom_theme_logo_url'] ) ) {
        $logo_url = esc_url_raw( wp_unslash( $_POST['custom_theme_logo_url'] ) );
        set_theme_mod( 'custom_theme_logo_url', $logo_url );
        
        $logo_id = isset( $_POST['custom_theme_logo_id'] ) ? absint( $_POST['custom_theme_logo_id'] ) : 0;
        set_theme_mod( 'custom_theme_logo_id', $logo_id );

        // Sync with core WordPress custom_logo theme mod
        if ( $logo_id > 0 ) {
            set_theme_mod( 'custom_logo', $logo_id );
        } elseif ( empty( $logo_url ) ) {
            remove_theme_mod( 'custom_logo' );
        }
    }
    if ( isset( $_POST['custom_theme_logo_max_height'] ) ) {
        set_theme_mod( 'custom_theme_logo_max_height', absint( $_POST['custom_theme_logo_max_height'] ) );
    }
    if ( isset( $_POST['custom_theme_logo_mobile_max_height'] ) ) {
        set_theme_mod( 'custom_theme_logo_mobile_max_height', absint( $_POST['custom_theme_logo_mobile_max_height'] ) );
    }
    set_theme_mod( 'custom_theme_show_tagline', isset( $_POST['custom_theme_show_tagline'] ) );
    if ( isset( $_POST['custom_theme_site_title_font_size'] ) ) {
        set_theme_mod( 'custom_theme_site_title_font_size', absint( $_POST['custom_theme_site_title_font_size'] ) );
    }
    set_theme_mod( 'custom_theme_site_title_uppercase', isset( $_POST['custom_theme_site_title_uppercase'] ) );

    // 5. Navigation & Dropdowns
    if ( isset( $_POST['custom_theme_nav_alignment'] ) ) {
        $nav_align = sanitize_text_field( wp_unslash( $_POST['custom_theme_nav_alignment'] ) );
        set_theme_mod( 'custom_theme_nav_alignment', in_array( $nav_align, array( 'left', 'center', 'right' ), true ) ? $nav_align : 'center' );
    }
    if ( isset( $_POST['custom_theme_nav_indicator_style'] ) ) {
        $indicator = sanitize_text_field( wp_unslash( $_POST['custom_theme_nav_indicator_style'] ) );
        set_theme_mod( 'custom_theme_nav_indicator_style', in_array( $indicator, array( 'underline', 'pill', 'dot', 'none' ), true ) ? $indicator : 'underline' );
    }
    if ( isset( $_POST['custom_theme_nav_font_size'] ) ) {
        $nav_size = sanitize_text_field( wp_unslash( $_POST['custom_theme_nav_font_size'] ) );
        set_theme_mod( 'custom_theme_nav_font_size', in_array( $nav_size, array( 'small', 'regular', 'medium' ), true ) ? $nav_size : 'regular' );
    }
    set_theme_mod( 'custom_theme_nav_uppercase', isset( $_POST['custom_theme_nav_uppercase'] ) );
    if ( isset( $_POST['custom_theme_nav_link_color'] ) ) {
        set_theme_mod( 'custom_theme_nav_link_color', sanitize_hex_color( wp_unslash( $_POST['custom_theme_nav_link_color'] ) ) );
    }
    if ( isset( $_POST['custom_theme_nav_hover_color'] ) ) {
        set_theme_mod( 'custom_theme_nav_hover_color', sanitize_hex_color( wp_unslash( $_POST['custom_theme_nav_hover_color'] ) ) );
    }

    // 6. Action Buttons & CTA
    set_theme_mod( 'custom_theme_show_header_search', isset( $_POST['custom_theme_show_header_search'] ) );
    set_theme_mod( 'custom_theme_enable_header_cta', isset( $_POST['custom_theme_enable_header_cta'] ) );
    if ( isset( $_POST['custom_theme_header_cta_text'] ) ) {
        set_theme_mod( 'custom_theme_header_cta_text', sanitize_text_field( wp_unslash( $_POST['custom_theme_header_cta_text'] ) ) );
    }
    if ( isset( $_POST['custom_theme_header_cta_url'] ) ) {
        set_theme_mod( 'custom_theme_header_cta_url', esc_url_raw( wp_unslash( $_POST['custom_theme_header_cta_url'] ) ) );
    }
    set_theme_mod( 'custom_theme_header_cta_target', isset( $_POST['custom_theme_header_cta_target'] ) );
    if ( isset( $_POST['custom_theme_header_cta_style'] ) ) {
        $cta_style = sanitize_text_field( wp_unslash( $_POST['custom_theme_header_cta_style'] ) );
        set_theme_mod( 'custom_theme_header_cta_style', in_array( $cta_style, array( 'primary', 'outline', 'subtle' ), true ) ? $cta_style : 'primary' );
    }

    // 7. Sticky Header
    set_theme_mod( 'custom_theme_sticky_header', isset( $_POST['custom_theme_sticky_header'] ) );
    if ( isset( $_POST['custom_theme_sticky_behavior'] ) ) {
        $sticky_mode = sanitize_text_field( wp_unslash( $_POST['custom_theme_sticky_behavior'] ) );
        set_theme_mod( 'custom_theme_sticky_behavior', in_array( $sticky_mode, array( 'smart-hide', 'always-fixed' ), true ) ? $sticky_mode : 'smart-hide' );
    }
    set_theme_mod( 'custom_theme_sticky_blur', isset( $_POST['custom_theme_sticky_blur'] ) );
    if ( isset( $_POST['custom_theme_sticky_shadow'] ) ) {
        $shadow = sanitize_text_field( wp_unslash( $_POST['custom_theme_sticky_shadow'] ) );
        set_theme_mod( 'custom_theme_sticky_shadow', in_array( $shadow, array( 'subtle', 'medium', 'none' ), true ) ? $shadow : 'subtle' );
    }

    // 8. Dark Mode Suite
    set_theme_mod( 'custom_theme_enable_dark_mode', isset( $_POST['custom_theme_enable_dark_mode'] ) );
    if ( isset( $_POST['custom_theme_dark_mode_default'] ) ) {
        $dm_default = sanitize_text_field( wp_unslash( $_POST['custom_theme_dark_mode_default'] ) );
        set_theme_mod( 'custom_theme_dark_mode_default', in_array( $dm_default, array( 'light', 'dark', 'auto' ), true ) ? $dm_default : 'light' );
    }
    if ( isset( $_POST['custom_theme_dark_bg_color'] ) ) {
        set_theme_mod( 'custom_theme_dark_bg_color', sanitize_hex_color( wp_unslash( $_POST['custom_theme_dark_bg_color'] ) ) );
    }
    if ( isset( $_POST['custom_theme_dark_surface_color'] ) ) {
        set_theme_mod( 'custom_theme_dark_surface_color', sanitize_hex_color( wp_unslash( $_POST['custom_theme_dark_surface_color'] ) ) );
    }
    if ( isset( $_POST['custom_theme_dark_surface_subtle'] ) ) {
        set_theme_mod( 'custom_theme_dark_surface_subtle', sanitize_hex_color( wp_unslash( $_POST['custom_theme_dark_surface_subtle'] ) ) );
    }
    if ( isset( $_POST['custom_theme_dark_text_main'] ) ) {
        set_theme_mod( 'custom_theme_dark_text_main', sanitize_hex_color( wp_unslash( $_POST['custom_theme_dark_text_main'] ) ) );
    }
    if ( isset( $_POST['custom_theme_dark_text_secondary'] ) ) {
        set_theme_mod( 'custom_theme_dark_text_secondary', sanitize_hex_color( wp_unslash( $_POST['custom_theme_dark_text_secondary'] ) ) );
    }
    if ( isset( $_POST['custom_theme_dark_text_muted'] ) ) {
        set_theme_mod( 'custom_theme_dark_text_muted', sanitize_hex_color( wp_unslash( $_POST['custom_theme_dark_text_muted'] ) ) );
    }
    if ( isset( $_POST['custom_theme_dark_accent_color'] ) ) {
        set_theme_mod( 'custom_theme_dark_accent_color', sanitize_hex_color( wp_unslash( $_POST['custom_theme_dark_accent_color'] ) ) );
    }
    if ( isset( $_POST['custom_theme_dark_accent_hover_color'] ) ) {
        set_theme_mod( 'custom_theme_dark_accent_hover_color', sanitize_hex_color( wp_unslash( $_POST['custom_theme_dark_accent_hover_color'] ) ) );
    }
    if ( isset( $_POST['custom_theme_dark_link_color'] ) ) {
        set_theme_mod( 'custom_theme_dark_link_color', sanitize_hex_color( wp_unslash( $_POST['custom_theme_dark_link_color'] ) ) );
    }
    if ( isset( $_POST['custom_theme_dark_link_hover_color'] ) ) {
        set_theme_mod( 'custom_theme_dark_link_hover_color', sanitize_hex_color( wp_unslash( $_POST['custom_theme_dark_link_hover_color'] ) ) );
    }
    if ( isset( $_POST['custom_theme_dark_border_color'] ) ) {
        set_theme_mod( 'custom_theme_dark_border_color', sanitize_hex_color( wp_unslash( $_POST['custom_theme_dark_border_color'] ) ) );
    }
    if ( isset( $_POST['custom_theme_dark_header_bg'] ) ) {
        set_theme_mod( 'custom_theme_dark_header_bg', sanitize_hex_color( wp_unslash( $_POST['custom_theme_dark_header_bg'] ) ) );
    }

    // 9. Newsletter & Social
    if ( isset( $_POST['custom_theme_newsletter_title'] ) ) {
        set_theme_mod( 'custom_theme_newsletter_title', sanitize_text_field( wp_unslash( $_POST['custom_theme_newsletter_title'] ) ) );
    }
    if ( isset( $_POST['custom_theme_newsletter_desc'] ) ) {
        set_theme_mod( 'custom_theme_newsletter_desc', sanitize_textarea_field( wp_unslash( $_POST['custom_theme_newsletter_desc'] ) ) );
    }
    if ( isset( $_POST['custom_theme_newsletter_action'] ) ) {
        set_theme_mod( 'custom_theme_newsletter_action', esc_url_raw( wp_unslash( $_POST['custom_theme_newsletter_action'] ) ) );
    }

    $socials = array( 'twitter', 'facebook', 'linkedin', 'instagram', 'github', 'youtube' );
    foreach ( $socials as $soc ) {
        if ( isset( $_POST[ 'custom_theme_social_' . $soc ] ) ) {
            set_theme_mod( 'custom_theme_social_' . $soc, esc_url_raw( wp_unslash( $_POST[ 'custom_theme_social_' . $soc ] ) ) );
        }
    }

    // 10. Footer
    set_theme_mod( 'custom_theme_display_footer', isset( $_POST['custom_theme_display_footer'] ) );
    if ( isset( $_POST['custom_theme_footer_bio'] ) ) {
        set_theme_mod( 'custom_theme_footer_bio', sanitize_textarea_field( wp_unslash( $_POST['custom_theme_footer_bio'] ) ) );
    }
    if ( isset( $_POST['custom_theme_footer_copyright'] ) ) {
        set_theme_mod( 'custom_theme_footer_copyright', sanitize_text_field( wp_unslash( $_POST['custom_theme_footer_copyright'] ) ) );
    }

    // Redirect back with success message and active tab preserved
    $active_tab = isset( $_POST['custom_theme_active_tab'] ) ? sanitize_key( $_POST['custom_theme_active_tab'] ) : 'general';
    wp_safe_redirect( add_query_arg( array( 'page' => 'custom-theme-options', 'saved' => 'true', 'tab' => $active_tab ), admin_url( 'admin.php' ) ) );
    exit;
}
add_action( 'admin_init', 'custom_theme_save_options' );

/**
 * Render Theme Customization Admin Page
 */
function custom_theme_render_options_page() {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    $active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
    $saved      = isset( $_GET['saved'] ) && 'true' === $_GET['saved'];
    ?>
    <div class="wrap custom-theme-admin-wrap">
        
        <!-- Header Banner -->
        <header class="theme-admin-header">
            <div class="theme-header-left">
                <div class="theme-header-badge"><?php esc_html_e( 'Lucidia Editorial', 'custom-theme' ); ?></div>
                <h1 class="theme-admin-title"><?php esc_html_e( 'Theme Customization', 'custom-theme' ); ?></h1>
                <p class="theme-admin-subtitle"><?php esc_html_e( 'Manage all your theme options, colors, header layouts, dark mode, and typography in one central dashboard.', 'custom-theme' ); ?></p>
            </div>
            <div class="theme-header-right">
                <a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button button-secondary customize-quick-btn" target="_blank">
                    <span class="dashicons dashicons-visibility"></span>
                    <span><?php esc_html_e( 'Live Customizer ↗', 'custom-theme' ); ?></span>
                </a>
            </div>
        </header>

        <?php if ( $saved ) : ?>
            <div class="theme-save-alert" role="alert">
                <div class="theme-save-alert-content">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <span><strong><?php esc_html_e( 'Settings saved successfully!', 'custom-theme' ); ?></strong> <?php esc_html_e( 'Changes are live across your site and the Customizer.', 'custom-theme' ); ?></span>
                </div>
                <button type="button" class="theme-save-alert-close" aria-label="<?php esc_attr_e( 'Dismiss notice', 'custom-theme' ); ?>">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Main Dashboard Form -->
        <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=custom-theme-options' ) ); ?>" class="theme-options-form" id="theme-options-form" novalidate>
            <?php wp_nonce_field( 'custom_theme_save_options_action', 'custom_theme_options_nonce' ); ?>
            <input type="hidden" name="custom_theme_active_tab" id="custom_theme_active_tab" value="<?php echo esc_attr( $active_tab ); ?>">

            <div class="theme-options-layout">
                
                <!-- Sidebar Tabs Navigation -->
                <nav class="theme-tabs-nav" aria-label="<?php esc_attr_e( 'Theme Options Tabs', 'custom-theme' ); ?>">
                    <button type="button" class="tab-nav-btn <?php echo ( 'general' === $active_tab ) ? 'is-active' : ''; ?>" data-tab="general">
                        <span class="dashicons dashicons-art"></span>
                        <span><?php esc_html_e( 'General & Colors', 'custom-theme' ); ?></span>
                    </button>
                    <button type="button" class="tab-nav-btn <?php echo ( 'header' === $active_tab ) ? 'is-active' : ''; ?>" data-tab="header">
                        <span class="dashicons dashicons-heading"></span>
                        <span><?php esc_html_e( 'Header & Top Bar', 'custom-theme' ); ?></span>
                    </button>
                    <button type="button" class="tab-nav-btn <?php echo ( 'branding' === $active_tab ) ? 'is-active' : ''; ?>" data-tab="branding">
                        <span class="dashicons dashicons-admin-appearance"></span>
                        <span><?php esc_html_e( 'Logo & Branding', 'custom-theme' ); ?></span>
                    </button>
                    <button type="button" class="tab-nav-btn <?php echo ( 'navigation' === $active_tab ) ? 'is-active' : ''; ?>" data-tab="navigation">
                        <span class="dashicons dashicons-menu-alt3"></span>
                        <span><?php esc_html_e( 'Navigation & Menu', 'custom-theme' ); ?></span>
                    </button>
                    <button type="button" class="tab-nav-btn <?php echo ( 'darkmode' === $active_tab ) ? 'is-active' : ''; ?>" data-tab="darkmode">
                        <svg class="admin-tab-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                        <span><?php esc_html_e( 'Dark Mode Palette', 'custom-theme' ); ?></span>
                    </button>
                    <button type="button" class="tab-nav-btn <?php echo ( 'blog' === $active_tab ) ? 'is-active' : ''; ?>" data-tab="blog">
                        <span class="dashicons dashicons-welcome-write-blog"></span>
                        <span><?php esc_html_e( 'Blog & Single Post', 'custom-theme' ); ?></span>
                    </button>
                    <button type="button" class="tab-nav-btn <?php echo ( 'actions' === $active_tab ) ? 'is-active' : ''; ?>" data-tab="actions">
                        <span class="dashicons dashicons-button"></span>
                        <span><?php esc_html_e( 'CTA & Action Buttons', 'custom-theme' ); ?></span>
                    </button>
                    <button type="button" class="tab-nav-btn <?php echo ( 'newsletter' === $active_tab ) ? 'is-active' : ''; ?>" data-tab="newsletter">
                        <span class="dashicons dashicons-email-alt"></span>
                        <span><?php esc_html_e( 'Newsletter & Social', 'custom-theme' ); ?></span>
                    </button>
                    <button type="button" class="tab-nav-btn <?php echo ( 'footer' === $active_tab ) ? 'is-active' : ''; ?>" data-tab="footer">
                        <span class="dashicons dashicons-tagcloud"></span>
                        <span><?php esc_html_e( 'Footer & Sticky Scroll', 'custom-theme' ); ?></span>
                    </button>
                    <button type="button" class="tab-nav-btn <?php echo ( 'tools' === $active_tab ) ? 'is-active' : ''; ?>" data-tab="tools">
                        <span class="dashicons dashicons-admin-tools"></span>
                        <span><?php esc_html_e( 'System & Tools', 'custom-theme' ); ?></span>
                    </button>
                </nav>

                <!-- Tabs Content Panels -->
                <div class="theme-tabs-content">
                    
                    <!-- TAB 1: General & Colors -->
                    <div class="tab-panel <?php echo ( 'general' === $active_tab ) ? 'is-active' : ''; ?>" id="tab-general">
                        <div class="panel-header">
                            <h2><?php esc_html_e( 'General Theme Colors', 'custom-theme' ); ?></h2>
                            <p><?php esc_html_e( 'Set primary brand accent colors for buttons, links, badges, and interactive highlights.', 'custom-theme' ); ?></p>
                        </div>
                        
                        <div class="theme-field-row">
                            <label for="custom_theme_accent_color" class="field-label"><?php esc_html_e( 'Primary Accent Color', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="text" name="custom_theme_accent_color" id="custom_theme_accent_color" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_accent_color', '#184a7e' ) ); ?>" data-default-color="#184a7e">
                                <p class="field-desc"><?php esc_html_e( 'Used across primary buttons, active badges, and highlights.', 'custom-theme' ); ?></p>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_accent_hover_color" class="field-label"><?php esc_html_e( 'Accent Hover Color', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="text" name="custom_theme_accent_hover_color" id="custom_theme_accent_hover_color" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_accent_hover_color', '#11365d' ) ); ?>" data-default-color="#11365d">
                                <p class="field-desc"><?php esc_html_e( 'Darker shade for interactive button and link hover states.', 'custom-theme' ); ?></p>
                            </div>
                        </div>
                        
                        <hr class="section-divider">
                        <h3><?php esc_html_e( 'Site Navigation Settings', 'custom-theme' ); ?></h3>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Breadcrumb Navigation', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_show_breadcrumbs" value="1" <?php checked( get_theme_mod( 'custom_theme_show_breadcrumbs', true ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Display breadcrumb trail on posts, pages, and archives for improved navigation and SEO.', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Header & Top Bar -->
                    <div class="tab-panel <?php echo ( 'header' === $active_tab ) ? 'is-active' : ''; ?>" id="tab-header">
                        <div class="panel-header">
                            <h2><?php esc_html_e( 'Header & Top Bar Settings', 'custom-theme' ); ?></h2>
                            <p><?php esc_html_e( 'Configure header visibility, layout style, height, fluid width, and announcement top bar.', 'custom-theme' ); ?></p>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Display Site Header', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_display_header" value="1" <?php checked( get_theme_mod( 'custom_theme_display_header', true ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Display theme header on site (Disable if using a page builder custom header template)', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_header_layout" class="field-label"><?php esc_html_e( 'Header Layout Style', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <?php $hl = get_theme_mod( 'custom_theme_header_layout', 'default' ); ?>
                                <select name="custom_theme_header_layout" id="custom_theme_header_layout">
                                    <option value="default" <?php selected( $hl, 'default' ); ?>><?php esc_html_e( 'Inline (Logo Left, Nav Center/Right)', 'custom-theme' ); ?></option>
                                    <option value="centered" <?php selected( $hl, 'centered' ); ?>><?php esc_html_e( 'Centered (Stacked Logo & Navigation)', 'custom-theme' ); ?></option>
                                    <option value="split" <?php selected( $hl, 'split' ); ?>><?php esc_html_e( 'Split Navigation (Logo Centered)', 'custom-theme' ); ?></option>
                                    <option value="minimal" <?php selected( $hl, 'minimal' ); ?>><?php esc_html_e( 'Minimal (Logo + Hamburger Drawer)', 'custom-theme' ); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_header_width" class="field-label"><?php esc_html_e( 'Header Container Width', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <?php $hw = get_theme_mod( 'custom_theme_header_width', 'contained' ); ?>
                                <select name="custom_theme_header_width" id="custom_theme_header_width">
                                    <option value="contained" <?php selected( $hw, 'contained' ); ?>><?php esc_html_e( 'Contained (Max Width 1240px)', 'custom-theme' ); ?></option>
                                    <option value="full-width" <?php selected( $hw, 'full-width' ); ?>><?php esc_html_e( 'Full Width (Fluid Edge-to-Edge)', 'custom-theme' ); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_header_height" class="field-label"><?php esc_html_e( 'Header Height (px)', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="number" name="custom_theme_header_height" id="custom_theme_header_height" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_header_height', 72 ) ); ?>" min="54" max="130" step="2">
                                <p class="field-desc"><?php esc_html_e( 'Minimum height for the main header (default: 72px).', 'custom-theme' ); ?></p>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_header_bg_color" class="field-label"><?php esc_html_e( 'Custom Header Background', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="text" name="custom_theme_header_bg_color" id="custom_theme_header_bg_color" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_header_bg_color', '' ) ); ?>">
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Header Bottom Border', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_header_border_bottom" value="1" <?php checked( get_theme_mod( 'custom_theme_header_border_bottom', true ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Show bottom border line below header', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <hr class="section-divider">
                        <h3><?php esc_html_e( 'Top Bar Announcement', 'custom-theme' ); ?></h3>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Enable Top Bar', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_enable_topbar" value="1" <?php checked( get_theme_mod( 'custom_theme_enable_topbar', false ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Display top bar above the header', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_topbar_text" class="field-label"><?php esc_html_e( 'Announcement Message', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="text" name="custom_theme_topbar_text" id="custom_theme_topbar_text" class="regular-text" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_topbar_text', 'Welcome to our publication — Exploring ideas, research & thoughtful stories.' ) ); ?>">
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Top Bar Elements', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label><input type="checkbox" name="custom_theme_topbar_show_date" value="1" <?php checked( get_theme_mod( 'custom_theme_topbar_show_date', true ) ); ?>> <?php esc_html_e( 'Show Today&rsquo;s Date', 'custom-theme' ); ?></label><br>
                                <label><input type="checkbox" name="custom_theme_topbar_show_social" value="1" <?php checked( get_theme_mod( 'custom_theme_topbar_show_social', true ) ); ?>> <?php esc_html_e( 'Show Social Media Links', 'custom-theme' ); ?></label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_topbar_bg_color" class="field-label"><?php esc_html_e( 'Top Bar Background & Text Colors', 'custom-theme' ); ?></label>
                            <div class="field-control flex-colors">
                                <div>
                                    <span class="mini-label"><?php esc_html_e( 'Background:', 'custom-theme' ); ?></span>
                                    <input type="text" name="custom_theme_topbar_bg_color" id="custom_theme_topbar_bg_color" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_topbar_bg_color', '' ) ); ?>">
                                </div>
                                <div>
                                    <span class="mini-label"><?php esc_html_e( 'Text Color:', 'custom-theme' ); ?></span>
                                    <input type="text" name="custom_theme_topbar_text_color" id="custom_theme_topbar_text_color" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_topbar_text_color', '' ) ); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Logo & Branding -->
                    <div class="tab-panel <?php echo ( 'branding' === $active_tab ) ? 'is-active' : ''; ?>" id="tab-branding">
                        <div class="panel-header">
                            <h2><?php esc_html_e( 'Logo & Branding', 'custom-theme' ); ?></h2>
                            <p><?php esc_html_e( 'Upload your custom site logo and fine-tune desktop and mobile dimensions.', 'custom-theme' ); ?></p>
                        </div>

                        <div class="theme-field-row logo-uploader-row">
                            <label class="field-label"><?php esc_html_e( 'Site Logo Image', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <?php
                                $logo_url = get_theme_mod( 'custom_theme_logo_url', '' );
                                $logo_id  = get_theme_mod( 'custom_theme_logo_id', 0 );
                                if ( empty( $logo_url ) && has_custom_logo() ) {
                                    $logo_id  = get_theme_mod( 'custom_logo' );
                                    $logo_url = wp_get_attachment_image_url( $logo_id, 'full' );
                                }
                                ?>
                                <div class="logo-uploader-wrap">
                                    <div class="logo-preview-box" id="logo-preview-box" <?php echo empty( $logo_url ) ? 'style="display:none;"' : ''; ?>>
                                        <img src="<?php echo esc_url( $logo_url ); ?>" id="logo-preview-img" alt="<?php esc_attr_e( 'Logo Preview', 'custom-theme' ); ?>" style="max-height: <?php echo esc_attr( get_theme_mod( 'custom_theme_logo_max_height', 48 ) ); ?>px;">
                                    </div>
                                    <input type="hidden" name="custom_theme_logo_url" id="custom_theme_logo_url" value="<?php echo esc_url( $logo_url ); ?>">
                                    <input type="hidden" name="custom_theme_logo_id" id="custom_theme_logo_id" value="<?php echo esc_attr( $logo_id ); ?>">
                                    
                                    <div class="logo-uploader-actions">
                                        <button type="button" class="button button-secondary" id="upload-logo-btn">
                                            <span class="dashicons dashicons-upload"></span>
                                            <span class="upload-btn-text"><?php echo empty( $logo_url ) ? esc_html__( 'Upload / Select Logo', 'custom-theme' ) : esc_html__( 'Change Logo', 'custom-theme' ); ?></span>
                                        </button>
                                        <button type="button" class="button button-link-delete" id="remove-logo-btn" <?php echo empty( $logo_url ) ? 'style="display:none;"' : ''; ?>>
                                            <span class="dashicons dashicons-trash"></span>
                                            <span><?php esc_html_e( 'Remove Logo', 'custom-theme' ); ?></span>
                                        </button>
                                    </div>
                                </div>
                                <p class="field-desc"><?php esc_html_e( 'Upload a PNG, SVG, JPG, or WebP logo image. When uploaded, this replaces the site title text in the header.', 'custom-theme' ); ?></p>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_logo_max_height" class="field-label"><?php esc_html_e( 'Desktop Logo Max Height (px)', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="number" name="custom_theme_logo_max_height" id="custom_theme_logo_max_height" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_logo_max_height', 48 ) ); ?>" min="24" max="120" step="2">
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_logo_mobile_max_height" class="field-label"><?php esc_html_e( 'Mobile Logo Max Height (px)', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="number" name="custom_theme_logo_mobile_max_height" id="custom_theme_logo_mobile_max_height" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_logo_mobile_max_height', 36 ) ); ?>" min="18" max="80" step="2">
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_site_title_font_size" class="field-label"><?php esc_html_e( 'Site Title Font Size (px)', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="number" name="custom_theme_site_title_font_size" id="custom_theme_site_title_font_size" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_site_title_font_size', 24 ) ); ?>" min="16" max="48" step="1">
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Title & Tagline Display', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label><input type="checkbox" name="custom_theme_site_title_uppercase" value="1" <?php checked( get_theme_mod( 'custom_theme_site_title_uppercase', false ) ); ?>> <?php esc_html_e( 'Transform Site Title to Uppercase', 'custom-theme' ); ?></label><br>
                                <label><input type="checkbox" name="custom_theme_show_tagline" value="1" <?php checked( get_theme_mod( 'custom_theme_show_tagline', false ) ); ?>> <?php esc_html_e( 'Display Tagline under Site Title', 'custom-theme' ); ?></label>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: Navigation & Menu -->
                    <div class="tab-panel <?php echo ( 'navigation' === $active_tab ) ? 'is-active' : ''; ?>" id="tab-navigation">
                        <div class="panel-header">
                            <h2><?php esc_html_e( 'Navigation & Dropdown Styles', 'custom-theme' ); ?></h2>
                            <p><?php esc_html_e( 'Customize primary menu alignment, indicator style, and font sizes.', 'custom-theme' ); ?></p>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_nav_alignment" class="field-label"><?php esc_html_e( 'Menu Alignment', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <?php $na = get_theme_mod( 'custom_theme_nav_alignment', 'center' ); ?>
                                <select name="custom_theme_nav_alignment" id="custom_theme_nav_alignment">
                                    <option value="left" <?php selected( $na, 'left' ); ?>><?php esc_html_e( 'Left', 'custom-theme' ); ?></option>
                                    <option value="center" <?php selected( $na, 'center' ); ?>><?php esc_html_e( 'Center', 'custom-theme' ); ?></option>
                                    <option value="right" <?php selected( $na, 'right' ); ?>><?php esc_html_e( 'Right', 'custom-theme' ); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_nav_indicator_style" class="field-label"><?php esc_html_e( 'Active & Hover Indicator', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <?php $ni = get_theme_mod( 'custom_theme_nav_indicator_style', 'underline' ); ?>
                                <select name="custom_theme_nav_indicator_style" id="custom_theme_nav_indicator_style">
                                    <option value="underline" <?php selected( $ni, 'underline' ); ?>><?php esc_html_e( 'Animated Bottom Underline', 'custom-theme' ); ?></option>
                                    <option value="pill" <?php selected( $ni, 'pill' ); ?>><?php esc_html_e( 'Subtle Pill Background Badge', 'custom-theme' ); ?></option>
                                    <option value="dot" <?php selected( $ni, 'dot' ); ?>><?php esc_html_e( 'Accent Dot Under Link', 'custom-theme' ); ?></option>
                                    <option value="none" <?php selected( $ni, 'none' ); ?>><?php esc_html_e( 'Text Color Only (No Indicator)', 'custom-theme' ); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_nav_font_size" class="field-label"><?php esc_html_e( 'Navigation Font Size', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <?php $ns = get_theme_mod( 'custom_theme_nav_font_size', 'regular' ); ?>
                                <select name="custom_theme_nav_font_size" id="custom_theme_nav_font_size">
                                    <option value="small" <?php selected( $ns, 'small' ); ?>><?php esc_html_e( 'Small (13px)', 'custom-theme' ); ?></option>
                                    <option value="regular" <?php selected( $ns, 'regular' ); ?>><?php esc_html_e( 'Regular (14px)', 'custom-theme' ); ?></option>
                                    <option value="medium" <?php selected( $ns, 'medium' ); ?>><?php esc_html_e( 'Medium (15px)', 'custom-theme' ); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Uppercase Menu Items', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label><input type="checkbox" name="custom_theme_nav_uppercase" value="1" <?php checked( get_theme_mod( 'custom_theme_nav_uppercase', false ) ); ?>> <?php esc_html_e( 'Transform navigation links to uppercase', 'custom-theme' ); ?></label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_nav_link_color" class="field-label"><?php esc_html_e( 'Custom Navigation Link Colors', 'custom-theme' ); ?></label>
                            <div class="field-control flex-colors">
                                <div>
                                    <span class="mini-label"><?php esc_html_e( 'Link Color:', 'custom-theme' ); ?></span>
                                    <input type="text" name="custom_theme_nav_link_color" id="custom_theme_nav_link_color" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_nav_link_color', '' ) ); ?>">
                                </div>
                                <div>
                                    <span class="mini-label"><?php esc_html_e( 'Hover Color:', 'custom-theme' ); ?></span>
                                    <input type="text" name="custom_theme_nav_hover_color" id="custom_theme_nav_hover_color" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_nav_hover_color', '' ) ); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 5: Dark Mode Palette -->
                    <div class="tab-panel <?php echo ( 'darkmode' === $active_tab ) ? 'is-active' : ''; ?>" id="tab-darkmode">
                        <div class="panel-header">
                            <h2><?php esc_html_e( 'Dark Mode Color Palette', 'custom-theme' ); ?></h2>
                            <p><?php esc_html_e( 'Customize every shade, surface, heading, in-content link, and border in Dark Theme.', 'custom-theme' ); ?></p>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Dark Mode Feature', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_enable_dark_mode" value="1" <?php checked( get_theme_mod( 'custom_theme_enable_dark_mode', false ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Enable Dark Mode Toggle Switcher', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_dark_mode_default" class="field-label"><?php esc_html_e( 'Default Initial Scheme', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <?php $dmd = get_theme_mod( 'custom_theme_dark_mode_default', 'light' ); ?>
                                <select name="custom_theme_dark_mode_default" id="custom_theme_dark_mode_default">
                                    <option value="light" <?php selected( $dmd, 'light' ); ?>><?php esc_html_e( 'Light', 'custom-theme' ); ?></option>
                                    <option value="dark" <?php selected( $dmd, 'dark' ); ?>><?php esc_html_e( 'Dark', 'custom-theme' ); ?></option>
                                    <option value="auto" <?php selected( $dmd, 'auto' ); ?>><?php esc_html_e( 'Auto (Follow User OS / Browser)', 'custom-theme' ); ?></option>
                                </select>
                            </div>
                        </div>

                        <hr class="section-divider">
                        <h3><?php esc_html_e( 'Dark Theme Surface & Background Colors', 'custom-theme' ); ?></h3>

                        <div class="theme-field-row">
                            <label for="custom_theme_dark_bg_color" class="field-label"><?php esc_html_e( 'Dark Page Background', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="text" name="custom_theme_dark_bg_color" id="custom_theme_dark_bg_color" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_dark_bg_color', '' ) ); ?>" data-default-color="#0f1115">
                                <p class="field-desc"><?php esc_html_e( 'Default: #0f1115', 'custom-theme' ); ?></p>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_dark_surface_color" class="field-label"><?php esc_html_e( 'Dark Surface & Card Background', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="text" name="custom_theme_dark_surface_color" id="custom_theme_dark_surface_color" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_dark_surface_color', '' ) ); ?>" data-default-color="#181b20">
                                <p class="field-desc"><?php esc_html_e( 'Default: #181b20', 'custom-theme' ); ?></p>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_dark_surface_subtle" class="field-label"><?php esc_html_e( 'Dark Subtle Surface (Code, Chips)', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="text" name="custom_theme_dark_surface_subtle" id="custom_theme_dark_surface_subtle" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_dark_surface_subtle', '' ) ); ?>" data-default-color="#20242b">
                            </div>
                        </div>

                        <hr class="section-divider">
                        <h3><?php esc_html_e( 'Dark Theme Typography & Link Colors', 'custom-theme' ); ?></h3>

                        <div class="theme-field-row">
                            <label for="custom_theme_dark_text_main" class="field-label"><?php esc_html_e( 'Headings & Primary Text', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="text" name="custom_theme_dark_text_main" id="custom_theme_dark_text_main" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_dark_text_main', '' ) ); ?>" data-default-color="#f8fafc">
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_dark_text_secondary" class="field-label"><?php esc_html_e( 'Body & Paragraph Text', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="text" name="custom_theme_dark_text_secondary" id="custom_theme_dark_text_secondary" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_dark_text_secondary', '' ) ); ?>" data-default-color="#cbd5e1">
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_dark_text_muted" class="field-label"><?php esc_html_e( 'Muted Text & Meta (Dates, Read Time)', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="text" name="custom_theme_dark_text_muted" id="custom_theme_dark_text_muted" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_dark_text_muted', '' ) ); ?>" data-default-color="#94a3b8">
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_dark_accent_color" class="field-label"><?php esc_html_e( 'Dark Accent & Hover Color', 'custom-theme' ); ?></label>
                            <div class="field-control flex-colors">
                                <div>
                                    <span class="mini-label"><?php esc_html_e( 'Accent:', 'custom-theme' ); ?></span>
                                    <input type="text" name="custom_theme_dark_accent_color" id="custom_theme_dark_accent_color" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_dark_accent_color', '' ) ); ?>" data-default-color="#60a5fa">
                                </div>
                                <div>
                                    <span class="mini-label"><?php esc_html_e( 'Accent Hover:', 'custom-theme' ); ?></span>
                                    <input type="text" name="custom_theme_dark_accent_hover_color" id="custom_theme_dark_accent_hover_color" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_dark_accent_hover_color', '' ) ); ?>" data-default-color="#93c5fd">
                                </div>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_dark_link_color" class="field-label"><?php esc_html_e( 'In-Content Link & Hover Color', 'custom-theme' ); ?></label>
                            <div class="field-control flex-colors">
                                <div>
                                    <span class="mini-label"><?php esc_html_e( 'Link:', 'custom-theme' ); ?></span>
                                    <input type="text" name="custom_theme_dark_link_color" id="custom_theme_dark_link_color" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_dark_link_color', '' ) ); ?>" data-default-color="#60a5fa">
                                </div>
                                <div>
                                    <span class="mini-label"><?php esc_html_e( 'Link Hover:', 'custom-theme' ); ?></span>
                                    <input type="text" name="custom_theme_dark_link_hover_color" id="custom_theme_dark_link_hover_color" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_dark_link_hover_color', '' ) ); ?>" data-default-color="#93c5fd">
                                </div>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_dark_border_color" class="field-label"><?php esc_html_e( 'Dark Border & Divider Color', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="text" name="custom_theme_dark_border_color" id="custom_theme_dark_border_color" class="color-picker-field" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_dark_border_color', '' ) ); ?>" data-default-color="#2c323c">
                            </div>
                        </div>
                    </div>

                    <!-- TAB 6: Blog & Single Post Layout -->
                    <div class="tab-panel <?php echo ( 'blog' === $active_tab ) ? 'is-active' : ''; ?>" id="tab-blog">
                        <div class="panel-header">
                            <h2><?php esc_html_e( 'Blog & Single Post Template Settings', 'custom-theme' ); ?></h2>
                            <p><?php esc_html_e( 'Control sidebar visibility, content width, featured media, table of contents, author boxes, social sharing, and related posts.', 'custom-theme' ); ?></p>
                        </div>

                        <h3><?php esc_html_e( 'Blog Archive Layout', 'custom-theme' ); ?></h3>

                        <div class="theme-field-row">
                            <label for="custom_theme_archive_layout" class="field-label"><?php esc_html_e( 'Blog Archive Layout', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <?php $al = get_theme_mod( 'custom_theme_archive_layout', 'grid' ); ?>
                                <select name="custom_theme_archive_layout" id="custom_theme_archive_layout">
                                    <option value="grid" <?php selected( $al, 'grid' ); ?>><?php esc_html_e( 'Grid Cards (Default)', 'custom-theme' ); ?></option>
                                    <option value="list" <?php selected( $al, 'list' ); ?>><?php esc_html_e( 'List', 'custom-theme' ); ?></option>
                                    <option value="classic" <?php selected( $al, 'classic' ); ?>><?php esc_html_e( 'Classic Stream', 'custom-theme' ); ?></option>
                                </select>
                                <p class="field-desc"><?php esc_html_e( 'Choose how articles are displayed on blog, archive, and category pages.', 'custom-theme' ); ?></p>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_pagination_type" class="field-label"><?php esc_html_e( 'Archive Pagination Mode', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <?php $pt = get_theme_mod( 'custom_theme_pagination_type', 'numbered' ); ?>
                                <select name="custom_theme_pagination_type" id="custom_theme_pagination_type">
                                    <option value="numbered" <?php selected( $pt, 'numbered' ); ?>><?php esc_html_e( 'Standard Numbered Pagination (Classic)', 'custom-theme' ); ?></option>
                                    <option value="load_more" <?php selected( $pt, 'load_more' ); ?>><?php esc_html_e( 'AJAX "Load More" Button', 'custom-theme' ); ?></option>
                                    <option value="infinite" <?php selected( $pt, 'infinite' ); ?>><?php esc_html_e( 'Infinite Scroll (Auto-load on scroll)', 'custom-theme' ); ?></option>
                                </select>
                                <p class="field-desc"><?php esc_html_e( 'Select how subsequent posts are loaded on archive and category feeds.', 'custom-theme' ); ?></p>
                            </div>
                        </div>

                        <h3><?php esc_html_e( 'Single Blog Post Layout & Sidebar', 'custom-theme' ); ?></h3>

                        <div class="theme-field-row">
                            <label for="custom_theme_default_single_template" class="field-label"><?php esc_html_e( 'Default Single Post Layout', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <?php $dst = get_theme_mod( 'custom_theme_default_single_template', 'classic' ); ?>
                                <select name="custom_theme_default_single_template" id="custom_theme_default_single_template">
                                    <option value="classic" <?php selected( $dst, 'classic' ); ?>><?php esc_html_e( 'Classic Editorial (Standard Featured Image & Byline)', 'custom-theme' ); ?></option>
                                    <option value="magazine" <?php selected( $dst, 'magazine' ); ?>><?php esc_html_e( 'Magazine Hero (Full-Bleed Hero Image with Text Overlay)', 'custom-theme' ); ?></option>
                                    <option value="minimal" <?php selected( $dst, 'minimal' ); ?>><?php esc_html_e( 'Minimal Clean (Distraction-Free Centered Text)', 'custom-theme' ); ?></option>
                                </select>
                                <p class="field-desc"><?php esc_html_e( 'Choose the global default layout for all single blog articles. You can also override this on individual posts in the post editor.', 'custom-theme' ); ?></p>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Single Post Sidebar', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_single_show_sidebar" value="1" <?php checked( get_theme_mod( 'custom_theme_single_show_sidebar', true ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Enable sidebar on single blog posts (Disable for clean, centered full-width reading layout)', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_single_content_width" class="field-label"><?php esc_html_e( 'Single Post Reading Width', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <?php $scw = get_theme_mod( 'custom_theme_single_content_width', 'contained' ); ?>
                                <select name="custom_theme_single_content_width" id="custom_theme_single_content_width">
                                    <option value="contained" <?php selected( $scw, 'contained' ); ?>><?php esc_html_e( 'Standard Editorial (920px - Recommended)', 'custom-theme' ); ?></option>
                                    <option value="narrow" <?php selected( $scw, 'narrow' ); ?>><?php esc_html_e( 'Narrow Focused (780px)', 'custom-theme' ); ?></option>
                                    <option value="full" <?php selected( $scw, 'full' ); ?>><?php esc_html_e( 'Wide Container (1240px)', 'custom-theme' ); ?></option>
                                </select>
                                <p class="field-desc"><?php esc_html_e( 'Controls the maximum reading width for article content when sidebar is disabled.', 'custom-theme' ); ?></p>
                            </div>
                        </div>

                        <hr class="section-divider">
                        <h3><?php esc_html_e( 'Single Post Content Elements & Sections', 'custom-theme' ); ?></h3>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Featured Hero Image', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_single_show_featured_image" value="1" <?php checked( get_theme_mod( 'custom_theme_single_show_featured_image', true ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Display featured hero image at top of single articles', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Table of Contents (TOC)', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_single_show_toc" value="1" <?php checked( get_theme_mod( 'custom_theme_single_show_toc', true ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Automatically generate interactive Table of Contents box if 2+ headings exist', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Sticky Active TOC Tracking', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_toc_sticky" value="1" <?php checked( get_theme_mod( 'custom_theme_toc_sticky', true ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Highlight the active heading in TOC as reader scrolls through the article', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Distraction-Free Reading Mode', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_enable_reading_mode" value="1" <?php checked( get_theme_mod( 'custom_theme_enable_reading_mode', true ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Enable Reader View with custom themes (Light, Sepia, Dark) and typography controls', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Social Share Buttons', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_single_show_social_share" value="1" <?php checked( get_theme_mod( 'custom_theme_single_show_social_share', true ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Display social share buttons (X/Twitter, Facebook, LinkedIn, Copy Link)', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Author Bio Box', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_single_show_author_box" value="1" <?php checked( get_theme_mod( 'custom_theme_single_show_author_box', true ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Display author profile card and bio below post content', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Next / Previous Navigation', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_single_show_post_nav" value="1" <?php checked( get_theme_mod( 'custom_theme_single_show_post_nav', true ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Display Next / Previous article navigation cards', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Related Posts Section', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_single_show_related_posts" value="1" <?php checked( get_theme_mod( 'custom_theme_single_show_related_posts', true ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Display related articles section from matching category', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Comments Section', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_single_show_comments" value="1" <?php checked( get_theme_mod( 'custom_theme_single_show_comments', true ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Display comments list and discussion reply form on single posts', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <hr class="section-divider">
                        <h3><?php esc_html_e( 'Archive & Category Pages Layout', 'custom-theme' ); ?></h3>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Archive Pages Sidebar', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_archive_show_sidebar" value="1" <?php checked( get_theme_mod( 'custom_theme_archive_show_sidebar', true ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Display sidebar on Category, Tag, Author, and Search archive listings', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 7: Action Buttons & CTA -->
                    <div class="tab-panel <?php echo ( 'actions' === $active_tab ) ? 'is-active' : ''; ?>" id="tab-actions">
                        <div class="panel-header">
                            <h2><?php esc_html_e( 'Call-to-Action & Header Actions', 'custom-theme' ); ?></h2>
                            <p><?php esc_html_e( 'Manage search buttons and configure a custom Header CTA button (e.g. Subscribe, Contact).', 'custom-theme' ); ?></p>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Header Search Button', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label><input type="checkbox" name="custom_theme_show_header_search" value="1" <?php checked( get_theme_mod( 'custom_theme_show_header_search', true ) ); ?>> <?php esc_html_e( 'Show Smart Search modal button in header', 'custom-theme' ); ?></label>
                            </div>
                        </div>

                        <hr class="section-divider">
                        <h3><?php esc_html_e( 'Header Call-to-Action (CTA) Button', 'custom-theme' ); ?></h3>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Enable CTA Button', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_enable_header_cta" value="1" <?php checked( get_theme_mod( 'custom_theme_enable_header_cta', false ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Display prominent CTA button in header', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_header_cta_text" class="field-label"><?php esc_html_e( 'CTA Button Label', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="text" name="custom_theme_header_cta_text" id="custom_theme_header_cta_text" class="regular-text" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_header_cta_text', 'Subscribe' ) ); ?>">
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_header_cta_url" class="field-label"><?php esc_html_e( 'CTA Target Link URL', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="text" name="custom_theme_header_cta_url" id="custom_theme_header_cta_url" class="regular-text" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_header_cta_url', '#newsletter' ) ); ?>">
                                <p class="field-desc"><?php esc_html_e( 'Supports page anchors (e.g. #newsletter) or full external links.', 'custom-theme' ); ?></p>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Link Target', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label><input type="checkbox" name="custom_theme_header_cta_target" value="1" <?php checked( get_theme_mod( 'custom_theme_header_cta_target', false ) ); ?>> <?php esc_html_e( 'Open CTA link in a new tab', 'custom-theme' ); ?></label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_header_cta_style" class="field-label"><?php esc_html_e( 'CTA Button Style', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <?php $cs = get_theme_mod( 'custom_theme_header_cta_style', 'primary' ); ?>
                                <select name="custom_theme_header_cta_style" id="custom_theme_header_cta_style">
                                    <option value="primary" <?php selected( $cs, 'primary' ); ?>><?php esc_html_e( 'Filled Accent Button (Primary)', 'custom-theme' ); ?></option>
                                    <option value="outline" <?php selected( $cs, 'outline' ); ?>><?php esc_html_e( 'Outlined Accent Button', 'custom-theme' ); ?></option>
                                    <option value="subtle" <?php selected( $cs, 'subtle' ); ?>><?php esc_html_e( 'Subtle Surface Pill Button', 'custom-theme' ); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 7: Newsletter & Social -->
                    <div class="tab-panel <?php echo ( 'newsletter' === $active_tab ) ? 'is-active' : ''; ?>" id="tab-newsletter">
                        <div class="panel-header">
                            <h2><?php esc_html_e( 'Newsletter Callout & Social Links', 'custom-theme' ); ?></h2>
                            <p><?php esc_html_e( 'Configure the homepage email signup section and social media profile URLs.', 'custom-theme' ); ?></p>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_newsletter_title" class="field-label"><?php esc_html_e( 'Newsletter Headline', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="text" name="custom_theme_newsletter_title" id="custom_theme_newsletter_title" class="regular-text" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_newsletter_title', 'Get thoughtful stories delivered directly to your inbox.' ) ); ?>">
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_newsletter_desc" class="field-label"><?php esc_html_e( 'Newsletter Description', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <textarea name="custom_theme_newsletter_desc" id="custom_theme_newsletter_desc" rows="3" class="large-text"><?php echo esc_textarea( get_theme_mod( 'custom_theme_newsletter_desc', 'Join our weekly digest featuring deep dives, editorial commentary, and design inspiration. No spam, ever.' ) ); ?></textarea>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_newsletter_action" class="field-label"><?php esc_html_e( 'Newsletter Endpoint URL', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="text" name="custom_theme_newsletter_action" id="custom_theme_newsletter_action" class="regular-text" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_newsletter_action', '' ) ); ?>" placeholder="https://your-service.com/subscribe">
                            </div>
                        </div>

                        <hr class="section-divider">
                        <h3><?php esc_html_e( 'Social Media Profiles', 'custom-theme' ); ?></h3>

                        <?php
                        $social_fields = array(
                            'twitter'   => esc_html__( 'X / Twitter URL', 'custom-theme' ),
                            'facebook'  => esc_html__( 'Facebook URL', 'custom-theme' ),
                            'linkedin'  => esc_html__( 'LinkedIn URL', 'custom-theme' ),
                            'instagram' => esc_html__( 'Instagram URL', 'custom-theme' ),
                            'github'    => esc_html__( 'GitHub URL', 'custom-theme' ),
                            'youtube'   => esc_html__( 'YouTube URL', 'custom-theme' ),
                        );
                        foreach ( $social_fields as $s_key => $s_label ) :
                            ?>
                            <div class="theme-field-row">
                                <label for="custom_theme_social_<?php echo esc_attr( $s_key ); ?>" class="field-label"><?php echo esc_html( $s_label ); ?></label>
                                <div class="field-control">
                                    <input type="text" name="custom_theme_social_<?php echo esc_attr( $s_key ); ?>" id="custom_theme_social_<?php echo esc_attr( $s_key ); ?>" class="regular-text" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_social_' . $s_key, '' ) ); ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- TAB 8: Footer & Sticky -->
                    <div class="tab-panel <?php echo ( 'footer' === $active_tab ) ? 'is-active' : ''; ?>" id="tab-footer">
                        <div class="panel-header">
                            <h2><?php esc_html_e( 'Footer & Sticky Scroll Controls', 'custom-theme' ); ?></h2>
                            <p><?php esc_html_e( 'Configure footer visibility, bio text, custom copyright message, and sticky header scroll behavior.', 'custom-theme' ); ?></p>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Display Site Footer', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_display_footer" value="1" <?php checked( get_theme_mod( 'custom_theme_display_footer', true ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Display theme footer on site (Disable if using a page builder custom footer template)', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_footer_bio" class="field-label"><?php esc_html_e( 'Footer About / Bio Text', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <textarea name="custom_theme_footer_bio" id="custom_theme_footer_bio" rows="3" class="large-text" placeholder="<?php esc_attr_e( 'Leave empty to use standard site tagline.', 'custom-theme' ); ?>"><?php echo esc_textarea( get_theme_mod( 'custom_theme_footer_bio', '' ) ); ?></textarea>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_footer_copyright" class="field-label"><?php esc_html_e( 'Custom Copyright Notice', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <input type="text" name="custom_theme_footer_copyright" id="custom_theme_footer_copyright" class="regular-text" value="<?php echo esc_attr( get_theme_mod( 'custom_theme_footer_copyright', '' ) ); ?>" placeholder="<?php esc_attr_e( 'Leave empty for automatic year & site name copyright.', 'custom-theme' ); ?>">
                            </div>
                        </div>

                        <hr class="section-divider">
                        <h3><?php esc_html_e( 'Sticky Header Scroll Behavior', 'custom-theme' ); ?></h3>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Sticky Header', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label class="theme-switch">
                                    <input type="checkbox" name="custom_theme_sticky_header" value="1" <?php checked( get_theme_mod( 'custom_theme_sticky_header', true ) ); ?>>
                                    <span class="switch-slider"></span>
                                    <span class="switch-label"><?php esc_html_e( 'Enable Sticky Header', 'custom-theme' ); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_sticky_behavior" class="field-label"><?php esc_html_e( 'Sticky Scroll Mode', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <?php $sm = get_theme_mod( 'custom_theme_sticky_behavior', 'smart-hide' ); ?>
                                <select name="custom_theme_sticky_behavior" id="custom_theme_sticky_behavior">
                                    <option value="smart-hide" <?php selected( $sm, 'smart-hide' ); ?>><?php esc_html_e( 'Smart Hide (Hide on Scroll Down, Reveal on Scroll Up)', 'custom-theme' ); ?></option>
                                    <option value="always-fixed" <?php selected( $sm, 'always-fixed' ); ?>><?php esc_html_e( 'Always Fixed Continuous', 'custom-theme' ); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label class="field-label"><?php esc_html_e( 'Glassmorphism Blur', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <label><input type="checkbox" name="custom_theme_sticky_blur" value="1" <?php checked( get_theme_mod( 'custom_theme_sticky_blur', true ) ); ?>> <?php esc_html_e( 'Enable backdrop-filter blur on sticky state', 'custom-theme' ); ?></label>
                            </div>
                        </div>

                        <div class="theme-field-row">
                            <label for="custom_theme_sticky_shadow" class="field-label"><?php esc_html_e( 'Sticky Shadow Depth', 'custom-theme' ); ?></label>
                            <div class="field-control">
                                <?php $ss = get_theme_mod( 'custom_theme_sticky_shadow', 'subtle' ); ?>
                                <select name="custom_theme_sticky_shadow" id="custom_theme_sticky_shadow">
                                    <option value="subtle" <?php selected( $ss, 'subtle' ); ?>><?php esc_html_e( 'Subtle Depth Shadow', 'custom-theme' ); ?></option>
                                    <option value="medium" <?php selected( $ss, 'medium' ); ?>><?php esc_html_e( 'Elevated Medium Shadow', 'custom-theme' ); ?></option>
                                    <option value="none" <?php selected( $ss, 'none' ); ?>><?php esc_html_e( 'No Shadow (Flat Border Only)', 'custom-theme' ); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 10: System & Tools -->
                    <div class="tab-panel <?php echo ( 'tools' === $active_tab ) ? 'is-active' : ''; ?>" id="tab-tools">
                        <div class="panel-header">
                            <h2><?php esc_html_e( 'System Tools & Media Maintenance', 'custom-theme' ); ?></h2>
                            <p><?php esc_html_e( 'Manage media assets, thumbnail crops, and performance utilities for your theme.', 'custom-theme' ); ?></p>
                        </div>

                        <?php
                        $media_counts = wp_count_attachments( 'image' );
                        $total_images = is_object( $media_counts ) ? array_sum( (array) $media_counts ) : 0;
                        if ( $total_images === 0 ) {
                            global $wpdb;
                            $total_images = (int) $wpdb->get_var( "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%' AND post_status != 'trash'" );
                        }
                        ?>

                        <div class="tools-card-box">
                            <div class="tools-card-header">
                                <div class="tools-card-icon">
                                    <span class="dashicons dashicons-images-alt2"></span>
                                </div>
                                <div class="tools-card-title-group">
                                    <h3><?php esc_html_e( 'Media Library Thumbnail Regenerator', 'custom-theme' ); ?></h3>
                                    <p><?php esc_html_e( 'Bulk process and generate all registered image dimensions for existing media attachments.', 'custom-theme' ); ?></p>
                                </div>
                            </div>

                            <div class="tools-card-body">
                                <div class="tools-feature-badges">
                                    <span class="tools-badge tools-badge-active">
                                        <span class="dashicons dashicons-yes"></span>
                                        <strong><?php esc_html_e( 'JIT On-Demand Engine:', 'custom-theme' ); ?></strong> <?php esc_html_e( 'Active (Missing sizes generate automatically on first page view)', 'custom-theme' ); ?>
                                    </span>
                                </div>

                                <p class="tools-desc-text">
                                    <?php esc_html_e( 'This tool will iterate through your media library and generate all 5 custom theme thumbnail crops for full frontend compatibility:', 'custom-theme' ); ?>
                                </p>

                                <ul class="theme-sizes-list">
                                    <li><code>custom-theme-featured</code> &mdash; <strong>1200 &times; 675 px</strong> (16:9 Hero Spotlight)</li>
                                    <li><code>custom-theme-card</code> &mdash; <strong>700 &times; 465 px</strong> (3:2 Grid Cards)</li>
                                    <li><code>custom-theme-compact</code> &mdash; <strong>320 &times; 215 px</strong> (Magazine Secondary Cards)</li>
                                    <li><code>custom-theme-thumbnail</code> &mdash; <strong>160 &times; 120 px</strong> (Sidebar &amp; Live Search Thumbnails)</li>
                                    <li><code>custom-theme-avatar</code> &mdash; <strong>96 &times; 96 px</strong> (Author Avatars)</li>
                                </ul>

                                <div class="tools-stats-bar">
                                    <span class="dashicons dashicons-media-default"></span>
                                    <span><?php echo sprintf( esc_html__( 'Total Images in Media Library: %s', 'custom-theme' ), '<strong>' . number_format_i18n( $total_images ) . '</strong>' ); ?></span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="tools-action-buttons">
                                    <button type="button" class="button button-primary button-large" id="btn-start-regenerate-thumbs" <?php disabled( $total_images === 0 ); ?>>
                                        <span class="dashicons dashicons-update"></span>
                                        <span class="btn-text"><?php esc_html_e( 'Regenerate All Thumbnails', 'custom-theme' ); ?></span>
                                    </button>
                                    <button type="button" class="button button-secondary button-large" id="btn-pause-regenerate-thumbs" style="display:none;">
                                        <span class="dashicons dashicons-controls-pause"></span>
                                        <span><?php esc_html_e( 'Pause', 'custom-theme' ); ?></span>
                                    </button>
                                </div>

                                <!-- Progress UI (Initially Hidden) -->
                                <div id="regenerate-progress-box" class="regenerate-progress-box" style="display:none;">
                                    <div class="progress-info-row">
                                        <span class="progress-status-title" id="regenerate-status-title"><?php esc_html_e( 'Processing thumbnails...', 'custom-theme' ); ?></span>
                                        <span class="progress-stats-counter" id="regenerate-stats-text">0 / 0 (0%)</span>
                                    </div>

                                    <div class="progress-track">
                                        <div class="progress-fill" id="regenerate-bar-fill" style="width: 0%;"></div>
                                    </div>

                                    <p class="progress-current-item" id="regenerate-current-item"><?php esc_html_e( 'Initializing batch...', 'custom-theme' ); ?></p>

                                    <!-- Live Activity Log -->
                                    <div class="regenerate-log-container">
                                        <div class="regenerate-log-header">
                                            <span><?php esc_html_e( 'Activity Log', 'custom-theme' ); ?></span>
                                            <button type="button" class="button-link" id="btn-clear-regenerate-log"><?php esc_html_e( 'Clear', 'custom-theme' ); ?></button>
                                        </div>
                                        <ul class="regenerate-log-list" id="regenerate-log-list"></ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div><!-- .theme-tabs-content -->

            </div><!-- .theme-options-layout -->

            <!-- Sticky Save Changes Footer Bar -->
            <div class="theme-options-footer">
                <button type="submit" class="button button-primary button-hero theme-save-btn">
                    <span class="dashicons dashicons-saved"></span>
                    <span><?php esc_html_e( 'Save All Changes', 'custom-theme' ); ?></span>
                </button>
            </div>

        </form>

    </div><!-- .custom-theme-admin-wrap -->
    <?php
}
