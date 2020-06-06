<?php
/**
 * PowerPack WooCommerce Products.
 *
 * @package PowerPack
 */

namespace PowerpackElements\Modules\Woocommerce\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Config;
use PowerpackElements\Classes\PP_Posts_Helper;

use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;
use PowerpackElements\Modules\Woocommerce\Skins;

if ( ! defined( 'ABSPATH' ) ) {
	exit;   // Exit if accessed directly.
}

/**
 * Class Woo_Products.
 */
class Woo_Products extends Powerpack_Widget {

	/**
	 * Products Query
	 *
	 * @var query
	 */
	private $query = null;

	/**
	 * Has Template content
	 *
	 * @var _has_template_content
	 */
	protected $_has_template_content = false;

	/**
	 * Retrieve Woo Product Grid Widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Woo_Products' );
	}

	/**
	 * Retrieve Woo Product Grid Widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Woo_Products' );
	}

    /**
	 * Retrieve the list of categories the Woo Product Grid widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Woo_Products' );
    }

	/**
	 * Retrieve Woo Product Grid Widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Woo_Products' );
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
		return parent::get_widget_keywords( 'Woo_Products' );
	}

	/**
	 * Get Script Depends.
	 *
	 * @access public
	 *
	 * @return array scripts.
	 */
	public function get_script_depends() {
		return [ 'imagesloaded', 'jquery-slick', 'pp-woocommerce', 'flexslider' ];
	}

    /**
	 * Retrieve the list of styles the Woo - Products widget depended on.
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
	 * Register Get Query.
	 *
	 * @access protected
	 */
	public function get_query() {
		return $this->query;
	}

	/**
	 * Register Register Skins.
	 *
	 * @access protected
	 */
	protected function _register_skins() {
		$this->add_skin( new Skins\Skin_Grid_Skin_1( $this ) );
		$this->add_skin( new Skins\Skin_Grid_Skin_2( $this ) );
		$this->add_skin( new Skins\Skin_Grid_Skin_3( $this ) );
		$this->add_skin( new Skins\Skin_Grid_Skin_4( $this ) );
		$this->add_skin( new Skins\Skin_Grid_Skin_5( $this ) );
	}

	/**
	 * Register Woo Product Grid controls.
	 *
	 * @access protected
	 */
	protected function _register_controls() {

		/* Content Tab */
		$this->register_content_layout_controls();
		$this->register_content_slider_controls();
		$this->register_content_query_controls();
		$this->register_content_content_controls();
		$this->register_content_product_badges_controls();
		$this->register_image_controls();
		$this->register_quick_view_controls();
		$this->register_content_pagination_controls();
		$this->register_content_help_docs();

		/* Style Tab */
		$this->register_style_layout_controls();
		$this->register_style_content_controls();
		$this->register_style_product_badges_controls();
		$this->register_quick_view_style_controls();
		$this->register_lightbox_style_controls();
		
		$this->register_style_pagination_controls();
		$this->register_style_navigation_controls();
	}

	/**
	 * Register Woo Products Layout Controls.
	 *
	 * @access protected
	 */
	protected function register_content_layout_controls() {

		$this->start_controls_section(
			'section_layout',
			[
				'label'             => __( 'Layout', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(
            'products_layout_type',
            [
                'label'             => __( 'Layout', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'default'           => 'grid',
                'options'           => [
                    'grid'      => __( 'Grid', 'powerpack' ),
                    'slider'    => __( 'Carousel', 'powerpack' ),
                ],
                'condition'         => [
                    '_skin'     => [ 'skin-1', 'skin-2', 'skin-3', 'skin-4', 'skin-5' ],
                ],
            ]
        );
        
        $this->add_responsive_control(
            'products_columns',
            [
                'label'             => __( 'Columns', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'default'           => '4',
                'tablet_default'    => '3',
                'mobile_default'    => '1',
                'options'           => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ],
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                ],
            ]
        );

        $this->add_control(
            'products_per_page',
            [
                'label'             => __( 'Products Per Page', 'powerpack' ),
                'type'              => Controls_Manager::NUMBER,
                'default'           => '8',
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                ],
            ]
        );

		$this->add_control(
			'slider_products_per_page',
			[
				'label'             => __( 'Total Products', 'powerpack' ),
				'type'              => Controls_Manager::NUMBER,
				'default'           => '8',
				'condition'         => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'slider',
				],
			]
		);

		$this->add_responsive_control(
			'slides_to_show',
			[
				'label'             => __( 'Products to Show', 'powerpack' ),
				'type'              => Controls_Manager::NUMBER,
				'default'           => 4,
				'tablet_default'    => 3,
				'mobile_default'    => 1,
				'condition'         => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'slider',
				],
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'label'             => __( 'Products to Scroll', 'powerpack' ),
				'type'              => Controls_Manager::NUMBER,
				'default'           => 1,
				'tablet_default'    => 1,
				'mobile_default'    => 1,
				'condition'         => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'slider',
				],
			]
		);
        
