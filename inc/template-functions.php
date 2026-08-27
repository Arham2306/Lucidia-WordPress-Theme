<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function custom_theme_body_classes( $classes ) {
    // Adds a class of hfeed to non-singular pages.
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }

    if ( is_single() ) {
        $sidebar_override = get_post_meta( get_the_ID(), '_custom_theme_sidebar_override', true );
        if ( 'show' === $sidebar_override ) {
            $is_single_no_sidebar = false;
        } elseif ( 'hide' === $sidebar_override ) {
            $is_single_no_sidebar = true;
        } else {
            $is_single_no_sidebar = ! get_theme_mod( 'custom_theme_single_show_sidebar', true );
        }
    } else {
        $is_single_no_sidebar = false;
    }
    $is_archive_no_sidebar = ( is_archive() || is_search() ) && ! get_theme_mod( 'custom_theme_archive_show_sidebar', true );

    // Adds a class of no-sidebar on full-width templates, pages, or when sidebar is disabled.
    if ( is_page_template( 'page-templates/template-full-width.php' ) || is_page_template( 'page-templates/template-canvas.php' ) || is_page() || $is_single_no_sidebar || $is_archive_no_sidebar ) {
        $classes[] = 'no-sidebar';
    } else {
        $classes[] = 'has-sidebar';
    }

    if ( $is_single_no_sidebar ) {
        $classes[] = 'single-no-sidebar';
        $width_override = get_post_meta( get_the_ID(), '_custom_theme_width_override', true );
        $single_w = ( ! empty( $width_override ) && 'inherit' !== $width_override ) ? $width_override : get_theme_mod( 'custom_theme_single_content_width', 'contained' );
        $classes[] = 'single-width-' . sanitize_html_class( $single_w );
    }

    if ( is_single() ) {
        $tmpl = get_post_meta( get_the_ID(), '_custom_theme_single_template', true );
        $active_tmpl = ( ! empty( $tmpl ) && 'inherit' !== $tmpl ) ? $tmpl : get_theme_mod( 'custom_theme_default_single_template', 'classic' );
        $classes[] = 'single-template-' . sanitize_html_class( $active_tmpl );
    }

    // Add class if current single post has featured image
    if ( is_singular() && has_post_thumbnail() ) {
        $classes[] = 'has-post-thumbnail';
    }

    return $classes;
}
add_filter( 'body_class', 'custom_theme_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function custom_theme_pingback_header() {
    if ( is_singular() && pings_open() ) {
        printf( '<link rel="pingback" href="%s">' . "\n", esc_url( get_bloginfo( 'pingback_url' ) ) );
    }
}
add_action( 'wp_head', 'custom_theme_pingback_header' );

/**
 * Output Schema.org JSON-LD structured data for single articles.
 */
function custom_theme_schema_article() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }

    global $post;

    $author_id = $post->post_author;
    $schema = array(
        '@context'         => 'https://schema.org',
        '@type'            => 'BlogPosting',
        'mainEntityOfPage' => array(
            '@type' => 'WebPage',
            '@id'   => get_permalink( $post->ID ),
        ),
        'headline'         => get_the_title( $post->ID ),
        'datePublished'    => get_the_date( DATE_W3C, $post->ID ),
        'dateModified'     => get_the_modified_date( DATE_W3C, $post->ID ),
        'author'           => array(
            '@type' => 'Person',
            'name'  => get_the_author_meta( 'display_name', $author_id ),
            'url'   => get_author_posts_url( $author_id ),
        ),
        'publisher'        => array(
            '@type' => 'Organization',
            'name'  => get_bloginfo( 'name' ),
            'url'   => home_url( '/' ),
        ),
        'description'      => wp_strip_all_tags( get_the_excerpt( $post->ID ) ),
    );

    if ( has_post_thumbnail( $post->ID ) ) {
        $image_id  = get_post_thumbnail_id( $post->ID );
        $image_src = wp_get_attachment_image_src( $image_id, 'full' );
        if ( $image_src ) {
            $schema['image'] = array(
                '@type'  => 'ImageObject',
                'url'    => $image_src[0],
                'width'  => $image_src[1],
                'height' => $image_src[2],
            );
        }
    }

    echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}
add_action( 'wp_head', 'custom_theme_schema_article' );

/**
 * Output Open Graph and Twitter Card meta tags.
 */
function custom_theme_open_graph_meta() {
    // Site Name
    echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
    // Locale
    echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . '">' . "\n";
    
    // Type
    $og_type = is_single() ? 'article' : 'website';
    echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '">' . "\n";
    
    // URL
    global $wp;
    $og_url = is_singular() ? get_permalink() : home_url( add_query_arg( array(), $wp->request ) );
    echo '<meta property="og:url" content="' . esc_url( $og_url ) . '">' . "\n";
    
    // Title
    $og_title = '';
    if ( is_singular() ) {
        $og_title = get_the_title();
    } elseif ( is_archive() ) {
        $og_title = get_the_archive_title();
    } else {
        $og_title = get_bloginfo( 'name' );
    }
    $og_title = wp_strip_all_tags( $og_title );
    echo '<meta property="og:title" content="' . esc_attr( $og_title ) . '">' . "\n";
    
    // Description
    $og_desc = '';
    if ( is_singular() ) {
        $og_desc = has_excerpt() ? get_the_excerpt() : get_post()->post_content;
    } elseif ( is_archive() ) {
        $og_desc = get_the_archive_description();
    }
    
    $og_desc = wp_trim_words( wp_strip_all_tags( $og_desc ), 30, '' );
    if ( empty( $og_desc ) ) {
        $og_desc = get_bloginfo( 'description' );
    }
    echo '<meta property="og:description" content="' . esc_attr( $og_desc ) . '">' . "\n";
    
    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $og_title ) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $og_desc ) . '">' . "\n";
    
    // Single post specific tags
    if ( is_single() ) {
        echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . "\n";
        echo '<meta property="article:author" content="' . esc_attr( get_the_author() ) . '">' . "\n";
        $categories = get_the_category();
        if ( ! empty( $categories ) ) {
            echo '<meta property="article:section" content="' . esc_attr( $categories[0]->name ) . '">' . "\n";
        }
    }
    
    // Image for Singular (pages and posts)
    if ( is_singular() && has_post_thumbnail() ) {
        $image_id  = get_post_thumbnail_id();
        $image_src = wp_get_attachment_image_src( $image_id, 'full' );
        if ( $image_src ) {
            echo '<meta property="og:image" content="' . esc_url( $image_src[0] ) . '">' . "\n";
            echo '<meta property="og:image:width" content="' . esc_attr( $image_src[1] ) . '">' . "\n";
            echo '<meta property="og:image:height" content="' . esc_attr( $image_src[2] ) . '">' . "\n";
            echo '<meta name="twitter:image" content="' . esc_url( $image_src[0] ) . '">' . "\n";
        }
    }
}
add_action( 'wp_head', 'custom_theme_open_graph_meta', 5 );

