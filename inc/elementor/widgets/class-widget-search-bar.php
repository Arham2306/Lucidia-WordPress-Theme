<?php
/**
 * Elementor Widget: Editorial Smart Search
 *
 * Provides an interactive live search bar with debounced REST API results,
 * a modal trigger button with keyboard shortcut badge, or a classic search form.
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

class Search_Bar extends Widget_Base {

    /**
     * Get Widget Name
     *
     * @return string
     */
    public function get_name() {
        return 'custom_theme_search_bar';
    }

    /**
     * Get Widget Title
     *
     * @return string
     */
    public function get_title() {
        return esc_html__( 'Editorial Smart Search', 'custom-theme' );
    }

    /**
     * Get Widget Icon
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-search';
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
        return array( 'search', 'live search', 'ajax', 'modal', 'find', 'query', 'input', 'bar' );
    }

    /**
     * Register Widget Controls
     */
    protected function register_controls() {

        // =========================================================================
        // CONTENT TAB: Search Configuration
        // =========================================================================
        $this->start_controls_section(
            'section_search_config',
            array(
                'label' => esc_html__( 'Search Configuration', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'display_mode',
            array(
                'label'   => esc_html__( 'Display Mode', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'inline_live',
                'options' => array(
                    'inline_live'   => esc_html__( 'Inline Live Search Bar (Real-time Dropdown)', 'custom-theme' ),
                    'modal_trigger' => esc_html__( 'Search Modal Trigger Button (Popup Overlay)', 'custom-theme' ),
                    'classic_form'  => esc_html__( 'Classic Search Form', 'custom-theme' ),
                ),
            )
        );

        $this->add_control(
            'placeholder_text',
            array(
                'label'       => esc_html__( 'Placeholder Text', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'Search stories, topics, authors...', 'custom-theme' ),
                'placeholder' => esc_html__( 'Type placeholder here...', 'custom-theme' ),
                'condition'   => array(
                    'display_mode!' => 'modal_trigger',
                ),
            )
        );

        $this->add_control(
            'icon_position',
            array(
                'label'     => esc_html__( 'Search Icon Position', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'left',
                'options'   => array(
                    'left'  => esc_html__( 'Inside Left', 'custom-theme' ),
                    'right' => esc_html__( 'Inside Right', 'custom-theme' ),
                    'none'  => esc_html__( 'None', 'custom-theme' ),
                ),
                'condition' => array(
                    'display_mode!' => 'modal_trigger',
                ),
            )
        );

        $this->add_control(
            'custom_search_icon',
            array(
                'label'       => esc_html__( 'Search Icon', 'custom-theme' ),
                'type'        => Controls_Manager::ICONS,
                'description' => esc_html__( 'Upload custom SVG or choose from library.', 'custom-theme' ),
                'default'     => array(
                    'value'   => 'fas fa-search',
                    'library' => 'fa-solid',
                ),
                'condition'   => array(
                    'display_mode!'  => 'modal_trigger',
                    'icon_position!' => 'none',
                ),
            )
        );

        $this->add_control(
            'show_clear_btn',
            array(
                'label'        => esc_html__( 'Show Clear (X) Button', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Yes', 'custom-theme' ),
                'label_off'    => esc_html__( 'No', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition'    => array(
                    'display_mode' => 'inline_live',
                ),
            )
        );

        $this->add_control(
            'show_submit_btn',
            array(
                'label'        => esc_html__( 'Show Submit Button', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Yes', 'custom-theme' ),
                'label_off'    => esc_html__( 'No', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'no',
                'condition'    => array(
                    'display_mode!' => 'modal_trigger',
                ),
            )
        );

        $this->add_control(
            'submit_btn_text',
            array(
                'label'     => esc_html__( 'Submit Button Text', 'custom-theme' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => esc_html__( 'Search', 'custom-theme' ),
                'condition' => array(
                    'display_mode!'   => 'modal_trigger',
                    'show_submit_btn' => 'yes',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // CONTENT TAB: Live Search Settings (for inline_live mode)
        // =========================================================================
        $this->start_controls_section(
            'section_live_settings',
            array(
                'label'     => esc_html__( 'Live Results Settings', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
                'condition' => array(
                    'display_mode' => 'inline_live',
                ),
            )
        );

        $this->add_control(
            'results_limit',
            array(
                'label'   => esc_html__( 'Max Live Results', 'custom-theme' ),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 10,
                'step'    => 1,
                'default' => 5,
            )
        );

        $this->add_control(
            'show_result_thumbnail',
            array(
                'label'        => esc_html__( 'Show Thumbnail', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Yes', 'custom-theme' ),
                'label_off'    => esc_html__( 'No', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'show_result_category',
            array(
                'label'        => esc_html__( 'Show Category Badge', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Yes', 'custom-theme' ),
                'label_off'    => esc_html__( 'No', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'show_result_date',
            array(
                'label'        => esc_html__( 'Show Published Date', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Yes', 'custom-theme' ),
                'label_off'    => esc_html__( 'No', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'min_query_length',
            array(
                'label'   => esc_html__( 'Minimum Characters to Search', 'custom-theme' ),
                'type'    => Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 5,
                'default' => 2,
            )
        );

        $this->add_control(
            'no_results_text',
            array(
                'label'   => esc_html__( 'No Results Message', 'custom-theme' ),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__( 'No stories found matching your search.', 'custom-theme' ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // CONTENT TAB: Modal Trigger Settings (for modal_trigger mode)
        // =========================================================================
        $this->start_controls_section(
            'section_modal_trigger_settings',
            array(
                'label'     => esc_html__( 'Modal Trigger Settings', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_CONTENT,
                'condition' => array(
                    'display_mode' => 'modal_trigger',
                ),
            )
        );

        $this->add_control(
            'modal_trigger_label',
            array(
                'label'   => esc_html__( 'Button Label (Optional)', 'custom-theme' ),
                'type'    => Controls_Manager::TEXT,
                'default' => esc_html__( 'Search stories...', 'custom-theme' ),
            )
        );

        $this->add_control(
            'show_shortcut_badge',
            array(
                'label'        => esc_html__( 'Show Shortcut Badge (⌘K / Ctrl+K)', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Yes', 'custom-theme' ),
                'label_off'    => esc_html__( 'No', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'modal_trigger_icon',
            array(
                'label'       => esc_html__( 'Trigger Icon', 'custom-theme' ),
                'type'        => Controls_Manager::ICONS,
                'default'     => array(
                    'value'   => 'fas fa-search',
                    'library' => 'fa-solid',
                ),
            )
        );

        $this->add_responsive_control(
            'modal_trigger_alignment',
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
                ),
                'default'   => 'flex-start',
                'selectors' => array(
                    '{{WRAPPER}} .editorial-search-widget' => 'display: flex; justify-content: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Search Input Field
        // =========================================================================
        $this->start_controls_section(
            'section_style_input',
            array(
                'label'     => esc_html__( 'Search Input Field', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'display_mode!' => 'modal_trigger',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'input_typography',
                'selector' => '{{WRAPPER}} .editorial-search-input',
            )
        );

        $this->add_responsive_control(
            'input_height',
            array(
                'label'      => esc_html__( 'Input Height', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array( 'min' => 36, 'max' => 70 ),
                ),
                'default'    => array( 'size' => 46 ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-search-input' => 'height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'input_padding',
            array(
                'label'      => esc_html__( 'Padding', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-search-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->start_controls_tabs( 'tabs_input_colors' );

        // Normal Input
        $this->start_controls_tab(
            'tab_input_normal',
            array(
                'label' => esc_html__( 'Normal', 'custom-theme' ),
            )
        );

        $this->add_control(
            'input_text_color',
            array(
                'label'     => esc_html__( 'Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-search-input' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_placeholder_color',
            array(
                'label'     => esc_html__( 'Placeholder Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-search-input::placeholder' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-search-input' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'input_border',
                'selector' => '{{WRAPPER}} .editorial-search-input',
            )
        );

        $this->end_controls_tab();

        // Focus Input
        $this->start_controls_tab(
            'tab_input_focus',
            array(
                'label' => esc_html__( 'Focus', 'custom-theme' ),
            )
        );

        $this->add_control(
            'input_focus_text_color',
            array(
                'label'     => esc_html__( 'Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-search-input:focus' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_focus_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-search-input:focus' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'input_focus_border_color',
            array(
                'label'     => esc_html__( 'Border Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-search-input:focus' => 'border-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'input_focus_glow',
            array(
                'label'     => esc_html__( 'Focus Ring Glow', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-search-input:focus' => 'box-shadow: 0 0 0 3px {{VALUE}} !important;',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'input_border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'separator'  => 'before',
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-search-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'input_box_shadow',
                'selector' => '{{WRAPPER}} .editorial-search-input',
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Search & Action Icons
        // =========================================================================
        $this->start_controls_section(
            'section_style_icons',
            array(
                'label'     => esc_html__( 'Icons & Buttons', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'display_mode!' => 'modal_trigger',
                ),
            )
        );

        $this->add_responsive_control(
            'search_icon_size',
            array(
                'label'      => esc_html__( 'Search Icon Size', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px', 'em' ),
                'range'      => array(
                    'px' => array( 'min' => 12, 'max' => 36 ),
                ),
                'default'    => array( 'size' => 16 ),
                'selectors'  => array(
                    '{{WRAPPER}} .search-icon-wrapper i'   => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .search-icon-wrapper svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'search_icon_color',
            array(
                'label'     => esc_html__( 'Search Icon Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .search-icon-wrapper' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .search-icon-wrapper svg' => 'color: {{VALUE}}; fill: currentColor;',
                ),
            )
        );

        $this->add_control(
            'clear_icon_color',
            array(
                'label'     => esc_html__( 'Clear (X) Button Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-search-clear-btn' => 'color: {{VALUE}};',
                ),
                'condition' => array(
                    'show_clear_btn' => 'yes',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Live Results Dropdown Card
        // =========================================================================
        $this->start_controls_section(
            'section_style_live_dropdown',
            array(
                'label'     => esc_html__( 'Live Results Dropdown', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'display_mode' => 'inline_live',
                ),
            )
        );

        $this->add_control(
            'live_dropdown_bg',
            array(
                'label'     => esc_html__( 'Dropdown Background', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .editorial-live-results' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'live_dropdown_border',
                'selector' => '{{WRAPPER}} .editorial-live-results',
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'live_dropdown_shadow',
                'selector' => '{{WRAPPER}} .editorial-live-results',
            )
        );

        $this->add_responsive_control(
            'live_dropdown_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em' ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-live-results' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'live_item_hover_bg',
            array(
                'label'     => esc_html__( 'Result Item Hover Background', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'separator' => 'before',
                'selectors' => array(
                    '{{WRAPPER}} .editorial-search-result-item:hover, {{WRAPPER}} .editorial-search-result-item.is-selected' => 'background-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'result_title_typography',
                'label'    => esc_html__( 'Story Title Typography', 'custom-theme' ),
                'selector' => '{{WRAPPER}} .result-item-title',
            )
        );

        $this->add_control(
            'result_title_color',
            array(
                'label'     => esc_html__( 'Story Title Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .result-item-title' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'result_thumb_width',
            array(
                'label'      => esc_html__( 'Thumbnail Width', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array( 'min' => 30, 'max' => 120 ),
                ),
                'default'    => array( 'size' => 54 ),
                'separator'  => 'before',
                'selectors'  => array(
                    '{{WRAPPER}} .result-item-thumb' => 'width: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'result_thumb_height',
            array(
                'label'      => esc_html__( 'Thumbnail Height', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array( 'min' => 30, 'max' => 120 ),
                ),
                'default'    => array( 'size' => 54 ),
                'selectors'  => array(
                    '{{WRAPPER}} .result-item-thumb' => 'height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'result_thumb_radius',
            array(
                'label'      => esc_html__( 'Thumbnail Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .result-item-thumb' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Modal Trigger Button
        // =========================================================================
        $this->start_controls_section(
            'section_style_modal_trigger',
            array(
                'label'     => esc_html__( 'Modal Trigger Button', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'display_mode' => 'modal_trigger',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'modal_btn_typography',
                'selector' => '{{WRAPPER}} .search-modal-trigger-btn',
            )
        );

        $this->add_responsive_control(
            'modal_btn_padding',
            array(
                'label'      => esc_html__( 'Button Padding', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', 'rem' ),
                'selectors'  => array(
                    '{{WRAPPER}} .search-modal-trigger-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->start_controls_tabs( 'tabs_modal_btn_style' );

        // Normal
        $this->start_controls_tab(
            'tab_modal_btn_normal',
            array(
                'label' => esc_html__( 'Normal', 'custom-theme' ),
            )
        );

        $this->add_control(
            'modal_btn_color',
            array(
                'label'     => esc_html__( 'Text / Icon Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .search-modal-trigger-btn' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .search-modal-trigger-btn svg' => 'color: {{VALUE}}; fill: currentColor;',
                ),
            )
        );

        $this->add_control(
            'modal_btn_bg',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .search-modal-trigger-btn' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        // Hover
        $this->start_controls_tab(
            'tab_modal_btn_hover',
            array(
                'label' => esc_html__( 'Hover', 'custom-theme' ),
            )
        );

        $this->add_control(
            'modal_btn_hover_color',
            array(
                'label'     => esc_html__( 'Text / Icon Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .search-modal-trigger-btn:hover' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .search-modal-trigger-btn:hover svg' => 'color: {{VALUE}} !important; fill: currentColor;',
                ),
            )
        );

        $this->add_control(
            'modal_btn_hover_bg',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .search-modal-trigger-btn:hover' => 'background-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'modal_btn_hover_border',
            array(
                'label'     => esc_html__( 'Border Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .search-modal-trigger-btn:hover' => 'border-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'      => 'modal_btn_border',
                'separator' => 'before',
                'selector'  => '{{WRAPPER}} .search-modal-trigger-btn',
            )
        );

        $this->add_responsive_control(
            'modal_btn_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .search-modal-trigger-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

        $mode          = ! empty( $settings['display_mode'] ) ? $settings['display_mode'] : 'inline_live';
        $placeholder   = ! empty( $settings['placeholder_text'] ) ? $settings['placeholder_text'] : esc_html__( 'Search...', 'custom-theme' );
        $icon_pos      = ! empty( $settings['icon_position'] ) ? $settings['icon_position'] : 'left';
        $show_clear    = 'yes' === $settings['show_clear_btn'];
        $show_submit   = 'yes' === $settings['show_submit_btn'];
        $submit_text   = ! empty( $settings['submit_btn_text'] ) ? $settings['submit_btn_text'] : esc_html__( 'Search', 'custom-theme' );
        $widget_id     = $this->get_id();
        $home_url      = home_url( '/' );

        // Live settings
        $limit         = ! empty( $settings['results_limit'] ) ? intval( $settings['results_limit'] ) : 5;
        $min_len       = ! empty( $settings['min_query_length'] ) ? intval( $settings['min_query_length'] ) : 2;
        $show_thumb    = 'yes' === $settings['show_result_thumbnail'] ? '1' : '0';
        $show_cat      = 'yes' === $settings['show_result_category'] ? '1' : '0';
        $show_date     = 'yes' === $settings['show_result_date'] ? '1' : '0';
        $no_res_msg    = ! empty( $settings['no_results_text'] ) ? $settings['no_results_text'] : esc_html__( 'No stories found.', 'custom-theme' );

        $wrapper_classes = array(
            'editorial-search-widget',
            'mode-' . esc_attr( $mode ),
            'icon-pos-' . esc_attr( $icon_pos ),
        );
        ?>
        <div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>" id="search-widget-<?php echo esc_attr( $widget_id ); ?>">

            <?php if ( 'modal_trigger' === $mode ) : ?>
                <!-- Search Modal Trigger Button -->
                <button type="button" class="search-modal-trigger-btn header-search-btn" aria-haspopup="dialog" aria-label="<?php esc_attr_e( 'Open search modal', 'custom-theme' ); ?>">
                    <span class="trigger-icon-wrap" aria-hidden="true">
                        <?php
                        if ( ! empty( $settings['modal_trigger_icon']['value'] ) ) {
                            Icons_Manager::render_icon( $settings['modal_trigger_icon'], array( 'aria-hidden' => 'true' ) );
                        } elseif ( function_exists( 'custom_theme_svg_icon' ) ) {
                            echo custom_theme_svg_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        }
                        ?>
                    </span>
                    <?php if ( ! empty( $settings['modal_trigger_label'] ) ) : ?>
                        <span class="trigger-text"><?php echo esc_html( $settings['modal_trigger_label'] ); ?></span>
                    <?php endif; ?>
                    <?php if ( 'yes' === $settings['show_shortcut_badge'] ) : ?>
                        <kbd class="trigger-shortcut-kbd" aria-hidden="true">⌘K</kbd>
                    <?php endif; ?>
                </button>

            <?php else : ?>
                <!-- Search Form (Inline Live or Classic Form) -->
                <form role="search" method="get" class="editorial-search-form" action="<?php echo esc_url( $home_url ); ?>"
                    data-live-search="<?php echo 'inline_live' === $mode ? 'true' : 'false'; ?>"
                    data-limit="<?php echo esc_attr( $limit ); ?>"
                    data-min-length="<?php echo esc_attr( $min_len ); ?>"
                    data-show-thumb="<?php echo esc_attr( $show_thumb ); ?>"
                    data-show-cat="<?php echo esc_attr( $show_cat ); ?>"
                    data-show-date="<?php echo esc_attr( $show_date ); ?>"
                    data-no-results="<?php echo esc_attr( $no_res_msg ); ?>">

                    <div class="search-field-container">
                        <?php if ( 'left' === $icon_pos ) : ?>
                            <span class="search-icon-wrapper icon-left" aria-hidden="true">
                                <?php
                                if ( ! empty( $settings['custom_search_icon']['value'] ) ) {
                                    Icons_Manager::render_icon( $settings['custom_search_icon'], array( 'aria-hidden' => 'true' ) );
                                } elseif ( function_exists( 'custom_theme_svg_icon' ) ) {
                                    echo custom_theme_svg_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                }
                                ?>
                            </span>
                        <?php endif; ?>

                        <input type="search"
                            class="editorial-search-input"
                            name="s"
                            placeholder="<?php echo esc_attr( $placeholder ); ?>"
                            value="<?php echo get_search_query(); ?>"
                            autocomplete="off"
                            aria-label="<?php esc_attr_e( 'Search query', 'custom-theme' ); ?>">

                        <!-- Search Loading Spinner -->
                        <span class="editorial-search-spinner" aria-hidden="true" style="display: none;">
                            <svg class="search-spinner-svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-dasharray="32" stroke-linecap="round"></circle></svg>
                        </span>

                        <?php if ( 'inline_live' === $mode && $show_clear ) : ?>
                            <button type="button" class="editorial-search-clear-btn" aria-label="<?php esc_attr_e( 'Clear search query', 'custom-theme' ); ?>" style="display: none;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        <?php endif; ?>

                        <?php if ( 'right' === $icon_pos ) : ?>
                            <span class="search-icon-wrapper icon-right" aria-hidden="true">
                                <?php
                                if ( ! empty( $settings['custom_search_icon']['value'] ) ) {
                                    Icons_Manager::render_icon( $settings['custom_search_icon'], array( 'aria-hidden' => 'true' ) );
                                } elseif ( function_exists( 'custom_theme_svg_icon' ) ) {
                                    echo custom_theme_svg_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                }
                                ?>
                            </span>
                        <?php endif; ?>

                        <?php if ( $show_submit ) : ?>
                            <button type="submit" class="editorial-search-submit-btn">
                                <?php echo esc_html( $submit_text ); ?>
                            </button>
                        <?php endif; ?>
                    </div><!-- .search-field-container -->

                    <?php if ( 'inline_live' === $mode ) : ?>
                        <!-- Live Dropdown Results Container -->
                        <div class="editorial-live-results" aria-live="polite" style="display: none;">
                            <div class="live-results-list"></div>
                            <div class="live-results-footer" style="display: none;">
                                <a href="#" class="view-all-results-link">
                                    <?php esc_html_e( 'View all results', 'custom-theme' ); ?> &rarr;
                                </a>
                            </div>
                        </div><!-- .editorial-live-results -->
                    <?php endif; ?>

                </form>
            <?php endif; ?>

        </div><!-- .editorial-search-widget -->
        <?php
    }
}
