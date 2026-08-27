<?php
/**
 * Elementor Widget: Editorial Navigation Menu
 *
 * Displays a customizable navigation menu with multiple layouts (horizontal, vertical, scrollable pills),
 * pointer indicators, dropdown submenus, and mobile responsive accordion toggles.
 *
 * @package Custom_Theme
 */

namespace CustomTheme\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Nav_Menu extends Widget_Base {

    /**
     * Get Widget Name
     *
     * @return string
     */
    public function get_name() {
        return 'custom_theme_nav_menu';
    }

    /**
     * Get Widget Title
     *
     * @return string
     */
    public function get_title() {
        return esc_html__( 'Editorial Navigation Menu', 'custom-theme' );
    }

    /**
     * Get Widget Icon
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-nav-menu';
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
        return array( 'menu', 'nav', 'navigation', 'header', 'links', 'dropdown', 'pills', 'mega' );
    }

    /**
     * Helper: Fetch available WordPress menus for dropdown
     *
     * @return array
     */
    private function get_available_menus() {
        $menus   = wp_get_nav_menus();
        $options = array( '' => esc_html__( '— Select a Menu —', 'custom-theme' ) );

        if ( ! empty( $menus ) && ! is_wp_error( $menus ) ) {
            foreach ( $menus as $menu ) {
                $options[ $menu->slug ] = $menu->name;
            }
        }

        return $options;
    }

