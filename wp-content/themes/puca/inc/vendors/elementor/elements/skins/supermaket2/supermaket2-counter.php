<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Supermaket2_Counter') ) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;

/**
 * Elementor tabs widget.
 *
 * Elementor widget that displays vertical or horizontal tabs with different
 * pieces of content.
 *
 * @since 1.0.0
 */
class Puca_Elementor_Supermaket2_Counter extends  Puca_Elementor_Widget_Base {
    /**
     * Get widget name.
     *
     * Retrieve tabs widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'tbay-supermaket2-counter';
    }

    /**
     * Get widget title.
     *
     * Retrieve tabs widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return esc_html__( 'Tbay Supermaket2 Counter', 'puca' );
    }

    public function get_script_depends()
    {
        return [ 'jquery-counter' ];
    }
    public function get_categories() {
        return [ 'puca-elements'];
    }
    
    /**
     * Get widget icon.
     *
     * Retrieve tabs widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-counter';
    }

    public function get_keywords() {
        return [ 'counter', 'supermaket2' ];
    }
    
    protected function _register_controls() {

        $this->start_controls_section(
            'section_counter',
            [
                'label' => esc_html__( 'Tbay Counter', 'puca' ),
            ]
        );

        $this->add_control(
            'number',
            [
                'label' => esc_html__( 'Number', 'puca' ),
                'type' => Controls_Manager::NUMBER,
                'default' => 999,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__( 'Title', 'puca' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__( 'Cool Number', 'puca' ),
                'placeholder' => esc_html__( 'Cool Number', 'puca' ),
            ]
        );

        $this->add_control(
            'selected_icon',
            [
                'label' => esc_html('Choose Icon','puca'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'linear-icon-gift',
                    'library' => 'linear-icon',
                ],
            ]
        ); 

        $this->add_control(
            'tag',
            [
                'label' => esc_html__( 'Tag name', 'puca' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => esc_html__( '#facebook', 'puca' ),
            ]
        );

        $this->add_control (
            'tag_link', 
            [
                'label' => esc_html__('External link','puca'),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                        'url' => 'https://www.facebook.com/thembayteam',
                        'is_external' => true,
                        'nofollow' => true,
                    ],
                'placeholder' => esc_html__( 'https://your-link.com', 'puca' ),
            ]
        );
        
        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__( 'Content', 'puca' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->register_style();
        $this->end_controls_section();
    }
    protected function register_style() {
        $this->add_control(
            'counter_bg',
            [
                'label' => esc_html__( 'Background', 'puca' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#3b5998',
                'selectors' => [
                    '{{WRAPPER}} .counters' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'color',
            [
                'label' => esc_html__( 'Text color', 'puca' ),
                'type' => Controls_Manager::COLOR,
                'default' => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .counters, {{WRAPPER}} .counters a' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .counters .counter:after, {{WRAPPER}} .counters .counter:before' => 'background: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'tag_color',
            [
                'label' => esc_html__( 'Tag color', 'puca' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .counters a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'tag_color_hover',
            [
                'label' => esc_html__( 'Tag color hover', 'puca' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .counters a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

    }
    public function render_content() {
        $settings = $this->get_settings_for_display();
        extract($settings);
        
        ?>
        <div class="counters">
            <div class="counter-wrap">
                <?php if(isset($selected_icon['value']) && !empty($selected_icon['value'])): ?>
                    <div class="icon-inner"><?php $this->render_item_icon($selected_icon) ?></div>
                <?php endif; ?>
                <?php if ( $title ) : ?>
                    <h5 class="title"><?php echo trim($title); ?></h5>
                <?php endif; ?>
 
                <span class="timer counter counterUp count-number" data-to="<?php echo esc_attr($number); ?>" data-speed="4000"></span>
                           
                <?php if(isset($tag) && !empty($tag)): ?>
                    <p>
                        <?php if(isset($tag_link['url']) && !empty($tag_link['url'])): ?>
                            <a href="<?php echo esc_url($tag_link['url'])?>">
                        <?php endif; ?>  
                            <?php echo trim($tag); ?>
                        <?php if(isset($tag_link['url']) && !empty($tag_link['url'])): ?>
                            </a>
                        <?php endif; ?> 
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
    protected function render_item_icon($selected_icon) {
        $settings = $this->get_settings_for_display();
        if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
            // add old default
            $settings['icon'] = 'fa fa-star';
        }
        $has_icon = ! empty( $settings['icon'] );

        if ( $has_icon ) {
            $this->add_render_attribute( 'i', 'class', $settings['icon'] );
            $this->add_render_attribute( 'i', 'aria-hidden', 'true' );
        }
        
        if ( ! $has_icon && ! empty( $selected_icon['value'] ) ) {
            $has_icon = true;
        }
        $migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
        $is_new = ! isset( $settings['icon'] ) && Icons_Manager::is_migration_allowed();
        
        Icons_Manager::enqueue_shim();

        if( !$has_icon ) return;  
        
        if ( $is_new || $migrated ) {
            Icons_Manager::render_icon( $selected_icon, [ 'aria-hidden' => 'true' ] );
        } elseif ( ! empty( $settings['icon'] ) ) {
            ?><i <?php echo trim($this->get_render_attribute_string( 'i' )); ?>></i><?php
        }
    }
    public function on_import( $element ) {
        return Icons_Manager::on_import_migration( $element, 'icon', 'selected_icon', true );
    }
    
}
$widgets_manager->register_widget_type(new Puca_Elementor_Supermaket2_Counter());
