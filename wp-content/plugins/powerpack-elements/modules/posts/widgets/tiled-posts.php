<?php
namespace PowerpackElements\Modules\Posts\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Posts_Helper;
use PowerpackElements\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Tiled Posts Widget
 */
class Tiled_Posts extends Powerpack_Widget {
    
    /**
	 * Retrieve tiled posts widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return parent::get_widget_name( 'Tiled_Posts' );
    }

    /**
	 * Retrieve tiled posts widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
    public function get_title() {
        return parent::get_widget_title( 'Tiled_Posts' );
    }

    /**
	 * Retrieve the list of categories the tiled posts widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Tiled_Posts' );
    }

    /**
	 * Retrieve tiled posts widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
    public function get_icon() {
        return parent::get_widget_icon( 'Tiled_Posts' );
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
		return parent::get_widget_keywords( 'Tiled_Posts' );
	}

    /**
	 * Register tiled posts widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
    protected function _register_controls() {

		/* Content Tab: Layout */
		$this->register_content_layout_controls();

		/* Content Tab: Other Posts */
		$this->register_content_other_posts_controls();

		/* Content Tab: Query */
		$this->register_content_query_controls();

		/* Content Tab: Post Meta */
		$this->register_content_post_meta_controls();

		/* Content Tab: Help Docs */
		$this->register_content_help_docs();

		/* Style Tab: Layout */
		$this->register_style_layout_controls();

		/* Style Tab: Content */
		$this->register_style_content_controls();

		/* Style Tab: Title */
		$this->register_style_title_controls();

		/* Style Tab: Post Category */
		$this->register_style_post_category_controls();

		/* Style Tab: Post Meta */
		$this->register_style_post_meta_controls();

		/* Style Tab: Post Excerpt */
		$this->register_style_post_excerpt_controls();

