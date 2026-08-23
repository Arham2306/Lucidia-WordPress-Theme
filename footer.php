<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #page div and all content after.
 *
 * @package Custom_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$footer_bio       = get_theme_mod( 'custom_theme_footer_bio', '' );
$footer_copyright = get_theme_mod( 'custom_theme_footer_copyright', '' );

$social_twitter   = get_theme_mod( 'custom_theme_social_twitter', '' );
$social_facebook  = get_theme_mod( 'custom_theme_social_facebook', '' );
$social_linkedin  = get_theme_mod( 'custom_theme_social_linkedin', '' );
$social_instagram = get_theme_mod( 'custom_theme_social_instagram', '' );
$social_github    = get_theme_mod( 'custom_theme_social_github', '' );
$social_youtube   = get_theme_mod( 'custom_theme_social_youtube', '' );
?>

    <?php
    $show_footer = get_theme_mod( 'custom_theme_display_footer', true );
    if ( is_singular() ) {
        $footer_override = get_post_meta( get_the_ID(), '_custom_theme_footer_override', true );
        if ( 'show' === $footer_override ) {
            $show_footer = true;
        } elseif ( 'hide' === $footer_override ) {
            $show_footer = false;
        }
    }
    if ( $show_footer ) :
    ?>
    <footer id="colophon" class="site-footer" role="contentinfo">
        
        <!-- Main Multi-column Footer Container -->
        <div class="container footer-container">
            <div class="footer-grid">

                <!-- Column 1: Brand & About -->
                <div class="footer-col footer-col-brand">
                    <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
                        <?php dynamic_sidebar( 'footer-1' ); ?>
                    <?php else : ?>
                        <div class="footer-branding">
                            <span class="footer-title"><?php bloginfo( 'name' ); ?></span>
                        </div>
                        <p class="footer-about">
                            <?php
                            if ( ! empty( $footer_bio ) ) {
                                echo esc_html( $footer_bio );
                            } else {
                                $custom_theme_description = get_bloginfo( 'description', 'display' );
                                if ( $custom_theme_description ) {
                                    echo esc_html( $custom_theme_description );
                                } else {
                                    esc_html_e( 'An editorial publication delivering thoughtful insights, in-depth articles, and modern stories.', 'custom-theme' );
                                }
                            }
                            ?>
                        </p>
                        <div class="footer-social-links">
                            <a href="<?php echo esc_url( $social_twitter ? $social_twitter : '#' ); ?>" class="social-icon-btn" aria-label="<?php esc_attr_e( 'X / Twitter', 'custom-theme' ); ?>" target="_blank" rel="noopener noreferrer"><?php echo custom_theme_svg_icon( 'x-twitter' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
                            <a href="<?php echo esc_url( $social_facebook ? $social_facebook : '#' ); ?>" class="social-icon-btn" aria-label="<?php esc_attr_e( 'Facebook', 'custom-theme' ); ?>" target="_blank" rel="noopener noreferrer"><?php echo custom_theme_svg_icon( 'facebook' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
                            <a href="<?php echo esc_url( $social_linkedin ? $social_linkedin : '#' ); ?>" class="social-icon-btn" aria-label="<?php esc_attr_e( 'LinkedIn', 'custom-theme' ); ?>" target="_blank" rel="noopener noreferrer"><?php echo custom_theme_svg_icon( 'linkedin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
                            <a href="mailto:<?php bloginfo( 'admin_email' ); ?>" class="social-icon-btn" aria-label="<?php esc_attr_e( 'Email', 'custom-theme' ); ?>"><?php echo custom_theme_svg_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Column 2: Navigation / Quick Links -->
                <div class="footer-col footer-col-nav">
                    <?php if ( is_active_sidebar( 'footer-2' ) ) : ?>
                        <?php dynamic_sidebar( 'footer-2' ); ?>
                    <?php else : ?>
                        <h3 class="footer-heading"><?php esc_html_e( 'Navigation', 'custom-theme' ); ?></h3>
                        <?php
                        if ( has_nav_menu( 'footer' ) ) :
                            wp_nav_menu(
                                array(
                                    'theme_location' => 'footer',
                                    'menu_class'     => 'footer-links-list',
                                    'container'      => false,
                                    'depth'          => 1,
                                )
                            );
                        else :
                            ?>
                            <ul class="footer-links-list">
                                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'custom-theme' ); ?></a></li>
                                <?php
                                $pages = get_posts(array(
                                    'post_type' => 'page',
                                    'posts_per_page' => 4,
                                    'no_found_rows' => true,
                                    'update_post_meta_cache' => false,
                                    'update_post_term_cache' => false,
                                    'orderby' => 'menu_order',
                                    'order' => 'ASC',
                                ));
                                foreach ( $pages as $page_item ) :
                                    ?>
                                    <li><a href="<?php echo esc_url( get_permalink( $page_item->ID ) ); ?>"><?php echo esc_html( $page_item->post_title ); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Column 3: Categories -->
                <div class="footer-col footer-col-categories">
                    <?php if ( is_active_sidebar( 'footer-3' ) ) : ?>
                        <?php dynamic_sidebar( 'footer-3' ); ?>
                    <?php else : ?>
                        <h3 class="footer-heading"><?php esc_html_e( 'Topics', 'custom-theme' ); ?></h3>
                        <ul class="footer-links-list">
                            <?php
                            $footer_cats = get_transient('custom_theme_nav_categories');
                            if (false === $footer_cats) {
                                $footer_cats = get_categories(array('orderby' => 'count', 'order' => 'DESC', 'number' => 6));
                                set_transient('custom_theme_nav_categories', $footer_cats, HOUR_IN_SECONDS);
                            }
                            $footer_cats = array_slice($footer_cats, 0, 5);
                            foreach ( $footer_cats as $cat ) :
                                ?>
                                <li>
                                    <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
                                        <span><?php echo esc_html( $cat->name ); ?></span>
                                        <span class="count-badge"><?php echo esc_html( $cat->count ); ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Column 4: Recent Stories -->
                <div class="footer-col footer-col-recent">
                    <?php if ( is_active_sidebar( 'footer-4' ) ) : ?>
                        <?php dynamic_sidebar( 'footer-4' ); ?>
                    <?php else : ?>
                        <h3 class="footer-heading"><?php esc_html_e( 'Recent Stories', 'custom-theme' ); ?></h3>
                        <div class="footer-recent-posts">
                            <?php
                            $footer_posts = new WP_Query(
                                array(
                                    'posts_per_page'      => 2,
                                    'post_status'         => 'publish',
                                    'ignore_sticky_posts' => 1,
                                    'no_found_rows'       => true,
                                )
                            );

                            if ( $footer_posts->have_posts() ) :
                                while ( $footer_posts->have_posts() ) :
                                    $footer_posts->the_post();
                                    ?>
                                    <article class="footer-post-mini">
                                        <h4 class="footer-post-title">
                                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                        </h4>
                                        <div class="footer-post-meta">
                                            <time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
                                        </div>
                                    </article>
                                    <?php
                                endwhile;
                                wp_reset_postdata();
                            endif;
                            ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div><!-- .footer-grid -->
        </div><!-- .container -->

        <!-- Bottom Footer Row -->
        <div class="site-footer-bottom">
            <div class="container bottom-container">
                <p class="copyright-text">
                    <?php if ( ! empty( $footer_copyright ) ) : ?>
                        <?php echo esc_html( $footer_copyright ); ?>
                    <?php else : ?>
                        &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>. <?php esc_html_e( 'All rights reserved.', 'custom-theme' ); ?>
                    <?php endif; ?>
                </p>
                <p class="credit-text">
                    <?php
                    /* translators: %s: Theme name */
                    printf( esc_html__( 'Designed with %s for WordPress', 'custom-theme' ), '<span>Lucidia</span>' );
                    ?>
                </p>
            </div>
        </div>

    </footer><!-- #colophon -->
    <?php endif; // End custom_theme_display_footer ?>

    <!-- Floating Back to Top Button -->
    <button id="back-to-top" class="back-to-top-btn" aria-label="<?php esc_attr_e( 'Back to top of page', 'custom-theme' ); ?>" hidden>
        <?php echo custom_theme_svg_icon( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </button>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
