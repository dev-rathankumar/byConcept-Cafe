<?php

if ( ! defined( 'ABSPATH' ) || function_exists('Puca_Elementor_Features') ) {
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
class Puca_Elementor_Features extends  Puca_Elementor_Carousel_Base{
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
        return 'tbay-features';
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
        return esc_html__( 'Puca Features', 'puca' );
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
        $this->start_controls_section(
            'section_general',
            [
                'label' => esc_html__( 'General', 'puca' ),
            ]
        );
 
        $repeater = new \Elementor\Repeater();


        
        $this->add_control(
            'styles',
            [
                'label'     => esc_html__('Choose style', 'puca'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default'   => 'default',
                'options'   => [
                    'default'           => esc_html__('Default', 'puca'), 
                    'style1'            => esc_html__('Style 1', 'puca'), 
                    'style2'            => esc_html__('Style 2', 'puca'), 
                    'style3'            => esc_html__('Style 3', 'puca'), 
                    'contact-us'        => esc_html__('Contact Us', 'puca'), 
                ],
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

        $this->end_controls_section();

        $this->register_controls_item_style();
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
            'feature_type',
            [
                'label' => esc_html__( 'Display Type', 'puca' ),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'icon',
                'options' => [
                    'image' => [
                        'title' => esc_html__('Image', 'puca'),
                        'icon' => 'fa fa-image',
                    ],
                    'icon' => [
                        'title' => esc_html__('Icon', 'puca'),
                        'icon' => 'fa fa-info',
                    ],
                ],
                'default' => 'images',
            ]
        ); 
        
        $repeater->add_control(
            'selected_icon',
            [
                'label' => esc_html('Choose Icon','puca'),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'linear-icon-gift',
					'library' => 'linear-icon',
                ],
                'condition' => [
                    'feature_type' => 'icon'
                ]
            ]
        );
        $repeater->add_control(
            'type_image',
            [
                'label' => esc_html('Choose Image','puca'),
                'type' => Controls_Manager::MEDIA,
                'condition' => [
                    'feature_type' => 'image'
                ]
            ]
        );
    
        $repeater->add_responsive_control(
			'feature_margin_icon',
			[
				'label' => esc_html__( 'Margin "Icon"', 'puca' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .widget-features .fbox-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'feature_type' => 'icon'
                ]
			]
        );

        return $repeater;
    }

    protected function register_controls_item_style(){
        $this->start_controls_section(
            'section_item_style',
            [
                'label' => esc_html__( 'Style Item', 'puca' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'feature_title_font',
            [
                'label' => esc_html__( 'Font Title', 'puca' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
					'px' => [
						'min' => 10,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .widget-features .ourservice-heading' => 'font-size: {{SIZE}}{{UNIT}};',
				],

            ]
        );
        $this->add_control(
            'feature_title_line_height',
            [
                'label' => esc_html__( 'Line Height', 'puca' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
					'px' => [
						'min' => 10,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .widget-features .ourservice-heading' => 'line-height: {{SIZE}}{{UNIT}};',
				],

            ]
        );
        $this->add_control(
            'spacing_title',
            [
                'label' => esc_html('Spacing title','puca'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ], 
                'selectors' => [
                    '{{WRAPPER}} .widget-features .ourservice-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'feature_desc_font',
            [
                'label' => esc_html__( 'Font Description', 'puca' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
					'px' => [
						'min' => 10,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .widget-features .description' => 'font-size: {{SIZE}}{{UNIT}};',
				],

            ]
        );
        $this->add_control(
            'feature_desc_line-height',
            [
                'label' => esc_html__( 'Line Height', 'puca' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
					'px' => [
						'min' => 10,
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .widget-features .description' => 'line-height: {{SIZE}}{{UNIT}};',
				],

            ]
        );
        $this->add_control(
            'spacing_desc',
            [
                'label' => esc_html('Spacing Description','puca'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ], 
                'selectors' => [
                    '{{WRAPPER}} .widget-features .description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'feature_align',
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
                    '{{WRAPPER}} .feature-box .inner' => 'text-align: {{VALUE}} !important',
                ]
            ]
        );
        $this->add_control(
            'feature_icon_font',
            [
                'label' => esc_html__( 'Font Icon', 'puca' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 80,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .icon-inner i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],

            ]
        );

        $this->end_controls_section();

    }

    protected function render_item($item) {
        extract($item);
        ?> 
        <div class="inner"> 
            <?php
                $this->render_item_fbox($feature_type,$type_image,$selected_icon);
                $this->render_item_content($feature_title,$feature_desc);     
            ?>
        </div>
        <?php
    }      
    public function render_item_content($feature_title,$feature_desc) {
        ?>
            <div class="fbox-content">
                <?php
                if(isset($feature_title) && !empty($feature_title)) ?>
                    <h3 class="ourservice-heading">
                        <?php echo trim($feature_title) ?>
                    </h3>
                <?php
                if(isset($feature_desc) && !empty($feature_desc)) ?>
                    <p class="description">
                        <?php echo trim($feature_desc) ?>
                    </p>
                <?php
                ?>
            </div>
        <?php
    }
    
    public function render_item_fbox($feature_type,$type_image,$selected_icon){
        $image_id = $type_image['id'];

        $fbox_class = '';
        $fbox_class .= 'fbox-'.$feature_type;

        ?>
        <div class="<?php echo esc_attr($fbox_class); ?>">
            <?php if(isset($selected_icon['value']) && !empty($selected_icon['value'])): ?>
                <div class="icon-inner"><?php $this->render_item_icon($selected_icon) ?></div>
            <?php elseif(isset($image_id) && !empty($image_id)): ?>
                <div class="image-inner tbay-image-loaded">
                    <?php 
                        $img        = wp_get_attachment_image_src($image_id,'full'); 
                        $image_alt  = get_post_meta( $image_id, '_wp_attachment_image_alt', true);
                        puca_tbay_src_image_loaded($img[0], array('alt'=> $image_alt)); 
                    ?>
                </div>
            <?php endif;?>
        </div>

        <?php

    }
    
    public function on_import( $element ) {
		return Icons_Manager::on_import_migration( $element, 'icon', 'selected_icon', true );
    }
    

}
$widgets_manager->register_widget_type(new Puca_Elementor_Features());