    /**
     * Register Widget Controls
     */
    protected function register_controls() {

        // =========================================================================
        // CONTENT TAB: Menu Source & Layout
        // =========================================================================
        $this->start_controls_section(
            'section_menu_layout',
            array(
                'label' => esc_html__( 'Menu & Layout', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'menu_slug',
            array(
                'label'   => esc_html__( 'Select Menu', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => '',
                'options' => $this->get_available_menus(),
                'description' => esc_html__( 'Choose a registered WordPress menu. If none is chosen, fallback topics will be displayed.', 'custom-theme' ),
            )
        );

        $this->add_control(
            'layout_mode',
            array(
                'label'   => esc_html__( 'Layout Mode', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'horizontal',
                'options' => array(
                    'horizontal'   => esc_html__( 'Horizontal Bar', 'custom-theme' ),
                    'vertical'     => esc_html__( 'Vertical List', 'custom-theme' ),
                    'scroll_pills' => esc_html__( 'Scrollable Pill Strip (Horizontal)', 'custom-theme' ),
                ),
            )
        );

        $this->add_responsive_control(
            'menu_alignment',
            array(
                'label'     => esc_html__( 'Alignment', 'custom-theme' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => array(
                    'flex-start' => array(
                        'title' => esc_html__( 'Left / Start', 'custom-theme' ),
                        'icon'  => 'eicon-text-align-left',
                    ),
                    'center'     => array(
                        'title' => esc_html__( 'Center', 'custom-theme' ),
                        'icon'  => 'eicon-text-align-center',
                    ),
                    'flex-end'   => array(
                        'title' => esc_html__( 'Right / End', 'custom-theme' ),
                        'icon'  => 'eicon-text-align-right',
                    ),
                    'space-between' => array(
                        'title' => esc_html__( 'Space Between / Justified', 'custom-theme' ),
                        'icon'  => 'eicon-text-align-justify',
                    ),
                ),
                'default'   => 'flex-start',
                'selectors' => array(
                    '{{WRAPPER}} .editorial-nav-list' => 'justify-content: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'pointer_style',
            array(
                'label'     => esc_html__( 'Active / Hover Pointer', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'underline',
                'options'   => array(
                    'underline' => esc_html__( 'Underline Indicator', 'custom-theme' ),
                    'pill'      => esc_html__( 'Background Pill Highlight', 'custom-theme' ),
                    'dot'       => esc_html__( 'Bottom Dot Indicator', 'custom-theme' ),
                    'framed'    => esc_html__( 'Framed Border Box', 'custom-theme' ),
                    'none'      => esc_html__( 'None (Text Highlight Only)', 'custom-theme' ),
                ),
                'condition' => array(
                    'layout_mode!' => 'scroll_pills',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // CONTENT TAB: Dropdowns & Submenus
        // =========================================================================
        $this->start_controls_section(
            'section_dropdown_settings',
            array(
                'label'     => esc_html__( 'Dropdowns & Submenus', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
                'condition' => array(
                    'layout_mode!' => 'scroll_pills',
                ),
            )
        );

        $this->add_control(
            'show_dropdown_arrows',
            array(
                'label'        => esc_html__( 'Show Dropdown Arrows', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Yes', 'custom-theme' ),
                'label_off'    => esc_html__( 'No', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'dropdown_animation',
            array(
                'label'   => esc_html__( 'Submenu Animation', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'slide_down',
                'options' => array(
                    'slide_down' => esc_html__( 'Slide Down & Fade', 'custom-theme' ),
                    'fade_in'    => esc_html__( 'Fade In', 'custom-theme' ),
                    'scale_up'   => esc_html__( 'Scale Up', 'custom-theme' ),
                    'none'       => esc_html__( 'Instant (No Animation)', 'custom-theme' ),
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // CONTENT TAB: Mobile / Responsive Breakpoint
        // =========================================================================
        $this->start_controls_section(
            'section_mobile_settings',
            array(
                'label' => esc_html__( 'Mobile Responsive', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'mobile_breakpoint',
            array(
                'label'   => esc_html__( 'Mobile Toggle Breakpoint', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'mobile_768',
                'options' => array(
                    'tablet_1024' => esc_html__( 'Tablet (<= 1024px)', 'custom-theme' ),
                    'mobile_768'  => esc_html__( 'Mobile (<= 768px)', 'custom-theme' ),
                    'none'        => esc_html__( 'None (Always Desktop View)', 'custom-theme' ),
                ),
                'condition' => array(
                    'layout_mode!' => 'scroll_pills',
                ),
            )
        );

        $this->add_control(
            'mobile_toggle_label',
            array(
                'label'       => esc_html__( 'Toggle Button Text (Optional)', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'Menu', 'custom-theme' ),
                'default'     => '',
                'condition'   => array(
                    'layout_mode!' => 'scroll_pills',
                    'mobile_breakpoint!' => 'none',
                ),
            )
        );

        $this->add_control(
            'mobile_toggle_icon',
            array(
                'label'       => esc_html__( 'Menu Open Icon', 'custom-theme' ),
                'type'        => Controls_Manager::ICONS,
                'description' => esc_html__( 'Upload a custom SVG icon or choose from the icon library.', 'custom-theme' ),
                'default'     => array(
                    'value'   => 'fas fa-bars',
                    'library' => 'fa-solid',
                ),
                'condition'   => array(
                    'layout_mode!' => 'scroll_pills',
                    'mobile_breakpoint!' => 'none',
                ),
            )
        );

        $this->add_control(
            'mobile_toggle_close_icon',
            array(
                'label'       => esc_html__( 'Menu Close Icon (When Expanded)', 'custom-theme' ),
                'type'        => Controls_Manager::ICONS,
                'description' => esc_html__( 'Upload a custom SVG icon or choose from the icon library.', 'custom-theme' ),
                'default'     => array(
                    'value'   => 'fas fa-times',
                    'library' => 'fa-solid',
                ),
                'condition'   => array(
                    'layout_mode!' => 'scroll_pills',
                    'mobile_breakpoint!' => 'none',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Main Menu Items
        // =========================================================================
        $this->start_controls_section(
            'section_style_main_items',
            array(
                'label' => esc_html__( 'Main Menu Items', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'items_typography',
                'selector' => '{{WRAPPER}} .editorial-nav-list > .menu-item > a, {{WRAPPER}} .editorial-nav-list .fallback-item a',
            )
        );

        $this->add_responsive_control(
            'items_gap',
            array(
                'label'      => esc_html__( 'Item Spacing (Gap)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px', 'em' ),
                'range'      => array(
                    'px' => array( 'min' => 0, 'max' => 50 ),
                ),
                'default'    => array( 'size' => 20 ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-nav-list' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'item_padding',
            array(
                'label'      => esc_html__( 'Item Padding', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', 'rem' ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-nav-list > .menu-item > a, {{WRAPPER}} .editorial-nav-list .fallback-item a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->start_controls_tabs( 'tabs_menu_item_style' );

        // Normal State
        $this->start_controls_tab(
            'tab_item_normal',
            array(
                'label' => esc_html__( 'Normal', 'custom-theme' ),
            )
        );

        $this->add_control(
            'item_text_color',
            array(
                'label'     => esc_html__( 'Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-nav-list > .menu-item > a, {{WRAPPER}} .editorial-nav-list .fallback-item a' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'item_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-nav-list > .menu-item > a, {{WRAPPER}} .editorial-nav-list .fallback-item a' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        // Hover State
        $this->start_controls_tab(
            'tab_item_hover',
            array(
                'label' => esc_html__( 'Hover', 'custom-theme' ),
            )
        );

        $this->add_control(
            'item_hover_text_color',
            array(
                'label'     => esc_html__( 'Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-nav-list > .menu-item:hover > a, {{WRAPPER}} .editorial-nav-list .fallback-item:hover a' => 'color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'item_hover_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-nav-list > .menu-item:hover > a, {{WRAPPER}} .editorial-nav-list .fallback-item:hover a' => 'background-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'pointer_hover_color',
            array(
                'label'     => esc_html__( 'Pointer / Indicator Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .pointer-underline .editorial-nav-list > .menu-item:hover > a::before' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .pointer-dot .editorial-nav-list > .menu-item:hover > a::after'       => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .pointer-framed .editorial-nav-list > .menu-item:hover > a'           => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        // Active State
        $this->start_controls_tab(
            'tab_item_active',
            array(
                'label' => esc_html__( 'Active', 'custom-theme' ),
            )
        );

        $this->add_control(
            'item_active_text_color',
            array(
                'label'     => esc_html__( 'Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-nav-list > .current-menu-item > a, {{WRAPPER}} .editorial-nav-list > .current_page_item > a' => 'color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'item_active_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-nav-list > .current-menu-item > a, {{WRAPPER}} .editorial-nav-list > .current_page_item > a' => 'background-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'pointer_active_color',
            array(
                'label'     => esc_html__( 'Pointer Indicator Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .pointer-underline .editorial-nav-list > .current-menu-item > a::before' => 'background-color: {{VALUE}} !important; width: 100% !important;',
                    '{{WRAPPER}} .pointer-underline .editorial-nav-list > .current_page_item > a::before' => 'background-color: {{VALUE}} !important; width: 100% !important;',
                    '{{WRAPPER}} .pointer-dot .editorial-nav-list > .current-menu-item > a::after'       => 'background-color: {{VALUE}} !important; transform: translateX(-50%) scale(1) !important;',
                    '{{WRAPPER}} .pointer-framed .editorial-nav-list > .current-menu-item > a'           => 'border-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'item_border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'separator'  => 'before',
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-nav-list > .menu-item > a, {{WRAPPER}} .editorial-nav-list .fallback-item a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Dropdowns / Submenus
        // =========================================================================
        $this->start_controls_section(
            'section_style_dropdown',
            array(
                'label'     => esc_html__( 'Dropdown / Submenus', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'layout_mode!' => 'scroll_pills',
                ),
            )
        );

        $this->add_control(
            'dropdown_bg_color',
            array(
                'label'     => esc_html__( 'Dropdown Background', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-nav-list .sub-menu' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'dropdown_min_width',
            array(
                'label'      => esc_html__( 'Min Width (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array( 'min' => 160, 'max' => 400 ),
                ),
                'default'    => array( 'size' => 220 ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-nav-list .sub-menu' => 'min-width: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'dropdown_border',
                'selector' => '{{WRAPPER}} .editorial-nav-list .sub-menu',
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'dropdown_shadow',
                'selector' => '{{WRAPPER}} .editorial-nav-list .sub-menu',
            )
        );

        $this->add_responsive_control(
            'dropdown_border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-nav-list .sub-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'      => 'dropdown_item_typography',
                'label'     => esc_html__( 'Submenu Item Typography', 'custom-theme' ),
                'separator' => 'before',
                'selector'  => '{{WRAPPER}} .editorial-nav-list .sub-menu .menu-item a',
            )
        );

        $this->add_control(
            'dropdown_item_color',
            array(
                'label'     => esc_html__( 'Submenu Item Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-nav-list .sub-menu .menu-item a' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'dropdown_item_hover_color',
            array(
                'label'     => esc_html__( 'Submenu Item Hover Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-nav-list .sub-menu .menu-item a:hover' => 'color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'dropdown_item_hover_bg',
            array(
                'label'     => esc_html__( 'Submenu Item Hover Background', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-nav-list .sub-menu .menu-item a:hover' => 'background-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Mobile Toggle Button
        // =========================================================================
        $this->start_controls_section(
            'section_style_mobile_toggle',
            array(
                'label'     => esc_html__( 'Mobile Toggle Button', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'layout_mode!' => 'scroll_pills',
                    'mobile_breakpoint!' => 'none',
                ),
            )
        );

        $this->add_responsive_control(
            'toggle_icon_size',
            array(
                'label'      => esc_html__( 'Icon Size', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px', 'em' ),
                'range'      => array(
                    'px' => array( 'min' => 10, 'max' => 48 ),
                ),
                'default'    => array( 'size' => 18 ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-menu-toggle i'   => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .editorial-menu-toggle svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'toggle_btn_padding',
            array(
                'label'      => esc_html__( 'Button Padding', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', 'rem' ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-menu-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->start_controls_tabs( 'tabs_toggle_btn_style' );

        // Normal
        $this->start_controls_tab(
            'tab_toggle_btn_normal',
            array(
                'label' => esc_html__( 'Normal', 'custom-theme' ),
            )
        );

        $this->add_control(
            'toggle_btn_color',
            array(
                'label'     => esc_html__( 'Icon / Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-menu-toggle' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .editorial-menu-toggle svg' => 'color: {{VALUE}}; fill: currentColor;',
                ),
            )
        );

        $this->add_control(
            'toggle_btn_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-menu-toggle' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        // Hover
        $this->start_controls_tab(
            'tab_toggle_btn_hover',
            array(
                'label' => esc_html__( 'Hover', 'custom-theme' ),
            )
        );

        $this->add_control(
            'toggle_btn_hover_color',
            array(
                'label'     => esc_html__( 'Icon / Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-menu-toggle:hover' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .editorial-menu-toggle:hover svg' => 'color: {{VALUE}} !important; fill: currentColor;',
                ),
            )
        );

        $this->add_control(
            'toggle_btn_hover_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-menu-toggle:hover' => 'background-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'toggle_btn_hover_border_color',
            array(
                'label'     => esc_html__( 'Border Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-menu-toggle:hover' => 'border-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'      => 'toggle_btn_border',
                'separator' => 'before',
                'selector'  => '{{WRAPPER}} .editorial-menu-toggle',
            )
        );

        $this->add_responsive_control(
            'toggle_btn_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-menu-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Fallback menu if no WP Menu is assigned
     */
    public function render_fallback_menu() {
        $categories = get_categories( array(
            'number'     => 6,
            'orderby'    => 'count',
            'order'      => 'DESC',
            'hide_empty' => true,
        ) );
        ?>
        <ul class="editorial-nav-list fallback-menu">
            <li class="menu-item fallback-item current-menu-item">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'custom-theme' ); ?></a>
            </li>
            <?php foreach ( $categories as $cat ) : ?>
                <li class="menu-item fallback-item">
                    <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
                        <?php echo esc_html( $cat->name ); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
    }

    /**
     * Render Widget Output
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        $menu_slug       = ! empty( $settings['menu_slug'] ) ? $settings['menu_slug'] : '';
        $layout_mode     = ! empty( $settings['layout_mode'] ) ? $settings['layout_mode'] : 'horizontal';
        $pointer         = ! empty( $settings['pointer_style'] ) ? $settings['pointer_style'] : 'underline';
        $show_arrows     = 'yes' === $settings['show_dropdown_arrows'] ? 'has-dropdown-arrows' : 'no-dropdown-arrows';
        $animation       = ! empty( $settings['dropdown_animation'] ) ? 'anim-' . $settings['dropdown_animation'] : 'anim-slide_down';
        $breakpoint      = ! empty( $settings['mobile_breakpoint'] ) ? 'break-' . $settings['mobile_breakpoint'] : 'break-mobile_768';
        $widget_id       = $this->get_id();

        $wrapper_classes = array(
            'editorial-nav-menu-widget',
            'layout-' . esc_attr( $layout_mode ),
            'pointer-' . esc_attr( $pointer ),
            esc_attr( $show_arrows ),
            esc_attr( $animation ),
            esc_attr( $breakpoint ),
        );
        ?>
        <nav class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" id="widget-nav-<?php echo esc_attr( $widget_id ); ?>" aria-label="<?php esc_attr_e( 'Navigation Menu', 'custom-theme' ); ?>">

            <?php if ( 'scroll_pills' !== $layout_mode && 'none' !== $settings['mobile_breakpoint'] ) : ?>
                <!-- Mobile Hamburger Toggle Button -->
                <button type="button" class="editorial-menu-toggle" aria-expanded="false" aria-controls="widget-nav-content-<?php echo esc_attr( $widget_id ); ?>" aria-label="<?php esc_attr_e( 'Toggle navigation menu', 'custom-theme' ); ?>">
                    <span class="toggle-icon-open">
                        <?php
                        if ( ! empty( $settings['mobile_toggle_icon']['value'] ) ) {
                            Icons_Manager::render_icon( $settings['mobile_toggle_icon'], array( 'aria-hidden' => 'true' ) );
                        } elseif ( function_exists( 'custom_theme_svg_icon' ) ) {
                            echo custom_theme_svg_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }
                        ?>
                    </span>
                    <span class="toggle-icon-close" style="display: none;">
                        <?php
                        if ( ! empty( $settings['mobile_toggle_close_icon']['value'] ) ) {
                            Icons_Manager::render_icon( $settings['mobile_toggle_close_icon'], array( 'aria-hidden' => 'true' ) );
                        } elseif ( function_exists( 'custom_theme_svg_icon' ) ) {
                            echo custom_theme_svg_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }
                        ?>
                    </span>
                    <?php if ( ! empty( $settings['mobile_toggle_label'] ) ) : ?>
                        <span class="toggle-label"><?php echo esc_html( $settings['mobile_toggle_label'] ); ?></span>
                    <?php endif; ?>
                </button>
            <?php endif; ?>

            <div class="editorial-nav-container" id="widget-nav-content-<?php echo esc_attr( $widget_id ); ?>">
                <?php
                if ( ! empty( $menu_slug ) ) {
                    wp_nav_menu(
                        array(
                            'menu'        => $menu_slug,
                            'menu_class'  => 'editorial-nav-list',
                            'container'   => false,
                            'depth'       => 3,
                            'fallback_cb' => array( $this, 'render_fallback_menu' ),
                        )
                    );
                } else {
                    $this->render_fallback_menu();
                }
                ?>
            </div><!-- .editorial-nav-container -->

        </nav><!-- .editorial-nav-menu-widget -->
        <?php
    }
}
