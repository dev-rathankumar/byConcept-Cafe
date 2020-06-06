<?php
namespace PowerpackElements\Modules\OffcanvasContent\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Repeater;
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
 * Offcanvas Content Widget
 */
class Offcanvas_Content extends Powerpack_Widget {
    
    /**
	 * Retrieve offcanvas content widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return parent::get_widget_name( 'Offcanvas_Content' );
    }

    /**
	 * Retrieve offcanvas content widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
    public function get_title() {
        return parent::get_widget_title( 'Offcanvas_Content' );
    }

    /**
	 * Retrieve the list of categories the offcanvas content widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Offcanvas_Content' );
    }

    /**
	 * Retrieve offcanvas content widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
    public function get_icon() {
        return parent::get_widget_icon( 'Offcanvas_Content' );
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
		return parent::get_widget_keywords( 'Offcanvas_Content' );
	}
    
    /**
	 * Retrieve the list of scripts the offcanvas content widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_script_depends() {
        return [
            'pp-offcanvas-content',
            'powerpack-frontend'
        ];
    }
    
    /**
	 * Retrieve the list of styles the offcanvas content widget depended on.
	 *
	 * Used to set styles dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget styles dependencies.
	 */
    public function get_style_depends() {
        return [
            'pp-hamburgers'
        ];
    }

