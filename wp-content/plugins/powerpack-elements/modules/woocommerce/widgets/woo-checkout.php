<?php
/**
 * PowerPack WooCommerce Checkout widget.
 *
 * @package PowerPack
 */

namespace PowerpackElements\Modules\Woocommerce\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Config;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Woo_Categories.
 */
class Woo_Checkout extends Powerpack_Widget {

	/**
	 * Retrieve toggle widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Woo_Checkout' );
	}

	/**
	 * Retrieve toggle widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Woo_Checkout' );
	}

    /**
	 * Retrieve the list of categories the toggle widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Woo_Checkout' );
    }

	/**
	 * Retrieve toggle widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Woo_Checkout' );
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
		return parent::get_widget_keywords( 'Woo_Checkout' );
	}
    
    /**
	 * Retrieve the list of scripts the toggle widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_script_depends() {
        return [
            'pp-woocommerce',
            'powerpack-frontend'
        ];
    }

    /**
	 * Retrieve the list of styles the Woo - Categories depended on.
	 *
	 * Used to set style dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_style_depends() {
        return [
            'pp-woocommerce'
        ];
    }

	/**
	 * Register controls.
	 *
	 * @since 1.3.3
	 * @access protected
	 */
	protected function _register_controls() {

		/* Product Control */
		$this->register_content_general_controls();

		/* Help Docs */
		$this->register_content_help_docs();

		/* Style: Sections */
		$this->register_style_controls_sections();

		/* Style: Sections */
		$this->register_style_controls_columns();

		/* Style: Inputs */
		$this->register_style_controls_inputs();

		/* Style: Coupon Bar */
		$this->register_style_controls_coupon_bar();

		/* Style: Headings */
		$this->register_style_controls_headings();

		/* Style: Billing Details */
		$this->register_style_controls_billing_details();
	}

