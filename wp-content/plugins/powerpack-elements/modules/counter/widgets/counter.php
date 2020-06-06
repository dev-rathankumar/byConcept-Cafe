<?php
namespace PowerpackElements\Modules\Counter\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Scheme_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Counter Widget
 */
class Counter extends Powerpack_Widget {
    
    /**
	 * Retrieve counter widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return parent::get_widget_name( 'Counter' );
    }

    /**
	 * Retrieve counter widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
    public function get_title() {
        return parent::get_widget_title( 'Counter' );
    }

    /**
	 * Retrieve the list of categories the counter widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Counter' );
    }

    /**
	 * Retrieve counter widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
    public function get_icon() {
        return parent::get_widget_icon( 'Counter' );
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
		return parent::get_widget_keywords( 'Counter' );
	}

    /**
	 * Retrieve the list of scripts the counter widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_script_depends() {
        return [
            'waypoints',
            'odometer',
            'powerpack-frontend',
        ];
    }

    /**
	 * Retrieve the list of styles the counter widget depended on.
	 *
	 * Used to set style dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_style_depends() {
        return [
            'odometer'
        ];
    }

    /**
	 * Register counter widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
    protected function _register_controls() {
		
		/* Content Tab */
		$this->register_content_counter_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_counter_controls();
		$this->register_style_counter_icon_controls();
		$this->register_style_counter_number_controls();
		$this->register_style_number_prefix_controls();
		$this->register_style_number_suffix_controls();
		$this->register_style_title_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/
	
