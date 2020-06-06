<?php
/**
 * PowerPack WooCommerce Cart widget.
 *
 * @package PowerPack
 */

namespace PowerpackElements\Modules\Woocommerce\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Config;

use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Woo_Mini_Cart.
 */
class Woo_Mini_Cart extends Powerpack_Widget {

	/**
	 * Retrieve toggle widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Woo_Mini_Cart' );
	}

	/**
	 * Retrieve toggle widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Woo_Mini_Cart' );
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
        return parent::get_widget_categories( 'Woo_Mini_Cart' );
    }

	/**
	 * Retrieve toggle widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Woo_Mini_Cart' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.3.7
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Woo_Mini_Cart' );
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
            'pp-mini-cart',
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
	 * @access protected
	 */
	protected function _register_controls() {

		/* Button Settings */
		$this->register_content_button_controls();

		/* General Settings */
		$this->register_content_cart_controls();

		/* Help Docs */
		$this->register_content_help_docs();
		
		/* Style Tab: Cart Button */
		$this->register_style_cart_button_controls();

		/* Style Tab: Items Container */
		$this->register_style_items_container_controls();

		/* Style Tab: Item */
		$this->register_style_items_controls();
		
		/* Style Tab: Checkout Button */
		$this->register_style_buttons_controls();
	}

	/**
	 * Register toggle widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_content_cart_controls() {

		$this->start_controls_section(
			'section_settings',
			[
				'label'                 => __( 'Cart', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_cart_on',
			[
				'label'                 => __( 'Show Cart on', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'on-click',
				'options'               => [
					'on-click'		=> __( 'Click', 'powerpack' ),
					'on-hover'		=> __( 'Hover', 'powerpack' ),
					'none'			=> __( 'None', 'powerpack' ),
				],
			]
		);

		$this->add_control(
			'cart_title',
			[
				'label'					=> __( 'Cart Title', 'powerpack' ),
				'description'			=> __( 'Cart title is displayed on top of mini cart.', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'default'               => __( 'PowerPack Mini Cart', 'powerpack' ),
				'separator'				=> 'before',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_message',
			[
				'label'					=> __( 'Cart Message', 'powerpack' ),
				'description'			=> __( 'Cart message is displayed on bottom of mini cart.', 'powerpack' ),
				'type'                  => Controls_Manager::TEXTAREA,
				'default'               => __( '100% Secure Checkout!', 'powerpack' ),
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

        $this->add_control(
            'link',
            [
                'label'                 => __( 'Link', 'powerpack' ),
                'type'                  => Controls_Manager::URL,
				'dynamic'               => [
					'active'        => true,
                    'categories'    => [
                        TagsModule::POST_META_CATEGORY,
                        TagsModule::URL_CATEGORY
                    ],
				],
                'placeholder'           => 'https://www.your-link.com',
                'default'               => [
                    'url' => '#',
                ],
                'condition'             => [
                    'show_cart_on'   => 'none',
                ],
            ]
        );
        
		$this->end_controls_section();
	}

	/**
	 * Register toggle widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_content_button_controls() {

		$this->start_controls_section(
			'section_button_settings',
			[
				'label'                 => __( 'Cart Button', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'icon_style',
			[
				'label'                 => __( 'Style', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'icon',
				'options'               => [
					'icon'      => __( 'Icon only', 'powerpack' ),
					'icon_text' => __( 'Icon + Text', 'powerpack' ),
					'text'      => __( 'Text only', 'powerpack' ),
				],
			]
		);

		$this->add_control(
			'cart_text',
			[
				'label'                 => __( 'Text', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'default'               => __( 'Cart', 'powerpack' ),
				'condition'             => [
					'icon_style' => ['icon_text', 'text'],
				],
			]
		);
        
        $this->add_control(
			'icon_type',
			[
				'label'					=> esc_html__( 'Icon Type', 'powerpack' ),
				'type'					=> Controls_Manager::CHOOSE,
				'label_block'			=> false,
				'toggle'				=> false,
				'options'				=> [
					'icon' => [
						'title' => esc_html__( 'Icon', 'powerpack' ),
						'icon' => 'fa fa-star',
					],
					'image' => [
						'title' => esc_html__( 'Image', 'powerpack' ),
						'icon' => 'fa fa-picture-o',
					],
				],
				'default'				=> 'icon',
				'condition'				=> [
					'icon_style' => ['icon_text', 'icon'],
				],
			]
		);
		
		$this->add_control(
			'icon',
			[
				'label'					=> __( 'Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'default'				=> [
					'value'		=> 'fas fa-shopping-bag',
					'library'	=> 'fa-solid',
				],
                'condition'				=> [
					'icon_style'	=> ['icon_text', 'icon'],
                    'icon_type'     => 'icon',
                ],
			]
		);

		$this->add_control(
			'icon_image',
			[
				'label'                 => __( 'Image Icon', 'powerpack' ),
				'type'                  => Controls_Manager::MEDIA,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => [
					'url' => Utils::get_placeholder_image_src(),
				],
                'condition'             => [
					'icon_style' => ['icon_text', 'icon'],
                    'icon_type' => 'image',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'					=> 'icon_image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default'				=> 'full',
				'separator'				=> 'none',
                'condition'             => [
					'icon_style' => ['icon_text', 'icon'],
                    'icon_type' => 'image',
                ],
			]
		);

		$this->add_control(
			'counter_position',
			[
				'label'                 => __( 'Counter', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'top',
				'options'               => [
					'none'		=> __( 'None', 'powerpack' ),
					'top'		=> __( 'Bubble', 'powerpack' ),
					'after'		=> __( 'After Button', 'powerpack' ),
				],
			]
		);

		$this->add_control(
			'show_subtotal',
			[
				'label'					=> __( 'Subtotal', 'powerpack' ),
				'type'					=> Controls_Manager::SWITCHER,
				'label_on'				=> __( 'Show', 'powerpack' ),
				'label_off'				=> __( 'Hide', 'powerpack' ),
				'return_value'			=> 'yes',
				'default'				=> 'yes',
			]
		);
        
        $this->add_responsive_control(
			'button_align',
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
                'prefix_class'          => 'pp-woo-menu-cart%s-align-',
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-cart-button'   => 'text-align: {{VALUE}};',
				],
			]
		);
        
		$this->end_controls_section();
	}
	
	protected function register_content_help_docs() {

		$help_docs = PP_Config::get_widget_help_links('Woo_Mini_Cart');

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
	 * Style Tab
	 */
	/**
	 * Register Layout Controls.
	 *
	 * @access protected
	 */
	
