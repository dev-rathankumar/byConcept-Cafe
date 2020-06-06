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

			/*Custom style smart mobile menu*/
			.mm-menu .mm-panels > .mm-panel > .mm-navbar + .mm-listview li.active > a, .mm-menu .mm-panels > .mm-panel > .mm-navbar + .mm-listview li.active .mm-counter,
			.mm-menu .mm-navbars_bottom .mm-navbar a:hover, .mm-menu .mm-navbars_bottom .mm-navbar a:focus,
			.widget.upsells .owl-carousel .slick-arrow:hover, .widget.related .owl-carousel .slick-arrow:hover {
				color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			.mm-menu .mm-panels > .mm-panel > .mm-navbar + .mm-listview li.active .mm-btn_next:after {
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			/*End custom style smart mobile menu*/

			.tbay-breadscrumb .breadscrumb-inner .breadcrumb .active,
			.testimonials-body .job,
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
			.name a:hover,
			.post-grid.vertical .entry-content .entry-title a:hover,
			.widget-categories.widget-grid.style2 .item-cat:hover .cat-name,
			.navbar-nav.megamenu > li:hover > a, 
			.navbar-nav.megamenu > li:focus > a, 
			.navbar-nav.megamenu > li.active > a,
			.navbar-nav.megamenu .dropdown-menu .widget ul li.active a,
			.tbay-offcanvas-main .navbar-nav li.active > a, .tbay-offcanvas-main .navbar-nav li:hover > a,
			.widget-categoriestabs ul.nav-tabs > li:hover a, .widget_deals_products ul.nav-tabs > li:hover a,
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
			.widget-features .image-inner, 
			.widget-features .icon-inner,
			.widget-categories .owl-carousel.categories .item:hover .cat-name,
			.product-nav .single_nav a:hover, 
			.product-nav .single_nav a:focus,
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
			.tbay-login:hover i,
			.woocommerce .quantity button.minus:focus, .woocommerce .quantity button.minus:hover, 
			.woocommerce-page .quantity button.minus:focus, .woocommerce-page .quantity button.minus:hover, 
			.woocommerce .quantity button.plus:focus, 
			.woocommerce .quantity button.plus:hover, 
			.woocommerce-page .quantity button.plus:focus, 
			.woocommerce-page .quantity button.plus:hover,
			.top-cart .dropdown-menu .product-details .product-name:hover,
			.flex-control-nav .slick-arrow:hover.owl-prev:after, .flex-control-nav .slick-arrow:hover.owl-next:after,
			#tbay-footer .tagcloud a:hover,
			.tbay-breadscrumb.breadcrumbs-color .breadscrumb-inner .breadcrumb a:hover,
			.tbay-breadscrumb.breadcrumbs-text .breadscrumb-inner .breadcrumb a:hover,
			.treeview a.selected i
			{
				color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			a:hover, a:focus,
			.navbar-nav.megamenu > li > ul.dropdown-menu li:hover:before,
			.navbar-nav.megamenu > li > ul.dropdown-menu li:focus:before,
			.navbar-nav.megamenu > li > ul.dropdown-menu li.active:before,
			.navbar-nav.megamenu .dropdown-menu .widget ul li.active a,
			.navbar-nav.megamenu .dropdown-menu .widget ul li:hover a,
			.navbar-nav.megamenu .dropdown-menu .widget ul li.active:before,
			.navbar-nav.megamenu .dropdown-menu .widget ul li:hover:before,
			#tbay-category-image .category-inside ul.tbay-menu-category > li ul.dropdown-menu > li:hover > a,
			#tbay-category-image .category-inside ul.tbay-menu-category > li ul.dropdown-menu > li:focus > a,
			#tbay-category-image .category-inside ul.tbay-menu-category > li ul.dropdown-menu > li.active > a,
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
			.singular-shop div.product .information .price ins,
			.singular-shop div.product .information .price,
			.layout-blog .post-list > article .entry-title a:hover,
			.related-posts .entry-title a:hover,
			.page-404 .sub-title .backtohome,
			.treeview a.selected,
			.sidebar ul.woof_list li .hover, .product-top-sidebar ul.woof_list li .hover, .product-canvas-sidebar ul.woof_list li .hover, .related-posts ul.woof_list li .hover, .blog-top-search ul.woof_list li .hover, .blog-top-sidebar1 ul.woof_list li .hover,
			.cart-bottom .update:before,
			.topbar-device-mobile .active-mobile .btn-danger,
			.topbar-device-mobile .cart-dropdown .mini-cart .cart-icon i,
			.footer-device-mobile > *.active a i,
			.widget_tbay_custom_menu .menu-category-menu-container ul li:hover i,
			.widget_categories > ul li.current-cat a, .widget_pages > ul li.current-cat a,
			.widget_meta > ul li.current-cat a, .widget_archive > ul li.current-cat a,
			.navbar-offcanvas .dropdown-menu > li.active > a, 
			.navbar-offcanvas .dropdown-menu > li > a:hover, 
			.navbar-offcanvas .dropdown-menu > li > a:focus,
			.cart_totals table tr.shipping a, .cart_totals table * tr.shipping a,
			#add_payment_method #payment div.form-row a, .woocommerce-cart #payment div.form-row a, .woocommerce-checkout #payment div.form-row a,
			#tbay-category-image .category-inside ul.tbay-menu-category > li.aligned-left a:hover, #tbay-category-image .category-inside ul.tbay-menu-category > li.aligned-right a:hover, #tbay-category-image .category-inside ul.tbay-menu-category > li.aligned-fullwidth a:hover,
			.ui-autocomplete.ui-widget-content li.list-header > a, .dokan-message a, .dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li.active, .dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li:hover, .dokan-dashboard .dokan-dash-sidebar ul.dokan-dashboard-menu li.dokan-common-links a:hover, .dokan-dashboard .pagination-wrap > ul.pagination > li a:hover, ul.subsubsub li.active a, .dokan-seller-listing .wrapper-dokan > span:hover, .dokan-single-store .profile-frame .profile-info-box .profile-info-summery-wrapper .profile-info-summery .profile-info .dokan-store-info i, .dokan-withdraw-content .dokan-withdraw-area ul li.active a, .dokan-orders-content .dokan-orders-area > a.dokan-btn:hover, .dokan-orders-content .dokan-orders-area .dokan-table .woocommerce-Price-amount, .woocommerce table.cart .product-name a:hover, .woocommerce table.cart .product-name a:focus
			{
				color:<?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}

			.verticle-menu .navbar-nav > li.active > a, 
			.verticle-menu .navbar-nav > li:hover > a,
			.metis.tparrows:hover:before {
				color: #fff !important;
			}
			.navbar-nav.megamenu > li > a:before,
			.tbay-to-top a span,
			.tp-bullets .tp-bullet.selected,
			.metis.tparrows:hover,
			.hades .tp-tab.selected .tp-tab-title:after, 
			.hades .tp-tab:hover .tp-tab-title:after,
			.verticle-menu .navbar-nav > li.active, 
			.verticle-menu .navbar-nav > li:hover,
			.tp-bullets .tp-bullet:hover,
			#onepage-single-product .shop-now a,
			.sidebar .widget .widget-title:after,
			.woocommerce-account button[type="submit"], 
			.woocommerce-account input[type="submit"],
			{
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
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
			.yith-wcqv-wrapper .carousel-indicators li,
			.tp-bullets .tp-bullet
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
			#tbay-header .header-main .header-right .top-cart-wishlist a:hover .cart-icon,
			#tbay-header .header-main .header-right .tbay-login:hover .account-button,
			#tbay-main-content .tbay_custom_menu > .widget.widget_nav_menu .menu > li > a:hover, 
			#tbay-main-content .tbay_custom_menu > .widget.widget_nav_menu .menu > li > a:focus,
			.dokan-dashboard .pagination-wrap > ul.pagination > li a:hover,
			.dokan-pagination-container ul.dokan-pagination > li:not(.disabled):not(.active):hover a
			{
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}

			.singular-shop div.product .information .tbay-wishlist a.added, 
			.singular-shop div.product .information .tbay-compare a.added,
			.singular-shop div.product .information .tbay-wishlist a.added:hover, 
			.singular-shop div.product .information .tbay-compare a.added:hover, .product-block .yith-wcwl-add-to-wishlist > div.yith-wcwl-add-button a.delete_item {
			    background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			    border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}

			/*Buy now*/  
			#shop-now.has-buy-now .tbay-buy-now,
			#shop-now.has-buy-now .tbay-buy-now.disabled {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-bottom-color: <?php echo esc_html( puca_tbay_get_config('button_border_color') ) ?>;
			}
			@media (max-width: 767px) {
				#shop-now.has-buy-now .single_add_to_cart_button:hover, 
				#shop-now.has-buy-now .single_add_to_cart_button:focus,
				#shop-now.has-buy-now .tbay-buy-now.button:hover, 
				#shop-now.has-buy-now .tbay-buy-now.button:focus {
				    background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				    border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				}
			}

			#content > .products.load-ajax:after {
				border-top-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			
			.tbay-search-form .button-search,
			#awesome-font .fontawesome-icon-list .fa-hover:hover .preview,
			.icon-preview-box:hover .preview,
			.layout-blog .post-list > article:hover .meta-info > span.entry-date,
			.widget.widget_tbay_popular_post .post-widget .post-list:hover .meta-info > span.entry-date,
			.entry-single span.entry-date:hover,
			.page-404 .notfound-bottom .search-form .input-group-btn .btn
			{
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}

			.woocommerce .compare-list .add-cart a.button:hover {
				background:  <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				border-color:  <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				color: #fff !important;	
			}

			.woocommerce-checkout .form-row input[type="submit"],
			.topbar-mobile .active-mobile .btn, .product-block.list .yith-wcwl-add-to-wishlist > div.yith-wcwl-add-button a.delete_item, .singular-shop div.product .information .tbay-wishlist .yith-wcwl-add-to-wishlist a.delete_item {
				background:  <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color:  <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			#content .products.load-ajax:after {
				border-top-color:  <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			
			/* Background Theme color 2 */
			<?php if ( puca_tbay_get_config('main_color') != "" ) : ?>
			.woocommerce span.onsale .saled,
			.meta-info > span.entry-date,
			#tbay-header.header-v2 .header-main .tbay-search-form .select-category .CaptionCont, 
			#tbay-header.header-v3 .header-main .tbay-search-form .select-category .CaptionCont,
			.widget.widget-blog .widget-title span:after, 
			.widget.widget-blog .widgettitle span:after, 
			.widget.widget-blog .widget-heading span:after,
			.entry-single span.entry-date,
			.tbay-offcanvas .offcanvas-head .btn-toggle-canvas, .tbay-offcanvas-main .offcanvas-head .btn-toggle-canvas
			{
				background: <?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
			}
			.widget_price_filter .ui-slider-horizontal .ui-slider-range,
			.widget_price_filter .ui-slider .ui-slider-handle,
			{
				background: <?php echo esc_html( puca_tbay_get_config('main_color2') ) ?> !important;
			}

			.tbay-addon-button a {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				border-bottom-color: <?php echo esc_html( puca_tbay_get_config('button_border_color') ) ?> !important;
			}
			.tbay-addon-button a:hover {
				background: <?php echo esc_html( puca_tbay_get_config('button_hover_color') ) ?> !important;
				border-bottom-color: <?php echo esc_html( puca_tbay_get_config('button_border_color_hover') ) ?> !important;
			}
			
			#tbay-header .header-main .header-right .tbay-login .account-name,
			.title-account
			{
				color: <?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
			}
			.singular-shop div.product .information .yith-wcwl-wishlistexistsbrowse.show > a:hover,
			.singular-shop div.product .information .yith-wcwl-wishlistaddedbrowse.show > a:hover {
				background: <?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>; 
			}
			.woocommerce-checkout .form-row input[type="submit"]:hover,
			.woocommerce .cart-dropdown.cart-popup .dropdown-menu ul.cart_empty a.wc-continue:hover, .cart-dropdown.cart-popup .dropdown-menu ul.cart_empty a.wc-continue:hover, .widget-newletter .btn-default:focus, .widget-categoriestabs .woocommerce .btn-view-all:hover, .widget-categoriestabs .woocommerce .btn-view-all:focus, .widget-categoriestabs .woocommerce .show-view-all a:hover, .widget-categoriestabs .woocommerce .show-view-all a:focus, .widget_deals_products .woocommerce .btn-view-all:hover, .widget_deals_products .woocommerce .btn-view-all:focus, .widget_deals_products .woocommerce .show-view-all a:hover, .widget_deals_products .woocommerce .show-view-all a:focus, .woocommerce #tbay-top-cart a.wc-continue:hover, #tbay-top-cart a.wc-continue:hover, #tbay-bottom-cart a.wc-continue:hover, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button:hover, .woocommerce-cart .return-to-shop .button:hover, .woocommerce #payment #place_order:hover, .woocommerce-page #payment #place_order:hover, .woocommerce-page .woocommerce-message .button:hover, .yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button:hover, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a:hover, .woocommerce .woocommerce-MyAccount-content a.button:hover, .woocommerce .woocommerce-MyAccount-content input.button:hover, .btn-theme:focus, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button:focus, .woocommerce-cart .return-to-shop .button:focus, .singular-shop div.product .information .single_add_to_cart_button:focus, .woocommerce #payment #place_order:focus, .woocommerce-page #payment #place_order:focus, .woocommerce-page .woocommerce-message .button:focus, .yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button:focus, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a:focus, .woocommerce .woocommerce-MyAccount-content a.button:focus, .woocommerce .woocommerce-MyAccount-content input.button:focus, .btn-theme:active, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button:active, .woocommerce-cart .return-to-shop .button:active, .singular-shop div.product .information .single_add_to_cart_button:active, .woocommerce #payment #place_order:active, .woocommerce-page #payment #place_order:active, .woocommerce-page .woocommerce-message .button:active, .yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button:active, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a:active, .woocommerce .woocommerce-MyAccount-content a.button:active, .woocommerce .woocommerce-MyAccount-content input.button:active, .btn-theme.active, .woocommerce-cart .wc-proceed-to-checkout a.active.checkout-button, .woocommerce-cart .return-to-shop .active.button, .singular-shop div.product .information .active.single_add_to_cart_button, .woocommerce #payment .active#place_order, .woocommerce-page #payment .active#place_order, .woocommerce-page .woocommerce-message .active.button, .yith-wcqv-wrapper #yith-quick-view-content .summary .active.single_add_to_cart_button, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a.active, .woocommerce .woocommerce-MyAccount-content a.active.button, .woocommerce .woocommerce-MyAccount-content input.active.button, .btn-default:hover, .btn-default:focus, .btn-theme:hover, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button:hover, .woocommerce-cart .return-to-shop .button:hover, .singular-shop div.product .information .single_add_to_cart_button:hover, .woocommerce #payment #place_order:hover, .woocommerce-page #payment #place_order:hover, .woocommerce-page .woocommerce-message .button:hover, .yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button:hover, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a:hover, .woocommerce .woocommerce-MyAccount-content a.button:hover, .woocommerce .woocommerce-MyAccount-content input.button:hover, .btn-theme:focus, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button:focus, .woocommerce-cart .return-to-shop .button:focus, .singular-shop div.product .information .single_add_to_cart_button:focus, .woocommerce #payment #place_order:focus, .woocommerce-page #payment #place_order:focus, .woocommerce-page .woocommerce-message .button:focus, .yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button:focus, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a:focus, .woocommerce .woocommerce-MyAccount-content a.button:focus, .woocommerce .woocommerce-MyAccount-content input.button:focus, .btn-default:hover, .btn-default:focus, .btn-theme:hover, #comments .form-submit input[type="submit"]:hover, .btn-theme:focus, #comments .form-submit input[type="submit"]:focus, .single-project.has-gallery .project-meta .url .tbay-button:hover, .single-project.has-gallery .project-meta .url .tbay-button:focus, .single-project.no-gallery .project-meta .url .tbay-button:hover, .single-project.no-gallery .project-meta .url .tbay-button:focus, .page-404 .v2 .backtohome:hover, .page-404 .v2 .backtohome:focus, .tbay-dropdown-cart a.wc-continue:hover, #tbay-top-cart a.wc-continue:hover, .tbay-bottom-cart a.wc-continue:hover, .cart-popup a.wc-continue:hover, .tbay-dropdown-cart.v2 .group-button a.button:hover, .tbay-dropdown-cart.v2 .group-button a.button.checkout, #tbay-top-cart.v2 .group-button a.button:hover, #tbay-top-cart.v2 .group-button a.button.checkout, .tbay-bottom-cart.v2 .group-button a.button:hover, .tbay-bottom-cart.v2 .group-button a.button.checkout, .cart-popup.v2 .group-button a.button:hover, .cart-popup.v2 .group-button a.button.checkout, .coupon .box input[type=submit]:hover, .coupon .box input[type=submit]:focus, .woocommerce .cart-dropdown.cart-popup .dropdown-menu p.buttons a.view-cart:hover, .woocommerce .cart-dropdown.cart-popup .dropdown-menu p.buttons a.checkout:hover, .cart-dropdown.cart-popup .dropdown-menu p.buttons a.view-cart:hover, .cart-dropdown.cart-popup .dropdown-menu p.buttons a.checkout:hover, .woocommerce .cart-dropdown.cart-popup .dropdown-menu p.buttons a:hover, .woocommerce .cart-dropdown.cart-popup .dropdown-menu p.buttons a.checkout, .cart-dropdown.cart-popup .dropdown-menu p.buttons a:hover, .cart-dropdown.cart-popup .dropdown-menu p.buttons a.checkout, #add_payment_method #payment div.form-row #place_order:hover, .woocommerce-cart #payment div.form-row #place_order:hover, .woocommerce-checkout #payment div.form-row #place_order:hover, .checkout .form-row input[type="submit"]:hover, .woocommerce #tbay-top-cart .group-button a.button:hover, .woocommerce #tbay-top-cart .group-button a.button.checkout, #tbay-top-cart .group-button a.button:hover, #tbay-top-cart .group-button a.button.checkout, #tbay-bottom-cart .group-button a.button:hover, #tbay-bottom-cart .group-button a.button.checkout, .product-block .add-cart .product_type_external:hover, .product-block .add-cart .product_type_grouped:hover, .product-block .add-cart .add_to_cart_button:hover, .product-block .add-cart a.button:hover, .product-block .add-cart a.added_to_cart:hover, .coupon .box input[type=submit]:hover,
			.singular-shop div.product .information .single_add_to_cart_button.added + a:hover, .singular-shop div.product .information .single_add_to_cart_button.added + a:focus, .questions-section #ywqa-submit-question:hover,
			#shop-now.has-buy-now .tbay-buy-now:hover,
			#shop-now.has-buy-now .tbay-buy-now:focus,
			#shop-now.has-buy-now .tbay-buy-now:hover,
			#shop-now.has-buy-now .tbay-buy-now.disabled:focus {
				background: <?php echo esc_html( puca_tbay_get_config('main_color2') ) ?> !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color2') ) ?> !important;
			}
			/* End Background Theme color 2 */
			<?php endif; ?>
			
			/* Button */
			.cart-dropdown.cart-popup .dropdown-menu ul.cart_empty a.wc-continue,
			.woocommerce a.button:not(.yith-wcqv-button):not(.compare),
			.woocommerce button.button, .woocommerce input.button,
			.woocommerce #respond input#submit,
			.widget-newletter .btn-default,
			.widget-categoriestabs .woocommerce .btn-view-all, .widget-categoriestabs .woocommerce .show-view-all a, .widget_deals_products .woocommerce .btn-view-all, .widget_deals_products .woocommerce .show-view-all a,
			.woocommerce #tbay-top-cart a.wc-continue, #tbay-top-cart a.wc-continue, #tbay-bottom-cart a.wc-continue,
			.woocommerce .cart-dropdown.cart-popup .dropdown-menu ul.cart_empty a.wc-continue,
			.cart-dropdown.cart-popup .dropdown-menu ul.cart_empty a.wc-continue,
			.more_products a, .tbay-pagination-load-more a,
			.btn-default, .btn-theme, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button, .woocommerce-cart .return-to-shop .button, 
			.woocommerce #payment #place_order, .woocommerce-page #payment #place_order, .woocommerce-page .woocommerce-message .button, .yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button, 
			.woocommerce table.wishlist_table .product-add-to-cart .add-cart a, .woocommerce .woocommerce-MyAccount-content a.button, .woocommerce .woocommerce-MyAccount-content input.button,.singular-shop div.product .information .single_add_to_cart_button, 
			.btn-default, .btn-theme, #comments .form-submit input[type="submit"],
			.single-project.has-gallery .project-meta .url .tbay-button, .single-project.no-gallery .project-meta .url .tbay-button,
			.page-404 .v2 .backtohome,
			.tbay-dropdown-cart a.wc-continue, #tbay-top-cart a.wc-continue, .tbay-bottom-cart a.wc-continue, .cart-popup a.wc-continue,
			.coupon .box input[type=submit],
			.woocommerce .cart-dropdown.cart-popup .dropdown-menu p.buttons a, .cart-dropdown.cart-popup .dropdown-menu p.buttons a,
			#add_payment_method #payment div.form-row #place_order, 
			.woocommerce-cart #payment div.form-row #place_order, 
			.woocommerce-checkout #payment div.form-row #place_order,
			.checkout .form-row input[type="submit"],
			.tbay-dropdown-cart.v2 .group-button a.button, #tbay-top-cart.v2 .group-button a.button, .tbay-bottom-cart.v2 .group-button a.button, .cart-popup.v2 .group-button a.button,
			.woocommerce #tbay-top-cart .group-button a.button, #tbay-top-cart .group-button a.button, #tbay-bottom-cart .group-button a.button,
			.product-block .add-cart .product_type_external, .product-block .add-cart .product_type_grouped, .product-block .add-cart .add_to_cart_button, .product-block .add-cart a.button, .product-block .add-cart a.added_to_cart,
			.singular-shop div.product .information .single_add_to_cart_button.added + a,
			.questions-section #ywqa-submit-question, input[type="submit"].dokan-btn, a.dokan-btn-theme, .dokan-btn-theme, .dokan-btn-success, input[type="submit"].dokan-btn[disabled], a.dokan-btn-theme[disabled], .dokan-btn-theme[disabled], .dokan-btn-success[disabled]
			{
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-bottom-color: <?php echo esc_html( puca_tbay_get_config('button_border_color') ) ?>;
			}
			.woocommerce .cart-dropdown.cart-popup .dropdown-menu ul.cart_empty a.wc-continue:hover,
			.cart-dropdown.cart-popup .dropdown-menu ul.cart_empty a.wc-continue:hover,
			.widget-newletter .btn-default:hover, .widget-newletter .btn-default:focus,
			.widget-categoriestabs .woocommerce .btn-view-all:hover, .widget-categoriestabs .woocommerce .btn-view-all:focus, .widget-categoriestabs .woocommerce .show-view-all a:hover, .widget-categoriestabs .woocommerce .show-view-all a:focus, .widget_deals_products .woocommerce .btn-view-all:hover, .widget_deals_products .woocommerce .btn-view-all:focus, .widget_deals_products .woocommerce .show-view-all a:hover, .widget_deals_products .woocommerce .show-view-all a:focus,
			.woocommerce #tbay-top-cart a.wc-continue:hover, #tbay-top-cart a.wc-continue:hover, #tbay-bottom-cart a.wc-continue:hover,
			.woocommerce-cart .wc-proceed-to-checkout a.checkout-button:hover, .woocommerce-cart .return-to-shop .button:hover, .woocommerce #payment #place_order:hover, .woocommerce-page #payment #place_order:hover, .woocommerce-page .woocommerce-message .button:hover, .yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button:hover, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a:hover, .woocommerce .woocommerce-MyAccount-content a.button:hover, .woocommerce .woocommerce-MyAccount-content input.button:hover, .btn-theme:focus, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button:focus, .woocommerce-cart .return-to-shop .button:focus, .singular-shop div.product .information .single_add_to_cart_button:focus, .woocommerce #payment #place_order:focus, .woocommerce-page #payment #place_order:focus, .woocommerce-page .woocommerce-message .button:focus, .yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button:focus, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a:focus, .woocommerce .woocommerce-MyAccount-content a.button:focus, .woocommerce .woocommerce-MyAccount-content input.button:focus, .btn-theme:active, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button:active, .woocommerce-cart .return-to-shop .button:active, .singular-shop div.product .information .single_add_to_cart_button:active, .woocommerce #payment #place_order:active, .woocommerce-page #payment #place_order:active, .woocommerce-page .woocommerce-message .button:active, .yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button:active, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a:active, .woocommerce .woocommerce-MyAccount-content a.button:active, .woocommerce .woocommerce-MyAccount-content input.button:active, .btn-theme.active, .woocommerce-cart .wc-proceed-to-checkout a.active.checkout-button, .woocommerce-cart .return-to-shop .active.button, .singular-shop div.product .information .active.single_add_to_cart_button, .woocommerce #payment .active#place_order, .woocommerce-page #payment .active#place_order, .woocommerce-page .woocommerce-message .active.button, .yith-wcqv-wrapper #yith-quick-view-content .summary .active.single_add_to_cart_button, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a.active, .woocommerce .woocommerce-MyAccount-content a.active.button, .woocommerce .woocommerce-MyAccount-content input.active.button,
			.btn-default:hover, .btn-default:focus, .btn-theme:hover, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button:hover, .woocommerce-cart .return-to-shop .button:hover, .singular-shop div.product .information .single_add_to_cart_button:hover, .woocommerce #payment #place_order:hover, .woocommerce-page #payment #place_order:hover, .woocommerce-page .woocommerce-message .button:hover, .yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button:hover, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a:hover, .woocommerce .woocommerce-MyAccount-content a.button:hover, .woocommerce .woocommerce-MyAccount-content input.button:hover, .btn-theme:focus, .woocommerce-cart .wc-proceed-to-checkout a.checkout-button:focus, .woocommerce-cart .return-to-shop .button:focus, .singular-shop div.product .information .single_add_to_cart_button:focus, .woocommerce #payment #place_order:focus, .woocommerce-page #payment #place_order:focus, .woocommerce-page .woocommerce-message .button:focus, .yith-wcqv-wrapper #yith-quick-view-content .summary .single_add_to_cart_button:focus, .woocommerce table.wishlist_table .product-add-to-cart .add-cart a:focus, .woocommerce .woocommerce-MyAccount-content a.button:focus, .woocommerce .woocommerce-MyAccount-content input.button:focus,
			.btn-default:hover, .btn-default:focus, .btn-theme:hover, #comments .form-submit input[type="submit"]:hover, .btn-theme:focus, #comments .form-submit input[type="submit"]:focus,
			.single-project.has-gallery .project-meta .url .tbay-button:hover, .single-project.has-gallery .project-meta .url .tbay-button:focus, .single-project.no-gallery .project-meta .url .tbay-button:hover, .single-project.no-gallery .project-meta .url .tbay-button:focus,
			.page-404 .v2 .backtohome:hover, .page-404 .v2 .backtohome:focus,
			.tbay-dropdown-cart a.wc-continue:hover, #tbay-top-cart a.wc-continue:hover, .tbay-bottom-cart a.wc-continue:hover, .cart-popup a.wc-continue:hover,
			.tbay-dropdown-cart.v2 .group-button a.button:hover, .tbay-dropdown-cart.v2 .group-button a.button.checkout, #tbay-top-cart.v2 .group-button a.button:hover, #tbay-top-cart.v2 .group-button a.button.checkout, .tbay-bottom-cart.v2 .group-button a.button:hover, .tbay-bottom-cart.v2 .group-button a.button.checkout, .cart-popup.v2 .group-button a.button:hover, .cart-popup.v2 .group-button a.button.checkout, .coupon .box input[type=submit]:focus,
			.woocommerce .cart-dropdown.cart-popup .dropdown-menu p.buttons a.view-cart:hover, .woocommerce .cart-dropdown.cart-popup .dropdown-menu p.buttons a.checkout:hover, .cart-dropdown.cart-popup .dropdown-menu p.buttons a.view-cart:hover, .cart-dropdown.cart-popup .dropdown-menu p.buttons a.checkout:hover,
			.woocommerce .cart-dropdown.cart-popup .dropdown-menu p.buttons a:hover, .woocommerce .cart-dropdown.cart-popup .dropdown-menu p.buttons a.checkout, .cart-dropdown.cart-popup .dropdown-menu p.buttons a:hover, .cart-dropdown.cart-popup .dropdown-menu p.buttons a.checkout,
			#add_payment_method #payment div.form-row #place_order:hover, 
			.woocommerce-cart #payment div.form-row #place_order:hover, 
			.woocommerce-checkout #payment div.form-row #place_order:hover,
			.checkout .form-row input[type="submit"]:hover,
			.woocommerce #tbay-top-cart .group-button a.button:hover, .woocommerce #tbay-top-cart .group-button a.button.checkout, #tbay-top-cart .group-button a.button:hover, #tbay-top-cart .group-button a.button.checkout, #tbay-bottom-cart .group-button a.button:hover, #tbay-bottom-cart .group-button a.button.checkout,
			.product-block .add-cart .product_type_external:hover, .product-block .add-cart .product_type_grouped:hover, .product-block .add-cart .add_to_cart_button:hover, .product-block .add-cart a.button:hover, .product-block .add-cart a.added_to_cart:hover,
			.coupon .box input[type=submit]:hover, input[type="submit"].dokan-btn-theme:hover, a.dokan-btn-theme:hover, .dokan-btn-theme:hover, input[type="submit"].dokan-btn-theme:focus, a.dokan-btn-theme:focus, .dokan-btn-theme:focus, input[type="submit"].dokan-btn-theme:active, a.dokan-btn-theme:active, .dokan-btn-theme:active, input[type="submit"].dokan-btn-theme.active, a.dokan-btn-theme.active, .dokan-btn-theme.active
			{
				background: <?php echo esc_html( puca_tbay_get_config('button_hover_color') ) ?>;
				border-bottom-color: <?php echo esc_html( puca_tbay_get_config('button_border_color_hover') ) ?>;
			}
			
			.woocommerce a.button:not(.yith-wcqv-button):not(.compare):hover,
			.woocommerce button.button:hover, .woocommerce input.button:hover,
			.woocommerce #respond input#submit:hover
			{
				background: <?php echo esc_html( puca_tbay_get_config('button_hover_color') ) ?>;
				border-bottom-color: <?php echo esc_html( puca_tbay_get_config('button_border_color_hover') ) ?> !important;
			}
			body .more_products a:hover,
			body .widget-categories.widget-grid .show-all:hover 
			{
				background: <?php echo esc_html( puca_tbay_get_config('button_hover_color') ) ?> !important;
				border-bottom-color: <?php echo esc_html( puca_tbay_get_config('button_border_color_hover') ) ?> !important;
			}
			
			/* Shop */
			/*Mini Cart*/
			.singular-shop div.product .information .tbay-wishlist a:hover,
			.singular-shop div.product .information .tbay-compare a:hover, .product-block .groups-button-image > div a:hover,
			.product-block .groups-button-image > a a:hover, 
			.product-block .buttons > div a:hover, 
			.product-block .buttons > a a:hover,
			{
				background: <?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
				color: #fff !important;
			}
			
			.tbay-to-top a#back-to-top,
			.singular-shop div.product .information .yith-wcwl-wishlistexistsbrowse.show > a, .singular-shop div.product .information .yith-wcwl-wishlistaddedbrowse.show > a {
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				color: #fff !important;
			}

			.singular-shop div.product .information .tbay-wishlist a:hover, .singular-shop div.product .information .tbay-compare a:hover,
			.singular-shop div.product .information .tbay-wishlist .yith-wcwl-add-to-wishlist a.delete_item:hover {
				background: <?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>;
			}

			.tbay-to-top a#back-to-top i {
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
			
			/*End icon*/
			/*Button cart*/
			.widget-products.special .product-block .caption .groups-button .add-cart a.button:hover, 
			.widget-products.widget-special .product-block .caption .groups-button .add-cart a.button:hover, 
			.widget-products.carousel-special .product-block .caption .groups-button .add-cart a.button:hover, 
			.widget-products.widget-carousel-special .product-block .caption .groups-button .add-cart a.button:hover,
			.widget-products.special .product-block .caption .groups-button .add-cart a.button, 
			.widget-products.widget-special .product-block .caption .groups-button .add-cart a.button, 
			.widget-products.carousel-special .product-block .caption .groups-button .add-cart a.button, 
			.widget-products.widget-carousel-special .product-block .caption .groups-button .add-cart a.button,
			.more_products a, .tbay-pagination-load-more a, .widget-features.style3 .feature-box-group .feature-box:before,
			.widget-categories .owl-carousel.categories .item .cat-name:after,
			.widget_deals_products .tbay-countdown .times > div,
			.group-text.home_3 .signature .job:before	
			{
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.product-block .yith-wcwl-wishlistexistsbrowse > a, 
			.product-block .yith-wcwl-wishlistaddedbrowse > a,
			.widget_price_filter .price_slider_amount .button,
			.widget.yith-woocompare-widget a.compare,
			.pagination span.current, .pagination a.current, .tbay-pagination span.current, .tbay-pagination a.current,
			.pagination a:hover, .tbay-pagination a:hover,
			.shop_table a.remove:hover
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
			.categorymenu .menu-category-menu-container ul li a:hover, .widget_tbay_custom_menu .menu-category-menu-container ul li a:hover{
				border-right-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			
			.tbay-to-top a:hover, .tbay-to-top button.btn-search-totop:hover{
				background: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			body table.compare-list .add-to-cart td a:hover {
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.widget-testimonials .owl-carousel .slick-arrow:hover,
			.tbay-offcanvas .offcanvas-head .btn-toggle-canvas:hover, .tbay-offcanvas .offcanvas-head .btn-toggle-canvas:focus, .tbay-offcanvas-main .offcanvas-head .btn-toggle-canvas:hover, .tbay-offcanvas-main .offcanvas-head .btn-toggle-canvas:focus,
			body table.compare-list .add-to-cart td a,
			.top-footer .widget-newletter .input-group .btn.btn-default
			{
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?> !important;
			}
			.tbay-slider-for .slick-arrow:hover:after
			{
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>;
			}
			
			.yith-compare a.added,
			.product-block .button-wishlist .yith-wcwl-wishlistexistsbrowse.show a,
			.product-block .button-wishlist .yith-wcwl-wishlistaddedbrowse.show a,
			.widget-features.style2 .feature-box:hover .fbox-icon,
			.more_products a:hover, .more_products a:focus,
			.yith-wcqv-wrapper #yith-quick-view-content .carousel-controls-v3 .carousel-control:hover,
			#cboxClose:hover,
			.product-canvas-sidebar .product-canvas-close,
			.yith-wcqv-wrapper .carousel-indicators li.active
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
			.product-block .yith-wcwl-wishlistexistsbrowse > a:hover, 
			.product-block .yith-wcwl-wishlistaddedbrowse > a:hover, 
			.product-block .yith-wcwl-add-to-wishlist > a:hover, 
			.product-block .yith-compare > a:hover, 
			.product-block .add_to_wishlist:hover, 
			.product-block .yith-wcqv-button:hover,
			.product-block .yith-wcwl-add-to-wishlist > div.yith-wcwl-add-button a.delete_item:hover,
			body table.compare-list .add-to-cart td a {
				background-color: <?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>  !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color2') ) ?>  !important;
			}
			body table.compare-list .add-to-cart td a:hover {
				background-color: #fff !important;
				border-color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
				color: <?php echo esc_html( puca_tbay_get_config('main_color') ) ?>  !important;
			}

			.more_products a, .tbay-pagination-load-more a,
			.woocommerce .woocommerce-MyAccount-navigation ul li.is-active a, 
			.woocommerce .woocommerce-MyAccount-navigation ul li:hover a, 
			.woocommerce .woocommerce-MyAccount-navigation ul li:focus a,
			#tbay-category-image .category-inside ul.tbay-menu-category > li:after,
			.widget .widget-title span:after, .widget .widgettitle span:after, .widget .widget-heading span:after,
			.slick-dots li.slick-active button:before,
			.slick-dots li button:hover:before, .slick-dots li button:focus:before,
			.post-grid:hover .meta-info > span.entry-date,
			.tbay-category-fixed ul li a:hover, .tbay-category-fixed ul li a:active,
			.related-posts .entry-content:hover .meta-info > span.entry-date,
			.cart-dropdown .cart-icon .mini-cart-items
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
				#tbay-header .header-mainmenu,
				#tbay-header.header-v2 .header-mainmenu,
				#tbay-header.header-v3 .header-mainmenu
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
				#tbay-header.header-v2 .header-mainmenu p, 
				#tbay-header.header-v3 .header-mainmenu p 
				{
					color: <?php echo esc_html(puca_tbay_get_config('topbar_text_color')); ?>;
				}
			<?php endif; ?>
			/* Top Bar Link Color */
			<?php if ( puca_tbay_get_config('topbar_link_color') != "" ) : ?>
				#tbay-header.header-v2 .header-topmenu > div > a, 
				#tbay-header.header-v3 .header-topmenu > div > a,
				#tbay-header.header-v2 .header-topmenu .tbay-menu-top > li > a,
				#tbay-header.header-v3 .header-topmenu .tbay-menu-top > li > a,
				#tbay-header.header-v2 .header-mainmenu .tbay-login .account-button,
				#tbay-header.header-v3 .header-mainmenu .tbay-login .account-button,
				#tbay-header.header-v2 .header-mainmenu .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont,
				#tbay-header.header-v3 .header-mainmenu .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont
				{
					color: <?php echo esc_html(puca_tbay_get_config('topbar_link_color')); ?>;
				}
			<?php endif; ?>			

			/* Top Bar Link Color Hover*/
			<?php if ( puca_tbay_get_config('topbar_link_color_hover') != "" ) : ?>
				#tbay-header.header-v2 .header-topmenu > div > a:hover,
				#tbay-header.header-v3 .header-topmenu > div > a:hover,
				#tbay-header.header-v2 .header-topmenu .tbay-menu-top > li:hover > a, #tbay-header.header-v2 .header-topmenu .tbay-menu-top > li:focus > a, #tbay-header.header-v2 .header-topmenu .tbay-menu-top > li.active > a, #tbay-header.header-v3 .header-topmenu .tbay-menu-top > li:hover > a, #tbay-header.header-v3 .header-topmenu .tbay-menu-top > li:focus > a, #tbay-header.header-v3 .header-topmenu .tbay-menu-top > li.active > a,
				#tbay-header.header-v2 .header-mainmenu .tbay-login .account-button:hover,
				#tbay-header.header-v3 .header-mainmenu .tbay-login .account-button:hover,
				#tbay-header.header-v2 .header-mainmenu .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont:hover,
				#tbay-header.header-v3 .header-mainmenu .woocommerce-currency-switcher-form .SumoSelect > .CaptionCont:hover
				{
					color: <?php echo esc_html(puca_tbay_get_config('topbar_link_color_hover')); ?> !important;
				}
				#tbay-header .header-topmenu .tbay-menu-top .dropdown-menu > li:hover > a,
				#tbay-header .header-topmenu .tbay-menu-top .dropdown-menu > li:focus > a,
				#tbay-header .header-topmenu .tbay-menu-top .dropdown-menu > li.active > a,
				#tbay-header .header-topmenu .tbay-menu-top .dropdown-menu > li:hover:before,
				#tbay-header .header-topmenu .tbay-menu-top .dropdown-menu > li:focus:before,
				#tbay-header .header-topmenu .tbay-menu-top .dropdown-menu > li.active:before 				
				{
					color: <?php echo esc_html(puca_tbay_get_config('topbar_link_color_hover')); ?> !important;
					border-color: <?php echo esc_html(puca_tbay_get_config('topbar_link_color_hover')); ?>;
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
				#tbay-header .header-main .header-right .top-cart-wishlist .mini-cart-text,
				#tbay-header .header-main .header-right .tbay-login .well-come,
				#tbay-header .header-main .header-right .top-cart-wishlist .qty,
				#tbay-header.header-v3 .header-main .header-right .top-cart-wishlist .cart-icon i
				{
					color: <?php echo esc_html(puca_tbay_get_config('header_text_color')); ?>;
				}
				
			<?php endif; ?>
			/* Header Link Color */
			<?php if ( puca_tbay_get_config('header_link_color') != "" ) : ?>
				#tbay-header a,
				#tbay-header .list-inline.acount li a.login, 
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
				.tbay-offcanvas-main .navbar-nav > li > a,
				#tbay-header .dropdown-menu .menu li a,
				#tbay-header .navbar-nav.megamenu .dropdown-menu > li > a,
				#tbay-header .navbar-nav.megamenu > li > a,
				#tbay-header .tbay-offcanvas-main .navbar-nav > li > a,
				#tbay-header.header-v2 .header-main .navbar-nav.megamenu > li > a, 
				#tbay-header.header-v3 .header-main .navbar-nav.megamenu > li > a
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
				.navbar-nav.megamenu > li > a:before,
				.verticle-menu .navbar-nav > li.active, .verticle-menu .navbar-nav > li:hover {
					background: <?php echo esc_html(puca_tbay_get_config('main_menu_link_color_active')); ?> !important;
				}
				.verticle-menu .navbar-nav > li.active > a, .verticle-menu .navbar-nav > li:hover > a {
					color: #fff !important;
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
				#tbay-header .header-main .header-right .top-cart-wishlist a:hover .cart-icon,
				#tbay-header .header-main .header-right .tbay-login:hover .account-button,
				#tbay-main-content .tbay_custom_menu > .widget.widget_nav_menu .menu > li > a:hover, 
				#tbay-main-content .tbay_custom_menu > .widget.widget_nav_menu .menu > li > a:focus
				
				{
					border-color: <?php echo esc_html( puca_tbay_get_config('main_menu_link_color_active') ) ?>;
				}
			<?php endif; ?>
			
			/* Background Color Menu for Home Layout 01 */
			<?php if ( puca_tbay_get_config('main_menu_background_color_hover') != "" ) : ?>
				#tbay-header.header-v1 .navbar-nav.megamenu > li:hover > a,
				#tbay-header.header-v1 .navbar-nav.megamenu > li:focus > a,
				#tbay-header.header-v1 .navbar-nav.megamenu > li.active > a 
				{
					background: <?php echo esc_html(puca_tbay_get_config('main_menu_background_color_hover')); ?>;
					color: #fff !important;
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
				#tbay-footer a,
				#tbay-footer .menu > li a
				{
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
				.tbay-footer .tb-copyright p {
					color: <?php echo esc_html(puca_tbay_get_config('copyright_text_color')); ?>;
				}
			<?php endif; ?>
			/* Footer Link Color */
			<?php if ( puca_tbay_get_config('copyright_link_color') != "" ) : ?>
				.tbay-copyright a,
				#tbay-footer .tb-copyright a {
					color: <?php echo esc_html(puca_tbay_get_config('copyright_link_color')); ?> !important;
				}
			<?php endif; ?>

			/* Footer Link Color Hover*/
			<?php if ( puca_tbay_get_config('copyright_link_color_hover') != "" ) : ?>
				.tbay-copyright a:hover,
				#tbay-footer .tb-copyright a:hover {
					color: <?php echo esc_html(puca_tbay_get_config('copyright_link_color_hover')); ?> !important;
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