	protected function register_content_counter_controls() {
        
        /**
         * Content Tab: Counter
         */
        $this->start_controls_section(
            'section_counter',
            [
                'label'                 => __( 'Counter', 'powerpack' ),
            ]
        );
        
        $this->add_control(
            'icon_heading',
            [
                'label'                 => __( 'Icon', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
            ]
        );
        
        $this->add_control(
			'pp_icon_type',
			[
				'label'                 => esc_html__( 'Icon Type', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'toggle'				=> false,
				'options'               => [
					'none'        => [
						'title'   => esc_html__( 'None', 'powerpack' ),
						'icon'    => 'fa fa-ban',
					],
					'icon'        => [
						'title'   => esc_html__( 'Icon', 'powerpack' ),
						'icon'    => 'fa fa-star',
					],
					'image'       => [
						'title'   => esc_html__( 'Image', 'powerpack' ),
						'icon'    => 'fa fa-picture-o',
					],
				],
				'default'               => 'none',
			]
		);
		
		$this->add_control(
			'icon',
			[
				'label'					=> __( 'Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'fa4compatibility'		=> 'counter_icon',
				'default'				=> [
					'value'		=> 'fas fa-star',
					'library'	=> 'fa-solid',
				],
                'condition'             => [
                    'pp_icon_type'	=> 'icon',
                ],
			]
		);
        
        $this->add_control(
            'icon_image',
            [
                'label'                 => __( 'Image', 'powerpack' ),
                'type'                  => Controls_Manager::MEDIA,
                'default'               => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
				'condition'             => [
					'pp_icon_type'  => 'image',
				],
            ]
        );

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'image', // Usage: '{name}_size' and '{name}_custom_dimension', in this case 'image_size' and 'image_custom_dimension'.
				'default'               => 'full',
				'separator'             => 'none',
				'condition'             => [
					'pp_icon_type'  => 'image',
				],
			]
		);
        
        $this->add_control(
            'icon_divider',
            [
                'label'                 => __( 'Icon Divider', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'no',
                'label_on'              => __( 'On', 'powerpack' ),
                'label_off'             => __( 'Off', 'powerpack' ),
                'return_value'          => 'yes',
                'condition'             => [
                    'pp_icon_type!'		=> 'none',
                    'counter_layout'	=> ['layout-1', 'layout-2'],
                ],
            ]
        );
        
        $this->add_control(
            'number_heading',
            [
                'label'                 => __( 'Number', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
            ]
        );
        
        $this->add_control(
            'starting_number',
            [
                'label'                 => __( 'Starting Number', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
				'dynamic'               => [
					'active'   => true,
				],
                'default'               => 0,
            ]
        );
        
        $this->add_control(
            'ending_number',
            [
                'label'                 => __( 'Ending Number', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
				'dynamic'               => [
					'active'   => true,
				],
                'default'               => 250,
            ]
        );
        
        $this->add_control(
            'number_prefix',
            [
                'label'                 => __( 'Number Prefix', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
            ]
        );
        
        $this->add_control(
            'number_suffix',
            [
                'label'                 => __( 'Number Suffix', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
            ]
        );
        
        $this->add_control(
            'num_divider',
            [
                'label'                 => __( 'Number Divider', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'no',
                'label_on'              => __( 'On', 'powerpack' ),
                'label_off'             => __( 'Off', 'powerpack' ),
                'return_value'          => 'yes',
                'condition'             => [
                    'counter_layout'    => [
                        'layout-1',
                        'layout-3',
                        'layout-5',
                        'layout-6'
                    ],
                ],
            ]
        );
        
        $this->add_control(
            'title_heading',
            [
                'label'                 => __( 'Title', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
            ]
        );

        $this->add_control(
            'counter_title',
            [
                'label'                 => __( 'Title', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
                'default'               => __( 'Counter Title', 'powerpack' ),
            ]
        );
        
        $this->add_control(
            'title_html_tag',
            [
                'label'                => __( 'Title HTML Tag', 'powerpack' ),
                'type'                 => Controls_Manager::SELECT,
                'default'              => 'div',
                'options'              => [
                    'h1'     => __( 'H1', 'powerpack' ),
                    'h2'     => __( 'H2', 'powerpack' ),
                    'h3'     => __( 'H3', 'powerpack' ),
                    'h4'     => __( 'H4', 'powerpack' ),
                    'h5'     => __( 'H5', 'powerpack' ),
                    'h6'     => __( 'H6', 'powerpack' ),
                    'div'    => __( 'div', 'powerpack' ),
                    'span'   => __( 'span', 'powerpack' ),
                    'p'      => __( 'p', 'powerpack' ),
                ],
                'condition'             => [
                    'counter_title!'    => '',
                ],
            ]
        );

        $this->add_control(
            'counter_subtitle',
            [
                'label'                 => __( 'Subtitle', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
                'default'               => '',
            ]
        );
        
        $this->add_control(
            'subtitle_html_tag',
            [
                'label'                => __( 'Subtitle HTML Tag', 'powerpack' ),
                'type'                 => Controls_Manager::SELECT,
                'default'              => 'div',
                'options'              => [
                    'h1'     => __( 'H1', 'powerpack' ),
                    'h2'     => __( 'H2', 'powerpack' ),
                    'h3'     => __( 'H3', 'powerpack' ),
                    'h4'     => __( 'H4', 'powerpack' ),
                    'h5'     => __( 'H5', 'powerpack' ),
                    'h6'     => __( 'H6', 'powerpack' ),
                    'div'    => __( 'div', 'powerpack' ),
                    'span'   => __( 'span', 'powerpack' ),
                    'p'      => __( 'p', 'powerpack' ),
                ],
                'condition'             => [
                    'counter_subtitle!' => '',
                ],
            ]
        );
		
		
		$layouts = array();
		for ($x = 1; $x <= 10; $x++) {
			$layouts['layout-' . $x] = __( 'Layout', 'powerpack' ) . ' ' . $x;
		}
        
        $this->add_control(
            'counter_layout',
            [
                'label'                => __( 'Layout', 'powerpack' ),
                'type'                 => Controls_Manager::SELECT,
                'default'              => 'layout-1',
                'options'              => $layouts,
                'separator'             => 'before',
            ]
        );
        
        $this->add_control(
            'counter_speed',
            [
                'label'                 => __( 'Counter Speed', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [ 'size' => 1500 ],
                'range'                 => [
                    'px' => [
                        'min'   => 100,
                        'max'   => 2000,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => '',
                'separator'             => 'before',
            ]
        );

        $this->end_controls_section();
	}

	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links('Counter');

		if ( !empty($help_docs) ) {
			/**
			 * Content Tab: Docs Links
			 *
			 * @since 1.4.8
			 * @access protected
			 */
			$this->start_controls_section(
				'section_help_docs',
				[
					'label' => __( 'Help Docs', 'powerpack' ),
				]
			);
			
			$hd_counter = 1;
			foreach( $help_docs as $hd_title => $hd_link ) {
				$this->add_control(
					'help_doc_' . $hd_counter,
					[
						'type'            => Controls_Manager::RAW_HTML,
						'raw'             => sprintf( '%1$s ' . $hd_title . ' %2$s', '<a href="' . $hd_link . '" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'pp-editor-doc-links',
					]
				);

				$hd_counter++;
			}

			$this->end_controls_section();
		}
	}

	/*-----------------------------------------------------------------------------------*/
	/*	STYLE TAB
	/*-----------------------------------------------------------------------------------*/
	
	protected function register_style_counter_controls() {
        /**
         * Style Tab: Counter
         */
        $this->start_controls_section(
            'section_counter_style',
            [
                'label'                 => __( 'Counter', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_responsive_control(
			'counter_align',
			[
				'label'                 => __( 'Alignment', 'powerpack' ),
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
				'selectors'             => [
					'{{WRAPPER}} .pp-counter-container'   => 'text-align: {{VALUE}};',
				],
			]
		);

        $this->end_controls_section();
	}
	
	protected function register_style_counter_icon_controls() {
        /**
         * Style Tab: Icon
         */
        $this->start_controls_section(
            'section_counter_icon_style',
            [
                'label'                 => __( 'Icon', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'pp_icon_type!' => 'none',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'                  => 'counter_icon_bg',
                'label'                 => __( 'Background', 'powerpack' ),
                'types'                 => [ 'none','classic','gradient' ],
                'condition'             => [
                    'pp_icon_type!' => 'none',
                ],
                'selector'              => '{{WRAPPER}} .pp-counter-icon',
            ]
        );

        $this->add_control(
            'counter_icon_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .pp-counter-icon svg' => 'fill: {{VALUE}};',
                ],
                'condition'             => [
                    'pp_icon_type'  => 'icon',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'counter_icon_size',
            [
                'label'                 => __( 'Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 5,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', 'em' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-icon' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'pp_icon_type'  => 'icon',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'counter_icon_img_width',
            [
                'label'                 => __( 'Image Width', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 10,
                        'max'   => 500,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => ['px', '%'],
				'default'               => [
					'unit' => 'px',
					'size' => 80,
				],
                'condition'             => [
                    'pp_icon_type'  => 'image',
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-icon img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'counter_icon_rotation',
            [
                'label'                 => __( 'Rotation', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 360,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => '',
                'condition'             => [
                    'pp_icon_type!' => 'none',
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-icon .fa, {{WRAPPER}} .pp-counter-icon img' => 'transform: rotate( {{SIZE}}deg );',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'counter_icon_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-counter-icon',
                'condition'             => [
                    'pp_icon_type!' => 'none',
                ],
			]
		);

		$this->add_control(
			'counter_icon_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-counter-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'pp_icon_type!' => 'none',
                ],
			]
		);

		$this->add_responsive_control(
			'counter_icon_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'placeholder'           => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-counter-icon' => 'padding-top: {{TOP}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
				],
                'condition'             => [
                    'pp_icon_type!' => 'none',
                ],
			]
		);

		$this->add_responsive_control(
			'counter_icon_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'placeholder'           => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-counter-icon-wrap' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
                'condition'             => [
                    'pp_icon_type!' => 'none',
                ],
			]
		);
        
        $this->add_control(
            'icon_divider_heading',
            [
                'label'                 => __( 'Icon Divider', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'pp_icon_type!' => 'none',
                    'icon_divider'  => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'icon_divider_type',
            [
            'label'                     => __( 'Divider Type', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'solid',
                'options'               => [
                    'solid'     => __( 'Solid', 'powerpack' ),
                    'double'    => __( 'Double', 'powerpack' ),
                    'dotted'    => __( 'Dotted', 'powerpack' ),
                    'dashed'    => __( 'Dashed', 'powerpack' ),
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-icon-divider' => 'border-bottom-style: {{VALUE}}',
                ],
                'condition'             => [
                    'pp_icon_type!' => 'none',
                    'icon_divider'  => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'icon_divider_height',
            [
                'label'                 => __( 'Height', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'  => 2,
                ],
                'range'                 => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 20,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-icon-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'pp_icon_type!' => 'none',
                    'icon_divider'  => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'icon_divider_width',
            [
                'label'                 => __( 'Width', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'  => 30,
                ],
                'range'                 => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 1000,
                        'step'  => 1,
                    ],
                    '%' => [
                        'min'   => 1,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-icon-divider' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'pp_icon_type!' => 'none',
                    'icon_divider'  => 'yes',
                ],
            ]
        );

        $this->add_control(
            'icon_divider_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-icon-divider' => 'border-bottom-color: {{VALUE}}',
                ],
                'condition'             => [
                    'pp_icon_type!' => 'none',
                    'icon_divider'  => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'icon_divider_margin',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                    '%' => [
                        'min'   => 0,
                        'max'   => 30,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-icon-divider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'pp_icon_type!' => 'none',
                    'icon_divider'  => 'yes',
                ],
            ]
        );
        
        $this->end_controls_section();
	}
	
	protected function register_style_counter_number_controls() {
        /**
         * Style Tab: Number
         */
        $this->start_controls_section(
            'section_counter_num_style',
            [
                'label'                 => __( 'Number', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'counter_num_color',
            [
                'label'                 => __( 'Number Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-number' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'counter_num_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-counter-number-wrap',
            ]
        );

		$this->add_responsive_control(
			'counter_num_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'placeholder'           => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-counter-number-wrap' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);
        
        $this->add_control(
            'num_divider_heading',
            [
                'label'                 => __( 'Number Divider', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'num_divider'       => 'yes',
                    'counter_layout'    => [
                        'layout-1',
                        'layout-3',
                        'layout-5',
                        'layout-6'
                    ],
                ],
            ]
        );
        
        $this->add_control(
            'num_divider_type',
            [
                'label'                 => __( 'Divider Type', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'solid',
                'options'               => [
                    'solid'     => __( 'Solid', 'powerpack' ),
                    'double'    => __( 'Double', 'powerpack' ),
                    'dotted'    => __( 'Dotted', 'powerpack' ),
                    'dashed'    => __( 'Dashed', 'powerpack' ),
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-num-divider' => 'border-bottom-style: {{VALUE}}',
                ],
                'condition'             => [
                    'num_divider'       => 'yes',
                    'counter_layout'    => [
                        'layout-1',
                        'layout-3',
                        'layout-5',
                        'layout-6'
                    ],
                ],
            ]
        );
        
        $this->add_responsive_control(
            'num_divider_height',
            [
                'label'                 => __( 'Height', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'  => 2,
                ],
                'range'                 => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 20,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-num-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'num_divider'       => 'yes',
                    'counter_layout'    => [
                        'layout-1',
                        'layout-3',
                        'layout-5',
                        'layout-6'
                    ],
                ],
            ]
        );
        
        $this->add_responsive_control(
            'num_divider_width',
            [
                'label'                 => __( 'Width', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'  => 30,
                ],
                'range'                 => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 1000,
                        'step'  => 1,
                    ],
                    '%' => [
                        'min'   => 1,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-num-divider' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'num_divider'       => 'yes',
                    'counter_layout'    => [
                        'layout-1',
                        'layout-3',
                        'layout-5',
                        'layout-6'
                    ],
                ],
            ]
        );

        $this->add_control(
            'num_divider_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-num-divider' => 'border-bottom-color: {{VALUE}}',
                ],
                'condition'             => [
                    'num_divider'       => 'yes',
                    'counter_layout'    => [
                        'layout-1',
                        'layout-3',
                        'layout-5',
                        'layout-6'
                    ],
                ],
            ]
        );
        
        $this->add_responsive_control(
            'num_divider_margin',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                    '%' => [
                        'min'   => 0,
                        'max'   => 30,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-num-divider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'num_divider'       => 'yes',
                    'counter_layout'    => [
                        'layout-1',
                        'layout-3',
                        'layout-5',
                        'layout-6'
                    ],
                ],
            ]
        );
        
        $this->end_controls_section();
	}
	
	protected function register_style_number_prefix_controls() {
        /**
         * Style Tab: Prefix
         */
        $this->start_controls_section(
            'section_number_prefix_style',
            [
                'label'                 => __( 'Prefix', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'number_prefix!' => '',
                ],
            ]
        );

        $this->add_control(
            'number_prefix_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-number-prefix' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'number_prefix!' => '',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'number_prefix_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-counter-number-prefix',
                'condition'             => [
                    'number_prefix!' => '',
                ],
            ]
        );
        
        $this->end_controls_section();
	}
	
	protected function register_style_number_suffix_controls() {
        /**
         * Style Tab: Suffix
         */
        $this->start_controls_section(
            'section_number_suffix_style',
            [
                'label'                 => __( 'Suffix', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'number_suffix!' => '',
                ],
            ]
        );

        $this->add_control(
            'section_number_suffix_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-number-suffix' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'number_suffix!' => '',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'section_number_suffix_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-counter-number-suffix',
                'condition'             => [
                    'number_suffix!' => '',
                ],
            ]
        );
        
        $this->end_controls_section();
	}
	
	protected function register_style_title_controls() {
		/**
         * Style Tab: Title
         */
        $this->start_controls_section(
            'section_counter_title_style',
            [
                'label'                 => __( 'Title', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'counter_title!' => '',
                ],
            ]
        );

        $this->add_control(
            'counter_title_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-title-wrap' => 'background-color: {{VALUE}};',
                ],
                'condition'             => [
                    'counter_title!' => '',
                ],
            ]
        );
        
        $this->add_control(
            'title_style_heading',
            [
                'label'                 => __( 'Title', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'counter_title!' => '',
                ],
            ]
        );

        $this->add_control(
            'counter_title_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-title' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'counter_title!' => '',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'counter_title_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-counter-title',
                'condition'             => [
                    'counter_title!' => '',
                ],
            ]
        );
        
        $this->add_control(
            'subtitle_style_heading',
            [
                'label'                 => __( 'Subtitle', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'counter_subtitle!' => '',
                ],
            ]
        );

        $this->add_control(
            'counter_subtitle_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-counter-subtitle' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'counter_subtitle!' => '',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'counter_subtitle_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-counter-subtitle',
                'condition'             => [
                    'counter_subtitle!' => '',
                ],
            ]
        );

		$this->add_responsive_control(
			'counter_title_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'placeholder'           => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-counter-title-wrap' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
                'separator'             => 'before',
                'condition'             => [
                    'counter_title!' => '',
                ],
			]
		);

		$this->add_responsive_control(
			'counter_title_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'placeholder'           => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-counter-title-wrap' => 'padding-top: {{TOP}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
				],
                'condition'             => [
                    'counter_title!' => '',
                ],
			]
		);
        
        $this->end_controls_section();
    }

    /**
	 * Render counter widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
		$starting_number =  ( $settings['starting_number'] ) ? $settings['starting_number'] : 0;
        $ending_number =  ( $settings['ending_number'] ) ? $settings['ending_number'] : 250;
        $counter_speed =  ( $settings['counter_speed']['size'] ) ? $settings['counter_speed']['size'] : 1500;
		
        $this->add_render_attribute([
			'counter' => [
				'class' => [
					'pp-counter pp-counter-'.esc_attr( $this->get_id() ),
					'pp-counter-' . $settings['counter_layout']
				],
				'data-target' => '.pp-counter-number-'.esc_attr( $this->get_id() )
			],
			'counter-number'	=> [
				'class'			=> 'pp-counter-number pp-counter-number-'.esc_attr( $this->get_id() ),
				'data-from'		=> $starting_number,
				'data-to'		=> $ending_number,
				'data-speed'	=> $counter_speed,
			]
		]);
        ?>
        <div class="pp-counter-container">
            <div <?php echo $this->get_render_attribute_string( 'counter' ); ?>>
                <?php if ( $settings['counter_layout'] == 'layout-1' || $settings['counter_layout'] == 'layout-5' || $settings['counter_layout'] == 'layout-6' ) { ?>
                    <?php
                        // Counter Icon
                        $this->render_icon();
                    ?>
                
                    <div class="pp-counter-number-title-wrap">
                        <?php
                            // Counter Number
                            $this->render_counter_number();
                        ?>

                        <?php if ( $settings['num_divider'] == 'yes' ) { ?>
                            <div class="pp-counter-num-divider-wrap">
                                <span class="pp-counter-num-divider"></span>
                            </div>
                        <?php } ?>

                        <?php
                            // Counter Title
                            $this->render_counter_title();
                        ?>
                    </div>
                <?php } elseif ( $settings['counter_layout'] == 'layout-2' ) { ?>
                    <?php
                        // Counter Icon
                        $this->render_icon();

                        // Counter Title
                        $this->render_counter_title();
            
                        // Counter Number
                        $this->render_counter_number();
            
            
                        if ( $settings['num_divider'] == 'yes' ) { ?>
                            <div class="pp-counter-num-divider-wrap">
                                <span class="pp-counter-num-divider"></span>
                            </div>
                            <?php
                        }
                    } elseif ( $settings['counter_layout'] == 'layout-3' ) {
            
                        // Counter Number
                        $this->render_counter_number();
                        ?>

                    <?php if ( $settings['num_divider'] == 'yes' ) { ?>
                        <div class="pp-counter-num-divider-wrap">
                            <span class="pp-counter-num-divider"></span>
                        </div>
                    <?php } ?>
                
                    <div class="pp-icon-title-wrap">
                        <?php
                            // Counter Icon
                            $this->render_icon();
            
                            // Counter Title
                            $this->render_counter_title();
                        ?>
                    </div>
                <?php } elseif ( $settings['counter_layout'] == 'layout-4' ) { ?>
                    <div class="pp-icon-title-wrap">
                        <?php
                            // Counter Icon
                            $this->render_icon();
            
                            // Counter Title
                            $this->render_counter_title();
                        ?>
                    </div>
                
                    <?php
                        // Counter Number
                        $this->render_counter_number();
                    ?>

                    <?php if ( $settings['num_divider'] == 'yes' ) { ?>
                        <div class="pp-counter-num-divider-wrap">
                            <span class="pp-counter-num-divider"></span>
                        </div>
                    <?php }
                    } elseif ( $settings['counter_layout'] == 'layout-7' || $settings['counter_layout'] == 'layout-8' ) {

                        // Counter Number
                        $this->render_counter_number();
                        ?>
                
                        <div class="pp-icon-title-wrap">
                            <?php
                                // Counter Icon
                                $this->render_icon();

                                // Counter Title
                                $this->render_counter_title();
                            ?>
                        </div>
                <?php } elseif ( $settings['counter_layout'] == 'layout-9' ) {
                        ?>
                        <div class="pp-icon-number-wrap">
                            <?php
                                // Counter Icon
                                $this->render_icon();

								// Counter Number
								$this->render_counter_number();
							?>
						</div>
						<?php
							// Counter Title
							$this->render_counter_title();
						?>
                <?php } elseif ( $settings['counter_layout'] == 'layout-10' ) {
                        ?>
                        <div class="pp-icon-number-wrap">
                            <?php
								// Counter Number
								$this->render_counter_number();

                                // Counter Icon
                                $this->render_icon();
							?>
						</div>
						<?php
							// Counter Title
							$this->render_counter_title();
						?>
                <?php } ?>
            </div>
        </div><!-- .pp-counter-container -->
        <?php
    }
    
    /**
	 * Render counter icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    private function render_icon() {
        $settings = $this->get_settings_for_display();
		
		if ( ! isset( $settings['counter_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['counter_icon'] = 'fa fa-star';
		}

		$has_icon = ! empty( $settings['counter_icon'] );
		
		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['counter_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}

		$icon_attributes = $this->get_render_attribute_string( 'counter_icon' );
		
		if ( ! $has_icon && ! empty( $settings['icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['icon'] );
		$is_new = ! isset( $settings['counter_icon'] ) && Icons_Manager::is_migration_allowed();
        
        if ( $settings['pp_icon_type'] == 'icon' ) { ?>
			<span class="pp-counter-icon-wrap">
				<span class="pp-counter-icon pp-icon">
					<?php
					if ( $is_new || $migrated ) {
						Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );
					} elseif ( ! empty( $settings['counter_icon'] ) ) {
						?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
					}
					?>
				</span>
			</span>
            <?php
        } elseif ( $settings['pp_icon_type'] == 'image' ) {
            $image = $settings['icon_image'];
            if ( $image['url'] ) { ?>
                <span class="pp-counter-icon-wrap">
                    <span class="pp-counter-icon pp-counter-icon-img">
                        <?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'image', 'icon_image' ); ?>
                    </span>
                </span>
            <?php }
        }

        if ( $settings['icon_divider'] == 'yes' ) {
            if ( $settings['counter_layout'] == 'layout-1' || $settings['counter_layout'] == 'layout-2' ) { ?>
                <div class="pp-counter-icon-divider-wrap">
                    <span class="pp-counter-icon-divider"></span>
                </div>
                <?php
            }
        }
    }
    
    /**
	 * Render counter number output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    private function render_counter_number() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="pp-counter-number-wrap">
            <?php
                if ( $settings['number_prefix'] != '' ) {
                    printf( '<span class="pp-counter-number-prefix">%1$s</span>', $settings['number_prefix'] );
                }
            ?>
            <div <?php echo $this->get_render_attribute_string( 'counter-number' ); ?>>
                <?php
					$starting_number =  ( $settings['starting_number'] ) ? $settings['starting_number'] : 0;
					echo $starting_number;
				?>
            </div>
            <?php
                if ( $settings['number_suffix'] != '' ) {
                    printf( '<span class="pp-counter-number-suffix">%1$s</span>', $settings['number_suffix'] );
                }
            ?>
        </div>
        <?php
    }
    
    /**
	 * Render counter title output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    private function render_counter_title() {
        $settings = $this->get_settings_for_display();
        
        if ( $settings['counter_title'] || $settings['counter_subtitle'] ) {
            $this->add_inline_editing_attributes( 'counter_title', 'none' );
            $this->add_render_attribute( 'counter_title', 'class', 'pp-counter-title' );
            $this->add_inline_editing_attributes( 'counter_subtitle', 'none' );
            $this->add_render_attribute( 'counter_subtitle', 'class', 'pp-counter-subtitle' );
            ?>
            <div class="pp-counter-title-wrap">
                <?php
                if ( $settings['counter_title'] ) {
                    printf( '<%1$s %2$s>', $settings['title_html_tag'], $this->get_render_attribute_string( 'counter_title' ) );
                    echo $settings['counter_title'];
                    printf( '</%1$s>', $settings['title_html_tag'] );
                }
                if ( $settings['counter_subtitle'] ) {
                    printf( '<%1$s %2$s>', $settings['subtitle_html_tag'], $this->get_render_attribute_string( 'counter_subtitle' ) );
                    echo $settings['counter_subtitle'];
                    printf( '</%1$s>', $settings['subtitle_html_tag'] );
                }
                ?>
            </div>
            <?php
        }
    }

    /**
	 * Render counter widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
    protected function _content_template() {
        ?>
		<#
		   function icon_template() {
			   var iconHTML = elementor.helpers.renderIcon( view, settings.icon, { 'aria-hidden': true }, 'i' , 'object' ),
					migrated = elementor.helpers.isIconMigrated( settings, 'icon' );
		   
			   if ( settings.pp_icon_type == 'icon' ) {
		   			if ( settings.counter_icon || settings.icon ) { #>
						<span class="pp-counter-icon-wrap">
							<span class="pp-counter-icon pp-icon">
								<# if ( iconHTML && iconHTML.rendered && ( ! settings.counter_icon || migrated ) ) { #>
								{{{ iconHTML.value }}}
								<# } else { #>
									<i class="{{ settings.counter_icon }}" aria-hidden="true"></i>
								<# } #>
							</span>
						</span>
						<#
					}
				} else if ( settings.pp_icon_type == 'image' ) {
					if ( settings.icon_image.url != '' ) { #>
						<span class="pp-counter-icon-wrap">
							<span class="pp-counter-icon pp-counter-icon-img">
								<#
								var image = {
									id: settings.icon_image.id,
									url: settings.icon_image.url,
									size: settings.image_size,
									dimension: settings.image_custom_dimension,
									model: view.getEditModel()
								};
								var image_url = elementor.imagesManager.getImageUrl( image );
								#>
								<img src="{{{ image_url }}}" />
							</span>
						</span>
						<#
					}
				}
						   
				if ( settings.icon_divider == 'yes' ) {
					if ( settings.counter_layout == 'layout-1' || settings.counter_layout == 'layout-2' ) { #>
						<div class="pp-counter-icon-divider-wrap">
							<span class="pp-counter-icon-divider"></span>
						</div>
						<#
					}
				}
			}
						   
			function number_template() { #>
				<div class="pp-counter-number-wrap">
					<#
						if ( settings.number_prefix != '' ) {
							var prefix = settings.number_prefix;

							view.addRenderAttribute( 'prefix', 'class', 'pp-counter-number-prefix' );

							var prefix_html = '<span' + ' ' + view.getRenderAttributeString( 'prefix' ) + '>' + prefix + '</span>';

							print( prefix_html );
						}
					#>
					<div class="pp-counter-number" data-from="{{ settings.starting_number }}" data-to="{{ settings.ending_number }}" data-speed="{{ settings.counter_speed.size }}">
						{{{ settings.starting_number }}}
					</div>
					<#
						if ( settings.number_suffix != '' ) {
							var suffix = settings.number_suffix;

							view.addRenderAttribute( 'suffix', 'class', 'pp-counter-number-suffix' );

							var suffix_html = '<span' + ' ' + view.getRenderAttributeString( 'suffix' ) + '>' + suffix + '</span>';

							print( suffix_html );
						}
					#>
				</div>
				<#
			}

			function title_template() {
				if ( settings.counter_title != '' ) {
					var title = settings.counter_title;

					view.addRenderAttribute( 'counter_title', 'class', 'pp-counter-title' );

					view.addInlineEditingAttributes( 'counter_title' );

					var title_html = '<' + settings.title_html_tag  + ' ' + view.getRenderAttributeString( 'counter_title' ) + '>' + title + '</' + settings.title_html_tag + '>';

					print( title_html );
				}
			}
		#>

        <div class="pp-counter-container">
            <div class="pp-counter pp-counter-{{ settings.counter_layout }}" data-target=".pp-counter-number">
                <# if ( settings.counter_layout == 'layout-1' || settings.counter_layout == 'layout-5' || settings.counter_layout == 'layout-6' ) { #>
                    <# icon_template(); #>
                
                    <div class="pp-counter-number-title-wrap">
						<# number_template(); #>

                        <# if ( settings.num_divider == 'yes' ) { #>
                            <div class="pp-counter-num-divider-wrap">
                                <span class="pp-counter-num-divider"></span>
                            </div>
                        <# } #>

						<# title_template(); #>
                    </div>
                <# } else if ( settings.counter_layout == 'layout-2' ) { #>
					<# icon_template(); #>
					<# title_template(); #>
					<# number_template(); #>

                    <# if ( settings.num_divider == 'yes' ) { #>
                        <div class="pp-counter-num-divider-wrap">
                            <span class="pp-counter-num-divider"></span>
                        </div>
                    <# } #>
                <# } else if ( settings.counter_layout == 'layout-3' ) { #>
					<# number_template(); #>

                    <# if ( settings.num_divider == 'yes' ) { #>
                        <div class="pp-counter-num-divider-wrap">
                            <span class="pp-counter-num-divider"></span>
                        </div>
                    <# } #>
                
                    <div class="pp-icon-title-wrap">
						<# icon_template(); #>
						<# title_template(); #>
                    </div>
                <# } else if ( settings.counter_layout == 'layout-4' ) { #>
                    <div class="pp-icon-title-wrap">
						<# icon_template(); #>
						<# title_template(); #>
                    </div>

					<# number_template(); #>

                    <# if ( settings.num_divider == 'yes' ) { #>
                        <div class="pp-counter-num-divider-wrap">
                            <span class="pp-counter-num-divider"></span>
                        </div>
                    <# } #>
                <# } else if ( settings.counter_layout == 'layout-7' || settings.counter_layout == 'layout-8' ) { #>
					<# number_template(); #>
                    
                    <div class="pp-icon-title-wrap">
						<# icon_template(); #>
						<# title_template(); #>
                    </div>
                <# } else if ( settings.counter_layout == 'layout-9' ) { #>
					<div class="pp-icon-number-wrap">
						<# icon_template(); #>
						<# number_template(); #>
					</div>

					<# title_template(); #>
                <# } else if ( settings.counter_layout == 'layout-10' ) { #>
					<div class="pp-icon-number-wrap">
						<#
						   number_template();
						   icon_template();
						#>
					</div>

					<# title_template(); #>
                <# } #>
            </div>
        </div>
        <?php
    }
}