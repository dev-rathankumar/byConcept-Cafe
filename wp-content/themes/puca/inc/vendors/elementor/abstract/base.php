<?php
if (!defined('ABSPATH') || function_exists('Puca_Elementor_Widget_Base') ) {
    exit; // Exit if accessed directly.
}

use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;

abstract class Puca_Elementor_Widget_Base extends Elementor\Widget_Base {
	public function get_name_template() {
        return str_replace('tbay-', '', $this->get_name());
    }

    public function get_categories() {
        return [ 'puca-elements' ];
    }
    
    public function get_name() {
        return 'puca-base';
    }

    /**
	 * Get view template
	 *
	 * @param string $tpl_name
	 */
	protected function get_view_template( $tpl_slug, $tpl_name, $settings = [] ) {
		$located   = '';
		$templates = [];
		

		if ( ! $settings ) {
			$settings = $this->get_settings_for_display();
		} 

		if ( !empty($tpl_name) ) {
			$tpl_name  = trim( str_replace( '.php', '', $tpl_name ), DIRECTORY_SEPARATOR );
			$templates[] = 'elementor_templates/' . $tpl_slug . '-' . $tpl_name . '.php';
			$templates[] = 'elementor_templates/' . $tpl_slug . '/' . $tpl_name . '.php';
		}

		$templates[] = 'elementor_templates/' . $tpl_slug . '.php';
 
		foreach ( $templates as $template ) {
			if ( file_exists( PUCA_THEMEROOT . '/' . $template ) ) {
				$located = PUCA_THEMEROOT . '/' . $template;
				break;
			} else {
				$located = false;
			}
		}

		if ( $located ) {
			include $located;
		} else {
			echo sprintf( __( 'Failed to load template with slug "%s" and name "%s".', 'puca' ), $tpl_slug, $tpl_name );
		}
	}

	protected function render() {
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('wrapper', 'class', 'widget tbay-element tbay-element-'. $this->get_name_template() );

        $this->get_view_template($this->get_name_template(), '', $settings);
	}
	
