<?php
namespace PowerpackElements\Modules\Showcase\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Config;
use PowerpackElements\Modules\Showcase\Module;
use PowerpackElements\Classes\PP_Posts_Helper;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Scheme_Typography;
use Elementor\Embed;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Showcase Widget
 */
class Showcase extends Powerpack_Widget {
    
    /**
	 * Retrieve showcase widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return parent::get_widget_name( 'Showcase' );
    }

    /**
	 * Retrieve showcase widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
    public function get_title() {
        return parent::get_widget_title( 'Showcase' );
    }

    /**
	 * Retrieve the list of categories the showcase widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Showcase' );
    }

    /**
	 * Retrieve showcase widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
    public function get_icon() {
        return parent::get_widget_icon( 'Showcase' );
    }

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.3.6
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Showcase' );
	}
    
    /**
	 * Retrieve the list of scripts the showcase widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_script_depends() {
        return [
            'jquery-fancybox',
            'jquery-resize',
			'jquery-slick',
            'powerpack-frontend'
        ];
    }
    
    /**
	 * Retrieve the list of styles the image slider widget depended on.
	 *
	 * Used to set styles dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_style_depends() {
        return [
            'fancybox',
        ];
    }

    /**
	 * Register showcase widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
    protected function _register_controls() {
		
		/* Content Tab */
		$this->register_content_general_controls();
		$this->register_content_items_controls();
		$this->register_content_query_controls();
		$this->register_content_navigation_controls();
		$this->register_content_preview_controls();
		$this->register_content_additional_options_controls();
		$this->register_content_help_docs_controls();
		
		/* Style Tab */
		$this->register_style_preview_controls();
		$this->register_style_preview_content_controls();
		$this->register_style_preview_overlay_controls();
		$this->register_style_navigation_controls();
		$this->register_style_play_icon_controls();
		$this->register_style_arrows_controls();
		$this->register_style_dots_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/
        
	protected function register_content_general_controls() {
        /**
         * Content Tab: General
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
            'posts_per_page',
            [
                'label'                 => __( 'Posts Count', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => 4,
                'condition'             => [
                    'source'	=> 'posts'
                ]
            ]
        );
		
		$this->end_controls_section();
	}
     
	protected function register_content_items_controls() {
        /**
         * Content Tab: Items
         */
        $this->start_controls_section(
            'section_items',
            [
                'label'                 => __( 'Items', 'powerpack' ),
                'condition'             => [
                    'source'	=> 'custom'
                ]
            ]
        );
        
        $repeater = new Repeater();
        
        $repeater->start_controls_tabs( 'content_tabs' );

        $repeater->start_controls_tab(
			'tab_content',
			[
				'label'					=> __( 'Content', 'powerpack' ),
			]
		);

        $repeater->add_control(
            'title',
            [
                'label'             => __( 'Title', 'powerpack' ),
                'type'              => Controls_Manager::TEXT,
                'default'           => '',
                'dynamic'           => [
                    'active'   => true,
                ],
            ]
        );

        $repeater->add_control(
            'description',
            [
                'label'             => __( 'Description', 'powerpack' ),
                'type'              => Controls_Manager::TEXTAREA,
                'default'           => '',
                'dynamic'           => [
                    'active'   => true,
                ],
            ]
        );
        
        $repeater->end_controls_tab();
        
        $repeater->start_controls_tab(
			'tab_preview',
			[
				'label'					=> __( 'Preview', 'powerpack' ),
			]
		);
        
        $repeater->add_control(
			'content_type',
			[
				'label'                 => esc_html__( 'Content Type', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'label_block'           => false,
                'options'               => [
                    'image'     => __( 'Image', 'powerpack' ),
                    'video' 	=> __( 'Video', 'powerpack' ),
                    'section'   => __( 'Saved Section', 'powerpack' ),
                    'widget'    => __( 'Saved Widget', 'powerpack' ),
                    'template'  => __( 'Saved Page Template', 'powerpack' ),
                ],
				'default'               => 'image',
			]
		);
        
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
                'condition'             => [
                    'content_type'	=> 'image',
                ]
            ]
        );

        $repeater->add_control(
            'link_to',
            [
                'label'                 => __( 'Link to', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'none',
                'options'               => [
                    'none' 		=> __( 'None', 'powerpack' ),
                    'file' 		=> __( 'Media File', 'powerpack' ),
                    'custom' 	=> __( 'Custom URL', 'powerpack' ),
                ],
                'condition'             => [
                    'content_type'	=> 'image',
                ]
            ]
        );
		
		$repeater->add_control(
			'link',
			[
				'label'					=> __( 'Link', 'powerpack' ),
				'type'					=> Controls_Manager::URL,
				'dynamic'				=> [
					'active'        => true,
					'categories'    => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY
					],
				],
				'placeholder'			=> 'https://www.your-link.com',
				'default'				=> [
					'url' => '#',
				],
				'condition'				=> [
                    'content_type'	=> 'image',
                    'link_to'		=> 'custom',
				],
			]
		);

