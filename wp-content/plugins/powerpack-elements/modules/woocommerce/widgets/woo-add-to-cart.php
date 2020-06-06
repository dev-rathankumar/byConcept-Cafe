<?php
/**
 * PowerPack WooCommerce Add To Cart Button.
 *
 * @package PowerPack
 */

namespace PowerpackElements\Modules\Woocommerce\Widgets;

use PowerpackElements\Base\Powerpack_Widget;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Woo_Add_To_Cart.
 */
class Woo_Add_To_Cart extends Powerpack_Widget {

	/**
	 * Retrieve Widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Woo_Add_To_Cart' );
	}

	/**
	 * Retrieve Widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Woo_Add_To_Cart' );
	}

    /**
	 * Retrieve the list of categories the Woo Add to Cart widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Woo_Add_To_Cart' );
    }

	/**
	 * Retrieve Widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Woo_Add_To_Cart' );
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
		return parent::get_widget_keywords( 'Woo_Add_To_Cart' );
	}

	/**
	 * Get Script Depends.
	 *
	 * @access public
	 *
	 * @return array scripts.
	 */
	public function get_script_depends() {
		return [ 'pp-woocommerce' ];
	}

    /**
	 * Retrieve the list of styles the Add to Cart widget depended on.
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
	 * @access protected
	 */
	protected function _register_controls() {

		/* Product Control */
		$this->register_content_product_controls();
		/* Button Control */
		$this->register_content_button_controls();
		/* Button Style */
		$this->register_style_button_controls();
	}

	public function unescape_html( $safe_text, $text ) {
		return $text;
	}

