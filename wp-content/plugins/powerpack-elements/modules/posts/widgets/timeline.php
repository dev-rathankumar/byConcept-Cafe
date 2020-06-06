<?php
namespace PowerpackElements\Modules\Posts\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Posts_Helper;
use PowerpackElements\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Timeline Widget
 */
class Timeline extends Powerpack_Widget {
    
    /**
	 * Retrieve timeline widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return parent::get_widget_name( 'Timeline' );
    }

    /**
	 * Retrieve timeline widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
    public function get_title() {
        return parent::get_widget_title( 'Timeline' );
    }

    /**
	 * Retrieve the list of categories the timeline widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Timeline' );
    }

    /**
	 * Retrieve timeline widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
    public function get_icon() {
        return parent::get_widget_icon( 'Timeline' );
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
		return parent::get_widget_keywords( 'Timeline' );
	}

	/**
	 * Retrieve the list of scripts the timeline widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [
			'jquery-slick',
			'pp-timeline',			
			'powerpack-frontend'
		];
	}

    /**
	 * Register timeline widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
    protected function _register_controls() {

		/* Content Tab: Settings */
		$this->register_content_settings_controls();

		/* Content Tab: Timeline */
		$this->register_content_timeline_items_controls();

		/* Content Tab: Query */
		$this->register_content_query_controls();

		/* Content Tab: Posts */
		$this->register_content_posts_controls();

		/* Content Tab: Help Docs */
		$this->register_content_help_docs();

		/* Style Tab: Layout */
		$this->register_style_layout_controls();

		/* Style Tab: Cards */
		$this->register_style_cards_controls();

		/* Style Tab: Marker */
		$this->register_style_marker_controls();

		/* Style Tab: Dates */
		$this->register_style_dates_controls();

		/* Style Tab: Connector */
		$this->register_style_connector_controls();

		/* Style Tab: Arrows */
		$this->register_style_arrows_controls();

		/* Style Tab: Button */
		$this->register_style_button_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	Content Tab
	/*-----------------------------------------------------------------------------------*/
	
