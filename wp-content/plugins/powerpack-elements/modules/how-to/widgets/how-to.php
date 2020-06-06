<?php
namespace PowerpackElements\Modules\HowTo\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Posts_Helper;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Control_Media;
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
 * How To Widget
 */
class How_To extends Powerpack_Widget {

	private $_schema_rendered = false;

	/**
	 * Retrieve How To widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'How_To' );
	}

	/**
	 * Retrieve How To widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'How_To' );
	}

	/**
	 * Retrieve the list of categories the How To widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return parent::get_widget_categories( 'How_To' );
	}

	/**
	 * Retrieve How To widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'How_To' );
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
		return parent::get_widget_keywords( 'How_To' );
	}

	/**
	 * Retrieve the list of scripts the How To widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [
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

		/*-----------------------------------------------------------------------------------*/
		/*	CONTENT TAB
		/*-----------------------------------------------------------------------------------*/
		/**
		 * Content Tab: General
		 */
		$this->start_controls_section(
			'section_schema_markup',
			[
				'label' => esc_html__( 'Schema Markup', 'powerpack' ),
			]
		);

		$this->add_control(
			'enable_schema',
			[
				'label'       => __( 'Enable Schema Markup', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'yes',
				'label_block' => true,
				'options'     => [
					'yes' => __( 'Yes', 'powerpack' ),
					'no'  => __( 'No', 'powerpack' ),
				],
				'description' => __( 'Enable Schema Markup option if you are setting up a unique "HowTo" page on your website. The Module adds "HowTo" Page schema to the page as per Google\'s Structured Data guideline. <a target="_blank" rel="noopener" href="https://developers.google.com/search/docs/data-types/how-to"><b style="color: #d30c5c;">Click here</b></a> for more details. <p style="font-style: normal; padding: 10px; background: #fffbd4; color: #333; margin-top: 10px; border: 1px solid #FFEB3B; border-radius: 5px; font-size: 12px;">To use schema markup, your page must have only single instance of HowTo widget.</p>', 'powerpack' ),
			]
		);

		$this->end_controls_section();

		/**
		 * Content Tab: How To
		 */
		$this->start_controls_section(
			'section_how_to',
			[
				'label' => esc_html__( 'How To', 'powerpack' ),
			]
		);

		$this->add_control(
			'how_to_title',
			[
				'label'   => __( 'Title', 'powerpack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'How To', 'powerpack' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'how_to_description',
			[
				'label'   => esc_html__( 'Description', 'powerpack' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'powerpack' ),
				'dynamic' => [ 'active' => true ],
			]
		);
		$this->add_control(
			'how_to_image',
			[
				'label'   => __( 'Image', 'powerpack' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'    => 'how_to_image_size',
				'label'   => __( 'Image Size', 'powerpack' ),
				'default' => 'full',
			]
		);
		$this->add_control(
			'show_advanced',
			[
				'label'       => __( 'Show Advanced Options', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'no',
				'label_block' => true,
				'options'     => [
					'yes' => __( 'Yes', 'powerpack' ),
					'no'  => __( 'No', 'powerpack' ),
				],
				'separator'   => 'before',
			]
		);
		$this->end_controls_section();

		/**
		 * Content Tab: Advanced Options
		 */
		$this->start_controls_section(
			'advanced_options',
			[
				'label'     => __( 'Advanced Options', 'powerpack' ),
				'condition' => [
					'show_advanced' => 'yes',
				],
			]
		);
		$this->add_control(
			'total_time_text',
			[
				'label'   => __( 'Total Time Text', 'powerpack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Time Needed:', 'powerpack' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'total_time_heading',
			[
				'label'       => __( 'Duration', 'powerpack' ),
				'type'        => Controls_Manager::HEADING,
				'description' => __( 'How much time this process will take', 'powerpack' ),
				'separator'   => 'before',
			]
		);
		$this->add_control(
			'total_time_years',
			[
				'label'   => __( 'Years', 'powerpack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => '',
				'units'   => array( 'years' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'total_time_months',
			[
				'label'   => __( 'Months', 'powerpack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => '',
				'units'   => array( 'months' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'total_time_days',
			[
				'label'   => __( 'Days', 'powerpack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => '',
				'units'   => array( 'days' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'total_time_hours',
			[
				'label'   => __( 'Hours', 'powerpack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => __( '1', 'powerpack' ),
				'units'   => array( 'hours' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'total_time_minutes',
			[
				'label'   => __( 'Minutes', 'powerpack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => __( '30', 'powerpack' ),
				'units'   => array( 'minutes' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'estimated_cost_text',
			[
				'label'     => __( 'Estimated Cost Text', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Total Cost:', 'powerpack' ),
				'dynamic'   => [
					'active' => true,
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'estimated_cost',
			[
				'label'       => __( 'Estimated Cost', 'powerpack' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'How much cost of this.', 'powerpack' ),
				'default'     => __( '100', 'powerpack' ),
				'dynamic'     => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'currency_iso_code',
			[
				'label'       => __( 'Currency ISO Code', 'powerpack' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( '$', 'powerpack' ),
				'description' => __( 'For your country ISO code <a href="https://en.wikipedia.org/wiki/List_of_circulating_currencies" target="_blank" rel="noopener"><b style="color: #d30c5c;">Click here</b></a>', 'powerpack' ),
				'dynamic'     => [
					'active' => true,
				],
				'separator'   => 'after',
			]
		);
		$this->add_control(
			'add_supply',
			[
				'label'       => __( 'Add Supply', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'no',
				'label_block' => true,
				'options'     => [
					'yes' => __( 'Yes', 'powerpack' ),
					'no'  => __( 'No', 'powerpack' ),
				],
			]
		);
		$this->add_control(
			'supply_title',
			[
				'label'     => __( 'Supply Title', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Necessary Supply Items', 'powerpack' ),
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'add_supply' => 'yes',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'supply_text',
			[
				'label'   => __( 'Supply', 'powerpack' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'supply',
			[
				'label'       => esc_html__( 'Add Supply', 'powerpack' ),
				'type'        => Controls_Manager::REPEATER,
				'default'     => [
					[ 'supply_text' => esc_html__( 'Supply 1', 'powerpack' ) ],
					[ 'supply_text' => esc_html__( 'Supply 2', 'powerpack' ) ],
					[ 'supply_text' => esc_html__( 'Supply 3', 'powerpack' ) ],
				],
				'fields'      => array_values( $repeater->get_controls() ),
				'title_field' => '{{supply_text}}',
				'condition'   => [
					'add_supply' => 'yes',
				],
			]
		);
		$this->add_control(
			'supply_icon',
			[
				'label'            => __( 'Supply Icon', 'powerpack' ),
				'type'             => Controls_Manager::ICONS,
				'default'          => [
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				],
				'fa4compatibility' => 'list_icon',
				'condition'        => [
					'add_supply' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'supply_icon_spacing',
			[
				'label'     => __( 'Icon Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pp-supply-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'add_supply' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'supply_icon_size',
			[
				'label'     => __( 'Icon Size', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pp-supply-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'add_supply' => 'yes',
				],
			]
		);
		$this->add_control(
			'add_tools',
			[
				'label'       => __( 'Add Tools', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'no',
				'label_block' => true,
				'options'     => [
					'yes' => __( 'Yes', 'powerpack' ),
					'no'  => __( 'No', 'powerpack' ),
				],
				'separator'   => 'before',
			]
		);
		$this->add_control(
			'tool_title',
			[
				'label'     => __( 'Tool Title', 'powerpack' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Necessary Tool Items', 'powerpack' ),
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'add_tools' => 'yes',
				],
			]
		);
		$repeater = new Repeater();

		$repeater->add_control(
			'tool_text',
			[
				'label'   => __( 'Tools', 'powerpack' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'tools',
			[
				'label'       => esc_html__( 'Add Tools', 'powerpack' ),
				'type'        => Controls_Manager::REPEATER,
				'default'     => [
					[ 'tool_text' => esc_html__( 'Tool 1', 'powerpack' ) ],
					[ 'tool_text' => esc_html__( 'Tool 2', 'powerpack' ) ],
					[ 'tool_text' => esc_html__( 'Tool 3', 'powerpack' ) ],
				],
				'fields'      => array_values( $repeater->get_controls() ),
				'title_field' => '{{tool_text}}',
				'condition'   => [
					'add_tools' => 'yes',
				],
			]
		);
		$this->add_control(
			'tools_icon',
			[
				'label'            => __( 'Tool Icon', 'powerpack' ),
				'type'             => Controls_Manager::ICONS,
				'default'          => [
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				],
				'fa4compatibility' => 'list_icon',
				'condition'        => [
					'add_tools' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'tools_icon_spacing',
			[
				'label'     => __( 'Icon Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pp-tool-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'add_tools' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'tools_icon_size',
			[
				'label'     => __( 'Icon Size', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pp-tool-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'add_tools' => 'yes',
				],
			]
		);
		$this->end_controls_section();

		/**
		 * Content Tab: Steps
		 */
		$this->start_controls_section(
			'steps',
			[
				'label' => __( 'Steps', 'powerpack' ),
			]
		);
		$this->add_control(
			'step_section_title',
			[
				'label'   => __( 'Section Title', 'powerpack' ),
				'default' => __( 'Necessary Steps', 'powerpack' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$repeater = new Repeater();

		$repeater->add_control(
			'step_title',
			[
				'label'   => __( 'Title', 'powerpack' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$repeater->add_control(
			'step_description',
			[
				'label'   => esc_html__( 'Description', 'powerpack' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'powerpack' ),
				'dynamic' => [ 'active' => true ],
			]
		);
		$repeater->add_control(
			'step_image',
			[
				'label'   => __( 'Image', 'powerpack' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);
		$repeater->add_control(
			'step_link',
			[
				'label'         => __( 'Link', 'powerpack' ),
				'type'          => Controls_Manager::URL,
				'dynamic'       => [
					'active' => true,
				],
				'show_external' => true,
				'label_block'   => true,
				'placeholder'   => __( 'http://your-link.com', 'powerpack' ),
			]
		);
		$this->add_control(
			'steps_form',
			[
				'label'       => esc_html__( 'Add Steps', 'powerpack' ),
				'type'        => Controls_Manager::REPEATER,
				'default'     => [
					[ 'step_title' => esc_html__( 'Step 1', 'powerpack' ) ],
					[ 'step_title' => esc_html__( 'Step 2', 'powerpack' ) ],
				],
				'fields'      => array_values( $repeater->get_controls() ),
				'title_field' => '{{step_title}}',
			]
		);

		$this->add_control(
			'open_lightbox',
			[
				'label'     => __( 'Lightbox', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'no',
				'options'   => [
					'default' => __( 'Default', 'powerpack' ),
					'yes'     => __( 'Yes', 'powerpack' ),
					'no'      => __( 'No', 'powerpack' ),
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();

		/**
		 * Style Tab: Box
		 */
		$this->start_controls_section(
			'section_box_style',
			[
				'label' => esc_html__( 'Box', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'box_align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => 'left',
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-container' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'box_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-container' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'box_padding',
			[
				'label'      => esc_html__( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pp-how-to-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'box_border',
				'label'    => esc_html__( 'Border', 'powerpack' ),
				'selector' => '{{WRAPPER}} .pp-how-to-container',
			]
		);
		$this->add_responsive_control(
			'box_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pp-how-to-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .pp-how-to-container',
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Title
		 */
		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Title', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'title_bottom_spacing',
			[
				'label'     => __( 'Bottom Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'title_tag',
			[
				'label'       => __( 'Title HTML Tag', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h2',
				'label_block' => true,
				'options'     => [
					'h1' => __( 'H1', 'powerpack' ),
					'h2' => __( 'H2', 'powerpack' ),
					'h3' => __( 'H3', 'powerpack' ),
					'h4' => __( 'H4', 'powerpack' ),
					'h5' => __( 'H5', 'powerpack' ),
					'h6' => __( 'H6', 'powerpack' ),
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .pp-how-to-title',
			]
		);
		$this->add_responsive_control(
			'title_align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-title' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/**
		 * Style Tab: Description
		 */
		$this->start_controls_section(
			'section_description_style',
			[
				'label' => esc_html__( 'Description', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-description' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'description_bottom_spacing',
			[
				'label'     => __( 'Bottom Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .pp-how-to-description',
			]
		);
		$this->add_responsive_control(
			'description_align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-description' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/**
		 * Style Tab: Image
		 */
		$this->start_controls_section(
			'section_image_style',
			[
				'label' => esc_html__( 'Image', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'image_align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => 'left',
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-image' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'image_padding',
			[
				'label'      => esc_html__( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pp-how-to-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_border',
				'label'    => esc_html__( 'Border', 'powerpack' ),
				'selector' => '{{WRAPPER}} .pp-how-to-image img',
			]
		);
		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pp-how-to-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'image_shadow',
				'selector' => '{{WRAPPER}} .pp-how-to-image img',
			]
		);
		$this->end_controls_section();
		/**
		 * Style Tab: Advanced Options
		 */
		$this->start_controls_section(
			'section_advanced_options_style',
			[
				'label'     => esc_html__( 'Advanced Options', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_advanced' => 'yes',
				],
			]
		);
		$this->add_control(
			'total_time_color',
			[
				'label'     => esc_html__( 'Total Time Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-total-time' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'total_time_bottom_spacing',
			[
				'label'     => __( 'Total Time Bottom Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-total-time' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'total_time_typography',
				'selector' => '{{WRAPPER}} .pp-how-to-total-time',
			]
		);
		$this->add_responsive_control(
			'total_time_align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-total-time' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'estimated_cost_color',
			[
				'label'     => esc_html__( 'Estimated Cost Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-estimated-cost' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'estimated_cost_bottom_spacing',
			[
				'label'     => __( 'Estimated Cost Bottom Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-estimated-cost' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'estimated_cost_typography',
				'selector' => '{{WRAPPER}} .pp-how-to-estimated-cost',
			]
		);
		$this->add_responsive_control(
			'estimated_cost_align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-estimated-cost' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'supply_title_color',
			[
				'label'     => esc_html__( 'Supply Title Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-supply-title' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
				'condition' => [
					'add_supply' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'supply_box_bottom_spacing',
			[
				'label'     => __( 'Supply Box Bottom Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-supply' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'add_supply' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'supply_title_bottom_spacing',
			[
				'label'     => __( 'Supply Title Bottom Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-supply-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'add_supply' => 'yes',
				],
			]
		);
		$this->add_control(
			'supply_title_tag',
			[
				'label'       => __( 'Supply Title HTML Tag', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h3',
				'label_block' => true,
				'options'     => [
					'h1' => __( 'H1', 'powerpack' ),
					'h2' => __( 'H2', 'powerpack' ),
					'h3' => __( 'H3', 'powerpack' ),
					'h4' => __( 'H4', 'powerpack' ),
					'h5' => __( 'H5', 'powerpack' ),
					'h6' => __( 'H6', 'powerpack' ),
				],
				'condition'   => [
					'add_supply' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'supply_title_typography',
				'selector'  => '{{WRAPPER}} .pp-how-to-supply-title',
				'condition' => [
					'add_supply' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'supply_title_align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-supply-title' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'add_supply' => 'yes',
				],
			]
		);
		$this->add_control(
			'supply_text_color',
			[
				'label'     => esc_html__( 'Supply Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pp-supply' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
				'condition' => [
					'add_supply' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'supply_text_bottom_spacing',
			[
				'label'     => __( 'Supply Text Bottom Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pp-supply:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'add_supply' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'supply_text_typography',
				'selector'  => '{{WRAPPER}} .pp-supply',
				'condition' => [
					'add_supply' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'supply_text_align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-supply' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'add_supply' => 'yes',
				],
			]
		);
		$this->add_control(
			'tools_title_color',
			[
				'label'     => esc_html__( 'Tools Title Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-tools-title' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
				'condition' => [
					'add_tools' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'tools_box_bottom_spacing',
			[
				'label'     => __( 'Tools Box Bottom Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-tools' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'add_tools' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'tools_title_bottom_spacing',
			[
				'label'     => __( 'Tools Title Bottom Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-tools-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'add_tools' => 'yes',
				],
			]
		);
		$this->add_control(
			'tools_title_tag',
			[
				'label'       => __( 'Tools Title HTML Tag', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h3',
				'label_block' => true,
				'options'     => [
					'h1' => __( 'H1', 'powerpack' ),
					'h2' => __( 'H2', 'powerpack' ),
					'h3' => __( 'H3', 'powerpack' ),
					'h4' => __( 'H4', 'powerpack' ),
					'h5' => __( 'H5', 'powerpack' ),
					'h6' => __( 'H6', 'powerpack' ),
				],
				'condition'   => [
					'add_tools' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tools_title_typography',
				'selector'  => '{{WRAPPER}} .pp-how-to-tools-title',
				'condition' => [
					'add_tools' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'tools_title_align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-tools-title' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'add_tools' => 'yes',
				],
			]
		);
		$this->add_control(
			'tool_text_color',
			[
				'label'     => esc_html__( 'Tools Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pp-tool' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
				'condition' => [
					'add_tools' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'tool_text_bottom_spacing',
			[
				'label'     => __( 'Tools Text Bottom Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pp-tool:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'add_tools' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tools_text_typography',
				'selector'  => '{{WRAPPER}} .pp-tool',
				'condition' => [
					'add_tools' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'tools_text_align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-tool' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'add_tools' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/**
		 * Style Tab: Steps
		 */
		$this->start_controls_section(
			'section_step_style',
			[
				'label' => esc_html__( 'Steps', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'step_section_title_heading',
			[
				'label'       => __( 'Section Title', 'powerpack' ),
				'type'        => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'step_section_title_color',
			[
				'label'     => esc_html__( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-step-section-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'step_section_title_bottom_spacing',
			[
				'label'     => __( 'Bottom Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-step-section-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'step_section_title_tag',
			[
				'label'       => __( 'Title HTML Tag', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h3',
				'label_block' => true,
				'options'     => [
					'h1' => __( 'H1', 'powerpack' ),
					'h2' => __( 'H2', 'powerpack' ),
					'h3' => __( 'H3', 'powerpack' ),
					'h4' => __( 'H4', 'powerpack' ),
					'h5' => __( 'H5', 'powerpack' ),
					'h6' => __( 'H6', 'powerpack' ),
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'step_section_title_typography',
				'selector' => '{{WRAPPER}} .pp-how-to-step-section-title',
			]
		);
		$this->add_responsive_control(
			'step_section_title_align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-step-section-title' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'step_img_heading',
			[
				'label'       => __( 'Step Image', 'powerpack' ),
				'type'        => Controls_Manager::HEADING,
				'separator'   => 'before',
			]
		);
		$this->add_responsive_control(
			'step_img_position',
			[
				'label'       => __( 'Position', 'powerpack' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'column-reverse' => [
						'title' => __( 'Top', 'powerpack' ),
						'icon'  => 'eicon-v-align-top',
					],
					'column'         => [
						'title' => __( 'Bottom', 'powerpack' ),
						'icon'  => 'eicon-v-align-bottom',
					],
					'row-reverse'    => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-h-align-left',
					],
					'row'            => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'dynamic'     => [
					'active' => true,
				],
				'default'     => 'row',
				'render_type' => 'template',
				'separator' => 'before',
				'selectors'   => [
					'{{WRAPPER}} .pp-how-to-step.pp-has-img' => 'flex-direction: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'step_align_h',
			[
				'label'     => __( 'Horizontal Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-step.pp-has-img' => 'align-items: {{VALUE}};',
				],
				'condition' => [
					'step_img_position' => [ 'column', 'column-reverse' ],
				],
			]
		);
		$this->add_responsive_control(
			'step_align_v',
			[
				'label'     => __( 'Vertical Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => __( 'Top', 'powerpack' ),
						'icon'  => 'eicon-v-align-top',
					],
					'center'     => [
						'title' => __( 'Middle', 'powerpack' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'flex-end'   => [
						'title' => __( 'Bottom', 'powerpack' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'   => 'flex-start',
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-step.pp-has-img' => 'align-items: {{VALUE}};',
				],
				'condition' => [
					'step_img_position' => [ 'row', 'row-reverse' ],
				],
			]
		);
		$this->add_responsive_control(
			'steps_spacing',
			[
				'label'     => __( 'Steps Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-step:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'step_image_width',
			[
				'label'      => __( 'Image Width', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default'    => [
					'size' => 30,
				],
				'size_units' => [ '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pp-how-to-step.pp-has-img' => 'display: flex;',
					'{{WRAPPER}} .pp-how-to-step.pp-has-img .pp-how-to-step-image'   => 'width: {{SIZE}}%;',
					'{{WRAPPER}} .pp-how-to-step.pp-has-img.pp-step-img-left .pp-how-to-step-content' => 'width: calc( 100% - {{SIZE}}% );',
					'{{WRAPPER}} .pp-how-to-step.pp-has-img.pp-step-img-right .pp-how-to-step-content' => 'width: calc( 100% - {{SIZE}}% );',
				],
			]
		);
		$this->add_responsive_control(
			'step_image_spacing',
			[
				'label'      => __( 'Image Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'size_units' => [ 'px' ],
				'default'    => [
					'size' => 10,
				],
				'selectors'  => [
					'{{WRAPPER}} .pp-how-to-step.pp-has-img.pp-step-img-top .pp-how-to-step-image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-how-to-step.pp-has-img.pp-step-img-bottom .pp-how-to-step-image' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-how-to-step.pp-has-img.pp-step-img-left .pp-how-to-step-image' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-how-to-step.pp-has-img.pp-step-img-right .pp-how-to-step-image' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'step_title_heading',
			[
				'label'       => __( 'Step Title', 'powerpack' ),
				'type'        => Controls_Manager::HEADING,
				'separator'   => 'before',
			]
		);
		$this->add_control(
			'step_title_color',
			[
				'label'     => esc_html__( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-step-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'step_title_bottom_spacing',
			[
				'label'     => __( 'Bottom Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-step-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'step_title_tag',
			[
				'label'       => __( 'Title HTML Tag', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h3',
				'label_block' => true,
				'options'     => [
					'h1' => __( 'H1', 'powerpack' ),
					'h2' => __( 'H2', 'powerpack' ),
					'h3' => __( 'H3', 'powerpack' ),
					'h4' => __( 'H4', 'powerpack' ),
					'h5' => __( 'H5', 'powerpack' ),
					'h6' => __( 'H6', 'powerpack' ),
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'step_title_typography',
				'selector' => '{{WRAPPER}} .pp-how-to-step-title',
			]
		);
		$this->add_responsive_control(
			'step_title_align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-step-title' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'step_description_heading',
			[
				'label'       => __( 'Step Description', 'powerpack' ),
				'type'        => Controls_Manager::HEADING,
				'separator'   => 'before',
			]
		);
		$this->add_control(
			'step_description_color',
			[
				'label'     => esc_html__( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-step-description' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'step_description_typography',
				'selector' => '{{WRAPPER}} .pp-how-to-step-description',
			]
		);
		$this->add_responsive_control(
			'step_description_align',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .pp-how-to-step-description' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
	}

	/**
	 * Render How To Slug.
	 *
	 * @access protected
	 */
	protected function get_how_to_json_ld() {
		$settings           = $this->get_settings_for_display();
		$id                 = $this->get_id();
		$how_to_title       = $settings['how_to_title'];
		$how_to_description = $settings['how_to_description'];
		$how_to_image       = $settings['how_to_image'];
		$show_advanced      = $settings['show_advanced'];
		$years              = ( '' !== $settings['total_time_years'] ) ? $settings['total_time_years'] : '0';
		$months             = ( '' !== $settings['total_time_months'] ) ? $settings['total_time_months'] : '0';
		$days               = ( '' !== $settings['total_time_days'] ) ? $settings['total_time_days'] : '0';
		$hours              = ( '' !== $settings['total_time_hours'] ) ? $settings['total_time_hours'] : '0';
		$minutes            = ( '' !== $settings['total_time_minutes'] ) ? $settings['total_time_minutes'] : '0';

		$total_time         = $settings['total_time_minutes'];
		$estimated_cost     = $settings['estimated_cost'];
		$currency_iso_code  = $settings['currency_iso_code'];
		$add_supply         = $settings['add_supply'];
		$supplies           = $settings['supply'];
		$add_tools          = $settings['add_tools'];
		$tools              = $settings['tools'];
		$step_section_title = $settings['step_section_title'];
		$steps_form         = $settings['steps_form'];
		$enable_schema      = true;

		$y          = ( 525600 * $years );
		$m          = ( 43200 * $months );
		$d          = ( 1440 * $days );
		$h          = ( 60 * $hours );
		$total_time = $y + $m + $d + $h + $minutes;

		if ( isset( $settings['enable_schema'] ) && 'no' === $settings['enable_schema'] ) {
			$enable_schema = false;
		}

		if ( ! $enable_schema ) {
			return;
		}

		if ( $this->_schema_rendered ) {
			return;
		}

		if ( ! empty( $how_to_image['url'] ) ) {
			$image_url = Group_Control_Image_Size::get_attachment_image_src( $how_to_image['id'], 'how_to_image_size', $settings );
		};
		?>
		<script type="application/ld+json">
			{
				"@context":    "http://schema.org",
				"@type":       "HowTo",
				"name":        "<?php echo $how_to_title; ?>",
				"description": "<?php echo $how_to_description; ?>",
				"image":       "<?php echo $image_url; ?>",

				<?php if ( 'yes' === $show_advanced ) { ?>
					<?php if ( '' !== $estimated_cost ) { ?>
					"estimatedCost": {
						"@type": "MonetaryAmount",
						"currency": "<?php echo $currency_iso_code; ?>",
						"value": "<?php echo $estimated_cost; ?>"
					},
					<?php } ?>
					<?php if ( '' !== $total_time ) { ?>
					"totalTime": "PT<?php echo $total_time; ?>M",
					<?php } ?>

					<?php
					if ( 'yes' === $add_supply && isset( $supplies ) ) {
						?>
						"supply": [
							<?php foreach ( $supplies as $key => $supply ) { ?>
								{
									"@type": "HowToSupply",
									"name": "<?php echo $supply['supply_text']; ?>"
								}<?php echo ( ( $key + 1 ) !== sizeof( $supplies ) ) ? ',' : ''; ?>
							<?php } ?>
						],
						<?php
					}
					if ( 'yes' === $add_tools && isset( $tools ) ) {
						?>
						"tool": [
							<?php foreach ( $tools as $key => $tool ) { ?>
								{
									"@type": "HowToTool",
									"name": "<?php echo $tool['tool_text']; ?>"
								}<?php echo ( ( $key + 1 ) !== sizeof( $tools ) ) ? ',' : ''; ?>
							<?php } ?>
						],
						<?php
					}
				}
				if ( isset( $steps_form ) ) {
					?>
				"step": [
					<?php
					foreach ( $steps_form as $key => $step ) {
						$step_id      = 'step-' . $id . '-' . ( $key + 1 );
						$step_image   = $step['step_image'];
						$step_img_url = '';

						if ( ! empty( $step_image['url'] ) ) {
							$step_img_url = $step_image['url'];
						}
						if ( isset( $step['step_link']['url'] ) && ! empty( $step['step_link']['url'] ) ) {
							$meta_link = $step['step_link']['url'];
						} else {
							$meta_link = get_permalink() . '#' . $step_id;
						}
						?>
						{
							"@type": "HowToStep",
							"name": "<?php echo $step['step_title']; ?>",
							"text": "<?php echo $step['step_description']; ?>",
							"image": "<?php echo $step_img_url; ?>",
							"url": "<?php echo $meta_link; ?>"
						}<?php echo ( ( $key + 1 ) !== sizeof( $steps_form ) ) ? ',' : ''; ?>
					<?php } ?>
				] 
				<?php } ?>
			}
		</script>
		<?php

		$this->_schema_rendered = true;
	}

	/**
	 * Render How To Slug.
	 *
	 * @access protected
	 */
	protected function get_how_to_slug() {
		$settings            = $this->get_settings_for_display();
		$total_time_text     = $settings['total_time_text'];
		$years               = $settings['total_time_years'];
		$months              = $settings['total_time_months'];
		$days                = $settings['total_time_days'];
		$hours               = $settings['total_time_hours'];
		$minutes             = $settings['total_time_minutes'];
		$estimated_cost_text = $settings['estimated_cost_text'];
		$estimated_cost      = $settings['estimated_cost'];
		$currency_iso_code   = $settings['currency_iso_code'];
		$time                = '';
		$estimated_text      = '';

		$total_time = array(
			// translators: %s for time duration.
			'year'   => ! empty( $years ) ? sprintf( _n( '%s year', '%s years', $years, 'powerpack' ), number_format_i18n( $years ) ) : '',
			// translators: %s for time duration.
			'month'  => ! empty( $months ) ? sprintf( _n( '%s month', '%s months', $months, 'powerpack' ), number_format_i18n( $months ) ) : '',
			// translators: %s for time duration.
			'day'    => ! empty( $days ) ? sprintf( _n( '%s day', '%s days', $days, 'powerpack' ), number_format_i18n( $days ) ) : '',
			// translators: %s for time duration.
			'hour'   => ! empty( $hours ) ? sprintf( _n( '%s hour', '%s hours', $hours, 'powerpack' ), number_format_i18n( $hours ) ) : '',
			// translators: %s for time duration.
			'minute' => ! empty( $minutes ) ? sprintf( _n( '%s minute', '%s minutes', $minutes, 'powerpack' ), number_format_i18n( $minutes ) ) : '',
		);

		foreach ( $total_time as $time_key => $duration ) {
			if ( empty( $duration ) ) {
				unset( $total_time[ $time_key ] );
			}
		}

		if ( ! empty( $total_time ) ) {
			$this->add_render_attribute( 'total_time', 'class', 'pp-how-to-total-time' );

			$time = implode( ', ', $total_time );

			if ( ! empty( $total_time_text ) ) {
				$time_text = $total_time_text . ' ' . $time;
			} else {
				$time_text = $time;
			}
		}
		if ( ! empty( $estimated_cost ) ) {
			$this->add_render_attribute( 'estimated_cost', 'class', 'pp-how-to-estimated-cost' );

			$estimated_text  = $estimated_cost_text;
			$estimated_text .= '<span> ';
			$estimated_text .= $currency_iso_code . ' ' . number_format( $estimated_cost );
			$estimated_text .= '</span>';
		}
		?>
		<div class="pp-how-to-slug">
			<?php if ( isset( $total_time ) && ! empty( $total_time ) ) { ?>
				<p <?php echo $this->get_render_attribute_string( 'total_time' ); ?>>
					<?php echo $time_text; ?>
				</p>
			<?php } ?>
			<?php if ( isset( $estimated_cost ) && ! empty( $estimated_cost ) ) { ?>
				<p <?php echo $this->get_render_attribute_string( 'estimated_cost' ); ?>>
					<?php echo $estimated_text; ?>
				</p>
			<?php } ?>
		</div>
		<?php
	}
	/**
	 * Render How To Supply.
	 *
	 * @access protected
	 */
	protected function get_how_to_supply() {
		$settings     = $this->get_settings_for_display();
		$add_supply   = $settings['add_supply'];
		$supply_title = $settings['supply_title'];
		$title_tag    = $settings['supply_title_tag'];
		$supplies     = $settings['supply'];
		$supply_icon  = $settings['supply_icon']['value'];
		if ( 'yes' === $add_supply ) {
			?>
			<div class="pp-how-to-supply">
				<?php if ( isset( $supply_title ) && ! empty( $supply_title ) ) { ?>
					<<?php echo $title_tag; ?> class="pp-how-to-supply-title">
						<?php echo $supply_title; ?>
					</<?php echo $title_tag; ?>>
				<?php } ?>
				<?php
				if ( isset( $supplies ) ) {
					foreach ( $supplies as $key => $supply ) {
						?>
						<div class="pp-supply pp-supply-<?php echo $key + 1; ?>">
							<i class="pp-supply-icon <?php echo $supply_icon; ?>"></i>
							<span><?php echo $supply['supply_text']; ?></span>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
			<?php
		}
	}
	/**
	 * Render How To Tools.
	 *
	 * @access protected
	 */
	protected function get_how_to_tools() {
		$settings   = $this->get_settings_for_display();
		$add_tools  = $settings['add_tools'];
		$tool_title = $settings['tool_title'];
		$title_tag  = $settings['tools_title_tag'];
		$tools      = $settings['tools'];
		$tools_icon = $settings['tools_icon']['value'];

		if ( 'yes' === $add_tools ) {
			?>
			<div class="pp-how-to-tools">
				<?php if ( isset( $tool_title ) && ! empty( $tool_title ) ) { ?>
					<<?php echo $title_tag; ?> class="pp-how-to-tools-title">
						<?php echo $tool_title; ?>
					</<?php echo $title_tag; ?>>
				<?php } ?>
				<?php
				if ( isset( $tools ) ) {
					foreach ( $tools as $key => $tool ) {
						?>
						<div class="pp-tool pp-tool-<?php echo $key + 1; ?>">
							<i class="pp-tool-icon <?php echo $tools_icon; ?>"></i>
							<span><?php echo $tool['tool_text']; ?></span>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
			<?php
		}
	}
	/**
	 * Render Steps.
	 *
	 * @access protected
	 */
	protected function get_steps() {
		$settings           = $this->get_settings_for_display();
		$id                 = $this->get_id();
		$step_section_title = $settings['step_section_title'];
		$section_title_tag  = $settings['step_section_title_tag'];
		$title_tag          = $settings['step_title_tag'];
		$steps_form         = $settings['steps_form'];
		$step_img_position  = '';
		if ( 'column-reverse' === $settings['step_img_position'] ) {
			$step_img_position = 'pp-step-img-top';
		} elseif ( 'column' === $settings['step_img_position'] ) {
			$step_img_position = 'pp-step-img-bottom';
		} elseif ( 'row' === $settings['step_img_position'] ) {
			$step_img_position = 'pp-step-img-right';
		} elseif ( 'row-reverse' === $settings['step_img_position'] ) {
			$step_img_position = 'pp-step-img-left';
		}

		$this->add_render_attribute( 'how_to_steps', 'class', 'pp-how-to-steps' );
		$this->add_render_attribute( 'how_to_steps', 'id', 'step-' . $id );
		$this->add_render_attribute( 'step_section_title', 'class', 'pp-how-to-step-section-title' );
		?>
		<div <?php echo $this->get_render_attribute_string( 'how_to_steps' ); ?>>
			<?php if ( isset( $step_section_title ) && ! empty( $step_section_title ) ) { ?>
				<<?php echo $section_title_tag; ?> <?php echo $this->get_render_attribute_string( 'step_section_title' ); ?>>
					<?php echo $step_section_title; ?>
				</<?php echo $section_title_tag; ?>>
			<?php } ?>
			<?php
			if ( isset( $steps_form ) ) {
				foreach ( $steps_form as $key => $step ) {
					$step_id        = 'step-' . $id . '-' . ( $key + 1 );
					$step_image     = $step['step_image'];
					$step_img_class = 'pp-no-img';
					$step_img_url   = '';

					if ( ! empty( $step['step_title'] ) ) {
						$this->add_render_attribute( 'step_title' . ( $key + 1 ), 'class', 'pp-how-to-step-title' );
					};
					if ( ! empty( $step['step_description'] ) ) {
						$this->add_render_attribute( 'step_description' . ( $key + 1 ), 'class', 'pp-how-to-step-description' );
					};
					if ( ! empty( $step_image['url'] ) ) {
						$step_img_url = $step_image['url'];
						$this->add_render_attribute( 'step_image' . ( $key + 1 ), 'src', $step_img_url );
						$this->add_render_attribute( 'step_image' . ( $key + 1 ), 'alt', Control_Media::get_image_alt( $step_image ) );
						$this->add_render_attribute( 'step_image' . ( $key + 1 ), 'title', Control_Media::get_image_title( $step_image ) );
						$step_img_class = 'pp-has-img';
					};

					$this->add_render_attribute( 'step' . ( $key + 1 ), 'class', 'pp-how-to-step' );
					$this->add_render_attribute( 'step' . ( $key + 1 ), 'class', $step_img_class );
					$this->add_render_attribute( 'step' . ( $key + 1 ), 'class', $step_img_position );
					$this->add_render_attribute( 'step' . ( $key + 1 ), 'id', $step_id );

					if ( isset( $step['step_link']['url'] ) && ! empty( $step['step_link']['url'] ) ) {
						$this->add_link_attributes( 'step-link', $step['step_link'] );
						$a_open    = '<a ' . $this->get_render_attribute_string( 'step-link' ) . '>';
						$a_close   = '</a>';
					} else {
						$a_open    = '';
						$a_close   = '';
					}
					?>

					<div <?php echo $this->get_render_attribute_string( 'step' . ( $key + 1 ) ); ?>>
						<div class="pp-how-to-step-content">
							<?php echo $a_open; ?>

							<?php if ( ! empty( $step['step_title'] ) ) { ?>
								<<?php echo $title_tag; ?> <?php echo $this->get_render_attribute_string( 'step_title' . ( $key + 1 ) ); ?> >
									<?php echo $step['step_title']; ?>
								</<?php echo $title_tag; ?>>
								<?php echo $a_close; ?>
							<?php } ?>

							<?php if ( ! empty( $step['step_description'] ) ) { ?>
								<div <?php echo $this->get_render_attribute_string( 'step_description' . ( $key + 1 ) ); ?> >
									<?php echo $step['step_description']; ?>
								</div>
							<?php } ?>
							<?php
							if ( empty( $step['step_title'] ) ) {
								echo $a_close;
							}
							?>
						</div>
						<?php if ( ! empty( $step_image['url'] ) ) { ?>
							<?php
							$link_key = 'pp-lightbox-' . ( $key + 1 );

							if ( 'no' !== $settings['open_lightbox'] ) {
								$this->add_render_attribute(
									$link_key,
									[
										'href'  => $step_image['url'],
										'class' => 'elementor-clickable',
										'data-elementor-open-lightbox' => $settings['open_lightbox'],
										'data-elementor-lightbox-slideshow' => $this->get_id(),
									]
								);
							}
							?>
							<div class="pp-how-to-step-image">
								<?php
								if ( 'no' !== $settings['open_lightbox'] ) {
									echo '<a ' . $this->get_render_attribute_string( $link_key ) . '>';
								}
								printf( '<img %s />', $this->get_render_attribute_string( 'step_image' . ( $key + 1 ) ) );
								if ( 'no' !== $settings['open_lightbox'] ) {
									echo '</a>';
								}
								?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
		<?php
	}

	protected function render() {
		$settings           = $this->get_settings_for_display();
		$how_to_title       = $settings['how_to_title'];
		$title_tag          = $settings['title_tag'];
		$how_to_description = $settings['how_to_description'];
		$how_to_image       = $settings['how_to_image'];
		$how_to_image_url   = '';

		$this->add_render_attribute( 'how_to_title', 'class', 'pp-how-to-title' );
		$this->add_render_attribute( 'how_to_description', 'class', 'pp-how-to-description' );
		if ( ! empty( $how_to_image['url'] ) ) {
			$how_to_image_url = Group_Control_Image_Size::get_attachment_image_src( $how_to_image['id'], 'how_to_image_size', $settings );
			$this->add_render_attribute( 'how_to_image', 'src', $how_to_image_url );
			$this->add_render_attribute( 'how_to_image', 'alt', Control_Media::get_image_alt( $how_to_image ) );
			$this->add_render_attribute( 'how_to_image', 'title', Control_Media::get_image_title( $how_to_image ) );
		}
		?>

		<div class="pp-how-to-wrap pp-clearfix">
			<?php
			if ( 'yes' === $settings['enable_schema'] ) {
				$this->get_how_to_json_ld();
			}
			?>
			<div class="pp-how-to-container pp-clearfix">
				<<?php echo $title_tag; ?> <?php echo $this->get_render_attribute_string( 'how_to_title' ); ?>>
					<?php echo $how_to_title; ?>
				</<?php echo $title_tag; ?>>
				<div <?php echo $this->get_render_attribute_string( 'how_to_description' ); ?>>
					<?php echo $how_to_description; ?>
				</div>
				<?php if ( ! empty( $how_to_image['url'] ) ) { ?>
					<div class="pp-how-to-image">
						<?php printf( '<img %s />', $this->get_render_attribute_string( 'how_to_image' ) ); ?>
					</div>
					<?php
				}
				if ( 'yes' === $settings['show_advanced'] ) {
					$this->get_how_to_slug();
					$this->get_how_to_supply();
					$this->get_how_to_tools();
				}
				$this->get_steps();
				?>
			</div>
		</div>
		<?php
	}
}
