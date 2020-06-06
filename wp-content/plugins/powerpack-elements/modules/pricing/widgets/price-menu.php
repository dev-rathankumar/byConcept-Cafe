<?php
namespace PowerpackElements\Modules\Pricing\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Price Menu Widget
 */
class Price_Menu extends Powerpack_Widget {
    
    public function get_name() {
        return parent::get_widget_name( 'Price_Menu' );
    }

    public function get_title() {
        return parent::get_widget_title( 'Price_Menu' );
    }

    public function get_categories() {
        return parent::get_widget_categories( 'Price_Menu' );
    }

    public function get_icon() {
        return parent::get_widget_icon( 'Price_Menu' );
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
		return parent::get_widget_keywords( 'Price_Menu' );
	}

    protected function _register_controls() {
		
		/* Content Tab */
		$this->register_content_price_menu_controls();
		$this->register_content_help_docs_controls();
		
		/* Style Tab */
		$this->register_style_items_controls();
		$this->register_style_content_controls();
		$this->register_style_title_controls();
		$this->register_style_title_separator_controls();
		$this->register_style_price_controls();
		$this->register_style_description_controls();
		$this->register_style_image_controls();
		$this->register_style_title_connector_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	Content Tab
	/*-----------------------------------------------------------------------------------*/
        
	protected function register_content_price_menu_controls() {
        $this->start_controls_section(
            'section_price_menu',
            [
                'label'                 => __( 'Price Menu', 'powerpack' ),
            ]
        );

		$this->add_control(
			'menu_items',
			[
				'label'                 => '',
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[
						'menu_title' => __( 'Menu Item #1', 'powerpack' ),
						'menu_price' => '$49',
					],
					[
						'menu_title' => __( 'Menu Item #2', 'powerpack' ),
						'menu_price' => '$49',
					],
					[
						'menu_title' => __( 'Menu Item #3', 'powerpack' ),
						'menu_price' => '$49',
					],
				],
				'fields'                => [
					[
						'name'          => 'menu_title',
						'label'         => __( 'Title', 'powerpack' ),
						'type'          => Controls_Manager::TEXT,
                        'dynamic'       => [
                            'active'    => true,
                        ],
						'label_block'   => true,
						'placeholder'   => __( 'Title', 'powerpack' ),
						'default'       => __( 'Title', 'powerpack' ),
					],
					[
						'name'          => 'menu_description',
						'label'         => __( 'Description', 'powerpack' ),
						'type'          => Controls_Manager::TEXTAREA,
                        'dynamic'       => [
                            'active'    => true,
                        ],
						'label_block'   => true,
						'placeholder'   => __( 'Description', 'powerpack' ),
						'default'       => __( 'Description', 'powerpack' ),
					],
                    [
						'name'          => 'menu_price',
                        'label'         => __( 'Price', 'powerpack' ),
                        'type'          => Controls_Manager::TEXT,
                        'dynamic'       => [
                            'active'    => true,
                        ],
                        'default'       => '$49',
                    ],
                    [
						'name'          => 'discount',
                        'label'         => __( 'Discount', 'powerpack' ),
                        'type'          => Controls_Manager::SWITCHER,
                        'default'       => 'no',
                        'label_on'      => __( 'On', 'powerpack' ),
                        'label_off'     => __( 'Off', 'powerpack' ),
                        'return_value'  => 'yes',
                    ],
                    [
						'name'          => 'original_price',
                        'label'         => __( 'Original Price', 'powerpack' ),
                        'type'          => Controls_Manager::TEXT,
                        'dynamic'       => [
                            'active'    => true,
                        ],
                        'default'       => '$69',
                        'conditions'        => [
                            'terms' => [
                                [
                                    'name' => 'discount',
                                    'operator' => '==',
                                    'value' => 'yes',
                                ],
                            ],
                        ],
                    ],
                    [
						'name'          => 'image_switch',
                        'label'         => __( 'Show Image', 'powerpack' ),
                        'type'          => Controls_Manager::SWITCHER,
                        'default'       => '',
                        'label_on'      => __( 'On', 'powerpack' ),
                        'label_off'     => __( 'Off', 'powerpack' ),
                        'return_value'  => 'yes',
                    ],
                    [
						'name'          => 'image',
                        'label'         => __( 'Image', 'powerpack' ),
                        'type'          => Controls_Manager::MEDIA,
                        'default'       => [
                            'url' => \Elementor\Utils::get_placeholder_image_src(),
                        ],
                        'dynamic'       => [
                            'active'    => true,
                        ],
                        'conditions'        => [
                            'terms' => [
                                [
                                    'name' => 'image_switch',
                                    'operator' => '==',
                                    'value' => 'yes',
                                ],
                            ],
                        ],
                    ],
                    [
						'name'          => 'link',
                        'label'         => __( 'Link', 'powerpack' ),
                        'type'          => Controls_Manager::URL,
                        'dynamic'       => [
                            'active'    => true,
                        ],
                        'placeholder'   => 'https://www.your-link.com',
                    ]
				],
				'title_field'       => '{{{ menu_title }}}',
			]
		);
		
        $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'image_size',
				'label'                 => __( 'Image Size', 'powerpack' ),
				'default'               => 'thumbnail',
				'separator'             => 'before',
			]
		);
        
        $this->add_control(
          'menu_style',
          [
             'label'                => __( 'Menu Style', 'powerpack' ),
             'type'                 => Controls_Manager::SELECT,
             'default'              => 'style-1',
             'options'              => [
                'style-powerpack'   => __( 'PowerPack Style', 'powerpack' ),
                'style-1'           => __( 'Style 1', 'powerpack' ),
                'style-2'           => __( 'Style 2', 'powerpack' ),
                'style-3'           => __( 'Style 3', 'powerpack' ),
                'style-4'           => __( 'Style 4', 'powerpack' ),
             ],
          ]
        );
        
        $this->add_responsive_control(
			'menu_align',
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
					'{{WRAPPER}} .pp-restaurant-menu-style-4'   => 'text-align: {{VALUE}};',
				],
                'condition'             => [
                    'menu_style' => 'style-4',
                ],
			]
		);
        
        $this->add_control(
            'title_price_connector',
            [
                'label'                 => __( 'Title-Price Connector', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'no',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'condition'             => [
                    'menu_style' => 'style-1',
                ],
            ]
        );
        
        $this->add_control(
            'title_separator',
            [
                'label'                 => __( 'Title Separator', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'no',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
            ]
        );

        $this->end_controls_section();
	}
	
	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links('Price_Menu');
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
	/*	Style Tab
	/*-----------------------------------------------------------------------------------*/

	protected function register_style_items_controls() {
        /**
         * Style Tab: Menu Items
         */
        $this->start_controls_section(
            'section_items_style',
            [
                'label'                 => __( 'Menu Items', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'items_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu .pp-restaurant-menu-item' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'items_spacing',
            [
                'label'                 => __( 'Items Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    '%' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu-item-wrap' => 'margin-bottom: calc(({{SIZE}}{{UNIT}})/2); padding-bottom: calc(({{SIZE}}{{UNIT}})/2)',
                ],
            ]
        );

		$this->add_responsive_control(
			'items_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-restaurant-menu .pp-restaurant-menu-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'items_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-restaurant-menu .pp-restaurant-menu-item',
			]
		);

		$this->add_control(
			'items_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-restaurant-menu .pp-restaurant-menu-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'pricing_table_shadow',
				'selector'              => '{{WRAPPER}} .pp-restaurant-menu-item',
				'separator'             => 'before',
			]
		);
        
        $this->end_controls_section();
	}
     
	protected function register_style_content_controls() {
        /**
         * Style Tab: Content
         */
        $this->start_controls_section(
            'section_content_style',
            [
                'label'                 => __( 'Content', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'menu_style' => 'style-powerpack',
                ],
            ]
        );

		$this->add_responsive_control(
			'content_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-restaurant-menu-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'menu_style' => 'style-powerpack',
                ],
			]
		);
        
        $this->end_controls_section();
	}
     
	protected function register_style_title_controls() {
        /**
         * Style Tab: Title Section
         */
        $this->start_controls_section(
            'section_title_style',
            [
                'label'                 => __( 'Title', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu .pp-restaurant-menu-title' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'title_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-restaurant-menu .pp-restaurant-menu-title',
            ]
        );
        
        $this->add_responsive_control(
            'title_margin',
            [
                'label'                 => __( 'Margin Bottom', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    '%' => [
                        'min'   => 0,
                        'max'   => 40,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu .pp-restaurant-menu-header' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        
        $this->end_controls_section();
	}
     
	protected function register_style_title_separator_controls() {
		/**
         * Style Tab: Title Separator
         */
        $this->start_controls_section(
            'section_title_separator_style',
            [
                'label'                 => __( 'Title Separator', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'title_separator' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'divider_title_border_type',
            [
                'label'                 => __( 'Border Type', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'dotted',
                'options'               => [
                    'none'      => __( 'None', 'powerpack' ),
                    'solid'     => __( 'Solid', 'powerpack' ),
                    'double'    => __( 'Double', 'powerpack' ),
                    'dotted'    => __( 'Dotted', 'powerpack' ),
                    'dashed'    => __( 'Dashed', 'powerpack' ),
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu .pp-price-menu-divider' => 'border-bottom-style: {{VALUE}}',
                ],
                'condition'             => [
                    'title_separator' => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'divider_title_border_weight',
            [
                'label'                 => __( 'Border Height', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => 1,
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
                    '{{WRAPPER}} .pp-restaurant-menu .pp-price-menu-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'title_separator' => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'divider_title_border_width',
            [
                'label'                 => __( 'Border Width', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => 100,
                    'unit'      => '%',
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
                    '{{WRAPPER}} .pp-restaurant-menu .pp-price-menu-divider' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'title_separator' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'divider_title_border_color',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu .pp-price-menu-divider' => 'border-bottom-color: {{VALUE}}',
                ],
                'condition'             => [
                    'title_separator' => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'divider_title_spacing',
            [
                'label'                 => __( 'Margin Bottom', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    '%' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu .pp-price-menu-divider' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
	}
     
	protected function register_style_price_controls() {

        $this->start_controls_section(
            'section_price_style',
            [
                'label'                 => __( 'Price', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'price_badge_heading',
            [
                'label'                 => __( 'Price Badge', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'menu_style' => 'style-powerpack',
                ],
            ]
        );

        $this->add_control(
            'badge_text_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu-style-powerpack .pp-restaurant-menu-price' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'menu_style' => 'style-powerpack',
                ],
            ]
        );

        $this->add_control(
            'badge_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu-style-powerpack .pp-restaurant-menu-price:after' => 'border-right-color: {{VALUE}}',
                ],
                'condition'             => [
                    'menu_style' => 'style-powerpack',
                ],
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu .pp-restaurant-menu-price-discount' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'menu_style!' => 'style-powerpack',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'price_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-restaurant-menu .pp-restaurant-menu-price-discount',
            ]
        );
        
        $this->add_control(
            'original_price_heading',
            [
                'label'                 => __( 'Original Price', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
            ]
        );
        
        $this->add_control(
            'original_price_strike',
            [
                'label'                 => __( 'Strikethrough', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'On', 'powerpack' ),
                'label_off'             => __( 'Off', 'powerpack' ),
                'return_value'          => 'yes',
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu .pp-restaurant-menu-price-original' => 'text-decoration: line-through;',
                ],
            ]
        );

        $this->add_control(
            'original_price_color',
            [
                'label'                 => __( 'Original Price Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '#a3a3a3',
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu .pp-restaurant-menu-price-original' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'original_price_typography',
                'label'                 => __( 'Original Price Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-restaurant-menu .pp-restaurant-menu-price-original',
            ]
        );

        $this->end_controls_section();
	}
     
	protected function register_style_description_controls() {

        $this->start_controls_section(
            'section_description_style',
            [
                'label'                 => __( 'Description', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu-description' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'description_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-restaurant-menu-description',
            ]
        );
        
        $this->add_responsive_control(
            'description_spacing',
            [
                'label'                 => __( 'Margin Bottom', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    '%' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->end_controls_section();
	}
     
	protected function register_style_image_controls() {
        /**
         * Style Tab: Image Section
         */
        $this->start_controls_section(
            'section_image_style',
            [
                'label'                 => __( 'Image', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'image_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu-image img' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'image_width',
            [
                'label'                 => __( 'Width', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 20,
                        'max'   => 300,
                        'step'  => 1,
                    ],
                    '%' => [
                        'min'   => 5,
                        'max'   => 50,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu-image img' => 'width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

		$this->add_responsive_control(
			'image_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-restaurant-menu-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-restaurant-menu-image img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector'              => '{{WRAPPER}} .pp-restaurant-menu-image img',
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-restaurant-menu-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_vertical_position',
			[
				'label'                 => __( 'Vertical Position', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'       => [
						'title' => __( 'Top', 'powerpack' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle'    => [
						'title' => __( 'Middle', 'powerpack' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom'    => [
						'title' => __( 'Bottom', 'powerpack' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-restaurant-menu .pp-restaurant-menu-image' => 'align-self: {{VALUE}}',
				],
				'selectors_dictionary'  => [
					'top'      => 'flex-start',
                    'middle'   => 'center',
					'bottom'   => 'flex-end',
				],
			]
		);

        $this->end_controls_section();
	}
     
	protected function register_style_title_connector_controls() {
        /**
         * Style Tab: Items Divider Section
         */
        $this->start_controls_section(
            'section_table_title_connector_style',
            [
                'label'                 => __( 'Title-Price Connector', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'title_price_connector' => 'yes',
                    'menu_style' => 'style-1',
                ],
            ]
        );
        
        $this->add_control(
			'title_connector_vertical_align',
			[
				'label'                 => __( 'Vertical Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
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
				'selectors'             => [
					'{{WRAPPER}} .pp-restaurant-menu-style-1 .pp-price-title-connector'   => 'align-self: {{VALUE}};',
				],
				'selectors_dictionary'  => [
					'top'          => 'flex-start',
					'middle'       => 'center',
					'bottom'       => 'flex-end',
				],
                'condition'             => [
                    'title_price_connector' => 'yes',
                    'menu_style' => 'style-1',
                ],
			]
		);
        
        $this->add_control(
            'items_divider_style',
            [
                'label'                 => __( 'Style', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'dashed',
                'options'              => [
                    'solid'     => __( 'Solid', 'powerpack' ),
                    'dashed'    => __( 'Dashed', 'powerpack' ),
                    'dotted'    => __( 'Dotted', 'powerpack' ),
                    'double'    => __( 'Double', 'powerpack' ),
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu-style-1 .pp-price-title-connector' => 'border-bottom-style: {{VALUE}}',
                ],
                'condition'             => [
                    'title_price_connector' => 'yes',
                    'menu_style' => 'style-1',
                ],
            ]
        );

        $this->add_control(
            'items_divider_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '#000',
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu-style-1 .pp-price-title-connector' => 'border-bottom-color: {{VALUE}}',
                ],
                'condition'             => [
                    'title_price_connector' => 'yes',
                    'menu_style' => 'style-1',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'items_divider_weight',
            [
                'label'                 => __( 'Divider Weight', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [ 'size' => '1'],
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 30,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-restaurant-menu-style-1 .pp-price-title-connector' => 'border-bottom-width: {{SIZE}}{{UNIT}}; bottom: calc((-{{SIZE}}{{UNIT}})/2)',
                ],
                'condition'             => [
                    'title_price_connector' => 'yes',
                    'menu_style' => 'style-1',
                ],
            ]
        );
        
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $i = 1;
        $this->add_render_attribute( 'price-menu', 'class', 'pp-restaurant-menu' );
        
		if ( $settings['menu_style'] ) {
			$this->add_render_attribute( 'price-menu', 'class', 'pp-restaurant-menu-' . $settings['menu_style'] );
		}
        ?>
        <div <?php echo $this->get_render_attribute_string( 'price-menu' ); ?>>
            <div class="pp-restaurant-menu-items">
                <?php foreach ( $settings['menu_items'] as $index => $item ) : ?>
                    <?php
                        $title_key = $this->get_repeater_setting_key( 'menu_title', 'menu_items', $index );
                        $this->add_render_attribute( $title_key, 'class', 'pp-restaurant-menu-title-text' );
                        $this->add_inline_editing_attributes( $title_key, 'none' );

                        $description_key = $this->get_repeater_setting_key( 'menu_description', 'menu_items', $index );
                        $this->add_render_attribute( $description_key, 'class', 'pp-restaurant-menu-description' );
                        $this->add_inline_editing_attributes( $description_key, 'basic' );

                        $discount_price_key = $this->get_repeater_setting_key( 'menu_price', 'menu_items', $index );
                        $this->add_render_attribute( $discount_price_key, 'class', 'pp-restaurant-menu-price-discount' );
                        $this->add_inline_editing_attributes( $discount_price_key, 'none' );

                        $original_price_key = $this->get_repeater_setting_key( 'original_price', 'menu_items', $index );
                        $this->add_render_attribute( $original_price_key, 'class', 'pp-restaurant-menu-price-original' );
                        $this->add_inline_editing_attributes( $original_price_key, 'none' );
                    ?>
                    <div class="pp-restaurant-menu-item-wrap">
                        <div class="pp-restaurant-menu-item">
                            <?php if ( $item['image_switch'] == 'yes' ) { ?>
                                <div class="pp-restaurant-menu-image">
                                    <?php
                                        if ( ! empty( $item['image']['url'] ) ) :
                                            $image = $item['image'];
                                            $image_url = Group_Control_Image_Size::get_attachment_image_src( $image['id'], 'image_size', $settings );

                                            if ( $image_url ) {
                                                echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( Control_Media::get_image_alt( $item['image'] ) ) . '">';
                                            } else {
                                                echo '<img src="' . esc_url( $item['image']['url'] ) . '">';
                                            }
                                        ?>
                                     <?php endif; ?>
                                </div>
                            <?php } ?>

                            <div class="pp-restaurant-menu-content">
                                <div class="pp-restaurant-menu-header">
                                    <?php if ( ! empty( $item['menu_title'] ) ) { ?>
                                        <h4 class="pp-restaurant-menu-title">
                                            <?php
                                                if ( ! empty( $item['link']['url'] ) ) {
													$this->add_link_attributes( 'price-menu-link' . $i, $item['link'] );
                                                    ?>
                                                    <a <?php echo $this->get_render_attribute_string( 'price-menu-link' . $i ); ?>>
                                                        <span <?php echo $this->get_render_attribute_string( $title_key ); ?>>
                                                            <?php echo $item['menu_title']; ?>
                                                        </span>
                                                    </a>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <span <?php echo $this->get_render_attribute_string( $title_key ); ?>>
                                                        <?php echo $item['menu_title']; ?>
                                                    </span>
                                                    <?php
                                                }
                                            ?>
                                        </h4>
                                    <?php } ?>
                                    
                                    <?php if ( $settings['title_price_connector'] == 'yes' ) { ?>
                                        <span class="pp-price-title-connector"></span>
                                    <?php } ?>
                                    
                                    <?php if ( $settings['menu_style'] == 'style-1' ) { ?>
                                        <?php if ( ! empty( $item['menu_price'] ) ) { ?>
                                            <span class="pp-restaurant-menu-price">
                                                <?php if ( $item['discount'] == 'yes' ) { ?>
                                                    <span <?php echo $this->get_render_attribute_string( $original_price_key ); ?>>
                                                        <?php echo esc_attr( $item['original_price'] ); ?>
                                                    </span>
                                                <?php } ?>
                                                <span <?php echo $this->get_render_attribute_string( $discount_price_key ); ?>>
                                                    <?php echo esc_attr( $item['menu_price'] ); ?>
                                                </span>
                                            </span>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                
                                <?php if ( $settings['title_separator'] == 'yes' ) { ?>
                                    <div class="pp-price-menu-divider-wrap">
                                        <div class="pp-price-menu-divider"></div>
                                    </div>
                                <?php } ?>

                                <?php
                                    if ( ! empty( $item['menu_description'] ) ) {
                                        $description_html = sprintf( '<div %1$s>%2$s</div>', $this->get_render_attribute_string( $description_key ), $item['menu_description'] );
                                        
                                        echo $description_html;
                                    }
                                ?>

                                <?php if ( $settings['menu_style'] != 'style-1' ) { ?>
                                    <?php if ( ! empty( $item['menu_price'] ) ) { ?>
                                        <span class="pp-restaurant-menu-price">
                                            <?php if ( $item['discount'] == 'yes' ) { ?>
                                                <span <?php echo $this->get_render_attribute_string( $original_price_key ); ?>>
                                                    <?php echo esc_attr( $item['original_price'] ); ?>
                                                </span>
                                            <?php } ?>
                                            <span <?php echo $this->get_render_attribute_string( $discount_price_key ); ?>>
                                                <?php echo esc_attr( $item['menu_price'] ); ?>
                                            </span>
                                        </span>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php $i++; endforeach; ?>
            </div>
        </div>
        <?php
    }

    protected function _price_template() {
        ?>
        <# if ( item.menu_price != '' ) { #>
            <span class="pp-restaurant-menu-price">
                <#
                   if ( item.discount == 'yes' ) {
                        var original_price = item.original_price;

                        view.addRenderAttribute( 'menu_items.' + ($i - 1) + '.original_price', 'class', 'pp-restaurant-menu-price-original' );

                        view.addInlineEditingAttributes( 'menu_items.' + ($i - 1) + '.original_price' );

                        var original_price_html = '<span' + ' ' + view.getRenderAttributeString( 'menu_items.' + ($i - 1) + '.original_price' ) + '>' + original_price + '</span>';

                        print( original_price_html );
                   }

                    var menu_price = item.menu_price;

                    view.addRenderAttribute( 'menu_items.' + ($i - 1) + '.menu_price', 'class', 'pp-restaurant-menu-price-discount' );

                    view.addInlineEditingAttributes( 'menu_items.' + ($i - 1) + '.menu_price' );

                    var menu_price_html = '<span' + ' ' + view.getRenderAttributeString( 'menu_items.' + ($i - 1) + '.menu_price' ) + '>' + menu_price + '</span>';

                    print( menu_price_html );
                #>
            </span>
        <# } #>
        <?php
    }

    protected function _title_template() {
        ?>
        <#
            var title = item.menu_title;

            view.addRenderAttribute( 'menu_items.' + ($i - 1) + '.menu_title', 'class', 'pp-restaurant-menu-description' );

            view.addInlineEditingAttributes( 'menu_items.' + ($i - 1) + '.menu_title' );

            var title_html = '<div' + ' ' + view.getRenderAttributeString( 'menu_items.' + ($i - 1) + '.menu_title' ) + '>' + title + '</div>';

            print( title_html );
        #>
        <?php
    }

    protected function _content_template() {
        ?>
        <#
            var $i = 1;
        #>
        <div class="pp-restaurant-menu pp-restaurant-menu-{{ settings.menu_style }}">
            <div class="pp-restaurant-menu-items">
                <# _.each( settings.menu_items, function( item ) { #>
                    <div class="pp-restaurant-menu-item-wrap">
                        <div class="pp-restaurant-menu-item">
                            <# if ( item.image_switch == 'yes' ) { #>
                                <div class="pp-restaurant-menu-image">
                                    <# if ( item.image.url != '' ) { #>
                                        <#
                                        var image = {
                                            id: item.image.id,
                                            url: item.image.url,
                                            size: settings.image_size_size,
                                            dimension: settings.image_size_custom_dimension,
                                            model: view.getEditModel()
                                        };
                                        var image_url = elementor.imagesManager.getImageUrl( image );
                                        #>
                                        <img src="{{{ image_url }}}" />
                                    <# } #>
                                </div>
                            <# } #>

                            <div class="pp-restaurant-menu-content">
                                <div class="pp-restaurant-menu-header">
                                    <# if ( item.menu_title != '' ) { #>
                                        <h4 class="pp-restaurant-menu-title">
                                            <# if ( item.link && item.link.url ) { #>
                                                <a href="{{ item.link.url }}">
                                                    <?php $this->_title_template(); ?>
                                                </a>
                                            <# } else { #>
                                                <?php $this->_title_template(); ?>
                                            <# } #>
                                        </h4>
                                    <# } #>
                                        
                                    <# if ( settings.title_price_connector == 'yes' ) { #>
                                        <span class="pp-price-title-connector"></span>
                                    <# } #>
                                    
                                    <# if ( settings.menu_style == 'style-1' ) { #>
                                        <?php $this->_price_template(); ?>
                                    <# } #>
                                </div>
                                
                                <# if ( settings.title_separator == 'yes' ) { #>
                                    <div class="pp-price-menu-divider-wrap">
                                        <div class="pp-price-menu-divider"></div>
                                    </div>
                                <# } #>

                                <#
                                   if ( item.menu_description != '' ) {
                                        var description = item.menu_description;

                                        view.addRenderAttribute( 'menu_items.' + ($i - 1) + '.menu_description', 'class', 'pp-restaurant-menu-description' );

                                        view.addInlineEditingAttributes( 'menu_items.' + ($i - 1) + '.menu_description' );

                                        var description_html = '<div' + ' ' + view.getRenderAttributeString( 'menu_items.' + ($i - 1) + '.menu_description' ) + '>' + description + '</div>';

                                        print( description_html );
                                   }
                                #>

                                <# if ( settings.menu_style != 'style-1' ) { #>
                                    <?php $this->_price_template(); ?>
                                <# } #>
                            </div>
                        </div>
                    </div>
                <# $i++; } ); #>
            </div>
        </div>
        <?php
    }
}