		$this->end_controls_section();
	}

	/**
	 * Register Woo Products Filter Controls.
	 *
	 * @access protected
	 */
	protected function register_content_query_controls() {

		$this->start_controls_section(
			'section_query',
			[
				'label'             => __( 'Query', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(
            'source',
            [
                'label'             => __( 'Source', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'default'           => 'all',
                'options'           => [
                    'all'       => __( 'All Products', 'powerpack' ),
                    'custom'    => __( 'Custom Query', 'powerpack' ),
                    'manual'	=> __( 'Manual Selection', 'powerpack' ),
                    'main'		=> __( 'Main Query', 'powerpack' ),
                    'related'	=> __( 'Related Products', 'powerpack' ),
                ],
            ]
        );

        $this->add_control(
            'category_filter_rule',
            [
                'label'             => __( 'Category Filter Rule', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'default'           => 'IN',
                'options'           => [
                    'IN'     => __( 'Match Categories', 'powerpack' ),
                    'NOT IN' => __( 'Exclude Categories', 'powerpack' ),
                ],
                'condition'         => [
                    'source' => 'custom',
                ],
            ]
        );
        $this->add_control(
            'category_filter',
            [
                'label'             => __( 'Select Categories', 'powerpack' ),
                'type'              => Controls_Manager::SELECT2,
                'multiple'          => true,
                'default'           => '',
                'options'           => $this->get_product_categories(),
                'condition'         => [
                    'source' => 'custom',
                ],
            ]
        );
        $this->add_control(
            'tag_filter_rule',
            [
                'label'             => __( 'Tag Filter Rule', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'default'           => 'IN',
                'options'           => [
                    'IN'     => __( 'Match Tags', 'powerpack' ),
                    'NOT IN' => __( 'Exclude Tags', 'powerpack' ),
                ],
                'condition'         => [
                    'source' => 'custom',
                ],
            ]
        );
        $this->add_control(
            'tag_filter',
            [
                'label'             => __( 'Select Tags', 'powerpack' ),
                'type'              => Controls_Manager::SELECT2,
                'multiple'          => true,
                'default'           => '',
                'options'           => $this->get_product_tags(),
                'condition'         => [
                    'source' => 'custom',
                ],
            ]
        );
        $this->add_control(
            'offset',
            [
                'label'             => __( 'Offset', 'powerpack' ),
                'type'              => Controls_Manager::NUMBER,
                'default'           => 0,
                'description'       => __( 'Number of post to displace or pass over.', 'powerpack' ),
                'condition'         => [
                    'source' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'query_manual_ids',
            [
                'label'             => __( 'Select Products', 'powerpack' ),
                'type'              => 'pp-query-posts',
                'post_type'         => 'product',
                'multiple'          => true,
                'condition'         => [
                    'source' => 'manual',
                ],
            ]
        );

        /* Exclude */
        $this->add_control(
            'query_exclude',
            [
                'label'             => __( 'Exclude', 'powerpack' ),
                'type'              => Controls_Manager::HEADING,
                'separator'         => 'before',
                'condition'         => [
                    'source!' => ['manual', 'main', 'related'],
                ],
            ]
        );
        $this->add_control(
            'query_exclude_ids',
            [
                'label'             => __( 'Select Products', 'powerpack' ),
                'type'              => 'pp-query-posts',
                'post_type'   => 'product',
                'multiple'    => true,
                'description'       => __( 'Select products to exclude from the query.', 'powerpack' ),
                'condition'         => [
                    'source!' => ['manual', 'main', 'related'],
                ],
            ]
        );
        $this->add_control(
            'query_exclude_current',
            [
                'label'             => __( 'Exclude Current Product', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
                'default'           => '',
                'description'  => __( 'Enable this option to remove current product from the query.', 'powerpack' ),
                'condition'         => [
                    'source!' => ['manual', 'main', 'related'],
                ],
            ]
        );

        /* Advanced Filter */
        $this->add_control(
            'query_advanced',
            [
                'label'             => __( 'Advanced', 'powerpack' ),
                'type'              => Controls_Manager::HEADING,
                'separator'         => 'before',
                'condition'         => [
                    'source!' => ['main', 'related'],
                ],
            ]
        );
        $this->add_control(
            'filter_by',
            [
                'label'             => __( 'Filter By', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'default'           => '',
                'options'           => [
                    ''         		=> __( 'None', 'powerpack' ),
                    'featured' 		=> __( 'Featured', 'powerpack' ),
                    'sale'     		=> __( 'Sale', 'powerpack' ),
                    'top_rated'     => __( 'Top Rated', 'powerpack' ),
                    'best_selling'  => __( 'Best Selling', 'powerpack' ),
                ],
                'condition'         => [
                    'source!' => ['main', 'related'],
                ],
            ]
        );
        $this->add_control(
            'orderby',
            [
                'label'             => __( 'Order by', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'default'           => 'date',
                'options'           => [
                    'date'       => __( 'Date', 'powerpack' ),
                    'title'      => __( 'Title', 'powerpack' ),
                    'price'      => __( 'Price', 'powerpack' ),
                    'popularity' => __( 'Popularity', 'powerpack' ),
                    'rating'     => __( 'Rating', 'powerpack' ),
                    'rand'       => __( 'Random', 'powerpack' ),
                    'menu_order' => __( 'Menu Order', 'powerpack' ),
                ],
                'condition'         => [
                    'source!' => ['main', 'related'],
                ],
            ]
        );
        $this->add_control(
            'order',
            [
                'label'             => __( 'Order', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'default'           => 'desc',
                'options'           => [
                    'desc' => __( 'Descending', 'powerpack' ),
                    'asc'  => __( 'Ascending', 'powerpack' ),
                ],
                'condition'         => [
                    'source!' => ['main', 'related'],
                ],
            ]
        );

		$this->end_controls_section();
	}

	/**
	 * Register Content Control Section.
	 *
	 * @access protected
	 */
	protected function register_content_content_controls() {

		$this->start_controls_section(
			'section_content_field',
			[
				'label'             => __( 'Content', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_CONTENT,
			]
		);
        
        $this->add_control(
            'show_category',
            [
                'label'             => __( 'Category', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'label_on'          => __( 'Show', 'powerpack' ),
                'label_off'         => __( 'Hide', 'powerpack' ),
                'return_value'      => 'yes',
                'default'           => 'yes',
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label'             => __( 'Title', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'label_on'          => __( 'Show', 'powerpack' ),
                'label_off'         => __( 'Hide', 'powerpack' ),
                'return_value'      => 'yes',
                'default'           => 'yes',
            ]
        );

        $this->add_control(
            'link_title',
            [
                'label'             => __( 'Link to Product', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
                'default'           => 'yes',
				'condition'         => [
					'show_title'	=> 'yes',
				],
            ]
        );
		
        $this->add_control(
            'show_ratings',
            [
                'label'             => __( 'Ratings', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'label_on'          => __( 'Show', 'powerpack' ),
                'label_off'         => __( 'Hide', 'powerpack' ),
                'return_value'      => 'yes',
                'default'           => 'yes',
            ]
        );
        $this->add_control(
            'show_price',
            [
                'label'             => __( 'Price', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'label_on'          => __( 'Show', 'powerpack' ),
                'label_off'         => __( 'Hide', 'powerpack' ),
                'return_value'      => 'yes',
                'default'           => 'yes',
            ]
        );

        $this->add_control(
            'show_short_desc',
            [
                'label'             => __( 'Short Description', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'label_on'          => __( 'Show', 'powerpack' ),
                'label_off'         => __( 'Hide', 'powerpack' ),
                'return_value'      => 'yes',
                'default'           => '',
            ]
        );
        $this->add_control(
            'show_add_cart',
            [
                'label'             => __( 'Add to Cart', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'label_on'          => __( 'Show', 'powerpack' ),
                'label_off'         => __( 'Hide', 'powerpack' ),
                'return_value'      => 'yes',
                'default'           => 'yes',
            ]
        );

		$this->end_controls_section();
	}

	/**
	 * Register Content Style Section.
	 *
	 * @access protected
	 */
	protected function register_style_content_controls() {
		
		$this->start_controls_section(
			'section_design_content',
			[
				'label'             => __( 'Content', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label'             => __( 'Alignment', 'powerpack' ),
				'type'              => Controls_Manager::CHOOSE,
				'label_block'       => true,
				'options'           => [
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
					'justify'  => [
						'title' => __( 'Justify', 'powerpack' ),
						'icon'  => 'fa fa-align-justify',
					],
				],
				'default'           => 'left',
				'prefix_class'      => 'pp-woo%s--align-',
			]
		);

		$this->add_control(
			'product_content_bg_color',
			[
				'label'             => __( 'Content Background Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'separator'         => 'before',
				'selectors'         => [
					'{{WRAPPER}} .pp-woo-products-summary-wrap' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'product_content_padding',
			[
				'label'             => __( 'Content Padding', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-woo-products-summary-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'product_category_style',
			[
				'label'             => __( 'Category', 'powerpack' ),
				'type'              => Controls_Manager::HEADING,
				'separator'         => 'before',
				'condition'         => [
					'show_category' => 'yes',
				],
			]
		);

		$this->add_control(
			'product_category_color',
			[
				'label'             => __( 'Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .pp-woocommerce .pp-woo-product-category, .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .product_meta' => 'color: {{VALUE}};',
				],
				'condition'         => [
					'show_category' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'product_category_typography',
				'scheme'            => Scheme_Typography::TYPOGRAPHY_3,
				'selector'          => '{{WRAPPER}} .pp-woocommerce .pp-woo-product-category',
				'condition'         => [
					'show_category' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'product_category_spacing',
			[
				'label'             => __( 'Spacing', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-woocommerce .pp-woo-product-category, .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .product_meta' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'         => [
					'show_category' => 'yes',
				],
			]
		);

		$this->add_control(
			'product_title_style',
			[
				'label'             => __( 'Title', 'powerpack' ),
				'type'              => Controls_Manager::HEADING,
				'separator'         => 'before',
				'condition'         => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'product_title_color',
			[
				'label'             => __( 'Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .pp-woocommerce .pp-loop-product__link, {{WRAPPER}} .pp-woocommerce .woocommerce-loop-product__title, .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .product_title' => 'color: {{VALUE}};',
				],
				'condition'         => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'product_title_hover_color',
			[
				'label'             => __( 'Hover Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .pp-woocommerce .pp-loop-product__link:hover .woocommerce-loop-product__title, .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .product_title:hover' => 'color: {{VALUE}};',
				],
				'condition'         => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'product_title_typography',
				'scheme'            => Scheme_Typography::TYPOGRAPHY_1,
				'selector'          => '{{WRAPPER}} .pp-woocommerce .pp-loop-product__link, {{WRAPPER}} .pp-woocommerce .woocommerce-loop-product__title, .pp-quick-view-{{ID}} .woocommerce div.product .product_title',
				'condition'         => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'product_title_spacing',
			[
				'label'             => __( 'Spacing', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-woocommerce .woocommerce-loop-product__title, .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .product_title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'         => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'product_rating_style',
			[
				'label'             => __( 'Rating', 'powerpack' ),
				'type'              => Controls_Manager::HEADING,
				'separator'         => 'before',
				'condition'         => [
					'show_ratings' => 'yes',
				],
			]
		);

		$this->add_control(
			'product_rating_color',
			[
				'label'             => __( 'Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .pp-woocommerce .star-rating, {{WRAPPER}} .pp-woocommerce .star-rating::before, .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .star-rating, .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .star-rating:before' => 'color: {{VALUE}};',
				],
				'condition'         => [
					'show_ratings' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'product_rating_spacing',
			[
				'label'             => __( 'Spacing', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-woocommerce .star-rating, .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .woocommerce-product-rating' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'         => [
					'show_ratings' => 'yes',
				],
			]
		);

		$this->add_control(
			'product_price_style',
			[
				'label'             => __( 'Price', 'powerpack' ),
				'type'              => Controls_Manager::HEADING,
				'separator'         => 'before',
				'condition'         => [
					'show_price' => 'yes',
				],
			]
		);

		$this->add_control(
			'product_price_color',
			[
				'label'             => __( 'Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .pp-woocommerce li.product .price, .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .price' => 'color: {{VALUE}};',
				],
				'condition'         => [
					'show_price' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'product_price_typography',
				'scheme'            => Scheme_Typography::TYPOGRAPHY_3,
				'selector'          => '{{WRAPPER}} .pp-woocommerce li.product .price',
				'condition'         => [
					'show_price' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'product_price_spacing',
			[
				'label'             => __( 'Spacing', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-woocommerce li.product .price, .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .price' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'         => [
					'show_price' => 'yes',
				],
			]
		);

		$this->add_control(
			'product_short_desc_style',
			[
				'label'             => __( 'Short Description', 'powerpack' ),
				'type'              => Controls_Manager::HEADING,
				'separator'         => 'before',
				'condition'         => [
					'show_short_desc' => 'yes',
				],
			]
		);

		$this->add_control(
			'product_short_desc_color',
			[
				'label'             => __( 'Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .pp-woocommerce .pp-woo-products-description, .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .woocommerce-product-details__short-description' => 'color: {{VALUE}};',
				],
				'condition'         => [
					'show_short_desc' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'product_short_desc_typography',
				'scheme'            => Scheme_Typography::TYPOGRAPHY_3,
				'selector'          => '{{WRAPPER}} .pp-woocommerce .pp-woo-products-description',
				'condition'         => [
					'show_short_desc' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'product_short_desc_spacing',
			[
				'label'             => __( 'Spacing', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-woocommerce .pp-woo-products-description, .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .woocommerce-product-details__short-description p:last-child' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'         => [
					'show_short_desc' => 'yes',
				],
			]
		);

		$this->add_control(
			'product_add_cart_style',
			[
				'label'             => __( 'Add to Cart', 'powerpack' ),
				'type'              => Controls_Manager::HEADING,
				'separator'         => 'before',
				'condition'         => [
					'show_add_cart' => 'yes',
					'_skin'			=> [ 'skin-1' ]	
				],
			]
		);

		$this->add_control(
			'product_actions_style',
			[
				'label'             => __( 'Product Actions', 'powerpack' ),
				'type'              => Controls_Manager::HEADING,
				'separator'         => 'before',
				'condition'         => [
					'show_add_cart' => 'yes',
					'_skin'			=> [ 'skin-2', 'skin-3', 'skin-4', 'skin-5' ]
				],
			]
		);

		$this->add_control(
			'actions_overlay_color',
			[
				'label'             => __( 'Overlay Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .pp-woo-skin-skin-5 .pp-product-actions:before' => 'background-color: {{VALUE}};',
				],
				'condition'         => [
					'_skin'	=> 'skin-5',
				],
			]
		);

		$this->add_responsive_control(
			'product_add_cart_padding',
			[
				'label'             => __( 'Padding', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-woocommerce .pp-woo-products-summary-wrap .button,
					{{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-action-item-wrap,
					.pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'         => [
					'show_add_cart' => 'yes',
					'_skin'	=> [ 'skin-1', 'skin-3', 'skin-4' ],
				],
			]
		);

		$this->start_controls_tabs( 'product_add_cart_tabs_style' );

        $this->start_controls_tab(
            'product_add_cart_normal',
            [
                'label'             => __( 'Normal', 'powerpack' ),
                'condition'         => [
                    'show_add_cart' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'product_add_cart_color',
            [
                'label'             => __( 'Text Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-woo-products-summary-wrap .button,
                    {{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-action-item-wrap,
                    {{WRAPPER}} .pp-product-actions .pp-action-item,
                    .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .button' => 'color: {{VALUE}};',
                ],
                'condition'         => [
                    'show_add_cart' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'product_view_cart_color',
            [
                'label'             => __( 'View Cart Text Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .added_to_cart' => 'color: {{VALUE}};',
                ],
                'condition'         => [
                    'show_add_cart' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'              => 'product_add_cart_background_color',
                'label'             => __( 'Background Color', 'powerpack' ),
                'types'             => [ 'classic', 'gradient' ],
                'exclude'           => [ 'image' ],
                'selector'          => '{{WRAPPER}} .pp-woocommerce .pp-woo-products-summary-wrap .button, 
                {{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-action-item-wrap,
                .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .button',
                'condition'         => [
                    'show_add_cart' => 'yes',
                    '_skin'			=> [ 'skin-1', 'skin-3', 'skin-4' ]
                ],
            ]
        );

        $this->add_control(
            'product_actions_background_color',
            [
                'label'             => __( 'Background Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-action-item-wrap,
                    .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .button' => 'background-color: {{VALUE}};',
                ],
                'condition'         => [
                    'show_add_cart' => 'yes',
                    '_skin'			=> [ 'skin-2', 'skin-5' ]
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'product_add_cart_hover',
            [
                'label'             => __( 'Hover', 'powerpack' ),
                'condition'         => [
                    'show_add_cart' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'product_add_cart_hover_color',
            [
                'label'             => __( 'Text Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-woo-products-summary-wrap .button:hover,
                    {{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-action-item-wrap:hover,
                    {{WRAPPER}} .pp-product-actions .pp-action-item-wrap:hover .pp-action-item,
                    .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .button:hover' => 'color: {{VALUE}};',
                ],
                'condition'         => [
                    'show_add_cart' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'product_view_cart_hover_color',
            [
                'label'     => __( 'View Cart Text Color', 'powerpack' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pp-woocommerce .added_to_cart:hover' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'show_add_cart' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'              => 'product_add_cart_background_hover_color',
                'label'             => __( 'Background Hover Color', 'powerpack' ),
                'types'             => [ 'classic', 'gradient' ],
                'selector'          => '{{WRAPPER}} .pp-woocommerce .pp-woo-products-summary-wrap .button:hover, 
                {{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-action-item-wrap:hover,
                .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .button:hover',
                'condition'         => [
                    'show_add_cart' => 'yes',
                    '_skin'			=> [ 'skin-1', 'skin-3', 'skin-4' ]
                ],
            ]
        );

        $this->add_control(
            'product_actions_background_hover_color',
            [
                'label'             => __( 'Background Hover Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-action-item-wrap:hover,
                    .pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content .button:hover' => 'background-color: {{VALUE}};',
                ],
                'condition'         => [
                    'show_add_cart' => 'yes',
                    '_skin'			=> [ 'skin-2', 'skin-5' ]
                ],
            ]
        );

        $this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'product_add_cart_typography',
				'selector'          => '{{WRAPPER}} .pp-woocommerce .pp-woo-products-summary-wrap .button, {{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-action-item-wrap',
				'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
				'condition'         => [
					'show_add_cart' => 'yes',
					'_skin'			=> [ 'skin-1', 'skin-3', 'skin-4' ]
				],
			]
		);

		$this->add_responsive_control(
			'product_add_cart_spacing',
			[
				'label'             => __( 'Spacing', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-woocommerce .pp-woo-products-summary-wrap .button' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'         => [
					'show_add_cart' => 'yes',
					'_skin'			=> 'skin-1'
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register Pagination Controls.
	 *
	 * @access protected
	 */
	protected function register_content_pagination_controls() {

		$this->start_controls_section(
			'section_pagination_field',
			[
				'label'             => __( 'Pagination', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_CONTENT,
				'condition'         => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'grid',
				],
			]
		);

        $this->add_control(
            'pagination_type',
            [
                'label'             => __( 'Type', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'default'           => '',
                'options'           => [
                    ''              => __( 'None', 'powerpack' ),
                    'numbers'       => __( 'Numbers', 'powerpack' ),
                    'numbers_arrow' => __( 'Numbers + Pre/Next Arrow', 'powerpack' ),
                ],
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                ],
            ]
        );

        $this->add_control(
            'pagination_prev_label',
            [
                'label'             => __( 'Previous Label', 'powerpack' ),
                'default'           => __( '←', 'powerpack' ),
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type'      => 'numbers_arrow',
                ],
            ]
        );

        $this->add_control(
            'pagination_next_label',
            [
                'label'             => __( 'Next Label', 'powerpack' ),
                'default'           => __( '→', 'powerpack' ),
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type'      => 'numbers_arrow',
                ],
            ]
        );

		$this->end_controls_section();
	}

	/**
	 * Register Product Badge Controls.
	 *
	 * @access protected
	 */
	protected function register_content_product_badges_controls() {

		$this->start_controls_section(
			'section_content_product_badge',
			[
				'label'             => __( 'Product Badges', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_CONTENT,
			]
		);

        $this->add_control(
            'sale_badge_heading',
            [
                'label'             => __( 'Sale', 'powerpack' ),
                'type'              => Controls_Manager::HEADING,
            ]
        );
        
        $this->add_control(
            'sale_badge_position',
            [
                'label'             => __( 'Position', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'options'           => [
                    ''				=> __( 'None', 'powerpack' ),
                    'left' 			=> __( 'Left', 'powerpack' ),
                    'right' 		=> __( 'Right', 'powerpack' ),
                ],
                'default'           => 'left',
            ]
        );

        $this->add_control(
            'sale_badge_custom_text',
            [
                'label'             => __( 'Custom Text', 'powerpack' ),
                'type'              => Controls_Manager::TEXT,
                'default'           => '',
                'description'       => __( 'Show Sale % Value ( [value] Autocalculated offer value will replace this ).', 'powerpack' ),
                'condition'         => [
                    'sale_badge_position!' => '',
                ],
            ]
        );

        $this->add_control(
            'featured_badge_heading',
            [
                'label'             => __( 'Featured', 'powerpack' ),
                'type'              => Controls_Manager::HEADING,
				'separator'         => 'before',
            ]
        );
        
        $this->add_control(
            'featured_badge_position',
            [
                'label'             => __( 'Position', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'options'           => [
                    ''				=> __( 'None', 'powerpack' ),
                    'left' 			=> __( 'Left', 'powerpack' ),
                    'right' 		=> __( 'Right', 'powerpack' ),
                ],
                'default'           => '',
            ]
        );

        $this->add_control(
            'featured_badge_custom_text',
            [
                'label'             => __( 'Custom Text', 'powerpack' ),
                'type'              => Controls_Manager::TEXT,
                'default'           => '',
                'condition'         => [
                    'featured_badge_position!' => '',
                ],
            ]
        );

        $this->add_control(
            'top_rating_badge_heading',
            [
                'label'             => __( 'Top Rated', 'powerpack' ),
                'type'              => Controls_Manager::HEADING,
				'separator'         => 'before',
            ]
        );
        
        $this->add_control(
            'top_rating_badge_position',
            [
                'label'             => __( 'Position', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'options'           => [
                    ''				=> __( 'None', 'powerpack' ),
                    'left' 			=> __( 'Left', 'powerpack' ),
                    'right' 		=> __( 'Right', 'powerpack' ),
                ],
                'default'           => '',
            ]
        );

        $this->add_control(
            'top_rating_badge_custom_text',
            [
                'label'             => __( 'Custom Text', 'powerpack' ),
                'type'              => Controls_Manager::TEXT,
                'default'           => '',
                'condition'         => [
                    'top_rating_badge_position!' => '',
                ],
            ]
        );

        $this->add_control(
            'number_of_ratings',
            [
                'label'             => __( 'Rating', 'powerpack' ),
                'type'              => Controls_Manager::TEXT,
                'default'           => '4',
                'description'       => __( 'Show badge according to count of total rating greater than rating.', 'powerpack' ),
                'condition'         => [
                    'top_rating_badge_position!' => '',
                ],
            ]
        );

        $this->add_control(
            'best_selling_badge_heading',
            [
                'label'             => __( 'Best Selling', 'powerpack' ),
                'type'              => Controls_Manager::HEADING,
				'separator'         => 'before',
            ]
        );
        
        $this->add_control(
            'best_selling_badge_position',
            [
                'label'             => __( 'Position', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'options'           => [
                    ''				=> __( 'None', 'powerpack' ),
                    'left' 			=> __( 'Left', 'powerpack' ),
                    'right' 		=> __( 'Right', 'powerpack' ),
                ],
                'default'           => '',
            ]
        );

        $this->add_control(
            'best_selling_badge_custom_text',
            [
                'label'             => __( 'Custom Text', 'powerpack' ),
                'type'              => Controls_Manager::TEXT,
                'default'           => '',
                'condition'         => [
                    'best_selling_badge_position!' => '',
                ],
            ]
        );

        $this->add_control(
            'number_of_sales',
            [
                'label'             => __( 'Number of Sales', 'powerpack' ),
                'type'              => Controls_Manager::TEXT,
                'default'           => '1',
                'description'       => __( 'Minimum number of sales.', 'powerpack' ),
                'condition'         => [
                    'best_selling_badge_position!' => '',
                ],
            ]
        );

		$this->end_controls_section();
	}

	/**
	 * Register style Product Badges Controls.
	 *
	 * @access protected
	 */
	protected function register_style_product_badges_controls() {

		$this->start_controls_section(
			'section_style_product_badges',
			[
				'label'             => __( 'Product Badges', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_STYLE,
			]
		);
        
        $this->add_responsive_control(
            'product_badge_margin',
            [
                'label'             => __( 'Margin', 'powerpack' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', '%' ],
                'default'           => [
                    'top'    => '10',
                    'bottom' => '10',
                    'left'   => '10',
                    'right'  => '10',
                    'unit'   => 'px',
                ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-badge-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'sale_badge_style_heading',
            [
                'label'             => __( 'Sale', 'powerpack' ),
                'type'              => Controls_Manager::HEADING,
                'separator'         => 'before',
                'condition'         => [
                    'sale_badge_position!' => '',
                ],
            ]
        );

        $this->add_control(
            'sale_badge_style',
            [
                'label'             => __( 'Style', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'options'           => [
                    'circle' => __( 'Circle', 'powerpack' ),
                    'square' => __( 'Square', 'powerpack' ),
                    'ribbon' => __( 'Ribbon', 'powerpack' ),
                    'custom' => __( 'Custom', 'powerpack' ),
                ],
                'default'           => 'custom',
                'condition'         => [
                    'sale_badge_position!' => '',
                ],
                'prefix_class'      => 'pp-sale-badge-',
            ]
        );

        $this->add_control(
            'sale_badge_color',
            [
                'label'             => __( 'Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-sale-badge' => 'color: {{VALUE}};',
                ],
                'condition'         => [
                    'sale_badge_position!' => '',
                ],
            ]
        );

        $this->add_control(
            'sale_badge_bg_color',
            [
                'label'             => __( 'Background Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-sale-badge' => 'background-color: {{VALUE}};',
                ],
                'condition'         => [
                    'sale_badge_position!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'              => 'sale_badge_typography',
                'scheme'            => Scheme_Typography::TYPOGRAPHY_3,
                'selector'          => '{{WRAPPER}} .pp-woocommerce .pp-sale-badge',
                'condition'         => [
                    'sale_badge_position!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'sale_badge_size',
            [
                'label'             => __( 'Size', 'powerpack' ),
                'type'              => Controls_Manager::SLIDER,
                'size_units'        => [ 'px', 'em' ],
                'range'             => [
                    'px' => [
                        'min' => 20,
                        'max' => 200,
                    ],
                    'em' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                ],
                'default'           => [
                    'size' => 2,
                    'unit' => 'em',
                ],
                'condition'         => [
                    'sale_badge_position!' => '',
                    'sale_badge_style' => ['circle', 'square', 'custom'],
                ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-sale-badge' => 'min-height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',

                ],
            ]
        );

        $this->add_responsive_control(
            'sale_badge_radius',
            [
                'label'             => __( 'Rounded Corners', 'powerpack' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', '%' ],
                'default'           => [
                    'top'    => '',
                    'bottom' => '',
                    'left'   => '',
                    'right'  => '',
                    'unit'   => 'px',
                ],
                'condition'         => [
                    'sale_badge_position!' => '',
                    'sale_badge_style' => 'custom',

                ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-sale-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'sale_badge_padding',
            [
                'label'             => __( 'Padding', 'powerpack' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', 'em', '%' ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-sale-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default'           => [
                    'top'      => '2',
                    'bottom'   => '2',
                    'left'     => '10',
                    'right'    => '10',
                    'unit'     => 'px',
                    'isLinked' => false,
                ],
                'condition'         => [
                    'sale_badge_position!' => '',
                    'sale_badge_style' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'featured_badge_style_heading',
            [
                'label'             => __( 'Featured', 'powerpack' ),
                'type'              => Controls_Manager::HEADING,
                'separator'         => 'before',
                'condition'         => [
                    'featured_badge_position!' => '',
                ],
            ]
        );

        $this->add_control(
            'featured_badge_style',
            [
                'label'             => __( 'Style', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'options'           => [
                    'circle' => __( 'Circle', 'powerpack' ),
                    'square' => __( 'Square', 'powerpack' ),
                    'ribbon' => __( 'Ribbon', 'powerpack' ),
                    'custom' => __( 'Custom', 'powerpack' ),
                ],
                'default'           => 'custom',
                'condition'         => [
                    'featured_badge_position!' => '',
                ],
                'prefix_class'      => 'pp-featured-badge-',
            ]
        );

        $this->add_control(
            'featured_badge_color',
            [
                'label'             => __( 'Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-featured-badge' => 'color: {{VALUE}};',
                ],
                'condition'         => [
                    'featured_badge_position!' => '',
                ],
            ]
        );

        $this->add_control(
            'featured_badge_bg_color',
            [
                'label'             => __( 'Background Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-featured-badge' => 'background-color: {{VALUE}};',
                ],
                'condition'         => [
                    'featured_badge_position!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'              => 'featured_badge_typography',
                'scheme'            => Scheme_Typography::TYPOGRAPHY_3,
                'selector'          => '{{WRAPPER}} .pp-woocommerce .pp-featured-badge',
                'condition'         => [
                    'featured_badge_position!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'featured_badge_size',
            [
                'label'             => __( 'Size', 'powerpack' ),
                'type'              => Controls_Manager::SLIDER,
                'size_units'        => [ 'px', 'em' ],
                'range'             => [
                    'px' => [
                        'min' => 20,
                        'max' => 200,
                    ],
                    'em' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                ],
                'default'           => [
                    'size' => 2,
                    'unit' => 'em',
                ],
                'condition'         => [
                    'featured_badge_position!' => '',
                    'featured_badge_style' => ['circle', 'square', 'custom'],
                ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-featured-badge' => 'min-height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',

                ],
            ]
        );

        $this->add_responsive_control(
            'featured_badge_radius',
            [
                'label'             => __( 'Rounded Corners', 'powerpack' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', '%' ],
                'default'           => [
                    'top'    => '',
                    'bottom' => '',
                    'left'   => '',
                    'right'  => '',
                    'unit'   => 'px',
                ],
                'condition'         => [
                    'featured_badge_position!' => '',
                    'featured_badge_style' => 'custom',

                ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-featured-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'featured_badge_padding',
            [
                'label'             => __( 'Padding', 'powerpack' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', 'em', '%' ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-featured-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default'           => [
                    'top'      => '2',
                    'bottom'   => '2',
                    'left'     => '10',
                    'right'    => '10',
                    'unit'     => 'px',
                    'isLinked' => false,
                ],
                'condition'         => [
                    'featured_badge_position!' => '',
                    'featured_badge_style' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'top_rating_badge_style_heading',
            [
                'label'             => __( 'Top Rated', 'powerpack' ),
                'type'              => Controls_Manager::HEADING,
                'separator'         => 'before',
                'condition'         => [
                    'top_rating_badge_position!' => '',
                ],
            ]
        );

        $this->add_control(
            'top_rating_badge_style',
            [
                'label'             => __( 'Style', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'options'           => [
                    'circle' => __( 'Circle', 'powerpack' ),
                    'square' => __( 'Square', 'powerpack' ),
                    'ribbon' => __( 'Ribbon', 'powerpack' ),
                    'custom' => __( 'Custom', 'powerpack' ),
                ],
                'default'           => 'custom',
                'condition'         => [
                    'top_rating_badge_position!' => '',
                ],
                'prefix_class'      => 'pp-top-rated-badge-',
            ]
        );

        $this->add_control(
            'top_rating_badge_color',
            [
                'label'             => __( 'Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-top-rated-badge' => 'color: {{VALUE}};',
                ],
                'condition'         => [
                    'top_rating_badge_position!' => '',
                ],
            ]
        );

        $this->add_control(
            'top_rating_badge_bg_color',
            [
                'label'             => __( 'Background Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-top-rated-badge' => 'background-color: {{VALUE}};',
                ],
                'condition'         => [
                    'top_rating_badge_position!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'              => 'top_rating_badge_typography',
                'scheme'            => Scheme_Typography::TYPOGRAPHY_3,
                'selector'          => '{{WRAPPER}} .pp-woocommerce .pp-top-rated-badge',
                'condition'         => [
                    'top_rating_badge_position!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'top_rating_badge_size',
            [
                'label'             => __( 'Size', 'powerpack' ),
                'type'              => Controls_Manager::SLIDER,
                'size_units'        => [ 'px', 'em' ],
                'range'             => [
                    'px' => [
                        'min' => 20,
                        'max' => 200,
                    ],
                    'em' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                ],
                'default'           => [
                    'size' => 2,
                    'unit' => 'em',
                ],
                'condition'         => [
                    'top_rating_badge_position!' => '',
                    'top_rating_badge_style' => ['circle', 'square', 'custom'],
                ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-top-rated-badge' => 'min-height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',

                ],
            ]
        );

        $this->add_responsive_control(
            'top_rating_badge_radius',
            [
                'label'             => __( 'Rounded Corners', 'powerpack' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', '%' ],
                'default'           => [
                    'top'    => '',
                    'bottom' => '',
                    'left'   => '',
                    'right'  => '',
                    'unit'   => 'px',
                ],
                'condition'         => [
                    'top_rating_badge_position!' => '',
                    'top_rating_badge_style' => 'custom',

                ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-top-rated-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'top_rating_badge_padding',
            [
                'label'             => __( 'Padding', 'powerpack' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', 'em', '%' ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-top-rated-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default'           => [
                    'top'      => '2',
                    'bottom'   => '2',
                    'left'     => '10',
                    'right'    => '10',
                    'unit'     => 'px',
                    'isLinked' => false,
                ],
                'condition'         => [
                    'top_rating_badge_position!' => '',
                    'top_rating_badge_style' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'best_selling_badge_style_heading',
            [
                'label'             => __( 'Best Selling', 'powerpack' ),
                'type'              => Controls_Manager::HEADING,
                'separator'         => 'before',
                'condition'         => [
                    'best_selling_badge_position!' => '',
                ],
            ]
        );

        $this->add_control(
            'best_selling_badge_style',
            [
                'label'             => __( 'Style', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'options'           => [
                    'circle' => __( 'Circle', 'powerpack' ),
                    'square' => __( 'Square', 'powerpack' ),
                    'ribbon' => __( 'Ribbon', 'powerpack' ),
                    'custom' => __( 'Custom', 'powerpack' ),
                ],
                'default'           => 'custom',
                'condition'         => [
                    'best_selling_badge_position!' => '',
                ],
                'prefix_class'      => 'pp-best-selling-badge-',
            ]
        );

        $this->add_control(
            'best_selling_badge_color',
            [
                'label'             => __( 'Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-best-selling-badge' => 'color: {{VALUE}};',
                ],
                'condition'         => [
                    'best_selling_badge_position!' => '',
                ],
            ]
        );

        $this->add_control(
            'best_selling_badge_bg_color',
            [
                'label'             => __( 'Background Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-best-selling-badge' => 'background-color: {{VALUE}};',
                ],
                'condition'         => [
                    'best_selling_badge_position!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'              => 'best_selling_badge_typography',
                'scheme'            => Scheme_Typography::TYPOGRAPHY_3,
                'selector'          => '{{WRAPPER}} .pp-woocommerce .pp-best-selling-badge',
                'condition'         => [
                    'best_selling_badge_position!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'best_selling_badge_size',
            [
                'label'             => __( 'Size', 'powerpack' ),
                'type'              => Controls_Manager::SLIDER,
                'size_units'        => [ 'px', 'em' ],
                'range'             => [
                    'px' => [
                        'min' => 20,
                        'max' => 200,
                    ],
                    'em' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                ],
                'default'           => [
                    'size' => 2,
                    'unit' => 'em',
                ],
                'condition'         => [
                    'best_selling_badge_position!' => '',
                    'best_selling_badge_style' => ['circle', 'square', 'custom'],
                ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-best-selling-badge' => 'min-height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',

                ],
            ]
        );

        $this->add_responsive_control(
            'best_selling_badge_radius',
            [
                'label'             => __( 'Rounded Corners', 'powerpack' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', '%' ],
                'default'           => [
                    'top'    => '',
                    'bottom' => '',
                    'left'   => '',
                    'right'  => '',
                    'unit'   => 'px',
                ],
                'condition'         => [
                    'best_selling_badge_position!' => '',
                    'best_selling_badge_style' => 'custom',

                ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-best-selling-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'best_selling_badge_padding',
            [
                'label'             => __( 'Padding', 'powerpack' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', 'em', '%' ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-best-selling-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'default'           => [
                    'top'      => '2',
                    'bottom'   => '2',
                    'left'     => '10',
                    'right'    => '10',
                    'unit'     => 'px',
                    'isLinked' => false,
                ],
                'condition'         => [
                    'best_selling_badge_position!' => '',
                    'best_selling_badge_style' => 'custom',
                ],
            ]
        );
        
		$this->end_controls_section();
	}

	/**
	 * Register Image Controls.
	 *
	 * @access protected
	 */
	protected function register_image_controls() {
		$this->start_controls_section(
			'section_design_image',
			[
				'label'             => __( 'Image', 'powerpack' ),
			]
		);

        $this->add_control(
            'link_image',
            [
                'label'             => __( 'Link to Product', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'label_on'          => __( 'Yes', 'powerpack' ),
                'label_off'         => __( 'No', 'powerpack' ),
                'return_value'      => 'yes',
                'default'           => 'yes',
            ]
        );
		
        $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'thumbnail',
				'label'                 => __( 'Image Size', 'powerpack' ),
				'default'               => 'woocommerce_thumbnail',
			]
		);

        $this->add_control(
            'products_hover_style',
            [
                'label'             => __( 'Image Hover Effect', 'powerpack' ),
                'type'              => Controls_Manager::SELECT,
                'default'           => '',
                'options'           => [
                    ''     => __( 'None', 'powerpack' ),
                    'swap' => __( 'Swap Images', 'powerpack' ),
                    'zoom' => __( 'Zoom Image', 'powerpack' ),
                ],
            ]
        );

		$this->end_controls_section();
	}

	/**
	 * Register Quick View Controls.
	 *
	 * @access protected
	 */
	protected function register_quick_view_controls() {

		$this->start_controls_section(
			'section_content_quick_view',
			[
				'label'             => __( 'Quick View', 'powerpack' ),
			]
		);
        
        $this->add_control(
			'quick_view_type',
			[
				'label'             => __( 'Quick View', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'label_on'          => __( 'Show', 'powerpack' ),
                'label_off'         => __( 'Hide', 'powerpack' ),
                'return_value'      => 'yes',
                'default'           => '',
            ]
        );

		$this->end_controls_section();
	}

	/**
	 * Register Quick View Style Controls.
	 *
	 * @access protected
	 */
	protected function register_quick_view_style_controls() {

		$this->start_controls_section(
			'section_content_quick_view_style',
			[
				'label'             => __( 'Quick View', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_STYLE,
				'condition'         => [
					'quick_view_type' => 'yes',
				],
			]
		);

        $this->add_control(
            'quick_view_color',
            [
                'label'             => __( 'Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-quick-view-btn span, {{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-quick-view-btn' => 'color: {{VALUE}};',
                ],
                'condition'         => [
                    'quick_view_type'   => 'yes',
                    '_skin'             => [ 'skin-1', 'skin-2', 'skin-5' ]
                ],
            ]
        );

        $this->add_control(
            'quick_view_bg_color',
            [
                'label'             => __( 'Background Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-quick-view-btn, {{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-quick-view-btn' => 'background-color: {{VALUE}};',
                ],
                'condition'         => [
                    'quick_view_type'   => 'yes',
                    '_skin'             => [ 'skin-1', 'skin-2', 'skin-5' ]
                ],
            ]
        );

        $this->add_control(
            'product_quick_view_style',
            [
                'label'             => __( '', 'powerpack' ),
                'type'              => Controls_Manager::HEADING,
                'condition'         => [
                    'quick_view_type'   => 'yes',
                    '_skin'             => [ 'skin-3' ,'skin-4' ]	
                ],
            ]
        );

        $this->add_responsive_control(
            'product_quick_view_padding',
            [
                'label'             => __( 'Padding', 'powerpack' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', 'em', '%' ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-quick-view-btn .pp-action-item-wrap, {{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-quick-view-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'         => [
                    'quick_view_type'   => 'yes',
                    '_skin'             => [ 'skin-3' ]	
                ],
            ]
        );

		$this->start_controls_tabs( 'product_quick_view_tabs_style' );

        $this->start_controls_tab(
            'product_quick_view_normal',
            [
                'label'             => __( 'Normal', 'powerpack' ),
                'condition'         => [
                    'quick_view_type'   => 'yes',
                    '_skin'             => [ 'skin-3' ,'skin-4' ]	
                ],
            ]
        );

        $this->add_control(
            'product_quick_view_color',
            [
                'label'             => __( 'Text Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-quick-view-btn .pp-action-item-wrap,
                    {{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-quick-view-btn .pp-action-item-wrap span, 
                    {{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-quick-view-btn' => 'color: {{VALUE}};',
                ],
                'condition'         => [
                    'quick_view_type'   => 'yes',
                    '_skin'             => [ 'skin-3' ,'skin-4' ]	
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'              => 'product_quick_view_background_color',
                'label'             => __( 'Background Color', 'powerpack' ),
                'types'             => [ 'classic', 'gradient' ],
                'selector'          => '{{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-quick-view-btn .pp-action-item-wrap, {{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-quick-view-btn',
                'condition'         => [
                    'quick_view_type'   => 'yes',
                    '_skin'             => [ 'skin-3' ,'skin-4' ]	
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'product_quick_view_hover',
            [
                'label'             => __( 'Hover', 'powerpack' ),
                'condition'         => [
                    'quick_view_type'   => 'yes',
                    '_skin'             => [ 'skin-3' ,'skin-4' ]	
                ],
            ]
        );

        $this->add_control(
            'product_quick_view_hover_color',
            [
                'label'             => __( 'Text Hover Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-quick-view-btn .pp-action-item-wrap:hover,
                    {{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-quick-view-btn .pp-action-item-wrap:hover span, 
                    {{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-quick-view-btn:hover' => 'color: {{VALUE}};',
                ],
                'condition'         => [
                    'quick_view_type'   => 'yes',
                    '_skin'             => [ 'skin-3' ,'skin-4' ]	
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'              => 'product_quick_view_background_hover_color',
                'label'             => __( 'Background Hover Color', 'powerpack' ),
                'types'             => [ 'classic', 'gradient' ],
                'selector'          => '{{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-quick-view-btn .pp-action-item-wrap:hover, {{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-quick-view-btn:hover',
                'condition'         => [
                    'quick_view_type'   => 'yes',
                    '_skin'             => [ 'skin-3' ,'skin-4' ]	
                ],
            ]
        );

        $this->end_controls_tab();

		$this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'              => 'quick_view_typography',
                'scheme'            => Scheme_Typography::TYPOGRAPHY_3,
                'selector'          => '{{WRAPPER}} .pp-woocommerce .pp-quick-view-btn, {{WRAPPER}} .pp-woocommerce .pp-product-actions .pp-quick-view-btn .pp-action-item-wrap',
                'condition'         => [
                    'quick_view_type'   => 'yes',
                    '_skin'             => [ 'skin-1', 'skin-3' ,'skin-4' ]	
                ],
            ]
        );

		$this->end_controls_section();
	}

	/**
	 * Register Carousel Controls.
	 *
	 * @access protected
	 */
	protected function register_content_slider_controls() {
		$this->start_controls_section(
			'section_carousel_options',
			[
				'label'             => __( 'Carousel Settings', 'powerpack' ),
				'type'              => Controls_Manager::SECTION,
				'condition'         => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'slider',
				],
			]
		);

		$this->add_control(
			'transition_speed',
			[
                'label'                 => __( 'Transition Speed', 'powerpack' ),
                'description'           => __( 'Duration of transition between slides (in ms)', 'powerpack' ),
				'type'              => Controls_Manager::NUMBER,
				'default'           => 400,
				'condition'         => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'slider',
				],
			]
		);
        
        $this->add_control(
            'autoplay',
            [
                'label'                 => __( 'Autoplay', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'separator'             => 'before',
                'frontend_available'    => true,
				'condition'             => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'slider',
				],
            ]
        );
        
        $this->add_control(
            'autoplay_speed',
            [
                'label'                 => __( 'Autoplay Speed', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => 2000,
                'min'                   => 500,
                'max'                   => 5000,
                'step'                  => 1,
                'frontend_available'    => true,
				'condition'             => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'slider',
					'autoplay'             => 'yes',
				],
            ]
        );
        
        $this->add_control(
            'loop',
            [
                'label'                 => __( 'Infinite Loop', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'frontend_available'    => true,
				'condition'             => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'slider',
				],
            ]
        );
        
        $this->add_control(
            'grab_cursor',
            [
                'label'                 => __( 'Grab Cursor', 'powerpack' ),
                'description'           => __( 'Shows grab cursor when you hover over the slider', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'separator'             => 'before',
                'frontend_available'    => true,
				'condition'             => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'slider',
				],
            ]
        );
		$this->add_control(
			'pause_on_hover',
			[
				'label'             => __( 'Pause on Hover', 'powerpack' ),
				'type'              => Controls_Manager::SWITCHER,
				'return_value'      => 'yes',
				'default'           => 'yes',
				'condition'         => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'slider',
					'autoplay'             => 'yes',
				],
			]
		);

		$this->add_control(
			'infinite',
			[
				'label'             => __( 'Infinite Loop', 'powerpack' ),
				'type'              => Controls_Manager::SWITCHER,
				'return_value'      => 'yes',
				'default'           => 'yes',
				'condition'         => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'slider',
				],
			]
		);
        
        $this->add_control(
            'navigation_heading',
            [
                'label'                 => __( 'Navigation', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
				'condition'             => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'slider',
				],
            ]
        );
        
        $this->add_control(
            'arrows',
            [
                'label'                 => __( 'Arrows', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'frontend_available'    => true,
				'condition'             => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'slider',
				],
            ]
        );
        
        $this->add_control(
            'carousel_pagination',
            [
                'label'                 => __( 'Dots', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'frontend_available'    => true,
				'condition'             => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'slider',
				],
            ]
        );

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Help Docs
	 *
	 * @since 1.4.8
	 * @access protected
	 */
	protected function register_content_help_docs() {

		$help_docs = PP_Config::get_widget_help_links('Woo_Products');

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
	protected function register_style_layout_controls() {
		$this->start_controls_section(
			'section_design_layout',
			[
				'label'             => __( 'Layout', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'column_gap',
			[
				'label'             => __( 'Columns Gap', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'default'           => [
					'size' => 20,
				],
				'range'             => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-woocommerce li.product' => 'padding-right: calc( {{SIZE}}{{UNIT}}/2 ); padding-left: calc( {{SIZE}}{{UNIT}}/2 );',
					'{{WRAPPER}} .pp-woocommerce ul.products' => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'             => __( 'Rows Gap', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'default'           => [
					'size' => 35,
				],
				'range'             => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-woocommerce li.product' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'         => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'grid',
				],
			]
		);

		$this->add_control(
			'product_box_bg_color',
			[
				'label'             => __( 'Box Background Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'separator'         => 'before',
				'selectors'         => [
					'{{WRAPPER}} .pp-woo-product-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'product_box_padding',
			[
				'label'             => __( 'Box Padding', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-woo-product-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'product_border',
				'label'             => __( 'Border', 'powerpack' ),
				'selector'          => '{{WRAPPER}} .pp-woo-product-wrapper',
				'separator'         => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'product_box_shadow',
				'selector'          => '{{WRAPPER}} .pp-woo-product-wrapper',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register Lightbox Style Controls.
	 *
	 * @access protected
	 */
	protected function register_lightbox_style_controls() {
		$this->start_controls_section(
			'section_content_lightbox_style',
			[
				'label'             => __( 'Lightbox', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
            'lightbox_overlay_color',
            [
                'label'             => __( 'Overlay Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '.pp-quick-view-{{ID}} .pp-quick-view-bg' => 'background-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_control(
            'lightbox_bg_color',
            [
                'label'             => __( 'Background Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '.pp-quick-view-{{ID}} #pp-quick-view-modal .pp-lightbox-content' => 'background-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_responsive_control(
			'lightbox_padding',
			[
				'label'             => __( 'Padding', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'.pp-quick-view-{{ID}} .pp-lightbox-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'lightbox_border',
				'label'             => __( 'Border', 'powerpack' ),
				'placeholder'       => '1px',
				'default'           => '1px',
				'separator'         => 'before',
				'selector'          => '.pp-quick-view-{{ID}} .pp-lightbox-content',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'lightbox_box_shadow',
				'separator'         => 'before',
				'selector'          => '.pp-quick-view-{{ID}} .pp-lightbox-content',
			]
		);

		$this->add_control(
			'close_icon_size',
			[
				'label'             => __( 'Close Icon Size', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min' => 10,
						'max' => 50,
					],
				],
				'selectors'         => [
					'.pp-quick-view-{{ID}} #pp-quick-view-close, .pp-quick-view-{{ID}} #pp-quick-view-close:before, .pp-quick-view-{{ID}} #pp-quick-view-close:after' => 'width: {{SIZE}}{{UNIT}};',
					'.pp-quick-view-{{ID}} #pp-quick-view-close' => 'height: {{SIZE}}{{UNIT}};',
				],
				'separator'         => 'before',
			]
		);

		$this->add_control(
			'close_icon_thickness',
			[
				'label'             => __( 'Close Icon Thickness', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min' => 1,
						'max' => 5,
					],
				],
				'selectors'         => [
					'.pp-quick-view-{{ID}} #pp-quick-view-close:before, .pp-quick-view-{{ID}} #pp-quick-view-close:after' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
            'close_icon_color',
            [
                'label'             => __( 'Close Icon Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '',
                'selectors'         => [
                    '.pp-quick-view-{{ID}} #pp-quick-view-close:before, .pp-quick-view-{{ID}} #pp-quick-view-close:after' => 'background-color: {{VALUE}}',
                ],
            ]
        );

		$this->end_controls_section();
	}

	/**
	 * Register Pagination Controls.
	 *
	 * @access protected
	 */
	protected function register_style_pagination_controls() {

		$this->start_controls_section(
			'section_design_pagination',
			[
				'label'             => __( 'Pagination', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_STYLE,
				'condition'         => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type' => 'grid',
					'pagination_type!'     => '',
				],
			]
		);

        $this->add_responsive_control(
            'pagination_align',
            [
                'label'             => __( 'Alignment', 'powerpack' ),
                'type'              => Controls_Manager::CHOOSE,
                'options'           => [
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
                'default'           => 'center',
                'prefix_class'      => 'pp-woo-pagination%s-align-',
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type!'     => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'              => 'pagination_typography',
                'selector'          => '{{WRAPPER}} nav.pp-woocommerce-pagination ul li > .page-numbers',
                'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type!'     => '',
                ],

            ]
        );

		$this->start_controls_tabs( 'pagination_tabs_style' );

        $this->start_controls_tab(
            'pagination_normal',
            [
                'label'             => __( 'Normal', 'powerpack' ),
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type!'     => '',
                ],
            ]
        );

        $this->add_control(
            'pagination_color',
            [
                'label'             => __( 'Text Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} nav.pp-woocommerce-pagination ul li > .page-numbers' => 'color: {{VALUE}};',
                ],
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type!'     => '',
                ],
            ]
        );

        $this->add_control(
            'pagination_background_color',
            [
                'label'             => __( 'Background Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} nav.pp-woocommerce-pagination ul li > .page-numbers' => 'background-color: {{VALUE}};',
                ],
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type!'     => '',
                ],
            ]
        );

        $this->add_control(
            'pagination_border_color',
            [
                'label'             => __( 'Border Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} nav.pp-woocommerce-pagination ul li .page-numbers, {{WRAPPER}} nav.pp-woocommerce-pagination ul li span.current' => 'border-color: {{VALUE}};',
                ],
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type!'     => '',
                ],
            ]
        );

		$this->add_responsive_control(
			'pagination_border_width',
			[
				'label'             => __( 'Border Width', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors'         => [
					'{{WRAPPER}} nav.pp-woocommerce-pagination ul li .page-numbers' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition'         => [
					'_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type!'     => '',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'pagination_hover',
            [
                'label'             => __( 'Hover', 'powerpack' ),
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type!'     => '',
                ],
            ]
        );

        $this->add_control(
            'pagination_hover_color',
            [
                'label'             => __( 'Text Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '#ffffff',
                'selectors'         => [
                    '{{WRAPPER}} nav.pp-woocommerce-pagination ul li .page-numbers:focus, {{WRAPPER}} nav.pp-woocommerce-pagination ul li .page-numbers:hover' => 'color: {{VALUE}};',
                ],
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type!'     => '',
                ],
            ]
        );

        $this->add_control(
            'pagination_background_hover_color',
            [
                'label'             => __( 'Background Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} nav.pp-woocommerce-pagination ul li .page-numbers:focus, {{WRAPPER}} nav.pp-woocommerce-pagination ul li .page-numbers:hover' => 'background-color: {{VALUE}};',
                ],
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type!'     => '',
                ],
            ]
        );

        $this->add_control(
            'pagination_border_hover_color',
            [
                'label'             => __( 'Border Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} nav.pp-woocommerce-pagination ul li .page-numbers:focus, {{WRAPPER}} nav.pp-woocommerce-pagination ul li .page-numbers:hover' => 'border-color: {{VALUE}};',
                ],
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type!'     => '',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'pagination_active',
            [
                'label'             => __( 'Active', 'powerpack' ),
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type!'     => '',
                ],
            ]
        );

        $this->add_control(
            'pagination_active_color',
            [
                'label'             => __( 'Text Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'default'           => '#ffffff',
                'selectors'         => [
                    '{{WRAPPER}} nav.pp-woocommerce-pagination ul li span.current' => 'color: {{VALUE}};',
                ],
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type!'     => '',
                ],
            ]
        );

        $this->add_control(
            'pagination_background_active_color',
            [
                'label'             => __( 'Background Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} nav.pp-woocommerce-pagination ul li span.current' => 'background-color: {{VALUE}};',
                ],
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type!'     => '',
                ],
            ]
        );

        $this->add_control(
            'pagination_border_active_color',
            [
                'label'             => __( 'Border Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} nav.pp-woocommerce-pagination ul li span.current' => 'border-color: {{VALUE}};',
                ],
                'condition'         => [
                    '_skin'                => [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
                    'products_layout_type' => 'grid',
                    'pagination_type!'     => '',
                ],
            ]
        );

        $this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Register Navigation Controls.
	 *
	 * @access protected
	 */
	protected function register_style_navigation_controls() {
		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'             => __( 'Navigation', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_STYLE,
				'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
				],
			]
		);

		$this->add_control(
			'heading_style_arrows',
			[
				'label'             => __( 'Arrows', 'powerpack' ),
				'type'              => Controls_Manager::HEADING,
				'separator'         => 'before',
				'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
				],
			]
		);

		$this->add_control(
			'arrows_position',
			[
				'label'             => __( 'Position', 'powerpack' ),
				'type'              => Controls_Manager::SELECT,
				'default'           => 'outside',
				'options'           => [
					'inside'  => __( 'Inside', 'powerpack' ),
					'outside' => __( 'Outside', 'powerpack' ),
				],
				'prefix_class'      => 'pp-woo-slider-arrow-',
				'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
				],
			]
		);

		$this->add_control(
			'arrows_style',
			[
				'label'             => __( 'Style', 'powerpack' ),
				'type'              => Controls_Manager::SELECT,
				'default'           => 'square',
				'options'           => [
					''       => __( 'Default', 'powerpack' ),
					'circle' => __( 'Circle', 'powerpack' ),
					'square' => __( 'Square', 'powerpack' ),
				],
				'prefix_class'      => 'pp-woo-slider-arrow-',
				'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_size',
			[
				'label'             => __( 'Size', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min' => 20,
						'max' => 60,
					],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-woo-products-slider .slick-slider .slick-arrow' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_spacing',
			[
				'label'             => __( 'Spacing', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'         => [
					'{{WRAPPER}}.pp-woo-slider-arrow-outside .pp-woo-products-slider .slick-prev' => 'left: calc( -{{SIZE}}{{UNIT}} + -25px );',
					'{{WRAPPER}}.pp-woo-slider-arrow-outside .pp-woo-products-slider .slick-next' => 'right: calc( -{{SIZE}}{{UNIT}} + -25px );',
				],
				'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
					'arrows_position'		=> 'outside',
				],
			]
		);

		$this->start_controls_tabs( 'arrow_tabs_style' );
        
        $this->start_controls_tab(
            'arrow_style_normal',
            [
                'label'             => __( 'Normal', 'powerpack' ),
                'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'arrows_color',
            [
                'label'             => __( 'Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woo-products-slider .slick-slider .slick-arrow' => 'color: {{VALUE}};',
                ],
                'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'arrows_bg_color',
            [
                'label'             => __( 'Background Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woo-products-slider .slick-slider .slick-arrow' => 'background-color: {{VALUE}};',
                ],
                'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
                    'arrows_style'			=> [ 'circle', 'square' ],
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'arrows_border',
				'label'             => __( 'Border', 'powerpack' ),
				'selector'          => '{{WRAPPER}} .pp-woo-products-slider .slick-slider .slick-arrow',
                'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
                    'arrows_style'			=> [ 'circle', 'square' ],
                ],
			]
		);

		$this->add_responsive_control(
			'arrows_padding',
			[
				'label'             => __( 'Padding', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-woo-products-slider .slick-slider .slick-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
                    'arrows_style'			=> [ 'circle', 'square' ],
                ],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'arrow_style_hover',
            [
                'label'             => __( 'Hover', 'powerpack' ),
                'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'arrows_hover_color',
            [
                'label'             => __( 'Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woo-products-slider .slick-slider .slick-arrow:hover' => 'color: {{VALUE}};',
                ],
                'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'arrows_hover_bg_color',
            [
                'label'             => __( 'Background Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woo-products-slider .slick-slider .slick-arrow:hover' => 'background-color: {{VALUE}};',
                ],
                'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
                    'arrows_style'			=> [ 'circle', 'square' ],
                ],
            ]
        );
        
        $this->add_control(
            'arrows_hover_border_color',
            [
                'label'             => __( 'Border Color', 'powerpack' ),
                'type'              => Controls_Manager::COLOR,
                'selectors'         => [
                    '{{WRAPPER}} .pp-woo-products-slider .slick-slider .slick-arrow:hover' => 'border-color: {{VALUE}};',
                ],
                'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
                    'arrows_style'			=> [ 'circle', 'square' ],
                ],
            ]
        );
        
        $this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'heading_style_dots',
			[
				'label'             => __( 'Dots', 'powerpack' ),
				'type'              => Controls_Manager::HEADING,
				'separator'         => 'before',
				'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'carousel_pagination'	=> 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_top_spacing',
			[
				'label'             => __( 'Top Spacing', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-woo-products-slider .slick-dots' => 'bottom: -{{SIZE}}{{UNIT}};',
				],
				'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'carousel_pagination'	=> 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dots_spacing',
			[
				'label'             => __( 'Spacing', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
                'default'           => [
                    'size' => 5,
                    'unit' => 'px',
                ],
				'selectors'         => [
					'{{WRAPPER}} .pp-woo-products-slider .slick-dots li' => 'margin-left: calc( {{SIZE}}{{UNIT}}/2 ); margin-right: calc( {{SIZE}}{{UNIT}}/2 );',
				],
				'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'carousel_pagination'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'dots_size',
			[
				'label'             => __( 'Size', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min' => 5,
						'max' => 20,
					],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-woo-products-slider .slick-dots li button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
				'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'carousel_pagination'	=> 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'dots_tabs_style' );
        
        $this->start_controls_tab(
            'dots_style_normal',
            [
                'label'             => __( 'Normal', 'powerpack' ),
                'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
                ],
            ]
        );

		$this->add_control(
			'dots_color',
			[
				'label'             => __( 'Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .pp-woo-products-slider .slick-dots li button' => 'background-color: {{VALUE}};',
				],
				'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'carousel_pagination'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'dots_border',
				'label'             => __( 'Border', 'powerpack' ),
				'selector'          => '{{WRAPPER}} .pp-woo-products-slider .slick-dots li',
                'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
                ],
			]
		);

        $this->add_responsive_control(
            'dots_border_radius',
            [
                'label'             => __( 'Rounded Corners', 'powerpack' ),
                'type'              => Controls_Manager::DIMENSIONS,
                'size_units'        => [ 'px', '%' ],
                'default'           => [
                    'top'    => '',
                    'bottom' => '',
                    'left'   => '',
                    'right'  => '',
                    'unit'   => 'px',
                ],
                'condition'         => [
                    'sale_badge_position!' => '',
                    'sale_badge_style' => 'custom',

                ],
                'selectors'         => [
                    '{{WRAPPER}} .pp-woo-products-slider .slick-dots li, {{WRAPPER}} .pp-woo-products-slider .slick-dots li button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'dots_box_shadow',
				'selector'          => '{{WRAPPER}} .pp-woo-products-slider .slick-dots li',
                'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
                ],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'dots_style_active',
            [
                'label'             => __( 'Active', 'powerpack' ),
                'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
                ],
            ]
        );

		$this->add_control(
			'dots_color_active',
			[
				'label'             => __( 'Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .pp-woo-products-slider .slick-dots li.slick-active button' => 'background-color: {{VALUE}};',
				],
				'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'carousel_pagination'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'dots_border_color_active',
			[
				'label'             => __( 'Border Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .pp-woo-products-slider .slick-dots li.slick-active' => 'border-color: {{VALUE}};',
				],
				'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'carousel_pagination'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'dots_box_shadow_active',
				'selector'          => '{{WRAPPER}} .pp-woo-products-slider .slick-dots li.slick-active',
                'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
                ],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'dots_style_hover',
            [
                'label'             => __( 'Hover', 'powerpack' ),
                'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
                ],
            ]
        );

		$this->add_control(
			'dots_color_hover',
			[
				'label'             => __( 'Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .pp-woo-products-slider .slick-dots li:hover button' => 'background-color: {{VALUE}};',
				],
				'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'carousel_pagination'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'dots_border_color_hover',
			[
				'label'             => __( 'Border Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .pp-woo-products-slider .slick-dots li:hover' => 'border-color: {{VALUE}};',
				],
				'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'carousel_pagination'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'dots_box_shadow_hover',
				'selector'          => '{{WRAPPER}} .pp-woo-products-slider .slick-dots li:hover',
                'condition'         => [
					'_skin'					=> [ 'skin-1', 'skin-2' , 'skin-3', 'skin-4', 'skin-5' ],
					'products_layout_type'	=> 'slider',
					'arrows'				=> 'yes',
                ],
			]
		);
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Get WooCommerce Product Categories.
	 *
	 * @access protected
	 */
	protected function get_product_categories() {

		$product_cat = array();

		$cat_args = array(
			'orderby'    => 'name',
			'order'      => 'asc',
			'hide_empty' => false,
		);

		$product_categories = get_terms( 'product_cat', $cat_args );

		if ( ! empty( $product_categories ) ) {

			foreach ( $product_categories as $key => $category ) {

				$product_cat[ $category->slug ] = $category->name;
			}
		}

		return $product_cat;
	}

	/**
	 * Get WooCommerce Product Tags.
	 *
	 * @access protected
	 */
	protected function get_product_tags() {

		$product_tag = array();

		$tag_args = array(
			'orderby'    => 'name',
			'order'      => 'asc',
			'hide_empty' => false,
		);

		$product_tag = get_terms( 'product_tag', $tag_args );

		if ( ! empty( $product_tag ) ) {

			foreach ( $product_tag as $key => $tag ) {

				$product_tag[ $tag->slug ] = $tag->name;
			}
		}

		return $product_tag;
	}

	/**
	 * Get query products based on settings.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access public
	 */
	public function query_posts() {

		$settings = $this->get_settings();
		
		if ( 'main' === $settings['source'] ) {

			global $wp_query;

			$main_query = clone $wp_query;

			$this->query = $main_query;

		} elseif ( 'related' === $settings['source'] ) {

			if ( is_product() ) {

				global $product;

				$product_id                  = $product->get_id();
				$product_visibility_term_ids = wc_get_product_visibility_term_ids();

				$query_args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'paged'          => 1,
					'post__not_in'   => array(),
				);

				if ( 'grid' === $settings['products_layout_type'] ) {

					if ( $settings['products_per_page'] > 0 ) {
						$query_args['posts_per_page'] = $settings['products_per_page'];
					}

					if ( '' !== $settings['pagination_type'] ) {

						$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : '1';

						if ( isset( $_POST['page_number'] ) && '' !== $_POST['page_number'] ) {
							$paged = $_POST['page_number'];
						}

						$query_args['paged'] = $paged;
					}
				} else {

					if ( $settings['slider_products_per_page'] > 0 ) {
						$query_args['posts_per_page'] = $settings['slider_products_per_page'];
					}
				}

				// Get current post categories and pass to filter.
				$product_cat = array();

				$product_categories = wp_get_post_terms( $product_id, 'product_cat' );

				if ( ! empty( $product_categories ) ) {

					foreach ( $product_categories as $key => $category ) {

						$product_cat[] = $category->slug;
					}
				}

				if ( ! empty( $product_cat ) ) {

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => $product_cat,
						'operator' => 'IN',
					);
				}

				// Exclude current product.
				$query_args['post__not_in'][] = $product_id;

				if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['outofstock'],
						'operator' => 'NOT IN',
					);
				}

				if ( ! empty( $product_visibility_term_ids['exclude-from-catalog'] ) ) {

					$query_args['tax_query'][] = array(
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['exclude-from-catalog'],
						'operator' => 'NOT IN',
					);
				}

				$this->query = new \WP_Query( $query_args );

			} else {

				$query_args = array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'paged'          => 1,
					'post__in'       => array( 0 ),
				);

				$this->query = new \WP_Query( $query_args );
			}
		} else {

			global $post;

			$query_args = [
				'post_type'      => 'product',
				'posts_per_page' => -1,
				'paged'          => 1,
				'post__not_in'   => array(),
			];

			if ( 'grid' === $settings['products_layout_type'] ) {

				if ( $settings['products_per_page'] > 0 ) {
					$query_args['posts_per_page'] = $settings['products_per_page'];
				}

				if ( '' !== $settings['pagination_type'] ) {

					$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : '1';

					$query_args['paged'] = $paged;
				}
			} else {

				if ( $settings['slider_products_per_page'] > 0 ) {
					$query_args['posts_per_page'] = $settings['slider_products_per_page'];
				}
			}

			// Default ordering args.
			$ordering_args = WC()->query->get_catalog_ordering_args( $settings['orderby'], $settings['order'] );

			$query_args['orderby'] = $ordering_args['orderby'];
			$query_args['order']   = $ordering_args['order'];

			if ( 'sale' === $settings['filter_by'] ) {

				$query_args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
			} elseif ( 'featured' === $settings['filter_by'] ) {

				$product_visibility_term_ids = wc_get_product_visibility_term_ids();

				$query_args['tax_query'][] = [
					'taxonomy' => 'product_visibility',
					'field'    => 'term_taxonomy_id',
					'terms'    => $product_visibility_term_ids['featured'],
				];
			} elseif ( 'top_rated' === $settings['filter_by'] ) {
				$query_args['meta_key'] = '_wc_average_rating';
				$query_args['orderby']  = 'meta_value_num';
				$query_args['meta_query']  = WC()->query->get_meta_query();
				$query_args['tax_query']  = WC()->query->get_tax_query();
			} elseif ( 'best_selling' === $settings['filter_by'] ) {
				$query_args['meta_key'] = 'total_sales';
				$query_args['order']    = 'DESC';
				$query_args['orderby']  = 'meta_value_num';
			}

			if ( 'custom' === $settings['source'] ) {

				if ( ! empty( $settings['category_filter'] ) ) {

					$cat_operator = $settings['category_filter_rule'];

					$query_args['tax_query'][] = [
						'taxonomy' => 'product_cat',
						'field'    => 'slug',
						'terms'    => $settings['category_filter'],
						'operator' => $cat_operator,
					];
				}

				if ( ! empty( $settings['tag_filter'] ) ) {

					$tag_operator = $settings['tag_filter_rule'];

					$query_args['tax_query'][] = [
						'taxonomy' => 'product_tag',
						'field'    => 'slug',
						'terms'    => $settings['tag_filter'],
						'operator' => $tag_operator,
					];
				}

				if ( 0 < $settings['offset'] ) {

					/**
					 * Offser break the pagination. Using WordPress's work around
					 *
					 * @see https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
					 */
					$query_args['offset_to_fix'] = $settings['offset'];
				}
			}

			if ( 'manual' === $settings['source'] ) {

				$manual_ids = $settings['query_manual_ids'];

				$query_args['post__in'] = $manual_ids;
			}

			if ( 'manual' !== $settings['source'] ) {

				if ( '' !== $settings['query_exclude_ids'] ) {

					$exclude_ids = $settings['query_exclude_ids'];

					$query_args['post__not_in'] = $exclude_ids;
				}

				if ( 'yes' === $settings['query_exclude_current'] ) {

					$query_args['post__not_in'][] = $post->ID;
				}
			}

			$this->query = new \WP_Query( $query_args );
		}
	}
}
