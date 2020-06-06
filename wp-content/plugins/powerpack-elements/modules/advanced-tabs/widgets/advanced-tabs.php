<?php
namespace PowerpackElements\Modules\AdvancedTabs\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Repeater;
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
 * Advanced Tabs Widget
 */
class Advanced_Tabs extends Powerpack_Widget {

	/**
	 * Retrieve advanced tabs widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Advanced_Tabs' );
	}

	/**
	 * Retrieve advanced tabs widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Advanced_Tabs' );
	}

	/**
	 * Retrieve the list of categories the advanced tabs widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return parent::get_widget_categories( 'Advanced_Tabs' );
	}

	/**
	 * Retrieve advanced tabs widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Advanced_Tabs' );
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
		return parent::get_widget_keywords( 'Advanced_Tabs' );
	}

    /**
	 * Retrieve the list of scripts the advanced tabs widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
        return [
            'pp-advanced-tabs',
            'powerpack-frontend',
        ];
    }

	/**
	 * Register advanced tabs widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() {
		
		/* Content Tab */
		$this->register_content_tabs_controls();
		$this->register_content_layout_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_tabs_controls();
		$this->register_style_title_controls();
		$this->register_style_content_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/
	protected function register_content_tabs_controls() {

		/**
		 * Content Tab: Advanced Tabs
		 */
		$this->start_controls_section(
			'section_advanced_tabs',
			[
				'label' => __( 'Advanced Tabs', 'powerpack' ),
			]
		);
        
        $repeater = new Repeater();

        $repeater->start_controls_tabs( 'tabs_at' );

        $repeater->start_controls_tab(
            'tab_content',
            [
                'label'                 => __( 'Content', 'powerpack' ),
            ]
        );
        
        $repeater->add_control(
			'tab_title',
			[
                'label'				=> __( 'Title', 'powerpack' ),
                'type'				=> Controls_Manager::TEXT,
				'dynamic'			=> [
					'active'   => true,
				],
                'label_block'		=> true,
                'placeholder'		=> __( 'Title', 'powerpack' ),
                'default'			=> __( 'Title', 'powerpack' ),
			]
		);
        
        $repeater->add_control(
			'content_type',
			[
                'label'				=> __( 'Content Type', 'powerpack' ),
                'type'				=> Controls_Manager::SELECT,
                'default'			=> 'tab_content',
                'options'			=> [
                    'tab_content'   => __( 'Content', 'powerpack' ),
                    'tab_photo'     => __( 'Image', 'powerpack' ),
                    'tab_video'     => __( 'Link (Video/Map/Page)', 'powerpack' ),
                    'section'       => __( 'Saved Section', 'powerpack' ),
                    'widget'        => __( 'Saved Widget', 'powerpack' ),
                    'template'      => __( 'Saved Page Template', 'powerpack' ),
                ],
			]
		);
        
        $repeater->add_control(
			'content',
            [
                'label'				=> __( 'Content', 'powerpack' ),
                'type'				=> Controls_Manager::WYSIWYG,
				'dynamic'			=> [
					'active'   => true,
				],
                'default'			=> __( 'I am tab content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'powerpack' ),
                'condition'			=> [
                    'content_type' => 'tab_content',
                ],
            ]
		);
        
        $repeater->add_control(
			'image',
            [
                'label'				=> __( 'Image', 'powerpack' ),
                'type'				=> Controls_Manager::MEDIA,
				'dynamic'			=> [
					'active'	=> true,
				],
                'default'			=> [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition'			=> [
                    'content_type' => 'tab_photo',
                ],
            ]
		);
        
        $repeater->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'				=> 'image',
                'label'				=> __( 'Image Size', 'powerpack' ),
                'default'			=> 'large',
                'exclude'			=> [ 'custom' ],
                'condition'			=> [
                    'content_type' => 'tab_photo',
                ],
            ]
        );
        