	/**
	 * Register Content Product Controls.
	 *
	 * @access protected
	 */
	protected function register_content_product_controls() {

		$this->start_controls_section(
			'section_product_field',
			[
				'label' => __( 'Product', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
			$this->add_control(
				'product_id',
				[
					'label'     => __( 'Select Product', 'powerpack' ),
					'type'      => 'pp-query-posts',
					'post_type' => 'product',
				]
			);

		$this->add_control(
			'show_quantity',
			[
				'label' => __( 'Show Quantity', 'powerpack' ),
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __( 'Hide', 'powerpack' ),
				'label_on' => __( 'Show', 'powerpack' ),
			]
		);

        $this->add_control(
            'quantity',
            [
                'label'   => __( 'Quantity', 'powerpack' ),
                'type'    => Controls_Manager::NUMBER,
                'default' => 1,
                'condition' => [
                    'show_quantity' => '',
                ],
            ]
        );

        $this->add_control(
            'enable_redirect',
            [
                'label'        => __( 'Auto Redirect', 'powerpack' ),
                'type'         => Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => '',
                'description'  => __( 'Enable this option to redirect cart page after the product gets added to cart', 'powerpack' ),
                'condition' => [
                    'show_quantity' => '',
                ],
            ]
        );

		$this->end_controls_section();
	}

	/**
	 * Register Content Button Controls.
	 *
	 * @access protected
	 */
	protected function register_content_button_controls() {
		$this->start_controls_section(
			'section_button_field',
			[
				'label' => __( 'Button', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
			$this->add_control(
				'btn_text',
				[
					'label'   => __( 'Text', 'powerpack' ),
					'type'    => Controls_Manager::TEXT,
					'default' => __( 'Add to cart', 'powerpack' ),
					'dynamic' => [
						'active' => true,
					],
				]
			);
			$this->add_responsive_control(
				'align',
				[
					'label'        => __( 'Alignment', 'powerpack' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => [
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
						'justify' => [
							'title' => __( 'Justified', 'powerpack' ),
							'icon'  => 'fa fa-align-justify',
						],
					],
					'prefix_class' => 'pp-add-to-cart%s-align-',
					'default'      => 'left',
				]
			);
			$this->add_control(
				'btn_size',
				[
					'label'   => __( 'Size', 'powerpack' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'sm',
					'options' => [
						'xs' => __( 'Extra Small', 'powerpack' ),
						'sm' => __( 'Small', 'powerpack' ),
						'md' => __( 'Medium', 'powerpack' ),
						'lg' => __( 'Large', 'powerpack' ),
						'xl' => __( 'Extra Large', 'powerpack' ),
					],
				]
			);
			$this->add_responsive_control(
				'btn_padding',
				[
					'label'      => __( 'Padding', 'powerpack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', 'em', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_control(
				'select_btn_icon',
				[
					'label'					=> __( 'Icon', 'powerpack' ),
					'type'					=> Controls_Manager::ICONS,
					'fa4compatibility'		=> 'btn_icon',
					'default'				=> [
						'value'		=> 'fas fa-shopping-cart',
						'library'	=> 'fa-solid',
					],
				]
			);
			$this->add_control(
				'btn_icon_align',
				[
					'label'     => __( 'Icon Position', 'powerpack' ),
					'type'      => Controls_Manager::SELECT,
					'default'   => 'left',
					'options'   => [
						'left'  => __( 'Before', 'powerpack' ),
						'right' => __( 'After', 'powerpack' ),
					],
				]
			);
			$this->add_control(
				'btn_icon_indent',
				[
					'label'     => __( 'Icon Spacing', 'powerpack' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'max' => 50,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
				]
			);
		$this->end_controls_section();
	}

	/**
	 * Register Style Button Controls.
	 *
	 * @access protected
	 */
	protected function register_style_button_controls() {

		$this->start_controls_section(
			'section_design_button',
			[
				'label' => __( 'Button', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .pp-button',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_4,
			]
		);

		$this->start_controls_tabs( 'button_tabs_style' );

			$this->start_controls_tab(
				'button_normal',
				[
					'label' => __( 'Normal', 'powerpack' ),
				]
			);

				$this->add_control(
					'button_color',
					[
						'label'     => __( 'Text Color', 'powerpack' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .pp-button' => 'color: {{VALUE}};',
							'{{WRAPPER}} .pp-button svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'           => 'button_background_color',
						'label'          => __( 'Background Color', 'powerpack' ),
						'types'          => [ 'classic', 'gradient' ],
						'selector'       => '{{WRAPPER}} .pp-button',
						'fields_options' => [
							'color' => [
								'scheme' => [
									'type'  => Scheme_Color::get_type(),
									'value' => Scheme_Color::COLOR_4,
								],
							],
						],
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'        => 'button_border',
						'placeholder' => '',
						'default'     => '',
						'selector'    => '{{WRAPPER}} .pp-button',
					]
				);

				$this->add_control(
					'border_radius',
					[
						'label'      => __( 'Border Radius', 'powerpack' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .pp-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'button_box_shadow',
						'selector' => '{{WRAPPER}} .pp-button',
					]
				);

				$this->add_control(
					'view_cart_color',
					[
						'label'     => __( 'View Cart Text Color', 'powerpack' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .added_to_cart' => 'color: {{VALUE}};',
						],
                        'condition' => [
                            'show_quantity' => '',
                        ],
					]
				);
			$this->end_controls_tab();

			$this->start_controls_tab(
				'button_hover',
				[
					'label' => __( 'Hover', 'powerpack' ),
				]
			);

				$this->add_control(
					'button_hover_color',
					[
						'label'     => __( 'Text Color', 'powerpack' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .pp-button:focus, {{WRAPPER}} .pp-button:hover' => 'color: {{VALUE}};',
							'{{WRAPPER}} .pp-button:focus svg, {{WRAPPER}} .pp-button:hover svg' => 'fill: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'           => 'button_background_hover_color',
						'label'          => __( 'Background Color', 'powerpack' ),
						'types'          => [ 'classic', 'gradient' ],
						'selector'       => '{{WRAPPER}} .pp-button:focus, {{WRAPPER}} .pp-button:hover',
						'fields_options' => [
							'color' => [
								'scheme' => [
									'type'  => Scheme_Color::get_type(),
									'value' => Scheme_Color::COLOR_4,
								],
							],
						],
					]
				);

				$this->add_control(
					'button_border_hover_color',
					[
						'label'     => __( 'Border Hover Color', 'powerpack' ),
						'type'      => Controls_Manager::COLOR,
						'scheme'    => [
							'type'  => Scheme_Color::get_type(),
							'value' => Scheme_Color::COLOR_4,
						],
						'condition' => [
							'button_border_border!' => '',
						],
						'selectors' => [
							'{{WRAPPER}} .pp-button:focus, {{WRAPPER}} .pp-button:hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'hover_animation',
					[
						'label' => __( 'Hover Animation', 'powerpack' ),
						'type'  => Controls_Manager::HOVER_ANIMATION,
					]
				);

				$this->add_control(
					'view_cart_hover_color',
					[
						'label'     => __( 'View Cart Text Color', 'powerpack' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .added_to_cart:hover' => 'color: {{VALUE}};',
						],
                        'condition' => [
                            'show_quantity' => '',
                        ],
					]
				);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render Woo Product Grid output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();
		$node_id  = $this->get_id();
		$product  = false;

		if ( ! empty( $settings['product_id'] ) ) {
			$product_data = get_post( $settings['product_id'] );
		}

		$product = ! empty( $product_data ) && in_array( $product_data->post_type, [ 'product', 'product_variation' ] ) ? wc_setup_product_data( $product_data ) : false;

        if ( $product ) {
            if ( 'yes' === $settings['show_quantity'] ) {
                $this->render_form_button( $product );
            } else {
                $this->render_ajax_button( $product );
            }
		} elseif ( current_user_can( 'manage_options' ) ) {
            $class = implode(
				' ', array_filter(
					[
						'button',
						'pp-button',
				        'elementor-button',
				        'elementor-size-' . $settings['btn_size'],
						'elementor-animation-' . $settings['hover_animation'],
					]
				)
			);
			$this->add_render_attribute(
				'button', [ 'class' => $class ]
			);
            
            echo '<div class="pp-woo-add-to-cart">';
            echo '<a ' . $this->get_render_attribute_string( 'button' ) . '>';
			echo __( 'Please select the product', 'powerpack' );
            echo '</a>';
            echo '</div>';
        }
	}

	/**
	 * @param \WC_Product $product
	 */
	private function render_ajax_button( $product ) {
		$settings = $this->get_settings_for_display();
		$atc_html = '';
        
        if ( $product ) {

			$product_id   = $product->get_id();
			$product_type = $product->get_type();

			$class = [
				'pp-button',
				'elementor-button',
				'elementor-animation-' . $settings['hover_animation'],
				'elementor-size-' . $settings['btn_size'],
				'product_type_' . $product_type,
				$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
				$product->supports( 'ajax_add_to_cart' ) ? 'ajax_add_to_cart' : '',
			];

			if ( 'yes' === $settings['enable_redirect'] ) {
				$class[] = 'pp-redirect';
			}

			$this->add_render_attribute(
				'button', [
					'rel'             => 'nofollow',
					'href'            => $product->add_to_cart_url(),
					'data-quantity'   => ( isset( $settings['quantity'] ) ? $settings['quantity'] : 1 ),
					'data-product_id' => $product_id,
					'class'           => $class,
				]
			);

			$this->add_render_attribute(
				'icon-align',
				'class',
				[
					'pp-icon',
					'pp-atc-icon-align',
					'elementor-align-icon-' . $settings['btn_icon_align'],
				]
			);
		
			if ( ! isset( $settings['btn_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
				// add old default
				$settings['btn_icon'] = 'fa fa-shopping-cart';
			}

			$has_icon = ! empty( $settings['btn_icon'] );

			if ( $has_icon ) {
				$this->add_render_attribute( 'i', 'class', $settings['btn_icon'] );
				$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
			}

			if ( ! $has_icon && ! empty( $settings['select_btn_icon']['value'] ) ) {
				$has_icon = true;
			}
			$migrated = isset( $settings['__fa4_migrated']['select_btn_icon'] );
			$is_new = ! isset( $settings['btn_icon'] ) && Icons_Manager::is_migration_allowed();
			?>
			<div class="pp-woo-add-to-cart">
				<a <?php echo $this->get_render_attribute_string( 'button' ); ?>>
					<span class="pp-atc-content-wrapper">
						<?php if ( $settings['btn_icon_align'] == 'right' ) { ?>
							<span class="pp-atc-btn-text"><?php echo $settings['btn_text']; ?></span>
						<?php } ?>
						<?php
							if ( $has_icon ) {
								echo '<span ' . $this->get_render_attribute_string( 'icon-align' ) . '>';
								if ( $is_new || $migrated ) {
									Icons_Manager::render_icon( $settings['select_btn_icon'], [ 'aria-hidden' => 'true' ] );
								} elseif ( ! empty( $settings['btn_icon'] ) ) {
									?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
								}
								echo '</span>';
							}
						?>
						<?php if ( $settings['btn_icon_align'] == 'left' ) { ?>
							<span class="pp-atc-btn-text"><?php echo $settings['btn_text']; ?></span>
						<?php } ?>
					</span>
				</a>
			</div>
			<?php
		}
	}

	private function render_form_button( $product ) {
		$settings = $this->get_settings_for_display();
        
        echo '<div class="pp-woo-add-to-cart">';
		if ( ! $product && current_user_can( 'manage_options' ) ) {

			return;
		}

		$text_callback = function() {
			ob_start();
			$this->render_text();

			return ob_get_clean();
		};

		add_filter( 'woocommerce_get_stock_html', '__return_empty_string' );
		add_filter( 'woocommerce_product_single_add_to_cart_text', $text_callback );
		add_filter( 'esc_html', [ $this, 'unescape_html' ], 10, 2 );

		ob_start();
		woocommerce_template_single_add_to_cart();
		$form = ob_get_clean();
		$form = str_replace( 'single_add_to_cart_button', 'single_add_to_cart_button elementor-button elementor-size-' . $settings["btn_size"] . ' pp-button', $form );
		echo $form;

		remove_filter( 'woocommerce_product_single_add_to_cart_text', $text_callback );
		remove_filter( 'woocommerce_get_stock_html', '__return_empty_string' );
		remove_filter( 'esc_html', [ $this, 'unescape_html' ] );
        echo '</div>';
	}

	/**
	 * Render button text.
	 *
	 * Render button widget text.
	 *
	 * @access protected
	 */
	protected function render_text() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
			'content-wrapper' => [
				'class' => 'elementor-button-content-wrapper',
			],
			'icon-align' => [
				'class' => [
					'pp-icon',
					'elementor-button-icon',
					'elementor-align-icon-' . $settings['btn_icon_align'],
				],
			],
			'btn_text' => [
				'class' => 'elementor-button-text',
			],
		] );
		
		if ( ! isset( $settings['btn_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['btn_icon'] = 'fa fa-shopping-cart';
		}

		$has_icon = ! empty( $settings['btn_icon'] );

		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['btn_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}

		if ( ! $has_icon && ! empty( $settings['select_btn_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['select_btn_icon'] );
		$is_new = ! isset( $settings['btn_icon'] ) && Icons_Manager::is_migration_allowed();

		$this->add_inline_editing_attributes( 'btn_text', 'none' );
		?>
		<span <?php echo $this->get_render_attribute_string( 'content-wrapper' ); ?>>
			<?php
				if ( $has_icon ) {
					echo '<span ' . $this->get_render_attribute_string( 'icon-align' ) . '>';
					if ( $is_new || $migrated ) {
						Icons_Manager::render_icon( $settings['select_btn_icon'], [ 'aria-hidden' => 'true' ] );
					} elseif ( ! empty( $settings['btn_icon'] ) ) {
						?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
					}
					echo '</span>';
				}
			?>
			<span <?php echo $this->get_render_attribute_string( 'btn_text' ); ?>>
                <?php echo $settings['btn_text']; ?>
            </span>
		</span>
		<?php
	}
}
