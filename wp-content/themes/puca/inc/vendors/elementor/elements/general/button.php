<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Button') ) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;

/**
 * Elementor tabs widget.
 *
 * Elementor widget that displays vertical or horizontal tabs with different
 * pieces of content.
 *
 * @since 1.0.0
 */
class Puca_Elementor_Button extends  Puca_Elementor_Widget_Base{
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
        return 'tbay-button';
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
        return esc_html__( 'Puca Button', 'puca' );
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
        return 'eicon-button';
    }

    /**
     * Register tabs widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */

    protected function _register_controls() {

        $this->start_controls_section(
            'section_general',
            [
                'label' => esc_html__( 'General', 'puca' ),
            ]
        );
        $this->add_control(
            'btn_align',
            [
                'label' => esc_html('Align','puca'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html('Left','puca'),
                        'icon' => 'fas fa-align-left'
                    ],
                    'center' => [
                        'title' => esc_html('Center','puca'),
                        'icon' => 'fas fa-align-center'
                    ],
                    'right' => [
                        'title' => esc_html('Right','puca'),
                        'icon' => 'fas fa-align-right'
                    ],   
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .tbay-addon-button' => 'text-align: {{VALUE}} !important',
                ]
            ]
        );        

        $this->add_control(
            'text_button',
            [
                'label' => esc_html__( 'Text Button', 'puca' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'link_button',
            [
                'label' => esc_html__( 'Link Button', 'puca' ),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => esc_html__( 'https://your-link.com', 'puca' )
            ]
        );
        $this->add_control(
            'add_icon',
            [
                'label' => esc_html__( 'Add Icon', 'puca' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no'
            ]
        );
        $this->add_control(
            'icon_button',
            [
                'label' => esc_html__( 'Choose Icon', 'puca' ),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'linear-icon-arrow-right',
					'library' => 'linear-icon',
                ],
                'condition' => [
                    'add_icon' => 'yes'
                ]
            ]
        );


        $this->end_controls_section();
    }

    protected function render_item() {
        $settings = $this->get_settings_for_display();
        extract($settings);

        $link = $settings['link_button']['url'];
        $is_external        = $link_button['is_external'];
        $nofollow           = $link_button['nofollow'];
		
        $attribute = '';
        if( $is_external === 'on' ) {
            $attribute .= 'target="_blank"';
        }                

        if( $nofollow === 'on' ) {
            $attribute .= 'rel="nofollow"';
        }
        ?>
            <a href="<?php echo esc_url($link) ?>" <?php echo trim($attribute) ?> class="tbay-btn-theme btn-theme"><?php echo trim($text_button); ?>
                <?php $this->render_item_icon($icon_button); ?>
            </a>
        <?php
    }
    protected function render_item_icon($icon_button) {

        if( empty( $icon_button['value'] ) ) return;
       
        $this->add_render_attribute( 'icon', 'class', $icon_button['value'] );

        echo '<i '. trim($this->get_render_attribute_string( 'icon' )) .'></i>';
    }
}
$widgets_manager->register_widget_type(new Puca_Elementor_Button());
