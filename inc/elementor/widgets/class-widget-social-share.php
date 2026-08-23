<?php
/**
 * Elementor Widget: Interactive Social Share Bar
 *
 * Displays an interactive multi-network social share bar with live clipboard copy,
 * native Web Share API support, customizable layouts, and brand presets.
 *
 * @package Custom_Theme
 */

namespace CustomTheme\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Social_Share extends Widget_Base {

    /**
     * Get Widget Name
     *
     * @return string
     */
    public function get_name() {
        return 'custom_theme_social_share';
    }

    /**
     * Get Widget Title
     *
     * @return string
     */
    public function get_title() {
        return esc_html__( 'Interactive Social Share Bar', 'custom-theme' );
    }

    /**
     * Get Widget Icon
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-share';
    }

    /**
     * Get Widget Categories
     *
     * @return array
     */
    public function get_categories() {
        return array( 'custom-editorial' );
    }

    /**
     * Get Widget Keywords
     *
     * @return array
     */
    public function get_keywords() {
        return array( 'share', 'social', 'twitter', 'facebook', 'linkedin', 'copy', 'link', 'buttons', 'viral' );
    }

    /**
     * Register Widget Controls
     */
    protected function register_controls() {

        // =========================================================================
        // CONTENT TAB: Share Target & Networks
        // =========================================================================
        $this->start_controls_section(
            'section_networks',
            array(
                'label' => esc_html__( 'Share Networks & Target', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'target_type',
            array(
                'label'   => esc_html__( 'Share URL Target', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'current_page',
                'options' => array(
                    'current_page' => esc_html__( 'Current Post / Page URL', 'custom-theme' ),
                    'custom_url'   => esc_html__( 'Custom Target URL', 'custom-theme' ),
                ),
            )
        );

        $this->add_control(
            'custom_url',
            array(
                'label'       => esc_html__( 'Custom URL to Share', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'https://your-domain.com/story',
                'dynamic'     => array( 'active' => true ),
                'condition'   => array(
                    'target_type' => 'custom_url',
                ),
            )
        );

        $this->add_control(
            'custom_title',
            array(
                'label'       => esc_html__( 'Custom Share Title', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'Story Title...',
                'dynamic'     => array( 'active' => true ),
                'condition'   => array(
                    'target_type' => 'custom_url',
                ),
            )
        );

        // Network Toggles
        $this->add_control(
            'heading_networks_toggle',
            array(
                'label'     => esc_html__( 'Enabled Networks', 'custom-theme' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_control(
            'show_x',
            array(
                'label'        => esc_html__( 'X (formerly Twitter)', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'show_facebook',
            array(
                'label'        => esc_html__( 'Facebook', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'show_linkedin',
            array(
                'label'        => esc_html__( 'LinkedIn', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'show_email',
            array(
                'label'        => esc_html__( 'Email', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'show_copy',
            array(
                'label'        => esc_html__( 'Copy Link (with Toast)', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // CONTENT TAB: Layout & Label
        // =========================================================================
        $this->start_controls_section(
            'section_layout_settings',
            array(
                'label' => esc_html__( 'Layout & Presentation', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_label',
            array(
                'label'        => esc_html__( 'Show "Share:" Label', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'label_text',
            array(
                'label'     => esc_html__( 'Label Text', 'custom-theme' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => esc_html__( 'Share:', 'custom-theme' ),
                'condition' => array(
                    'show_label' => 'yes',
                ),
            )
        );

        $this->add_control(
            'layout_orientation',
            array(
                'label'   => esc_html__( 'Orientation', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'horizontal',
                'options' => array(
                    'horizontal' => esc_html__( 'Horizontal Row', 'custom-theme' ),
                    'vertical'   => esc_html__( 'Vertical Column / Sidebar Strip', 'custom-theme' ),
                ),
            )
        );

        $this->add_responsive_control(
            'alignment',
            array(
                'label'     => esc_html__( 'Alignment', 'custom-theme' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => array(
                    'flex-start' => array(
                        'title' => esc_html__( 'Left', 'custom-theme' ),
                        'icon'  => 'eicon-text-align-left',
                    ),
                    'center'     => array(
                        'title' => esc_html__( 'Center', 'custom-theme' ),
                        'icon'  => 'eicon-text-align-center',
                    ),
                    'flex-end'   => array(
                        'title' => esc_html__( 'Right', 'custom-theme' ),
                        'icon'  => 'eicon-text-align-right',
                    ),
                    'space-between' => array(
                        'title' => esc_html__( 'Space Between', 'custom-theme' ),
                        'icon'  => 'eicon-text-align-justify',
                    ),
                ),
                'default'   => 'flex-start',
                'selectors' => array(
                    '{{WRAPPER}} .social-share-bar' => 'justify-content: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'button_style_preset',
            array(
                'label'   => esc_html__( 'Button Shape Preset', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'circular',
                'options' => array(
                    'circular'      => esc_html__( 'Circular Badge (Default)', 'custom-theme' ),
                    'rounded_pill'  => esc_html__( 'Rounded Pill', 'custom-theme' ),
                    'square_smooth' => esc_html__( 'Smooth Square', 'custom-theme' ),
                    'minimal_flat'  => esc_html__( 'Minimal Flat (Icon Only)', 'custom-theme' ),
                    'brand_colored' => esc_html__( 'Brand Colored Icons', 'custom-theme' ),
                ),
            )
        );

        $this->add_control(
            'show_network_names',
            array(
                'label'        => esc_html__( 'Show Network Text Labels', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Yes', 'custom-theme' ),
                'label_off'    => esc_html__( 'No', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'no',
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Share Buttons
        // =========================================================================
        $this->start_controls_section(
            'section_style_buttons',
            array(
                'label' => esc_html__( 'Share Buttons', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_responsive_control(
            'button_size',
            array(
                'label'      => esc_html__( 'Button Dimensions (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 28,
                        'max' => 64,
                    ),
                ),
                'default'    => array(
                    'size' => 36,
                ),
                'condition'  => array(
                    'show_network_names!' => 'yes',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .share-btn' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'icon_size',
            array(
                'label'      => esc_html__( 'Icon Size (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 12,
                        'max' => 32,
                    ),
                ),
                'default'    => array(
                    'size' => 16,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .share-btn svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'buttons_gap',
            array(
                'label'      => esc_html__( 'Gap Between Buttons (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 2,
                        'max' => 30,
                    ),
                ),
                'default'    => array(
                    'size' => 6,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .share-buttons-group' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->start_controls_tabs( 'tabs_btn_style' );

        $this->start_controls_tab(
            'tab_btn_normal',
            array(
                'label' => esc_html__( 'Normal', 'custom-theme' ),
            )
        );

        $this->add_control(
            'btn_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .share-btn' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'btn_text_color',
            array(
                'label'     => esc_html__( 'Icon / Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .share-btn'       => 'color: {{VALUE}};',
                    '{{WRAPPER}} .share-btn svg'   => 'color: {{VALUE}}; stroke: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'btn_border_color',
            array(
                'label'     => esc_html__( 'Border Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .share-btn' => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_btn_hover',
            array(
                'label' => esc_html__( 'Hover', 'custom-theme' ),
            )
        );

        $this->add_control(
            'btn_bg_hover_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .share-btn:hover' => 'background-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'btn_text_hover_color',
            array(
                'label'     => esc_html__( 'Icon / Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .share-btn:hover'       => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .share-btn:hover svg'   => 'color: {{VALUE}} !important; stroke: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'btn_border_hover_color',
            array(
                'label'     => esc_html__( 'Border Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .share-btn:hover' => 'border-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_responsive_control(
            'btn_hover_lift',
            array(
                'label'      => esc_html__( 'Hover Lift (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => -8,
                        'max' => 0,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .share-btn:hover' => 'transform: translateY({{SIZE}}{{UNIT}});',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'btn_border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'separator'  => 'before',
                'selectors'  => array(
                    '{{WRAPPER}} .share-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Share Label
        // =========================================================================
        $this->start_controls_section(
            'section_style_label',
            array(
                'label'     => esc_html__( 'Share Label', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'show_label' => 'yes',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'      => 'label_typography',
                'selector'  => '{{WRAPPER}} .share-label',
                'condition' => array(
                    'show_label' => 'yes',
                ),
            )
        );

        $this->add_control(
            'label_color',
            array(
                'label'     => esc_html__( 'Label Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'show_label' => 'yes',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .share-label' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'label_spacing',
            array(
                'label'      => esc_html__( 'Label Spacing (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 30,
                    ),
                ),
                'default'    => array(
                    'size' => 10,
                ),
                'condition'  => array(
                    'show_label' => 'yes',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .share-label' => 'margin-right: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Render Widget Output
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        // Resolve Target URL & Title
        $target_url   = ( 'custom_url' === $settings['target_type'] && ! empty( $settings['custom_url'] ) ) ? $settings['custom_url'] : get_permalink();
        $target_title = ( 'custom_url' === $settings['target_type'] && ! empty( $settings['custom_title'] ) ) ? $settings['custom_title'] : get_the_title();

        $encoded_url   = rawurlencode( $target_url );
        $encoded_title = rawurlencode( html_entity_decode( $target_title, ENT_QUOTES, 'UTF-8' ) );

        $show_label    = 'yes' === $settings['show_label'] && ! empty( $settings['label_text'] );
        $orientation   = ! empty( $settings['layout_orientation'] ) ? $settings['layout_orientation'] : 'horizontal';
        $preset        = ! empty( $settings['button_style_preset'] ) ? $settings['button_style_preset'] : 'circular';
        $show_names    = 'yes' === $settings['show_network_names'];

        $bar_classes = array(
            'social-share-bar',
            'editorial-social-share',
            'orientation-' . esc_attr( $orientation ),
            'preset-' . esc_attr( $preset ),
        );

        if ( $show_names ) {
            $bar_classes[] = 'has-network-names';
        }
        ?>
        <div class="<?php echo esc_attr( implode( ' ', $bar_classes ) ); ?>" aria-label="<?php esc_attr_e( 'Share this story', 'custom-theme' ); ?>">

            <?php if ( $show_label ) : ?>
                <span class="share-label"><?php echo esc_html( $settings['label_text'] ); ?></span>
            <?php endif; ?>

            <div class="share-buttons-group">

                <?php if ( 'yes' === $settings['show_x'] ) : ?>
                    <a href="<?php echo esc_url( "https://twitter.com/intent/tweet?text={$encoded_title}&url={$encoded_url}" ); ?>" class="share-btn share-btn-x" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Share on X', 'custom-theme' ); ?>" title="<?php esc_attr_e( 'Share on X', 'custom-theme' ); ?>">
                        <?php
                        if ( function_exists( 'custom_theme_svg_icon' ) ) {
                            echo custom_theme_svg_icon( 'x-twitter' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }
                        ?>
                        <?php if ( $show_names ) : ?>
                            <span class="share-btn-text"><?php esc_html_e( 'X', 'custom-theme' ); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

                <?php if ( 'yes' === $settings['show_facebook'] ) : ?>
                    <a href="<?php echo esc_url( "https://www.facebook.com/sharer/sharer.php?u={$encoded_url}" ); ?>" class="share-btn share-btn-facebook" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Share on Facebook', 'custom-theme' ); ?>" title="<?php esc_attr_e( 'Share on Facebook', 'custom-theme' ); ?>">
                        <?php
                        if ( function_exists( 'custom_theme_svg_icon' ) ) {
                            echo custom_theme_svg_icon( 'facebook' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }
                        ?>
                        <?php if ( $show_names ) : ?>
                            <span class="share-btn-text"><?php esc_html_e( 'Facebook', 'custom-theme' ); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

                <?php if ( 'yes' === $settings['show_linkedin'] ) : ?>
                    <a href="<?php echo esc_url( "https://www.linkedin.com/sharing/share-offsite/?url={$encoded_url}" ); ?>" class="share-btn share-btn-linkedin" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'custom-theme' ); ?>" title="<?php esc_attr_e( 'Share on LinkedIn', 'custom-theme' ); ?>">
                        <?php
                        if ( function_exists( 'custom_theme_svg_icon' ) ) {
                            echo custom_theme_svg_icon( 'linkedin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }
                        ?>
                        <?php if ( $show_names ) : ?>
                            <span class="share-btn-text"><?php esc_html_e( 'LinkedIn', 'custom-theme' ); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

                <?php if ( 'yes' === $settings['show_email'] ) : ?>
                    <a href="<?php echo esc_url( "mailto:?subject={$encoded_title}&body={$encoded_url}" ); ?>" class="share-btn share-btn-email" aria-label="<?php esc_attr_e( 'Share via Email', 'custom-theme' ); ?>" title="<?php esc_attr_e( 'Share via Email', 'custom-theme' ); ?>">
                        <?php
                        if ( function_exists( 'custom_theme_svg_icon' ) ) {
                            echo custom_theme_svg_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }
                        ?>
                        <?php if ( $show_names ) : ?>
                            <span class="share-btn-text"><?php esc_html_e( 'Email', 'custom-theme' ); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>

                <?php if ( 'yes' === $settings['show_copy'] ) : ?>
                    <button type="button" class="share-btn share-btn-copy" data-url="<?php echo esc_url( $target_url ); ?>" aria-label="<?php esc_attr_e( 'Copy Link', 'custom-theme' ); ?>" title="<?php esc_attr_e( 'Copy Link', 'custom-theme' ); ?>">
                        <?php
                        if ( function_exists( 'custom_theme_svg_icon' ) ) {
                            echo custom_theme_svg_icon( 'link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }
                        ?>
                        <?php if ( $show_names ) : ?>
                            <span class="share-btn-text"><?php esc_html_e( 'Copy Link', 'custom-theme' ); ?></span>
                        <?php endif; ?>
                    </button>
                <?php endif; ?>

            </div><!-- .share-buttons-group -->

        </div><!-- .social-share-bar -->
        <?php
    }
}
