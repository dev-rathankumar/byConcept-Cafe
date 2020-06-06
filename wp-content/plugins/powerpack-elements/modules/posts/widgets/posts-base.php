<?php
namespace PowerpackElements\Modules\Posts\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Posts_Helper;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Posts Grid Widget
 */
abstract class Posts_Base extends Powerpack_Widget {

	/**
	 * @var \WP_Query
	 */
	protected $query = null;
	protected $query_filters = null;

	protected $_has_template_content = false;

    /**
	 * Retrieve posts grid widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
    public function get_icon() {
        return 'eicon-posts-group power-pack-admin-icon';
    }

	public function get_script_depends() {
		return [
			'isotope',
			'imagesloaded',
			'jquery-slick',
			'powerpack-frontend-posts',
			'powerpack-pp-posts',
			'powerpack-frontend',
		];
	}

    /**
	 * Register posts grid widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
    public function register_query_section_controls() {
		$post_types = PP_Posts_Helper::get_post_types();

        /**
         * Content Tab: Query
         */
        $this->start_controls_section(
            'section_query',
            [
                'label'             	=> __( 'Query', 'powerpack' ),
            ]
        );
		
		$this->add_control(
			'query_type',
			[
				'label'					=> __( 'Query Type', 'powerpack' ),
				'type'					=> Controls_Manager::SELECT,
				'default'				=> 'custom',
				'label_block'			=> true,
				'options'				=> [
					'main'		=> __( 'Main Query', 'powerpack' ),
					'custom'	=> __( 'Custom Query', 'powerpack' ),
				],
			]
		);

		$post_types = PP_Posts_Helper::get_post_types();
		$post_types['related'] = __( 'Related', 'powerpack' );
		
		$this->add_control(
            'post_type',
            [
                'label'					=> __( 'Post Type', 'powerpack' ),
                'type'					=> Controls_Manager::SELECT,
                'options'				=> $post_types,
                'default'				=> 'post',
				'condition'				=> [
					'query_type' => 'custom',
				],

            ]
        );
        
