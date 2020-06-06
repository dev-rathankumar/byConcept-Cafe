<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Nav_Menu') ) {
    exit; // Exit if accessed directly.
}


use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;

class Puca_Elementor_Nav_Menu extends Puca_Elementor_Widget_Base {

    protected $nav_menu_index = 1;

    public function get_name() {
        return 'tbay-nav-menu';
    }

    public function get_title() {
        return esc_html__('Puca Nav Menu', 'puca');
    }

    protected function get_html_wrapper_class() {
        return 'vc_wp_custommenu elementor-widget-' . $this->get_name();
    }

    public function get_icon() {
        return 'eicon-nav-menu';
    }

    public function get_script_depends() {
        $script = [];

        $script[]   = 'jquery-treeview';

        return $script;
    }

    public function on_export($element) {
        unset($element['settings']['menu']);

        return $element;
    }

    protected function get_nav_menu_index() {
        return $this->nav_menu_index++;
    }

    protected function _register_controls() {
        $this->register_controls_heading();
        $this->register_remove_heading_element();

        $this->start_controls_section(
            'section_layout',
            [
                'label' => esc_html__('General', 'puca'),
            ]
        );

        $menus = $this->get_available_menus();

        if (!empty($menus)) {
            $this->add_control(
                'menu',
                [
                    'label'        => esc_html__('Menu', 'puca'),
                    'type'         => Controls_Manager::SELECT,
                    'options'      => $menus,
                    'default'      => array_keys($menus)[0],
                    'save_default' => true,
                    'separator'    => 'after',
                    'description'  => sprintf(__('Go to the <a href="%s" target="_blank">Menus screen</a> to manage your menus.', 'puca'), admin_url('nav-menus.php')),
                ]
            );
        } else {
            $this->add_control(
                'menu',
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => sprintf(__('<strong>There are no menus in your site.</strong><br>Go to the <a href="%s" target="_blank">Menus screen</a> to create one.', 'puca'), admin_url('nav-menus.php?action=edit&menu=0')),
                    'separator'       => 'after',
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                ]
            );
        }

        $this->add_control(
            'layout',
            [
                'label'              => esc_html__('Layout Menu', 'puca'),
                'type'               => Controls_Manager::SELECT,
                'default'            => 'treeview', 
                'options'            => [
                    'vertical'   => esc_html__('Vertical', 'puca'),
                    'treeview'   => esc_html__('Tree View', 'puca'),
                    'horizoltal'   => esc_html__('Horizoltal ', 'puca'),
                ],
                'frontend_available' => true,
            ]
        );
        
        $this->end_controls_section(); 
        $this->remove_control('heading_subtitle');
    }

    public function render_element_heading() {
        $heading_title = $heading_title_tag = $heading_subtitle = '';
        $settings = $this->get_settings_for_display();
        extract( $settings );

        if( !empty($heading_title) ) : ?>
            <<?php echo trim($heading_title_tag); ?> class="heading-tbay-title widget-title widgettitle">
               <?php echo trim($heading_title); ?>  
            </<?php echo trim($heading_title_tag); ?>>
        <?php endif;
    }  
}
$widgets_manager->register_widget_type(new Puca_Elementor_Nav_Menu());

