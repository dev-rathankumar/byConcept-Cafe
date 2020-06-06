<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Testimonials') ) {
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
class Puca_Elementor_Testimonials extends  Puca_Elementor_Carousel_Base{
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
        return 'tbay-testimonials';
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
        return esc_html__( 'Puca Testimonials', 'puca' );
    }

    public function get_script_depends() {
        return [ 'slick' ];
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
        return 'eicon-testimonial';
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
        $this->register_controls_heading();
        $this->register_remove_heading_element();
        
        $this->start_controls_section(
            'section_general',
            [
                'label' => esc_html__( 'General', 'puca' ),
            ]
        );
        
        $this->add_control(
            'number',
            [
                'label'     => esc_html__('Number', 'puca'),
                'description' => esc_html__( 'Get out the number of the custom post "tbay_testimonial"', 'puca' ),
                'type'      => \Elementor\Controls_Manager::NUMBER,
                'default'   => 3,
            ]
        );   

        $this->add_control(
            'styles',
            [
                'label'     => esc_html__('Choose style', 'puca'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'v1',
                'options'   => $this->get_testimonials_layouts(),
            ]
        );   

        $this->add_control(
            'layout_type',
            [
                'label'     => esc_html__('Layout Type', 'puca'),
                'type'      => Controls_Manager::HIDDEN,
                'default'   => 'carousel',
            ]
        );  

        $this->end_controls_section();

        $this->add_control_responsive();
        $this->add_control_carousel(['layout_type' => 'carousel']);
    }

    protected function get_testimonials_layouts() { 
		$testimonials = array();
		$active_theme = puca_tbay_get_part_theme();
		$files = glob( get_template_directory() . '/vc_templates/testimonial/'.$active_theme.'/testimonial-*.php' );
	    if ( !empty( $files ) ) {
	        foreach ( $files as $file ) {
	        	$testi = str_replace( "testimonial-", '', str_replace( '.php', '', basename($file) ) );
	            $testimonials[$testi] = $testi;
	        }
	    }

		return $testimonials;
    }

}
$widgets_manager->register_widget_type(new Puca_Elementor_Testimonials());
