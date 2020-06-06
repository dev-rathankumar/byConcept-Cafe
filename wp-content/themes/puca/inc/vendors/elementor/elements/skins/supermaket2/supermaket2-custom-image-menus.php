<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Supermaket2_Custom_Image_Menus') ) {
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
class Puca_Elementor_Supermaket2_Custom_Image_Menus extends  Puca_Elementor_Widget_Base {
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
        return 'tbay-supermaket2-custom-image-menus';
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
        return esc_html__( 'Tbay Supermaket2 Custom Image Menus', 'puca' );
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
        return 'eicon-nav-menu';
    }

    public function get_keywords() {
        return [ 'supermaket2' ];
    }
    
    protected function _register_controls() {

        $this->start_controls_section(
            'section_general',
            [
                'label' => esc_html__( 'Content', 'puca' ),
            ]
        );
        
        $menus = $this->get_available_menus();
        if (!empty($menus)) {
            $this->add_control(
                'nav_menu',
                [
                    'label'        => esc_html__('Menu', 'puca'),
                    'type'         => Controls_Manager::SELECT,
                    'options'      => $menus,
                    'default'      => array_keys($menus)[0],
                    'save_default' => true,
                    'separator'    => 'after',
                    'description'  => esc_html__('Note does not apply to Mega Menu.', 'puca'),
                ]
            );
        } else {
            $this->add_control(
                'nav_menu',
                [
                    'type'            => Controls_Manager::RAW_HTML,
                    'raw'             => sprintf(__('<strong>There are no menus in your site.</strong><br>Go to the <a href="%s" target="_blank">Menus screen</a> to create one.', 'puca'), admin_url('nav-menus.php?action=edit&menu=0')),
                    'separator'       => 'after',
                    'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                ]
            );
        }

        $this->add_control(
            'banner',
            [
                'label' => esc_html__( 'Banner', 'puca' ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control (
            'banner_link', 
            [
                'label' => esc_html__('External link','puca'),
                'type' => Controls_Manager::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => esc_html__( 'https://your-link.com', 'puca' ),
            ]
        );

        $this->end_controls_section();
    }

    public function render_content_banner($banner, $banner_link) {
        $settings = $this->get_settings_for_display();
        extract($settings);
        $cat_id         =   $banner;

        if( isset($cat_id) && $cat_id ) { ?>
            <div class="wpb_single_image">
                <div class="img-banner tbay-image-loaded">
                    
                    <?php if ( !empty($banner_link['url']) ) : ?>
                        <?php 
                            $this->add_render_attribute('link', 'href', $banner_link['url'] );
     
                            if( $banner_link['is_external'] === 'on' ) {
                                $this->add_render_attribute('link', 'target', '_blank');
                            }
                            if( $banner_link['nofollow'] === 'on' ) {
                                $this->add_render_attribute('link', 'rel', 'nofollow');
                            }    
                        ?>
                        <a <?php echo trim($this->get_render_attribute_string('link')); ?>>
                    <?php endif; ?>

                        <?php echo wp_get_attachment_image($cat_id['id'], 'full'); ?>

                    <?php if ( !empty($banner_link['url']) ) : ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php }
    }
}
$widgets_manager->register_widget_type(new Puca_Elementor_Supermaket2_Custom_Image_Menus());
