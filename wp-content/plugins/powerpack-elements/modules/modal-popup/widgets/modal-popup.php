<?php
namespace PowerpackElements\Modules\ModalPopup\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
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
 * Popup Box Widget
 */
class Modal_Popup extends Powerpack_Widget {
    
    /**
	 * Retrieve popup box widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return parent::get_widget_name( 'Popup_Box' );
    }

    /**
	 * Retrieve popup box widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
    public function get_title() {
        return parent::get_widget_title( 'Popup_Box' );
    }

    /**
	 * Retrieve the list of categories the popup box widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Popup_Box' );
    }

    /**
	 * Retrieve popup box widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
    public function get_icon() {
        return parent::get_widget_icon( 'Popup_Box' );
    }

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.3.4
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Popup_Box' );
	}
    
    /**
	 * Retrieve the list of scripts the popup box widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_script_depends() {
        return [
            'jquery-cookie',
            'magnific-popup',
            'powerpack-frontend'
        ];
    }
    
    /**
	 * Retrieve the list of scripts the image comparison widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_style_depends() {
        return [
            'magnific-popup',
        ];
    }

    protected function _register_controls() {
		
		/* Content Tab */
		$this->register_content_content_controls();
		$this->register_content_layout_controls();
		$this->register_content_trigger_controls();
		$this->register_content_settings_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_popup_controls();
		$this->register_style_overlay_controls();
		$this->register_style_title_controls();
		$this->register_style_content_controls();
		$this->register_style_trigger_icon_controls();
		$this->register_style_trigger_button_controls();
		$this->register_style_close_button_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/
        