        $repeater->add_control(
			'link_video',
            [
                'label'				=> __( 'Link', 'powerpack' ),
                'type'				=> Controls_Manager::TEXT,
				'dynamic'			=> [
					'active'   => true,
				],
                'placeholder'		=> __( 'Enter your Video link', 'powerpack' ),
                'label_block'		=> true,
                'condition'			=> [
                    'content_type' => 'tab_video',
                ],
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
                    'content_type'    => 'widget',
                ],
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
        
       /* $repeater->add_control(
			'saved_section',
            [
                'label'                 => __( 'Choose Section', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => $this->get_page_template_options( 'section' ),
                'default'               => '-1',
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
		);*/

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
        
        /*$repeater->add_control(
			'templates',
            [
                'label'                 => __( 'Choose Template', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => $this->get_page_template_options( 'page' ),
                'default'               => '-1',
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
		);*/

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
            'tab_icon',
            [
                'label'                 => __( 'Icon', 'powerpack' ),
            ]
        );
		
		$repeater->add_control(
			'selected_icon',
			[
				'label'				=> __( 'Icon', 'powerpack' ),
				'type'				=> Controls_Manager::ICONS,
				'label_block'		=> true,
				'default'			=> [
					'value' => 'fas fa-check',
					'library' => 'fa-solid',
				],
				'fa4compatibility'	=> 'icon',
			]
		);

        $repeater->end_controls_tab();
		
        $repeater->end_controls_tabs();

