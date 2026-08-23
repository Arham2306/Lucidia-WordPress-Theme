<?php
/**
 * Elementor Widget: Magazine Compact Spotlight
 *
 * Displays a high-density compact editorial spotlight list with numbered ranking badges,
 * thumbnail size controls, and sleek list presets.
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

class Compact_Spotlight extends Widget_Base {

    /**
     * Get Widget Name
     *
     * @return string
     */
    public function get_name() {
        return 'custom_theme_compact_spotlight';
    }

    /**
     * Get Widget Title
     *
     * @return string
     */
    public function get_title() {
        return esc_html__( 'Magazine Compact Spotlight', 'custom-theme' );
    }

    /**
     * Get Widget Icon
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-bullet-list';
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
        return array( 'compact', 'trending', 'popular', 'ranking', 'spotlight', 'numbers', 'sidebar', 'top', 'list' );
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
                'default' => 5,
                'min'     => 1,
                'max'     => 20,
            )
        );

        $this->add_control(
            'query_source',
            array(
                'label'   => esc_html__( 'Source', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'popular',
                'options' => array(
                    'popular'  => esc_html__( 'Popular / Trending (Comments)', 'custom-theme' ),
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
                'default'   => 'comment_count',
                'options'   => array(
                    'comment_count' => esc_html__( 'Comment Count (Popularity)', 'custom-theme' ),
                    'date'          => esc_html__( 'Date Published', 'custom-theme' ),
                    'modified'      => esc_html__( 'Last Modified', 'custom-theme' ),
                    'title'         => esc_html__( 'Post Title', 'custom-theme' ),
                    'rand'          => esc_html__( 'Random', 'custom-theme' ),
                    'ID'            => esc_html__( 'Post ID', 'custom-theme' ),
                ),
                'condition' => array(
                    'query_source!' => array( 'manual', 'popular' ),
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
                    'DESC' => esc_html__( 'Descending (Newest / Most Popular first)', 'custom-theme' ),
                    'ASC'  => esc_html__( 'Ascending', 'custom-theme' ),
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
            'section_layout',
            array(
                'label' => esc_html__( 'Layout & Structure', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_responsive_control(
            'layout_columns',
            array(
                'label'          => esc_html__( 'Columns', 'custom-theme' ),
                'type'           => Controls_Manager::SELECT,
                'default'        => '1',
                'tablet_default' => '1',
                'mobile_default' => '1',
                'options'        => array(
                    '1' => '1 Column (Sidebar / Vertical Stack)',
                    '2' => '2 Columns',
                ),
                'selectors'      => array(
                    '{{WRAPPER}} .compact-spotlight-container' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ),
            )
        );

        $this->add_control(
            'preset_style',
            array(
                'label'   => esc_html__( 'Preset Style', 'custom-theme' ),
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
                'label'   => esc_html__( 'Thumbnail Position', 'custom-theme' ),
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
                        'min' => 60,
                        'max' => 200,
                    ),
                ),
                'default'    => array(
                    'unit' => 'px',
                    'size' => 110,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .article-card-compact:not(.img-on-right):not(.no-media)' => 'grid-template-columns: auto {{SIZE}}{{UNIT}} 1fr;',
                    '{{WRAPPER}} .article-card-compact.img-on-right:not(.no-media)'        => 'grid-template-columns: auto 1fr {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .article-card-compact.no-rank:not(.img-on-right):not(.no-media)' => 'grid-template-columns: {{SIZE}}{{UNIT}} 1fr;',
                    '{{WRAPPER}} .article-card-compact.no-rank.img-on-right:not(.no-media)'        => 'grid-template-columns: 1fr {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'image_aspect_ratio',
            array(
                'label'     => esc_html__( 'Thumbnail Aspect Ratio', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => '1/1',
                'options'   => array(
                    '1/1'   => '1 : 1 (Square)',
                    '3/2'   => '3 : 2 (Photo)',
                    '16/10' => '16 : 10 (Editorial)',
                    '4/3'   => '4 : 3 (Classic)',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .card-compact-media' => 'aspect-ratio: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'item_gap',
            array(
                'label'      => esc_html__( 'Gap Between Items (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 50,
                    ),
                ),
                'default'    => array(
                    'unit' => 'px',
                    'size' => 14,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .compact-spotlight-container' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // CONTENT TAB: Ranking Badges
        // =========================================================================
        $this->start_controls_section(
            'section_ranking_badges',
            array(
                'label' => esc_html__( 'Numbered Ranking Badges', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_ranking_badge',
            array(
                'label'        => esc_html__( 'Show Ranking Number', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'badge_style',
            array(
                'label'     => esc_html__( 'Badge Style', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'number_bold',
                'options'   => array(
                    'number_bold'    => esc_html__( 'Bold Editorial Number', 'custom-theme' ),
                    'pill_solid'     => esc_html__( 'Solid Filled Pill / Circle', 'custom-theme' ),
                    'pill_outline'   => esc_html__( 'Outlined Pill / Circle', 'custom-theme' ),
                    'overlay_media'  => esc_html__( 'Corner Overlay on Thumbnail', 'custom-theme' ),
                ),
                'condition' => array(
                    'show_ranking_badge' => 'yes',
                ),
            )
        );

        $this->add_control(
            'number_format',
            array(
                'label'     => esc_html__( 'Number Format', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'leading_zero',
                'options'   => array(
                    'leading_zero' => '01, 02, 03...',
                    'plain'        => '1, 2, 3...',
                ),
                'condition' => array(
                    'show_ranking_badge' => 'yes',
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

        // Media
        $this->add_control(
            'show_thumbnail',
            array(
                'label'        => esc_html__( 'Thumbnail Image', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
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

        // Title
        $this->add_control(
            'title_tag',
            array(
                'label'   => esc_html__( 'Title HTML Tag', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'h4',
                'options' => array(
                    'h3'   => 'H3',
                    'h4'   => 'H4',
                    'h5'   => 'H5',
                    'h6'   => 'H6',
                    'div'  => 'div',
                ),
            )
        );

        $this->add_control(
            'title_lines',
            array(
                'label'     => esc_html__( 'Title Max Lines (Clamp)', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => '2',
                'options'   => array(
                    'none' => esc_html__( 'No limit', 'custom-theme' ),
                    '1'    => '1 Line',
                    '2'    => '2 Lines',
                    '3'    => '3 Lines',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .card-compact-title' => '-webkit-line-clamp: {{VALUE}}; display: -webkit-box; -webkit-box-orient: vertical; overflow: hidden;',
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
                'default'      => 'no',
            )
        );

        $this->add_control(
            'excerpt_length',
            array(
                'label'     => esc_html__( 'Excerpt Word Limit', 'custom-theme' ),
                'type'      => Controls_Manager::NUMBER,
                'default'   => 12,
                'min'       => 4,
                'max'       => 40,
                'condition' => array(
                    'show_excerpt' => 'yes',
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
            'show_author',
            array(
                'label'        => esc_html__( 'Author Name', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'no',
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
                'default'      => 'no',
                'condition'    => array(
                    'show_author' => 'yes',
                ),
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
                'label' => esc_html__( 'Item Card Container', 'custom-theme' ),
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
                    '{{WRAPPER}} .article-card-compact' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'item_box_shadow',
                'selector' => '{{WRAPPER}} .article-card-compact',
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'item_border',
                'selector' => '{{WRAPPER}} .article-card-compact',
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
                    '{{WRAPPER}} .article-card-compact:hover' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'item_hover_box_shadow',
                'selector' => '{{WRAPPER}} .article-card-compact:hover',
            )
        );

        $this->add_control(
            'item_hover_border_color',
            array(
                'label'     => esc_html__( 'Border Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .article-card-compact:hover' => 'border-color: {{VALUE}};',
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
                        'min'  => -10,
                        'max'  => 0,
                        'step' => 1,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .article-card-compact:hover' => 'transform: translateY({{SIZE}}{{UNIT}});',
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
                    '{{WRAPPER}} .article-card-compact' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .article-card-compact' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Ranking Badges
        // =========================================================================
        $this->start_controls_section(
            'section_style_ranking',
            array(
                'label'     => esc_html__( 'Ranking Badge Style', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'show_ranking_badge' => 'yes',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'ranking_typography',
                'selector' => '{{WRAPPER}} .spotlight-rank-badge',
            )
        );

        $this->add_control(
            'ranking_text_color',
            array(
                'label'     => esc_html__( 'Number Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .spotlight-rank-badge' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'ranking_bg_color',
            array(
                'label'     => esc_html__( 'Background Color (Pill Styles)', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .spotlight-rank-badge.style-pill_solid' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .spotlight-rank-badge.style-overlay_media' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'ranking_border_color',
            array(
                'label'     => esc_html__( 'Border Color (Outlined Styles)', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .spotlight-rank-badge.style-pill_outline' => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'ranking_badge_size',
            array(
                'label'      => esc_html__( 'Badge Width / Height (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 20,
                        'max' => 60,
                    ),
                ),
                'default'    => array(
                    'size' => 32,
                ),
                'condition'  => array(
                    'badge_style!' => 'number_bold',
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .spotlight-rank-badge:not(.style-number_bold)' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'ranking_margin',
            array(
                'label'      => esc_html__( 'Spacing (px)', 'custom-theme' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array( 'px' ),
                'range'      => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 30,
                    ),
                ),
                'default'    => array(
                    'size' => 12,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .spotlight-rank-badge' => 'margin-right: {{SIZE}}{{UNIT}};',
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
                    'size' => 1.06,
                ),
                'condition' => array(
                    'enable_hover_zoom' => 'yes',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .article-card-compact:hover .card-compact-media img' => 'transform: scale({{SIZE}});',
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
                    '{{WRAPPER}} .card-compact-media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'selector' => '{{WRAPPER}} .card-compact-title',
            )
        );

        $this->add_control(
            'title_color',
            array(
                'label'     => esc_html__( 'Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .card-compact-title a' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'title_hover_color',
            array(
                'label'     => esc_html__( 'Hover Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .card-compact-title a:hover' => 'color: {{VALUE}} !important;',
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
                        'max' => 25,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .card-compact-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
                'selector'  => '{{WRAPPER}} .card-compact-excerpt',
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
                    '{{WRAPPER}} .card-compact-excerpt' => 'color: {{VALUE}};',
                ),
                'condition' => array(
                    'show_excerpt' => 'yes',
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
                'selector' => '{{WRAPPER}} .card-compact-meta .compact-meta-left, {{WRAPPER}} .card-compact-meta .meta-item',
            )
        );

        $this->add_control(
            'meta_color',
            array(
                'label'     => esc_html__( 'Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .card-compact-meta .compact-meta-left' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .card-compact-meta .meta-item'         => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'meta_link_color',
            array(
                'label'     => esc_html__( 'Link Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .card-compact-meta a' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'meta_link_hover_color',
            array(
                'label'     => esc_html__( 'Link Hover Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .card-compact-meta a:hover' => 'color: {{VALUE}} !important;',
                ),
            )
        );

        $this->add_control(
            'divider_color',
            array(
                'label'     => esc_html__( 'Divider Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .card-compact-meta .meta-divider' => 'color: {{VALUE}}; opacity: 1;',
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

        $posts_per_page = ! empty( $settings['posts_per_page'] ) ? absint( $settings['posts_per_page'] ) : 5;

        $query_args = array(
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => $posts_per_page,
            'ignore_sticky_posts' => ( 'yes' === $settings['exclude_sticky'] ) ? 1 : 0,
            'no_found_rows'       => true,
        );

        $source = ! empty( $settings['query_source'] ) ? $settings['query_source'] : 'popular';

        switch ( $source ) {
            case 'popular':
                $query_args['orderby'] = 'comment_count';
                $query_args['order']   = 'DESC';
                break;

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

        // Orderby & Order override (if not popular or manual)
        if ( 'popular' !== $source && 'manual' !== $source ) {
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
        <div class="card-compact-meta entry-meta">
            <div class="compact-meta-left">
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
                echo '<div class="elementor-alert elementor-alert-info">' . esc_html__( 'No posts found matching the Compact Spotlight criteria.', 'custom-theme' ) . '</div>';
            }
            return;
        }

        $preset_style   = ! empty( $settings['preset_style'] ) ? $settings['preset_style'] : 'standard';
        $image_pos      = ! empty( $settings['image_position'] ) ? $settings['image_position'] : 'left';
        $show_rank      = 'yes' === $settings['show_ranking_badge'];
        $badge_style    = ! empty( $settings['badge_style'] ) ? $settings['badge_style'] : 'number_bold';
        $number_format  = ! empty( $settings['number_format'] ) ? $settings['number_format'] : 'leading_zero';
        $show_thumb     = 'yes' === $settings['show_thumbnail'];
        $show_badge     = 'yes' === $settings['show_badge'];
        $title_tag      = ! empty( $settings['title_tag'] ) ? $settings['title_tag'] : 'h4';
        $show_excerpt   = 'yes' === $settings['show_excerpt'];
        $excerpt_length = ! empty( $settings['excerpt_length'] ) ? absint( $settings['excerpt_length'] ) : 12;

        $container_classes = array(
            'compact-spotlight-container',
            'elementor-compact-spotlight',
            'style-' . esc_attr( $preset_style ),
        );

        $rank = 0;
        ?>
        <div class="<?php echo esc_attr( implode( ' ', $container_classes ) ); ?>">
            <?php
            while ( $query->have_posts() ) :
                $query->the_post();
                $rank++;

                $has_media = $show_thumb && has_post_thumbnail();

                $article_classes = array(
                    'article-card-compact',
                    'preset-' . esc_attr( $preset_style ),
                );

                if ( ! $has_media ) {
                    $article_classes[] = 'no-media';
                } elseif ( 'right' === $image_pos ) {
                    $article_classes[] = 'img-on-right';
                }

                if ( ! $show_rank ) {
                    $article_classes[] = 'no-rank';
                }

                $formatted_rank = ( 'leading_zero' === $number_format ) ? sprintf( '%02d', $rank ) : (string) $rank;
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( implode( ' ', $article_classes ) ); ?>>

                    <?php if ( $show_rank && 'overlay_media' !== $badge_style ) : ?>
                        <div class="spotlight-rank-badge style-<?php echo esc_attr( $badge_style ); ?>">
                            <?php echo esc_html( $formatted_rank ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $has_media ) : ?>
                        <div class="card-compact-media">
                            <?php
                            if ( function_exists( 'custom_theme_post_thumbnail' ) ) {
                                custom_theme_post_thumbnail( 'custom-theme-avatar', true );
                            } else {
                                the_post_thumbnail( 'thumbnail' );
                            }
                            ?>
                            <?php if ( $show_rank && 'overlay_media' === $badge_style ) : ?>
                                <div class="spotlight-rank-badge style-overlay_media">
                                    <?php echo esc_html( $formatted_rank ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="card-compact-content">

                        <?php if ( $show_badge && function_exists( 'custom_theme_category_badge' ) ) : ?>
                            <div class="card-compact-category">
                                <?php custom_theme_category_badge(); ?>
                            </div>
                        <?php endif; ?>

                        <<?php echo esc_attr( $title_tag ); ?> class="card-compact-title">
                            <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
                                <?php the_title(); ?>
                            </a>
                        </<?php echo esc_attr( $title_tag ); ?>>

                        <?php if ( $show_excerpt ) : ?>
                            <div class="card-compact-excerpt">
                                <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), $excerpt_length ) ); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php $this->render_meta( $settings ); ?>

                    </div><!-- .card-compact-content -->

                </article><!-- #post-<?php the_ID(); ?> -->
                <?php
            endwhile;
            ?>
        </div><!-- .compact-spotlight-container -->
        <?php
        wp_reset_postdata();
    }
}