	protected function register_controls_heading($condition = array()) {

        $this->start_controls_section(
            'section_heading',
            [
                'label' => esc_html__( 'Heading', 'puca' ),
                'condition' => $condition,
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => esc_html__('Alignment', 'puca'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [ 
                        'title' => esc_html__('Left', 'puca'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'puca'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'puca'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .heading-tbay-title' => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .description' => 'text-align: {{VALUE}};',
                ],
            ]
        );
     

        $this->add_control(
            'heading_title',
            [
                'label' => esc_html__('Title', 'puca'),
                'type' => Controls_Manager::TEXT,
            ]
        );

        $this->add_control(
            'heading_title_tag',
            [
                'label' => esc_html__( 'Title HTML Tag', 'puca' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'h3',
            ]
        );

        $this->add_control(
            'heading_subtitle',
            [
                'label' => esc_html__('Sub Title', 'puca'),
                'type' => Controls_Manager::TEXT,
            ]

		);  


        $this->add_control(
            'heading_description',
            [
                'label' => esc_html__('Description', 'puca'),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );
        
        $this->register_styles_heading();
     
        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_heading',
            [
                'label' => esc_html__( 'Heading', 'puca' ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => $condition,
            ]
        );
        $this->register_title_styles();
        $this->register_sub_title_styles();
        $this->register_description_styles();
        $this->register_content_styles();
        $this->end_controls_section();
    }
    protected function register_styles_heading() {
        $active_theme = puca_tbay_get_theme();
        if( $active_theme === 'fashion' ) return;

        $this->add_control(
            'styles',
            [
                'label'     => esc_html__('Style', 'puca'),
                'type'      => \Elementor\Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    ''          => esc_html__( 'Style Default', 'puca' ),
                    'style1'    => esc_html__( 'Style 1', 'puca' ),
                    'style2'    => esc_html__( 'Style 2', 'puca' ),
                    'style3'    => esc_html__( 'Style 3', 'puca' ),
                    'style4'    => esc_html__( 'Style 4', 'puca' ),
                    'style5'    => esc_html__( 'Style 5', 'puca' ),
                    'stylesmall'    => esc_html__( 'Style Small', 'puca' ),
                ],

            ]
        );
    }
    protected function register_remove_heading_element() {
        $this->remove_control('heading_description');
        $this->remove_control('styles');
    }
    protected function register_remove_align_heading_element() {
        $this->remove_control('align');
    }

    private function register_content_styles() {
        $this->add_control(
            'heading_stylecontent',
            [
                'label' => esc_html__( 'Content', 'puca' ),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'heading_title!' => ''
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'heading_style_margin',
            [
                'label' => esc_html__( 'Margin', 'puca' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ], 
                'condition' => [
                    'heading_title!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .widget .heading-tbay-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );        

        $this->add_responsive_control(
            'heading_style_padding',
            [
                'label' => esc_html__( 'Padding', 'puca' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ], 
                'condition' => [
                    'heading_title!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .widget .heading-tbay-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        ); 

        $this->add_control(
            'heading_style_bg',
            [
                'label' => esc_html__( 'Background', 'puca' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'condition' => [
                    'heading_title!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .widget .heading-tbay-title' => 'background: {{VALUE}};',
                ],
            ]
        );
    }
    private function register_title_styles() {
        $this->add_control(
            'heading_styletitle',
            [
                'label' => esc_html__( 'Title', 'puca' ),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'heading_title!' => ''
                ],
                'separator' => 'before',
            ]
        );


        $this->add_control(
            'heading_title_color',
            [
                'label' => esc_html__( 'Color', 'puca' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'condition' => [
                    'heading_title!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .widget .heading-tbay-title .title,{{WRAPPER}} .widget .widget_nav_menu .heading-tbay-title' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'heading_title_color_hover',
            [
                'label' => esc_html__( 'Hover Color', 'puca' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'condition' => [
                    'heading_title!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .widget .heading-tbay-title .title:hover,{{WRAPPER}} .widget .widget_nav_menu .heading-tbay-title:hover' => 'color: {{VALUE}} !important;',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_title_typography',
                'condition' => [
                    'heading_title!' => ''
                ],
                'selector' => '{{WRAPPER}} .widget .heading-tbay-title .title,{{WRAPPER}} .widget .widget_nav_menu .heading-tbay-title',
            ]
        );

        $this->add_responsive_control(
            'heading_title_bottom_space',
            [
                'label' => esc_html__( 'Spacing', 'puca' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 150,
                    ],
                ],
                'condition' => [
                    'heading_title!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .widget .heading-tbay-title .title,{{WRAPPER}} .widget .widget_nav_menu .heading-tbay-title' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );


    }     

    private function register_sub_title_styles() {

        $this->add_control(
            'heading_stylesubtitle',
            [
                'label' => esc_html__( 'Sub title', 'puca' ),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'heading_subtitle!' => ''
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'heading_subtitle_color',
            [
                'label' => esc_html__( 'Color', 'puca' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'condition' => [
                    'heading_subtitle!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .widget .heading-tbay-title .subtitle' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'heading_subtitle_color_hover',
            [
                'label' => esc_html__( 'Hover Color', 'puca' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'condition' => [
                    'heading_subtitle!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .widget .heading-tbay-title .subtitle:hover' => 'color: {{VALUE}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_subtitle_typography',
                'condition' => [
                    'heading_subtitle!' => ''
                ],
                'selector' => '{{WRAPPER}} .widget .heading-tbay-title .subtitle',
            ]
        );

        $this->add_responsive_control(
            'heading_subtitle_margin',
            [
                'label' => esc_html__( 'Margin', 'puca' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .widget .heading-tbay-title .subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition' => [
                    'heading_subtitle!' => ''
                ],
            ]
        );
    }    

    private function register_description_styles() {

        $this->add_control(
            'heading_style_description',
            [
                'label' => esc_html__( 'Description', 'puca' ),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'heading_description!' => ''
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'heading_description_color',
            [
                'label' => esc_html__( 'Color', 'puca' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'condition' => [
                    'heading_description!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .widget.tbay-element-heading .description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'heading_description_color_hover',
            [
                'label' => esc_html__( 'Hover Color', 'puca' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_1,
                ],
                'condition' => [
                    'heading_description!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .widget.tbay-element-heading .description:hover' => 'color: {{VALUE}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'heading_description_typography',
                'condition' => [
                    'heading_description!' => ''
                ],
                'selector' => '{{WRAPPER}} .widget.tbay-element-heading .description',
            ]
        );

        $this->add_responsive_control(
            'heading_description_margin',
            [
                'label' => esc_html__( 'Margin', 'puca' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .widget.tbay-element-heading .description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition' => [
                    'heading_description!' => ''
                ],
            ]
        );
    }     

    protected function get_available_pages() {
        $pages = get_pages();

        $options = [];

        foreach ($pages as $page) {
            $options[$page->ID] = $page->post_title;
        }

        return $options;
    }

    protected function get_available_on_sale_products() {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1
        );

        $product_ids_on_sale    = wc_get_product_ids_on_sale();
        $product_ids_on_sale[]  = 0;
        $args['post__in'] = $product_ids_on_sale;
        $loop = new WP_Query( $args );

        $options = []; 
        if ( $loop->have_posts() ): while ( $loop->have_posts() ): $loop->the_post();

            global $product;

            $options[$product->get_id()] = $product->get_title();


        endwhile; endif; wp_reset_postdata();

        return $options;
    }

    protected function get_layout_products_countdown() {
        $layout = puca_tbay_woo_get_product_countdown_layouts();

        return array_flip($layout);
    }

    protected function get_available_products_countdown() {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1
        );

        $product_ids_on_sale    = wc_get_product_ids_on_sale();
        $product_ids_on_sale[]  = 0;
        $args['post__in'] = $product_ids_on_sale;
        $loop = new WP_Query( $args );

        $options = []; 
        $time_sale = false;
        if ( $loop->have_posts() ): while ( $loop->have_posts() ): $loop->the_post();

            global $product;
            $time_sale = get_post_meta( $product->get_id(), '_sale_price_dates_to', true );

            if( $time_sale )  {
                $options[$product->get_id()] = $product->get_title();
            } 

        endwhile; endif; wp_reset_postdata();

        return $options;
    }


    protected function get_available_menus() {
        $menus = wp_get_nav_menus();

        $options = [];

        foreach ($menus as $menu) {
            $options[$menu->slug] = $menu->name;
        }

        return $options;
    }
	
	public function render_element_heading() {
        $heading_description = $heading_title = $heading_title_tag = $heading_subtitle = '';
        $settings = $this->get_settings_for_display();
        extract( $settings );  

		if( !empty($heading_subtitle) || !empty($heading_title) ) : ?>
			<<?php echo trim($heading_title_tag); ?> class="heading-tbay-title widget-title">
				<?php if( !empty($heading_title) ) : ?>
					<span class="title"><?php echo trim($heading_title); ?></span>
				<?php endif; ?>	    	
				<?php if( !empty($heading_subtitle) ) : ?>
					<span class="subtitle"><?php echo trim($heading_subtitle); ?></span>
				<?php endif; ?>
			</<?php echo trim($heading_title_tag); ?>>
		<?php endif;

        if( !empty($heading_description) ) : ?>
            <div class="description"><?php echo trim($heading_description); ?></div>
        <?php endif;
    }  

    protected function get_product_type() {
        $type = [
            'newest' => esc_html__('Newest Products', 'puca'),
            'on_sale' => esc_html__('On Sale Products', 'puca'),
            'best_selling' => esc_html__('Best Selling', 'puca'),
            'top_rated' => esc_html__('Top Rated', 'puca'),
            'featured' => esc_html__('Featured Product', 'puca'),
            'random_product' => esc_html__('Random Product', 'puca'),
        ];

        return apply_filters( 'puca_woocommerce_product_type', $type);
    }

    protected function get_title_product_type($key) {
        $array = $this->get_product_type();

        return $array[$key];
    }

    protected function get_attribute_query_product_type($args, $product_type) {
        global $woocommerce;

        switch ($product_type) {
            case 'best_selling':
                $args['meta_key']   = 'total_sales';
                $args['order']          = 'desc';
                $args['orderby']    = 'meta_value_num';
                $args['ignore_sticky_posts']   = 1;
                $args['meta_query'] = array();
                $args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
                $args['meta_query'][] = $woocommerce->query->visibility_meta_query();
                break;

            case 'featured':
                $args['ignore_sticky_posts']    = 1;
                $args['meta_query']             = array();
                $args['meta_query'][]           = $woocommerce->query->stock_status_meta_query();
                $args['meta_query'][]           = $woocommerce->query->visibility_meta_query();
                $args['tax_query'][]              = array(
                    array(
                        'taxonomy' => 'product_visibility',
                        'field'    => 'name',
                        'terms'    => 'featured',
                        'operator' => 'IN'
                    )
                );
                break;

            case 'top_rated':
                $args['meta_key']       = '_wc_average_rating';
                $args['orderby']        = 'meta_value_num';
                $args['order']          = 'desc';
                break;

            case 'newest':
                $args['meta_query'] = array();
                $args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
                break;

            case 'random_product':
                $args['orderby']    = 'rand';
                $args['meta_query'] = array();
                $args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
                break;

            case 'deals':
                $product_ids_on_sale    = wc_get_product_ids_on_sale();
                $product_ids_on_sale[]  = 0;
                $args['post__in'] = $product_ids_on_sale;
                $args['meta_query'] = array();
                $args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
                $args['meta_query'][] = $woocommerce->query->visibility_meta_query();
                $args['meta_query'][] =  array(
                    'relation' => 'AND',
                    array(
                        'relation' => 'OR',
                        array(
                            'key'           => '_sale_price',
                            'value'         => 0,
                            'compare'       => '>',
                            'type'          => 'numeric'
                        ),
                        array(
                            'key'           => '_min_variation_sale_price',
                            'value'         => 0,
                            'compare'       => '>',
                            'type'          => 'numeric'
                        ),
                    ),            
                    array(
                        'key'           => '_sale_price_dates_to',
                        'value'         => time(),
                        'compare'       => '>',
                        'type'          => 'numeric'
                    ),
                );
                break;  

            case 'on_sale':
                $product_ids_on_sale    = wc_get_product_ids_on_sale();
                $product_ids_on_sale[]  = 0;
                $args['post__in'] = $product_ids_on_sale;
                break;
        }

        return $args;
    }

    protected function get_query_products($categories = array(), $cat_operator = '', $product_type = 'newest', $limit = '', $orderby = '', $order = '') {
        $atts = [
            'limit' => $limit,
            'orderby' => $orderby,
            'order' => $order
        ];
        
        if (!empty($categories)) {
            
            if( !is_array( $categories ) )  { 
                $atts['category'] = $categories;
            } else {
                $atts['category'] = implode(', ', $categories);
                $atts['cat_operator'] = $cat_operator; 
            }
            
        }
        
        $type = 'products';

        $shortcode = new WC_Shortcode_Products($atts, $type);
        $args = $shortcode->get_query_args();
        
        $args = $this->get_attribute_query_product_type($args, $product_type);
        return new WP_Query($args); 
    }

    protected function get_product_categories($number = '') {
        $args = array(
            'taxonomy'   => 'product_cat',
            'hide_empty' => false,
        );
        if ($number === 0) {
            return;
        }
        if( !empty($number) && $number !== -1 ) {
            $args['number'] = $number;
        }
       

        $category = get_terms($args);
        $results = array();
        if (!is_wp_error($category)) {
            foreach ($category as $category) {
                $results[$category->slug] = $category->name.' ('.$category->count.') ';
            }
        }
        return $results;
    }

    protected function get_cat_operator() {
        $operator = [
            'AND' => esc_html__('AND', 'puca'),
            'IN' => esc_html__('IN', 'puca'),
            'NOT IN' => esc_html__('NOT IN', 'puca'),
        ];

        return apply_filters( 'puca_woocommerce_cat_operator', $operator);
    }

    protected function get_woo_order_by() { 
        $oder_by = [
            'date' => esc_html__('Date', 'puca'),
            'title' => esc_html__('Title', 'puca'),
            'id' => esc_html__('ID', 'puca'),
            'price' => esc_html__('Price', 'puca'),
            'popularity' => esc_html__('Popularity', 'puca'),
            'rating' => esc_html__('Rating', 'puca'),
            'rand' => esc_html__('Random', 'puca'),
            'menu_order' => esc_html__('Menu Order', 'puca'),
        ];

        return apply_filters( 'puca_woocommerce_oder_by', $oder_by);
    }

    protected function get_woo_order() {
        $order = [
            'asc' => esc_html__('ASC', 'puca'), 
            'desc' => esc_html__('DESC', 'puca'),
        ];

        return apply_filters( 'puca_woocommerce_order', $order);
    }

    protected function register_woocommerce_layout_type() {

        $layouts = array(
            'grid'              => 'Grid',
            'carousel'          => 'Carousel',
            'carousel-special'  => 'Carousel Special'    
        );

        $active_theme = puca_tbay_get_theme();

        if( $active_theme !== 'furniture' ) {
            $layouts['special'] = 'Special';
        }

        if( $active_theme == 'fashion' ) {
            $layouts['list'] =  esc_html__('List', 'puca');
        }


        $this->add_control(
            'layout_type',
            [
                'label'     => esc_html__('Layout Type', 'puca'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'grid',
                'options'   => $layouts,
            ]
        );  
    }

    protected function register_woocommerce_order() {
        $this->add_control(
            'orderby',
            [
                'label' => esc_html__('Order By', 'puca'),
                'type' => Controls_Manager::SELECT,
                'default' => 'date',
                'options' => $this->get_woo_order_by(),
                'conditions' => [
					'relation' => 'AND',
					'terms' => [
						[
							'name' => 'product_type',
							'operator' => '!==',
							'value' => 'top_rated',
						],
						[
							'name' => 'product_type',
							'operator' => '!==',
							'value' => 'random_product',
						],
						[
							'name' => 'product_type',
							'operator' => '!==',
							'value' => 'best_selling',
						],
					],
				],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => esc_html__('Order', 'puca'),
                'type' => Controls_Manager::SELECT,
                'default' => 'asc',
                'options' => $this->get_woo_order(),
                'conditions' => [
					'relation' => 'AND',
					'terms' => [
						[
							'name' => 'product_type',
							'operator' => '!==',
							'value' => 'top_rated',
						],
						[
							'name' => 'product_type',
							'operator' => '!==',
							'value' => 'random_product',
						],
						[
							'name' => 'product_type',
							'operator' => '!==',
							'value' => 'best_selling',
						],
					],
				],
            ]
        );
    }

    protected function register_woocommerce_categories_operator() {
        $categories = $this->get_product_categories();

        $this->add_control(
            'categories', 
            [
                'label' => esc_html__('Categories', 'puca'),
                'type' => Controls_Manager::SELECT2,
                'label_block' => true, 
                'default'   => array_keys($categories)[0],
                'options'   => $categories,   
                'multiple' => true,
            ]
        );

        $this->add_control(
            'cat_operator',
            [
                'label' => esc_html__('Category Operator', 'puca'),
                'type' => Controls_Manager::SELECT,
                'default' => 'IN',
                'options' => $this->get_cat_operator(),
                'condition' => [
                    'categories!' => ''
                ],
            ]
        );
    }

    protected function get_woocommerce_tags() {
        $tags = array();
        
        $args = array(
            'order' => 'ASC',
        );

        $product_tags = get_terms( 'product_tag', $args );

        foreach ( $product_tags as $key => $tag ) {

            $tags[$tag->slug] = $tag->name . ' (' .$tag->count .')';

        }

        return $tags;
    }
    public function settings_layout() {
        $settings = $this->get_settings_for_display();
        extract( $settings );

        if( !isset($layout_type) ) return;

        $this->add_render_attribute('row', 'class', $this->get_name_template());

        if( isset($rows) && !empty($rows) ) {
            $this->add_render_attribute( 'row', 'class', 'row-'. $rows);
        }

        if( $layout_type !== 'grid' && $layout_type !== 'list' && $layout_type !== 'grid-v2' ) {
            $this->settings_carousel($settings);  
        }else{
            $this->settings_responsive($settings);
        }  
    }
    
    protected function get_widget_field_img( $image ) {
        $image_id   = $image['id'];
        $img  = '';

        if( !empty($image_id) ) {
            $img = wp_get_attachment_image($image_id, 'full');    
        } else if( !empty($image['url']) ) {
            $img = '<img src="'. $image['url'] .'">';
        }

        return $img;
    }
    protected function get_name_tab_by_slug($tab_slug) {
        switch ($tab_slug) {
            case 'newest':
                $tab_name = esc_html__('New Arrivals', 'puca');
                break;                            
            case 'featured':
                $tab_name = esc_html__('Featured Products', 'puca');
                break;                           
            case 'best_selling':
                $tab_name = esc_html__('Best Seller', 'puca');
                break;                            
            case 'top_rated':
                $tab_name = esc_html__('Top Rated', 'puca');
                break;                            
            case 'on_sale':
                $tab_name = esc_html__('On Sale', 'puca');
                break;
            
            default:
                $tab_name = esc_html__('New Arrivals', 'puca');
                break;
        }
        return $tab_name;
    }

    protected function get_id_cat_product_by_slug($slug) {
        $category   = get_term_by( 'slug', $slug, 'product_cat' );
        $id   = $category->term_id;   

        return $id;
    }

    protected function get_products_category_childs( $categories, $id_parent, $level, &$dropdown ) {
        foreach ( $categories as $key => $category ) {
            if ( $category->category_parent == $id_parent ) {
                $dropdown = array_merge( $dropdown, array( str_repeat( "- ", $level ) . $category->name . ' (' .$category->count .')' => $category->term_id ) );
                unset($categories[$key]);
                $this->get_products_category_childs( $categories, $category->term_id, $level + 1, $dropdown );
            }
        }
    }

    protected function render_controls_tab($tab_id) {
        $settings = $this->get_settings_for_display();
        extract($settings);
        
        $i = 0;
        ?>
        <ul role="tablist" class="nav nav-tabs">
            <?php foreach ($categoriestabs as $tab) : ?>

            <?php 

                if( isset($show_catname_tabs) && $show_catname_tabs == 'yes' ) {

                    $category   = get_term_by( 'slug', $tab['category'], 'product_cat' );
                    $tab_name   = $category->name;   

                } else {

                    $tab_slug = (isset($tab['product_type'])) ? $tab['product_type'] : '';
                    
                    if (!empty($tab['title'])) {
                        $tab_name = $tab['title'];
                    } else {
                        $tab_name = $this->get_name_tab_by_slug($tab_slug);
                    }
                    
                }
            
            ?> 
            <?php 
                $li_class = ($i == 0 ? ' class="active"' : '');
            ?>
            <li <?php echo trim( $li_class ); ?>>
                <a href="#tab-<?php echo esc_attr($tab_id);?>-<?php echo esc_attr($i); ?>" data-toggle="tab">
                    <?php echo esc_html($tab_name); ?>
                </a>
            </li>

            <?php $i++; endforeach; ?>
        </ul>
        <?php
    }

    
    public function render_layout_products_tab($tab) {
        $product_type = $category = $cat_operator  = $limit = $orderby = $order = '';
        $rows = 1;
        extract( $tab );

        $settings = $this->get_settings_for_display();
        extract( $settings );

        $loop = $this->get_query_products($category,  $cat_operator, $product_type, $limit, $orderby, $order);
    
        $attr_row = $this->get_render_attribute_string('row');
 
        $active_theme = puca_tbay_get_part_theme();

        wc_get_template( 'layout-products/'. $active_theme .'/'. $layout_type .'.php' , array( 'loop' => $loop, 'attr_row' => $attr_row, 'rows' => $rows) );
    }


    protected $nav_menu_index = 1;

    protected function get_nav_menu_index() {
        return $this->nav_menu_index++;
    }

    
    protected function render_item_icon($selected_icon) {
        $settings = $this->get_settings_for_display();
        if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['icon'] = 'fa fa-star';
        }
        $has_icon = ! empty( $settings['icon'] );

        if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
        }
        
        if ( ! $has_icon && ! empty( $selected_icon['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['selected_icon'] );
        $is_new = ! isset( $settings['icon'] ) && Icons_Manager::is_migration_allowed();
        
        Icons_Manager::enqueue_shim();

        if( !$has_icon ) return;  
        
        if ( $is_new || $migrated ) {
            Icons_Manager::render_icon( $selected_icon, [ 'aria-hidden' => 'true' ] );
        } elseif ( ! empty( $settings['icon'] ) ) {
            ?><i <?php echo trim($this->get_render_attribute_string( 'i' )); ?>></i><?php
        }
    }

    protected function render_content_menu($menu_id) {
        $settings = $this->get_settings_for_display();
        extract($settings);

        $available_menus = $this->get_available_menus();

        if (!$available_menus) {
            return;
        }
        
        $_id = puca_tbay_random_key();

        $args = [
            'echo'        => false, 
            'menu'        => $menu_id,
            'container_class' => 'collapse navbar-collapse',
            'menu_id'     => 'menu-' . $this->get_nav_menu_index() . '-' . $_id,
            'walker'      => new Puca_Tbay_Nav_Menu(),
            'fallback_cb' => '__return_empty_string',
            'container'   => '',
        ];  

        $args['menu_class']     = 'elementor-nav-menu menu';


        // General Menu.
        $menu_html = wp_nav_menu($args);

        $this->add_render_attribute('main-menu', 'class', [
            'elementor-nav-menu--main',
            'elementor-nav-menu__container'
        ]);

        ?>
        <div class="tab-menu-wrapper">
            <nav <?php echo trim($this->get_render_attribute_string('main-menu')); ?>><?php echo trim($menu_html); ?></nav>
        </div>
        <?php
    }
}

