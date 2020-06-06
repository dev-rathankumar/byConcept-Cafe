<?php
//convert hex to rgb
if ( !function_exists ('puca_tbay_getbowtied_hex2rgb') ) {
	function puca_tbay_getbowtied_hex2rgb($hex) {
		$hex = str_replace("#", "", $hex);
		
		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);
		return implode(",", $rgb); // returns the rgb values separated by commas
		//return $rgb; // returns an array with the rgb values
	}
}
if ( !function_exists ('puca_tbay_custom_styles') ) {
	function puca_tbay_custom_styles() {
		global $post;	
		
		$logo_img_width        		= puca_tbay_get_config( 'logo_img_width' );
		$logo_padding        		= puca_tbay_get_config( 'logo_padding' );

		$logo_tablets_img_width 	= puca_tbay_get_config( 'logo_tablets_img_width' );
		$logo_tablets_padding 		= puca_tbay_get_config( 'logo_tablets_padding' );		

		$logo_img_width_mobile 		= puca_tbay_get_config( 'logo_img_width_mobile' );
		$logo_mobile_padding 		= puca_tbay_get_config( 'logo_mobile_padding' );


		$custom_css 			= puca_tbay_get_config( 'custom_css' );
		$css_desktop 			= puca_tbay_get_config( 'css_desktop' );
		$css_tablet 			= puca_tbay_get_config( 'css_tablet' );
		$css_wide_mobile 		= puca_tbay_get_config( 'css_wide_mobile' );
		$css_mobile         	= puca_tbay_get_config( 'css_mobile' );

		$show_typography         	= puca_tbay_get_config( 'show_typography', false );

		ob_start();	
		?>
		
		/* Theme Options Styles */
		
		<?php if( $show_typography ) : ?>	
			/* Typography */
			/* Main Font */
			<?php
				$font_source = puca_tbay_get_config('font_source');
				$main_font = puca_tbay_get_config('main_font');
				$main_font = isset($main_font['font-family']) ? $main_font['font-family'] : false;
				$main_google_font_face = puca_tbay_get_config('main_google_font_face');
				$main_custom_font_face = puca_tbay_get_config('main_custom_font_face');
			?>
			<?php if ( ($font_source == "1" && $main_font) || ($font_source == "2" && $main_google_font_face) || ($font_source == "3" && $main_custom_font_face) ): ?>
				body,
				p, .btn, .button, .rev-btn, .rev-btn:visited
				{font-family: 
					<?php 
						switch ($font_source) {
							case '3':
								echo trim($main_custom_font_face);
								break;								
							case '2':
								echo trim($main_google_font_face);
								break;							
							case '1':
								echo trim($main_font);
								break;
							
							default:
								echo trim($main_google_font_face);
								break;
						}
					?>
				}
			<?php endif; ?>
			/* Second Font */
			<?php
				$secondary_font = puca_tbay_get_config('secondary_font');
				$secondary_font = isset($secondary_font['font-family']) ? $secondary_font['font-family'] : false;
				$secondary_google_font_face = puca_tbay_get_config('secondary_google_font_face');
				$secondary_custom_font_face = puca_tbay_get_config('secondary_custom_font_face');
			?>
			<?php if ( ($font_source == "1" && $secondary_font) || ($font_source == "2" && $secondary_google_font_face)  || ($font_source == "3" && $secondary_custom_font_face) ): ?>
					h1, h2, h3, h4, h5, h6, .widget-title, btn ,.navbar-nav.megamenu > li > a, .name
				{font-family: 
					<?php 
						switch ($font_source) {
							case '3':
								echo trim($secondary_custom_font_face);
								break;								
							case '2':
								echo trim($secondary_google_font_face);
								break;							
							case '1':
								echo trim($secondary_font);
								break;
							
							default:
								echo trim($secondary_google_font_face);
								break;
						}
					?>
				}		
			<?php endif; ?>

		<?php endif; ?>

			/* Custom Color (skin) */ 

			/* check main color */ 
			<?php if ( puca_tbay_get_config('main_color') != "" ) : ?>
			/*color*/

			/*Custom style smart mobile menu*/
			.mm-menu .mm-panels > .mm-panel > .mm-navbar + .mm-listview li.active > a, .mm-menu .mm-panels > .mm-panel > .mm-navbar + .mm-listview li.active .mm-counter,
			.mm-menu .mm-navbars_bottom .mm-navbar a:hover, .mm-menu .mm-navbars_bottom .mm-navbar a:focus {
				color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.mm-menu .mm-panels > .mm-panel > .mm-navbar + .mm-listview li.active .mm-btn_next:after {
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			/*End custom style smart mobile menu*/

			.tbay-breadscrumb .breadscrumb-inner .breadcrumb .active,
			.entry-single .entry-meta .entry-date,
			.tbay-search-form .button-search:hover,
			.testimonials-body .name-client,
			.slick-dots li.slick-active button:before,
			.search-modal .btn-search-totop:hover, 
			.search-modal .btn-search-totop:focus,
			.widget-social .social.style2 > li a:hover,
			.widget-social .social.style2 > li a:hover i,
			.widget-categoriestabs ul.nav-tabs > li.active > a, 
			.widget_deals_products ul.nav-tabs > li.active > a,
			.widget-testimonials.icon-red .testimonials-body .description:before,
			.tparrows:hover,
			.tparrows.tp-rightarrow:hover:before,
			.hades .tp-tab.selected .tp-tab-title, 
			.hades .tp-tab:hover .tp-tab-title,
			#tbay-header.header-v7 .tbay-mainmenu .active-mobile button:hover, 
			#tbay-header.header-v7 .tbay-mainmenu .active-mobile button:focus,
			.name a:hover,
			.post-grid.vertical .entry-content .entry-title a:hover,
			.widget-categories.widget-grid.style2 .item-cat:hover .cat-name,
			body.v13 .header-category-logo .widget-categories .item-cat:hover .cat-name,
			.entry-single .entry-date a:hover,
			.tbay-breadscrumb.breadcrumbs-color .breadscrumb-inner .breadcrumb a:hover, .tbay-breadscrumb.breadcrumbs-text .breadscrumb-inner .breadcrumb a:hover,
			.singular-shop div.product .information .yith-wcwl-wishlistexistsbrowse>a:hover, .singular-shop div.product .information .yith-wcwl-wishlistaddedbrowse>a:hover, .singular-shop div.product .information .yith-compare>a:hover, .singular-shop div.product .information .add_to_wishlist:hover,
			.singular-shop div.product .information .compare:hover,
			.singular-shop div.product .information .compare:hover:before,
			.ui-autocomplete.ui-widget-content li.list-header a,
			.tbay-vertical-menu>.widget.widget_nav_menu .menu>li.active>a, .tbay-vertical-menu>.widget.widget_nav_menu .menu>li:hover>a,
			.tbay-vertical-menu>.widget.widget_nav_menu .menu>li a:hover,
			.top-newsletter a,
			.widget-features.contact-us .feature-box-group .feature-box:hover .icon-inner
			{
				color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.tbay-vertical-menu>.widget.widget_nav_menu .menu>li a:hover {
				border-right-color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			a:hover, a:focus, .topbar-device-mobile .active-mobile .btn-danger {
				color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.widget-categories .owl-carousel.categories .slick-arrow:hover,
			.navbar-nav.megamenu .show-demo .dropdown-menu .widget ul li:hover a, .navbar-nav.megamenu .show-demo .dropdown-menu .widget ul li.active a,
			.topbar-device-mobile .topbar-post > * i:hover,
			.singular-shop div.product .information .woocommerce-variation-price .price,
			#respond p.stars a.active:after, #respond p.stars a:hover:after, #respond p.stars a:active:after,
			.widget.upsells .owl-carousel .slick-arrow:hover, .widget.related .owl-carousel .slick-arrow:hover,
			.cart_totals table tr.shipping a, .cart_totals table * tr.shipping a,
			#add_payment_method #payment div.form-row a, 
			.woocommerce-cart #payment div.form-row a, 
			.woocommerce-checkout #payment div.form-row a,
			.navbar-offcanvas .navbar-nav > li.open > a:hover, 
			.navbar-offcanvas .navbar-nav > li.open > a:focus, 
			.navbar-offcanvas .navbar-nav > li.open > a, 
			.navbar-offcanvas .navbar-nav > li.active > a:hover, 
			.navbar-offcanvas .navbar-nav > li.active > a:focus, 
			.navbar-offcanvas .navbar-nav > li.active > a, 
			.navbar-offcanvas .navbar-nav > li.collapsable > a:hover, 
			.navbar-offcanvas .navbar-nav > li.collapsable > a:focus, 
			.navbar-offcanvas .navbar-nav > li.collapsable > a,
			.navbar-offcanvas .navbar-nav > li.open > .hitarea:after,
			.navbar-offcanvas .navbar-nav > li.active > .hitarea:after,
			.navbar-offcanvas .navbar-nav > li.collapsable > .hitarea:after,
			.navbar-offcanvas .navbar-nav > li li.open > a:hover, 
			.navbar-offcanvas .navbar-nav > li li.open > a:focus, 
			.navbar-offcanvas .navbar-nav > li li.open > a, 
			.navbar-offcanvas .navbar-nav > li li.active > a:hover, 
			.navbar-offcanvas .navbar-nav > li li.active > a:focus, 
			.navbar-offcanvas .navbar-nav > li li.active > a, 
			.navbar-offcanvas .navbar-nav > li li.collapsable > a:hover, 
			.navbar-offcanvas .navbar-nav > li li.collapsable > a:focus, 
			.navbar-offcanvas .navbar-nav > li li.collapsable > a,
			.footer-device-mobile > *.active a i,
			.woocommerce-checkout form.checkout .subtitle a,
			.woocommerce-checkout .form-row a,
			.sidebar ul.woof_list li .hover, .product-top-sidebar ul.woof_list li .hover, .product-canvas-sidebar ul.woof_list li .hover, .related-posts ul.woof_list li .hover, .blog-top-search ul.woof_list li .hover, .blog-top-sidebar1 ul.woof_list li .hover,
			.singular-shop div.product .information .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse.show > a, .singular-shop div.product .information .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse.show > a
			{
				color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}

			.woo-variation-swatches-stylesheet-enabled .variable-items-wrapper .variable-item:not(.radio-variable-item).selected, .woo-variation-swatches-stylesheet-enabled .variable-items-wrapper .variable-item:not(.radio-variable-item).selected:hover, .woo-variation-swatches-stylesheet-enabled .variable-items-wrapper .variable-item:not(.radio-variable-item):hover {
				-webkit-box-shadow: 0 0 0 1px <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				box-shadow: 0 0 0 1px <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}

			#reviews .reviews-summary .review-summary-total .review-summary-result {
				color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}

			#content > .products.load-ajax:after {
				border-top-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}

			    
			.navbar-nav.megamenu > li:hover > a, 
			.navbar-nav.megamenu > li:focus > a, 
			.navbar-nav.megamenu > li.active > a,
			#tbay-footer .menu > li:hover > a,
			.navbar-nav.megamenu .dropdown-menu .widget ul li.active a,
			.tbay-offcanvas-main .navbar-nav li.active > a, 
			.tbay-offcanvas-main .navbar-nav li:hover > a {
				color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.verticle-menu .navbar-nav > li.active, 
			.verticle-menu .navbar-nav > li:hover {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.questions-section #ywqa-submit-question {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.verticle-menu .navbar-nav > li.active > a, 
			.verticle-menu .navbar-nav > li:hover > a {
				color: #fff !important;
			}
			.topbar-mobile .btn.btn-offcanvas,
			.topbar-mobile .search-popup .show-search:hover, .topbar-mobile .search-popup .show-search:focus, .topbar-mobile .search-popup .show-search.active,.woocommerce-account #main-content .woocommerce button[type="submit"], 
			.woocommerce-account #main-content .woocommerce input[type="submit"],
			.cart_totals table tr.shipping .button, .cart_totals table * tr.shipping .button,
			.singular-shop .tbay-time .tbay-countdown .times > div > span,
			.tbay-slider-for .slick-arrow:hover:after, .product-block .yith-wcwl-add-to-wishlist > div.yith-wcwl-add-button a.delete_item {
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> ;
			}
			.woocommerce-account #main-content .woocommerce form.login > p a, .singular-shop div.product .information .yith-wcwl-add-to-wishlist a.delete_item {
				color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.navbar-nav.megamenu > li > a:before,
			.cart-dropdown .cart-icon .mini-cart-items,
			.tbay-to-top a span,
			.tp-bullets .tp-bullet.selected,
			.tp-bullets .tp-bullet:hover,
			.SumoSelect > .optWrapper > .options li.opt:hover, 
			.SumoSelect > .optWrapper > .options li.opt.selected,
			.metis.tparrows:hover,
			.hades .tp-tab.selected .tp-tab-title:after, 
			.hades .tp-tab:hover .tp-tab-title:after,
			.widget_price_filter .ui-slider .ui-slider-handle,
			.yith-wcqv-wrapper #yith-quick-view-content .summary .disabled.single_add_to_cart_button {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.widget-categoriestabs ul.nav-tabs > li:hover a, .widget_deals_products ul.nav-tabs > li:hover a {
				color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.widget-categoriestabs ul.nav-tabs > li:hover, .widget_deals_products ul.nav-tabs > li:hover,
			.widget-categoriestabs .woocommerce .btn-view-all, .widget_deals_products .woocommerce .btn-view-all {
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.widget-woof .woof_submit_search_form_container .woof_reset_search_form {
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			/* Shop */
			/*Mini Cart*/
			.tbay-dropdown-cart .total .woocs_special_price_code, 
			#tbay-top-cart .total .woocs_special_price_code, 
			.tbay-bottom-cart .total .woocs_special_price_code, 
			.cart-popup .total .woocs_special_price_code,
			.tbay-dropdown-cart .dropdown-menu .product-details .woocommerce-Price-amount, 
			#tbay-top-cart .dropdown-menu .product-details .woocommerce-Price-amount, 
			.tbay-bottom-cart .dropdown-menu .product-details .woocommerce-Price-amount, 
			.cart-popup .dropdown-menu .product-details .woocommerce-Price-amount,
			.woocommerce .cart-dropdown.cart-popup .dropdown-menu .total .amount, 
			.cart-dropdown.cart-popup .dropdown-menu .total .amount,
			.woocommerce table.wishlist_table .product-add-to-cart .remove_from_wishlist.button:hover {
				color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.metis.tparrows:hover:before {
				color: #fff !important;
			}

			.woocommerce .cart-dropdown.cart-popup .dropdown-menu p.buttons a.checkout, .cart-dropdown.cart-popup .dropdown-menu p.buttons a.checkout {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.woocommerce .cart-dropdown.cart-popup .dropdown-menu p.buttons a:hover, .cart-dropdown.cart-popup .dropdown-menu p.buttons a:hover,.woocommerce table.wishlist_table tbody tr .product-remove a:hover {
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}

			.topbar-mobile .active-mobile .btn,
			#onepage-single-product .shop-now a,
			.widget_price_filter .ui-slider-horizontal .ui-slider-range {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.woocommerce-checkout .form-row input[type="submit"],
			.woo-variation-swatches-stylesheet-enabled .variable-items-wrapper .variable-item:not(.radio-variable-item).button-variable-item.selected, .woo-variation-swatches-stylesheet-enabled .variable-items-wrapper .variable-item:not(.radio-variable-item).button-variable-item:hover {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.tbay-dropdown-cart.v2 .group-button a.button:hover, 
			.tbay-dropdown-cart.v2 .group-button a.button.checkout, 
			#tbay-top-cart.v2 .group-button a.button:hover, 
			#tbay-top-cart.v2 .group-button a.button.checkout, 
			.tbay-bottom-cart.v2 .group-button a.button:hover, 
			.tbay-bottom-cart.v2 .group-button a.button.checkout, 
			.cart-popup.v2 .group-button a.button:hover, 
			.cart-popup.v2 .group-button a.button.checkout {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				color: #fff !important;
			}
			.woocommerce .cart-dropdown.cart-popup .dropdown-menu p.buttons a.view-cart:hover, 
			.woocommerce .cart-dropdown.cart-popup .dropdown-menu p.buttons a.checkout:hover, 
			.cart-dropdown.cart-popup .dropdown-menu p.buttons a.view-cart:hover, 
			.cart-dropdown.cart-popup .dropdown-menu p.buttons a.checkout:hover, .widget-features.style1 .feature-box-group .feature-box:hover .ourservice-heading {
				background: #fff !important;
				color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.woocommerce a.button:hover, 
			.woocommerce button.button:hover, 
			.woocommerce input.button:hover, 
			.woocommerce #respond input#submit:hover,
			.product-block .groups-button-image > div a:hover,
			.product-block .groups-button-image > a a:hover, 
			.product-block .buttons > div a:hover, 
			.product-block .buttons > a a:hover,
			.tbay-to-top a#back-to-top {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				color: #fff !important;
			}
			.woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover, .woocommerce #respond input#submit:hover {
			    border-color: #000;
    			background: #000;	
			}
			.product-block .add-cart .product_type_external, 
			.product-block .add-cart .product_type_grouped, 
			.product-block .add-cart .add_to_cart_button, .product-block .add-cart a.button, 
			.product-block .add-cart a.added_to_cart,
			.widget-newletter .btn-default, .product-block.list .yith-wcwl-wishlistexistsbrowse a, .product-block.list .yith-wcwl-wishlistaddedbrowse a
			 {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.tbay-variations .reset_variations:hover, 
			.tbay-variations .reset_variations:focus {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			
			.more_products a, 
			.tbay-pagination-load-more a
			#add_payment_method .wc-proceed-to-checkout a.checkout-button, 
			.woocommerce-cart .wc-proceed-to-checkout a.checkout-button, 
			.woocommerce-checkout .wc-proceed-to-checkout a.checkout-button, 
			#add_payment_method #payment div.form-row.place-order #place_order, 
			.woocommerce-cart #payment div.form-row.place-order #place_order, 
			.woocommerce-checkout #payment div.form-row.place-order #place_order			
			{
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.widget-categories.widget-grid .show-all:hover {
				color: #fff !important;
				background: #000 !important;
			}
			.widget-categoriestabs .woocommerce .btn-view-all:hover, 
			.widget_deals_products .woocommerce .btn-view-all:hover {
				color: #fff !important;
				background: #222;
				border-color: #222 !important;
			}
			.product-block .add-cart .product_type_external:hover, 
			.product-block .add-cart .product_type_grouped:hover, 
			.product-block .add-cart .add_to_cart_button:hover, 
			.product-block .add-cart a.button:hover, 
			.product-block .add-cart a.added_to_cart:hover,
			.tbay-to-top a#back-to-top:hover, 
			.tbay-to-top button.btn-search-totop#back-to-top:hover,
			.widget-newletter .btn-default:hover {
				color: #fff !important;
				border-color: #000 !important;
				background: #000 !important;
			}
			.woocommerce #tbay-top-cart .group-button a.button:hover, 
			.woocommerce #tbay-top-cart .group-button a.button.checkout, 
			#tbay-top-cart .group-button a.button:hover, 
			#tbay-top-cart .group-button a.button.checkout,
			#tbay-bottom-cart .group-button a.button:hover,
			#tbay-bottom-cart .group-button a.button.checkout,
			.tagcloud a:focus, 
			.tagcloud a:hover {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				color: #fff !important;
			}
			.tbay-dropdown-cart .mini_cart_content a.remove:hover, 
			#tbay-top-cart .mini_cart_content a.remove:hover, 
			.tbay-bottom-cart .mini_cart_content a.remove:hover, 
			.cart-popup .mini_cart_content a.remove:hover {
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.tbay-dropdown-cart .offcanvas-close:hover span, 
			#tbay-top-cart .offcanvas-close:hover span, 
			.tbay-bottom-cart .offcanvas-close:hover span, 
			.cart-popup .offcanvas-close:hover span {
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			/*End mini cart*/ 
			/*Icon*/
			.widget-features .image-inner, 
			.widget-features .icon-inner, .widget-features.contact-us .feature-box-group .feature-box:hover .icon-inner {
				color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.widget-features .feature-box-group .feature-box:hover .icon-inner {
				color: #fff !important;
			}
			.widget-features.style3 .feature-box-group .feature-box:before, .widget-features.style3 .feature-box-group .feature-box:hover .icon-inner {
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;	
			}
			/*End icon*/
			/*Button cart*/
			.widget-products.special .product-block .caption .groups-button .add-cart a.button:hover, 
			.widget-products.widget-special .product-block .caption .groups-button .add-cart a.button:hover, 
			.widget-products.carousel-special .product-block .caption .groups-button .add-cart a.button:hover, 
			.widget-products.widget-carousel-special .product-block .caption .groups-button .add-cart a.button:hover,
			.widget-products.special .product-block .caption .groups-button .add-cart a.button, 
			.widget-products.widget-special .product-block .caption .groups-button .add-cart a.button, 
			.widget-products.carousel-special .product-block .caption .groups-button .add-cart a.button, 
			.widget-products.widget-carousel-special .product-block .caption .groups-button .add-cart a.button {
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.product-block .yith-wcwl-wishlistexistsbrowse > a, 
			.product-block .yith-wcwl-wishlistaddedbrowse > a {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			/*End button cart*/
			.widget-product-tabs.style-tab2 .nav-tabs > li.active > a, 
			.widget-product-tabs.style-tab2 .nav-tabs > li.active > a:hover, 
			.widget-product-tabs.style-tab2 .nav-tabs > li.active a:focus, 
			.widget-product-tabs.style-tab2 .nav-tabs > li:hover > a, 
			.widget-product-tabs.style-tab2 .nav-tabs > li:hover > a:hover, 
			.widget-product-tabs.style-tab2 .nav-tabs > li:hover a:focus, 
			.widget-categoriestabs.style-tab2 .nav-tabs > li.active > a, 
			.widget-categoriestabs.style-tab2 .nav-tabs > li.active > a:hover, 
			.widget-categoriestabs.style-tab2 .nav-tabs > li.active a:focus, 
			.widget-categoriestabs.style-tab2 .nav-tabs > li:hover > a, 
			.widget-categoriestabs.style-tab2 .nav-tabs > li:hover > a:hover, 
			.widget-categoriestabs.style-tab2 .nav-tabs > li:hover a:focus {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				color: #fff !important;
			}
			.widget-categories .owl-carousel.categories .item:hover .cat-name,
			.product-nav .single_nav a:hover, 
			.product-nav .single_nav a:focus {
				color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.widget-categories .owl-carousel.categories .item .cat-name:after,
			.tbay-addon-button a {
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.widget_price_filter .price_slider_amount .button,
			.widget.yith-woocompare-widget a.compare,
			.pagination span.current, .pagination a.current, .tbay-pagination span.current, .tbay-pagination a.current,
			.pagination a:hover, .tbay-pagination a:hover{
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.pagination a:hover, .tbay-pagination a:hover{
				color: #fff !important;
			}
			.categorymenu .widgettitle:before, .widget_tbay_custom_menu .widgettitle:before{
				background-color: transparent !important;
			}
			.categorymenu .menu-category-menu-container ul li a:hover, .widget_tbay_custom_menu .menu-category-menu-container ul li a:hover{
				border-right-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			#tbay-header.header-v4 .header-main .tbay-mainmenu .btn-offcanvas:hover,
			#tbay-header.header-v5 .right-item .tbay-mainmenu .btn-offcanvas:hover{
				border-right-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}

			.top-footer .widget-newletter .input-group .btn.btn-default{
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.widget-testimonials.v2 .testimonials-body .description i,
			.vc_blog .title-heading-blog a,
			.meta-info span.author a,
			#tbay-footer .top-footer .txt2 strong,
			#tbay-footer .ft-contact-info .txt1 i,
			#tbay-footer .ft-contact-info .txt3,
			.navbar-nav.megamenu > li.active > a i,
			.navbar-nav.megamenu > li > a:hover i, .navbar-nav.megamenu > li > a:active i,
			.dropdown-menu > .active > a, .dropdown-menu > .active > a:hover, .dropdown-menu > .active > a:focus,
			.navbar-nav.megamenu .dropdown-menu > li > a:hover, .navbar-nav.megamenu .dropdown-menu > li > a:active,
			.widget-features.style1 .fbox-image i, .widget-features.style1 .fbox-icon i,
			.categorymenu .menu-category-menu-container ul li a:hover i, .widget_tbay_custom_menu .menu-category-menu-container ul li a:hover i,
			#tbay-header.header-v4 .header-main .top-contact .contact-layoutv4 li i,
			.widget-features.style2 .fbox-image i, .widget-features.style2 .fbox-icon i,
			.tit_heading_v5 a,
			.tbay-breadscrumb .breadscrumb-inner .breadcrumb a,
			.widget_product_categories .product-categories .current-cat > a,
			.contactinfos li i,
			.page-404 .notfound-top h1,
			.widget-categories .owl-carousel.categories .owl-item .item:hover .cat-name,
			.wpb_text_column a,
			.post-grid .entry-title a:hover,
			#tbay-footer a:hover,
			.woocommerce .total .woocs_special_price_code, .tbay-dropdown-cart .total .woocs_special_price_code, .tbay-bottom-cart .total .woocs_special_price_code,
			.footer-device-mobile > * a:hover,
			body.woocommerce-wishlist .footer-device-mobile > .device-wishlist a,
			.topbar-device-mobile .topbar-post .topbar-back,
			.search-device-mobile .show-search.active .icon-magnifier:before,
			.tbay-login:hover i
			{
				color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			
			#tbay-header.header-v5 .box-search-5 .tbay-search-form .button-search:hover{
				background: #fff !important;
				color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
			}
			.widget-testimonials.v2 .testimonials-body:hover,
			.post-grid:hover .entry,
			.product-block.grid:hover,
			.vc_category .box:hover img,
			.products-grid.products .list:hover,
			.singular-shop div.product .flex-control-thumbs .slick-list li img.flex-active,
			.tbay-variations .tawcvs-swatches .swatch.selected,
			.tbay-variations .tawcvs-swatches .swatch:hover,
			.tbay-variations .tawcvs-swatches .swatch:focus
			{
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			
			.tbay-to-top a:hover, .tbay-to-top button.btn-search-totop:hover{
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.singular-shop div.product .information .single_add_to_cart_button,
			.widget-testimonials .owl-carousel .slick-arrow:hover,
			.tbay-offcanvas .offcanvas-head .btn-toggle-canvas:hover, .tbay-offcanvas .offcanvas-head .btn-toggle-canvas:focus, .tbay-offcanvas-main .offcanvas-head .btn-toggle-canvas:hover, .tbay-offcanvas-main .offcanvas-head .btn-toggle-canvas:focus,
			.woocommerce .cart-dropdown.cart-popup .dropdown-menu ul.cart_empty a.wc-continue, .cart-dropdown.cart-popup .dropdown-menu ul.cart_empty a.wc-continue,
			body table.compare-list .add-to-cart td a{
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.group-text.home_3 .signature .job:before,
			#reviews .review_form_wrapper #respond .form-submit input,
			.wpcf7-form input[type="submit"]{
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
			}
			.widget-categoriestabs ul.nav-tabs > li.active, .widget_deals_products ul.nav-tabs > li.active{
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
			}
			
			.yith-compare a.added,
			.product-block .button-wishlist .yith-wcwl-wishlistexistsbrowse.show a,
			.product-block .button-wishlist .yith-wcwl-wishlistaddedbrowse.show a,
			.widget-features.default .feature-box-group .feature-box:hover .icon-inner,
			.widget-features.style2 .feature-box:hover .fbox-icon,
			.yith-wcqv-wrapper #yith-quick-view-content .carousel-controls-v3 .carousel-control:hover
			{
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
			
			}
			.woocommerce .quantity button.minus:focus, .woocommerce .quantity button.minus:hover, 
			.woocommerce-page .quantity button.minus:focus, .woocommerce-page .quantity button.minus:hover, 
			.woocommerce .quantity button.plus:focus, 
			.woocommerce .quantity button.plus:hover, 
			.woocommerce-page .quantity button.plus:focus, 
			.woocommerce-page .quantity button.plus:hover {
				color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.top-cart .dropdown-menu .product-details .product-name:hover,
			.tbay-category-fixed ul li a:hover, .tbay-category-fixed ul li a:active,
			.flex-control-nav .slick-arrow:hover.owl-prev:after, .flex-control-nav .slick-arrow:hover.owl-next:after{
				color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}

			/*Border-color*/
			.tabs-v1 ul.nav-tabs li:hover > a, .tabs-v1 ul.nav-tabs li.active > a,
			.tabs-v1 ul.nav-tabs li:hover > a:hover, .tabs-v1 ul.nav-tabs li:hover > a:focus, .tabs-v1 ul.nav-tabs li.active > a:hover, .tabs-v1 ul.nav-tabs li.active > a:focus,
			.btn-theme
			{
				border-color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
			}
			/*background color*/
			.comment-list .comment-reply-link,
			{
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.widget_deals_products .products-carousel .widget-title::after{
				border-color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?> <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> rgba(0, 0, 0, 0) rgba(0, 0, 0, 0);
			}
			/*Archive shop*/ 
			.tbay-filter .change-view.active, .tbay-filter .change-view:hover {
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
			}
			.categorymenu .menu-category-menu-container ul li:hover .hitarea, 
			.categorymenu .menu-category-menu-container ul li:hover > a, 
			.widget_tbay_custom_menu .menu-category-menu-container ul li:hover .hitarea, 
			.widget_tbay_custom_menu .menu-category-menu-container ul li:hover > a,
			.tbay-filter .SumoSelect > .CaptionCont:hover,
			.product-top-sidebar .button-product-top:focus, 
			.product-top-sidebar .button-product-top:hover,
			.widget_product_categories .product-categories a:hover {
				color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.product-block .yith-wcwl-wishlistexistsbrowse > a:hover, 
			.product-block .yith-wcwl-wishlistaddedbrowse > a:hover, 
			.product-block .yith-wcwl-add-to-wishlist > a:hover, 
			.product-block .yith-compare > a:hover, 
			.product-block .add_to_wishlist:hover, 
			.product-block .yith-wcqv-button:hover,
			.product-block.list .yith-wcwl-wishlistexistsbrowse.show a, 
			.product-block.list .yith-wcwl-wishlistaddedbrowse.show a,
			body table.compare-list .add-to-cart td a {
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
			}
			#cboxClose:hover,
			.product-canvas-sidebar .product-canvas-close,
			.yith-wcqv-wrapper .carousel-indicators li.active {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
			}
			.more_products a, .tbay-pagination-load-more a, .show-view-all a {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.yith-wcqv-wrapper .carousel-indicators li {
    			border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
			}
			#cboxClose:hover:before {
				color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;				
			}
			.show-view-all a,
			.widget-categories.widget-grid .show-all:hover {
    			background: #000 !important;
			}
			.btn-default, .btn-theme, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button, .woocommerce-cart .return-to-shop .button, .singular-shop div.product .information .single_add_to_cart_button, .woocommerce #payment #place_order, .woocommerce-page #payment #place_order, .woocommerce-page .woocommerce-message .button, .yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a, .woocommerce .woocommerce-MyAccount-content a.button, .woocommerce .woocommerce-MyAccount-content input.button,.singular-shop div.product .information .single_add_to_cart_button.added + a {
				color: #fff;
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}

			/*Buy now*/
			#shop-now.has-buy-now .tbay-buy-now,
			#shop-now.has-buy-now .disabled.tbay-buy-now {
				color: #fff;
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;	
			}

			.btn-default:hover, .btn-default:focus, .btn-default:active, .btn-default.active, .btn-theme:hover, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button:hover, .woocommerce-cart .return-to-shop .button:hover, .singular-shop div.product .information .single_add_to_cart_button:hover, .woocommerce #payment #place_order:hover, .woocommerce-page #payment #place_order:hover, .woocommerce-page .woocommerce-message .button:hover, .yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button:hover, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a:hover, .woocommerce .woocommerce-MyAccount-content a.button:hover, .woocommerce .woocommerce-MyAccount-content input.button:hover, .btn-theme:focus, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button:focus, .woocommerce-cart .return-to-shop .button:focus, .singular-shop div.product .information .single_add_to_cart_button:focus, .woocommerce #payment #place_order:focus, .woocommerce-page #payment #place_order:focus, .woocommerce-page .woocommerce-message .button:focus, .yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button:focus, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a:focus, .woocommerce .woocommerce-MyAccount-content a.button:focus, .woocommerce .woocommerce-MyAccount-content input.button:focus, .btn-theme:active, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button:active, .woocommerce-cart .return-to-shop .button:active, .singular-shop div.product .information .single_add_to_cart_button:active, .woocommerce #payment #place_order:active, .woocommerce-page #payment #place_order:active, .woocommerce-page .woocommerce-message .button:active, .yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button:active, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a:active, .woocommerce .woocommerce-MyAccount-content a.button:active, .woocommerce .woocommerce-MyAccount-content input.button:active, .btn-theme.active, .woocommerce-cart .wc-proceed-to-checkout a.active.checkout-button, .woocommerce-cart .return-to-shop .active.button, .singular-shop div.product .information .active.single_add_to_cart_button, .woocommerce #payment .active#place_order, .woocommerce-page #payment .active#place_order, .woocommerce-page .woocommerce-message .active.button, .yith-wcqv-wrapper #yith-quick-view-content .summary .active.single_add_to_cart_button, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a.active, .woocommerce .woocommerce-MyAccount-content a.active.button, .woocommerce .woocommerce-MyAccount-content input.active.button,.singular-shop div.product .information .single_add_to_cart_button.added + a:hover {
				color: #fff;
				background-color: #232323 !important;
				border-color: #232323 !important;
			}
			.tbay-dropdown-cart a.wc-continue, #tbay-top-cart a.wc-continue, 
			.tbay-bottom-cart a.wc-continue, .cart-popup a.wc-continue,
			.widget-testimonials .owl-carousel .slick-arrow:hover, 
			.widget-brands .owl-carousel .slick-arrow:hover {
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.cart-bottom .update:hover,
			.woocommerce .continue-to-shop a:hover {
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;	
			}
			.shop_table a.remove:hover {
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;	
				background: <?php echo esc_html( puca_tbay_get_config('main_color') )?> !important;
			}
			/*Blog*/
			.layout-blog .post-list > article:hover .entry-thumb .post-type, .layout-blog .post-list > article:hover .content-image .post-type, .layout-blog .post-list > article:hover .link-wrap .post-type, .layout-blog .post-list > article:hover .owl-carousel-play .post-type {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.btn-default, .btn-theme, #comments .form-submit input[type="submit"] {
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.entry-title a:hover,
			.style-center .layout-blog .post-list > article .entry-category a,
			.sidebar .search-form > form .btn:hover i, .product-top-sidebar .search-form > form .btn:hover i, .product-canvas-sidebar .search-form > form .btn:hover i, .related-posts .search-form > form .btn:hover i, .blog-top-search .search-form > form .btn:hover i, .blog-top-sidebar1 .search-form > form .btn:hover i,
			.widget_tbay_posts .vertical .entry-category a,
			.entry-single .entry-category a,
			.single-project.has-gallery .project-meta ul li a:hover, 
			.single-project.no-gallery .project-meta ul li a:hover, .elementor-text-editor a {
				color: 	<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			/*Other page*/
			.woocommerce-account button[type="submit"], 
			.woocommerce-account input[type="submit"],
			.filter-options.btn-group .btn.active,
			.filter-options.btn-group .btn:hover,
			#projects_list .item-col .work-overlay a:hover,
			.single-project.has-gallery .project-meta .url .tbay-button, 
			.single-project.no-gallery .project-meta .url .tbay-button,
			.ourteam-inner .avarta .social-link li a,
			#add_payment_method #payment div.form-row #place_order, .woocommerce-cart #payment div.form-row #place_order, .woocommerce-checkout #payment div.form-row #place_order {
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.my-account form.login > p a,
			.page-portfolio .entry-header .author a,
			.vc_toggle.vc_toggle_active h4,
			.vc_toggle .vc_toggle_title:hover h4,
			.checkout form.checkout .subtitle a,
			#add_payment_method #payment label a, .woocommerce-cart #payment label a, .woocommerce-checkout #payment label a {
				color: 	<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.single-project .project > .project-carousel #owl-slider-one-img .slick-arrow:hover:after,
			.woocommerce #tbay-top-cart a.wc-continue, #tbay-top-cart a.wc-continue, #tbay-bottom-cart a.wc-continue,
			.woocommerce #tbay-top-cart a.wc-continue:hover, .woocommerce #tbay-top-cart a.wc-continue.checkout, #tbay-top-cart a.wc-continue:hover, #tbay-top-cart a.wc-continue.checkout, #tbay-bottom-cart a.wc-continue:hover, #tbay-bottom-cart a.wc-continue.checkout{
				background:  <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			} 
			.checkout .form-row input[type="submit"],
			.widget-categoriestabs .woocommerce .btn-view-all, 
			.widget_deals_products .woocommerce .btn-view-all,
			.widget-categories.widget-grid .show-all
			{
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			/*Account*/
			.woocommerce .woocommerce-MyAccount-navigation ul li.is-active a, 
			.woocommerce .woocommerce-MyAccount-navigation ul li:hover a, 
			.woocommerce .woocommerce-MyAccount-navigation ul li:focus a {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			<?php endif; ?>

			/*Buy now*/
			@media (max-width: 767px) {
				#shop-now.has-buy-now .single_add_to_cart_button:hover, 
				#shop-now.has-buy-now .single_add_to_cart_button:focus,
				#shop-now.has-buy-now .single_add_to_cart_button + .added_to_cart.wc-forward:hover, #shop-now.has-buy-now .single_add_to_cart_button + .added_to_cart.wc-forward:focus,
				#shop-now.has-buy-now .tbay-buy-now.button:hover, 
				#shop-now.has-buy-now .tbay-buy-now.button:focus {
					background:  <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
					border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;	
				}
				.product-block .yith-wcwl-add-to-wishlist > div.yith-wcwl-add-button a.delete_item {
					color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				}
			}

			/**********************************button hover*****************************/
			<?php if ( puca_tbay_get_config('button_hover_color') != "" ) : ?>
				.btn-theme:hover,.btn-theme:active,
				.archive-shop div.product .information .single_add_to_cart_button:hover,
				.archive-shop div.product .information .single_add_to_cart_button:active {
					background:<?php echo esc_html( puca_tbay_get_config('button_hover_color') ) ?> !important;
				}
			<?php endif; ?>

			/***************************************************************/
			/* Top Bar *****************************************************/
			/***************************************************************/
			/* Top Bar Backgound */
			<?php $topbar_bg = puca_tbay_get_config('topbar_bg'); ?>
			<?php if ( !empty($topbar_bg) ) :
				$image = isset($topbar_bg['background-image']) ? str_replace(array('http://', 'https://'), array('//', '//'), $topbar_bg['background-image']) : '';
				$topbar_bg_image = $image && $image != 'none' ? 'url('.esc_url($image).')' : $image;
			?>
				#tbay-topbar .top-bar,
				#tbay-header .header-bottom-main,
				#tbay-header .header-category-logo,
				#tbay-header.header-v4 .top-bar,
				.header-bottom-main,
				#tbay-header.header-v10 .top-bar {
					<?php if ( isset($topbar_bg['background-color']) && $topbar_bg['background-color'] ): ?>
				    background-color: <?php echo esc_html( $topbar_bg['background-color'] ) ?>;
				    <?php endif; ?>
				    <?php if ( isset($topbar_bg['background-repeat']) && $topbar_bg['background-repeat'] ): ?>
				    background-repeat: <?php echo esc_html( $topbar_bg['background-repeat'] ) ?>;
				    <?php endif; ?>
				    <?php if ( isset($topbar_bg['background-size']) && $topbar_bg['background-size'] ): ?>
				    background-size: <?php echo esc_html( $topbar_bg['background-size'] ) ?>;
				    <?php endif; ?>
				    <?php if ( isset($topbar_bg['background-attachment']) && $topbar_bg['background-attachment'] ): ?>
				    background-attachment: <?php echo esc_html( $topbar_bg['background-attachment'] ) ?>;
				    <?php endif; ?>
				    <?php if ( isset($topbar_bg['background-position']) && $topbar_bg['background-position'] ): ?>
				    background-position: <?php echo esc_html( $topbar_bg['background-position'] ) ?>;
				    <?php endif; ?>
				    <?php if ( $topbar_bg_image ): ?>
				    background-image: <?php echo esc_html( $topbar_bg_image ) ?>;
				    <?php endif; ?>
				}
			<?php endif; ?>
			/* Top Bar Color */
			<?php if ( puca_tbay_get_config('topbar_text_color') != "" ) : ?>
				#tbay-topbar .top-bar,
				#tbay-header .header-bottom-main ,
				#tbay-header .header-category-logo,
				.widget-categories.widget-grid .item-cat .cat-name span {
					color: <?php echo esc_html(puca_tbay_get_config('topbar_text_color')); ?>;
				}
			<?php endif; ?>
			/* Top Bar Link Color */
			<?php if ( puca_tbay_get_config('topbar_link_color') != "" ) : ?>
				#tbay-topbar .top-bar a,
				#tbay-header .header-bottom-main a, 
				#tbay-header .header-category-logo a,
				#tbay-header.header-v13 .header-category-logo .widget_tbay_list_categories .item-cat .cat-name {
					color: <?php echo esc_html(puca_tbay_get_config('topbar_link_color')); ?>;
				}
			<?php endif; ?>			

			/* Top Bar Link Color Hover*/
			<?php if ( puca_tbay_get_config('topbar_link_color_hover') != "" ) : ?>
				#tbay-topbar .top-bar a:hover,
				#tbay-header .header-bottom-main a:hover,
				#tbay-header .header-category-logo a:hover,
				#tbay-header.header-v13 .header-category-logo .widget_tbay_list_categories .item-cat .cat-name:hover {
					color: <?php echo esc_html(puca_tbay_get_config('topbar_link_color_hover')); ?>;
				}
			<?php endif; ?>

			/***************************************************************/
			/* Header *****************************************************/
			/***************************************************************/
			/* Header Backgound */
			<?php $header_bg = puca_tbay_get_config('header_bg'); ?>
			<?php if ( !empty($header_bg) ) :
				$image = isset($header_bg['background-image']) ? str_replace(array('http://', 'https://'), array('//', '//'), $header_bg['background-image']) : '';
				$header_bg_image = $image && $image != 'none' ? 'url('.esc_url($image).')' : $image;
			?>
				#tbay-header .header-main,
				#tbay-header.header-v4 #tbay-topbar >.container-full,
				#tbay-header.header-v14 .header-left .content {
					<?php if ( isset($header_bg['background-color']) && $header_bg['background-color'] ): ?>
				    background-color: <?php echo esc_html( $header_bg['background-color'] ) ?>;
				    <?php endif; ?>
				    <?php if ( isset($header_bg['background-repeat']) && $header_bg['background-repeat'] ): ?>
				    background-repeat: <?php echo esc_html( $header_bg['background-repeat'] ) ?>;
				    <?php endif; ?>
				    <?php if ( isset($header_bg['background-size']) && $header_bg['background-size'] ): ?>
				    background-size: <?php echo esc_html( $header_bg['background-size'] ) ?>;
				    <?php endif; ?>
				    <?php if ( isset($header_bg['background-attachment']) && $header_bg['background-attachment'] ): ?>
				    background-attachment: <?php echo esc_html( $header_bg['background-attachment'] ) ?>;
				    <?php endif; ?>
				    <?php if ( isset($header_bg['background-position']) && $header_bg['background-position'] ): ?>
				    background-position: <?php echo esc_html( $header_bg['background-position'] ) ?>;
				    <?php endif; ?>
				    <?php if ( $header_bg_image ): ?>
				    background-image: <?php echo esc_html( $header_bg_image ) ?>;
				    <?php endif; ?>
				}
			<?php endif; ?>
			/* Header Color */
			<?php if ( puca_tbay_get_config('header_text_color') != "" ) : ?>
				#tbay-header,
				#tbay-header.header-v4 #tbay-topbar >.container-full,
				#tbay-header.header-v14 .header-left .content{
					color: <?php echo esc_html(puca_tbay_get_config('header_text_color')); ?>;
				}
				
			<?php endif; ?>
			/* Header Link Color */
			<?php if ( puca_tbay_get_config('header_link_color') != "" ) : ?>
				#tbay-header a,
				#tbay-header .list-inline.acount li a.login,
				#tbay-header .tbay-login i, 
				#tbay-header .search-modal .btn-search-totop,
				#tbay-header .cart-dropdown .cart-icon i,
				#tbay-header.header-v5 .active-mobile button,
				#tbay-header.header-v7 .tbay-mainmenu .active-mobile button,
				#tbay-header #cart .mini-cart .qty,
				#tbay-header.header-v14 .header-left .content a{
					color: <?php echo esc_html(puca_tbay_get_config('header_link_color'));?> ;
				}
			<?php endif; ?>
			/* Header Link Color Active */
			<?php if ( puca_tbay_get_config('header_link_color_active') != "" ) : ?>
				#tbay-header .active > a,
				#tbay-header .tbay-login i:hover,
				#tbay-header a:active,
				#tbay-header .search-modal .btn-search-totop:hover,
				#tbay-header .cart-dropdown .cart-icon:hover i,
				#tbay-header .cart-dropdown:hover .qty,
				#tbay-header.header-v5 .active-mobile button:hover,
				#tbay-header.header-v7 .tbay-mainmenu .active-mobile button:hover,
				#tbay-header #cart .mini-cart a:hover,
				#tbay-header.header-v14 .header-left .content a:hover {
					color: <?php echo esc_html(puca_tbay_get_config('header_link_color_active')); ?>;
				}
			<?php endif; ?>


			/* Menu Link Color */
			<?php if ( puca_tbay_get_config('main_menu_link_color') != "" ) : ?>
				.dropdown-menu .menu li a,
				.navbar-nav.megamenu .dropdown-menu > li > a,
				.navbar-nav.megamenu > li > a,
				.tbay-offcanvas-main .navbar-nav > li > a
				{
					color: <?php echo esc_html(puca_tbay_get_config('main_menu_link_color'));?> !important;
				}
			<?php endif; ?>
			/* Menu Link Color Active */
			<?php if ( puca_tbay_get_config('main_menu_link_color_active') != "" ) : ?>
				.navbar-nav.megamenu > li.active > a,
				.navbar-nav.megamenu > li > a:hover,
				.navbar-nav.megamenu > li > a:active,
				.navbar-nav.megamenu .dropdown-menu > li.active > a,
				.navbar-nav.megamenu .dropdown-menu > li > a:hover,
				.dropdown-menu .menu li a:hover,
				.dropdown-menu .menu li.active > a,
				.tbay-offcanvas-main .navbar-nav > li.active > a,
				.tbay-offcanvas-main .navbar-nav > li.hover > a,
				.tbay-offcanvas-main  .dropdown-menu > li.active > a,
				.tbay-offcanvas-main  .dropdown-menu > li > a:hover,
				.tbay-offcanvas-main .navbar-nav li.active > a, 
				.tbay-offcanvas-main .navbar-nav li:hover > a
				{
					color: <?php echo esc_html(puca_tbay_get_config('main_menu_link_color_active')); ?> !important;
				}
				.navbar-nav.megamenu > li > a:before,
				.verticle-menu .navbar-nav > li.active, .verticle-menu .navbar-nav > li:hover {
					background: <?php echo esc_html(puca_tbay_get_config('main_menu_link_color_active')); ?> !important;
				}
				.verticle-menu .navbar-nav > li.active > a, .verticle-menu .navbar-nav > li:hover > a {
					color: #fff !important;
				}
			}
			<?php endif; ?>


			/***************************************************************/
			/* Footer *****************************************************/
			/***************************************************************/
			/* Footer Backgound */
			<?php $footer_bg = puca_tbay_get_config('footer_bg'); ?>
			<?php if ( !empty($footer_bg) ) :
				$image = isset($footer_bg['background-image']) ? str_replace(array('http://', 'https://'), array('//', '//'), $footer_bg['background-image']) : '';
				$footer_bg_image = $image && $image != 'none' ? 'url('.esc_url($image).')' : $image;
			?>
				#tbay-footer, .bottom-footer {
					<?php if ( isset($footer_bg['background-color']) && $footer_bg['background-color'] ): ?>
				    background-color: <?php echo esc_html( $footer_bg['background-color'] ) ?> !important;
				    <?php endif; ?>
				    <?php if ( isset($footer_bg['background-repeat']) && $footer_bg['background-repeat'] ): ?>
				    background-repeat: <?php echo esc_html( $footer_bg['background-repeat'] ) ?>;
				    <?php endif; ?>
				    <?php if ( isset($footer_bg['background-size']) && $footer_bg['background-size'] ): ?>
				    background-size: <?php echo esc_html( $footer_bg['background-size'] ) ?>;
				    <?php endif; ?>
				    <?php if ( isset($footer_bg['background-attachment']) && $footer_bg['background-attachment'] ): ?>
				    background-attachment: <?php echo esc_html( $footer_bg['background-attachment'] ) ?>;
				    <?php endif; ?>
				    <?php if ( isset($footer_bg['background-position']) && $footer_bg['background-position'] ): ?>
				    background-position: <?php echo esc_html( $footer_bg['background-position'] ) ?>;
				    <?php endif; ?>
				    <?php if ( $footer_bg_image ): ?>
				    background-image: <?php echo esc_html( $footer_bg_image ) ?>;
				    <?php endif; ?>
				}
			<?php endif; ?>
			/* Footer Heading Color*/
			<?php if ( puca_tbay_get_config('footer_heading_color') != "" ) : ?>
				#tbay-footer h1, #tbay-footer h2, #tbay-footer h3, #tbay-footer h4, #tbay-footer h5, #tbay-footer h6 ,#tbay-footer .widget-title {
					color: <?php echo esc_html(puca_tbay_get_config('footer_heading_color')); ?> !important;
				}
			<?php endif; ?>
			/* Footer Color */
			<?php if ( puca_tbay_get_config('footer_text_color') != "" ) : ?>
				#tbay-footer {
					color: <?php echo esc_html(puca_tbay_get_config('footer_text_color')); ?>;
				}
			<?php endif; ?>
			/* Footer Link Color */
			<?php if ( puca_tbay_get_config('footer_link_color') != "" ) : ?>
				#tbay-footer a {
					color: <?php echo esc_html(puca_tbay_get_config('footer_link_color')); ?>;
				}
			<?php endif; ?>

			/* Footer Link Color Hover*/
			<?php if ( puca_tbay_get_config('footer_link_color_hover') != "" ) : ?>
				#tbay-footer a:hover,
				#tbay-footer .menu > li:hover > a {
					color: <?php echo esc_html(puca_tbay_get_config('footer_link_color_hover')); ?>;
				}
			<?php endif; ?>




			/***************************************************************/
			/* Copyright *****************************************************/
			/***************************************************************/
			/* Copyright Backgound */
			<?php $copyright_bg = puca_tbay_get_config('copyright_bg'); ?>
			<?php if ( !empty($copyright_bg) ) :
				$image = isset($copyright_bg['background-image']) ? str_replace(array('http://', 'https://'), array('//', '//'), $copyright_bg['background-image']) : '';
				$copyright_bg_image = $image && $image != 'none' ? 'url('.esc_url($image).')' : $image;
			?>
				.tbay-copyright {
					<?php if ( isset($copyright_bg['background-color']) && $copyright_bg['background-color'] ): ?>
				    background-color: <?php echo esc_html( $copyright_bg['background-color'] ) ?> !important;
				    <?php endif; ?>
				    <?php if ( isset($copyright_bg['background-repeat']) && $copyright_bg['background-repeat'] ): ?>
				    background-repeat: <?php echo esc_html( $copyright_bg['background-repeat'] ) ?>;
				    <?php endif; ?>
				    <?php if ( isset($copyright_bg['background-size']) && $copyright_bg['background-size'] ): ?>
				    background-size: <?php echo esc_html( $copyright_bg['background-size'] ) ?>;
				    <?php endif; ?>
				    <?php if ( isset($copyright_bg['background-attachment']) && $copyright_bg['background-attachment'] ): ?>
				    background-attachment: <?php echo esc_html( $copyright_bg['background-attachment'] ) ?>;
				    <?php endif; ?>
				    <?php if ( isset($copyright_bg['background-position']) && $copyright_bg['background-position'] ): ?>
				    background-position: <?php echo esc_html( $copyright_bg['background-position'] ) ?>;
				    <?php endif; ?>
				    <?php if ( $copyright_bg_image ): ?>
				    background-image: <?php echo esc_html( $copyright_bg_image ) ?> !important;
				    <?php endif; ?>
				}
			<?php endif; ?>

			/* Footer Color */
			<?php if ( puca_tbay_get_config('copyright_text_color') != "" ) : ?>
				.tbay-copyright,
				.tbay-footer .tb-copyright p {
					color: <?php echo esc_html(puca_tbay_get_config('copyright_text_color')); ?>;
				}
			<?php endif; ?>
			/* Footer Link Color */
			<?php if ( puca_tbay_get_config('copyright_link_color') != "" ) : ?>
				.tbay-copyright a,
				#tbay-footer .tb-copyright a {
					color: <?php echo esc_html(puca_tbay_get_config('copyright_link_color')); ?>;
				}
			<?php endif; ?>

			/* Footer Link Color Hover*/
			<?php if ( puca_tbay_get_config('copyright_link_color_hover') != "" ) : ?>
				.tbay-copyright a:hover,
				.tbay-footer .tb-copyright a:hover {
					color: <?php echo esc_html(puca_tbay_get_config('copyright_link_color_hover')); ?>;
				}
			<?php endif; ?>
 
			/* Woocommerce Breadcrumbs */
			<?php if ( puca_tbay_get_config('breadcrumbs') == "0" ) : ?>
			.woocommerce .woocommerce-breadcrumb,
			.woocommerce-page .woocommerce-breadcrumb
			{
				display:none;
			}
			<?php endif; ?>


			<?php if ( $logo_img_width != "" ) : ?>
			.site-header .logo img {
	            max-width: <?php echo esc_html( $logo_img_width ); ?>px;
	        } 
	        <?php endif; ?>

	        <?php if ( $logo_padding != "" ) : ?>
	        .site-header .logo img {  
	            padding-top: <?php echo esc_html( $logo_padding['padding-top'] ); ?>;
	            padding-right: <?php echo esc_html( $logo_padding['padding-right'] ); ?>;
	            padding-bottom: <?php echo esc_html( $logo_padding['padding-bottom'] ); ?>;
	            padding-left: <?php echo esc_html( $logo_padding['padding-left'] ); ?>;
	        }
	        <?php endif; ?>


	        @media (max-width: 1024px) {

	        	<?php if ( $logo_tablets_img_width != "" ) : ?>
	            /* Limit logo image height for tablets according to tablets header height */
	            .logo-tablet a img {
	               	max-width: <?php echo esc_html( $logo_tablets_img_width ); ?>px;
	            }     
	            <?php endif; ?>       

	            <?php if ( $logo_tablets_padding != "" ) : ?>
	            .logo-tablet a img {
		            padding-top: <?php echo esc_html( $logo_tablets_padding['padding-top'] ); ?>;
		            padding-right: <?php echo esc_html( $logo_tablets_padding['padding-right'] ); ?>;
		            padding-bottom: <?php echo esc_html( $logo_tablets_padding['padding-bottom'] ); ?>;
		            padding-left: <?php echo esc_html( $logo_tablets_padding['padding-left'] ); ?>;
	            }
	            <?php endif; ?>

	        }	        

	        @media (max-width: 768px) {

	        	<?php if ( $logo_img_width_mobile != "" ) : ?>
	            /* Limit logo image height for mobile according to mobile header height */
	            .mobile-logo a img {
	               	max-width: <?php echo esc_html( $logo_img_width_mobile ); ?>px;
	            }     
	            <?php endif; ?>       

	            <?php if ( $logo_mobile_padding != "" ) : ?>
	            .mobile-logo a img {
		            padding-top: <?php echo esc_html( $logo_mobile_padding['padding-top'] ); ?>;
		            padding-right: <?php echo esc_html( $logo_mobile_padding['padding-right'] ); ?>;
		            padding-bottom: <?php echo esc_html( $logo_mobile_padding['padding-bottom'] ); ?>;
		            padding-left: <?php echo esc_html( $logo_mobile_padding['padding-left'] ); ?>;
	            }
	            <?php endif; ?>


				<?php if ( puca_tbay_get_config('main_color') != "" ) : ?>

					.product-block .yith-wcwl-wishlistexistsbrowse > a,
					.product-block .yith-wcwl-wishlistaddedbrowse > a,
					.product-block .button-wishlist .yith-wcwl-wishlistexistsbrowse.show a,
					.product-block .button-wishlist .yith-wcwl-wishlistaddedbrowse.show a {
						border-color: transparent !important;
   			 			background: transparent !important;
						color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
					}
					.product-block.list .yith-wcwl-wishlistexistsbrowse.show a,
					.product-block.list .yith-wcwl-wishlistaddedbrowse.show a {
						border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
						background: transparent !important;
						color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
					}
					.flex-control-nav .slick-arrow:hover.owl-prev, .flex-control-nav .slick-arrow:hover.owl-next {
						background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			        }
					
		            .flex-control-nav .slick-arrow:hover.owl-prev:after, .flex-control-nav .slick-arrow:hover.owl-next:after {
						color: #fff !important;
					}
				<?php endif; ?>

	        }

			/* Custom CSS */
	        <?php 
	        if( $custom_css != '' ) {
	            echo trim($custom_css);
	        }
	        if( $css_desktop != '' ) {
	            echo '@media (min-width: 1024px) { ' . ($css_desktop) . ' }'; 
	        }
	        if( $css_tablet != '' ) {
	            echo '@media (min-width: 768px) and (max-width: 1023px) {' . ($css_tablet) . ' }'; 
	        }
	        if( $css_wide_mobile != '' ) {
	            echo '@media (min-width: 481px) and (max-width: 767px) { ' . ($css_wide_mobile) . ' }'; 
	        }
	        if( $css_mobile != '' ) {
	            echo '@media (max-width: 480px) { ' . ($css_mobile) . ' }'; 
	        }
	        ?>


	<?php
		$content = ob_get_clean();
		$content = str_replace(array("\r\n", "\r"), "\n", $content);
		$lines = explode("\n", $content);
		$new_lines = array();
		foreach ($lines as $i => $line) {
			if (!empty($line)) {
				$new_lines[] = trim($line);
			} 
		}

		$custom_css = implode($new_lines);

		
		return $custom_css;
	}
}

?>