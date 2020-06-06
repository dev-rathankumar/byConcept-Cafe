<?php
namespace PowerpackElements\Classes;

class PP_Elements_WPML {
	public function __construct() {
		add_filter( 'wpml_elementor_widgets_to_translate', array( $this, 'translate_fields' ) );
		$this->type = 'widgetType';
	}

	public function translate_fields( $widgets ) {
		$widgets['pp-advanced-accordion']   = [
			'conditions'        => [ $this->type => 'pp-advanced-accordion' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Advanced_Accordion',
		];
		$widgets['pp-advanced-menu']        = [
			'conditions' => [ $this->type => 'pp-advanced-menu' ],
			'fields'     => [
				[
					'field'       => 'toggle_label',
					'type'        => __( 'Advanced Menu - Toggle Label', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-advanced-tabs']        = [
			'conditions'        => [ $this->type => 'pp-advanced-tabs' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Advanced_Tabs',
		];
		$widgets['pp-album']                = [
			'conditions' => [ $this->type => 'pp-album' ],
			'fields'     => [
				[
					'field'       => 'album_trigger_button_text',
					'type'        => __( 'Album - Trigger Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'album_title',
					'type'        => __( 'Album - Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'album_subtitle',
					'type'        => __( 'Album - Subtitle', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'album_cover_button_text',
					'type'        => __( 'Album - Cover Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-breadcrumbs']          = [
			'conditions' => [ $this->type => 'pp-breadcrumbs' ],
			'fields'     => [
				[
					'field'       => 'home_text',
					'type'        => __( 'Breadcrumbs - Home Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'blog_text',
					'type'        => __( 'Breadcrumbs - Blog Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'		  => 'separator_text',
					'type'		  => __( 'Breadcrumbs - Separator Text', 'powerpack' ),
				],
			],
		];
		$widgets['pp-business-hours']       = [
			'conditions'        => [ $this->type => 'pp-business-hours' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Business_Hours',
		];
		$widgets['pp-buttons']              = [
			'conditions'        => [ $this->type => 'pp-buttons' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Buttons',
		];
		$widgets['pp-caldera-forms']        = [
			'conditions' => [ $this->type => 'pp-caldera-forms' ],
			'fields'     => [
				[
					'field'       => 'form_title_custom',
					'type'        => __( 'Caldera Forms - Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'form_description_custom',
					'type'        => __( 'Caldera Forms - Description', 'powerpack' ),
					'editor_type' => 'AREA',
				],
			],
		];
		$widgets['pp-card-slider']          = [
			'conditions'        => [ $this->type => 'pp-card-slider' ],
			'fields'            => [
				[
					'field'       => 'button_text',
					'type'        => __( 'Card Slider - Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => 'WPML_PP_Card_Slider',
		];
		$widgets['pp-contact-form-7']       = [
			'conditions' => [ $this->type => 'pp-contact-form-7' ],
			'fields'     => [
				[
					'field'       => 'form_title_text',
					'type'        => __( 'Contact Form 7 - Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'form_description_text',
					'type'        => __( 'Contact Form 7 - Description', 'powerpack' ),
					'editor_type' => 'AREA',
				],
			],
		];
		$widgets['pp-content-ticker']       = [
			'conditions'        => [ $this->type => 'pp-content-ticker' ],
			'fields'            => [
				[
					'field'       => 'heading',
					'type'        => __( 'Content Ticker - Heading Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => 'WPML_PP_Content_Ticker',
		];
		$widgets['pp-content-reveal']       = [
			'conditions' => [ $this->type => 'pp-content-reveal' ],
			'fields'     => [
				[
					'field'       => 'content',
					'type'        => __( 'Content Reveal - Content Type = Content', 'powerpack' ),
					'editor_type' => 'VISUAL',
				],
				[
					'field'       => 'button_text_closed',
					'type'        => __( 'Content Reveal - Content Unreveal Label', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_text_open',
					'type'        => __( 'Content Reveal - Content Reveal Label', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-countdown']            = [
			'conditions' => [ $this->type => 'pp-countdown' ],
			'fields'     => [
				[
					'field'       => 'fixed_expire_message',
					'type'        => __( 'Countdown - Fixed Expiry Message', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				'fixed_redirect_link' => [
					'field'       => 'url',
					'type'        => __( 'Countdown - Fixed Redirect Link', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'evergreen_expire_message',
					'type'        => __( 'Countdown - Evergreen Expire Message', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				'evergreen_redirect_link' => [
					'field'       => 'url',
					'type'        => __( 'Countdown - Evergreen Redirect Link', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'label_years_plural',
					'type'        => __( 'Countdown - Years in Plural', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'label_years_singular',
					'type'        => __( 'Countdown - Years in Singular', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'label_months_plural',
					'type'        => __( 'Countdown - Months in Plural', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'label_months_singular',
					'type'        => __( 'Countdown - Months in Singular', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'label_days_plural',
					'type'        => __( 'Countdown - Days in Plural', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'label_days_singular',
					'type'        => __( 'Countdown - Days in Singular', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'label_hours_plural',
					'type'        => __( 'Countdown - Hours in Plural', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'label_hours_singular',
					'type'        => __( 'Countdown - Hours in Singular', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'label_minutes_plural',
					'type'        => __( 'Countdown - Minutes in Plural', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'label_minutes_singular',
					'type'        => __( 'Countdown - Minutes in Singular', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'label_seconds_plural',
					'type'        => __( 'Countdown - Seconds in Plural', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'label_seconds_singular',
					'type'        => __( 'Countdown - Seconds in Singular', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-counter']              = [
			'conditions' => [ $this->type => 'pp-counter' ],
			'fields'     => [
				[
					'field'       => 'starting_number',
					'type'        => __( 'Counter - Starting Number', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'ending_number',
					'type'        => __( 'Counter - Ending Number', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'number_prefix',
					'type'        => __( 'Counter - Number Prefix', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'number_suffix',
					'type'        => __( 'Counter - Number Suffix', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'counter_title',
					'type'        => __( 'Counter - Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'counter_subtitle',
					'type'        => __( 'Counter - Subtitle', 'powerpack' ),
					'editor_type' => 'LINE'
				],
			],
		];
		$widgets['pp-coupons']              = [
			'conditions'        => [ $this->type => 'pp-coupons' ],
			'fields'            => [
				[
					'field'       => 'coupon_reveal',
					'type'        => __( 'Coupons - Coupon Reveal Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'no_code_need',
					'type'        => __( 'Coupons - No Coupon Code Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_text',
					'type'        => __( 'Coupons - Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => 'WPML_PP_Coupons',
		];
		$widgets['pp-devices']              = [
			'conditions' => [ $this->type => 'pp-devices' ],
			'fields'     => [
				[
					'field'       => 'youtube_url',
					'type'        => __( 'Devices - Youtube URL', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'vimeo_url',
					'type'        => __( 'Devices - Vimeo URL', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'dailymotion_url',
					'type'        => __( 'Devices - Dailymotion URL', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'video_url_mp4',
					'type'        => __( 'Devices - Video URL MP4', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'video_source_m4v',
					'type'        => __( 'Devices - Video URL M4V', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'video_url_ogg',
					'type'        => __( 'Devices - Video URL OGG', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'video_url_webm',
					'type'        => __( 'Devices - Video URL WEBM', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'start_time',
					'type'        => __( 'Devices - Start Time', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'end_time',
					'type'        => __( 'Devices - End Time', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-divider']              = [
			'conditions' => [ $this->type => 'pp-divider' ],
			'fields'     => [
				[
					'field'       => 'divider_text',
					'type'        => __( 'Divider - Divider Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-dual-heading']         = [
			'conditions' => [ $this->type => 'pp-dual-heading' ],
			'fields'     => [
				[
					'field'       => 'first_text',
					'type'        => __( 'Dual Heading - First Text', 'powerpack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'second_text',
					'type'        => __( 'Dual Heading - Second Text', 'powerpack' ),
					'editor_type' => 'AREA',
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Dual Heading - Link', 'powerpack' ),
					'editor_type' => 'LINK',
				],
			],
		];
		$widgets['pp-faq']                  = [
			'conditions'        => [ $this->type => 'pp-faq' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Faq',
		];
		$widgets['pp-fancy-heading']        = [
			'conditions' => [ $this->type => 'pp-fancy-heading' ],
			'fields'     => [
				[
					'field'       => 'heading_text',
					'type'        => __( 'Fancy Heading - Heading Text', 'powerpack' ),
					'editor_type' => 'AREA',
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Fancy Heading - Link', 'powerpack' ),
					'editor_type' => 'LINK',
				],
			],
		];
		$widgets['pp-flipbox']              = [
			'conditions' => [ $this->type => 'pp-flipbox' ],
			'fields'     => [
				[
					'field'       => 'title_front',
					'type'        => __( 'Flip Box - Front Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_front',
					'type'        => __( 'Flip Box - Front Description', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'title_back',
					'type'        => __( 'Flip Box - Back Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_back',
					'type'        => __( 'Flip Box - Back Description', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Flip Box - Link', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'flipbox_button_text',
					'type'        => __( 'Flip Box - Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-fluent-forms']         = [
			'conditions' => [ $this->type => 'pp-fluent-forms' ],
			'fields'     => [
				[
					'field'       => 'form_title_custom',
					'type'        => __( 'Fluent Forms - Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'form_description_custom',
					'type'        => __( 'Fluent Forms - Description', 'powerpack' ),
					'editor_type' => 'AREA',
				],
			],
		];
		$widgets['pp-formidable-forms']     = [
			'conditions' => [ $this->type => 'pp-formidable-forms' ],
			'fields'     => [
				[
					'field'       => 'form_title_custom',
					'type'        => __( 'Formidable Forms - Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'form_description_custom',
					'type'        => __( 'Formidable Forms - Description', 'powerpack' ),
					'editor_type' => 'AREA',
				],
			],
		];
		$widgets['pp-google-maps']          = [
			'conditions'        => [ $this->type => 'pp-google-maps' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Google_Maps',
		];
		$widgets['pp-gravity-forms']        = [
			'conditions' => [ $this->type => 'pp-gravity-forms' ],
			'fields'     => [
				[
					'field'       => 'form_title_custom',
					'type'        => __( 'Gravity Forms - Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'form_description_custom',
					'type'        => __( 'Gravity Forms - Description', 'powerpack' ),
					'editor_type' => 'AREA',
				],
			],
		];
		$widgets['pp-how-to']               = [
			'conditions'        => [ $this->type => 'pp-how-to' ],
			'fields'            => [
				[
					'field'       => 'how_to_title',
					'type'        => __( 'How To - Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'how_to_description',
					'type'        => __( 'How To - Description', 'powerpack' ),
					'editor_type' => 'VISUAL',
				],
				[
					'field'       => 'total_time_text',
					'type'        => __( 'How To - Total Time Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'total_time_years',
					'type'        => __( 'How To - Total Time Years', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'total_time_months',
					'type'        => __( 'How To - Total Time Months', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'total_time_days',
					'type'        => __( 'How To - Total Time Days', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'total_time_hours',
					'type'        => __( 'How To - Total Time Hours', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'total_time_minutes',
					'type'        => __( 'How To - Total Time Minutes', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'estimated_cost_text',
					'type'        => __( 'How To - Estimated Cost Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'estimated_cost',
					'type'        => __( 'How To - Estimated Cost', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'supply_title',
					'type'        => __( 'How To - Supply Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'tool_title',
					'type'        => __( 'How To - Tool Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'step_section_title',
					'type'        => __( 'How To - Steps Section Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => 'WPML_PP_How_To',
		];
		$widgets['pp-image-accordion']      = [
			'conditions'        => [ $this->type => 'pp-image-accordion' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Image_Accordion',
		];
		$widgets['pp-image-hotspots']       = [
			'conditions'        => [ $this->type => 'pp-image-hotspots' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Image_Hotspots',
		];
		$widgets['pp-icon-list']            = [
			'conditions'        => [ $this->type => 'pp-icon-list' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Icon_List',
		];
		$widgets['pp-image-comparison']     = [
			'conditions' => [ $this->type => 'pp-image-comparison' ],
			'fields'     => [
				[
					'field'       => 'before_label',
					'type'        => __( 'Image Comparision - Before Label', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'after_label',
					'type'        => __( 'Image Comparision - After Label', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-image-gallery']        = [
			'conditions' => [ $this->type => 'pp-image-gallery' ],
			'fields'     => [
				[
					'field'       => 'filter_all_label',
					'type'        => __( 'Image Gallery - "All" Filter Label', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'load_more_text',
					'type'        => __( 'Image Gallery - Load More Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => 'WPML_PP_Image_Gallery',
		];
		$widgets['pp-info-box']             = [
			'conditions' => [ $this->type => 'pp-info-box' ],
			'fields'     => [
				[
					'field'       => 'icon_text',
					'type'        => __( 'Info Box - Icon Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'heading',
					'type'        => __( 'Info Box - Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'sub_heading',
					'type'        => __( 'Info Box - Subtitle', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description',
					'type'        => __( 'Info Box - Description', 'powerpack' ),
					'editor_type' => 'AREA',
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Info Box - Link', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_text',
					'type'        => __( 'Info Box - Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-info-box-carousel']    = [
			'conditions'        => [ $this->type => 'pp-info-box-carousel' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Info_Box_Carousel',
		];
		$widgets['pp-info-list']            = [
			'conditions'        => [ $this->type => 'pp-info-list' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Info_List',
		];
		$widgets['pp-info-table']           = [
			'conditions' => [ $this->type => 'pp-info-table' ],
			'fields'     => [
				[
					'field'       => 'icon_text',
					'type'        => __( 'Info Table - Icon Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'heading',
					'type'        => __( 'Info Table - Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'sub_heading',
					'type'        => __( 'Info Table - Subtitle', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description',
					'type'        => __( 'Info Table - Description', 'powerpack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'sale_badge_text',
					'type'        => __( 'Info Table - Sale Badge Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Info Table - Link', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_text',
					'type'        => __( 'Info Table - Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-instafeed']            = [
			'conditions' => [ $this->type => 'pp-instafeed' ],
			'fields'     => [
				[
					'field'       => 'insta_link_title',
					'type'        => __( 'Instagram Feed - Link Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				'insta_profile_url' => [
					'field'       => 'url',
					'type'        => __( 'Instagram Feed - Instagram Profile URL', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'load_more_button_text',
					'type'        => __( 'Instagram Feed - Load More Button Text', 'powerpack' ),
					'editor_type' => 'LINE'
				],
			],
		];
		$widgets['pa-link-effects']         = [
			'conditions' => [ $this->type => 'pa-link-effects' ],
			'fields'     => [
				[
					'field'       => 'text',
					'type'        => __( 'Link Effects - Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'secondary_text',
					'type'        => __( 'Link Effects - Secondary Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Link Effects - link', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-logo-carousel']        = [
			'conditions'        => [ $this->type => 'pp-logo-carousel' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Logo_Carousel',
		];
		$widgets['pp-logo-grid']            = [
			'conditions'        => [ $this->type => 'pp-logo-grid' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Logo_Grid',
		];
		$widgets['pp-magazine-slider']      = [
			'conditions' => [ $this->type => 'pp-magazine-slider' ],
			'fields'     => [
				[
					'field'       => 'post_meta_divider',
					'type'        => __( 'Magazine Slider - Post Meta Divider', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-modal-popup']          = [
			'conditions' => [ $this->type => 'pp-modal-popup' ],
			'fields'     => [
				[
					'field'       => 'title',
					'type'        => __( 'Popup Box - Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				'popup_link' => [
					'field'       => 'url',
					'type'        => __( 'Popup Box - URL', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'content',
					'type'        => __( 'Popup Box - Content', 'powerpack' ),
					'editor_type' => 'VISUAL',
				],
				[
					'field'       => 'custom_html',
					'type'        => __( 'Popup Box - Custom HTML', 'powerpack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'button_text',
					'type'        => __( 'Popup Box - Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'element_identifier',
					'type'        => __( 'Popup Box - CSS Class or ID', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-offcanvas-content']    = [
			'conditions'        => [ $this->type => 'pp-offcanvas-content' ],
			'fields'            => [
				[
					'field'       => 'button_text',
					'type'        => __( 'Offcanvas Content - Toggle Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'burger_label',
					'type'        => __( 'Offcanvas Content - Burger Icon Label', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => 'WPML_PP_Offcanvas_Content',
		];
		$widgets['pp-ninja-forms']          = [
			'conditions' => [ $this->type => 'pp-ninja-forms' ],
			'fields'     => [
				[
					'field'       => 'form_title_custom',
					'type'        => __( 'Ninja Forms - Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'form_description_custom',
					'type'        => __( 'Ninja Forms - Description', 'powerpack' ),
					'editor_type' => 'AREA',
				],
			],
		];
		$widgets['pp-one-page-nav']         = [
			'conditions'        => [ $this->type => 'pp-one-page-nav' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_One_Page_Nav',
		];
		$widgets['pp-posts']                = [
			'conditions' => [ $this->type => 'pp-posts' ],
			'fields'     => [
				[
					'field'       => 'query_id',
					'type'        => __( 'Posts - Query Id', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'nothing_found_message',
					'type'        => __( 'Posts - Nothing Found Message', 'powerpack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'filter_all_label',
					'type'        => __( 'Posts - "All" Filter Label', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'search_form_input_placeholder',
					'type'        => __( 'Posts - Search Form Placeholder', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'search_form_button_text',
					'type'        => __( 'Posts - Search Form Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'post_terms_separator',
					'type'        => __( 'Posts - Terms Separator', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'post_meta_separator',
					'type'        => __( 'Posts - Post Meta Separator', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'author_prefix',
					'type'        => __( 'Posts - Author Prefix', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'date_custom_format',
					'type'        => __( 'Posts - Custom Format', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'date_meta_key',
					'type'        => __( 'Posts - Custom Meta Key', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'date_prefix',
					'type'        => __( 'Posts - Date Prefix', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_text',
					'type'        => __( 'Posts - Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'pagination_load_more_label',
					'type'        => __( 'Posts - Pagination Load More Label', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'pagination_prev_label',
					'type'        => __( 'Posts - Pagination Prev Label', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'pagination_next_label',
					'type'        => __( 'Posts - Pagination Next Label', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-price-menu']           = [
			'conditions'        => [ $this->type => 'pp-price-menu' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Price_Menu',
		];
		$widgets['pp-pricing-table']        = [
			'conditions'        => [ $this->type => 'pp-pricing-table' ],
			'fields'            => [
				[
					'field'       => 'table_title',
					'type'        => __( 'Pricing Table - Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'table_subtitle',
					'type'        => __( 'Pricing Table - Subtitle', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'table_price',
					'type'        => __( 'Pricing Table - Price', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'table_original_price',
					'type'        => __( 'Pricing Table - Origibal Price', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'table_duration',
					'type'        => __( 'Pricing Table - Duration', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'ribbon_title',
					'type'        => __( 'Pricing Table - Ribbon Title', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'table_button_text',
					'type'        => __( 'Pricing Table - Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Pricing Table - Link', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'table_additional_info',
					'type'        => __( 'Pricing Table - Additional Info', 'powerpack' ),
					'editor_type' => 'AREA',
				],
			],
			'integration-class' => 'WPML_PP_Pricing_Table',
		];
		$widgets['pp-promo-box']            = [
			'conditions' => [ $this->type => 'pp-promo-box' ],
			'fields'     => [
				[
					'field'       => 'heading',
					'type'        => __( 'Promo Box - Heading', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'sub_heading',
					'type'        => __( 'Promo Box - Sub Heading', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'content',
					'type'        => __( 'Promo Box - Description', 'powerpack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'button_text',
					'type'        => __( 'Promo Box - Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Promo Box - link', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-wpforms']              = [
			'conditions' => [ $this->type => 'pp-wpforms' ],
			'fields'     => [
				[
					'field'       => 'form_title_custom',
					'type'        => __( 'WPForms - Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'form_description_custom',
					'type'        => __( 'WPForms - Description', 'powerpack' ),
					'editor_type' => 'AREA',
				],
			],
		];
		$widgets['pp-recipe']               = [
			'conditions'        => [ $this->type => 'pp-recipe' ],
			'fields'            => [
				[
					'field'       => 'recipe_name',
					'type'        => __( 'Recipe - Name', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'recipe_description',
					'type'        => __( 'Recipe - Description', 'powerpack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'prep_time',
					'type'        => __( 'Recipe - Prep Time', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'cook_time',
					'type'        => __( 'Recipe - Cook Time', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'total_time',
					'type'        => __( 'Recipe - Total Time', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'servings',
					'type'        => __( 'Recipe - Servings', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'calories',
					'type'        => __( 'Recipe - Calories', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'item_notes',
					'type'        => __( 'Recipe - Item Notes', 'powerpack' ),
					'editor_type' => 'VISUAL',
				],
			],
			'integration-class' => 'WPML_PP_Recipe',
		];
		$widgets['pp-review-box']           = [
			'conditions'        => [ $this->type => 'pp-review-box' ],
			'fields'            => [
				[
					'field'       => 'box_title',
					'type'        => __( 'Review Box - Review Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'review_description',
					'type'        => __( 'Review Box - Review Description', 'powerpack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'final_rating_title',
					'type'        => __( 'Review Box - Final Rating Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'pros_title',
					'type'        => __( 'Review Box - Pros Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'cons_title',
					'type'        => __( 'Review Box - Cons Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'summary_title',
					'type'        => __( 'Review Box - Summary Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'summary_text',
					'type'        => __( 'Review Box - Summary Text', 'powerpack' ),
					'editor_type' => 'AREA',
				],
			],
			'integration-class' => 'WPML_PP_Review_Box',
		];
		$widgets['pp-scroll-image']         = [
			'conditions' => [ $this->type => 'pp-scroll-image' ],
			'fields'     => [
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Scroll Image - URL', 'powerpack' ),
					'editor_type' => 'LINK',
				],
			],
		];
		$widgets['pp-showcase']             = [
			'conditions'        => [ $this->type => 'pp-showcase' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Showcase',
		];
		$widgets['pp-tabbed-gallery']       = [
			'conditions'        => [ $this->type => 'pp-tabbed-gallery' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Tabbed_Gallery',
		];
		$widgets['pp-team-member']          = [
			'conditions'        => [ $this->type => 'pp-team-member' ],
			'fields'            => [
				[
					'field'       => 'team_member_name',
					'type'        => __( 'Team Member - Name', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'team_member_position',
					'type'        => __( 'Team Member - Position', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'team_member_description',
					'type'        => __( 'Team Member - Description', 'powerpack' ),
					'editor_type' => 'VISUAL',
				],
				'link' => [
					'field'       => 'url',
					'type'        => __( 'Team Member - URL', 'powerpack' ),
					'editor_type' => 'LINK',
				],
			],
			'integration-class' => 'WPML_PP_Team_Member',
		];
		$widgets['pp-team-member-carousel'] = [
			'conditions'        => [ $this->type => 'pp-team-member-carousel' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Team_Member_Carousel',
		];
		$widgets['pp-testimonials']         = [
			'conditions'        => [ $this->type => 'pp-testimonials' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Testimonials',
		];
		$widgets['pp-tiled-posts']          = [
			'conditions' => [ $this->type => 'pp-tiled-posts' ],
			'fields'     => [
				[
					'field'       => 'post_meta_divider',
					'type'        => __( 'Tiled Posts - Post Meta Divider', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-timeline']             = [
			'conditions'        => [ $this->type => 'pp-timeline' ],
			'fields'            => [
				[
					'field'       => 'button_text',
					'type'        => __( 'Timeline - Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => 'WPML_PP_Timeline',
		];
		$widgets['pp-toggle']               = [
			'conditions' => [ $this->type => 'pp-toggle' ],
			'fields'     => [
				[
					'field'       => 'primary_label',
					'type'        => __( 'Toggle - Primary Label', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'primary_content',
					'type'        => __( 'Toggle - Primary Content', 'powerpack' ),
					'editor_type' => 'VISUAL',
				],
				[
					'field'       => 'secondary_label',
					'type'        => __( 'Toggle - Secondary Label', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'secondary_content',
					'type'        => __( 'Toggle - Secondary Content', 'powerpack' ),
					'editor_type' => 'VISUAL',
				],
			],
		];
		$widgets['pp-twitter-buttons']      = [
			'conditions' => [ $this->type => 'pp-twitter-buttons' ],
			'fields'     => [
				[
					'field'       => 'profile',
					'type'        => __( 'Twitter Button - Profile URL or Username', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'recipient_id',
					'type'        => __( 'Twitter Button - Recipient Id', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'default_text',
					'type'        => __( 'Twitter Button - Default Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'hashtag_url',
					'type'        => __( 'Twitter Button - Hashtag URL or #hashtag', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'via',
					'type'        => __( 'Twitter Button - Via (twitter handler)', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'share_text',
					'type'        => __( 'Twitter Button - Custom Share Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'share_url',
					'type'        => __( 'Twitter Button - Custom Share URL', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-twitter-grid']         = [
			'conditions' => [ $this->type => 'pp-twitter-grid' ],
			'fields'     => [
				[
					'field'       => 'url',
					'type'        => __( 'Twitter Grid - Collection URL', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'tweet_limit',
					'type'        => __( 'Twitter Grid - Tweet Limit', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-twitter-timeline']     = [
			'conditions' => [ $this->type => 'pp-twitter-timeline' ],
			'fields'     => [
				[
					'field'       => 'username',
					'type'        => __( 'Twitter Timeline - Username', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'tweet_limit',
					'type'        => __( 'Twitter Timeline - Tweet Limit', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-twitter-tweet']        = [
			'conditions' => [ $this->type => 'pp-twitter-tweet' ],
			'fields'     => [
				[
					'field'       => 'tweet_url',
					'type'        => __( 'Twitter Tweet - Tweet URL', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-video']                = [
			'conditions' => [ $this->type => 'pp-video' ],
			'fields'     => [
				[
					'field'       => 'youtube_url',
					'type'        => __( 'Video - YouTube URL', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'vimeo_url',
					'type'        => __( 'Video - Vimeo URL', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'dailymotion_url',
					'type'        => __( 'Video - Dailymotion URL', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'start_time',
					'type'        => __( 'Video - Start Time', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'end_time',
					'type'        => __( 'Video - End Time', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-video-gallery']        = [
			'conditions'        => [ $this->type => 'pp-video-gallery' ],
			'fields'            => [
				[
					'field'       => 'filter_all_label',
					'type'        => __( 'Video Gallery - "All" Filter Label', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => 'WPML_PP_Video_Gallery',
		];
		$widgets['pp-woo-add-to-cart']      = [
			'conditions' => [ $this->type => 'pp-woo-add-to-cart' ],
			'fields'     => [
				[
					'field'       => 'btn_text',
					'type'        => __( 'Woo Add To Cart - Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-woo-offcanvas-cart']   = [
			'conditions' => [ $this->type => 'pp-woo-offcanvas-cart' ],
			'fields'     => [
				[
					'field'       => 'cart_text',
					'type'        => __( 'Woo Off Canvas Cart - Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'cart_title',
					'type'        => __( 'Woo Off Canvas Cart - Cart Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'cart_message',
					'type'        => __( 'Woo Off Canvas Cart - Cart Message', 'powerpack' ),
					'editor_type' => 'AREA',
				],
			],
		];
		$widgets['pp-woo-mini-cart']        = [
			'conditions' => [ $this->type => 'pp-woo-mini-cart' ],
			'fields'     => [
				[
					'field'       => 'cart_text',
					'type'        => __( 'Woo Mini Cart - Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'cart_title',
					'type'        => __( 'Woo Mini Cart - Cart Title', 'powerpack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'cart_message',
					'type'        => __( 'Woo Mini Cart - Cart Message', 'powerpack' ),
					'editor_type' => 'AREA',
				],
			],
		];
		$widgets['pp-woo-products']         = [
			'conditions' => [ $this->type => 'pp-woo-products' ],
			'fields'     => [
				[
					'field'       => 'sale_badge_custom_text',
					'type'        => __( 'Woo Products - Sale Badge Custom Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];
		$widgets['pp-table']                = [
			'conditions'        => [ $this->type => 'pp-table' ],
			'fields'            => [],
			'integration-class' => 'WPML_PP_Table',
		];
		$widgets[ 'pp-categories' ]               = [
			'conditions' => [ $this->type => 'pp-categories' ],
			'fields'     => [
				[
					'field'       => 'count_text_singular',
					'type'        => __( 'Categories - Count Text (Singular)', 'powerpack' ),
					'editor_type' => 'LINE'
				],
				[
					'field'       => 'count_text_plural',
					'type'        => __( 'Categories - Count Text (Plural)', 'powerpack' ),
					'editor_type' => 'LINE'
				],
			],
		];
		$widgets['pp-woo-add-to-cart']         = [
			'conditions' => [ $this->type => 'pp-woo-add-to-cart' ],
			'fields'     => [
				[
					'field'       => 'btn_text',
					'type'        => __( 'Woo Add to Cart - Button Text', 'powerpack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$this->init_classes();

		return $widgets;
	}

	private function init_classes() {
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-advanced-accordion.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-advanced-tabs.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-business-hours.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-buttons.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-card-slider.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-content-ticker.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-coupons.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-faq.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-google-maps.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-how-to.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-icon-list.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-info-box-carousel.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-info-list.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-image-accordion.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-image-gallery.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-image-hotspots.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-logo-carousel.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-logo-grid.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-offcanvas-content.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-one-page-nav.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-price-menu.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-pricing-table.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-recipe.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-review-box.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-showcase.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-tabbed-gallery.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-team-member.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-team-member-carousel.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-testimonials.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-timeline.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-video-gallery.php';
		require_once POWERPACK_ELEMENTS_PATH . 'classes/wpml/class-wpml-pp-table.php';
	}
}

$pp_elements_wpml = new PP_Elements_WPML();
