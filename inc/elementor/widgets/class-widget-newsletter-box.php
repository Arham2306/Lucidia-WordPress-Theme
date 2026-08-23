<?php
/**
 * Elementor Widget: Newsletter Subscription Box
 *
 * Displays an editorial newsletter subscription callout banner or card with
 * built-in form endpoints, third-party shortcode embeds, and customizable styling.
 *
 * @package Custom_Theme
 */

namespace CustomTheme\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Newsletter_Box extends Widget_Base {

    /**
     * Get Widget Name
     *
     * @return string
     */
    public function get_name() {
        return 'custom_theme_newsletter_box';
    }

    /**
     * Get Widget Title
     *
     * @return string
     */
    public function get_title() {
        return esc_html__( 'Newsletter Subscription Box', 'custom-theme' );
    }

    /**
     * Get Widget Icon
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-mail';
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
        return array( 'newsletter', 'subscribe', 'email', 'signup', 'mailchimp', 'substack', 'convertkit', 'form', 'cta' );
    }

    /**
     * Register Widget Controls
     */
    protected function register_controls() {

        // =========================================================================
        // CONTENT TAB: Text & Content
        // =========================================================================
        $this->start_controls_section(
            'section_content',
            array(
                'label' => esc_html__( 'Text & Content', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        // Badge
        $this->add_control(
            'show_badge',
            array(
                'label'        => esc_html__( 'Show Top Badge', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'badge_text',
            array(
                'label'       => esc_html__( 'Badge Text', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'Stay Informed', 'custom-theme' ),
                'dynamic'     => array( 'active' => true ),
                'condition'   => array(
                    'show_badge' => 'yes',
                ),
            )
        );

        // Title
        $this->add_control(
            'title_text',
            array(
                'label'       => esc_html__( 'Headline Title', 'custom-theme' ),
                'type'        => Controls_Manager::TEXTAREA,
                'rows'        => 2,
                'default'     => esc_html__( 'Get thoughtful stories delivered directly to your inbox.', 'custom-theme' ),
                'dynamic'     => array( 'active' => true ),
                'separator'   => 'before',
            )
        );

        $this->add_control(
            'title_tag',
            array(
                'label'   => esc_html__( 'Title HTML Tag', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'h3',
                'options' => array(
                    'h2'   => 'H2',
                    'h3'   => 'H3',
                    'h4'   => 'H4',
                    'div'  => 'div',
                ),
            )
        );

        // Description
        $this->add_control(
            'show_description',
            array(
                'label'        => esc_html__( 'Show Description', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'description_text',
            array(
                'label'       => esc_html__( 'Description Text', 'custom-theme' ),
                'type'        => Controls_Manager::TEXTAREA,
                'rows'        => 3,
                'default'     => esc_html__( 'Join our weekly digest featuring deep dives, editorial commentary, and design inspiration. No spam, ever.', 'custom-theme' ),
                'dynamic'     => array( 'active' => true ),
                'condition'   => array(
                    'show_description' => 'yes',
                ),
            )
        );

        // Privacy Note
        $this->add_control(
            'show_privacy',
            array(
                'label'        => esc_html__( 'Show Privacy Note', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
                'separator'    => 'before',
            )
        );

        $this->add_control(
            'privacy_text',
            array(
                'label'       => esc_html__( 'Privacy Note', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'We respect your privacy. Unsubscribe at any time.', 'custom-theme' ),
                'dynamic'     => array( 'active' => true ),
                'condition'   => array(
                    'show_privacy' => 'yes',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // CONTENT TAB: Form Integration
        // =========================================================================
        $this->start_controls_section(
            'section_form_settings',
            array(
                'label' => esc_html__( 'Form Integration', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'form_type',
            array(
                'label'   => esc_html__( 'Form Type', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'builtin',
                'options' => array(
                    'builtin'   => esc_html__( 'Built-in Lucidia Form', 'custom-theme' ),
                    'shortcode' => esc_html__( 'Third-Party Plugin Shortcode', 'custom-theme' ),
                    'embed'     => esc_html__( 'Custom HTML / JS Embed (Substack, ConvertKit...)', 'custom-theme' ),
                ),
            )
        );

        // Builtin Form Controls
        $this->add_control(
            'action_url',
            array(
                'label'       => esc_html__( 'Action Endpoint URL', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'https://your-service.com/subscribe',
                'description' => esc_html__( 'Leave blank to use theme default or trigger JS alert confirmation.', 'custom-theme' ),
                'condition'   => array(
                    'form_type' => 'builtin',
                ),
            )
        );

        $this->add_control(
            'input_placeholder',
            array(
                'label'     => esc_html__( 'Email Input Placeholder', 'custom-theme' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => esc_html__( 'Enter your email address...', 'custom-theme' ),
                'condition' => array(
                    'form_type' => 'builtin',
                ),
            )
        );

        $this->add_control(
            'button_text',
            array(
                'label'     => esc_html__( 'Button Text', 'custom-theme' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => esc_html__( 'Subscribe', 'custom-theme' ),
                'condition' => array(
                    'form_type' => 'builtin',
                ),
            )
        );

        $this->add_control(
            'show_button_icon',
            array(
                'label'        => esc_html__( 'Show Button Arrow Icon', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition'    => array(
                    'form_type' => 'builtin',
                ),
            )
        );

        // Shortcode Control
        $this->add_control(
            'plugin_shortcode',
            array(
                'label'       => esc_html__( 'Shortcode', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => '[mc4wp_form id="123"]',
                'description' => esc_html__( 'Paste any form shortcode (Mailchimp, MailPoet, Fluent Forms, etc.)', 'custom-theme' ),
                'condition'   => array(
                    'form_type' => 'shortcode',
                ),
            )
        );

        // Embed Code Control
        $this->add_control(
            'embed_html',
            array(
                'label'       => esc_html__( 'Embed Code / HTML', 'custom-theme' ),
                'type'        => Controls_Manager::CODE,
                'language'    => 'html',
                'placeholder' => '<form action="...">...</form>',
                'description' => esc_html__( 'Paste Substack, ConvertKit, Beehiiv, or custom HTML embed code.', 'custom-theme' ),
                'condition'   => array(
                    'form_type' => 'embed',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // CONTENT TAB: Layout & Presets
        // =========================================================================
        $this->start_controls_section(
            'section_layout_settings',
            array(
                'label' => esc_html__( 'Layout & Presets', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'box_layout',
            array(
                'label'   => esc_html__( 'Layout Mode', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'split',
                'options' => array(
                    'split'   => esc_html__( 'Split (Content Left, Form Right)', 'custom-theme' ),
                    'stacked' => esc_html__( 'Stacked Card (Centered / Sidebar format)', 'custom-theme' ),
                ),
            )
        );

        $this->add_control(
            'box_preset',
            array(
                'label'   => esc_html__( 'Preset Theme', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'gradient_accent',
                'options' => array(
                    'gradient_accent' => esc_html__( 'Gradient with Ambient Glow', 'custom-theme' ),
                    'surface_card'    => esc_html__( 'Surface Card (Bordered)', 'custom-theme' ),
                    'dark_luxury'     => esc_html__( 'Dark Luxury Editorial', 'custom-theme' ),
                    'borderless'      => esc_html__( 'Borderless Minimal Flow', 'custom-theme' ),
                ),
            )
        );

        $this->add_control(
            'show_ambient_glow',
            array(
                'label'        => esc_html__( 'Ambient Corner Glow', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition'    => array(
                    'box_preset' => array( 'gradient_accent', 'surface_card', 'dark_luxury' ),
                ),
            )
        );

        $this->add_responsive_control(
            'container_max_width',
            array(
                'label'      => esc_html__( 'Max Width (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px', '%' ),
                'range'      => array(
                    'px' => array(
                        'min' => 320,
                        'max' => 1200,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-newsletter-box' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Box Container
        // =========================================================================
        $this->start_controls_section(
            'section_style_box',
            array(
                'label' => esc_html__( 'Box Container', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
                'name'     => 'box_background',
                'selector' => '{{WRAPPER}} .editorial-newsletter-box',
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'box_border',
                'selector' => '{{WRAPPER}} .editorial-newsletter-box',
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'box_shadow',
                'selector' => '{{WRAPPER}} .editorial-newsletter-box',
            )
        );

        $this->add_responsive_control(
            'box_padding',
            array(
                'label'      => esc_html__( 'Inner Padding', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', 'rem' ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-newsletter-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'box_border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-newsletter-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Badge, Title & Description
        // =========================================================================
        $this->start_controls_section(
            'section_style_typography',
            array(
                'label' => esc_html__( 'Badge, Title & Description', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        // Badge Style
        $this->add_control(
            'heading_badge_style',
            array(
                'label'     => esc_html__( 'Badge', 'custom-theme' ),
                'type'      => Controls_Manager::HEADING,
                'condition' => array(
                    'show_badge' => 'yes',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'      => 'badge_typography',
                'selector'  => '{{WRAPPER}} .newsletter-badge',
                'condition' => array(
                    'show_badge' => 'yes',
                ),
            )
        );

        $this->add_control(
            'badge_color',
            array(
                'label'     => esc_html__( 'Badge Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'show_badge' => 'yes',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .newsletter-badge' => 'color: {{VALUE}};',
                ),
            )
        );

        // Title Style
        $this->add_control(
            'heading_title_style',
            array(
                'label'     => esc_html__( 'Title', 'custom-theme' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .newsletter-title',
            )
        );

        $this->add_control(
            'title_color',
            array(
                'label'     => esc_html__( 'Title Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .newsletter-title' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'title_spacing',
            array(
                'label'      => esc_html__( 'Bottom Spacing (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 40,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .newsletter-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        // Description Style
        $this->add_control(
            'heading_desc_style',
            array(
                'label'     => esc_html__( 'Description', 'custom-theme' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => array(
                    'show_description' => 'yes',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'      => 'desc_typography',
                'selector'  => '{{WRAPPER}} .newsletter-description',
                'condition' => array(
                    'show_description' => 'yes',
                ),
            )
        );

        $this->add_control(
            'desc_color',
            array(
                'label'     => esc_html__( 'Description Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'show_description' => 'yes',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .newsletter-description' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Input & Submit Button
        // =========================================================================
        $this->start_controls_section(
            'section_style_form',
            array(
                'label'     => esc_html__( 'Input & Submit Button', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'form_type' => 'builtin',
                ),
            )
        );

        // Input Field
        $this->add_control(
            'heading_input_style',
            array(
                'label' => esc_html__( 'Email Input Field', 'custom-theme' ),
                'type'  => Controls_Manager::HEADING,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'input_typography',
                'selector' => '{{WRAPPER}} .newsletter-input',
            )
        );

        $this->add_control(
            'input_bg_color',
            array(
                'label'     => esc_html__( 'Input Background', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .newsletter-input' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_text_color',
            array(
                'label'     => esc_html__( 'Input Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .newsletter-input' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_border_color',
            array(
                'label'     => esc_html__( 'Input Border Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .newsletter-input' => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'input_border_radius',
            array(
                'label'      => esc_html__( 'Input Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .newsletter-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        // Submit Button
        $this->add_control(
            'heading_button_style',
            array(
                'label'     => esc_html__( 'Submit Button', 'custom-theme' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'button_typography',
                'selector' => '{{WRAPPER}} .newsletter-submit-btn',
            )
        );

        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab(
            'tab_btn_normal',
            array(
                'label' => esc_html__( 'Normal', 'custom-theme' ),
            )
        );

        $this->add_control(
            'button_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .newsletter-submit-btn' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'button_text_color',
            array(
                'label'     => esc_html__( 'Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .newsletter-submit-btn'       => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .newsletter-submit-btn svg'   => 'stroke: {{VALUE}} !important; color: {{VALUE}} !important;',
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
            'button_bg_hover_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .newsletter-submit-btn:hover' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'button_text_hover_color',
            array(
                'label'     => esc_html__( 'Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .newsletter-submit-btn:hover'       => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .newsletter-submit-btn:hover svg'   => 'stroke: {{VALUE}} !important; color: {{VALUE}} !important;',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'button_border_radius',
            array(
                'label'      => esc_html__( 'Button Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'separator'  => 'before',
                'selectors'  => array(
                    '{{WRAPPER}} .newsletter-submit-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        // Privacy Note Style
        $this->add_control(
            'heading_privacy_style',
            array(
                'label'     => esc_html__( 'Privacy Note', 'custom-theme' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => array(
                    'show_privacy' => 'yes',
                ),
            )
        );

        $this->add_control(
            'privacy_color',
            array(
                'label'     => esc_html__( 'Privacy Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'show_privacy' => 'yes',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .newsletter-privacy' => 'color: {{VALUE}};',
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

        $show_badge   = 'yes' === $settings['show_badge'] && ! empty( $settings['badge_text'] );
        $title_text   = ! empty( $settings['title_text'] ) ? $settings['title_text'] : '';
        $title_tag    = ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h3';
        $show_desc    = 'yes' === $settings['show_description'] && ! empty( $settings['description_text'] );
        $show_privacy = 'yes' === $settings['show_privacy'] && ! empty( $settings['privacy_text'] );
        $form_type    = ! empty( $settings['form_type'] ) ? $settings['form_type'] : 'builtin';
        $box_layout   = ! empty( $settings['box_layout'] ) ? $settings['box_layout'] : 'split';
        $box_preset   = ! empty( $settings['box_preset'] ) ? $settings['box_preset'] : 'gradient_accent';
        $ambient_glow = 'yes' === $settings['show_ambient_glow'];

        $section_classes = array(
            'newsletter-section',
            'editorial-newsletter-box',
            'layout-' . esc_attr( $box_layout ),
            'preset-' . esc_attr( $box_preset ),
        );

        if ( ! $ambient_glow ) {
            $section_classes[] = 'no-glow';
        }

        $action_url = ! empty( $settings['action_url'] ) ? $settings['action_url'] : get_theme_mod( 'custom_theme_newsletter_action', '' );
        ?>
        <section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>" aria-label="<?php esc_attr_e( 'Newsletter Subscription', 'custom-theme' ); ?>">
            <div class="newsletter-inner">

                <div class="newsletter-content">
                    <?php if ( $show_badge ) : ?>
                        <span class="newsletter-badge"><?php echo esc_html( $settings['badge_text'] ); ?></span>
                    <?php endif; ?>

                    <?php if ( ! empty( $title_text ) ) : ?>
                        <<?php echo esc_attr( $title_tag ); ?> class="newsletter-title">
                            <?php echo esc_html( $title_text ); ?>
                        </<?php echo esc_attr( $title_tag ); ?>>
                    <?php endif; ?>

                    <?php if ( $show_desc ) : ?>
                        <p class="newsletter-description"><?php echo esc_html( $settings['description_text'] ); ?></p>
                    <?php endif; ?>
                </div><!-- .newsletter-content -->

                <div class="newsletter-form-wrapper">
                    <?php if ( 'builtin' === $form_type ) : ?>
                        <form action="<?php echo esc_url( $action_url ? $action_url : '#' ); ?>" method="post" class="newsletter-form" <?php echo empty( $action_url ) ? 'onsubmit="event.preventDefault(); alert(\'' . esc_js( __( 'Thank you for subscribing!', 'custom-theme' ) ) . '\');"' : ''; ?>>
                            <div class="newsletter-input-group">
                                <input type="email" name="newsletter_email" class="newsletter-input" placeholder="<?php echo esc_attr( ! empty( $settings['input_placeholder'] ) ? $settings['input_placeholder'] : __( 'Enter your email address...', 'custom-theme' ) ); ?>" required>
                                <button type="submit" class="btn btn-primary newsletter-submit-btn">
                                    <span><?php echo esc_html( ! empty( $settings['button_text'] ) ? $settings['button_text'] : __( 'Subscribe', 'custom-theme' ) ); ?></span>
                                    <?php
                                    if ( 'yes' === $settings['show_button_icon'] && function_exists( 'custom_theme_svg_icon' ) ) {
                                        echo custom_theme_svg_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    }
                                    ?>
                                </button>
                            </div>
                            <?php if ( $show_privacy ) : ?>
                                <p class="newsletter-privacy"><?php echo esc_html( $settings['privacy_text'] ); ?></p>
                            <?php endif; ?>
                        </form>
                    <?php elseif ( 'shortcode' === $form_type && ! empty( $settings['plugin_shortcode'] ) ) : ?>
                        <div class="newsletter-plugin-form">
                            <?php echo do_shortcode( shortcode_unautop( $settings['plugin_shortcode'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    <?php elseif ( 'embed' === $form_type && ! empty( $settings['embed_html'] ) ) : ?>
                        <div class="newsletter-embed-form">
                            <?php echo $settings['embed_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </div>
                    <?php endif; ?>
                </div><!-- .newsletter-form-wrapper -->

            </div><!-- .newsletter-inner -->
        </section><!-- .editorial-newsletter-box -->
        <?php
    }
}
