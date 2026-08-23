<?php
/**
 * Elementor Widget: Hero Featured Story
 *
 * Displays a high-impact editorial hero featured post with comprehensive query,
 * layout, element, and styling customization.
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
use Elementor\Group_Control_Css_Filter;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Hero_Post extends Widget_Base {

    /**
     * Get Widget Name
     *
     * @return string
     */
    public function get_name() {
        return 'custom_theme_hero_post';
    }

    /**
     * Get Widget Title
     *
     * @return string
     */
    public function get_title() {
        return esc_html__( 'Hero Featured Story', 'custom-theme' );
    }

    /**
     * Get Widget Icon
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-featured-image';
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
        return array( 'hero', 'featured', 'post', 'article', 'editorial', 'story', 'banner', 'spotlight', 'lead' );
    }

    /**
     * Helper: Fetch categories for query control
     *
     * @return array
     */
    private function get_post_categories() {
        $options = array( '' => esc_html__( 'All Categories', 'custom-theme' ) );
        $categories = get_categories( array( 'hide_empty' => true ) );
        if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
            foreach ( $categories as $category ) {
                $options[ $category->slug ] = $category->name . ' (' . $category->count . ')';
            }
        }
        return $options;
    }

    /**
     * Helper: Fetch tags for query control
     *
     * @return array
     */
    private function get_post_tags() {
        $options = array( '' => esc_html__( 'All Tags', 'custom-theme' ) );
        $tags = get_tags( array( 'hide_empty' => true ) );
        if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
            foreach ( $tags as $tag ) {
                $options[ $tag->slug ] = $tag->name . ' (' . $tag->count . ')';
            }
        }
        return $options;
    }

    /**
     * Helper: Fetch authors for query control
     *
     * @return array
     */
    private function get_post_authors() {
        $options = array( '' => esc_html__( 'All Authors', 'custom-theme' ) );
        $users = get_users( array( 'has_published_posts' => array( 'post' ) ) );
        if ( ! empty( $users ) && ! is_wp_error( $users ) ) {
            foreach ( $users as $user ) {
                $options[ $user->ID ] = $user->display_name;
            }
        }
        return $options;
    }

    /**
     * Register Widget Controls
     */
    protected function register_controls() {

        // =========================================================================
        // CONTENT TAB: Query Settings
        // =========================================================================
        $this->start_controls_section(
            'section_query',
            array(
                'label' => esc_html__( 'Query Settings', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'query_source',
            array(
                'label'   => esc_html__( 'Source', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'latest',
                'options' => array(
                    'latest'   => esc_html__( 'Latest Post', 'custom-theme' ),
                    'sticky'   => esc_html__( 'Sticky / Featured Post', 'custom-theme' ),
                    'category' => esc_html__( 'Filter by Category', 'custom-theme' ),
                    'tag'      => esc_html__( 'Filter by Tag', 'custom-theme' ),
                    'author'   => esc_html__( 'Filter by Author', 'custom-theme' ),
                    'manual'   => esc_html__( 'Specific Post (by ID)', 'custom-theme' ),
                ),
            )
        );

        $this->add_control(
            'category_slug',
            array(
                'label'     => esc_html__( 'Category', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => '',
                'options'   => $this->get_post_categories(),
                'condition' => array(
                    'query_source' => 'category',
                ),
            )
        );

        $this->add_control(
            'tag_slug',
            array(
                'label'     => esc_html__( 'Tag', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => '',
                'options'   => $this->get_post_tags(),
                'condition' => array(
                    'query_source' => 'tag',
                ),
            )
        );

        $this->add_control(
            'author_id',
            array(
                'label'     => esc_html__( 'Author', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => '',
                'options'   => $this->get_post_authors(),
                'condition' => array(
                    'query_source' => 'author',
                ),
            )
        );

        $this->add_control(
            'manual_post_id',
            array(
                'label'       => esc_html__( 'Post ID', 'custom-theme' ),
                'type'        => Controls_Manager::NUMBER,
                'placeholder' => esc_html__( 'e.g. 124', 'custom-theme' ),
                'condition'   => array(
                    'query_source' => 'manual',
                ),
            )
        );

        $this->add_control(
            'orderby',
            array(
                'label'     => esc_html__( 'Order By', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'date',
                'options'   => array(
                    'date'          => esc_html__( 'Date Published', 'custom-theme' ),
                    'modified'      => esc_html__( 'Last Modified', 'custom-theme' ),
                    'title'         => esc_html__( 'Post Title', 'custom-theme' ),
                    'comment_count' => esc_html__( 'Comment Count (Popular)', 'custom-theme' ),
                    'rand'          => esc_html__( 'Random', 'custom-theme' ),
                    'ID'            => esc_html__( 'Post ID', 'custom-theme' ),
                ),
                'condition' => array(
                    'query_source!' => 'manual',
                ),
            )
        );

        $this->add_control(
            'order',
            array(
                'label'     => esc_html__( 'Order', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'DESC',
                'options'   => array(
                    'DESC' => esc_html__( 'Descending (3, 2, 1)', 'custom-theme' ),
                    'ASC'  => esc_html__( 'Ascending (1, 2, 3)', 'custom-theme' ),
                ),
                'condition' => array(
                    'query_source!' => array( 'manual', 'rand' ),
                ),
            )
        );

        $this->add_control(
            'query_offset',
            array(
                'label'       => esc_html__( 'Offset', 'custom-theme' ),
                'type'        => Controls_Manager::NUMBER,
                'default'     => 0,
                'min'         => 0,
                'max'         => 100,
                'description' => esc_html__( 'Skip a number of posts (useful to avoid duplicating items from other grids).', 'custom-theme' ),
                'condition'   => array(
                    'query_source!' => 'manual',
                ),
            )
        );

        $this->add_control(
            'exclude_sticky',
            array(
                'label'        => esc_html__( 'Ignore Sticky Posts', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Yes', 'custom-theme' ),
                'label_off'    => esc_html__( 'No', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition'    => array(
                    'query_source!' => array( 'manual', 'sticky' ),
                ),
            )
        );

        $this->add_control(
            'exclude_posts',
            array(
                'label'       => esc_html__( 'Exclude Post IDs', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'e.g. 10, 24, 56', 'custom-theme' ),
                'description' => esc_html__( 'Comma-separated post IDs to exclude from this hero card.', 'custom-theme' ),
                'condition'   => array(
                    'query_source!' => 'manual',
                ),
            )
        );

        $this->add_control(
            'date_filter',
            array(
                'label'     => esc_html__( 'Date Period', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'all',
                'options'   => array(
                    'all'     => esc_html__( 'All Time', 'custom-theme' ),
                    'today'   => esc_html__( 'Past 24 Hours', 'custom-theme' ),
                    'week'    => esc_html__( 'Past Week', 'custom-theme' ),
                    'month'   => esc_html__( 'Past Month', 'custom-theme' ),
                    'year'    => esc_html__( 'Past Year', 'custom-theme' ),
                ),
                'condition' => array(
                    'query_source!' => 'manual',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // CONTENT TAB: Layout & Structure
        // =========================================================================
        $this->start_controls_section(
            'section_layout',
            array(
                'label' => esc_html__( 'Layout & Structure', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'layout_style',
            array(
                'label'   => esc_html__( 'Layout Style', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'split',
                'options' => array(
                    'split'   => esc_html__( 'Split Card (Side-by-Side)', 'custom-theme' ),
                    'cover'   => esc_html__( 'Cover Card (Full-Bleed Overlay)', 'custom-theme' ),
                    'stacked' => esc_html__( 'Stacked (Top Media, Bottom Content)', 'custom-theme' ),
                ),
            )
        );

        $this->add_control(
            'image_position',
            array(
                'label'     => esc_html__( 'Image Position', 'custom-theme' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => array(
                    'left'  => array(
                        'title' => esc_html__( 'Left', 'custom-theme' ),
                        'icon'  => 'eicon-h-align-left',
                    ),
                    'right' => array(
                        'title' => esc_html__( 'Right', 'custom-theme' ),
                        'icon'  => 'eicon-h-align-right',
                    ),
                ),
                'default'   => 'left',
                'condition' => array(
                    'layout_style' => 'split',
                ),
            )
        );

        $this->add_responsive_control(
            'split_image_ratio',
            array(
                'label'      => esc_html__( 'Image Column Width (%)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( '%' ),
                'range'      => array(
                    '%' => array(
                        'min' => 25,
                        'max' => 75,
                        'step' => 1,
                    ),
                ),
                'default'    => array(
                    'unit' => '%',
                    'size' => 55,
                ),
                'condition'  => array(
                    'layout_style' => 'split',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .featured-hero-inner:not(.img-on-right)' => 'grid-template-columns: {{SIZE}}% calc(100% - {{SIZE}}%);',
                    '{{WRAPPER}} .featured-hero-inner.img-on-right'        => 'grid-template-columns: calc(100% - {{SIZE}}%) {{SIZE}}%;',
                ),
            )
        );

        $this->add_responsive_control(
            'content_valign',
            array(
                'label'     => esc_html__( 'Content Vertical Alignment', 'custom-theme' ),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => array(
                    'flex-start'    => array(
                        'title' => esc_html__( 'Top', 'custom-theme' ),
                        'icon'  => 'eicon-v-align-top',
                    ),
                    'center'        => array(
                        'title' => esc_html__( 'Center', 'custom-theme' ),
                        'icon'  => 'eicon-v-align-middle',
                    ),
                    'flex-end'      => array(
                        'title' => esc_html__( 'Bottom', 'custom-theme' ),
                        'icon'  => 'eicon-v-align-bottom',
                    ),
                    'space-between' => array(
                        'title' => esc_html__( 'Space Between', 'custom-theme' ),
                        'icon'  => 'eicon-v-align-stretch',
                    ),
                ),
                'default'   => 'center',
                'selectors' => array(
                    '{{WRAPPER}} .featured-hero-content' => 'justify-content: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'min_height',
            array(
                'label'      => esc_html__( 'Card Min Height', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px', 'vh' ),
                'range'      => array(
                    'px' => array(
                        'min' => 250,
                        'max' => 900,
                    ),
                    'vh' => array(
                        'min' => 20,
                        'max' => 100,
                    ),
                ),
                'default'    => array(
                    'unit' => 'px',
                    'size' => 440,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .featured-article-hero' => 'min-height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .featured-hero-inner'   => 'min-height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .featured-hero-cover'   => 'min-height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // CONTENT TAB: Elements & Toggles
        // =========================================================================
        $this->start_controls_section(
            'section_elements',
            array(
                'label' => esc_html__( 'Elements & Content', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        // Category Badge
        $this->add_control(
            'show_badge',
            array(
                'label'        => esc_html__( 'Category Badge', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'badge_position',
            array(
                'label'     => esc_html__( 'Badge Position', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'overlay',
                'options'   => array(
                    'overlay' => esc_html__( 'Overlay on Image', 'custom-theme' ),
                    'inline'  => esc_html__( 'Inside Content (Above Title)', 'custom-theme' ),
                ),
                'condition' => array(
                    'show_badge' => 'yes',
                ),
            )
        );

        // Title
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
                    'div'  => 'div',
                ),
            )
        );

        // Excerpt
        $this->add_control(
            'show_excerpt',
            array(
                'label'        => esc_html__( 'Excerpt', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'excerpt_length',
            array(
                'label'     => esc_html__( 'Excerpt Word Limit', 'custom-theme' ),
                'type'      => Controls_Manager::NUMBER,
                'default'   => 28,
                'min'       => 5,
                'max'       => 150,
                'condition' => array(
                    'show_excerpt' => 'yes',
                ),
            )
        );

        // Meta Controls
        $this->add_control(
            'heading_meta_elements',
            array(
                'label'     => esc_html__( 'Metadata Byline', 'custom-theme' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_control(
            'meta_position',
            array(
                'label'   => esc_html__( 'Meta Position', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'below_excerpt',
                'options' => array(
                    'above_title'   => esc_html__( 'Above Title', 'custom-theme' ),
                    'below_excerpt' => esc_html__( 'Below Excerpt', 'custom-theme' ),
                ),
            )
        );

        $this->add_control(
            'show_author',
            array(
                'label'        => esc_html__( 'Author Name', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'show_avatar',
            array(
                'label'        => esc_html__( 'Author Avatar', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition'    => array(
                    'show_author' => 'yes',
                ),
            )
        );

        $this->add_control(
            'show_date',
            array(
                'label'        => esc_html__( 'Publication Date', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'show_reading_time',
            array(
                'label'        => esc_html__( 'Reading Time Badge', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'meta_divider_symbol',
            array(
                'label'   => esc_html__( 'Divider Symbol', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'bullet',
                'options' => array(
                    'bullet' => esc_html__( '• Bullet', 'custom-theme' ),
                    'slash'  => esc_html__( '/ Slash', 'custom-theme' ),
                    'dash'   => esc_html__( '– Dash', 'custom-theme' ),
                    'pipe'   => esc_html__( '| Pipe', 'custom-theme' ),
                    'none'   => esc_html__( 'None (Spacing Only)', 'custom-theme' ),
                ),
            )
        );

        // CTA Button
        $this->add_control(
            'heading_cta_settings',
            array(
                'label'     => esc_html__( 'Call to Action Button', 'custom-theme' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_control(
            'show_cta',
            array(
                'label'        => esc_html__( 'CTA Button', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'cta_text',
            array(
                'label'       => esc_html__( 'Button Text', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'Read Full Article', 'custom-theme' ),
                'condition'   => array(
                    'show_cta' => 'yes',
                ),
            )
        );

        $this->add_control(
            'show_cta_icon',
            array(
                'label'        => esc_html__( 'Show Arrow Icon', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Yes', 'custom-theme' ),
                'label_off'    => esc_html__( 'No', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition'    => array(
                    'show_cta' => 'yes',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Card Container
        // =========================================================================
        $this->start_controls_section(
            'section_style_container',
            array(
                'label' => esc_html__( 'Card Container', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->start_controls_tabs( 'tabs_container_style' );

        $this->start_controls_tab(
            'tab_container_normal',
            array(
                'label' => esc_html__( 'Normal', 'custom-theme' ),
            )
        );

        $this->add_control(
            'card_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .featured-article-hero' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .featured-article-hero',
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'card_border',
                'selector' => '{{WRAPPER}} .featured-article-hero',
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_container_hover',
            array(
                'label' => esc_html__( 'Hover', 'custom-theme' ),
            )
        );

        $this->add_control(
            'card_bg_hover_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .featured-article-hero:hover' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'card_hover_box_shadow',
                'selector' => '{{WRAPPER}} .featured-article-hero:hover',
            )
        );

        $this->add_control(
            'card_hover_border_color',
            array(
                'label'     => esc_html__( 'Border Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .featured-article-hero:hover' => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'card_hover_lift',
            array(
                'label'      => esc_html__( 'Hover Lift Effect (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => -20,
                        'max' => 0,
                        'step' => 1,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .featured-article-hero:hover' => 'transform: translateY({{SIZE}}{{UNIT}});',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'card_padding',
            array(
                'label'      => esc_html__( 'Content Padding', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', 'rem', '%' ),
                'separator'  => 'before',
                'selectors'  => array(
                    '{{WRAPPER}} .featured-hero-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .featured-hero-cover-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'card_border_radius',
            array(
                'label'      => esc_html__( 'Card Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .featured-article-hero' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Image / Media
        // =========================================================================
        $this->start_controls_section(
            'section_style_media',
            array(
                'label' => esc_html__( 'Image / Media', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'enable_hover_zoom',
            array(
                'label'        => esc_html__( 'Image Hover Zoom', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Enable', 'custom-theme' ),
                'label_off'    => esc_html__( 'Disable', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'image_zoom_scale',
            array(
                'label'     => esc_html__( 'Zoom Scale', 'custom-theme' ),
                'type'      => Controls_Manager::SLIDER,
                'range'     => array(
                    'px' => array(
                        'min'  => 1.0,
                        'max'  => 1.25,
                        'step' => 0.01,
                    ),
                ),
                'default'   => array(
                    'size' => 1.05,
                ),
                'condition' => array(
                    'enable_hover_zoom' => 'yes',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .featured-article-hero:hover .featured-hero-media img' => 'transform: scale({{SIZE}});',
                ),
            )
        );

        $this->add_responsive_control(
            'media_border_radius',
            array(
                'label'      => esc_html__( 'Image Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .featured-hero-media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'cover_overlay_color',
            array(
                'label'     => esc_html__( 'Cover Gradient Overlay', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'layout_style' => 'cover',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .featured-hero-cover-overlay' => 'background: linear-gradient(180deg, transparent 0%, {{VALUE}} 80%);',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Category Badge
        // =========================================================================
        $this->start_controls_section(
            'section_style_badge',
            array(
                'label'     => esc_html__( 'Category Badge', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'show_badge' => 'yes',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'badge_typography',
                'selector' => '{{WRAPPER}} .category-badge',
            )
        );

        $this->start_controls_tabs( 'tabs_badge_style' );

        $this->start_controls_tab(
            'tab_badge_normal',
            array(
                'label' => esc_html__( 'Normal', 'custom-theme' ),
            )
        );

        $this->add_control(
            'badge_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .category-badge' => 'background-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'badge_text_color',
            array(
                'label'     => esc_html__( 'Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .category-badge' => 'color: {{VALUE}} !important;',
                ),
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_badge_hover',
            array(
                'label' => esc_html__( 'Hover', 'custom-theme' ),
            )
        );

        $this->add_control(
            'badge_bg_hover_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .category-badge:hover' => 'background-color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'badge_text_hover_color',
            array(
                'label'     => esc_html__( 'Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .category-badge:hover' => 'color: {{VALUE}} !important;',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'badge_padding',
            array(
                'label'      => esc_html__( 'Padding', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', 'rem' ),
                'separator'  => 'before',
                'selectors'  => array(
                    '{{WRAPPER}} .category-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ),
            )
        );

        $this->add_responsive_control(
            'badge_border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .category-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Title & Excerpt
        // =========================================================================
        $this->start_controls_section(
            'section_style_typography',
            array(
                'label' => esc_html__( 'Title & Excerpt', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        // Title
        $this->add_control(
            'heading_title_style',
            array(
                'label' => esc_html__( 'Post Title', 'custom-theme' ),
                'type'  => Controls_Manager::HEADING,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .featured-hero-title',
            )
        );

        $this->add_control(
            'title_color',
            array(
                'label'     => esc_html__( 'Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .featured-hero-title a' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'title_hover_color',
            array(
                'label'     => esc_html__( 'Hover Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .featured-hero-title a:hover' => 'color: {{VALUE}} !important;',
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
                        'max' => 60,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .featured-hero-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        // Excerpt
        $this->add_control(
            'heading_excerpt_style',
            array(
                'label'     => esc_html__( 'Excerpt', 'custom-theme' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => array(
                    'show_excerpt' => 'yes',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'      => 'excerpt_typography',
                'selector'  => '{{WRAPPER}} .featured-hero-excerpt',
                'condition' => array(
                    'show_excerpt' => 'yes',
                ),
            )
        );

        $this->add_control(
            'excerpt_color',
            array(
                'label'     => esc_html__( 'Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .featured-hero-excerpt' => 'color: {{VALUE}};',
                ),
                'condition' => array(
                    'show_excerpt' => 'yes',
                ),
            )
        );

        $this->add_responsive_control(
            'excerpt_spacing',
            array(
                'label'      => esc_html__( 'Bottom Spacing (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 60,
                    ),
                ),
                'condition'  => array(
                    'show_excerpt' => 'yes',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .featured-hero-excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Metadata Byline
        // =========================================================================
        $this->start_controls_section(
            'section_style_meta',
            array(
                'label' => esc_html__( 'Metadata Byline', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'meta_typography',
                'selector' => '{{WRAPPER}} .featured-hero-meta, {{WRAPPER}} .featured-hero-meta .meta-item',
            )
        );

        $this->add_control(
            'meta_color',
            array(
                'label'     => esc_html__( 'Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .featured-hero-meta' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .featured-hero-meta .meta-item' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'meta_link_color',
            array(
                'label'     => esc_html__( 'Link Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .featured-hero-meta a' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'meta_link_hover_color',
            array(
                'label'     => esc_html__( 'Link Hover Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .featured-hero-meta a:hover' => 'color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'divider_color',
            array(
                'label'     => esc_html__( 'Divider Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .featured-hero-meta .meta-divider' => 'color: {{VALUE}}; opacity: 1;',
                ),
            )
        );

        $this->add_responsive_control(
            'avatar_size',
            array(
                'label'      => esc_html__( 'Avatar Size (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 16,
                        'max' => 60,
                    ),
                ),
                'default'    => array(
                    'size' => 24,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .featured-hero-meta .author-avatar img' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important; border-radius: 50% !important; object-fit: cover !important;',
                ),
            )
        );

        $this->add_responsive_control(
            'avatar_border_radius',
            array(
                'label'      => esc_html__( 'Avatar Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'default'    => array(
                    'top'      => 50,
                    'right'    => 50,
                    'bottom'   => 50,
                    'left'     => 50,
                    'unit'     => '%',
                    'isLinked' => true,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .featured-hero-meta .author-avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ),
            )
        );

        $this->add_responsive_control(
            'meta_spacing',
            array(
                'label'      => esc_html__( 'Bottom Spacing (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 60,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .featured-hero-meta' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: CTA Button
        // =========================================================================
        $this->start_controls_section(
            'section_style_cta',
            array(
                'label'     => esc_html__( 'CTA Button', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'show_cta' => 'yes',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'cta_typography',
                'selector' => '{{WRAPPER}} .featured-hero-cta .btn',
            )
        );

        $this->start_controls_tabs( 'tabs_cta_style' );

        $this->start_controls_tab(
            'tab_cta_normal',
            array(
                'label' => esc_html__( 'Normal', 'custom-theme' ),
            )
        );

        $this->add_control(
            'cta_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .featured-hero-cta .btn' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'cta_text_color',
            array(
                'label'     => esc_html__( 'Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .featured-hero-cta .btn' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .featured-hero-cta .btn *' => 'color: {{VALUE}} !important; stroke: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'cta_border',
                'selector' => '{{WRAPPER}} .featured-hero-cta .btn',
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'cta_box_shadow',
                'selector' => '{{WRAPPER}} .featured-hero-cta .btn',
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_cta_hover',
            array(
                'label' => esc_html__( 'Hover', 'custom-theme' ),
            )
        );

        $this->add_control(
            'cta_bg_hover_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .featured-hero-cta .btn:hover' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'cta_text_hover_color',
            array(
                'label'     => esc_html__( 'Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .featured-hero-cta .btn:hover' => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .featured-hero-cta .btn:hover *' => 'color: {{VALUE}} !important; stroke: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'cta_border_hover_color',
            array(
                'label'     => esc_html__( 'Border Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .featured-hero-cta .btn:hover' => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'cta_hover_box_shadow',
                'selector' => '{{WRAPPER}} .featured-hero-cta .btn:hover',
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'cta_padding',
            array(
                'label'      => esc_html__( 'Padding', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', 'rem' ),
                'separator'  => 'before',
                'selectors'  => array(
                    '{{WRAPPER}} .featured-hero-cta .btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'cta_border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .featured-hero-cta .btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Query post based on widget settings
     *
     * @return \WP_Query
     */
    private function get_query() {
        $settings = $this->get_settings_for_display();

        $query_args = array(
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => 1,
            'ignore_sticky_posts' => ( 'yes' === $settings['exclude_sticky'] ) ? 1 : 0,
            'no_found_rows'       => true,
        );

        $source = ! empty( $settings['query_source'] ) ? $settings['query_source'] : 'latest';

        switch ( $source ) {
            case 'sticky':
                $sticky = get_option( 'sticky_posts' );
                if ( ! empty( $sticky ) ) {
                    $query_args['post__in'] = $sticky;
                    $query_args['ignore_sticky_posts'] = 0;
                }
                break;

            case 'category':
                if ( ! empty( $settings['category_slug'] ) ) {
                    $query_args['category_name'] = sanitize_title( $settings['category_slug'] );
                }
                break;

            case 'tag':
                if ( ! empty( $settings['tag_slug'] ) ) {
                    $query_args['tag'] = sanitize_title( $settings['tag_slug'] );
                }
                break;

            case 'author':
                if ( ! empty( $settings['author_id'] ) ) {
                    $query_args['author'] = absint( $settings['author_id'] );
                }
                break;

            case 'manual':
                if ( ! empty( $settings['manual_post_id'] ) ) {
                    $query_args['p'] = absint( $settings['manual_post_id'] );
                }
                break;
        }

        // Exclude specific post IDs
        if ( ! empty( $settings['exclude_posts'] ) ) {
            $exclude_ids = array_map( 'absint', explode( ',', $settings['exclude_posts'] ) );
            $query_args['post__not_in'] = $exclude_ids;
        }

        // Orderby & Order
        if ( 'manual' !== $source ) {
            $query_args['orderby'] = ! empty( $settings['orderby'] ) ? $settings['orderby'] : 'date';
            $query_args['order']   = ! empty( $settings['order'] ) ? $settings['order'] : 'DESC';

            if ( ! empty( $settings['query_offset'] ) ) {
                $query_args['offset'] = absint( $settings['query_offset'] );
            }
        }

        // Date Query Filter
        if ( ! empty( $settings['date_filter'] ) && 'all' !== $settings['date_filter'] ) {
            $date_query = array();
            switch ( $settings['date_filter'] ) {
                case 'today':
                    $date_query['after'] = '1 day ago';
                    break;
                case 'week':
                    $date_query['after'] = '1 week ago';
                    break;
                case 'month':
                    $date_query['after'] = '1 month ago';
                    break;
                case 'year':
                    $date_query['after'] = '1 year ago';
                    break;
            }
            $query_args['date_query'] = array( $date_query );
        }

        return new \WP_Query( $query_args );
    }

    /**
     * Helper: Render metadata row
     *
     * @param array $settings
     */
    private function render_meta( $settings ) {
        $show_author  = 'yes' === $settings['show_author'];
        $show_avatar  = 'yes' === $settings['show_avatar'];
        $show_date    = 'yes' === $settings['show_date'];
        $show_reading = 'yes' === $settings['show_reading_time'];
        $divider_sym  = ! empty( $settings['meta_divider_symbol'] ) ? $settings['meta_divider_symbol'] : 'bullet';

        $divider_char = '&bull;';
        if ( 'slash' === $divider_sym ) {
            $divider_char = '/';
        } elseif ( 'dash' === $divider_sym ) {
            $divider_char = '&ndash;';
        } elseif ( 'pipe' === $divider_sym ) {
            $divider_char = '|';
        } elseif ( 'none' === $divider_sym ) {
            $divider_char = '';
        }

        if ( ! $show_author && ! $show_date && ! $show_reading ) {
            return;
        }
        ?>
        <div class="featured-hero-meta entry-meta">
            <?php
            $has_prev = false;
            if ( $show_author && function_exists( 'custom_theme_posted_by' ) ) {
                custom_theme_posted_by( $show_avatar );
                $has_prev = true;
            }
            if ( $show_date && function_exists( 'custom_theme_posted_on' ) ) {
                if ( $has_prev && ! empty( $divider_char ) ) {
                    echo '<span class="meta-divider">' . $divider_char . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                }
                custom_theme_posted_on();
                $has_prev = true;
            }
            if ( $show_reading && function_exists( 'custom_theme_reading_time_badge' ) ) {
                if ( $has_prev && ! empty( $divider_char ) ) {
                    echo '<span class="meta-divider">' . $divider_char . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                }
                custom_theme_reading_time_badge();
            }
            ?>
        </div>
        <?php
    }

    /**
     * Render Widget Output
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $query    = $this->get_query();

        if ( ! $query->have_posts() ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div class="elementor-alert elementor-alert-info">' . esc_html__( 'No posts found matching your query criteria.', 'custom-theme' ) . '</div>';
            }
            return;
        }

        while ( $query->have_posts() ) :
            $query->the_post();

            $layout_style   = ! empty( $settings['layout_style'] ) ? $settings['layout_style'] : 'split';
            $image_position = ! empty( $settings['image_position'] ) ? $settings['image_position'] : 'left';
            $title_tag      = ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h2';
            $show_badge     = 'yes' === $settings['show_badge'];
            $badge_pos      = ! empty( $settings['badge_position'] ) ? $settings['badge_position'] : 'overlay';
            $show_excerpt   = 'yes' === $settings['show_excerpt'];
            $excerpt_length = ! empty( $settings['excerpt_length'] ) ? absint( $settings['excerpt_length'] ) : 28;
            $meta_pos       = ! empty( $settings['meta_position'] ) ? $settings['meta_position'] : 'below_excerpt';
            $show_cta       = 'yes' === $settings['show_cta'];
            $cta_text       = ! empty( $settings['cta_text'] ) ? $settings['cta_text'] : esc_html__( 'Read Full Article', 'custom-theme' );
            $show_cta_icon  = 'yes' === $settings['show_cta_icon'];

            $article_classes = array(
                'featured-article-hero',
                'elementor-hero-post',
                'layout-' . esc_attr( $layout_style ),
            );

            if ( 'split' === $layout_style ) {
                $article_classes[] = 'img-pos-' . esc_attr( $image_position );
            }
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( implode( ' ', $article_classes ) ); ?>>

                <?php if ( 'cover' === $layout_style ) : ?>
                    <!-- Cover Layout Style -->
                    <div class="featured-hero-cover" style="<?php if ( has_post_thumbnail() ) : ?>background-image: url('<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'custom-theme-featured' ) ); ?>');<?php endif; ?>">
                        <div class="featured-hero-cover-overlay"></div>
                        <div class="featured-hero-cover-content">

                            <?php if ( $show_badge && function_exists( 'custom_theme_category_badge' ) ) : ?>
                                <div class="featured-hero-badge-overlay">
                                    <?php custom_theme_category_badge(); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( 'above_title' === $meta_pos ) : ?>
                                <?php $this->render_meta( $settings ); ?>
                            <?php endif; ?>

                            <<?php echo esc_attr( $title_tag ); ?> class="featured-hero-title">
                                <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
                                    <?php the_title(); ?>
                                </a>
                            </<?php echo esc_attr( $title_tag ); ?>>

                            <?php if ( $show_excerpt ) : ?>
                                <div class="featured-hero-excerpt lead">
                                    <?php echo esc_html( wp_trim_words( get_the_excerpt(), $excerpt_length ) ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( 'below_excerpt' === $meta_pos ) : ?>
                                <?php $this->render_meta( $settings ); ?>
                            <?php endif; ?>

                            <?php if ( $show_cta ) : ?>
                                <div class="featured-hero-cta">
                                    <a href="<?php echo esc_url( get_permalink() ); ?>" class="btn btn-primary">
                                        <span><?php echo esc_html( $cta_text ); ?></span>
                                        <?php
                                        if ( $show_cta_icon && function_exists( 'custom_theme_svg_icon' ) ) {
                                            echo custom_theme_svg_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        }
                                        ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                        </div><!-- .featured-hero-cover-content -->
                    </div><!-- .featured-hero-cover -->

                <?php else : ?>
                    <!-- Split / Stacked Layout Style -->
                    <div class="featured-hero-inner <?php echo ( 'split' === $layout_style && 'right' === $image_position ) ? 'img-on-right' : ''; ?> <?php echo ( 'stacked' === $layout_style ) ? 'layout-stacked' : ''; ?>">
                        
                        <!-- Media Container -->
                        <div class="featured-hero-media">
                            <?php
                            if ( function_exists( 'custom_theme_post_thumbnail' ) ) {
                                custom_theme_post_thumbnail( 'custom-theme-featured', true, 'featured-hero-thumbnail', false );
                            } else {
                                the_post_thumbnail( 'custom-theme-featured' );
                            }
                            ?>
                            <?php if ( $show_badge && 'overlay' === $badge_pos && function_exists( 'custom_theme_category_badge' ) ) : ?>
                                <div class="featured-hero-badge-overlay">
                                    <?php custom_theme_category_badge(); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Content Details -->
                        <div class="featured-hero-content">

                            <?php if ( $show_badge && 'inline' === $badge_pos && function_exists( 'custom_theme_category_badge' ) ) : ?>
                                <div class="featured-hero-badge-inline" style="margin-bottom: var(--space-xs);">
                                    <?php custom_theme_category_badge(); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( 'above_title' === $meta_pos ) : ?>
                                <?php $this->render_meta( $settings ); ?>
                            <?php endif; ?>
                            
                            <<?php echo esc_attr( $title_tag ); ?> class="featured-hero-title">
                                <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
                                    <?php the_title(); ?>
                                </a>
                            </<?php echo esc_attr( $title_tag ); ?>>

                            <?php if ( $show_excerpt ) : ?>
                                <div class="featured-hero-excerpt lead">
                                    <?php echo esc_html( wp_trim_words( get_the_excerpt(), $excerpt_length ) ); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ( 'below_excerpt' === $meta_pos ) : ?>
                                <?php $this->render_meta( $settings ); ?>
                            <?php endif; ?>

                            <?php if ( $show_cta ) : ?>
                                <div class="featured-hero-cta">
                                    <a href="<?php echo esc_url( get_permalink() ); ?>" class="btn btn-primary">
                                        <span><?php echo esc_html( $cta_text ); ?></span>
                                        <?php
                                        if ( $show_cta_icon && function_exists( 'custom_theme_svg_icon' ) ) {
                                            echo custom_theme_svg_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        }
                                        ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                        </div><!-- .featured-hero-content -->

                    </div><!-- .featured-hero-inner -->
                <?php endif; ?>

            </article><!-- #post-<?php the_ID(); ?> -->
            <?php
        endwhile;

        wp_reset_postdata();
    }
}
