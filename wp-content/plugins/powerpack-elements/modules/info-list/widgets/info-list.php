<?php
namespace PowerpackElements\Modules\InfoList\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Info List Widget
 */
class Info_List extends Powerpack_Widget {
    
    /**
	 * Retrieve info list widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return parent::get_widget_name( 'Info_List' );
    }

    /**
	 * Retrieve info list widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
    public function get_title() {
        return parent::get_widget_title( 'Info_List' );
    }

    /**
	 * Retrieve the list of categories the info list widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Info_List' );
    }

    /**
	 * Retrieve info list widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
    public function get_icon() {
        return parent::get_widget_icon( 'Info_List' );
    }

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.4.13.1
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Info_List' );
	}
    
    /**
	 * Retrieve the list of scripts the instagram feed widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_script_depends() {
        return [
            'powerpack-frontend'
        ];
    }

    /**
	 * Register info list widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
    protected function _register_controls() {
		
		/* Content Tab */
		$this->register_content_list_items_controls();
		$this->register_content_help_docs_controls();
		
		/* Style Tab */
		$this->register_style_list_controls();
		$this->register_style_connector_controls();
		$this->register_style_icon_controls();
		$this->register_style_title_controls();
		$this->register_style_button_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/
        
	protected function register_content_list_items_controls() {
        /**
         * Content Tab: List Items
         */
        $this->start_controls_section(
            'section_list',
            [
                'label'                 => __( 'List Items', 'powerpack' ),
            ]
        );
        
        $this->add_control(
			'list_items',
			[
				'label'                 => '',
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[
						'text'      => __( 'List Item #1', 'powerpack' ),
                        'icon' => __('fa fa-check','powerpack')
					],
                    [
						'text'      => __( 'List Item #2', 'powerpack' ),
                        'icon' => __('fa fa-check','powerpack')
					],
				],
				'fields'                => [
					[
						'name'        => 'text',
						'label'       => __( 'Title', 'powerpack' ),
						'label_block' => true,
						'type'        => Controls_Manager::TEXT,
                        'dynamic'     => [
                            'active'  => true,
                        ],
                        'default'     => __('List Item #1','powerpack')
					],
					[
						'name'        => 'description',
						'label'       => __( 'Description', 'powerpack' ),
						'label_block' => true,
						'type'        => Controls_Manager::TEXTAREA,
                        'dynamic'     => [
                            'active'  => true,
                        ],
                        'default'     => __('List Item Description','powerpack')
					],
                    [
						'name'        => 'pp_icon_type',
						'label'       => esc_html__( 'Icon Type', 'powerpack' ),
                        'type'        => Controls_Manager::CHOOSE,
                        'label_block' => false,
                        'options'     => [
                            'none' => [
                                'title' => esc_html__( 'None', 'powerpack' ),
                                'icon'  => 'fa fa-ban',
                            ],
                            'icon' => [
                                'title' => esc_html__( 'Icon', 'powerpack' ),
                                'icon'  => 'fa fa-gear',
                            ],
                            'image' => [
                                'title' => esc_html__( 'Image', 'powerpack' ),
                                'icon'  => 'fa fa-picture-o',
                            ],
                            'text' => [
                                'title' => esc_html__( 'Text', 'powerpack' ),
                                'icon'  => 'fa fa-hashtag',
                            ],
                        ],
                        'default'       => 'icon',
					],
					[
						'name'				=> 'icon',
						'label'				=> __( 'Icon', 'powerpack' ),
						'type'				=> Controls_Manager::ICONS,
						'label_block'		=> true,
						'default'			=> [
							'value'		=> 'fas fa-check',
							'library'	=> 'fa-solid',
						],
						'fa4compatibility'	=> 'list_icon',
						'condition'         => [
							'pp_icon_type'  => 'icon',
						],
					],
                    [
						'name'              => 'list_image',
						'label'             => __( 'Image', 'powerpack' ),
						'label_block'       => true,
						'type'              => Controls_Manager::MEDIA,
				        'default'           => [
                            'url' => Utils::get_placeholder_image_src(),
                         ],
				        'condition'         => [
                            'pp_icon_type' => 'image',
                        ],
					],
					[
						'name'              => 'icon_text',
						'label'             => __( 'Icon Text', 'powerpack' ),
						'label_block'       => false,
						'type'              => Controls_Manager::TEXT,
                        'default'           => __('1','powerpack'),
				        'condition'         => [
                            'pp_icon_type' => 'text',
                        ],
					],
                    [
						'name'              => 'link_type',
                        'label'             => __( 'Link Type', 'powerpack' ),
                        'type'              => Controls_Manager::SELECT,
                        'options'           => [
                            'none'      => __( 'None', 'powerpack' ),
                            'box'       => __( 'Box', 'powerpack' ),
                            'title'     => __( 'Title', 'powerpack' ),
                            'button'    => __( 'Button', 'powerpack' ),
                        ],
                        'default'           => 'none',
                    ],
                    [
                        'name'              => 'button_text',
                        'label'             => __( 'Button Text', 'powerpack' ),
                        'type'              => Controls_Manager::TEXT,
                        'dynamic'           => [
                            'active'    => true,
                        ],
                        'default'           => __( 'Get Started', 'powerpack' ),
                        'condition'         => [
                            'link_type' => 'button',
                        ],
                    ],
					[
						'name'				=> 'selected_icon',
						'label'				=> __( 'Button Icon', 'powerpack' ),
						'type'				=> Controls_Manager::ICONS,
						'label_block'		=> true,
						'fa4compatibility'	=> 'button_icon',
						'condition'         => [
							'link_type' => 'button',
						],
					],
                    [
                        'name'              => 'button_icon_position',
                        'label'             => __( 'Icon Position', 'powerpack' ),
                        'type'              => Controls_Manager::SELECT,
                        'default'           => 'after',
                        'options'           => [
                            'after'     => __( 'After', 'powerpack' ),
                            'before'    => __( 'Before', 'powerpack' ),
                        ],
                        'condition'         => [
                            'link_type'     => 'button',
                        ],
                    ],
					[
						'name'              => 'link',
						'label'             => __( 'Link', 'powerpack' ),
						'type'              => Controls_Manager::URL,
                        'dynamic'           => [
                            'active'  => true,
                        ],
						'label_block'       => true,
						'placeholder'       => __( 'http://your-link.com', 'powerpack' ),
                        'default'               => [
                            'url' => '#',
                        ],
                        'conditions'        => [
							'terms' => [
								[
									'name' => 'link_type',
									'operator' => '!=',
									'value' => 'none',
								],
							],
						],
					],
				],
				'title_field'       => '{{{ text }}}',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'thumbnail', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.,
                'label'                 => __( 'Image Size', 'powerpack' ),
                'default'               => 'full',
				'separator'             => 'before',
			]
		);
        