	/**
	 * Style Tab: Items Container
	 * -------------------------------------------------
	 */
	protected function register_style_items_container_controls() {
		$this->start_controls_section(
			'section_items_container_style',
			[
				'label'                 => __( 'Items Container', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'items_container_background',
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .pp-woo-mini-cart',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'items_container_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-woo-mini-cart',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'items_container_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'items_container_width',
			[
				'label'                 => __( 'Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%' ],
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 150,
						'max' => 500,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'items_container_margin_top',
			[
				'label'                 => __( 'Margin Top', 'powerpack' ),
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
					'{{WRAPPER}} .pp-woo-mini-cart' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'items_container_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default'				=> [
					'top' => '10',
					'right' => '10',
					'bottom' => '10',
					'left' => '10',
					'unit' => 'px',
					'isLinked' => true,
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'					=> 'items_container_box_shadow',
				'separator'				=> 'before',
				'selector'				=> '{{WRAPPER}} .pp-woo-mini-cart',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_title_heading',
			[
				'label'                 => __( 'Cart Title', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
				'condition'             => [
					'show_cart_on!' => 'none',
					'cart_title!' => '',
				],
			]
		);

		$this->add_control(
			'cart_title_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .pp-woo-mini-cart-title' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
					'cart_title!' => '',
				],
			]
		);

		$this->add_control(
			'cart_title_bg_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .pp-woo-mini-cart-title' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
					'cart_title!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'cart_title_typography',
				'label'					=> __( 'Typography', 'powerpack' ),
				'selector'				=> '{{WRAPPER}} .pp-woo-mini-cart .pp-woo-mini-cart-title',
				'condition'             => [
					'show_cart_on!' => 'none',
					'cart_title!' => '',
				],
			]
		);
        
        $this->add_responsive_control(
			'cart_title_text_align',
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
					'{{WRAPPER}} .pp-woo-mini-cart .pp-woo-mini-cart-title'   => 'text-align: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
					'cart_title!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'cart_title_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .pp-woo-mini-cart-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
					'cart_title!' => '',
				],
			]
		);

		$this->add_control(
			'subtotal_heading',
			[
				'label'                 => __( 'Subtotal', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'subtotal_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .woocommerce-mini-cart__total' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'subtotal_bg_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .woocommerce-mini-cart__total' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'subtotal_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-woo-mini-cart .woocommerce-mini-cart__total',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'subtotal_typography',
				'label'					=> __( 'Typography', 'powerpack' ),
				'selector'				=> '{{WRAPPER}} .pp-woo-mini-cart .woocommerce-mini-cart__total',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);
        
        $this->add_responsive_control(
			'subtotal_text_align',
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
					'{{WRAPPER}} .pp-woo-mini-cart .woocommerce-mini-cart__total'   => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'subtotal_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .woocommerce-mini-cart__total' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_message_heading',
			[
				'label'                 => __( 'Cart Message', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
				'condition'             => [
					'show_cart_on!' => 'none',
					'cart_message!' => '',
				],
			]
		);

		$this->add_control(
			'cart_message_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .pp-woo-mini-cart-message' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
					'cart_message!' => '',
				],
			]
		);

		$this->add_control(
			'cart_message_bg_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .pp-woo-mini-cart-message' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
					'cart_message!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'cart_message_typography',
				'label'					=> __( 'Typography', 'powerpack' ),
				'selector'				=> '{{WRAPPER}} .pp-woo-mini-cart .pp-woo-mini-cart-message',
				'condition'             => [
					'show_cart_on!' => 'none',
					'cart_message!' => '',
				],
			]
		);
        
        $this->add_responsive_control(
			'cart_message_text_align',
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
					'{{WRAPPER}} .pp-woo-mini-cart .pp-woo-mini-cart-message'   => 'text-align: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
					'cart_message!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'cart_message_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'default'				=> [
					'top'		=> '10',
					'right'		=> '0',
					'bottom'	=> '0',
					'left'		=> '0',
					'unit'		=> 'px',
					'isLinked'	=> false,
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .pp-woo-mini-cart-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
					'cart_message!' => '',
				],
			]
		);

		$this->add_control(
			'empty_cart_message_heading',
			[
				'label'                 => __( 'Empty Cart Message', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'empty_cart_message_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .woocommerce-mini-cart__empty-message' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'empty_cart_message_typography',
				'label'					=> __( 'Typography', 'powerpack' ),
				'selector'				=> '{{WRAPPER}} .pp-woo-mini-cart .woocommerce-mini-cart__empty-message',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);
        
        $this->add_responsive_control(
			'empty_cart_message_text_align',
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
					'{{WRAPPER}} .pp-woo-mini-cart .woocommerce-mini-cart__empty-message'   => 'text-align: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);
        
		$this->end_controls_section();
	}
	
	/**
	 * Style Tab: Cart Table
	 * -------------------------------------------------
	 */
	protected function register_style_items_controls() {
		$this->start_controls_section(
			'section_items_style',
			[
				'label'                 => __( 'Item', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);
        
        $this->add_control(
            'cart_items_row_separator_type',
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
					'{{WRAPPER}} .pp-woo-mini-cart ul.product_list_widget li:not(:last-child)' => 'border-bottom-style: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

		$this->add_control(
			'cart_items_row_separator_color',
			[
				'label'                 => __( 'Separator Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart ul.product_list_widget li:not(:last-child)' => 'border-bottom-color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
					'cart_items_row_separator_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_items_row_separator_size',
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
					'{{WRAPPER}} .pp-woo-mini-cart ul.product_list_widget li:not(:last-child)' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
					'cart_items_row_separator_type!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_items_spacing',
			[
				'label'                 => __( 'Items Spacing', 'powerpack' ),
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
					'{{WRAPPER}} .pp-woo-mini-cart ul.product_list_widget li' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_items_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart ul.product_list_widget li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->start_controls_tabs( 'cart_items_rows_tabs_style' );

		$this->start_controls_tab(
			'cart_items_even_row',
			[
				'label'                 => __( 'Even Row', 'powerpack' ),
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_items_even_row_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .mini_cart_item:nth-child(2n)' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_items_even_row_links_color',
			[
				'label'                 => __( 'Links Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .mini_cart_item:nth-child(2n) a' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_items_even_row_background_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .mini_cart_item:nth-child(2n)' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cart_items_odd_row',
			[
				'label'                 => __( 'Odd Row', 'powerpack' ),
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_items_odd_row_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .mini_cart_item:nth-child(2n+1)' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_items_odd_row_links_color',
			[
				'label'                 => __( 'Links Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .mini_cart_item:nth-child(2n+1) a' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_items_odd_row_background_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .mini_cart_item:nth-child(2n+1)' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'item_name_heading',
			[
				'label'                 => __( 'Item Name', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'item_name_typography',
				'label'					=> __( 'Typography', 'powerpack' ),
				'selector'				=> '{{WRAPPER}} .pp-woo-mini-cart .mini_cart_item a:not(.remove)',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'item_name_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .mini_cart_item a:not(.remove)' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'item_name_bottom_spacing',
			[
				'label'                 => __( 'Bottom Spacing', 'powerpack' ),
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
					'{{WRAPPER}} .pp-woo-mini-cart .mini_cart_item a:not(.remove)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
					'cart_items_row_separator_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_items_image_heading',
			[
				'label'                 => __( 'Image', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

        $this->add_responsive_control(
            'cart_items_image_position',
            [
                'label'                 => __( 'Position', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'label_block'			=> false,
                'options'               => [
                    'left' 	=> [
                        'title' 	=> __( 'Left', 'powerpack' ),
                        'icon' 		=> 'eicon-h-align-left',
                    ],
                    'right' 		=> [
                        'title' 	=> __( 'Right', 'powerpack' ),
                        'icon' 		=> 'eicon-h-align-right',
                    ],
                ],
                'default'               => 'left',
				'selectors' => [
					'{{WRAPPER}} .pp-woo-mini-cart ul li.woocommerce-mini-cart-item a img' => 'float: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

		$this->add_responsive_control(
			'cart_items_image_spacing',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px' ],
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart ul li.woocommerce-mini-cart-item a img' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_items_image_width',
			[
				'label'                 => __( 'Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px' ],
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 10,
						'max' => 250,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart ul li.woocommerce-mini-cart-item a img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_items_price_heading',
			[
				'label'                 => __( 'Item Quantity & Price', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'cart_items_price_typography',
				'label'					=> __( 'Typography', 'powerpack' ),
				'selector'				=> '{{WRAPPER}} .pp-woo-mini-cart .cart_list .quantity',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_items_price_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .cart_list .quantity' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_items_remove_icon_heading',
			[
				'label'                 => __( 'Remove Item Icon', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'cart_items_remove_icon_size',
			[
				'label'                 => __( 'Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', 'em' ],
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
					'{{WRAPPER}} .pp-woo-mini-cart ul.cart_list li a.remove' => 'font-size: {{SIZE}}{{UNIT}}; width: calc({{SIZE}}{{UNIT}} + 6px); height: calc({{SIZE}}{{UNIT}} + 6px);',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

        $this->start_controls_tabs( 'tabs_cart_items_remove_icon_style' );

        $this->start_controls_tab(
            'tab_cart_items_remove_icon_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

		$this->add_control(
			'cart_items_remove_icon_color',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart ul.cart_list li a.remove' => 'color: {{VALUE}} !important;',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_items_remove_icon_bg_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart ul.cart_list li a.remove' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_items_remove_icon_border_color',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart ul.cart_list li a.remove' => 'border-color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_cart_items_remove_icon_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

		$this->add_control(
			'cart_items_remove_icon_color_hover',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart ul.cart_list li a.remove:hover' => 'color: {{VALUE}} !important;',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_items_remove_icon_bg_color_hover',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart ul.cart_list li a.remove:hover' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'cart_items_remove_icon_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart ul.cart_list li a.remove:hover' => 'border-color: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();
        
		$this->end_controls_section();
	}
		
	/**
	 * Style Tab: Buttons
	 * -------------------------------------------------
	 */
	protected function register_style_buttons_controls() {

        $this->start_controls_section(
            'section_buttons_style',
            [
                'label'                 => __( 'Buttons', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'buttons_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-woo-mini-cart .buttons .button',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

		$this->add_control(
			'buttons_layout',
			[
				'label'                 => __( 'Layout', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'inline',
				'options'               => [
					'inline'		=> __( 'Inline', 'powerpack' ),
					'stacked'		=> __( 'Stacked', 'powerpack' ),
				],
                'prefix_class'          => 'pp-woo-cart-buttons-',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);
        
        $this->add_responsive_control(
			'buttons_align',
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
                'prefix_class'          => 'pp-woo-menu-cart-align-',
				'selectors'             => [
					'{{WRAPPER}}.pp-woo-cart-buttons-inline .buttons'   => 'text-align: {{VALUE}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
					'buttons_layout' => 'inline',
				],
			]
		);

		$this->add_responsive_control(
			'buttons_gap',
			[
				'label'                 => __( 'Space Between', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors'             => [
					'{{WRAPPER}}.pp-woo-cart-buttons-inline .buttons .button.checkout.checkout' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pp-woo-cart-buttons-stacked .buttons .button.checkout.checkout' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'buttons_margin_top',
			[
				'label'                 => __( 'Margin Top', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart-items .buttons' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'buttons_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .buttons .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'view_cart_button_heading',
			[
				'label'                 => __( 'View Cart Button', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

        $this->start_controls_tabs( 'tabs_view_cart_button_style' );

        $this->start_controls_tab(
            'tab_view_cart_button_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

        $this->add_control(
            'view_cart_button_bg_color_normal',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-mini-cart .buttons .button:not(.checkout)' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

        $this->add_control(
            'view_cart_button_text_color_normal',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-mini-cart .buttons .button:not(.checkout)' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'view_cart_button_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-woo-mini-cart .buttons .button:not(.checkout)',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'view_cart_button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .buttons .button:not(.checkout)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'view_cart_button_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-woo-mini-cart .buttons .button:not(.checkout)',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_view_cart_button_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

        $this->add_control(
            'view_cart_button_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-mini-cart .buttons .button:not(.checkout):hover' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

        $this->add_control(
            'view_cart_button_text_color_hover',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-mini-cart .buttons .button:not(.checkout):hover' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

        $this->add_control(
            'view_cart_button_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-mini-cart .buttons .button:not(.checkout):hover' => 'border-color: {{VALUE}}',
                ],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'view_cart_button_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-woo-mini-cart .buttons .button:not(.checkout):hover',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();

		$this->add_control(
			'checkout_button_heading',
			[
				'label'                 => __( 'Checkout Button', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

        $this->start_controls_tabs( 'tabs_checkout_button_style' );

        $this->start_controls_tab(
            'tab_checkout_button_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

        $this->add_control(
            'checkout_button_bg_color_normal',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-mini-cart .buttons .button.checkout' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

        $this->add_control(
            'checkout_button_text_color_normal',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-mini-cart .buttons .button.checkout' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'checkout_button_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-woo-mini-cart .buttons .button.checkout',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_control(
			'checkout_button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart .buttons .button.checkout' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'checkout_button_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-woo-mini-cart .buttons .button.checkout',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_checkout_button_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

        $this->add_control(
            'checkout_button_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-mini-cart .buttons .button.checkout:hover' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

        $this->add_control(
            'checkout_button_text_color_hover',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-mini-cart .buttons .button.checkout:hover' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

        $this->add_control(
            'checkout_button_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-mini-cart .buttons .button.checkout:hover' => 'border-color: {{VALUE}}',
                ],
				'condition'             => [
					'show_cart_on!' => 'none',
				],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'checkout_button_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-woo-mini-cart .buttons .button.checkout:hover',
				'condition'             => [
					'show_cart_on!' => 'none',
				],
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->end_controls_section();
	}
		
	/**
	 * Style Tab: Cart Button
	 * -------------------------------------------------
	 */
	protected function register_style_cart_button_controls() {

        $this->start_controls_section(
            'section_cart_button_style',
            [
                'label'                 => __( 'Cart Button', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'					=> 'cart_button_typography',
				'label'					=> __( 'Typography', 'powerpack' ),
				'selector'				=> '{{WRAPPER}} .pp-woo-mini-cart-container .pp-woo-cart-contents',
			]
		);

		$this->start_controls_tabs( 'tabs_cart_button' );

		$this->start_controls_tab(
			'tab_cart_button_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
			]
		);

        $this->add_control(
            'cart_button_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-mini-cart-container .pp-woo-cart-contents' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'cart_button_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-mini-cart-container .pp-woo-cart-contents' => 'background-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'cart_button_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-woo-mini-cart-container .pp-woo-cart-contents',
			]
		);

		$this->add_control(
			'cart_button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart-container .pp-woo-cart-contents' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cart_button_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-mini-cart-container .pp-woo-cart-contents' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_cart_button_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
			]
		);

        $this->add_control(
            'cart_button_color_hover',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-mini-cart-container .pp-woo-cart-contents:hover' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'cart_button_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-mini-cart-container .pp-woo-cart-contents:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'cart_button_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-mini-cart-container .pp-woo-cart-contents:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'cart_button_icon_heading',
			[
				'label'                 => __( 'Button Icon', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
				'condition'             => [
					'icon_style' => ['icon_text', 'icon'],
				],
			]
		);

        $this->add_control(
            'cart_button_icon_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-woo-cart-button .pp-icon' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-woo-cart-button .pp-icon svg' => 'fill: {{VALUE}}',
                ],
				'condition'             => [
					'icon_style' => ['icon_text', 'icon'],
					'icon_type' => 'icon',
				],
            ]
        );

		$this->add_responsive_control(
			'cart_button_icon_size',
			[
				'label'                 => __( 'Icon Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', 'em' ],
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 40,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-cart-button .pp-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_style' => ['icon_text', 'icon'],
					'icon_type' => 'icon',
				],
			]
		);

		$this->add_responsive_control(
			'cart_button_icon_img_size',
			[
				'label'                 => __( 'Icon Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-cart-button .pp-cart-contents-icon-image img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_style' => ['icon_text', 'icon'],
					'icon_type' => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'cart_button_icon_spacing',
			[
				'label'                 => __( 'Icon Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => '',
				],
				'range'                => [
					'px' => [
						'min' => 0,
						'max' => 40,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-woo-cart-button .pp-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'icon_style' => ['icon_text', 'icon'],
				],
			]
		);

		$this->add_control(
			'cart_button_counter_heading',
			[
				'label'                 => __( 'Counter', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
				'condition'             => [
					'counter_position!' => 'none',
				],
			]
		);

        $this->add_control(
            'cart_button_counter_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-cart-counter' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'counter_position!' => 'none',
				],
            ]
        );

        $this->add_control(
            'cart_button_counter_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-cart-counter' => 'background-color: {{VALUE}}',
                    '{{WRAPPER}} .pp-woo-menu-cart-counter-after .pp-cart-counter:before' => 'border-right-color: {{VALUE}}',
                ],
				'condition'             => [
					'counter_position!' => 'none',
				],
            ]
        );

		$this->add_control(
			'cart_button_counter_gap',
			[
				'label'					=> __( 'Spacing', 'powerpack' ),
				'type'					=> Controls_Manager::SLIDER,
				'default'				=> [
					'unit' => 'px',
				],
				'range'					=> [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'selectors'				=> [
					'{{WRAPPER}} .pp-woo-menu-cart-counter-after .pp-cart-contents-count-after' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition'				=> [
					'counter_position' => 'after',
				],
			]
		);

		$this->add_control(
			'cart_button_counter_distance',
			[
				'label'					=> __( 'Distance', 'powerpack' ),
				'type'					=> Controls_Manager::SLIDER,
				'default'				=> [
					'unit' => 'em',
				],
				'range'					=> [
					'em' => [
						'min' => 0,
						'max' => 4,
						'step' => 0.1,
					],
				],
				'selectors'				=> [
					'{{WRAPPER}} .pp-cart-counter' => 'right: -{{SIZE}}{{UNIT}}; top: -{{SIZE}}{{UNIT}}',
				],
				'condition'				=> [
					'counter_position' => 'top',
				],
			]
		);
        
        $this->end_controls_section();
	}
	
	/**
	 * Render output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_counter() {
		?>
		<span class="pp-cart-counter"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
		<?php
	}
	
	/**
	 * Render output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_cart_icon() {
		$settings = $this->get_settings();
		
		if ( 'icon' == $settings['icon_type'] ) {
			?>
			<span class="pp-mini-cart-button-icon pp-icon">
				<?php
					\Elementor\Icons_Manager::render_icon( $settings['icon'], [ 'class' => 'pp-cart-contents-icon', 'aria-hidden' => 'true' ] ); ?>
			</span>
			<?php
		} elseif ( 'image' == $settings['icon_type'] && $settings['icon_image']['url'] ) { ?>
			<span class="pp-cart-contents-icon-image pp-icon">
				<?php
					echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'icon_image', 'icon_image' );
				?>
			</span>
			<?php
		}
	}
	
	/**
	 * Render output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_text() {
		$settings = $this->get_settings();
		?>
		<span class="pp-cart-contents-text"><?php echo $settings['cart_text']; ?></span>
		<?php
	}
	
	/**
	 * Render output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_subtotal() {
		$settings = $this->get_settings();
		
		$sub_total = WC()->cart->get_cart_subtotal();
		
		if ( 'yes' == $settings['show_subtotal'] ) {
			?>
			<span class="pp-cart-subtotal"><?php echo $sub_total; ?></span>
			<?php
		}
	}
	
	/**
	 * Render output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		if ( null === WC()->cart ) {
			return;
		}

		$settings = $this->get_settings();
        
        $this->add_render_attribute( 'container', [
			'class' => [
				'pp-woocommerce',
				'pp-woo-mini-cart-container',
				'woocommerce',
				'pp-woo-menu-cart-counter-' . $settings['counter_position'],
			],
			'data-target'	=> $settings['show_cart_on'],
        ] );
        
        $this->add_render_attribute( 'button', [
			'class'			=> [
				'pp-woo-cart-contents',
				'pp-woo-cart-' . $settings['icon_style'],
			],
			'title'			=> __( 'View your shopping cart' ),
		] );
		
		if ( $settings['show_cart_on'] == 'none' ) {
			if ( ! empty( $settings['link']['url'] ) ) {
				
				$this->add_link_attributes( 'button', $settings['link'] );

            }
		} else {
			$this->add_render_attribute( 'button', 'href', '#' );
		}
        ?>
		<?php do_action( 'pp_woo_before_mini_cart_wrap' ); ?>

		<div <?php echo $this->get_render_attribute_string( 'container' ); ?>>
			
			<div class="pp-woo-cart-button">
				<div class="pp-woo-cart-button-inner">
				
				<a <?php echo $this->get_render_attribute_string( 'button' ); ?>>
					<span class="pp-cart-button-wrap">
						<?php
							if ( 'icon' == $settings['icon_style'] ) {

								$this->render_subtotal();
								$this->render_cart_icon();

							} elseif ( 'icon_text' == $settings['icon_style'] ) {

								$this->render_text();
								$this->render_subtotal();
								$this->render_cart_icon();

							} else {

								$this->render_text();
								$this->render_subtotal();

							}
						?>
					</span>
					
					<?php if ( 'top' == $settings['counter_position'] ) { ?>
						<span class="pp-cart-contents-count">
							<?php $this->render_counter(); ?>
						</span>
					<?php } ?>
				</a>

				<?php if ( 'after' == $settings['counter_position'] ) { ?>
					<span class="pp-cart-contents-count-after">
						<?php $this->render_counter(); ?>
					</span>
				<?php } ?>
				</div>
			</div>

			<?php if ( 'none' != $settings['show_cart_on'] ) { ?>
				<div class="pp-woo-mini-cart-wrap pp-v-hidden">
					<div class="pp-woo-mini-cart pp-woo-menu-cart">
						<?php if ( $settings['cart_title'] ) { ?>
							<h3 class="pp-woo-mini-cart-title">
								<?php echo $settings['cart_title']; ?>
							</h3>
						<?php } ?>

						<div class="pp-woo-mini-cart-items">
							<div class="widget_shopping_cart_content"><?php woocommerce_mini_cart();?></div>
						</div>

						<?php if ( $settings['cart_message'] ) { ?>
							<div class="pp-woo-mini-cart-message">
								<?php echo $settings['cart_message']; ?>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		</div>

		<?php do_action( 'pp_woo_after_mini_cart_wrap' ); ?>
		<?php
	}
}