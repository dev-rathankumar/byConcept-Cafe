<?php
/**
 * PowerPack WooCommerce Add To Cart Button.
 *
 * @package PowerPack
 */

namespace PowerpackElements\Modules\Woocommerce\Widgets;

use PowerpackElements\Base\Powerpack_Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Woo_Categories.
 */
class Woo_Categories extends Powerpack_Widget {

	/**
	 * Retrieve toggle widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Woo_Categories' );
	}

	/**
	 * Retrieve toggle widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Woo_Categories' );
	}

    /**
	 * Retrieve the list of categories the toggle widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Woo_Categories' );
    }

	/**
	 * Retrieve toggle widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Woo_Categories' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.4.11.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Woo_Categories' );
	}
    
    /**
	 * Retrieve the list of scripts the toggle widget depended on.
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
            'imagesloaded',
            'pp-woocommerce',
            'powerpack-frontend'
        ];
    }

    /**
	 * Retrieve the list of styles the Woo - Categories depended on.
	 *
	 * Used to set style dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_style_depends() {
        return [
            'pp-woocommerce'
        ];
    }

	/**
	 * Register controls.
	 *
	 * @since 1.3.3
	 * @access protected
	 */
	protected function _register_controls() {

		/* Product Control */
		$this->register_content_general_controls();
		$this->register_content_grid_controls();
		$this->register_content_carousel_controls();
		$this->register_content_filter_controls();

		/* Style */
		$this->register_style_layout_controls();
		$this->register_style_category_controls();
		$this->register_style_navigation_controls();
	}

	/**
	 * Register toggle widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_content_general_controls() {

		$this->start_controls_section(
			'section_layout',
			[
				'label'                 => __( 'Layout', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_CONTENT,
			]
		);
        
        $this->add_control(
            'layout',
            [
                'label'                 => __( 'Layout', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'grid',
                'options'               => [
                    'grid'		=> __( 'Grid', 'powerpack' ),
                    'carousel'	=> __( 'Carousel', 'powerpack' ),
                    'tiles'		=> __( 'Tiles', 'powerpack' ),
                ],
            ]
        );
        
        $this->add_control(
            'tiles_style',
            [
                'label'                 => __( 'Tiles Style', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => '1',
                'options'               => [
                    '1'		=> __( 'Style 1', 'powerpack' ),
                    '2'		=> __( 'Style 2', 'powerpack' ),
                ],
                'condition'             => [
                    'layout'	=> 'tiles',
                ],
            ]
        );

        $this->add_control(
            'cats_count',
            [
                'label'                 => __( 'Categories Count', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => '4',
            ]
        );
        
        $this->add_control(
            'content_position',
            [
                'label'                 => __( 'Content Position', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'overlay',
                'options'               => [
                    'default'   => __( 'Below Image', 'powerpack' ),
                    'overlay'   => __( 'Over Image', 'powerpack' ),
                ],
                'condition'             => [
                    'layout!'	=> 'tiles',
                ],
            ]
        );
        
        $this->add_control(
            'cat_title',
            [
                'label'                 => __( 'Category Title', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Show', 'powerpack' ),
                'label_off'             => __( 'Hide', 'powerpack' ),
                'return_value'          => 'yes',
            ]
        );
        
        $this->add_control(
            'product_count',
            [
                'label'                 => __( 'Product Count', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Show', 'powerpack' ),
                'label_off'             => __( 'Hide', 'powerpack' ),
                'return_value'          => 'yes',
            ]
        );
        
        $this->add_control(
            'cat_desc',
            [
                'label'                 => __( 'Category Description', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Show', 'powerpack' ),
                'label_off'             => __( 'Hide', 'powerpack' ),
                'return_value'          => 'yes',
            ]
        );
        
        $this->add_control(
            'category_desc_limit',
            [
                'label'                 => __( 'Words Limit', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'default'               => '',
                'condition'             => [
                    'cat_desc' => 'yes',
                ],
            ]
        );
        
		$this->end_controls_section();
	}

	/**
	 * Register Grid Controls.
	 *
	 * @access protected
	 */
	protected function register_content_grid_controls() {

		$this->start_controls_section(
			'section_grid_settings',
			[
				'label'                 => __( 'Grid Options', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_CONTENT,
                'condition'             => [
                    'layout!' => 'carousel',
                ],
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
                    'layout!' => 'carousel',
                ],
            ]
        );
        
		$this->end_controls_section();
	}