        $this->add_control(
            'title_html_tag',
            [
                'label'                 => __( 'Title HTML Tag', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'div',
                'options'               => [
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
            ]
        );

		$this->add_control(
			'connector',
			[
				'label'                 => __( 'Connector', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
			]
		);

		$this->add_control(
			'corner_lines',
			[
				'label'                 => __( 'Hide Corner Lines', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'condition'             => [
                    'connector' => 'yes',
                ],
			]
		);

        $this->end_controls_section();
	}

	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links('Info_List');

		if ( !empty($help_docs) ) {

			/**
			 * Content Tab: Help Docs
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

	protected function register_style_list_controls() {
        /**
         * Style Tab: List
         */
        $this->start_controls_section(
            'section_list_style',
            [
                'label'                 => __( 'List', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_control(
			'items_spacing',
			[
				'label'                 => __( 'Items Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
                    'size' => 10,
                ],
				'range'                 => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors'             => [
					'{{WRAPPER}}.pp-info-list-icon-left .pp-info-list-item:not(:last-child) .pp-info-list-item-inner, {{WRAPPER}}.pp-info-list-icon-right .pp-info-list-item:not(:last-child) .pp-info-list-item-inner' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pp-info-list-icon-top .pp-info-list-item .pp-info-list-item-inner' => 'margin-right: calc({{SIZE}}{{UNIT}}/2); margin-left: calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}}.pp-info-list-icon-top .pp-list-items' => 'margin-right: calc(-{{SIZE}}{{UNIT}}/2); margin-left: calc(-{{SIZE}}{{UNIT}}/2);',
            
					'(tablet){{WRAPPER}}.pp-info-list-stack-tablet.pp-info-list-icon-top .pp-info-list-item .pp-info-list-item-inner' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-left: 0; margin-right: 0;',
                    '(tablet){{WRAPPER}}.pp-info-list-stack-tablet.pp-info-list-icon-top .pp-list-items' => 'margin-right: 0; margin-left: 0;',
            
					'(mobile){{WRAPPER}}.pp-info-list-stack-mobile.pp-info-list-icon-top .pp-info-list-item .pp-info-list-item-inner' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-left: 0; margin-right: 0;',
                    '(mobile){{WRAPPER}}.pp-info-list-stack-mobile.pp-info-list-icon-top .pp-list-items' => 'margin-right: 0; margin-left: 0;',
				],
			]
		);

		$this->add_control(
			'icon_position',
			[
                'label'                 => __( 'Position', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'toggle'                => false,
                'default'               => 'left',
                'options'               => [
                    'left'      => [
                        'title' => __( 'Left', 'powerpack' ),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'top'       => [
                        'title' => __( 'Top', 'powerpack' ),
                        'icon'  => 'eicon-v-align-top',
                    ],
                    'right'     => [
                        'title' => __( 'Right', 'powerpack' ),
                        'icon'  => 'eicon-h-align-right',
                    ],
                ],
				'prefix_class'          => 'pp-info-list-icon-',
			]
		);

		$this->add_control(
			'responsive_breakpoint',
			[
				'label'                 => __( 'Responsive Breakpoint', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'label_block'           => false,
				'default'               => 'mobile',
				'options'               => [
					''         => __( 'None', 'powerpack' ),
					'tablet'   => __( 'Tablet', 'powerpack' ),
					'mobile'   => __( 'Mobile', 'powerpack' ),
				],
				'prefix_class'          => 'pp-info-list-stack-',
                'condition'             => [
                    'icon_position' => 'top',
                ],
			]
		);

        $this->end_controls_section();
	}

	protected function register_style_connector_controls() {
        /**
         * Style Tab: Connector
         */
        $this->start_controls_section(
            'section_connector_style',
            [
                'label'                 => __( 'Connector', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'connector' => 'yes',
                ],
            ]
        );

		$this->add_control(
			'connector_color',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-info-list-connector .pp-infolist-icon-wrapper:before, {{WRAPPER}} .pp-info-list-connector .pp-infolist-icon-wrapper:after' => 'border-color: {{VALUE}};',
				],
                'condition'             => [
                    'connector' => 'yes',
                ],
			]
		);

		$this->add_control(
			'connector_style',
			[
				'label'                 => __( 'Style', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					'solid'    => __( 'Solid', 'powerpack' ),
					'double'   => __( 'Double', 'powerpack' ),
					'dotted'   => __( 'Dotted', 'powerpack' ),
					'dashed'   => __( 'Dashed', 'powerpack' ),
				],
				'default'               => 'solid',
				'selectors'             => [
					'{{WRAPPER}}.pp-info-list-icon-left .pp-info-list-connector .pp-infolist-icon-wrapper:before, {{WRAPPER}}.pp-info-list-icon-left .pp-info-list-connector .pp-infolist-icon-wrapper:after' => 'border-right-style: {{VALUE}};',
					'{{WRAPPER}}.pp-info-list-icon-right .pp-info-list-connector .pp-infolist-icon-wrapper:before, {{WRAPPER}}.pp-info-list-icon-right .pp-info-list-connector .pp-infolist-icon-wrapper:after' => 'border-left-style: {{VALUE}};',
					'{{WRAPPER}}.pp-info-list-icon-top .pp-info-list-connector .pp-infolist-icon-wrapper:before, {{WRAPPER}}.pp-info-list-icon-top .pp-info-list-connector .pp-infolist-icon-wrapper:after' => 'border-top-style: {{VALUE}};',
            
					'(tablet){{WRAPPER}}.pp-info-list-stack-tablet.pp-info-list-icon-top .pp-info-list-connector .pp-infolist-icon-wrapper:before, {{WRAPPER}}.pp-info-list-icon-top .pp-info-list-connector .pp-infolist-icon-wrapper:after' => 'border-right-style: {{VALUE}};',
            
					'(mobile){{WRAPPER}}.pp-info-list-stack-mobile.pp-info-list-icon-top .pp-info-list-connector .pp-infolist-icon-wrapper:before, {{WRAPPER}}.pp-info-list-icon-top .pp-info-list-connector .pp-infolist-icon-wrapper:after' => 'border-right-style: {{VALUE}};',
				],
                'condition'             => [
                    'connector' => 'yes',
                ],
			]
		);

		$this->add_control(
			'connector_width',
			[
				'label'                 => __( 'Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 1,
				],
				'range'                 => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'selectors'             => [
					'{{WRAPPER}}.pp-info-list-icon-left .pp-info-list-connector .pp-infolist-icon-wrapper:before, {{WRAPPER}}.pp-info-list-icon-left .pp-info-list-connector .pp-infolist-icon-wrapper:after' => 'border-right-width: {{SIZE}}px;',
					'{{WRAPPER}}.pp-info-list-icon-right .pp-info-list-connector .pp-infolist-icon-wrapper:before, {{WRAPPER}}.pp-info-list-icon-right .pp-infolist-icon-wrapper:after' => 'border-left-width: {{SIZE}}px;',
					'{{WRAPPER}}.pp-info-list-icon-top .pp-info-list-connector .pp-infolist-icon-wrapper:before, {{WRAPPER}}.pp-info-list-icon-top .pp-info-list-connector .pp-infolist-icon-wrapper:after' => 'border-top-width: {{SIZE}}px;',
            
					'(tablet){{WRAPPER}}.pp-info-list-stack-tablet.pp-info-list-icon-top .pp-info-list-connector .pp-infolist-icon-wrapper:before, {{WRAPPER}}.pp-info-list-icon-top .pp-info-list-connector .pp-infolist-icon-wrapper:after' => 'border-right-width: {{SIZE}}px;',
            
					'(mobile){{WRAPPER}}.pp-info-list-stack-mobile.pp-info-list-icon-top .pp-info-list-connector .pp-infolist-icon-wrapper:before, {{WRAPPER}}.pp-info-list-icon-top .pp-info-list-connector .pp-infolist-icon-wrapper:after' => 'border-right-width: {{SIZE}}px;',
				],
                'condition'             => [
                    'connector' => 'yes',
                ],
			]
		);
        
        $this->end_controls_section();
	}

	protected function register_style_icon_controls() {
        /**
         * Style Tab: Icon
         */
        $this->start_controls_section(
            'section_icon_style',
            [
                'label'                 => __( 'Icon', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
			'icon_vertical_align',
			[
				'label'                 => __( 'Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
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
				'prefix_class'          => 'pp-info-list-icon-vertical-',
                'condition'             => [
                    'icon_position' => ['left','right'],
                ],
			]
		);
        
        $this->add_control(
			'icon_horizontal_align',
			[
				'label'                 => __( 'Horizontal Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'toggle'                => false,
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center'           => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'            => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'               => 'left',
                'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'center'   => 'center',
					'right'    => 'flex-end',
				],
				'prefix_class'          => 'pp-info-list-icon-horizontal-',
                'condition'             => [
                    'icon_position' => 'top',
                ],
			]
		);

        $this->start_controls_tabs( 'tabs_icon_style' );

        $this->start_controls_tab(
            'tab_icon_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
            ]
        );

		$this->add_control(
			'icon_color',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items .pp-info-list-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-list-items .pp-info-list-icon svg' => 'fill: {{VALUE}};',
				],
				'scheme'                => [
					'type'     => Scheme_Color::get_type(),
					'value'    => Scheme_Color::COLOR_2,
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items .pp-infolist-icon-wrapper' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label'                 => __( 'Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 14,
				],
				'range'                 => [
					'px' => [
						'min' => 6,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items .pp-info-list-icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-list-items .pp-info-list-image img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_box_size',
			[
				'label'                 => __( 'Box Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 14,
				],
				'range'                 => [
					'px' => [
						'min' => 6,
						'max' => 200,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-infolist-icon-wrapper' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',

					'{{WRAPPER}}.pp-info-list-icon-left .pp-info-list-container .pp-infolist-icon-wrapper:before' => 'left: calc(({{SIZE}}px/2) - ({{connector_width.SIZE}}px/2)); bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pp-info-list-icon-left .pp-info-list-container .pp-infolist-icon-wrapper:after' => 'left: calc(({{SIZE}}px/2) - ({{connector_width.SIZE}}px/2)); top: {{SIZE}}{{UNIT}};',

					'{{WRAPPER}}.pp-info-list-icon-right .pp-info-list-container .pp-infolist-icon-wrapper:before' => 'right: calc(({{SIZE}}px/2) - ({{connector_width.SIZE}}px/2)); bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pp-info-list-icon-right .pp-info-list-container .pp-infolist-icon-wrapper:after' => 'right: calc(({{SIZE}}px/2) - ({{connector_width.SIZE}}px/2)); top: {{SIZE}}{{UNIT}};',
            
					'{{WRAPPER}}.pp-info-list-icon-top .pp-info-list-container .pp-infolist-icon-wrapper:before' => 'top: calc(({{SIZE}}px/2) - ({{connector_width.SIZE}}px/2)); right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pp-info-list-icon-top .pp-info-list-container .pp-infolist-icon-wrapper:after' => 'top: calc(({{SIZE}}px/2) - ({{connector_width.SIZE}}px/2)); left: {{SIZE}}{{UNIT}};',
            
					'(tablet){{WRAPPER}}.pp-info-list-stack-tablet.pp-info-list-icon-top .pp-info-list-container .pp-infolist-icon-wrapper:before' => 'left: calc(({{SIZE}}px/2) - ({{connector_width.SIZE}}px/2)); bottom: {{SIZE}}{{UNIT}}; right: auto; top: auto;',
					'(tablet){{WRAPPER}}.pp-info-list-stack-tablet.pp-info-list-icon-top .pp-info-list-container .pp-infolist-icon-wrapper:after' => 'left: calc(({{SIZE}}px/2) - ({{connector_width.SIZE}}px/2)); top: {{SIZE}}{{UNIT}};',
            
					'(mobile){{WRAPPER}}.pp-info-list-stack-mobile.pp-info-list-icon-top .pp-info-list-container .pp-infolist-icon-wrapper:before' => 'left: calc(({{SIZE}}px/2) - ({{connector_width.SIZE}}px/2)); bottom: {{SIZE}}{{UNIT}}; right: auto; top: auto;',
					'(mobile){{WRAPPER}}.pp-info-list-stack-mobile.pp-info-list-icon-top .pp-info-list-container .pp-infolist-icon-wrapper:after' => 'left: calc(({{SIZE}}px/2) - ({{connector_width.SIZE}}px/2)); top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_spacing',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 8,
				],
				'range'                 => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors'             => [
					'{{WRAPPER}}.pp-info-list-icon-left .pp-infolist-icon-wrapper' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pp-info-list-icon-right .pp-infolist-icon-wrapper' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pp-info-list-icon-top .pp-infolist-icon-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
            
					'(tablet){{WRAPPER}}.pp-info-list-stack-tablet.pp-info-list-icon-top .pp-infolist-icon-wrapper' => 'margin-right: {{SIZE}}{{UNIT}}; margin-bottom: 0;',
            
					'(mobile){{WRAPPER}}.pp-info-list-stack-mobile.pp-info-list-icon-top .pp-infolist-icon-wrapper' => 'margin-right: {{SIZE}}{{UNIT}}; margin-bottom: 0;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'icon_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-list-items .pp-infolist-icon-wrapper',
			]
		);

		$this->add_control(
			'icon_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items .pp-infolist-icon-wrapper, {{WRAPPER}} .pp-list-items .pp-info-list-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_icon_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
            ]
        );

		$this->add_control(
			'icon_color_hover',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items .pp-infolist-icon-wrapper:hover .pp-info-list-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-list-items .pp-infolist-icon-wrapper:hover .pp-info-list-icon svg' => 'fill: {{VALUE}};',
				],
				'scheme'                => [
					'type'     => Scheme_Color::get_type(),
					'value'    => Scheme_Color::COLOR_2,
				],
			]
		);

		$this->add_control(
			'icon_bg_color_hover',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items .pp-infolist-icon-wrapper:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items .pp-infolist-icon-wrapper:hover' => 'border-color: {{VALUE}};',
				],
				'scheme'                => [
					'type'     => Scheme_Color::get_type(),
					'value'    => Scheme_Color::COLOR_2,
				],
			]
		);

		$this->add_control(
			'icon_hover_animation',
			[
				'label'                 => __( 'Animation', 'powerpack' ),
				'type'                  => Controls_Manager::HOVER_ANIMATION,
			]
		);

        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->add_control(
            'icon_number_heading',
            [
                'label'                 => __( 'Icon Type: Number', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'icon_number_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-list-items .pp-info-list-number',
            ]
        );

        $this->end_controls_section();
	}

	protected function register_style_title_controls() {
        /**
         * Style Tab: Title
         */
        $this->start_controls_section(
            'section_content_style',
            [
                'label'                 => __( 'Content', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_responsive_control(
			'content_align',
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
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-infolist-content-wrapper' => 'text-align: {{VALUE}};',
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
			'title_color',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-info-list-title' => 'color: {{VALUE}};',
				],
				'scheme'                => [
					'type'     => Scheme_Color::get_type(),
					'value'    => Scheme_Color::COLOR_2,
				],
			]
		);
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'title_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-info-list-title',
            ]
        );
        
        $this->add_control(
            'description_heading',
            [
                'label'                 => __( 'Description', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
            ]
        );

		$this->add_control(
			'description_color',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-info-list-description' => 'color: {{VALUE}};',
				],
				'scheme'                => [
					'type'     => Scheme_Color::get_type(),
					'value'    => Scheme_Color::COLOR_2,
				],
			]
		);
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'description_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-info-list-description',
            ]
        );
        
        $this->end_controls_section();
	}

	protected function register_style_button_controls() {
        /**
         * Style Tab: Button
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_info_box_button_style',
            [
                'label'                 => __( 'Button', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_control(
			'button_size',
			[
				'label'                 => __( 'Size', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'sm',
				'options'               => [
					'xs' => __( 'Extra Small', 'powerpack' ),
					'sm' => __( 'Small', 'powerpack' ),
					'md' => __( 'Medium', 'powerpack' ),
					'lg' => __( 'Large', 'powerpack' ),
					'xl' => __( 'Extra Large', 'powerpack' ),
				],
			]
		);

        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
            ]
        );

        $this->add_control(
            'button_bg_color_normal',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-info-list-button' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_text_color_normal',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-info-list-button' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-info-list-button svg' => 'fill: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'button_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-info-list-button',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-info-list-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'button_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-info-list-button',
            ]
        );

		$this->add_responsive_control(
			'button_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-info-list-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-info-list-button',
			]
		);
        
        $this->add_control(
            'info_box_button_icon_heading',
            [
                'label'                 => __( 'Button Icon', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
            ]
        );

		$this->add_responsive_control(
			'button_icon_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'placeholder'       => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-info-list-button .pp-button-icon' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
            ]
        );

        $this->add_control(
            'button_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-info-list-button:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_text_color_hover',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-info-list-button:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'button_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-info-list-button:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_control(
			'button_animation',
			[
				'label'                 => __( 'Animation', 'powerpack' ),
				'type'                  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-info-list-button:hover',
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->end_controls_section();
    }

    /**
	 * Render info list widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $this->add_render_attribute( [
            'info-list' => [
                'class' => [
                    'pp-info-list-container',
                    'pp-list-container',
                ]
            ],
            'info-list-items' => [
                'class' => 'pp-list-items'
            ],
            'list-item' => [
                'class' => 'pp-info-list-item'
            ],
            'icon' => [
                'class' => ['pp-info-list-icon', 'pp-icon'],
            ],
            'info-list-button' => [
                'class' => [
                    'pp-info-list-button',
                    'elementor-button',
                    'elementor-size-' . $settings['button_size'],
                ]
            ]
		] );

		if ( $settings['connector'] == 'yes' ) {
            $this->add_render_attribute( 'info-list', 'class', 'pp-info-list-connector' );
			if ( $settings['corner_lines'] == 'yes' ) {
				$this->add_render_attribute( 'info-list', 'class', 'pp-info-list-corners-hide' );
			}
        }

		if ( $settings['button_animation'] ) {
			$this->add_render_attribute( 'info-list-button', 'class', 'elementor-animation-' . $settings['button_animation'] );
		}
        
        $i = 1;
        ?>
        <div <?php echo $this->get_render_attribute_string( 'info-list' ); ?>>
            <ul <?php echo $this->get_render_attribute_string( 'info-list-items' ); ?>>
                <?php foreach ( $settings['list_items'] as $index => $item ) : ?>
                    <?php if ( $item['text'] || $item['description'] ) { ?>
                        <li <?php echo $this->get_render_attribute_string( 'list-item' ); ?>>
                            <div class="pp-info-list-item-inner">
                            <?php
                                $text_key = $this->get_repeater_setting_key( 'text', 'list_items', $index );
                                $this->add_render_attribute( $text_key, 'class', 'pp-info-list-title' );
                                $this->add_inline_editing_attributes( $text_key, 'none' );
            
                                $description_key = $this->get_repeater_setting_key( 'description', 'list_items', $index );
                                $this->add_render_attribute( $description_key, 'class', 'pp-info-list-description' );
                                $this->add_inline_editing_attributes( $description_key, 'basic' );

                                $button_key = $this->get_repeater_setting_key( 'button-wrap', 'list_items', $index );
                                $this->add_render_attribute( $button_key, 'class', 'pp-info-list-button-wrapper pp-info-list-button-icon-'.$item['button_icon_position'] );

                                if ( ! empty( $item['link']['url'] ) ) {
                                    $link_key = 'link_' . $i;
									
									$this->add_link_attributes( $link_key, $item['link'] );
                                }
																		
								$this->render_infolist_icon( $item, $i );
                            ?>
                            <div class="pp-infolist-content-wrapper">
                                <?php if ( ! empty( $item['link']['url'] ) && $item['link_type'] == 'box' ) { ?>
                                    <a <?php echo $this->get_render_attribute_string( $link_key ); ?>>
                                <?php } ?>
								<?php if ( $item['text'] ) { ?>
								<?php printf( '<%1$s %2$s>', $settings['title_html_tag'], $this->get_render_attribute_string( $text_key ) ); ?>
                                    <?php if ( ! empty( $item['link']['url'] ) && $item['link_type'] == 'title' ) { ?>
                                        <a <?php echo $this->get_render_attribute_string( $link_key ); ?>>
                                    <?php } ?>
                                        <?php echo $item['text']; ?>
                                    <?php if ( ! empty( $item['link']['url'] ) && $item['link_type'] == 'title' ) { ?>
                                        </a>
                                    <?php } ?>
                                </<?php echo $settings['title_html_tag']; ?>>
								<?php } ?>
                                <?php
									if ( $item['description'] ) {
                                    	printf( '<div %1$s>%2$s</div>', $this->get_render_attribute_string( $description_key ), $item['description'] );
									}
                                ?>
                                <?php if ( ! empty( $item['link']['url'] ) && $item['link_type'] == 'button' ) { ?>
                                    <div <?php echo $this->get_render_attribute_string( $button_key ); ?>>
                                        <a <?php echo $this->get_render_attribute_string( $link_key ); ?>>
                                            <div <?php echo $this->get_render_attribute_string( 'info-list-button' ); ?>>
                                                <?php $this->render_infolist_button_icon($item); ?>

                                                <?php if ( ! empty( $item['button_text'] ) ) { ?>
                                                    <span <?php echo $this->get_render_attribute_string( 'button_text' ); ?>>
                                                        <?php echo esc_attr( $item['button_text'] ); ?>
                                                    </span>
                                                <?php } ?>
                                            </div>
                                        </a>
                                    </div>
                                <?php } ?>
                                <?php
                                if ( ! empty( $item['link']['url'] ) && $item['link_type'] == 'box' ) {
                                    echo '</a>';
                                }
                                ?>
                            </div>
                            </div>
                        </li>
                    <?php } ?>
                <?php $i++; endforeach; ?>
            </ul>
        </div>
        <?php
    }

    /**
	 * Render info-box carousel icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_infolist_button_icon( $item ) {
        $settings = $this->get_settings_for_display();
		
		$migration_allowed = Icons_Manager::is_migration_allowed();
		
		// add old default
		if ( ! isset( $item['button_icon'] ) && ! $migration_allowed ) {
			$item['button_icon'] = '';
		}

		$migrated = isset( $item['__fa4_migrated']['icon'] );
		$is_new = ! isset( $item['button_icon'] ) && $migration_allowed;

		if ( ! empty( $item['button_icon'] ) || ( ! empty( $item['selected_icon']['value'] ) && $is_new ) ) {
			echo '<span class="pp-button-icon pp-icon">';
			if ( $is_new || $migrated ) {
				Icons_Manager::render_icon( $item['selected_icon'], [ 'aria-hidden' => 'true' ] );
			} else { ?>
					<i class="<?php echo esc_attr( $item['button_icon'] ); ?>" aria-hidden="true"></i>
			<?php }
			echo '</span>';
		}
    }

    /**
	 * Render info-box carousel icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_infolist_icon( $item, $i ) {
        $settings = $this->get_settings_for_display();

		$fallback_defaults = [
			'fa fa-check',
			'fa fa-times',
			'fa fa-dot-circle-o',
		];
		
		$migration_allowed = Icons_Manager::is_migration_allowed();
		
		// add old default
		if ( ! isset( $item['list_icon'] ) && ! $migration_allowed ) {
			$item['list_icon'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-check';
		}

		$migrated = isset( $item['__fa4_migrated']['icon'] );
		$is_new = ! isset( $item['list_icon'] ) && $migration_allowed;
																
		if ( $item['pp_icon_type'] != 'none' ) {
			$icon_key = 'icon_' . $i;
			$this->add_render_attribute( $icon_key, 'class', 'pp-infolist-icon-wrapper' );

			if ( $settings['icon_hover_animation'] != '' ) {
				$icon_animation = 'elementor-animation-' . $settings['icon_hover_animation'];
			} else {
				$icon_animation = '';
			}
			?>
			<div <?php echo $this->get_render_attribute_string( $icon_key ); ?>>
				<?php
					if ( $item['pp_icon_type'] == 'icon' ) {
						if ( ! empty( $item['list_icon'] ) || ( ! empty( $item['icon']['value'] ) && $is_new ) ) {
						echo '<span class="pp-info-list-icon pp-icon ' . $icon_animation . '">';
						if ( $is_new || $migrated ) {
							Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] );
						} else { ?>
								<i class="<?php echo esc_attr( $item['list_icon'] ); ?>" aria-hidden="true"></i>
						<?php }
						echo '</span>';
						}
					} elseif ( $item['pp_icon_type'] == 'image' ) {
						$image_url = Group_Control_Image_Size::get_attachment_image_src( $item['list_image']['id'], 'thumbnail', $settings );

						if ( $image_url ) {
							printf( '<span class="pp-info-list-image %2$s"><img src="%1$s" alt="%3$s"></span>', esc_url( $image_url ), $icon_animation, esc_attr( Control_Media::get_image_alt( $item['list_image'] ) ) );
						} else {
							echo '<img src="' . esc_url( $item['list_image']['url'] ) . '">';
						}
					} elseif ( $item['pp_icon_type'] == 'text' ) {
						printf( '<span class="pp-info-list-icon pp-info-list-number %2$s">%1$s</span>', esc_attr( $item['icon_text'] ), $icon_animation );
					}
				?>
			</div>
			<?php
		}
    }

    /**
	 * Render info list widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
    protected function _content_template() {
        ?>
        <#
        view.addRenderAttribute(
            'info-list',
            {
                'class': [ 'pp-info-list-container', 'pp-list-container' ],
            }
        );
           
        if ( settings.connector == 'yes' ) {
           view.addRenderAttribute( 'info-list', 'class', 'pp-info-list-connector' );  
			if ( settings.corner_lines == 'yes' ) {
			   view.addRenderAttribute( 'info-list', 'class', 'pp-info-list-corners-hide' );
			}
        }
			   
		var iconsHTML = {},
			migrated = {},
		   	buttonIconHTML = {},
			buttonMigrated = {};
        #>
        <div {{{ view.getRenderAttributeString( 'info-list' ) }}}>
            <ul class="pp-list-items">
                <# var i = 1; #>
                <# _.each( settings.list_items, function( item, index ) { #>
                    <#
                        var text_key = 'list_items.' + (i - 1) + '.text';
                        var description_key = 'list_items.' + (i - 1) + '.description';

                        view.addInlineEditingAttributes( text_key );

                        view.addRenderAttribute( description_key, 'class', 'pp-info-list-description' );
                        view.addInlineEditingAttributes( description_key );
                    #>
					<# if ( item.text || item.description ) { #>
						<li class="pp-info-list-item">
							<div class="pp-info-list-item-inner">
								<# if ( item.pp_icon_type != 'none' ) { #>
									<div class="pp-infolist-icon-wrapper">
										<# if ( item.pp_icon_type == 'icon' ) { #>
											<# if ( item.list_icon || item.icon.value ) { #>
												<span class="pp-info-list-icon pp-icon elementor-animation-{{ settings.icon_hover_animation }}" aria-hidden="true">
												<#
													iconsHTML[ index ] = elementor.helpers.renderIcon( view, item.icon, { 'aria-hidden': true }, 'i', 'object' );
													migrated[ index ] = elementor.helpers.isIconMigrated( item, 'icon' );
													if ( iconsHTML[ index ] && iconsHTML[ index ].rendered && ( ! item.list_icon || migrated[ index ] ) ) { #>
														{{{ iconsHTML[ index ].value }}}
													<# } else { #>
														<i class="{{ item.list_icon }}" aria-hidden="true"></i>
													<# }
												#>
												</span>
											<# } #>
										<# } else if ( item.pp_icon_type == 'image' ) { #>
											<span class="pp-info-list-image elementor-animation-{{ settings.icon_hover_animation }}">
												<#
												var image = {
													id: item.list_image.id,
													url: item.list_image.url,
													size: settings.thumbnail_size,
													dimension: settings.thumbnail_custom_dimension,
													model: view.getEditModel()
												};
												var image_url = elementor.imagesManager.getImageUrl( image );
												#>
												<img src="{{{ image_url }}}" />
											</span>
										<# } else if ( item.pp_icon_type == 'text' ) { #>
											<span class="pp-info-list-icon pp-info-list-number elementor-animation-{{ settings.icon_hover_animation }}">
												{{ item.icon_text }}
											</span>
										<# } #>
									</div>
								<# } #>
								<div class="pp-infolist-content-wrapper">
									<# if ( item.link.url != '' && item.link_type == 'box' ) { #>
										<a href="{{ item.link.url }}">
									<# } #>
										<# if ( item.text ) { #>
										<{{settings.title_html_tag}} class="pp-info-list-title">
											<# if ( item.link.url != '' && item.link_type == 'title' ) { #>
												<a href="{{ item.link.url }}">
											<# } #>
											<span {{{ view.getRenderAttributeString( 'list_items.' + (i - 1) + '.text' ) }}}>
											{{{ item.text }}}
											</span>
											<# if ( item.link.url != '' && item.link_type == 'title' ) { #>
												</a>
											<# } #>
										</{{settings.title_html_tag}}>
										<# } #>
										<# if ( item.description ) { #>
										<div {{{ view.getRenderAttributeString( description_key ) }}}>
											{{{ item.description }}}
										</div>
										<# } #>
										<# if ( item.link.url != '' && item.link_type == 'button' ) { #>
											<div class="pp-info-list-button-wrapper pp-info-list-button-icon-{{ item.button_icon_position }}">
												<a href="{{ item.link.url }}">
													<div class="pp-info-list-button elementor-button elementor-size-{{ settings.button_size }} elementor-animation-{{ settings.button_animation }}">
														<#
															buttonIconHTML[ index ] = elementor.helpers.renderIcon( view, item.selected_icon, { 'aria-hidden': true }, 'i', 'object' );
															buttonMigrated[ index ] = elementor.helpers.isIconMigrated( item, 'selected_icon' );
													   #>
														<# if ( buttonIconHTML[ index ] && buttonIconHTML[ index ].rendered && ( ! item.button_icon || buttonMigrated[ index ] ) ) { #>
															<span class="pp-button-icon pp-icon">
																{{{ buttonIconHTML[ index ].value }}}
															</span>
														<# } else if ( item.button_icon ) { #>
															<span class="pp-button-icon pp-icon">
																<i class="{{ item.button_icon }}" aria-hidden="true"></i>
															</span>
														<# } #>
															
														<# if ( item.button_text != '' ) { #>
															<span class="pp-button-text">
																{{{ item.button_text }}}
															</span>
														<# } #>
													</div>
												</a>
											</div>
										<# } #>
									<# if ( item.link_type == 'box' ) { #>
										</a>
									<# } #>
								</div>
							</div>
						</li>
					<# } #>
                <# i++ } ); #>
            </ul>
        </div>
        <?php
    }
}