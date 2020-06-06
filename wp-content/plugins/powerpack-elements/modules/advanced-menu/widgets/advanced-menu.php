<?php
namespace PowerpackElements\Modules\AdvancedMenu\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Advanced Menu Widget
 */
class Advanced_Menu extends Powerpack_Widget {

	protected $nav_menu_index = 1;

	/**
	 * Retrieve advanced menu widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Advanced_Menu' );
	}

	 /**
	 * Retrieve advanced menu widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Advanced_Menu' );
	}

	/**
	 * Retrieve the list of categories the advanced menu widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Advanced_Menu' );
    }

	/**
	 * Retrieve advanced menu widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Advanced_Menu' );
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
		return parent::get_widget_keywords( 'Advanced_Menu' );
	}

	/**
	 * Retrieve the list of scripts the advanced menu widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_script_depends() {
        return [
            'jquery-smartmenu',
			'pp-advanced-menu',
			'powerpack-frontend'
        ];
    }

	public function on_export( $element ) {
		unset( $element['settings']['menu'] );

		return $element;
	}

	public function get_widget_id() {
		return $this->get_id();
	}

	protected function get_nav_menu_index() {
		return $this->nav_menu_index++;
	}

	private function get_available_menus() {
		$menus = wp_get_nav_menus();

		$options = [];

		foreach ( $menus as $menu ) {
			$options[ $menu->slug ] = $menu->name;
		}

		return $options;
	}

	protected function _register_controls() {
		
		/* Content Tab */
		$this->register_content_layout_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_menu_controls();
	}
	
	protected function register_content_layout_controls() {

		$this->start_controls_section(
			'section_layout',
			[
				'label'                 => __( 'Layout', 'powerpack' ),
			]
		);

		$menus = $this->get_available_menus();

		if ( ! empty( $menus ) ) {
			$this->add_control(
				'menu',
				[
					'label'   => __( 'Menu', 'powerpack' ),
					'type'    => Controls_Manager::SELECT,
					'options'               => $menus,
					'default'               => array_keys( $menus )[0],
					'separator'             => 'after',
					'description' => sprintf( __( 'Go to the <a href="%s" target="_blank">Menus screen</a> to manage your menus.', 'powerpack' ), admin_url( 'nav-menus.php' ) ),
				]
			);
		} else {
			$this->add_control(
				'menu',
				[
					'type'                  => Controls_Manager::RAW_HTML,
					'raw' => sprintf( __( '<strong>There are no menus in your site.</strong><br>Go to the <a href="%s" target="_blank">Menus screen</a> to create one.', 'powerpack' ), admin_url( 'nav-menus.php?action=edit&menu=0' ) ),
					'separator'             => 'after',
					'content_classes' => 'pp-editor-info',
				]
			);
		}

		$this->add_control(
			'layout',
			[
				'label'                 => __( 'Layout', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'horizontal',
				'options'               => [
					'horizontal' => __( 'Horizontal', 'powerpack' ),
					'vertical' => __( 'Vertical', 'powerpack' ),
				],
				'frontend_available'    => true,
			]
		);

		$this->add_control(
			'align_items',
			[
				'label'                 => __( 'Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'left' => [
						'title' => __( 'Left', 'powerpack' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'powerpack' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'powerpack' ),
						'icon' => 'eicon-h-align-right',
					],
					'justify' => [
						'title' => __( 'Stretch', 'powerpack' ),
						'icon' => 'eicon-h-align-stretch',
					],
				],
				'condition'             => [
					'layout!' => 'dropdown',
				],
			]
		);

		$this->add_control(
			'pointer',
			[
				'label'                 => __( 'Pointer', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'underline',
				'options'               => [
					'none'					=> __( 'None', 'powerpack' ),
					'underline'				=> __( 'Underline', 'powerpack' ),
					'overline'				=> __( 'Overline', 'powerpack' ),
					'double-line'			=> __( 'Double Line', 'powerpack' ),
					'framed'				=> __( 'Framed', 'powerpack' ),
					'background'			=> __( 'Background', 'powerpack' ),
					'brackets'				=> __( 'Brackets', 'powerpack' ),
					'right-angle-slides'	=> __( 'Right Angle Slides Down Over Title', 'powerpack' ),
					'text'					=> __( 'Text', 'powerpack' ),
				],
				'condition'             => [
					'layout!' => 'dropdown',
				],
			]
		);

		$this->add_control(
			'animation_line',
			[
				'label'                 => __( 'Animation', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'fade',
				'options'               => [
					'fade'		=> 'Fade',
					'slide'		=> 'Slide',
					'grow'		=> 'Grow',
					'drop-in'	=> 'Drop In',
					'drop-out'	=> 'Drop Out',
					'none'		=> 'None',
				],
				'condition'             => [
					'layout!' => 'dropdown',
					'pointer' => [ 'underline', 'overline', 'double-line' ],
				],
			]
		);

		$this->add_control(
			'animation_framed',
			[
				'label'                 => __( 'Animation', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'fade',
				'options'               => [
					'fade'		=> 'Fade',
					'grow'		=> 'Grow',
					'shrink'	=> 'Shrink',
					'draw'		=> 'Draw',
					'corners'	=> 'Corners',
					'none'		=> 'None',
				],
				'condition'             => [
					'layout!' => 'dropdown',
					'pointer' => 'framed',
				],
			]
		);

		$this->add_control(
			'animation_background',
			[
				'label'                 => __( 'Animation', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'fade',
				'options'               => [
					'fade'						=> 'Fade',
					'grow'						=> 'Grow',
					'shrink'					=> 'Shrink',
					'sweep-left'				=> 'Sweep Left',
					'sweep-right'				=> 'Sweep Right',
					'sweep-up'					=> 'Sweep Up',
					'sweep-down'				=> 'Sweep Down',
					'shutter-in-vertical'		=> 'Shutter In Vertical',
					'shutter-out-vertical'		=> 'Shutter Out Vertical',
					'shutter-in-horizontal'		=> 'Shutter In Horizontal',
					'shutter-out-horizontal'	=> 'Shutter Out Horizontal',
					'none' => 'None',
				],
				'condition'             => [
					'layout!' => 'dropdown',
					'pointer' => 'background',
				],
			]
		);

		$this->add_control(
			'animation_text',
			[
				'label'                 => __( 'Animation', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'grow',
				'options'               => [
					'grow' => 'Grow',
					'shrink' => 'Shrink',
					'sink' => 'Sink',
					'float' => 'Float',
					'skew' => 'Skew',
					'rotate' => 'Rotate',
					'none' => 'None',
				],
				'condition'             => [
					'layout!' => 'dropdown',
					'pointer' => 'text',
				],
			]
		);

		$this->add_control(
			'indicator',
			[
				'label'                 => __( 'Submenu Indicator', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'classic',
				'options'               => [
					'none' => __( 'None', 'powerpack' ),
					'classic' => __( 'Classic', 'powerpack' ),
					'chevron' => __( 'Chevron', 'powerpack' ),
					'angle' => __( 'Angle', 'powerpack' ),
					'plus' => __( 'Plus', 'powerpack' ),
				],
			]
		);

		$this->add_control(
			'heading_mobile_dropdown',
			[
				'label'                 => __( 'Responsive', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'layout!' => 'dropdown',
				],
			]
		);

		$this->add_control(
			'dropdown',
			[
				'label'                 => __( 'Breakpoint', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'tablet',
				'options'               => [
					'all'	=> __('Always', 'powerpack'),
					'mobile' => __( 'Mobile (767px >)', 'powerpack' ),
					'tablet' => __( 'Tablet (1023px >)', 'powerpack' ),
					'none' => __( 'None', 'powerpack' ),
				],
			]
		);

		$this->add_control(
			'menu_type',
			[
				'label'                 => __( 'Menu Type', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'default',
				'options'               => [
					'default' 		=> __( 'Default', 'powerpack' ),
					'off-canvas' 	=> __( 'Off Canvas', 'powerpack' ),
					'full-screen' 	=> __( 'Full Screen', 'powerpack' ),
				],
				'condition'             => [
					'toggle!' 				=> '',
					'dropdown!'				=> 'none'
				],
			]
		);

		$this->add_control(
			'onepage_menu',
			[
				'label'                 => __( 'One Page Menu', 'powerpack' ),
				'description'			=> __( 'Set this option to \'Yes\' to close menu when user clicks on same page links.', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'no',
				'options'               => [
					'yes' => __( 'Yes', 'powerpack' ),
					'no' => __( 'No', 'powerpack' ),
				],
				'condition'             => [
					'dropdown!'		=> 'none',
					'menu_type!'	=> 'default',
				],
			]
		);

		$this->add_control(
			'full_width',
			[
				'label'                 => __( 'Full Width', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'description' => __( 'Stretch the dropdown of the menu to full width.', 'powerpack' ),
				'prefix_class' => 'pp-advanced-menu--',
				'return_value' => 'stretch',
				'frontend_available'    => true,
				'condition'             => [
					'dropdown!'				=> 'none',
					'menu_type' => 'default',
				],
			]
		);

		$this->add_control(
			'toggle',
			[
				'label'                 => __( 'Toggle Button', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'icon',
				'options'               => [
					'icon'         => __( 'Icon', 'powerpack' ),
					'icon-label'   => __( 'Icon + Label', 'powerpack' ),
					'button'       => __( 'Label', 'powerpack' ),
				],
				'render_type'           => 'template',
				'frontend_available'    => true,
				'condition'			=> [
					'dropdown!'			=> 'none'
				]
			]
		);
        
        $this->add_control(
			'toggle_label',
			[
				'label'                 => __( 'Toggle Label', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'default'               => __( 'Menu', 'powerpack' ),
				'condition'             => [
					'toggle' 				=> ['icon-label', 'button'],
					'dropdown!'				=> 'none'
				],
			]
		);

		$this->add_control(
			'label_align',
			[
				'label'                 => __( 'Label Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'default'               => 'right',
				'options'               => [
					'left' => [
						'title' => __( 'Left', 'powerpack' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'Right', 'powerpack' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'condition'             => [
					'toggle' 				=> ['icon-label'],
					'dropdown!'				=> 'none'
				],
				'label_block'           => false,
				'toggle'                => false,
			]
		);

		$this->add_control(
			'toggle_align',
			[
				'label'                 => __( 'Toggle Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'default'               => 'center',
				'options'               => [
					'left' => [
						'title' => __( 'Left', 'powerpack' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'powerpack' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'powerpack' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors_dictionary'  => [
					'left' => 'margin-right: auto',
					'center' => 'margin: 0 auto',
					'right' => 'margin-left: auto',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-menu-toggle' => '{{VALUE}}',
				],
				'condition'             => [
					'toggle!' 				=> '',
					'dropdown!'				=> 'none'
				],
				'label_block'           => false,
			]
		);

		$this->add_control(
			'text_align',
			[
				'label'                 => __( 'Align', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'aside',
				'options'               => [
					'aside' => __( 'Aside', 'powerpack' ),
					'center' => __( 'Center', 'powerpack' ),
				],
				'condition'             => [
					'dropdown!'			=> 'none',
					'menu_type!' 		=> ['off-canvas', 'full-screen']
				]
			]
		);

		$this->end_controls_section();

	}
	
	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links('Advanced_Menu');
		
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
	
	protected function register_style_menu_controls() {
		$this->start_controls_section(
			'section_style_main_menu',
			[
				'label'                 => __( 'Main Menu', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'dropdown!' => 'all',
				],

			]
		);

		$this->add_control(
			'heading_menu_item',
			[
				'type'                  => Controls_Manager::HEADING,
				'label'                 => __( 'Menu Item', 'powerpack' ),
			]
		);

		$this->start_controls_tabs( 'tabs_menu_item_style' );

		$this->start_controls_tab(
			'tab_menu_item_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'color_menu_item',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'scheme'                => [
					'type'     => Scheme_Color::get_type(),
					'value'    => Scheme_Color::COLOR_3,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-menu-item' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'color_menu_item_hover',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'scheme'                => [
					'type'     => Scheme_Color::get_type(),
					'value'    => Scheme_Color::COLOR_4,
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-menu-item:hover,
					{{WRAPPER}} .pp-advanced-menu--main .pp-menu-item.pp-menu-item-active,
					{{WRAPPER}} .pp-advanced-menu--main .pp-menu-item.highlighted,
					{{WRAPPER}} .pp-advanced-menu--main .pp-menu-item:focus' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'pointer!' => 'background',
				],
			]
		);

		$this->add_control(
			'color_menu_item_hover_pointer_bg',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#fff',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-menu-item:hover,
					{{WRAPPER}} .pp-advanced-menu--main .pp-menu-item.pp-menu-item-active,
					{{WRAPPER}} .pp-advanced-menu--main .pp-menu-item.highlighted,
					{{WRAPPER}} .pp-advanced-menu--main .pp-menu-item:focus' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'pointer' => 'background',
				],
			]
		);

		$this->add_control(
			'pointer_color_menu_item_hover',
			[
				'label'                 => __( 'Pointer Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'scheme'                => [
					'type'     => Scheme_Color::get_type(),
					'value'    => Scheme_Color::COLOR_4,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main:not(.pp--pointer-framed) .pp-menu-item:before,
					{{WRAPPER}} .pp-advanced-menu--main:not(.pp--pointer-framed) .pp-menu-item:after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .pp--pointer-framed .pp-menu-item:before,
					{{WRAPPER}} .pp--pointer-framed .pp-menu-item:after' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .pp--pointer-brackets .pp-menu-item:before,
					{{WRAPPER}} .pp--pointer-brackets .pp-menu-item:after' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'pointer!' => [ 'none', 'text' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_active',
			[
				'label'                 => __( 'Active', 'powerpack' ),
			]
		);

		$this->add_control(
			'color_menu_item_active',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-menu-item.pp-menu-item-active' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pointer_color_menu_item_active',
			[
				'label'                 => __( 'Pointer Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main:not(.pp--pointer-framed) .pp-menu-item.pp-menu-item-active:before,
					{{WRAPPER}} .pp-advanced-menu--main:not(.pp--pointer-framed) .pp-menu-item.pp-menu-item-active:after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .pp--pointer-framed .pp-menu-item.pp-menu-item-active:before,
					{{WRAPPER}} .pp--pointer-framed .pp-menu-item.pp-menu-item-active:after' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .pp--pointer-brackets .pp-menu-item.pp-menu-item-active:before,
					{{WRAPPER}} .pp--pointer-brackets .pp-menu-item.pp-menu-item-active:after' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'pointer!' => [ 'none', 'text' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'padding_horizontal_menu_item',
			[
				'label'                 => __( 'Horizontal Padding', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'max' => 50,
					],
				],
				'devices'               => [ 'desktop', 'tablet', 'mobile' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-menu-item' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'padding_vertical_menu_item',
			[
				'label'                 => __( 'Vertical Padding', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'max' => 50,
					],
				],
				'devices'               => [ 'desktop', 'tablet', 'mobile' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-menu-item' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'menu_space_between',
			[
				'label'                 => __( 'Space Between', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'devices'               => [ 'desktop', 'tablet', 'mobile' ],
				'selectors'             => [
					'body:not(.rtl) {{WRAPPER}} .pp-advanced-menu--layout-horizontal .pp-advanced-menu > li:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}} .pp-advanced-menu--layout-horizontal .pp-advanced-menu > li:not(:last-child)' => 'margin-left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .pp-advanced-menu--main:not(.pp-advanced-menu--layout-horizontal) .pp-advanced-menu > li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'pointer_width',
			[
				'label'                 => __( 'Pointer Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'devices'               => [ self::RESPONSIVE_DESKTOP, self::RESPONSIVE_TABLET ],
				'range'                 => [
					'px' => [
						'max' => 30,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp--pointer-framed .pp-menu-item:before' => 'border-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .pp--pointer-framed.e--animation-draw .pp-menu-item:before' => 'border-width: 0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .pp--pointer-framed.e--animation-draw .pp-menu-item:after' => 'border-width: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0',
					'{{WRAPPER}} .pp--pointer-framed.e--animation-corners .pp-menu-item:before' => 'border-width: {{SIZE}}{{UNIT}} 0 0 {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .pp--pointer-framed.e--animation-corners .pp-menu-item:after' => 'border-width: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0',
					'{{WRAPPER}} .pp--pointer-underline .pp-menu-item:after,
					 {{WRAPPER}} .pp--pointer-overline .pp-menu-item:before,
					 {{WRAPPER}} .pp--pointer-double-line .pp-menu-item:before,
					 {{WRAPPER}} .pp--pointer-double-line .pp-menu-item:after' => 'height: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'pointer' => [ 'underline', 'overline', 'double-line', 'framed' ],
				],
			]
		);

		$this->add_responsive_control(
			'border_radius_menu_item',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', 'em', '%' ],
				'devices'               => [ 'desktop', 'tablet' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-menu-item:before' => 'border-radius: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .e--animation-shutter-in-horizontal .pp-menu-item:before' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0',
					'{{WRAPPER}} .e--animation-shutter-in-horizontal .pp-menu-item:after' => 'border-radius: 0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .e--animation-shutter-in-vertical .pp-menu-item:before' => 'border-radius: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0',
					'{{WRAPPER}} .e--animation-shutter-in-vertical .pp-menu-item:after' => 'border-radius: {{SIZE}}{{UNIT}} 0 0 {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'pointer' => 'background',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_dropdown',
			[
				'label'                 => __( 'Dropdown', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'dropdown',
							'operator' => '!==',
							'value' => 'all',
						],
						[
							'relation' => 'and',
							'terms' => [
								[
									'name' => 'dropdown',
									'operator' => '==',
									'value' => 'all',
								],
								[
									'name' => 'menu_type',
									'operator' => '==',
									'value' => 'default',
								],
							],
						],
					],
				],
			]
		);

		$this->add_control(
			'dropdown_description',
			[
				'raw'                   => __( 'On desktop, this will affect the submenu. On mobile, this will affect the entire menu.', 'powerpack' ),
				'type'                  => Controls_Manager::RAW_HTML,
				'content_classes'       => 'pp-editor-info',
			]
		);

		$this->start_controls_tabs( 'tabs_dropdown_item_style' );

		$this->start_controls_tab(
			'tab_dropdown_item_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'color_dropdown_item',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-advanced-menu--dropdown a, {{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu--dropdown.pp-menu-default a, {{WRAPPER}} .pp-menu-toggle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_color_dropdown_item',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-advanced-menu--dropdown, {{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu--dropdown.pp-menu-default' => 'background-color: {{VALUE}}',
				],
				'separator'             => 'none',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dropdown_item_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'color_dropdown_item_hover',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-advanced-menu--dropdown a:hover, {{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu--dropdown.pp-menu-default a:hover, {{WRAPPER}} .pp-menu-toggle:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_color_dropdown_item_hover',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-advanced-menu--dropdown a:hover,
					{{WRAPPER}} .pp-advanced-menu--main:not(.pp-advanced-menu--layout-expanded) .pp-advanced-menu--dropdown a.highlighted, {{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu--dropdown.pp-menu-default a:hover,
					{{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu--dropdown.pp-menu-default a.highlighted' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dropdown_item_active',
			[
				'label'                 => __( 'Active', 'powerpack' ),
			]
		);

		$this->add_control(
			'color_dropdown_item_active',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-advanced-menu--dropdown a.pp-menu-item-active, {{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu--dropdown.pp-menu-default a.pp-menu-item-active' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_color_dropdown_item_active',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-advanced-menu--dropdown a.pp-menu-item-active, {{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu--dropdown.pp-menu-default a.pp-menu-item-active' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'dropdown_border',
				'selector'              => '{{WRAPPER}} .pp-advanced-menu--main .pp-advanced-menu--dropdown, {{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu--dropdown.pp-menu-default',
				'separator'             => 'before',
			]
		);

		$this->add_responsive_control(
			'dropdown_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-advanced-menu--dropdown, {{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu--dropdown.pp-menu-default' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .pp-advanced-menu--main .pp-advanced-menu--dropdown li:first-child a, {{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu--dropdown.pp-menu-default li:first-child a' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}};',
					'{{WRAPPER}} .pp-advanced-menu--main .pp-advanced-menu--dropdown li:last-child a, {{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu--dropdown.pp-menu-default li:last-child a' => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'dropdown_box_shadow',
				'exclude'               => [
					'box_shadow_position',
				],
				'selector'              => '{{WRAPPER}} .pp-advanced-menu--main .pp-advanced-menu--dropdown, {{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu__container.pp-advanced-menu--dropdown',
			]
		);

		$this->add_responsive_control(
			'dropdown_min_width',
			[
				'label'                 => __( 'Minimum Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'                 => [
					'size' => 200,
				],
				'range'                 => [
					'px' => [
						'min' => 50,
						'max' => 400,
					],
				],
				'devices'               => [ 'desktop', 'tablet', 'mobile' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-advanced-menu--dropdown' => 'min-width: {{SIZE}}{{UNIT}};',
				],
				'separator'             => 'before',
			]
		);

		$this->add_responsive_control(
			'padding_horizontal_dropdown_item',
			[
				'label'                 => __( 'Horizontal Padding', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-advanced-menu--dropdown a, {{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu--dropdown.pp-menu-default a' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}',
				],

			]
		);

		$this->add_responsive_control(
			'padding_vertical_dropdown_item',
			[
				'label'                 => __( 'Vertical Padding', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-advanced-menu--dropdown a, {{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu--dropdown.pp-menu-default a' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'heading_dropdown_divider',
			[
				'label'                 => __( 'Divider', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'dropdown_divider',
				'selector'              => '{{WRAPPER}} .pp-advanced-menu--main .pp-advanced-menu--dropdown li:not(:last-child), {{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu--dropdown.pp-menu-default li:not(:last-child)',
				'exclude'               => [ 'width' ],
			]
		);

		$this->add_control(
			'dropdown_divider_width',
			[
				'label'                 => __( 'Border Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main .pp-advanced-menu--dropdown li:not(:last-child), {{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu--dropdown.pp-menu-default li:not(:last-child)' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'dropdown_divider_border!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'dropdown_top_distance',
			[
				'label'                 => __( 'Distance', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--main > .pp-advanced-menu > li > .pp-advanced-menu--dropdown, {{WRAPPER}} .pp-advanced-menu--type-default .pp-advanced-menu__container.pp-advanced-menu--dropdown' => 'margin-top: {{SIZE}}{{UNIT}} !important',
				],
				'separator'             => 'before',
			]
		);

		$this->end_controls_section();

		/**
         * Content Tab: Toggle Button
         */
		$this->start_controls_section( 'style_toggle',
			[
				'label'                 => __( 'Toggle Button', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'dropdown!'		=> 'none',
					'toggle!'		=> '',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_toggle_style' );

		$this->start_controls_tab(
			'tab_toggle_style_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'toggle_color',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-menu-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner,
					{{WRAPPER}} .pp-menu-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner:before,
					{{WRAPPER}} .pp-menu-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner:after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .pp-menu-toggle .pp-menu-toggle-label'	=> 'color: {{VALUE}}' // Harder selector to override text color control
				],
				'condition'             => [
					'dropdown!'		=> 'none',
				],
			]
		);

		$this->add_control(
			'toggle_background_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-menu-toggle' => 'background-color: {{VALUE}}',
				],
				'condition'             => [
					'dropdown!'		=> 'none',
				],
			]
		);
        
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'toggle_border',
				'label'                 => __( 'Border', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-menu-toggle',
				'condition'             => [
					'dropdown!'		=> 'none',
				],
			]
		);

		$this->add_control(
			'toggle_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-menu-toggle' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'dropdown!'		=> 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'toggle_box_shadow',
				'selector' 				=> '{{WRAPPER}} .pp-menu-toggle',
				'condition'             => [
					'dropdown!'		=> 'none',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_toggle_style_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'toggle_color_hover',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-menu-toggle:hover .pp-hamburger .pp-hamburger-box .pp-hamburger-inner,
					{{WRAPPER}} .pp-menu-toggle:hover .pp-hamburger .pp-hamburger-box .pp-hamburger-inner:before,
					{{WRAPPER}} .pp-menu-toggle:hover .pp-hamburger .pp-hamburger-box .pp-hamburger-inner:after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .pp-menu-toggle:hover .pp-menu-toggle-label'	=> 'color: {{VALUE}}' // Harder selector to override text color control
				],
				'condition'             => [
					'dropdown!'		=> 'none',
				],
			]
		);

		$this->add_control(
			'toggle_background_color_hover',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-menu-toggle:hover' => 'background-color: {{VALUE}}',
				],
				'condition'             => [
					'dropdown!'		=> 'none',
				],
			]
		);

		$this->add_control(
			'toggle_baorder_color_hover',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-menu-toggle:hover' => 'border-color: {{VALUE}}',
				],
				'condition'             => [
					'dropdown!'		=> 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'toggle_box_shadow_hover',
				'selector' 				=> '{{WRAPPER}} .pp-menu-toggle:hover',
				'condition'             => [
					'dropdown!'		=> 'none',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'toggle_size',
			[
				'label'                 => __( 'Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 15,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-menu-toggle .pp-hamburger .pp-hamburger-box' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'dropdown!'		=> 'none',
					'toggle'		=> ['icon', 'icon-label'],
				],
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'toggle_thickness',
			[
				'label'                 => __( 'Thickness', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 1,
						'max' => 16,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-menu-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner,
					{{WRAPPER}} .pp-menu-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner:before,
					{{WRAPPER}} .pp-menu-toggle .pp-hamburger .pp-hamburger-box .pp-hamburger-inner:after' => 'height: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'dropdown!'		=> 'none',
					'toggle'		=> ['icon', 'icon-label'],
				],
			]
		);
		
		$this->add_responsive_control(
			'toggle_padding',
			[
				'label'					=> __( 'Padding', 'powerpack' ),
				'type'					=> Controls_Manager::DIMENSIONS,
				'size_units'			=> [ 'px', 'em' ],
				'selectors'				=> [
					'{{WRAPPER}} .pp-menu-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_toggle_label_style',
			[
				'label'                 => __( 'Label', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'dropdown!'		=> 'none',
					'toggle'		=> ['icon-label','button'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'toggle_label_typography',
				'scheme'                => Scheme_Typography::TYPOGRAPHY_1,
				'selector'              => '{{WRAPPER}} .pp-menu-toggle .pp-menu-toggle-label',
				'condition'             => [
					'dropdown!'		=> 'none',
					'toggle'		=> ['icon-label','button'],
				],
			]
		);

		$this->end_controls_section();

		/**
         * Content Tab: Off Canvas/Full Screen
         */
		$this->start_controls_section( 'style_responsive',
			[
				'label'                 => __( 'Off Canvas/Full Screen', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'dropdown!'		=> 'none',
					'menu_type'		=> ['off-canvas', 'full-screen'],
				],
			]
		);

		$this->add_control(
			'offcanvas_position',
			[
				'label'                 => __( 'Position', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'left',
				'options'               => [
					'left' => __( 'Left', 'powerpack' ),
					'right' => __( 'Right', 'powerpack' ),
				],
				'condition'             => [
					'dropdown!'		=> 'none',
					'menu_type'		=> 'off-canvas',
				],
			]
		);

		$this->add_control(
			'responsive_menu_alignment',
			[
				'label'                 => __( 'Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'space-between',
				'options'               => [
					'space-between' => __( 'Left', 'powerpack' ),
					'center'        => __( 'Center', 'powerpack' ),
					'flex-end'      => __( 'Right', 'powerpack' ),
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--dropdown a, .pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} a'  => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'offcanvas_menu_width',
			[
				'label'                 => __( 'Off Canvas Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%', 'vw' ],
				'range'                 => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
				],
				'selectors'             => [
					'body.pp-menu--off-canvas .pp-menu-off-canvas.pp-menu-{{ID}}' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'menu_type' 		=> 'off-canvas'
				]

			]
		);

		$this->add_control(
			'overlay_bg_color',
			[
				'label'                 => __( 'Menu Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => 'rgba(0,0,0,0.8)',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container,
					.pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}}'  => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_responsive_style' );

		$this->start_controls_tab(
			'tab_responsive_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'mobile_link_color',
			[
				'label'                 => __( 'Link Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container .pp-menu-item,
					.pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} .pp-menu-item' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'mobile_sub_link_bg_color',
			[
				'label'                 => __( 'Sub Menu Link Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container a.pp-sub-item,
					.pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} a.pp-sub-item, .pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} .sub-menu' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'mobile_sub_link_color',
			[
				'label'                 => __( 'Sub Menu Link Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container a.pp-sub-item, .pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} a.pp-sub-item' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_responsive_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'mobile_link_hover',
			[
				'label'                 => __( 'Link Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container .pp-menu-item:hover,
					{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container .pp-menu-item:focus,
					{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container .pp-menu-item.pp-menu-item-active,
					.pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} .pp-menu-item:hover,
					.pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} .pp-menu-item:focus,
					.pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} .pp-menu-item.pp-menu-item-active' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'mobile_link_bg_hover',
			[
				'label'                 => __( 'Link Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container .pp-menu-item:hover,
					{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container .pp-menu-item:focus,
					{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container .pp-menu-item.pp-menu-item-active,
					.pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} .pp-menu-item:hover,
					.pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} .pp-menu-item:focus,
					.pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} .pp-menu-item.pp-menu-item-active' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'mobile_sub_link_bg_hover',
			[
				'label'                 => __( 'Sub Menu Link Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container a.pp-sub-item:hover,
					{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container a.pp-sub-item:focus,
					{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container a.pp-sub-item:active,
					.pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} a.pp-sub-item:hover,
					.pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} a.pp-sub-item:focus,
					.pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} a.pp-sub-item:active' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'mobile_sub_link_hover',
			[
				'label'                 => __( 'Sub Menu Link Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container a.pp-sub-item:hover, .pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} a.pp-sub-item:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'mobile_submenu_indent',
			[
				'label'                 => __( 'Submenu Indent', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'selectors'             => [
					'.pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} .sub-menu' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}',
				],
				'separator'             => 'before',

			]
		);

		$this->add_control(
			'padding_horizontal_mobile_link_item',
			[
				'label'                 => __( 'Horizontal Padding', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container .pp-menu-item, {{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container a.pp-sub-item, .pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} .pp-menu-item, .pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} a.pp-sub-item' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}',
				],
				'separator'             => 'before',

			]
		);

		$this->add_control(
			'padding_vertical_mobile_link_item',
			[
				'label'                 => __( 'Vertical Padding', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container .pp-menu-item, {{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container a.pp-sub-item, .pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} .pp-menu-item, .pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} a.pp-sub-item' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'mobile_menu_border',
				'selector'              => '{{WRAPPER}} .pp-advanced-menu--dropdown li:not(:last-child), .pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} li:not(:last-child)',
				'separator'             => 'before',
			]
		);

		$this->add_control(
            'hr',
            [
                'type'                  => Controls_Manager::DIVIDER,
                'style'                 => 'thick',
				'condition'             => [
					'dropdown!'		=> 'none',
					'menu_type'		=> 'off-canvas',
				],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'mobile_menu_box_shadow',
				'selector' 				=> '{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container, .pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}}',
				'condition'             => [
					'dropdown!'		=> 'none',
					'menu_type'		=> 'off-canvas',
				],
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'heading_close_icon_style',
			[
				'label'                 => __( 'Close Icon', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'dropdown!'		=> 'none',
					'menu_type'		=> ['off-canvas', 'full-screen'],
				],
			]
		);

		$this->add_control(
			'close_icon_size',
			[
				'label'                 => __( 'Close Icon Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min' => 15,
					],
				],
				'selectors'             => [
					'body.pp-menu--off-canvas .pp-advanced-menu--dropdown.pp-menu-{{ID}} .pp-menu-close, {{WRAPPER}} .pp-advanced-menu--type-full-screen .pp-advanced-menu--dropdown.pp-advanced-menu__container .pp-menu-close' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
					'body.pp-menu--off-canvas .pp-advanced-menu--dropdown.pp-menu-{{ID}} .pp-menu-close:before, {{WRAPPER}} .pp-advanced-menu--type-full-screen .pp-advanced-menu--dropdown.pp-advanced-menu__container .pp-menu-close:before,
					body.pp-menu--off-canvas .pp-advanced-menu--dropdown.pp-menu-{{ID}} .pp-menu-close:after, {{WRAPPER}} .pp-advanced-menu--type-full-screen .pp-advanced-menu--dropdown.pp-advanced-menu__container .pp-menu-close:after' => 'height: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'dropdown!'		=> 'none',
					'menu_type'		=> ['off-canvas', 'full-screen'],
				],
			]
		);

		$this->add_control(
			'close_icon_color',
			[
				'label'                 => __( 'Close Icon Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'body.pp-menu--off-canvas .pp-advanced-menu--dropdown.pp-menu-{{ID}} .pp-menu-close:before, {{WRAPPER}} .pp-advanced-menu--type-full-screen .pp-advanced-menu--dropdown.pp-advanced-menu__container .pp-menu-close:before,
					body.pp-menu--off-canvas .pp-advanced-menu--dropdown.pp-menu-{{ID}} .pp-menu-close:after, {{WRAPPER}} .pp-advanced-menu--type-full-screen .pp-advanced-menu--dropdown.pp-advanced-menu__container .pp-menu-close:after' => 'background-color: {{VALUE}}',
				],
				'condition'             => [
					'dropdown!'		=> 'none',
					'menu_type'		=> ['off-canvas', 'full-screen'],
				],
			]
		);

		$this->end_controls_section();

		/**
         * Content Tab: Typography
         */
		$this->start_controls_section( 'style_typography',
			[
				'label'                 => __( 'Typography', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'main_typography_heading',
			[
				'label'                 => __( 'Main Menu/Off Canvas/Full Screen', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'menu_typography',
				'separator'             => 'before',
				'selector'              => '{{WRAPPER}} .pp-advanced-menu--main, {{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container, {{WRAPPER}} .pp-advanced-menu-main-wrapper.pp-advanced-menu--type-full-screen .pp-advanced-menu--dropdown, .pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}}',
			]
		);

		$this->add_control(
			'dropdown_typography_heading',
			[
				'label'                 => __( 'Dropdown/Submenu', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'dropdown_typography',
				'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
				'exclude'               => [ 'line_height' ],
				'selector'              => '{{WRAPPER}} .pp-advanced-menu-main-wrapper .pp-advanced-menu--dropdown, 
				{{WRAPPER}} .pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-open .sub-menu,
				.pp-advanced-menu--dropdown.pp-advanced-menu__container.pp-menu-{{ID}} .sub-menu',
				'separator'             => 'before',
			]
		);
		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_indicator',
			[
				'label'                 => __( 'Submenu Indicator', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'indicator!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'indicator_size',
			[
				'label'                 => __( 'Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-menu .sub-arrow, .pp-advanced-menu__container.pp-menu-{{ID}} .sub-arrow' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'indicator!' => 'none',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$available_menus = $this->get_available_menus();

		if ( ! $available_menus ) {
			return;
		}

		$settings = $this->get_settings();

		$settings_attr = array(
			'menu_type'		=> $settings['menu_type'],
			'menu_id'		=> esc_attr( $this->get_id() ),
			'breakpoint'	=> $settings['dropdown'],
			'menu_layout'	=> $settings['layout'],
			'onepage_menu'	=> $settings['onepage_menu'],
		);
		
		if ( $settings['dropdown'] != 'none' ) {
			$settings_attr['full_width'] = ( ! $settings['full_width'] || empty( $settings['full_width'] ) ) ? false : true;
		}

		$args = [
			'echo' => false,
			'menu' => $settings['menu'],
			'menu_class' => 'pp-advanced-menu',
			//'menu_id' => 'menu-' . $this->get_nav_menu_index() . '-' . $this->get_id(),
			'fallback_cb' => '__return_empty_string',
			'container' => '',
		];

		if ( 'vertical' === $settings['layout'] ) {
			$args['menu_class'] .= ' sm-vertical';
		}

		// Add custom filter to handle Nav Menu HTML output.
		add_filter( 'nav_menu_link_attributes', [ $this, 'handle_link_classes' ], 10, 4 );
		add_filter( 'nav_menu_submenu_css_class', [ $this, 'handle_sub_menu_classes' ] );
		add_filter( 'nav_menu_item_id', '__return_empty_string' );

		// General Menu.
		$menu_html = wp_nav_menu( $args );

		// Dropdown Menu.
		//$args['menu_id'] = 'menu-' . $this->get_nav_menu_index() . '-' . $this->get_id();
		$dropdown_menu_html = wp_nav_menu( $args );

		// Remove all our custom filters.
		remove_filter( 'nav_menu_link_attributes', [ $this, 'handle_link_classes' ] );
		remove_filter( 'nav_menu_submenu_css_class', [ $this, 'handle_sub_menu_classes' ] );
		remove_filter( 'nav_menu_item_id', '__return_empty_string' );

		if ( empty( $menu_html ) ) {
			return;
		}

		$menu_toggle_classes = [
			'pp-menu-toggle',
		];

		if ( $settings['layout'] !== 'dropdown' ) {
			$menu_toggle_classes[] = 'pp-menu-toggle-on-' . $settings['dropdown'];
		} else {
			$menu_toggle_classes[] = 'pp-menu-toggle-on-all';
		}
        
        if ( $settings['toggle'] == 'icon-label' ) {
            $menu_toggle_classes[] = 'pp-menu-toggle-label-' . $settings['label_align'];
        }

		$this->add_render_attribute( 'menu-toggle', 'class', $menu_toggle_classes);

		// if ( Plugin::elementor()->editor->is_edit_mode() ) {
		// 	$this->add_render_attribute( 'menu-toggle', [
		// 		'class' => 'pp-clickable',
		// 	] );
		// }

		$menu_wrapper_classes = 'pp-advanced-menu__align-' . $settings['align_items'];
		$menu_wrapper_classes .= ' pp-advanced-menu--indicator-' . $settings['indicator'];
		$menu_wrapper_classes .= ' pp-advanced-menu--dropdown-' . $settings['dropdown'];
		$menu_wrapper_classes .= ' pp-advanced-menu--type-' . $settings['menu_type'];
		$menu_wrapper_classes .= ' pp-advanced-menu__text-align-' . $settings['text_align'];
		$menu_wrapper_classes .= ' pp-advanced-menu--toggle pp-advanced-menu--' . $settings['toggle'];
		?>

		<?php do_action( 'ppe_before_advanced_menu_wrapper' ); ?>
		<div class="pp-advanced-menu-main-wrapper <?php echo $menu_wrapper_classes; ?>">
		<?php
		if ( 'all' != $settings['dropdown'] ) :
			$this->add_render_attribute( 'main-menu', 'class', [
				'pp-advanced-menu--main',
				'pp-advanced-menu__container',
				'pp-advanced-menu--layout-' . $settings['layout'],
			] );

			if ( $settings['pointer'] ) :
				$this->add_render_attribute( 'main-menu', 'class', 'pp--pointer-' . $settings['pointer'] );

				foreach ( $settings as $key => $value ) :
					if ( 0 === strpos( $key, 'animation' ) && $value ) :
						$this->add_render_attribute( 'main-menu', 'class', 'e--animation-' . $value );

						break;
					endif;
				endforeach;
			endif; ?>

			<?php do_action( 'ppe_before_advanced_menu' ); ?>
			<nav id="pp-menu-<?php echo $this->get_id(); ?>" <?php echo $this->get_render_attribute_string( 'main-menu' ); ?> data-settings="<?php echo htmlspecialchars(json_encode($settings_attr)); ?>"><?php echo $menu_html; ?></nav>
			<?php do_action( 'ppe_after_advanced_menu' ); ?>
			<?php
		endif;
		?>
		<?php if ( 'none' != $settings['dropdown'] ) { ?>
			<?php if ( $settings['toggle'] != '' ) { ?>
				<div <?php echo $this->get_render_attribute_string( 'menu-toggle' ); ?>>
					<?php if ( $settings['toggle'] == 'icon-label' || $settings['toggle'] == 'icon' ) { ?>
						<div class="pp-hamburger">
							<div class="pp-hamburger-box">
								<div class="pp-hamburger-inner"></div>
							</div>
						</div>
					<?php } ?>
					<?php if ( $settings['toggle'] == 'icon-label' || $settings['toggle'] == 'button' ) { ?>
						<?php if ( $settings['toggle_label'] != '' ) { ?>
							<span class="pp-menu-toggle-label">
								<?php echo $settings['toggle_label']; ?>
							</span>
						<?php } ?>
					<?php } ?>
				</div>
			<?php } ?>
			<?php
				$offcanvas_pos = '';
				if( $settings['menu_type'] == 'off-canvas' ) {
					$offcanvas_pos = ' pp-menu-off-canvas-' . $settings['offcanvas_position'];
				}
			?>
			<?php do_action( 'ppe_before_advanced_menu_responsive' ); ?>
			<nav class="pp-advanced-menu--dropdown pp-menu-style-toggle pp-advanced-menu__container pp-menu-<?php echo $this->get_id(); ?><?php if( 'default' != $settings['menu_type']) { ?> pp-advanced-menu--indicator-<?php echo $settings['indicator']; ?><?php } ?> pp-menu-<?php echo $settings['menu_type']; ?><?php echo $offcanvas_pos; ?>" data-settings="<?php echo htmlspecialchars(json_encode($settings_attr)); ?>">
				<?php if( $settings['menu_type'] == 'full-screen' || $settings['menu_type'] == 'off-canvas' ) { ?>
					<div class="pp-menu-close">
					</div>
				<?php } ?>
				<?php echo $dropdown_menu_html; ?>
			</nav>
			<?php do_action( 'ppe_after_advanced_menu_responsive' ); ?>
		<?php } ?>
        </div>
		<?php do_action( 'ppe_after_advanced_menu_wrapper' ); ?>
        <?php
	}

	public function handle_link_classes( $atts, $item, $args, $depth ) {
		$settings = $this->get_settings();
		
		$classes = $depth ? 'pp-sub-item' : 'pp-menu-item';

		if ( in_array( 'current-menu-item', $item->classes ) ) {
			$classes .= '  pp-menu-item-active';
		}

		if ( empty( $atts['class'] ) ) {
			$atts['class'] = $classes;
		} else {
			$atts['class'] .= ' ' . $classes;
		}

		return $atts;
	}

	public function handle_sub_menu_classes( $classes ) {
		$classes[] = 'pp-advanced-menu--dropdown';

		return $classes;
	}

	public function render_plain_content() {}
}