<?php
namespace PowerpackElements\Modules\Coupons\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Posts_Helper;
use PowerpackElements\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Repeater;
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
 * Coupons Widget
 */
class Coupons extends Powerpack_Widget {
    
    /**
	 * Retrieve coupons widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return parent::get_widget_name( 'Coupons' );
    }

    /**
	 * Retrieve coupons widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
    public function get_title() {
        return parent::get_widget_title( 'Coupons' );
    }

    /**
	 * Retrieve the list of categories the coupons widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Coupons' );
    }

    /**
	 * Retrieve coupons widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
    public function get_icon() {
        return parent::get_widget_icon( 'Coupons' );
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
		return parent::get_widget_keywords( 'Coupons' );
	}
	
	/**
	 * Retrieve the list of scripts the coupons carousel widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_script_depends() {
        return [
            'jquery-swiper',
            'powerpack-frontend'
        ];
    }

    /**
	 * Register coupons carousel widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
    protected function _register_controls() {
		
		/* Content Tab */
		$this->register_content_general_controls();
		$this->register_content_coupons_controls();
		$this->register_content_post_query_controls();
		$this->register_content_link_controls();
		$this->register_content_carousel_settings_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_coupon_box_controls();
		$this->register_style_discount_controls();
		$this->register_style_coupon_code_controls();
		$this->register_style_content_controls();
		$this->register_style_button_controls();
		$this->register_style_arrows_controls();
		$this->register_style_dots_controls();
		$this->register_style_fraction_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	protected function register_content_general_controls() {
        /**
         * Content Tab: General
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_general',
            [
                'label'                 => __( 'General', 'powerpack' ),
            ]
        );

        $this->add_control(
            'source',
            [
                'label'                 => __( 'Source', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => [
                   'custom'     => __( 'Custom', 'powerpack' ),
                   'posts'      => __( 'Posts', 'powerpack' ),
                ],
                'default'               => 'custom',
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
                ],
                'frontend_available'    => true,
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
                ],
                'prefix_class'          => 'elementor-grid%s-',
				'render_type'           => 'template',
                'frontend_available'    => true,
				'conditions'			=> [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'layout',
							'operator' => '==',
							'value' => 'grid',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'layout',
									'operator' => '==',
									'value' => 'carousel',
								],
								[
									'relation' => 'or',
									'terms' => [
										[
											'name' => 'carousel_effect',
											'operator' => '==',
											'value' => 'slide',
										],
										[
											'name' => 'carousel_effect',
											'operator' => '==',
											'value' => 'coverflow',
										],
									],
								],
							],
						],
					],
				],
            ]
        );
        
		$this->add_control(
			'coupon_style',
			[
				'label'                 => __( 'Coupon Style', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'copy',
				'options'               => [
					'copy'			=> __( 'Click to Copy Code', 'powerpack' ),
					'reveal'		=> __( 'Click to Reveal Code and Copy', 'powerpack' ),
					'no-code'		=> __( 'No Code Needed', 'powerpack' ),
				],
                'frontend_available'    => true,
			]
		);

		$this->add_control(
			'coupon_reveal',
			[
				'label'                 => __( 'Reveal Text', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => __( 'Click to Reveal Coupon Code', 'powerpack' ),
				'condition'             => [
					'coupon_style'		=> 'reveal',
				],
			]
		);

		$this->add_control(
			'no_code_need',
			[
				'label'                 => __( 'No Code Text', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
				'default'				=> __( 'No Code Needed', 'powerpack' ),
				'condition'             => [
					'coupon_style'		=> 'no-code',
				],
			]
		);
        
		$this->add_control(
			'icon_type',
			[
				'label'                 => esc_html__( 'Coupon Icon', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'toggle'				=> false,
				'options'               => [
					'none' => [
						'title' => esc_html__( 'None', 'powerpack' ),
						'icon' => 'fa fa-ban',
					],
					'icon' => [
						'title' => esc_html__( 'Icon', 'powerpack' ),
						'icon' => 'fa fa-gear',
					],
					'image' => [
						'title' => esc_html__( 'Image', 'powerpack' ),
						'icon' => 'fa fa-picture-o',
					],
				],
				'default'               => 'icon',
			]
		);

		$this->add_control(
			'icon',
			[
				'label'					=> __( 'Choose Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'label_block'			=> true,
				'default'				=> [
					'value'		=> 'fas fa-check',
					'library'	=> 'fa-solid',
				],
				'condition'             => [
					'icon_type'     => 'icon',
				],
			]
		);

		$this->add_control(
			'icon_image',
			[
				'label'                 => __( 'Choose Image', 'powerpack' ),
				'type'                  => Controls_Manager::MEDIA,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition'             => [
					'icon_type' => 'image',
				],
			]
		);
        
        $this->add_control(
            'show_discount',
            [
                'label'             	=> __( 'Show Discount', 'powerpack' ),
                'type'              	=> Controls_Manager::SWITCHER,
                'default'           	=> 'yes',
                'label_on'          	=> __( 'Yes', 'powerpack' ),
                'label_off'         	=> __( 'No', 'powerpack' ),
                'return_value'      	=> 'yes',
            ]
        );

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.,
                'label'                 => __( 'Image Size', 'powerpack' ),
                'default'               => 'full',
                'separator'         	=> 'before',
			]
		);
        
        $this->add_control(
            'title_html_tag',
            [
                'label'                 => __( 'Title HTML Tag', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'h4',
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
        
        $this->end_controls_section();
	}
     
	protected function register_content_coupons_controls() {
        /**
         * Content Tab: Coupons
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_coupons',
            [
                'label'                     => __( 'Coupons', 'powerpack' ),
            ]
        );
        
        $repeater = new Repeater();
        
        $repeater->start_controls_tabs( 'items_repeater' );
        
        $repeater->start_controls_tab( 'tab_icon', [ 'label' => __( 'General', 'powerpack' ) ] );

            $repeater->add_control(
                'image',
                [
                    'label'                 => __( 'Image', 'powerpack' ),
                    'type'                  => Controls_Manager::MEDIA,
                    'dynamic'               => [
                        'active'   => true,
                    ],
                    'default'               => [
                        'url' => Utils::get_placeholder_image_src(),
                    ],
                ]
            );

            $repeater->add_control(
                'discount',
                [
                    'label'                 => __( 'Discount', 'powerpack' ),
                    'type'                  => Controls_Manager::TEXT,
                    'dynamic'               => [
                        'active'   => true,
                    ],
                    'default'               => '10% OFF',
                ]
            );

            $repeater->add_control(
                'coupon_code',
                [
                    'label'                 => __( 'Coupon Code', 'powerpack' ),
                    'type'                  => Controls_Manager::TEXT,
                    'dynamic'               => [
                        'active'   => true,
                    ],
                    'default'               => 'ABCDEF',
                ]
            );
        
        $repeater->end_controls_tab();

        $repeater->start_controls_tab( 'tab_content', [ 'label' => __( 'Content', 'powerpack' ) ] );

            $repeater->add_control(
                'title',
                [
                    'label'                 => __( 'Title', 'powerpack' ),
                    'type'                  => Controls_Manager::TEXT,
                    'dynamic'               => [
                        'active'   => true,
                    ],
                    'default'               => __( 'Title', 'powerpack' ),
                ]
            );

            $repeater->add_control(
                'description',
                [
                    'label'                 => __( 'Description', 'powerpack' ),
                    'type'                  => Controls_Manager::WYSIWYG,
                    'dynamic'               => [
                        'active'   => true,
                    ],
                    'default'               => __( 'Enter coupons description', 'powerpack' ),
                ]
            );
        
        $repeater->end_controls_tab();
        
        $repeater->start_controls_tab( 'tab_link', [ 'label' => __( 'Link', 'powerpack' ) ] );

        $repeater->add_control(
            'link',
            [
                'label'                 => __( 'Link', 'powerpack' ),
                'type'                  => Controls_Manager::URL,
                'dynamic'               => [
                    'active'   => true,
                ],
                'placeholder'           => 'https://www.your-link.com',
                'default'               => [
                    'url' => '#',
                ],
            ]
        );
        
        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $this->add_control(
            'pp_coupons',
            [
                'label' 	=> '',
                'type' 		=> Controls_Manager::REPEATER,
                'default' 	=> [
                    [
                        'title' => __( 'Coupon 1', 'powerpack' ),
                    ],
                    [
                        'title' => __( 'Coupon 2', 'powerpack' ),
                    ],
                    [
                        'title' => __( 'Coupon 3', 'powerpack' ),
                    ],
                ],
                'fields' 		=> array_values( $repeater->get_controls() ),
                'title_field' 	=> '{{{ title }}}',
                'condition'             => [
                    'source'	=> 'custom'
                ]
            ]
        );
        
        $this->add_control(
            'posts_per_page',
            [
                'label'                 => __( 'Coupons Count', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => 6,
                'condition'             => [
                    'source'	=> 'posts'
                ]
            ]
        );
		
		$this->add_control(
			'posts_content_type',
			[
				'label'					=> __( 'Content Type', 'powerpack' ),
				'type'					=> Controls_Manager::SELECT,
				'default'				=> 'excerpt',
				'label_block'			=> true,
				'options'				=> [
					'excerpt'		=> __( 'Excerpt', 'powerpack' ),
					'full_content'	=> __( 'Full Content', 'powerpack' ),
				],
                'condition'             => [
                    'source'	=> 'posts'
                ]
			]
		);
        
        $this->add_control(
            'excerpt_length',
            [
                'label'             => __( 'Excerpt Length', 'powerpack' ),
                'type'              => Controls_Manager::NUMBER,
                'default'           => 50,
                'min'               => 0,
                'max'               => 58,
                'step'              => 1,
                'condition'         => [
                    'source'				=> 'posts',
                    'posts_content_type'	=> 'excerpt'
                ]
            ]
        );

		$this->add_control(
			'coupon_custom_field',
			[
				'label'                 => __( 'Coupon Custom Field', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'label_block'			=> true,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => '',
				'condition'             => [
					'source'	=> 'posts'
				],
			]
		);

		$this->add_control(
			'discount_custom_field',
			[
				'label'                 => __( 'Discount Custom Field', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'label_block'			=> true,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => '',
				'condition'             => [
					'source'	=> 'posts'
				],
			]
		);
        
        $this->end_controls_section();
	}
     
	protected function register_content_post_query_controls() {
        /**
         * Content Tab: Query
         */
        $this->start_controls_section(
            'section_post_query',
            [
                'label'                 => __( 'Query', 'powerpack' ),
                'condition'             => [
                    'source'    => 'posts'
                ]
            ]
        );

		$this->add_control(
            'post_type',
            [
                'label'					=> __( 'Post Type', 'powerpack' ),
                'type'					=> Controls_Manager::SELECT,
                'options'				=> PP_Posts_Helper::get_post_types(),
                'default'				=> 'post',

            ]
        );
		
		$post_types = PP_Posts_Helper::get_post_types();
		
		foreach ( $post_types as $post_type_slug => $post_type_label ) {

			$taxonomy = PP_Posts_Helper::get_post_taxonomies( $post_type_slug );
			
			if ( ! empty( $taxonomy ) ) {

				foreach ( $taxonomy as $index => $tax ) {
					
					$terms = PP_Posts_Helper::get_tax_terms( $index );

					$tax_terms = array();

					if ( ! empty( $terms ) ) {

						foreach ( $terms as $term_index => $term_obj ) {

							$tax_terms[ $term_obj->term_id ] = $term_obj->name;
						}
						
						if ( $post_type_slug == 'post' ) {
							if ( $index == 'post_tag' ) {
								$tax_control_key = 'tags';
							} else if ( $index == 'category' ) {
								$tax_control_key = 'categories';
							} else {
								$tax_control_key = $index . '_' . $post_type_slug;
							}
						} else {
							$tax_control_key = $index . '_' . $post_type_slug;
						}
						
						// Taxonomy filter type
						$this->add_control(
							$index . '_' . $post_type_slug . '_filter_type',
							[
								/* translators: %s Label */
								'label'       => sprintf( __( '%s Filter Type', 'powerpack' ), $tax->label ),
								'type'        => Controls_Manager::SELECT,
								'default'     => 'IN',
								'label_block' => true,
								'options'     => [
									/* translators: %s label */
									'IN'     => sprintf( __( 'Include %s', 'powerpack' ), $tax->label ),
									/* translators: %s label */
									'NOT IN' => sprintf( __( 'Exclude %s', 'powerpack' ), $tax->label ),
								],
                				'separator'         => 'before',
								'condition'   => [
									'post_type' => $post_type_slug,
								],
							]
						);

						// Add control for all taxonomies.
						// $this->add_control(
						// 	$tax_control_key,
						// 	[
						// 		'label'       => $tax->label,
						// 		'type'        => Controls_Manager::SELECT2,
						// 		'multiple'    => true,
						// 		'default'     => '',
						// 		'label_block' => true,
						// 		'options'     => $tax_terms,
						// 		'condition'   => [
						// 			'post_type' => $post_type_slug,
						// 		],
						// 	]
						// );

						$this->add_control(
							$tax_control_key,
							[
								'label'			=> $tax->label,
								'type'			=> 'pp-query',
								'post_type' 	=> $post_type_slug,
								'options' 		=> [],
								'label_block' 	=> true,
								'multiple' 		=> true,
								'query_type' 	=> 'terms',
								'object_type' 	=> $index,
								'include_type' 	=> true,
								'condition'   => [
									'post_type' => $post_type_slug,
								],
							]
						);
						
					}
				}
			}
		}
		
		$this->add_control(
			'author_filter_type',
			[
				'label'       => __( 'Authors Filter Type', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'author__in',
				'label_block' => true,
                'separator'         => 'before',
				'options'     => [
					'author__in'     => __( 'Include Authors', 'powerpack' ),
					'author__not_in' => __( 'Exclude Authors', 'powerpack' ),
				],
			]
		);

        // $this->add_control(
        //     'authors',
        //     [
        //         'label'					=> __( 'Authors', 'powerpack' ),
        //         'type'					=> Controls_Manager::SELECT2,
		// 		'label_block'			=> true,
		// 		'multiple'				=> true,
		// 		'options'				=> PP_Posts_Helper::get_users(),
        //     ]
		// );
		
		$this->add_control(
            'authors',
            [
                'label'					=> __( 'Authors', 'powerpack' ),
                'type'					=> 'pp-query',
				'label_block'			=> true,
				'multiple'				=> true,
				'query_type'			=> 'authors',
				'condition'			=> [
					'post_type!' => 'related',
				],
            ]
        );
		
		foreach ( $post_types as $post_type_slug => $post_type_label ) {
		
			//$posts_all = PP_Posts_Helper::get_all_posts_by_type( $post_type_slug );
						
			if ( $post_type_slug == 'post' ) {
				$posts_control_key = 'exclude_posts';
			} else {
				$posts_control_key = $post_type_slug . '_filter';
			}
		
			$this->add_control(
				$post_type_slug . '_filter_type',
				[
					'label'				=> sprintf( __( '%s Filter Type', 'powerpack' ), $post_type_label ),
					'type'				=> Controls_Manager::SELECT,
					'default'			=> 'post__not_in',
					'label_block'		=> true,
					'separator'         => 'before',
					'options'			=> [
						'post__in'     => sprintf( __( 'Include %s', 'powerpack' ), $post_type_label ),
						'post__not_in' => sprintf( __( 'Exclude %s', 'powerpack' ), $post_type_label ),
					],
					'condition'   => [
						'post_type' => $post_type_slug,
					],
				]
			);
			
			// $this->add_control(
			// 	$posts_control_key,
			// 	[
			// 		/* translators: %s Label */
			// 		'label'       => $post_type_label,
			// 		'type'        => Controls_Manager::SELECT2,
			// 		'default'     => '',
			// 		'multiple'     => true,
			// 		'label_block' => true,
			// 		'options'     => $posts_all,
			// 		'condition'   => [
			// 			'post_type' => $post_type_slug,
			// 		],
			// 	]
			// );

			$this->add_control(
				$posts_control_key,
				[
					/* translators: %s Label */
					'label'				=> $post_type_label,
					'type'				=> 'pp-query',
					'default'			=> '',
					'multiple'			=> true,
					'label_block'		=> true,
					'query_type'		=> 'posts',
					'object_type'		=> $post_type_slug,
					'condition'			=> [
						'post_type' => $post_type_slug,
					],
				]
			);
		}

		$this->add_control(
            'select_date',
            [
				'label'				=> __( 'Date', 'powerpack' ),
				'type'				=> Controls_Manager::SELECT,
				'options'			=> [
					'anytime'	=> __( 'All', 'powerpack' ),
					'today'		=> __( 'Past Day', 'powerpack' ),
					'week'		=> __( 'Past Week', 'powerpack' ),
					'month'		=> __( 'Past Month', 'powerpack' ),
					'quarter'	=> __( 'Past Quarter', 'powerpack' ),
					'year'		=> __( 'Past Year', 'powerpack' ),
					'exact'		=> __( 'Custom', 'powerpack' ),
				],
				'default'			=> 'anytime',
				'label_block'		=> false,
				'multiple'			=> false,
				'separator'			=> 'before',
			]
        );

		$this->add_control(
            'date_before',
            [
				'label'				=> __( 'Before', 'powerpack' ),
				'description'		=> __( 'Setting a ‘Before’ date will show all the posts published until the chosen date (inclusive).', 'powerpack' ),
				'type'				=> Controls_Manager::DATE_TIME,
				'label_block'		=> false,
				'multiple'			=> false,
				'placeholder'		=> __( 'Choose', 'powerpack' ),
				'condition'			=> [
					'select_date' => 'exact',
				],
			]
        );


		$this->add_control(
            'date_after',
            [
				'label'				=> __( 'After', 'powerpack' ),
				'description'		=> __( 'Setting an ‘After’ date will show all the posts published since the chosen date (inclusive).', 'powerpack' ),
				'type'				=> Controls_Manager::DATE_TIME,
				'label_block'		=> false,
				'multiple'			=> false,
				'placeholder'		=> __( 'Choose', 'powerpack' ),
				'condition'			=> [
					'select_date' => 'exact',
				],
			]
        );

        $this->add_control(
            'order',
            [
                'label'             => __( 'Order', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'options'           => [
                   'DESC'           => __( 'Descending', 'powerpack' ),
                   'ASC'       => __( 'Ascending', 'powerpack' ),
                ],
                'default'           => 'DESC',
                'separator'         => 'before',
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label'             => __( 'Order By', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'options'           => [
                   'date'           => __( 'Date', 'powerpack' ),
                   'modified'       => __( 'Last Modified Date', 'powerpack' ),
                   'rand'           => __( 'Random', 'powerpack' ),
                   'comment_count'  => __( 'Comment Count', 'powerpack' ),
                   'title'          => __( 'Title', 'powerpack' ),
                   'ID'             => __( 'Post ID', 'powerpack' ),
                   'author'         => __( 'Post Author', 'powerpack' ),
                ],
                'default'           => 'date',
            ]
        );
        
        $this->add_control(
            'sticky_posts',
            [
                'label'             => __( 'Sticky Posts', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => '',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
                'separator'         => 'before',
            ]
        );
        
        $this->add_control(
            'all_sticky_posts',
            [
                'label'             => __( 'Show Only Sticky Posts', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => '',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
				'condition'			=> [
					'sticky_posts' => 'yes',
				],
            ]
        );

        $this->add_control(
            'offset',
            [
                'label'             => __( 'Offset', 'powerpack' ),
                'description'		=> __( 'Use this setting to skip this number of initial posts', 'powerpack' ),
                'type'              => Controls_Manager::NUMBER,
                'default'           => '',
                'separator'         => 'before',
            ]
        );

        $this->end_controls_section();
	}
     
	protected function register_content_link_controls() {
		/**
         * Content Tab: Links
         * -------------------------------------------------
         */
		$this->start_controls_section(
			'section_link',
			[
				'label'					=> __( 'Link', 'powerpack' ),
			]
		);
        
        $this->add_control(
            'link_type',
            [
                'label'                 => __( 'Link Type', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'button',
                'options'               => [
                    'none'      => __( 'None', 'powerpack' ),
                    'box'       => __( 'Box', 'powerpack' ),
                    'title'     => __( 'Title', 'powerpack' ),
                    'button'    => __( 'Button', 'powerpack' ),
                ],
            ]
        );

        $this->add_control(
            'button_text',
            [
                'label'                 => __( 'Button Text', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'dynamic'               => [
                    'active'   => true,
                ],
                'default'               => __( 'View This Deal', 'powerpack' ),
                'condition'             => [
                    'link_type'   => 'button',
                ],
            ]
        );

		$this->add_control(
			'button_icon',
			[
				'label'					=> __( 'Button Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'label_block'			=> true,
				'default'				=> [
					'value'		=> 'fas fa-long-arrow-alt-right',
					'library'	=> 'fa-solid',
				],
				'condition'             => [
					'link_type'   => 'button',
				],
			]
		);
        
        $this->add_control(
            'button_icon_position',
            [
                'label'                 => __( 'Icon Position', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'after',
                'options'               => [
                    'before'    => __( 'Before', 'powerpack' ),
                    'after'     => __( 'After', 'powerpack' ),
                ],
                'prefix_class'		    => 'pp-coupon-button-icon-',
				'render_type'           => 'template',
                'condition'             => [
                    'link_type'     => 'button',
                ],
            ]
        );
        
        $this->add_control(
            'button_separator',
            [
                'label'                 => __( 'Separator', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'				=> __( 'Yes', 'powerpack' ),
                'label_off'				=> __( 'No', 'powerpack' ),
                'return_value'			=> 'yes',
                'condition'             => [
                    'link_type'     => 'button',
                ],
            ]
        );

        $this->end_controls_section();
	}
     
	protected function register_content_carousel_settings_controls() {
        /**
         * Content Tab: Carousel Settings
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_carousel_settings',
            [
                'label'                 => __( 'Carousel Settings', 'powerpack' ),
                'condition'             => [
                    'layout'	=> 'carousel',
                ],
            ]
        );
        
        $this->add_control(
            'carousel_effect',
            [
                'label'                 => __( 'Effect', 'powerpack' ),
                'description'           => '',
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'slide',
                'options'               => [
                    'slide'     => __( 'Slide', 'powerpack' ),
                    'cube'      => __( 'Cube', 'powerpack' ),
                    'coverflow' => __( 'Coverflow', 'powerpack' ),
                    'flip'      => __( 'Flip', 'powerpack' ),
                ],
                'condition'             => [
                    'layout'	=> 'carousel',
                ],
            ]
        );
        
        $this->add_control(
            'slider_speed',
            [
                'label'                 => __( 'Slider Speed', 'powerpack' ),
                'description'           => __( 'Duration of transition between slides (in ms)', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [ 'size' => 600 ],
                'range'                 => [
                    'px' => [
                        'min'   => 100,
                        'max'   => 3000,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => '',
                'separator'             => 'before',
                'condition'             => [
                    'layout'	=> 'carousel',
                ],
            ]
        );
        
        $this->add_control(
            'autoplay',
            [
                'label'                 => __( 'Autoplay', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
                'separator'             => 'before',
                'condition'             => [
                    'layout'	=> 'carousel',
                ],
            ]
        );
        
        $this->add_control(
            'autoplay_speed',
            [
                'label'                 => __( 'Autoplay Speed', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [ 'size' => 2400 ],
                'range'                 => [
                    'px' => [
                        'min'   => 500,
                        'max'   => 5000,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => '',
                'condition'             => [
                    'layout'	=> 'carousel',
                    'autoplay'	=> 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'infinite_loop',
            [
                'label'                 => __( 'Infinite Loop', 'powerpack' ),
                'description'           => '',
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
                'condition'             => [
                    'layout'	=> 'carousel',
                ],
            ]
        );
        
        $this->add_control(
            'pause_on_hover',
            [
                'label'                 => __( 'Pause on Hover', 'powerpack' ),
                'description'           => '',
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'          	=> __( 'Yes', 'powerpack' ),
                'label_off'         	=> __( 'No', 'powerpack' ),
                'return_value'      	=> 'yes',
                'frontend_available'	=> true,
                'condition'             => [
                    'layout'	=> 'carousel',
                    'autoplay'	=> 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'grab_cursor',
            [
                'label'                 => __( 'Grab Cursor', 'powerpack' ),
                'description'           => __( 'Shows grab cursor when you hover over the slider', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Show', 'powerpack' ),
                'label_off'             => __( 'Hide', 'powerpack' ),
                'return_value'          => 'yes',
                'separator'             => 'before',
                'condition'             => [
                    'layout'	=> 'carousel',
                ],
            ]
        );
        
        $this->add_control(
            'navigation_heading',
            [
                'label'                 => __( 'Navigation', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'layout'	=> 'carousel',
                ],
            ]
        );
        
        $this->add_control(
            'arrows',
            [
                'label'                 => __( 'Arrows', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
                'condition'             => [
                    'layout'	=> 'carousel',
                ],
            ]
        );
        
        $this->add_control(
            'dots',
            [
                'label'                 => __( 'Pagination', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
                'condition'             => [
                    'layout'	=> 'carousel',
                ],
            ]
        );
        
        $this->add_control(
            'pagination_type',
            [
                'label'                 => __( 'Pagination Type', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'bullets',
                'options'               => [
                    'bullets'       => __( 'Dots', 'powerpack' ),
                    'fraction'      => __( 'Fraction', 'powerpack' ),
                ],
                'condition'             => [
                    'layout'	=> 'carousel',
                    'dots'		=> 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'direction',
            [
                'label'                 => __( 'Direction', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'left',
                'options'               => [
                    'auto'       => __( 'Auto', 'powerpack' ),
                    'left'       => __( 'Left', 'powerpack' ),
                    'right'      => __( 'Right', 'powerpack' ),
                ],
				'separator'             => 'before',
                'condition'             => [
                    'layout'	=> 'carousel',
                ],
            ]
        );

        $this->end_controls_section();
	}
     
	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links('Coupons');

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

	protected function register_style_coupon_box_controls() {
        /**
         * Style Tab: Coupon Boxes
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_coupon_box_style',
            [
                'label'                 => __( 'Coupon Boxes', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_responsive_control(
			'align',
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
				'default'               => 'left',
				'selectors'             => [
					'{{WRAPPER}} .pp-coupons .pp-coupon'   => 'text-align: {{VALUE}};',
				],
			]
		);
        
        $this->add_responsive_control(
            'column_spacing',
            [
                'label'                 => __( 'Column Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' 	=> [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'               => [
                    'size' 	=> 25,
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-grid-item-wrap' => 'padding-left: calc( {{SIZE}}{{UNIT}}/2 ); padding-right: calc( {{SIZE}}{{UNIT}}/2 );',
                    '{{WRAPPER}} .pp-coupons-grid'  => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
                ],
				'render_type'           => 'template',
                'separator'             => 'before',
            ]
        );

        $this->add_responsive_control(
            'row_spacing',
            [
                'label'                 => __( 'Row Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' 	=> [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'               => [
                    'size' 	=> 25,
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-elementor-grid .pp-grid-item-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition'             => [
					'layout'	=> 'grid',
				],
            ]
        );
        
        $this->add_control(
            'coupon_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupons .pp-coupon' => 'background-color: {{VALUE}}',
                ],
                'separator'             => 'before',
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'coupon_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-coupons .pp-coupon',
			]
		);

		$this->add_control(
			'coupon_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-coupons .pp-coupon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'coupon_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-coupons .pp-coupon',
                'condition'             => [
                    'layout'	=> 'grid',
                ],
			]
		);

		$this->add_responsive_control(
			'coupon_padding',
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
					'{{WRAPPER}} .pp-coupons .pp-coupon' => 'padding-top: {{TOP}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();
	}

	protected function register_style_discount_controls() {
        /**
         * Style Tab: Discount
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_discount_style',
            [
                'label'                 => __( 'Discount', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'show_discount'	=> 'yes',
				],
            ]
        );
        
        $this->add_control(
			'discount_position',
			[
				'label'                 => __( 'Position', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'         => [
						'title'    => __( 'Left', 'powerpack' ),
						'icon'     => 'eicon-h-align-left',
					],
					'right'        => [
						'title'    => __( 'Right', 'powerpack' ),
						'icon'     => 'eicon-h-align-right',
					],
				],
				'default'               => 'left',
                'prefix_class'		    => 'pp-coupon-discount-',
				'condition'             => [
					'show_discount'	=> 'yes',
				],
			]
		);
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'discount_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-coupon-discount',
            ]
        );

        $this->start_controls_tabs( 'tabs_discount_style' );

        $this->start_controls_tab(
            'tab_discount_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
				'condition'             => [
					'show_discount'	=> 'yes',
				],
            ]
        );

        $this->add_control(
            'discount_color_normal',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-discount' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'show_discount'	=> 'yes',
				],
            ]
        );
        
        $this->add_control(
            'discount_bg_color_normal',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-discount' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
					'show_discount'	=> 'yes',
				],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'discount_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-coupon-discount',
				'condition'             => [
					'show_discount'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'discount_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-coupon-discount' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'show_discount'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'discount_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-coupon-discount',
				'condition'             => [
					'show_discount'	=> 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'discount_padding',
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
					'{{WRAPPER}} .pp-coupon-discount' => 'padding-top: {{TOP}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
				],
				'condition'             => [
					'show_discount'	=> 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'discount_margin',
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
					'{{WRAPPER}} .pp-coupon-discount' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
				'condition'             => [
					'show_discount'	=> 'yes',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_discount_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
				'condition'             => [
					'show_discount'	=> 'yes',
				],
            ]
        );

        $this->add_control(
            'discount_color_hover',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-discount:hover' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'show_discount'	=> 'yes',
				],
            ]
        );
        
        $this->add_control(
            'discount_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-discount:hover' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
					'show_discount'	=> 'yes',
				],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
        
        $this->end_controls_section();
	}

	protected function register_style_coupon_code_controls() {
        /**
         * Style Tab: Coupon Code
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_coupon_code_style',
            [
                'label'                 => __( 'Coupon Code', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
			'coupon_code_position',
			[
				'label'                 => __( 'Position', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'         => [
						'title'    => __( 'Left', 'powerpack' ),
						'icon'     => 'eicon-h-align-left',
					],
					'right'        => [
						'title'    => __( 'Right', 'powerpack' ),
						'icon'     => 'eicon-h-align-right',
					],
				],
				'default'               => 'left',
                'prefix_class'		    => 'pp-coupon-code-'
			]
		);
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'coupon_code_typography',
                'label'                 => __( 'Coupon Code Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-coupon-code',
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'coupon_code_copy_text_typography',
                'label'                 => __( 'Copy Text Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-coupon-code .pp-coupon-copy-text',
            ]
        );

        $this->start_controls_tabs( 'tabs_coupon_style' );

        $this->start_controls_tab(
            'tab_coupon_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
            ]
        );

        $this->add_control(
            'coupon_code_color_normal',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-code-text' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'coupon_code_bg_color_normal',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-code' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'coupon_code_reveal_text_color_normal',
            [
                'label'                 => __( 'Reveal Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-reveal-wrap' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'coupon_style'	=> 'reveal',
				],
            ]
        );
        
        $this->add_control(
            'coupon_code_reveal_text_bg_color',
            [
                'label'                 => __( 'Reveal Text Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '#ff0000',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-reveal-wrap' => 'background-color: {{VALUE}}; box-shadow: 0px 0px 0px 20px {{VALUE}};',
                ],
				'condition'             => [
					'coupon_style'	=> 'reveal',
				],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'coupon_code_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-coupon-code',
			]
		);

		$this->add_control(
			'coupon_code_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-coupon-code' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'coupon_code_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-coupon-code',
			]
		);

		$this->add_responsive_control(
			'coupon_code_padding',
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
					'{{WRAPPER}} .pp-coupon-code' => 'padding-top: {{TOP}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_code_margin',
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
					'{{WRAPPER}} .pp-coupon-code' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_coupon_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
            ]
        );

        $this->add_control(
            'coupon_code_color_hover',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-code:hover' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'coupon_code_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-code:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'coupon_code_reveal_text_color_hover',
            [
                'label'                 => __( 'Reveal Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-code:hover .pp-coupon-reveal-wrap' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'coupon_style'	=> 'reveal',
				],
            ]
        );
        
        $this->add_control(
            'coupon_code_reveal_text_bg_color_hover',
            [
                'label'                 => __( 'Reveal Text Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '#ff0000',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-code:hover .pp-coupon-reveal-wrap' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .pp-coupon-code.pp-coupon-style-reveal:hover .pp-coupon-reveal-wrap' => 'box-shadow: 0px 0px 0px 3px {{VALUE}};',
                ],
				'condition'             => [
					'coupon_style'	=> 'reveal',
				],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
        
        $this->add_control(
            'coupon_icon_heading',
            [
                'label'                 => __( 'Coupon Icon', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
            ]
        );

        $this->add_control(
            'coupon_icon_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon .pp-coupon-code-icon' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'coupon_icon_size',
            [
                'label'                 => __( 'Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 60,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-coupon .pp-coupon-code-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
            ]
        );
        
        $this->add_responsive_control(
            'coupon_icon_spacing',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 60,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-coupon .pp-coupon-code-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
            ]
        );
        
        $this->end_controls_section();
	}

	protected function register_style_content_controls() {
        /**
         * Style Tab: Content
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_content_style',
            [
                'label'                 => __( 'Content', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
			'conent_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-coupon-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->add_control(
            'content_title_heading',
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
                    '{{WRAPPER}} .pp-coupon-title' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'title_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-coupon-title',
            ]
        );
        
        $this->add_responsive_control(
            'title_margin',
            [
                'label'                 => __( 'Margin Bottom', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'  => 10,
                ],
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
                    '{{WRAPPER}} .pp-coupon-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        
        $this->add_control(
            'content_description_heading',
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
                    '{{WRAPPER}} .pp-coupon-description' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'description_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-coupon-description',
            ]
        );
        
        $this->add_responsive_control(
            'description_margin',
            [
                'label'                 => __( 'Margin Bottom', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'  => 20,
                ],
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
                    '{{WRAPPER}} .pp-coupon-description' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
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
            'section_coupons_button_style',
            [
                'label'                 => __( 'Button', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'link_type'  => 'button',
                ],
            ]
        );

		$this->add_control(
			'button_size',
			[
				'label'                 => __( 'Size', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'md',
				'options'               => [
					'xs' => __( 'Extra Small', 'powerpack' ),
					'sm' => __( 'Small', 'powerpack' ),
					'md' => __( 'Medium', 'powerpack' ),
					'lg' => __( 'Large', 'powerpack' ),
					'xl' => __( 'Extra Large', 'powerpack' ),
				],
                'condition'             => [
                    'link_type'  => 'button',
                ],
			]
		);
        
        $this->add_responsive_control(
            'button_spacing',
            [
                'label'                 => __( 'Button Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'  => 20,
                ],
                'range'                 => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 30,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-button-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
                'condition'             => [
                    'link_type'  => 'button',
                ],
            ]
        );
        
        $this->add_responsive_control(
			'button_align',
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
					'{{WRAPPER}} .pp-coupons .pp-coupon-button-wrap'   => 'text-align: {{VALUE}};',
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
            'button_text_color_normal',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-button' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-coupon-button .pp-icon' => 'fill: {{VALUE}}',
                ],
                'condition'             => [
                    'link_type'  => 'button',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color_normal',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-button' => 'background-color: {{VALUE}}',
                ],
                'condition'             => [
                    'link_type'  => 'button',
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
				'selector'              => '{{WRAPPER}} .pp-coupon-button',
                'condition'             => [
                    'link_type'  => 'button',
                ],
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-coupon-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'link_type'  => 'button',
                ],
			]
		);
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'button_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-coupon-button',
                'condition'             => [
                    'link_type'  => 'button',
                ],
            ]
        );

		$this->add_responsive_control(
			'button_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-coupon-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'link_type'  => 'button',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-coupon-button',
                'condition'             => [
                    'link_type'  => 'button',
                ],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'link_type'  => 'button',
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
                    '{{WRAPPER}} .pp-coupon-button:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-coupon-button:hover .pp-icon' => 'fill: {{VALUE}}',
                ],
                'condition'             => [
                    'link_type'  => 'button',
                ],
            ]
        );

        $this->add_control(
            'button_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-button:hover' => 'background-color: {{VALUE}}',
                ],
                'condition'             => [
                    'link_type'  => 'button',
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
                    '{{WRAPPER}} .pp-coupon-button:hover' => 'border-color: {{VALUE}}',
                ],
                'condition'             => [
                    'link_type'  => 'button',
                ],
            ]
        );

		$this->add_control(
			'button_hover_animation',
			[
				'label'                 => __( 'Animation', 'powerpack' ),
				'type'                  => Controls_Manager::HOVER_ANIMATION,
                'condition'             => [
                    'link_type'  => 'button',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-coupon-button:hover',
                'condition'             => [
                    'link_type'  => 'button',
                ],
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->add_control(
            'button_icon_heading',
            [
                'label'                 => __( 'Button Icon', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
            ]
        );

        $this->add_control(
            'button_icon_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupons .pp-button-icon' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'button_icon_size',
            [
                'label'                 => __( 'Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 60,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-coupons .pp-button-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
            ]
        );
        
        $this->add_responsive_control(
            'button_icon_spacing',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 60,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
				'selectors'             => [
					'{{WRAPPER}}.pp-coupon-button-icon-before .pp-coupons .pp-button-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pp-coupon-button-icon-after .pp-coupons .pp-button-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
            ]
        );
        
        $this->add_control(
            'button_separator_heading',
            [
                'label'                 => __( 'Separator', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'button_separator'	=> 'yes',
                ],
            ]
        );

        $this->add_control(
            'button_separator_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-separator' => 'border-top-color: {{VALUE}}',
                ],
                'condition'             => [
                    'button_separator'	=> 'yes',
                ],
            ]
        );

        $this->add_control(
            'button_separator_style',
            [
                'label'                 => __( 'Style', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => [
                   'solid'     => __( 'Solid', 'powerpack' ),
                   'dotted'    => __( 'Dotted', 'powerpack' ),
                   'dashed'    => __( 'Dashed', 'powerpack' ),
                ],
                'default'               => 'solid',
                'selectors'             => [
                    '{{WRAPPER}} .pp-coupon-separator' => 'border-top-style: {{VALUE}}',
                ],
                'condition'             => [
                    'button_separator'	=> 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'button_separator_size',
            [
                'label'                 => __( 'Width', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 60,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-coupon-separator' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'button_separator'	=> 'yes',
                ],
            ]
        );
        
        $this->end_controls_section();
	}

	protected function register_style_arrows_controls() {
        /**
         * Style Tab: Arrows
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_arrows_style',
            [
                'label'                 => __( 'Arrows', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'layout'	=> 'carousel',
                    'arrows'	=> 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'arrow',
            [
                'label'                 => __( 'Choose Arrow', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'label_block'           => true,
                'default'               => 'fa fa-angle-right',
                'options'               => [
                    'fa fa-angle-right'             => __( 'Angle', 'powerpack' ),
                    'fa fa-angle-double-right'      => __( 'Double Angle', 'powerpack' ),
                    'fa fa-chevron-right'           => __( 'Chevron', 'powerpack' ),
                    'fa fa-chevron-circle-right'    => __( 'Chevron Circle', 'powerpack' ),
                    'fa fa-arrow-right'             => __( 'Arrow', 'powerpack' ),
                    'fa fa-long-arrow-right'        => __( 'Long Arrow', 'powerpack' ),
                    'fa fa-caret-right'             => __( 'Caret', 'powerpack' ),
                    'fa fa-caret-square-o-right'    => __( 'Caret Square', 'powerpack' ),
                    'fa fa-arrow-circle-right'      => __( 'Arrow Circle', 'powerpack' ),
                    'fa fa-arrow-circle-o-right'    => __( 'Arrow Circle O', 'powerpack' ),
                    'fa fa-toggle-right'            => __( 'Toggle', 'powerpack' ),
                    'fa fa-hand-o-right'            => __( 'Hand', 'powerpack' ),
                ],
                'condition'             => [
                    'layout'	=> 'carousel',
                    'arrows'	=> 'yes',
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'font-size: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'	=> 'carousel',
                    'arrows'	=> 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'align_arrows',
            [
                'label'                 => __( 'Align Arrows', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => -100,
                        'max'   => 40,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
				'selectors'             => [
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'	=> 'carousel',
                    'arrows'	=> 'yes',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_arrows_style' );

        $this->start_controls_tab(
            'tab_arrows_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
                    'layout'	=> 'carousel',
                    'arrows'	=> 'yes',
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
                    '{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'background-color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'	=> 'carousel',
                    'arrows'	=> 'yes',
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
                    '{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'	=> 'carousel',
                    'arrows'	=> 'yes',
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
				'selector'              => '{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev',
                'condition'             => [
                    'layout'	=> 'carousel',
                    'arrows'	=> 'yes',
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'	=> 'carousel',
                    'arrows'	=> 'yes',
                ],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_arrows_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'layout'	=> 'carousel',
                    'arrows'	=> 'yes',
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
                    '{{WRAPPER}} .swiper-container-wrap .swiper-button-next:hover, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev:hover' => 'background-color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'	=> 'carousel',
                    'arrows'	=> 'yes',
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
                    '{{WRAPPER}} .swiper-container-wrap .swiper-button-next:hover, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev:hover' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'	=> 'carousel',
                    'arrows'	=> 'yes',
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
                    '{{WRAPPER}} .swiper-container-wrap .swiper-button-next:hover, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev:hover' => 'border-color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'	=> 'carousel',
                    'arrows'	=> 'yes',
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-button-next, {{WRAPPER}} .swiper-container-wrap .swiper-button-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'separator'             => 'before',
                'condition'             => [
                    'layout'	=> 'carousel',
                    'arrows'	=> 'yes',
                ],
			]
		);
        
        $this->end_controls_section();
	}

	protected function register_style_dots_controls() {
        /**
         * Style Tab: Pagination: Dots
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_dots_style',
            [
                'label'                 => __( 'Pagination: Dots', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'layout'			=> 'carousel',
                    'dots'              => 'yes',
                    'pagination_type'   => 'bullets',
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
                'condition'             => [
                    'layout'			=> 'carousel',
                    'dots'              => 'yes',
                    'pagination_type'   => 'bullets',
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
                    '{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'layout'			=> 'carousel',
                    'dots'              => 'yes',
                    'pagination_type'   => 'bullets',
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
                    '{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'layout'			=> 'carousel',
                    'dots'              => 'yes',
                    'pagination_type'   => 'bullets',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_dots_style' );

        $this->start_controls_tab(
            'tab_dots_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
                    'layout'			=> 'carousel',
                    'dots'              => 'yes',
                    'pagination_type'   => 'bullets',
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
                    '{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'background: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'			=> 'carousel',
                    'dots'              => 'yes',
                    'pagination_type'   => 'bullets',
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
                    '{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet-active' => 'background: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'			=> 'carousel',
                    'dots'              => 'yes',
                    'pagination_type'   => 'bullets',
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
				'selector'              => '{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet',
                'condition'             => [
                    'layout'			=> 'carousel',
                    'dots'              => 'yes',
                    'pagination_type'   => 'bullets',
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'			=> 'carousel',
                    'dots'              => 'yes',
                    'pagination_type'   => 'bullets',
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
					'{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullets' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'			=> 'carousel',
                    'dots'              => 'yes',
                    'pagination_type'   => 'bullets',
                ],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_dots_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'layout'			=> 'carousel',
                    'dots'              => 'yes',
                    'pagination_type'   => 'bullets',
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
                    '{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet:hover' => 'background: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'			=> 'carousel',
                    'dots'              => 'yes',
                    'pagination_type'   => 'bullets',
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
                    '{{WRAPPER}} .swiper-container-wrap .swiper-pagination-bullet:hover' => 'border-color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'			=> 'carousel',
                    'dots'              => 'yes',
                    'pagination_type'   => 'bullets',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
	}

	protected function register_style_fraction_controls() {
        /**
         * Style Tab: Pagination: Fraction
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_fraction_style',
            [
                'label'                 => __( 'Pagination: Fraction', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'layout'			=> 'carousel',
                    'dots'              => 'yes',
                    'pagination_type'   => 'fraction',
                ],
            ]
        );

        $this->add_control(
            'fraction_text_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .swiper-pagination-fraction' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'			=> 'carousel',
                    'dots'              => 'yes',
                    'pagination_type'   => 'fraction',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'fraction_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .swiper-pagination-fraction',
                'condition'             => [
                    'layout'			=> 'carousel',
                    'dots'              => 'yes',
                    'pagination_type'   => 'fraction',
                ],
            ]
        );
        
        $this->end_controls_section();
    }

	/**
	 * Slider Settings.
	 *
	 * @access public
	 */
	public function slider_settings() {
        $settings = $this->get_settings();
        
        $slider_options = [
			'direction'              => 'horizontal',
			'speed'                  => ( $settings['slider_speed']['size'] !== '' ) ? $settings['slider_speed']['size'] : 400,
			'effect'                 => ( $settings['carousel_effect'] ) ? $settings['carousel_effect'] : 'slide',
			'slidesPerView'          => 1,
			'spaceBetween'           => 10,
			'grabCursor'             => ( $settings['grab_cursor'] === 'yes' ),
			'autoHeight'             => true,
			'loop'                   => ( $settings['infinite_loop'] === 'yes' ),
		];
        
        if ( $settings['autoplay'] == 'yes' && ! empty( $settings['autoplay_speed']['size'] ) ) {
            $autoplay_speed = $settings['autoplay_speed']['size'];
        } else {
            $autoplay_speed = 999999;
        }
        
        $slider_options['autoplay'] = [
            'delay'                  => $autoplay_speed,
        ];
        
        if ( $settings['dots'] == 'yes' ) {
            $slider_options['pagination'] = [
                'el'                 => '.swiper-pagination-'.esc_attr( $this->get_id() ),
                'type'               => $settings['pagination_type'],
                'clickable'          => true,
            ];
        }
        
        if ( $settings['arrows'] == 'yes' ) {
            $slider_options['navigation'] = [
                'nextEl'             => '.swiper-button-next-'.esc_attr( $this->get_id() ),
                'prevEl'             => '.swiper-button-prev-'.esc_attr( $this->get_id() ),
            ];
        }
		
		$elementor_bp_lg		= get_option( 'elementor_viewport_lg' );
		$elementor_bp_md		= get_option( 'elementor_viewport_md' );
		$bp_desktop				= !empty($elementor_bp_lg) ? $elementor_bp_lg : 1025;
		$bp_tablet				= !empty($elementor_bp_md) ? $elementor_bp_md : 768;
		$bp_mobile				= 320;
        
        $slider_options['breakpoints'] = [
            $bp_desktop   => [
                'slidesPerView'      => ( $settings['columns'] !== '' ) ? absint( $settings['columns'] ) : 3,
                'spaceBetween'       => ( $settings['column_spacing']['size'] !== '' ) ? $settings['column_spacing']['size'] : 25,
            ],
            $bp_tablet   => [
                'slidesPerView'      => ( $settings['columns_tablet'] !== '' ) ? absint( $settings['columns_tablet'] ) : 2,
                'spaceBetween'       => ( $settings['column_spacing_tablet']['size'] !== '' ) ? $settings['column_spacing_tablet']['size'] : 10,
            ],
            $bp_mobile   => [
                'slidesPerView'      => ( $settings['columns_mobile'] !== '' ) ? absint( $settings['columns_mobile'] ) : 1,
                'spaceBetween'       => ( $settings['column_spacing_mobile']['size'] !== '' ) ? $settings['column_spacing_mobile']['size'] : 10,
            ],
        ];
        
        $this->add_render_attribute(
			'container-wrap',
			[
				'data-slider-settings' => wp_json_encode( $slider_options ),
			]
		);
    }

    /**
	 * Get post query arguments.
	 *
	 * @access protected
	 */
    protected function get_posts_query_arguments() {
        $settings = $this->get_settings();
        $posts_count = absint( $settings['posts_per_page'] );
        
		// Post Authors
		$pp_tiled_post_author = '';
		$pp_tiled_post_authors = $settings['authors'];
		if ( !empty( $pp_tiled_post_authors) ) {
			$pp_tiled_post_author = implode( ",", $pp_tiled_post_authors );
		}

		// Post Categories
		$pp_tiled_post_cat = '';
		$pp_tiled_post_cats = $settings['categories'];
		if ( !empty( $pp_tiled_post_cats) ) {
			$pp_tiled_post_cat = implode( ",", $pp_tiled_post_cats );
		}

		// Query Arguments
		$args = array(
			'post_status'           => array( 'publish' ),
			'post_type'             => $settings['post_type'],
			'orderby'               => $settings['orderby'],
			'order'                 => $settings['order'],
			'offset'                => $settings['offset'],
			'ignore_sticky_posts'   => ( 'yes' == $settings[ 'sticky_posts' ] ) ? 0 : 1,
			'showposts'             => $posts_count
		);
		
		// Author Filter
		if ( !empty( $settings['authors'] ) ) {
			$args[ $settings['author_filter_type'] ] = $settings['authors'];
		}
		
		// Posts Filter
		$post_type = $settings['post_type'];
						
		if ( $post_type == 'post' ) {
			$posts_control_key = 'exclude_posts';
		} else {
			$posts_control_key = $post_type . '_filter';
		}

		if ( !empty( $settings[$posts_control_key] ) ) {
			$args[ $settings[$post_type . '_filter_type'] ] = $settings[$posts_control_key];
		}
		
		// Taxonomy Filter
		$taxonomy = pp_get_post_taxonomies( $post_type );

		if ( ! empty( $taxonomy ) && ! is_wp_error( $taxonomy ) ) {

			foreach ( $taxonomy as $index => $tax ) {
				
				if ( $post_type == 'post' ) {
					if ( $index == 'post_tag' ) {
						$tax_control_key = 'tags';
					} else if ( $index == 'category' ) {
						$tax_control_key = 'categories';
					} else {
						$tax_control_key = $index . '_' . $post_type;
					}
				} else {
					$tax_control_key = $index . '_' . $post_type;
				}

				if ( ! empty( $settings[ $tax_control_key ] ) ) {

					$operator = $settings[ $index . '_' . $post_type . '_filter_type' ];

					$args['tax_query'][] = [
						'taxonomy' => $index,
						'field'    => 'term_id',
						'terms'    => $settings[ $tax_control_key ],
						'operator' => $operator,
					];
				}
			}
		}
		
		if ( $settings['select_date'] != 'anytime' ) {
			$select_date = $settings['select_date'];
			if ( ! empty( $select_date ) ) {
				$date_query = [];
				if ( $select_date == 'today' ) {
						$date_query['after'] = '-1 day';
				}
				elseif ( $select_date == 'week' ) {
						$date_query['after'] = '-1 week';
				}
				elseif ( $select_date == 'month' ) {
						$date_query['after'] = '-1 month';
				}
				elseif ( $select_date == 'quarter' ) {
						$date_query['after'] = '-3 month';
				}
				elseif ( $select_date == 'year' ) {
						$date_query['after'] = '-1 year';
				}
				elseif ( $select_date == 'exact' ) {
					$after_date = $settings['date_after'];
					if ( ! empty( $after_date ) ) {
						$date_query['after'] = $after_date;
					}
					$before_date = $settings['date_before'];
					if ( ! empty( $before_date ) ) {
						$date_query['before'] = $before_date;
					}
					$date_query['inclusive'] = true;
				}

				$args['date_query'] = $date_query;
			}
		}
		
		// Sticky Posts Filter
		if ( $settings['sticky_posts'] == 'yes' && $settings['all_sticky_posts'] == 'yes' ) {
			$post__in = get_option( 'sticky_posts' );
			
			$args['post__in'] = $post__in;
		}
		
		return $args;
    }

    /**
	 * Get custom post excerpt.
	 *
	 * @access protected
	 */
    protected function get_coupons_post_content( $limit = '' ) {
        $settings = $this->get_settings();
		
		if ( $settings['posts_content_type'] == 'excerpt' ) {
			$content = explode(' ', get_the_excerpt(), $limit);

			if ( count( $content ) >= $limit ) {
				array_pop($content);
				$content = implode(" ",$content).'...';
			} else {
				$content = implode(" ",$content);
			}

			$content = preg_replace('`[[^]]*]`','',$content);
		} else {
			$content = get_the_content();
		}

        return $content;
    }

    /**
	 * Render posts output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function get_coupons_posts() {
        $settings = $this->get_settings();

        $i = 0;
		$coupons = array();

        // Query Arguments
        $args = $this->get_posts_query_arguments();
        $posts_query = new \WP_Query( $args );

        if ( $posts_query->have_posts() ) : while ($posts_query->have_posts()) : $posts_query->the_post();

			$limit = $settings['excerpt_length'];
			$coupon = ( $settings['coupon_custom_field'] ) ? get_post_meta( get_the_ID(), $settings['coupon_custom_field'], true) : '';
			$discount_code = ( $settings['discount_custom_field'] ) ? get_post_meta( get_the_ID(), $settings['discount_custom_field'], true) : '';
		
			$coupons[$i]['coupon_code'] = ( $coupon ) ? $coupon : '';
			$coupons[$i]['discount'] = ( $discount_code ) ? $discount_code : '';
			$coupons[$i]['title'] = get_the_title();
			$coupons[$i]['description'] = $this->get_coupons_post_content( $limit );
			$coupons[$i]['image']['id'] = get_post_thumbnail_id();
			$coupons[$i]['image']['url'] = get_the_post_thumbnail_url();
			$coupons[$i]['icon_type'] = $settings['icon_type'];
			$coupons[$i]['link_type'] = $settings['link_type'];
			$coupons[$i]['link']['url'] = get_permalink();
			$coupons[$i]['link']['is_external'] = '';
			$coupons[$i]['link']['nofollow'] = '';
			$coupons[$i]['link']['custom_attributes'] = '';
		
        $i++;
		endwhile;
		endif;
		wp_reset_query();
		
		return $coupons;
    }
    
	 /**
	 * Render custom coupons output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function get_coupons_custom() {
        $settings = $this->get_settings_for_display();
		
		$coupons = array();
		
		foreach( $settings['pp_coupons'] as $index => $item ) {
			$coupons[$index]['coupon_code'] = $item['coupon_code'];
			$coupons[$index]['discount'] = $item['discount'];
			$coupons[$index]['title'] = $item['title'];
			$coupons[$index]['description'] = $item['description'];
			$coupons[$index]['image']['id'] = $item['image']['id'];
			$coupons[$index]['image']['url'] = $item['image']['url'];
			$coupons[$index]['icon_type'] = $settings['icon_type'];
			$coupons[$index]['link_type'] = $settings['link_type'];
			$coupons[$index]['link']['url'] = $item['link']['url'];
			$coupons[$index]['link']['is_external'] = $item['link']['is_external'];
			$coupons[$index]['link']['nofollow'] = $item['link']['nofollow'];
			$coupons[$index]['link']['custom_attributes'] = $item['link']['custom_attributes'];
		}
		
		return $coupons;
    }
    
    protected function get_coupons() {
        $settings = $this->get_settings_for_display();
		
		if ( $settings['source'] == 'posts' ) {

			return $this->get_coupons_posts();

		} elseif ( $settings['source'] == 'custom' ) {

			return $this->get_coupons_custom();

		}
	}
    
    protected function render_coupons() {
        $settings = $this->get_settings_for_display();
		
		$coupons = $this->get_coupons();
		
		if ( empty( $coupons ) ) {
			return;
		}

        $title_html_tag = 'div';
        $button_html_tag = 'div';
		
		foreach( $coupons as $index => $item ) :

			if ( $settings['link_type'] != 'none' ) {
				if ( ! empty( $item['link']['url'] ) ) {
					
					$this->add_link_attributes( 'link' . $index, $item['link'] );

					if ( $settings['link_type'] == 'title' ) {
						$title_html_tag = 'a';
					}
					elseif ( $settings['link_type'] == 'button' ) {
						$button_html_tag = 'a';
					}
				}
			}
            
        	$this->add_render_attribute( 'title-container' . $index, 'class', 'pp-coupon-title-container' );
		
			$this->add_render_attribute(
				'coupon-code-' . $index,
				[
					'class' => ['pp-coupon-code', 'pp-coupon-style-' . $settings['coupon_style'] ],
					'data-coupon-code' => $item['coupon_code'],
				]
			);
			?>
			<div <?php echo $this->get_render_attribute_string( 'coupon-wrap' ); ?>>
				<div <?php echo $this->get_render_attribute_string( 'coupon' ); ?>>
					<?php
						//print_r($item);
					?>
					<?php
					if ( $item['image']['url'] ) {
						$image_url = Group_Control_Image_Size::get_attachment_image_src( $item['image']['id'], 'image', $settings );

						if ( ! $image_url ) {
							$image_url = $item['image']['url'];
						}
						?>
						<div class="pp-coupon-image-wrapper">
							<?php if ( $settings['show_discount'] == 'yes' && $item['discount'] ) { ?>
								<div class="pp-coupon-discount">
									<?php echo $item['discount']; ?>
								</div>
							<?php } ?>

							<?php if ( $item['coupon_code'] ) { ?>
								<div <?php echo $this->get_render_attribute_string( 'coupon-code-' . $index ); ?>>
									<?php $this->render_coupon( $item ); ?>
								</div>
							<?php } ?>

							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( Control_Media::get_image_alt( $item['image'] ) ); ?>">

						</div>
						<?php
					}
					?>
					
					<?php if ( $settings['link_type'] == 'box' ) { ?>
						<a <?php echo $this->get_render_attribute_string( 'link' . $index ); ?>>
					<?php } ?>
					<div class="pp-coupon-content">
						<div class="pp-coupon-title-wrap">
							<?php
								if ( ! empty( $item['title'] ) ) {
									printf( '<%1$s %2$s %3$s>', $title_html_tag, $this->get_render_attribute_string( 'title-container' . $index ), $this->get_render_attribute_string( 'link' . $index ) );
									printf( '<%1$s class="pp-coupon-title">', $settings['title_html_tag'] );
									echo $item['title'];
									printf( '</%1$s>', $settings['title_html_tag'] );
									printf( '</%1$s>', $title_html_tag );
								}
							?>
						</div>

						<?php if ( ! empty( $item['description'] ) ) { ?>
							<div class="pp-coupon-description">
								<?php echo $this->parse_text_editor( nl2br( $item['description'] ) ); ?>
							</div>
						<?php } ?>

						<?php if ( $settings['button_separator'] == 'yes' ) { ?>
							<hr class="pp-coupon-separator">
						<?php } ?>

						<?php if ( $settings['link_type'] == 'button' ) { ?>
							<div class="pp-coupon-button-wrap">
								<a <?php echo $this->get_render_attribute_string( 'coupon-button' . $index ) . $this->get_render_attribute_string( 'link' . $index ); ?>>
									<div <?php echo $this->get_render_attribute_string( 'coupon-button' ); ?>>
										<?php
											if ( $settings['button_icon_position'] == 'before' ) { 
												$this->render_coupon_button_icon();
											}
										?>
										<?php if ( ! empty( $settings['button_text'] ) ) { ?>
											<span class="pp-button-text">
												<?php echo esc_attr( $settings['button_text'] ); ?>
											</span>
										<?php } ?>
										<?php
											if ( $settings['button_icon_position'] == 'after' ) { 
												$this->render_coupon_button_icon();
											}
										?>
									</div>
								</a>
							</div>
						<?php } ?>
						<?php if ( $settings['link_type'] == 'box' ) { ?>
							</a>
						<?php } ?>
					</div>
				</div>
			</div>
			<?php
		endforeach;
	}

    /**
	 * Render coupons widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render() {
        $settings = $this->get_settings_for_display();
		
        $this->add_render_attribute( 'container-wrap', 'class', 'pp-coupons-wrap' );

        if ( $settings['dots_position'] ) {
            $this->add_render_attribute( 'container-wrap', 'class', 'swiper-container-wrap-dots-' . $settings['dots_position'] );
        } elseif ( $settings['pagination_type'] == 'fraction' ) {
            $this->add_render_attribute( 'container-wrap', 'class', 'swiper-container-wrap-dots-outside' );
        }
        
		if ( $settings['layout'] == 'carousel' ) {
        	$this->slider_settings();
		}
        
        $this->add_render_attribute( 'container', 'class', 'pp-coupons' );
		
		$this->add_render_attribute( 'coupon', 'class', 'pp-coupon' );
		$this->add_render_attribute( 'coupon-wrap', 'class', 'pp-coupon-wrap' );
        
		if ( $settings['layout'] == 'carousel' ) {
        	$this->add_render_attribute( 'container-wrap', 'class', 'swiper-container-wrap pp-coupons-carousel-wrap' );
			$this->add_render_attribute(
				'container',
				[
					'class'             => [ 'swiper-container', 'pp-coupons-carousel', 'swiper-container-'.esc_attr( $this->get_id() ) ],
					'data-pagination'   => '.swiper-pagination-'.esc_attr( $this->get_id() ),
					'data-arrow-next'   => '.swiper-button-next-'.esc_attr( $this->get_id() ),
					'data-arrow-prev'   => '.swiper-button-prev-'.esc_attr( $this->get_id() ),
				]
			);
        	$this->add_render_attribute( 'wrapper', 'class', 'swiper-wrapper' );
        	$this->add_render_attribute( 'coupon-wrap', 'class', 'swiper-slide' );
		} else {
        	$this->add_render_attribute( 'container', 'class', 'pp-coupons-grid' );
			$this->add_render_attribute( 'wrapper', 'class', 'pp-elementor-grid' );
        	$this->add_render_attribute( 'coupon-wrap', 'class', 'pp-grid-item-wrap' );
        	$this->add_render_attribute( 'coupon', 'class', 'pp-grid-item' );
		}
        
        if ( is_rtl() ) {
            $this->add_render_attribute( 'container', 'dir', 'rtl' );
        }
        
        $pp_if_html_tag = 'div';
        $button_html_tag = 'div';
        
        $this->add_render_attribute( 'coupon-button', 'class', [
				'pp-coupon-button',
				'elementor-button',
				'elementor-size-' . $settings['button_size'],
			]
		);

		if ( $settings['button_hover_animation'] ) {
			$this->add_render_attribute( 'coupon-button', 'class', 'elementor-animation-' . $settings['button_hover_animation'] );
		}
        
        $this->add_render_attribute( 'icon', 'class', ['pp-coupon-code-icon', 'pp-icon'] );

		/*if ( $settings['icon_animation'] ) {
			$this->add_render_attribute( 'icon', 'class', 'elementor-animation-' . $settings['icon_animation'] );
		}*/
		
        ?>
        <div <?php echo $this->get_render_attribute_string( 'container-wrap' ); ?>>
            <div <?php echo $this->get_render_attribute_string( 'container' ); ?>>
                <div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
                <?php
					$coupons = $this->render_coupons();
				?>
                </div>
            </div>
            <?php
				if ( $settings['layout'] == 'carousel' ) {
					$this->render_dots();

					$this->render_arrows();
				}
            ?>
        </div>
        <?php
    }

    /**
	 * Render caoupon icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_coupon( $item ) {
        $settings = $this->get_settings_for_display();
		?>
		<?php if ( $settings['coupon_style'] == 'copy' ) { ?>
			<span class="pp-coupon-code-text">
				<?php
					if ( $settings['icon_type'] != 'none' ) {
					  $this->render_coupon_icon();
					}

					echo $item['coupon_code'];
				?>
			</span>
			<span class="pp-coupon-copy-text">
				<?php echo __('Copy', 'powerpack'); ?>
			</span>
		<?php } elseif ( $settings['coupon_style'] == 'reveal' ) { ?>
			<?php
				// Trim coupon code for Reveal style
				$str 	= $item['coupon_code'];
				$strlth = strlen( $item['coupon_code'] );
				if ( 1 == $strlth ) {
					$str = $item['coupon_code'];
				} elseif ( 3 >= $strlth ) {
					$str = substr( $str, 1 );
				} else {
					$strcut = $strlth - 3;
					$str = substr( $str, $strcut );
				}
			?>
			<div class='pp-coupon-reveal-wrap'>
				<span class='pp-coupon-reveal'>
					<?php
						if ( $settings['icon_type'] != 'none' ) {
						  $this->render_coupon_icon();
						}
						echo $settings['coupon_reveal'];
					?>
				</span>
			</div>
			<div class='pp-coupon-code-text-wrap pp-unreavel'>
				<span class='pp-coupon-code-text' id='pp-coupon-code-<?php echo $this->get_id(); ?>'><?php echo $str; ?></span>
				<span class='pp-coupon-copy-text'style='display: none;'></span>
			</div>
		<?php } else { ?>
			<span class='pp-coupon-code-no-code' id='pp-coupon-code-<?php echo $this->get_id(); ?>'>
				<?php
					if ( $settings['icon_type'] != 'none' ) {
					  $this->render_coupon_icon();
					}

					echo $settings['no_code_need'];
				?>
			</span>
		<?php }
    }

    /**
	 * Render caoupon icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_coupon_icon() {
        $settings = $this->get_settings_for_display();
		?>
		<span <?php echo $this->get_render_attribute_string( 'icon' ); ?>>
			<?php if ( $settings['icon_type'] == 'icon' ) { ?>
				<?php
					if ( ! empty( $settings['icon']['value'] ) ) {
						Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] );
					}
				?>
			<?php } elseif ( $settings['icon_type'] == 'image' ) { ?>
				<?php
					if ( ! empty( $settings['icon_image']['url'] ) ) {
						$image_url = Group_Control_Image_Size::get_attachment_image_src( $settings['icon_image']['id'], 'thumbnail', $settings );

						if ( $image_url ) {
							echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( Control_Media::get_image_alt( $settings['icon_image'] ) ) . '">';
						} else {
							echo '<img src="' . esc_url( $settings['icon_image']['url'] ) . '">';
						}
					}
				?>
			<?php } ?>
		</span>
		<?php
    }

    /**
	 * Render coupon button icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_coupon_button_icon() {
        $settings = $this->get_settings_for_display();
		
		if ( ! empty( $settings['button_icon']['value'] ) ) {
			?>
			<span class="pp-button-icon pp-icon">
				<?php
					Icons_Manager::render_icon( $settings['button_icon'], [ 'aria-hidden' => 'true' ] );
				?>
			</span>
			<?php
		}
    }

    /**
	 * Render coupons carousel dots output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_dots() {
        $settings = $this->get_settings_for_display();

        if ( $settings['dots'] == 'yes' ) { ?>
            <!-- Add Pagination -->
            <div class="swiper-pagination swiper-pagination-<?php echo esc_attr( $this->get_id() ); ?>"></div>
        <?php }
    }

    /**
	 * Render coupons carousel arrows output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_arrows() {
        $settings = $this->get_settings_for_display();

        if ( $settings['arrows'] == 'yes' ) { ?>
            <?php
                if ( $settings['arrow'] ) {
                    $pa_next_arrow = $settings['arrow'];
                    $pa_prev_arrow = str_replace("right","left",$settings['arrow']);
                }
                else {
                    $pa_next_arrow = 'fa fa-angle-right';
                    $pa_prev_arrow = 'fa fa-angle-left';
                }
            ?>
            <!-- Add Arrows -->
            <div class="swiper-button-next swiper-button-next-<?php echo esc_attr( $this->get_id() ); ?>">
                <i class="<?php echo esc_attr( $pa_next_arrow ); ?>"></i>
            </div>
            <div class="swiper-button-prev swiper-button-prev-<?php echo esc_attr( $this->get_id() ); ?>">
                <i class="<?php echo esc_attr( $pa_prev_arrow ); ?>"></i>
            </div>
        <?php }
    }

    protected function _content_templates() {}

}