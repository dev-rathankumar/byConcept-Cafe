<?php
namespace PowerpackElements\Modules\GoogleMaps\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Google Maps Widget
 */
class Google_Maps extends Powerpack_Widget {
	
	/**
	 * Retrieve link effects widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Google_Maps' );
	}

	/**
	 * Retrieve link effects widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Google_Maps' );
	}

	/**
	 * Retrieve the list of categories the link effects widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return parent::get_widget_categories( 'Google_Maps' );
	}

	/**
	 * Retrieve link effects widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Google_Maps' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Google_Maps' );
	}
	
	/**
	 * Retrieve the list of scripts the instagram feed widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [
			'powerpack-google-maps',
			'pp-google-maps',
			'powerpack-frontend',
		];
	}

	/**
	 * Register link effects widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() {
		
		/* Content Tab */
		$this->register_content_map_addresses_controls();
		$this->register_content_map_settings_controls();
		$this->register_content_map_controls_controls();
		$this->register_content_map_style_controls();
		$this->register_content_help_docs_controls();
		
		/* Style Tab */
		$this->register_style_map_controls();
		$this->register_style_info_window_controls();
	}

	protected function register_content_map_addresses_controls() {
		/**
         * Content Tab: Addresses
         */
		$this->start_controls_section(
			'section_map_addresses',
			[
				'label'                 => esc_html__( 'Addresses', 'powerpack' ),
			]
		);
		
		$repeater = new Repeater();

		$repeater->add_control(
			'map_latitude',
			[
				'label'           => esc_html__( 'Latitude', 'powerpack' ),
				'description'     => sprintf( '<a href="https://www.latlong.net/" target="_blank">%1$s</a> %2$s',__( 'Click here', 'powerpack' ), __( 'to find Latitude and Longitude of your location', 'powerpack' ) ),
				'type'            => Controls_Manager::TEXT,
				'label_block'     => true,
				'dynamic'         => [
					'active'   => true,
				],
			]
		);

		$repeater->add_control(
			'map_longitude',
			[
				'label'           => esc_html__( 'Longitude', 'powerpack' ),
				'description'     => sprintf( '<a href="https://www.latlong.net/" target="_blank">%1$s</a> %2$s',__( 'Click here', 'powerpack' ), __( 'to find Latitude and Longitude of your location', 'powerpack' ) ),
				'type'            => Controls_Manager::TEXT,
				'label_block'     => true,
				'dynamic'         => [
					'active'   => true,
				],
			]
		);

		$repeater->add_control(
			'map_title',
			[
				'label'           => esc_html__( 'Address Title', 'powerpack' ),
				'type'            => Controls_Manager::TEXT,
				'label_block'     => true,
				'dynamic'         => [
					'active'   => true,
				],
			]
		);

		$repeater->add_control(
			'map_marker_infowindow',
			[
				'label'           => esc_html__( 'Info Window', 'powerpack' ),
				'type'            => Controls_Manager::SWITCHER,
				'default'         => 'no',
				'label_on'        => __( 'On', 'powerpack' ),
				'label_off'       => __( 'Off', 'powerpack' ),
				'return_value'    => 'yes',
			]
		);

		$repeater->add_control(
			'map_info_window_open',
			[
				'label'           => esc_html__( 'Open Info Window on Load', 'powerpack' ),
				'type'            => Controls_Manager::SWITCHER,
				'default'         => 'yes',
				'condition'      => [
					'map_marker_infowindow' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'map_description',
			[
				'label'           => esc_html__( 'Address Description', 'powerpack' ),
				'type'            => Controls_Manager::TEXTAREA,
				'label_block'     => true,
				'condition'      => [
					'map_marker_infowindow' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'map_marker_icon_type',
			[
				'label'           => esc_html__( 'Marker Icon', 'powerpack' ),
				'type'            => Controls_Manager::SELECT,
				'default'         => 'default',
				'options'         => [
					'default'     => esc_html__( 'Default', 'powerpack' ),
					'custom'      => esc_html__( 'Custom', 'powerpack' ),
				],
			]
		);

		$repeater->add_control(
			'map_marker_icon',
			[
				'label'           => esc_html__( 'Custom Marker Icon', 'powerpack' ),
				'type'            => Controls_Manager::MEDIA,
				'condition'      => [
					'map_marker_icon_type' => 'custom',
				],
			]
		);

		$repeater->add_control(
			'map_custom_marker_size',
			[
				'label'           => esc_html__( 'Size', 'powerpack' ),
				'type'            => Controls_Manager::NUMBER,
				'default'         => 30,
				'min'             => 5,
				'max'             => 100,
				'step'            => 1,
				'condition'      => [
					'map_marker_icon_type' => 'custom',
				],
			]
		);
		
		$this->add_control(
			'pp_map_addresses',
			[
				'label'                 => '',
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[
						'map_latitude'    => 24.553311,
						'map_longitude'   => 73.694076,
						'map_title'       => esc_html__( 'IdeaBox Creations', 'powerpack' ),
						'map_description' => esc_html__( 'Add description to your map pins', 'powerpack' ),
					],
				],
				'fields' => $repeater->get_controls(),
				'title_field' => '<i class="fa fa-map-marker"></i> {{{ map_title }}}',
			]
		);

		$this->end_controls_section();
	}

	protected function register_content_map_settings_controls() {
		/**
         * Content Tab: Settings
         */
		$this->start_controls_section(
			'section_map_settings',
			[
				'label'                 => esc_html__( 'Settings', 'powerpack' )
			]
		);

		$this->add_control(
			'zoom_type',
			[
				'label'              => esc_html__( 'Zoom Type', 'powerpack' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'custom',
				'options'            => [
					'auto'   => esc_html__( 'Auto', 'powerpack' ),
					'custom' => esc_html__( 'Custom', 'powerpack' ),
				],
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'map_zoom',
			[
				'label'     => esc_html__( 'Map Zoom', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 12,
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 22,
					],
				],
				'condition' => [
					'zoom_type' => 'custom',
				],
			]
		);
		
		$this->add_control(
			'map_type',
			[
				'label'                 => esc_html__( 'Map Type', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'roadmap',
				'options'               => [
					'roadmap'      => esc_html__( 'Road Map', 'powerpack' ),
					'satellite'    => esc_html__( 'Satellite', 'powerpack' ),
					'hybrid'       => esc_html__( 'Hybrid', 'powerpack' ),
					'terrain'      => esc_html__( 'Terrain', 'powerpack' ),
				],
				'frontend_available'    => true,
			]
		);
		
		$this->add_control(
			'marker_animation',
			[
				'label'                 => esc_html__( 'Marker Animation', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => '',
				'options'               => [
					''         => esc_html__( 'None', 'powerpack' ),
					'drop'     => esc_html__( 'Drop', 'powerpack' ),
					'bounce'   => esc_html__( 'Bounce', 'powerpack' ),
				],
				'frontend_available'    => true,
			]
		);
		
		$this->end_controls_section();
	}

	protected function register_content_map_controls_controls() {
		/**
         * Content Tab: Controls
         */
		$this->start_controls_section(
			'section_map_controls',
			[
				'label'                 => esc_html__( 'Controls', 'powerpack' )
			]
		);
		
		$this->add_control(
			'map_option_streeview',
			[
				'label'                 => esc_html__( 'Street View Controls', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'On', 'powerpack' ),
				'label_off'             => __( 'Off', 'powerpack' ),
				'return_value'          => 'yes',
				'frontend_available'    => true,
			]
		);

		$this->add_control(
			'map_type_control',
			[
				'label'                 => esc_html__( 'Map Type Control', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'On', 'powerpack' ),
				'label_off'             => __( 'Off', 'powerpack' ),
				'return_value'          => 'yes',
				'frontend_available'    => true,
			]
		);

		$this->add_control(
			'zoom_control',
			[
				'label'                 => esc_html__( 'Zoom Control', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'On', 'powerpack' ),
				'label_off'             => __( 'Off', 'powerpack' ),
				'return_value'          => 'yes',
				'frontend_available'    => true,
			]
		);

		$this->add_control(
			'fullscreen_control',
			[
				'label'                 => esc_html__( 'Fullscreen Control', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'On', 'powerpack' ),
				'label_off'             => __( 'Off', 'powerpack' ),
				'return_value'          => 'yes',
				'frontend_available'    => true,
			]
		);
		
		$this->add_control(
			'map_scroll_zoom',
			[
				'label'                 => esc_html__( 'Scroll Wheel Zoom', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'On', 'powerpack' ),
				'label_off'             => __( 'Off', 'powerpack' ),
				'return_value'          => 'yes',
				'frontend_available'    => true,
			]
		);
		
		$this->end_controls_section();
	}

	protected function register_content_map_style_controls() {
		/**
         * Content Tab: Map Style
         */
		$this->start_controls_section(
			'section_map_theme',
			[
				'label'                 => esc_html__( 'Map Style', 'powerpack' ),
			]
		);
		
		$this->add_control(
			'map_theme',
			[
				'label'                 => esc_html__( 'Map Theme', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'standard',
				'options'               => [
					'standard'     => __( 'Standard', 'powerpack' ),
					'silver'       => __( 'Silver', 'powerpack' ),
					'retro'        => __( 'Retro', 'powerpack' ),
					'dark'         => __( 'Dark', 'powerpack' ),
					'night'        => __( 'Night', 'powerpack' ),
					'aubergine'    => __( 'Aubergine', 'powerpack' ),
					'custom'       => __( 'Custom', 'powerpack' ),
				],
			]
		);
		
		$this->add_control(
			'map_custom_style',
			[
				'label'                 => __( 'Custom Style', 'powerpack' ),
				'description'           => sprintf( __( 'Get JSON style code from <a href="%1$s" target="_blank">Snazzy Maps</a> or <a href="%2$s" target="_blank">Map Style</a> to style your map', 'powerpack' ), 'https://snazzymaps.com/', 'https://mapstyle.withgoogle.com/' ),
				'type'                  => Controls_Manager::TEXTAREA,
				'condition'             => [
					'map_theme'     => 'custom',
				],
			]
		);
		
		$this->end_controls_section();
	}

	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links('Google_Maps');

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
		
	/*-----------------------------------------------------------------------------------*/
	/*	STYLE TAB
	/*-----------------------------------------------------------------------------------*/
		
	protected function register_style_map_controls() {
		/**
		 * Style Tab: Map
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_map_style',
			[
				'label'                 => esc_html__( 'Map', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE
			]
		);
		
		$this->add_responsive_control(
			'map_align',
			[
				'label'                 => __( 'Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'        => [
						'title'   => __( 'Left', 'powerpack' ),
						'icon'    => 'eicon-h-align-left',
					],
					'center'      => [
						'title'   => __( 'Center', 'powerpack' ),
						'icon'    => 'eicon-h-align-center',
					],
					'right'       => [
						'title'   => __( 'Right', 'powerpack' ),
						'icon'    => 'eicon-h-align-right',
					],
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-google-map-container' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'map_width',
			[
				'label'                 => esc_html__( 'Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%' ],
				'default'               => [
					'size' => 100,
					'unit' => '%',
				],
				'range'                 => [
					'px' => [
						'min' => 100,
						'max' => 1920,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-map-height' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'map_height',
			[
				'label'                 => esc_html__( 'Height', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px' ],
				'default'               => [
					'size' => 500,
					'unit' => 'px',
				],
				'range'                 => [
					'px' => [
						'min' => 80,
						'max' => 1200,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-map-height' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->end_controls_section();
	}
	
	protected function register_style_info_window_controls() {
		/**
		 * Style Tab: Info Window
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_info_window_style',
			[
				'label'                 => esc_html__( 'Info Window', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE
			]
		);
		
		$this->add_control(
			'iw_max_width',
			[
				'label'                 => __( 'Info Window Max Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => 240,
				],
				'range'                 => [
					'px'        => [
						'min'   => 40,
						'max'   => 500,
						'step'  => 1,
					],
				],
			]
		);
		
		$this->add_responsive_control(
			'info_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .gm-style .pp-infowindow-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'title_heading',
			[
				'label'                 => __( 'Title', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'                 => esc_html__( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .gm-style .pp-infowindow-title' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'title_spacing',
			[
				'label'                 => __( 'Bottom Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .gm-style .pp-infowindow-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'title_typography',
				'scheme'                => Scheme_Typography::TYPOGRAPHY_1,
				'selector'              => '{{WRAPPER}} .gm-style .pp-infowindow-title',
			]
		);
		
		$this->add_control(
			'description_heading',
			[
				'label'                 => __( 'Description', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'                 => esc_html__( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .gm-style .pp-infowindow-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'description_typography',
				'selector'              => '{{WRAPPER}} .gm-style .pp-infowindow-description',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Get map styles.
	 *
	 * @access protected
	 */
	protected function get_map_styles() {
		$pp_map_themes = array(
			'silver' => '[{"elementType":"geometry","stylers":[{"color":"#f5f5f5"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#f5f5f5"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#bdbdbd"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#eeeeee"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#ffffff"}]},{"featureType":"road.arterial","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#dadada"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#eeeeee"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#c9c9c9"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]}]',
			'retro' => '[{"elementType":"geometry","stylers":[{"color":"#ebe3cd"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#523735"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#f5f1e6"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#c9b2a6"}]},{"featureType":"administrative.land_parcel","elementType":"geometry.stroke","stylers":[{"color":"#dcd2be"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#ae9e90"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#93817c"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#a5b076"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#447530"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#f5f1e6"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#fdfcf8"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#f8c967"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#e9bc62"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry","stylers":[{"color":"#e98d58"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry.stroke","stylers":[{"color":"#db8555"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#806b63"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"transit.line","elementType":"labels.text.fill","stylers":[{"color":"#8f7d77"}]},{"featureType":"transit.line","elementType":"labels.text.stroke","stylers":[{"color":"#ebe3cd"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#b9d3c2"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#92998d"}]}]',
			'dark' => '[{"elementType":"geometry","stylers":[{"color":"#212121"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#212121"}]},{"featureType":"administrative","elementType":"geometry","stylers":[{"color":"#757575"}]},{"featureType":"administrative.country","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"administrative.land_parcel","stylers":[{"visibility":"off"}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"color":"#bdbdbd"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#181818"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"poi.park","elementType":"labels.text.stroke","stylers":[{"color":"#1b1b1b"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#2c2c2c"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#8a8a8a"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#373737"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#3c3c3c"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry","stylers":[{"color":"#4e4e4e"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"transit","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#3d3d3d"}]}]',
			'night' => '[{"elementType":"geometry","stylers":[{"color":"#242f3e"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#746855"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#242f3e"}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"color":"#d59563"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#d59563"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#263c3f"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#6b9a76"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#38414e"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"color":"#212a37"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#9ca5b3"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#746855"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#1f2835"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#f3d19c"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#2f3948"}]},{"featureType":"transit.station","elementType":"labels.text.fill","stylers":[{"color":"#d59563"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#17263c"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#515c6d"}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"color":"#17263c"}]}]',
			'aubergine' => '[{"elementType":"geometry","stylers":[{"color":"#1d2c4d"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#8ec3b9"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#1a3646"}]},{"featureType":"administrative.country","elementType":"geometry.stroke","stylers":[{"color":"#4b6878"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#64779e"}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"color":"#4b6878"}]},{"featureType":"landscape.man_made","elementType":"geometry.stroke","stylers":[{"color":"#334e87"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#023e58"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#283d6a"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#6f9ba5"}]},{"featureType":"poi","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#023e58"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#3C7680"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#304a7d"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#98a5be"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#2c6675"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#255763"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#b0d5ce"}]},{"featureType":"road.highway","elementType":"labels.text.stroke","stylers":[{"color":"#023e58"}]},{"featureType":"transit","elementType":"labels.text.fill","stylers":[{"color":"#98a5be"}]},{"featureType":"transit","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"transit.line","elementType":"geometry.fill","stylers":[{"color":"#283d6a"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#3a4762"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#0e1626"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#4e6d70"}]}]',
		);
		
		return $pp_map_themes;
	}

	/**
	 * Render google maps widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();
		
		$map_styles = $this->get_map_styles();

		$i = 1;
		
		$this->add_render_attribute( 'google-map',
			[
				'id'            => 'pp-google-map-' . esc_attr( $this->get_id() ),
				'class'         => [ 'pp-google-map', 'pp-map-height' ],
			]
		);

		if ( 'custom' === $settings['zoom_type'] ) {
			if ( ! empty( $settings['map_zoom']['size'] ) ) {
				$this->add_render_attribute( 'google-map', 'data-zoom', $settings['map_zoom']['size'] );
				$this->add_render_attribute( 'google-map', 'data-zoomtype', $settings['zoom_type'] );
			}
		} else {
			$this->add_render_attribute( 'google-map', 'data-zoomType', $settings['zoom_type'] );
		}

		if ( $settings['iw_max_width']['size'] ) {
			$this->add_render_attribute( 'google-map', 'data-iw-max-width', $settings['iw_max_width']['size'] );
		}

		if ( $settings['map_theme'] != 'standard' ) {
			if ( $settings['map_theme'] != 'custom' ) {
				$this->add_render_attribute( 'google-map', 'data-custom-style', $map_styles[$settings['map_theme']] );
			} else if ( ! empty( $settings['map_custom_style'] ) ) {
				$this->add_render_attribute( 'google-map', 'data-custom-style', $settings['map_custom_style'] );
			}
		}
		
		$pp_map_locations = array();
		
		foreach ( $settings['pp_map_addresses'] as $index => $item ) {
			
			$pp_map_location = array(
				$item['map_latitude'],
				$item['map_longitude']
			);
			
			if ( $item['map_marker_infowindow'] == 'yes' ) {
				$pp_map_location[] = 'yes';
			} else {
				$pp_map_location[] = '';
			}
			
			$pp_map_location[] = $item['map_title'];
			$pp_map_location[] = $item['map_description'];
			
			if ( $item['map_marker_icon_type'] == 'custom' && $item['map_marker_icon']['url'] != '' ) {
				$pp_map_location[] = 'custom';
				$pp_map_location[] = $item['map_marker_icon']['url'];
				$pp_map_location[] = $item['map_custom_marker_size'];
			} else {
				$pp_map_location[] = '';
				$pp_map_location[] = '';
				$pp_map_location[] = '';
			}
			
			if ( $item['map_info_window_open'] == 'yes' ) {
				$pp_map_location[] = 'iw_open';
			} else {
				$pp_map_location[] = '';
			}
			
			$pp_map_locations[] = $pp_map_location;
		}
		
		$this->add_render_attribute( 'google-map', 'data-locations', wp_json_encode( $pp_map_locations ) );
	?>
	<div class="pp-google-map-container">
		<div <?php echo $this->get_render_attribute_string( 'google-map' ); ?>></div>
	</div>
	<?php
	}

	protected function _content_template() {
		?>
		<#
			function get_map_styles() {
				return {
					'silver': '[{"elementType":"geometry","stylers":[{"color":"#f5f5f5"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#f5f5f5"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#bdbdbd"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#eeeeee"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#ffffff"}]},{"featureType":"road.arterial","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#dadada"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#eeeeee"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#c9c9c9"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]}]',
					'retro': '[{"elementType":"geometry","stylers":[{"color":"#ebe3cd"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#523735"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#f5f1e6"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#c9b2a6"}]},{"featureType":"administrative.land_parcel","elementType":"geometry.stroke","stylers":[{"color":"#dcd2be"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#ae9e90"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#93817c"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#a5b076"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#447530"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#f5f1e6"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#fdfcf8"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#f8c967"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#e9bc62"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry","stylers":[{"color":"#e98d58"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry.stroke","stylers":[{"color":"#db8555"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#806b63"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"transit.line","elementType":"labels.text.fill","stylers":[{"color":"#8f7d77"}]},{"featureType":"transit.line","elementType":"labels.text.stroke","stylers":[{"color":"#ebe3cd"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#b9d3c2"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#92998d"}]}]',
					'dark': '[{"elementType":"geometry","stylers":[{"color":"#212121"}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#212121"}]},{"featureType":"administrative","elementType":"geometry","stylers":[{"color":"#757575"}]},{"featureType":"administrative.country","elementType":"labels.text.fill","stylers":[{"color":"#9e9e9e"}]},{"featureType":"administrative.land_parcel","stylers":[{"visibility":"off"}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"color":"#bdbdbd"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#181818"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"poi.park","elementType":"labels.text.stroke","stylers":[{"color":"#1b1b1b"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#2c2c2c"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#8a8a8a"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#373737"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#3c3c3c"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry","stylers":[{"color":"#4e4e4e"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#616161"}]},{"featureType":"transit","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#3d3d3d"}]}]',
					'night': '[{"elementType":"geometry","stylers":[{"color":"#242f3e"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#746855"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#242f3e"}]},{"featureType":"administrative.locality","elementType":"labels.text.fill","stylers":[{"color":"#d59563"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#d59563"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#263c3f"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#6b9a76"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#38414e"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"color":"#212a37"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#9ca5b3"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#746855"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#1f2835"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#f3d19c"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#2f3948"}]},{"featureType":"transit.station","elementType":"labels.text.fill","stylers":[{"color":"#d59563"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#17263c"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#515c6d"}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"color":"#17263c"}]}]',
					'aubergine': '[{"elementType":"geometry","stylers":[{"color":"#1d2c4d"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#8ec3b9"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#1a3646"}]},{"featureType":"administrative.country","elementType":"geometry.stroke","stylers":[{"color":"#4b6878"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#64779e"}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"color":"#4b6878"}]},{"featureType":"landscape.man_made","elementType":"geometry.stroke","stylers":[{"color":"#334e87"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#023e58"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#283d6a"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#6f9ba5"}]},{"featureType":"poi","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#023e58"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#3C7680"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#304a7d"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#98a5be"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#2c6675"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#255763"}]},{"featureType":"road.highway","elementType":"labels.text.fill","stylers":[{"color":"#b0d5ce"}]},{"featureType":"road.highway","elementType":"labels.text.stroke","stylers":[{"color":"#023e58"}]},{"featureType":"transit","elementType":"labels.text.fill","stylers":[{"color":"#98a5be"}]},{"featureType":"transit","elementType":"labels.text.stroke","stylers":[{"color":"#1d2c4d"}]},{"featureType":"transit.line","elementType":"geometry.fill","stylers":[{"color":"#283d6a"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#3a4762"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#0e1626"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#4e6d70"}]}]',
				};
			}
		
			var map_styles = get_map_styles();

			view.addRenderAttribute(
				'google-map',
				{
					'class' : [ 'pp-google-map', 'pp-map-height' ],
				}
			);
			if ( 'custom' === settings.zoom_type ) {
				if ( settings.map_zoom.size != '' ) {
					view.addRenderAttribute( 'google-map', { 'data-zoom' : settings.map_zoom.size } );
				}
			} else {
					view.addRenderAttribute( 'google-map', { 'data-zoomType' : settings.zoom_type } );
			}
		
			if ( settings.iw_max_width.size != '' ) {
				view.addRenderAttribute( 'google-map', { 'data-iw-max-width' : settings.iw_max_width.size } );
			}

			if ( settings.map_theme != 'standard' ) {
				if ( settings.map_theme != 'custom' ) {
					view.addRenderAttribute( 'google-map', { 'data-custom-style' : map_styles[settings.map_theme] } );
				} else if ( settings.map_custom_style ) {
					view.addRenderAttribute( 'google-map', { 'data-custom-style' : settings.map_custom_style } );
				}
			}
		
			var pp_map_locations = [];

			_.each( settings.pp_map_addresses, function( item ) {

				var pp_map_location = [ item.map_latitude, item.map_longitude ];
		
				if ( item.map_marker_infowindow == 'yes' ) {
					pp_map_location.push( 'yes' );
				} else {
					pp_map_location.push( "" );
				}

				pp_map_location.push( item.map_title );
				pp_map_location.push( item.map_description );

				if ( item.map_marker_icon_type == 'custom' && item.map_marker_icon.url != '' ) {
					pp_map_location.push( 'custom' );
					pp_map_location.push( item.map_marker_icon.url );
					pp_map_location.push( item.map_custom_marker_size );
				} else {
					pp_map_location.push( "" );
					pp_map_location.push( "" );
					pp_map_location.push( "" );
				}

				if ( item.map_info_window_open == 'yes' ) {
					pp_map_location.push( 'iw_open' );
				} else {
					pp_map_location.push( "" );
				}

				pp_map_locations.push( pp_map_location );

			});
		
			view.addRenderAttribute( 'google-map', { 'data-locations' : JSON.stringify( pp_map_locations ) } );
		#>
		<div class="pp-google-map-container">
			<div {{{ view.getRenderAttributeString( 'google-map' ) }}}></div>
		</div>
		<?php
	}

}