<?php
namespace PowerpackElements\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class PP_Config.
 */
class PP_Config {
	
	/**
	 * Widget List
	 *
	 * @var widget_list
	 */
	public static $widget_info = null;
	
	/**
	 * Help Docs Links
	 *
	 * @var help_docs
	 */
	public static $help_docs = null;

	/**
	 * Get Widget List.
	 *
	 * @since 1.4.13.1
	 *
	 * @return array The Widget List.
	 */
	public static function get_widget_info() {
		if ( null === self::$widget_info ) {
			self::$widget_info = array(
				'Advanced_Accordion' => array(
					'name'			=> 'pp-advanced-accordion',
					'title'			=> __( 'Advanced Accordion', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'icon'			=> 'ppicon-advanced-accordion power-pack-admin-icon',
					'keywords'		=> array( 'powerpack', 'accordion', 'advanced' ),
				),
				'Advanced_Menu' => array(
					'name'			=> 'pp-advanced-menu',
					'title'			=> __( 'Advanced Menu', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'menu', 'navigation' ),
					'icon'			=> 'ppicon-advanced-menu power-pack-admin-icon'
				),
				'Advanced_Tabs' => array(
					'name'			=> 'pp-advanced-tabs',
					'title'			=> __( 'Advanced Tabs', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'tabs' ),
					'icon'			=> 'ppicon-tabs power-pack-admin-icon'
				),
				'Album' => array(
					'name'			=> 'pp-album',
					'title'			=> __( 'Album', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'album', 'gallery', 'lightbox' ),
					'icon'			=> 'ppicon-tabs power-pack-admin-icon'
				),
				'Breadcrumbs' => array(
					'name'			=> 'pp-breadcrumbs',
					'title'			=> __( 'Breadcrumbs', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'breadcrumbs' ),
					'icon'			=> 'ppicon-breadcrumbs power-pack-admin-icon'
				),
				'Business_Hours' => array(
					'name'			=> 'pp-business-hours',
					'title'			=> __( 'Business Hours', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'business', 'hours' ),
					'icon'			=> 'ppicon-business-hours power-pack-admin-icon'
				),
				'Buttons' => array(
					'name'			=> 'pp-buttons',
					'title'			=> __( 'Buttons', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'buttons' ),
					'icon'			=> 'ppicon-multi-buttons power-pack-admin-icon'
				),
				'Caldera_Forms' => array(
					'name'			=> 'pp-caldera-forms',
					'title'			=> __( 'Caldera Forms', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'contact', 'form' ),
					'icon'			=> 'ppicon-contact-form power-pack-admin-icon'
				),
				'Card_Slider' => array(
					'name'			=> 'pp-card-slider',
					'title'			=> __( 'Card Slider', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'posts', 'cpt', 'slider' ),
					'icon'			=> 'ppicon-card-slider power-pack-admin-icon'
				),
				'Categories' => array(
					'name'			=> 'pp-categories',
					'title'			=> __( 'Categories', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'categories' ),
					'icon'			=> 'ppicon-categories power-pack-admin-icon'
				),
				'Contact_Form_7' => array(
					'name'			=> 'pp-contact-form-7',
					'title'			=> __( 'Contact Form 7', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'contact', 'form' ),
					'icon'			=> 'ppicon-contact-form power-pack-admin-icon'
				),
				'Content_Ticker' => array(
					'name'			=> 'pp-content-ticker',
					'title'			=> __( 'Content Ticker', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'posts' ),
					'icon'			=> 'ppicon-content-ticker power-pack-admin-icon'
				),
				'Countdown' => array(
					'name'			=> 'pp-countdown',
					'title'			=> __( 'Countdown Timer', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'countdown', 'timer' ),
					'icon'			=> 'ppicon-countdown power-pack-admin-icon'
				),
				'Counter' => array(
					'name'			=> 'pp-counter',
					'title'			=> __( 'Counter', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'counter' ),
					'icon'			=> 'ppicon-counter power-pack-admin-icon'
				),
				'Coupons' => array(
					'name'			=> 'pp-coupons',
					'title'			=> __( 'Coupons', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'coupon' ),
					'icon'			=> 'ppicon-coupon power-pack-admin-icon'
				),
				'Devices' => array(
					'name'			=> 'pp-devices',
					'title'			=> __( 'Devices', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'devices' ),
					'icon'			=> 'ppicon-device power-pack-admin-icon'
				),
				'Divider' => array(
					'name'			=> 'pp-divider',
					'title'			=> __( 'Divider', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'divider' ),
					'icon'			=> 'ppicon-divider power-pack-admin-icon'
				),
				'Faq' => array(
					'name'			=> 'pp-faq',
					'title'			=> __( 'FAQ', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'faq' ),
					'icon'			=> 'ppicon-advanced-accordion power-pack-admin-icon'
				),
				'Flipbox' => array(
					'name'			=> 'pp-flipbox',
					'title'			=> __( 'Flip Box', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'flip', 'box', 'flipbox' ),
					'icon'			=> 'ppicon-flip-box power-pack-admin-icon'
				),
				'Fluent_Forms' => array(
					'name'			=> 'pp-fluent-forms',
					'title'			=> __( 'Fluent Forms', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'contact', 'form' ),
					'icon'			=> 'ppicon-contact-form power-pack-admin-icon'
				),
				'Formidable_Forms' => array(
					'name'			=> 'pp-formidable-forms',
					'title'			=> __( 'Formidable Forms', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'contact', 'form' ),
					'icon'			=> 'ppicon-contact-form power-pack-admin-icon'
				),
				'Image_Gallery' => array(
					'name'			=> 'pp-image-gallery',
					'title'			=> __( 'Image Gallery', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'image', 'gallery' ),
					'icon'			=> 'ppicon-image-gallery power-pack-admin-icon'
				),
				'Image_Slider' => array(
					'name'			=> 'pp-image-slider',
					'title'			=> __( 'Image Slider', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'image', 'slider', 'slideshow', 'gallery', 'thumbnail', 'carousel' ),
					'icon'			=> 'ppicon-gallery-slider power-pack-admin-icon'
				),
				'Google_Maps' => array(
					'name'			=> 'pp-google-maps',
					'title'			=> __( 'Google Maps', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'google', 'maps' ),
					'icon'			=> 'ppicon-map power-pack-admin-icon'
				),
				'Gravity_Forms' => array(
					'name'			=> 'pp-gravity-forms',
					'title'			=> __( 'Gravity Forms', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'contact', 'form' ),
					'icon'			=> 'ppicon-contact-form power-pack-admin-icon'
				),
				'Dual_Heading' => array(
					'name'			=> 'pp-dual-heading',
					'title'			=> __( 'Dual Heading', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'dual', 'heading' ),
					'icon'			=> 'ppicon-dual-heading power-pack-admin-icon'
				),
				'Fancy_Heading' => array(
					'name'			=> 'pp-fancy-heading',
					'title'			=> __( 'Fancy Heading', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'fancy', 'heading' ),
					'icon'			=> 'ppicon-heading power-pack-admin-icon'
				),
				'Hotspots' => array(
					'name'			=> 'pp-image-hotspots',
					'title'			=> __( 'Image Hotspots', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'image', 'hotspots' ),
					'icon'			=> 'ppicon-image-hotspot power-pack-admin-icon'
				),
				'How_To' => array(
					'name'			=> 'pp-how-to',
					'title'			=> __( 'How To', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'how' ),
					'icon'			=> 'ppicon-how-to power-pack-admin-icon'
				),
				'Icon_List' => array(
					'name'			=> 'pp-icon-list',
					'title'			=> __( 'Icon List', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'icon', 'list' ),
					'icon'			=> 'ppicon-icon-list power-pack-admin-icon'
				),
				'Image_Accordion' => array(
					'name'			=> 'pp-image-accordion',
					'title'			=> __( 'Image Accordion', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack' ),
					'icon'			=> 'ppicon-image-accordion power-pack-admin-icon'
				),
				'Image_Comparison' => array(
					'name'			=> 'pp-image-comparison',
					'title'			=> __( 'Image Comparison', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'image', 'comparison', 'before', 'after', 'slider' ),
					'icon'			=> 'ppicon-image-comparison power-pack-admin-icon'
				),
				'Info_Box' => array(
					'name'			=> 'pp-info-box',
					'title'			=> __( 'Info Box', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'info' ),
					'icon'			=> 'ppicon-info-box power-pack-admin-icon'
				),
				'Info_Box_Carousel' => array(
					'name'			=> 'pp-info-box-carousel',
					'title'			=> __( 'Info Box Carousel', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'info' ),
					'icon'			=> 'ppicon-info-box-carousel power-pack-admin-icon'
				),
				'Info_List' => array(
					'name'			=> 'pp-info-list',
					'title'			=> __( 'Info List', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'info' ),
					'icon'			=> 'ppicon-info-list power-pack-admin-icon'
				),
				'Info_Table' => array(
					'name'			=> 'pp-info-table',
					'title'			=> __( 'Info Table', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'info' ),
					'icon'			=> 'ppicon-info-table power-pack-admin-icon'
				),
				'Instafeed' => array(
					'name'			=> 'pp-instafeed',
					'title'			=> __( 'Instagram Feed', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'instagram' ),
					'icon'			=> 'ppicon-instagram-feed power-pack-admin-icon'
				),
				'Link_Effects' => array(
					'name'			=> 'pa-link-effects',
					'title'			=> __( 'Link Effects', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack' ),
					'icon'			=> 'ppicon-link-effects power-pack-admin-icon'
				),
				'Logo_Carousel' => array(
					'name'			=> 'pp-logo-carousel',
					'title'			=> __( 'Logo Carousel', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'logo', 'carousel', 'image' ),
					'icon'			=> 'ppicon-logo-carousel power-pack-admin-icon'
				),
				'Logo_Grid' => array(
					'name'			=> 'pp-logo-grid',
					'title'			=> __( 'Logo Grid', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'logo', 'image' ),
					'icon'			=> 'ppicon-logo-grid power-pack-admin-icon'
				),
				'Magazine_Slider' => array(
					'name'			=> 'pp-magazine-slider',
					'title'			=> __( 'Magazine Slider', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'posts' ),
					'icon'			=> 'ppicon-magazine-slider power-pack-admin-icon'
				),
				'Ninja_Forms' => array(
					'name'			=> 'pp-ninja-forms',
					'title'			=> __( 'Ninja Forms', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'contact', 'form' ),
					'icon'			=> 'ppicon-contact-form power-pack-admin-icon'
				),
				'Offcanvas_Content' => array(
					'name'			=> 'pp-offcanvas-content',
					'title'			=> __( 'Offcanvas Content', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'offcanvas', 'off canvas' ),
					'icon'			=> 'ppicon-offcanvas-content power-pack-admin-icon'
				),
				'Onepage_Nav' => array(
					'name'			=> 'pp-one-page-nav',
					'title'			=> __( 'One Page Navigation', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'one', 'page', 'dot' ),
					'icon'			=> 'ppicon-page-navigation power-pack-admin-icon'
				),
				'Popup_Box' => array(
					'name'			=> 'pp-modal-popup',
					'title'			=> __( 'Popup Box', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'modal', 'popup' ),
					'icon'			=> 'ppicon-popup-box power-pack-admin-icon'
				),
				'Posts' => array(
					'name'			=> 'pp-posts',
					'title'			=> __( 'Posts', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack' ),
					'icon'			=> 'ppicon-posts-grid power-pack-admin-icon'
				),
				'Price_Menu' => array(
					'name'			=> 'pp-price-menu',
					'title'			=> __( 'Price Menu', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'price' ),
					'icon'			=> 'ppicon-pricing-menu power-pack-admin-icon'
				),
				'Pricing_Table' => array(
					'name'			=> 'pp-pricing-table',
					'title'			=> __( 'Pricing Table', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'price' ),
					'icon'			=> 'ppicon-pricing-table power-pack-admin-icon'
				),
				'Promo_Box' => array(
					'name'			=> 'pp-promo-box',
					'title'			=> __( 'Promo Box', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'info' ),
					'icon'			=> 'ppicon-promo-box power-pack-admin-icon'
				),
				'Recipe' => array(
					'name'			=> 'pp-recipe',
					'title'			=> __( 'Recipe', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'dish' ),
					'icon'			=> 'ppicon-recipe power-pack-admin-icon'
				),
				'Review_Box' => array(
					'name'			=> 'pp-review-box',
					'title'			=> __( 'Review Box', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'image' ),
					'icon'			=> 'ppicon-review-box power-pack-admin-icon'
				),
				'Scroll_Image' => array(
					'name'			=> 'pp-scroll-image',
					'title'			=> __( 'Scroll Image', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'image' ),
					'icon'			=> 'ppicon-scroll-image power-pack-admin-icon'
				),
				'Showcase' => array(
					'name'			=> 'pp-showcase',
					'title'			=> __( 'Showcase', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'image', 'video', 'embed', 'youtube', 'vimeo', 'dailymotion', 'slider' ),
					'icon'			=> 'ppicon-showcase power-pack-admin-icon'
				),
				'Table' => array(
					'name'			=> 'pp-table',
					'title'			=> __( 'Table', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'table', 'csv' ),
					'icon'			=> 'ppicon-table power-pack-admin-icon'
				),
				'Tabbed_Gallery' => array(
					'name'			=> 'pp-tabbed-gallery',
					'title'			=> __( 'Tabbed Gallery', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'image', 'gallery', 'carousel', 'tab', 'slider' ),
					'icon'			=> 'ppicon-tabbed-gallery power-pack-admin-icon'
				),
				'Team_Member' => array(
					'name'			=> 'pp-team-member',
					'title'			=> __( 'Team Member', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'team', 'member' ),
					'icon'			=> 'ppicon-team-member power-pack-admin-icon'
				),
				'Team_Member_Carousel' => array(
					'name'			=> 'pp-team-member-carousel',
					'title'			=> __( 'Team Member Carousel', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'team', 'member', 'carousel' ),
					'icon'			=> 'ppicon-team-member-carousel power-pack-admin-icon'
				),
				'Testimonials' => array(
					'name'			=> 'pp-testimonials',
					'title'			=> __( 'Testimonials', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'testimonials', 'reviews' ),
					'icon'			=> 'ppicon-testimonial-carousel power-pack-admin-icon'
				),
				'Tiled_Posts' => array(
					'name'			=> 'pp-tiled-posts',
					'title'			=> __( 'Tiled Posts', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack' ),
					'icon'			=> 'ppicon-tiled-post power-pack-admin-icon'
				),
				'Timeline' => array(
					'name'			=> 'pp-timeline',
					'title'			=> __( 'Timeline', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack' ),
					'icon'			=> 'ppicon-timeline power-pack-admin-icon'
				),
				'Toggle' => array(
					'name'			=> 'pp-toggle',
					'title'			=> __( 'Toggle', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'toggle', 'youtube', 'dailymotion' ),
					'icon'			=> 'ppicon-content-toggle power-pack-admin-icon'
				),
				'Twitter_Buttons' => array(
					'name'			=> 'pp-twitter-buttons',
					'title'			=> __( 'Twitter Buttons', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack' ),
					'icon'			=> 'ppicon-twitter-buttons power-pack-admin-icon'
				),
				'Twitter_Grid' => array(
					'name'			=> 'pp-twitter-grid',
					'title'			=> __( 'Twitter Grid', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack' ),
					'icon'			=> 'ppicon-twitter-grid power-pack-admin-icon'
				),
				'Twitter_Timeline' => array(
					'name'			=> 'pp-twitter-timeline',
					'title'			=> __( 'Twitter Timeline', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack' ),
					'icon'			=> 'ppicon-twitter-timeline power-pack-admin-icon'
				),
				'Twitter_Tweet' => array(
					'name'			=> 'pp-twitter-tweet',
					'title'			=> __( 'Twitter Tweet', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack' ),
					'icon'			=> 'ppicon-twitter-tweet power-pack-admin-icon'
				),
				'Video' => array(
					'name'			=> 'pp-video',
					'title'			=> __( 'Video', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'video', 'youtube', 'dailymotion' ),
					'icon'			=> 'ppicon-video power-pack-admin-icon'
				),
				'Video_Gallery' => array(
					'name'			=> 'pp-video-gallery',
					'title'			=> __( 'Video Gallery', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'video', 'youtube', 'dailymotion' ),
					'icon'			=> 'ppicon-video-gallery power-pack-admin-icon'
				),
				'WP_Forms' => array(
					'name'			=> 'pp-wpforms',
					'title'			=> __( 'WP Forms', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'contact', 'form' ),
					'icon'			=> 'ppicon-contact-form power-pack-admin-icon'
				),
				'Woo_Add_To_Cart' => array(
					'name'			=> 'pp-woo-add-to-cart',
					'title'			=> __( 'Woo - Add To Cart', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'woocommerce' ),
					'icon'			=> 'ppicon-woo-add-to-cart power-pack-admin-icon'
				),
				'Woo_Cart' => array(
					'name'			=> 'pp-woo-cart',
					'title'			=> __( 'Woo - Cart', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'woocommerce' ),
					'icon'			=> 'ppicon-woo-cart power-pack-admin-icon'
				),
				'Woo_Categories' => array(
					'name'			=> 'pp-woo-categories',
					'title'			=> __( 'Woo - Categories', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'woocommerce', 'category' ),
					'icon'			=> 'ppicon-woo-categories power-pack-admin-icon'
				),
				'Woo_Checkout' => array(
					'name'			=> 'pp-woo-checkout',
					'title'			=> __( 'Woo - Checkout', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'woocommerce' ),
					'icon'			=> 'ppicon-woo-checkout power-pack-admin-icon'
				),
				'Woo_Mini_Cart' => array(
					'name'			=> 'pp-woo-mini-cart',
					'title'			=> __( 'Woo - Mini Cart', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'woocommerce' ),
					'icon'			=> 'ppicon-mini-cart power-pack-admin-icon'
				),
				'Woo_Offcanvas_Cart' => array(
					'name'			=> 'pp-woo-offcanvas-cart',
					'title'			=> __( 'Woo - Off Canvas Cart', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'woocommerce', 'offcanvas' ),
					'icon'			=> 'ppicon-offcanvas-cart power-pack-admin-icon'
				),
				'Woo_Products' => array(
					'name'			=> 'pp-woo-products',
					'title'			=> __( 'Woo - Products', 'powerpack' ),
					'categories'	=> array('power-pack'),
					'keywords'		=> array( 'powerpack', 'woocommerce' ),
					'icon'			=> 'ppicon-woo-products power-pack-admin-icon'
				),
			);
		}
		
		return apply_filters( 'pp_elements_widget_info', self::$widget_info );
	}
	
	/**
	 * Add helper links for widgets
	 *
	 * @since 1.4.13.1
	 * @access public
	 */
    public static function widgets_help_links() {
		$utm_suffix = '?utm_source=widget&utm_medium=panel&utm_campaign=userkb';
		
		self::$help_docs = [
			// Advanced Menu
			'Advanced_Menu' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=zqmVdbwJKv0&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj'
			),
			// Advanced Tabs
			'Advanced_Tabs' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=B8pZWoWv6qM&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj'
			),
			// Business Hours
			'Business_Hours' => array(
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/business-hours/business-hours-widget-overview/' . $utm_suffix
			),
			// Countdown
			'Countdown' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=yVoK82Nji4E&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj'
			),
			// Counter
			'Counter' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=g70UKxK_1dU&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/counter/counter-widget-overview/' . $utm_suffix
			),
			// Card Slider
			'Card_Slider' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=DZhexjnQ1rQ&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Coupons
			'Coupons' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=3sIYhMcud88&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Devices
			'Devices' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=r2acXl2Hzak&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Dual Heading
			'Dual_Heading' => array(
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/dual-heading/dual-heading-widget-overview/' . $utm_suffix,
			),
			// Fancy Heading
			'Fancy_Heading' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=PxWWUTeW4dc&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// FAQ
			'Faq' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=bN2g-fDuqss&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Flip Box
			'Flipbox' => array(
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/flip-box/flip-box-widget-overview/' . $utm_suffix,
			),
			// Google Maps
			'Google_Maps' => array(
				__( 'How to Add Custom Style to the PowerPack Google Maps Widget?', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/google-maps/how-to-add-custom-style-to-the-powerpack-google-maps-widget/' . $utm_suffix,
				__( 'How to get the API Key for Google Maps Widget?', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/google-maps/how-to-get-the-api-key-for-google-maps-widget/' . $utm_suffix,
				__( 'How to Set-Up Google Maps Widget?', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/google-maps/how-to-set-up-google-maps-widget/' . $utm_suffix,
			),
			// Gravity Forms
			'Gravity_Forms' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=fw47JcVDIpI&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Icon List
			'Icon_List' => array(
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/icon-list/icon-list-widget-overview/' . $utm_suffix,
			),
			// Image Comparison
			'Image_Comparison' => array(
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/image-comparison/image-comparison-widget-overview/' . $utm_suffix,
			),
			// Image Hotspots
			'Hotspots' => array(
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/image-hotspots/image-hotspots-widget-overview/' . $utm_suffix,
			),
			// Info Box
			'Info_Box' => array(
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/info-box/info-box-widget-overview/' . $utm_suffix,
			),
			// Info Box Carousel
			'Info_Box_Carousel' => array(
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/info-box-carousel/info-box-carousel-widget-overview/' . $utm_suffix,
			),
			// Info List
			'Info_List' => array(
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/info-list/info-list-widget-overview/' . $utm_suffix,
			),
			// Instafeed
			'Instafeed' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=33A9XL1twFE&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/instagram-feed/instagram-feed-widget-overview/' . $utm_suffix,
				__( 'How to set up Instagram Feed widget?', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/instagram-feed/elementor-instagram-widget-setup/' . $utm_suffix,
			),
			// Image Gallery
			'Image_Gallery' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=nMdbTXq6-HI&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
				__( 'How to add Load More Button?', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/image-gallery/how-to-add-load-more-button/' . $utm_suffix,
				__( 'How to Enable and use Filters?', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/image-gallery/how-to-enable-and-use-filters/' . $utm_suffix,
			),
			// Image Slider
			'Image_Slider' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=fqHXz_vPqwk&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Logo Carousel
			'Logo_Carousel' => array(
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/logo-carousel/logo-carousel-widget-overview/' . $utm_suffix,
			),
			// Logo Grid
			'Logo_Grid' => array(
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/logo-grid/logo-grid-widget-overview/' . $utm_suffix,
			),
			// Popup Box
			'Popup_Box' => array(
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/modal-popup/modal-popup-widget-overview/' . $utm_suffix,
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=DpnrcazTLeU&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
				__( 'How to Create Time Delayed Pop Up Modal Box With Elementor', 'powerpack' ) => 'https://www.youtube.com/watch?v=2fhUycMf0Uk&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Posts
			'Posts' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=9-SF5w93Yr8&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj&index=14',
				__( 'Action Hooks for Post Widget', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/posts/actions-hooks-for-post-widget/' . $utm_suffix,
				__( 'How to Customize Query in Post Widget?', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/posts/how-to-customize-query-in-post-widget/' . $utm_suffix,
			),
			// Offcanvas Content
			'Offcanvas_Content' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=NCWch6s7g8w&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
				__( 'How to Trigger Off-Canvas Content from Menu Item?', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/offcanvas-content/trigger-off-canvas-content-menu-item/' . $utm_suffix,
			),
			// One Page Navigation
			'Onepage_Nav' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=onZ0mnkRJiY&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Price Menu
			'Price_Menu' => array(
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/price-menu/price-menu-widget-overview/' . $utm_suffix,
			),
			// Pricing Table
			'Pricing_Table' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=cO-WFCHtwiM&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/pricing-table/pricing-table-widget-overview/' . $utm_suffix,
			),
			// Promo Box
			'Promo_Box' => array(
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/promo-box/promo-box-widget-overview/' . $utm_suffix,
			),
			// Review Box
			'Review_Box' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=Qj0GzfoUSyQ&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Scroll Image
			'Scroll_Image' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=eduATa8FPpU&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Showcase
			'Showcase' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=yjw1PuRXQ2M&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Tabbed Gallery
			'Tabbed_Gallery' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=KSx-eNJNgG0&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Table
			'pp-table' => array(
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/table/table-widget-overview/' . $utm_suffix,
				__( 'How to Extend Cells to Multiple Columns and Rows?', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/table/how-to-extend-cells-to-multiple-columns-and-rows-in-table/' . $utm_suffix,
				__( 'How to make Table Sortable?', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/table/how-to-make-any-powerpack-table-sortable/' . $utm_suffix,
				__( 'How to setup Table to Display Content?', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/table/how-to-setup-powerpack-table-to-display-content/' . $utm_suffix,
			),
			// Tiled Posts
			'Tiled_Posts' => array(
				__( 'Widget Overview', 'powerpack' ) => 'https://powerpackelements.com/docs/powerpack/widgets/tiled-posts/tiled-posts-widget-overview/' . $utm_suffix,
			),
			// Timeline
			'Timeline' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=uEZDhXeqT4E&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Toggle
			'Toggle' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=xz8gJ0QhOYI&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Woo Cart
			'Woo_Cart' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=bMZd3aC4b9E&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Woo Checkout
			'Woo_Checkout' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=XFE04Mzk_p0&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Woo Mini Cart
			'Woo_Mini_Cart' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=jkfzj3qXwGM&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Woo Offcanvas Cart
			'Woo_Offcanvas_Cart' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=u1Wh5z6vpo4&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
			// Woo Products
			'Woo_Products' => array(
				__( 'Watch Video Overview', 'powerpack' ) => 'https://www.youtube.com/watch?v=sVtUtiHbr5E&list=PLpsSO_wNe8Dz4vfe2tWlySBCCFEgh1qZj',
			),
		];

		return apply_filters( 'pp_elements_help_links', self::$help_docs );
	}
	
	public static function get_widget_help_links( $widget ) {
		$settings = \PowerpackElements\Classes\PP_Admin_Settings::get_settings();
		
		if ( 'on' != $settings['hide_support'] ) {
			$links = self::widgets_help_links();
		} else {
			$links = array();
		}
		
		if ( isset( $links[ $widget ] ) ) {
			return $links[ $widget ];
		}

		return '';
	}
}