		$this->add_control(
			'tab_features',
			[
				'label'       => '',
				'type'        => Controls_Manager::REPEATER,
				'default'     => [
					[
						'tab_title'     => __( 'Tab #1', 'powerpack' ),
						'content' => __( 'I am tab 1 content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'powerpack' ),
					],
					[
						'tab_title'     => __( 'Tab #2', 'powerpack' ),
						'content' => __( 'I am tab 2 content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'powerpack' ),
					],
					[
						'tab_title'     => __( 'Tab #3', 'powerpack' ),
						'content' => __( 'I am tab 3 content. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'powerpack' ),
					],
				],
				'fields'                => array_values( $repeater->get_controls() ),
				'title_field' => '{{{ tab_title }}}',
			]
		);

		$this->end_controls_section();
	}
	
	protected function register_content_layout_controls() {
		/**
		 * Content Tab: Layout
		 */
		$this->start_controls_section(
			'section_general_layout',
			[
				'label' => __( 'Layout', 'powerpack' ),
			]
		);
		$this->add_control(
			'type',
			[
				'label'   => __( 'Layout', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'at-horizontal',
				'options' => [
					'at-horizontal' => __( 'Horizontal', 'powerpack' ),
					'at-vertical'   => __( 'Vertical', 'powerpack' ),
				],
			]
		);
		
		$this->add_control(
			'responsive_support',
			[
				'label'   => __( 'Responsive Support', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'tablet',
				'options' => [
					'no'		=> __( 'No', 'powerpack' ),
					'tablet'	=> __( 'For Tablet & Mobile', 'powerpack' ),
					'mobile'	=> __( 'For Mobile Only', 'powerpack' ),
				],
			]
		);
		
		$this->add_control(
			'custom_style',
			[
				'label'   => __( 'Select Style', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-0',
				'options' => [
					'style-0' => __( 'Basic', 'powerpack' ),
					'style-1' => __( 'Style 1', 'powerpack' ),
					'style-2' => __( 'Style 2', 'powerpack' ),
					'style-3' => __( 'Style 3', 'powerpack' ),
					'style-4' => __( 'Style 4', 'powerpack' ),
					'style-5' => __( 'Style 5', 'powerpack' ),
					'style-6' => __( 'Style 6', 'powerpack' ),
					'style-7' => __( 'Style 7', 'powerpack' ),
					'style-8' => __( 'Style 8', 'powerpack' ),
					'style-custom' => __( 'Custom', 'powerpack' ),
				],
			]
		);
		
		$this->add_control(
			'default_tab',
			[
				'label'       => __( 'Default Active Tab Index', 'powerpack' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => __( 1 ),
				'placeholder' => __( 'Default Active Tab Index', 'powerpack' ),
			]
		);
		
		$this->add_control(
			'custom_id_prefix',
			[
				'label'			=> __( 'Custom ID Prefix', 'powerpack' ),
				'description'	=> __( 'A prefix that will be applied to ID attribute of tabs\'s in HTML. For example, prefix "mytab" will be applied as "mytab-1", "mytab-2" in ID attribute of Tab 1 and Tab 2 respectively. It should only contain dashes, underscores, letters or numbers. No spaces.', 'powerpack' ),
				'type'			=> Controls_Manager::TEXT,
				'label_block'	=> true,
				'default'		=> '',
				'placeholder'	=> __( 'mytab', 'powerpack' ),
			]
		);

		$this->end_controls_section();
	}
	
	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links('Advanced_Tabs');

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
	
	protected function register_style_tabs_controls() {
		/**
		 * Style Tab: Tabs
		 */
		$this->start_controls_section(
			'section_tabs_style',
			[
				'label' => __( 'Tabs', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_responsive_control(
			'title_align_horizontal',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => __( 'Left', 'powerpack' ),
						'icon' 		=> 'eicon-h-align-left',
					],
					'center'     => [
						'title' => __( 'Center', 'powerpack' ),
						'icon' 		=> 'eicon-h-align-center',
					],
					'flex-end'   => [
						'title' => __( 'Right', 'powerpack' ),
						'icon' 		=> 'eicon-h-align-right',
					],
				],
				'default'   => 'center',
				'condition' => [
					'type' => 'at-horizontal',
				],
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-tabs-wrapper.at-horizontal, {{WRAPPER}} .pp-advanced-tabs .pp-advanced-tabs-content-wrapper .pp-tab-responsive.pp-advanced-tabs-title' => 'justify-content: {{VALUE}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'title_margin_right',
			[
				'label'      => __( 'Margin Right', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 0,
				],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'condition' => [
					'type' => 'at-horizontal',
					'title_align_horizontal' => 'flex-end',
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .at-horizontal .pp-advanced-tabs-title:last-child' => 'margin-right: {{SIZE}}{{UNIT}}',
				],
			]
		);
		
		$this->add_responsive_control(
			'title_margin_left',
			[
				'label'      => __( 'Margin Left', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 0,
				],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'condition' => [
					'type' => 'at-horizontal',
					'title_align_horizontal' => 'flex-start',
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .at-horizontal .pp-advanced-tabs-title:first-child' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
			]
		);
		
		$this->add_responsive_control(
			'title_align_vertical',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => __( 'Top', 'powerpack' ),
						'icon' 		=> 'eicon-v-align-top',
					],
					'center'     => [
						'title' => __( 'Center', 'powerpack' ),
						'icon' 		=> 'eicon-v-align-middle',
					],
					'flex-end'   => [
						'title' => __( 'Bottom', 'powerpack' ),
						'icon' 		=> 'eicon-v-align-bottom',
					],
				],
				'default'   => 'flex-start',
				'condition' => [
					'type' => 'at-vertical',
				],
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-tabs-wrapper.at-vertical' => 'justify-content: {{VALUE}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'title_margin_top',
			[
				'label'      => __( 'Margin Top', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 0,
				],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'condition' => [
					'type' => 'at-vertical',
					'title_align_vertical' => 'flex-start',
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .at-vertical .pp-advanced-tabs-title:first-child' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);
		
		$this->add_responsive_control(
			'title_margin_bottom',
			[
				'label'      => __( 'Margin Bottom', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 0,
				],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'condition' => [
					'type' => 'at-vertical',
					'title_align_vertical' => 'flex-end',
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .at-vertical .pp-advanced-tabs-title:last-child' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);
		
		$this->add_responsive_control(
			'title_space',
			[
				'label'      => __( 'Space Between Tabs', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 0,
				],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'size_units' => [ 'px' ],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .at-horizontal .pp-advanced-tabs-title:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .at-horizontal-content .pp-advanced-tabs-title:not(:first-child)' => 'margin-top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .at-vertical .pp-advanced-tabs-title:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'(tablet){{WRAPPER}} .pp-tabs-responsive-tablet .pp-tabs-panel:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'(mobile){{WRAPPER}} .pp-tabs-responsive-mobile .pp-tabs-panel:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
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
			'section_title_style',
			[
				'label' => __( 'Title', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'icon_position',
			[
				'label'   => __( 'Icon Position', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'top'    => __( 'Top', 'powerpack' ),
					'bottom' => __( 'Bottom', 'powerpack' ),
					'left'   => __( 'Left', 'powerpack' ),
					'right'  => __( 'Right', 'powerpack' ),
				],
				'default' => 'left',
			]
		);
		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => __( 'Icon Size', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 15,
				],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .pp-advanced-tabs-title .pp-icon' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'separator'  => 'after',
			]
		);
		$this->add_control(
			'title_border_radius',
			[
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top' => 0,
					'bottom' => 0,
					'left' => 0,
					'right' => 0,
				],
				'selectors'  => [
					'{{WRAPPER}} .pp-advanced-tabs-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'separator'  => 'after',
				'default'    => [
					'top' => 10,
					'bottom' => 10,
					'left' => 10,
					'right' => 10,
				],
				'selectors'  => [
					'{{WRAPPER}} .pp-advanced-tabs-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'label'     => __( 'Title Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .pp-advanced-tabs-title .pp-advanced-tabs-title-text',
			]
		);
		$this->start_controls_tabs( 'tabs_title_style' );

		$this->start_controls_tab(
			'tab_title_normal',
			[
				'label' => __( 'Normal', 'powerpack' ),
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label'     => __( 'Icon Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#808080',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-tabs-title .pp-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-advanced-tabs-title svg' => 'fill: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'title_text_color',
			[
				'label'     => __( 'Title Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#808080',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-tabs-title .pp-advanced-tabs-title-text' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'title_bg_color',
			[
				'label'     => __( 'Title Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-tabs-title' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'custom_style!' => 'style-6',
				],
			]
		);
		$this->add_control(
			'title_border_color',
			[
				'label'     => __( 'Title Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#808080',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-tabs-title' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .pp-style-6 .pp-advanced-tabs-title:after' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'custom_style' => [ 'style-6' ],
				],
			]
		);
        
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'                  => 'tab_title_border',
                'label'                 => esc_html__( 'Border', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-style-custom .pp-advanced-tabs-title',
				'condition' => [
					'custom_style' => [ 'style-custom' ],
				],
            ]
        );
		
		$this->end_controls_tab(); // End Normal Tab

		$this->start_controls_tab(
			'tab_title_active',
			[
				'label' => __( 'Active', 'powerpack' ),
			]
		);
		$this->add_control(
			'icon_color_active',
			[
				'label'     => __( 'Icon Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => [
					'{{WRAPPER}} .pp-tab-active .pp-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-tab-active svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .at-hover .pp-advanced-tabs-title:hover .pp-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .at-hover .pp-advanced-tabs-title:hover svg' => 'fill: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'title_text_color_active',
			[
				'label'     => __( 'Title Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'selectors' => [
					'{{WRAPPER}} .pp-tab-active .pp-advanced-tabs-title-text' => 'color: {{VALUE}}',
					'{{WRAPPER}} .at-hover .pp-advanced-tabs-title:hover .pp-advanced-tabs-title-text' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'title_bg_color_active',
			[
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-tab-active' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .at-hover .pp-advanced-tabs-title:hover' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .pp-style-1 .at-horizontal .pp-tab-active:after' => 'border-top-color: {{VALUE}}',
					'{{WRAPPER}} .pp-style-1 .at-vertical .pp-tab-active:after' => 'border-left-color: {{VALUE}}',
					'{{WRAPPER}} .pp-style-6 .pp-advanced-tabs-title.pp-tab-active:after' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'title_border_color_active',
			[
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'condition' => [
					'custom_style!' => [ 'style-1', 'style-6', 'style-7', 'style-8' ],
				],
				'selectors' => [
					'{{WRAPPER}} .pp-tab-active, {{WRAPPER}} .pp-style-custom .pp-advanced-tabs-title.pp-tab-active' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .at-hover .pp-advanced-tabs-title:hover' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .pp-style-2 .pp-advanced-tabs-title.pp-tab-active:before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .pp-style-2 .at-hover .pp-advanced-tabs-title.pp-tab-active:before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .pp-style-3 .pp-advanced-tabs-title.pp-tab-active:before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .pp-style-3 .at-hover .pp-advanced-tabs-title.pp-tab-active:before' => 'background-color: {{VALUE}}',
				],
			]
			);
			$this->add_control(
				'title_animation_color',
				[
				'label'     => __( 'Animation Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000000',
				'condition' => [
					'custom_style' => [ 'style-4', 'style-5', 'style-7', 'style-8' ],
				],
				'selectors' => [
					'{{WRAPPER}} .pp-style-4 .pp-advanced-tabs-title.pp-tab-active:before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .pp-style-4 .pp-advanced-tabs-title.pp-tab-active:after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .pp-style-5 .pp-advanced-tabs-title.pp-tab-active:before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .pp-style-5 .pp-advanced-tabs-title.pp-tab-active:after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .pp-style-7 .pp-advanced-tabs-title .active-slider-span' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .pp-style-8 .pp-advanced-tabs-title .active-slider-span' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab(); // End Hover Tab

		$this->end_controls_tabs(); // End Controls Tab

		$this->add_control(
			'tab_hover_effect',
			[
				'label'     => __( 'Hover Effect', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'yes' => [
						'title' => __( 'Yes', 'powerpack' ),
						'icon'  => 'fa fa-check',
					],
					'no'     => [
						'title' => __( 'No', 'powerpack' ),
						'icon'  => 'fa fa-ban',
					],
				],
				'condition' => [
					'custom_style!' => [ 'style-6' ],
				],
				'separator' => 'before',
				'default'   => 'no',
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
				'label' => __( 'Content', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'tab_align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'start' => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center'     => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'end'   => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => 'center',
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-tabs-content'   => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'tab_bg_style',
				'label'    => __( 'Background', 'powerpack' ),
				'types'    => [ 'none','classic','gradient' ],
				'selector' => '{{WRAPPER}} .pp-advanced-tabs-content',
			]
		);
		$this->add_control(
			'text_color',
			[
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#808080',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-tabs-content' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tab_text_typography',
				'label'    => __( 'Text Typography', 'powerpack' ),
				'scheme'   => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .pp-advanced-tabs-content',
			]
		);
        
        $this->add_control(
			'tab_border_type',
			[
                'label'   => __( 'Border Type', 'powerpack' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'solid',
                'options' => [
                    'none'   => __( 'None', 'powerpack' ),
                    'solid'     => __( 'Solid', 'powerpack' ),
                    'double'     => __( 'Double', 'powerpack' ),
                    'dotted'     => __( 'Dotted', 'powerpack' ),
                    'dashed'     => __( 'Dashed', 'powerpack' ),
                    'groove'     => __( 'Groove', 'powerpack' ),
                ],
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-tabs-content' => 'border-style: {{VALUE}}',
				],
			]
		);
		
		$this->add_responsive_control(
			'tab_border_width',
			[
				'label'      => __( 'Border Width', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top' => 1,
					'bottom' => 1,
					'left' => 1,
					'right' => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .pp-advanced-tabs-content' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition' => [
                    'tab_border_type!' => 'none',
                ],
			]
		);
		
		$this->add_control(
			'tab_border_color',
			[
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#808080',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-tabs-content' => 'border-color: {{VALUE}}',
				],
                'condition' => [
                    'tab_border_type!' => 'none',
                ],
			]
		);
		$this->add_control(
			'tab_border_radius',
			[
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'separator' => 'before',
				'default'    => [
					'top' => 0,
					'bottom' => 0,
					'left' => 0,
					'right' => 0,
				],
				'selectors'  => [
					'{{WRAPPER}} .pp-advanced-tabs-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'tab_padding',
			[
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top' => 10,
					'bottom' => 10,
					'left' => 10,
					'right' => 10,
				],
				'selectors'  => [
					'{{WRAPPER}} .pp-advanced-tabs-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_section();
	}

		/**
	 * Render tabs widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();
		$tabs = $this->get_settings_for_display( 'tab_features' );
		$id_int = substr( $this->get_id_int(), 0, 3 );
		$hover_class = $defaultTabNo = $defaultTitle = $defaultContent = '';
		
		$fallback_defaults = [
			'fa fa-check',
			'fa fa-times',
			'fa fa-dot-circle-o',
		];
		 
		if ( 0 < $settings['default_tab'] && sizeof($tabs) >= $settings['default_tab'] ) {
			$defaultTabNo = $settings['default_tab'];
		} else {
			$defaultTabNo = 1;
		}
        
		$hover_state = $settings['tab_hover_effect'];
        
		if ( 'yes' == $hover_state ) {
			$hover_class = ' at-hover';
		} else {
			$hover_class = ' at-no-hover';
		}
		
		$this->add_render_attribute( 'container', [
			'class'	=> ['pp-advanced-tabs', 'pp-' . $settings['custom_style'], 'pp-tabs-responsive-' . $settings['responsive_support']],
			'role'	=> 'tablist'
		] );
		
		if ( $settings['responsive_support'] != 'no' ) {
			$this->add_render_attribute( 'container', 'class', 'pp-advabced-tabs-responsive' );
		}
		
		$this->add_render_attribute( 'tabs-wrap', 'class', [
			'pp-advanced-tabs-wrapper',
			'pp-tabs-labels',
			$settings['type'],
			$hover_class
		] );
		?>
		<div <?php echo $this->get_render_attribute_string( 'container' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'tabs-wrap' ); ?>>
				<?php
                    foreach ( $tabs as $index => $item ) {

                        $tab_count = $index + 1;

                        if ( $tab_count == $defaultTabNo ) {
                            $defaultTitle = 'pp-tab-active';
                        } else {
                            $defaultTitle = '';
                        }
						
						if ( $settings['custom_id_prefix'] ) {
							$tab_id = $settings['custom_id_prefix'] . '-' . $tab_count;
						} else {
							$tab_id = 'pp-advanced-tabs-title-' . $id_int . $tab_count;
						}

                        $title_text_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tab_features', $index );

                        $this->add_render_attribute( $title_text_setting_key, [
                            'id' => $tab_id,
                            'class' => [ 'pp-advanced-tabs-title', 'pp-tabs-label', 'pp-advanced-tabs-desktop-title', $defaultTitle ],
                            'data-tab' => $tab_count,
                            'data-index' => $id_int . $tab_count,
                            'tabindex' => $id_int . $tab_count,
                            'role' => 'tab',
                            'aria-controls' => 'pp-advanced-tabs-content-' . $id_int . $tab_count,
                        ] );

                        if ( 'top' == $settings['icon_position'] || 'left' == $settings['icon_position'] ) { ?>
                            <div <?php echo $this->get_render_attribute_string( $title_text_setting_key ); ?>>
                                
								<?php $this->render_tab_title_icon( $item ); ?>
                                
                                <span class="pp-advanced-tabs-title-text"><?php echo $item['tab_title']; ?></span>
                                <?php if ( 'style-7' == $settings['custom_style'] || 'style-8' == $settings['custom_style'] ) { ?>
                                    <span class="active-slider-span"></span>
                                <?php }?>
                            </div>
                        <?php } elseif ( 'bottom' == $settings['icon_position'] || 'right' == $settings['icon_position'] ) { ?>
                            <div <?php echo $this->get_render_attribute_string( $title_text_setting_key ); ?>>
                                <span class="pp-advanced-tabs-title-text"><?php echo $item['tab_title']; ?></span>
                                
								<?php $this->render_tab_title_icon( $item ); ?>
                                
                                <?php if ( 'style-7' == $settings['custom_style'] || 'style-8' == $settings['custom_style'] ) { ?>
                                    <span class="active-slider-span"></span>
                                <?php }?>
                            </div>
                        <?php }
                    } ?>
			</div>
			<div class="pp-advanced-tabs-content-wrapper pp-tabs-panels <?php echo $settings['type']; ?>-content">
				<?php foreach ( $tabs as $index => $item ) :
					$tab_count = $index + 1;
					if ( $tab_count == $defaultTabNo ) {
						$defaultContent = 'pp-tab-active';
					} else {
						$defaultContent = '';
					}
						
					if ( $settings['custom_id_prefix'] ) {
						$tab_id = $settings['custom_id_prefix'] . '-' . $tab_count;
					} else {
						$tab_id = 'pp-advanced-tabs-title-' . $id_int . $tab_count;
					}
		
					$tab_content_setting_key = $this->get_repeater_setting_key( 'content', 'tab_features', $index );

					$this->add_render_attribute( $tab_content_setting_key, [
						'id'              => 'pp-advanced-tabs-content-' . $id_int . $tab_count,
						'class'           => [ 'pp-advanced-tabs-content', 'elementor-clearfix', 'pp-advanced-tabs-' . $item['content_type'], $defaultContent ],
						'data-tab'        => $tab_count,
						'role'            => 'tabpanel',
						'aria-labelledby' => $tab_id,
					] );

					//$this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );
					?>
					<div class="pp-tabs-panel">
					<div class="pp-advanced-tabs-title pp-tabs-label pp-tab-responsive<?php echo $hover_class;?>" data-index ="<?php echo $id_int . $tab_count;?>">
						<div class="pp-advanced-tabs-title-inner">
							<?php $this->render_tab_title_icon( $item ); ?>

							<span><?php echo $item['tab_title']; ?></span>
							<i class="pp-toggle-icon pp-tab-open fa"></i>
						</div>
					</div>
					<div <?php echo $this->get_render_attribute_string( $tab_content_setting_key ); ?>>
						<?php
                            if ( 'tab_content' == $item['content_type'] ) {
                                
                                echo $this->parse_text_editor( $item['content'] );
                                
                            } elseif ( 'tab_photo' == $item['content_type'] && $item['image']['url'] != '' ) {
                                
                                echo Group_Control_Image_Size::get_attachment_image_html( $item, 'image', 'image' );
                                
                            } elseif ( 'tab_video' == $item['content_type'] ) {
                                
                                echo $this->parse_text_editor( $item['link_video'] );
                                
                            } elseif ( $item['content_type'] == 'section' && !empty( $item['saved_section'] ) ) {

                                echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $item['saved_section'] );

                            } elseif ( $item['content_type'] == 'template' && !empty( $item['templates'] ) ) {

                                echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $item['templates'] );

                            } elseif ( $item['content_type'] == 'widget' && !empty( $item['saved_widget'] ) ) {

                                echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $item['saved_widget'] );

                            }
                        ?>
					</div>
					</div>
					<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 *  Get Saved Widgets
	 *
	 *  @param string $type Type.
	 *  
	 *  @return string
	 */
	public function render_tab_title_icon( $item ) {
		$settings = $this->get_settings();
		
		$migration_allowed = Icons_Manager::is_migration_allowed();
		
		// add old default
		if ( ! isset( $item['icon'] ) && ! $migration_allowed ) {
			$item['icon'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-check';
		}

		$migrated = isset( $item['__fa4_migrated']['selected_icon'] );
		$is_new = ! isset( $item['icon'] ) && $migration_allowed;

		if ( ! empty( $item['icon'] ) || ( ! empty( $item['selected_icon']['value'] ) && $is_new ) ) {
			?>
			<span class="pp-icon pp-advanced-tabs-icon-<?php echo $settings['icon_position']; ?>">
			<?php
			if ( $is_new || $migrated ) {
				Icons_Manager::render_icon( $item['selected_icon'], [ 'aria-hidden' => 'true' ] );
			} else { ?>
				<i class="<?php echo esc_attr( $item['icon'] ); ?>" aria-hidden="true"></i>
			<?php } ?>
			</span>
			<?php
		}
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
    
	/**
	 * Render Advanced Tabs widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
    protected function _content_template() {
	}
}