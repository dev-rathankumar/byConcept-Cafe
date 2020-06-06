<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Supermaket_Features') ) {
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
class Puca_Elementor_Supermaket_Features extends  Puca_Elementor_Widget_Base{
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
        return 'tbay-supermaket-features';
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
        return esc_html__( 'Puca Supermaket Features', 'puca' );
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
        return 'eicon-star-o';
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

        $features = $this->register_features_repeater();

        $this->add_control(
            'features',
            [
                'label' => esc_html('Feature Item','puca'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $features->get_controls(),
            ]
        );
        $this->register_view_more();

        $this->end_controls_section();

    }

    protected function register_features_repeater() {
        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'feature_title',
            [
                'label' => esc_html__( 'Title', 'puca' ),
                'type' => Controls_Manager::TEXT,
            ]
        );
        
        $repeater->add_control(
            'feature_desc',
            [
                'label' => esc_html__( 'Description', 'puca' ),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );
        
        $repeater->add_control(
            'feature_img',
            [
                'label' => esc_html__( 'Choose Image', 'puca' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_control (
            'feature_link', 
            [
                'label' => esc_html__('External link','puca'),
                'type' => Controls_Manager::URL,
                'separator'     => 'after',
                'placeholder' => esc_html__( 'https://your-link.com', 'puca' ),
            ]
        );

        return $repeater;
    }

    protected function register_view_more() {
        $this->add_control(
            'show_view_more',
            [
                'label'     => esc_html__('Show button?', 'puca'),
                'type'      => Controls_Manager::SWITCHER,
                'separator' => 'before',
                'default'   => 'yes'
            ]
        );  
        $this->add_control(
            'view_more_text',
            [
                'label'     => esc_html__('Text Button', 'puca'),
                'type'      => Controls_Manager::TEXT,
                'default' => esc_html__('View All', 'puca'),
                'condition' => [
                    'show_view_more' => 'yes'
                ]
            ]
        ); 
        $this->add_control (
            'view_more_link', 
            [
                'label' => esc_html__('External link','puca'),
                'type' => Controls_Manager::URL,
                'condition' => [
                    'show_view_more' => 'yes'
                ],
                'placeholder' => esc_html__( 'https://your-link.com', 'puca' ),
            ]
        );
    }

    protected function render_item($item, $index) {
        extract($item);
        ?> 
        <div class="row feature-box media"> 
            <?php
                $this->render_item_img($feature_img, $feature_link, $index);
                $this->render_item_content($feature_title,$feature_desc);     
            ?>
        </div>
        <?php
    }      
    public function render_item_content($feature_title,$feature_desc) {
        ?>
            <div class="media-body col-md-6">
               <div class="media-heading">
                   <?php if( !empty($feature_title) ) : ?>
                        <span class="title"><?php echo trim($feature_title); ?></span>
                   <?php endif; ?>

                   <?php if( !empty($feature_desc) ) : ?>
                        <span class="description"><?php echo trim($feature_desc); ?></span>
                   <?php endif; ?>
               </div>
            </div>
        <?php
    }
    
    public function render_item_img($feature_img, $feature_link, $index){
        ?>
        <div class="col-md-6 fbox-image">
            <?php if( isset($feature_img['id']) && $feature_img['id'] ) : ?>
                <div class="img-banner tbay-image-loaded">
                    
                    <?php if ( !empty($feature_link['url']) ) : ?>
                        <?php 
                            $item_setting_key = $this->get_repeater_setting_key( 'link', 'list_banner', $index );

                            $this->add_render_attribute($item_setting_key, 'href', $feature_link['url'] );

                            if( $feature_link['is_external'] === 'on' ) {
                                $this->add_render_attribute('link', 'target', '_blank');
                            }
                            if( $feature_link['nofollow'] === 'on' ) {
                                $this->add_render_attribute('link', 'rel', 'nofollow');
                            }    
                        ?>
                        <a <?php echo trim($this->get_render_attribute_string($item_setting_key)); ?>>
                    <?php endif; ?>
    
                        <?php echo wp_get_attachment_image($feature_img['id'], 'full'); ?>
    
                    <?php if ( !empty($feature_link['url']) ) : ?>
                    </a>
                    <?php endif; ?>
                </div>
                
            <?php endif; ?>
        </div>
        <?php
        
    }

    public function render_element_heading() {
        $heading_title = $heading_title_tag = $heading_subtitle = '';
        $settings = $this->get_settings_for_display();
        extract( $settings );  

        if( !empty($heading_subtitle) || !empty($heading_title) ) : ?>
            <div class="space-25">
                <<?php echo trim($heading_title_tag); ?> class="heading-tbay-title widget-title">
                    <?php if( !empty($heading_title) ) : ?>
                        <span class="title"><?php echo trim($heading_title); ?></span>
                    <?php endif; ?>	    	
                    <?php if( !empty($heading_subtitle) ) : ?>
                        <span class="subtitle"><?php echo trim($heading_subtitle); ?></span>
                    <?php endif; ?>
                </<?php echo trim($heading_title_tag); ?>>
            </div>
		<?php endif;

    }  

    public function render_view_more() {
        $settings = $this->get_settings_for_display();
        extract( $settings );  
        if( $show_view_more !== 'yes' || empty($view_more_link['url']) ) return;

        $this->add_render_attribute('link', 'href', $view_more_link['url'] );

        if( $view_more_link['is_external'] === 'on' ) {
            $this->add_render_attribute('link', 'target', '_blank');
        }
 
        if( $view_more_link['nofollow'] === 'on' ) {
            $this->add_render_attribute('link', 'rel', 'nofollow');
        }

        $this->add_render_attribute('link', 'class', 'more_link' );

        ?>
        <a <?php echo trim($this->get_render_attribute_string('link')); ?>><?php echo trim($view_more_text); ?></a>
        <?php
    }

}
$widgets_manager->register_widget_type(new Puca_Elementor_Supermaket_Features());
