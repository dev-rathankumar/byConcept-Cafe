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
		
		<?php if( $show_typography ) : ?>
		/* Theme Options Styles */
			
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
				p, .btn
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
					h1, h2, h3, h4, h5, h6, .widget-title, btn ,.navbar-nav.megamenu > li > a
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
			.slick-dots li.slick-active button:before,
			.search-modal .btn-search-totop:hover, 
			.search-modal .btn-search-totop:focus,
			.widget_deals_products ul.nav-tabs > li.active > a,
			.widget-testimonials.icon-red .testimonials-body .description:before,
			.name a:hover,
			.post-grid.vertical .entry-content .entry-title a:hover,
			.widget-categories.widget-grid.style2 .item-cat:hover .cat-name,
			.navbar-nav.megamenu > li:hover > a, 
			.navbar-nav.megamenu > li:focus > a, 
			.navbar-nav.megamenu > li.active > a,
			.navbar-nav.megamenu .dropdown-menu .widget ul li.active a,
			.tbay-offcanvas-main .navbar-nav li.active > a, .tbay-offcanvas-main .navbar-nav li:hover > a,
			.widget_deals_products ul.nav-tabs > li:hover a,
			.widget-categories .owl-carousel.categories .item:hover .cat-name,
			.product-nav .single_nav a:hover, 
			.product-nav .single_nav a:focus,
			.widget-testimonials.v2 .testimonials-body .description i,
			.vc_blog .title-heading-blog a,
			.meta-info span.author a,
			#tbay-footer .top-footer .txt2 strong,
			#tbay-footer .ft-contact-info .txt1 i,
			#tbay-footer .ft-contact-info .txt3,
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
			.footer-device-mobile > * a:hover,
			body.woocommerce-wishlist .footer-device-mobile > .device-wishlist a,
			.topbar-device-mobile .topbar-post .topbar-back,
			.search-device-mobile .show-search.active .icon-magnifier:before,
			.woocommerce .quantity button.minus:focus, .woocommerce .quantity button.minus:hover, 
			.woocommerce-page .quantity button.minus:focus, .woocommerce-page .quantity button.minus:hover, 
			.woocommerce .quantity button.plus:focus, 
			.woocommerce .quantity button.plus:hover, 
			.woocommerce-page .quantity button.plus:focus, 
			.woocommerce-page .quantity button.plus:hover,
			.top-cart .dropdown-menu .product-details .product-name:hover,
			#tbay-footer .tagcloud a:hover,
			.tbay-breadscrumb.breadcrumbs-color .breadscrumb-inner .breadcrumb a:hover,
			.tbay-breadscrumb.breadcrumbs-text .breadscrumb-inner .breadcrumb a:hover,
			.treeview a.selected i, .copyright a:hover, .tbay-dropdown-cart.v2 .dropdown-content .mini_cart_content a.remove:hover, #tbay-top-cart.v2 .dropdown-content .mini_cart_content a.remove:hover, .tbay-bottom-cart.v2 .dropdown-content .mini_cart_content a.remove:hover, .cart-popup.v2 .dropdown-content .mini_cart_content a.remove:hover, a.hover,
			.woocommerce div.product .woocommerce-tabs.tabs-v1 ul.tabs li.active>a, .woocommerce div.product .woocommerce-tabs.tabs-v1 ul.tabs li:hover>a,
			.wpcf7-form .required,
			.woocommerce-info:before, .woocommerce-message:before,
			.tbay-variations .reset_variations:hover, .tbay-variations .reset_variations:focus,
			.style-slide .tbay-slider-for .slick-arrow:hover:after,
			.navbar-nav.megamenu>li.active>a, .navbar-nav.megamenu>li:focus>a, .navbar-nav.megamenu>li:hover>a
			{
				color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.questions-section #ywqa-submit-question:hover, .questions-section #ywqa-submit-question:focus {
				background:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.style-slide .tbay-slider-for .slick-arrow:hover {
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.woocommerce-message,.woocommerce-info,
			#content > .products.load-ajax:after {
				border-top-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			a:hover, a:focus, .treeview .sub-menu a:hover:before,
			.navbar-nav.megamenu > li > ul.dropdown-menu li:hover:before,
			.navbar-nav.megamenu > li > ul.dropdown-menu li:focus:before,
			.navbar-nav.megamenu > li > ul.dropdown-menu li.active:before,
			.navbar-nav.megamenu .dropdown-menu .widget ul li.active a,
			.navbar-nav.megamenu .dropdown-menu .widget ul li:hover a,
			.navbar-nav.megamenu .dropdown-menu .widget ul li.active:before,
			.navbar-nav.megamenu .dropdown-menu .widget ul li:hover:before,
			.navbar-nav.megamenu > li:hover > a i, .navbar-nav.megamenu > li:hover > a .caret, .navbar-nav.megamenu > li:focus > a i, .navbar-nav.megamenu > li:focus > a .caret, .navbar-nav.megamenu > li.active > a i, .navbar-nav.megamenu > li.active > a .caret,
			.SumoSelect > .optWrapper > .options li.opt:hover, .SumoSelect > .optWrapper > .options li.opt.selected,
			.SumoSelect > .optWrapper > .options li.opt:hover:before, .SumoSelect > .optWrapper > .options li.opt.selected:before,
			#tbay-header .header-main .header-right .top-cart-wishlist a:hover .cart-icon i,
			#tbay-main-content .tbay_custom_menu > .widget.widget_nav_menu .menu > li.active > a,
			#tbay-main-content .tbay_custom_menu > .widget.widget_nav_menu .menu > li:hover > a,
			#tbay-main-content .tbay_custom_menu > .widget.widget_nav_menu .menu > li.active > a i,
			#tbay-main-content .tbay_custom_menu > .widget.widget_nav_menu .menu > li:hover > a i,
			.owl-carousel .slick-arrow:hover, .owl-carousel .slick-arrow:focus,
			.tbay-category-fixed ul li:last-child a i,
			#tbay-footer .menu > li:hover > a,
			#tbay-header.header-v3 .tbay-search-form .button-search,
			.woocommerce div.product div.images .woocommerce-product-gallery__trigger:hover,
			.layout-blog .post-list > article .entry-title a:hover,
			.related-posts .entry-title a:hover,
			.page-404 .sub-title .backtohome,
			.treeview a.selected,
			.sidebar ul.woof_list li .hover, .product-top-sidebar ul.woof_list li .hover, .product-canvas-sidebar ul.woof_list li .hover, .related-posts ul.woof_list li .hover, .blog-top-search ul.woof_list li .hover, .blog-top-sidebar1 ul.woof_list li .hover,
			.cart-bottom .update input,
			.cart-bottom .update:before,
			.topbar-device-mobile .cart-dropdown .mini-cart .cart-icon i,
			.footer-device-mobile > *.active a i,
			.widget_tbay_custom_menu .menu-category-menu-container ul li:hover i,
			.widget_categories > ul li.current-cat a, .widget_pages > ul li.current-cat a,
			.widget_meta > ul li.current-cat a, .widget_archive > ul li.current-cat a,
			.tbay-search-form .button-search:hover, .tbay-search-form .button-search:focus,
			.header-right .dropdown .account-menu ul li a:hover, .tbay-login .dropdown .account-menu ul li a:hover,
			.owl-carousel .slick-arrow i:hover:before, .flex-control-nav .slick-arrow i:hover:before, .slider .slick-arrow i:hover:before, .widget-newletter .input-group-btn:hover:before,
			#tbay-header .topbar-mobile .btn:hover, #tbay-header .topbar-mobile .btn:focus, .tbay-login.offcanvas a span:hover, .has-after:hover,
			.tbay-offcanvas .offcanvas-head .btn-toggle-canvas:hover, .tbay-offcanvas-main .offcanvas-head .btn-toggle-canvas:hover, .tparrows:hover,
			.has-after:hover, .tbay-footer .none-menu ul.menu a:hover, .widget-categories .tab-menu-wrapper ul.menu a:hover, .readmore:hover, .post-grid.v2 .entry-date .day, .widget-newletter.style-2 .input-group-btn input:hover, .widget-categories:not(.custom) .item-cat:hover .cat-name, .tbay-dropdown-cart .cart_list .product-name:hover, #tbay-top-cart .cart_list .product-name:hover, .tbay-bottom-cart .cart_list .product-name:hover, .cart-popup .cart_list .product-name:hover, .top-info a, #tbay-header.header-v24 .tbay-login .account-button:hover, #tbay-header.header-v24 .navbar-nav.megamenu > li > a:hover, #tbay-header.header-v24 .tbay-topcart a:hover, #tbay-header.header-v24 .tbay-search-min .btn-search-min:hover, #tbay-header.header-v23 .tbay-login .account-button:hover, #tbay-header.header-v23 .navbar-nav.megamenu > li > a:hover, #tbay-header.header-v23 .tbay-topcart .cart-dropdown >a:hover, .sidebar ul.woof_list.woof_list_checkbox li label:hover, .sidebar ul.woof_list.woof_list_checkbox li label.woof_checkbox_label_selected, .product-top-sidebar ul.woof_list.woof_list_checkbox li label:hover, .product-top-sidebar ul.woof_list.woof_list_checkbox li label.woof_checkbox_label_selected, .product-canvas-sidebar ul.woof_list.woof_list_checkbox li label:hover, .product-canvas-sidebar ul.woof_list.woof_list_checkbox li label.woof_checkbox_label_selected, .blog-top-search ul.woof_list.woof_list_checkbox li label:hover, .blog-top-search ul.woof_list.woof_list_checkbox li label.woof_checkbox_label_selected, .blog-top-sidebar1 ul.woof_list.woof_list_checkbox li label:hover, .blog-top-sidebar1 ul.woof_list.woof_list_checkbox li label.woof_checkbox_label_selected, .woocommerce div.product .product_title,
			.singular-shop div.product .information .tbay-social-share > span a, .singular-shop div.product .information .product_meta > span a,
			.single-product .tbay-modalButton:hover, .shop_table .cart_item > span.product-name a:hover, .woocommerce .continue-to-shop a:hover,
			.cart_totals table tr.shipping a, .cart_totals table * tr.shipping a, .coupon .box .input-group-btn:hover:before,
			#add_payment_method #payment div.form-row a, .woocommerce-cart #payment div.form-row a, .woocommerce-checkout #payment div.form-row a,
			.mc4wp-response .mc4wp-alert:after, .tbay-footer ul.menu li.active a, .footer-device-mobile > *.active a, body.woocommerce-wishlist .footer-device-mobile > .device-wishlist a, body.woocommerce-wishlist .footer-device-mobile > .device-wishlist a i, .flex-control-nav.flex-control-thumbs .slick-arrow:hover:after, .style-slide .tbay-slider-for .slick-arrow:hover {
				color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			@media (min-width: 1600px) {
				body.v17 .wishlist:hover, body.v17 .wishlist:hover .count_wishlist, body.v18 .navbar-nav > li > a:hover, body.v18 .top-cart a.mini-cart:hover, body.v18 .header-bottom-vertical .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont:hover, body.v18 .tbay-login a.account-button:hover, body.v16 .header-top-left .account-button:hover, body.v16 .header-top-left .copyright a:hover, body.v16 .header-top-left .mini-cart:hover, body.v16 .header-top-left .tbay-search-form .btn-search-min:hover, body.v17 .header-top-left .account-button:hover, body.v17 .header-top-left .copyright a:hover, body.v17 .header-top-left .mini-cart:hover, body.v17 .header-top-left .tbay-search-form .btn-search-min:hover, body.v18 .header-top-left .account-button:hover, body.v18 .header-top-left .copyright a:hover, body.v18 .header-top-left .mini-cart:hover, body.v18 .header-top-left .tbay-search-form .btn-search-min:hover {
					color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				}
			}
			.woocommerce .cart-dropdown.cart-popup .group-button p.buttons a.button.checkout, .cart-dropdown.cart-popup .group-button p.buttons a.button.checkout, .tbay-addon-video .tbay-modalButton,
			.tbay-dropdown-cart.v2 .group-button p.buttons a.button.checkout, #tbay-top-cart.v2 .group-button p.buttons a.button.checkout, .tbay-bottom-cart.v2 .group-button p.buttons a.button.checkout, .cart-popup.v2 .group-button p.buttons a.button.checkout {
				background:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				color: #fff;
			}

			.navbar-nav.megamenu > li > a:before,
			.tbay-to-top a span,
			#onepage-single-product .shop-now a,
			.sidebar .widget .widget-title:after,
			#reviews .progress .progress-bar-success,
			.treeview .sub-menu a:hover:before
			{
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}

			/*Buy now button*/
			#shop-now.has-buy-now .tbay-buy-now {
				background:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				color: #fff;		
			}

			/*Fix customize 1.3.6*/
			.woocommerce span.onsale .saled,
			.product-block.list .group-buttons > div.add-cart a {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}

			/*End fix customize 1.3.6*/

			.widget-categoriestabs ul.nav-tabs > li:hover, .widget_deals_products ul.nav-tabs > li:hover,
			.widget-testimonials.v2 .testimonials-body:hover,
			.post-grid:hover .entry,
			.vc_category .box:hover img,
			.products-grid.products .list:hover,
			.singular-shop div.product .flex-control-thumbs .slick-list li img.flex-active,
			.widget-categoriestabs ul.nav-tabs > li.active, .widget_deals_products ul.nav-tabs > li.active,
			.tabs-v1 ul.nav-tabs li:hover > a, .tabs-v1 ul.nav-tabs li.active > a,
			.tabs-v1 ul.nav-tabs li:hover > a:hover, .tabs-v1 ul.nav-tabs li:hover > a:focus, .tabs-v1 ul.nav-tabs li.active > a:hover, .tabs-v1 ul.nav-tabs li.active > a:focus,
			.btn-theme,
			.yith-wcqv-wrapper .carousel-indicators li
			{
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.navbar-nav.megamenu > li > ul.dropdown-menu li:hover > a,
			.navbar-nav.megamenu > li > ul.dropdown-menu li:focus > a,
			.navbar-nav.megamenu > li > ul.dropdown-menu li.active > a,
			#tbay-header.header-v2 .header-main .navbar-nav.megamenu > li:hover > a, 
			#tbay-header.header-v2 .header-main .navbar-nav.megamenu > li:focus > a,
			#tbay-header.header-v2 .header-main .navbar-nav.megamenu > li.active > a,
			#tbay-header.header-v3 .header-main .navbar-nav.megamenu > li:hover > a,
			#tbay-header.header-v3 .header-main .navbar-nav.megamenu > li:focus > a,
			#tbay-header.header-v3 .header-main .navbar-nav.megamenu > li.active > a,
			.navbar-nav.megamenu .dropdown-menu .widget ul li.active,
			.navbar-nav.megamenu .dropdown-menu .widget ul li:hover,
			#tbay-header .header-topmenu .tbay-menu-top .dropdown-menu > li:hover > a,
			#tbay-header .header-topmenu .tbay-menu-top .dropdown-menu > li:focus > a,
			#tbay-header .header-topmenu .tbay-menu-top .dropdown-menu > li.active > a,
			#tbay-main-content .tbay_custom_menu > .widget.widget_nav_menu .menu > li > a:hover, 
			#tbay-main-content .tbay_custom_menu > .widget.widget_nav_menu .menu > li > a:focus,
			.sidebar ul.woof_list li > div.checked, .sidebar ul.woof_list li > div:hover, .product-top-sidebar ul.woof_list li > div.checked, .product-top-sidebar ul.woof_list li > div:hover, .product-canvas-sidebar ul.woof_list li > div.checked, .product-canvas-sidebar ul.woof_list li > div:hover, .blog-top-search ul.woof_list li > div.checked, .blog-top-search ul.woof_list li > div:hover, .blog-top-sidebar1 ul.woof_list li > div.checked, .blog-top-sidebar1 ul.woof_list li > div:hover,
			.singular-shop div.product .flex-control-thumbs .slick-list li img.flex-active, .singular-shop div.product .flex-control-thumbs .slick-list li img:hover, #reviews .reviews-summary .review-summary-total .review-summary-result
			{
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			#awesome-font .fontawesome-icon-list .fa-hover:hover .preview,
			.icon-preview-box:hover .preview,
			.widget.widget_tbay_popular_post .post-widget .post-list:hover .meta-info > span.entry-date,
			.navbar-nav.megamenu > li > a:after,
			.btn-theme-2, .has-after:after, .tbay-footer .none-menu ul.menu a:after, .copyright a:after, .widget-categories .tab-menu-wrapper ul.menu a:after, .tbay-offcanvas-main .navbar-nav > li > a:before, .has-after:after, #tbay-header.header-v19 .search-min .btn-search-min,
			.widget-testimonials.v2 .testimonials-body:hover .testimonials-profile:before, .woocommerce a.button:not(.checkout):not(.view-cart):not(.product_type_simple):not(.add_to_cart_button):not(.yith-wcqv-button):not(.continue):not(.view-cart):not(.product_type_external):not(.product_type_grouped), .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit, .woocommerce-checkout .form-row button[type="submit"], .woocommerce-checkout .form-row input[type="submit"], #add_payment_method #payment div.form-row #place_order, .woocommerce-cart #payment div.form-row #place_order, .woocommerce-checkout #payment div.form-row #place_order,
			.page-404 .v2 .backtohome, #comments .form-submit input[type="submit"], .ourteam-inner .avarta .social-link li a,
			.filter-options.btn-group .btn:hover, .filter-options.btn-group .btn.active,
			#projects_list .item-col .work-overlay a:hover, .footer-device-mobile > * a span.count, .footer-device-mobile > * a .mini-cart-items, .entry-description a {
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.singular-shop div.product .information .single_add_to_cart_button,
			.singular-shop div.product .information .single_add_to_cart_button.added + a,
			.woocommerce #tbay-top-cart .group-button p.buttons a.button.checkout, #tbay-top-cart .group-button p.buttons a.button.checkout, #tbay-bottom-cart .group-button p.buttons a.button.checkout,
			.popup-cart .gr-buttons a:first-child {
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				color: #fff;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			@media (max-width: 992px) {
				.topbar-mobile .btn:hover, .topbar-mobile .btn:focus, .topbar-mobile .btn.active,
				.topbar-mobile .search-popup .show-search:hover, .topbar-mobile .search-popup .show-search:focus, .topbar-mobile .search-popup .show-search.active, .topbar-mobile .btn-danger {
					background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
					border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				}
			}

			@media (max-width: 767px) {
				#shop-now.has-buy-now .tbay-buy-now.button:hover, 
				#shop-now.has-buy-now .tbay-buy-now.button:focus,
				#shop-now.has-buy-now .single_add_to_cart_button + .added_to_cart.wc-forward:hover, #shop-now.has-buy-now .single_add_to_cart_button + .added_to_cart.wc-forward:focus,
				#shop-now.has-buy-now .single_add_to_cart_button:hover, #shop-now.has-buy-now .single_add_to_cart_button:focus {
					background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
					border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				}
				.product-block .group-buttons > div.button-wishlist .yith-wcwl-add-button a.delete_item, .product-block .group-image > div.button-wishlist .yith-wcwl-add-button a.delete_item {
					color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				}
			}

			/* Shop */
			.product-block .groups-button-image > div a:hover,
			.product-block .groups-button-image > a a:hover, 
			.product-block .buttons > div a:hover, 
			.product-block .buttons > a a:hover		{
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				color: #fff !important;
			}

			.tbay-dropdown-cart .mini_cart_content a.remove:hover, 
			#tbay-top-cart .mini_cart_content a.remove:hover, 
			.tbay-bottom-cart .mini_cart_content a.remove:hover, 
			.cart-popup .mini_cart_content a.remove:hover,
			.shop_table a.remove:hover, .woocommerce table.wishlist_table .product-add-to-cart .remove_from_wishlist.button, .woocommerce table.cart .product-name a:hover, .woocommerce table.cart .product-name a:focus {
				color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			/*End mini cart*/ 
			
			/*End icon*/
			/*Button cart*/
			.widget-products.special .product-block .caption .groups-button .add-cart a.button:hover, 
			.widget-products.widget-special .product-block .caption .groups-button .add-cart a.button:hover, 
			.widget-products.carousel-special .product-block .caption .groups-button .add-cart a.button:hover, 
			.widget-products.widget-carousel-special .product-block .caption .groups-button .add-cart a.button:hover,
			.widget-products.special .product-block .caption .groups-button .add-cart a.button, 
			.widget-products.widget-special .product-block .caption .groups-button .add-cart a.button, 
			.widget-products.carousel-special .product-block .caption .groups-button .add-cart a.button, 
			.widget-products.widget-carousel-special .product-block .caption .groups-button .add-cart a.button, .widget-features.style3 .feature-box-group .feature-box:before,
			.widget-categories .owl-carousel.categories .item .cat-name:after,
			.widget_deals_products .tbay-countdown .times > div,
			.group-text.home_3 .signature .job:before	
			{
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.widget_price_filter .price_slider_amount .button,
			.widget.yith-woocompare-widget a.compare,
			.pagination span.current, .pagination a.current, .tbay-pagination span.current, .tbay-pagination a.current,
			.pagination a:hover, .tbay-pagination a:hover, .singular-shop div.product .information .yith-wcwl-wishlistexistsbrowse.show > a, .singular-shop div.product .information .yith-wcwl-wishlistaddedbrowse.show > a
			{
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
			.categorymenu .widgettitle:before, .widget_tbay_custom_menu .widgettitle:before{
				background-color: transparent !important;
			}
			.categorymenu .menu-category-menu-container ul li a:hover, .widget_tbay_custom_menu .menu-category-menu-container ul li a:hover {
				border-right-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.widget_product_categories .product-categories a:hover:before, .widget_product_categories ul a:hover:before, .widget_categories .product-categories a:hover:before, .widget_categories ul a:hover:before,
			.widget-features .fbox-icon .icon-inner, .widget_product_categories .product-categories .current-cat > a:before, .widget_product_categories ul .current-cat > a:before, .widget_categories .product-categories .current-cat > a:before, .widget_categories ul .current-cat > a:before {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.tbay-to-top a:hover, .tbay-to-top button.btn-search-totop:hover{
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			body table.compare-list .add-to-cart td a:hover {
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.tbay-slider-for .slick-arrow:hover:after
			{
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			
			.widget-features.style2 .feature-box:hover .fbox-icon,
			.yith-wcqv-wrapper #yith-quick-view-content .carousel-controls-v3 .carousel-control:hover,
			#cboxClose:hover,
			.product-canvas-sidebar .product-canvas-close,
			.yith-wcqv-wrapper .carousel-indicators li.active,
			.widget_price_filter .ui-slider-horizontal .ui-slider-range,
			.widget_price_filter .ui-slider .ui-slider-handle
			{
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
			
			}

			/*background color*/

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
			body table.compare-list .add-to-cart td a:hover {
				background-color: #fff !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
				color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
			}
			#tbay-category-image .category-inside ul.tbay-menu-category > li:after,
			.widget .widget-title span:after, .widget .widgettitle span:after, .widget .widget-heading span:after,
			.tbay-category-fixed ul li a:hover, .tbay-category-fixed ul li a:active,
			.related-posts .entry-content:hover .meta-info > span.entry-date,
			.cart-dropdown .cart-icon .mini-cart-items, .navbar-nav .text-label.label-hot
			{
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}

			#cboxClose:hover:before {
				color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;				
			}
			
			/*Blog*/
			.entry-title a:hover,
			.style-center .layout-blog .post-list > article .entry-category a,
			.sidebar .search-form > form .btn:hover i, .product-top-sidebar .search-form > form .btn:hover i, .product-canvas-sidebar .search-form > form .btn:hover i, .related-posts .search-form > form .btn:hover i, .blog-top-search .search-form > form .btn:hover i, .blog-top-sidebar1 .search-form > form .btn:hover i,
			.widget_tbay_posts .vertical .entry-category a,
			.entry-single .entry-category a,
			.single-project.has-gallery .project-meta ul li a:hover, 
			.single-project.no-gallery .project-meta ul li a:hover {
				color: 	<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			/*Other page*/
			.my-account form.login > p a,
			.page-portfolio .entry-header .author a,
			.vc_toggle.vc_toggle_active h4,
			.vc_toggle .vc_toggle_title:hover h4,
			.checkout form.checkout .subtitle a {
				color: 	<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.single-project .project > .project-carousel #owl-slider-one-img .slick-arrow:hover:after {
				background:  <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			#content .products.load-ajax:after {
				border-top-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;		
			}
			<?php endif; ?>
			/* check main color 2*/ 
			<?php if ( puca_tbay_get_config('main_color2') != "" ) : ?>
			/*color*/
			.tbay-dropdown-cart .cart_list .product-name, #tbay-top-cart .cart_list .product-name, .tbay-bottom-cart .cart_list .product-name, .cart-popup .cart_list .product-name, .owl-carousel .slick-arrow i:before, .flex-control-nav .slick-arrow i:before, .slider .slick-arrow i:before, .entry-title a, .readmore, .copyright a, .widget-categories .owl-carousel .item .cat-label, .tparrows.ohio-2:before,
			.product-block .name a, .ui-autocomplete.ui-widget-content .name a, .tbay-footer .menu.treeview li > a, .contact-info li,
			#tbay-header.header-v14 .search-modal .btn-search-totop:hover, #tbay-header.header-v14 .cart-dropdown .mini-cart:hover, #tbay-header.header-v9 .search-modal .btn-search-totop:hover, #tbay-header.header-v9 .cart-dropdown .mini-cart:hover, .tbay-footer.footer-6 p, .flex-control-nav.flex-control-thumbs .slick-arrow:after,
			.woocommerce .continue-to-shop a, .coupon .box .input-group-btn:before, .testimonials-body .name-client,
			.popup-cart .name a, .style-slide .tbay-slider-for .slick-arrow:after,
			.woocommerce div.product .product_title {
				color:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
			}

			/*Buy now button*/
			#shop-now.has-buy-now .tbay-buy-now:hover,
			#shop-now.has-buy-now .tbay-buy-now:focus, .product-block .group-buttons .yith-wcwl-wishlistexistsbrowse a, .product-block .group-buttons .yith-wcwl-wishlistaddedbrowse a, .product-block .group-image .yith-wcwl-wishlistexistsbrowse a, .product-block .group-image .yith-wcwl-wishlistaddedbrowse a,.product-block .group-buttons > div.button-wishlist .yith-wcwl-add-button a.delete_item, .product-block .group-image > div.button-wishlist .yith-wcwl-add-button a.delete_item {
				background:  <?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;		
			}

			.btn-default, .btn-theme,
			.more_products a, .tbay-pagination-load-more a,.woocommerce .cart-dropdown.cart-popup .group-button p.buttons a.button, .cart-dropdown.cart-popup .group-button p.buttons a.button, .product-block .group-buttons > div a, .product-block .group-image > div a,
			.btn-default, .btn-theme, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button, .woocommerce-cart .return-to-shop .button, .woocommerce #tbay-top-cart a.wc-continue, #tbay-top-cart a.wc-continue, #tbay-bottom-cart a.wc-continue, .woocommerce .cart-dropdown.cart-popup .dropdown-menu ul.cart_empty a.wc-continue, .cart-dropdown.cart-popup .dropdown-menu ul.cart_empty a.wc-continue, .tbay-dropdown-cart a.wc-continue, .tbay-bottom-cart a.wc-continue, .cart-popup a.wc-continue, .tbay-dropdown-cart.v2 .group-button p.buttons a.button, #tbay-top-cart.v2 .group-button p.buttons a.button, .tbay-bottom-cart.v2 .group-button p.buttons a.button, .cart-popup.v2 .group-button p.buttons a.button, .woocommerce #payment #place_order, .woocommerce-page #payment #place_order, .woocommerce-page .woocommerce-message .button, .yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a, .woocommerce .woocommerce-MyAccount-content a.button, .woocommerce .woocommerce-MyAccount-content input.button, .show-view-all a, .more_products a,
			.widget-categories:not(.custom) .show-all, .widget-categories.widget-grid.custom .show-all, .widget-categories .owl-carousel.categories.v3 .show-all, .widget-product-tabs .nav-tabs > li > a, .widget-categoriestabs .nav-tabs > li > a, .btn-theme, .btn-view-all, ul.list-tags li a, .tagcloud a, .more_products a, .single-project.has-gallery .project-meta .url .tbay-button, .single-project.no-gallery .project-meta .url .tbay-button, .woocommerce #tbay-top-cart .group-button a.button, #tbay-top-cart .group-button a.button, #tbay-bottom-cart .group-button a.button,
			.yith-wcqv-wrapper #yith-quick-view-close:hover, .yith-wcqv-wrapper #yith-quick-view-close:focus, .post-password-form input[type=submit], .tbay-to-top #back-to-top, .tbay-to-top a {
				color:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
				border-color:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
			}
			.questions-section #ywqa-submit-question {
				background:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
			}
			.btn-default:hover, .btn-theme:hover,
			.btn-default:focus, .btn-theme:focus,
			.more_products a:focus, .woocommerce .cart-dropdown.cart-popup .group-button p.buttons a.button:hover, .cart-dropdown.cart-popup .group-button p.buttons a.button:hover, .product-block .group-buttons > div a:hover, .product-block .group-image > div a:hover, .tbay-addon-video .tbay-modalButton:hover, .product-block .yith-wcwl-wishlistexistsbrowse.show a, .product-block .yith-wcwl-wishlistaddedbrowse.show a,
			.tbay-dropdown-cart.v2 .group-button p.buttons a.button:hover, #tbay-top-cart.v2 .group-button p.buttons a.button:hover, .tbay-bottom-cart.v2 .group-button p.buttons a.button:hover, .cart-popup.v2 .group-button p.buttons a.button:hover,
			.widget-categories:not(.custom) .show-all:hover, .widget-categories.widget-grid.custom .show-all:hover, .widget-categories .owl-carousel.categories.v3 .show-all:hover, .widget-product-tabs .nav-tabs > li > a:hover, .widget-categoriestabs .nav-tabs > li > a:hover, .btn-default:focus, .widget-categories:not(.custom) .show-all:focus, .widget-categories.widget-grid.custom .show-all:focus, .widget-categories .owl-carousel.categories.v3 .show-all:focus, .widget-product-tabs .nav-tabs > li > a:focus, .widget-categoriestabs .nav-tabs > li > a:focus, .btn-theme:hover, .btn-view-all:hover, ul.list-tags li a:hover, .tagcloud a:hover, .btn-theme:focus, .btn-view-all:focus, ul.list-tags li a:focus, .tagcloud a:focus, .more_products a:hover, .more_products a:focus,
			.widget-product-tabs .nav-tabs > li.active > a, .widget-product-tabs .nav-tabs > li.active > a:hover, .widget-product-tabs .nav-tabs > li.active > a:focus, .widget-product-tabs .nav-tabs > li:hover > a, .widget-product-tabs .nav-tabs > li:hover > a:hover, .widget-product-tabs .nav-tabs > li:hover > a:focus, .widget-categoriestabs .nav-tabs > li.active > a, .widget-categoriestabs .nav-tabs > li.active > a:hover, .widget-categoriestabs .nav-tabs > li.active > a:focus, .widget-categoriestabs .nav-tabs > li:hover > a, .widget-categoriestabs .nav-tabs > li:hover > a:hover, .widget-categoriestabs .nav-tabs > li:hover > a:focus, .show-view-all a:hover,
			.single-project.has-gallery .project-meta .url .tbay-button:hover, .single-project.no-gallery .project-meta .url .tbay-button:hover,
			#tbay-top-cart .group-button a.button:hover, #tbay-bottom-cart .group-button a.button:hover,
			.woocommerce table.wishlist_table .product-add-to-cart .add-cart a:hover,
			.yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button:hover, .post-password-form input[type=submit]:hover,
			.tbay-to-top a:hover, .tbay-to-top button.btn-search-totop:hover {
				background:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
				border-color:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
				color: #fff;
			}
			.woocommerce .cart-dropdown.cart-popup .group-button p.buttons a.button:hover, .cart-dropdown.cart-popup .group-button p.buttons a.button:hover {
				color:#fff !important;
			}
			.btn-theme-2:hover, .btn-theme-2:focus, .slick-dots li.slick-active button, .tp-bullets.ohio-1 .tp-bullet.selected,
			.widget-categories .owl-carousel .item .cat-label:before, .variations .tawcvs-swatches .swatch.swatch-label,
			.woocommerce a.button:hover:not(.checkout):not(.view-cart):not(.product_type_simple):not(.add_to_cart_button):not(.yith-wcqv-button):not(.continue):not(.view-cart):not(.product_type_external):not(.product_type_grouped), .woocommerce button.button:hover, .woocommerce input.button:hover, .woocommerce #respond input#submit:hover, .woocommerce-checkout .form-row button[type="submit"]:hover, .woocommerce-checkout .form-row input[type="submit"]:hover, #add_payment_method #payment div.form-row #place_order:hover, .woocommerce-cart #payment div.form-row #place_order:hover, .woocommerce-checkout #payment div.form-row #place_order:hover, .woocommerce a.button:focus:not(.checkout):not(.view-cart):not(.product_type_simple):not(.add_to_cart_button):not(.yith-wcqv-button):not(.continue):not(.view-cart):not(.product_type_external):not(.product_type_grouped), .woocommerce button.button:focus, .woocommerce input.button:focus, .woocommerce #respond input#submit:focus, .woocommerce-checkout .form-row button[type="submit"]:focus, .woocommerce-checkout .form-row input[type="submit"]:focus, #add_payment_method #payment div.form-row #place_order:focus, .woocommerce-cart #payment div.form-row #place_order:focus, .woocommerce-checkout #payment div.form-row #place_order:focus,
			.singular-shop div.product .information .single_add_to_cart_button:hover,
			.product-block .group-buttons > div a.added, .product-block .group-image > div a.added,
			.singular-shop div.product .information .single_add_to_cart_button.added + a:hover {
				background:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
				border-color:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
			}
			.widget-features:not(.style2) .feature-box-group .feature-box:hover .fbox-content,
			.woocommerce #tbay-top-cart .group-button p.buttons a.button.checkout:hover, #tbay-top-cart .group-button p.buttons a.button.checkout:hover, #tbay-bottom-cart .group-button p.buttons a.button.checkout:hover,
			.product-block .add-cart .product_type_external.added + a.added_to_cart, .product-block .add-cart .product_type_grouped.added + a.added_to_cart, .product-block .add-cart .add_to_cart_button.added + a.added_to_cart, .product-block .add-cart a.button.added + a.added_to_cart, .product-block .add-cart a.added_to_cart.added + a.added_to_cart {
				background:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
				border-color:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
			}
			.btn-theme-2:hover, .page-404 .v2 .backtohome:hover, #comments .form-submit input[type="submit"]:hover, .btn-theme-2:focus, .page-404 .v2 .backtohome:focus, #comments .form-submit input[type="submit"]:focus, .category-inside, .entry-description a:hover, .navbar-nav .text-label,
			.woocommerce .woocommerce-MyAccount-navigation ul li.is-active a, .woocommerce .woocommerce-MyAccount-navigation ul li:hover a, .woocommerce .woocommerce-MyAccount-navigation ul li:focus a {
				background:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
			}
			.widget-social .social.style2 > li a:hover {
				background:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?> !important;
			}
			.none-before .vc_general.vc_btn3, .tbay-addon-button > a, body table.compare-list .add-to-cart td .add-cart a {
				color:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?> !important;
				border-color:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?> !important;
			}
			.none-before .vc_general.vc_btn3:hover, .tbay-addon-button > a:hover, .tbay-addon-button > a:focus,
			.singular-shop div.product .information .tbay-wishlist a:hover, .singular-shop div.product .information .tbay-wishlist a.added, .singular-shop div.product .information .tbay-compare a:hover, .singular-shop div.product .information .tbay-compare a.added,
			.singular-shop div.product .information .yith-wcwl-wishlistexistsbrowse.show > a:hover, .singular-shop div.product .information .yith-wcwl-wishlistaddedbrowse.show > a:hover, .woocommerce .tb-cart-total a.checkout-button:hover, body table.compare-list .add-to-cart td .add-cart a:hover, .yith-compare a.added, .singular-shop div.product .information .yith-wcwl-wishlistexistsbrowse.show > a, .singular-shop div.product .information .yith-wcwl-wishlistaddedbrowse.show > a {
				color:#fff !important;
				border-color:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?> !important;
				background:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?> !important;
			}
			.style-slide .tbay-slider-for .slick-arrow {
				border-color:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
			}
			.navbar-nav .text-label:before {
				border-top-color:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
			}

			/*Fix customize 1.3.6*/
			.product-block.list .group-buttons > div.add-cart a:hover,
			.product-block.list .group-buttons > div a:hover,
			.tbay-pagination-load-more a:hover, .singular-shop div.product .information .tbay-wishlist .yith-wcwl-add-to-wishlist a.delete_item {
				border-color:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?> !important;
				background:<?php echo esc_html( puca_tbay_get_config('main_color2') ) ?> !important;	
			}

			<?php endif; ?>

			/**********************************button hover*****************************/
			<?php if ( puca_tbay_get_config('button_hover_color') != "" ) : ?>
				.btn-theme:hover,.btn-theme:active,
				.archive-shop div.product .information .single_add_to_cart_button:hover,.archive-shop div.product .information .single_add_to_cart_button:active {
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
				#tbay-topbar, #tbay-header #tbay-topbar
				{
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
				#tbay-topbar, #tbay-header.header-v19 .top-info 
				{
					color: <?php echo esc_html(puca_tbay_get_config('topbar_text_color')); ?>;
				}
			<?php endif; ?>
			/* Top Bar Link Color */
			<?php if ( puca_tbay_get_config('topbar_link_color') != "" ) : ?>
				#tbay-topbar .tbay-login a, #tbay-topbar .tbay-topcart a.mini-cart, #tbay-topbar .top-info a, #tbay-topbar .SumoSelect p
				{
					color: <?php echo esc_html(puca_tbay_get_config('topbar_link_color')); ?>;
				}
			<?php endif; ?>			

			/* Top Bar Link Color Hover*/
			<?php if ( puca_tbay_get_config('topbar_link_color_hover') != "" ) : ?>
				#tbay-topbar .tbay-login a:hover, #tbay-topbar .tbay-topcart a.mini-cart:hover, #tbay-topbar .top-info a:hover, #tbay-topbar .SumoSelect p
				{
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
				#tbay-header, #tbay-header.header-v23, #tbay-header.header-v24 .header-main  {
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
				.tbay-dropdown-cart .heading-title, #tbay-top-cart .heading-title, .tbay-bottom-cart .heading-title, .cart-popup .heading-title, .tbay-dropdown-cart .total, #tbay-top-cart .total, .tbay-bottom-cart .total, .cart-popup .total, #tbay-header .tbay-megamenu .widget .widgettitle, #tbay-header .tbay-megamenu .widget .widget-title, .tbay-offcanvas-main .dropdown-menu .widget-heading, .tbay-offcanvas-main .dropdown-menu .widget-title, .tbay-offcanvas-main .dropdown-menu .widgettitle, #tbay-offcanvas-main .offcanvas-head, .header-bottom-vertical, .v17 .header-right, .bottom-canvas, .header-bottom-vertical, .v17 .header-right, #tbay-offcanvas-main .copyright, .top-contact .widget_text span, body.v16 .header-top-left .top-copyright .copyright, body.v17 .header-top-left .top-copyright .copyright, body.v18 .top-copyright .copyright, .top-info
				{
					color: <?php echo esc_html(puca_tbay_get_config('header_text_color')); ?>;
				}
				@media (min-width: 1600px) {
					body.v17 .wishlist .count_wishlist, body.v17 #tbay-header .header-right, body.v18 .navbar-nav > li > a, body.v18 .top-cart a.mini-cart, body.v18 .header-bottom-vertical .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont, body.v18 .tbay-login a.account-button, body.v18 .widget_tbay_socials_widget .social li a, body.v18 .top-contact .widget_text {
						color: <?php echo esc_html(puca_tbay_get_config('header_text_color')); ?>;
					}
				}
				
			<?php endif; ?>
			/* Header Link Color */
			<?php if ( puca_tbay_get_config('header_link_color') != "" ) : ?>
				#tbay-header .list-inline.acount li a.login,
				#tbay-header .cart-dropdown .cart-icon i,
				.tbay-dropdown-cart .offcanvas-close, #tbay-top-cart .offcanvas-close, .tbay-bottom-cart .offcanvas-close, .cart-popup .offcanvas-close,
				.header-right .tbay-search-form button, .tbay-mainmenu button.btn-offcanvas,
				.tbay-offcanvas .offcanvas-head .btn-toggle-canvas, .tbay-offcanvas-main .offcanvas-head .btn-toggle-canvas,
				.tbay-offcanvas-main .navbar-nav > li > a, .tbay-offcanvas-main .dropdown-menu li a, .tbay-login.offcanvas a,
				#tbay-offcanvas-main .copyright a, #tbay-header .topbar-mobile .btn,
				#tbay-header.header-v14 .tbay-search-form button, #tbay-header.header-v14 .cart-dropdown .mini-cart, #tbay-header.header-v9 .tbay-search-form button, #tbay-header.header-v9 .cart-dropdown .mini-cart, #tbay-header.header-v10 .tbay-login a,
				.top-social .widget_tbay_socials_widget .social li a, .top-info a, .tbay-login a, .tbay-topcart a.mini-cart, #tbay-header .tbay-currency .SumoSelect p, #tbay-header.header-v23 .tbay-login .account-button, #tbay-header.header-v23 .navbar-nav.megamenu > li > a, #tbay-header.header-v23 .tbay-topcart .cart-dropdown > a, #tbay-header.header-v24 .tbay-login .account-button, #tbay-header.header-v24 .navbar-nav.megamenu > li > a, #tbay-header.header-v24 .tbay-topcart a, #tbay-header.header-v24 .tbay-search-min .btn-search-min {
					color: <?php echo esc_html(puca_tbay_get_config('header_link_color'));?> ;
				}
				@media (min-width: 1600px) {
					body.v16 .header-top-left .account-button, body.v16 .header-top-left .tbay-login > a .copyright a, body.v16 .header-top-left .mini-cart, body.v16 .header-top-left .tbay-search-form .btn-search-min, body.v17 .header-top-left .account-button, body.v17 .header-top-left .tbay-login > a .copyright a, body.v17 .header-top-left .mini-cart, body.v17 .header-top-left .tbay-search-form .btn-search-min, body.v18  .account-button, body.v18 .tbay-login > a .copyright a, body.v18 .mini-cart, body.v18 .tbay-search-form .btn-search-min, body.v17 .wishlist, body.v17 .wishlist:hover .count_wishlist, .top-copyright .copyright a {
						color: <?php echo esc_html(puca_tbay_get_config('header_link_color'));?> ;
					}
				}
			<?php endif; ?>
			/* Header Link Color Active */
			<?php if ( puca_tbay_get_config('header_link_color_active') != "" ) : ?>
				#tbay-header .tbay-mainmenu a:hover,
				#tbay-header .tbay-mainmenu a:focus,
				#tbay-header .active > a,
				#tbay-header a:active,
				.tbay-offcanvas-main .dropdown-menu .menu-category-menu-container li a:hover,
				.tbay-offcanvas-main .dropdown-menu .menu-category-menu-container li.active a,
				#tbay-header .search-modal .btn-search-totop:hover,
				#tbay-header .cart-dropdown .cart-icon:hover i,
				#tbay-header #cart .mini-cart a:hover,
				.tbay-dropdown-cart .offcanvas-close:hover, #tbay-top-cart .offcanvas-close:hover, .tbay-bottom-cart .offcanvas-close:hover, .cart-popup .offcanvas-close:hover,
				.header-right .dropdown .account-menu ul li a:hover, .tbay-login .dropdown .account-menu ul li a:hover,
				.header-right .tbay-search-form button:hover, .tbay-mainmenu button.btn-offcanvas:hover,
				.tbay-offcanvas .offcanvas-head .btn-toggle-canvas:hover, .tbay-offcanvas-main .offcanvas-head .btn-toggle-canvas:hover,
				.tbay-offcanvas-main .navbar-nav > li > a:hover,
				.tbay-offcanvas-main .dropdown-menu li a:hover, .tbay-login.offcanvas a:hover, #tbay-offcanvas-main .copyright a:hover, #tbay-offcanvas-main .copyright a:after, #tbay-header .topbar-mobile .btn:hover, #tbay-header .topbar-mobile .btn:focus,
				#tbay-header.header-v14 .tbay-search-form button:hover, #tbay-header.header-v14 .cart-dropdown .mini-cart:hover, #tbay-header.header-v9 .tbay-search-form button:hover, #tbay-header.header-v9 .cart-dropdown .mini-cart:hover, #tbay-header.header-v10 .tbay-login a:hover,
				.top-social .widget_tbay_socials_widget .social li a:hover, .top-info a:hover, .tbay-login a:hover, #tbay-header .tbay-currency .SumoSelect p:hover, #tbay-header.header-v23 .tbay-login .account-button:hover, #tbay-header.header-v23 .navbar-nav.megamenu > li > a:hover, #tbay-header.header-v23 .tbay-topcart .cart-dropdown > a:hover, .tbay-search-form .button-search:hover, .tbay-search-form .button-search:focus, #tbay-header.header-v24 .tbay-login .account-button:hover, #tbay-header.header-v24 .navbar-nav.megamenu > li > a:hover, #tbay-header.header-v24 .tbay-topcart a:hover, #tbay-header.header-v24 .tbay-search-min .btn-search-min:hover {
					color: <?php echo esc_html(puca_tbay_get_config('header_link_color_active')); ?>;
				}
				@media (min-width: 1600px) {
					body.v16 .header-top-left .account-button:hover, body.v16 .header-top-left .tbay-login > a .copyright a:hover, body.v16 .header-top-left .mini-cart:hover, body.v16 .header-top-left .tbay-search-form .btn-search-min:hover, body.v17 .header-top-left .account-button:hover, body.v17 .header-top-left .tbay-login > a .copyright a:hover, body.v17 .header-top-left .mini-cart:hover, body.v17 .header-top-left .tbay-search-form .btn-search-min:hover, body.v18 .account-button:hover, body.v18 .tbay-login > a .copyright a:hover, body.v18 .mini-cart:hover, body.v18 .tbay-search-form .btn-search-min:hover, body.v17 .wishlist:hover, .top-copyright .copyright a:hover, .navbar-nav.megamenu > li > a:hover i, .navbar-nav.megamenu > li > a:hover .caret, .navbar-nav.megamenu > li > a:active i, .navbar-nav.megamenu > li > a:active .caret {
						color: <?php echo esc_html(puca_tbay_get_config('header_link_color_active')); ?>;
					}
				}
				.navbar-nav.megamenu > li:hover > a, .navbar-nav.megamenu > li:focus > a, .navbar-nav.megamenu > li.active > a {
					color: <?php echo esc_html(puca_tbay_get_config('header_link_color_active')); ?> !important;
				}
				.tbay-offcanvas-main .navbar-nav > li > a:before, .top-copyright .copyright a:after, .navbar-nav.megamenu > li > a:after {
					background: <?php echo esc_html(puca_tbay_get_config('header_link_color_active')); ?>;
				}
			<?php endif; ?>


			/* Menu Link Color */
			<?php if ( puca_tbay_get_config('main_menu_link_color') != "" ) : ?>
				.dropdown-menu .menu li a,
				.navbar-nav.megamenu .dropdown-menu > li > a,
				.navbar-nav.megamenu > li > a,
				.tbay-offcanvas-main .navbar-nav > li > a,
				#tbay-header .dropdown-menu .menu li a,
				#tbay-header .navbar-nav.megamenu .dropdown-menu > li > a,
				#tbay-header .navbar-nav.megamenu > li > a,
				#tbay-header .tbay-offcanvas-main .navbar-nav > li > a,
				#tbay-header.header-v23 .tbay-login .account-button, #tbay-header.header-v23 .navbar-nav.megamenu > li > a, #tbay-header.header-v23 .tbay-topcart .cart-dropdown > a,
				#tbay-header.header-v24 .tbay-login .account-button, #tbay-header.header-v24 .navbar-nav.megamenu > li > a
				{
					color: <?php echo esc_html(puca_tbay_get_config('main_menu_link_color'));?>;
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
				.tbay-offcanvas-main .navbar-nav li:hover > a,
				.navbar-nav.megamenu > li:hover:before,
				.navbar-nav.megamenu > li:focus:before,
				.navbar-nav.megamenu > li.active:before
				{
					color: <?php echo esc_html(puca_tbay_get_config('main_menu_link_color_active')); ?> !important;
				}
				.navbar-nav.megamenu > li:hover > a i, .navbar-nav.megamenu > li:hover > a .caret, .navbar-nav.megamenu > li:focus > a i, .navbar-nav.megamenu > li:focus > a .caret, .navbar-nav.megamenu > li.active > a i, .navbar-nav.megamenu > li.active > a .caret {
					color: <?php echo esc_html(puca_tbay_get_config('main_menu_link_color_active')); ?> ;
				}
				.tbay-offcanvas-main .navbar-nav > li > a:before, .navbar-nav.megamenu > li > a:after {
					background: <?php echo esc_html(puca_tbay_get_config('main_menu_link_color_active')); ?> !important;
				}
				.navbar-nav.megamenu > li > ul.dropdown-menu li:hover > a,
				.navbar-nav.megamenu > li > ul.dropdown-menu li:focus > a,
				.navbar-nav.megamenu > li > ul.dropdown-menu li.active > a,
				.navbar-nav.megamenu .dropdown-menu .widget ul li.active,
				.navbar-nav.megamenu .dropdown-menu .widget ul li:hover,
				#tbay-header .header-topmenu .tbay-menu-top .dropdown-menu > li:hover > a,
				#tbay-header .header-topmenu .tbay-menu-top .dropdown-menu > li:focus > a,
				#tbay-header .header-topmenu .tbay-menu-top .dropdown-menu > li.active > a,
				#tbay-header .header-main .header-right .top-cart-wishlist a:hover .cart-icon,
				#tbay-header .header-main .header-right .tbay-login:hover .account-button,
				#tbay-main-content .tbay_custom_menu > .widget.widget_nav_menu .menu > li > a:hover, 
				#tbay-main-content .tbay_custom_menu > .widget.widget_nav_menu .menu > li > a:focus,
				
				{
					border-color: <?php echo esc_html( puca_tbay_get_config('main_menu_link_color_active') ) ?>;
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
				#tbay-footer p, #tbay-footer .copyright, #tbay-footer .contact-info li, #tbay-footer .widget .subtitle {
					color: <?php echo esc_html(puca_tbay_get_config('footer_text_color')); ?>;
				}
			<?php endif; ?>
			
			/* Footer Link Color */
			<?php if ( puca_tbay_get_config('footer_link_color') != "" ) : ?>
				#tbay-footer a,
				#tbay-footer .menu > li a, #tbay-footer .widget-newletter .input-group-btn input,
				#tbay-footer .widget-newletter .input-group-btn:before
				{
					color: <?php echo esc_html(puca_tbay_get_config('footer_link_color')); ?>;
				}
				#tbay-footer .feedback a {
					color: <?php echo esc_html(puca_tbay_get_config('footer_link_color')); ?> !important;
				}
			<?php endif; ?>

			/* Footer Link Color Hover*/
			<?php if ( puca_tbay_get_config('footer_link_color_hover') != "" ) : ?>
				#tbay-footer a:hover,
				#tbay-footer .menu > li:hover > a, #tbay-footer .widget-newletter .input-group-btn input:hover,
				#tbay-footer .widget-newletter .input-group-btn:hover:before {
					color: <?php echo esc_html(puca_tbay_get_config('footer_link_color_hover')); ?>;
				}
				.tbay-footer .none-menu ul.menu a:after, .copyright a:after {
					background: <?php echo esc_html(puca_tbay_get_config('footer_link_color_hover')); ?>;
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
				.tbay-footer .tb-copyright {
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
				.tbay-footer .tb-copyright p, .tbay-footer .copyright {
					color: <?php echo esc_html(puca_tbay_get_config('copyright_text_color')); ?>;
				}
			<?php endif; ?>
			/* Footer Link Color */
			<?php if ( puca_tbay_get_config('copyright_link_color') != "" ) : ?>
				.tbay-copyright a,
				#tbay-footer .tb-copyright a, .tbay-footer .copyright a {
					color: <?php echo esc_html(puca_tbay_get_config('copyright_link_color')); ?> !important;
				}
			<?php endif; ?>

			/* Footer Link Color Hover*/
			<?php if ( puca_tbay_get_config('copyright_link_color_hover') != "" ) : ?>
				.tbay-copyright a:hover,
				#tbay-footer .tb-copyright a:hover, .tbay-footer .copyright a:hover {
					color: <?php echo esc_html(puca_tbay_get_config('copyright_link_color_hover')); ?> !important;
				}
				.tbay-footer .copyright a:after {
					background: <?php echo esc_html(puca_tbay_get_config('copyright_link_color_hover')); ?>;
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
					.product-block.list .group-buttons > div.add-cart a,
					.product-block.list .yith-wcwl-wishlistaddedbrowse.show a {
						border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
						background: transparent !important;
						color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
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
