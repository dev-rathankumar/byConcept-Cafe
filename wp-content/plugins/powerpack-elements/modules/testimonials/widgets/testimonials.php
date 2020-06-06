<?php
namespace PowerpackElements\Modules\Testimonials\Widgets;

use PowerpackElements\Base\Powerpack_Widget;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Control_Media;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Testimonials Widget
 */
class Testimonials extends Powerpack_Widget {
    
    /**
	 * Retrieve testimonials widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return parent::get_widget_name( 'Testimonials' );
    }

    /**
	 * Retrieve testimonials widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
    public function get_title() {
        return parent::get_widget_title( 'Testimonials' );
    }

    /**
	 * Retrieve the list of categories the testimonials widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Testimonials' );
    }

    /**
	 * Retrieve testimonials widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
    public function get_icon() {
        return parent::get_widget_icon( 'Testimonials' );
    }

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Testimonials' );
	}
    
    /**
	 * Retrieve the list of scripts the testimonials widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_script_depends() {
        return [
			'jquery-slick',
            'jquery-resize',
            'imagesloaded',
            'isotope',
            'powerpack-frontend'
        ];
    }

    /**
	 * Register testimonials widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
    protected function _register_controls() {

        /*-----------------------------------------------------------------------------------*/
        /*	CONTENT TAB
        /*-----------------------------------------------------------------------------------*/
        
        /**
         * Content Tab: Testimonials
         */
        $this->start_controls_section(
            'section_testimonials',
            [
                'label'                 => __( 'Testimonials', 'powerpack' ),
            ]
        );
        
        $repeater = new Repeater();
        
        $repeater->add_control(
			'content',
			[
                'label'                => __( 'Content', 'powerpack' ),
                'type'                 => Controls_Manager::TEXTAREA,
                'default'              => '',
                'dynamic'              => [
                    'active'  => true,
                ],
			]
		);
        
        $repeater->add_control(
			'image',
			[
                'label'                => __( 'Image', 'powerpack' ),
                'type'                 => Controls_Manager::MEDIA,
                'dynamic'              => [
                    'active'  => true,
                ],
                'default'              => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
			]
		);
        
        $repeater->add_control(
			'name',
			[
                'label'                => __( 'Name', 'powerpack' ),
                'type'                 => Controls_Manager::TEXT,
                'default'              => __( 'John Doe', 'powerpack' ),
                'dynamic'              => [
                    'active'  => true,
                ],
			]
		);
        
        $repeater->add_control(
			'position',
			[
                'label'                => __( 'Position', 'powerpack' ),
                'type'                 => Controls_Manager::TEXT,
                'default'              => __( 'CEO', 'powerpack' ),
                'dynamic'              => [
                    'active'  => true,
                ],
			]
		);

		$repeater->add_control(
			'rating',
			[
				'label'                => __( 'Rating', 'powerpack' ),
				'type'                 => Controls_Manager::NUMBER,
				'min'                  => 0,
				'max'                  => 5,
				'step'                 => 0.1,
			]
		);
        
        $this->add_control(
			'testimonials',
			[
				'label'                => '',
				'type'                 => Controls_Manager::REPEATER,
				'default'              => [
					[
						'name'        => __( 'John Doe', 'powerpack' ),
						'position'    => __( 'CEO', 'powerpack' ),
						'content'     => __( 'I am slide content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'powerpack' ),
					],
					[
						'name'        => __( 'John Doe', 'powerpack' ),
						'position'    => __( 'CEO', 'powerpack' ),
						'content'     => __( 'I am slide content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'powerpack' ),
					],
					[
						'name'        => __( 'John Doe', 'powerpack' ),
						'position'    => __( 'CEO', 'powerpack' ),
						'content'     => __( 'I am slide content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'powerpack' ),
					],
				],
				'fields'            => array_values( $repeater->get_controls() ),
			]
		);
        
        $this->add_control(
            'layout',
            [
                'label'                => __( 'Layout', 'powerpack' ),
                'type'                 => Controls_Manager::SELECT,
                'default'              => 'carousel',
                'options'              => [
                    'carousel'  => __( 'Carousel', 'powerpack' ),
                    'slideshow' => __( 'Slideshow', 'powerpack' ),
                    'grid'      => __( 'Grid', 'powerpack' ),
                ],
                'separator'            => 'before',
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label'                 => __( 'Columns', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => '3',
                'tablet_default'        => '2',
                'mobile_default'        => '1',
                'options'               => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                ],
                'prefix_class'          => 'elementor-grid%s-',
                'frontend_available'    => true,
                'condition'             => [
                    'layout'    => 'grid',
                ],
            ]
        );
        
        $this->add_control(
            'skin',
            [
                'label'                => __( 'Skin', 'powerpack' ),
                'type'                 => Controls_Manager::SELECT,
                'default'              => 'skin-1',
                'options'              => [
                    'skin-1'    => __( 'Skin 1', 'powerpack' ),
                    'skin-2'    => __( 'Skin 2', 'powerpack' ),
                    'skin-3'    => __( 'Skin 3', 'powerpack' ),
                    'skin-4'    => __( 'Skin 4', 'powerpack' ),
                    'skin-5'    => __( 'Skin 5', 'powerpack' ),
                    'skin-6'    => __( 'Skin 6', 'powerpack' ),
                    'skin-7'    => __( 'Skin 7', 'powerpack' ),
                    'skin-8'    => __( 'Skin 8', 'powerpack' ),
                ],
            ]
        );
        
        $this->add_control(
            'content_style',
            [
                'label'                => __( 'Content Style', 'powerpack' ),
                'type'                 => Controls_Manager::SELECT,
                'default'              => 'default',
                'options'              => [
                    'default'   => __( 'Default', 'powerpack' ),
                    'bubble'    => __( 'Bubble', 'powerpack' ),
                ],
                'prefix_class'          => 'pp-testimonials-content-',
            ]
        );
        
        $this->add_control(
            'show_image',
            [
                'label'                => __( 'Show Image', 'powerpack' ),
                'type'                 => Controls_Manager::SELECT,
                'default'              => '',
                'options'              => [
                    ''      => __( 'Yes', 'powerpack' ),
                    'no'    => __( 'No', 'powerpack' ),
                ],
            ]
        );
        
        $this->add_control(
            'image_position',
            [
                'label'                => __( 'Image Position', 'powerpack' ),
                'type'                 => Controls_Manager::SELECT,
                'default'              => 'inline',
                'options'              => [
                    'inline'    => __( 'Inline', 'powerpack' ),
                    'stacked'   => __( 'Stacked', 'powerpack' ),
                ],
                'condition'             => [
                    'show_image'    => '',
                    'skin'          => ['skin-1','skin-2','skin-3','skin-4'],
                ],
            ]
        );
        
        $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'thumbnail',
				'default'               => 'full',
                'condition'             => [
                    'show_image'    => '',
                ],
			]
		);
        
        $this->add_control(
            'show_quote',
            [
                'label'                => __( 'Show Quote', 'powerpack' ),
                'type'                 => Controls_Manager::SELECT,
                'default'              => 'no',
                'options'              => [
                    ''      => __( 'Yes', 'powerpack' ),
                    'no'    => __( 'No', 'powerpack' ),
                ],
            ]
        );
        