	protected function register_content_content_controls() {
        /**
         * Content Tab: Content
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_content',
            [
                'label'                 => __( 'Content', 'powerpack' ),
            ]
        );
        
        $this->add_control(
            'popup_title',
            [
                'label'             => __( 'Enable Title', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => 'yes',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
            ]
        );

        $this->add_control(
            'title',
            [
                'label'                 => __( 'Title', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
                'default'               => __( 'Modal Title', 'powerpack' ),
                'condition'             => [
                    'popup_title'  => 'yes',
                ],
            ]
        );

        $this->add_control(
            'popup_type',
            [
                'label'                 => __( 'Type', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => [
                    'image'         => __( 'Image', 'powerpack' ),
                    'link'          => __( 'Link (Video/Map/Page)', 'powerpack' ),
                    'content'       => __( 'Content', 'powerpack' ),
                    'template'      => __( 'Saved Templates', 'powerpack' ),
                    'custom-html'   => __( 'Custom HTML', 'powerpack' ),
                ],
                'default'               => 'image',
            ]
        );
        
        $this->add_control(
            'image',
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
					'popup_type'    => 'image',
				],
            ]
        );
        
        $this->add_control(
            'popup_link',
            [
                'label'                 => __( 'Enter URL', 'powerpack' ),
                'type'                  => Controls_Manager::URL,
				'dynamic'               => [
					'active'   => true,
				],
                'default'               => [
                    'url' => 'https://',
                ],
                'show_external'     => false, // Show the 'open in new tab' button.
				'condition'             => [
					'popup_type'    => 'link',
				],
            ]
        );
        
        $this->add_control(
            'content',
            [
                'label'                 => __( 'Content', 'powerpack' ),
                'type'                  => Controls_Manager::WYSIWYG,
                'default'               => __( 'I am the popup Content', 'powerpack' ),
				'dynamic'               => [
					'active'   => true,
				],
				'condition'             => [
					'popup_type'    => 'content',
				],
            ]
        );

        /*$this->add_control(
            'templates',
            [
                'label'                 => __( 'Choose Template', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => pp_get_page_templates(),
				'condition'             => [
					'popup_type'    => 'template',
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
					'popup_type'    => 'template',
				],
			]
		);
        
        $this->add_control(
            'custom_html',
            [
                'label'                 => __( 'Custom HTML', 'powerpack' ),
                'type'                  => Controls_Manager::CODE,
                'language'          => 'html',
				'condition'             => [
					'popup_type'    => 'custom-html',
				],
            ]
        );
        
        $this->end_controls_section();
	}
     
	protected function register_content_layout_controls() {
        /**
         * Content Tab: Layout
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_layout',
            [
                'label'                 => __( 'Layout', 'powerpack' ),
            ]
        );

        $this->add_control(
            'layout_type',
            [
                'label'                 => __( 'Layout', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => [
                    'standard'      => __( 'Standard', 'powerpack' ),
                    'fullscreen'    => __( 'Fullscreen', 'powerpack' ),
                ],
                'default'               => 'standard',
				'frontend_available'	=> true
            ]
        );
        
        $this->add_responsive_control(
            'popup_width',
            [
                'label'                 => __( 'Width', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => '550',
                    'unit'      => 'px',
                ],
                'range'                 => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 1920,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '.pp-modal-popup-window-{{ID}}' => 'width: {{SIZE}}{{UNIT}}',
                ],
				'condition'             => [
					'layout_type'    => 'standard',
				],
            ]
        );
        
        $this->add_control(
            'auto_height',
            [
                'label'             => __( 'Auto Height', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => 'yes',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
				'condition'             => [
					'layout_type'  => 'standard',
				],
            ]
        );
        
        $this->add_responsive_control(
            'popup_height',
            [
                'label'                 => __( 'Height', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => '450',
                    'unit'      => 'px',
                ],
                'range'                 => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 1000,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '.pp-modal-popup-window-{{ID}}' => 'height: {{SIZE}}{{UNIT}}',
                ],
				'condition'             => [
					'auto_height!' => 'yes',
					'layout_type'  => 'standard',
				],
            ]
        );

        $this->end_controls_section();
	}
     
	protected function register_content_trigger_controls() {
        /**
         * Content Tab: Trigger
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_trigger',
            [
                'label'                 => __( 'Trigger', 'powerpack' ),
            ]
        );

		$this->add_control(
			'trigger',
			[
				'label'                 => __( 'Trigger', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'on-click',
				'options'               => [
					'on-click'     => __( 'On Click', 'powerpack' ),
					'page-load'    => __( 'Time Delayed', 'powerpack' ),
					'exit-intent'  => __( 'Exit Intent', 'powerpack' ),
					'other'        => __( 'Element Class/ID', 'powerpack' ),
				],
				'frontend_available'	=> true
			]
		);

		$this->add_control(
			'trigger_type',
			[
				'label'                 => __( 'Type', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'button',
				'options'               => [
					'button'       => __( 'Button', 'powerpack' ),
					'icon'         => __( 'Icon', 'powerpack' ),
					'image'        => __( 'Image', 'powerpack' ),
				],
				'condition'             => [
					'trigger'    => 'on-click',
				],
			]
		);

        $this->add_control(
            'button_text',
            [
                'label'                 => __( 'Button Text', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'default'               => __( 'Click Here', 'powerpack' ),
                'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
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
                    'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
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
                    'after'     => __( 'After', 'powerpack' ),
                    'before'    => __( 'Before', 'powerpack' ),
                ],
                'condition'             => [
                    'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
                ],
            ]
        );
		
		$this->add_control(
			'select_trigger_icon',
			[
				'label'					=> __( 'Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'fa4compatibility'		=> 'trigger_icon',
                'condition'             => [
                    'trigger'       => 'on-click',
                    'trigger_type'  => 'icon',
                ],
			]
		);
        
        $this->add_control(
            'trigger_image',
            [
                'label'                 => __( 'Choose Image', 'powerpack' ),
                'type'                  => Controls_Manager::MEDIA,
                'default'               => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'image',
				],
            ]
        );

        $this->add_control(
            'delay',
            [
                'label'                 => __( 'Delay', 'powerpack' ),
                'title'                 => __( 'seconds', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'default'               => '1',
                'condition'             => [
					'trigger'       => 'page-load',
                ],
            ]
        );

        $this->add_control(
            'display_after_page_load',
            [
                'label'                 => __( 'Display After', 'powerpack' ),
                'title'                 => __( 'day(s)', 'powerpack' ),
                'description'           => __( 'If a user closes the modal box, it will be displayed only after the defined day(s)', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'default'               => '1',
                'condition'             => [
					'trigger'       => 'page-load',
                ],
            ]
        );

        $this->add_control(
            'display_after_exit_intent',
            [
                'label'                 => __( 'Display After', 'powerpack' ),
                'title'                 => __( 'day(s)', 'powerpack' ),
                'description'           => __( 'If a user closes the modal box, it will be displayed only after the defined day(s)', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'default'               => '1',
                'condition'             => [
					'trigger'       => 'exit-intent',
                ],
            ]
        );

        $this->add_control(
            'element_identifier',
            [
                'label'                 => __( 'Element CSS Class/ID', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'label_block'           => true,
                'default'               => '.pp-modal-popup-link',
				'dynamic'               => [
					'active'   => true,
				],
                'condition'             => [
					'trigger'       => 'other',
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
            'prevent_scroll',
            [
                'label'             => __( 'Prevent Page Scroll', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => 'yes',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
				'frontend_available'	=> true
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
				'frontend_available'	=> true
            ]
        );
        
        $this->add_control(
            'esc_exit',
            [
                'label'					=> __( 'Close on Esc Keypress', 'powerpack' ),
                'description'			=> __( 'Close the popup when user presses the Esc key', 'powerpack' ),
                'type'					=> Controls_Manager::SWITCHER,
                'default'				=> 'yes',
                'label_on'				=> __( 'Yes', 'powerpack' ),
                'label_off'				=> __( 'No', 'powerpack' ),
                'return_value'			=> 'yes',
				'frontend_available'	=> true
            ]
        );
        
        $this->add_control(
            'click_exit',
            [
                'label'					=> __( 'Close on Overlay Click', 'powerpack' ),
                'description'			=> __( 'Close the popup when user clicks on the overlay', 'powerpack' ),
                'type'					=> Controls_Manager::SWITCHER,
                'default'				=> 'yes',
                'label_on'				=> __( 'Yes', 'powerpack' ),
                'label_off'				=> __( 'No', 'powerpack' ),
                'return_value'			=> 'yes',
				'frontend_available'	=> true
            ]
        );
        
        $this->add_control(
            'content_close',
            [
                'label'             => __( 'Close on Content Click', 'powerpack' ),
                'description'       => __( 'Close the popup when user clicks on content of it', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => '',
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
				'frontend_available'	=> true
            ]
        );

		$this->add_control(
			'popup_disable_on',
			[
				'label'                 => __( 'Disable On', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => '',
				'options'               => [
					''			=> __( 'None', 'powerpack' ),
					'tablet'	=> __( 'Mobile & Tablet', 'powerpack' ),
					'mobile'	=> __( 'Mobile', 'powerpack' ),
				],
				'separator'             => 'before',
			]
		);
        
        $this->add_control(
            'popup_animation_in',
            [
                'label'                 => __( 'Animation', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'mfp-zoom-in',
                'options'               => [
                    ''					=> __( 'None', 'powerpack' ),
                    'mfp-zoom-in'       => __( 'Zoom In', 'powerpack' ),
                    'mfp-zoom-out'      => __( 'Zoom Out', 'powerpack' ),
                    'mfp-3d-unfold'     => __( '3D Unfold', 'powerpack' ),
                    'mfp-newspaper'     => __( 'Newspaper', 'powerpack' ),
                    'mfp-move-from-top' => __( 'Move From Top', 'powerpack' ),
                    'mfp-move-left'     => __( 'Move Left', 'powerpack' ),
                    'mfp-move-right'    => __( 'Move Right', 'powerpack' ),
                ],
				'frontend_available'	=> true
            ]
        );

        $this->end_controls_section();
	}
	
	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links('Popup_Box');

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
        
	protected function register_style_popup_controls() {
        /**
         * Style Tab: Popup
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_popup_window_style',
            [
                'label'                 => __( 'Popup', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'                  => 'popup_bg',
                'label'                 => __( 'Background', 'powerpack' ),
                'types'                 => [ 'classic', 'gradient' ],
                'selector'              => '.pp-modal-popup-window-{{ID}}',
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'popup_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '.pp-modal-popup-window-{{ID}}',
			]
		);

		$this->add_control(
			'popup_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'.pp-modal-popup-window-{{ID}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'popup_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'.pp-modal-popup-window-{{ID}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'popup_box_shadow',
				'selector'              => '.pp-modal-popup-window-{{ID}}',
				'separator'             => 'before',
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
            'section_popup_overlay_style',
            [
                'label'                 => __( 'Overlay', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'overlay_switch',
            [
                'label'             => __( 'Overlay', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => 'yes',
                'label_on'          => __( 'Show', 'powerpack' ),
                'label_off'         => __( 'Hide', 'powerpack' ),
                'return_value'      => 'yes',
				'frontend_available'	=> true
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'                  => 'overlay_bg',
                'label'                 => __( 'Background', 'powerpack' ),
                'types'                 => [ 'classic', 'gradient' ],
                'exclude'               => [ 'image' ],
                'selector'              => '.pp-modal-popup-{{ID}}',
				'condition'             => [
					'overlay_switch'   => 'yes',
				],
            ]
        );
        
        $this->end_controls_section();
	}

	protected function register_style_title_controls() {
        /**
         * Style Tab: Title
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_title_style',
            [
                'label'                 => __( 'Title', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'popup_title'   => 'yes',
				],
            ]
        );
        
        $this->add_responsive_control(
			'title_align',
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
					'.pp-modal-popup-window-{{ID}} .pp-popup-header .pp-popup-title' => 'text-align: {{VALUE}};',
				],
				'condition'             => [
					'popup_title'   => 'yes',
				],
			]
		);

        $this->add_control(
            'title_bg',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '.pp-modal-popup-window-{{ID}} .pp-popup-header .pp-popup-title' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
					'popup_title'   => 'yes',
				],
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '.pp-modal-popup-window-{{ID}} .pp-popup-header .pp-popup-title' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'popup_title'   => 'yes',
				],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'title_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '.pp-modal-popup-window-{{ID}} .pp-popup-header .pp-popup-title',
				'condition'             => [
					'popup_title'   => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'.pp-modal-popup-window-{{ID}} .pp-popup-header .pp-popup-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'popup_title'   => 'yes',
				],
			]
		);
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'title_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '.pp-modal-popup-window-{{ID}} .pp-popup-header .pp-popup-title',
				'condition'             => [
					'popup_title'   => 'yes',
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
            'section_popup_content_style',
            [
                'label'                 => __( 'Content', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'popup_type'   => 'content',
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
					'.pp-modal-popup-window-{{ID}} .pp-popup-content'   => 'text-align: {{VALUE}};',
				],
				'condition'             => [
					'popup_type'   => 'content',
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
                    '.pp-modal-popup-window-{{ID}} .pp-popup-content' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'popup_type'   => 'content',
				],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'content_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '.pp-modal-popup-window-{{ID}} .pp-popup-content',
				'condition'             => [
					'popup_type'   => 'content',
				],
            ]
        );

        $this->end_controls_section();
	}

	protected function register_style_trigger_icon_controls() {
        /**
         * Style Tab: Trigger Icon
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_icon_style',
            [
                'label'                 => __( 'Trigger Icon/Image', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'trigger'       => 'on-click',
					'trigger_type!' => 'button',
				],
            ]
        );
        
        $this->add_responsive_control(
			'icon_align',
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
					'{{WRAPPER}} .pp-modal-popup-wrap .pp-modal-popup'   => 'text-align: {{VALUE}};',
				],
				'condition'             => [
					'trigger'       => 'on-click',
					'trigger_type'  => ['icon','image'],
				],
			]
		);

        $this->add_control(
            'icon_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-trigger-icon' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-trigger-icon svg' => 'fill: {{VALUE}}',
                ],
				'condition'             => [
					'trigger'       => 'on-click',
					'trigger_type'  => 'icon',
				],
            ]
        );
        
        $this->add_responsive_control(
            'icon_size',
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
                    '{{WRAPPER}} .pp-trigger-icon' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
				'condition'             => [
					'trigger'       => 'on-click',
					'trigger_type'  => 'icon',
				],
            ]
        );
        
        $this->add_responsive_control(
            'icon_image_width',
            [
                'label'                 => __( 'Width', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px'        => [
                        'min'   => 10,
                        'max'   => 1200,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-trigger-image' => 'width: {{SIZE}}{{UNIT}}',
                ],
				'condition'             => [
					'trigger'       => 'on-click',
					'trigger_type'  => 'image',
				],
            ]
        );
        
        $this->end_controls_section();
	}

	protected function register_style_trigger_button_controls() {
        /**
         * Style Tab: Trigger Button
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_modal_button_style',
            [
                'label'                 => __( 'Trigger Button', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
				],
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
					'{{WRAPPER}} .pp-modal-popup-wrap .pp-modal-popup'   => 'text-align: {{VALUE}};',
				],
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
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
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
				],
			]
		);

        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
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
                    '{{WRAPPER}} .pp-modal-popup-button' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
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
                    '{{WRAPPER}} .pp-modal-popup-button' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-modal-popup-button svg' => 'fill: {{VALUE}}',
                ],
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
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
				'selector'              => '{{WRAPPER}} .pp-modal-popup-button',
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
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
					'{{WRAPPER}} .pp-modal-popup-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
				],
			]
		);
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'button_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-modal-popup-button',
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
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
					'{{WRAPPER}} .pp-modal-popup-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-modal-popup-button',
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
				],
			]
		);
        
        $this->add_control(
            'button_icon_heading',
            [
                'label'                 => __( 'Button Icon', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
                ],
            ]
        );

		$this->add_responsive_control(
			'button_icon_margin',
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
					'{{WRAPPER}} .pp-modal-popup-button .pp-button-icon' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
                'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
                ],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
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
                    '{{WRAPPER}} .pp-modal-popup-button:hover' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
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
                    '{{WRAPPER}} .pp-modal-popup-button:hover' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
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
                    '{{WRAPPER}} .pp-modal-popup-button:hover' => 'border-color: {{VALUE}}',
                ],
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
				],
            ]
        );

		$this->add_control(
			'button_animation',
			[
				'label'                 => __( 'Animation', 'powerpack' ),
				'type'                  => Controls_Manager::HOVER_ANIMATION,
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-modal-popup-button:hover',
				'condition'             => [
					'trigger'       => 'on-click',
                    'trigger_type'  => 'button',
					'button_text!'  => '',
				],
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
					'close_button'   => 'yes',
				],
            ]
        );

		$this->add_control(
			'close_button_position',
			[
				'label'                 => __( 'Position', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'win-top-right',
				'options'               => [
					'box-top-right'    => __( 'Box - Top Right', 'powerpack' ),
					'box-top-left'     => __( 'Box - Top Left', 'powerpack' ),
					'win-top-right'    => __( 'Window - Top Right', 'powerpack' ),
					'win-top-left'     => __( 'Window - Top Left', 'powerpack' ),
				],
				'frontend_available'	=> true,
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_control(
			'close_button_weight',
			[
				'label'                 => __( 'Weight', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'normal',
				'options'               => [
					'normal'   => __( 'Normal', 'powerpack' ),
					'bold'     => __( 'Bold', 'powerpack' ),
				],
				'condition'             => [
					'close_button'   => 'yes',
				],
                'selectors'             => [
                    '.pp-modal-popup-window-{{ID}} .mfp-close' => 'font-weight: {{VALUE}}',
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
                    '.pp-modal-popup-window-{{ID}} .mfp-close' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
				'condition'             => [
					'close_button'   => 'yes',
				],
            ]
        );

        $this->start_controls_tabs( 'tabs_close_button_style' );

        $this->start_controls_tab(
            'tab_close_button_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
				'condition'             => [
					'close_button'   => 'yes',
				],
            ]
        );

        $this->add_control(
            'close_button_color_normal',
            [
                'label'                 => __( 'Icon Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '.pp-modal-popup-window-{{ID}} .mfp-close' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'close_button'   => 'yes',
				],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'                  => 'close_button_bg',
                'label'                 => __( 'Background', 'powerpack' ),
                'types'                 => [ 'classic', 'gradient' ],
				'exclude'               => [ 'image' ],
                'selector'              => '.pp-modal-popup-window-{{ID}} .mfp-close',
				'condition'             => [
					'close_button'   => 'yes',
				],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'close_button_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '.pp-modal-popup-window-{{ID}} .mfp-close',
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_control(
			'close_button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'.pp-modal-popup-window-{{ID}} .mfp-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_margin',
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
					'.pp-modal-popup-window-{{ID}} .mfp-close' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_padding',
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
					'.pp-modal-popup-window-{{ID}} .mfp-close' => 'padding-top: {{TOP}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
				],
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_close_button_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
				'condition'             => [
					'close_button'   => 'yes',
				],
            ]
        );

        $this->add_control(
            'close_button_color_hover',
            [
                'label'                 => __( 'Icon Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '.pp-modal-popup-window-{{ID}} .mfp-close:hover' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'close_button'   => 'yes',
				],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'                  => 'close_button_bg_hover',
                'label'                 => __( 'Background', 'powerpack' ),
                'types'                 => [ 'classic', 'gradient' ],
                'selector'              => '.pp-modal-popup-window-{{ID}} .mfp-close:hover',
				'condition'             => [
					'close_button'   => 'yes',
				],
            ]
        );

        $this->add_control(
            'close_button_border_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '.pp-modal-popup-window-{{ID}} .mfp-close:hover' => 'border-color: {{VALUE}}',
                ],
				'condition'             => [
					'close_button'   => 'yes',
				],
            ]
        );

		$this->add_control(
			'close_button_border_radius_hover',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'.pp-modal-popup-window-{{ID}} .mfp-close:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->end_controls_section();

    }

    /**
	 * Render popup box button icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_button_icon() {
        $settings = $this->get_settings_for_display();
		
		// Trigger Button Icon
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
		
		if ( $has_icon ) {
			?>
			<span class="pp-button-icon pp-icon">
				<?php
				if ( $is_new || $migrated ) {
					Icons_Manager::render_icon( $settings['select_button_icon'], [ 'aria-hidden' => 'true' ] );
				} elseif ( ! empty( $settings['button_icon'] ) ) {
					?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
				}
				?>
			</span>
			<?php
		}
    }

    /**
	 * Render popup box button icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_trigger_icon() {
        $settings = $this->get_settings_for_display();
		
		// Trigger Button Icon
		if ( ! isset( $settings['trigger_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['trigger_icon'] = '';
		}

		$has_icon = ! empty( $settings['trigger_icon'] );
		
		if ( $has_icon ) {
			$this->add_render_attribute( 'trigger-i', 'class', $settings['trigger_icon'] );
			$this->add_render_attribute( 'trigger-i', 'aria-hidden', 'true' );
		}
		
		if ( ! $has_icon && ! empty( $settings['select_trigger_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['select_trigger_icon'] );
		$is_new = ! isset( $settings['trigger_icon'] ) && Icons_Manager::is_migration_allowed();
		
		if ( $has_icon ) {
			?>
			<span class="pp-trigger-icon pp-icon pp-modal-popup-link pp-modal-popup-link-<?php echo esc_attr( $this->get_id() ); ?>">
				<?php
				if ( $is_new || $migrated ) {
					Icons_Manager::render_icon( $settings['select_trigger_icon'], [ 'aria-hidden' => 'true' ] );
				} elseif ( ! empty( $settings['trigger_icon'] ) ) {
					?><i <?php echo $this->get_render_attribute_string( 'trigger-i' ); ?>></i><?php
				}
				?>
			</span>
			<?php
		}
    }

    /**
	 * Render popup box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'modal-popup', 'class', 'pp-modal-popup' );
        
        $is_editor = \Elementor\Plugin::instance()->editor->is_edit_mode();
        
        if ( $settings['popup_type'] == 'template' || $settings['popup_type'] == 'image' || $settings['popup_type'] == 'content' || $settings['popup_type'] == 'custom-html' ) {
            $this->add_render_attribute( 'modal-popup', 'data-type', 'inline' );
        }
        else if ( $settings['popup_type'] == 'link' ) {
            $this->add_render_attribute( 'modal-popup', 'data-type', 'iframe' );
        }
        else {
            $this->add_render_attribute( 'modal-popup', 'data-type', $settings['popup_type'] );
        }

        if ( $settings['popup_type'] == 'link' ) {
            $this->add_render_attribute( 'modal-popup', 'data-src', $settings['popup_link']['url'] );
            $this->add_render_attribute( 'modal-popup', 'data-iframe-class', 'pp-modal-popup-window pp-modal-popup-window-' . esc_attr( $this->get_id() ) );
        } else {
            $this->add_render_attribute( 'modal-popup', 'data-src', '#pp-modal-popup-window-' . esc_attr( $this->get_id() ) );
        }
        
        // Trigger
        if ( $settings['trigger'] != 'other' ) {
            $this->add_render_attribute( 'modal-popup', 'data-trigger-element', '.pp-modal-popup-link-' . esc_attr( $this->get_id() ) );
        }
        
        if ( $settings['trigger'] == 'on-click' && $settings['trigger_type'] == 'button' ) {
            $pp_button_html_tag = 'span';
            
            $this->add_render_attribute( 'button', 'class', [
                    'pp-modal-popup-button',
                    'pp-modal-popup-link',
                    'pp-modal-popup-link-' . esc_attr( $this->get_id() ),
                    'elementor-button',
                    'elementor-size-' . $settings['button_size'],
                ]
            );

            if ( $settings['button_animation'] ) {
                $this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['button_animation'] );
            }
        }
        else if ( $settings['trigger'] == 'page-load' ) {
            $pp_delay = 1000;
            if ( $settings['delay'] != '' ) {
                $pp_delay = $settings['delay'] * 1000;
            }
            $this->add_render_attribute( 'modal-popup', 'data-delay', $pp_delay );
            
            if ( $settings['display_after_page_load'] != '' ) {
                $this->add_render_attribute( 'modal-popup', 'data-display-after', $settings['display_after_page_load'] );
            }
        }
        else if ( $settings['trigger'] == 'exit-intent' ) {
            if ( $settings['display_after_exit_intent'] != '' ) {
                $this->add_render_attribute( 'modal-popup', 'data-display-after', $settings['display_after_exit_intent'] );
            }
        }
        else if ( $settings['trigger'] == 'other' ) {
            if ( $settings['element_identifier'] != '' ) {
                $this->add_render_attribute( 'modal-popup', 'data-trigger-element', $settings['element_identifier'] );
            }
        }
		
		$elementor_bp_tablet	= get_option( 'elementor_viewport_lg' );
		$elementor_bp_mobile	= get_option( 'elementor_viewport_md' );
		$bp_tablet				= !empty($elementor_bp_tablet) ? $elementor_bp_tablet : 1025;
		$bp_mobile				= !empty($elementor_bp_mobile) ? $elementor_bp_mobile : 768;
		
		if ( $settings['popup_disable_on'] == 'tablet' ) {
			$popup_disable_on = $bp_tablet;
		} elseif ( $settings['popup_disable_on'] == 'mobile' ) {
			$popup_disable_on = $bp_mobile;
		} else {
			$popup_disable_on = '';
		}
		
		if ( $popup_disable_on ) {
			$this->add_render_attribute( 'modal-popup', 'data-disable-on', $popup_disable_on );
		}
        
        // Popup Window
        $this->add_render_attribute( 'modal-popup-window', [
			'class' => ['pp-modal-popup-window pp-modal-popup-window-' . esc_attr( $this->get_id() )],
			'id' => 'pp-modal-popup-window-' . esc_attr( $this->get_id() )
		] );
        ?>
        <div id="pp-modal-popup-wrap-<?php echo esc_attr( $this->get_id() ); ?>" class="pp-modal-popup-wrap">
            <div <?php echo $this->get_render_attribute_string( 'modal-popup' ); ?>>
                <?php
                    if ( $settings['trigger'] == 'on-click' ) {
                        if ( $settings['trigger_type'] == 'button' ) {
                            printf( '<%1$s %2$s>', $pp_button_html_tag, $this->get_render_attribute_string( 'button' ) );
                                if ( $settings['button_icon_position'] == 'before' ) {
									$this->render_button_icon();
                                }

                                if ( ! empty( $settings['button_text'] ) ) {
                                    printf( '<span %1$s>', $this->get_render_attribute_string( 'button_text' ) );
                                        echo esc_attr( $settings['button_text'] );
                                    printf( '</span>' );
                                }

                                if ( $settings['button_icon_position'] == 'after' ) {
									$this->render_button_icon();
                                }
                            printf( '</%1$s>', $pp_button_html_tag );
                        }
                        else if ( $settings['trigger_type'] == 'icon' ) {
                            $this->render_trigger_icon();
                        }
                        else if ( $settings['trigger_type'] == 'image' ) {
                            $trigger_image = $this->get_settings( 'trigger_image' );
                            if ( ! empty( $trigger_image['url'] ) ) {
                                printf( '<img class="pp-trigger-image pp-modal-popup-link %1$s" src="%2$s">','pp-modal-popup-link-' . esc_attr( $this->get_id() ), esc_url( $trigger_image['url'] ) );
                            }
                        }
                    } else {
                        if ( $is_editor ) {
                            ?>
                            <div class="pp-editor-message" style="text-align: center;">
                                <h5>
                                    <?php printf( 'Modal Popup ID - %1$s', esc_attr( $this->get_id() ) ); ?>
                                </h5>
                                <p>
                                    <?php _e( 'Click here to edit the "Popup Box" settings. This text will not be visible on frontend.', 'powerpack' ); ?>
                                </p>
                            </div>
                            <?php
                        }
                    }
                ?>
            </div>
        </div>
        <div <?php echo $this->get_render_attribute_string( 'modal-popup-window' ); ?>>
            <?php
                //echo '<span class="pp-modal-popup-link">Open popup</span>';
                ?>
                <?php if ( $settings['popup_title'] == 'yes' && $settings['title'] != '' ) { ?>
                    <div class="pp-popup-header">
                        <h2 class="pp-popup-title">
                            <?php echo $settings['title']; ?>
                        </h2>
                    </div>
                    <?php
                }
                echo '<div class="pp-popup-content" id="pp-popup-content">';
                    if ( $settings['popup_type'] == 'image' ) {
                        ?>
                            <?php
                            $image = $this->get_settings( 'image' );
                            echo '<img src="' . $image['url'] . '">';
                            ?>
                        <?php
                    }
                    else if ( $settings['popup_type'] == 'content' ) {
                        echo apply_filters('the_content', ( $settings['content'] ) );
                    }
                    else if ( $settings['popup_type'] == 'template' ) {
                        if ( !empty( $settings['templates'] ) ) {
                            $pp_template_id = $settings['templates'];

                            echo \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $pp_template_id );
                        }
                    }
                    else if ( $settings['popup_type'] == 'custom-html' ) {
                        echo $settings['custom_html'];
                    }
                    else {
                        echo '';
                    }
                echo '</div>';
            ?>
        </div><!-- .pp-modal-popup -->
        <?php
    }

    /**
	 * Render popup box widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
    protected function _content_template() {}

}