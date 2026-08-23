<?php
/**
 * Elementor Widget: Section Header Bar
 *
 * Displays a customizable editorial section header bar with title, icon, subtitle,
 * decorative border accents, and "View All" action links.
 *
 * @package Custom_Theme
 */

namespace CustomTheme\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Section_Header extends Widget_Base {

    /**
     * Get Widget Name
     *
     * @return string
     */
    public function get_name() {
        return 'custom_theme_section_header';
    }

    /**
     * Get Widget Title
     *
     * @return string
     */
    public function get_title() {
        return esc_html__( 'Section Header Bar', 'custom-theme' );
    }

    /**
     * Get Widget Icon
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-heading';
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
        return array( 'header', 'title', 'heading', 'section', 'bar', 'view all', 'category', 'divider' );
    }

    /**
     * Register Widget Controls
     */
    protected function register_controls() {

        // =========================================================================
        // CONTENT TAB: Title & Subtitle
        // =========================================================================
        $this->start_controls_section(
            'section_title_content',
            array(
                'label' => esc_html__( 'Title & Subtitle', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'title_text',
            array(
                'label'       => esc_html__( 'Title', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'Latest Stories', 'custom-theme' ),
                'placeholder' => esc_html__( 'Enter section title', 'custom-theme' ),
                'dynamic'     => array( 'active' => true ),
                'label_block' => true,
            )
        );

        $this->add_control(
            'title_tag',
            array(
                'label'   => esc_html__( 'Title HTML Tag', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'h2',
                'options' => array(
                    'h1'   => 'H1',
                    'h2'   => 'H2',
                    'h3'   => 'H3',
                    'h4'   => 'H4',
                    'h5'   => 'H5',
                    'h6'   => 'H6',
                    'div'  => 'div',
                    'span' => 'span',
                ),
            )
        );

        $this->add_control(
            'show_subtitle',
            array(
                'label'        => esc_html__( 'Show Subtitle', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'no',
            )
        );

        $this->add_control(
            'subtitle_text',
            array(
                'label'       => esc_html__( 'Subtitle', 'custom-theme' ),
                'type'        => Controls_Manager::TEXTAREA,
                'rows'        => 2,
                'default'     => esc_html__( 'Explore curated perspectives and in-depth journalism.', 'custom-theme' ),
                'dynamic'     => array( 'active' => true ),
                'condition'   => array(
                    'show_subtitle' => 'yes',
                ),
            )
        );

        // Icon Controls
        $this->add_control(
            'show_icon',
            array(
                'label'        => esc_html__( 'Show Title Icon', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
                'separator'    => 'before',
            )
        );

        $this->add_control(
            'icon_source',
            array(
                'label'     => esc_html__( 'Icon Source', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'builtin',
                'options'   => array(
                    'builtin' => esc_html__( 'Lucidia Theme SVGs', 'custom-theme' ),
                    'custom'  => esc_html__( 'Elementor Icons / Upload SVG', 'custom-theme' ),
                ),
                'condition' => array(
                    'show_icon' => 'yes',
                ),
            )
        );

        $this->add_control(
            'builtin_icon',
            array(
                'label'     => esc_html__( 'Built-in Icon', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'article',
                'options'   => array(
                    'article'         => esc_html__( 'Article Document', 'custom-theme' ),
                    'folder'          => esc_html__( 'Folder Category', 'custom-theme' ),
                    'tag'             => esc_html__( 'Tag Label', 'custom-theme' ),
                    'bookmark'        => esc_html__( 'Bookmark', 'custom-theme' ),
                    'book-open'       => esc_html__( 'Open Book', 'custom-theme' ),
                    'clock'           => esc_html__( 'Clock / Time', 'custom-theme' ),
                    'calendar'        => esc_html__( 'Calendar', 'custom-theme' ),
                    'user'            => esc_html__( 'User / Author', 'custom-theme' ),
                    'search'          => esc_html__( 'Search', 'custom-theme' ),
                    'arrow-top-right' => esc_html__( 'Arrow Diagonal', 'custom-theme' ),
                ),
                'condition' => array(
                    'show_icon'   => 'yes',
                    'icon_source' => 'builtin',
                ),
            )
        );

        $this->add_control(
            'custom_icon',
            array(
                'label'     => esc_html__( 'Custom Icon', 'custom-theme' ),
                'type'      => Controls_Manager::ICONS,
                'default'   => array(
                    'value'   => 'fas fa-newspaper',
                    'library' => 'fa-solid',
                ),
                'condition' => array(
                    'show_icon'   => 'yes',
                    'icon_source' => 'custom',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // CONTENT TAB: Action Link ("View All")
        // =========================================================================
        $this->start_controls_section(
            'section_link_content',
            array(
                'label' => esc_html__( 'Action Link ("View All")', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_link',
            array(
                'label'        => esc_html__( 'Show Action Link', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'link_text',
            array(
                'label'       => esc_html__( 'Link Text', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'View all articles', 'custom-theme' ),
                'dynamic'     => array( 'active' => true ),
                'condition'   => array(
                    'show_link' => 'yes',
                ),
            )
        );

        $this->add_control(
            'link_url',
            array(
                'label'       => esc_html__( 'Link URL', 'custom-theme' ),
                'type'        => Controls_Manager::URL,
                'placeholder' => esc_html__( 'https://your-link.com', 'custom-theme' ),
                'dynamic'     => array( 'active' => true ),
                'default'     => array(
                    'url'         => '#',
                    'is_external' => false,
                    'nofollow'    => false,
                ),
                'condition'   => array(
                    'show_link' => 'yes',
                ),
            )
        );

        $this->add_control(
            'link_style',
            array(
                'label'     => esc_html__( 'Link Style', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'text_link',
                'options'   => array(
                    'text_link'      => esc_html__( 'Editorial Inline Text Link', 'custom-theme' ),
                    'button_pill'    => esc_html__( 'Pill Button', 'custom-theme' ),
                    'button_outline' => esc_html__( 'Outlined Button', 'custom-theme' ),
                ),
                'condition' => array(
                    'show_link' => 'yes',
                ),
            )
        );

        $this->add_control(
            'show_link_arrow',
            array(
                'label'        => esc_html__( 'Show Arrow Icon', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Yes', 'custom-theme' ),
                'label_off'    => esc_html__( 'No', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition'    => array(
                    'show_link' => 'yes',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // CONTENT TAB: Layout & Border Accent
        // =========================================================================
        $this->start_controls_section(
            'section_layout_content',
            array(
                'label' => esc_html__( 'Layout & Divider Style', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'header_layout',
            array(
                'label'   => esc_html__( 'Layout Alignment', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'inline',
                'options' => array(
                    'inline'   => esc_html__( 'Inline (Title Left, Link Right)', 'custom-theme' ),
                    'stacked'  => esc_html__( 'Stacked (Title on Top)', 'custom-theme' ),
                    'centered' => esc_html__( 'Centered (All elements centered)', 'custom-theme' ),
                ),
            )
        );

        $this->add_control(
            'border_style_preset',
            array(
                'label'   => esc_html__( 'Bottom Border Accent', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'bold_line',
                'options' => array(
                    'bold_line'   => esc_html__( 'Bold Editorial Underline', 'custom-theme' ),
                    'subtle_line' => esc_html__( 'Subtle 1px Divider Line', 'custom-theme' ),
                    'accent_pill' => esc_html__( 'Accent Indicator Bar', 'custom-theme' ),
                    'none'        => esc_html__( 'None (Clean / No Border)', 'custom-theme' ),
                ),
            )
        );

        $this->add_responsive_control(
            'bottom_spacing',
            array(
                'label'      => esc_html__( 'Bottom Margin Spacing (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 60,
                    ),
                ),
                'default'    => array(
                    'size' => 24,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-section-header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Header Bar & Border
        // =========================================================================
        $this->start_controls_section(
            'section_style_bar',
            array(
                'label' => esc_html__( 'Header Bar & Border Accent', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'border_color',
            array(
                'label'     => esc_html__( 'Bottom Border Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'border_style_preset!' => 'none',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .editorial-section-header.border-bold_line'   => 'border-bottom-color: {{VALUE}};',
                    '{{WRAPPER}} .editorial-section-header.border-subtle_line' => 'border-bottom-color: {{VALUE}};',
                    '{{WRAPPER}} .section-accent-indicator'                    => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'border_width',
            array(
                'label'      => esc_html__( 'Border Thickness (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 1,
                        'max' => 8,
                    ),
                ),
                'condition'  => array(
                    'border_style_preset' => array( 'bold_line', 'subtle_line' ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-section-header' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'padding_bottom',
            array(
                'label'      => esc_html__( 'Inner Bottom Padding (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 40,
                    ),
                ),
                'default'    => array(
                    'size' => 10,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-section-header' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Title & Icon
        // =========================================================================
        $this->start_controls_section(
            'section_style_title',
            array(
                'label' => esc_html__( 'Title & Icon', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .section-title',
            )
        );

        $this->add_control(
            'title_color',
            array(
                'label'     => esc_html__( 'Title Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .section-title' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'heading_icon_style',
            array(
                'label'     => esc_html__( 'Icon Style', 'custom-theme' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => array(
                    'show_icon' => 'yes',
                ),
            )
        );

        $this->add_control(
            'icon_color',
            array(
                'label'     => esc_html__( 'Icon Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'show_icon' => 'yes',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .section-title-icon' => 'color: {{VALUE}}; stroke: {{VALUE}}; fill: currentColor;',
                    '{{WRAPPER}} .section-title-icon svg' => 'stroke: {{VALUE}}; color: {{VALUE}};',
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
                        'max' => 48,
                    ),
                ),
                'condition'  => array(
                    'show_icon' => 'yes',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .section-title-icon'     => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .section-title-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'icon_spacing',
            array(
                'label'      => esc_html__( 'Icon Gap (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 30,
                    ),
                ),
                'default'    => array(
                    'size' => 8,
                ),
                'condition'  => array(
                    'show_icon' => 'yes',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .section-title' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Subtitle
        // =========================================================================
        $this->start_controls_section(
            'section_style_subtitle',
            array(
                'label'     => esc_html__( 'Subtitle', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'show_subtitle' => 'yes',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'subtitle_typography',
                'selector' => '{{WRAPPER}} .section-subtitle',
            )
        );

        $this->add_control(
            'subtitle_color',
            array(
                'label'     => esc_html__( 'Subtitle Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .section-subtitle' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'subtitle_margin_top',
            array(
                'label'      => esc_html__( 'Margin Top (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 30,
                    ),
                ),
                'default'    => array(
                    'size' => 4,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .section-subtitle' => 'margin-top: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Action Link ("View All")
        // =========================================================================
        $this->start_controls_section(
            'section_style_link',
            array(
                'label'     => esc_html__( 'Action Link ("View All")', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'show_link' => 'yes',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'link_typography',
                'selector' => '{{WRAPPER}} .section-link',
            )
        );

        $this->start_controls_tabs( 'tabs_link_style' );

        $this->start_controls_tab(
            'tab_link_normal',
            array(
                'label' => esc_html__( 'Normal', 'custom-theme' ),
            )
        );

        $this->add_control(
            'link_color',
            array(
                'label'     => esc_html__( 'Text / Icon Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .section-link'       => 'color: {{VALUE}};',
                    '{{WRAPPER}} .section-link svg'   => 'stroke: {{VALUE}}; color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'link_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .section-link' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'link_border',
                'selector' => '{{WRAPPER}} .section-link',
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_link_hover',
            array(
                'label' => esc_html__( 'Hover', 'custom-theme' ),
            )
        );

        $this->add_control(
            'link_hover_color',
            array(
                'label'     => esc_html__( 'Text / Icon Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .section-link:hover'     => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .section-link:hover svg' => 'stroke: {{VALUE}} !important; color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'link_hover_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .section-link:hover' => 'background-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'link_hover_border_color',
            array(
                'label'     => esc_html__( 'Border Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .section-link:hover' => 'border-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'link_padding',
            array(
                'label'      => esc_html__( 'Padding', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', 'rem' ),
                'separator'  => 'before',
                'selectors'  => array(
                    '{{WRAPPER}} .section-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'link_border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .section-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

        $title_text    = ! empty( $settings['title_text'] ) ? $settings['title_text'] : '';
        $title_tag     = ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h2';
        $show_subtitle = 'yes' === $settings['show_subtitle'] && ! empty( $settings['subtitle_text'] );
        $show_icon     = 'yes' === $settings['show_icon'];
        $icon_source   = ! empty( $settings['icon_source'] ) ? $settings['icon_source'] : 'builtin';
        $builtin_icon  = ! empty( $settings['builtin_icon'] ) ? $settings['builtin_icon'] : 'article';
        $show_link     = 'yes' === $settings['show_link'] && ! empty( $settings['link_text'] );
        $link_style    = ! empty( $settings['link_style'] ) ? $settings['link_style'] : 'text_link';
        $show_arrow    = 'yes' === $settings['show_link_arrow'];
        $layout        = ! empty( $settings['header_layout'] ) ? $settings['header_layout'] : 'inline';
        $border_preset = ! empty( $settings['border_style_preset'] ) ? $settings['border_style_preset'] : 'bold_line';

        $header_classes = array(
            'section-header',
            'editorial-section-header',
            'layout-' . esc_attr( $layout ),
            'border-' . esc_attr( $border_preset ),
        );

        $link_classes = array(
            'section-link',
            'style-' . esc_attr( $link_style ),
        );

        if ( 'button_pill' === $link_style ) {
            $link_classes[] = 'btn-pill';
        } elseif ( 'button_outline' === $link_style ) {
            $link_classes[] = 'btn-outline';
        }
        ?>
        <header class="<?php echo esc_attr( implode( ' ', $header_classes ) ); ?>">

            <div class="section-title-group">
                <<?php echo esc_attr( $title_tag ); ?> class="section-title">
                    <?php if ( $show_icon ) : ?>
                        <span class="section-title-icon" aria-hidden="true">
                            <?php
                            if ( 'builtin' === $icon_source && function_exists( 'custom_theme_svg_icon' ) ) {
                                echo custom_theme_svg_icon( $builtin_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            } elseif ( 'custom' === $icon_source && ! empty( $settings['custom_icon']['value'] ) ) {
                                Icons_Manager::render_icon( $settings['custom_icon'], array( 'aria-hidden' => 'true' ) );
                            }
                            ?>
                        </span>
                    <?php endif; ?>

                    <span class="section-title-text"><?php echo esc_html( $title_text ); ?></span>
                </<?php echo esc_attr( $title_tag ); ?>>

                <?php if ( $show_subtitle ) : ?>
                    <p class="section-subtitle"><?php echo esc_html( $settings['subtitle_text'] ); ?></p>
                <?php endif; ?>

                <?php if ( 'accent_pill' === $border_preset ) : ?>
                    <div class="section-accent-indicator"></div>
                <?php endif; ?>
            </div><!-- .section-title-group -->

            <?php if ( $show_link ) : ?>
                <?php
                $url_target = ! empty( $settings['link_url']['is_external'] ) ? ' target="_blank"' : '';
                $url_rel    = ! empty( $settings['link_url']['nofollow'] ) ? ' rel="nofollow"' : '';
                $href       = ! empty( $settings['link_url']['url'] ) ? esc_url( $settings['link_url']['url'] ) : '#';
                ?>
                <a href="<?php echo esc_url( $href ); ?>" class="<?php echo esc_attr( implode( ' ', $link_classes ) ); ?>"<?php echo $url_target . $url_rel; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
                    <span class="section-link-text"><?php echo esc_html( $settings['link_text'] ); ?></span>
                    <?php if ( $show_arrow && function_exists( 'custom_theme_svg_icon' ) ) : ?>
                        <span class="section-link-arrow" aria-hidden="true">
                            <?php echo custom_theme_svg_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        </span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

        </header><!-- .editorial-section-header -->
        <?php
    }
}
