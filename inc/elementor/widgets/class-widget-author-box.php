<?php
/**
 * Elementor Widget: Author Spotlight Card
 *
 * Displays a rich author profile card with circular avatar, role byline,
 * biography, publication post count, social links, and author archive CTA.
 *
 * @package Custom_Theme
 */

namespace CustomTheme\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Author_Box extends Widget_Base {

    /**
     * Get Widget Name
     *
     * @return string
     */
    public function get_name() {
        return 'custom_theme_author_box';
    }

    /**
     * Get Widget Title
     *
     * @return string
     */
    public function get_title() {
        return esc_html__( 'Author Spotlight Card', 'custom-theme' );
    }

    /**
     * Get Widget Icon
     *
     * @return string
     */
    public function get_icon() {
        return 'eicon-person';
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
        return array( 'author', 'bio', 'profile', 'writer', 'journalist', 'avatar', 'spotlight', 'about', 'social', 'user' );
    }

    /**
     * Helper: Fetch authors for dropdown
     *
     * @return array
     */
    private function get_post_authors() {
        $options = array();
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
        // CONTENT TAB: Author Identity & Source
        // =========================================================================
        $this->start_controls_section(
            'section_author_source',
            array(
                'label' => esc_html__( 'Author Identity & Source', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'author_source',
            array(
                'label'   => esc_html__( 'Author Source', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'current_author',
                'options' => array(
                    'current_author' => esc_html__( 'Current Post / Archive Author', 'custom-theme' ),
                    'select_user'    => esc_html__( 'Select Registered Author', 'custom-theme' ),
                    'custom'         => esc_html__( 'Custom Profile (Manual)', 'custom-theme' ),
                ),
            )
        );

        $this->add_control(
            'selected_user_id',
            array(
                'label'     => esc_html__( 'Select Author', 'custom-theme' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => '',
                'options'   => $this->get_post_authors(),
                'condition' => array(
                    'author_source' => 'select_user',
                ),
            )
        );

        // Custom Author Controls
        $this->add_control(
            'custom_name',
            array(
                'label'       => esc_html__( 'Author Name', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'Eleanor Vance', 'custom-theme' ),
                'dynamic'     => array( 'active' => true ),
                'condition'   => array(
                    'author_source' => 'custom',
                ),
            )
        );

        $this->add_control(
            'custom_role',
            array(
                'label'       => esc_html__( 'Role / Bylines Title', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'default'     => esc_html__( 'Senior Technology Editor', 'custom-theme' ),
                'dynamic'     => array( 'active' => true ),
            )
        );

        $this->add_control(
            'custom_bio',
            array(
                'label'       => esc_html__( 'Biography', 'custom-theme' ),
                'type'        => Controls_Manager::TEXTAREA,
                'rows'        => 3,
                'default'     => esc_html__( 'Writer and contributor exploring modern ideas, long-form journalism, editorial design, and artificial intelligence.', 'custom-theme' ),
                'dynamic'     => array( 'active' => true ),
                'condition'   => array(
                    'author_source' => 'custom',
                ),
            )
        );

        $this->add_control(
            'custom_avatar',
            array(
                'label'     => esc_html__( 'Custom Avatar Image', 'custom-theme' ),
                'type'      => Controls_Manager::MEDIA,
                'default'   => array(
                    'url' => Utils::get_placeholder_image_src(),
                ),
                'condition' => array(
                    'author_source' => 'custom',
                ),
            )
        );

        $this->add_control(
            'custom_link',
            array(
                'label'       => esc_html__( 'Author Archive / Website URL', 'custom-theme' ),
                'type'        => Controls_Manager::URL,
                'placeholder' => 'https://your-site.com/author/profile',
                'dynamic'     => array( 'active' => true ),
                'condition'   => array(
                    'author_source' => 'custom',
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

        // Eyebrow Label
        $this->add_control(
            'show_label',
            array(
                'label'        => esc_html__( 'Show Eyebrow Label', 'custom-theme' ),
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
                'default'   => esc_html__( 'Written by', 'custom-theme' ),
                'condition' => array(
                    'show_label' => 'yes',
                ),
            )
        );

        // Avatar
        $this->add_control(
            'show_avatar',
            array(
                'label'        => esc_html__( 'Author Avatar', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
                'separator'    => 'before',
            )
        );

        // Role / Title
        $this->add_control(
            'show_role',
            array(
                'label'        => esc_html__( 'Author Role / Tagline', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        // Post Count
        $this->add_control(
            'show_post_count',
            array(
                'label'        => esc_html__( 'Articles Published Count', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
                'condition'    => array(
                    'author_source!' => 'custom',
                ),
            )
        );

        // Biography
        $this->add_control(
            'show_bio',
            array(
                'label'        => esc_html__( 'Biography', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        // Archive Link
        $this->add_control(
            'show_more_link',
            array(
                'label'        => esc_html__( '"View All Articles" Link', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
                'separator'    => 'before',
            )
        );

        $this->add_control(
            'more_link_text',
            array(
                'label'     => esc_html__( 'Link Text', 'custom-theme' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => esc_html__( 'View all articles by this author', 'custom-theme' ),
                'condition' => array(
                    'show_more_link' => 'yes',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // CONTENT TAB: Social Links
        // =========================================================================
        $this->start_controls_section(
            'section_social_links',
            array(
                'label' => esc_html__( 'Social Media Links', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_social',
            array(
                'label'        => esc_html__( 'Show Social Icons', 'custom-theme' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => esc_html__( 'Show', 'custom-theme' ),
                'label_off'    => esc_html__( 'Hide', 'custom-theme' ),
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'social_twitter',
            array(
                'label'       => esc_html__( 'X / Twitter URL', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'https://x.com/username',
                'condition'   => array(
                    'show_social' => 'yes',
                ),
            )
        );

        $this->add_control(
            'social_linkedin',
            array(
                'label'       => esc_html__( 'LinkedIn URL', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'https://linkedin.com/in/username',
                'condition'   => array(
                    'show_social' => 'yes',
                ),
            )
        );

        $this->add_control(
            'social_facebook',
            array(
                'label'       => esc_html__( 'Facebook URL', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'https://facebook.com/username',
                'condition'   => array(
                    'show_social' => 'yes',
                ),
            )
        );

        $this->add_control(
            'social_website',
            array(
                'label'       => esc_html__( 'Website / Portfolio URL', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'https://author-portfolio.com',
                'condition'   => array(
                    'show_social' => 'yes',
                ),
            )
        );

        $this->add_control(
            'social_mail',
            array(
                'label'       => esc_html__( 'Contact Email (mailto:)', 'custom-theme' ),
                'type'        => Controls_Manager::TEXT,
                'placeholder' => 'author@publication.com',
                'condition'   => array(
                    'show_social' => 'yes',
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
                'default' => 'horizontal',
                'options' => array(
                    'horizontal' => esc_html__( 'Horizontal (Avatar Beside Content)', 'custom-theme' ),
                    'stacked'    => esc_html__( 'Stacked (Centered Avatar on Top)', 'custom-theme' ),
                ),
            )
        );

        $this->add_control(
            'box_preset',
            array(
                'label'   => esc_html__( 'Preset Theme', 'custom-theme' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'surface_card',
                'options' => array(
                    'surface_card'  => esc_html__( 'Surface Card (Bordered)', 'custom-theme' ),
                    'elevated_card' => esc_html__( 'Elevated Floating Shadow', 'custom-theme' ),
                    'borderless'    => esc_html__( 'Borderless Minimal Profile', 'custom-theme' ),
                    'divided'       => esc_html__( 'Divided (Top & Bottom Separators)', 'custom-theme' ),
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
                        'min' => 300,
                        'max' => 1000,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .editorial-author-spotlight' => 'max-width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Card Container
        // =========================================================================
        $this->start_controls_section(
            'section_style_card',
            array(
                'label' => esc_html__( 'Spotlight Card Container', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'card_bg_color',
            array(
                'label'     => esc_html__( 'Background Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .author-box' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            array(
                'name'     => 'card_border',
                'selector' => '{{WRAPPER}} .author-box',
            )
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            array(
                'name'     => 'card_shadow',
                'selector' => '{{WRAPPER}} .author-box',
            )
        );

        $this->add_responsive_control(
            'card_padding',
            array(
                'label'      => esc_html__( 'Inner Padding', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', 'em', 'rem' ),
                'selectors'  => array(
                    '{{WRAPPER}} .author-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'card_border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'custom-theme' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => array( 'px', '%' ),
                'selectors'  => array(
                    '{{WRAPPER}} .author-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Avatar
        // =========================================================================
        $this->start_controls_section(
            'section_style_avatar',
            array(
                'label'     => esc_html__( 'Avatar', 'custom-theme' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'show_avatar' => 'yes',
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
                        'min' => 48,
                        'max' => 140,
                    ),
                ),
                'default'    => array(
                    'size' => 80,
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .author-box-avatar img'                      => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
                    '{{WRAPPER}} .author-box.layout-horizontal:not(.no-avatar)' => 'grid-template-columns: {{SIZE}}{{UNIT}} 1fr;',
                ),
            )
        );

        $this->add_responsive_control(
            'avatar_border_radius',
            array(
                'label'      => esc_html__( 'Border Radius', 'custom-theme' ),
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
                    '{{WRAPPER}} .author-box-avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important; object-fit: cover !important;',
                ),
            )
        );

        $this->add_control(
            'avatar_border_color',
            array(
                'label'     => esc_html__( 'Border Ring Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .author-box-avatar img' => 'border: 2px solid {{VALUE}} !important;',
                ),
            )
        );

        $this->end_controls_section();

        // =========================================================================
        // STYLE TAB: Typography & Colors
        // =========================================================================
        $this->start_controls_section(
            'section_style_typography',
            array(
                'label' => esc_html__( 'Typography & Details', 'custom-theme' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            )
        );

        // Label
        $this->add_control(
            'heading_label_style',
            array(
                'label'     => esc_html__( 'Eyebrow Label', 'custom-theme' ),
                'type'      => Controls_Manager::HEADING,
                'condition' => array(
                    'show_label' => 'yes',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'      => 'label_typography',
                'selector'  => '{{WRAPPER}} .author-box-label',
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
                    '{{WRAPPER}} .author-box-label' => 'color: {{VALUE}};',
                ),
            )
        );

        // Name
        $this->add_control(
            'heading_name_style',
            array(
                'label'     => esc_html__( 'Author Name', 'custom-theme' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'     => 'name_typography',
                'selector' => '{{WRAPPER}} .author-box-name',
            )
        );

        $this->add_control(
            'name_color',
            array(
                'label'     => esc_html__( 'Name Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .author-box-name, {{WRAPPER}} .author-box-name a' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'name_hover_color',
            array(
                'label'     => esc_html__( 'Name Hover Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .author-box-name a:hover' => 'color: {{VALUE}} !important;',
                ),
            )
        );

        // Role & Post Count
        $this->add_control(
            'heading_role_style',
            array(
                'label'     => esc_html__( 'Role & Post Count', 'custom-theme' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            )
        );

        $this->add_control(
            'role_color',
            array(
                'label'     => esc_html__( 'Role Text Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .author-box-role' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'post_count_color',
            array(
                'label'     => esc_html__( 'Post Count Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .author-box-count' => 'color: {{VALUE}};',
                ),
            )
        );

        // Bio
        $this->add_control(
            'heading_bio_style',
            array(
                'label'     => esc_html__( 'Biography', 'custom-theme' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => array(
                    'show_bio' => 'yes',
                ),
            )
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            array(
                'name'      => 'bio_typography',
                'selector'  => '{{WRAPPER}} .author-box-bio',
                'condition' => array(
                    'show_bio' => 'yes',
                ),
            )
        );

        $this->add_control(
            'bio_color',
            array(
                'label'     => esc_html__( 'Bio Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'show_bio' => 'yes',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .author-box-bio' => 'color: {{VALUE}};',
                ),
            )
        );

        // Social Icons Style
        $this->add_control(
            'heading_social_style',
            array(
                'label'     => esc_html__( 'Social Icons', 'custom-theme' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => array(
                    'show_social' => 'yes',
                ),
            )
        );

        $this->add_control(
            'social_icon_color',
            array(
                'label'     => esc_html__( 'Icon Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'show_social' => 'yes',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .author-social-link' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'social_icon_hover_color',
            array(
                'label'     => esc_html__( 'Icon Hover Color', 'custom-theme' ),
                'type'      => Controls_Manager::COLOR,
                'condition' => array(
                    'show_social' => 'yes',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .author-social-link:hover' => 'color: {{VALUE}} !important;',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Helper: Resolve author details
     *
     * @param array $settings
     * @return array
     */
    private function get_author_data( $settings ) {
        $source = ! empty( $settings['author_source'] ) ? $settings['author_source'] : 'current_author';

        if ( 'custom' === $source ) {
            return array(
                'name'       => ! empty( $settings['custom_name'] ) ? $settings['custom_name'] : esc_html__( 'Author', 'custom-theme' ),
                'role'       => ! empty( $settings['custom_role'] ) ? $settings['custom_role'] : '',
                'bio'        => ! empty( $settings['custom_bio'] ) ? $settings['custom_bio'] : '',
                'avatar_url' => ! empty( $settings['custom_avatar']['url'] ) ? $settings['custom_avatar']['url'] : '',
                'link'       => ! empty( $settings['custom_link']['url'] ) ? $settings['custom_link']['url'] : '',
                'post_count' => 0,
            );
        }

        $user_id = 0;
        if ( 'select_user' === $source && ! empty( $settings['selected_user_id'] ) ) {
            $user_id = absint( $settings['selected_user_id'] );
        } else {
            $user_id = get_the_author_meta( 'ID' );
            if ( ! $user_id ) {
                $user_id = get_current_user_id();
            }
        }

        $user = get_userdata( $user_id );
        if ( ! $user ) {
            return array(
                'name'       => esc_html__( 'Editorial Team', 'custom-theme' ),
                'role'       => esc_html__( 'Contributor', 'custom-theme' ),
                'bio'        => esc_html__( 'Writer and contributor exploring modern ideas and journalism.', 'custom-theme' ),
                'avatar_url' => '',
                'link'       => '#',
                'post_count' => 0,
            );
        }

        $bio = get_the_author_meta( 'description', $user_id );
        if ( empty( $bio ) ) {
            $bio = esc_html__( 'Writer and contributor exploring modern ideas, long-form stories, and technology.', 'custom-theme' );
        }

        return array(
            'id'         => $user_id,
            'name'       => $user->display_name,
            'role'       => ! empty( $settings['custom_role'] ) ? $settings['custom_role'] : '',
            'bio'        => $bio,
            'avatar_url' => get_avatar_url( $user_id, array( 'size' => 160 ) ),
            'link'       => get_author_posts_url( $user_id ),
            'post_count' => count_user_posts( $user_id, 'post' ),
        );
    }

    /**
     * Render Widget Output
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $author   = $this->get_author_data( $settings );

        $show_label      = 'yes' === $settings['show_label'] && ! empty( $settings['label_text'] );
        $show_avatar     = 'yes' === $settings['show_avatar'];
        $show_role       = 'yes' === $settings['show_role'] && ! empty( $author['role'] );
        $show_post_count = 'yes' === $settings['show_post_count'] && ! empty( $author['post_count'] );
        $show_bio        = 'yes' === $settings['show_bio'] && ! empty( $author['bio'] );
        $show_more       = 'yes' === $settings['show_more_link'] && ! empty( $author['link'] );
        $show_social     = 'yes' === $settings['show_social'];
        $box_layout      = ! empty( $settings['box_layout'] ) ? $settings['box_layout'] : 'horizontal';
        $box_preset      = ! empty( $settings['box_preset'] ) ? $settings['box_preset'] : 'surface_card';

        $box_classes = array(
            'author-box',
            'editorial-author-spotlight',
            'layout-' . esc_attr( $box_layout ),
            'preset-' . esc_attr( $box_preset ),
        );

        if ( ! $show_avatar ) {
            $box_classes[] = 'no-avatar';
        }
        ?>
        <div class="<?php echo esc_attr( implode( ' ', $box_classes ) ); ?>" itemprop="author" itemscope itemtype="https://schema.org/Person">

            <?php if ( $show_avatar && ! empty( $author['avatar_url'] ) ) : ?>
                <div class="author-box-avatar">
                    <?php if ( ! empty( $author['link'] ) ) : ?>
                        <a href="<?php echo esc_url( $author['link'] ); ?>" rel="author" tabindex="-1" aria-hidden="true">
                            <img src="<?php echo esc_url( $author['avatar_url'] ); ?>" alt="<?php echo esc_attr( $author['name'] ); ?>" loading="lazy" class="avatar-circle">
                        </a>
                    <?php else : ?>
                        <img src="<?php echo esc_url( $author['avatar_url'] ); ?>" alt="<?php echo esc_attr( $author['name'] ); ?>" loading="lazy" class="avatar-circle">
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="author-box-content">

                <?php if ( $show_label ) : ?>
                    <span class="author-box-label"><?php echo esc_html( $settings['label_text'] ); ?></span>
                <?php endif; ?>

                <h3 class="author-box-name" itemprop="name">
                    <?php if ( ! empty( $author['link'] ) ) : ?>
                        <a href="<?php echo esc_url( $author['link'] ); ?>" itemprop="url" rel="author">
                            <?php echo esc_html( $author['name'] ); ?>
                        </a>
                    <?php else : ?>
                        <?php echo esc_html( $author['name'] ); ?>
                    <?php endif; ?>
                </h3>

                <?php if ( $show_role || $show_post_count ) : ?>
                    <div class="author-box-meta-line" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem; font-size: var(--text-xs);">
                        <?php if ( $show_role ) : ?>
                            <span class="author-box-role" style="font-weight: 600; color: var(--color-accent);"><?php echo esc_html( $author['role'] ); ?></span>
                        <?php endif; ?>

                        <?php if ( $show_role && $show_post_count ) : ?>
                            <span class="meta-divider">&bull;</span>
                        <?php endif; ?>

                        <?php if ( $show_post_count ) : ?>
                            <span class="author-box-count" style="color: var(--color-text-muted);">
                                <?php
                                /* translators: %d: post count */
                                printf( esc_html__( '%d articles published', 'custom-theme' ), absint( $author['post_count'] ) );
                                ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ( $show_bio ) : ?>
                    <p class="author-box-bio" itemprop="description">
                        <?php echo esc_html( $author['bio'] ); ?>
                    </p>
                <?php endif; ?>

                <?php if ( $show_social ) : ?>
                    <div class="author-box-social-row" style="display: flex; align-items: center; gap: 0.65rem; margin-bottom: 0.65rem;">
                        <?php if ( ! empty( $settings['social_twitter'] ) && function_exists( 'custom_theme_svg_icon' ) ) : ?>
                            <a href="<?php echo esc_url( $settings['social_twitter'] ); ?>" target="_blank" rel="noopener noreferrer" class="author-social-link" title="X / Twitter" aria-label="X / Twitter">
                                <?php echo custom_theme_svg_icon( 'x-twitter' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </a>
                        <?php endif; ?>

                        <?php if ( ! empty( $settings['social_linkedin'] ) && function_exists( 'custom_theme_svg_icon' ) ) : ?>
                            <a href="<?php echo esc_url( $settings['social_linkedin'] ); ?>" target="_blank" rel="noopener noreferrer" class="author-social-link" title="LinkedIn" aria-label="LinkedIn">
                                <?php echo custom_theme_svg_icon( 'linkedin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </a>
                        <?php endif; ?>

                        <?php if ( ! empty( $settings['social_facebook'] ) && function_exists( 'custom_theme_svg_icon' ) ) : ?>
                            <a href="<?php echo esc_url( $settings['social_facebook'] ); ?>" target="_blank" rel="noopener noreferrer" class="author-social-link" title="Facebook" aria-label="Facebook">
                                <?php echo custom_theme_svg_icon( 'facebook' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </a>
                        <?php endif; ?>

                        <?php if ( ! empty( $settings['social_website'] ) && function_exists( 'custom_theme_svg_icon' ) ) : ?>
                            <a href="<?php echo esc_url( $settings['social_website'] ); ?>" target="_blank" rel="noopener noreferrer" class="author-social-link" title="Website" aria-label="Website">
                                <?php echo custom_theme_svg_icon( 'link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </a>
                        <?php endif; ?>

                        <?php if ( ! empty( $settings['social_mail'] ) && function_exists( 'custom_theme_svg_icon' ) ) : ?>
                            <a href="mailto:<?php echo esc_attr( $settings['social_mail'] ); ?>" class="author-social-link" title="Email" aria-label="Email">
                                <?php echo custom_theme_svg_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ( $show_more ) : ?>
                    <div class="author-box-links">
                        <a href="<?php echo esc_url( $author['link'] ); ?>" class="author-box-more-link">
                            <span><?php echo esc_html( ! empty( $settings['more_link_text'] ) ? $settings['more_link_text'] : __( 'View all articles by this author', 'custom-theme' ) ); ?></span>
                            <?php
                            if ( function_exists( 'custom_theme_svg_icon' ) ) {
                                echo custom_theme_svg_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            }
                            ?>
                        </a>
                    </div>
                <?php endif; ?>

            </div><!-- .author-box-content -->

        </div><!-- .editorial-author-spotlight -->
        <?php
    }
}