        $this->add_control(
            'quote_position',
            [
                'label'                => __( 'Quote Position', 'powerpack' ),
                'type'                 => Controls_Manager::SELECT,
                'default'              => 'above',
                'options'              => [
                    'above'         => __( 'Above Content', 'powerpack' ),
                    'before'        => __( 'Before Content', 'powerpack' ),
                    'before-after'  => __( 'Before/After Content', 'powerpack' ),
                ],
				'prefix_class'          => 'pp-testimonials-quote-position-',
                'condition'             => [
                    'show_quote'    => '',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Content Tab: Slider Options
         */
        $this->start_controls_section(
            'section_slider_options',
            [
                'label'                 => __( 'Slider Options', 'powerpack' ),
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                ],
            ]
        );
        
        $this->add_control(
            'effect',
            [
                'label'                => __( 'Effect', 'powerpack' ),
                'type'                 => Controls_Manager::SELECT,
                'default'              => 'slide',
                'options'              => [
                    'slide'     => __( 'Slide', 'powerpack' ),
                    'fade'      => __( 'Fade', 'powerpack' ),
                ],
                'condition'             => [
                    'layout'    => 'slideshow',
                ],
            ]
        );

		$slides_per_view = range( 1, 10 );
		$slides_per_view = array_combine( $slides_per_view, $slides_per_view );

		$this->add_responsive_control(
			'slides_per_view',
			[
				'type'                  => Controls_Manager::SELECT,
				'label'                 => __( 'Slides Per View', 'powerpack' ),
				'options'               => $slides_per_view,
				'default'               => '3',
				'tablet_default'        => '2',
				'mobile_default'        => '1',
				'condition'             => [
                    'layout'    => 'carousel',
				],
				'frontend_available'    => true,
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'type'                  => Controls_Manager::SELECT,
				'label'                 => __( 'Slides to Scroll', 'powerpack' ),
				'description'           => __( 'Set how many slides are scrolled per swipe.', 'powerpack' ),
				'options'               => $slides_per_view,
				'default'               => '1',
				'tablet_default'        => '1',
				'mobile_default'        => '1',
				'condition'             => [
                    'layout'    => 'carousel',
				],
				'frontend_available'    => true,
			]
		);
        
        $this->add_control(
            'autoplay',
            [
                'label'                 => __( 'Autoplay', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'separator'             => 'before',
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                ],
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label'                 => __( 'Autoplay Speed', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => 3000,
                'frontend_available'    => true,
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                    'autoplay'  => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'infinite_loop',
            [
                'label'                 => __( 'Infinite Loop', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                ],
            ]
        );

        $this->add_control(
            'animation_speed',
            [
                'label'                 => __( 'Animation Speed', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => 600,
                'frontend_available'    => true,
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                ],
            ]
        );
        
        $this->add_control(
            'center_mode',
            [
                'label'                 => __( 'Center Mode', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'frontend_available'    => true,
                'separator'				=> 'before',
            ]
        );
		
		$this->add_responsive_control(
            'center_padding',
            [
                'label'                 => __( 'Center Padding', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
                    'size' => 40,
                    'unit' => 'px',
                ],
				'size_units'            => [ 'px' ],
				'range'                 => [
					'px' => [
						'max' => 500,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
                'condition'             => [
                    'center_mode'	=> 'yes',
                ],
			]
		);
        
        $this->add_control(
            'name_navigation_heading',
            [
                'label'                 => __( 'Navigation', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                ],
            ]
        );
        
        $this->add_control(
            'arrows',
            [
                'label'                 => __( 'Arrows', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                ],
            ]
        );
        
        $this->add_control(
            'thumbnail_nav',
            [
                'label'                 => __( 'Thumbnail Navigation', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'frontend_available'    => true,
                'condition'             => [
                    'layout'    => 'slideshow',
                ],
            ]
        );
        
        $this->add_control(
            'dots',
            [
                'label'                 => __( 'Dots', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'frontend_available'    => true,
                'conditions'            => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'layout',
                            'operator' => '==',
                            'value' => 'carousel',
                        ],
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'slideshow',
								],
								[
									'name' => 'thumbnail_nav',
									'operator' => '!==',
									'value' => 'yes',
								],
							],
                        ],
                    ]
                ],
            ]
        );
        
        $this->add_control(
            'orientation',
            [
                'label'                 => __( 'Orientation', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'horizontal',
                'options'               => [
                    'horizontal'    => __( 'Horizontal', 'powerpack' ),
                    'vertical'      => __( 'Vertical', 'powerpack' ),
                ],
				'separator'             => 'before',
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                ],
            ]
        );

        $this->end_controls_section();

        /*-----------------------------------------------------------------------------------*/
        /*	STYLE TAB
        /*-----------------------------------------------------------------------------------*/

        /**
         * Style Tab: Testimonial
         */
        $this->start_controls_section(
            'section_testimonial_style',
            [
                'label'                 => __( 'Testimonial', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'row_spacing',
            [
                'label'                 => __( 'Row Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size' 	    => 20,
                ],
                'range' 		=> [
                    'px' 		=> [
                        'min' 	=> 0,
                        'max' 	=> 100,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-testimonials .pp-grid-item-wrap' => 'margin-bottom: {{SIZE}}px;',
                ],
                'condition'             => [
                    'layout'    => 'grid',
                ],
            ]
        );

        $this->add_responsive_control(
            'column_spacing',
            [
                'label'                 => __( 'Column Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size' 	    => 20,
                ],
                'range' 		=> [
                    'px' 		=> [
                        'min' 	=> 0,
                        'max' 	=> 100,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-testimonials .pp-testimonial-slide, {{WRAPPER}} .pp-testimonials .pp-grid-item-wrap' => 'padding-left: calc({{SIZE}}px/2); padding-right: calc({{SIZE}}px/2);',
                    '{{WRAPPER}} .pp-testimonials .slick-list, {{WRAPPER}} .pp-elementor-grid' => 'margin-left: calc(-{{SIZE}}px/2); margin-right: calc(-{{SIZE}}px/2);',
                ],
				'separator'             => 'after',
                'condition'             => [
                    'layout!'   => 'slideshow',
                ],
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'slide_background',
				'types'            	    => [ 'classic','gradient' ],
				'selector'              => '{{WRAPPER}} .pp-testimonial, {{WRAPPER}} .pp-testimonials-wrap .pp-testimonials-thumb-item:before',
			]
		);

		$this->add_control(
			'slide_border',
			[
				'label'                 => __( 'Border', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial, {{WRAPPER}} .pp-testimonials-wrap .pp-testimonials-thumb-item:before' => 'border-style: solid',
				],
                'separator'             => 'before',
			]
		);

		$this->add_control(
			'slide_border_color',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .pp-testimonials-wrap .pp-testimonials-thumb-item:before' => 'border-color: transparent transparent {{VALUE}} {{VALUE}};',
				],
				'condition'             => [
					'slide_border'   => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'slide_border_width',
			[
				'label'                 => __( 'Border Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial, {{WRAPPER}} .pp-testimonials-wrap .pp-testimonials-thumb-item:before' => 'border-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .pp-testimonials-wrap .pp-testimonials-thumb-item:before' => 'top: -{{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'slide_border'   => 'yes',
				],
			]
		);

		$this->add_control(
			'slide_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'slide_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-testimonial',
			]
		);

		$this->add_responsive_control(
			'slide_padding',
			[
				'label'                 => __( 'Inner Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'separator'             => 'before',
			]
		);

		$this->add_responsive_control(
			'slide_outer_padding',
			[
				'label'                 => __( 'Outer Padding', 'powerpack' ),
				'description'           => __( 'You must add outer padding for showing box shadow', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial-outer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
					'{{WRAPPER}} .pp-testimonials-wrap .pp-testimonials-thumb-item:before' => 'margin-top: -{{BOTTOM}}{{UNIT}}',
				],
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow']
                ],
			]
		);

        $this->end_controls_section();

        /**
         * Style Tab: Content
         */
        $this->start_controls_section(
            'section_content_style',
            [
                'label'                 => __( 'Content', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'content_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-testimonial-content, {{WRAPPER}} .pp-testimonial-content:after' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_text_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-testimonial-content' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'content_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-testimonial-content',
            ]
        );

		$this->add_control(
			'border',
			[
				'label'                 => __( 'Border', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial-content, {{WRAPPER}} .pp-testimonial-content:after' => 'border-style: solid',
				],
                'separator'             => 'before',
			]
		);

		$this->add_control(
			'border_color',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#000',
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial-content' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .pp-testimonial-content:after' => 'border-color: transparent {{VALUE}} {{VALUE}} transparent',
				],
				'condition'             => [
					'border'   => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'border_width',
			[
				'label'                 => __( 'Border Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial-content, {{WRAPPER}} .pp-testimonial-content:after' => 'border-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .pp-testimonial-skin-1 .pp-testimonial-content:after' => 'margin-top: -{{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .pp-testimonial-skin-2 .pp-testimonial-content:after' => 'margin-bottom: -{{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .pp-testimonial-skin-3 .pp-testimonial-content:after' => 'margin-left: -{{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .pp-testimonial-skin-4 .pp-testimonial-content:after' => 'margin-right: -{{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'border'   => 'yes',
				],
			]
		);

		$this->add_control(
			'content_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%', 'em' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->add_responsive_control(
			'content_gap',
			[
				'label'                 => __( 'Gap', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
                    'size' => '',
                    'unit' => 'px',
                ],
				'size_units'            => [ 'px', '%' ],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
                'separator'             => 'before',
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial-skin-1 .pp-testimonial-content, {{WRAPPER}} .pp-testimonial-skin-5 .pp-testimonial-content, {{WRAPPER}} .pp-testimonial-skin-6 .pp-testimonial-content, {{WRAPPER}} .pp-testimonial-skin-7 .pp-testimonial-content' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-testimonial-skin-2 .pp-testimonial-content' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-testimonial-skin-3 .pp-testimonial-content' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-testimonial-skin-4 .pp-testimonial-content' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
        $this->add_control(
            'content_text_alignment',
            [
                'label'                 => __( 'Text Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center'    => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'     => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
					'justify'   => [
						'title' => __( 'Justified', 'powerpack' ),
						'icon'  => 'fa fa-align-justify',
					],
				],
				'default'               => 'center',
                'separator'             => 'before',
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial-content' => 'text-align: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
            'details_h_alignment',
            [
                'label'                 => __( 'Name and Position Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'toggle'                => false,
				'options'               => [
					'left'    		=> [
                        'title' 	=> __( 'Left', 'powerpack' ),
                        'icon' 		=> 'eicon-h-align-left',
                    ],
                    'center' 		=> [
                        'title' 	=> __( 'Center', 'powerpack' ),
                        'icon' 		=> 'eicon-h-align-center',
                    ],
                    'right' 		=> [
                        'title' 	=> __( 'Right', 'powerpack' ),
                        'icon' 		=> 'eicon-h-align-right',
                    ],
				],
				'default'               => 'center',
				'prefix_class'          => 'pp-testimonials-h-align-',
                'condition'             => [
                    'skin'    => ['skin-1','skin-2','skin-5','skin-6','skin-7'],
                ],
			]
		);
        
        $this->add_control(
            'details_v_alignment',
            [
                'label'                 => __( 'Name and Position Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'toggle'                => false,
				'default'               => 'middle',
				'options'               => [
					'top'          => [
						'title'    => __( 'Top', 'powerpack' ),
						'icon'     => 'eicon-v-align-top',
					],
					'middle'       => [
						'title'    => __( 'Center', 'powerpack' ),
						'icon'     => 'eicon-v-align-middle',
					],
					'bottom'       => [
						'title'    => __( 'Bottom', 'powerpack' ),
						'icon'     => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary'  => [
					'top'          => 'flex-start',
					'middle'       => 'center',
					'bottom'       => 'flex-end',
				],
				'prefix_class'          => 'pp-testimonials-v-align-',
                'condition'             => [
                    'skin'    => ['skin-3','skin-4'],
                ],
			]
		);
        
        $this->add_control(
            'image_v_alignment',
            [
                'label'                 => __( 'Image Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'toggle'                => false,
				'default'               => 'middle',
				'options'               => [
					'top'          => [
						'title'    => __( 'Top', 'powerpack' ),
						'icon'     => 'eicon-v-align-top',
					],
					'middle'       => [
						'title'    => __( 'Center', 'powerpack' ),
						'icon'     => 'eicon-v-align-middle',
					],
					'bottom'       => [
						'title'    => __( 'Bottom', 'powerpack' ),
						'icon'     => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary'  => [
					'top'          => 'flex-start',
					'middle'       => 'center',
					'bottom'       => 'flex-end',
				],
				'prefix_class'          => 'pp-testimonials-v-align-',
                'condition'             => [
                    'skin'    => ['skin-5','skin-6'],
                ],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'content_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-testimonial-content',
			]
		);
        
        $this->add_control(
            'name_style_heading',
            [
                'label'                 => __( 'Name', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
            ]
        );

        $this->add_control(
            'name_text_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-testimonial-name' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'name_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-testimonial-name',
            ]
        );
        
        $this->add_responsive_control(
			'name_gap',
			[
				'label'                 => __( 'Gap', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
                    'size' => '',
                    'unit' => 'px',
                ],
				'size_units'            => [ 'px', '%' ],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial-name' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
        $this->add_control(
            'position_style_heading',
            [
                'label'                 => __( 'Position', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
            ]
        );

        $this->add_control(
            'position_text_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-testimonial-position' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'position_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-testimonial-position',
            ]
        );
        
        $this->add_responsive_control(
			'position_gap',
			[
				'label'                 => __( 'Gap', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
                    'size' => '',
                    'unit' => 'px',
                ],
				'size_units'            => [ 'px', '%' ],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial-position' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
        $this->add_control(
            'quote_style_heading',
            [
                'label'                 => __( 'Quote', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'show_quote'    => '',
                ],
            ]
        );

        $this->add_control(
            'quote_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-testimonial-text:before, {{WRAPPER}} .pp-testimonial-text:after' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'show_quote'    => '',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'quote_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-testimonial-text:before, {{WRAPPER}} .pp-testimonial-text:after',
                'condition'             => [
                    'show_quote'    => '',
                ],
            ]
        );

		$this->add_responsive_control(
			'quote_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
                'allowed_dimensions'    => 'vertical',
				'placeholder'           => [
					'top'      => '',
					'right'    => 'auto',
					'bottom'   => '',
					'left'     => 'auto',
				],
				'selectors'             => [
					'{{WRAPPER}}.pp-testimonials-quote-position-above .pp-testimonial-text:before' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'show_quote'        => '',
                    'quote_position'    => ['above', 'before'],
                ],
			]
		);

        $this->end_controls_section();

        /**
         * Style Tab: Image
         */
        $this->start_controls_section(
            'section_image_style',
            [
                'label'                 => __( 'Image', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'show_image'    => '',
                ],
            ]
        );
        
        $this->add_responsive_control(
			'image_size',
			[
				'label'                 => __( 'Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ '%', 'px' ],
				'range'             => [
					'px' => [
						'max' => 200,
					],
				],
				'tablet_default'    => [
					'unit' => 'px',
				],
				'mobile_default'    => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial-image img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pp-testimonials-content-bubble.pp-testimonials-h-align-right .pp-testimonial-skin-1 .pp-testimonial-content:after, {{WRAPPER}}.pp-testimonials-content-bubble.pp-testimonials-h-align-right .pp-testimonial-skin-2 .pp-testimonial-content:after' => 'right: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}}.pp-testimonials-content-bubble.pp-testimonials-h-align-left .pp-testimonial-skin-1 .pp-testimonial-content:after, {{WRAPPER}}.pp-testimonials-content-bubble.pp-testimonials-h-align-left .pp-testimonial-skin-2 .pp-testimonial-content:after' => 'left: calc({{SIZE}}{{UNIT}}/2);',
				],
                'condition'             => [
                    'show_image'    => '',
                ],
			]
		);
        
        $this->add_responsive_control(
			'image_gap',
			[
				'label'                 => __( 'Gap', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
                    'size' => 10,
                    'unit' => 'px',
                ],
				'size_units'            => [ 'px', '%' ],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonials-image-stacked .pp-testimonial-image, {{WRAPPER}} .pp-testimonial-skin-7 .pp-testimonial-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-testimonials-image-inline .pp-testimonial-image, {{WRAPPER}} .pp-testimonial-skin-5 .pp-testimonial-image, {{WRAPPER}} .pp-testimonial-skin-8 .pp-testimonial-image' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pp-testimonials-h-align-right .pp-testimonials-image-inline .pp-testimonial-image, {{WRAPPER}} .pp-testimonial-skin-6 .pp-testimonial-image' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: 0;',
				],
                'condition'             => [
                    'show_image'    => '',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'image_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-testimonial-image',
                'condition'             => [
                    'show_image'    => '',
                ],
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonial-image, {{WRAPPER}} .pp-testimonial-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'show_image'    => '',
                ],
			]
		);
        
        $this->end_controls_section();

        /**
         * Style Tab: Rating
         */
		$this->start_controls_section(
			'section_rating_style',
			[
				'label'                 => __( 'Rating', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'star_style',
			[
				'label'                 => __( 'Icon', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					'star_fontawesome' => 'Font Awesome',
					'star_unicode' => 'Unicode',
				],
				'default'               => 'star_fontawesome',
				'render_type'           => 'template',
				'prefix_class'          => 'elementor--star-style-',
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'unmarked_star_style',
			[
				'label'                 => __( 'Unmarked Style', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'solid' => [
						'title' => __( 'Solid', 'powerpack' ),
						'icon' => 'fa fa-star',
					],
					'outline' => [
						'title' => __( 'Outline', 'powerpack' ),
						'icon' => 'fa fa-star-o',
					],
				],
				'default'               => 'solid',
			]
		);

		$this->add_control(
			'star_size',
			[
				'label'                 => __( 'Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .elementor-star-rating' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'star_space',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'             => [
					'body:not(.rtl) {{WRAPPER}} .elementor-star-rating i:not(:last-of-type)' => 'margin-right: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}} .elementor-star-rating i:not(:last-of-type)' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'stars_color',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .elementor-star-rating i:before' => 'color: {{VALUE}}',
				],
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'stars_unmarked_color',
			[
				'label'                 => __( 'Unmarked Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .elementor-star-rating i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();
        
        /**
         * Style Tab: Order
         */
        $this->start_controls_section(
            'section_order_style',
            [
                'label'                 => __( 'Order', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'image_position'    => 'stacked',
                ],
            ]
        );

        $this->add_control(
            'image_order',
            [
                'label'                 => __( 'Image', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => 1,
                'min'                   => 1,
                'max'                   => 4,
                'step'                  => 1,
                'condition'             => [
                    'image_position'    => 'stacked',
                ],
            ]
        );

        $this->add_control(
            'name_order',
            [
                'label'                 => __( 'Name', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => 2,
                'min'                   => 1,
                'max'                   => 4,
                'step'                  => 1,
                'condition'             => [
                    'image_position'    => 'stacked',
                ],
            ]
        );

        $this->add_control(
            'position_order',
            [
                'label'                 => __( 'Position', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => 3,
                'min'                   => 1,
                'max'                   => 4,
                'step'                  => 1,
                'condition'             => [
                    'image_position'    => 'stacked',
                ],
            ]
        );

        $this->add_control(
            'rating_order',
            [
                'label'                 => __( 'Rating', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => 4,
                'min'                   => 1,
                'max'                   => 4,
                'step'                  => 1,
                'condition'             => [
                    'image_position'    => 'stacked',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Style Tab: Navigation Arrows
         */
        $this->start_controls_section(
            'section_arrows_style',
            [
                'label'                 => __( 'Navigation Arrows', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                    'arrows'    => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'arrow',
            [
                'label'                 => __( 'Choose Arrow', 'powerpack' ),
                'type'                  => Controls_Manager::ICON,
                'label_block'           => true,
                'default'               => 'fa fa-angle-right',
                'include'               => [
                    'fa fa-angle-right',
                    'fa fa-angle-double-right',
                    'fa fa-chevron-right',
                    'fa fa-chevron-circle-right',
                    'fa fa-arrow-right',
                    'fa fa-long-arrow-right',
                    'fa fa-caret-right',
                    'fa fa-caret-square-o-right',
                    'fa fa-arrow-circle-right',
                    'fa fa-arrow-circle-o-right',
                    'fa fa-toggle-right',
                    'fa fa-hand-o-right',
                ],
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                    'arrows'    => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'arrows_size',
            [
                'label'                 => __( 'Arrows Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [ 'size' => '22' ],
                'range'                 => [
                    'px' => [
                        'min'   => 15,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-slider-arrow' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                    'arrows'    => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'arrows_horitonal_position',
            [
                'label'                 => __( 'Horizontal Alignment', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => -100,
                        'max'   => 450,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-arrow-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-arrow-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                    'arrows'    => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'arrows_vertical_position',
            [
                'label'                 => __( 'Vertical Alignment', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => -400,
                        'max'   => 400,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-arrow-next, {{WRAPPER}} .pp-arrow-prev' => 'top: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                    'arrows'    => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_arrows_style' );

        $this->start_controls_tab(
            'tab_arrows_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                    'arrows'    => 'yes',
                ],
            ]
        );

        $this->add_control(
            'arrows_bg_color_normal',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slider-arrow' => 'background-color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                    'arrows'    => 'yes',
                ],
            ]
        );

        $this->add_control(
            'arrows_color_normal',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slider-arrow' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                    'arrows'    => 'yes',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'arrows_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-slider-arrow',
                'separator'             => 'before',
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                    'arrows'    => 'yes',
                ],
			]
		);

		$this->add_control(
			'arrows_border_radius_normal',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-slider-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                    'arrows'    => 'yes',
                ],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_arrows_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                    'arrows'    => 'yes',
                ],
            ]
        );

        $this->add_control(
            'arrows_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slider-arrow:hover' => 'background-color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                    'arrows'    => 'yes',
                ],
            ]
        );

        $this->add_control(
            'arrows_color_hover',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slider-arrow:hover' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                    'arrows'    => 'yes',
                ],
            ]
        );

        $this->add_control(
            'arrows_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slider-arrow:hover' => 'border-color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                    'arrows'    => 'yes',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

		$this->add_responsive_control(
			'arrows_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-slider-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'separator'             => 'before',
                'condition'             => [
                    'layout'    => ['carousel', 'slideshow'],
                    'arrows'    => 'yes',
                ],
			]
		);
        
        $this->end_controls_section();
        
        /**
         * Style Tab: Dots
         */
        $this->start_controls_section(
            'section_dots_style',
            [
                'label'                 => __( 'Pagination: Dots', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'conditions'            => [
                    'relation' => 'or',
                    'terms' => [
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'carousel',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
							],
                        ],
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'slideshow',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
								[
									'name' => 'thumbnail_nav',
									'operator' => '!==',
									'value' => 'yes',
								],
							],
                        ],
                    ]
                ],
            ]
        );

        $this->add_control(
            'dots_position',
            [
                'label'                 => __( 'Position', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => [
                   'inside'     => __( 'Inside', 'powerpack' ),
                   'outside'    => __( 'Outside', 'powerpack' ),
                ],
                'default'               => 'outside',
				'prefix_class'          => 'pp-slick-slider-dots-',
                'conditions'            => [
                    'relation' => 'or',
                    'terms' => [
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'carousel',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
							],
                        ],
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'slideshow',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
								[
									'name' => 'thumbnail_nav',
									'operator' => '!==',
									'value' => 'yes',
								],
							],
                        ],
                    ]
                ],
            ]
        );
        
        $this->add_responsive_control(
            'dots_size',
            [
                'label'                 => __( 'Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 2,
                        'max'   => 40,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slick-slider .slick-dots li button' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
                ],
                'conditions'            => [
                    'relation' => 'or',
                    'terms' => [
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'carousel',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
							],
                        ],
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'slideshow',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
								[
									'name' => 'thumbnail_nav',
									'operator' => '!==',
									'value' => 'yes',
								],
							],
                        ],
                    ]
                ],
            ]
        );
        
        $this->add_responsive_control(
            'dots_gap',
            [
                'label'                 => __( 'Gap Between Dots', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 30,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slick-slider .slick-dots li' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
                ],
                'conditions'            => [
                    'relation' => 'or',
                    'terms' => [
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'carousel',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
							],
                        ],
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'slideshow',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
								[
									'name' => 'thumbnail_nav',
									'operator' => '!==',
									'value' => 'yes',
								],
							],
                        ],
                    ]
                ],
            ]
        );
        
        $this->add_responsive_control(
            'dots_top_spacing',
            [
                'label'                 => __( 'Top Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slick-slider .slick-dots' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
                'conditions'            => [
                    'relation' => 'or',
                    'terms' => [
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'carousel',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
							],
                        ],
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'slideshow',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
								[
									'name' => 'thumbnail_nav',
									'operator' => '!==',
									'value' => 'yes',
								],
							],
                        ],
                    ]
                ],
            ]
        );
        
        $this->add_responsive_control(
            'dots_vertical_alignment',
            [
                'label'                 => __( 'Vertical Alignment', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => -100,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slick-slider .slick-dots' => 'bottom: {{SIZE}}{{UNIT}};',
                ],
                'conditions'            => [
                    'relation' => 'or',
                    'terms' => [
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'carousel',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
							],
                        ],
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'slideshow',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
								[
									'name' => 'thumbnail_nav',
									'operator' => '!==',
									'value' => 'yes',
								],
							],
                        ],
                    ]
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_dots_style' );

        $this->start_controls_tab(
            'tab_dots_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'conditions'            => [
                    'relation' => 'or',
                    'terms' => [
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'carousel',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
							],
                        ],
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'slideshow',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
								[
									'name' => 'thumbnail_nav',
									'operator' => '!==',
									'value' => 'yes',
								],
							],
                        ],
                    ]
                ],
            ]
        );

        $this->add_control(
            'dots_color_normal',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slick-slider .slick-dots li' => 'background: {{VALUE}};',
                ],
                'conditions'            => [
                    'relation' => 'or',
                    'terms' => [
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'carousel',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
							],
                        ],
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'slideshow',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
								[
									'name' => 'thumbnail_nav',
									'operator' => '!==',
									'value' => 'yes',
								],
							],
                        ],
                    ]
                ],
            ]
        );

        $this->add_control(
            'active_dot_color_normal',
            [
                'label'                 => __( 'Active Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slick-slider .slick-dots li.slick-active' => 'background: {{VALUE}};',
                ],
                'conditions'            => [
                    'relation' => 'or',
                    'terms' => [
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'carousel',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
							],
                        ],
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'slideshow',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
								[
									'name' => 'thumbnail_nav',
									'operator' => '!==',
									'value' => 'yes',
								],
							],
                        ],
                    ]
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'dots_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-slick-slider .slick-dots li',
                'conditions'            => [
                    'relation' => 'or',
                    'terms' => [
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'carousel',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
							],
                        ],
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'slideshow',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
								[
									'name' => 'thumbnail_nav',
									'operator' => '!==',
									'value' => 'yes',
								],
							],
                        ],
                    ]
                ],
			]
		);

		$this->add_control(
			'dots_border_radius_normal',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-slick-slider .slick-dots li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'conditions'            => [
                    'relation' => 'or',
                    'terms' => [
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'carousel',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
							],
                        ],
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'slideshow',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
								[
									'name' => 'thumbnail_nav',
									'operator' => '!==',
									'value' => 'yes',
								],
							],
                        ],
                    ]
                ],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_dots_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'conditions'            => [
                    'relation' => 'or',
                    'terms' => [
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'carousel',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
							],
                        ],
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'slideshow',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
								[
									'name' => 'thumbnail_nav',
									'operator' => '!==',
									'value' => 'yes',
								],
							],
                        ],
                    ]
                ],
            ]
        );

        $this->add_control(
            'dots_color_hover',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slick-slider .slick-dots li:hover' => 'background: {{VALUE}};',
                ],
                'conditions'            => [
                    'relation' => 'or',
                    'terms' => [
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'carousel',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
							],
                        ],
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'slideshow',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
								[
									'name' => 'thumbnail_nav',
									'operator' => '!==',
									'value' => 'yes',
								],
							],
                        ],
                    ]
                ],
            ]
        );

        $this->add_control(
            'dots_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slick-slider .slick-dots li:hover' => 'border-color: {{VALUE}};',
                ],
                'conditions'            => [
                    'relation' => 'or',
                    'terms' => [
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'carousel',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
							],
                        ],
                        [
							'relation' => 'and',
							'terms' => [
								[
                                    'name' => 'layout',
                                    'operator' => '==',
                                    'value' => 'slideshow',
								],
								[
                                    'name' => 'dots',
                                    'operator' => '==',
                                    'value' => 'yes',
								],
								[
									'name' => 'thumbnail_nav',
									'operator' => '!==',
									'value' => 'yes',
								],
							],
                        ],
                    ]
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();

        /**
         * Style Tab: Thumbnail Navigation
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_thumbnail_nav_style',
            [
                'label'                 => __( 'Thumbnail Navigation', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'layout'        => 'slideshow',
                    'thumbnail_nav' => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
			'thumbnail_nav_thumbs_size',
			[
				'label'                 => __( 'Thumbnails Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ '%', 'px' ],
				'range'             => [
					'px' => [
						'max' => 200,
					],
				],
				'tablet_default'    => [
					'unit' => 'px',
				],
				'mobile_default'    => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonials-thumb-item img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'        => 'slideshow',
                    'thumbnail_nav' => 'yes',
                ],
			]
		);
        
        $this->add_responsive_control(
			'thumbnail_nav_thumbs_gap',
			[
				'label'                 => __( 'Thumbnails Gap', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
                    'size' => 10,
                    'unit' => 'px',
                ],
				'size_units'            => [ 'px', '%' ],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonials-thumb-item-wrap' => 'padding-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-testimonials-thumb-pagination' => 'margin-left: -{{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'        => 'slideshow',
                    'thumbnail_nav' => 'yes',
                ],
			]
		);
        
        $this->add_responsive_control(
			'thumbnail_nav_thumbs_arrow_size',
			[
				'label'                 => __( 'Arrow Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ '%', 'px' ],
				'range'             => [
					'px' => [
						'max' => 200,
					],
				],
				'tablet_default'    => [
					'unit' => 'px',
				],
				'mobile_default'    => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonials-wrap .pp-testimonials-thumb-item:before' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'        => 'slideshow',
                    'thumbnail_nav' => 'yes',
                ],
			]
		);
        
        $this->add_responsive_control(
			'thumbnail_nav_thumb_nav_spacing',
			[
				'label'                 => __( 'Top Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
                    'size' => 30,
                    'unit' => 'px',
                ],
				'size_units'            => [ 'px', '%' ],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonials-thumb-item' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'        => 'slideshow',
                    'thumbnail_nav' => 'yes',
                ],
			]
		);

        $this->start_controls_tabs( 'tabs_thumbnail_nav_style' );

        $this->start_controls_tab(
            'tab_thumbnail_nav_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
                    'layout'        => 'slideshow',
                    'thumbnail_nav' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'thumbnail_nav_grayscale_normal',
            [
                'label'                 => __( 'Grayscale', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'no',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'thumbnail_nav_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-testimonials-thumb-image',
                'condition'             => [
                    'layout'        => 'slideshow',
                    'thumbnail_nav' => 'yes',
                ],
			]
		);

		$this->add_control(
			'thumbnail_nav_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonials-thumb-image, {{WRAPPER}} .pp-testimonials-thumb-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'        => 'slideshow',
                    'thumbnail_nav' => 'yes',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'thumbnail_nav_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-testimonials-thumb-image',
                'condition'             => [
                    'layout'        => 'slideshow',
                    'thumbnail_nav' => 'yes',
                ],
			]
		);

        $this->add_control(
            'thumbnail_nav_scale_normal',
            [
                'label'                 => __( 'Scale', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'  => 0.5,
                        'max'  => 2,
                        'step' => 0.01,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-testimonials-thumb-image' => 'transform: scale({{SIZE}});',
                ],
                'condition'             => [
                    'layout'        => 'slideshow',
                    'thumbnail_nav' => 'yes',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_thumbnail_nav_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'layout'        => 'slideshow',
                    'thumbnail_nav' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'thumbnail_nav_grayscale_hover',
            [
                'label'                 => __( 'Grayscale', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'no',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
            ]
        );

		$this->add_control(
			'thumbnail_nav_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-testimonials-thumb-image:hover' => 'border-color: {{VALUE}}',
				],
				'condition'             => [
                    'layout'        => 'slideshow',
					'thumbnail_nav' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'thumbnail_nav_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-testimonials-thumb-image:hover',
			]
		);

        $this->add_control(
            'thumbnail_nav_scale_hover',
            [
                'label'                 => __( 'Scale', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'  => 0.5,
                        'max'  => 2,
                        'step' => 0.01,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-testimonials-thumb-image:hover' => 'transform: scale({{SIZE}});',
                ],
                'condition'             => [
                    'layout'        => 'slideshow',
                    'thumbnail_nav' => 'yes',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_thumbnail_nav_active',
            [
                'label'                 => __( 'Active', 'powerpack' ),
                'condition'             => [
                    'layout'        => 'slideshow',
                    'thumbnail_nav' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'thumbnail_nav_grayscale_active',
            [
                'label'                 => __( 'Grayscale', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'no',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
            ]
        );

		$this->add_control(
			'thumbnail_nav_border_color_active',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-active-slide .pp-testimonials-thumb-image' => 'border-color: {{VALUE}}',
				],
				'condition'             => [
                    'layout'        => 'slideshow',
					'thumbnail_nav' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'thumbnail_nav_box_shadow_active',
				'selector'              => '{{WRAPPER}} .pp-active-slide .pp-testimonials-thumb-image',
                'condition'             => [
                    'layout'        => 'slideshow',
                    'thumbnail_nav' => 'yes',
                ],
			]
		);

        $this->add_control(
            'thumbnail_nav_scale_active',
            [
                'label'                 => __( 'Scale', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'  => 0.5,
                        'max'  => 2,
                        'step' => 0.01,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-active-slide .pp-testimonials-thumb-image' => 'transform: scale({{SIZE}});',
                ],
                'condition'             => [
                    'layout'        => 'slideshow',
                    'thumbnail_nav' => 'yes',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
        
        $this->end_controls_section();
    }

	/**
	 * Slider Settings.
	 *
	 * @access public
	 */
	public function slider_settings() {
        $settings = $this->get_settings();
        
        if ( $settings['layout'] == 'carousel' ) {
            $slides_to_show = ( $settings['slides_per_view'] !== '' ) ? absint( $settings['slides_per_view'] ) : 3;
            $slides_to_show_tablet = ( $settings['slides_per_view_tablet'] !== '' ) ? absint( $settings['slides_per_view_tablet'] ) : 2;
            $slides_to_show_mobile = ( $settings['slides_per_view_mobile'] !== '' ) ? absint( $settings['slides_per_view_mobile'] ) : 1;
            
            $slides_to_scroll = ( $settings['slides_to_scroll'] !== '' ) ? absint( $settings['slides_to_scroll'] ) : 1;
            $slides_to_scroll_tablet = ( $settings['slides_to_scroll_tablet'] !== '' ) ? absint( $settings['slides_to_scroll_tablet'] ) : 1;
            $slides_to_scroll_mobile = ( $settings['slides_to_scroll_mobile'] !== '' ) ? absint( $settings['slides_to_scroll_mobile'] ) : 1;
        } else {
            $slides_to_show = 1;
            $slides_to_show_tablet = 1;
            $slides_to_show_mobile = 1;
            $slides_to_scroll = 1;
            $slides_to_scroll_tablet = 1;
            $slides_to_scroll_mobile = 1;
        }
        
        $slider_options = [
			'slidesToShow'           => $slides_to_show,
			'slidesToScroll'         => $slides_to_scroll,
			'autoplay'               => ( $settings['autoplay'] === 'yes' ),
			'autoplaySpeed'          => ( $settings['autoplay_speed'] !== '' ) ? $settings['autoplay_speed'] : 3000,
			'speed'                  => ( $settings['animation_speed'] !== '' ) ? $settings['animation_speed'] : 600,
			'fade'                   => ( $settings['effect'] === 'fade' && $settings['layout'] === 'slideshow' ),
			'vertical'               => ( $settings['orientation'] === 'vertical' ),
			'adaptiveHeight'         => false,
			'loop'                   => ( $settings['infinite_loop'] === 'yes' ),
		];

        if ( $settings['center_mode'] == 'yes' ) {
			$center_mode = true;
			$center_padding = ( $settings['center_padding']['size'] !== '' ) ? $settings['center_padding']['size'] . 'px' : '0px';
			$center_padding_tablet = ( $settings['center_padding_tablet']['size'] !== '' ) ? $settings['center_padding_tablet']['size'] . 'px' : '0px';
			$center_padding_mobile = ( $settings['center_padding_mobile']['size'] !== '' ) ? $settings['center_padding_mobile']['size'] . 'px' : '0px';
			
            $slider_options['centerMode'] = $center_mode;
            $slider_options['centerPadding'] = $center_padding;
        } else {
			$center_mode = false;
			$center_padding_tablet = '0px';
			$center_padding_mobile = '0px';
		}
        
        if ( $settings['arrows'] == 'yes' ) {
            if ( $settings['arrow'] ) {
                $pa_next_arrow = '<span class="' . $settings['arrow'] . '"></span>';
                $pa_prev_arrow = '<span class="' . str_replace( "right","left",$settings['arrow'] ) . '"></span>';
            }
            else {
                $pa_next_arrow = '<span class="fa fa-angle-right"></span>';
                $pa_prev_arrow = '<span class="fa fa-angle-left"></span>';
            }
            
            $pa_next_arrow = '<div class="pp-slider-arrow pp-arrow pp-arrow-next">' . $pa_next_arrow . '</div>';
            $pa_prev_arrow = '<div class="pp-slider-arrow pp-arrow pp-arrow-prev">' . $pa_prev_arrow . '</div>';
            
            $slider_options['arrows']       = true;
            $slider_options['prevArrow']    = $pa_prev_arrow;
            $slider_options['nextArrow']    = $pa_next_arrow;
        } else {
            $slider_options['arrows']       = false;
        }
        
        if ( $settings['layout'] == 'carousel' && $settings['dots'] == 'yes' ) {
            $slider_options['dots']     = true;
        } elseif ( $settings['layout'] == 'slideshow' && $settings['dots'] == 'yes' && $settings['thumbnail_nav'] != 'yes' ) {
            $slider_options['dots']     = true;
        } else {
            $slider_options['dots']     = false;
        }
		
		$elementor_bp_tablet	= get_option( 'elementor_viewport_lg' );
		$elementor_bp_mobile	= get_option( 'elementor_viewport_md' );
		$bp_tablet				= !empty($elementor_bp_tablet) ? $elementor_bp_tablet : 1025;
		$bp_mobile				= !empty($elementor_bp_mobile) ? $elementor_bp_mobile : 768;
        
        $slider_options['responsive'] = [
            [
                'breakpoint'    => $bp_tablet,
                'settings'      => [
                    'slidesToShow'      => $slides_to_show_tablet,
                    'slidesToScroll'    => $slides_to_scroll_tablet,
                    'centerMode'		=> $center_mode,
                    'centerPadding'		=> $center_padding_tablet,
                ],
            ],
            [
                'breakpoint'    => $bp_mobile,
                'settings'      => [
                    'slidesToShow'      => $slides_to_show_mobile,
                    'slidesToScroll'    => $slides_to_scroll_mobile,
                    'centerMode'		=> $center_mode,
                    'centerPadding'		=> $center_padding_mobile,
                ],
            ]
        ];
        
        $this->add_render_attribute(
			'testimonials',
			[
				'data-slider-settings' => wp_json_encode( $slider_options ),
			]
		);
    }
    
    protected function render_testimonial_footer( $item, $index ) {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="pp-testimonial-footer">
            <div class="pp-testimonial-footer-inner">
                <?php
                if ( $settings['image_position'] == 'stacked' ) {
                    $elements_order = array();

                    $elements_order['image'] = $settings['image_order'];
                    $elements_order['name'] = $settings['name_order'];
                    $elements_order['position'] = $settings['position_order'];
                    $elements_order['rating'] = $settings['rating_order'];

                    for ( $i = 0; $i <= 4; $i++ ) {
                        if ( $elements_order['image'] == $i ) {
                            $this->render_image( $item, $index );
                        }
                        
                        if ( $elements_order['name'] == $i ) {
                            $this->render_name( $item, $index );
                        }
                        
                        if ( $elements_order['position'] == $i ) {
                            $this->render_position( $item );
                        }
                        
                        if ( $elements_order['rating'] == $i ) {
                            $this->render_stars( $item, $settings );
                        }
                    }
                }
                else {
                    $this->render_image( $item );
                    ?>
                    <div class="pp-testimonial-cite">
                        <?php
                            $this->render_name( $item, $index );

                            $this->render_position( $item );
                    
                            $this->render_stars( $item, $settings );
                        ?>
                    </div>
                    <?php
                } ?>
            </div>
        </div>
        <?php
    }
    
    protected function render_testimonial_default( $item, $index ) {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="pp-testimonial-content">
            <?php
                $this->render_description( $item );
            ?>
        </div>
        <?php
        $this->render_testimonial_footer( $item, $index );
    }
    
    protected function render_testimonial_skin_2( $item, $index ) {
        $settings = $this->get_settings_for_display();
        
        $this->render_testimonial_footer( $item, $index );
        ?>
        <div class="pp-testimonial-content">
            <?php
                $this->render_description( $item );
            ?>
        </div>
        <?php
    }
    
    protected function render_testimonial_skin_3( $item, $index ) {
        $settings = $this->get_settings_for_display();
        
        $this->render_testimonial_footer( $item, $index );
        ?>
        <div class="pp-testimonial-content-wrap">
            <div class="pp-testimonial-content">
                <?php
                    $this->render_description( $item );
                ?>
            </div>
        </div>
        <?php
    }
    
    protected function render_testimonial_skin_4( $item, $index ) {
        $settings = $this->get_settings_for_display();
        
        $this->render_testimonial_footer( $item, $index );
        ?>
        <div class="pp-testimonial-content-wrap">
            <div class="pp-testimonial-content">
                <?php
                    $this->render_description( $item );
                ?>
            </div>
        </div>
        <?php
    }
    
    protected function render_testimonial_skin_5( $item, $index ) {
        $settings = $this->get_settings_for_display();
        
        $this->render_image( $item );
        ?>
        <div class="pp-testimonial-content-wrap">
            <div class="pp-testimonial-content">
                <?php
                    $this->render_description( $item );
                ?>
            </div>
            <div class="pp-testimonial-footer">
                <div class="pp-testimonial-footer-inner">
                    <div class="pp-testimonial-cite">
                        <?php
                            $this->render_name( $item, $index );

                            $this->render_position( $item );
                    
                            $this->render_stars( $item, $settings );
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    protected function render_testimonial_skin_8( $item, $index ) {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="pp-testimonial-content">
			<?php $this->render_image( $item ); ?>
        	<div class="pp-testimonial-content-wrap">
                <?php
                    $this->render_description( $item );

					$this->render_stars( $item, $settings );
                ?>
            </div>
        </div>
		<div class="pp-testimonial-footer">
			<div class="pp-testimonial-footer-inner">
				<div class="pp-testimonial-cite">
					<?php
						$this->render_name( $item, $index );

						$this->render_position( $item );
					?>
				</div>
			</div>
		</div>
        <?php
    }
    
    protected function render_image( $item ) {
        $settings = $this->get_settings_for_display();
        
        if ( $settings['show_image'] == '' ) {
            if ( $item['image']['url'] != '' ) {
                ?>
                <div class="pp-testimonial-image">
                    <?php
                        if ( $item['image']['id'] ) {
                            $image_url = Group_Control_Image_Size::get_attachment_image_src( $item['image']['id'], 'thumbnail', $settings );
                        } else {
                            $image_url = $item['image']['url'];
                        }

                        echo '<img src="' . $image_url . '" alt="' . esc_attr( Control_Media::get_image_alt( $item['image'] ) ) . '">';
                    ?>
                </div>
                <?php
            }
        }
    }
    
    protected function render_name( $item, $index ) {
        $settings = $this->get_settings_for_display();
        
        if ( $item['name'] == '' ) {
            return;
        }
        
        $member_key = $this->get_repeater_setting_key( 'name', 'testimonials', $index );
        $link_key = $this->get_repeater_setting_key( 'link', 'testimonials', $index );
        
        $this->add_render_attribute( $member_key, 'class', 'pp-testimonial-name' );
        
        printf( '<div class="pp-testimonial-name">%1$s</div>', $item['name'] );
    }
    
    protected function render_position( $item ) {
        $settings = $this->get_settings_for_display();
        
        if ( $item['position'] != '' ) {
            printf( '<div class="pp-testimonial-position">%1$s</div>', $item['position'] );
        }
    }
    
    protected function render_description( $item ) {
        $settings = $this->get_settings_for_display();
        if ( $item['content'] != '' ) { ?>
            <div class="pp-testimonial-text">
                <?php echo $this->parse_text_editor( $item['content'] ); ?>
            </div>
        <?php }
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'testimonials-wrap', 'class', 'pp-testimonials-wrap' );
        
        $this->add_render_attribute( [
            'testimonials' => [
                'class' => [
                    'pp-testimonials',
                    'pp-testimonials-' . $settings['layout'],
                    'pp-testimonials-image-' . $settings['image_position']
                ],
                'data-layout' => $settings['layout']
            ],
            'testimonial' => [
                'class' => [
                    'pp-testimonial',
                    'pp-testimonial-' . $settings['skin']
                ]
            ]
        ] );
        
        if ( $settings['layout'] == 'carousel' || $settings['layout'] == 'slideshow' ) {
            $this->add_render_attribute( 'testimonials', 'class', 'pp-slick-slider' );
        
            $this->slider_settings();
            
            $this->add_render_attribute( 'testimonial-wrap', 'class', 'pp-testimonial-slide' );
        } else {
            $this->add_render_attribute( [
                'testimonials' => [
                    'class' => 'pp-elementor-grid'
                ],
                'testimonial-wrap' => [
                    'class' => 'pp-grid-item-wrap'
                ],
                'testimonial' => [
                    'class' => 'pp-grid-item'
                ]
            ] );
        }
        
        if ($settings['layout'] == 'slideshow' && $settings['thumbnail_nav'] == 'yes' ) {
            if ( $settings['thumbnail_nav_grayscale_normal'] == 'yes' ) {
                $this->add_render_attribute( 'testimonials-wrap', 'class', 'pp-thumb-nav-gray' );
            }
            if ( $settings['thumbnail_nav_grayscale_hover'] == 'yes' ) {
                $this->add_render_attribute( 'testimonials-wrap', 'class', 'pp-thumb-nav-gray-hover' );
            }
            if ( $settings['thumbnail_nav_grayscale_active'] == 'yes' ) {
                $this->add_render_attribute( 'testimonials-wrap', 'class', 'pp-thumb-nav-gray-active' );
            }
        }
            
        $this->add_render_attribute( 'testimonial-outer', 'class', 'pp-testimonial-outer' );
        ?>
        <div <?php echo $this->get_render_attribute_string( 'testimonials-wrap' ); ?>>
            <div <?php echo $this->get_render_attribute_string( 'testimonials' ); ?>>
                <?php foreach ( $settings['testimonials'] as $index => $item ) : ?>
                    <div <?php echo $this->get_render_attribute_string( 'testimonial-wrap' ); ?>>
                        <div <?php echo $this->get_render_attribute_string( 'testimonial-outer' ); ?>>
                            <div <?php echo $this->get_render_attribute_string( 'testimonial' ); ?>>
                                <?php
                                    if ( $settings['skin'] == 'skin-2' ) {
                                        $this->render_testimonial_skin_2( $item, $index );
                                    } elseif ( $settings['skin'] == 'skin-3' ) {
                                        $this->render_testimonial_skin_3( $item, $index );
                                    } elseif ( $settings['skin'] == 'skin-4' ) {
                                        $this->render_testimonial_skin_4( $item, $index );
                                    } elseif ( $settings['skin'] == 'skin-5' || $settings['skin'] == 'skin-6' || $settings['skin'] == 'skin-7' ) {
                                        $this->render_testimonial_skin_5( $item, $index );
                                    } elseif ( $settings['skin'] == 'skin-8' ) {
                                        $this->render_testimonial_skin_8( $item, $index );
                                    } else {
                                        $this->render_testimonial_default( $item, $index );
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php
                if ( $settings['layout'] == 'slideshow' && $settings['thumbnail_nav'] == 'yes' ) {
                    $this->render_thumbnails();
                }
            ?>
        </div>
        <?php
    }
    
    protected function render_thumbnails() {
        $settings = $this->get_settings_for_display();
		$thumbnails = $settings['testimonials'];
        ?>
        <div class="pp-testimonials-thumb-pagination">
            <?php
                foreach ( $thumbnails as $index => $item ) {
                    if ( $item['image']['url'] ) {
                        if ( $item['image']['id'] ) {
                            $image_url = Group_Control_Image_Size::get_attachment_image_src( $item['image']['id'], 'thumbnail', $settings );
                        } else {
                            $image_url = $item['image']['url'];
                        }
                        ?>
                        <div class="pp-testimonials-thumb-item-wrap pp-grid-item-wrap">
                            <div class="pp-grid-item pp-testimonials-thumb-item">
                                <div class="pp-testimonials-thumb-image">
                                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( Control_Media::get_image_alt( $item['image'] ) ); ?>" />
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
            ?>
        </div>
        <?php
    }

	protected function render_stars( $item, $settings ) {
		$icon = '&#61445;';
        
        if ( ! empty( $item['rating'] ) ) {
            if ( 'star_fontawesome' === $settings['star_style'] ) {
                if ( 'outline' === $settings['unmarked_star_style'] ) {
                    $icon = '&#61446;';
                }
            } elseif ( 'star_unicode' === $settings['star_style'] ) {
                $icon = '&#9733;';

                if ( 'outline' === $settings['unmarked_star_style'] ) {
                    $icon = '&#9734;';
                }
            }

            $rating = (float) $item['rating'] > 5 ? 5 : $item['rating'];
            $floored_rating = (int) $rating;
            $stars_html = '';

            for ( $stars = 1; $stars <= 5; $stars++ ) {
                if ( $stars <= $floored_rating ) {
                    $stars_html .= '<i class="elementor-star-full">' . $icon . '</i>';
                } elseif ( $floored_rating + 1 === $stars && $rating !== $floored_rating ) {
                    $stars_html .= '<i class="elementor-star-' . ( $rating - $floored_rating ) * 10 . '">' . $icon . '</i>';
                } else {
                    $stars_html .= '<i class="elementor-star-empty">' . $icon . '</i>';
                }
            }

            echo '<div class="elementor-star-rating">' . $stars_html . '</div>';
        }
	}

    protected function _content_template() {
    }
}