        $repeater->add_control(
            'open_lightbox',
            [
                'label'                 => __( 'Lightbox', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'default',
                'options'               => [
                    'default' 	=> __( 'Default', 'powerpack' ),
                    'yes' 		=> __( 'Yes', 'powerpack' ),
                    'no' 		=> __( 'No', 'powerpack' ),
                ],
                'condition'             => [
                    'content_type'	=> 'image',
                    'link_to'		=> 'file',
                ]
            ]
        );

		$repeater->add_control(
			'video_source',
			[
				'label'                 => __( 'Video Source', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'youtube',
				'options'               => [
					'youtube'      => __( 'YouTube', 'powerpack' ),
					'vimeo'        => __( 'Vimeo', 'powerpack' ),
					'dailymotion'  => __( 'Dailymotion', 'powerpack' ),
				],
                'condition'             => [
                    'content_type'	=> 'video',
                ]
			]
		);

        $repeater->add_control(
            'video_url',
            [
                'label'                 => __( 'Video URL', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'label_block'           => true,
                'default'               => 'https://www.youtube.com/watch?v=9uOETcuFjbE',
                'dynamic'               => [
                    'active'   => true,
                ],
                'condition'             => [
                    'content_type'	=> 'video',
                ]
            ]
        );

		$repeater->add_control(
			'thumbnail_size',
			[
				'label'                 => __( 'Thumbnail Size', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'maxresdefault',
				'options'               => [
					'maxresdefault' => __( 'Maximum Resolution', 'powerpack' ),
                    'hqdefault'     => __( 'High Quality', 'powerpack' ),
                    'mqdefault'     => __( 'Medium Quality', 'powerpack' ),
                    'sddefault'     => __( 'Standard Quality', 'powerpack' ),
				],
                'condition'             => [
                    'content_type'	=> 'video',
                    'video_source'	=> 'youtube',
                ]
			]
		);

        /*$repeater->add_control(
            'saved_widget',
            [
                'label'                 => __( 'Choose Widget', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => $this->get_page_template_options( 'widget' ),
				'default'               => '-1',
                'condition'             => [
                    'content_type'	=> 'widget',
                ]
            ]
        );

        $repeater->add_control(
            'saved_section',
            [
                'label'                 => __( 'Choose Section', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => $this->get_page_template_options( 'section' ),
				'default'               => '-1',
                'condition'             => [
                    'content_type'	=> 'section',
                ]
            ]
        );

        $repeater->add_control(
            'templates',
            [
                'label'                 => __( 'Choose Template', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => $this->get_page_template_options( 'page' ),
				'default'               => '-1',
                'condition'             => [
                    'content_type'	=> 'template',
                ]
            ]
        );*/

		$repeater->add_control(
			'saved_widget',
			[
				'label'                 => __( 'Choose Widget', 'powerpack' ),
				'type'					=> 'pp-query',
				'label_block'			=> false,
				'multiple'				=> false,
				'query_type'			=> 'templates-widget',
                'conditions'        => [
                    'terms' => [
                        [
                            'name'      => 'content_type',
                            'operator'  => '==',
                            'value'     => 'widget',
                        ],
                    ],
                ]
			]
		);

		$repeater->add_control(
			'saved_section',
			[
				'label'                 => __( 'Choose Section', 'powerpack' ),
				'type'					=> 'pp-query',
				'label_block'			=> false,
				'multiple'				=> false,
				'query_type'			=> 'templates-section',
                'conditions'        => [
                    'terms' => [
                        [
                            'name'      => 'content_type',
                            'operator'  => '==',
                            'value'     => 'section',
                        ],
                    ],
                ]
			]
		);

		$repeater->add_control(
			'templates',
			[
				'label'                 => __( 'Choose Template', 'powerpack' ),
				'type'					=> 'pp-query',
				'label_block'			=> false,
				'multiple'				=> false,
				'query_type'			=> 'templates-page',
                'conditions'        => [
                    'terms' => [
                        [
                            'name'      => 'content_type',
                            'operator'  => '==',
                            'value'     => 'template',
                        ],
                    ],
                ]
			]
		);
        
        $repeater->end_controls_tab();

        $repeater->start_controls_tab(
			'tab_navigation',
			[
				'label'					=> __( 'Navigation', 'powerpack' ),
			]
		);
        
        $repeater->add_control(
			'nav_icon_type',
            [
                'label'             => esc_html__( 'Icon Type', 'powerpack' ),
                'type'              => Controls_Manager::CHOOSE,
                'label_block'       => false,
                'options'           => [
                    'none' => [
                        'title' => esc_html__( 'None', 'powerpack' ),
                        'icon'  => 'fa fa-ban',
                    ],
                    'icon' => [
                        'title' => esc_html__( 'Icon', 'powerpack' ),
                        'icon'  => 'fa fa-star',
                    ],
                    'image' => [
                        'title' => esc_html__( 'Image', 'powerpack' ),
                        'icon'  => 'fa fa-picture-o',
                    ],
                ],
                'default'           => 'none',
            ]
        );
		
		$repeater->add_control(
			'select_nav_icon',
			[
				'label'				=> __( 'Icon', 'powerpack' ),
				'type'				=> Controls_Manager::ICONS,
				'fa4compatibility'	=> 'nav_icon',
				'default'			=> [
					'value'		=> 'far fa-image',
					'library'	=> 'fa-regular',
				],
                'condition'			=> [
                    'nav_icon_type' => 'icon',
                ],
			]
		);
        
        $repeater->add_control(
			'nav_icon_image',
            [
                'label'             => esc_html__( 'Icon Image', 'powerpack' ),
                'type'              => Controls_Manager::MEDIA,
                'default'           => [
                    'url' => Utils::get_placeholder_image_src(),
                 ],
                'condition'         => [
                    'nav_icon_type' => 'image',
                ],
            ]
        );
        
        $repeater->end_controls_tab();
        
        $repeater->end_controls_tabs();

        $this->add_control(
            'items',
            [
                'label' 	=> '',
                'type' 		=> Controls_Manager::REPEATER,
                'default' 	=> [
                    [
                        'content_type'   => 'image',
                        'image'         => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'title'         => __( 'Item 1', 'powerpack' ),
                        'description'   => __( 'I am the description for item 1', 'powerpack' ),
                    ],
                    [
                        'content_type'   => 'image',
                        'image'         => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'title'         => __( 'Item 2', 'powerpack' ),
                        'description'   => __( 'I am the description for item 2', 'powerpack' ),
                    ],
                    [
                        'content_type'   => 'image',
                        'image'         => [
                            'url' => Utils::get_placeholder_image_src(),
                        ],
                        'title'         => __( 'Item 3', 'powerpack' ),
                        'description'   => __( 'I am the description for item 3', 'powerpack' ),
                    ],
                ],
                'fields' 		=> array_values( $repeater->get_controls() ),
                'title_field' 	=> '{{ title }}',
                'condition'             => [
                    'source'		=> 'custom'
                ]
            ]
        );

        $this->end_controls_section();
	}
     
	protected function register_content_query_controls() {
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
					
					$terms = get_terms( $index );

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
     
	protected function register_content_navigation_controls() {
        /**
         * Content Tab: Navigation
         */
        $this->start_controls_section(
            'section_navigation',
            [
                'label'                 => __( 'Navigation', 'powerpack' ),
            ]
        );
        
        $this->add_control(
            'navigation_title',
            [
                'label'                 => __( 'Show Title', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
            ]
        );
        
        $this->add_control(
            'navigation_description',
            [
                'label'                 => __( 'Show Description/Excerpt', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
            ]
        );

		$this->add_control(
			'navigation_description_visibility',
			[
				'label'                 => __( 'Description Visibility', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					''			=> __( 'Always', 'powerpack' ),
					'active'	=> __( 'Active Tab Only', 'powerpack' ),
				],
                'condition'         => [
                    'navigation_description' => 'yes'
                ]
			]
		);
        
        $this->add_control(
            'navigation_excerpt_length',
            [
                'label'             => __( 'Excerpt Length', 'powerpack' ),
                'type'              => Controls_Manager::NUMBER,
                'default'           => 8,
                'min'               => 0,
                'max'               => 58,
                'step'              => 1,
                'condition'         => [
                    'source'				 => 'posts',
                    'navigation_description' => 'yes'
                ]
            ]
        );
        
        $this->add_control(
            'scrollable_nav',
            [
                'label'                 => __( 'Scrollable Navigation', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'frontend_available'    => true,
            ]
        );
        
        $this->add_control(
            'nav_center_mode',
            [
                'label'                 => __( 'Center Mode', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'frontend_available'    => true,
                'condition'             => [
					'scrollable_nav'    => 'yes',
				],
            ]
        );

		$slides_to_show = range( 1, 10 );
		$slides_to_show = array_combine( $slides_to_show, $slides_to_show );

		$this->add_responsive_control(
			'nav_items',
			[
				'label'                 => __( 'Items to Show', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					'' => __( 'Default', 'powerpack' ),
				] + $slides_to_show,
				'frontend_available'    => true,
                'condition'             => [
					'scrollable_nav'    => 'yes',
				],
			]
		);

        $this->add_responsive_control(
            'navigation_columns',
            [
                'label'                 => __( 'Columns', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => '1',
                'tablet_default'        => '1',
                'mobile_default'        => '1',
                'options'               => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'prefix_class'          => 'elementor-grid%s-',
                'frontend_available'    => true,
                'condition'             => [
					'scrollable_nav!'   => 'yes',
				],
            ]
        );
		
        $this->end_controls_section();
	}
     
	protected function register_content_preview_controls() {
        /**
         * Content Tab: Preview
         */
        $this->start_controls_section(
            'section_preview',
            [
                'label'                 => __( 'Preview', 'powerpack' ),
            ]
        );

		$this->add_control(
			'images_heading',
			[
				'label'                 => __( 'Images', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
                'condition'             => [
                    'source'		=> 'custom'
                ]
			]
		);
        
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'                  => 'image',
                'label'                 => __( 'Image Size', 'powerpack' ),
                'default'               => 'full',
                'exclude'               => [ 'custom' ],
            ]
        );

		$this->add_control(
			'preview_caption',
			[
				'type'                  => Controls_Manager::SELECT,
				'label'                 => __( 'Caption', 'powerpack' ),
				'default'               => '',
				'options'               => [
					''         => __( 'None', 'powerpack' ),
					'caption'  => __( 'Caption', 'powerpack' ),
					'title'    => __( 'Title', 'powerpack' ),
				],
                'condition'             => [
                    'source'		=> 'custom'
                ]
			]
		);
        
        $this->add_control(
            'preview_title',
            [
                'label'                 => __( 'Show Title', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
            ]
        );
        
        $this->add_control(
            'preview_description',
            [
                'label'                 => __( 'Show Description/Excerpt', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
            ]
        );
        
        $this->add_control(
            'preview_excerpt_length',
            [
                'label'             => __( 'Excerpt Length', 'powerpack' ),
                'type'              => Controls_Manager::NUMBER,
                'default'           => 25,
                'min'               => 0,
                'max'               => 58,
                'step'              => 1,
                'condition'         => [
                    'source'				 => 'posts',
                    'preview_description'	 => 'yes'
                ]
            ]
        );

        $this->add_control(
            'posts_link_to',
            [
                'label'                 => __( 'Link to', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'none',
                'options'               => [
                    'none' 		=> __( 'None', 'powerpack' ),
                    'file' 		=> __( 'Media File', 'powerpack' ),
                    'post_url' 	=> __( 'Post URL', 'powerpack' ),
                    'custom' 	=> __( 'Custom URL', 'powerpack' ),
                ],
                'condition'             => [
                    'source'		=> 'posts',
                ]
            ]
        );

        $this->add_control(
            'posts_link',
            [
                'label'                 => __( 'Link', 'powerpack' ),
                'show_label'            => false,
                'type'                  => Controls_Manager::URL,
                'placeholder'           => __( 'http://your-link.com', 'powerpack' ),
                'condition'             => [
                    'source'		=> 'posts',
                    'posts_link_to'	=> 'custom',
                ]
            ]
        );
        
        $this->add_control(
            'posts_link_external',
            [
                'label'                 => __( 'Open in new window', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
                'condition'             => [
                    'source'		=> 'posts',
                    'posts_link_to'	=> 'post_url',
                ]
            ]
        );
        
        $this->add_control(
            'posts_link_nofollow',
            [
                'label'                 => __( 'Add nofollow', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
                'condition'             => [
                    'source'		=> 'posts',
                    'posts_link_to'	=> 'post_url',
                ]
            ]
        );

        $this->add_control(
            'posts_open_lightbox',
            [
                'label'                 => __( 'Lightbox', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'default',
                'options'               => [
                    'default' 	=> __( 'Default', 'powerpack' ),
                    'yes' 		=> __( 'Yes', 'powerpack' ),
                    'no' 		=> __( 'No', 'powerpack' ),
                ],
                'condition'             => [
                    'source'		=> 'posts',
                    'posts_link_to'	=> 'file',
                ]
            ]
        );

		$this->add_control(
			'videos_heading',
			[
				'label'                 => __( 'Videos', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
                'condition'             => [
                    'source'		=> 'custom'
                ]
			]
		);

		$this->add_control(
			'aspect_ratio',
			[
				'label'                 => __( 'Aspect Ratio', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					'169'  => '16:9',
					'219'  => '21:9',
					'43'   => '4:3',
					'32'   => '3:2',
				],
				'default'               => '169',
				'prefix_class'          => 'elementor-aspect-ratio-',
				'frontend_available'    => true,
                'condition'             => [
                    'source'		=> 'custom'
                ]
			]
		);
        
        $this->add_control(
            'mute',
            [
                'label'                 => __( 'Mute', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
                'condition'             => [
                    'source'		=> 'custom'
                ]
            ]
        );

		$this->add_control(
			'play_icon_heading',
			[
				'label'                 => __( 'Play Icon', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
                'condition'             => [
                    'source'		=> 'custom'
                ]
			]
		);

        $this->add_control(
            'play_icon_type',
            [
                'label'                 => __( 'Icon Type', 'powerpack' ),
				'label_block'           => false,
				'toggle'                => false,
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
					'none'        => [
						'title'   => esc_html__( 'None', 'powerpack' ),
						'icon'    => 'fa fa-ban',
					],
                    'icon'  => [
                        'title' => __( 'Icon', 'powerpack' ),
                        'icon'  => 'fa fa-info-circle',
                    ],
                    'image' => [
                        'title' => __( 'Image', 'powerpack' ),
                        'icon'  => 'fa fa-picture-o',
                    ],
                ],
                'default'               => 'icon',
                'condition'             => [
                    'source'		=> 'custom'
                ]
            ]
        );
		
		$this->add_control(
			'select_play_icon',
			[
				'label'					=> __( 'Select Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'fa4compatibility'		=> 'play_icon',
				'default'				=> [
					'value'		=> 'fas fa-play-circle',
					'library'	=> 'fa-solid',
				],
				'recommended'			=> [
					'fa-regular' => [
						'play-circle',
					],
					'fa-solid' => [
						'play',
						'play-circle',
					],
				],
                'condition'             => [
                    'source'			=> 'custom',
                    'play_icon_type'	=> 'icon',
                ],
			]
		);

        $this->add_control(
            'play_icon_image',
            [
                'label'                 => __( 'Select Image', 'powerpack' ),
                'type'                  => Controls_Manager::MEDIA,
                'default'               => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition'             => [
                    'source'			=> 'custom',
                    'play_icon_type'	=> 'image',
                ],
            ]
        );
        
        $this->end_controls_section();
	}
     
	protected function register_content_additional_options_controls() {
        /**
         * Content Tab: Additional Options
         */
        $this->start_controls_section(
            'section_additional_options',
            [
                'label'                 => __( 'Additional Options', 'powerpack' ),
            ]
        );

		$this->add_control(
			'effect',
			[
				'type'                  => Controls_Manager::SELECT,
				'label'                 => __( 'Effect', 'powerpack' ),
				'default'               => 'slide',
				'options'               => [
					'slide'    => __( 'Slide', 'powerpack' ),
					'fade'     => __( 'Fade', 'powerpack' ),
				],
				'frontend_available'    => true,
			]
		);

        $this->add_control(
            'animation_speed',
            [
                'label'                 => __( 'Animation Speed', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => 600,
                'frontend_available'    => true,
            ]
        );
        
        $this->add_control(
            'arrows',
            [
                'label'                 => __( 'Arrows', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
            ]
        );
        
        $this->add_control(
            'dots',
            [
                'label'                 => __( 'Dots', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
            ]
        );
        
        $this->add_control(
            'autoplay',
            [
                'label'                 => __( 'Autoplay', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'frontend_available'    => true,
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
                    'autoplay'  => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'pause_on_hover',
            [
                'label'                 => __( 'Pause on Hover', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'frontend_available'    => true,
                'condition'             => [
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
                'frontend_available'    => true,
            ]
        );
        
        $this->add_control(
            'adaptive_height',
            [
                'label'                 => __( 'Adaptive Height', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'frontend_available'    => true,
            ]
        );

		$this->add_control(
			'lightbox_heading',
			[
				'label'                 => __( 'Lightbox', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

        $this->add_control(
            'lightbox_library',
            [
                'label'                 => __( 'Lightbox Library', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => '',
                'options'               => [
                    ''          => __( 'Elementor', 'powerpack' ),
                    'fancybox'  => __( 'Fancybox', 'powerpack' ),
                ],
            ]
        );

		$this->add_control(
			'lightbox_caption',
			[
				'type'                  => Controls_Manager::SELECT,
				'label'                 => __( 'Lightbox Caption', 'powerpack' ),
				'default'               => '',
				'options'               => [
					''         => __( 'None', 'powerpack' ),
					'caption'  => __( 'Caption', 'powerpack' ),
					'title'    => __( 'Title', 'powerpack' ),
				],
                'condition'             => [
                    'lightbox_library'  => 'fancybox',
				],
			]
		);

        $this->end_controls_section();
	}
	
	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links('Showcase');
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

	protected function register_style_preview_controls() {
        /**
         * Style Tab: Preview
         */
        $this->start_controls_section(
            'section_preview_style',
            [
                'label'                 => __( 'Preview', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
			'preview_position',
			[
                'label'                 => __( 'Position', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'label_block'           => false,
                'toggle'                => false,
                'default'               => 'right',
                'options'               => [
                    'left'          => [
                        'title'     => __( 'Left', 'powerpack' ),
                        'icon'      => 'eicon-h-align-left',
                    ],
                    'top'           => [
                        'title'     => __( 'Top', 'powerpack' ),
                        'icon'      => 'eicon-v-align-top',
                    ],
                    'right'         => [
                        'title'     => __( 'Right', 'powerpack' ),
                        'icon'      => 'eicon-h-align-right',
                    ],
                    'bottom'           => [
                        'title'     => __( 'Bottom', 'powerpack' ),
                        'icon'      => 'eicon-v-align-bottom',
                    ],
                ],
                'frontend_available'    => true,
			]
		);
        
        $this->add_control(
			'preview_vertical_align',
			[
                'label'                 => __( 'Vertical Align', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'label_block'           => false,
                'toggle'                => false,
                'default'               => 'right',
                'options'               => [
                    'top'           => [
                        'title'     => __( 'Top', 'powerpack' ),
                        'icon'      => 'eicon-v-align-top',
                    ],
                    'middle' 		=> [
                        'title' 	=> __( 'Middle', 'powerpack' ),
                        'icon' 		=> 'eicon-v-align-middle',
                    ],
                    'bottom'           => [
                        'title'     => __( 'Bottom', 'powerpack' ),
                        'icon'      => 'eicon-v-align-bottom',
                    ],
                ],
                'default'               => 'top',
				'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'bottom'   => 'flex-end',
					'middle'   => 'center',
				],
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase' => 'align-items: {{VALUE}};',
                ],
                'condition'             => [
					'preview_position'	=> ['left', 'right'],
				],
			]
		);

        $this->add_responsive_control(
            'preview_image_align',
            [
                'label'                 => __( 'Image Align', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
                    'left' 	=> [
                        'title' 	=> __( 'Left', 'powerpack' ),
                        'icon' 		=> 'fa fa-align-left',
                    ],
                    'center' 		=> [
                        'title' 	=> __( 'Center', 'powerpack' ),
                        'icon' 		=> 'fa fa-align-center',
                    ],
                    'right' 		=> [
                        'title' 	=> __( 'Right', 'powerpack' ),
                        'icon' 		=> 'fa fa-align-right',
                    ],
                ],
                'default'               => 'left',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-image' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
            'preview_stack',
            [
                'label'                 => __( 'Stack On', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'tablet',
                'options'               => [
                    'tablet' 	=> __( 'Tablet', 'powerpack' ),
                    'mobile' 	=> __( 'Mobile', 'powerpack' ),
                ],
                'prefix_class'          => 'pp-showcase-preview-stack-',
                'frontend_available'    => true,
                'condition'             => [
					'preview_position'	=> ['left', 'right'],
				],
            ]
        );

        $this->add_responsive_control(
            'preview_width',
            [
                'label'                 => __( 'Width', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'size_units'            => [ '%' ],
                'devices'               => [ 'desktop', 'tablet' ],
                'range'                 => [
                    '%' 	=> [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'               => [
                    'size' 	=> 70,
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-preview-align-left .pp-showcase-preview-wrap' => 'width: {{SIZE}}%',
                    '{{WRAPPER}} .pp-showcase-preview-align-right .pp-showcase-preview-wrap' => 'width: {{SIZE}}%',
                    '{{WRAPPER}} .pp-showcase-preview-align-right .pp-showcase-navigation' => 'width: calc(100% - {{SIZE}}%)',
                    '{{WRAPPER}} .pp-showcase-preview-align-left .pp-showcase-navigation' => 'width: calc(100% - {{SIZE}}%)',
                ],
                'condition'             => [
					'preview_position'	=> ['left', 'right'],
				],
            ]
        );
        
        $this->add_responsive_control(
            'preview_spacing',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' 	=> [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'default'               => [
                    'size' 	=> 20,
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-preview-align-left .pp-showcase,
                    {{WRAPPER}} .pp-showcase-preview-align-right .pp-showcase' => 'margin-left: -{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .pp-showcase-preview-align-left .pp-showcase > *,
                    {{WRAPPER}} .pp-showcase-preview-align-right .pp-showcase > *' => 'padding-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .pp-showcase-preview-align-top .pp-showcase-preview-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .pp-showcase-preview-align-bottom .pp-showcase-preview-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
                    '(tablet){{WRAPPER}} .pp-showcase-preview-stack-tablet .pp-showcase-preview-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}} .pp-showcase-preview-stack-mobile .pp-showcase-preview-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$this->add_responsive_control(
			'preview_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-showcase-preview' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'preview_background',
				'types'            	    => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .pp-showcase-preview',
                'exclude'               => [
                    'image',
                ],
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'preview_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-showcase-preview',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'preview_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-showcase-preview',
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'                  => 'preview_css_filters',
				'selector'              => '{{WRAPPER}} .pp-showcase-preview img',
			]
		);

        $this->end_controls_section();
	}

	protected function register_style_preview_content_controls() {
        /**
         * Style Tab: Preview Content
         */
        $this->start_controls_section(
            'section_preview_captions_style',
            [
                'label'                 => __( 'Preview Content', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
				'conditions'			=> [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'preview_caption',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name' => 'preview_title',
							'operator' => '==',
							'value' => 'yes',
						],
						[
							'name' => 'preview_description',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
            ]
        );

        $this->add_responsive_control(
            'preview_captions_vertical_align',
            [
                'label'                 => __( 'Vertical Align', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
                    'top' 	=> [
                        'title' 	=> __( 'Top', 'powerpack' ),
                        'icon' 		=> 'eicon-v-align-top',
                    ],
                    'middle' 		=> [
                        'title' 	=> __( 'Middle', 'powerpack' ),
                        'icon' 		=> 'eicon-v-align-middle',
                    ],
                    'bottom' 		=> [
                        'title' 	=> __( 'Bottom', 'powerpack' ),
                        'icon' 		=> 'eicon-v-align-bottom',
                    ],
                ],
                'default'               => 'bottom',
				'selectors' => [
					'{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-content' => 'justify-content: {{VALUE}};',
				],
				'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'bottom'   => 'flex-end',
					'middle'   => 'center',
				],
				'conditions'			=> [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'preview_caption',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name' => 'preview_title',
							'operator' => '==',
							'value' => 'yes',
						],
						[
							'name' => 'preview_description',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
            ]
        );

        $this->add_responsive_control(
            'preview_captions_horizontal_align',
            [
                'label'                 => __( 'Horizontal Align', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
                    'left' 	=> [
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
                    'justify' 		=> [
                        'title' 	=> __( 'Justify', 'powerpack' ),
                        'icon' 		=> 'eicon-h-align-stretch',
                    ],
                ],
                'default'               => 'left',
				'selectors' => [
					'{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-content' => 'align-items: {{VALUE}};',
				],
				'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'right'    => 'flex-end',
					'center'   => 'center',
					'justify'  => 'stretch',
				],
				'conditions'			=> [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'preview_caption',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name' => 'preview_title',
							'operator' => '==',
							'value' => 'yes',
						],
						[
							'name' => 'preview_description',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
            ]
        );

        $this->add_responsive_control(
            'preview_captions_align',
            [
                'label'                 => __( 'Text Align', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
                    'left' 	=> [
                        'title' 	=> __( 'Left', 'powerpack' ),
                        'icon' 		=> 'fa fa-align-left',
                    ],
                    'center' 		=> [
                        'title' 	=> __( 'Center', 'powerpack' ),
                        'icon' 		=> 'fa fa-align-center',
                    ],
                    'right' 		=> [
                        'title' 	=> __( 'Right', 'powerpack' ),
                        'icon' 		=> 'fa fa-align-right',
                    ],
                ],
                'default'               => 'center',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-info' => 'text-align: {{VALUE}};',
                ],
				'conditions'			=> [
					'relation' => 'or',
					'terms' => [
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'preview_caption',
									'operator' => '!=',
									'value' => '',
								],
								[
									'name' => 'preview_captions_horizontal_align',
									'operator' => '==',
									'value' => 'justify',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'preview_title',
									'operator' => '==',
									'value' => 'yes',
								],
								[
									'name' => 'preview_captions_horizontal_align',
									'operator' => '==',
									'value' => 'justify',
								],
							],
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'preview_description',
									'operator' => '==',
									'value' => 'yes',
								],
								[
									'name' => 'preview_captions_horizontal_align',
									'operator' => '==',
									'value' => 'justify',
								],
							],
						],
					],
				],
            ]
        );

		$this->add_responsive_control(
			'preview_captions_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-info' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions'			=> [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'preview_caption',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name' => 'preview_title',
							'operator' => '==',
							'value' => 'yes',
						],
						[
							'name' => 'preview_description',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'preview_captions_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions'			=> [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'preview_caption',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name' => 'preview_title',
							'operator' => '==',
							'value' => 'yes',
						],
						[
							'name' => 'preview_description',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
			]
		);

        $this->start_controls_tabs( 'tabs_preview_captions_style' );

        $this->start_controls_tab(
            'tab_preview_captions_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
				'conditions'			=> [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'preview_caption',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name' => 'preview_title',
							'operator' => '==',
							'value' => 'yes',
						],
						[
							'name' => 'preview_description',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'preview_captions_background',
				'types'            	    => [ 'classic','gradient' ],
				'selector'              => '{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-info',
                'exclude'               => [
                    'image',
                ],
			]
		);

		$this->add_control(
			'preview_caption_style_heading',
			[
				'label'                 => __( 'Caption', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
                'condition'             => [
                    'preview_caption!'  => '',
                ],
			]
		);

        $this->add_control(
            'preview_captions_text_color',
            [
                'label'                 => __( 'Caption Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-info' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'preview_caption!'  => '',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'preview_captions_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-caption',
                'condition'             => [
                    'preview_caption!'  => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name'                  => 'preview_text_shadow',
                'selector' 	            => '{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-caption',
                'condition'             => [
                    'preview_caption!'  => '',
                ],
            ]
        );

		$this->add_control(
			'preview_title_style_heading',
			[
				'label'                 => __( 'Title', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
                'condition'             => [
                    'preview_title!'  => '',
                ],
			]
		);

        $this->add_control(
            'preview_title_color',
            [
                'label'                 => __( 'Title Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-title' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'preview_title!'  => '',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'preview_title_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-caption',
                'condition'             => [
                    'preview_title!'  => '',
                ],
            ]
        );

		$this->add_control(
			'preview_description_style_heading',
			[
				'label'                 => __( 'Description', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
                'condition'             => [
                    'preview_description!'  => '',
                ],
			]
		);

        $this->add_control(
            'preview_description_color',
            [
                'label'                 => __( 'Description/Excerpt Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-description' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'preview_description!'  => '',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'preview_description_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-description',
                'condition'             => [
                    'preview_description!'  => '',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'preview_captions_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-info',
				'separator'             => 'before',
				'conditions'			=> [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'preview_caption',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name' => 'preview_title',
							'operator' => '==',
							'value' => 'yes',
						],
						[
							'name' => 'preview_description',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'preview_captions_border_radius_normal',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-info' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'conditions'			=> [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'preview_caption',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name' => 'preview_title',
							'operator' => '==',
							'value' => 'yes',
						],
						[
							'name' => 'preview_description',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_preview_captions_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
				'conditions'			=> [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'preview_caption',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name' => 'preview_title',
							'operator' => '==',
							'value' => 'yes',
						],
						[
							'name' => 'preview_description',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'preview_captions_background_hover',
				'types'            	    => [ 'classic','gradient' ],
				'selector'              => '{{WRAPPER}} .pp-showcase-preview:hover .pp-showcase-preview-info',
                'exclude'               => [
                    'image',
                ],
			]
		);

        $this->add_control(
            'preview_captions_text_color_hover',
            [
                'label'                 => __( 'Caption Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-preview:hover .pp-showcase-preview-caption' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'preview_caption!'  => '',
                ],
            ]
        );

        $this->add_control(
            'preview_title_color_hover',
            [
                'label'                 => __( 'Title Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-preview:hover .pp-showcase-preview-title' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'preview_title!'  => '',
                ],
            ]
        );

        $this->add_control(
            'preview_description_color_hover',
            [
                'label'                 => __( 'Description/Excerpt Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-preview:hover .pp-showcase-preview-description' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'preview_description!'  => '',
                ],
            ]
        );

        $this->add_control(
            'preview_captions_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-preview:hover .pp-showcase-preview-info' => 'border-color: {{VALUE}}',
                ],
				'conditions'			=> [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'preview_caption',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name' => 'preview_title',
							'operator' => '==',
							'value' => 'yes',
						],
						[
							'name' => 'preview_description',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name'                  => 'preview_text_shadow_hover',
                'selector' 	            => '{{WRAPPER}} .pp-showcase-preview:hover .pp-showcase-preview-caption',
                'condition'             => [
                    'preview_caption!'  => '',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->add_control(
			'preview_captions_blend_mode',
			[
				'label'                 => __( 'Blend Mode', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					''             => __( 'Normal', 'powerpack' ),
					'multiply'     => 'Multiply',
					'screen'       => 'Screen',
					'overlay'      => 'Overlay',
					'darken'       => 'Darken',
					'lighten'      => 'Lighten',
					'color-dodge'  => 'Color Dodge',
					'saturation'   => 'Saturation',
					'color'        => 'Color',
					'difference'   => 'Difference',
					'exclusion'    => 'Exclusion',
					'hue'          => 'Hue',
					'luminosity'   => 'Luminosity',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-showcase-preview .pp-showcase-preview-info' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator'             => 'before',
				'conditions'			=> [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'preview_caption',
							'operator' => '!=',
							'value' => '',
						],
						[
							'name' => 'preview_title',
							'operator' => '==',
							'value' => 'yes',
						],
						[
							'name' => 'preview_description',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
			]
		);

        $this->end_controls_section();
	}

	protected function register_style_preview_overlay_controls() {
        /**
         * Style Tab: Preview Overlay
         */
        $this->start_controls_section(
            'section_preview_overlay_style',
            [
                'label'                 => __( 'Preview Overlay', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'tabs_preview_overlay_style' );

        $this->start_controls_tab(
            'tab_preview_overlay_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'preview_overlay_background',
				'types'            	    => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .pp-showcase-preview-overlay',
                'exclude'               => [
                    'image',
                ],
			]
		);

		$this->add_responsive_control(
			'preview_overlay_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-showcase-preview-overlay' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_tab();

        $this->start_controls_tab(
            'tab_preview_overlay_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'preview_overlay_background_hover',
				'types'            	    => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .pp-showcase-preview:hover .pp-showcase-preview-overlay',
                'exclude'               => [
                    'image',
                ],
			]
		);
		
		$this->end_controls_tab();
		
		$this->end_controls_tabs();

        $this->end_controls_section();
	}

	protected function register_style_navigation_controls() {
        /**
         * Style Tab: Navigation
         */
        $this->start_controls_section(
            'section_navigation_style',
            [
                'label'                 => __( 'Navigation', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_responsive_control(
            'navigation_items_horizontal_spacing',
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
                    'size' 	=> '',
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-navigation-items .pp-showcase-navigation-item-wrap' => 'padding-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .pp-showcase-navigation-items'  => 'margin-left: -{{SIZE}}{{UNIT}};',
                ],
                'condition'             => [
                    'source'			=> 'custom',
                ],
            ]
        );

        $this->add_responsive_control(
            'navigation_items_vertical_spacing',
            [
                'label'                 => __( 'Row Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' 	=> [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'default'               => [
                    'size'  => 15,
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-navigation-item-wrap:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'navigation_text_align',
            [
                'label'                 => __( 'Text Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => true,
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
				],
				'default'               => 'left',
				'selectors'             => [
					'{{WRAPPER}} .pp-showcase-navigation-items .pp-showcase-navigation-item' => 'text-align: {{VALUE}};',
				],
			]
		);

        $this->add_control(
            'navigation_background',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-navigation-items' => 'background-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_responsive_control(
			'navigation_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-showcase-navigation-items' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->start_controls_tabs( 'tabs_title_style' );

        $this->start_controls_tab(
            'tab_title_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
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
                    '{{WRAPPER}} .pp-showcase-navigation-title' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'title_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-showcase-navigation-title',
            ]
        );
        
        $this->add_responsive_control(
            'title_margin',
            [
                'label'                 => __( 'Margin Bottom', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'  => 5,
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
                    '{{WRAPPER}} .pp-showcase-navigation-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
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
                    '{{WRAPPER}} .pp-showcase-navigation-description' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'description_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-showcase-navigation-description',
            ]
        );

		$this->add_control(
			'navigation_icon_heading',
			[
				'label'                 => __( 'Navigation Icon/Image', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

        $this->add_control(
            'navigation_icon_color',
            [
                'label'                 => __( 'Icon Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-navigation-icon' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'navigation_icon_size',
            [
                'label'                 => __( 'Icon Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min' => 10,
                        'max' => 400,
                    ],
                ],
                'default'               => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-navigation-icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                ],
                'condition'             => [
                    'source'			=> 'custom',
                ],
            ]
        );

        $this->add_responsive_control(
            'navigation_icon_img_size',
            [
                'label'                 => __( 'Icon Image Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min' => 10,
                        'max' => 400,
                    ],
                ],
                'default'               => [
                    'size' => 80,
                    'unit' => 'px',
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-navigation-icon img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'navigation_icon_margin',
            [
                'label'                 => __( 'Margin Bottom', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'  => 5,
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
                    '{{WRAPPER}} .pp-showcase-navigation-icon-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

		$this->add_control(
			'navigation_item_heading',
			[
				'label'                 => __( 'Navigation Item', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

        $this->add_control(
            'navigation_item_background_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-navigation-item' => 'background-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'navigation_item_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-showcase-navigation-item',
			]
		);

		$this->add_control(
			'navigation_item_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-showcase-navigation-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'navigation_item_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-showcase-navigation-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'navigation_item_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-showcase-navigation-item',
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_title_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
            ]
        );

		$this->add_control(
			'title_heading_hover',
			[
				'label'                 => __( 'Title', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

        $this->add_control(
            'title_color_hover',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-navigation-item:hover .pp-showcase-navigation-title' => 'color: {{VALUE}}',
                ],
            ]
        );

		$this->add_control(
			'description_heading_hover',
			[
				'label'                 => __( 'Description', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

        $this->add_control(
            'description_color_hover',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-navigation-item:hover .pp-showcase-navigation-description' => 'color: {{VALUE}}',
                ],
            ]
        );

		$this->add_control(
			'navigation_icon_hover_heading',
			[
				'label'                 => __( 'Navigation Icon', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

        $this->add_control(
            'navigation_icon_color_hover',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-navigation-item:hover .pp-showcase-navigation-icon' => 'color: {{VALUE}}',
                ],
            ]
        );

		$this->add_control(
			'navigation_item_hover_heading',
			[
				'label'                 => __( 'Navigation Item', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

        $this->add_control(
            'navigation_item_background_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-navigation-item:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'navigation_item_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-showcase-navigation-item:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'navigation_item_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-showcase-navigation-item:hover',
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_color_active',
            [
                'label'                 => __( 'Active', 'powerpack' ),
            ]
        );

		$this->add_control(
			'title_heading_active',
			[
				'label'                 => __( 'Title', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

        $this->add_control(
            'title_color_active',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-active-slide .pp-showcase-navigation-item .pp-showcase-navigation-title, {{WRAPPER}} .slick-current .pp-showcase-navigation-item .pp-showcase-navigation-title' => 'color: {{VALUE}}',
                ],
            ]
        );

		$this->add_control(
			'description_heading_active',
			[
				'label'                 => __( 'Description', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

        $this->add_control(
            'description_color_active',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-active-slide .pp-showcase-navigation-item .pp-showcase-navigation-description, {{WRAPPER}} .slick-current .pp-showcase-navigation-item .pp-showcase-navigation-description' => 'color: {{VALUE}}',
                ],
            ]
        );

		$this->add_control(
			'navigation_icon_active_heading',
			[
				'label'                 => __( 'Navigation Icon', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

        $this->add_control(
            'navigation_icon_active_hover',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-active-slide .pp-showcase-navigation-item .pp-showcase-navigation-icon, {{WRAPPER}} .slick-current .pp-showcase-navigation-item .pp-showcase-navigation-icon' => 'color: {{VALUE}}',
                ],
            ]
        );

		$this->add_control(
			'navigation_item_active_heading',
			[
				'label'                 => __( 'Navigation Item', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

        $this->add_control(
            'navigation_item_background_color_active',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-active-slide .pp-showcase-navigation-item, {{WRAPPER}} .slick-current .pp-showcase-navigation-item' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'navigation_item_border_color_active',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-active-slide .pp-showcase-navigation-item, {{WRAPPER}} .slick-current .pp-showcase-navigation-item' => 'border-color: {{VALUE}}',
                ],
            ]
        );
        
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'navigation_item_box_shadow_active',
				'selector'              => '{{WRAPPER}} .pp-active-slide .pp-showcase-navigation-item, {{WRAPPER}} .slick-current .pp-showcase-navigation-item',
			]
		);
        
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
	}

	protected function register_style_play_icon_controls() {
        /**
         * Style Tab: Play Icon
         */
        $this->start_controls_section(
			'section_play_icon_style',
			[
				'label'                 => __( 'Play Icon', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'source'			=> 'custom',
                    'play_icon_type!'   => 'none',
                ],
			]
		);

        $this->add_responsive_control(
            'play_icon_size',
            [
                'label'                 => __( 'Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min' => 10,
                        'max' => 400,
                    ],
                ],
                'default'               => [
                    'size' => 80,
                    'unit' => 'px',
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-play-icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                ],
                'condition'             => [
                    'source'			=> 'custom',
                    'play_icon_type!'   => 'none',
                ],
            ]
        );

		$this->add_control(
			'play_icon_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-video-play-icon img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'source'			=> 'custom',
                    'play_icon_type'    => 'image',
                ],
			]
		);

        $this->start_controls_tabs( 'tabs_play_icon_style' );

        $this->start_controls_tab(
            'tab_play_icon_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
                    'source'			=> 'custom',
                    'play_icon_type'    => 'icon',
                    'select_play_icon[value]!' => '',
                ],
            ]
        );

        $this->add_control(
            'play_icon_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-play-icon' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-video-play-icon svg' => 'fill: {{VALUE}}',
                ],
                'condition'             => [
                    'source'			=> 'custom',
                    'play_icon_type'    => 'icon',
                    'select_play_icon[value]!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name'                  => 'play_icon_text_shadow',
                'selector'              => '{{WRAPPER}} .pp-video-play-icon',
                'condition'             => [
                    'source'			=> 'custom',
                    'play_icon_type'    => 'icon',
                    'select_play_icon[value]!' => '',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_play_icon_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'source'			=> 'custom',
                    'play_icon_type'    => 'icon',
                    'select_play_icon[value]!' => '',
                ],
            ]
        );

        $this->add_control(
            'play_icon_hover_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-container:hover .pp-video-play-icon' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-video-container:hover .pp-video-play-icon svg' => 'fill: {{VALUE}}',
                ],
                'condition'             => [
                    'source'			=> 'custom',
                    'play_icon_type'    => 'icon',
                    'select_play_icon[value]!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name'                  => 'play_icon_hover_text_shadow',
                'selector'              => '{{WRAPPER}} .pp-video-container:hover .pp-video-play-icon',
                'condition'             => [
                    'source'			=> 'custom',
                    'play_icon_type'    => 'icon',
                    'select_play_icon[value]!' => '',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_style_arrows_controls() {
        /**
         * Style Tab: Arrows
         */
        $this->start_controls_section(
            'section_arrows_style',
            [
                'label'                 => __( 'Arrows', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'arrows'        => 'yes',
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
                    'arrows'        => 'yes',
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
                    'arrows'        => 'yes',
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
                    'arrows'        => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_arrows_style' );

        $this->start_controls_tab(
            'tab_arrows_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
                    'arrows'        => 'yes',
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
                    'arrows'        => 'yes',
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
                    'arrows'        => 'yes',
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
                    'arrows'        => 'yes',
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
                    'arrows'        => 'yes',
                ],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_arrows_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'arrows'        => 'yes',
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
                    'arrows'        => 'yes',
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
                    'arrows'        => 'yes',
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
                    '{{WRAPPER}} .pp-slider-arrow:hover',
                ],
                'condition'             => [
                    'arrows'        => 'yes',
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
                    'arrows'        => 'yes',
                ],
			]
		);
        
        $this->end_controls_section();
	}

	protected function register_style_dots_controls() {
        /**
         * Style Tab: Dots
         */
        $this->start_controls_section(
            'section_dots_style',
            [
                'label'                 => __( 'Dots', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
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
                    '{{WRAPPER}} .pp-showcase-preview .slick-dots li button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition'             => [
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
                    '{{WRAPPER}} .pp-showcase-preview .slick-dots li' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
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
                    '{{WRAPPER}} .pp-showcase-preview .slick-dots li' => 'background: {{VALUE}};',
                ],
                'condition'             => [
                    'dots'      => 'yes',
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
                    '{{WRAPPER}} .pp-showcase-preview .slick-dots li.slick-active' => 'background: {{VALUE}};',
                ],
                'condition'             => [
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
				'selector'              => '{{WRAPPER}} .pp-showcase-preview .slick-dots li',
                'condition'             => [
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
					'{{WRAPPER}} .pp-showcase-preview .slick-dots li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
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
					'{{WRAPPER}} .pp-showcase-preview .slick-dots' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
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
                    '{{WRAPPER}} .pp-showcase-preview .slick-dots li:hover' => 'background: {{VALUE}};',
                ],
                'condition'             => [
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
                    '{{WRAPPER}} .pp-showcase-preview .slick-dots li:hover' => 'border-color: {{VALUE}};',
                ],
                'condition'             => [
                    'dots'      => 'yes',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
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

    protected function render() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute( [
			'showcase-wrap' => [
				'class' => [
					'pp-showcase-wrap',
					'pp-showcase-preview-align-' . $settings['preview_position']
				]
			],
			'preview-wrap' => [
				'class' => [
					'pp-showcase-preview-wrap'
				]
			],
			'preview' => [
				'class' => [
					'pp-showcase-preview',
					'pp-slick-slider'
				],
				'id' => [
					'pp-showcase-preview-' . esc_attr( $this->get_id() )
				]
			]
		] );
		
		if ( $settings['navigation_description_visibility'] == 'active' ) {
			$this->add_render_attribute( 'showcase-wrap', 'class', 'pp-showcase-description-visible-active' );
		}
		
		if ( is_rtl() ) {
			$this->add_render_attribute( 'preview', 'data-rtl', 'yes' );
		}
        ?>
        <div <?php echo $this->get_render_attribute_string( 'showcase-wrap' ); ?>>
			<div class="pp-showcase">
				<div <?php echo $this->get_render_attribute_string( 'preview-wrap' ); ?>>
					<div <?php echo $this->get_render_attribute_string( 'preview' ); ?>>
						<?php
							$this->render_preview();
						?>
					</div>
				</div>
				<?php
					// Items Navigation
					$this->render_navigation();
				?>
			</div>
        </div>
        <?php
    }
    
    protected function render_preview() {
        $settings = $this->get_settings_for_display();
		
		if ( $settings['source'] == 'posts' ) {
			$this->render_preview_posts();
		} else {
			$this->render_preview_custom();
		}
	}
    
    protected function render_preview_custom() {
        $settings = $this->get_settings_for_display();
        
        foreach ( $settings['items'] as $index => $item ) {
            ?>
            <div class="pp-showcase-preview-item">
                <?php
                if ( $item['content_type'] == 'image' && $item['image']['url'] ) {

                    $image_url = Group_Control_Image_Size::get_attachment_image_src( $item['image']['id'], 'image', $settings );

                    if ( ! $image_url ) {
                        $image_url = $item['image']['url'];
                    }

                    $image_html = '<div class="pp-showcase-preview-image">';

                    $image_html .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( Control_Media::get_image_alt( $item['image'] ) ) . '">';

                    $image_html .= '</div>';
					
                    $image_html .= $this->render_image_overlay();

                    if ( $settings['preview_caption'] != '' || $settings['preview_title'] == 'yes' || $settings['preview_description'] == 'yes' ) {
						$image_html .= '<div class="pp-showcase-preview-content pp-media-content">';
						$image_html .= '<div class="pp-showcase-preview-info">';
							if ( $settings['preview_caption'] != '' ) {
								$image_html .= $this->render_image_caption( $item['image']['id'], $settings['preview_caption'] );
							}

							if ( $settings['preview_title'] == 'yes' && $item['title'] != '' ) {
								$image_html .= '<div class="pp-showcase-preview-title">';
									$image_html .= $item['title'];
								$image_html .= '</div>';
							}

							if ( $settings['preview_description'] == 'yes' && $item['description'] != '' ) {
								$image_html .= '<div class="pp-showcase-preview-description">';
									$image_html .= $item['description'];
								$image_html .= '</div>';
							}
						$image_html .= '</div>';
						$image_html .= '</div>';
                    }
                    
                    if ( $item['link_to'] != 'none' ) {

                        $link_key = $this->get_repeater_setting_key( 'link', 'items', $index );

                        if ( $item['link_to'] == 'file' ) {

                            $lightbox_library = $settings['lightbox_library'];
                            $lightbox_caption = $settings['lightbox_caption'];

                            $link = wp_get_attachment_url( $item['image']['id'] );

                            if ( $lightbox_library == 'fancybox' ) {
                                $this->add_render_attribute( $link_key, [
                                    'data-elementor-open-lightbox'      => 'no',
                                    'data-fancybox'                     => 'pp-showcase-preview-' . $this->get_id(),
                                ] );

                                if ( $lightbox_caption != '' ) {
                                    $caption = Module::get_image_caption( $item['image']['id'], $settings['lightbox_caption'] );

                                    $this->add_render_attribute( $link_key, [
                                        'data-caption'                  => $caption
                                    ] );
                                }

                                $this->add_render_attribute( $link_key, [
                                    'data-src' 						    => $link,
                                ] );
                            } else {
                                $this->add_render_attribute( $link_key, [
                                    'data-elementor-open-lightbox' 		=> $item['open_lightbox'],
                                    'data-elementor-lightbox-slideshow' => $this->get_id(),
                                    'data-elementor-lightbox-index' 	=> $index,
                                ] );

                                $this->add_render_attribute( $link_key, [
                                    'href' 								=> $link,
                                    'class' 							=> 'elementor-clickable',
                                ] );
                            }
                        } elseif ( $item['link_to'] == 'custom' && $item['link']['url'] != '' ) {
                            $link = $item['link']['url'];
							
							$this->add_link_attributes( $link_key, $item['link'] );
                        }

                        $this->add_render_attribute( $link_key, [
                            'class' 							=> 'pp-showcase-item-link',
                        ] );

                        $image_html = '<a ' . $this->get_render_attribute_string( $link_key ) . '>' . $image_html . '</a>';
                    }

                    echo $image_html;
                    
                } elseif ( $item['content_type'] == 'video' ) {
                    
                    $embed_params   = $this->get_embed_params( $item );
                    $video_url      = Embed::get_embed_url( $item['video_url'], $embed_params, [] );
                    $thumb_size     = $item['thumbnail_size'];
                    ?>
                    <div class="pp-video-container elementor-fit-aspect-ratio" data-src="<?php echo $video_url; ?>">
                        <div class="pp-video-player">
                            <img class="pp-video-thumb" src="<?php echo esc_url( $this->get_video_thumbnail( $item, $thumb_size ) ); ?>">
                            <?php $this->render_play_icon(); ?>
                        </div>
                    </div>
                    <?php
                    
                } elseif ( $item['content_type'] == 'section' && !empty( $item['saved_section'] ) ) {

                    echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $item['saved_section'] );

                } elseif ( $item['content_type'] == 'template' && !empty( $item['templates'] ) ) {

                    echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $item['templates'] );

                } elseif ( $item['content_type'] == 'widget' && !empty( $item['saved_widget'] ) ) {

                    echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $item['saved_widget'] );

                }
                ?>
            </div>
            <?php
        }
    }

    /**
	 * Render posts output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_preview_posts() {
        $settings = $this->get_settings();

        $i = 0;

        // Query Arguments
        $args = $this->get_posts_query_arguments();
        $posts_query = new \WP_Query( $args );

        if ( $posts_query->have_posts() ) : while ($posts_query->have_posts()) : $posts_query->the_post();
			if ( has_post_thumbnail() ) {
				?>
				<div class="pp-showcase-preview-item">
					<?php
						$image_id = get_post_thumbnail_id( get_the_ID() );
						$image_url = Group_Control_Image_Size::get_attachment_image_src( $image_id, 'image', $settings );

						$image_html = '<div class="pp-showcase-preview-image">';

						$image_html .= '<img src="' . esc_url( $image_url ) . '" alt="' . get_the_title() . '">';

						$image_html .= '</div>';
					
						$image_html .= $this->render_image_overlay();

						if ( $settings['preview_title'] == 'yes' || $settings['preview_description'] == 'yes' ) {
							$image_html .= '<div class="pp-showcase-preview-content">';
							$image_html .= '<div class="pp-showcase-preview-info">';
								if ( $settings['preview_title'] == 'yes' ) {
									$image_html .= '<div class="pp-showcase-preview-title">';
										$image_html .= get_the_title();
									$image_html .= '</div>';
								}

								if ( $settings['preview_description'] == 'yes' ) {
									$image_html .= '<div class="pp-showcase-preview-description">';
										$limit = $settings['preview_excerpt_length'];
										$image_html .= $this->get_custom_post_excerpt( $limit );
									$image_html .= '</div>';
								}
							$image_html .= '</div>';
							$image_html .= '</div>';
						}
				
						if ( $settings['posts_link_to'] != 'none' ) {

							$link_key = 'post-link-' . $i;

							if ( $settings['posts_link_to'] == 'file' ) {

								$lightbox_library = $settings['lightbox_library'];
								$lightbox_caption = $settings['lightbox_caption'];

								$link = wp_get_attachment_url( $image_id );

								if ( $lightbox_library == 'fancybox' ) {
									$this->add_render_attribute( $link_key, [
										'data-elementor-open-lightbox'      => 'no',
										'data-fancybox'                     => 'pp-showcase-preview-' . $this->get_id(),
									] );

									if ( $lightbox_caption != '' ) {
										$caption = Module::get_image_caption( $image_id, $settings['lightbox_caption'] );

										$this->add_render_attribute( $link_key, [
											'data-caption'                  => $caption
										] );
									}

									$this->add_render_attribute( $link_key, [
										'data-src' 						    => $link,
									] );
								} else {
									$this->add_render_attribute( $link_key, [
										'data-elementor-open-lightbox' 		=> $settings['posts_open_lightbox'],
										'data-elementor-lightbox-slideshow' => $this->get_id(),
										'data-elementor-lightbox-index' 	=> $i,
									] );

									$this->add_render_attribute( $link_key, [
										'href' 								=> $link,
										'class' 							=> 'elementor-clickable',
									] );
								}
							} elseif ( $settings['posts_link_to'] == 'custom' && $settings['posts_link']['url'] != '' ) {
								$link = $settings['posts_link']['url'];
								
								$this->add_link_attributes( $link_key, $settings['posts_link'] );
							} elseif ( $settings['posts_link_to'] == 'post_url' ) {
								$link = get_permalink();

								$this->add_render_attribute( $link_key, [
									'href' 								=> $link,
								] );

								if ( $settings['posts_link_external'] == 'yes' ) {
									$this->add_render_attribute( $link_key, 'target', '_blank' );
								}

								if ( $settings['posts_link_nofollow'] == 'yes' ) {
									$this->add_render_attribute( $link_key, 'rel', 'nofollow' );
								}
							}

							$this->add_render_attribute( $link_key, [
								'class' 							=> 'pp-showcase-item-link',
							] );

							$image_html = '<a ' . $this->get_render_attribute_string( $link_key ) . '>' . $image_html . '</a>';
						}
				
						echo $image_html;
					?>
				</div>
				<?php
			}
        $i++;
		endwhile;
		endif;
		wp_reset_query();
    }
    
    protected function render_navigation() {
        $settings = $this->get_settings_for_display();
			
		$this->add_render_attribute( 'navigation', 'class', 'pp-showcase-navigation-items' );
		
		if ( $settings['scrollable_nav'] == '' ) {
			$this->add_render_attribute( 'navigation', 'class', 'pp-elementor-grid' );
		}
        ?>
        <div class="pp-showcase-navigation">
			<div <?php echo $this->get_render_attribute_string( 'navigation' ); ?>>
				<?php
					if ( $settings['source'] == 'posts' ) {
						$this->render_navigation_posts();
					} else {
						$this->render_navigation_custom();
					}
                ?>
            </div>
        </div>
		<?php
    }
    
    protected function render_navigation_custom() {
        $settings = $this->get_settings_for_display();
		
		$i = 1;

		$item_wrap_key = 'navigation-item-wrap-' . $i;
		$item_key = 'navigation-item-' . $i;
		$this->add_render_attribute( $item_wrap_key, 'class', 'pp-showcase-navigation-item-wrap' );
		$this->add_render_attribute( $item_key, 'class', 'pp-showcase-navigation-item' );
		
		if ( $settings['scrollable_nav'] == '' ) {
			$this->add_render_attribute( $item_wrap_key, 'class', 'pp-grid-item-wrap' );
			$this->add_render_attribute( $item_key, 'class', 'pp-grid-item' );
		}

		foreach ( $settings['items'] as $item ) {
			?>
			<div <?php echo $this->get_render_attribute_string( $item_wrap_key ); ?>>
				<div <?php echo $this->get_render_attribute_string( $item_key ); ?>>
					<?php if ( $item['nav_icon_type'] != 'none' ) { ?>
						<div class="pp-showcase-navigation-icon-wrap">
							<?php
								if ( $item['nav_icon_type'] == 'icon' ) {
									
									if ( ! isset( $item['nav_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
										// add old default
										$item['nav_icon'] = 'fa fa-picture-o';
									}

									$has_nav_icon = ! empty( $item['nav_icon'] );

									if ( $has_nav_icon ) {
										$this->add_render_attribute( 'i', 'class', $item['nav_icon'] );
										$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
									}

									$icon_attributes = $this->get_render_attribute_string( 'nav_icon' );

									if ( ! $has_nav_icon && ! empty( $item['select_nav_icon']['value'] ) ) {
										$has_nav_icon = true;
									}
									$migrated_nav_icon = isset( $item['__fa4_migrated']['select_nav_icon'] );
									$is_new_nav_icon = ! isset( $item['nav_icon'] ) && Icons_Manager::is_migration_allowed();
									
									echo '<span class="pp-showcase-navigation-icon pp-icon">';
									if ( $is_new_nav_icon || $migrated_nav_icon ) {
										Icons_Manager::render_icon( $item['select_nav_icon'], [ 'aria-hidden' => 'true' ] );
									} elseif ( ! empty( $item['icon'] ) ) {
										?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
									}
									echo '</span>';
								} elseif ( $item['nav_icon_type'] == 'image' ) {
									printf( '<span class="pp-showcase-navigation-icon"><img src="%1$s" alt="%2$s"></span>', esc_url( $item['nav_icon_image']['url'] ), esc_attr( Control_Media::get_image_alt( $item['nav_icon_image'] ) ) );
								}
							?>
						</div>
					<?php } ?>

					<?php if ( $settings['navigation_title'] == 'yes' && $item['title'] ) { ?>
						<h4 class="pp-showcase-navigation-title">
							<?php echo $item['title']; ?>
						</h4>
					<?php } ?>

					<?php if ( $settings['navigation_description'] == 'yes' && $item['description'] ) { ?>
						<div class="pp-showcase-navigation-description">
							<?php echo $item['description']; ?>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php
			$i++;
		}
    }

    /**
	 * Render navigation posts output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_navigation_posts() {
        $settings = $this->get_settings();

        $i = 1;
		
		$item_wrap_key = 'navigation-item-wrap-' . $i;
		$item_key = 'navigation-item-' . $i;
		$this->add_render_attribute( $item_wrap_key, 'class', 'pp-showcase-navigation-item-wrap' );
		$this->add_render_attribute( $item_key, 'class', 'pp-showcase-navigation-item' );
		
		if ( $settings['scrollable_nav'] == '' ) {
			$this->add_render_attribute( $item_wrap_key, 'class', 'pp-grid-item-wrap' );
			$this->add_render_attribute( $item_key, 'class', 'pp-grid-item' );
		}

        // Query Arguments
        $args = $this->get_posts_query_arguments();
        $posts_query = new \WP_Query( $args );

        if ( $posts_query->have_posts() ) : while ($posts_query->have_posts()) : $posts_query->the_post();
			if ( has_post_thumbnail() ) {
				?>
				<div <?php echo $this->get_render_attribute_string( $item_wrap_key ); ?>>
					<div <?php echo $this->get_render_attribute_string( $item_key ); ?>>
						<?php if ( $settings['navigation_title'] == 'yes' ) { ?>
							<h4 class="pp-showcase-navigation-title">
								<?php the_title(); ?>
							</h4>
						<?php } ?>
						
						<?php if ( $settings['navigation_description'] == 'yes' ) { ?>
							<div class="pp-showcase-navigation-description">
								<?php
									$limit = $settings['navigation_excerpt_length'];
									echo $this->get_custom_post_excerpt( $limit );
								?>
							</div>
						<?php } ?>
					</div>
				</div>
				<?php
			}
        $i++;
		endwhile;
		endif;
		wp_reset_query();
    }
    
     /**
	 * Render image overlay output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_image_overlay() {
        return '<div class="pp-showcase-preview-overlay pp-media-overlay"></div>';
    }
    
    /**
	 * Render play icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_play_icon() {
        $settings = $this->get_settings_for_display();
        
        if ( $settings['play_icon_type'] == 'none' ) {
            return;
        }

        $this->add_render_attribute( 'play-icon', 'class', 'pp-video-play-icon' );
        
        if ( $settings['play_icon_type'] == 'icon' ) {
			$this->add_render_attribute( 'play-icon', 'class', 'pp-icon' );
			
			if ( ! isset( $settings['play_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
				// add old default
				$settings['play_icon'] = 'fa fa-play-circle';
			}

			$has_icon = ! empty( $settings['play_icon'] );

			if ( $has_icon ) {
				$this->add_render_attribute( 'play-icon', 'class', $settings['play_icon'] );
				$this->add_render_attribute( 'play-icon', 'aria-hidden', 'true' );
			}

			$icon_attributes = $this->get_render_attribute_string( 'play_icon' );

			if ( ! $has_icon && ! empty( $settings['select_play_icon']['value'] ) ) {
				$has_icon = true;
			}
			$migrated = isset( $settings['__fa4_migrated']['select_play_icon'] );
			$is_new = ! isset( $settings['play_icon'] ) && Icons_Manager::is_migration_allowed();
            ?>
			<span <?php echo $this->get_render_attribute_string( 'play-icon' ); ?>>
				<?php
				if ( $is_new || $migrated ) {
					Icons_Manager::render_icon( $settings['select_play_icon'], [ 'aria-hidden' => 'true' ] );
				} elseif ( ! empty( $settings['play_icon'] ) ) {
					?><i <?php echo $this->get_render_attribute_string( 'play-icon-i' ); ?>></i><?php
				}
				?>
            </span>
            <?php

        } elseif ( $settings['play_icon_type'] == 'image' ) {
            
            if ( $settings['play_icon_image']['url'] != '' ) {
                ?>
                <span <?php echo $this->get_render_attribute_string( 'play-icon' ); ?>>
                    <img src="<?php echo esc_url( $settings['play_icon_image']['url'] ); ?>">
                </span>
                <?php
            }

        }
    }
    
    protected function render_image_caption( $id, $caption_type = 'caption' ) {
        $settings = $this->get_settings_for_display();
        
        $caption = Module::get_image_caption( $id, $caption_type );
        
        if ( $caption == '' ) {
			return '';
		}
        
        ob_start();
        ?>
        <div class="pp-showcase-preview-caption">
            <?php echo $caption; ?>
        </div>
        <?php
        $html = ob_get_contents();
		ob_end_clean();
        return $html;
    }

	/**
	 * Returns Video Thumbnail.
	 *
	 * @access protected
	 */
	protected function get_video_thumbnail( $item, $thumb_size ) {
        
        $thumb_url  = '';
        $video_id   = $this->get_video_id( $item );
        
        if ( $item['video_source'] == 'youtube' ) {

            if ( $video_id != '' ) {
                $thumb_url = 'https://i.ytimg.com/vi/' . $video_id . '/' . $thumb_size . '.jpg';
            }

        } elseif ( $item['video_source'] == 'vimeo' ) {

            if ( $video_id != '' ) {
                $vimeo = unserialize( file_get_contents( "https://vimeo.com/api/v2/video/$video_id.php" ) );
                $thumb_url = $vimeo[0]['thumbnail_large'];
            }
            
        } elseif ( $item['video_source'] == 'dailymotion' ) {

            if ( $video_id != '' ) {
                $dailymotion = 'https://api.dailymotion.com/video/'.$video_id.'?fields=thumbnail_url';
                $get_thumbnail = json_decode( file_get_contents( $dailymotion ), TRUE );
                $thumb_url = $get_thumbnail['thumbnail_url'];
            }
        }
        
        return $thumb_url;

    }

	/**
	 * Returns Video ID.
	 *
	 * @access protected
	 */
	protected function get_video_id( $item ) {

		$video_id = '';
		$url      = $item['video_url'];

		if ( $item['video_source'] == 'youtube' ) {

			if ( preg_match( "#(?<=v=|v\/|vi=|vi\/|youtu.be\/)[a-zA-Z0-9_-]{11}#", $url, $matches ) ) {
				$video_id = $matches[0];
			}

		} elseif ( $item['video_source'] == 'vimeo' ) {

			$video_id = preg_replace( '/[^\/]+[^0-9]|(\/)/', '', rtrim( $url, '/' ) );

		} elseif ( $item['video_source'] == 'dailymotion' ) {
            
            if ( preg_match('/^.+dailymotion.com\/(?:video|swf\/video|embed\/video|hub|swf)\/([^&?]+)/', $url, $matches) ) {
				$video_id = $matches[1];
			}

		}

		return $video_id;

	}

	/**
	 * Get embed params.
	 *
	 * Retrieve video widget embed parameters.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @return array Video embed parameters.
	 */
	public function get_embed_params( $item ) {
		$settings = $this->get_settings_for_display();

		$params = [];

		$params_dictionary = [];

		if ( 'youtube' === $item['video_source'] ) {
            
            $params_dictionary = [
				'mute',
			];

			$params['autoplay'] = 1;

			$params['wmode'] = 'opaque';
		} elseif ( 'vimeo' === $item['video_source'] ) {
            
            $params_dictionary = [
				'mute' => 'muted',
			];

            $params['autopause'] = '0';
			$params['autoplay'] = '1';
		} elseif ( 'dailymotion' === $item['video_source'] ) {
            
            $params_dictionary = [
				'mute',
			];
            
			$params['endscreen-enable'] = '0';
			$params['autoplay'] = 1;

		}

		foreach ( $params_dictionary as $key => $param_name ) {
			$setting_name = $param_name;

			if ( is_string( $key ) ) {
				$setting_name = $key;
			}

			$setting_value = $settings[ $setting_name ] ? '1' : '0';

			$params[ $param_name ] = $setting_value;
		}

		return $params;
	}

	/**
	 *  Get Saved Widgets
	 *
	 *  @param string $type Type.
	 *  
	 *  @return string
	 */
	public function get_page_template_options( $type = '' ) {

		$page_templates = pp_get_page_templates( $type );

		$options[-1]   = __( 'Select', 'powerpack' );

		if ( count( $page_templates ) ) {
			foreach ( $page_templates as $id => $name ) {
				$options[ $id ] = $name;
			}
		} else {
			$options['no_template'] = __( 'No saved templates found!', 'powerpack' );
		}

		return $options;
	}

    protected function _content_template() {
    }
}