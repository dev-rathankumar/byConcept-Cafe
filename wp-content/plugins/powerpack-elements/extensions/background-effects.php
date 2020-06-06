<?php

namespace PowerpackElements\Extensions;

// Powerpack Elements classes
use PowerpackElements\Base\Extension_Base;
use PowerpackElements\Classes\PP_Posts_Helper;

// Elementor classes
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Background Effects Extension
 *
 * Adds background effects to sections
 *
 * @since 1.4.7
 */
class Extension_Background_Effects extends Extension_Base {

	/**
	 * Is Common Extension
	 *
	 * Defines if the current extension is common for all element types or not
	 *
	 * @since 1.4.7
	 * @access protected
	 *
	 * @var bool
	 */
	protected $is_common = true;

	/**
	 * A list of scripts that the widgets is depended in
	 *
	 * @since 1.4.7
	 **/
	public function get_script_depends() {
		return [
			'pp-bg-effects',
		];
	}

	/**
	 * The description of the current extension
	 *
	 * @since 2.-.0
	 **/
	public static function get_description() {
		return __( 'Adds background effects to sections allowing you to show particle backgrounds and more fancy effects for sections.', 'powerpack' );
	}

	/**
	 * Is disabled by default
	 *
	 * Return wether or not the extension should be disabled by default,
	 * prior to user actually saving a value in the admin page
	 *
	 * @access public
	 * @since 1.4.7
	 * @return bool
	 */
	public static function is_default_disabled() {
		return true;
	}

	/**
	 * Add Actions
	 *
	 * @since 0.1.0
	 *
	 * @access private
	 */
	protected function add_background_effects_sections( $element, $args ) {

		// The name of the section
		$section_name = 'section_powerpack_elements_background_effects';

		// Check if this section exists
		$section_exists = \Elementor\Plugin::instance()->controls_manager->get_control_from_stack( $element->get_unique_name(), $section_name );

		if ( ! is_wp_error( $section_exists ) ) {
			// We can't and should try to add this section to the stack
			return false;
		}

		$element->start_controls_section(
			$section_name,
			[
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => __( 'PowerPack Background Effects', 'powerpack' ),
			]
		);

		$element->end_controls_section();

	}

	/**
	 * Add common sections
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 */
	protected function add_common_sections_actions() {

		// Activate sections for widgets
		/*add_action(
			'elementor/element/common/section_background/after_section_end',
			function( $element, $args ) {
				$this->add_background_effects_sections( $element, $args );
			},
			10,
			2
		);*/

		// Activate sections for sections
		add_action(
			'elementor/element/section/section_background/after_section_end',
			function( $element, $args ) {
				$this->add_background_effects_sections( $element, $args );
			},
			10,
			2
		);

		// Activate sections for widgets if elementor pro
		/*add_action(
			'elementor/element/common/section_custom_css_pro/after_section_end',
			function( $element, $args ) {
				$this->add_background_effects_sections( $element, $args );
			},
			10,
			2
		);*/
	}