		/* Style Tab: Post Overlay */
		$this->register_style_overlay_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	Content Tab
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Content Tab: Layout
	 */
	protected function register_content_layout_controls() {
        $this->start_controls_section(
            'section_post_settings',
            [
                'label'             => __( 'Layout', 'powerpack' ),
            ]
        );

		$this->add_control(
			'layout',
			[
				'label'             => __( 'Layout', 'powerpack' ),
				'type'              => Controls_Manager::CHOOSE,
				'label_block'       => true,
				'toggle'            => false,
				'options'           => [
					'layout-1'  => [
						'title' => __( 'Layout 1', 'powerpack' ),
						'icon'  => 'ppicon-layout-1',
					],
					'layout-2'  => [
						'title' => __( 'Layout 2', 'powerpack' ),
						'icon'  => 'ppicon-layout-2',
					],
					'layout-3'  => [
						'title' => __( 'Layout 3', 'powerpack' ),
						'icon'  => 'ppicon-layout-3',
					],
					'layout-4'  => [
						'title' => __( 'Layout 4', 'powerpack' ),
						'icon'  => 'ppicon-layout-4',
					],
					'layout-5'  => [
						'title' => __( 'Layout 5', 'powerpack' ),
						'icon'  => 'ppicon-layout-5',
					],
					'layout-6'  => [
						'title' => __( 'Layout 6', 'powerpack' ),
						'icon'  => 'ppicon-layout-6',
					],
				],
				'separator'         => 'none',
				'default'           => 'layout-1',
			]
		);

		$this->add_control(
			'content_vertical_position',
			[
				'label'             => __( 'Content Position', 'powerpack' ),
				'type'              => Controls_Manager::CHOOSE,
				'label_block'       => false,
				'options'           => [
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
				'separator'         => 'before',
				'default'           => 'bottom',
			]
		);
        
        $this->add_control(
            'post_title',
            [
                'label'             => __( 'Post Title', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => 'yes',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
            ]
        );
        
        $this->add_control(
			'post_title_length',
			[
				'label'             => __( 'Post Title Length', 'powerpack' ),
				'title'             => __( 'In characters', 'powerpack' ),
                'description'       => __( 'Leave blank to show full title', 'powerpack' ),
				'type'              => Controls_Manager::NUMBER,
				'step'              => 1,
                'condition'         => [
                    'post_title'    => 'yes'
                ]
			]
		);

        $this->add_control(
            'fallback_image',
            [
                'label'             => __( 'Fallback Image', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'options'           => [
                   ''               => __( 'None', 'powerpack' ),
                   'placeholder'    => __( 'Placeholder', 'powerpack' ),
                   'custom'         => __( 'Custom', 'powerpack' ),
                ],
                'default'           => '',
                'separator'         => 'before',
            ]
        );

		$this->add_control(
			'fallback_image_custom',
			[
				'label'             => __( 'Fallback Image Custom', 'powerpack' ),
				'type'              => Controls_Manager::MEDIA,
                'condition'         => [
                    'fallback_image'    => 'custom'
                ]
			]
		);
        
        $this->add_control(
            'large_tile_heading',
            [
                'label'             => __( 'Large Tile', 'powerpack' ),
                'type'              => Controls_Manager::HEADING,
                'separator'         => 'before',
                'condition'         => [
                    'layout!'   => 'layout-5'
                ]
            ]
        );
		
        $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'              => 'image_size',
				'label'             => __( 'Image Size', 'powerpack' ),
				'default'           => 'medium_large',
                'condition'         => [
                    'layout!'   => 'layout-5'
                ]
			]
		);
        
        $this->add_control(
            'post_excerpt',
            [
                'label'             => __( 'Post Excerpt', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => 'no',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
                'condition'         => [
                    'layout!'   => 'layout-5'
                ]
            ]
        );
        
        $this->add_control(
            'excerpt_length',
            [
                'label'             => __( 'Excerpt Length', 'powerpack' ),
                'type'              => Controls_Manager::NUMBER,
                'default'           => 20,
                'min'               => 0,
                'max'               => 58,
                'step'              => 1,
                'condition'         => [
                    'layout!'       => 'layout-5',
                    'post_excerpt'  => 'yes'
                ]
            ]
        );
        
        $this->add_control(
            'small_tiles_heading',
            [
                'label'             => __( 'Small Tiles', 'powerpack' ),
                'type'              => Controls_Manager::HEADING,
                'separator'         => 'before',
            ]
        );
		
        $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'              => 'image_size_small',
				'label'             => __( 'Image Size', 'powerpack' ),
				'default'           => 'medium_large',
			]
		);
        
        $this->add_control(
            'post_excerpt_small',
            [
                'label'             => __( 'Post Excerpt', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => 'no',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
            ]
        );
        
        $this->add_control(
            'excerpt_length_small',
            [
                'label'             => __( 'Excerpt Length', 'powerpack' ),
                'type'              => Controls_Manager::NUMBER,
                'default'           => 20,
                'min'               => 0,
                'max'               => 58,
                'step'              => 1,
                'condition'         => [
                    'post_excerpt_small' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();
	}

	/**
	 * Content Tab: Other Posts
	 */
	protected function register_content_other_posts_controls() {
        $this->start_controls_section(
            'section_other_posts',
            [
                'label'             => __( 'Other Posts', 'powerpack' ),
            ]
        );
        
        $this->add_control(
            'other_posts',
            [
                'label'             => __( 'Show Other Posts', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => 'no',
                'return_value'      => 'yes',
            ]
        );
        
        $this->add_control(
			'other_posts_count',
			[
				'label'             => __( 'Posts Count', 'powerpack' ),
                'description'       => __( 'Leave blank to show all posts', 'powerpack' ),
				'type'              => Controls_Manager::NUMBER,
				'step'              => 1,
				'default'           => 4,
                'condition'         => [
                    'other_posts'   => 'yes'
                ]
			]
		);

        $this->add_control(
            'other_posts_columns',
            [
                'label'             => __( 'Columns', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'options'           => [
                   '1'     => __( '1', 'powerpack' ),
                   '2'     => __( '2', 'powerpack' ),
                   '3'     => __( '3', 'powerpack' ),
                   '4'     => __( '4', 'powerpack' ),
                ],
                'default'           => '2',
                'condition'         => [
                    'other_posts'   => 'yes'
                ]
            ]
        );

        $this->end_controls_section();
	}

	/**
	 * Content Tab: Query
	 */
	protected function register_content_query_controls() {
        $this->start_controls_section(
            'section_post_query',
            [
                'label'             => __( 'Query', 'powerpack' ),
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

	/**
	 * Content Tab: Post Meta
	 */
	protected function register_content_post_meta_controls() {
        $this->start_controls_section(
            'section_post_meta',
            [
                'label'             => __( 'Post Meta', 'powerpack' ),
            ]
        );
        
        $this->add_control(
            'post_meta',
            [
                'label'             => __( 'Post Meta', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => 'yes',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
            ]
        );

        $this->add_control(
            'post_meta_divider',
            [
                'label'             => __( 'Post Meta Divider', 'powerpack' ),
                'type'              => Controls_Manager::TEXT,
                'default'           => '-',
				'selectors'         => [
					'{{WRAPPER}} .pp-tiled-posts-meta > span:not(:last-child):after' => 'content: "{{UNIT}}";',
				],
                'condition'         => [
                    'post_meta'     => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'post_author',
            [
                'label'             => __( 'Post Author', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => 'yes',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
                'condition'         => [
                    'post_meta'     => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'post_category',
            [
                'label'             => __( 'Post Category', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => 'yes',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
                'condition'         => [
                    'post_meta'     => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'post_date',
            [
                'label'             => __( 'Post Date', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => 'yes',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
                'condition'         => [
                    'post_meta'     => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
	}

	/**
	 * Content Tab: Help Docs
	 *
	 * @since 1.4.8
	 * @access protected
	 */
	protected function register_content_help_docs() {

		$help_docs = PP_Config::get_widget_help_links('Tiled_Posts');

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

	/**
	 * Style Tab: Layout
	 */
	protected function register_style_layout_controls() {
        $this->start_controls_section(
            'section_layout_style',
            [
                'label'             => __( 'Layout', 'powerpack' ),
                'tab'               => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
			'height',
			[
				'label'             => __( 'Height', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'size_units'        => [ 'px' ],
				'range'             => [
					'px' => [
						'min' => 200,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default'           => [
					'unit' => 'px',
					'size' => 535,
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-tiled-post' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-tiled-post-medium, {{WRAPPER}} .pp-tiled-post-small, {{WRAPPER}} .pp-tiled-post-xs, {{WRAPPER}} .pp-tiled-post-large' => 'height: calc( ({{SIZE}}{{UNIT}} - {{vertical_spacing.SIZE}}px)/2 );',
				],
			]
		);
        
        $this->add_control(
			'horizontal_spacing',
			[
				'label'             => __( 'Horizontal Spacing', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'size_units'        => [ 'px' ],
				'range'             => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'default'           => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-tiled-posts' => 'margin-left: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-tiled-post, {{WRAPPER}} .pp-tiled-posts-layout-6 .pp-tiles-posts-left .pp-tiled-post, {{WRAPPER}} .pp-tiled-posts-layout-6 .pp-tiles-posts-right .pp-tiled-post' => 'margin-left: {{SIZE}}{{UNIT}}; width: calc( 100% - {{SIZE}}{{UNIT}} );',
					'{{WRAPPER}} .pp-tiled-post-medium' => 'width: calc( 50% - {{SIZE}}{{UNIT}} );',
					'{{WRAPPER}} .pp-tiled-post-small' => 'width: calc( 33.333% - {{SIZE}}{{UNIT}} );',
					'{{WRAPPER}} .pp-tiled-post-xs' => 'width: calc( 25% - {{SIZE}}{{UNIT}} );',
				],
			]
		);
        
        $this->add_control(
			'vertical_spacing',
			[
				'label'             => __( 'Vertical Spacing', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'size_units'        => [ 'px' ],
				'range'             => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 1,
					],
				],
				'default'           => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-tiled-post' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
        $this->add_control(
            'tiles_style_heading',
            [
                'label'             => __( 'Tiles', 'powerpack' ),
                'type'              => Controls_Manager::HEADING,
                'separator'         => 'before',
            ]
        );

        $this->add_control(
            'fallback_img_bg_color',
            [
                'label'             => __( 'Background Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .pp-tiled-post-bg' => 'background-color: {{VALUE}}',
                ],
                'condition'         => [
                    'fallback_image'    => ''
                ]
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'tiles_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-tiled-post',
			]
		);

		$this->add_control(
			'tiles_border_radius',
			[
				'label'					=> __( 'Border Radius', 'powerpack' ),
				'type'					=> Controls_Manager::DIMENSIONS,
				'size_units'			=> [ 'px', '%' ],
				'selectors'				=> [
					'{{WRAPPER}} .pp-tiled-post' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'tiles_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-tiled-post',
			]
		);

        $this->end_controls_section();
	}

	/**
	 * Style Tab: Content
	 */
	protected function register_style_content_controls() {
        $this->start_controls_section(
            'section_post_content_style',
            [
                'label'             => __( 'Content', 'powerpack' ),
                'tab'               => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'              => 'post_content_bg',
                'label'             => __( 'Post Content Background', 'powerpack' ),
                'types'             => [ 'classic', 'gradient' ],
                'exclude'           => [ 'image' ],
                'selector'          => '{{WRAPPER}} .pp-tiled-post-content',
            ]
        );

		$this->add_control(
			'post_content_padding',
			[
				'label'             => __( 'Padding', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-tiled-post-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->end_controls_section();
	}

	/**
	 * Style Tab: Title
	 */
	protected function register_style_title_controls() {
        $this->start_controls_section(
            'section_title_style',
            [
                'label'             => __( 'Title', 'powerpack' ),
                'tab'               => Controls_Manager::TAB_STYLE,
                'condition'         => [
                    'post_title'  => 'yes'
                ]
            ]
        );

        $this->add_control(
            'title_text_color',
            [
                'label'             => __( 'Text Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .pp-tiled-post-title' => 'color: {{VALUE}}',
                ],
                'condition'         => [
                    'post_title'  => 'yes'
                ]
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'              => 'title_typography',
                'label'             => __( 'Typography', 'powerpack' ),
                'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
                'selector'          => '{{WRAPPER}} .pp-tiled-post-title',
                'condition'         => [
                    'post_title'  => 'yes'
                ]
            ]
        );
        
        $this->add_responsive_control(
            'title_margin_bottom',
            [
                'label'             => __( 'Margin Bottom', 'powerpack' ),
                'type'              => Controls_Manager::SLIDER,
                'range'             => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'        => [ 'px' ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-tiled-post-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition'         => [
                    'post_title'  => 'yes'
                ]
            ]
        );
        
        $this->end_controls_section();
	}

	/**
	 * Style Tab: Post Category
	 */
	protected function register_style_post_category_controls() {
        $this->start_controls_section(
            'section_cat_style',
            [
                'label'             => __( 'Post Category', 'powerpack' ),
                'tab'               => Controls_Manager::TAB_STYLE,
                'condition'         => [
                    'post_category'  => 'yes'
                ]
            ]
        );

        $this->add_control(
            'category_style',
            [
                'label'             => __( 'Category Style', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'options'           => [
                   'style-1'       => __( 'Style 1', 'powerpack' ),
                   'style-2'       => __( 'Style 2', 'powerpack' ),
                ],
                'default'           => 'style-1',
                'condition'         => [
                    'post_category'  => 'yes'
                ]
            ]
        );

        $this->add_control(
            'cat_bg_color',
            [
                'label'             => __( 'Background Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'scheme'            => [
                    'type'  => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-post-categories-style-2 span' => 'background: {{VALUE}}',
                ],
                'condition'         => [
                    'post_category'     => 'yes',
                    'category_style'    => 'style-2'
                ]
            ]
        );

        $this->add_control(
            'cat_text_color',
            [
                'label'             => __( 'Text Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '#fff',
                'selectors'         => [
                    '{{WRAPPER}} .pp-post-categories' => 'color: {{VALUE}}',
                ],
                'condition'         => [
                    'post_category'  => 'yes'
                ]
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'              => 'cat_typography',
                'label'             => __( 'Typography', 'powerpack' ),
                'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
                'selector'          => '{{WRAPPER}} .pp-post-categories',
                'condition'         => [
                    'post_category'  => 'yes'
                ]
            ]
        );
        
        $this->add_responsive_control(
            'cat_margin_bottom',
            [
                'label'             => __( 'Margin Bottom', 'powerpack' ),
                'type'              => Controls_Manager::SLIDER,
                'range'             => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'        => [ 'px' ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-post-categories' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition'         => [
                    'post_category'  => 'yes'
                ]
            ]
        );

		$this->add_control(
			'cat_padding',
			[
				'label'             => __( 'Padding', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-post-categories-style-2 span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'         => [
                    'post_category'     => 'yes',
                    'category_style'    => 'style-2'
                ]
			]
		);
        
        $this->end_controls_section();
	}

	/**
	 * Style Tab: Post Meta
	 */
	protected function register_style_post_meta_controls() {
        $this->start_controls_section(
            'section_meta_style',
            [
                'label'             => __( 'Post Meta', 'powerpack' ),
                'tab'               => Controls_Manager::TAB_STYLE,
                'condition'         => [
                    'post_meta' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'meta_text_color',
            [
                'label'             => __( 'Text Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '#fff',
                'selectors'         => [
                    '{{WRAPPER}} .pp-tiled-posts-meta' => 'color: {{VALUE}}',
                ],
                'condition'         => [
                    'post_meta' => 'yes'
                ]
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'              => 'meta_typography',
                'label'             => __( 'Typography', 'powerpack' ),
                'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
                'selector'          => '{{WRAPPER}} .pp-tiled-posts-meta',
                'condition'         => [
                    'post_meta' => 'yes'
                ]
            ]
        );
        
        $this->add_responsive_control(
            'meta_items_spacing',
            [
                'label'             => __( 'Items Spacing', 'powerpack' ),
                'type'              => Controls_Manager::SLIDER,
                'range'             => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'        => [ 'px' ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-tiled-posts-meta > span:not(:last-child):after' => 'margin-left: calc({{SIZE}}{{UNIT}}/2); margin-right: calc({{SIZE}}{{UNIT}}/2);',
                ],
                'condition'         => [
                    'post_meta' => 'yes'
                ]
            ]
        );
        
        $this->add_responsive_control(
            'meta_margin_bottom',
            [
                'label'             => __( 'Margin Bottom', 'powerpack' ),
                'type'              => Controls_Manager::SLIDER,
                'range'             => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'        => [ 'px' ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-tiled-posts-meta' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition'         => [
                    'post_meta' => 'yes'
                ]
            ]
        );

        $this->end_controls_section();
	}

	/**
	 * Style Tab: Post Excerpt
	 */
	protected function register_style_post_excerpt_controls() {
        $this->start_controls_section(
            'section_excerpt_style',
            [
                'label'             => __( 'Post Excerpt', 'powerpack' ),
                'tab'               => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'excerpt_text_color',
            [
                'label'             => __( 'Text Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '#fff',
                'selectors'         => [
                    '{{WRAPPER}} .pp-tiled-post-excerpt' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'              => 'excerpt_typography',
                'label'             => __( 'Typography', 'powerpack' ),
                'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
                'selector'          => '{{WRAPPER}} .pp-tiled-post-excerpt',
            ]
        );

        $this->end_controls_section();
	}

	/**
	 * Style Tab: Overlay
	 */
	protected function register_style_overlay_controls() {
        $this->start_controls_section(
            'section_overlay_style',
            [
                'label'             => __( 'Overlay', 'powerpack' ),
                'tab'               => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'tabs_overlay_style' );

        $this->start_controls_tab(
            'tab_overlay_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'                  => 'post_overlay_bg',
                'label'                 => __( 'Overlay Background', 'powerpack' ),
                'types'                 => [ 'classic', 'gradient' ],
                'exclude'               => [ 'image' ],
                'selector'              => '{{WRAPPER}} .pp-tiled-post-overlay',
            ]
        );
        
        $this->add_control(
            'post_overlay_opacity',
            [
                'label'                 => __( 'Opacity', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 1,
                        'step'  => 0.1,
                    ],
                ],
				'selectors'             => [
					'{{WRAPPER}} .pp-tiled-post-overlay' => 'opacity: {{SIZE}};',
				],
            ]
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_overlay_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'                  => 'post_overlay_bg_hover',
                'label'                 => __( 'Overlay Background', 'powerpack' ),
                'types'                 => [ 'classic', 'gradient' ],
                'exclude'               => [ 'image' ],
                'selector'              => '{{WRAPPER}} .pp-tiled-post:hover .pp-tiled-post-overlay',
            ]
        );
        
        $this->add_control(
            'post_overlay_opacity_hover',
            [
                'label'                 => __( 'Opacity', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 1,
                        'step'  => 0.1,
                    ],
                ],
				'selectors'             => [
					'{{WRAPPER}} .pp-tiled-post:hover .pp-tiled-post-overlay' => 'opacity: {{SIZE}};',
				],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
    }

    /**
	 * Render tiled posts widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render() {
        $settings = $this->get_settings();
        
        $this->add_render_attribute( [
			'tiled-posts' => [
				'class' => [
					'pp-tiled-posts',
					'pp-tiled-posts-' . $settings['layout'],
					'clearfix'
				]
			],
			'post-content' => [
				'class' => [
					'pp-tiled-post-content',
					'pp-tiled-post-content-' . $settings['content_vertical_position']
				]
			],
			'post-categories' => [
				'class' => [
					'pp-post-categories',
					'pp-post-categories-' . $settings['category_style']
				]
			]
		] );
        ?>
        <div <?php echo $this->get_render_attribute_string( 'tiled-posts' ); ?>>
            <?php
                $count = 1;
        
                $layout = $settings['layout'];
        
                if ( $layout == 'layout-1' ) {
                    $pp_posts_count = 4;
                }
                elseif ( $layout == 'layout-2' || $layout == 'layout-3' ) {
                    $pp_posts_count = 3;
                }
                elseif ( $layout == 'layout-4' || $layout == 'layout-5' || $layout == 'layout-6' ) {
                    $pp_posts_count = 5;
                }
                else {
                    $pp_posts_count = 3;
                }
        
                if ( $settings['other_posts'] == 'yes' ) {
                    if ( ! empty( $settings['other_posts_count'] ) && is_numeric( $settings['other_posts_count'] ) ) {
                        $number_of_posts = absint( $settings['other_posts_count'] );
                        $pp_posts_count += $number_of_posts;
                    } else {
                        $pp_posts_count = '-1';
                    }
                }

                $args = $this->get_post_query_args( $pp_posts_count );
		
                $pp_posts_query = new \WP_Query( $args );

                if ( $pp_posts_query->have_posts() ) : while ($pp_posts_query->have_posts()) : $pp_posts_query->the_post();
                    if ( $count == 1 && $layout != 'layout-5' ) {
                        ?><div class="pp-tiles-posts-left"><?php
                    }
        
                    if ( $count == 3 && $layout == 'layout-6' ) {
                        ?><div class="pp-tiles-posts-center"><?php
                    }
        
                    if ( ( $count == 2 && ( $layout == 'layout-1' || $layout == 'layout-2' || $layout == 'layout-3' || $layout == 'layout-4' ) ) || ( $count == 4 && $layout == 'layout-6') ) {
                        ?><div class="pp-tiles-posts-right"><?php
                    }
        
                    if ( $settings['other_posts'] == 'yes' && ( ( $count == 5 && $layout == 'layout-1' ) || ( $count == 4 && ( $layout == 'layout-2' || $layout == 'layout-3' ) ) || ( $count == 6 && ( $layout == 'layout-4' || $layout == 'layout-5' || $layout == 'layout-6' ) ) ) ) {
                        echo '<div class="pp-tiled-post-group pp-tiled-post-col-' . $settings['other_posts_columns'] . '">';
                    }

                    $this->render_post_body( $count, $layout );
        
                    if ( ( $count == 1 && ( $layout == 'layout-1' || $layout == 'layout-2' || $layout == 'layout-3' || $layout == 'layout-4' ) ) || ( $count == 2 && $layout == 'layout-6' ) || ( $count == 3 && $layout == 'layout-6' ) ) {
                        ?></div><?php
                    }
        
                    if ( $settings['other_posts'] == 'yes' && $count == $pp_posts_count ) {
                        echo '</div>';
                    }
                        
                    if ( $layout == 'layout-1' ) {
                        if ( $count == 4 ) { ?></div><?php }
                    }
                    elseif ( $layout == 'layout-2' || $layout == 'layout-3' ) {
                        if ( $count == 3 ) { ?></div><?php }
                    }
                    elseif ( $layout == 'layout-4' ) {
                        if ( $count == 5 ) { ?></div><?php }
                    }
                    elseif ( $layout == 'layout-6' ) {
                        if ( $count == 5 ) { ?></div><?php }
                    }
                $count++; endwhile; endif; wp_reset_postdata();
        ?>
        </div><!--.pp-tiled-posts-->
        <?php
    }

    /**
	 * Get post query arguments.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function get_post_query_args( $posts_count ) {
        $settings = $this->get_settings();
        
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
	 * Render posts body output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_post_body( $count, $layout ) {
        $settings = $this->get_settings();
        
        $this->add_render_attribute( 'post-' . $count, 'class', [
            'pp-tiled-post',
            'pp-tiled-post-' . intval( $count ),
            $this->pp_get_post_class( $count, $layout )
        ] );
        
        if ( has_post_thumbnail() ) {
            $image_id = get_post_thumbnail_id( get_the_ID() );
            if ( ( $count == 1 && ( $layout == 'layout-1' || $layout == 'layout-2' || $layout == 'layout-3' || $layout == 'layout-4' ) ) || ( $count == 3 && $layout == 'layout-6' ) ) {
                $pp_thumb_url = Group_Control_Image_Size::get_attachment_image_src( $image_id, 'image_size', $settings );
            } else {
                $pp_thumb_url = Group_Control_Image_Size::get_attachment_image_src( $image_id, 'image_size_small', $settings );
            }
        } else {
            if ( $settings['fallback_image'] == 'placeholder' ) {
                $pp_thumb_url = Utils::get_placeholder_image_src();
            } elseif ( $settings['fallback_image'] == 'custom' && !empty( $settings['fallback_image_custom']['url'] ) ) {
                $custom_image_id = $settings['fallback_image_custom']['id'];
                if ( $count == 1 && $layout != 'layout-5' ) {
                    $pp_thumb_url = Group_Control_Image_Size::get_attachment_image_src( $custom_image_id, 'image_size', $settings );
                } else {
                    $pp_thumb_url = Group_Control_Image_Size::get_attachment_image_src( $custom_image_id, 'image_size_small', $settings );
                }
            } else {
                $pp_thumb_url = '';
            }
        }
        ?>
        <div <?php echo $this->get_render_attribute_string( 'post-' . $count ); ?>>
            <div class="pp-tiled-post-bg pp-media-background" <?php if ( $pp_thumb_url ) { echo "style='background-image:url(".esc_url( $pp_thumb_url ).")'"; } ?>>
            </div>
            <div class="pp-media-overlay pp-tiled-post-overlay"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"></a></div>
            <div <?php echo $this->get_render_attribute_string( 'post-content' ); ?>>
                <?php if ( $settings['post_meta'] == 'yes' ) { ?>
                    <?php if ( $settings['post_category'] == 'yes' ) { ?>
                        <div <?php echo $this->get_render_attribute_string( 'post-categories' ); ?>>
                            <span>
                                <?php
                                    $category = get_the_category();
                                    if ( $category ) {
                                        echo esc_attr( $category[0]->name );
                                    }
                                ?>
                            </span>
                        </div><!--.pp-post-categories-->
                    <?php } ?>
                <?php } ?>
                <?php if ( $settings['post_title'] == 'yes' ) { ?>
                    <header>
                        <h2 class="pp-tiled-post-title">
                            <?php echo $this->get_post_title_length( get_the_title() ); ?>
                        </h2>
                    </header>
                <?php } ?>
                <?php if ( $settings['post_meta'] == 'yes' ) { ?>
                    <div class="pp-tiled-posts-meta">
                        <?php if ( $settings['post_author'] == 'yes' ) { ?>
                            <span class="pp-post-author">
                                <?php echo get_the_author(); ?>
                            </span>
                        <?php } ?>
                        <?php if ( $settings['post_date'] == 'yes' ) { ?>
                                <?php
                                    $pp_time_string = sprintf( '<time class="entry-date" datetime="%1$s">%2$s</time>',
                                        esc_attr( get_the_date( 'c' ) ),
                                        get_the_date()
                                    );

                                    printf( '<span class="pp-post-date"><span class="screen-reader-text">%1$s </span>%2$s</span>',
                                        __( 'Posted on', 'powerpack' ),
                                        $pp_time_string
                                    );
                                ?>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php $this->render_post_excerpt( $count, $layout ) ?>
            </div><!--.post-inner-->
        </div>
        <?php
    }

    /**
	 * Render posts body output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_post_excerpt( $count, $layout ) {
        $settings = $this->get_settings();
        
        if ( ( $count == 1 && ( $layout == 'layout-1' || $layout == 'layout-2' || $layout == 'layout-3' || $layout == 'layout-4' ) ) || ( $count == 3 && $layout == 'layout-6' ) ) {
            $pp_post_excerpt = $settings['post_excerpt'];
            $limit = $settings['excerpt_length'];
        } else {
            $pp_post_excerpt = $settings['post_excerpt_small'];
            $limit = $settings['excerpt_length_small'];
        }
        
        if ( $pp_post_excerpt == 'yes' ) { ?>
        <div class="pp-tiled-post-excerpt">
            <?php echo $this->get_custom_post_excerpt( $limit ); ?>
        </div>
        <?php }
    }

    /**
	 * Get post class.
	 *
	 * @access protected
	 */
    protected function pp_get_post_class( $count, $layout ) {
        $settings = $this->get_settings();

        $class = '';
        
        if ( ( $count == 2 && $layout == 'layout-1' ) || ( ( $count == 2 || $count == 3 ) && ( $layout == 'layout-2' || $layout == 'layout-3' ) ) ) {
			$class = 'pp-tiled-post-large';
		}
        if ( ( ( $count == 3 || $count == 4 ) && $layout == 'layout-1' ) || ( ( $count == 1 || $count == 2 ) && $layout == 'layout-5' ) || ( ( $count == 1 || $count == 2 || $count == 4 || $count == 5 ) && $layout == 'layout-6' ) ) {
			$class = 'pp-tiled-post-medium';
		}
        if ( $count > 1 && $count < 6 && $layout == 'layout-4' ) {
			$class = 'pp-tiled-post-medium';
		}
        if ( ( $count == 3 || $count == 4 || $count == 5 ) && $layout == 'layout-5' ) {
			$class = 'pp-tiled-post-small';
        }
        
        if ( $this->pp_check_other_posts( $count, $layout ) ) {
            if ( $settings['other_posts_columns'] == '4' ) {
                $class = 'pp-tiled-post-xs';
            }
            elseif ( $settings['other_posts_columns'] == '3' ) {
                $class = 'pp-tiled-post-small';
            }
            elseif ( $settings['other_posts_columns'] == '2' ) {
                $class = 'pp-tiled-post-medium';
            }
            elseif ( $settings['other_posts_columns'] == '1' ) {
                $class = 'pp-tiled-post-large';
            }
        }
        
        return $class;
    }

    /**
	 * Check other posts.
	 *
	 * @access protected
	 */
    protected function pp_check_other_posts( $count, $layout ) {
        $settings = $this->get_settings();

        if ( $settings['other_posts'] == 'yes' && ( ( $count >= 5 && $layout == 'layout-1' ) || ( $count >= 4 && ( $layout == 'layout-2' || $layout == 'layout-3' ) ) || ( $count >= 6 && ( $layout == 'layout-4' || $layout == 'layout-5' ) || ( $count >= 6 && $layout == 'layout-6' ) ) ) ) {
            return true;
        }
    }

    /**
	 * Get post title length.
	 *
	 * @access protected
	 */
    protected function get_post_title_length( $title ) {
        $settings = $this->get_settings();
        
        $length = absint( $settings['post_title_length'] );

        if ( $length != '' ) {
            if ( strlen( $title ) > $length ) {
                $title = substr( $title, 0, $length ). "&hellip;";
            }
        }

        return $title;
    }

    /**
	 * Get custom post excerpt.
	 *
	 * @access protected
	 */
    protected function get_custom_post_excerpt( $limit ) {
        $pp_excerpt = explode(' ', get_the_excerpt(), $limit);
    
        if ( count( $pp_excerpt ) >= $limit ) {
            array_pop($pp_excerpt);
            $pp_excerpt = implode(" ",$pp_excerpt).'...';
        } else {
            $pp_excerpt = implode(" ",$pp_excerpt);
        }

        $pp_excerpt = preg_replace('`[[^]]*]`','',$pp_excerpt);

        return $pp_excerpt;
    }

    /**
	 * Render tiled posts widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
    protected function _content_template() {}
}