        $this->add_control(
            'posts_per_page',
            [
                'label'                 => __( 'Posts Per Page', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => 6,
				'condition'				=> [
					'query_type' => 'custom',
				],
            ]
		);
		
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
						
						$tax_control_key = $index . '_' . $post_type_slug;
						
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
									'query_type' => 'custom',
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
									'query_type' => 'custom',
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
				'label'					=> __( 'Authors Filter Type', 'powerpack' ),
				'type'					=> Controls_Manager::SELECT,
				'default'				=> 'author__in',
				'label_block'			=> true,
                'separator'         	=> 'before',
				'options'				=> [
					'author__in'     => __( 'Include Authors', 'powerpack' ),
					'author__not_in' => __( 'Exclude Authors', 'powerpack' ),
				],
				'condition'			=> [
					'query_type' => 'custom',
					'post_type!' => 'related',
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
		// 		'condition'			=> [
		// 			'post_type!' => 'related',
		// 		],
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
					'query_type' => 'custom',
					'post_type!' => 'related',
				],
            ]
        );
		
		foreach ( $post_types as $post_type_slug => $post_type_label ) {
		
			//$posts_all = PP_Posts_Helper::get_all_posts_by_type( $post_type_slug );
		
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
					'condition'			=> [
						'query_type' => 'custom',
						'post_type' => $post_type_slug,
					],
				]
			);
			
			// $this->add_control(
			// 	$post_type_slug . '_filter',
			// 	[
			// 		/* translators: %s Label */
			// 		'label'				=> $post_type_label,
			// 		'type'				=> Controls_Manager::SELECT2,
			// 		'default'			=> '',
			// 		'multiple'			=> true,
			// 		'label_block'		=> true,
			// 		'options'			=> $posts_all,
			// 		'condition'			=> [
			// 			'post_type' => $post_type_slug,
			// 		],
			// 	]
			// );

			$this->add_control(
				$post_type_slug . '_filter',
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
						'query_type' => 'custom',
						'post_type' => $post_type_slug,
					],
				]
			);
		}
		
		$taxonomy = PP_Posts_Helper::get_post_taxonomies($post_type_slug);
		$taxonomies = array();
		foreach ( $taxonomy as $index => $tax ) {
			$taxonomies[$tax->name] = $tax->label;
		}
		
		$this->start_controls_tabs(
			'tabs_related',
            [
				'condition'				=> [
					'query_type' => 'custom',
					'post_type' => 'related',
				],
            ]
		);

        $this->start_controls_tab(
            'tab_related_include',
            [
                'label'                 => __( 'Include', 'powerpack' ),
				'condition'				=> [
					'query_type' => 'custom',
					'post_type' => 'related',
				],
            ]
        );
		
		$this->add_control(
			'related_include_by',
			[
				'label'					=> __( 'Include By', 'powerpack' ),
				'type'					=> Controls_Manager::SELECT2,
				'default'				=> '',
				'label_block'			=> true,
				'multiple'				=> true,
				'options'				=> [
					'terms'		=> __( 'Term', 'powerpack' ),
					'authors'	=> __( 'Author', 'powerpack' ),
				],
				'condition'				=> [
					'query_type' => 'custom',
					'post_type' => 'related',
				],
			]
		);
		
		$this->add_control(
			'related_filter_include',
			[
				'label'					=> __( 'Term', 'powerpack' ),
				'type'					=> Controls_Manager::SELECT2,
				'default'				=> '',
				'label_block'			=> true,
				'multiple'				=> true,
				'options'				=> PP_Posts_Helper::get_taxonomies_options(),
				'condition'				=> [
					'query_type' => 'custom',
					'post_type' => 'related',
					'related_include_by' => 'terms',
				],
			]
		);
		
		$this->end_controls_tab();

        $this->start_controls_tab(
            'tab_related_exclude',
            [
                'label'                 => __( 'Exclude', 'powerpack' ),
				'condition'				=> [
					'query_type' => 'custom',
					'post_type' => 'related',
				],
            ]
        );
		
		$this->add_control(
			'related_exclude_by',
			[
				'label'					=> __( 'Exclude By', 'powerpack' ),
				'type'					=> Controls_Manager::SELECT2,
				'default'				=> '',
				'label_block'			=> true,
				'multiple'				=> true,
				'options'				=> [
					'current_post'	=> __( 'Current Post', 'powerpack' ),
					'authors'		=> __( 'Author', 'powerpack' ),
				],
				'condition'				=> [
					'query_type' => 'custom',
					'post_type' => 'related',
				],
			]
		);
		
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
            'related_fallback',
            [
				'label'					=> __( 'Fallback', 'powerpack' ),
				'description'			=> __( 'Displayed if no relevant results are found.', 'powerpack' ),
				'type'					=> Controls_Manager::SELECT,
				'options'				=> [
					'none'		=> __( 'None', 'powerpack' ),
					'recent'	=> __( 'Recent Posts', 'powerpack' ),
				],
				'default'				=> 'none',
				'label_block'			=> false,
				'separator'				=> 'before',
				'condition'				=> [
					'query_type' => 'custom',
					'post_type' => 'related',
				],
			]
        );

		$this->add_control(
            'select_date',
            [
				'label'					=> __( 'Date', 'powerpack' ),
				'type'					=> Controls_Manager::SELECT,
				'options'				=> [
					'anytime'	=> __( 'All', 'powerpack' ),
					'today'		=> __( 'Past Day', 'powerpack' ),
					'week'		=> __( 'Past Week', 'powerpack' ),
					'month'		=> __( 'Past Month', 'powerpack' ),
					'quarter'	=> __( 'Past Quarter', 'powerpack' ),
					'year'		=> __( 'Past Year', 'powerpack' ),
					'exact'		=> __( 'Custom', 'powerpack' ),
				],
				'default'				=> 'anytime',
				'label_block'			=> false,
				'multiple'				=> false,
				'separator'				=> 'before',
				'condition'				=> [
					'query_type' => 'custom',
				],
			]
        );

		$this->add_control(
            'date_before',
            [
				'label'					=> __( 'Before', 'powerpack' ),
				'description'			=> __( 'Setting a ‘Before’ date will show all the posts published until the chosen date (inclusive).', 'powerpack' ),
				'type'					=> Controls_Manager::DATE_TIME,
				'label_block'			=> false,
				'multiple'				=> false,
				'placeholder'			=> __( 'Choose', 'powerpack' ),
				'condition'				=> [
					'query_type' => 'custom',
					'select_date' => 'exact',
				],
			]
        );


		$this->add_control(
            'date_after',
            [
				'label'					=> __( 'After', 'powerpack' ),
				'description'			=> __( 'Setting an ‘After’ date will show all the posts published since the chosen date (inclusive).', 'powerpack' ),
				'type'					=> Controls_Manager::DATE_TIME,
				'label_block'			=> false,
				'multiple'				=> false,
				'placeholder'			=> __( 'Choose', 'powerpack' ),
				'condition'				=> [
					'query_type' => 'custom',
					'select_date' => 'exact',
				],
			]
        );

        $this->add_control(
            'order',
            [
                'label'					=> __( 'Order', 'powerpack' ),
                'type'					=> Controls_Manager::SELECT,
                'options'				=> [
                   'DESC'		=> __( 'Descending', 'powerpack' ),
                   'ASC'		=> __( 'Ascending', 'powerpack' ),
                ],
                'default'				=> 'DESC',
                'separator'				=> 'before',
				'condition'				=> [
					'query_type' => 'custom',
				],
            ]
        );

        $this->add_control(
            'orderby',
            [
                'label'					=> __( 'Order By', 'powerpack' ),
                'type'					=> Controls_Manager::SELECT,
                'options'				=> [
                   'date'           => __( 'Date', 'powerpack' ),
                   'modified'       => __( 'Last Modified Date', 'powerpack' ),
                   'rand'           => __( 'Random', 'powerpack' ),
                   'comment_count'  => __( 'Comment Count', 'powerpack' ),
                   'title'          => __( 'Title', 'powerpack' ),
                   'ID'             => __( 'Post ID', 'powerpack' ),
                   'author'         => __( 'Post Author', 'powerpack' ),
                   'menu_order'     => __( 'Menu Order', 'powerpack' ),
                   'relevance'		=> __( 'Relevance', 'powerpack' ),
                ],
                'default'				=> 'date',
				'condition'				=> [
					'query_type' => 'custom',
				],
            ]
        );
        
        $this->add_control(
            'sticky_posts',
            [
                'label'					=> __( 'Sticky Posts', 'powerpack' ),
                'type'					=> Controls_Manager::SWITCHER,
                'default'				=> '',
                'label_on'				=> __( 'Yes', 'powerpack' ),
                'label_off'				=> __( 'No', 'powerpack' ),
                'return_value'			=> 'yes',
                'separator'				=> 'before',
				'condition'				=> [
					'query_type' => 'custom',
				],
            ]
        );
        
        $this->add_control(
            'all_sticky_posts',
            [
                'label'					=> __( 'Show Only Sticky Posts', 'powerpack' ),
                'type'					=> Controls_Manager::SWITCHER,
                'default'				=> '',
                'label_on'				=> __( 'Yes', 'powerpack' ),
                'label_off'				=> __( 'No', 'powerpack' ),
                'return_value'			=> 'yes',
				'condition'				=> [
					'query_type' => 'custom',
					'sticky_posts' => 'yes',
				],
            ]
        );

        $this->add_control(
            'offset',
            [
                'label'					=> __( 'Offset', 'powerpack' ),
                'description'			=> __( 'Use this setting to skip this number of initial posts', 'powerpack' ),
                'type'					=> Controls_Manager::NUMBER,
                'default'				=> '',
                'min'					=> 0,
                'separator'				=> 'before',
				'condition'				=> [
					'query_type' => 'custom',
					'post_type!' => 'related',
				],
            ]
        );

        $this->add_control(
            'query_id',
            [
                'label'                 => __( 'Query ID', 'powerpack' ),
                'description'           => __( 'Give your Query a custom unique id to allow server side filtering', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'default'               => '',
				'separator'             => 'before',
            ]
        );

		$this->add_control(
			'heading_nothing_found',
			[
				'label'                 => __( 'If Nothing Found!', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

        $this->add_control(
            'nothing_found_message',
            [
                'label'                 => __( 'Nothing Found Message', 'powerpack' ),
                'type'                  => Controls_Manager::TEXTAREA,
                'rows'                  => 3,
                'default'               => __( 'It seems we can\'t find what you\'re looking for.', 'powerpack' ),
            ]
        );

		$this->add_control(
			'show_search_form',
			[
				'label'					=> __( 'Show Search Form', 'powerpack' ),
				'type'					=> Controls_Manager::SWITCHER,
				'label_on'				=> __( 'Yes', 'powerpack' ),
				'label_off'				=> __( 'No', 'powerpack' ),
				'return_value'			=> 'yes',
				'default'				=> '',
			]
		);

        $this->end_controls_section();
    }

    /**
	 * Get post query arguments.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    public function query_posts_args( $filter = '', $taxonomy_filter = '', $search = '', $all_posts = false ) {
        $settings = $this->get_settings_for_display();
        $paged = $this->get_paged();
        $tax_count = 0;
		
		$query_args = array(
			'post_status'           => array( 'publish' ),
			'orderby'               => $settings['orderby'],
			'order'                 => $settings['order'],
			'ignore_sticky_posts'   => ( 'yes' == $settings[ 'sticky_posts' ] ) ? 0 : 1,
			'posts_per_page'		=> -1,
		);
		
		if ( !$all_posts ) {
			$query_args['posts_per_page'] = $settings['posts_per_page'];
		}
		
		if ( $settings['post_type'] == 'related' ) {

			$related_terms = $settings['related_filter_include'];
			$post_terms = wp_get_object_terms( get_the_ID(), $settings['related_filter_include'], array('fields'=>'ids') );
			
			// Query Arguments
			$query_args['post_type'] = get_post_type();
			
			if ( !empty( $settings['related_include_by'] ) ) {
				if ( in_array('authors', $settings['related_include_by']) ) {
					$query_args['author'] = get_the_author_meta('ID');
				}

				if ( in_array('terms', $settings['related_include_by']) ) {
					if ( ! empty( $related_terms ) && ! is_wp_error( $related_terms ) ) {

						foreach ( $related_terms as $index => $tax ) {

							$query_args['tax_query'][] = [
								'taxonomy' => $tax,
								'field'    => 'term_id',
								'terms'    => $post_terms,
							];

						}
					}
				}
			}
			
			if ( !empty( $settings['related_exclude_by'] ) ) {
				if ( in_array('current_post', $settings['related_exclude_by']) ) {
					$query_args['post__not_in'] = array( get_the_ID() );
				}

				if ( in_array('authors', $settings['related_exclude_by']) ) {
					$query_args['author'] = '-' . get_the_author_meta('ID');
				}
			}
			
			if ( $settings['related_fallback'] == 'recent' ) {
				$query = $this->get_query();

				if ( ! $query->found_posts ) {
					$query_args = array(
						'post_status'           => array( 'publish' ),
						'post_type'				=> get_post_type(),
						'orderby'               => $settings['orderby'],
						'order'                 => $settings['order'],
						'ignore_sticky_posts'   => ( 'yes' == $settings[ 'sticky_posts' ] ) ? 0 : 1,
						'showposts'             => $settings['posts_per_page'],
					);
				}
			}

		} else {
			
			// Query Arguments
			$query_args['post_type'] = $settings['post_type'];
			if ( 0 < $settings['offset'] ) {

				/**
				 * Offset break the pagination. Using WordPress's work around
				 *
				 * @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
				 */
				$query_args['offset_to_fix'] = $settings['offset'];
			}
			$query_args['paged'] = $paged;

			// Author Filter
			if ( !empty( $settings['authors'] ) ) {
				$query_args[ $settings['author_filter_type'] ] = $settings['authors'];
			}

			// Posts Filter
			$post_type = $settings['post_type'];

			if ( !empty( $settings[$post_type . '_filter'] ) ) {
				$query_args[ $settings[$post_type . '_filter_type'] ] = $settings[$post_type . '_filter'];
			}

			// Taxonomy Filter
			$taxonomy = pp_get_post_taxonomies( $post_type );

			if ( ! empty( $taxonomy ) && ! is_wp_error( $taxonomy ) ) {

				foreach ( $taxonomy as $index => $tax ) {

					$tax_control_key = $index . '_' . $post_type;

					if ( ! empty( $settings[ $tax_control_key ] ) ) {

						$operator = $settings[ $index . '_' . $post_type . '_filter_type' ];

						$query_args['tax_query'][] = [
							'taxonomy' => $index,
							'field'    => 'term_id',
							'terms'    => $settings[ $tax_control_key ],
							'operator' => $operator,
						];
					}
				}
			}

			$skin_id  = $settings['_skin'];

			if ( '' != $filter && '*' != $filter ) {
				$query_args['tax_query'][$tax_count]['taxonomy'] = $taxonomy_filter;
				$query_args['tax_query'][$tax_count]['field'] = 'slug';
				$query_args['tax_query'][$tax_count]['terms'] = $filter;
				$query_args['tax_query'][$tax_count]['operator'] = 'IN';
			}
			
			if ( '' != $search ) {
				$query_args['s'] = $search;
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

				$query_args['date_query'] = $date_query;
			}
		}

		// Sticky Posts Filter
		if ( $settings['sticky_posts'] == 'yes' && $settings['all_sticky_posts'] == 'yes' ) {
			$post__in = get_option( 'sticky_posts' );

			$query_args['post__in'] = $post__in;
		}
		
		return apply_filters( 'ppe_posts_query_args', $query_args, $settings );
	}

	/**
	 * @param \WP_Query $wp_query
	 */
	public function pre_get_posts_query_filter( $wp_query ) {
        $settings = $this->get_settings_for_display();
		
		$query_id = $settings['query_id'];
		/**
		 * Query args.
		 *
		 * It allows developers to alter individual posts widget queries.
		 *
		 * The dynamic portion of the hook name '$query_id', refers to the Query ID.
		 *
		 * @since 1.4.11.3
		 *
		 * @param \WP_Query     $wp_query
		 */
		do_action( "pp_query_{$query_id}", $wp_query );
		
	}

	public function query_posts( $filter = '', $taxonomy = '', $search = '' ) {
        $settings = $this->get_settings_for_display();
		$query_id = $settings['query_id'];
		
		if ( 'main' === $settings['query_type'] ) {

			global $wp_query;

			$main_query = clone $wp_query;

			$this->query = $main_query;

		} else {

			if ( ! empty( $query_id ) ) {
				add_action( 'pre_get_posts', [ $this, 'pre_get_posts_query_filter' ] );
			}
			$query_args  = $this->query_posts_args( $filter, $taxonomy, $search );
			$this->query = new \WP_Query( $query_args );
			remove_action( 'pre_get_posts', [ $this, 'pre_get_posts_query_filter' ] );

		}
	}

	public function query_filters_posts( $filter = '', $taxonomy = '', $search = '' ) {
        $settings = $this->get_settings();
		$query_id = $settings['query_id'];
		
		if ( 'main' === $settings['query_type'] ) {

			global $wp_query;

			$main_query = clone $wp_query;

			$this->query_filters = $main_query;

		} else {

			if ( ! empty( $query_id ) ) {
				add_action( 'pre_get_posts', [ $this, 'pre_get_posts_query_filter' ] );
			}
			$query_filter_args  = $this->query_posts_args( $filter, $taxonomy, $search, true );
			$this->query_filters = new \WP_Query( $query_filter_args );
			remove_action( 'pre_get_posts', [ $this, 'pre_get_posts_query_filter' ] );

		}
	}

	/**
	 * Render current query.
	 *
	 * @since 1.7.0
	 * @access protected
	 */
	public function get_query() {

		return $this->query;
	}

	/**
	 * Render current query.
	 *
	 * @since 1.7.0
	 * @access protected
	 */
	public function get_query_filters() {

		return $this->query_filters;
	}

	/**
	 * Returns the paged number for the query.
	 *
	 * @since 1.7.0
	 * @return int
	 */
	public function get_paged() {
		$settings = $this->get_settings_for_display();

		global $wp_the_query, $paged;
		
		$skin_id  = $settings['_skin'];
		$pagination_ajax = $settings[$skin_id . '_pagination_ajax'];
		$pagination_type = $settings[$skin_id . '_pagination_type'];
		
		if ( $pagination_ajax == 'yes' || $pagination_type == 'load_more' || $pagination_type == 'infinite' ) {
			if ( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'pp-posts-widget-nonce' ) ) {
				if ( isset( $_POST['page_number'] ) && '' !== $_POST['page_number'] ) {
					return $_POST['page_number'];
				}
			}

			// Check the 'paged' query var.
			$paged_qv = $wp_the_query->get( 'paged' );

			if ( is_numeric( $paged_qv ) ) {
				return $paged_qv;
			}

			// Check the 'page' query var.
			$page_qv = $wp_the_query->get( 'page' );

			if ( is_numeric( $page_qv ) ) {
				return $page_qv;
			}

			// Check the $paged global?
			if ( is_numeric( $paged ) ) {
				return $paged;
			}

			return 0;
		} else {
			return max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
		}
	}

	public function get_posts_nav_link( $page_limit = null ) {
		if ( ! $page_limit ) {
			$page_limit = $this->query->max_num_pages;
		}

		$return = [];

		$paged = $this->get_paged();

		$link_template = '<a class="page-numbers %s" href="%s">%s</a>';
		$disabled_template = '<span class="page-numbers %s">%s</span>';

		if ( $paged > 1 ) {
			$next_page = intval( $paged ) - 1;
			if ( $next_page < 1 ) {
				$next_page = 1;
			}

			$return['prev'] = sprintf( $link_template, 'prev', $this->get_wp_link_page( $next_page ), $this->get_settings( 'pagination_prev_label' ) );
		} else {
			$return['prev'] = sprintf( $disabled_template, 'prev', $this->get_settings( 'pagination_prev_label' ) );
		}

		$next_page = intval( $paged ) + 1;

		if ( $next_page <= $page_limit ) {
			$return['next'] = sprintf( $link_template, 'next', $this->get_wp_link_page( $next_page ), $this->get_settings( 'pagination_next_label' ) );
		} else {
			$return['next'] = sprintf( $disabled_template, 'next', $this->get_settings( 'pagination_next_label' ) );
		}

		return $return;
	}

	private function get_wp_link_page( $i ) {
		if ( ! is_singular() || is_front_page() ) {
			return get_pagenum_link( $i );
		}

		// Based on wp-includes/post-template.php:957 `_wp_link_page`.
		global $wp_rewrite;
		$post = get_post();
		$query_args = [];
		$url = get_permalink();

		if ( $i > 1 ) {
			if ( '' === get_option( 'permalink_structure' ) || in_array( $post->post_status, [ 'draft', 'pending' ] ) ) {
				$url = add_query_arg( 'page', $i, $url );
			} elseif ( get_option( 'show_on_front' ) === 'page' && (int) get_option( 'page_on_front' ) === $post->ID ) {
				$url = trailingslashit( $url ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . $i, 'single_paged' );
			} else {
				$url = trailingslashit( $url ) . user_trailingslashit( $i, 'single_paged' );
			}
		}

		if ( is_preview() ) {
			if ( ( 'draft' !== $post->post_status ) && isset( $_GET['preview_id'], $_GET['preview_nonce'] ) ) {
				$query_args['preview_id'] = wp_unslash( $_GET['preview_id'] );
				$query_args['preview_nonce'] = wp_unslash( $_GET['preview_nonce'] );
			}

			$url = get_preview_post_link( $post, $query_args, $url );
		}

		return $url;
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_skin_field',
			[
				'label'					=> __( 'Skin', 'powerpack' ),
			]
		);

        /*$this->add_control(
            'templates',
            [
                'label'                 => __( 'Choose Template', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT2,
                'label_block'			=> true,
                'options'               => pp_get_page_templates(),
				'condition'             => [
					'_skin'			=> 'template',
				],
            ]
        );*/

		$this->add_control(
			'templates',
			[
				'label'                 => __( 'Choose Template', 'powerpack' ),
				'type'					=> 'pp-query',
				'label_block'			=> false,
				'multiple'				=> false,
				'query_type'			=> 'templates-all',
				'condition'             => [
					'_skin'			=> 'template',
				],
			]
		);

		$this->end_controls_section();

		$this->register_query_section_controls();
	}
}