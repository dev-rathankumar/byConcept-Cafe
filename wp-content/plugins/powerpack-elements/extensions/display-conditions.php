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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Conditions Extension
 *
 * Adds display conditions to elements
 *
 * @since 1.4.7
 */
class Extension_Display_Conditions extends Extension_Base {

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
	 * Display Conditions 
	 *
	 * Holds all the conditions for display on the frontend
	 *
	 * @since 1.4.7
	 * @access protected
	 *
	 * @var bool
	 */
	protected $conditions = [];

	/**
	 * A list of scripts that the widgets is depended in
	 *
	 * @since 1.4.7
	 **/
	public function get_script_depends() {
		return [];
	}

	/**
	 * The description of the current extension
	 *
	 * @since 2.-.0
	 **/
	public static function get_description() {
		return __( 'Adds display conditions to widgets and sections allowing you to show them depending on authentication, roles, date and time of day.', 'powerpack' );
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
	 * Add common sections
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 */
	protected function add_common_sections_actions() {

		// Activate sections for widgets
		add_action( 'elementor/element/common/section_custom_css/after_section_end', function( $element, $args ) {

			$this->add_common_sections( $element, $args );

		}, 10, 2 );

		// Activate sections for sections
		add_action( 'elementor/element/section/section_custom_css/after_section_end', function( $element, $args ) {

			$this->add_common_sections( $element, $args );

		}, 10, 2 );

		// Activate sections for widgets if elementor pro
		add_action( 'elementor/element/common/section_custom_css_pro/after_section_end', function( $element, $args ) {

			$this->add_common_sections( $element, $args );

		}, 10, 2 );

	}

	/**
	 * Add Controls
	 *
	 * @since 1.4.7
	 *
	 * @access private
	 */
	private function add_controls( $element, $args ) {

		global $wp_roles;

		$default_date_start = date( 'Y-m-d', strtotime( '-3 day' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
		$default_date_end 	= date( 'Y-m-d', strtotime( '+3 day' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
		$default_interval 	= $default_date_start . ' to ' . $default_date_end;

		$element_type = $element->get_type();

		$element->add_control(
			'pp_display_conditions_enable',
			[
				'label'						=> __( 'Display Conditions', 'powerpack' ),
				'type' 						=> Controls_Manager::SWITCHER,
				'default' 					=> '',
				'label_on' 					=> __( 'Yes', 'powerpack' ),
				'label_off' 				=> __( 'No', 'powerpack' ),
				'return_value'				=> 'yes',
				'frontend_available'		=> true,
			]
		);

		if ( 'widget' === $element_type ) {
			$element->add_control(
				'pp_display_conditions_output',
				[
					'label'					=> __( 'Output HTML', 'powerpack' ),
					'description'			=> sprintf( __( 'If enabled, the HTML code will exist on the page but the %s will be hidden using CSS.', 'powerpack' ), $element_type ),
					'default'				=> '',
					'type'					=> Controls_Manager::SWITCHER,
					'label_on' 				=> __( 'Yes', 'powerpack' ),
					'label_off' 			=> __( 'No', 'powerpack' ),
					'return_value' 			=> 'yes',
					'frontend_available'	=> true,
					'condition'				=> [
						'pp_display_conditions_enable' => 'yes',
					],
				]
			);
		}

		$element->add_control(
			'pp_display_conditions_relation',
			[
				'label'						=> __( 'Display on', 'powerpack' ),
				'type'						=> Controls_Manager::SELECT,
				'default'					=> 'all',
				'options'					=> [
					'all' 		=> __( 'All conditions met', 'powerpack' ),
					'any' 		=> __( 'Any condition met', 'powerpack' ),
				],
				'condition'					=> [
					'pp_display_conditions_enable' => 'yes',
				],
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'pp_condition_key',
			[
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'authentication',
				'label_block'	=> true,
				'groups'		=> [
					[
						'label'					=> __( 'User', 'powerpack' ),
						'options'				=> [
							'authentication' 	=> __( 'Login Status', 'powerpack' ),
							'role' 				=> __( 'User Role', 'powerpack' ),
						],
					],
					[
						'label'					=> __( 'Singular', 'powerpack' ),
						'options' 				=> [
							'page' 				=> __( 'Page', 'powerpack' ),
							'post' 				=> __( 'Post', 'powerpack' ),
							'static_page' 		=> __( 'Special (404, Home, Front, Blog)', 'powerpack' ),
							'post_type' 		=> __( 'Post Type', 'powerpack' ),
						],
					],
					[
						'label'					=> __( 'Archive', 'powerpack' ),
						'options' 				=> [
							'taxonomy_archive' 	=> __( 'Taxonomy', 'powerpack' ),
							'post_type_archive'	=> __( 'Post Type', 'powerpack' ),
							'date_archive'		=> __( 'Date', 'powerpack' ),
							'author_archive'	=> __( 'Author', 'powerpack' ),
							'search_results'	=> __( 'Search', 'powerpack' ),
						],
					],
					[
						'label'					=> __( 'Date & Time', 'powerpack' ),
						'options'				=> [
							'date'				=> __( 'Current Date', 'powerpack' ),
							'time'				=> __( 'Time of Day', 'powerpack' ),
							'day'				=> __( 'Day of Week', 'powerpack' ),
						],
					],
					[
						'label'					=> __( 'Misc', 'powerpack' ),
						'options'				=> [
							'os' 				=> __( 'Operating System', 'powerpack' ),
							'browser' 			=> __( 'Browser', 'powerpack' ),
							'search_bot' 		=> __( 'Search Bots', 'powerpack' ),
						],
					],
				],
			]
		);

		$repeater->add_control(
			'pp_condition_operator',
			[
				'type' 				=> Controls_Manager::SELECT,
				'default' 			=> 'is',
				'label_block' 		=> true,
				'options' 			=> [
					'is' 		=> __( 'Is', 'powerpack' ),
					'not' 		=> __( 'Is not', 'powerpack' ),
				],
			]
		);

		$repeater->add_control(
			'pp_condition_authentication_value',
			[
				'type'				=> Controls_Manager::SELECT,
				'default'			=> 'authenticated',
				'label_block'		=> true,
				'options'			=> [
					'authenticated' => __( 'Logged in', 'powerpack' ),
				],
				'condition'			=> [
					'pp_condition_key' => 'authentication',
				],
			]
		);;

		$repeater->add_control(
			'pp_condition_role_value',
			[
				'type'				=> Controls_Manager::SELECT,
				'description'		=> __( 'Warning: This condition applies only to logged in visitors.', 'powerpack' ),
				'default'			=> 'subscriber',
				'label_block'		=> true,
				'options'			=> $wp_roles->get_names(),
				'condition'			=> [
					'pp_condition_key' => 'role',
				],
			]
		);

		$repeater->add_control(
			'pp_condition_date_value',
			[
				'label'				=> __( 'In interval', 'powerpack' ),
				'type'				=> \Elementor\Controls_Manager::DATE_TIME,
				'picker_options'	=> [
					'enableTime'	=> false,
					'mode' 			=> 'range',
				],
				'label_block'		=> true,
				'default'			=> $default_interval,
				'condition'			=> [
					'pp_condition_key' => 'date',
				],
			]
		);

		$repeater->add_control(
			'pp_condition_day_value',
			[
				'label'				=> __( 'Day(s)', 'powerpack' ),
				'type'				=> \Elementor\Controls_Manager::SELECT2,
				'options' => [
					'1' => __( 'Monday', 'powerpack' ),
					'2' => __( 'Tuesday', 'powerpack' ),
					'3' => __( 'Wednesday', 'powerpack' ),
					'4' => __( 'Thursday', 'powerpack' ),
					'5' => __( 'Friday', 'powerpack' ),
					'6' => __( 'Saturday', 'powerpack' ),
					'7' => __( 'Sunday', 'powerpack' ),
				],
				'multiple'			=> true,
				'label_block'		=> true,
				'default' 			=> '',
				'condition' 		=> [
					'pp_condition_key' => 'day',
				],
			]
		);

		$repeater->add_control(
			'pp_condition_time_value',
			[
				'label'				=> __( 'Before', 'powerpack' ),
				'type'				=> \Elementor\Controls_Manager::DATE_TIME,
				'picker_options'	=> [
					'dateFormat' 	=> "H:i",
					'enableTime' 	=> true,
					'noCalendar' 	=> true,
				],
				'label_block'		=> true,
				'default' 			=> '',
				'condition' 		=> [
					'pp_condition_key' => 'time',
				],
			]
		);

		$os_options = $this->get_os_options();

		$repeater->add_control(
			'pp_condition_os_value',
			[
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> array_keys( $os_options )[0],
				'label_block' 	=> true,
				'options' 		=> $os_options,
				'condition' 	=> [
					'pp_condition_key' => 'os',
				],
			]
		);

		$browser_options = $this->get_browser_options();

		$repeater->add_control(
			'pp_condition_browser_value',
			[
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> array_keys( $browser_options )[0],
				'label_block' 	=> true,
				'options' 		=> $browser_options,
				'condition' 	=> [
					'pp_condition_key' => 'browser',
				],
			]
		);

		$repeater->add_control(
			'pp_condition_search_bot_value',
			[
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'all_search_bots',
				'label_block' 	=> true,
				'options' 		=> ['all_search_bots' => 'All'],
				'condition' 	=> [
					'pp_condition_key' => 'search_bot',
				],
			]
		);
		
		//$pages_all = PP_Posts_Helper::get_all_posts_by_type( 'page' );
		//$posts_all = PP_Posts_Helper::get_all_posts_by_type( 'post' );

		// $repeater->add_control(
		// 	'pp_condition_page_value',
		// 	[
		// 		'type'				=> Controls_Manager::SELECT2,
		// 		'default'			=> '',
		// 		'placeholder'		=> __( 'Any', 'powerpack' ),
		// 		'description'		=> __( 'Leave blank for any page.', 'powerpack' ),
		// 		'label_block' 		=> true,
		// 		'multiple'			=> true,
		// 		'options'			=> $pages_all,
		// 		'condition' 		=> [
		// 			'pp_condition_key' => 'page',
		// 		],
		// 	]
		// );

		$repeater->add_control(
			'pp_condition_page_value',
			[
				'type'				=> 'pp-query',
				'default'			=> '',
				'multiple'			=> true,
				'label_block'		=> true,
				'placeholder'		=> __( 'Any', 'powerpack' ),
				'description'		=> __( 'Leave blank for any page.', 'powerpack' ),
				'query_type'		=> 'posts',
				'object_type'		=> 'page',
				'condition'			=> [
					'pp_condition_key' => 'page',
				],
			]
		);

		// $repeater->add_control(
		// 	'pp_condition_post_value',
		// 	[
		// 		'type'				=> Controls_Manager::SELECT2,
		// 		'default' 		=> '',
		// 		'placeholder'	=> __( 'Any', 'powerpack' ),
		// 		'description'	=> __( 'Leave blank for any post.', 'powerpack' ),
		// 		'label_block' 	=> true,
		// 		'multiple'		=> true,
		// 		'options'			=> $posts_all,
		// 		'condition' 	=> [
		// 			'pp_condition_key' => 'post',
		// 		],
		// 	]
		// );

		$repeater->add_control(
			'pp_condition_post_value',
			[
				'type'				=> 'pp-query',
				'default'			=> '',
				'multiple'			=> true,
				'label_block'		=> true,
				'placeholder'		=> __( 'Any', 'powerpack' ),
				'description'		=> __( 'Leave blank for any post.', 'powerpack' ),
				'query_type'		=> 'posts',
				'object_type'		=> 'post',
				'condition'			=> [
					'pp_condition_key' => 'post',
				],
			]
		);

		$repeater->add_control(
			'pp_condition_static_page_value',
			[
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'home',
				'label_block' 	=> true,
				'options' 		=> [
					'home'		=> __( 'Homepage', 'powerpack' ),
					'static'	=> __( 'Front Page', 'powerpack' ),
					'blog'		=> __( 'Blog', 'powerpack' ),
					'404'		=> __( '404 Page', 'powerpack' ),
				],
				'condition' 	=> [
					'pp_condition_key' => 'static_page',
				],
			]
		);

		$repeater->add_control(
			'pp_condition_post_type_value',
			[
				'type' 			=> Controls_Manager::SELECT2,
				'default' 		=> '',
				'placeholder'	=> __( 'Any', 'powerpack' ),
				'description'	=> __( 'Leave blank or select all for any post type.', 'powerpack' ),
				'label_block' 	=> true,
				'multiple'		=> true,
				'options' 		=> PP_Posts_Helper::get_post_types(),
				'condition' 	=> [
					'pp_condition_key' => 'post_type',
				],
			]
		);

		$repeater->add_control(
			'pp_condition_taxonomy_archive_value',
			[
				'type' 			=> Controls_Manager::SELECT2,
				'default' 		=> '',
				'placeholder'	=> __( 'Any', 'powerpack' ),
				'description'	=> __( 'Leave blank or select all for any taxonomy.', 'powerpack' ),
				'multiple'		=> true,
				'label_block' 	=> true,
				'options' 		=> PP_Posts_Helper::get_taxonomies_options(),
				'condition' 	=> [
					'pp_condition_key' => 'taxonomy_archive',
				],
			]
		);

		$repeater->add_control(
			'pp_condition_post_type_archive_value',
			[
				'type' 			=> Controls_Manager::SELECT2,
				'default' 		=> '',
				'placeholder'	=> __( 'Any', 'powerpack' ),
				'description'	=> __( 'Leave blank or select all for any post type.', 'powerpack' ),
				'multiple'		=> true,
				'label_block' 	=> true,
				'options' 		=> PP_Posts_Helper::get_post_types(),
				'condition' 	=> [
					'pp_condition_key' => 'post_type_archive',
				],
			]
		);

		$repeater->add_control(
			'pp_condition_date_archive_value',
			[
				'type' 			=> Controls_Manager::SELECT2,
				'default' 		=> '',
				'placeholder'	=> __( 'Any', 'powerpack' ),
				'description'	=> __( 'Leave blank or select all for any date based archive.', 'powerpack' ),
				'multiple'		=> true,
				'label_block' 	=> true,
				'options' 		=> [
					'day'		=> __( 'Day', 'powerpack' ),
					'month'		=> __( 'Month', 'powerpack' ),
					'year'		=> __( 'Year', 'powerpack' ),
				],
				'condition' 	=> [
					'pp_condition_key' => 'date_archive',
				],
			]
		);

		// $repeater->add_control(
		// 	'pp_condition_author_archive_value',
		// 	[
		// 		'type'				=> Controls_Manager::SELECT2,
		// 		'default' 		=> '',
		// 		'placeholder'	=> __( 'Any', 'powerpack' ),
		// 		'description'	=> __( 'Leave blank for all authors.', 'powerpack' ),
		// 		'multiple'		=> true,
		// 		'label_block' 	=> true,
		// 		'options'			=> PP_Posts_Helper::get_users(),
		// 		'condition' 	=> [
		// 			'pp_condition_key' => 'author_archive',
		// 		],
		// 	]
		// );

		$repeater->add_control(
            'pp_condition_author_archive_value',
            [
				'type'					=> 'pp-query',
				'placeholder'			=> __( 'Any', 'powerpack' ),
				'description'			=> __( 'Leave blank for all authors.', 'powerpack' ),
				'label_block'			=> true,
				'multiple'				=> true,
				'query_type'			=> 'authors',
				'condition'				=> [
					'pp_condition_key' 	=> 'author_archive',
				],
            ]
        );

		$repeater->add_control(
			'pp_condition_search_results_value',
			[
				'type' 			=> Controls_Manager::TEXT,
				'default' 		=> '',
				'placeholder'	=> __( 'Keywords', 'powerpack' ),
				'description'	=> __( 'Enter keywords, separated by commas, to condition the display on specific keywords and leave blank for any.', 'powerpack' ),
				'label_block' 	=> true,
				'condition' 	=> [
					'pp_condition_key' => 'search_results',
				],
			]
		);

		$element->add_control(
			'pp_display_conditions',
			[
				'label' 	=> __( 'Conditions', 'powerpack' ),
				'type' 		=> Controls_Manager::REPEATER,
				'default' 	=> [
					[
						'pp_condition_key' 					=> 'authentication',
						'pp_condition_operator' 			=> 'is',
						'pp_condition_authentication_value' => 'authenticated',
					],
				],
				'condition'		=> [
					'pp_display_conditions_enable' => 'yes',
				],
				'fields' 		=> $repeater->get_controls(),
				'title_field' 	=> 'Condition',
			]
		);

	}

	/**
	 * Get OS options for control
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 */
	protected function get_os_options() {
		return [
			'iphone' 		=> 'iPhone',
			'android' 		=> 'Android',
			'windows' 		=> 'Windows',
			'open_bsd'		=> 'OpenBSD',
			'sun_os'    	=> 'SunOS',
			'linux'     	=> 'Linux',
			'mac_os'    	=> 'Mac OS',
		];
	}

	/**
	 * Get browser options for control
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 */
	protected function get_browser_options() {
		return [
			'ie'			=> 'Internet Explorer',
			'firefox'		=> 'Mozilla Firefox',
			'chrome'		=> 'Google Chrome',
			'opera_mini'	=> 'Opera Mini',
			'opera'			=> 'Opera',
			'safari'		=> 'Safari',
			'edge'			=> 'Microsoft Edge',
		];
	}

	/**
	 * Add Actions
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 */
	protected function add_actions() {

		// Activate controls for widgets
		add_action( 'elementor/element/common/section_powerpack_elements_advanced/before_section_end', function( $element, $args ) {

			$this->add_controls( $element, $args );

		}, 10, 2 );

		add_action( 'elementor/element/section/section_powerpack_elements_advanced/before_section_end', function( $element, $args ) {

			$this->add_controls( $element, $args );

		}, 10, 2 );

		// Conditions for widgets
		add_action( 'elementor/widget/render_content', function( $widget_content, $element ) {

			$settings = $element->get_settings();

			if ( 'yes' === $settings[ 'pp_display_conditions_enable' ] ) {

				// Set the conditions
				$this->set_conditions( $element->get_id(), $settings['pp_display_conditions'] );

				// if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				// 	ob_start();
				// 	$this->render_editor_notice( $settings );
				// 	$widget_content .= ob_get_clean();
				// }

				if ( ! $this->is_visible( $element->get_id(), $settings['pp_display_conditions_relation'] ) ) { // Check the conditions
					if ( 'yes' !== $settings['pp_display_conditions_output'] ) {
						return; // And on frontend we stop the rendering of the widget
					}
				}
			}
   
			return $widget_content;
		
		}, 10, 2 );

		// Conditions for widgets
		add_action( 'elementor/frontend/widget/before_render', function( $element ) {
			
			$settings = $element->get_settings();

			if ( 'yes' === $settings[ 'pp_display_conditions_enable' ] ) {

				// Set the conditions
				$this->set_conditions( $element->get_id(), $settings['pp_display_conditions'] );

				if ( ! $this->is_visible( $element->get_id(), $settings['pp_display_conditions_relation'] ) ) { // Check the conditions
					$element->add_render_attribute( '_wrapper', 'class', 'pp-visibility-hidden' );
				}
			}

		}, 10, 1 );

		// Conditions for sections
		add_action( 'elementor/frontend/section/before_render', function( $element ) {
			
			$settings = $element->get_settings();

			if ( 'yes' === $settings[ 'pp_display_conditions_enable' ] ) {

				// Set the conditions
				$this->set_conditions( $element->get_id(), $settings['pp_display_conditions'] );

				if ( ! $this->is_visible( $element->get_id(), $settings['pp_display_conditions_relation'] ) ) { // Check the conditions
					$element->add_render_attribute( '_wrapper', 'class', 'pp-visibility-hidden' );
				}
			}

		}, 10, 1 );

	}

	protected function render_editor_notice( $settings ) {
		?><span><?php _e( 'This widget is displayed conditionally.', 'powerpack' ); ?></span>
		<?php
	}

	/**
	 * Set conditions.
	 *
	 * Sets the conditions property to all conditions comparison values
	 *
	 * @since 1.4.7
	 * @access protected
	 * @static
	 *
	 * @param mixed  $conditions  The conditions from the repeater field control
	 *
	 * @return void
	 */
	protected function set_conditions( $id, $conditions = [] ) {
		if ( ! $conditions )
			return;

		foreach ( $conditions as $index => $condition ) {
			$key 		= $condition['pp_condition_key'];
			$operator 	= $condition['pp_condition_operator'];
			$value 		= $condition['pp_condition_' . $key . '_value'];

			if ( method_exists( $this, 'check_' . $key ) ) {
				$check = call_user_func( [ $this, 'check_' . $key ], $value, $operator );
				$this->conditions[ $id ][ $key . '_' . $condition['_id'] ] = $check;
			}
		}
	}

	/**
	 * Check conditions.
	 *
	 * Checks for all or any conditions and returns true or false
	 * depending on wether the content can be shown or not
	 *
	 * @since 1.4.7
	 * @access protected
	 * @static
	 *
	 * @param mixed  $relation  Required conditions relation
	 *
	 * @return bool
	 */
	protected function is_visible( $id, $relation ) {

		if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			if ( 'any' === $relation ) {
				if ( ! in_array( true, $this->conditions[ $id ] ) )
					return false;
			} else {
				if ( in_array( false, $this->conditions[ $id ] ) )
					return false;
			}
		}

		return true;
	}

	/**
	 * Compare conditions.
	 *
	 * Checks two values against an operator
	 *
	 * @since 1.4.7
	 * @access protected
	 * @static
	 *
	 * @param mixed  $left_value  First value to compare.
	 * @param mixed  $right_value Second value to compare.
	 * @param string $operator    Comparison operator.
	 *
	 * @return bool
	 */
	protected static function compare( $left_value, $right_value, $operator ) {
		switch ( $operator ) {
			case 'is':
				return $left_value == $right_value;
			case 'not':
				return $left_value != $right_value;
			default:
				return $left_value === $right_value;
		}
	}

	/**
	 * Check user login status
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string  $operator  Comparison operator.
	 */
	protected static function check_authentication( $value, $operator ) {
		return self::compare( is_user_logged_in(), true, $operator );
	}

	/**
	 * Check user role
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string  $operator  Comparison operator.
	 */
	protected static function check_role( $value, $operator ) {

		$user = wp_get_current_user();
		return self::compare( is_user_logged_in() && in_array( $value, $user->roles ), true, $operator );
	}

	/**
	 * Check date interval
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string  $operator  Comparison operator.
	 */
	protected static function check_date( $value, $operator ) {

		// Split control valur into two dates
		$intervals = explode( 'to' , preg_replace('/\s+/', '', $value ) );

		// Make sure the explode return an array with exactly 2 indexes
		if ( ! is_array( $intervals ) || 2 !== count( $intervals ) ) 
			return;

		// Set start and end dates
		$start 	= $intervals[0];
		$end 	= $intervals[1];
		$today 	= date('Y-m-d');

		// Default returned bool to false
		$show 	= false;

		// Check vars
		if ( \DateTime::createFromFormat( 'Y-m-d', $start ) === false || // Make sure it's a date
			 \DateTime::createFromFormat( 'Y-m-d', $end ) === false ) // Make sure it's a date
			return;

		// Convert to timestamp
		$start_ts 	= strtotime( $start ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		$end_ts 	= strtotime( $end ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );
		$today_ts 	= strtotime( $today ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS );

		// Check that user date is between start & end
		$show = ( ($today_ts >= $start_ts ) && ( $today_ts <= $end_ts ) );

		return self::compare( $show, true, $operator );
	}

	/**
	 * Check time of day interval
	 *
	 * Checks wether current time is in given interval
	 * in order to display element
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string  $operator  Comparison operator.
	 */
	protected static function check_time( $value, $operator ) {

		// Split control valur into two dates
		$time 	= date( 'H:i', strtotime( preg_replace('/\s+/', '', $value ) ) );
		$now 	= date( 'H:i', strtotime("now") + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );

		// Default returned bool to false
		$show 	= false;

		// Check vars
		if ( \DateTime::createFromFormat( 'H:i', $time ) === false ) // Make sure it's a valid DateTime format
			return;

		// Convert to timestamp
		$time_ts 	= strtotime( $time );
		$now_ts 	= strtotime( $now );

		// Check that user date is between start & end
		$show = ( $now_ts < $time_ts );

		return self::compare( $show, true, $operator );
	}

	/**
	 * Check day of week
	 *
	 * Checks wether today falls inside a
	 * specified day of the week
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string  $operator  Comparison operator.
	 */
	protected static function check_day( $value, $operator ) {

		$show = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( $_value === date( 'w' ) ) {
					$show = true; break;
				}
			}
		} else { $show = $value === date( 'w' ); }

		return self::compare( $show, true, $operator );
	}

	/**
	 * Check operating system of visitor
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $operator  Comparison operator.
	 */
	protected static function check_os( $value, $operator ) {

		$oses = [
			'iphone'            => '(iPhone)',
			'android'            => '(Android)',
			'windows' 			=> 'Win16|(Windows 95)|(Win95)|(Windows_95)|(Windows 98)|(Win98)|(Windows NT 5.0)|(Windows 2000)|(Windows NT 5.1)|(Windows XP)|(Windows NT 5.2)|(Windows NT 6.0)|(Windows Vista)|(Windows NT 6.1)|(Windows 7)|(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)|Windows ME',
			'open_bsd'          => 'OpenBSD',
			'sun_os'            => 'SunOS',
			'linux'             => '(Linux)|(X11)',
			'mac_os'            => '(Mac_PowerPC)|(Macintosh)',
		];

		return self::compare( preg_match('@' . $oses[ $value ] . '@', $_SERVER['HTTP_USER_AGENT'] ), true, $operator );
	}

	/**
	 * Check operating system of visitor
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $operator  Comparison operator.
	 */
	protected static function check_search_bot( $value, $operator ) {

		$search_bot = [
			'all_search_bots'        => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)',
		];

		return self::compare( preg_match('@' . $search_bot[ $value ] . '@', $_SERVER['HTTP_USER_AGENT'] ), true, $operator );
	}

	/**
	 * Check browser of visitor
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $operator  Comparison operator.
	 */
	protected static function check_browser( $value, $operator ) {

		$browsers = [
			'ie'			=> [
				'MSIE',
				'Trident',
			],
			'firefox'		=> 'Firefox',
			'chrome'		=> 'Chrome',
			'opera_mini'	=> 'Opera Mini',
			'opera'			=> 'Opera',
			'safari'		=> 'Safari',
			'edge'			=> 'Edge',
		];

		$show = false;

		if ( 'ie' === $value ) {
			if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers[ $value ][0] ) || false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers[ $value ][1] ) ) {
				$show = true;
			}
		} else {
			if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], $browsers[ $value ] ) ) {
				$show = true;

				// Additional check for Chrome that returns Safari
				if ( 'safari' === $value || 'firefox' === $value ) {
					if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'Chrome' ) ) {
						$show = false;
					}
				}
			}
		}
		

		return self::compare( $show, true, $operator );
	}

	/**
	 * Check current page
	 *
	 * @since 2.1.0
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $operator  Comparison operator.
	 */
	protected static function check_page( $value, $operator ) {
		$show = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_page( $_value ) ) {
					$show = true; break;
				}
			}
		} else { $show = is_page( $value ); }

		return self::compare( $show, true, $operator );
	}

	/**
	 * Check current post
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $operator  Comparison operator.
	 */
	protected static function check_post( $value, $operator ) {
		$show = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_single( $_value ) || is_singular( $_value ) ) {
					$show = true; break;
				}
			}
		} else { $show = is_single( $value ) || is_singular( $value ); }

		return self::compare( $show, true, $operator );
	}

	/**
	 * Check static page
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $operator  Comparison operator.
	 */
	protected static function check_static_page( $value, $operator ) {

		if ( 'home' === $value ) {
			return self::compare( ( is_front_page() && is_home() ), true, $operator );
		} elseif ( 'static' === $value ) {
			return self::compare( ( is_front_page() && ! is_home() ), true, $operator );
		} elseif ( 'blog' === $value ) {
			return self::compare( ( ! is_front_page() && is_home() ), true, $operator );
		} elseif ( '404' === $value ) {
			return self::compare( is_404(), true, $operator );
		}
	}

	/**
	 * Check current post type
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $operator  Comparison operator.
	 */
	protected static function check_post_type( $value, $operator ) {
		$show = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_singular( $_value ) ) {
					$show = true; break;
				}
			}
		} else { $show = is_singular( $value ); }

		return self::compare( $show, true, $operator );
	}

	/**
	 * Check current taxonomy archive
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $operator  Comparison operator.
	 */
	protected static function check_taxonomy_archive( $value, $operator ) {
		$show = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {

				$show = self::check_taxonomy_archive_type( $_value );

				if ( $show ) break;
			}
		} else { $show = self::check_taxonomy_archive_type( $value ); }

		return self::compare( $show, true, $operator );
	}

	/**
	 * Checks a given taxonomy against the current page template
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param string  $taxonomy  The taxonomy to check against
	 */
	protected static function check_taxonomy_archive_type( $taxonomy ) {
		if ( 'category' === $taxonomy ) {
			return is_category();
		} else if ( 'post_tag' === $taxonomy ) {
			return is_tag();
		} else if ( '' === $taxonomy || empty( $taxonomy ) ) {
			return is_tax() || is_category() || is_tag();
		} else {
			return is_tax( $taxonomy );
		}

		return false;
	}

	/**
	 * Check current post type archive
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $operator  Comparison operator.
	 */
	protected static function check_post_type_archive( $value, $operator ) {
		$show = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_post_type_archive( $_value ) ) {
					$show = true; break;
				}
			}
		} else { $show = is_post_type_archive( $value ); }

		return self::compare( $show, true, $operator );
	}

	/**
	 * Check current date archive
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $operator  Comparison operator.
	 */
	protected static function check_date_archive( $value, $operator ) {
		$show = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( self::check_date_archive_type( $_value ) ) {
					$show = true; break;
				}
			}
		} else { $show = is_date( $value ); }

		return self::compare( $show, true, $operator );
	}

	/**
	 * Checks a given date type against the current page template
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param string  $type  The type of date archive to check against
	 */
	protected static function check_date_archive_type( $type ) {
		if ( 'day' === $type ) { // Day
			return is_day();
		} elseif ( 'month' === $type ) { // Month
			return is_month();
		} elseif ( 'year' === $type ) { // Year
			return is_year();
		}

		return false;
	}

	/**
	 * Check current author archive
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $operator  Comparison operator.
	 */
	protected static function check_author_archive( $value, $operator ) {
		$show = false;

		if ( is_array( $value ) && ! empty( $value ) ) {
			foreach ( $value as $_key => $_value ) {
				if ( is_author( $_value ) ) {
					$show = true; break;
				}
			}
		} else { $show = is_author( $value ); }

		return self::compare( $show, true, $operator );
	}

	/**
	 * Check current search query
	 *
	 * @since 1.4.7
	 *
	 * @access protected
	 *
	 * @param mixed  $value  The control value to check
	 * @param string $operator  Comparison operator.
	 */
	protected static function check_search_results( $value, $operator ) {
		$show = false;

		if ( is_search() ) {

			if ( empty( $value ) ) { // We're showing on all search pages

				$show = true;

			} else { // We're showing on specific keywords

				$phrase = get_search_query(); // The user search query

				if ( '' !== $phrase && ! empty( $phrase ) ) { // Only proceed if there is a query

					$keywords = explode( ',', $value ); // Separate keywords

					foreach ( $keywords as $index => $keyword ) {
						if ( self::keyword_exists( trim( $keyword ), $phrase ) ) {
							$show = true; break;
						}
					}
				}
			}
		}

		return self::compare( $show, true, $operator );
	}

	protected static function keyword_exists( $keyword, $phrase ) {
		return strpos( $phrase, trim( $keyword ) ) !== false;
	}
}