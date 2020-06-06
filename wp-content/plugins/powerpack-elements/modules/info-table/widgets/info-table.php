<?php
namespace PowerpackElements\Modules\InfoTable\Widgets;

use PowerpackElements\Base\Powerpack_Widget;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
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
 * Info Table Widget
 */
class Info_Table extends Powerpack_Widget {

	/**
	 * Retrieve info table widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Info_Table' );
	}

	/**
	 * Retrieve info table widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Info_Table' );
	}

	/**
	 * Retrieve the list of categories the info table widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return parent::get_widget_categories( 'Info_Table' );
	}

	/**
	 * Retrieve info table widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Info_Table' );
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
		return parent::get_widget_keywords( 'Info_Table' );
	}

	/**
	 * Register info table widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() {

		/*-----------------------------------------------------------------------------------*/
		/*	CONTENT TAB
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Content Tab: Icon
		 */
		$this->start_controls_section(
			'section_price_menu',
			[
				'label' => __( 'Icon', 'powerpack' ),
			]
		);
		$this->add_control(
			'icon_type',
			[
				'label'       => esc_html__( 'Icon Type', 'powerpack' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'none' => [
						'title' => esc_html__( 'None', 'powerpack' ),
						'icon'  => 'fa fa-ban',
					],
					'icon' => [
						'title' => esc_html__( 'Icon', 'powerpack' ),
						'icon'  => 'fa fa-gear',
					],
					'image' => [
						'title' => esc_html__( 'Image', 'powerpack' ),
						'icon'  => 'fa fa-picture-o',
					],
					'text' => [
						'title' => esc_html__( 'Text', 'powerpack' ),
						'icon'  => 'fa fa-font',
					],
				],
				'default'     => 'icon',
			]
		);
		
