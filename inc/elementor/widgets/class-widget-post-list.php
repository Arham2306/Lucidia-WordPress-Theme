<?php
/**
 * Elementor Widget: Horizontal Post List
 *
 * Displays a side-by-side horizontal editorial post list with flexible thumbnail sizing,
 * image positioning, list style presets (standard, borderless, divided), and rank counters.
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

class Post_List extends Widget_Base {

    /**
     * Get Widget Name
     *
     * @return string
     */
    public function get_name() {
        return 'custom_theme_post_list';
    }

    /**
     * Get Widget Title
     *
     * @return string
     */
    public function get_title() {
        return esc_html__( 'Horizontal Post List', 'custom-theme' );
    }

    /**
     * Get Widget Icon
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-post-list';
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
        return array( 'post', 'list', 'horizontal', 'side-by-side', 'articles', 'editorial', 'trending', 'sidebar', 'feed' );
    }

    /**
     * Helper: Fetch categories for query control
     *
     * @return array
     */
    private function get_post_categories() {
        $options = array();
        $categories = get_categories( array( 'hide_empty' => false ) );
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
        $options = array();
        $tags = get_tags( array( 'hide_empty' => false ) );
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
            'posts_per_page',
            array(
                'label'   => esc_html__( 'Number of Posts', 'custom-theme' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 4,
                'min'     => 1,
                'max'     => 24,
            )
        );

        $this->add_control(
            'query_source',
            array(
                'label'   => esc_html__( 'Source', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'latest',
                'options' => array(
                    'latest'   => esc_html__( 'Latest Posts', 'custom-theme' ),
                    'sticky'   => esc_html__( 'Sticky / Featured Posts', 'custom-theme' ),
                    'category' => esc_html__( 'Filter by Category', 'custom-theme' ),
                    'tag'      => esc_html__( 'Filter by Tag', 'custom-theme' ),
                    'author'   => esc_html__( 'Filter by Author', 'custom-theme' ),
                    'manual'   => esc_html__( 'Specific Posts (by IDs)', 'custom-theme' ),
                ),
            )
        );

        $this->add_control(
            'category_slugs',
            array(
                'label'       => esc_html__( 'Categories', 'custom-theme' ),
                'type'        => Controls_Manager::SELECT2,
                'multiple'    => true,
                'default'     => array(),
                'options'     => $this->get_post_categories(),
                'label_block' => true,
                'condition'   => array(
                    'query_source' => 'category',
                ),
            )
        );

        $this->add_control(
            'tag_slugs',
            array(
                'label'       => esc_html__( 'Tags', 'custom-theme' ),
                'type'        => Controls_Manager::SELECT2,
                'multiple'    => true,
                'default'     => array(),
                'options'     => $this->get_post_tags(),
                'label_block' => true,
                'condition'   => array(
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
            'manual_post_ids',
            array(
                'label'       => esc_html__( 'Post IDs', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => esc_html__( 'e.g. 12, 45, 89', 'custom-theme' ),
                'description' => esc_html__( 'Comma-separated list of post IDs.', 'custom-theme' ),
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
                    'comment_count' => esc_html__( 'Comment Count (Popularity)', 'custom-theme' ),
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
                    'DESC' => esc_html__( 'Descending (Newest first)', 'custom-theme' ),
                    'ASC'  => esc_html__( 'Ascending (Oldest first)', 'custom-theme' ),
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
                'description' => esc_html__( 'Skip a number of posts.', 'custom-theme' ),
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
                'description' => esc_html__( 'Comma-separated post IDs to exclude.', 'custom-theme' ),
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
            'section_list_layout',
            array(
                'label' => esc_html__( 'Layout & Structure', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_responsive_control(
            'list_columns',
            array(
                'label'          => esc_html__( 'List Columns', 'custom-theme' ),
                'type'           => Controls_Manager::SELECT,
                'default'        => '1',
                'tablet_default' => '1',
                'mobile_default' => '1',
                'options'        => array(
                    '1' => '1 Column (Standard Vertical Stack)',
                    '2' => '2 Columns (Dual List Columns)',
                ),
                'selectors'      => array(
                    '{{WRAPPER}} .editorial-post-list-container' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ),
            )
        );

        $this->add_control(
            'card_style_preset',
            array(
                'label'   => esc_html__( 'List Preset Style', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'standard',
                'options' => array(
                    'standard'   => esc_html__( 'Standard (Bordered Card)', 'custom-theme' ),
                    'borderless' => esc_html__( 'Borderless Minimal', 'custom-theme' ),
                    'divided'    => esc_html__( 'Divided (Separator Lines)', 'custom-theme' ),
                ),
            )
        );

        $this->add_control(
            'image_position',
            array(
                'label'   => esc_html__( 'Image Position', 'custom-theme' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => array(
                    'left'  => array(
                        'title' => esc_html__( 'Left', 'custom-theme' ),
                        'icon'  => 'eicon-h-align-left',
                    ),
                    'right' => array(
                        'title' => esc_html__( 'Right', 'custom-theme' ),
                        'icon'  => 'eicon-h-align-right',
                    ),
                ),
                'default' => 'left',
            )
        );

        $this->add_responsive_control(
            'image_width',
            array(
                'label'      => esc_html__( 'Thumbnail Width (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 80,
                        'max' => 360,
                    ),
                ),
                'default'    => array(
                    'unit' => 'px',
                    'size' => 200,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .article-card-list:not(.img-on-right)' => 'grid-template-columns: {{SIZE}}{{UNIT}} 1fr;',
                    '{{WRAPPER}} .article-card-list.img-on-right'        => 'grid-template-columns: 1fr {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'image_aspect_ratio',
            array(
                'label'     => esc_html__( 'Image Aspect Ratio', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => '3/2',
                'options'   => array(
                    '3/2'   => '3 : 2 (Standard Photo)',
                    '16/10' => '16 : 10 (Editorial)',
                    '16/9'  => '16 : 9 (Widescreen)',
                    '4/3'   => '4 : 3 (Classic Photo)',
                    '1/1'   => '1 : 1 (Square)',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .card-list-media' => 'aspect-ratio: {{VALUE}};',
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
                    '{{WRAPPER}} .card-list-content' => 'justify-content: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'item_gap',
            array(
                'label'      => esc_html__( 'List Items Gap (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 60,
                    ),
                ),
                'default'    => array(
                    'unit' => 'px',
                    'size' => 20,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-post-list-container' => 'gap: {{SIZE}}{{UNIT}};',
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
                'label' => esc_html__( 'Elements & Toggles', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        // Media
        $this->add_control(
            'show_thumbnail',
            array(
                'label'        => esc_html__( 'Featured Image', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        // Category
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

        // Title
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
                    'h5'   => 'H5',
                    'div'  => 'div',
                ),
            )
        );

        $this->add_control(
            'title_lines',
            array(
                'label'     => esc_html__( 'Title Max Lines (Clamp)', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'none',
                'options'   => array(
                    'none' => esc_html__( 'No limit', 'custom-theme' ),
                    '1'    => '1 Line',
                    '2'    => '2 Lines',
                    '3'    => '3 Lines',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .card-list-title' => '-webkit-line-clamp: {{VALUE}}; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden;',
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
                'default'   => 16,
                'min'       => 5,
                'max'       => 60,
                'condition' => array(
                    'show_excerpt' => 'yes',
                ),
            )
        );

        $this->add_control(
            'excerpt_lines',
            array(
                'label'     => esc_html__( 'Excerpt Max Lines', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => '2',
                'options'   => array(
                    'none' => esc_html__( 'No limit', 'custom-theme' ),
                    '1'    => '1 Line',
                    '2'    => '2 Lines',
                    '3'    => '3 Lines',
                ),
                'condition' => array(
                    'show_excerpt' => 'yes',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .card-list-excerpt' => '-webkit-line-clamp: {{VALUE}}; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden;',
                ),
            )
        );

        // Metadata Byline
        $this->add_control(
            'heading_meta_elements',
            array(
                'label'     => esc_html__( 'Metadata Row', 'custom-theme' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
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
                    'none'   => esc_html__( 'None', 'custom-theme' ),
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Item Card & Container
        // =========================================================================
        $this->start_controls_section(
            'section_style_items',
            array(
                'label' => esc_html__( 'List Item Container', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->start_controls_tabs( 'tabs_item_style' );

        $this->start_controls_tab(
            'tab_item_normal',
            array(
                'label' => esc_html__( 'Normal', 'custom-theme' ),
            )
        );

        $this->add_control(
            'item_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .article-card-list' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'item_box_shadow',
                'selector' => '{{WRAPPER}} .article-card-list',
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'item_border',
                'selector' => '{{WRAPPER}} .article-card-list',
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_item_hover',
            array(
                'label' => esc_html__( 'Hover', 'custom-theme' ),
            )
        );

        $this->add_control(
            'item_bg_hover_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .article-card-list:hover' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'item_hover_box_shadow',
                'selector' => '{{WRAPPER}} .article-card-list:hover',
            )
        );

        $this->add_control(
            'item_hover_border_color',
            array(
                'label'     => esc_html__( 'Border Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .article-card-list:hover' => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'item_hover_lift',
            array(
                'label'      => esc_html__( 'Hover Lift Effect (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min'  => -12,
                        'max'  => 0,
                        'step' => 1,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .article-card-list:hover' => 'transform: translateY({{SIZE}}{{UNIT}});',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'item_padding',
            array(
                'label'      => esc_html__( 'Item Inner Padding', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', 'rem' ),
                'separator'  => 'before',
                'selectors'  => array(
                    '{{WRAPPER}} .article-card-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'item_border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .article-card-list' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'divider_line_color',
            array(
                'label'     => esc_html__( 'Separator Line Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'card_style_preset' => 'divided',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .article-card-list.preset-divided' => 'border-bottom-color: {{VALUE}};',
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
                'label'     => esc_html__( 'Thumbnail & Media', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'show_thumbnail' => 'yes',
                ),
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
                    '{{WRAPPER}} .article-card-list:hover .card-list-media img' => 'transform: scale({{SIZE}});',
                ),
            )
        );

        $this->add_responsive_control(
            'media_border_radius',
            array(
                'label'      => esc_html__( 'Thumbnail Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .card-list-media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'selector' => '{{WRAPPER}} .card-list-title',
            )
        );

        $this->add_control(
            'title_color',
            array(
                'label'     => esc_html__( 'Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .card-list-title a' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'title_hover_color',
            array(
                'label'     => esc_html__( 'Hover Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .card-list-title a:hover' => 'color: {{VALUE}} !important;',
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
                        'max' => 30,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .card-list-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
                'selector'  => '{{WRAPPER}} .card-list-excerpt',
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
                    '{{WRAPPER}} .card-list-excerpt' => 'color: {{VALUE}};',
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
                        'max' => 30,
                    ),
                ),
                'condition'  => array(
                    'show_excerpt' => 'yes',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .card-list-excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Metadata Row
        // =========================================================================
        $this->start_controls_section(
            'section_style_meta',
            array(
                'label' => esc_html__( 'Metadata Row', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'meta_typography',
                'selector' => '{{WRAPPER}} .card-list-footer .card-meta-left, {{WRAPPER}} .card-list-footer .meta-item',
            )
        );

        $this->add_control(
            'meta_color',
            array(
                'label'     => esc_html__( 'Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .card-list-footer .card-meta-left' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .card-list-footer .meta-item'       => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'meta_link_color',
            array(
                'label'     => esc_html__( 'Link Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .card-list-footer a' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'meta_link_hover_color',
            array(
                'label'     => esc_html__( 'Link Hover Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .card-list-footer a:hover' => 'color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'divider_color',
            array(
                'label'     => esc_html__( 'Divider Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .card-list-footer .meta-divider' => 'color: {{VALUE}}; opacity: 1;',
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
                        'min' => 14,
                        'max' => 40,
                    ),
                ),
                'default'    => array(
                    'size' => 20,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .card-list-footer .author-avatar img' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important; border-radius: 50% !important; object-fit: cover !important;',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Query posts based on widget settings
     *
     * @return \WP_Query
     */
    private function get_query() {
        $settings = $this->get_settings_for_display();

        $posts_per_page = ! empty( $settings['posts_per_page'] ) ? absint( $settings['posts_per_page'] ) : 4;

        $query_args = array(
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => $posts_per_page,
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
                $cat_slugs = ! empty( $settings['category_slugs'] ) ? $settings['category_slugs'] : array();
                if ( ! empty( $cat_slugs ) ) {
                    if ( is_array( $cat_slugs ) ) {
                        $query_args['category_name'] = implode( ',', array_map( 'sanitize_title', $cat_slugs ) );
                    } else {
                        $query_args['category_name'] = sanitize_title( $cat_slugs );
                    }
                }
                break;

            case 'tag':
                $tag_slugs = ! empty( $settings['tag_slugs'] ) ? $settings['tag_slugs'] : array();
                if ( ! empty( $tag_slugs ) ) {
                    if ( is_array( $tag_slugs ) ) {
                        $query_args['tag_slug__in'] = array_map( 'sanitize_title', $tag_slugs );
                    } else {
                        $query_args['tag'] = sanitize_title( $tag_slugs );
                    }
                }
                break;

            case 'author':
                if ( ! empty( $settings['author_id'] ) ) {
                    $query_args['author'] = absint( $settings['author_id'] );
                }
                break;

            case 'manual':
                if ( ! empty( $settings['manual_post_ids'] ) ) {
                    $manual_ids = array_map( 'absint', explode( ',', $settings['manual_post_ids'] ) );
                    $query_args['post__in'] = $manual_ids;
                    $query_args['orderby']  = 'post__in';
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
        <footer class="card-list-footer entry-meta">
            <div class="card-meta-left">
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
        </footer>
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
                echo '<div class="elementor-alert elementor-alert-info">' . esc_html__( 'No posts found matching the Post List criteria.', 'custom-theme' ) . '</div>';
            }
            return;
        }

        $preset_style   = ! empty( $settings['card_style_preset'] ) ? $settings['card_style_preset'] : 'standard';
        $image_pos      = ! empty( $settings['image_position'] ) ? $settings['image_position'] : 'left';
        $show_thumb     = 'yes' === $settings['show_thumbnail'];
        $show_badge     = 'yes' === $settings['show_badge'];
        $title_tag      = ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h3';
        $show_excerpt   = 'yes' === $settings['show_excerpt'];
        $excerpt_length = ! empty( $settings['excerpt_length'] ) ? absint( $settings['excerpt_length'] ) : 16;

        $container_classes = array(
            'editorial-post-list-container',
            'elementor-post-list',
            'style-' . esc_attr( $preset_style ),
        );
        ?>
        <div class="<?php echo esc_attr( implode( ' ', $container_classes ) ); ?>">
            <?php
            while ( $query->have_posts() ) :
                $query->the_post();

                $has_media = $show_thumb && has_post_thumbnail();

                $article_classes = array(
                    'article-card-list',
                    'preset-' . esc_attr( $preset_style ),
                );

                if ( ! $has_media ) {
                    $article_classes[] = 'no-media';
                } elseif ( 'right' === $image_pos ) {
                    $article_classes[] = 'img-on-right';
                }
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( implode( ' ', $article_classes ) ); ?>>

                    <?php if ( $has_media ) : ?>
                        <div class="card-list-media">
                            <?php
                            if ( function_exists( 'custom_theme_post_thumbnail' ) ) {
                                custom_theme_post_thumbnail( 'custom-theme-compact', true );
                            } else {
                                the_post_thumbnail( 'custom-theme-compact' );
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <div class="card-list-content">

                        <?php if ( $show_badge && function_exists( 'custom_theme_category_badge' ) ) : ?>
                            <div class="card-list-category">
                                <?php custom_theme_category_badge(); ?>
                            </div>
                        <?php endif; ?>

                        <<?php echo esc_attr( $title_tag ); ?> class="card-list-title">
                            <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
                                <?php the_title(); ?>
                            </a>
                        </<?php echo esc_attr( $title_tag ); ?>>

                        <?php if ( $show_excerpt ) : ?>
                            <div class="card-list-excerpt">
                                <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), $excerpt_length ) ); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php $this->render_meta( $settings ); ?>

                    </div><!-- .card-list-content -->

                </article><!-- #post-<?php the_ID(); ?> -->
                <?php
            endwhile;
            ?>
        </div><!-- .editorial-post-list-container -->
        <?php
        wp_reset_postdata();
    }
}