	/**
	 * Content Tab: Settings
	 */
	protected function register_content_settings_controls() {
        $this->start_controls_section(
            'section_post_settings',
            [
                'label'                 => __( 'Settings', 'powerpack' ),
            ]
        );

        $this->add_control(
            'layout',
            [
                'label'                 => __( 'Layout', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => [
                   'horizontal' => __( 'Horizontal', 'powerpack' ),
                   'vertical'   => __( 'Vertical', 'powerpack' ),
                ],
                'default'               => 'vertical',
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
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                ],
                'frontend_available'    => true,
                'condition'             => [
                    'layout'    => 'horizontal'
                ]
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
        
        $this->add_control(
            'dates',
            [
                'label'                 => __( 'Date', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'return_value'          => 'yes',
                'frontend_available'    => true,
            ]
        );

        $this->add_control(
            'date_format',
			[
				'label'                 => __( 'Date Format', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => [
                   ''			=> __( 'Published Date', 'powerpack' ),
                   'modified'	=> __( 'Last Modified Date', 'powerpack' ),
                   'custom'		=> __( 'Custom Format', 'powerpack' ),
                   'key'		=> __( 'Custom Meta Key', 'powerpack' ),
                ],
                'default'               => '',
                'condition'             => [
                    'source'	=> 'posts',
                    'dates'		=> 'yes',
                ]
            ]
        );
		
		$this->add_control(
			'timeline_post_date_format',
			[
				'label'             => __( 'Custom Format', 'powerpack' ),
				'description'		=> sprintf( __( 'Refer to PHP date formats <a href="%s">here</a>', 'powerpack' ), 'https://wordpress.org/support/article/formatting-date-and-time/' ),
				'type'              => Controls_Manager::TEXT,
				'label_block'       => false,
				'default'           => '',
				'dynamic'			=> [
					'active' => true,
				],
                'condition'             => [
                    'source'		=> 'posts',
                    'dates'			=> 'yes',
                    'date_format'	=> 'custom',
                ]
			]
		);
		
		$this->add_control(
			'timeline_post_date_key',
			[
				'label'             => __( 'Custom Meta Key', 'powerpack' ),
				'description'		=> __( 'Display the post date stored in custom meta key.', 'powerpack' ),
				'type'              => Controls_Manager::TEXT,
				'label_block'       => false,
				'default'           => '',
				'dynamic'			=> [
					'active' => true,
				],
                'condition'             => [
                    'source'		=> 'posts',
                    'dates'			=> 'yes',
                    'date_format'	=> 'key',
                ]
			]
		);
        
        $this->add_control(
            'card_arrow',
            [
                'label'                 => __( 'Card Arrow', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'return_value'          => 'yes',
                'frontend_available'    => true,
            ]
        );
        
        $this->add_control(
            'animate_cards',
            [
                'label'                 => __( 'Animate Cards', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'return_value'          => 'yes',
                'frontend_available'    => true,
                'condition'             => [
                    'layout'    => 'vertical'
                ]
            ]
        );
        
        $this->add_control(
            'arrows',
            [
                'label'                 => __( 'Arrows', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'return_value'          => 'yes',
                'frontend_available'    => true,
                'condition'             => [
                    'layout'    => 'horizontal'
                ]
            ]
        );
        
        $this->add_control(
            'infinite_loop',
            [
                'label'                 => __( 'Infinite Loop', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'return_value'          => 'yes',
                'frontend_available'    => true,
                'condition'             => [
                    'layout'    => 'horizontal'
                ]
            ]
        );
        
        $this->add_control(
            'center_mode',
            [
                'label'                 => __( 'Center Mode', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'return_value'          => 'yes',
                'frontend_available'    => true,
                'condition'             => [
                    'layout'    	=> 'horizontal',
                    'infinite_loop'	=> 'yes'
                ]
            ]
        );
        
        $this->add_control(
            'autoplay',
            [
                'label'                 => __( 'Autoplay', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'return_value'          => 'yes',
                'frontend_available'    => true,
                'condition'             => [
                    'layout'    => 'horizontal'
                ]
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
                    'layout'    => 'horizontal',
                    'autoplay'  => 'yes',
                ],
            ]
        );

        $this->end_controls_section();
	}

	/**
	 * Content Tab: Timeline
	 */
	protected function register_content_timeline_items_controls() {
        $this->start_controls_section(
            'section_timeline_items',
            [
                'label'                 => __( 'Timeline', 'powerpack' ),
                'condition'             => [
                    'source'    => 'custom'
                ]
            ]
        );
        
        $repeater = new Repeater();
        
        $repeater->start_controls_tabs( 'timeline_items_tabs' );

        $repeater->start_controls_tab( 'tab_timeline_items_content', [ 'label' => __( 'Content', 'powerpack' ) ] );
        
            $repeater->add_control(
                'timeline_item_date',
                [
                    'label'             => __( 'Date', 'powerpack' ),
                    'type'              => Controls_Manager::TEXT,
                    'label_block'       => false,
                    'default'           => __( '1 June 2018', 'powerpack' ),
					'dynamic'     => [
						'active' => true,
					],
                ]
            );
        
            $repeater->add_control(
                'timeline_item_title',
                [
                    'label'             => __( 'Title', 'powerpack' ),
                    'type'              => Controls_Manager::TEXT,
                    'label_block'       => false,
                    'default'           => '',
					'dynamic'     => [
						'active' => true,
					],
                ]
            );
        
            $repeater->add_control(
                'timeline_item_content',
                [
                    'label'             => __( 'Content', 'powerpack' ),
                    'type'              => Controls_Manager::WYSIWYG,
                    'default'           => '',
					'dynamic'     => [
						'active' => true,
					],
                ]
            );

            $repeater->add_control(
                'timeline_item_link',
                [
                    'label'                 => __( 'Link', 'powerpack' ),
                    'type'                  => Controls_Manager::URL,
                    'dynamic'               => [
                        'active'        => true,
                        'categories'    => [
                            TagsModule::POST_META_CATEGORY,
                            TagsModule::URL_CATEGORY
                        ],
                    ],
                    'placeholder'           => 'https://www.your-link.com',
                    'default'               => [
                        'url' => '',
                    ],
					'dynamic'     => [
						'active' => true,
					],
                ]
            );
        
        $repeater->end_controls_tab();

        $repeater->start_controls_tab( 'tab_timeline_items_image', [ 'label' => __( 'Image', 'powerpack' ) ] );
        
        $repeater->add_control(
            'card_image',
            [
                'label'                 => __( 'Show Image', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
            ]
        );
        
        $repeater->add_control(
			'image',
			[
				'label'                 => __( 'Choose Image', 'powerpack' ),
				'type'                  => \Elementor\Controls_Manager::MEDIA,
				'default'               => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'dynamic'     => [
					'active' => true,
				],
                'conditions'            => [
                    'terms' => [
                        [
                            'name'      => 'card_image',
                            'operator'  => '==',
                            'value'     => 'yes',
                        ],
                    ],
                ],
			]
		);
        
        $repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'image',
                'exclude'               => [ 'custom' ],
				'include'               => [],
				'default'               => 'large',
                'conditions'            => [
                    'terms' => [
                        [
                            'name'      => 'card_image',
                            'operator'  => '==',
                            'value'     => 'yes',
                        ],
                    ],
                ],
			]
		);
        
        $repeater->end_controls_tab();

        $repeater->start_controls_tab( 'tab_timeline_items_style', [ 'label' => __( 'Style', 'powerpack' ) ] );
        
        $repeater->add_control(
            'custom_style',
            [
                'label'                 => __( 'Custom Style', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
            ]
        );

        $repeater->add_control(
            'single_marker_type',
			[
				'label'                 => esc_html__( 'Marker Type', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => [
                   'global'		=> __( 'Global', 'powerpack' ),
                   'none'		=> __( 'None', 'powerpack' ),
                   'icon'		=> __( 'Icon', 'powerpack' ),
                   'image'		=> __( 'Image', 'powerpack' ),
                   'text'		=> __( 'Text', 'powerpack' ),
                ],
                'default'               => 'global',
                'condition'             => [
                    'custom_style'  => 'yes',
                ]
            ]
        );
		
		$repeater->add_control(
			'marker_icon_single',
			[
				'label'					=> __( 'Choose Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'fa4compatibility'		=> 'single_marker_icon',
				'default'				=> [
					'value'		=> 'fas fa-calendar',
					'library'	=> 'fa-solid',
				],
                'condition'             => [
                    'custom_style'          => 'yes',
                    'single_marker_type'    => 'icon'
                ],
			]
		);
        
        $repeater->add_control(
			'single_marker_icon_image',
			[
				'label'                 => __( 'Choose Image', 'powerpack' ),
				'type'                  => \Elementor\Controls_Manager::MEDIA,
				'default'               => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
                'condition'             => [
                    'custom_style'          => 'yes',
                    'single_marker_type'	=> 'image'
                ]
			]
		);
        
        $repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'single_marker_icon_image',
				'include'               => [],
				'default'               => 'large',
                'condition'             => [
                    'custom_style'          => 'yes',
                    'single_marker_type'	=> 'image'
                ]
			]
		);

        $repeater->add_control(
            'single_marker_text',
            [
                'label'                 => __( 'Enter Marker Text', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'default'               => '',
                'condition'             => [
                    'custom_style'          => 'yes',
                    'single_marker_type'    => 'text'
                ]
            ]
        );

        $repeater->add_control(
            'single_marker_color',
            [
                'label'                 => __( 'Marker Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .pp-timeline-marker, {{WRAPPER}} .slick-center .pp-timeline-marker' => 'color: {{VALUE}}',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .pp-timeline-marker svg, {{WRAPPER}} .slick-center .pp-timeline-marker svg' => 'fill: {{VALUE}}',
                ],
                'conditions'            => [
                    'terms' => [
                        [
                            'name'      => 'custom_style',
                            'operator'  => '==',
                            'value'     => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'single_marker_bg_color',
            [
                'label'                 => __( 'Marker Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .pp-timeline-marker, {{WRAPPER}} .slick-center .pp-timeline-marker' => 'background-color: {{VALUE}}',
                ],
                'conditions'            => [
                    'terms' => [
                        [
                            'name'      => 'custom_style',
                            'operator'  => '==',
                            'value'     => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'single_card_background_color',
            [
                'label'                 => __( 'Card Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline {{CURRENT_ITEM}} .pp-timeline-card' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .pp-timeline {{CURRENT_ITEM}} .pp-timeline-arrow' => 'color: {{VALUE}}',
                ],
                'conditions'            => [
                    'terms' => [
                        [
                            'name'      => 'custom_style',
                            'operator'  => '==',
                            'value'     => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'single_title_color',
            [
                'label'                 => __( 'Title Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .pp-timeline-card-title' => 'color: {{VALUE}}',
                ],
                'conditions'            => [
                    'terms' => [
                        [
                            'name'      => 'custom_style',
                            'operator'  => '==',
                            'value'     => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $repeater->add_control(
            'single_content_color',
            [
                'label'                 => __( 'Content Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .pp-timeline-card-content' => 'color: {{VALUE}}',
                ],
                'conditions'            => [
                    'terms' => [
                        [
                            'name'      => 'custom_style',
                            'operator'  => '==',
                            'value'     => 'yes',
                        ],
                    ],
                ],
            ]
        );
        
        $repeater->end_controls_tab();

        $repeater->end_controls_tabs();

        $this->add_control(
            'items',
            [
                'label'                 => '',
                'type'                  => Controls_Manager::REPEATER,
                'default'               => [
                    [
                        'timeline_item_date'    => __( '1 May 2018', 'powerpack' ),
                        'timeline_item_title'   => __( 'Timeline Item 1', 'powerpack' ),
                        'timeline_item_content' => __( 'I am timeline item content. Click here to edit this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'powerpack' ),
                    ],
                    [
                        'timeline_item_date'    => __( '1 June 2018', 'powerpack' ),
                        'timeline_item_title'   => __( 'Timeline Item 2', 'powerpack' ),
                        'timeline_item_content' => __( 'I am timeline item content. Click here to edit this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'powerpack' ),
                    ],
                    [
                        'timeline_item_date'    => __( '1 July 2018', 'powerpack' ),
                        'timeline_item_title'   => __( 'Timeline Item 3', 'powerpack' ),
                        'timeline_item_content' => __( 'I am timeline item content. Click here to edit this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'powerpack' ),
                    ],
                    [
                        'timeline_item_date'    => __( '1 August 2018', 'powerpack' ),
                        'timeline_item_title'   => __( 'Timeline Item 4', 'powerpack' ),
                        'timeline_item_content' => __( 'I am timeline item content. Click here to edit this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'powerpack' ),
                    ],
                ],
                'fields'                => array_values( $repeater->get_controls() ),
                'title_field'           => '{{{ timeline_item_date }}}',
                'condition'             => [
                    'source'    => 'custom'
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
                'condition'             => [
                    'source'    => 'posts'
                ]
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
                    				'source'    => 'posts',
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
                    	// 			'source'    => 'posts',
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
									'source'    => 'posts',
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
                'condition'             => [
                    'source'    => 'posts'
                ]
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
        //         'condition'             => [
        //             'source'    => 'posts'
        //         ]
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
					'source'    => 'posts',
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
                    	'source'    => 'posts',
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
            //         	'source'    => 'posts',
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
						'source'    => 'posts',
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
                'condition'             => [
                    'source'    => 'posts'
                ]
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
                    'source'    => 'posts',
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
                    'source'    => 'posts',
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
                'condition'             => [
                    'source'    => 'posts'
                ]
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
                'condition'             => [
                    'source'    => 'posts'
                ]
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
                'condition'             => [
                    'source'    => 'posts'
                ]
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
                    'source'    => 'posts',
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
                'condition'             => [
                    'source'    => 'posts'
                ]
            ]
        );

        $this->end_controls_section();
	}

	/**
	 * Content Tab: Posts
	 */
	protected function register_content_posts_controls() {
        $this->start_controls_section(
            'section_posts',
            [
                'label'                 => __( 'Posts', 'powerpack' ),
                'condition'             => [
                    'source'    => 'posts'
                ]
            ]
        );
        
        $this->add_control(
            'post_title',
            [
                'label'                 => __( 'Post Title', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'show',
                'label_on'              => __( 'Show', 'powerpack' ),
                'label_off'             => __( 'Hide', 'powerpack' ),
                'return_value'          => 'show',
                'condition'             => [
                    'source'    => 'posts'
                ]
            ]
        );
        
        $this->add_control(
            'post_image',
            [
                'label'                 => __( 'Post Image', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'show',
                'label_on'              => __( 'Show', 'powerpack' ),
                'label_off'             => __( 'Hide', 'powerpack' ),
                'return_value'          => 'show',
                'condition'             => [
                    'source'    => 'posts'
                ]
            ]
        );
		
        $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'image_size',
				'label'                 => __( 'Image Size', 'powerpack' ),
				'default'               => 'medium_large',
                'condition'             => [
                    'source'        => 'posts',
                    'post_image'    => 'show'
                ]
			]
		);
        
        $this->add_control(
            'post_content',
            [
                'label'                 => __( 'Post Content', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'show',
                'label_on'              => __( 'Show', 'powerpack' ),
                'label_off'             => __( 'Hide', 'powerpack' ),
                'return_value'          => 'show',
                'condition'             => [
                    'source'    => 'posts'
                ]
            ]
        );

        $this->add_control(
            'content_type',
            [
                'label'                 => __( 'Content Type', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => [
                   'excerpt'            => __( 'Excerpt', 'powerpack' ),
                   'limited-content'    => __( 'Limited Content', 'powerpack' ),
                ],
                'default'               => 'excerpt',
                'condition'             => [
                    'source'        => 'posts',
                    'post_content'  => 'show'
                ]
            ]
        );
        
        $this->add_control(
            'content_length',
            [
                'label'                 => __( 'Content Limit', 'powerpack' ),
                'title'                 => __( 'Words', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => 40,
                'min'                   => 0,
                'step'                  => 1,
                'condition'             => [
                    'source'        => 'posts',
                    'post_content'  => 'show',
                    'content_type'  => 'limited-content'
                ]
            ]
        );

        $this->add_control(
            'link_type',
            [
                'label'                 => __( 'Link Type', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => [
                   ''           => __( 'None', 'powerpack' ),
                   'title'      => __( 'Title', 'powerpack' ),
                   'button'     => __( 'Button', 'powerpack' ),
                   'card'       => __( 'Card', 'powerpack' ),
                ],
                'default'               => 'title',
                'condition'             => [
                    'source'        => 'posts',
                ]
            ]
        );
        
        $this->add_control(
            'button_text',
            [
                'label'                 => __( 'Button Text', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'label_block'           => false,
                'default'               => __( 'Read More', 'powerpack' ),
                'condition'             => [
                    'source'        => 'posts',
                    'link_type'     => 'button',
                ]
            ]
        );
        
        $this->add_control(
            'post_meta',
            [
                'label'                 => __( 'Post Meta', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Show', 'powerpack' ),
                'label_off'             => __( 'Hide', 'powerpack' ),
                'return_value'          => 'show',
                'condition'             => [
                    'source'    => 'posts'
                ]
            ]
        );

        $this->add_control(
            'meta_items_divider',
            [
                'label'             => __( 'Meta Items Divider', 'powerpack' ),
                'type'              => Controls_Manager::TEXT,
                'default'           => '-',
				'selectors'         => [
					'{{WRAPPER}} .pp-timeline-meta > span:not(:last-child):after' => 'content: "{{UNIT}}";',
				],
                'condition'         => [
                    'source'    => 'posts',
                    'post_meta' => 'show'
                ],
            ]
        );
        
        $this->add_control(
            'post_author',
            [
                'label'                 => __( 'Post Author', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'show',
                'label_on'              => __( 'Show', 'powerpack' ),
                'label_off'             => __( 'Hide', 'powerpack' ),
                'return_value'          => 'show',
                'condition'             => [
                    'source'    => 'posts',
                    'post_meta' => 'show'
                ]
            ]
        );
        
        $this->add_control(
            'post_category',
            [
                'label'                 => __( 'Post Terms', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Show', 'powerpack' ),
                'label_off'             => __( 'Hide', 'powerpack' ),
                'return_value'          => 'show',
                'condition'             => [
                    'source'    => 'posts',
                    'post_meta' => 'show'
                ]
            ]
        );

		$post_types = PP_Posts_Helper::get_post_types();

		foreach ( $post_types as $post_type_slug => $post_type_label ) {

			$taxonomy = PP_Posts_Helper::get_post_taxonomies( $post_type_slug );

			if ( ! empty( $taxonomy ) ) {

				$related_tax = [];

				// Get all taxonomy values under the taxonomy.
				foreach ( $taxonomy as $index => $tax ) {

					$terms = get_terms( $index );

					$related_tax[ $index ] = $tax->label;
				}

				// Add control for all taxonomies.
				$this->add_control(
					'tax_badge_' . $post_type_slug,
					[
						'label'     	=> __( 'Select Taxonomy', 'powerpack' ),
						'type'      	=> Controls_Manager::SELECT2,
						'label_block'	=> true,
						'options'   	=> $related_tax,
						'multiple'  	=> true,
						'default'   	=> array_keys( $related_tax )[0],
						'condition' 	=> [
                    		'source'    	=> 'posts',
							'post_type' 	=> $post_type_slug,
                    		'post_meta' 	=> 'show',
                    		'post_category' => 'show',
						],
					]
				);
			}
		}

        $this->end_controls_section();
	}

	/**
	 * Content Tab: Help Docs
	 *
	 * @since 1.4.8
	 * @access protected
	 */
	protected function register_content_help_docs() {

		$help_docs = PP_Config::get_widget_help_links('Timeline');

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
                'label'                 => __( 'Layout', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'direction',
            [
                'label'                 => __( 'Direction', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'toggle'                => true,
                'default'               => 'center',
                'tablet_default'        => 'left',
                'mobile_default'        => 'left',
                'options'               => [
                    'left' 		=> [
                        'title' => __( 'Left', 'powerpack' ),
                        'icon' 	=> 'eicon-h-align-left',
                    ],
                    'center' 	=> [
                        'title' => __( 'Center', 'powerpack' ),
                        'icon' 	=> 'eicon-h-align-center',
                    ],
                    'right' 	=> [
                        'title' => __( 'Right', 'powerpack' ),
                        'icon' 	=> 'eicon-h-align-right',
                    ],
                ],
                'condition'             => [
                    'layout'    => 'vertical',
                ]
            ]
        );

		$this->add_control(
			'cards_arrows_alignment',
			[
				'label'                 => __( 'Arrows Alignment', 'powerpack' ),
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
				'default'               => 'top',
                'condition'             => [
                    'layout'    => 'vertical'
                ]
			]
		);

        $this->add_responsive_control(
            'items_spacing',
            [
                'label'                 => __( 'Items Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size' 	    => '',
                ],
                'range' 		=> [
                    'px' 		=> [
                        'min' 	=> 0,
                        'max' 	=> 100,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-vertical .pp-timeline-item:not(:last-child)' => 'margin-bottom: {{SIZE}}px;',
                    '{{WRAPPER}} .pp-timeline-horizontal .pp-timeline-item' => 'padding-left: {{SIZE}}px; padding-right: {{SIZE}}px;',
                    '{{WRAPPER}} .pp-timeline-horizontal .slick-list'       => 'margin-left: -{{SIZE}}px; margin-right: -{{SIZE}}px;',
                ],
            ]
        );

        $this->end_controls_section();
	}

	/**
	 * Style Tab: Cards
	 */
	protected function register_style_cards_controls() {
        $this->start_controls_section(
            'section_cards_style',
            [
                'label'                 => __( 'Cards', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
			'cards_padding',
			[
				'label'                 => __( 'Cards Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-timeline .pp-timeline-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'                 => __( 'Content Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-timeline .pp-timeline-card-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'cards_text_align',
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
                'default'               => 'left',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-card' => 'text-align: {{VALUE}};',
                ],
            ]
        );
        
        $this->start_controls_tabs( 'card_tabs' );
        
        $this->start_controls_tab( 'tab_card_normal', [ 'label' => __( 'Normal', 'powerpack' ) ] );

        $this->add_control(
            'cards_bg',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline .pp-timeline-card' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .pp-timeline .pp-timeline-arrow' => 'color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'cards_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-timeline .pp-timeline-card',
			]
		);

		$this->add_responsive_control(
			'cards_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-timeline .pp-timeline-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->add_control(
			'cards_box_shadow',
			[
				'label'                 => __( 'Box Shadow', 'powerpack' ),
				'type'                  => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'label_off'             => __( 'Default', 'powerpack' ),
				'label_on'              => __( 'Custom', 'powerpack' ),
				'return_value'          => 'yes',
			]
		);

		$this->start_popover();

			$this->add_control(
				'cards_box_shadow_color',
				[
					'label'                => __( 'Color', 'powerpack' ),
					'type'                 => Controls_Manager::COLOR,
					'default'              => 'rgba(0,0,0,0.5)',
					'selectors'            => [
						'{{WRAPPER}} .pp-timeline-card-wrapper' => 'filter: drop-shadow({{cards_box_shadow_horizontal.SIZE}}px {{cards_box_shadow_vertical.SIZE}}px {{cards_box_shadow_blur.SIZE}}px {{VALUE}}); -webkit-filter: drop-shadow({{cards_box_shadow_horizontal.SIZE}}px {{cards_box_shadow_vertical.SIZE}}px {{cards_box_shadow_blur.SIZE}}px {{VALUE}});',
					],
					'condition'            => [
						'cards_box_shadow' => 'yes',
					],
				]
			);

			$this->add_control(
				'cards_box_shadow_horizontal',
				[
					'label'                => __( 'Horizontal', 'powerpack' ),
					'type'                 => Controls_Manager::SLIDER,
					'default'              => [
						'size' => 0,
						'unit' => 'px',
					],
					'range'                => [
						'px' => [
							'min'  => -100,
							'max'  => 100,
							'step' => 1,
						],
					],
					'condition'            => [
						'cards_box_shadow' => 'yes',
					],
				]
			);

			$this->add_control(
				'cards_box_shadow_vertical',
				[
					'label'                => __( 'Vertical', 'powerpack' ),
					'type'                 => Controls_Manager::SLIDER,
					'default'              => [
						'size' => 0,
						'unit' => 'px',
					],
					'range'                => [
						'px' => [
							'min'  => -100,
							'max'  => 100,
							'step' => 1,
						],
					],
					'condition'            => [
						'cards_box_shadow' => 'yes',
					],
				]
			);

			$this->add_control(
				'cards_box_shadow_blur',
				[
					'label'                => __( 'Blur', 'powerpack' ),
					'type'                 => Controls_Manager::SLIDER,
					'default'              => [
						'size' => 4,
						'unit' => 'px',
					],
					'range'                => [
						'px' => [
							'min'  => 1,
							'max'  => 10,
							'step' => 1,
						],
					],
					'condition'            => [
						'cards_box_shadow' => 'yes',
					],
				]
			);

		$this->end_popover();

		$this->add_control(
			'heading_image',
			[
				'label'                 => __( 'Image', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);
        
        $this->add_responsive_control(
            'image_margin_bottom',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size' 	    => 20,
                ],
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-card-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$this->add_control(
			'heading_title',
			[
				'label'                 => __( 'Title', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

        $this->add_control(
            'title_bg',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline .pp-timeline-card-title-wrap' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'title_text_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-card-title' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'title_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-timeline-card-title',
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'title_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-timeline .pp-timeline-card-title-wrap',
			]
		);
        
        $this->add_responsive_control(
            'title_margin_bottom',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-card-title-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$this->add_responsive_control(
			'title_padding',
			[
				'label'                 => __( 'Title Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-timeline .pp-timeline-card-title-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_content',
			[
				'label'                 => __( 'Content', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

        $this->add_control(
            'card_text_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline .pp-timeline-card' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'card_text_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-timeline-card',
            ]
        );

		$this->add_control(
			'meta_content',
			[
				'label'                 => __( 'Post Meta', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
                'condition'             => [
                    'source'    => 'posts',
                    'post_meta' => 'show'
                ]
			]
		);

        $this->add_control(
            'meta_text_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-meta' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'source'    => 'posts',
                    'post_meta' => 'show'
                ]
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'meta_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-timeline-meta',
                'condition'             => [
                    'source'    => 'posts',
                    'post_meta' => 'show'
                ]
            ]
        );
        
        $this->add_responsive_control(
            'meta_items_gap',
            [
                'label'                 => __( 'Meta Items Gap', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size' 	    => 10,
                ],
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 60,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-meta > span:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .pp-timeline-meta > span:not(:last-child):after' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
                'condition'         => [
                    'source'    => 'posts',
                    'post_meta' => 'show'
                ],
            ]
        );
        
        $this->add_responsive_control(
            'meta_margin_bottom',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size' 	    => 20,
                ],
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 60,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-meta' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition'         => [
                    'source'    => 'posts',
                    'post_meta' => 'show'
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->start_controls_tab( 'tab_card_hover', [ 'label' => __( 'Hover', 'powerpack' ) ] );

        $this->add_control(
            'card_bg_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline .pp-timeline-item:hover .pp-timeline-card' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .pp-timeline .pp-timeline-item:hover .pp-timeline-arrow' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'title_bg_hover',
            [
                'label'                 => __( 'Title Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline .pp-timeline-item:hover .pp-timeline-card-title-wrap' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'card_title_color_hover',
            [
                'label'                 => __( 'Title Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline .pp-timeline-item:hover .pp-timeline-card-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'card_title_border_color_hover',
            [
                'label'                 => __( 'Title Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline .pp-timeline-item:hover .pp-timeline-card-title-wrap' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'card_color_hover',
            [
                'label'                 => __( 'Content Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline .pp-timeline-item:hover .pp-timeline-card' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'card_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline .pp-timeline-item:hover .pp-timeline-card' => 'border-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->start_controls_tab( 'tab_card_focused', [ 'label' => __( 'Focused', 'powerpack' ) ] );

        $this->add_control(
            'card_bg_focused',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline .pp-timeline-item-active .pp-timeline-card, {{WRAPPER}} .pp-timeline .slick-current .pp-timeline-card' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .pp-timeline .pp-timeline-item-active .pp-timeline-arrow, {{WRAPPER}} .pp-timeline .slick-current .pp-timeline-arrow' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'title_bg_focused',
            [
                'label'                 => __( 'Title Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline .pp-timeline-item-active .pp-timeline-card-title-wrap, {{WRAPPER}} .pp-timeline .slick-current .pp-timeline-card-title-wrap' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'card_title_color_focused',
            [
                'label'                 => __( 'Title Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline .pp-timeline-item-active .pp-timeline-card-title, {{WRAPPER}} .pp-timeline .slick-current .pp-timeline-card-title' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'card_title_border_color_focused',
            [
                'label'                 => __( 'Title Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline .pp-timeline-item-active .pp-timeline-card-title-wrap, {{WRAPPER}} .pp-timeline .slick-current .pp-timeline-card-title-wrap' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'card_color_focused',
            [
                'label'                 => __( 'Content Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline .pp-timeline-item-active .pp-timeline-card, {{WRAPPER}} .pp-timeline .slick-current .pp-timeline-card' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'card_border_color_focused',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline .pp-timeline-item-active .pp-timeline-card, {{WRAPPER}} .pp-timeline .slick-current .pp-timeline-card' => 'border-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
	}

	/**
	 * Style Tab: Marker
	 */
	protected function register_style_marker_controls() {
        $this->start_controls_section(
            'section_marker_style',
            [
                'label'                 => __( 'Marker', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
			'marker_type',
			[
				'label'                 => esc_html__( 'Type', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'toggle'                => false,
				'options'               => [
                    'none' => [
                        'title' => esc_html__( 'None', 'powerpack' ),
                        'icon'  => 'fa fa-ban',
                    ],
					'icon' => [
						'title' => esc_html__( 'Icon', 'powerpack' ),
						'icon' => 'fa fa-star',
					],
					'image' => [
						'title' => esc_html__( 'Icon Image', 'powerpack' ),
						'icon' => 'fa fa-image',
					],
					'number' => [
						'title' => esc_html__( 'Number', 'powerpack' ),
						'icon' => 'fa fa-sort-numeric-asc',
					],
					'letter' => [
						'title' => esc_html__( 'Letter', 'powerpack' ),
						'icon' => 'fa fa-sort-alpha-asc',
					],
				],
				'default'               => 'icon',
			]
		);
		
		$this->add_control(
			'select_marker_icon',
			[
				'label'					=> __( 'Choose Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'fa4compatibility'		=> 'marker_icon',
				'default'				=> [
					'value'		=> 'fas fa-calendar',
					'library'	=> 'fa-solid',
				],
                'condition'             => [
                    'marker_type'   => 'icon'
                ],
			]
		);
        
        $this->add_control(
			'icon_image',
			[
				'label'                 => __( 'Choose Icon Image', 'powerpack' ),
				'type'                  => \Elementor\Controls_Manager::MEDIA,
				'default'               => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
                'condition'             => [
                    'marker_type'   => 'image'
                ]
			]
		);
        
        $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'icon_image',
				'include'               => [],
				'default'               => 'large',
                'condition'             => [
                    'marker_type'   => 'image'
                ]
			]
		);
        
        $this->add_responsive_control(
            'marker_size',
            [
                'label'                 => __( 'Marker Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 5,
                        'max'   => 150,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', 'em' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-marker' => 'font-size: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .pp-timeline-marker img' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'marker_type!'  => 'none'
                ]
            ]
        );
        
        $this->add_responsive_control(
            'marker_box_size',
            [
                'label'                 => __( 'Marker Box Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 10,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-marker' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .pp-timeline-connector-wrap' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .pp-timeline-navigation:before, {{WRAPPER}} .pp-timeline-navigation .pp-slider-arrow' => 'bottom: calc( {{SIZE}}{{UNIT}}/2 );',
                ],
            ]
        );
        
        $this->start_controls_tabs( 'marker_tabs' );
        
        $this->start_controls_tab( 'tab_marker_normal', [ 'label' => __( 'Normal', 'powerpack' ) ] );

        $this->add_control(
            'marker_color',
            [
                'label'                 => __( 'Marker Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '#ffffff',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-marker' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-timeline-marker svg' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'marker_bg',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-marker' => 'background-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'marker_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-timeline-marker',
			]
		);

		$this->add_responsive_control(
			'marker_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-timeline-marker' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'marker_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-timeline-marker',
			]
		);
        
        $this->end_controls_tab();
        
        $this->start_controls_tab( 'tab_marker_hover', [ 'label' => __( 'Hover', 'powerpack' ) ] );

        $this->add_control(
            'marker_color_hover',
            [
                'label'                 => __( 'Marker Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-marker-wrapper:hover .pp-timeline-marker' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-timeline-marker-wrapper:hover .pp-timeline-marker svg' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'marker_bg_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-marker-wrapper:hover .pp-timeline-marker' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'marker_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-marker-wrapper:hover .pp-timeline-marker' => 'border-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->start_controls_tab( 'tab_marker_focused', [ 'label' => __( 'Focused', 'powerpack' ) ] );

        $this->add_control(
            'marker_color_focused',
            [
                'label'                 => __( 'Marker Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-item-active .pp-timeline-marker, {{WRAPPER}} .slick-current .pp-timeline-marker' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-timeline-item-active .pp-timeline-marker svg, {{WRAPPER}} .slick-current .pp-timeline-marker svg' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'marker_bg_focused',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-item-active .pp-timeline-marker, {{WRAPPER}} .slick-current .pp-timeline-marker' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'marker_border_color_focused',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-item-active .pp-timeline-marker, {{WRAPPER}} .slick-current .pp-timeline-marker' => 'border-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
	}

	/**
	 * Style Tab: Dates
	 */
	protected function register_style_dates_controls() {
        $this->start_controls_section(
            'section_dates_style',
            [
                'label'                 => __( 'Dates', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'dates'		=> 'yes'
                ]
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'dates_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-timeline-card-date',
                'condition'             => [
                    'dates'		=> 'yes'
                ]
            ]
        );
        
        $this->start_controls_tabs( 'dates_tabs' );
        
        $this->start_controls_tab( 'tab_dates_normal', [ 'label' => __( 'Normal', 'powerpack' ) ] );

        $this->add_control(
            'dates_bg',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-card-date' => 'background-color: {{VALUE}}',
                ],
                'condition'             => [
                    'dates'		=> 'yes'
                ]
            ]
        );

        $this->add_control(
            'dates_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-card-date' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'dates'		=> 'yes'
                ]
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'dates_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-timeline-card-date',
                'condition'             => [
                    'dates'		=> 'yes'
                ]
			]
		);

		$this->add_responsive_control(
			'dates_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-timeline-card-date' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'dates'		=> 'yes'
                ]
			]
		);

		$this->add_responsive_control(
			'dates_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-timeline-card-date' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'dates'		=> 'yes'
                ]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'dates_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-timeline-card-date',
                'condition'             => [
                    'dates'		=> 'yes'
                ]
			]
		);
        
        $this->end_controls_tab();
        
        $this->start_controls_tab( 'tab_dates_hover', [ 'label' => __( 'Hover', 'powerpack' ) ] );

        $this->add_control(
            'dates_bg_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-card-date:hover' => 'background-color: {{VALUE}}',
                ],
                'condition'             => [
                    'dates'		=> 'yes'
                ]
            ]
        );

        $this->add_control(
            'dates_color_hover',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-card-date:hover' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'dates'		=> 'yes'
                ]
            ]
        );

        $this->add_control(
            'dates_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-card-date:hover' => 'border-color: {{VALUE}}',
                ],
                'condition'             => [
                    'dates'		=> 'yes'
                ]
            ]
        );
        
        $this->end_controls_tab();
        
        $this->start_controls_tab( 'tab_dates_focused', [ 'label' => __( 'Focused', 'powerpack' ) ] );

        $this->add_control(
            'dates_bg_focused',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-item-active .pp-timeline-card-date' => 'background-color: {{VALUE}}',
                ],
                'condition'             => [
                    'dates'		=> 'yes'
                ]
            ]
        );

        $this->add_control(
            'dates_color_focused',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-item-active .pp-timeline-card-date' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'dates'		=> 'yes'
                ]
            ]
        );

        $this->add_control(
            'dates_border_color_focused',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-item-active .pp-timeline-card-date' => 'border-color: {{VALUE}}',
                ],
                'condition'             => [
                    'dates'		=> 'yes'
                ]
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

        $this->end_controls_section();
	}

	/**
	 * Style Tab: Connector
	 */
	protected function register_style_connector_controls() {
        $this->start_controls_section(
            'section_connector_style',
            [
                'label'                 => __( 'Connector', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_responsive_control(
            'connector_spacing',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size' 	    => '',
                ],
                'range'                 => [
                    'px' 		=> [
                        'min' 	=> 0,
                        'max' 	=> 100,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-vertical.pp-timeline-left .pp-timeline-marker-wrapper' => 'margin-right: {{SIZE}}px;',
                    '{{WRAPPER}} .pp-timeline-vertical.pp-timeline-right .pp-timeline-marker-wrapper' => 'margin-left: {{SIZE}}px;',
                    '{{WRAPPER}} .pp-timeline-vertical.pp-timeline-center .pp-timeline-marker-wrapper' => 'margin-left: {{SIZE}}px; margin-right: {{SIZE}}px',
            
                    '(tablet){{WRAPPER}} .pp-timeline-vertical.pp-timeline-tablet-left .pp-timeline-marker-wrapper' => 'margin-right: {{SIZE}}px;',
                    '(tablet){{WRAPPER}} .pp-timeline-vertical.pp-timeline-tablet-right .pp-timeline-marker-wrapper' => 'margin-left: {{SIZE}}px;',
                    '(tablet){{WRAPPER}} .pp-timeline-vertical.pp-timeline-tablet-center .pp-timeline-marker-wrapper' => 'margin-left: {{SIZE}}px; margin-right: {{SIZE}}px',
            
                    '(mobile){{WRAPPER}} .pp-timeline-vertical.pp-timeline-mobile-left .pp-timeline-marker-wrapper' => 'margin-right: {{SIZE}}px !important;',
                    '(mobile){{WRAPPER}} .pp-timeline-vertical.pp-timeline-mobile-right .pp-timeline-marker-wrapper' => 'margin-left: {{SIZE}}px !important;',
                    '(mobile){{WRAPPER}} .pp-timeline-vertical.pp-timeline-mobile-center .pp-timeline-marker-wrapper' => 'margin-left: {{SIZE}}px !important; margin-right: {{SIZE}}px !important;',
            
                    '{{WRAPPER}} .pp-timeline-horizontal' 	=> 'margin-top: {{SIZE}}px;',
                    '{{WRAPPER}} .pp-timeline-navigation .pp-timeline-card-date-wrapper' 	=> 'margin-bottom: {{SIZE}}px;',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'connector_thickness',
            [
                'label'                 => __( 'Thickness', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'             => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 12,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-vertical .pp-timeline-connector' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .pp-timeline-navigation:before' => 'height: {{SIZE}}{{UNIT}}; transform: translateY(calc({{SIZE}}{{UNIT}}/2))',
                ],
            ]
        );
        
        $this->start_controls_tabs( 'tabs_connector' );
        
        $this->start_controls_tab( 'tab_connector_normal', [ 'label' => __( 'Normal', 'powerpack' ) ] );
			
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'                  => 'connector_bg',
                'label'                 => __( 'Background', 'powerpack' ),
                'types'                 => [ 'classic', 'gradient' ],
                'exclude'               => [ 'image' ],
                'selector'              => '{{WRAPPER}} .pp-timeline-connector',
            ]
        );
        
        $this->end_controls_tab();
        
        $this->start_controls_tab( 'tab_connector_progress', [ 'label' => __( 'Progress', 'powerpack' ) ] );
			
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'                  => 'connector_bg_progress',
                'label'                 => __( 'Background', 'powerpack' ),
                'types'                 => [ 'classic', 'gradient' ],
                'exclude'               => [ 'image' ],
                'selector'              => '{{WRAPPER}} .pp-timeline-connector-inner',
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
	}

	/**
	 * Style Tab: Arrows
	 */
	protected function register_style_arrows_controls() {
        $this->start_controls_section(
            'section_arrows_style',
            [
                'label'                 => __( 'Arrows', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'layout'        => 'horizontal',
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
                    'layout'        => 'horizontal',
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
					'{{WRAPPER}} .pp-timeline-navigation .pp-slider-arrow' => 'font-size: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'        => 'horizontal',
                    'arrows'        => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'arrows_box_size',
            [
                'label'                 => __( 'Arrows Box Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [ 'size' => '40' ],
                'range'                 => [
                    'px' => [
                        'min'   => 15,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-timeline-navigation .pp-slider-arrow' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; transform: translateY(calc({{SIZE}}{{UNIT}}/2))',
				],
                'condition'             => [
                    'layout'        => 'horizontal',
                    'arrows'        => 'yes',
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
                        'min'   => -40,
                        'max'   => 0,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-timeline-navigation .pp-arrow-prev' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-timeline-navigation .pp-arrow-next' => 'right: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'        => 'horizontal',
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
                    'layout'        => 'horizontal',
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
                    '{{WRAPPER}} .pp-timeline-navigation .pp-slider-arrow' => 'background-color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'        => 'horizontal',
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
                    '{{WRAPPER}} .pp-timeline-navigation .pp-slider-arrow' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'        => 'horizontal',
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
				'selector'              => '{{WRAPPER}} .pp-timeline-navigation .pp-slider-arrow',
                'condition'             => [
                    'layout'        => 'horizontal',
                    'arrows'        => 'yes',
                ],
			]
		);

		$this->add_responsive_control(
			'arrows_border_radius_normal',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-timeline-navigation .pp-slider-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'        => 'horizontal',
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
                    'layout'        => 'horizontal',
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
                    '{{WRAPPER}} .pp-timeline-navigation .pp-slider-arrow:hover' => 'background-color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'        => 'horizontal',
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
                    '{{WRAPPER}} .pp-timeline-navigation .pp-slider-arrow:hover' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'        => 'horizontal',
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
                    '{{WRAPPER}} .pp-timeline-navigation .pp-slider-arrow:hover' => 'border-color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'        => 'horizontal',
                    'arrows'        => 'yes',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
	}

	/**
	 * Style Tab: Button
	 * -------------------------------------------------
	 */
	protected function register_style_button_controls() {
        $this->start_controls_section(
            'section_button_style',
            [
                'label'                 => __( 'Button', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
				],
            ]
        );

        $this->add_control(
            'button_spacing',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size' 	    => 20,
                ],
                'range' 		=> [
                    'px' 		=> [
                        'min' 	=> 0,
                        'max' 	=> 60,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-timeline-button' => 'margin-top: {{SIZE}}px;',
                ],
				'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
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
                    'source'       => 'posts',
					'link_type'    => 'button',
				],
			]
		);

        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
				'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
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
                    '{{WRAPPER}} .pp-timeline-button' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
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
                    '{{WRAPPER}} .pp-timeline-button' => 'color: {{VALUE}}',
                ],
				'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
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
				'selector'              => '{{WRAPPER}} .pp-timeline-button',
				'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
				],
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-timeline-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
				],
			]
		);
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'button_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-timeline-button',
				'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
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
					'{{WRAPPER}} .pp-timeline-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-timeline-button',
				'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
				],
			]
		);
        
        $this->add_control(
            'info_box_button_icon_heading',
            [
                'label'                 => __( 'Button Icon', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
                    'button_icon!' => '',
                ],
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
                'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
                    'button_icon!' => '',
                ],
				'selectors'             => [
					'{{WRAPPER}} .pp-info-box .pp-button-icon' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
				'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
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
                    '{{WRAPPER}} .pp-timeline-button:hover' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
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
                    '{{WRAPPER}} .pp-timeline-button:hover' => 'color: {{VALUE}}',
                ],
				'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
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
                    '{{WRAPPER}} .pp-timeline-button:hover' => 'border-color: {{VALUE}}',
                ],
				'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
				],
            ]
        );

		$this->add_control(
			'button_animation',
			[
				'label'                 => __( 'Animation', 'powerpack' ),
				'type'                  => Controls_Manager::HOVER_ANIMATION,
				'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-timeline-button:hover',
				'condition'             => [
                    'source'       => 'posts',
					'link_type'    => 'button',
				],
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->end_controls_section();
    }

    /**
	 * Render timeline widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $timeline_classes = array();
        
        $timeline_classes[] = 'pp-timeline';
        
        // Layout
        if ( $settings['layout'] ) {
            $timeline_classes[] = 'pp-timeline-' . $settings['layout'];
        }
        
        // Direction
        if ( $settings['direction'] ) {
            $timeline_classes[] = 'pp-timeline-' . $settings['direction'];
        }
        
        if ( $settings['direction_tablet'] ) {
            $timeline_classes[] = 'pp-timeline-tablet-' . $settings['direction_tablet'];
        }
        
        if ( $settings['direction_mobile'] ) {
            $timeline_classes[] = 'pp-timeline-mobile-' . $settings['direction_mobile'];
        }
        
        if ( $settings['dates'] == 'yes' ) {
            $timeline_classes[] = 'pp-timeline-dates';
        }
        
        if ( $settings['cards_arrows_alignment'] ) {
            $timeline_classes[] = 'pp-timeline-arrows-' . $settings['cards_arrows_alignment'];
        }
        
        $this->add_render_attribute( 'timeline', 'class', $timeline_classes );
        
        $this->add_render_attribute( 'timeline', 'data-timeline-layout', $settings['layout'] );
		
        $this->add_render_attribute( 'timeline-wrapper', 'class', 'pp-timeline-wrapper' );
		
		if ( $settings['layout'] == 'horizontal' && is_rtl() ) {
        	$this->add_render_attribute( 'timeline-wrapper', 'data-rtl', 'yes' );
		}
        
        $this->add_render_attribute( 'post-categories', 'class', 'pp-post-categories' );
        ?>
        <div <?php echo $this->get_render_attribute_string( 'timeline-wrapper' ); ?>>
            <?php $this->render_horizontal_timeline_nav(); ?>

            <div <?php echo $this->get_render_attribute_string( 'timeline' ); ?>>
                <?php if ( $settings['layout'] == 'vertical' ) { ?>
                    <div class="pp-timeline-connector-wrap">
                        <div class="pp-timeline-connector">
                            <div class="pp-timeline-connector-inner">
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="pp-timeline-items">
                <?php
                    if ( $settings['source'] == 'posts' ) {
                        $this->render_source_posts();
                    } elseif ( $settings['source'] == 'custom' ) {
                        $this->render_source_custom();
                    }
                ?>
                </div>
            </div><!--.pp-timeline-->
        </div><!--.pp-timeline-wrapper-->
        <?php
    }

    /**
	 * Render vertical timeline output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_horizontal_timeline_nav() {
        $settings = $this->get_settings_for_display();
        
        $this->add_render_attribute( 'navigation', 'class', 'pp-timeline-navigation' );
        
        if ( $settings['layout'] == 'horizontal' ) {
            ?>
            <div <?php echo $this->get_render_attribute_string( 'navigation' ); ?>>
                <?php
                    $i = 1;
                    if ( $settings['source'] == 'custom' ) {
                        foreach ( $settings['items'] as $index => $item ) {
                        
                            $date = $item['timeline_item_date'];
                            
                            $this->render_connector_marker( $i, $date, $item );

                            $i++;
                        }
                    } if ( $settings['source'] == 'posts' ) {
                        $args = $this->get_posts_query_arguments();
                        $posts_query = new \WP_Query( $args );

                        if ( $posts_query->have_posts() ) : while ($posts_query->have_posts()) : $posts_query->the_post();
                        
							$date = $this->pp_get_date( $settings );
                        
                            $this->render_connector_marker( $i, $date );
                        
                        $i++; endwhile; endif; wp_reset_query();
                    }
                ?>
            </div>
            <?php
        }
    }

    /**
	 * Render custom content output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_connector_marker( $number = '', $date = '', $item = '' ) {
        $settings = $this->get_settings_for_display();

		$fallback_defaults = [
			'fa fa-check',
			'fa fa-calendar',
		];
		
		$migration_allowed = Icons_Manager::is_migration_allowed();
		
		// add old default
		if ( ! isset( $item['single_marker_icon'] ) && ! $migration_allowed ) {
			$item['single_marker_icon'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-calendar';
		}

		$migrated_single = isset( $item['__fa4_migrated']['marker_icon_single'] );
		$is_new_single = ! isset( $item['single_marker_icon'] ) && $migration_allowed;
			
		// Global Icon
		if ( ! isset( $settings['marker_icon'] ) && ! $migration_allowed ) {
			// add old default
			$settings['marker_icon'] = 'fa fa-calendar';
		}

		$has_icon = ! empty( $settings['marker_icon'] );
		
		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['marker_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}
		
		if ( ! $has_icon && ! empty( $settings['select_marker_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['select_marker_icon'] );
		$is_new = ! isset( $settings['marker_icon'] ) && $migration_allowed;
        ?>
        <div class="pp-timeline-marker-wrapper">
            <?php if ( $settings['layout'] == 'horizontal' && $settings['dates'] == 'yes' ) { ?>
                <div class="pp-timeline-card-date-wrapper">
                    <div class="pp-timeline-card-date">
                        <?php echo $date; ?>
                    </div>
                </div>
            <?php } ?>

            <div class="pp-timeline-marker">
                <?php
                    if ( $settings['source'] == 'custom' &&  $item['custom_style'] == 'yes' &&  $item['single_marker_type'] != 'global' ) {
                        if ( $item['single_marker_type'] == 'icon' ) {
							if ( ! empty( $item['single_marker_icon'] ) || ( ! empty( $item['marker_icon_single']['value'] ) && $is_new_single ) ) {
								echo '<span class="pp-icon">';
								if ( $is_new_single || $migrated_single ) {
									Icons_Manager::render_icon( $item['marker_icon_single'], [ 'aria-hidden' => 'true' ] );
								} else { ?>
										<i class="<?php echo esc_attr( $item['single_marker_icon'] ); ?>" aria-hidden="true"></i>
								<?php }
								echo '</span>';
							}
                        } elseif ( $item['single_marker_type'] == 'image' ) {
                            echo Group_Control_Image_Size::get_attachment_image_html( $item, 'single_marker_icon_image', 'single_marker_icon_image' );
                        } elseif ( $item['single_marker_type'] == 'text' ) {
                            echo $item['single_marker_text'];
                        }
                    } else {
                        if ( $settings['marker_type'] == 'icon' && $has_icon ) {
                            ?>
							<span class="pp-icon">
								<?php
									if ( $is_new || $migrated ) {
										Icons_Manager::render_icon( $settings['select_marker_icon'], [ 'aria-hidden' => 'true' ] );
									} elseif ( ! empty( $settings['marker_icon'] ) ) {
										?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
									}
								?>
							</span>
							<?php
                        } elseif ( $settings['marker_type'] == 'image' ) {
                            echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'icon_image', 'icon_image' );
                        } elseif ( $settings['marker_type'] == 'number' ) {
                            echo $number;
                        } elseif ( $settings['marker_type'] == 'letter' ) {
                            $alphabets = range('A', 'Z');

                            $alphabets = array_combine( range(1, count( $alphabets ) ), $alphabets );

                            echo $alphabets[ $number ];
                        }
                    }
                ?>
            </div>
        </div>
        <?php if ( $settings['layout'] == 'vertical' ) { ?>
            <div class="pp-timeline-card-date-wrapper">
                <?php if ( $settings['dates'] == 'yes' ) { ?>
                    <div class="pp-timeline-card-date">
                        <?php echo $date; ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <?php
    }

    /**
	 * Render custom content output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_source_custom() {
        $settings = $this->get_settings_for_display();
        
        $i = 1;
        
        foreach ( $settings['items'] as $index => $item ) {
            
            $item_key       = $this->get_repeater_setting_key( 'item', 'items', $index );
            $title_key      = $this->get_repeater_setting_key( 'timeline_item_title', 'items', $index );
            $content_key    = $this->get_repeater_setting_key( 'timeline_item_content', 'items', $index );
            
            $this->add_inline_editing_attributes( $title_key, 'basic' );
            $this->add_inline_editing_attributes( $content_key, 'advanced' );
            
            $this->add_render_attribute( $item_key, 'class', [
                'pp-timeline-item',
                'elementor-repeater-item-' . esc_attr( $item['_id'] )
            ] );
            
            if ( $settings['animate_cards'] === 'yes' ) {
				$this->add_render_attribute( $item_key, 'class', 'pp-timeline-item-hidden' );
			}
            
            $this->add_render_attribute( $title_key, 'class', 'pp-timeline-card-title' );
            
            $this->add_render_attribute( $content_key, 'class', 'pp-timeline-card-content' );
            
            if ( ! empty( $item['timeline_item_link']['url'] ) ) {
            	$link_key = $this->get_repeater_setting_key( 'link', 'items', $index );
				
				$this->add_link_attributes( $link_key, $item['timeline_item_link'] );
            }
            ?>
            <div <?php echo $this->get_render_attribute_string( $item_key ); ?>>
                <div class="pp-timeline-card-wrapper">
                    <?php if ( $item['timeline_item_link']['url'] != '' ) { ?>
                    <a <?php echo $this->get_render_attribute_string( $link_key ); ?>>
                    <?php } ?>
                    <?php if ( $settings['card_arrow'] == 'yes' ) { ?>
                    <div class="pp-timeline-arrow"></div>
                    <?php } ?>
                    <div class="pp-timeline-card">
                        <?php if ( $item['card_image'] == 'yes' && ! empty( $item['image']['url'] ) ) { ?>
                            <div class="pp-timeline-card-image">
                                <?php echo Group_Control_Image_Size::get_attachment_image_html( $item, 'image', 'image' ); ?>
                            </div>
						<?php } ?>
                        <?php if ( $settings['post_title'] == 'show' || $settings['dates'] == 'yes' ) { ?>
                            <div class="pp-timeline-card-title-wrap">
                                <?php if ( $item['timeline_item_title'] != '' ) { ?>
                                    <?php if ( $settings['layout'] == 'vertical' ) { ?>
                                        <?php if ( $settings['dates'] == 'yes' ) { ?>
                                            <div class="pp-timeline-card-date">
                                                <?php echo $item['timeline_item_date']; ?>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                    <h2 <?php echo $this->get_render_attribute_string( $title_key ); ?>>
                                        <?php
                                            echo $item['timeline_item_title'];
                                        ?>
                                    </h2>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if ( $item['timeline_item_content'] != '' ) { ?>
                            <div <?php echo $this->get_render_attribute_string( $content_key ); ?>>
                                <?php
                                    echo $this->parse_text_editor( $item['timeline_item_content'] );
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if ( $item['timeline_item_link'] != '' ) { ?>
                    </a>
                    <?php } ?>
                </div>
                
                <?php if ( $settings['layout'] == 'vertical' ) { ?>
                    <?php $this->render_connector_marker( $i, $item['timeline_item_date'], $item ); ?>
                <?php } ?>
            </div>
            <?php
            $i++;
        }
    }
    
    /**
	 * Render post terms output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_terms() {
        $settings = $this->get_settings_for_display();
		$post_meta = $settings['post_meta'];
		$post_terms = $settings['post_category'];
        
        if ( $post_meta != 'show' )
            return;
        
        if ( $post_terms != 'show' )
            return;
		
		$post_type = $settings['post_type'];
		
		if ( $settings['post_type'] == 'related' ) {
			$post_type = get_post_type();
		}

		$taxonomies = $settings['tax_badge_' . $post_type];
		
		$terms = array();
		
		if ( is_array($taxonomies) ) {
			foreach ( $taxonomies as $taxonomy ) {
				$terms_tax = wp_get_post_terms( get_the_ID(), $taxonomy );
				$terms = array_merge($terms, $terms_tax);
			}
		} else {
			$terms = wp_get_post_terms( get_the_ID(), $taxonomies );
		}
		
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return;
		}
		
		$max_terms = 1;
		
		if ( $max_terms != '' ) {
			$terms = array_slice( $terms, 0, $max_terms );
		}
		
		$format = '<span class="pp-post-term">%1$s</span>';
        ?>
		<span class="pp-timeline-category">
			<?php
				foreach ( $terms as $term ) {
					printf( $format, $term->name, get_term_link( (int) $term->term_id ) );
				}
			?>
		</span>
		<?php
    }

    /**
	 * Render posts output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_source_posts() {
        $settings = $this->get_settings_for_display();

        $i = 1;

        // Query Arguments
        $args = $this->get_posts_query_arguments();
        $posts_query = new \WP_Query( $args );

        if ( $posts_query->have_posts() ) : while ($posts_query->have_posts()) : $posts_query->the_post();
            
            $item_key = 'timeline-item' . $i;

            if ( has_post_thumbnail() ) {
                $image_id = get_post_thumbnail_id( get_the_ID() );
                $pp_thumb_url = Group_Control_Image_Size::get_attachment_image_src( $image_id, 'image_size', $settings );
                $image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true);
            } else {
                $pp_thumb_url = '';
                $image_alt = '';
            }
        
            $this->add_render_attribute( $item_key, 'class', [
                'pp-timeline-item',
                'pp-timeline-item-' . intval( $i )
            ] );
            
            if ( $settings['animate_cards'] === 'yes' ) {
				$this->add_render_attribute( $item_key, 'class', 'pp-timeline-item-hidden' );
			}
		
			$post_date = $this->pp_get_date( $settings );
            ?>
            <div <?php echo $this->get_render_attribute_string( $item_key ); ?>>
                <div class="pp-timeline-card-wrapper">
                    <?php if ( $settings['link_type'] == 'card' ) { ?>
                    <a href="<?php the_permalink() ?>">
                    <?php } ?>
                    <?php if ( $settings['card_arrow'] == 'yes' ) { ?>
                    <div class="pp-timeline-arrow"></div>
                    <?php } ?>
                    <div class="pp-timeline-card">
                        <?php if ( $settings['post_image'] == 'show' ) { ?>
                            <div class="pp-timeline-card-image">
                                <img src="<?php echo esc_url( $pp_thumb_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>">
                            </div>
                        <?php } ?>
                        <?php if ( $settings['post_title'] == 'show' || $settings['dates'] == 'yes' ) { ?>
                            <div class="pp-timeline-card-title-wrap">
                                <?php if ( $settings['layout'] == 'vertical' ) { ?>
                                    <?php if ( $settings['dates'] == 'yes' ) { ?>
                                        <div class="pp-timeline-card-date">
											<?php
												echo $post_date;
											?>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ( $settings['post_title'] == 'show' ) { ?>
                                    <h2 class="pp-timeline-card-title">
                                        <?php
                                            if ( $settings['link_type'] == 'title' ) {
                                                printf( '<a href="%1$s">%2$s</a>', get_permalink(), get_the_title() );
                                            } else {
                                                the_title();
                                            }
                                        ?>
                                    </h2>
                                <?php } ?>
                                <?php if ( $settings['post_meta'] == 'show' ) { ?>
                                    <div class="pp-timeline-meta">
                                        <?php if ( $settings['post_author'] == 'show' ) { ?>
                                            <span class="pp-timeline-author">
                                                <?php the_author(); ?>
                                            </span>
                                        <?php } ?>
                                        <?php $this->render_terms(); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div class="pp-timeline-card-content">
                            <?php if ( $settings['post_content'] == 'show' ) { ?>
                                <div class="pp-timeline-card-excerpt">
                                    <?php
                                        $this->render_post_content();
                                    ?>
                                </div>
                            <?php } ?>
                            <?php if ( $settings['link_type'] == 'button' && $settings['button_text'] ) { ?>
                                <?php
                                    $this->add_render_attribute( 'button', 'class', [
                                            'pp-timeline-button',
                                            'elementor-button',
                                            'elementor-size-' . $settings['button_size'],
                                        ]
                                    );                                           
                                ?>
                                <a <?php echo $this->get_render_attribute_string( 'button' ); ?> href="<?php the_permalink() ?>">
                                    <span class="pp-timeline-button-text">
                                        <?php echo esc_attr( $settings['button_text'] ); ?>
                                    </span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if ( $settings['link_type'] == 'card' ) { ?>
                    </a>
                    <?php } ?>
                </div>
                
                <?php
                    if ( $settings['layout'] == 'vertical' ) {
                        $this->render_connector_marker( $i, $post_date );
                    }
                ?>
            </div>
            <?php
        $i++; endwhile; endif; wp_reset_query();
    }

	/**
	 * Get post date.
	 *
	 * Returns the post date.
	 *
	 * @since 1.4.11.0
	 * @param array $settings object.
	 * @access public
	 */
	public function pp_get_date( $settings ) {
		
		$pp_date = '';
		
		if ( $settings['date_format'] == '' ) {
			$pp_date =  get_the_date();
		} elseif ( $settings['date_format'] == 'modified' ) {
			$pp_date = get_the_modified_date( '', get_the_ID() );
		} elseif ( $settings['date_format'] == 'custom' ) {
			$date_format = $settings['timeline_post_date_format'];
			$pp_date = ( $date_format ) ? get_the_date($date_format) : get_the_date();
		} elseif ( $settings['date_format'] == 'key' ) {
			$date_meta_key = $settings['timeline_post_date_key'];
			if ( $date_meta_key ) {
				$pp_date = get_post_meta( get_the_ID(), $date_meta_key, 'true' );
			}
		}

		return apply_filters( 'pp_timeline_date_format', $pp_date, get_option( 'date_format' ), '', '' );
	}

    /**
	 * Get post query arguments.
	 *
	 * @access protected
	 */
    protected function get_posts_query_arguments() {
        $settings = $this->get_settings_for_display();
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
	 * Get post content.
	 *
	 * @access protected
	 */
    protected function render_post_content() {
        $settings = $this->get_settings_for_display();
        
        $content_length = $settings['content_length'];
        
		if ( $content_length == '' ) {
			$content = get_the_excerpt();
		} else {
			$content = wp_trim_words( get_the_content(), $content_length );
		}
        
		echo $content;
    }

    /**
	 * Render timeline widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
    protected function _content_template() {}
}