		$this->add_control(
			'selected_icon',
			[
				'label'					=> __( 'Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'fa4compatibility'		=> 'icon',
				'default'				=> [
					'value'		=> 'fas fa-star',
					'library'	=> 'fa-solid',
				],
                'condition'             => [
                    'icon_type'     => 'icon',
                ],
			]
		);
        
		$this->add_control(
			'image',
			[
				'label'     => __( 'Image', 'powerpack' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image', // Usage: '{name}_size' and '{name}_custom_dimension', in this case 'image_size' and 'image_custom_dimension'.
				'default'   => 'full',
				'separator' => 'none',
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);
        
		$this->add_control(
			'icon_text',
			[
				'label'     => __( 'Icon Text', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '1',
				'condition' => [
					'icon_type' => 'text',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Content Tab: Content
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'powerpack' ),
			]
		);
		$this->add_control(
			'heading',
			[
				'label'   => __( 'Title', 'powerpack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Title', 'powerpack' ),
			]
		);
		$this->add_control(
			'title_html_tag',
			[
				'label'   => __( 'Title HTML Tag', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h4',
				'options' => [
					'h1'   => __( 'H1', 'powerpack' ),
					'h2'   => __( 'H2', 'powerpack' ),
					'h3'   => __( 'H3', 'powerpack' ),
					'h4'   => __( 'H4', 'powerpack' ),
					'h5'   => __( 'H5', 'powerpack' ),
					'h6'   => __( 'H6', 'powerpack' ),
					'div'  => __( 'div', 'powerpack' ),
					'span' => __( 'span', 'powerpack' ),
					'p'    => __( 'p', 'powerpack' ),
				],
			]
		);
		$this->add_control(
			'sub_heading',
			[
				'label'   => __( 'Subtitle', 'powerpack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Subtitle', 'powerpack' ),
			]
		);
		$this->add_control(
			'sub_title_html_tag',
			[
				'label'     => __( 'Subtitle HTML Tag', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h5',
				'options'   => [
					'h1'   => __( 'H1', 'powerpack' ),
					'h2'   => __( 'H2', 'powerpack' ),
					'h3'   => __( 'H3', 'powerpack' ),
					'h4'   => __( 'H4', 'powerpack' ),
					'h5'   => __( 'H5', 'powerpack' ),
					'h6'   => __( 'H6', 'powerpack' ),
					'div'  => __( 'div', 'powerpack' ),
					'span' => __( 'span', 'powerpack' ),
					'p'    => __( 'p', 'powerpack' ),
				],
				'condition' => [
					'sub_heading!' => '',
				],
			]
		);
		$this->add_control(
			'description',
			[
				'label'   => __( 'Description', 'powerpack' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Enter description', 'powerpack' ),
			]
		);
		$this->add_responsive_control(
			'sale_badge',
			[
				'label'     => __( 'Sale Badge', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'show'    => [
						'title' => __( 'Show', 'powerpack' ),
						'icon'  => 'fa fa-eye',
					],
					'hide'  => [
						'title' => __( 'Hide', 'powerpack' ),
						'icon'  => 'fa fa-eye-slash',
					],
				],
				'default'   => 'show',
			]
		);
		$this->add_control(
			'sale_badge_text',
			[
				'label'   => __( 'Sale Badge Text', 'powerpack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Sale', 'powerpack' ),
				'condition' => [
					'sale_badge' => 'show',
				],
			]
		);
		$this->end_controls_section();
		/**
		 * Content Tab: Link
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_link',
			[
				'label' => __( 'Link', 'powerpack' ),
			]
		);
		$this->add_control(
			'link_type',
			[
				'label'   => __( 'Link Type', 'powerpack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => __( 'None', 'powerpack' ),
					'box'   => __( 'Box', 'powerpack' ),
					'title' => __( 'Title', 'powerpack' ),
					'button'    => __( 'Button', 'powerpack' ),
				],
			]
		);
		$this->add_control(
			'link',
			[
				'label'       => __( 'Link', 'powerpack' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://www.your-link.com',
				'default'     => [
					'url' => '#',
				],
				'condition'   => [
					'link_type!' => 'none',
				],
			]
		);
		$this->add_control(
			'button_text',
			[
				'label'     => __( 'Button Text', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Get Started', 'powerpack' ),
				'condition' => [
					'link_type' => 'button',
				],
			]
		);
		
		$this->add_control(
			'select_button_icon',
			[
				'label'					=> __( 'Button', 'powerpack' ) . ' ' . __( 'Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'fa4compatibility'		=> 'button_icon',
                'condition'             => [
                    'link_type'   => 'button',
                ],
			]
		);
		
		$this->add_control(
			'button_icon_position',
			[
				'label'     => __( 'Icon Position', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'after',
				'options'   => [
					'after'  => __( 'After', 'powerpack' ),
					'before' => __( 'Before', 'powerpack' ),
				],
				'condition' => [
					'link_type'    => 'button',
					'button_icon!' => '',
				],
			]
		);
		$this->end_controls_section();
		/*-----------------------------------------------------------------------------------*/
		/*	STYLE TAB
		/*-----------------------------------------------------------------------------------*/
		/**
		 * Style Tab: Info Table
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_info_table_style',
			[
				'label' => __( 'Info Table', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'    => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center'  => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'   => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .pp-info-table' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_info_table_style' );
		/**
		 * Style Control Tab: Normal
		 * -------------------------------------------------
		 */
			$this->start_controls_tab(
				'tab_info_table_normal',
				[
					'label' => __( 'Normal', 'powerpack' ),
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'info_table_bg',
					'label'    => __( 'Background', 'powerpack' ),
					'types'    => [ 'none','classic','gradient' ],
					'selector' => '{{WRAPPER}} .pp-info-table-container',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'        => 'info_table_border',
					'label'       => __( 'Border', 'powerpack' ),
					'placeholder' => '1px',
					'default'     => '1px',
					'selector'    => '{{WRAPPER}} .pp-info-table-container',
				]
			);
			$this->add_control(
				'info_table_border_radius',
				[
					'label'      => __( 'Border Radius', 'powerpack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .pp-info-table-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'info_table_shadow',
					'selector' => '{{WRAPPER}} .pp-info-table-container',
				]
			);
			$this->end_controls_tab();
		/**
		 * Style Control Tab: Hover
		 * -------------------------------------------------
		 */
			$this->start_controls_tab(
				'tab_info_table_hover',
				[
					'label' => __( 'Hover', 'powerpack' ),
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'     => 'info_table_bg_hover',
					'label'    => __( 'Background', 'powerpack' ),
					'types'    => [ 'none','classic','gradient' ],
					'selector' => '{{WRAPPER}} .pp-info-table-container:hover',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'        => 'info_table_border_hover',
					'label'       => __( 'Border', 'powerpack' ),
					'placeholder' => '1px',
					'default'     => '1px',
					'selector'    => '{{WRAPPER}} .pp-info-table-container:hover',
				]
			);
			$this->add_control(
				'info_table_border_radius_hover',
				[
					'label'      => __( 'Border Radius', 'powerpack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .pp-info-table-container:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'     => 'info_table_shadow_hover',
					'selector' => '{{WRAPPER}} .pp-info-table-container:hover',
				]
			);
			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_responsive_control(
				'info_table_padding',
				[
					'label'      => __( 'Padding', 'powerpack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'separator'  => 'before',
					'selectors'  => [
						'{{WRAPPER}} .pp-info-table-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

		$this->end_controls_section();

		/**
		 * Style Tab: Title
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_info_table_title_style',
			[
				'label' => __( 'Title', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'title_box_bg_color',
			[
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-title-wrap' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => __( 'Typography', 'powerpack' ),
				'scheme'   => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .pp-info-table-title',
			]
		);
		$this->add_responsive_control(
			'title_box_padding',
			[
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-title-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'title_margin',
			[
				'label'      => __( 'Margin Bottom', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 20,
				],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'%' => [
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_control(
			'subtitle_heading',
			[
				'label'     => __( 'Sub Title', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'sub_heading!' => '',
				],
			]
		);

		$this->add_control(
			'subtitle_color',
			[
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'condition' => [
					'sub_heading!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-subtitle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'subtitle_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'condition' => [
					'sub_heading!' => '',
				],
				'selector'  => '{{WRAPPER}} .pp-info-table-subtitle',
			]
		);

		$this->add_responsive_control(
			'subtitle_margin',
			[
				'label'      => __( 'Margin Bottom', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 20,
				],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'%' => [
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					],
				],
				'size_units' => [ 'px', '%' ],
				'condition'  => [
					'sub_heading!' => '',
				],
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->end_controls_section();

		/**
		 * Style Tab: Icon / Image
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_info_table_icon_style',
			[
				'label'     => __( 'Icon / Image', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'icon_type!' => 'none',
				],
			]
		);
		$this->add_control(
			'icon_box_bg_color',
			[
				'label'     => __( 'Box Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-icon-container' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'icon_box_padding',
			[
				'label'      => __( 'Box Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-icon-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => __( 'Icon Size', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 5,
						'max'  => 100,
						'step' => 1,
					],
				],
				'size_units' => [ 'px', 'em' ],
				'condition'  => [
					'icon_type' => 'icon',
				],
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'icon_box_size',
			[
				'label'      => __( 'Icon Box Size', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 5,
						'max'  => 100,
						'step' => 1,
					],
				],
				'size_units' => [ 'px', 'em' ],
				'condition'  => [
					'icon_type' => 'icon',
				],
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-icon-wrap' => 'height: calc({{SIZE}}{{UNIT}} * 2); width: calc({{SIZE}}{{UNIT}} * 2);',
				],
			]
		);
		$this->add_responsive_control(
			'icon_img_width',
			[
				'label'      => __( 'Image Width', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 25,
						'max'  => 600,
						'step' => 1,
					],
					'%' => [
						'min'  => 25,
						'max'  => 100,
						'step' => 1,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-icon img' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition'  => [
					'icon_type' => 'image',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'icon_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'condition' => [
					'icon_type' => 'text',
				],
				'selector'  => '{{WRAPPER}} .pp-info-table-icon',
			]
		);
		$this->start_controls_tabs( 'tabs_icon_style' );

		$this->start_controls_tab(
			'tab_icon_normal',
			[
				'label' => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'icon_color_normal',
			[
				'label'     => __( 'Icon Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-info-table-icon svg' => 'fill: {{VALUE}}',
				],
				'condition' => [
					'icon_type!' => 'image',
				],
			]
		);
		
		$this->add_control(
			'icon_bg_color_normal',
			[
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-icon-wrap' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'icon_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'condition'   => [
					'icon_type!' => 'none',
				],
				'selector'    => '{{WRAPPER}} .pp-info-table-icon-container',
			]
		);

		$this->add_control(
			'icon_border_radius',
			[
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'condition'  => [
					'icon_type!' => 'none',
				],
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-icon-wrap, {{WRAPPER}} .pp-info-table-icon-wrap img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_rotation',
			[
				'label'      => __( 'Icon Rotation', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 360,
						'step' => 1,
					],
				],
				'size_units' => '',
				'condition'  => [
					'icon_type!' => 'none',
					'icon_type!' => 'text',
				],
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-icon span' => 'transform: rotate( {{SIZE}}deg );',
					'{{WRAPPER}} .pp-info-table-icon img' => 'transform: rotate( {{SIZE}}deg );',
				],
			]
		);
		$this->add_responsive_control(
			'img_inside_padding',
			[
				'label'      => __( 'Inside Spacing', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'condition'  => [
					'icon_type!' => 'icon',
				],
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-icon-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_icon_hover',
			[
				'label' => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label'     => __( 'Icon Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-icon-wrap:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-info-table-icon-wrap:hover svg' => 'fill: {{VALUE}}',
				],
				'condition' => [
					'icon_type!' => 'image',
				],
			]
		);

		$this->add_control(
			'icon_bg_color_hover',
			[
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'condition' => [
					'icon_type!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-icon-wrap:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_border_color_hover',
			[
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'condition' => [
					'icon_type!' => 'none',
				],
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-icon-wrap:hover .fa' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'hover_animation_icon',
			[
				'label' => __( 'Icon Animation', 'powerpack' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Style Tab: Description
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_info_description_style',
			[
				'label'     => __( 'Description', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'description!' => '',
				],
			]
		);
		$this->add_control(
			'description_bg_color',
			[
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-description' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'description_color',
			[
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-description' => 'color: {{VALUE}}',
				],
				'condition' => [
					'description!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'description_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .pp-info-table-description',
				'condition' => [
					'description!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'description_padding',
			[
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'description_margin',
			[
				'label'      => __( 'Margin Bottom', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size' => 20,
				],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'%' => [
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-description' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->end_controls_section();

		/**
		 * Style Tab: Button
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_info_table_button_style',
			[
				'label'     => __( 'Button', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'link_type'    => 'button',
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_size',
			[
				'label'     => __( 'Size', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'md',
				'options'   => [
					'xs' => __( 'Extra Small', 'powerpack' ),
					'sm' => __( 'Small', 'powerpack' ),
					'md' => __( 'Medium', 'powerpack' ),
					'lg' => __( 'Large', 'powerpack' ),
					'xl' => __( 'Extra Large', 'powerpack' ),
				],
				'condition' => [
					'link_type'    => 'button',
					'button_text!' => '',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label'     => __( 'Normal', 'powerpack' ),
				'condition' => [
					'link_type'    => 'button',
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_bg_color_normal',
			[
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000',
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-button' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'link_type'    => 'button',
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_text_color_normal',
			[
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-button' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-info-table-button .pp-icon svg' => 'fill: {{VALUE}}',
				],
				'condition' => [
					'link_type'    => 'button',
					'button_text!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border_normal',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-info-table-button',
				'condition'   => [
					'link_type'    => 'button',
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'link_type'    => 'button',
					'button_text!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .pp-info-table-button',
				'condition' => [
					'link_type'    => 'button',
					'button_text!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'link_type'    => 'button',
					'button_text!' => '',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'button_box_shadow',
				'selector'  => '{{WRAPPER}} .pp-info-table-button',
				'condition' => [
					'link_type'    => 'button',
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'info_table_button_icon_heading',
			[
				'label'     => __( 'Button Icon', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'link_type'    => 'button',
					'button_icon!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'button_icon_margin',
			[
				'label'       => __( 'Margin', 'powerpack' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => [ 'px', '%' ],
				'placeholder' => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'condition'   => [
					'link_type'    => 'button',
					'button_icon!' => '',
				],
				'selectors'   => [
					'{{WRAPPER}} .pp-info-table .pp-button-icon' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label'     => __( 'Hover', 'powerpack' ),
				'condition' => [
					'link_type'    => 'button',
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-button:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'link_type'    => 'button',
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-button:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-info-table-button:hover .pp-icon svg' => 'fill: {{VALUE}}',
				],
				'condition' => [
					'link_type'    => 'button',
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_border_color_hover',
			[
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-button:hover' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'link_type'    => 'button',
					'button_text!' => '',
				],
			]
		);

		$this->add_control(
			'button_animation',
			[
				'label'     => __( 'Animation', 'powerpack' ),
				'type'      => Controls_Manager::HOVER_ANIMATION,
				'condition' => [
					'link_type'    => 'button',
					'button_text!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'button_box_shadow_hover',
				'selector'  => '{{WRAPPER}} .pp-info-table-button:hover',
				'condition' => [
					'link_type'    => 'button',
					'button_text!' => '',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
		/**
		 * Style Tab: Sale Badge
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_info_sale_badge_style',
			[
				'label'     => __( 'Sale Badge', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'sale_badge' => 'show',
				],
			]
		);
		$this->add_control(
			'sale_badge_bg_color',
			[
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#000',
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-sale-badge' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .pp-info-table-sale-badge.right:after' => 'border-left-color: {{VALUE}}',
					'{{WRAPPER}} .pp-info-table-sale-badge.left:after' => 'border-right-color: {{VALUE}}',
				],
				'condition' => [
					'sale_badge' => 'show',
				],
			]
		);
		$this->add_control(
			'sale_badge_color',
			[
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .pp-info-table-sale-badge' => 'color: {{VALUE}}',
				],
				'condition' => [
					'sale_badge' => 'show',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'sale_badge_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .pp-info-table-sale-badge',
				'condition' => [
					'sale_badge' => 'show',
				],
			]
		);

		$this->add_responsive_control(
			'sale_badge_padding',
			[
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-sale-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'sale_badge' => 'show',
				],
			]
		);
		$this->add_responsive_control(
			'sale_badge_width',
			[
				'label'      => __( 'Width', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 25,
						'max'  => 600,
						'step' => 1,
					],
					'%' => [
						'min'  => 25,
						'max'  => 100,
						'step' => 1,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-sale-badge' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'sale_badge' => 'show',
				],
			]
		);
		$this->add_responsive_control(
			'sale_badge_align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'    => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'right'   => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => 'right',
			]
		);
		$this->add_responsive_control(
			'sale_badge_position',
			[
				'label'      => __( 'Position From Top', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'%' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'size_units' => [ '%', '' ],
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-sale-badge' => 'top: {{SIZE}}%',
				],
				'condition' => [
					'sale_badge' => 'show',
				],
			]
		);
		$this->add_control(
			'sale_badge_border_radius',
			[
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pp-info-table-sale-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
	}

    /**
	 * Render info box icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_infotable_icon() {
        $settings = $this->get_settings_for_display();
        
        $this->add_render_attribute( 'icon', 'class', ['pp-info-table-icon', 'pp-icon'] );

		if ( $settings['hover_animation_icon'] ) {
			$this->add_render_attribute( 'icon', 'class', 'elementor-animation-' . $settings['hover_animation_icon'] );
		}
		
		if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['icon'] = 'fa fa-star';
		}

		$has_icon = ! empty( $settings['icon'] );
		
		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}

		$icon_attributes = $this->get_render_attribute_string( 'icon' );
		
		if ( ! $has_icon && ! empty( $settings['selected_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
		$is_new = ! isset( $settings['icon'] ) && Icons_Manager::is_migration_allowed();
		?>
		<?php if ( 'none' != $settings['icon_type'] ) { ?>
			<div class="pp-info-table-icon-container">
				<div class="pp-info-table-icon-inner">
					<div class="pp-info-table-icon-wrap">
						<span <?php echo $this->get_render_attribute_string( 'icon' ); ?>>
							<?php if ( 'icon' == $settings['icon_type'] ) { ?>
								<?php
								if ( $is_new || $migrated ) {
									Icons_Manager::render_icon( $settings['selected_icon'], [ 'aria-hidden' => 'true' ] );
								} elseif ( ! empty( $settings['icon'] ) ) {
									?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
								}
								?>
							<?php } elseif ( 'image' == $settings['icon_type'] ) { ?>
								<?php
									if ( ! empty( $settings['image']['url'] ) ) {
										echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'image', 'image' );
									}                                                  
								?>
							<?php } elseif ( 'text' == $settings['icon_type'] ) { ?>
								<span class="pp-icon-text">
									<?php echo $settings['icon_text']; ?>
								</span>
							<?php } ?>
						</span>
					</div>
				</div>
			</div>
		<?php }
    }

	/**
	 * Render info table widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();
		$this->add_render_attribute( 'info-table', 'class', 'pp-info-table' );
		$this->add_render_attribute( 'info-table-container', 'class', 'pp-info-table-container' );
		$this->add_render_attribute( 'info-table-link', 'class', 'pp-info-table-link' );
		$this->add_render_attribute( 'title-container', 'class', 'pp-info-table-title-container' );

		$pp_if_html_tag = 'div';
		$pp_if_html_tag_a = 'div';
		$pp_title_html_tag = 'div';
		$pp_button_html_tag = 'div';

		$this->add_inline_editing_attributes( 'icon_text', 'none' );
		$this->add_render_attribute( 'icon_text', 'class', 'pp-icon-text' );
		$this->add_inline_editing_attributes( 'heading', 'none' );
		$this->add_render_attribute( 'heading', 'class', 'pp-info-table-title' );
		$this->add_inline_editing_attributes( 'sub_heading', 'none' );
		$this->add_render_attribute( 'sub_heading', 'class', 'pp-info-table-subtitle' );
		$this->add_inline_editing_attributes( 'description', 'basic' );
		$this->add_render_attribute( 'description', 'class', 'pp-info-table-description' );
		$this->add_inline_editing_attributes( 'button_text', 'none' );
		$this->add_render_attribute( 'button_text', 'class', 'pp-button-text' );

		$this->add_render_attribute( 'info-table-button', 'class', [
				'pp-info-table-button',
				'elementor-button',
				'elementor-size-' . $settings['button_size'],
			]
		);

		if ( $settings['button_animation'] ) {
			$this->add_render_attribute( 'info-table-button', 'class', 'elementor-animation-' . $settings['button_animation'] );
		}

		if ( 'none' != $settings['link_type'] ) {

			if ( ! empty( $settings['link']['url'] ) ) {

				if ( 'box' == $settings['link_type'] ) {

					$pp_if_html_tag_a = 'a';
					$this->add_link_attributes( 'info-table-container-a', $settings['link'] );
					
				} elseif ( 'title' == $settings['link_type'] ) {

					$pp_title_html_tag = 'a';
					$this->add_link_attributes( 'title-container', $settings['link'] );
					
				} elseif ( 'button' == $settings['link_type'] ) {

					$pp_button_html_tag = 'a';
					
					$this->add_link_attributes( 'info-table-button', $settings['link'] );
					
				} // End if().
			} // End if().
		} // End if().
		
		if ( ! isset( $settings['button_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['button_icon'] = '';
		}

		$has_button_icon = ! empty( $settings['button_icon'] );
		
		if ( $has_button_icon ) {
			$this->add_render_attribute( 'button-icon', 'class', $settings['button_icon'] );
			$this->add_render_attribute( 'button-icon', 'aria-hidden', 'true' );
		}
		
		if ( ! $has_button_icon && ! empty( $settings['select_button_icon']['value'] ) ) {
			$has_button_icon = true;
		}
		$button_icon_migrated = isset( $settings['__fa4_migrated']['select_button_icon'] );
		$is_new_button_icon = ! isset( $settings['button_icon'] ) && Icons_Manager::is_migration_allowed();
		?>
		<<?php echo $pp_if_html_tag . ' ' . $this->get_render_attribute_string( 'info-table-container' ); ?>>
		<?php if ( $pp_if_html_tag_a ) { ?><<?php echo $pp_if_html_tag_a . ' ' . $this->get_render_attribute_string( 'info-table-container-a' ); ?>> <?php } ?>
			<div <?php echo $this->get_render_attribute_string( 'info-table' ); ?>>
				<?php if ( 'show' == $settings['sale_badge'] && ! empty( $settings['sale_badge_text'] ) ) {
					if ( 'right' == $settings['sale_badge_align'] ) { ?>
						<div class='pp-info-table-sale-badge right'>
							<?php } elseif ( 'left' == $settings['sale_badge_align'] ) { ?>
						<div class='pp-info-table-sale-badge left'>
					<?php } ?>
							<p><?php echo $this->parse_text_editor( nl2br( $settings['sale_badge_text'] ) ); ?></p>
						</div>
				<?php } ?>
				<div class="pp-info-table-title-wrap">
				<?php
				if ( ! empty( $settings['heading'] ) ) {
					echo '<' . $pp_title_html_tag . ' ' . $this->get_render_attribute_string( 'title-container' ) . '>';
					printf( '<%1$s %2$s>', $settings['title_html_tag'], $this->get_render_attribute_string( 'heading' ) );
					echo esc_attr( $settings['heading'] );
					printf( '</%1$s>', $settings['title_html_tag'] );
					echo '</' . $pp_title_html_tag . '>';
				}
				if ( ! empty( $settings['sub_heading'] ) ) {
					printf( '<%1$s %2$s>', $settings['sub_title_html_tag'], $this->get_render_attribute_string( 'sub_heading' ) );
					echo esc_attr( $settings['sub_heading'] );
					printf( '</%1$s>', $settings['sub_title_html_tag'] );
				}
				?>
				</div>

				<?php $this->render_infotable_icon(); ?>

				<?php if ( ! empty( $settings['description'] ) ) { ?>
					<div <?php echo $this->get_render_attribute_string( 'description' ); ?>>
						<?php echo $this->parse_text_editor( nl2br( $settings['description'] ) ); ?>
					</div>
				<?php } ?>
				<?php if ( 'button' == $settings['link_type'] ) { ?>
					<div class="pp-info-table-footer">
						<<?php echo $pp_button_html_tag . ' ' . $this->get_render_attribute_string( 'info-table-button' ); ?>>
							<?php if ( $has_button_icon && 'before' == $settings['button_icon_position'] ) { ?>
								<span class='pp-button-icon pp-icon pp-no-trans'>
									<?php
									if ( $is_new_button_icon || $button_icon_migrated ) {
										Icons_Manager::render_icon( $settings['select_button_icon'], [ 'aria-hidden' => 'true' ] );
									} elseif ( ! empty( $settings['button_icon'] ) ) {
										?><i <?php echo $this->get_render_attribute_string( 'button-icon' ); ?>></i><?php
									}
									?>
								</span>
							<?php } ?>
							<?php if ( ! empty( $settings['button_text'] ) ) { ?>
								<span <?php echo $this->get_render_attribute_string( 'button_text' ); ?>>
									<?php echo esc_attr( $settings['button_text'] ); ?>
								</span>
							<?php } ?>
							<?php if ( $has_button_icon && 'after' == $settings['button_icon_position'] ) { ?>
								<span class='pp-button-icon pp-icon pp-no-trans'>
									<?php
									if ( $is_new_button_icon || $button_icon_migrated ) {
										Icons_Manager::render_icon( $settings['select_button_icon'], [ 'aria-hidden' => 'true' ] );
									} elseif ( ! empty( $settings['button_icon'] ) ) {
										?><i <?php echo $this->get_render_attribute_string( 'button-icon' ); ?>></i><?php
									}
									?>
								</span>
							<?php } ?>
						</<?php echo $pp_button_html_tag; ?>>
					</div>
				<?php } ?>
			</div>
			<?php if ( $pp_if_html_tag_a ) { ?></<?php echo $pp_if_html_tag_a; ?>> <?php } ?>
		</<?php echo $pp_if_html_tag; ?>>
		<?php
	}

	/**
	 * Render info table widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function _content_template() {
		?>
		<#
		   view.addRenderAttribute( 'info-table', 'class', 'pp-info-table' );
		   view.addRenderAttribute( 'info-table-container', 'class', 'pp-info-table-container' );
		   view.addRenderAttribute( 'info-table-link', 'class', 'pp-info-table-link' );
		   view.addRenderAttribute( 'title-container', 'class', 'pp-info-table-title-container' );
		   
		   var pp_if_html_tag = 'div',
               pp_if_html_tag_a = 'div',
               pp_title_html_tag = 'div',
               pp_button_html_tag = 'div',
               iconHTML = elementor.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
               migrated = elementor.helpers.isIconMigrated( settings, 'selected_icon' ),
               buttonIconHTML = elementor.helpers.renderIcon( view, settings.select_button_icon, { 'aria-hidden': true }, 'i' , 'object' ),
               buttonIconMigrated = elementor.helpers.isIconMigrated( settings, 'select_button_icon' );
		   
		   view.addInlineEditingAttributes( 'icon_text', 'none' );
		   view.addRenderAttribute( 'icon_text', 'class', 'pp-icon-text' );
		   view.addInlineEditingAttributes( 'heading', 'none' );
		   view.addRenderAttribute( 'heading', 'class', 'pp-info-table-title' );
		   view.addInlineEditingAttributes( 'sub_heading', 'none' );
		   view.addRenderAttribute( 'sub_heading', 'class', 'pp-info-table-subtitle' );
		   view.addInlineEditingAttributes( 'description', 'basic' );
		   view.addRenderAttribute( 'description', 'class', 'pp-info-table-description' );
		   view.addInlineEditingAttributes( 'button_text', 'none' );
		   view.addRenderAttribute( 'button_text', 'class', 'pp-button-text' );

		   view.addRenderAttribute( 'icon', 'class', 'pp-info-table-icon pp-icon' );

		   if ( settings.hover_animation_icon ) {
		   		view.addRenderAttribute( 'icon', 'class', 'elementor-animation-' + settings.hover_animation_icon );
			}
		   
		   view.addRenderAttribute( 'info-table-button', 'class', [ 'pp-info-table-button', 'elementor-button', 'elementor-size-' + settings.button_size ] );
		   
		   if ( settings.button_animation ) {
			   view.addRenderAttribute( 'info-table-button', 'class', 'elementor-animation-' + settings.button_animation );
		   }
		   
		   if (settings.link_type != 'none' ) {
			   if ( settings.link.url != '' ) {
					if ( settings.link_type == 'box' ) {
						var pp_if_html_tag = 'a';
		   				view.addRenderAttribute( 'info-table-container-a', 'href', settings.link.url );

		   				if ( settings.link.is_external ) {
							view.addRenderAttribute( 'info-table-container-a', 'target', '_blank' );
						}
						if ( settings.link.nofollow ) {
							view.addRenderAttribute( 'info-table-container-a', 'rel', 'nofollow' );
						}
					}
					else if ( settings.link_type == 'title' ) {
						var pp_title_html_tag = 'a';
						view.addRenderAttribute( 'title-container', 'href', settings.link.url );

						if ( settings.link.is_external ) {
							view.addRenderAttribute( 'title-container', 'target', '_blank' );
						}
						if ( settings.link.nofollow ) {
							view.addRenderAttribute( 'title-container', 'rel', 'nofollow' );
						}
					}
					else if ( settings.link_type == 'button' ) {
						var pp_button_html_tag = 'a';
						view.addRenderAttribute( 'info-table-button', 'href', settings.link.url );

						if ( settings.link.is_external ) {
							view.addRenderAttribute( 'info-table-button', 'target', '_blank' );
						}
						if ( settings.link.nofollow ) {
							view.addRenderAttribute( 'info-table-button', 'rel', 'nofollow' );
						}
					}
				}
            }
		#>
		<{{{pp_if_html_tag}}} {{{view.getRenderAttributeString('info-table-container')}}}>
			<{{{pp_if_html_tag_a}}} {{{view.getRenderAttributeString('info-table-container-a')}}}>
			<div {{{view.getRenderAttributeString('info-table')}}}>
				<#
				  	if ( settings.sale_badge == 'show' && settings.sale_badge_text != '' ) {
					if ( settings.sale_badge_align == 'right' ) { #>
						<div class='pp-info-table-sale-badge right'>
						<# } else if ( settings.sale_badge_align == 'left' ) { #>
						<div class='pp-info-table-sale-badge left'>
						<# } #>
							<p>{{{ settings.sale_badge_text }}}</p>
						</div>
				<# } #>
				<div class="pp-info-table-title-wrap">
				<#
				if ( settings.heading != '' ) { #>
					<{{{pp_title_html_tag}}} {{{view.getRenderAttributeString('title-container')}}}>
						<{{{settings.title_html_tag}}} {{{view.getRenderAttributeString('heading')}}}>
							{{{ settings.heading }}}
						</{{{settings.title_html_tag}}}>
					</{{{pp_title_html_tag}}}>
					<#
				}
				if ( settings.sub_heading != '' ) { #>
					<{{{settings.sub_title_html_tag}}} {{{view.getRenderAttributeString('sub_heading')}}}>
						{{{ settings.sub_heading }}}
					</{{{settings.sub_title_html_tag}}}>
					<#
				}
				#>
				</div>
				<# if ( settings.icon_type != 'none' ) { #>
					<div class="pp-info-table-icon-container">
						<div class="pp-info-table-icon-inner">
							<div class="pp-info-table-icon-wrap">
								<span {{{view.getRenderAttributeString('icon')}}}>
									<# if ( settings.icon_type == 'icon' ) { #>
										<# if ( settings.icon || settings.selected_icon ) { #>
										<# if ( iconHTML && iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
										{{{ iconHTML.value }}}
										<# } else { #>
											<i class="{{ settings.icon }}" aria-hidden="true"></i>
										<# } #>
										<# } #>
									<# } else if ( settings.icon_type == 'image' ) { #>
										<#
										var image = {
											id: settings.image.id,
											url: settings.image.url,
											size: settings.image_size,
											dimension: settings.image_custom_dimension,
											model: view.getEditModel()
										};
										var image_url = elementor.imagesManager.getImageUrl( image );
										#>
										<img src="{{{ image_url }}}" />
									<# } else if ( settings.icon_type == 'text' ) { #>
										<span class="pp-icon-text">
											{{{ settings.icon_text }}}
										</span>
									<# } #>
								</span>
							</div>
						</div>
					</div>
				<# } #>

				<# if ( settings.description != '' ) { #>
					<div {{{view.getRenderAttributeString('description')}}}>
						{{{ settings.description }}}
					</div>
				<# } #>
					
				<# if ( settings.link_type == 'button' ) { #>
					<div class="pp-info-table-footer">
						<{{{pp_button_html_tag}}} {{{view.getRenderAttributeString('info-table-button')}}}>
							<# if ( settings.button_icon_position == 'before' ) { #>
								<# if ( settings.button_icon || settings.select_button_icon ) { #>
								<span class='pp-button-icon pp-icon pp-no-trans'>
									<# if ( buttonIconHTML && buttonIconHTML.rendered && ( ! settings.button_icon || buttonIconMigrated ) ) { #>
									{{{ buttonIconHTML.value }}}
									<# } else { #>
										<i class="{{ settings.select_button_icon }}" aria-hidden="true"></i>
									<# } #>
								</span>
								<# } #>
							<# } #>
							<# if ( settings.button_text != '' ) { #>
								<span {{{view.getRenderAttributeString('button_text')}}}>
									 {{{ settings.button_text }}}
								</span>
							<# } #>
							<# if ( settings.button_icon_position == 'after' ) { #>
								<# if ( settings.button_icon || settings.select_button_icon ) { #>
								<span class='pp-button-icon pp-icon pp-no-trans'>
									<# if ( buttonIconHTML && buttonIconHTML.rendered && ( ! settings.button_icon || buttonIconMigrated ) ) { #>
									{{{ buttonIconHTML.value }}}
									<# } else { #>
										<i class="{{ settings.select_button_icon }}" aria-hidden="true"></i>
									<# } #>
								</span>
								<# } #>
							<# } #>
						</{{{pp_button_html_tag}}}>
					</div>
				<# } #>
			</div>
			<# if ( pp_if_html_tag_a ) { #></{{{pp_if_html_tag_a}}}> <# } #>
		</{{{pp_if_html_tag}}}>
		<?php
	}
}