    protected function _register_controls() {
		
		/* Content Tab */
		$this->register_content_offcanvas_controls();
		$this->register_content_toggle_controls();
		$this->register_content_settings_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_offcanvas_controls();
		$this->register_style_content_controls();
		$this->register_style_toggle_controls();
		$this->register_style_close_button_controls();
		$this->register_style_overlay_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/
	
	protected function register_content_offcanvas_controls() {
        
        /**
         * Content Tab: Offcanvas Content
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_offcanvas_content',
            [
                'label'                 => __( 'Offcanvas Content', 'powerpack' ),
            ]
        );

        $this->add_control(
            'content_type',
            [
                'label'                 => __( 'Content Type', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => [
                    'sidebar'   => __( 'Sidebar', 'powerpack' ),
                    'custom'    => __( 'Custom Content', 'powerpack' ),
                    'section'   => __( 'Saved Section', 'powerpack' ),
                    'widget'    => __( 'Saved Widget', 'powerpack' ),
                    'template'  => __( 'Saved Page Template', 'powerpack' ),
                ],
                'default'               => 'custom',
            ]
        );
        
        global $wp_registered_sidebars;

		$options = [];

		if ( ! $wp_registered_sidebars ) {
			$options[''] = __( 'No sidebars were found', 'powerpack' );
		} else {
			$options[''] = __( 'Choose Sidebar', 'powerpack' );

			foreach ( $wp_registered_sidebars as $sidebar_id => $sidebar ) {
				$options[ $sidebar_id ] = $sidebar['name'];
			}
		}

		$default_key = array_keys( $options );
		$default_key = array_shift( $default_key );

		$this->add_control(
            'sidebar',
            [
                'label'                 => __( 'Choose Sidebar', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => $default_key,
                'options'               => $options,
				'condition'             => [
					'content_type' => 'sidebar',
				],
            ]
        );

        /*$this->add_control(
            'saved_widget',
            [
                'label'                 => __( 'Choose Widget', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => $this->get_page_template_options( 'widget' ),
				'default'               => '-1',
				'condition'             => [
					'content_type'    => 'widget',
				],
            ]
        );*/

		$this->add_control(
			'saved_widget',
			[
				'label'                 => __( 'Choose Widget', 'powerpack' ),
				'type'					=> 'pp-query',
				'label_block'			=> false,
				'multiple'				=> false,
				'query_type'			=> 'templates-widget',
				'condition'             => [
					'content_type'    => 'widget',
				],
			]
		);

        /*$this->add_control(
            'saved_section',
            [
                'label'                 => __( 'Choose Section', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => $this->get_page_template_options( 'section' ),
				'default'               => '-1',
				'condition'             => [
					'content_type'    => 'section',
				],
            ]
        );*/

		$this->add_control(
			'saved_section',
			[
				'label'                 => __( 'Choose Section', 'powerpack' ),
				'type'					=> 'pp-query',
				'label_block'			=> false,
				'multiple'				=> false,
				'query_type'			=> 'templates-section',
				'condition'             => [
					'content_type'    => 'section',
				],
			]
		);

        /*$this->add_control(
            'templates',
            [
                'label'                 => __( 'Choose Template', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => $this->get_page_template_options( 'page' ),
				'default'               => '-1',
				'condition'             => [
					'content_type'    => 'template',
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
				'query_type'			=> 'templates-page',
				'condition'             => [
					'content_type'    => 'template',
				],
			]
		);
        
        $this->add_control(
			'custom_content',
			[
				'label'                 => '',
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[
						'title'       => __( 'Box 1', 'powerpack' ),
						'description' => __( 'Text box description goes here', 'powerpack' ),
					],
					[
						'title'       => __( 'Box 2', 'powerpack' ),
						'description' => __( 'Text box description goes here', 'powerpack' ),
					],
				],
				'fields'                => [
                    [
                        'name'              => 'title',
                        'label'             => __( 'Title', 'powerpack' ),
                        'type'              => Controls_Manager::TEXT,
                        'dynamic'           => [
                            'active'   => true,
                        ],
                        'default'           => __( 'Title', 'powerpack' ),
                    ],
                    [
                        'name'              => 'description',
                        'label'             => __( 'Description', 'powerpack' ),
                        'type'              => Controls_Manager::WYSIWYG,
                        'dynamic'           => [
                            'active'   => true,
                        ],
                        'default'           => '',
                    ],
				],
				'title_field'           => '{{{ title }}}',
                'condition'             => [
                    'content_type'  => 'custom',
                ],
			]
		);

        $this->end_controls_section();
	}
	
	protected function register_content_toggle_controls() {

        /**
         * Content Tab: Toggle
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_button_settings',
            [
                'label'                 => __( 'Toggle', 'powerpack' ),
            ]
        );
        
        $this->add_control(
            'toggle_source',
            [
                'label'                 => __( 'Toggle Source', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'burger',
                'options'               => [
                    'button'		=> __( 'Button', 'powerpack' ),
                    'burger'		=> __( 'Burger Icon', 'powerpack' ),
                    'element-class'	=> __( 'Element Class', 'powerpack' ),
                    'element-id'	=> __( 'Element ID', 'powerpack' ),
                ],
				'frontend_available'    => true,
            ]
        );

        $this->add_control(
            'toggle_class',
            [
                'label'                 => __( 'Toggle CSS Class', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
                'default'               => '',
				'frontend_available'    => true,
                'condition'             => [
                    'toggle_source'     => 'element-class',
                ],
            ]
        );

        $this->add_control(
            'toggle_id',
            [
                'label'                 => __( 'Toggle CSS ID', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
                'default'               => '',
				'frontend_available'    => true,
                'condition'             => [
                    'toggle_source'     => 'element-id',
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
                'default'               => __( 'Click Here', 'powerpack' ),
                'condition'             => [
                    'toggle_source'     => 'button',
                ],
            ]
        );
		
		$this->add_control(
			'select_button_icon',
			[
				'label'					=> __( 'Button Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'fa4compatibility'		=> 'button_icon',
                'condition'             => [
                    'toggle_source'     => 'button',
                ],
			]
		);
        
        $this->add_control(
            'button_icon_position',
            [
                'label'                 => __( 'Icon Position', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'before',
                'options'               => [
                    'before'    => __( 'Before', 'powerpack' ),
                    'after'     => __( 'After', 'powerpack' ),
                ],
                'prefix_class'          => 'pp-offcanvas-icon-',
                'condition'             => [
                    'toggle_source'     => 'button',
                    'select_button_icon[value]!'	=> '',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'button_icon_spacing',
            [
                'label'                 => __( 'Icon Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => 5,
                    'unit'      => 'px',
                ],
                'range'                 => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 50,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}}.pp-offcanvas-icon-before .pp-offcanvas-toggle-icon' => 'margin-right: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}}.pp-offcanvas-icon-after .pp-offcanvas-toggle-icon' => 'margin-left: {{SIZE}}{{UNIT}}',
                ],
				'condition'             => [
                    'toggle_source'     => 'button',
                    'select_button_icon[value]!'	=> '',
				],
            ]
        );

        $this->add_control(
            'toggle_effect',
            [
                'label'                 => __( 'Animation', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'arrow',
                'options'               => [
                    '' 				=> __( 'None', 'powerpack' ),
                    'arrow' 		=> __( 'Arrow Left', 'powerpack' ),
                    'arrow-r' 		=> __( 'Arrow Right', 'powerpack' ),
                    'arrowalt' 		=> __( 'Arrow Alt Left', 'powerpack' ),
                    'arrowalt-r' 	=> __( 'Arrow Alt Right', 'powerpack' ),
                    'arrowturn' 	=> __( 'Arrow Turn Left', 'powerpack' ),
                    'arrowturn-r' 	=> __( 'Arrow Turn Right', 'powerpack' ),
                    'boring' 		=> __( 'Boring', 'powerpack' ),
                    'collapse' 		=> __( 'Collapse Left', 'powerpack' ),
                    'collapse-r' 	=> __( 'Collapse Right', 'powerpack' ),
                    'elastic' 		=> __( 'Elastic Left', 'powerpack' ),
                    'elastic-r' 	=> __( 'Elastic Right', 'powerpack' ),
                    'emphatic' 		=> __( 'Emphatic Left', 'powerpack' ),
                    'emphatic-r' 	=> __( 'Emphatic Right', 'powerpack' ),
                    'minus' 		=> __( 'Minus', 'powerpack' ),
                    'slider' 		=> __( 'Slider Left', 'powerpack' ),
                    'slider-r' 		=> __( 'Slider Right', 'powerpack' ),
                    'spin' 			=> __( 'Spin Left', 'powerpack' ),
                    'spin-r' 		=> __( 'Spin Right', 'powerpack' ),
                    'spring' 		=> __( 'Spring Left', 'powerpack' ),
                    'spring-r' 		=> __( 'Spring Right', 'powerpack' ),
                    'squeeze' 		=> __( 'Squeeze', 'powerpack' ),
                    'stand' 		=> __( 'Stand Left', 'powerpack' ),
                    'stand-r' 		=> __( 'Stand Right', 'powerpack' ),
                    'vortex' 		=> __( 'Vortex Left', 'powerpack' ),
                    'vortex-r' 		=> __( 'Vortex Right', 'powerpack' ),
                    '3dx'           => __( '3DX', 'powerpack' ),
                    '3dy'           => __( '3DY', 'powerpack' ),
                    '3dxy'          => __( '3DXY', 'powerpack' ),
                ],
                'condition'             => [
                    'toggle_source'     => 'burger',
                ],
            ]
        );

        $this->add_control(
            'burger_label',
            [
                'label'                 => __( 'Label', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
                'default'               => __( 'Menu', 'powerpack' ),
                'condition'             => [
                    'toggle_source'     => 'burger',
                ],
            ]
        );
        
        $this->end_controls_section();
	}
	
	protected function register_content_settings_controls() {

        /**
         * Content Tab: Settings
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_settings',
            [
                'label'                 => __( 'Settings', 'powerpack' ),
            ]
        );
        
        $this->add_control(
			'direction',
			[
				'label'                 => __( 'Direction', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'toggle'                => false,
				'default'               => 'left',
				'options'               => [
					'left'          => [
						'title'     => __( 'Left', 'powerpack' ),
						'icon'      => 'eicon-h-align-left',
					],
					'right'         => [
						'title'     => __( 'Right', 'powerpack' ),
						'icon'      => 'eicon-h-align-right',
					],
					'top'         => [
						'title'     => __( 'Top', 'powerpack' ),
						'icon'      => 'eicon-v-align-top',
					],
					'bottom'         => [
						'title'     => __( 'Bottom', 'powerpack' ),
						'icon'      => 'eicon-v-align-bottom',
					],
				],
				'frontend_available'    => true,
			]
		);

		$this->add_control(
			'content_transition',
			[
				'label'                 => __( 'Content Transition', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'slide',
				'options'               => [
					'slide'        			=> __( 'Slide', 'powerpack' ),
					'reveal'       			=> __( 'Reveal', 'powerpack' ),
					'push'         			=> __( 'Push', 'powerpack' ),
					'slide-along'  	        => __( 'Slide Along', 'powerpack' ),
				],
				'frontend_available'    => true,
				'separator'             => 'before',
			]
		);
        
        $this->add_control(
            'close_button',
            [
                'label'             => __( 'Show Close Button', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => 'yes',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
                'separator'         => 'before',
            ]
        );
        
        $this->add_control(
            'esc_close',
            [
                'label'             => __( 'Esc to Close', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => 'yes',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
            ]
		);
		
		$this->add_control(
            'body_click_close',
            [
                'label'             => __( 'Click anywhere to Close', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => 'yes',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
            ]
        );
		
		$this->add_control(
            'links_click_close',
            [
                'label'             => __( 'Click links to Close', 'powerpack' ),
                'description'		=> __( 'Click on links inside offcanvas body to close the offcanvas bar', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => '',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
            ]
        );

        $this->end_controls_section();
	}
	
	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links('Offcanvas_Content');

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
	
	protected function register_style_offcanvas_controls() {
        /**
         * Style Tab: Offcanvas Bar
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_offcanvas_bar_style',
            [
                'label'                 => __( 'Offcanvas Bar', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_responsive_control(
            'offcanvas_bar_width',
            [
                'label'                 => __( 'Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => 300,
                    'unit'      => 'px',
                ],
                'range'                 => [
                    'px'        => [
                        'min'   => 100,
                        'max'   => 1000,
                        'step'  => 1,
                    ],
                    '%'			=> [
                        'min'   => 1,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '.pp-offcanvas-content-{{ID}}' => 'width: {{SIZE}}{{UNIT}}',
                    '.pp-offcanvas-content-{{ID}}.pp-offcanvas-content-top, .pp-offcanvas-content-{{ID}}.pp-offcanvas-content-bottom' => 'width: 100%; height: {{SIZE}}{{UNIT}}',

                    '.pp-offcanvas-content-reveal.pp-offcanvas-content-{{ID}}-open.pp-offcanvas-content-left .pp-offcanvas-container,
                    .pp-offcanvas-content-push.pp-offcanvas-content-{{ID}}-open.pp-offcanvas-content-left .pp-offcanvas-container,
                    .pp-offcanvas-content-slide-along.pp-offcanvas-content-{{ID}}-open.pp-offcanvas-content-left .pp-offcanvas-container' => 'transform: translate3d({{SIZE}}{{UNIT}}, 0, 0)',

                    '.pp-offcanvas-content-reveal.pp-offcanvas-content-{{ID}}-open.pp-offcanvas-content-right .pp-offcanvas-container,
                    .pp-offcanvas-content-push.pp-offcanvas-content-{{ID}}-open.pp-offcanvas-content-right .pp-offcanvas-container,
                    .pp-offcanvas-content-slide-along.pp-offcanvas-content-{{ID}}-open.pp-offcanvas-content-right .pp-offcanvas-container' => 'transform: translate3d(-{{SIZE}}{{UNIT}}, 0, 0)',

                    '.pp-offcanvas-content-reveal.pp-offcanvas-content-{{ID}}-open.pp-offcanvas-content-top .pp-offcanvas-container,
                    .pp-offcanvas-content-push.pp-offcanvas-content-{{ID}}-open.pp-offcanvas-content-top .pp-offcanvas-container,
                    .pp-offcanvas-content-slide-along.pp-offcanvas-content-{{ID}}-open.pp-offcanvas-content-top .pp-offcanvas-container' => 'transform: translate3d(0, {{SIZE}}{{UNIT}}, 0)',

                    '.pp-offcanvas-content-reveal.pp-offcanvas-content-{{ID}}-open.pp-offcanvas-content-bottom .pp-offcanvas-container,
                    .pp-offcanvas-content-push.pp-offcanvas-content-{{ID}}-open.pp-offcanvas-content-bottom .pp-offcanvas-container,
                    .pp-offcanvas-content-slide-along.pp-offcanvas-content-{{ID}}-open.pp-offcanvas-content-bottom .pp-offcanvas-container' => 'transform: translate3d(0, -{{SIZE}}{{UNIT}}, 0)',
                ]
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'                  => 'offcanvas_bar_bg',
                'label'                 => __( 'Background', 'powerpack' ),
                'types'                 => [ 'classic', 'gradient' ],
                'selector'              => '.pp-offcanvas-content-{{ID}}',
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'offcanvas_bar_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '.pp-offcanvas-content-{{ID}}',
			]
		);

		$this->add_control(
			'offcanvas_bar_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'.pp-offcanvas-content-{{ID}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'offcanvas_bar_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'.pp-offcanvas-content-{{ID}} .pp-offcanvas-body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'offcanvas_bar_box_shadow',
				'selector'              => '.pp-offcanvas-content-{{ID}}',
				'separator'             => 'before',
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
            'section_popup_content_style',
            [
                'label'                 => __( 'Content', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
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
					'.pp-offcanvas-content-{{ID}} .pp-offcanvas-body'   => 'text-align: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
            'widget_heading',
            [
                'label'                 => __( 'Box', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
            ]
        );

        $this->add_control(
            'widgets_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '.pp-offcanvas-content-{{ID}} .pp-offcanvas-custom-widget, .pp-offcanvas-content-{{ID}} .widget' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'widgets_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '.pp-offcanvas-content-{{ID}} .pp-offcanvas-custom-widget, .pp-offcanvas-content-{{ID}} .widget',
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);

		$this->add_control(
			'widgets_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'.pp-offcanvas-content-{{ID}} .pp-offcanvas-custom-widget, .pp-offcanvas-content-{{ID}} .widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);
        
        $this->add_responsive_control(
            'widgets_bottom_spacing',
            [
                'label'                 => __( 'Bottom Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => '20',
                    'unit'      => 'px',
                ],
                'range'                 => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 60,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '.pp-offcanvas-content-{{ID}} .pp-offcanvas-custom-widget, .pp-offcanvas-content-{{ID}} .widget' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
            ]
        );

		$this->add_responsive_control(
			'widgets_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'.pp-offcanvas-content-{{ID}} .pp-offcanvas-custom-widget, .pp-offcanvas-content-{{ID}} .widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
			]
		);
        
        $this->add_control(
            'text_heading',
            [
                'label'                 => __( 'Text', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
            ]
        );

        $this->add_control(
            'content_text_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '.pp-offcanvas-content-{{ID}} .pp-offcanvas-body, .pp-offcanvas-content-{{ID}} .pp-offcanvas-body *:not(.fa):not(.eicon)' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'text_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '.pp-offcanvas-content-{{ID}} .pp-offcanvas-body, .pp-offcanvas-content-{{ID}} .pp-offcanvas-body *:not(.fa):not(.eicon)',
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
            ]
        );
        
        $this->add_control(
            'links_heading',
            [
                'label'                 => __( 'Links', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
            ]
        );

        $this->start_controls_tabs( 'tabs_links_style' );

        $this->start_controls_tab(
            'tab_links_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
            ]
        );

        $this->add_control(
            'content_links_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '.pp-offcanvas-content-{{ID}} .pp-offcanvas-body a' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'links_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '.pp-offcanvas-content-{{ID}} .pp-offcanvas-body a',
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_links_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
            ]
        );

        $this->add_control(
            'content_links_color_hover',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '.pp-offcanvas-content-{{ID}} .pp-offcanvas-body a:hover' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'content_type'      => [ 'sidebar', 'custom' ],
				],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();
	}
	
	protected function register_style_toggle_controls() {

        /**
         * Style Tab: Toggle
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_toggle_button_style',
            [
                'label'                 => __( 'Toggle', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_responsive_control(
			'button_align',
			[
				'label'                 => __( 'Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'default'               => 'left',
				'options'               => [
					'left'          => [
						'title'     => __( 'Left', 'powerpack' ),
						'icon'      => 'eicon-h-align-left',
					],
					'center'        => [
						'title'     => __( 'Center', 'powerpack' ),
						'icon'      => 'eicon-h-align-center',
					],
					'right'         => [
						'title'     => __( 'Right', 'powerpack' ),
						'icon'      => 'eicon-h-align-right',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-offcanvas-toggle-wrap'   => 'text-align: {{VALUE}};',
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
                    'toggle_source'     => 'button',
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
                    '{{WRAPPER}} .pp-offcanvas-toggle' => 'background-color: {{VALUE}}',
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
                    '{{WRAPPER}} .pp-offcanvas-toggle' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-offcanvas-toggle svg' => 'fill: {{VALUE}}',
                    '{{WRAPPER}} .pp-hamburger-inner, {{WRAPPER}} .pp-hamburger-inner::before, {{WRAPPER}} .pp-hamburger-inner::after' => 'background-color: {{VALUE}}',
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
				'selector'              => '{{WRAPPER}} .pp-offcanvas-toggle',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-offcanvas-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pp-offcanvas-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-offcanvas-toggle',
			]
		);
        
        $this->add_control(
            'toggle_icon_heading',
            [
                'label'                 => __( 'Icon', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
				'condition'             => [
					'toggle_source'     => 'burger',
				],
            ]
        );
        
        $this->add_responsive_control(
            'toggle_icon_size',
            [
                'label'                 => __( 'Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => 1,
                ],
                'range'                 => [
                    'px'        => [
                        'min'   => 0.1,
                        'max'   => 3,
                        'step'  => 0.01,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-hamburger-box' => 'font-size: {{SIZE}}em',
                ],
				'condition'             => [
					'toggle_source'     => 'burger',
				],
            ]
        );
        
        $this->add_control(
            'toggle_label_heading',
            [
                'label'                 => __( 'Label', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
				'condition'             => [
					'toggle_source'     => 'burger',
					'burger_label!'     => '',
				],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'button_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-offcanvas-toggle',
				'condition'             => [
					'toggle_source'     => ['button', 'burger'],
					'burger_label!'     => '',
				],
            ]
        );
        
        $this->add_responsive_control(
            'toggle_label_spacing',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => '',
                ],
                'range'                 => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 30,
                        'step'  => 1,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-hamburger-label' => 'margin-left: {{SIZE}}{{UNIT}}',
                ],
				'condition'             => [
					'toggle_source'     => 'burger',
					'burger_label!'     => '',
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
                    '{{WRAPPER}} .pp-offcanvas-toggle:hover' => 'background-color: {{VALUE}}',
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
                    '{{WRAPPER}} .pp-offcanvas-toggle:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-offcanvas-toggle:hover svg' => 'fill: {{VALUE}}',
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
                    '{{WRAPPER}} .pp-offcanvas-toggle:hover' => 'border-color: {{VALUE}}',
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
				'selector'              => '{{WRAPPER}} .pp-offcanvas-toggle:hover',
			]
		);

		$this->end_controls_tab();

        $this->end_controls_tabs();
        
		$this->end_controls_section();
	}
	
	protected function register_style_close_button_controls() {
		
		/**
         * Style Tab: Close Button
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_close_button_style',
            [
                'label'                 => __( 'Close Button', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'close_button' => 'yes',
				],
            ]
		);
        
        $this->add_control(
			'close_button_align',
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
				],
				'default'               => '',
				'selectors'             => [
					'.pp-offcanvas-content-{{ID}} .pp-offcanvas-header'   => 'text-align: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'select_close_button_icon',
			[
				'label'					=> __( 'Button Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'fa4compatibility'		=> 'close_button_icon',
				'default'				=> [
					'value'		=> 'fas fa-times',
					'library'	=> 'fa-solid',
				],
				'recommended'			=> [
					'fa-regular' => [
						'times-circle',
					],
					'fa-solid' => [
						'times',
						'times-circle',
					],
				],
                'condition'             => [
                    'close_button' => 'yes',
                ],
			]
		);

		$this->add_control(
            'close_button_text_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '.pp-offcanvas-close-{{ID}}' => 'color: {{VALUE}}',
                    '.pp-offcanvas-close-{{ID}} svg' => 'fill: {{VALUE}}',
                ],
				'condition'             => [
					'close_button' => 'yes',
				],
            ]
        );
        
        $this->add_responsive_control(
            'close_button_size',
            [
                'label'                 => __( 'Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => '28',
                    'unit'      => 'px',
                ],
                'range'                 => [
                    'px'        => [
                        'min'   => 10,
                        'max'   => 80,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '.pp-offcanvas-content-{{ID}} .pp-offcanvas-close-{{ID}}' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
				'condition'             => [
					'close_button' => 'yes',
				],
            ]
        );
		
		$this->end_controls_section();
	}
	
	protected function register_style_overlay_controls() {
		
		/**
         * Style Tab: Overlay
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_overlay_style',
            [
                'label'                 => __( 'Overlay', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
		);

		$this->add_control(
            'overlay_bg_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '.pp-offcanvas-content-{{ID}}-open .pp-offcanvas-container:after' => 'background: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'overlay_opacity',
            [
                'label'                 => __( 'Opacity', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 1,
                        'step'  => 0.01,
                    ],
                ],
				'selectors'             => [
					'.pp-offcanvas-content-{{ID}}-open .pp-offcanvas-container:after' => 'opacity: {{SIZE}};',
				],
            ]
        );
        
		$this->end_controls_section();

    }

    /**
	 * Render offcanvas content widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render() {
        $settings = $this->get_settings_for_display();

        $settings_attr = array(
            'toggle_source'		=> esc_attr( $settings['toggle_source'] ),
            'toggle_id'			=> esc_attr( $settings['toggle_id'] ),
            'toggle_class'		=> esc_attr( $settings['toggle_class'] ),
            'content_id'		=> esc_attr( $this->get_id() ),
			'transition'		=> esc_attr( $settings['content_transition'] ),
			'direction'		    => esc_attr( $settings['direction'] ),
			'esc_close'			=> esc_attr( $settings['esc_close'] ),
			'body_click_close'	=> esc_attr( $settings['body_click_close'] ),
			'links_click_close'	=> esc_attr( $settings['links_click_close'] )
        );

        $this->add_render_attribute( 'content-wrap', 'class', 'pp-offcanvas-content-wrap');

        $this->add_render_attribute( 'content-wrap', 'data-settings', htmlspecialchars( json_encode( $settings_attr ) ) );

        $this->add_render_attribute( 'content', 'class',
            [
                'pp-offcanvas-content',
				'pp-offcanvas-content-' . $this->get_id(),
				'pp-offcanvas-' . $settings_attr['transition'],
				'elementor-element-' . $this->get_id(),
            ]
        );

        $this->add_render_attribute( 'content', 'class', 'pp-offcanvas-content-' . $settings['direction'] );
        
        $this->add_render_attribute( 'toggle-button', 'class', [
                'pp-offcanvas-toggle',
                'pp-offcanvas-toggle-' . esc_attr( $this->get_id() ),
                'elementor-button',
                'elementor-size-' . $settings['button_size'],
            ]
        );

        if ( $settings['button_animation'] ) {
            $this->add_render_attribute( 'toggle-button', 'class', 'elementor-animation-' . $settings['button_animation'] );
        }
        
        $this->add_render_attribute( 'hamburger', 'class', [
                'pp-offcanvas-toggle',
                'pp-offcanvas-toggle-' . esc_attr( $this->get_id() ),
                'pp-button',
                'pp-hamburger',
                'pp-hamburger--' . $settings['toggle_effect'],
            ]
        );
        ?>
        
        <div <?php echo $this->get_render_attribute_string( 'content-wrap' ); ?>>

            <?php
				if ( $settings['toggle_source'] == 'button' || $settings['toggle_source'] == 'burger' ) {
					// Toggle
					$this->render_toggle();
				} else {
					$placeholder = __( 'You have selected to open offcanvas bar using another element. This placeholder will not be shown on the live page.', 'powerpack' );
					
					echo $this->render_editor_placeholder( [
						'body' => $placeholder,
					] );
				}
            ?>
            
			<div <?php echo $this->get_render_attribute_string( 'content' ); ?>>
				<?php echo $this->render_close_button(); ?>
				<div class="pp-offcanvas-body">
                <?php
                    if ( $settings['content_type'] == 'sidebar' ) {

                        $this->render_sidebar();

                    } elseif ( $settings['content_type'] == 'custom' ) {

                        $this->render_custom_content();

                    } elseif ( $settings['content_type'] == 'section' && !empty( $settings['saved_section'] ) ) {

                        echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['saved_section'] );

                    } elseif ( $settings['content_type'] == 'template' && !empty( $settings['templates'] ) ) {
                        
                        echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['templates'] );
                        
                    } elseif ( $settings['content_type'] == 'widget' && !empty( $settings['saved_widget'] ) ) {

                        echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['saved_widget'] );

                    }
				?>
				</div>
            </div>
        </div>
        <?php
    }
    
    /**
	 * Render toggle output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_toggle() {
        $settings = $this->get_settings_for_display();
		
		if ( ! isset( $settings['button_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['button_icon'] = '';
		}

		$has_icon = ! empty( $settings['button_icon'] );
		
		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['button_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}
		
		if ( ! $has_icon && ! empty( $settings['select_button_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['select_button_icon'] );
		$is_new = ! isset( $settings['button_icon'] ) && Icons_Manager::is_migration_allowed();
        
        if ( $settings['toggle_source'] == 'button' ) {
            if ( $settings['button_text'] != '' || $has_icon ) { ?>
                <div class="pp-offcanvas-toggle-wrap">
                    <div <?php echo $this->get_render_attribute_string( 'toggle-button' ); ?>>
                        <?php if ( $has_icon ) { ?>
                            <span class="pp-offcanvas-toggle-icon pp-icon pp-no-trans">
								<?php
									if ( $is_new || $migrated ) {
										Icons_Manager::render_icon( $settings['select_button_icon'], [ 'aria-hidden' => 'true' ] );
									} elseif ( ! empty( $settings['button_icon'] ) ) {
										?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
									}
								?>
							</span>
                        <?php } ?>
                        <span class="pp-offcanvas-toggle-text">
                            <?php echo $settings['button_text']; ?>
                        </span>
                    </div>
                </div>
            <?php }
        } elseif ( $settings['toggle_source'] == 'burger' ) { ?>
            <div class="pp-offcanvas-toggle-wrap">
                <div <?php echo $this->get_render_attribute_string( 'hamburger' ); ?>>
                    <span class="pp-hamburger-box">
                        <span class="pp-hamburger-inner"></span>
                    </span>
                    <?php if ( $settings['burger_label'] ) { ?>
                        <span class="pp-hamburger-label">
                            <?php echo $settings['burger_label']; ?>
                        </span>
                    <?php } ?>
                </div>
            </div>
        <?php }
    }
    
    /**
	 * Render sidebar content output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_close_button() {
        $settings = $this->get_settings_for_display();
        
        if ( $settings['close_button'] != 'yes' ) {
            return;
        }
		
		if ( ! isset( $settings['close_button_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['close_button_icon'] = '';
		}

		$has_icon = ! empty( $settings['close_button_icon'] );
		
		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['close_button_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}
		
		if ( ! $has_icon && ! empty( $settings['select_close_button_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['select_close_button_icon'] );
		$is_new = ! isset( $settings['close_button_icon'] ) && Icons_Manager::is_migration_allowed();
        
        $this->add_render_attribute( 'close-button', 'class',
            [
                'pp-icon',
                'pp-offcanvas-close',
				'pp-offcanvas-close-' . $this->get_id()
            ]
        );
        
        $this->add_render_attribute( 'close-button', 'role', 'button' );
        ?>
        <div class="pp-offcanvas-header">
            <div <?php echo $this->get_render_attribute_string( 'close-button' ); ?>>
				<?php
					if ( $is_new || $migrated ) {
						Icons_Manager::render_icon( $settings['select_close_button_icon'], [ 'aria-hidden' => 'true' ] );
					} elseif ( ! empty( $settings['close_button_icon'] ) ) {
						?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
					}
				?>
            </div>
        </div>
        <?php
    }
    
    /**
	 * Render sidebar content output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_sidebar() {
        $settings = $this->get_settings_for_display();
        
        $sidebar = $settings['sidebar'];

        if ( empty( $sidebar ) ) {
            return;
        }

        dynamic_sidebar( $sidebar );
    }
    
    /**
	 * Render saved template output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_custom_content() {
        $settings = $this->get_settings_for_display();
        
        foreach ( $settings['custom_content'] as $index => $item ) :
            ?>
            <div class="pp-offcanvas-custom-widget">
                <h3 class="pp-offcanvas-widget-title">
                    <?php echo $item['title']; ?>
                </h3>
                <div class="pp-offcanvas-widget-content">
                    <?php echo $item['description']; ?>
                </div>
            </div>
            <?php
        endforeach;
    }
    
    /**
	 * Render saved template output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_saved_template() {
        $settings = $this->get_settings_for_display();
        
        if ( $settings['content_type'] == 'section' && !empty( $settings['saved_section'] ) ) {
            //$pp_template_id = $settings['templates'];
            
            echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['saved_section'] );
            
            //echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $pp_template_id );
        } elseif ( $settings['content_type'] == 'template' && !empty( $settings['templates'] ) ) {

            echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['templates'] );

        } elseif ( $settings['content_type'] == 'widget' && !empty( $settings['saved_widget'] ) ) {

            echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $settings['saved_widget'] );

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
	 * Render offcanvas content widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
    protected function _content_template() {}

}