	/**
	 * Register toggle widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_content_general_controls() {

		$this->start_controls_section(
			'section_layout',
			[
				'label'                 => __( 'Layout', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_CONTENT,
			]
		);
        
        $this->add_control(
            'layout',
            [
                'label'                 => __( 'Layout', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => '1',
                'options'               => [
                    '1'		=> __( 'One Column', 'powerpack' ),
                    '2'		=> __( 'Two Columns', 'powerpack' ),
                ],
            ]
        );
        
        $this->add_control(
            'columns_stack',
            [
                'label'                 => __( 'Stack On', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'tablet',
                'options'               => [
                    'tablet' 	=> __( 'Tablet', 'powerpack' ),
                    'mobile' 	=> __( 'Mobile', 'powerpack' ),
                ],
                'prefix_class'          => 'pp-woo-cols-stack-',
                'frontend_available'    => true,
                'condition'             => [
					'layout' => '2',
				],
            ]
        );

		$this->add_responsive_control(
			'column_1_width',
			[
				'label'                 => __( 'First Column Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ '%' ],
                'devices'               => [ 'desktop', 'tablet' ],
				'default'               => [
					'size' => 50,
				],
				'range'                => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'separator'             => 'before',
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout-col-2 .woocommerce .col2-set' => 'width: calc( {{SIZE}}% - ({{column_gap.size}}px / 2) );',
					'{{WRAPPER}} .pp-woo-checkout-col-2 #order_review_heading, {{WRAPPER}} .pp-woo-checkout-col-2 .woocommerce-checkout-review-order' => 'width: calc( ( 100% - {{SIZE}}% ) - ({{column_gap.size}}px / 2) );',
				],
                'condition'             => [
                    'layout' => '2',
                ],
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'                 => __( 'Columns Gap', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
                'devices'               => [ 'desktop', 'tablet' ],
				'size_units'            => [ 'px' ],
				'default'               => [
					'size' => 30,
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout-col-2 .woocommerce .col2-set' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'layout' => '2',
                ],
			]
		);
        
        $this->add_control(
            'hide_additional_info',
            [
                'label'                 => __( 'Hide Additonal Information Box', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
            ]
        );
        
		$this->end_controls_section();
	}

	protected function register_content_help_docs() {

		$help_docs = PP_Config::get_widget_help_links('Woo_Checkout');

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

	/**
	 * Style Tab: Section
	 * -------------------------------------------------
	 */
	protected function register_style_controls_columns() {
		
		$this->start_controls_section(
			'section_columns_style',
			[
				'label'                 => __( 'Columns', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'column_1_style_heading',
			[
				'label'                 => __( 'Column 1', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'column_1_background',
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '.pp-woo-checkout.pp-woo-checkout-col-1 .woocommerce-checkout, .pp-woo-checkout.pp-woo-checkout-col-2 #customer_details',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'column_1_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '.pp-woo-checkout.pp-woo-checkout-col-1 .woocommerce-checkout, .pp-woo-checkout.pp-woo-checkout-col-2 #customer_details',
			]
		);

		$this->add_responsive_control(
			'column_1_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'.pp-woo-checkout.pp-woo-checkout-col-1 .woocommerce-checkout, .pp-woo-checkout.pp-woo-checkout-col-2 #customer_details' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'column_1_box_shadow',
				'selector' 				=> '.pp-woo-checkout.pp-woo-checkout-col-1 .woocommerce-checkout, .pp-woo-checkout.pp-woo-checkout-col-2 #customer_details',
			]
		);

		$this->add_responsive_control(
			'column_1_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'.pp-woo-checkout.pp-woo-checkout-col-1 .woocommerce-checkout, .pp-woo-checkout.pp-woo-checkout-col-2 #customer_details' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'column_2_style_heading',
			[
				'label'                 => __( 'Column 2', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
                'condition'             => [
					'layout' => '2',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'column_2_background',
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '.pp-woo-checkout.pp-woo-checkout-col-2 .woocommerce-checkout #order_review',
                'condition'             => [
					'layout' => '2',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'column_2_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '.pp-woo-checkout.pp-woo-checkout-col-2 .woocommerce-checkout #order_review',
                'condition'             => [
					'layout' => '2',
				],
			]
		);

		$this->add_responsive_control(
			'column_2_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'.pp-woo-checkout.pp-woo-checkout-col-2 .woocommerce-checkout #order_review' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
					'layout' => '2',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'column_2_box_shadow',
				'selector' 				=> '.pp-woo-checkout.pp-woo-checkout-col-2 .woocommerce-checkout #order_review',
                'condition'             => [
					'layout' => '2',
				],
			]
		);

		$this->add_responsive_control(
			'column_2_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'.pp-woo-checkout.pp-woo-checkout-col-2 .woocommerce-checkout #order_review' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
					'layout' => '2',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Section
	 * -------------------------------------------------
	 */
	protected function register_style_controls_sections() {
		
		$this->start_controls_section(
			'section_sections_style',
			[
				'label'                 => __( 'Sections', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'sections_background',
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper, {{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper, {{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table, {{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-payment',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'sections_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'separator'             => 'before',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper, {{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper, {{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table, {{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-payment',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'sections_box_shadow',
				'separator'				=> 'before',
				'selector' 				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper, {{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper, {{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table, {{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-payment',
			]
		);

		$this->add_responsive_control(
			'sections_gap',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 35,
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'separator'             => 'before',
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout-col-2 .woocommerce .col2-set .col-1, {{WRAPPER}} .pp-woo-checkout-col-2 .woocommerce-checkout-review-order-table' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'sections_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper, {{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper, {{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table, {{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-payment' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
		$this->end_controls_section();
	}

	/**
	 * Style Tab: Inputs
	 * -------------------------------------------------
	 */
	protected function register_style_controls_inputs() {
		
		$this->start_controls_section(
			'section_inputs_style',
			[
				'label'                 => __( 'Inputs', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);
        
		$this->add_control(
			'inputs_text_align',
			[
				'label'                 => __( 'Text Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'  => false,
				'options'      => [
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
				'default'               => 'left',
				'selectors'             => [
					'{{WRAPPER}} .woocommerce form .input-text, {{WRAPPER}} .woocommerce form  select' => 'text-align: {{VALUE}};',
				],
				'separator'             => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                 => 'inputs_typography',
				'label'                => __( 'Typography', 'powerpack' ),
				'selector'             => '{{WRAPPER}} .woocommerce form .input-text, {{WRAPPER}} .woocommerce form  select',
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce form .input-text, {{WRAPPER}} .woocommerce form  select' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_background_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .woocommerce form .input-text, {{WRAPPER}} .woocommerce form  select' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'inputs_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'separator'             => 'before',
				'selector'              => '{{WRAPPER}} .woocommerce form .input-text, {{WRAPPER}} .woocommerce form  select',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'inputs_box_shadow',
				'separator'				=> 'before',
				'selector'				=> '{{WRAPPER}} .woocommerce form .input-text, {{WRAPPER}} .woocommerce form  select',
			]
		);

		$this->add_responsive_control(
			'inputs_gap',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'separator'             => 'before',
				'selectors'             => [
					'{{WRAPPER}} .woocommerce form .input-text, {{WRAPPER}} .woocommerce form  select' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'inputs_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce form .input-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'inputs_height',
			[
				'label'                 => __( 'Input Height', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 35,
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'separator'             => 'before',
				'selectors'             => [
					'{{WRAPPER}} .woocommerce .form-row input.input-text, {{WRAPPER}} .woocommerce .form-row select' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'textarea_height',
			[
				'label'                 => __( 'Textarea Height', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .woocommerce form .form-row textarea' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
		$this->end_controls_section();
	}

	/**
	 * Style Tab: Coupon Bar
	 * -------------------------------------------------
	 */
	protected function register_style_controls_coupon_bar() {
		$this->start_controls_section(
			'section_coupon_bar_style',
			[
				'label'                 => __( 'Coupon Bar', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'coupon_bar_toggle_heading',
			[
				'label'                 => __( 'Coupon Toggle', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'coupon_bar_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon-toggle .woocommerce-info' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'coupon_bar_icon_color',
			[
				'label'                 => __( 'Icon Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon-toggle .woocommerce-info:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'coupon_bar_links_color',
			[
				'label'                 => __( 'Links Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon-toggle .woocommerce-info a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'coupon_bar_links_color_hover',
			[
				'label'                 => __( 'Links Hover Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon-toggle .woocommerce-info a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'coupon_bar_toggle_typography',
				'label'					=> __( 'Typography', 'powerpack' ),
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon-toggle .woocommerce-info',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'coupon_bar_background',
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon-toggle .woocommerce-info',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'coupon_bar_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon-toggle .woocommerce-info',
			]
		);

		$this->add_responsive_control(
			'coupon_bar_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon-toggle .woocommerce-info' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'coupon_bar_box_shadow',
				'separator'				=> 'before',
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon-toggle .woocommerce-info',
			]
		);

		$this->add_control(
			'coupon_bar_form_heading',
			[
				'label'                 => __( 'Coupon Form', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_control(
			'coupon_form_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'coupon_form_typography',
				'label'					=> __( 'Typography', 'powerpack' ),
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'coupon_form_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon',
			]
		);

		$this->add_responsive_control(
			'coupon_form_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'coupon_form_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon',
			]
		);

		$this->add_responsive_control(
			'coupon_form_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'coupon_bar_form_input_heading',
			[
				'label'                 => __( 'Coupon Form Input', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_control(
			'coupon_bar_form_input_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon #coupon_code' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'coupon_bar_form_input_background_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon #coupon_code' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'coupon_bar_form_input_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon #coupon_code',
			]
		);

		$this->add_responsive_control(
			'coupon_bar_form_input_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon #coupon_code' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'coupon_bar_form_input_typography',
				'label'					=> __( 'Typography', 'powerpack' ),
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon #coupon_code',
			]
		);

		$this->add_responsive_control(
			'coupon_bar_form_input_height',
			[
				'label'                 => __( 'Input Height', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon #coupon_code' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_bar_form_input_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon #coupon_code' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'coupon_bar_form_button_heading',
			[
				'label'                 => __( 'Coupon Form Button', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'coupon_bar_form_button_typography',
				'label'					=> __( 'Typography', 'powerpack' ),
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon .button',
			]
		);

        $this->start_controls_tabs( 'tabs_coupon_form_button_style' );

        $this->start_controls_tab(
            'tab_coupon_form_button_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
            ]
        );

        $this->add_control(
            'coupon_form_button_text_color_normal',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon .button' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'coupon_form_button_bg_color_normal',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon .button' => 'background-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'coupon_form_button_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon .button',
			]
		);

		$this->add_responsive_control(
			'coupon_form_button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'coupon_form_button_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'coupon_form_button_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon .button',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_coupon_form_button_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
            ]
        );

        $this->add_control(
            'coupon_form_button_text_color_hover',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon .button:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'coupon_form_button_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon .button:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'coupon_form_button_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon .button:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'coupon_form_button_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-form-coupon .button:hover',
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();
        
		$this->end_controls_section();
	}

	/**
	 * Style Tab: Headings
	 * -------------------------------------------------
	 */
	protected function register_style_controls_headings() {
		$this->start_controls_section(
			'section_headings_style',
			[
				'label'                 => __( 'Headings', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'headings_text_color',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout #customer_details .woocommerce-billing-fields > h3, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields > h3, {{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields > h3, {{WRAPPER}} .pp-woo-checkout #order_review_heading' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'headings_typography',
				'label'					=> __( 'Typography', 'powerpack' ),
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout #customer_details .woocommerce-billing-fields > h3, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields > h3, {{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields > h3, {{WRAPPER}} .pp-woo-checkout #order_review_heading',
			]
		);

		$this->add_responsive_control(
			'headings_spacing',
			[
				'label'					=> __( 'Spacing', 'powerpack' ),
				'type'					=> Controls_Manager::SLIDER,
				'default'				=> [
					'size' => '',
				],
				'range'					=> [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'				=> [
					'{{WRAPPER}} .pp-woo-checkout #customer_details .woocommerce-billing-fields > h3, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields > h3, {{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields > h3, {{WRAPPER}} .pp-woo-checkout #order_review_heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
		$this->end_controls_section();
	}

	/**
	 * Style Tab: Billing Details
	 * -------------------------------------------------
	 */
	protected function register_style_controls_billing_details() {
		$this->start_controls_section(
			'section_billing_details_style',
			[
				'label'                 => __( 'Billing Details', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'section_billing_details_heading',
			[
				'label'                 => __( 'Section', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'section_billing_details_background',
				'types'                 => [ 'classic', 'gradient' ],
				'separator'				=> 'before',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'section_billing_details_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper',
			]
		);

		$this->add_responsive_control(
			'section_billing_details_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'section_billing_details_box_shadow',
				'separator'				=> 'before',
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper',
			]
		);

		$this->add_control(
			'section_billing_details_inputs_heading',
			[
				'label'                 => __( 'Inputs', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'section_billing_details_inputs_typography',
				'label'					=> __( 'Typography', 'powerpack' ),
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper input.input-text, {{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper select, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper input.input-text, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper select',
			]
		);

		$this->add_responsive_control(
			'section_billing_details_inputs_height',
			[
				'label'                 => __( 'Input Height', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 35,
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper input.input-text, {{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper select, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper input.input-text, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper select' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_billing_details_inputs_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper input.input-text, {{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper select, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper input.input-text, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_billing_details_inputs_style' );

		$this->start_controls_tab(
			'tab_billing_details_inputs_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'section_billing_details_inputs_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper input.input-text, {{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper select, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper input.input-text, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper select' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_billing_details_inputs_background_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper input.input-text, {{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper select, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper input.input-text, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper select' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'section_billing_details_inputs_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper input.input-text, {{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper select, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper input.input-text, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper select',
			]
		);

		$this->add_responsive_control(
			'section_billing_details_inputs_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper input.input-text, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper input.input-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'section_billing_details_inputs_box_shadow',
				'separator'				=> 'before',
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper input.input-text, {{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper select, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper input.input-text, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper select',
			]
		);
		
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_billing_details_inputs_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'section_billing_details_inputs_text_color_hover',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper input.input-text:hover, {{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper select:hover, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper input.input-text:hover, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper select:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_billing_details_inputs_background_color_hover',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper input.input-text:hover, {{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper select:hover, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper input.input-text:hover, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper select:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_billing_details_inputs_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper input.input-text:hover, {{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper select:hover, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper input.input-text:hover, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper select:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'section_billing_details_inputs_box_shadow_hover',
				'separator'				=> 'before',
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper input.input-text:hover, {{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper select:hover, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper input.input-text:hover, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper select:hover',
			]
		);
		
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_billing_details_inputs_focus',
			[
				'label'                 => __( 'Focus', 'powerpack' ),
			]
		);

		$this->add_control(
			'section_billing_details_inputs_text_color_focus',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper input.input-text:focus, {{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper select:focus, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper input.input-text:focus, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper select:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_billing_details_inputs_background_color_focus',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper input.input-text:focus, {{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper select:focus, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper input.input-text:focus, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper select:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_billing_details_inputs_border_color_focus',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper input.input-text:focus, {{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper select:focus, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper input.input-text:focus, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper select:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'section_billing_details_inputs_box_shadow_focus',
				'separator'				=> 'before',
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper input.input-text:focus, {{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper select:focus, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper input.input-text:focus, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper select:focus',
			]
		);
		
		$this->end_controls_tab();
		
		$this->end_controls_tabs();

		$this->add_control(
			'section_billing_details_inputs_label_heading',
			[
				'label'                 => __( 'Input Label', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_control(
			'section_billing_details_inputs_label_color',
			[
				'label'					=> __( 'Color', 'powerpack' ),
				'type'					=> Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper label, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'section_billing_details_inputs_label_typography',
				'label'					=> __( 'Typography', 'powerpack' ),
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper label, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper label',
			]
		);

		$this->add_responsive_control(
			'section_billing_details_inputs_label_spacing',
			[
				'label'					=> __( 'Spacing', 'powerpack' ),
				'type'					=> Controls_Manager::SLIDER,
				'default'				=> [
					'size' => 5,
				],
				'range'					=> [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'				=> [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-billing-fields__field-wrapper label, {{WRAPPER}} .pp-woo-checkout .woocommerce-shipping-fields__field-wrapper label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
		$this->end_controls_section();
		
		/**
         * Style Tab: Additional Information
         * -------------------------------------------------
         */
		$this->start_controls_section(
			'section_additional_fields_style',
			[
				'label'                 => __( 'Additional Information', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'section_additional_fields_heading',
			[
				'label'                 => __( 'Section', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'section_additional_fields_background',
				'types'                 => [ 'classic', 'gradient' ],
				'separator'				=> 'before',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'section_additional_fields_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper',
			]
		);

		$this->add_responsive_control(
			'section_additional_fields_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'section_additional_fields_box_shadow',
				'separator'				=> 'before',
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper',
			]
		);

		$this->add_control(
			'section_additional_fields_textarea_heading',
			[
				'label'                 => __( 'Textarea', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'section_additional_fields_textarea_typography',
				'label'					=> __( 'Typography', 'powerpack' ),
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper textarea',
			]
		);

		$this->start_controls_tabs( 'tabs_additional_fields_textarea_style' );

		$this->start_controls_tab(
			'tab_additional_fields_textarea_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'section_additional_fields_textarea_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper textarea' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_additional_fields_textarea_background_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper textarea' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'section_additional_fields_textarea_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper textarea',
			]
		);

		$this->add_responsive_control(
			'section_additional_fields_textarea_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'section_additional_fields_textarea_box_shadow',
				'separator'				=> 'before',
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper textarea',
			]
		);
		
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_additional_fields_textarea_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'section_additional_fields_textarea_text_color_hover',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper textarea:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_additional_fields_textarea_background_color_hover',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper textarea:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_additional_fields_textarea_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper textarea:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'section_additional_fields_textarea_box_shadow_hover',
				'separator'				=> 'before',
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper textarea:hover',
			]
		);
		
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_additional_fields_textarea_focus',
			[
				'label'                 => __( 'Focus', 'powerpack' ),
			]
		);

		$this->add_control(
			'section_additional_fields_textarea_text_color_focus',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper textarea:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_additional_fields_textarea_background_color_focus',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper textarea:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_additional_fields_textarea_border_color_focus',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper textarea:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'section_additional_fields_textarea_box_shadow_focus',
				'separator'				=> 'before',
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper textarea:focus',
			]
		);
		
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'section_additional_fields_textarea_label_heading',
			[
				'label'                 => __( 'Textarea Label', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_control(
			'section_additional_fields_textarea_label_color',
			[
				'label'					=> __( 'Color', 'powerpack' ),
				'type'					=> Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'section_additional_fields_textarea_label_typography',
				'label'					=> __( 'Typography', 'powerpack' ),
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper label',
			]
		);

		$this->add_responsive_control(
			'section_additional_fields_textarea_label_spacing',
			[
				'label'					=> __( 'Spacing', 'powerpack' ),
				'type'					=> Controls_Manager::SLIDER,
				'default'				=> [
					'size' => 5,
				],
				'range'					=> [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'				=> [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-additional-fields__field-wrapper label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
		$this->end_controls_section();
		
		/**
         * Style Tab: Review Order
         * -------------------------------------------------
         */
		$this->start_controls_section(
			'section_review_order_style',
			[
				'label'                 => __( 'Review Order', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'section_review_order_table_heading',
			[
				'label'                 => __( 'Table', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                 => 'section_review_order_typography',
				'label'                => __( 'Typography', 'powerpack' ),
				'selector'             => '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'section_review_order_background',
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'section_review_order_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-woocommerce.pp-woo-checkout .woocommerce-checkout #order_review .shop_table, {{WRAPPER}} .pp-woocommerce.pp-woo-checkout .woocommerce-checkout #order_review .shop_table th, {{WRAPPER}} .pp-woocommerce.pp-woo-checkout .woocommerce-checkout #order_review .shop_table td',
			]
		);

		$this->add_responsive_control(
			'section_review_order_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'section_review_order_box_shadow',
				'separator'				=> 'before',
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table',
			]
		);

		$this->add_responsive_control(
			'section_review_order_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'section_review_order_table_cell_heading',
			[
				'label'                 => __( 'Table Cell', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_responsive_control(
			'section_review_order_cell_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woocommerce.pp-woo-checkout .woocommerce-checkout #order_review .shop_table th, {{WRAPPER}} .pp-woocommerce.pp-woo-checkout .woocommerce-checkout #order_review .shop_table td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'section_review_order_table_head_heading',
			[
				'label'                 => __( 'Table Head', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_control(
			'section_review_order_table_head_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table thead th' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_review_order_table_head_background_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table thead th' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_review_order_table_foot_heading',
			[
				'label'                 => __( 'Table Footer', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_control(
			'section_review_order_table_foot_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table tfoot tr' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_review_order_table_foot_background_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table tfoot tr' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_review_order_table_body_heading',
			[
				'label'                 => __( 'Table Body', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->start_controls_tabs( 'section_review_order_tbody_rows_tabs_style' );

		$this->start_controls_tab(
			'tab_section_review_order_even_row',
			[
				'label'                 => __( 'Even Row', 'powerpack' ),
			]
		);

		$this->add_control(
			'section_review_order_even_row_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table .cart_item:nth-child(2n)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_review_order_even_row_background_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table .cart_item:nth-child(2n)' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_section_review_order_odd_row',
			[
				'label'                 => __( 'Odd Row', 'powerpack' ),
			]
		);

		$this->add_control(
			'section_review_order_odd_row_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table .cart_item:nth-child(2n+1)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_review_order_odd_row_background_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout-review-order-table .cart_item:nth-child(2n+1)' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'section_review_order_row_separator_heading',
			[
				'label'                 => __( 'Row Separator', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);
        
        $this->add_control(
            'section_review_order_row_separator_type',
            [
                'label'                 => __( 'Separator Type', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'solid',
                'options'               => [
                    'none'		=> __( 'None', 'powerpack' ),
                    'solid'		=> __( 'Solid', 'powerpack' ),
                    'dotted'	=> __( 'Dotted', 'powerpack' ),
                    'dashed'	=> __( 'Dashed', 'powerpack' ),
                    'double'	=> __( 'Double', 'powerpack' ),
                ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce table.shop_table td, {{WRAPPER}} .pp-woo-checkout .woocommerce table.shop_table tfoot th' => 'border-top-style: {{VALUE}};',
				],
            ]
        );

		$this->add_control(
			'section_review_order_row_separator_color',
			[
				'label'                 => __( 'Separator Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce table.shop_table td, {{WRAPPER}} .pp-woo-checkout .woocommerce table.shop_table tfoot th' => 'border-top-color: {{VALUE}};',
				],
				'condition'             => [
					'section_review_order_row_separator_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'section_review_order_row_separator_size',
			[
				'label'                 => __( 'Separator Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce table.shop_table td, {{WRAPPER}} .pp-woo-checkout .woocommerce table.shop_table tfoot th' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'section_review_order_row_separator_type!' => 'none',
				],
			]
		);
        
		$this->end_controls_section();
		
		/**
         * Style Tab: Payment Method
         * -------------------------------------------------
         */
		$this->start_controls_section(
			'section_payment_method_style',
			[
				'label'                 => __( 'Payment Method', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'section_payment_method_heading',
			[
				'label'                 => __( 'Section', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'section_payment_method_background',
				'types'                 => [ 'classic', 'gradient' ],
				'separator'				=> 'before',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #payment',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'section_payment_method_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #payment',
			]
		);

		$this->add_responsive_control(
			'section_payment_method_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #payment' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'section_payment_method_box_shadow',
				'separator'				=> 'before',
				'selector'				=> '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #payment',
			]
		);

		$this->add_control(
			'section_payment_method_label_heading',
			[
				'label'                 => __( 'Label', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                 => 'payment_method_label_typography',
				'label'                => __( 'Typography', 'powerpack' ),
				'selector'             => '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout .payment_methods label',
			]
		);

		$this->add_control(
			'payment_method_label_text_color',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout .payment_methods label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'section_payment_method_message_heading',
			[
				'label'                 => __( 'Message', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                 => 'payment_method_message_typography',
				'label'                => __( 'Typography', 'powerpack' ),
				'selector'             => '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout .payment_box',
			]
		);

		$this->add_control(
			'payment_method_message_text_color',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout .payment_box' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'payment_method_message_background_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout .payment_box' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout .payment_box:before' => 'border-bottom-color: {{VALUE}};',
				],
			]
		);
		
		$this->end_controls_section();

        /**
         * Style Tab: Privacy Policy
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_privacy_policy_style',
            [
                'label'                 => __( 'Privacy Policy', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'privacy_policy_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-checkout .woocommerce-terms-and-conditions-wrapper .woocommerce-privacy-policy-text' => 'color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'privacy_policy_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-terms-and-conditions-wrapper .woocommerce-privacy-policy-text',
            ]
        );
		
		$this->end_controls_section();

        /**
         * Style Tab: Button
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_checkout_button_style',
            [
                'label'                 => __( 'Button', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'button_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #place_order',
            ]
        );

		$this->add_control(
			'button_width',
			[
				'label'                 => __( 'Width', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'auto',
				'options'               => [
					'auto'		=> __( 'Auto', 'powerpack' ),
					'full'		=> __( 'Full Width', 'powerpack' ),
					'custom'	=> __( 'Custom', 'powerpack' ),
				],
                'prefix_class'          => 'pp-woo-checkout-button-',
			]
		);

		$this->add_responsive_control(
			'button_custom_width',
			[
				'label'                 => __( 'Custom Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px' ],
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #place_order' => 'width: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'button_width'	=> 'custom',
                ],
			]
		);
        
        $this->add_responsive_control(
			'button_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
                'allowed_dimensions'    => 'vertical',
				'placeholder'           => [
					'top'      => '',
					'right'    => 'auto',
					'bottom'   => '',
					'left'     => 'auto',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #place_order' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #place_order' => 'background-color: {{VALUE}}',
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
                    '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #place_order' => 'color: {{VALUE}}',
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
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #place_order',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #place_order' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #place_order' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #place_order',
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
                    '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #place_order:hover' => 'background-color: {{VALUE}}',
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
                    '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #place_order:hover' => 'color: {{VALUE}}',
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
                    '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #place_order:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-woo-checkout .woocommerce-checkout #place_order:hover',
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->end_controls_section();
	}

	private function get_shortcode() {

		$shortcode = sprintf( '[%s %s]', 'woocommerce_checkout', $this->get_render_attribute_string( 'shortcode' ) );

		return $shortcode;
	}

	/**
	 * Render output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings();
        
        $this->add_render_attribute( 'container', 'class', [
            'pp-woocommerce',
            'pp-woo-checkout',
            'pp-woo-checkout-col-' . $settings['layout'],
			'clearfix'
        ] );
		
		if ( $settings['hide_additional_info'] == 'yes' ) {
			add_filter( 'woocommerce_enable_order_notes_field', '__return_false', 9999 );
		}
        ?>
        <div <?php echo $this->get_render_attribute_string( 'container' ); ?>>
			<?php do_action( 'pp_woo_before_checkout_wrap' ); ?>

			<div class="woopack-product-checkout">
				<?php do_action( 'pp_woo_before_checkout_content' ); ?>
				<?php echo do_shortcode('[woocommerce_checkout]'); ?>
				<?php do_action( 'pp_woo_after_checkout_content' ); ?>
			</div>

			<?php do_action( 'pp_woo_after_checkout_wrap' ); ?>
        </div>
		<?php
	}

	public function render_plain_content() {
		echo $this->get_shortcode();
	}
}
