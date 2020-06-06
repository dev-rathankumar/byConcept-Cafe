<?php
namespace PowerpackElements\Modules\Countdown\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
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
 * Countdown Widget
 */
class Countdown extends Powerpack_Widget {

	/**
	 * Retrieve contact form 7 widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Countdown' );
	}

	/**
	 * Retrieve contact form 7 widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Countdown' );
	}

    /**
	 * Retrieve the list of categories the counter widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Countdown' );
    }

	 /**
	 * Retrieve contact form 7 widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Countdown' );
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
		return parent::get_widget_keywords( 'Countdown' );
	}

    /**
	 * Retrieve the list of scripts the logo carousel widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_script_depends() {
        return [
            'pp-jquery-plugin',
            'pp-countdown-plugin',
            'jquery-cookie',
            'pp-frontend-countdown',
			'powerpack-frontend'
        ];
    }

	protected function _register_controls() {
		
		/* Content Tab */
		$this->register_content_countdown_controls();
		$this->register_content_action_controls();
		$this->register_content_structure_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_general_controls();
		$this->register_style_separator_controls();
		$this->register_style_box_controls();
		$this->register_style_labels_controls();
		$this->register_style_typography_controls();
	}
	
	protected function register_content_countdown_controls() {
		/**
         * Content Tab: Countdown
         */
		$this->start_controls_section(
			'section_countdown',
			[
				'label' => __( 'Countdown', 'powerpack' ),
			]
		);

		$this->add_control(
			'timer_type',
			[
				'label'                 => __( 'Timer Type', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'fixed',
				'options'               => [
					'fixed' 	=> __( 'Fixed', 'powerpack' ),
					'evergreen' => __( 'Evergreen', 'powerpack' ),
				],
			]
		);

		$this->add_control(
			'years',
			[
				'label'                 => __( 'Years', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'				=> pp_get_normal_years(),
				'default'               => '2020',
				'condition'             => [
					'timer_type'    => 'fixed',
				],
			]
		);

		$this->add_control(
			'months',
			[
				'label'                 => __( 'Months', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'				=> pp_get_normal_month(),
				'default'               => '8',
				'condition'             => [
					'timer_type'    => 'fixed',
				],
			]
		);

		$this->add_control(
			'days',
			[
				'label'                 => __( 'Days', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'				=> pp_get_normal_date(),
				'default'               => '9',
			]
		);

		$this->add_control(
			'hours',
			[
				'label'                 => __( 'Hours', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'				=> pp_get_normal_hour(),
				'default'               => '7',
			]
		);

		$this->add_control(
			'minutes',
			[
				'label'                 => __( 'Minutes', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'				=> pp_get_normal_minutes(),
				'default'               => '10',
			]
		);

		$this->add_control(
			'seconds',
			[
				'label'                 => __( 'Seconds', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'				=> pp_get_normal_seconds(),
				'default'               => '20',
				'condition'             => [
					'timer_type'    => 'evergreen',
				],
			]
		);

		$this->end_controls_section();
	}
	
	protected function register_content_action_controls() {
		/**
         * Content Tab: Action
         */
		$this->start_controls_section(
			'section_counter_fixed_action',
			[
				'label' => __( 'Action', 'powerpack' ),
				'condition'	=> [
					'timer_type'    => 'fixed',
				],
			]
		);

		$this->add_control(
			'fixed_timer_action',
			[
				'label' => __( 'Action After Timer Expires', 'powerpack' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => __( 'None', 'powerpack' ),
					'hide' => __( 'Hide Timer', 'powerpack' ),
					'msg' => __( 'Display Message', 'powerpack' ),
					'redirect' => __( 'Redirect to URL', 'powerpack' ),
				],
				'default' => 'none',
				'condition'	=> [
					'timer_type'    => 'fixed',
				],
			]
		);

		$this->add_control(
            'fixed_expire_message',
            [
                'label'                 => __( 'Expiry Message', 'powerpack' ),
                'type'                  => Controls_Manager::TEXTAREA,
				 'condition'	=> [
					'fixed_timer_action'    => 'msg',
				],
            ]
        );

		$this->add_control(
            'fixed_redirect_link',
            [
                'label'                 => __( 'Link', 'powerpack' ),
                'type'                  => Controls_Manager::URL,
                'placeholder'           => '',
                'default'               => [
                    'url' => '#',
                ],
                'condition'	=> [
					'fixed_timer_action'    => 'redirect',
				],
            ]
        );

		$this->add_control(
			'fixed_redirect_link_target',
			[
				'label' => __( 'Link Target', 'powerpack' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'_self' => __( 'Same Window', 'powerpack' ),
					'_blank' => __( 'New Window', 'powerpack' ),
				],
				'default' => '_self',
				'condition'	=> [
					'fixed_timer_action'    => 'redirect',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_counter_evergreen_action',
			[
				'label' => __( 'Action', 'powerpack' ),
				'condition'	=> [
					'timer_type'    => 'evergreen',
				],
			]
		);

		$this->add_control(
			'evergreen_timer_action',
			[
				'label' => __( 'Action After Timer Expires', 'powerpack' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => __( 'None', 'powerpack' ),
					'hide' => __( 'Hide Timer', 'powerpack' ),
					'reset' => __( 'Reset Timer', 'powerpack' ),
					'msg' => __( 'Display Message', 'powerpack' ),
					'redirect' => __( 'Redirect to URL', 'powerpack' ),
				],
				'default' => 'none',
				'condition'	=> [
					'timer_type'    => 'evergreen',
				],
			]
		);

		$this->add_control(
            'evergreen_expire_message',
            [
                'label'                 => __( 'Expiry Message', 'powerpack' ),
                'type'                  => Controls_Manager::TEXTAREA,
				 'condition'	=> [
					'evergreen_timer_action'    => 'msg',
				],
            ]
        );

		$this->add_control(
            'evergreen_redirect_link',
            [
                'label'                 => __( 'Link', 'powerpack' ),
                'type'                  => Controls_Manager::URL,
                'placeholder'           => '',
                'default'               => [
                    'url' => '#',
                ],
                'condition'	=> [
					'evergreen_timer_action'    => 'redirect',
				],
            ]
        );

		$this->add_control(
			'evergreen_redirect_link_target',
			[
				'label' => __( 'Link Target', 'powerpack' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'_self' => __( 'Same Window', 'powerpack' ),
					'_blank' => __( 'New Window', 'powerpack' ),
				],
				'default' => '_self',
				'condition'	=> [
					'evergreen_timer_action'    => 'redirect',
				],
			]
		);

		$this->end_controls_section();
	}
	
	protected function register_content_structure_controls() {
		/**
         * Content Tab: Structure
         */
		$this->start_controls_section(
			'section_structure',
			[
				'label' => __( 'Structure', 'powerpack' ),
			]
		);

		$this->add_control(
			'show_labels',
			[
				'label' => __( 'Show Labels', 'powerpack' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'powerpack' ),
				'label_off' => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_years',
			[
				'label' => __( 'Show Years', 'powerpack' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'powerpack' ),
				'label_off' => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'label_years_plural',
			[
				'label' => __( 'Label in Plural', 'powerpack' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Years', 'powerpack' ),
				'placeholder' => __( 'Years', 'powerpack' ),
				'condition' => [
					'show_labels!' => '',
					'show_years' => 'yes',
				],
			]
		);

		$this->add_control(
			'label_years_singular',
			[
				'label' => __( 'Label in Singular', 'powerpack' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Year', 'powerpack' ),
				'placeholder' => __( 'Year', 'powerpack' ),
				'condition' => [
					'show_labels!' => '',
					'show_years' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_months',
			[
				'label' => __( 'Show Months', 'powerpack' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'powerpack' ),
				'label_off' => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'label_months_plural',
			[
				'label' => __( 'Label in Plural', 'powerpack' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Months', 'powerpack' ),
				'placeholder' => __( 'Months', 'powerpack' ),
				'condition' => [
					'show_labels!' => '',
					'show_months' => 'yes',
				],
			]
		);

		$this->add_control(
			'label_months_singular',
			[
				'label' => __( 'Label in Singular', 'powerpack' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Month', 'powerpack' ),
				'placeholder' => __( 'Month', 'powerpack' ),
				'condition' => [
					'show_labels!' => '',
					'show_months' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_days',
			[
				'label' => __( 'Show Days', 'powerpack' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'powerpack' ),
				'label_off' => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'label_days_plural',
			[
				'label' => __( 'Label in Plural', 'powerpack' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Days', 'powerpack' ),
				'placeholder' => __( 'Days', 'powerpack' ),
				'condition' => [
					'show_labels!' => '',
					'show_days' => 'yes',
				],
			]
		);

		$this->add_control(
			'label_days_singular',
			[
				'label' => __( 'Label in Singular', 'powerpack' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Day', 'powerpack' ),
				'placeholder' => __( 'Day', 'powerpack' ),
				'condition' => [
					'show_labels!' => '',
					'show_days' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_hours',
			[
				'label' => __( 'Show Hours', 'powerpack' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'powerpack' ),
				'label_off' => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'label_hours_plural',
			[
				'label' => __( 'Label in Plural', 'powerpack' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Hours', 'powerpack' ),
				'placeholder' => __( 'Hours', 'powerpack' ),
				'condition' => [
					'show_labels!' => '',
					'show_hours' => 'yes',
				],
			]
		);

		$this->add_control(
			'label_hours_singular',
			[
				'label' => __( 'Label in Singular', 'powerpack' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Hour', 'powerpack' ),
				'placeholder' => __( 'Hour', 'powerpack' ),
				'condition' => [
					'show_labels!' => '',
					'show_hours' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_minutes',
			[
				'label' => __( 'Show Minutes', 'powerpack' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'powerpack' ),
				'label_off' => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'label_minutes_plural',
			[
				'label' => __( 'Label in Plural', 'powerpack' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Minutes', 'powerpack' ),
				'placeholder' => __( 'Minutes', 'powerpack' ),
				'condition' => [
					'show_labels!' => '',
					'show_minutes' => 'yes',
				],
			]
		);

		$this->add_control(
			'label_minutes_singular',
			[
				'label' => __( 'Label in Singular', 'powerpack' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Minute', 'powerpack' ),
				'placeholder' => __( 'Minute', 'powerpack' ),
				'condition' => [
					'show_labels!' => '',
					'show_minutes' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_seconds',
			[
				'label' => __( 'Show Seconds', 'powerpack' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'powerpack' ),
				'label_off' => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'label_seconds_plural',
			[
				'label' => __( 'Label in Plural', 'powerpack' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Seconds', 'powerpack' ),
				'placeholder' => __( 'Seconds', 'powerpack' ),
				'condition' => [
					'show_labels!' => '',
					'show_seconds' => 'yes',
				],
			]
		);

		$this->add_control(
			'label_seconds_singular',
			[
				'label' => __( 'Label in Singular', 'powerpack' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Second', 'powerpack' ),
				'placeholder' => __( 'Second', 'powerpack' ),
				'condition' => [
					'show_labels!' => '',
					'show_seconds' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}
	
	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links('Countdown');

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
	
	protected function register_style_general_controls() {
		/**
         * Style Tab: General
         */
		$this->start_controls_section(
			'section_general_style',
			[
				'label' => __( 'General', 'powerpack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'counter_alignment',
			[
				'label' => __( 'Alignment', 'powerpack' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'left' => __( 'Left', 'powerpack' ),
					'center' => __( 'Center', 'powerpack' ),
					'right' => __( 'Right', 'powerpack' ),
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .pp-countdown-wrapper' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'digit_label_spacing',
			[
				'label' => __( 'Space between label & digit', 'powerpack' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition'             => [
                    'show_labels'    => 'yes',
                ],
				'selectors' => [
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-label-pos-out_below .pp-countdown-item .pp-countdown-digit-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-label-pos-out_above .pp-countdown-item .pp-countdown-digit-wrapper' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-label-pos-out_right .pp-countdown-item .pp-countdown-digit-wrapper' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-label-pos-out_left .pp-countdown-item .pp-countdown-digit-wrapper' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-label-pos-in_below .pp-countdown-item .pp-countdown-digit' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-label-pos-in_above .pp-countdown-item .pp-countdown-digit' => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-label-pos-normal_below .pp-countdown-item .pp-countdown-digit' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-label-pos-normal_above .pp-countdown-item .pp-countdown-digit' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'block_spacing',
			[
				'label' => __( 'Space between blocks', 'powerpack' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => -20,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-align-right .pp-countdown-item' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-align-left .pp-countdown-item' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-align-center .pp-countdown-item' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 ); margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-separator-line .pp-countdown-item' => 'padding-left: calc( {{SIZE}}{{UNIT}}/2 ); padding-right: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-separator-colon .pp-countdown-item .pp-countdown-digit-wrapper' => 'padding-left: calc( {{SIZE}}{{UNIT}}/2 ); padding-right: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-separator-colon.pp-countdown-style-default .pp-countdown-item .pp-countdown-digit-wrapper' => 'padding: 0 {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-separator-line.pp-countdown-style-default .pp-countdown-item' => 'padding: 0 {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-separator-colon .pp-countdown-item .pp-countdown-digit-wrapper:after' => 'right: calc( -{{SIZE}}{{UNIT}} / 2 + -5{{UNIT}} );',
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-separator-line .pp-countdown-item:after' => 'right: calc( -{{SIZE}}{{UNIT}} / 2 + 5{{UNIT}} );',
				],
			]
		);

		$this->end_controls_section();
	}
	
	protected function register_style_separator_controls() {
		/**
         * Style Tab: Separator
         */
		$this->start_controls_section(
			'section_separator_style',
			[
				'label' => __( 'Separator', 'powerpack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'separator_type',
			[
				'label' => __( 'Show Separator', 'powerpack' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => __( 'None', 'powerpack' ),
					'colon' => __( 'Colon', 'powerpack' ),
					'line' => __( 'Line', 'powerpack' ),
				],
				'default' => 'none',
			]
		);

		$this->add_control(
            'separator_color',
            [
                'label'                 => __( 'Separator Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'condition'             => [
                    'separator_type!'    => 'none',
                ],
				'selectors' => [
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-separator-line .pp-countdown-item:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-separator-colon .pp-countdown-item .pp-countdown-digit-wrapper:after' => 'color: {{VALUE}};',
				],
            ]
        );

		$this->add_responsive_control(
			'separator_size',
			[
				'label' => __( 'Separator Size', 'powerpack' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition'             => [
                    'separator_type'    => 'colon',
                ],
				'selectors' => [
					'{{WRAPPER}} .pp-countdown-wrapper.pp-countdown-separator-colon .pp-countdown-item .pp-countdown-digit-wrapper:after' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'hide_separator',
			[
				'label' => __( 'Hide on mobile', 'powerpack' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'powerpack' ),
				'label_off' => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->end_controls_section();
	}
	
	protected function register_style_box_controls() {
		/**
         * Style Tab: Boxes
         */
		$this->start_controls_section(
			'section_box_style',
			[
				'label' => __( 'Boxes', 'powerpack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'block_style',
			[
				'label' => __( 'Style', 'powerpack' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => __( 'Default', 'powerpack' ),
					'circle' => __( 'Circle', 'powerpack' ),
					'square' => __( 'Square', 'powerpack' ),
				],
				'default' => 'default',
			]
		);

		$this->add_responsive_control(
			'block_width',
			[
				'label' => __( 'Width', 'powerpack' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 100,
				],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 500,
					],
				],
				'condition'             => [
                    'block_style!'    => 'default',
                ],
				'selectors' => [
					'{{WRAPPER}} .pp-countdown-wrapper .pp-countdown-item .pp-countdown-digit-wrapper' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; padding: calc( {{SIZE}}{{UNIT}}/4 );',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'              => 'toggle_switch_background',
				'types'            	=> [ 'none','classic','gradient' ],
				'selector' 			=> '{{WRAPPER}} .pp-countdown-wrapper .pp-countdown-item .pp-countdown-digit-wrapper',
				'condition'             => [
                    'block_style!'    => 'default',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'box_border',
				'selector'	=> '{{WRAPPER}} .pp-countdown-wrapper .pp-countdown-item .pp-countdown-digit-wrapper',
				'separator' => 'before',
				'condition'             => [
                    'block_style!'    => 'default',
                ],
			]
		);

		$this->add_control(
			'box_border_radius',
			[
				'label' => __( 'Border Radius', 'powerpack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-countdown-wrapper .pp-countdown-item .pp-countdown-digit-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	=> [
                    'block_style'    => 'square',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'block_box_shadow',
				'selector' 				=> '{{WRAPPER}} .pp-countdown-wrapper .pp-countdown-item .pp-countdown-digit-wrapper',
				'separator'             => 'before',
				'condition'             => [
                    'block_style!'    => 'default',
                ],
			]
		);

		$this->end_controls_section();
	}
	
	protected function register_style_labels_controls() {
		/**
         * Style Tab: Labels
         */
		$this->start_controls_section(
			'section_label_style',
			[
				'label' => __( 'Labels', 'powerpack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_position',
			[
				'label' => __( 'Label Position', 'powerpack' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'inside' => __( 'Inside Digit Container', 'powerpack' ),
					'outside' => __( 'Outside Digit Container', 'powerpack' ),
				],
				'default' => 'outside',
			]
		);

		$this->add_control(
			'label_inside_position',
			[
				'label' => __( 'Label Inside Position', 'powerpack' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'in_below' => __( 'Below Digit', 'powerpack' ),
					'in_above' => __( 'Above Digit', 'powerpack' ),
				],
				'default' => 'in_below',
				'condition'             => [
                    'label_position'    => 'inside',
                ],
			]
		);

		$this->add_control(
			'label_outside_position',
			[
				'label' => __( 'Label Outside Position', 'powerpack' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'out_below' => __( 'Below Digit', 'powerpack' ),
					'out_above' => __( 'Above Digit', 'powerpack' ),
					'out_right' => __( 'Right Side of Digit', 'powerpack' ),
					'out_left' 	=> __( 'Left Side of Digit', 'powerpack' ),
				],
				'default' => 'out_below',
				'condition'             => [
                    'label_position'    => 'outside',
                ],
			]
		);

		$this->add_control(
			'default_position',
			[
				'label' => __( 'Select Position', 'powerpack' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'normal_below' => __( 'Below Digit', 'powerpack' ),
					'normal_above' => __( 'Above Digit', 'powerpack' ),
				],
				'default' => 'normal_below',
				'condition'             => [
                    'block_style'    => 'default',
                ],
			]
		);

		$this->add_control(
			'label_bg_color',
			[
				'label' => __( 'Background Color', 'powerpack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pp-countdown-wrapper .pp-countdown-item .pp-countdown-label' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'label_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-countdown-wrapper .pp-countdown-item .pp-countdown-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}
	
	protected function register_style_typography_controls() {
		/**
         * Style Tab: Typography
         */
		$this->start_controls_section(
			'section_typography',
			[
				'label' => __( 'Typography', 'powerpack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_digits',
			[
				'label' => __( 'Digits', 'powerpack' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'digit_color',
			[
				'label' => __( 'Color', 'powerpack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pp-countdown-wrapper .pp-countdown-item .pp-countdown-digit' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'digit_tag',
			[
				'label'	=> __('Tag', 'powerpack'),
				'type'	=> Controls_Manager::SELECT,
				'default'	=> 'h3',
				'options'	=> [
					'h1'	=> 'h1',
					'h2'	=> 'h2',
					'h3'	=> 'h3',
					'h4'	=> 'h4',
					'h5'	=> 'h5',
					'h6'	=> 'h6',
					'div'	=> 'div',
					'p'		=> 'p',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'digit_typography',
				'selector' => '{{WRAPPER}} .pp-countdown-wrapper .pp-countdown-item .pp-countdown-digit',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->add_control(
			'heading_label',
			[
				'label' => __( 'Label', 'powerpack' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => __( 'Color', 'powerpack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pp-countdown-wrapper .pp-countdown-item .pp-countdown-label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'label_tag',
			[
				'label'	=> __('Tag', 'powerpack'),
				'type'	=> Controls_Manager::SELECT,
				'default'	=> 'p',
				'options'	=> [
					'h1'	=> 'h1',
					'h2'	=> 'h2',
					'h3'	=> 'h3',
					'h4'	=> 'h4',
					'h5'	=> 'h5',
					'h6'	=> 'h6',
					'div'	=> 'div',
					'p'		=> 'p',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .pp-countdown-wrapper .pp-countdown-item .pp-countdown-label',
				'scheme' => Scheme_Typography::TYPOGRAPHY_2,
			]
		);

		$this->add_control(
			'heading_message',
			[
				'label' => __( 'Expiry Message', 'powerpack' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'message_color',
			[
				'label' => __( 'Color', 'powerpack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pp-countdown-wrapper .pp-countdown-expire-message' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'message_typography',
				'selector' => '{{WRAPPER}} .pp-countdown-wrapper .pp-countdown-expire-message',
				'scheme' => Scheme_Typography::TYPOGRAPHY_2,
			]
		);

		$this->end_controls_section();
	}

	public function get_gmt_difference() {

		$timezone = get_option('timezone_string');

		if( ! empty( $timezone ) ) {

            $time_zone_kolkata = new \DateTimeZone("Asia/Kolkata");
            $time_zone = new \DateTimeZone($timezone);

            $time_kolkata = new \DateTime("now", $time_zone_kolkata);

            $timeOffset = $time_zone->getOffset($time_kolkata);

            return $timeOffset / 3600;
        }
        else {
            return "NULL";
		}
		
	}

	private function _get_settings() {
		$instance  = $this->get_settings();

		$keys = array(
			'timer_type',
			'years',
			'months',
			'days',
			'hours',
			'minutes',
			'seconds',
			'show_labels',
			'show_years',
			'show_months',
			'show_days',
			'show_hours',
			'show_minutes',
			'show_seconds',
			'label_years_plural',
			'label_years_singular',
			'label_months_plural',
			'label_months_singular',
			'label_days_plural',
			'label_days_singular',
			'label_hours_plural',
			'label_hours_singular',
			'label_minutes_plural',
			'label_minutes_singular',
			'label_seconds_plural',
			'label_seconds_singular',
			'block_style',
			'label_position',
			'label_position_inside',
			'label_position_outside',
			'evergreen_timer_action',
			'fixed_timer_action',
		);

		$defaults = array(
			'label_years_plural'		=> 'Years',
			'label_years_singular'		=> 'Year',
			'label_months_plural'		=> 'Months',
			'label_months_singular'		=> 'Month',
			'label_days_plural'			=> 'Days',
			'label_days_singular'		=> 'Day',
			'label_hours_plural'		=> 'Hours',
			'label_hours_singular'		=> 'Hour',
			'label_minutes_plural'		=> 'Minutes',
			'label_minutes_singular'	=> 'Minute',
			'label_seconds_plural'		=> 'Seconds',
			'label_seconds_singular'	=> 'Second',
		);

		$settings = array();

		foreach ( $keys as $key ) {
			if ( ! isset( $instance[$key] ) ) {
				continue;
			}

			if ( empty( $instance[$key] ) && in_array( $key, $defaults ) ) {
				$settings[$key] = $defaults[$key];
			} else {
				$settings[$key] = $instance[$key];
			}
		}

		/***********************************
		 * Build layout
		 ***********************************/

		// default block style.
		if ( 'default' == $instance['block_style'] ) {
			if ( 'normal_below' == $instance['default_position'] ) {
				$layout = '';
				if ( 'evergreen' != $instance['timer_type'] ) {
					$layout .= "{y<} {$this->render_normal_countdown( '{ynn}', '{yl}' )} {y>}";
				}
				$layout .= "
					{o<} {$this->render_normal_countdown( '{onn}', '{ol}' )} {o>}
					{d<} {$this->render_normal_countdown( '{dnn}', '{dl}' )} {d>}
					{h<} {$this->render_normal_countdown( '{hnn}', '{hl}' )} {h>}
					{m<} {$this->render_normal_countdown( '{mnn}', '{ml}' )} {m>}
					{s<} {$this->render_normal_countdown( '{snn}', '{sl}' )} {s>}
				";

				$settings['timer_layout'] = "{$layout}";
			}

			if ( 'normal_above' == $instance['default_position'] ) {
				$layout = '';
				if ( 'evergreen' != $instance['timer_type'] ) {
					$layout .= "{y<} {$this->render_normal_above_countdown( '{ynn}', '{yl}', '{y>}' )}";
				}
				$layout .= "
					{o<} {$this->render_normal_above_countdown( '{onn}', '{ol}', '{o>}' )}
					{d<} {$this->render_normal_above_countdown( '{dnn}', '{dl}', '{d>}' )}
					{h<} {$this->render_normal_above_countdown( '{hnn}', '{hl}', '{h>}' )}
					{m<} {$this->render_normal_above_countdown( '{mnn}', '{ml}', '{m>}' )}
					{s<} {$this->render_normal_above_countdown( '{snn}', '{sl}', '{s>}' )}
				";

				$settings['timer_layout'] = "{$layout}";
			}
		}

		// label position outside.
		if ( 'outside' == $instance['label_position'] ) {
			if ( 'out_below' == $instance['label_outside_position'] ) {
				$layout = '';
				if ( 'evergreen' != $instance['timer_type'] ) {
					$layout .= "{y<} {$this->render_normal_countdown( '{ynn}', '{yl}' )} {y>}";
				}
				$layout .= "
					{o<} {$this->render_normal_countdown( '{onn}', '{ol}' )} {o>}
					{d<} {$this->render_normal_countdown( '{dnn}', '{dl}' )} {d>}
					{h<} {$this->render_normal_countdown( '{hnn}', '{hl}' )} {h>}
					{m<} {$this->render_normal_countdown( '{mnn}', '{ml}' )} {m>}
					{s<} {$this->render_normal_countdown( '{snn}', '{sl}' )} {s>}
				";

				$settings['timer_layout'] = "{$layout}";
			}

			if ( 'out_above' == $instance['label_outside_position'] || 'out_right' == $instance['label_outside_position'] || 'out_left' == $instance['label_outside_position'] ) {
				$layout = '';
				if ( 'evergreen' != $instance['timer_type'] ) {
					$layout .= "{y<} {$this->render_outside_countdown( '{ynn}', '{yl}', '{y>}' )}";
				}
				$layout .= "
					{o<} {$this->render_outside_countdown( '{onn}', '{ol}', '{o>}' )}
					{d<} {$this->render_outside_countdown( '{dnn}', '{dl}', '{d>}' )}
					{h<} {$this->render_outside_countdown( '{hnn}', '{hl}', '{h>}' )}
					{m<} {$this->render_outside_countdown( '{mnn}', '{ml}', '{m>}' )}
					{s<} {$this->render_outside_countdown( '{snn}', '{sl}', '{s>}' )}
				";

				$settings['timer_layout'] = "{$layout}";
			}
		}

		// label position inside.
		if ( 'inside' == $instance['label_position'] ) {
			if ( 'in_below' == $instance['label_inside_position'] ) {
				$layout = '';
				if ( 'evergreen' != $instance['timer_type'] ) {
					$layout .= "{y<} {$this->render_inside_below_countdown( '{ynn}', '{yl}', '{y>}' )}";
				}
				$layout .= "
					{o<} {$this->render_inside_below_countdown( '{onn}', '{ol}', '{o>}' )}
					{d<} {$this->render_inside_below_countdown( '{dnn}', '{dl}', '{d>}' )}
					{h<} {$this->render_inside_below_countdown( '{hnn}', '{hl}', '{h>}' )}
					{m<} {$this->render_inside_below_countdown( '{mnn}', '{ml}', '{m>}' )}
					{s<} {$this->render_inside_below_countdown( '{snn}', '{sl}', '{s>}' )}
				";

				$settings['timer_layout'] = "{$layout}";
			}

			if ( 'in_above' == $instance['label_inside_position'] ) {
				$layout = '';
				if ( 'evergreen' != $instance['timer_type'] ) {
					$layout .= "{y<} {$this->render_inside_above_countdown( '{ynn}', '{yl}', '{y>}' )}";
				}
				$layout .= "
					{o<} {$this->render_inside_above_countdown( '{onn}', '{ol}', '{o>}' )}
					{d<} {$this->render_inside_above_countdown( '{dnn}', '{dl}', '{d>}' )}
					{h<} {$this->render_inside_above_countdown( '{hnn}', '{hl}', '{h>}' )}
					{m<} {$this->render_inside_above_countdown( '{mnn}', '{ml}', '{m>}' )}
					{s<} {$this->render_inside_above_countdown( '{snn}', '{sl}', '{s>}' )}
				";

				$settings['timer_layout'] = "{$layout}";
			}
		}

		/***********************************
		 * Timer format
		 ***********************************/
		$settings['timer_format'] = '';

		if ( $settings['show_years'] ) {
			$settings['timer_format'] .= 'Y';
		}
		if ( $settings['show_months'] ) {
			$settings['timer_format'] .= 'O';
		}
		if ( $settings['show_days'] ) {
			$settings['timer_format'] .= 'D';
		}
		if ( $settings['show_hours'] ) {
			$settings['timer_format'] .= 'H';
		}
		if ( $settings['show_minutes'] ) {
			$settings['timer_format'] .= 'M';
		}
		if ( $settings['show_seconds'] ) {
			$settings['timer_format'] .= 'S';
		}

		/***********************************
		 * Timer labels
		 ***********************************/
		$settings['timer_labels'] = "{$settings['label_years_plural']},{$settings['label_months_plural']},,{$settings['label_days_plural']},{$settings['label_hours_plural']},{$settings['label_minutes_plural']},{$settings['label_seconds_plural']}";
		$settings['timer_labels_singular'] = "{$settings['label_years_singular']},{$settings['label_months_singular']},,{$settings['label_days_singular']},{$settings['label_hours_singular']},{$settings['label_minutes_singular']},{$settings['label_seconds_singular']}";

		/***********************************
		 * Timezone difference
		 ***********************************/
		$settings['time_zone'] = "{$this->get_gmt_difference()}";

		$settings['timer_exp_text'] = '';

		/***********************************
		 * Redirect link and expire message
		 ***********************************/
		if ( 'evergreen' == $instance['timer_type'] ) {
			$settings['redirect_link'] 			= ! empty( $instance['evergreen_redirect_link'] ) ? $instance['evergreen_redirect_link'] : '';
			$settings['redirect_link_target'] 	= ! empty( $instance['evergreen_redirect_link_target'] ) ? $instance['evergreen_redirect_link_target'] : '';
			$settings['expire_message'] 		= ! empty( $instance['evergreen_expire_message'] ) ? preg_replace( '/\s+/', ' ', $instance['evergreen_expire_message'] ) : '';
		}
		if ( 'fixed' == $instance['timer_type'] ) {
			$settings['redirect_link'] 			= ! empty( $instance['fixed_redirect_link'] ) ? $instance['fixed_redirect_link'] : '';
			$settings['redirect_link_target'] 	= ! empty( $instance['fixed_redirect_link_target'] ) ? $instance['fixed_redirect_link_target'] : '';
			$settings['expire_message'] 		= ! empty( $instance['fixed_expire_message'] ) ? preg_replace( '/\s+/', ' ', $instance['fixed_expire_message'] ) : '';
			$settings['timer_exp_text'] 		= '<div class="pp-countdown-expire-message">' . $settings['expire_message'] . '</div>';
		}

		return $settings;
	}

	public function render_normal_countdown( $str1, $str2 ) {
		$settings = (object) $this->get_settings();
		ob_start();

		?>
		<div class="pp-countdown-item">
			<div class="pp-countdown-digit-wrapper">
				<<?php echo $settings->digit_tag; ?> class="pp-countdown-digit">
					<?php echo $str1; ?>
				</<?php echo $settings->digit_tag; ?>>
			</div>
			<?php if( 'yes' == $settings->show_labels ) { ?>
				<div class="pp-countdown-label-wrapper">
					<<?php echo $settings->label_tag; ?> class="pp-countdown-label">
						<?php echo $str2; ?>
					</<?php echo $settings->label_tag; ?>>
				</div>
			<?php } ?>
		</div>
		<?php

		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	public function render_normal_above_countdown( $str1, $str2, $str3 ) {
		$settings = (object) $this->get_settings();
		ob_start();

		?>
		<div class="pp-countdown-item">
			<div class="pp-countdown-digit-wrapper">
				<?php if( 'yes' == $settings->show_labels ) { ?>
					<div class="pp-countdown-label-wrapper">
						<<?php echo $settings->label_tag; ?> class="pp-countdown-label">
							<?php echo $str2; ?>
						</<?php echo $settings->label_tag; ?>>
					</div>
				<?php } ?>
				<<?php echo $settings->digit_tag; ?> class="pp-countdown-digit">
					<?php echo $str1; ?>
				</<?php echo $settings->digit_tag; ?>>
			</div>
			<?php echo $str3; ?>
		</div>
		<?php

		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	public function render_inside_below_countdown( $str1, $str2, $str3 ) {
		$settings = (object) $this->get_settings();
		ob_start();

		?>
		<div class="pp-countdown-item">
			<div class="pp-countdown-digit-wrapper">
				<div class="pp-countdown-digit-content">
					<<?php echo $settings->digit_tag; ?> class="pp-countdown-digit">
						<?php echo $str1; ?>
					</<?php echo $settings->digit_tag; ?>>
				</div>
				<?php if( 'yes' == $settings->show_labels ) { ?>
				<div class="pp-countdown-label-wrapper">
					<<?php echo $settings->label_tag; ?> class="pp-countdown-label">
						<?php echo $str2; ?>
					</<?php echo $settings->label_tag; ?>>
				</div>
				<?php } ?>
			</div>
			<?php echo $str3; ?>
		</div>
		<?php

		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	public function render_inside_above_countdown( $str1, $str2, $str3 ) {
		$settings = (object) $this->get_settings();
		ob_start();

		?>
		<div class="pp-countdown-item">
			<div class="pp-countdown-digit-wrapper">
				<?php if( 'yes' == $settings->show_labels ) { ?>
				<div class="pp-countdown-label-wrapper">
					<<?php echo $settings->label_tag; ?> class="pp-countdown-label">
						<?php echo $str2; ?>
					</<?php echo $settings->label_tag; ?>>
				</div>
				<?php } ?>
				<<?php echo $settings->digit_tag; ?> class="pp-countdown-digit">
					<?php echo $str1; ?>
				</<?php echo $settings->digit_tag; ?>>
			</div>
			<?php echo $str3; ?>
		</div>
		<?php

		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	public function render_outside_countdown( $str1, $str2, $str3 ) {
		$settings = (object) $this->get_settings();
		ob_start();

		?>
		<div class="pp-countdown-item">
			<?php if( 'yes' == $settings->show_labels ) { ?>
			<div class="pp-countdown-label-wrapper">
				<<?php echo $settings->label_tag; ?> class="pp-countdown-label">
					<?php echo $str2; ?>
				</<?php echo $settings->label_tag; ?>>
			</div>
			<?php } ?>
			<div class="pp-countdown-digit-wrapper">
				<<?php echo $settings->digit_tag; ?> class="pp-countdown-digit">
					<?php echo $str1; ?>
				</<?php echo $settings->digit_tag; ?>>
			</div>
			<?php echo $str3; ?>
		</div>
		<?php

		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	protected function render() {
		$instance  = $this->get_settings();
		$class = array(
			'pp-countdown-wrapper',
		);

		if ( 'default' != $instance['block_style'] ) {
			$class[] = 'pp-countdown-label-' . $instance['label_position'];

			if ( 'inside' == $instance['label_position'] ) {
				$class[] = 'pp-countdown-label-pos-' . $instance['label_inside_position'];
			}
			if ( 'outside' == $instance['label_position'] ) {
				$class[] = 'pp-countdown-label-pos-' . $instance['label_outside_position'];
			}
		} else {
			$class[] = 'pp-countdown-label-pos-' . $instance['default_position'];
		}

		if( 'none' != $instance['separator_type'] ) {
			$class[] = 'pp-countdown-separator-' . $instance['separator_type'];
		}

		if( 'yes' == $instance['hide_separator'] ) {
			$class[] = 'pp-countdown-separator-hide-mobile';
		}

		$class[] = 'pp-countdown-align-' . $instance['counter_alignment'];
		$class[] = 'pp-countdown-style-' . $instance['block_style'];

		$this->add_render_attribute( 'pp-countdown', 'class', $class );
		?>
		<div <?php echo $this->get_render_attribute_string( 'pp-countdown' ); ?>></div>
		<textarea name="pp-countdown-settings" style="display: none;"><?php echo esc_attr(json_encode($this->_get_settings())); ?></textarea>
		<?php
	}
}