	/**
	 * Register Carousel Controls.
	 *
	 * @access protected
	 */
	protected function register_content_carousel_controls() {
		$this->start_controls_section(
			'section_carousel_options',
			[
				'label'                 => __( 'Carousel Options', 'powerpack' ),
				'type'                  => Controls_Manager::SECTION,
				'condition'             => [
					'layout'   => 'carousel',
				],
			]
		);

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label'                 => __( 'Categories to Show', 'powerpack' ),
				'type'                  => Controls_Manager::NUMBER,
				'default'               => 4,
				'tablet_default'        => 3,
				'mobile_default'        => 1,
				'condition'             => [
					'layout'   => 'carousel',
				],
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'label'                 => __( 'Categories to Scroll', 'powerpack' ),
				'type'                  => Controls_Manager::NUMBER,
				'default'               => 1,
				'tablet_default'        => 1,
				'mobile_default'        => 1,
				'condition'             => [
					'layout'   => 'carousel',
				],
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'                 => __( 'Autoplay', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'return_value'          => 'yes',
				'default'               => '',
				'condition'             => [
					'layout'   => 'carousel',
				],
			]
		);
		$this->add_control(
			'autoplay_speed',
			[
				'label'                 => __( 'Autoplay Speed', 'powerpack' ),
				'type'                  => Controls_Manager::NUMBER,
				'default'               => 5000,
				'selectors'             => [
					'{{WRAPPER}} .slick-slide-bg' => 'animation-duration: calc({{VALUE}}ms*1.2); transition-duration: calc({{VALUE}}ms)',
				],
				'condition'             => [
					'layout'   => 'carousel',
					'autoplay' => 'yes',
				],
			]
		);
		$this->add_control(
			'pause_on_hover',
			[
				'label'                 => __( 'Pause on Hover', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'return_value'          => 'yes',
				'default'               => 'yes',
				'condition'             => [
					'layout'   => 'carousel',
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'infinite',
			[
				'label'                 => __( 'Infinite Loop', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'return_value'          => 'yes',
				'default'               => 'yes',
				'condition'             => [
					'layout'   => 'carousel',
				],
			]
		);

		$this->add_control(
			'transition_speed',
			[
				'label'                 => __( 'Transition Speed (ms)', 'powerpack' ),
				'type'                  => Controls_Manager::NUMBER,
				'default'               => 500,
				'condition'             => [
					'layout'   => 'carousel',
				],
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'                 => __( 'Navigation', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'layout'   => 'carousel',
				],
			]
		);

		$this->add_control(
			'arrows',
			[
				'label'                 => __( 'Arrows', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'return_value'          => 'yes',
				'default'               => 'yes',
				'condition'             => [
					'layout'   => 'carousel',
				],
			]
		);

		$this->add_control(
			'dots',
			[
				'label'                 => __( 'Dots', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'return_value'          => 'yes',
				'default'               => 'yes',
				'condition'             => [
					'layout'   => 'carousel',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register Woo Products Filter Controls.
	 *
	 * @access protected
	 */
	protected function register_content_filter_controls() {

		$this->start_controls_section(
			'section_filter_field',
			[
				'label'                 => __( 'Filters', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control(
				'category_filter_rule',
				[
					'label'   => __( 'Category Filter Rule', 'powerpack' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'all',
					'options' => [
						'all'     => __( 'Show All', 'powerpack' ),
						'top'     => __( 'Only Top Level', 'powerpack' ),
						'include' => __( 'Match These Categories', 'powerpack' ),
						'exclude' => __( 'Exclude These Categories', 'powerpack' ),
					],
				]
			);
			$this->add_control(
				'category_filter',
				[
					'label'                 => __( 'Category Filter', 'powerpack' ),
					'type'                  => Controls_Manager::SELECT2,
					'multiple'  => true,
					'default'               => '',
					'options'   => $this->get_product_categories(),
					'condition'             => [
						'category_filter_rule' => [ 'include', 'exclude' ],
					],
				]
			);
			$this->add_control(
				'display_empty_cat',
				[
					'label'                 => __( 'Display Empty Categories', 'powerpack' ),
					'type'                  => Controls_Manager::SWITCHER,
					'default'               => '',
					'label_on'     => 'Yes',
					'label_off'    => 'No',
					'return_value'          => 'yes',
				]
			);
			$this->add_control(
				'orderby',
				[
					'label'   => __( 'Order by', 'powerpack' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'name',
					'options' => [
						'name'  => __( 'Name', 'powerpack' ),
						'slug'  => __( 'Slug', 'powerpack' ),
						'desc'  => __( 'Description', 'powerpack' ),
						'count' => __( 'Count', 'powerpack' ),
					],
				]
			);

			$this->add_control(
				'order',
				[
					'label'   => __( 'Order', 'powerpack' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'desc',
					'options' => [
						'desc' => __( 'Descending', 'powerpack' ),
						'asc'  => __( 'Ascending', 'powerpack' ),
					],
				]
			);
		$this->end_controls_section();
	}

	/**
	 * Style Tab
	 */
	/**
	 * Register Layout Controls.
	 *
	 * @since 1.3.3
	 * @access protected
	 */
	protected function register_style_layout_controls() {
		$this->start_controls_section(
			'section_design_layout',
			[
				'label'                 => __( 'Layout', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'                 => __( 'Columns Gap', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 20,
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-categories .product' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .pp-woo-categories ul.products, {{WRAPPER}} .pp-woo-categories .products .pp-elementor-grid, {{WRAPPER}} .pp-woo-categories .products .slick-list' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'                 => __( 'Rows Gap', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 35,
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-categories-grid li.product, {{WRAPPER}} .pp-woo-categories-tiles .product' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-woo-cat-tiles-1 .pp-woo-cat-tiles-center .product, {{WRAPPER}} .pp-woo-cat-tiles-2 .pp-woo-cat-tiles-right .product' => 'height: calc((550px - {{SIZE}}{{UNIT}}) / 2);',
				],
                'condition'             => [
                    'layout' => ['grid', 'tiles'],
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'column_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'separator'             => 'before',
				'selector'              => '{{WRAPPER}} .pp-woo-categories .product-category .pp-grid-item',
			]
		);

            $this->add_control(
                'column_border_color_hover',
                [
                    'label'                 => __( 'Border Hover Color', 'powerpack' ),
                    'type'                  => Controls_Manager::COLOR,
                    'selectors'             => [
                        '{{WRAPPER}} .pp-woo-categories .product-category .pp-grid-item:hover' => 'border-color: {{VALUE}};',
                    ],
                ]
            );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'column_box_shadow',
				'separator'	=> 'before',
				'selector' 	=> '{{WRAPPER}} .pp-woo-categories .product-category .pp-grid-item',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register Category Content Controls.
	 *
	 * @since 1.3.3
	 * @access protected
	 */
	protected function register_style_category_controls() {
		$this->start_controls_section(
			'section_design_cat_content',
			[
				'label'                 => __( 'Category Content', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);
        
        $this->add_control(
			'cat_content_vertical_align',
			[
				'label'                 => __( 'Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
                'label_block'           => false,
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
					'{{WRAPPER}} .pp-woo-categories-overlay .product .pp-product-cat-content-wrap'   => 'justify-content: {{VALUE}};',
				],
				'selectors_dictionary'  => [
					'top'          => 'flex-start',
					'middle'       => 'center',
					'bottom'       => 'flex-end',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'relation' => 'or',
									'terms' => [
										[
											'name' => 'layout',
											'operator' => '==',
											'value' => 'grid',
										],
										[
											'name' => 'layout',
											'operator' => '==',
											'value' => 'carousel',
										],
									],
								],
								[
									'name' => 'content_position',
									'operator' => '==',
									'value' => 'overlay',
								],
							],
						],
						[
							'name' => 'layout',
							'operator' => '==',
							'value' => 'tiles',
						],
					],
				],
			]
		);
        
        $this->add_control(
			'cat_content_horizontal_align',
			[
				'label'                 => __( 'Horizontal Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
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
					'justify'   => [
						'title'    => __( 'Stretch', 'powerpack' ),
						'icon'     => 'eicon-h-align-stretch',
					],
				],
				'default'               => 'center',
                'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'center'   => 'center',
					'right'    => 'flex-end',
					'justify'  => 'stretch',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-categories-overlay .product .pp-product-cat-content-wrap' => 'align-items: {{VALUE}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'relation' => 'or',
									'terms' => [
										[
											'name' => 'layout',
											'operator' => '==',
											'value' => 'grid',
										],
										[
											'name' => 'layout',
											'operator' => '==',
											'value' => 'carousel',
										],
									],
								],
								[
									'name' => 'content_position',
									'operator' => '==',
									'value' => 'overlay',
								],
							],
						],
						[
							'name' => 'layout',
							'operator' => '==',
							'value' => 'tiles',
						],
					],
				],
			]
		);
			$this->add_control(
				'cat_content_text_align',
				[
					'label'                 => __( 'Text Alignment', 'powerpack' ),
					'type'                  => Controls_Manager::CHOOSE,
					'label_block'  => false,
					'options'      => [
						'left'   => [
							'title' => __( 'Left', 'powerpack' ),
							'icon'  => 'fa fa-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'powerpack' ),
							'icon'  => 'fa fa-align-center',
						],
						'right'  => [
							'title' => __( 'Right', 'powerpack' ),
							'icon'  => 'fa fa-align-right',
						],
					],
					'default'               => 'center',
                    'selectors'             => [
                        '{{WRAPPER}} .pp-woo-categories .product .pp-product-cat-content' => 'text-align: {{VALUE}};',
                    ],
					'separator'             => 'after',
				]
			);

			$this->start_controls_tabs( 'cat_content_tabs_style' );

            $this->start_controls_tab(
                'cat_content_normal',
                [
                    'label'                 => __( 'Normal', 'powerpack' ),
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name'                  => 'cat_content_background',
                    'types'                 => [ 'classic', 'gradient' ],
                    'selector'              => '{{WRAPPER}} .pp-woo-categories .product .pp-product-cat-content',
                ]
            );

			$this->add_control(
				'cat_content_margin',
				[
					'label'                 => __( 'Margin', 'powerpack' ),
					'type'                  => Controls_Manager::DIMENSIONS,
					'size_units'            => [ 'px', 'em', '%' ],
					'selectors'             => [
						'{{WRAPPER}} .pp-woo-categories .product .pp-product-cat-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator'             => 'before',
				]
			);

			$this->add_control(
				'cat_content_padding',
				[
					'label'                 => __( 'Padding', 'powerpack' ),
					'type'                  => Controls_Manager::DIMENSIONS,
					'size_units'            => [ 'px', 'em', '%' ],
					'default'               => [
						'top'      => '10',
						'right'    => '10',
						'bottom'   => '10',
						'left'     => '10',
						'isLinked' => true,
					],
					'selectors'             => [
						'{{WRAPPER}} .pp-woo-categories .product .pp-product-cat-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'cat_content_title_heading',
				[
					'label'                 => __( 'Title', 'powerpack' ),
					'type'                  => Controls_Manager::HEADING,
					'separator'             => 'before',
                    'condition'             => [
                        'cat_title' => 'yes',
                    ],
				]
			);

            $this->add_control(
                'cat_title_color',
                [
                    'label'                 => __( 'Color', 'powerpack' ),
                    'type'                  => Controls_Manager::COLOR,
                    'selectors'             => [
                        '{{WRAPPER}} .pp-woo-categories .product .woocommerce-loop-category__title' => 'color: {{VALUE}};',
                    ],
                    'condition'             => [
                        'cat_title' => 'yes',
                    ],
                ]
            );

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'                 => 'cat_content_title_typography',
					'label'                => __( 'Typography', 'powerpack' ),
					'selector'             => '{{WRAPPER}} .pp-woo-categories .product .woocommerce-loop-category__title',
                    'condition'             => [
                        'cat_title' => 'yes',
                    ],
				]
			);

			$this->add_control(
				'cat_content_count_heading',
				[
					'label'                 => __( 'Product Count', 'powerpack' ),
					'type'                  => Controls_Manager::HEADING,
					'separator'             => 'before',
                    'condition'             => [
                        'product_count' => 'yes',
                    ],
				]
			);

            $this->add_control(
                'cat_count_color',
                [
                    'label'                 => __( 'Color', 'powerpack' ),
                    'type'                  => Controls_Manager::COLOR,
                    'selectors'             => [
                        '{{WRAPPER}} .pp-woo-categories .product .pp-product-cat-content .pp-count' => 'color: {{VALUE}};',
                    ],
                    'condition'             => [
                        'product_count' => 'yes',
                    ],
                ]
            );

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'                 => 'cat_content_count_typography',
					'label'                => __( 'Typography', 'powerpack' ),
					'selector'             => '{{WRAPPER}} .pp-woo-categories .product .pp-product-cat-content .pp-count',
                    'condition'             => [
                        'product_count' => 'yes',
                    ],
				]
			);

			$this->add_control(
				'cat_desc_heading',
				[
					'label'                 => __( 'Category Description', 'powerpack' ),
					'type'                  => Controls_Manager::HEADING,
					'separator'             => 'before',
                    'condition'             => [
                        'cat_desc'  => 'yes',
                    ],
				]
			);

            $this->add_control(
                'cat_desc_color',
                [
                    'label'                 => __( 'Color', 'powerpack' ),
                    'type'                  => Controls_Manager::COLOR,
                    'selectors'             => [
                        '{{WRAPPER}} .pp-woo-categories .product .pp-product-cat-content .pp-product-cat-desc' => 'color: {{VALUE}};',
                    ],
                    'condition'             => [
                        'cat_desc'  => 'yes',
                    ],
                ]
            );

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'                 => 'cat_desc_typography',
					'label'                => __( 'Typography', 'powerpack' ),
					'selector'             => '{{WRAPPER}} .pp-woo-categories .product .pp-product-cat-content .pp-product-cat-desc',
                    'condition'             => [
                        'cat_desc'  => 'yes',
                    ],
				]
			);

			$this->add_control(
				'cat_content_opacity',
				[
					'label'                 => __( 'Opacity', 'powerpack' ),
					'type'                  => Controls_Manager::SLIDER,
					'default'               => [
						'size' => 1,
					],
					'range'                 => [
						'px' => [
							'min'   => 0,
                            'max'   => 1,
                            'step'  => 0.01,
						],
					],
					'selectors'             => [
						'{{WRAPPER}} .pp-woo-categories .product .pp-product-cat-content' => 'opacity: {{SIZE}};',
					],
					'separator'             => 'before',
                    'condition'             => [
                        'content_position' => 'overlay',
                    ],
				]
			);

            $this->end_controls_tab();

            $this->start_controls_tab(
                'cat_content_hover',
                [
                    'label'                 => __( 'Hover', 'powerpack' ),
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name'                  => 'cat_content_background_hover',
                    'types'                 => [ 'classic', 'gradient' ],
                    'selector'              => '{{WRAPPER}} .pp-woo-categories .product-category .pp-grid-item:hover .pp-product-cat-content',
					'separator'             => 'after',
                ]
            );

            $this->add_control(
                'cat_content_hover_title_color',
                [
                    'label'                 => __( 'Title Color', 'powerpack' ),
                    'type'                  => Controls_Manager::COLOR,
                    'selectors'             => [
                        '{{WRAPPER}} .pp-woo-categories .product-category .pp-grid-item:hover .woocommerce-loop-category__title' => 'color: {{VALUE}};',
                    ],
                    'condition'             => [
                        'cat_title' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'cat_content_hover_count_color',
                [
                    'label'                 => __( 'Product Count Color', 'powerpack' ),
                    'type'                  => Controls_Manager::COLOR,
                    'selectors'             => [
                        '{{WRAPPER}} .pp-woo-categories .product-category .pp-grid-item:hover .pp-product-cat-content .pp-count' => 'color: {{VALUE}};',
                    ],
                    'condition'             => [
                        'product_count' => 'yes',
                    ],
                ]
            );

            $this->add_control(
                'cat_hover_desc_color',
                [
                    'label'                 => __( 'Description Color', 'powerpack' ),
                    'type'                  => Controls_Manager::COLOR,
                    'selectors'             => [
                        '{{WRAPPER}} .pp-woo-categories .product-category .pp-grid-item:hover .pp-product-cat-content .pp-product-cat-desc' => 'color: {{VALUE}};',
                    ],
                    'condition'             => [
                        'cat_desc'  => 'yes',
                    ],
                ]
            );

			$this->add_control(
				'cat_content_opacity_hover',
				[
					'label'                 => __( 'Opacity', 'powerpack' ),
					'type'                  => Controls_Manager::SLIDER,
					'default'               => [
						'size' => 1,
					],
					'range'                 => [
						'px' => [
							'min'   => 0,
                            'max'   => 1,
                            'step'  => 0.01,
						],
					],
					'selectors'             => [
						'{{WRAPPER}} .pp-woo-categories .product .pp-grid-item:hover .pp-product-cat-content' => 'opacity: {{SIZE}};',
					],
					'separator'             => 'before',
                    'condition'             => [
                        'content_position' => 'overlay',
                    ],
				]
			);

            $this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Category Description Content Controls.
	 *
	 * @access protected
	 */
	protected function register_style_navigation_controls() {

        /**
         * Style Tab: Arrows
         */
        $this->start_controls_section(
            'section_arrows_style',
            [
                'label'                 => __( 'Arrows', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
					'layout'   => 'carousel',
                    'arrows'   => 'yes',
                ],
            ]
        );
        
        $this->add_control(
			'arrow',
			[
				'label'                 => __( 'Choose Arrow', 'powerpack' ),
				'type'                  => Controls_Manager::ICON,
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
				'default'               => 'fa fa-angle-right',
				'frontend_available'    => true,
                'condition'             => [
                    'layout'   => 'carousel',
                    'arrows'   => 'yes',
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
                    'layout'   => 'carousel',
                    'arrows'   => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'arrows_position',
            [
                'label'                 => __( 'Align Arrows', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => -100,
                        'max'   => 50,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-arrow-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-arrow-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'   => 'carousel',
                    'arrows'   => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_arrows_style' );

        $this->start_controls_tab(
            'tab_arrows_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
                    'layout'   => 'carousel',
                    'arrows'   => 'yes',
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
                    'layout'   => 'carousel',
                    'arrows'   => 'yes',
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
                    'layout'   => 'carousel',
                    'arrows'   => 'yes',
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
                'condition'             => [
                    'layout'   => 'carousel',
                    'arrows'   => 'yes',
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
                    'layout'   => 'carousel',
                    'arrows'   => 'yes',
                ],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_arrows_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'layout'   => 'carousel',
                    'arrows'   => 'yes',
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
                    'layout'   => 'carousel',
                    'arrows'   => 'yes',
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
                    'layout'   => 'carousel',
                    'arrows'   => 'yes',
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
                    'layout'   => 'carousel',
                    'arrows'   => 'yes',
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
                    'layout'   => 'carousel',
                    'arrows'   => 'yes',
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
                'label'                 => __( 'Dots', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
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
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
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
                    '{{WRAPPER}} .pp-woo-categories-carousel .slick-dots li button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'dots_spacing',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 30,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-categories-carousel .slick-dots li' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_dots_style' );

        $this->start_controls_tab(
            'tab_dots_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
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
                    '{{WRAPPER}} .pp-woo-categories-carousel .slick-dots li' => 'background: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
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
				'selector'              => '{{WRAPPER}} .pp-woo-categories-carousel .slick-dots li',
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
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
					'{{WRAPPER}} .pp-woo-categories-carousel .slick-dots li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
			]
		);

		$this->add_responsive_control(
			'dots_margin',
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
					'{{WRAPPER}} .pp-woo-categories-carousel .slick-dots' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_dots_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
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
                    '{{WRAPPER}} .pp-woo-categories-carousel .slick-dots li:hover' => 'background: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
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
                    '{{WRAPPER}} .pp-woo-categories-carousel .slick-dots li:hover' => 'border-color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
            ]
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_dots_active',
            [
                'label'                 => __( 'Active', 'powerpack' ),
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
            ]
        );

        $this->add_control(
            'dots_color_active',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-categories-carousel .slick-dots li.slick-active' => 'background: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
            ]
        );

        $this->add_control(
            'dots_border_color_active',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-categories-carousel .slick-dots li.slick-active' => 'border-color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
	}

	/**
	 * Get WooCommerce Product Categories.
	 *
	 * @since 1.3.3
	 * @access protected
	 */
	protected function get_product_categories() {

		$product_cat = array();

		$cat_args = array(
			'orderby'    => 'name',
			'order'      => 'asc',
			'hide_empty' => false,
		);

		$product_categories = get_terms( 'product_cat', $cat_args );

		if ( ! empty( $product_categories ) ) {

			foreach ( $product_categories as $key => $category ) {

				$product_cat[ $category->term_id ] = $category->name;
			}
		}

		return $product_cat;
	}

	/**
	 * List all product categories.
	 *
	 * @return string
	 */
	public function query_product_categories() {

		$settings    = $this->get_settings();
		$include_ids = array();
		$exclude_ids = array();

		$atts = array(
			'limit'   => ( $settings['cats_count'] ) ? $settings['cats_count'] : '-1',
			'columns' => ( $settings['columns'] ) ? $settings['columns'] : '4',
			'parent'  => '',
		);

		if ( 'top' === $settings['category_filter_rule'] ) {
			$atts['parent'] = 0;
		} elseif ( 'include' === $settings['category_filter_rule'] && is_array( $settings['category_filter'] ) ) {
			$include_ids = array_filter( array_map( 'trim', $settings['category_filter'] ) );
		} elseif ( 'exclude' === $settings['category_filter_rule'] && is_array( $settings['category_filter'] ) ) {
			$exclude_ids = array_filter( array_map( 'trim', $settings['category_filter'] ) );
		}

		$hide_empty = ( 'yes' === $settings['display_empty_cat'] ) ? 0 : 1;

		// Get terms and workaround WP bug with parents/pad counts.
		$args = array(
			'orderby'    => ( $settings['orderby'] ) ? $settings['orderby'] : 'name',
			'order'      => ( $settings['order'] ) ? $settings['order'] : 'ASC',
			'hide_empty' => $hide_empty,
			'pad_counts' => true,
			'child_of'   => $atts['parent'],
			'include'    => $include_ids,
			'exclude'    => $exclude_ids,
		);

		$product_categories = get_terms( 'product_cat', $args );

		if ( '' !== $atts['parent'] ) {
			$product_categories = wp_list_filter(
				$product_categories, array(
					'parent' => $atts['parent'],
				)
			);
		}

		if ( $hide_empty ) {
			foreach ( $product_categories as $key => $category ) {
				if ( 0 === $category->count ) {
					unset( $product_categories[ $key ] );
				}
			}
		}

		$atts['limit'] = intval( $atts['limit'] );

		if ( $atts['limit'] > 0 ) {
			$product_categories = array_slice( $product_categories, 0, $atts['limit'] );
		}
        
        $columns = absint( $atts['columns'] );

		wc_set_loop_prop( 'columns', $columns );

		/* Category Link */
		remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
		add_action( 'woocommerce_before_subcategory', array( $this, 'template_loop_category_link_open' ), 10 );

		/* Category Wrapper */
		add_action( 'woocommerce_before_subcategory', array( $this, 'category_wrap_start' ), 15 );
		add_action( 'woocommerce_after_subcategory', array( $this, 'category_wrap_end' ), 8 );

		/* Content Wrapper */
		add_action( 'woocommerce_before_subcategory_title', array( $this, 'category_content_start' ), 15 );
		add_action( 'woocommerce_after_subcategory_title', array( $this, 'category_content_end' ), 8 );

		if ( 'yes' === $settings['cat_desc'] ) {
			add_action( 'woocommerce_shop_loop_subcategory_title', array( $this, 'category_description' ), 12 );
		}

		/* Category Title */
		remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );
		add_action( 'woocommerce_shop_loop_subcategory_title', array( $this, 'template_loop_category_title' ), 10 );

		ob_start();

		if ( $product_categories ) {
			$i = 1;
			$pp_products_count =  count($product_categories);

			if ( $settings['layout'] == 'tiles' ) {
				echo '<div class="products">';
				
				$pp_tiles_template = ( $settings['layout'] == 'tiles' && $settings['tiles_style'] ) ? $settings['tiles_style'] : '1';
				
				foreach ( $product_categories as $category ) {

					include POWERPACK_ELEMENTS_PATH . 'modules/woocommerce/templates/content-product-cat-tiles-' . $pp_tiles_template . '.php';
					$i++;
				}

				echo '</div>';
			
				if ( $pp_tiles_template == '1' ) {
					if ( 4 > $pp_products_count ) {
						echo '</div>';
					}
				} elseif ( $pp_tiles_template == '2' ) {
					if ( 3 > $pp_products_count ) {
						echo '</div>';
					}
				}
			} elseif ( $settings['layout'] == 'carousel' ) {
				echo '<div class="products pp-slick-slider">';
				
				foreach ( $product_categories as $category ) {

					include POWERPACK_ELEMENTS_PATH . 'modules/woocommerce/templates/content-product-cat-carousel.php';
					$i++;
				}

				echo '</div>';
			} else {
				echo '<ul class="products pp-elementor-grid columns-'. $settings['columns'] .'">';
				
				foreach ( $product_categories as $category ) {

					include POWERPACK_ELEMENTS_PATH . 'modules/woocommerce/templates/content-product-cat.php';
					$i++;
				}

				echo '</ul>';
			}
		}

		woocommerce_reset_loop();
        
        $this->add_render_attribute( 'categories-inner', 'class', [
            'pp-woo-categories-inner',
        ] );

		$inner_content = ob_get_clean();

		/* Category Link */
		add_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
		remove_action( 'woocommerce_before_subcategory', array( $this, 'template_loop_category_link_open' ), 10 );

		/* Category Wrapper */
		remove_action( 'woocommerce_before_subcategory', array( $this, 'category_wrap_start' ), 15 );
		remove_action( 'woocommerce_after_subcategory', array( $this, 'category_wrap_end' ), 8 );

		if ( 'yes' === $settings['cat_desc'] ) {
			remove_action( 'woocommerce_after_subcategory', array( $this, 'category_description' ), 8 );
		}

		/* Category Title */
		remove_action( 'woocommerce_shop_loop_subcategory_title', array( $this, 'template_loop_category_title' ), 10 );
		add_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title', 10 );

		return '<div ' . $this->get_render_attribute_string( 'categories-inner' ) . '>' . $inner_content . '</div>';
	}

	/**
	 * Wrapper Start.
	 *
	 * @param object $category Category object.
	 */
	function template_loop_category_link_open( $category ) {
		$link = apply_filters( 'pp_woo_category_link', esc_url( get_term_link( $category, 'product_cat' ) ) );

		echo '<a href="' . $link . '">';
	}

	/**
	 * Wrapper Start.
	 *
	 * @param object $category Category object.
	 */
	public function category_wrap_start( $category ) {
		echo '<div class="pp-product-cat-inner">';
	}


	/**
	 * Wrapper End.
	 *
	 * @param object $category Category object.
	 */
	public function category_wrap_end( $category ) {
		echo '</div>';
	}

	/**
	 * Content Start.
	 *
	 * @param object $category Category object.
	 */
	public function category_content_start( $category ) {
		echo '<div class="pp-product-cat-content-wrap">';
		echo '<div class="pp-product-cat-content">';
	}


	/**
	 * Content End.
	 *
	 * @param object $category Category object.
	 */
	public function category_content_end( $category ) {
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Category Description.
	 *
	 * @param object $category Category object.
	 */
	public function category_description( $category ) {

		$settings = $this->get_settings();

		if ( $category && ! empty( $category->description ) ) {

			echo '<div class="pp-product-cat-desc">';
			if( $settings['category_desc_limit'] != '' ) {
				echo '<div class="pp-term-description">' . wp_trim_words(wc_format_content( $category->description ), $settings['category_desc_limit'], '') . '</div>'; // WPCS: XSS ok.
			} else {
				echo '<div class="pp-term-description">' . wc_format_content( $category->description ) . '</div>'; // WPCS: XSS ok.
			}
			echo '</div>';
		}
	}


	/**
	 * Show the subcategory title in the product loop.
	 *
	 * @param object $category Category object.
	 */
	public function template_loop_category_title( $category ) {
        
        $settings = $this->get_settings();
        
		$output          = '<div class="pp-category__title-wrap">';
		$output          .= '<div class="pp-category__title-inner">';
        if ( $settings['cat_title'] == 'yes' ) {
			$output     .= '<h2 class="woocommerce-loop-category__title">';
				$output .= esc_html( $category->name );
			$output     .= '</h2>';
        }

        if ( $settings['product_count'] == 'yes' ) {
            if ( $category->count > 0 ) {
                    $output .= sprintf( // WPCS: XSS OK.
                        /* translators: 1: number of products */
                        _nx( '<mark class="pp-count">%1$s Product</mark>', '<mark class="pp-count">%1$s Products</mark>', $category->count, 'product categories', 'powerpack' ),
                        number_format_i18n( $category->count )
                    );
            }
        }
		$output .= '</div>';
		$output .= '</div>';

		echo $output;
	}

	/**
	 * Set slider attributes.
	 *
	 * @access public
	 */
	public function set_slider_attr() {
        
        $settings = $this->get_settings();
        
        if ( $settings['layout'] !=='carousel' ) {
			return;
		}
        
        $is_rtl      = is_rtl();
        
        $slick_options = [
            'slidesToShow'   => ( $settings['slides_to_show'] ) ? $settings['slides_to_show'] : '4',
			'autoplay'       => ( 'yes' === $settings['autoplay'] ),
			'autoplaySpeed'  => ( $settings['autoplay_speed'] ) ? absint( $settings['autoplay_speed'] ) : 5000,
			'pauseOnHover'   => ( 'yes' === $settings['pause_on_hover'] ),
			'infinite'       => ( 'yes' === $settings['infinite'] ),
			'speed'          => ( $settings['transition_speed'] ) ? absint( $settings['transition_speed'] ) : 500,
			'arrows'         => ( 'yes' === $settings['arrows'] ),
			'dots'           => ( 'yes' === $settings['dots'] ),
			'prevArrow'      => '<div class="pp-slider-arrow pp-arrow pp-arrow-prev slick-prev slick-arrow"><i class="fa fa-angle-left"></i></div>',
			'nextArrow'      => '<div class="pp-slider-arrow pp-arrow pp-arrow-next slick-next slick-arrow"><i class="fa fa-angle-right"></i></div>',
			'rtl'            => $is_rtl,
        ];

		if ( $settings['slides_to_show_tablet'] || $settings['slides_to_show_mobile'] ) {

			$slick_options['responsive'] = [];

			if ( $settings['slides_to_show_tablet'] ) {

				$tablet_slides_show   = absint( $settings['slides_to_show_tablet'] );
				$tablet_slides_scroll = ( $settings['slides_to_scroll_tablet'] ) ? absint( $settings['slides_to_scroll_tablet'] ) : $tablet_slides_show;

				$slick_options['responsive'][] = [
					'breakpoint' => 1024,
					'settings'   => [
						'slidesToShow'   => $tablet_slides_show,
						'slidesToScroll' => $tablet_slides_scroll,
					],
				];
			}

			if ( $settings['slides_to_show_mobile'] ) {

				$mobile_slides_show   = absint( $settings['slides_to_show_mobile'] );
				$mobile_slides_scroll = ( $settings['slides_to_scroll_mobile'] ) ? absint( $settings['slides_to_scroll_mobile'] ) : $mobile_slides_show;

				$slick_options['responsive'][] = [
					'breakpoint' => 767,
					'settings'   => [
						'slidesToShow'   => $mobile_slides_show,
						'slidesToScroll' => $mobile_slides_scroll,
					],
				];
			}
		}
        
        $this->add_render_attribute(
			'container', [
				'data-cat-carousel-options' => wp_json_encode( $slick_options ),
			]
		);
	}

	/**
	 * Render output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings();
        
        $this->add_render_attribute( 'container', 'class', [
            'pp-woocommerce',
            'pp-woo-categories',
            'pp-woo-categories-' . $settings['layout'],
        ] );
        
		if ( $settings['layout'] == 'tiles' ) {
			$this->add_render_attribute( 'container', 'class', [
				'pp-woo-categories-overlay'
			] );
		} else {
			$this->add_render_attribute( 'container', 'class', [
				'pp-woo-categories-' . $settings['content_position']
			] );
		}
        
		$this->set_slider_attr();
        ?>
        <div <?php echo $this->get_render_attribute_string( 'container' ); ?>>
			<?php echo $this->query_product_categories(); ?>
        </div>
		<?php
	}
}