	/**
	 * Add Controls
	 *
	 * @since 1.4.7
	 *
	 * @access private
	 */
	private function add_controls( $element, $args ) {

		$element_type = $element->get_type();

		$element->add_control(
			'pp_background_effects_enable',
			[
				'label'              => __( 'Background Effects', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => '',
				'label_on'           => __( 'Yes', 'powerpack' ),
				'label_off'          => __( 'No', 'powerpack' ),
				'return_value'       => 'yes',
				'prefix_class'       => 'pp-bg-effects-',
				'frontend_available' => true,
				'render_type'        => 'template',
			]
		);
		$element->add_control(
			'pp_animation_type',
			[
				'label'     => __( 'Animation Types', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'birds',
				'options'   => [
					'birds'     => __( 'Birds', 'powerpack' ),
					'fog'       => __( 'Fog', 'powerpack' ),
					'waves'     => __( 'Waves', 'powerpack' ),
					'net'       => __( 'Net', 'powerpack' ),
					'dots'      => __( 'Dots', 'powerpack' ),
					'particles' => __( 'Line Particles', 'powerpack' ),
					'nasa'      => __( 'NASA', 'powerpack' ),
					'bubble'    => __( 'Bubble', 'powerpack' ),
					'snow'      => __( 'Snow', 'powerpack' ),
					'custom'    => __( 'Custom', 'powerpack' ),
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
				],
			]
		);
		$element->add_control(
			'vanta_bg_opacity',
			[
				'label'     => __( 'Opacity', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => [ 'birds', 'fog', 'waves', 'net', 'dots' ],
				],
			]
		);
		$element->add_control(
			'part_color',
			[
				'label'     => esc_html__( 'Particles Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => [ 'particles', 'nasa', 'bubble', 'snow' ],
				],
			]
		);
		$element->add_control(
			'line_color',
			[
				'label'     => esc_html__( 'Line Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'particles',
				],
			]
		);
		$element->add_control(
			'part_opacity',
			[
				'label'     => __( 'Color Opacity', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => [ 'particles', 'nasa', 'bubble', 'snow' ],
				],
			]
		);
		$element->add_control(
			'part_rand_opacity',
			[
				'label'              => __( 'Randomized Opacity', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => '',
				'label_on'           => __( 'Yes', 'powerpack' ),
				'label_off'          => __( 'No', 'powerpack' ),
				'return_value'       => 'true',
				'frontend_available' => true,
				'condition'          => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => [ 'particles', 'nasa', 'bubble', 'snow' ],
				],
			]
		);
		$element->add_control(
			'part_quantity',
			[
				'label'     => __( 'Quantity', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => [ 'particles', 'nasa', 'bubble', 'snow' ],
				],
			]
		);
		$element->add_control(
			'part_size',
			[
				'label'     => __( 'Size', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => [ 'particles', 'nasa', 'bubble', 'snow' ],
				],
			]
		);
		$element->add_control(
			'part_speed',
			[
				'label'     => __( 'Moving Speed', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => [ 'particles', 'nasa', 'bubble', 'snow' ],
				],
			]
		);
		$element->add_control(
			'part_direction',
			[
				'label'     => __( 'Moving direction', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'none',
				'options'   => [
					'none'         => __( 'Default', 'powerpack' ),
					'top'          => __( 'Top', 'powerpack' ),
					'bottom'       => __( 'Bottom', 'powerpack' ),
					'left'         => __( 'Left', 'powerpack' ),
					'right'        => __( 'Right', 'powerpack' ),
					'top-left'     => __( 'Top Left', 'powerpack' ),
					'top-right'    => __( 'Top Right', 'powerpack' ),
					'bottom-left'  => __( 'Bottom Left', 'powerpack' ),
					'bottom-right' => __( 'Bottom Right', 'powerpack' ),
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => [ 'particles', 'nasa', 'bubble', 'snow' ],
				],
			]
		);
		$element->add_control(
			'part_hover_effect',
			[
				'label'     => __( 'Hover Effect', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'noeffect',
				'options'   => [
					'none'     => __( 'Default', 'powerpack' ),
					'grab'     => __( 'Grab', 'powerpack' ),
					'bubble'   => __( 'Bubble', 'powerpack' ),
					'repulse'  => __( 'Repulse', 'powerpack' ),
					'noeffect' => __( 'None', 'powerpack' ),
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => [ 'particles', 'nasa', 'bubble', 'snow' ],
				],
			]
		);
		$element->add_control(
			'line_hover_color',
			[
				'label'     => esc_html__( 'Line Hover Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => [ 'nasa', 'bubble', 'snow' ],
					'part_hover_effect'            => 'grab',
				],
			]
		);
		$element->add_control(
			'part_hover_size',
			[
				'label'     => __( 'Particles Size on Hover', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => [ 'particles', 'nasa', 'bubble', 'snow' ],
					'part_hover_effect'            => 'bubble',
				],
			]
		);
		$element->add_control(
			'part_custom_code',
			[
				'label'       => __( 'Custom Code', 'powerpack' ),
				'type'        => Controls_Manager::TEXTAREA,
				'description' => __( '<span class="fl-field-description"><p> Add custom JSON for the Particles in the Background Effects.</p> <p>To add custom effects to the background particles, Follow steps below.</p><br/><ol><li>1. <a href="https://vincentgarreau.com/particles.js/" target="_blank"><b style="color: #d30a5c;">Click Here</b></a> and you can choose from the multiple options to customize every aspect of the background particles.</li><br/><li>2. Once you created a custom style for particles, you can download JSON file from the "Download current config (json)" link.</li><br/><li>3. Copy JSON code from the download file & paste it.</li></ol></span>', 'powerpack' ),
				'condition'   => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'custom',
				],
			]
		);
		$element->add_control(
			'bird_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'birds',
				],
			]
		);
		$element->add_control(
			'bird_color_1',
			[
				'label'     => esc_html__( 'Primary Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'birds',
				],
			]
		);
		$element->add_control(
			'bird_color_2',
			[
				'label'     => esc_html__( 'Secondary Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'birds',
				],
			]
		);
		$element->add_control(
			'bird_color_mode',
			[
				'label'     => __( 'Color Mode', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'lerp',
				'options'   => [
					'lerp'             => __( 'Lerp', 'powerpack' ),
					'variance'         => __( 'Variance', 'powerpack' ),
					'lerpGradient'     => __( 'Lerp Gradient', 'powerpack' ),
					'varianceGradient' => __( 'Variance Gradient', 'powerpack' ),
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'birds',
				],
			]
		);
		$element->add_control(
			'bird_quantity',
			[
				'label'     => __( 'Quantity', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 5,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'birds',
				],
			]
		);
		$element->add_control(
			'bird_size',
			[
				'label'     => __( 'Bird Size', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 1,
						'max'  => 3,
						'step' => 0.1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'birds',
				],
			]
		);
		$element->add_control(
			'bird_wing_span',
			[
				'label'     => __( 'Wing Span', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 10,
						'max'  => 40,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'birds',
				],
			]
		);
		$element->add_control(
			'bird_speed_limit',
			[
				'label'     => __( 'Speed Limit', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'birds',
				],
			]
		);
		$element->add_control(
			'bird_separation',
			[
				'label'     => __( 'Separation', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'birds',
				],
			]
		);
		$element->add_control(
			'bird_alignment',
			[
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'birds',
				],
			]
		);
		$element->add_control(
			'bird_cohesion',
			[
				'label'     => __( 'Cohesion', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 1,
						'max'  => 100,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'birds',
				],
			]
		);
		$element->add_control(
			'fog_highlight_color',
			[
				'label'     => esc_html__( 'Highlight Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'fog',
				],
			]
		);
		$element->add_control(
			'fog_midtone_color',
			[
				'label'     => esc_html__( 'Midtone Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'fog',
				],
			]
		);
		$element->add_control(
			'fog_lowlight_color',
			[
				'label'     => esc_html__( 'Lowlight Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'fog',
				],
			]
		);
		$element->add_control(
			'fog_base_color',
			[
				'label'     => esc_html__( 'Base Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'fog',
				],
			]
		);
		$element->add_control(
			'fog_blur_factor',
			[
				'label'     => __( 'Blur Factor', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0.1,
						'max'  => 0.9,
						'step' => 0.1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'fog',
				],
			]
		);
		$element->add_control(
			'fog_zoom',
			[
				'label'     => __( 'Zoom', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0.1,
						'max'  => 3,
						'step' => 0.1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'fog',
				],
			]
		);
		$element->add_control(
			'fog_speed',
			[
				'label'     => __( 'Speed', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0.1,
						'max'  => 5,
						'step' => 0.1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'fog',
				],
			]
		);
		$element->add_control(
			'waves_color',
			[
				'label'     => esc_html__( 'Waves Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'waves',
				],
			]
		);
		$element->add_control(
			'waves_shininess',
			[
				'label'     => __( 'Shininess', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 150,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'waves',
				],
			]
		);
		$element->add_control(
			'waves_height',
			[
				'label'     => __( 'Wave Height', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 40,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'waves',
				],
			]
		);
		$element->add_control(
			'waves_speed',
			[
				'label'     => __( 'Speed', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 40,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'waves',
				],
			]
		);
		$element->add_control(
			'waves_zoom',
			[
				'label'     => __( 'Zoom', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0.7,
						'max'  => 1.8,
						'step' => 0.1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'waves',
				],
			]
		);
		$element->add_control(
			'net_color',
			[
				'label'     => esc_html__( 'Net Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'net',
				],
			]
		);
		$element->add_control(
			'net_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'net',
				],
			]
		);
		$element->add_control(
			'net_points',
			[
				'label'     => __( 'Points', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 1,
						'max'  => 20,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'net',
				],
			]
		);
		$element->add_control(
			'net_max_distance',
			[
				'label'     => __( 'Max Distance', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 10,
						'max'  => 40,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'net',
				],
			]
		);
		$element->add_control(
			'net_spacing',
			[
				'label'     => __( 'Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 10,
						'max'  => 20,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'net',
				],
			]
		);
		$element->add_control(
			'net_show_dot',
			[
				'label'              => __( 'Show Dot', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'true',
				'label_on'           => __( 'Yes', 'powerpack' ),
				'label_off'          => __( 'No', 'powerpack' ),
				'return_value'       => 'true',
				'frontend_available' => true,
				'condition'          => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'net',
				],
			]
		);
		$element->add_control(
			'dots_color_1',
			[
				'label'     => esc_html__( 'Dots Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'dots',
				],
			]
		);
		$element->add_control(
			'dots_color_2',
			[
				'label'     => esc_html__( 'Center Ball Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'dots',
				],
			]
		);
		$element->add_control(
			'dots_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'alpha'     => false,
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'dots',
				],
			]
		);
		$element->add_control(
			'dots_size',
			[
				'label'     => __( 'Size', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0.5,
						'max'  => 10,
						'step' => 0.1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'dots',
				],
			]
		);
		$element->add_control(
			'dots_spacing',
			[
				'label'     => __( 'Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 5,
						'max'  => 100,
						'step' => 1,
					],
				],
				'condition' => [
					'pp_background_effects_enable' => 'yes',
					'pp_animation_type'            => 'dots',
				],
			]
		);

		$element->add_control(
			'effect_hide_tablet',
			[
				'label'              => __( 'Hide on Tablet', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'label_off',
				'label_on'           => __( 'Hide', 'powerpack' ),
				'label_off'          => __( 'Show', 'powerpack' ),
				'return_value'       => 'hide',
				'frontend_available' => true,
				'condition'          => [
					'pp_background_effects_enable' => 'yes',
				],
			]
		);
		$element->add_control(
			'effect_hide_mobile',
			[
				'label'              => __( 'Hide on Mobile', 'powerpack' ),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => 'label_off',
				'label_on'           => __( 'Hide', 'powerpack' ),
				'label_off'          => __( 'Show', 'powerpack' ),
				'return_value'       => 'hide',
				'frontend_available' => true,
				'condition'          => [
					'pp_background_effects_enable' => 'yes',
				],
			]
		);
	}

	protected function render() {
		$settings = $element->get_settings();
	}

	/**
	 * Add Actions
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 */
	protected function add_actions() {

		// Activate controls for rows
		add_action(
			'elementor/element/section/section_powerpack_elements_background_effects/before_section_end',
			function( $element, $args ) {
				$this->add_controls( $element, $args );
			},
			10,
			2
		);

		// Conditions for sections
		add_action(
			'elementor/frontend/section/before_render',
			function( $element ) {
				$settings  = $element->get_settings();
				$anim_type = $settings['pp_animation_type'];

				if ( 'yes' === $settings['pp_background_effects_enable'] ) {

					if ( 'particles' === $anim_type || 'nasa' === $anim_type || 'bubble' === $anim_type || 'snow' === $anim_type || 'custom' === $anim_type ) {
						wp_enqueue_script( 'particles' );
					} else {
						wp_enqueue_script( 'three-r92' );
						wp_enqueue_script( 'vanta-' . $anim_type );
					}
				}
			},
			10,
			1
		);
		// Conditions for sections
		add_action(
			'elementor/frontend/section/before_render',
			function( $element ) {
				$settings            = $element->get_settings();
				$hide_tablet         = $settings['effect_hide_tablet'];
				$hide_mobile         = $settings['effect_hide_mobile'];
				$elementor_bp_tablet = get_option( 'elementor_viewport_lg' );
				$elementor_bp_mobile = get_option( 'elementor_viewport_md' );
				$bp_tablet           = ! empty( $elementor_bp_tablet ) ? $elementor_bp_tablet : 1025;
				$bp_mobile           = ! empty( $elementor_bp_mobile ) ? $elementor_bp_mobile : 768;
				$max_width           = 'none';
				$min_width           = 'none';
				if ( 'yes' === $settings['pp_background_effects_enable'] ) {
					if ( 'hide' === $hide_tablet && 'hide' !== $hide_mobile ) {
						$max_width = $bp_tablet;
						$min_width = $bp_mobile;
					} elseif ( 'hide' !== $hide_tablet && 'hide' === $hide_mobile ) {
						$max_width = $bp_mobile - 1;
						$min_width = 0;
					} elseif ( 'hide' === $hide_tablet && 'hide' === $hide_mobile ) {
						$max_width = $bp_tablet;
						$min_width = 0;
					}

					$element->add_render_attribute(
						'_wrapper',
						[
							'class'               => 'pp-bg-effects',
							'id'                  => 'pp-bg-effects-' . $element->get_id(),
							'data-section-id'     => $element->get_id(),
							'data-effect-enable'  => $settings['pp_background_effects_enable'],
							'data-animation-type' => $settings['pp_animation_type'],
							'data-canvas-opacity' => ( ! empty( $settings['vanta_bg_opacity']['size'] ) ) ? $settings['vanta_bg_opacity']['size'] : '1',
							'data-hide-max-width' => $max_width,
							'data-hide-min-width' => $min_width,
						]
					);

					if ( 'particles' === $settings['pp_animation_type'] || 'nasa' === $settings['pp_animation_type'] || 'bubble' === $settings['pp_animation_type'] || 'snow' === $settings['pp_animation_type'] ) {

						$element->add_render_attribute(
							'_wrapper',
							[
								'data-part-color'     => $settings['part_color'],
								'data-line-color'     => $settings['line_color'],
								'data-line-h-color'   => $settings['line_hover_color'],
								'data-part-opacity'   => $settings['part_opacity']['size'],
								'data-rand-opacity'   => ( ! empty( $settings['part_rand_opacity'] ) ? true : false ),
								'data-quantity'       => $settings['part_quantity']['size'],
								'data-part-size'      => $settings['part_size']['size'],
								'data-part-speed'     => $settings['part_speed']['size'],
								'data-part-direction' => $settings['part_direction'],
								'data-hover-effect'   => $settings['part_hover_effect'],
								'data-hover-size'     => $settings['part_hover_size']['size'],
							]
						);
					} elseif ( 'custom' === $settings['pp_animation_type'] ) {
						$json_particles_custom = wp_strip_all_tags( $settings['part_custom_code'], $remove_breaks = true );
						$element->add_render_attribute(
							'_wrapper',
							[
								'data-custom-code' => $json_particles_custom,
							]
						);
					} elseif ( 'birds' === $settings['pp_animation_type'] ) {
						$element->add_render_attribute(
							'_wrapper',
							[
								'data-bird-bg-color'    => ( ! empty( $settings['bird_bg_color'] ) ) ? str_replace( '#', '0x', $settings['bird_bg_color'] ) : '0x07192f',
								'data-bird-color-1'     => ( ! empty( $settings['bird_color_1'] ) ) ? str_replace( '#', '0x', $settings['bird_color_1'] ) : '0xff0001',
								'data-bird-color-2'     => ( ! empty( $settings['bird_color_2'] ) ) ? str_replace( '#', '0x', $settings['bird_color_2'] ) : '0x00d1ff',
								'data-bird-color-mode'  => ( ! empty( $settings['bird_color_mode'] ) ) ? $settings['bird_color_mode'] : 'lerp',
								'data-bird-quantity'    => ( ! empty( $settings['bird_quantity']['size'] ) ) ? $settings['bird_quantity']['size'] : '4',
								'data-bird-size'        => ( ! empty( $settings['bird_size']['size'] ) ) ? $settings['bird_size']['size'] : '1.5',
								'data-bird-wing-span'   => ( ! empty( $settings['bird_wing_span']['size'] ) ) ? $settings['bird_wing_span']['size'] : '30',
								'data-bird-speed-limit' => ( ! empty( $settings['bird_speed_limit']['size'] ) ) ? $settings['bird_speed_limit']['size'] : '5',
								'data-bird-separation'  => ( ! empty( $settings['bird_separation']['size'] ) ) ? $settings['bird_separation']['size'] : '20',
								'data-bird-alignment'   => ( ! empty( $settings['bird_alignment']['size'] ) ) ? $settings['bird_alignment']['size'] : '20',
								'data-bird-cohesion'    => ( ! empty( $settings['bird_cohesion']['size'] ) ) ? $settings['bird_cohesion']['size'] : '30',
							]
						);
					} elseif ( 'fog' === $settings['pp_animation_type'] ) {
						$element->add_render_attribute(
							'_wrapper',
							[
								'data-fog-highlight-color' => ( ! empty( $settings['fog_highlight_color'] ) ) ? str_replace( '#', '0x', $settings['fog_highlight_color'] ) : '0xffc302',
								'data-fog-midtone-color'   => ( ! empty( $settings['fog_midtone_color'] ) ) ? str_replace( '#', '0x', $settings['fog_midtone_color'] ) : '0xff1d01',
								'data-fog-lowlight-color'  => ( ! empty( $settings['fog_lowlight_color'] ) ) ? str_replace( '#', '0x', $settings['fog_lowlight_color'] ) : '0x2c07ff',
								'data-fog-base-color'      => ( ! empty( $settings['fog_base_color'] ) ) ? str_replace( '#', '0x', $settings['fog_base_color'] ) : '0xffebeb',
								'data-fog-blur-factor'     => ( ! empty( $settings['fog_blur_factor']['size'] ) ) ? $settings['fog_blur_factor']['size'] : '0.6',
								'data-fog-zoom'            => ( ! empty( $settings['fog_zoom']['size'] ) ) ? $settings['fog_zoom']['size'] : '1',
								'data-fog-speed'           => ( ! empty( $settings['fog_speed']['size'] ) ) ? $settings['fog_speed']['size'] : '1',
							]
						);
					} elseif ( 'waves' === $settings['pp_animation_type'] ) {
						$element->add_render_attribute(
							'_wrapper',
							[
								'data-waves-color'     => ( ! empty( $settings['waves_color'] ) ) ? str_replace( '#', '0x', $settings['waves_color'] ) : '0x005588',
								'data-waves-shininess' => ( ! empty( $settings['waves_shininess']['size'] ) ) ? $settings['waves_shininess']['size'] : '30',
								'data-waves-height'    => ( ! empty( $settings['waves_height']['size'] ) ) ? $settings['waves_height']['size'] : '15',
								'data-waves-zoom'      => ( ! empty( $settings['waves_zoom']['size'] ) ) ? $settings['waves_zoom']['size'] : '1',
								'data-waves-speed'     => ( ! empty( $settings['waves_speed']['size'] ) ) ? $settings['waves_speed']['size'] : '1',
							]
						);
					} elseif ( 'net' === $settings['pp_animation_type'] ) {
						$element->add_render_attribute(
							'_wrapper',
							[
								'data-net-color'        => ( ! empty( $settings['net_color'] ) ) ? str_replace( '#', '0x', $settings['net_color'] ) : '0xff3f81',
								'data-net-bg-color'     => ( ! empty( $settings['net_bg_color'] ) ) ? str_replace( '#', '0x', $settings['net_bg_color'] ) : '0x23153d',
								'data-net-points'       => ( ! empty( $settings['net_points']['size'] ) ) ? $settings['net_points']['size'] : '10',
								'data-net-max-distance' => ( ! empty( $settings['net_max_distance']['size'] ) ) ? $settings['net_max_distance']['size'] : '20',
								'data-net-spacing'      => ( ! empty( $settings['net_spacing']['size'] ) ) ? $settings['net_spacing']['size'] : '15',
								'data-net-show-dot'     => ( ! empty( $settings['net_show_dot'] ) ) ? true : false,
							]
						);
					} elseif ( 'dots' === $settings['pp_animation_type'] ) {
						$element->add_render_attribute(
							'_wrapper',
							[
								'data-dots-color-1'  => ( ! empty( $settings['dots_color_1'] ) ) ? str_replace( '#', '0x', $settings['dots_color_1'] ) : '0xff8721',
								'data-dots-color-2'  => ( ! empty( $settings['dots_color_2'] ) ) ? str_replace( '#', '0x', $settings['dots_color_2'] ) : '0xff8721',
								'data-dots-bg-color' => ( ! empty( $settings['dots_bg_color'] ) ) ? str_replace( '#', '0x', $settings['dots_bg_color'] ) : '0x222222',
								'data-dots-size'     => ( ! empty( $settings['dots_size']['size'] ) ) ? $settings['dots_size']['size'] : '3',
								'data-dots-spacing'  => ( ! empty( $settings['dots_spacing']['size'] ) ) ? $settings['dots_spacing']['size'] : '35',
							]
						);
					}
				}
			},
			10,
			1
		);

		add_action(
			'elementor/section/print_template',
			function( $template, $widget ) {

				if ( ! $template ) {
					return;
				}
				ob_start();
				$old_template        = $template;
				$elementor_bp_tablet = get_option( 'elementor_viewport_lg' );
				$elementor_bp_mobile = get_option( 'elementor_viewport_md' );
				$bp_tablet           = ! empty( $elementor_bp_tablet ) ? $elementor_bp_tablet : 1025;
				$bp_mobile           = ! empty( $elementor_bp_mobile ) ? $elementor_bp_mobile : 768;
				?><#
				var $hide_tablet = settings.effect_hide_tablet;
				var $hide_mobile = settings.effect_hide_mobile;
				var $max_width   = 'none';
				var $min_width   = 'none';

				if ( 'hide' == $hide_tablet && 'hide' != $hide_mobile ) {
					var $max_width = <?php echo $bp_tablet; ?>;
					var $min_width = <?php echo $bp_mobile; ?>;
				} else if ( 'hide' != $hide_tablet && 'hide' == $hide_mobile ) {
					var $max_width = <?php echo $bp_mobile - 1; ?>;
					var $min_width = 0;
				} else if ( 'hide' == $hide_tablet && 'hide' == $hide_mobile ) {
					var $max_width = <?php echo $bp_tablet; ?>;
					var $min_width = 0;
				}

				view.addRenderAttribute( 'effects_data', 'class', 'pp-background-wrapper' );
				view.addRenderAttribute( 'effects_data', 'id', 'pp-background-' + view.getID() );
				view.addRenderAttribute( 'effects_data', 'data-section-id', view.getID() );
				view.addRenderAttribute( 'effects_data', 'data-effect-enable', settings.pp_background_effects_enable );
				view.addRenderAttribute( 'effects_data', 'data-animation-type', settings.pp_animation_type );
				view.addRenderAttribute( 'effects_data', 'data-canvas-opacity', (settings.vanta_bg_opacity.size != '') ? settings.vanta_bg_opacity.size : '1' );
				view.addRenderAttribute( 'effects_data', 'data-hide-max-width', $max_width );
				view.addRenderAttribute( 'effects_data', 'data-hide-min-width', $min_width );

				if ( 'yes' === settings.pp_background_effects_enable ) {
					if ( 'particles' === settings.pp_animation_type || 'nasa' === settings.pp_animation_type || 'bubble' === settings.pp_animation_type || 'snow' === settings.pp_animation_type ) {
						view.addRenderAttribute( 'effects_data', 'data-part-color', settings.part_color );
						view.addRenderAttribute( 'effects_data', 'data-line-color', settings.line_color );
						view.addRenderAttribute( 'effects_data', 'data-line-h-color', settings.line_hover_color );
						view.addRenderAttribute( 'effects_data', 'data-part-opacity', settings.part_opacity.size );
						view.addRenderAttribute( 'effects_data', 'data-rand-opacity', ( '' != settings.part_rand_opacity ? true : false ) );
						view.addRenderAttribute( 'effects_data', 'data-quantity', settings.part_quantity.size );
						view.addRenderAttribute( 'effects_data', 'data-part-size', settings.part_size.size );
						view.addRenderAttribute( 'effects_data', 'data-part-speed', settings.part_speed.size );
						view.addRenderAttribute( 'effects_data', 'data-part-direction', settings.part_direction );
						view.addRenderAttribute( 'effects_data', 'data-hover-effect', settings.part_hover_effect );
						view.addRenderAttribute( 'effects_data', 'data-hover-size', settings.part_hover_size.size );
					} else if ( 'custom' === settings.pp_animation_type ) {
						view.addRenderAttribute( 'effects_data', 'data-custom-code', settings.part_custom_code );
					} else if ( 'birds' === settings.pp_animation_type ) {
						view.addRenderAttribute( 'effects_data', 'data-bird-bg-color', ( ( '' != settings.bird_bg_color ) ? settings.bird_bg_color.replace( '#', '0x' ) : '0x07192f' ) );
						view.addRenderAttribute( 'effects_data', 'data-bird-color-1', ( ( '' != settings.bird_color_1 ) ? settings.bird_color_1.replace( '#', '0x' ) : '0xff0001' ) );
						view.addRenderAttribute( 'effects_data', 'data-bird-color-2', ( ( '' != settings.bird_color_2 ) ? settings.bird_color_2.replace( '#', '0x' ) : '0x00d1ff' ) );
						view.addRenderAttribute( 'effects_data', 'data-bird-color-mode', ( ( '' != settings.bird_color_mode ) ? settings.bird_color_mode : 'lerp' ) );
						view.addRenderAttribute( 'effects_data', 'data-bird-quantity', ( ( '' != settings.bird_quantity.size ) ? settings.bird_quantity.size : '4' ) );
						view.addRenderAttribute( 'effects_data', 'data-bird-size', ( ( '' != settings.bird_size.size ) ? settings.bird_size.size : '1.5' ) );
						view.addRenderAttribute( 'effects_data', 'data-bird-wing-span', ( ( '' != settings.bird_wing_span.size ) ? settings.bird_wing_span.size : '30' ) );
						view.addRenderAttribute( 'effects_data', 'data-bird-speed-limit', ( ( '' != settings.bird_speed_limit.size ) ? settings.bird_speed_limit.size : '5' ) );
						view.addRenderAttribute( 'effects_data', 'data-bird-separation', ( ( '' != settings.bird_separation.size ) ? settings.bird_separation.size : '20' ) );
						view.addRenderAttribute( 'effects_data', 'data-bird-alignment', ( ( '' != settings.bird_alignment.size ) ? settings.bird_alignment.size : '20' ) );
						view.addRenderAttribute( 'effects_data', 'data-bird-cohesion', ( ( '' != settings.bird_cohesion.size ) ? settings.bird_cohesion.size : '30' ) );
					} else if ( 'fog' === settings.pp_animation_type ) {
						view.addRenderAttribute( 'effects_data', 'data-fog-highlight-color', ( ( '' != settings.fog_highlight_color ) ? settings.fog_highlight_color.replace( '#', '0x' ) : '0xffc302' ) );
						view.addRenderAttribute( 'effects_data', 'data-fog-midtone-color', ( ( '' != settings.fog_midtone_color ) ? settings.fog_midtone_color.replace( '#', '0x' ) : '0xff1d01' ) );
						view.addRenderAttribute( 'effects_data', 'data-fog-lowlight-color', ( ( '' != settings.fog_lowlight_color ) ? settings.fog_lowlight_color.replace( '#', '0x' ) : '0x2c07ff' ) );
						view.addRenderAttribute( 'effects_data', 'data-fog-base-color', ( ( '' != settings.fog_base_color ) ? settings.fog_base_color.replace( '#', '0x' ) : '0xffebeb' ) );
						view.addRenderAttribute( 'effects_data', 'data-fog-blur-factor', ( ( '' != settings.fog_blur_factor.size ) ? settings.fog_blur_factor.size : '0.6' ) );
						view.addRenderAttribute( 'effects_data', 'data-fog-zoom', ( ( '' != settings.fog_zoom.size ) ? settings.fog_zoom.size : '1' ) );
						view.addRenderAttribute( 'effects_data', 'data-fog-speed', ( ( '' != settings.fog_speed.size ) ? settings.fog_speed.size : '1' ) );
					} else if ( 'waves' === settings.pp_animation_type ) {
						view.addRenderAttribute( 'effects_data', 'data-waves-color', ( ( '' != settings.waves_color ) ? settings.waves_color.replace( '#', '0x' ) : '0x005588' ) );
						view.addRenderAttribute( 'effects_data', 'data-waves-shininess', ( ( '' != settings.waves_shininess.size ) ? settings.waves_shininess.size : '30' ) );
						view.addRenderAttribute( 'effects_data', 'data-waves-height', ( ( '' != settings.waves_height.size ) ? settings.waves_height.size : '15' ) );
						view.addRenderAttribute( 'effects_data', 'data-waves-zoom', ( ( '' != settings.waves_zoom.size ) ? settings.waves_zoom.size : '1' ) );
						view.addRenderAttribute( 'effects_data', 'data-waves-speed', ( ( '' != settings.waves_speed.size ) ? settings.waves_speed.size : '1' ) );
					} else if ( 'net' === settings.pp_animation_type ) {
						view.addRenderAttribute( 'effects_data', 'data-net-color', ( ( '' != settings.net_color ) ? settings.net_color.replace( '#', '0x' ) : '0xff3f81' ) );
						view.addRenderAttribute( 'effects_data', 'data-net-bg-color', ( ( '' != settings.net_bg_color ) ? settings.net_bg_color.replace( '#', '0x' ) : '0x23153d' ) );
						view.addRenderAttribute( 'effects_data', 'data-net-points', ( ( '' != settings.net_points.size ) ? settings.net_points.size : '10' ) );
						view.addRenderAttribute( 'effects_data', 'data-net-max-distance', ( ( '' != settings.net_max_distance.size ) ? settings.net_max_distance.size : '20' ) );
						view.addRenderAttribute( 'effects_data', 'data-net-spacing', ( ( '' != settings.net_spacing.size ) ? settings.net_spacing.size : '15' ) );
						view.addRenderAttribute( 'effects_data', 'data-net-show-dot', ( ( '' != settings.net_show_dot ) ? true : false ) );
					} else if ( 'dots' === settings.pp_animation_type ) {
						view.addRenderAttribute( 'effects_data', 'data-dots-color-1', ( ( '' != settings.dots_color_1 ) ? settings.dots_color_1.replace( '#', '0x' ) : '0xff8721' ) );
						view.addRenderAttribute( 'effects_data', 'data-dots-color-2', ( ( '' != settings.dots_color_2 ) ? settings.dots_color_2.replace( '#', '0x' ) : '0xff8721' ) );
						view.addRenderAttribute( 'effects_data', 'data-dots-bg-color', ( ( '' != settings.dots_bg_color ) ? settings.dots_bg_color.replace( '#', '0x' ) : '0x222222' ) );
						view.addRenderAttribute( 'effects_data', 'data-dots-size', ( ( '' != settings.dots_size.size ) ? settings.dots_size.size : '3' ) );
						view.addRenderAttribute( 'effects_data', 'data-dots-spacing', ( ( '' != settings.dots_spacing.size ) ? settings.dots_spacing.size : '35' ) );
					}
					#>
					<div {{{ view.getRenderAttributeString( 'effects_data' ) }}}></div>
				<# } #>
				<?php

				$effects_content = ob_get_contents();
				ob_end_clean();
				$template = $effects_content . $old_template;
				return $template;

			},
			10,
			2
		);
	}
}
