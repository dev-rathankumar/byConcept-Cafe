<?php
if ( ! class_exists( 'WDAP_FORM' ) ) {

	class WDAP_FORM extends FlipperCode_HTML_Markup {

		function __construct( $options = array() ) {

			$productOverview = array(
				'subscribe_mailing_list' => esc_html__( 'Subscribe to our mailing list', 'woo-delivery-area-pro' ),
				'product_info_heading' => esc_html__( 'Product Information', 'woo-delivery-area-pro' ),
				'product_info_desc' => esc_html__( 'For our each product we have set up demo pages where you can see the plugin in working mode.', 'woo-delivery-area-pro' ),
				'live_demo_caption' => esc_html__( 'Live Demos', 'woo-delivery-area-pro' ),
				'installed_version' => esc_html__( 'Installed version :', 'woo-delivery-area-pro' ),
				'latest_version_available' => esc_html__( 'Latest Version Available : ', 'woo-delivery-area-pro' ),
				'updates_available' => esc_html__( 'Update Available', 'woo-delivery-area-pro' ),
				'subscribe_now' => array(
					'heading' => esc_html__( 'Subscribe Now', 'woo-delivery-area-pro' ),
					'desc1' => esc_html__( 'Receive updates on our new product features and new products effortlessly.', 'woo-delivery-area-pro' ),
					'desc2' => esc_html__( 'We will not share your email addresses in any case.', 'woo-delivery-area-pro' ),
				),
				'product_support' => array(
					'heading' => esc_html__( 'Product Support', 'woo-delivery-area-pro' ),
					'desc' => esc_html__( 'For our each product we have very well explained starting guide to get you started in matter of minutes.', 'woo-delivery-area-pro' ),
					'click_here' => esc_html__( ' Click Here', 'woo-delivery-area-pro' ),
					'desc2' => esc_html__( 'For our each product we have set up demo pages where you can see the plugin in working mode. You can see a working demo before making a purchase.', 'woo-delivery-area-pro' ),
				),
				'refund' => array(
					'heading' => esc_html__( 'Get Refund', 'woo-delivery-area-pro' ),
					'desc' => esc_html__( 'Please click on the below button to initiate the refund process.', 'woo-delivery-area-pro' ),
					'request' => esc_html__( 'Request a Refund', 'woo-delivery-area-pro' ),
				),
				'support' => array(
					'heading' => esc_html__( 'Extended Technical Support', 'woo-delivery-area-pro' ),
					'desc1' => esc_html__( 'We provide technical support for all of our products. You can opt for 12 months support below.', 'woo-delivery-area-pro' ),
					'link' => esc_html__( 'Extend support', 'woo-delivery-area-pro' ),
					'link2' => esc_html__( 'Get Extended Licence', 'woo-delivery-area-pro' ),
				),

			);

			$productInfo = array(
				'productName' => esc_html__( 'Woocommerce Delivery Area Pro', 'woo-delivery-area-pro' ),
				'productSlug' => esc_html__( 'wdap_view_overview', 'woo-delivery-area-pro' ),
				'productTagLine' => esc_html__( 'A woocommerce extention that allows users for checking shipping availablity of woocommerce products by zip code.', 'woo-delivery-area-pro' ),
				'productTextDomain' => 'woo-delivery-area-pro',
				'productIconImage' => WDAP_URL . 'core/core-assets/images/wp-poet.png',
				'productVersion' => WDAP_VERSION,
				'docURL' => 'https://www.flippercode.com/woocommerce-delivery-area-pro/',
				'videoURL' => 'https://www.youtube.com/watch?v=0x1gbCgn5b8&list=PLlCp-8jiD3p3skgYCjyW2ooRi62SY8fq6',
				'demoURL' => 'https://www.flippercode.com/woocommerce-delivery-area-pro/',
				'productImagePath' => WDAP_URL . 'core/core-assets/product-images/',
				'productSaleURL' => 'https://codecanyon.net/item/woo-delivery-area-pro/19476751',
				'multisiteLicence' => 'https://codecanyon.net/item/woo-delivery-area-pro/19476751?license=extended&open_purchase_for_item_id=19476751&purchasable=source',
				'productOverview' => $productOverview,

			);

			$productInfo = array_merge( $productInfo, $options );
			parent::__construct( $productInfo );

		}

	}

}