/**
 * Return inline SVG icons.
 *
 * @param string $name  Icon identifier.
 * @param string $class Extra CSS class.
 * @return string Inline SVG code.
 */
function custom_theme_svg_icon( $name, $class = '' ) {
    $class_attr = 'icon icon-' . esc_attr( $name ) . ( $class ? ' ' . esc_attr( $class ) : '' );

    static $icons = null;
    if ( $icons === null ) {
        $icons = array(
            'search' => '<svg class="' . $class_attr . '" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            'menu'   => '<svg class="' . $class_attr . '" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>',
            'close'  => '<svg class="' . $class_attr . '" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
            'clock'  => '<svg class="' . $class_attr . '" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>',
            'calendar' => '<svg class="' . $class_attr . '" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>',
            'user'   => '<svg class="' . $class_attr . '" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
            'chevron-right' => '<svg class="' . $class_attr . '" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"></polyline></svg>',
            'chevron-left' => '<svg class="' . $class_attr . '" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>',
            'chevron-down' => '<svg class="' . $class_attr . '" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>',
            'arrow-up' => '<svg class="' . $class_attr . '" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>',
            'arrow-right' => '<svg class="' . $class_attr . '" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>',
            'x-twitter' => '<svg class="' . $class_attr . '" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
            'facebook' => '<svg class="' . $class_attr . '" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
            'linkedin' => '<svg class="' . $class_attr . '" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.2V10.9H6.46M7.83 6.45a1.67 1.67 0 1 0 0 3.34 1.67 1.67 0 0 0 0-3.34z"/></svg>',
            'mail'   => '<svg class="' . $class_attr . '" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
            'link'   => '<svg class="' . $class_attr . '" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>',
            'tag'    => '<svg class="' . $class_attr . '" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>',
            'folder' => '<svg class="' . $class_attr . '" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>',
            'article' => '<svg class="' . $class_attr . '" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
            'check'  => '<svg class="' . $class_attr . '" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>',
            'arrow-top-right' => '<svg class="' . $class_attr . '" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>',
            'bookmark' => '<svg class="' . $class_attr . '" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>',
            'bookmark-filled' => '<svg class="' . $class_attr . '" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>',
            'book-open' => '<svg class="' . $class_attr . '" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>',
            'spinner' => '<svg class="' . $class_attr . ' spin-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="2" x2="12" y2="6"></line><line x1="12" y1="18" x2="12" y2="22"></line><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line><line x1="2" y1="12" x2="6" y2="12"></line><line x1="18" y1="12" x2="22" y2="12"></line><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line></svg>',
            'trash' => '<svg class="' . $class_attr . '" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>',
            'font-size' => '<svg class="' . $class_attr . '" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="4 7 4 4 20 4 20 7"></polyline><line x1="9" y1="20" x2="15" y2="20"></line><line x1="12" y1="4" x2="12" y2="20"></line></svg>',
        );
    }

    return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

/**
 * Append dropdown chevron icon to navigation menu items that have children.
 *
 * @param string   $title The menu item's title.
 * @param \WP_Post $item  The current menu item.
 * @param \stdClass $args  An object of wp_nav_menu() arguments.
 * @param int      $depth Depth of menu item.
 * @return string Modified title.
 */
function custom_theme_nav_menu_dropdown_icon( $title, $item, $args, $depth ) {
    // Exclude mobile navigation
    if ( is_object( $args ) ) {
        if ( ! empty( $args->menu_class ) && false !== strpos( $args->menu_class, 'mobile' ) ) {
            return $title;
        }
        if ( ! empty( $args->menu_id ) && false !== strpos( $args->menu_id, 'mobile' ) ) {
            return $title;
        }
    } elseif ( is_array( $args ) ) {
        if ( ! empty( $args['menu_class'] ) && false !== strpos( $args['menu_class'], 'mobile' ) ) {
            return $title;
        }
        if ( ! empty( $args['menu_id'] ) && false !== strpos( $args['menu_id'], 'mobile' ) ) {
            return $title;
        }
    }

    if ( ! empty( $item->classes ) && in_array( 'menu-item-has-children', (array) $item->classes, true ) ) {
        if ( false === strpos( $title, 'nav-dropdown-chevron' ) ) {
            $title .= '<span class="nav-dropdown-chevron" aria-hidden="true">' . custom_theme_svg_icon( 'chevron-down' ) . '</span>';
        }
    }
    return $title;
}
add_filter( 'nav_menu_item_title', 'custom_theme_nav_menu_dropdown_icon', 10, 4 );


