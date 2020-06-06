<?php
namespace PowerpackElements\Modules\Video\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Modules\Video\Module;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Scheme_Typography;
use Elementor\Embed;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Video Gallery Widget
 */
class Video_Gallery extends Powerpack_Widget {
    
    /**
	 * Retrieve video gallery widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return parent::get_widget_name( 'Video_Gallery' );
    }

    /**
	 * Retrieve video gallery widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
    public function get_title() {
        return parent::get_widget_title( 'Video_Gallery' );
    }

    /**
	 * Retrieve the list of categories the video gallery widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Video_Gallery' );
    }

    /**
	 * Retrieve video gallery widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
    public function get_icon() {
        return parent::get_widget_icon( 'Video_Gallery' );
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
		return parent::get_widget_keywords( 'Video_Gallery' );
	}
    
    /**
	 * Retrieve the list of scripts the video gallery widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_script_depends() {
        return [
            'jquery-fancybox',
            'isotope',
			'jquery-slick',
            'jquery-resize',
            'imagesloaded',
            'powerpack-frontend'
        ];
    }
    
    /**
	 * Retrieve the list of styles the video gallery widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_style_depends() {
        return [
            'fancybox',
        ];
    }

    /**
	 * Register video gallery widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
    protected function _register_controls() {

        /*-----------------------------------------------------------------------------------*/
        /*	CONTENT TAB
        /*-----------------------------------------------------------------------------------*/
        
        /**
         * Content Tab: Gallery
         */
        $this->start_controls_section(
            'section_gallery',
            [
                'label'                 => __( 'Gallery', 'powerpack' ),
            ]
        );
        
        $repeater = new Repeater();

		$repeater->add_control(
			'video_source',
			[
				'label'                 => __( 'Source', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'youtube',
				'options'               => [
					'youtube'      => __( 'YouTube', 'powerpack' ),
					'vimeo'        => __( 'Vimeo', 'powerpack' ),
					'dailymotion'  => __( 'Dailymotion', 'powerpack' ),
				],
			]
		);

		$repeater->add_control(
			'youtube_url',
			[
				'label'                 => __( 'URL', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'       => true,
					'categories'   => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder'           => __( 'Enter your YouTube URL', 'powerpack' ),
				'default'               => 'https://www.youtube.com/watch?v=9uOETcuFjbE',
				'label_block'           => true,
				'condition'             => [
					'video_source' => 'youtube',
				],
			]
		);

		$repeater->add_control(
			'vimeo_url',
			[
				'label'                 => __( 'URL', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'       => true,
					'categories'   => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder'           => __( 'Enter your Vimeo URL', 'powerpack' ),
				'default'               => 'https://vimeo.com/235215203',
				'label_block'           => true,
				'condition'             => [
					'video_source' => 'vimeo',
				],
			]
		);

		$repeater->add_control(
			'dailymotion_url',
			[
				'label'                 => __( 'URL', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'       => true,
					'categories'   => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'placeholder'           => __( 'Enter your Dailymotion URL', 'powerpack' ),
				'default'               => 'https://www.dailymotion.com/video/x6tqhqb',
				'label_block'           => true,
				'condition'             => [
					'video_source' => 'dailymotion',
				],
			]
		);
        
        $repeater->add_control(
			'video_title',
			[
				'label'                 => __( 'Video Title', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'default'               => '',
				'placeholder'           => '',
                'dynamic'               => [
                    'active' => true
                ],
			]
		);
        
        $repeater->add_control(
			'filter_label',
			[
				'label'                 => __( 'Filter Label', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'default'               => '',
				'placeholder'           => '',
                'dynamic'               => [
                    'active' => true
                ],
			]
		);

		$repeater->add_control(
			'thumbnail_size',
			[
				'label'                 => __( 'Thumbnail Size', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'maxresdefault',
				'options'               => [
					'maxresdefault' => __( 'Maximum Resolution', 'powerpack' ),
                    'hqdefault'     => __( 'High Quality', 'powerpack' ),
                    'mqdefault'     => __( 'Medium Quality', 'powerpack' ),
                    'sddefault'     => __( 'Standard Quality', 'powerpack' ),
				],
                'conditions'            => [
                    'terms' => [
                        [
                            'name'      => 'video_source',
                            'operator'  => '==',
                            'value'     => 'youtube',
                        ],
                    ],
                ],
			]
		);

		$repeater->add_control(
			'custom_thumbnail',
			[
				'label'                 => __( 'Custom Thumbnail', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => '',
			]
		);
        
        $repeater->add_control(
			'custom_image',
            [
                'label'                 => __( 'Image', 'powerpack' ),
                'type'                  => Controls_Manager::MEDIA,
                'default'               => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'conditions'            => [
                    'terms' => [
                        [
                            'name'      => 'custom_thumbnail',
                            'operator'  => '==',
                            'value'     => 'yes',
                        ],
                    ],
                ],
            ]
		);
        
        $this->add_control(
            'gallery_videos',
            [
                'label'                 => '',
                'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[
						'video_source'    => 'youtube',
						'youtube_url'     => 'https://www.youtube.com/watch?v=9uOETcuFjbE',
					],
					[
						'video_source'    => 'vimeo',
						'vimeo_url'       => 'https://vimeo.com/235215203',
					],
					[
						'video_source'    => 'dailymotion',
						'dailymotion_url' => 'https://www.dailymotion.com/video/x6tqhqb',
					],
					[
						'video_source'    => 'vimeo',
						'vimeo_url'       => 'https://vimeo.com/235215203',
					],
					[
						'video_source'    => 'dailymotion',
						'dailymotion_url' => 'https://www.dailymotion.com/video/x6tqhqb',
					],
					[
						'video_source'    => 'youtube',
						'youtube_url'     => 'https://www.youtube.com/watch?v=9uOETcuFjbE',
					],
				],
                'fields'                => array_values( $repeater->get_controls() ),
                'title_field'           => '{{{ video_title }}}',
            ]
        );

        $this->end_controls_section();

        /**
         * Content Tab: Filter
         */
        $this->start_controls_section(
            'section_filter',
            [
                'label'                 => __( 'Filter', 'powerpack' ),
                'condition'             => [
					'layout'   => 'grid',
                ]
            ]
        );

		$this->add_control(
			'filter_enable',
			[
				'label'                 => __( 'Enable Filter', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => '',
                'condition'             => [
					'layout'   => 'grid',
                ]
			]
		);

        $this->add_control(
            'filter_all_label',
            [
                'label'                 => __( '"All" Filter Label', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'default'               => __( 'All', 'powerpack' ),
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
            ]
        );
        
        $this->add_responsive_control(
			'filter_alignment',
			[
				'label'                 => __( 'Alignment', 'powerpack' ),
                'label_block'           => false,
				'type'                  => Controls_Manager::CHOOSE,
				'default'               => 'center',
				'options'               => [
					'left'          => [
						'title'     => __( 'Left', 'powerpack' ),
						'icon'      => 'eicon-h-align-left',
					],
					'center'        => [
						'title'     => __( 'Center', 'powerpack' ),
						'icon'      => 'eicon-h-align-center',
					],
					'right'         => [
						'title'     => __( 'Right', 'powerpack' ),
						'icon'      => 'eicon-h-align-right',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-gallery-filters'   => 'text-align: {{VALUE}};',
				],
				'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
				],
			]
		);
        
        $this->end_controls_section();

        /**
         * Content Tab: Play Icon
         */
        $this->start_controls_section(
            'section_play_icon_settings',
            [
                'label'                 => __( 'Play Icon', 'powerpack' ),
            ]
        );

        $this->add_control(
            'play_icon_type',
            [
                'label'                 => __( 'Icon Type', 'powerpack' ),
				'label_block'           => false,
				'toggle'                => false,
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
					'none'        => [
						'title'   => esc_html__( 'None', 'powerpack' ),
						'icon'    => 'fa fa-ban',
					],
                    'icon'  => [
                        'title' => __( 'Icon', 'powerpack' ),
                        'icon'  => 'fa fa-star',
                    ],
                    'image' => [
                        'title' => __( 'Image', 'powerpack' ),
                        'icon'  => 'fa fa-picture-o',
                    ],
                ],
                'default'               => 'icon',
            ]
        );
		
		$this->add_control(
			'select_play_icon',
			[
				'label'					=> __( 'Select Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'fa4compatibility'		=> 'play_icon',
				'default'				=> [
					'value'		=> 'fas fa-play-circle',
					'library'	=> 'fa-solid',
				],
				'recommended'			=> [
					'fa-regular' => [
						'play-circle',
					],
					'fa-solid' => [
						'play',
						'play-circle',
					],
				],
                'condition'             => [
                    'play_icon_type'	=> 'icon',
                ],
			]
		);

        $this->add_control(
            'play_icon_image',
            [
                'label'                 => __( 'Select Image', 'powerpack' ),
                'type'                  => Controls_Manager::MEDIA,
                'default'               => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition'             => [
                    'play_icon_type' => 'image',
                ],
            ]
        );
        
        $this->end_controls_section();

        /**
         * Content Tab: Gallery Settings
         */
        $this->start_controls_section(
            'section_settings',
            [
                'label'                 => __( 'Gallery Settings', 'powerpack' ),
            ]
        );

        $this->add_control(
            'layout',
            [
                'label'                 => __( 'Layout', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'grid',
                'options'               => [
                    'grid' 		=> __( 'Grid', 'powerpack' ),
                    'carousel' 	=> __( 'Carousel', 'powerpack' ),
                ],
                'frontend_available'    => true,
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label'                 => __( 'Columns', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => '3',
                'tablet_default'        => '2',
                'mobile_default'        => '1',
                'options'               => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                ],
                'prefix_class'          => 'elementor-grid%s-',
				'render_type'			=> 'template',
                'frontend_available'    => true,
            ]
        );

		$this->add_control(
			'aspect_ratio',
			[
				'label'                 => __( 'Aspect Ratio', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					'169'  => '16:9',
					'219'  => '21:9',
					'43'   => '4:3',
					'32'   => '3:2',
				],
				'default'               => '169',
				'prefix_class'          => 'elementor-aspect-ratio-',
				'frontend_available'    => true,
			]
		);
        
        $this->add_control(
            'mute',
            [
                'label'                 => __( 'Mute', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
            ]
        );

        $this->add_control(
            'click_action',
            [
                'label'                 => __( 'Click Action', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'inline',
                'options'               => [
                    'inline'    => __( 'Play Inline', 'powerpack' ),
                    'lightbox'  => __( 'Play in Lightbox', 'powerpack' ),
                ],
            ]
        );

        $this->add_control(
            'ordering',
            [
                'label'                 => __( 'Ordering', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => '',
                'options'               => [
                    ''          => __( 'Default', 'powerpack' ),
                    'random'    => __( 'Random', 'powerpack' ),
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Content Tab: Carousel Settings
         */
        $this->start_controls_section(
            'section_additional_options',
            [
                'label'                 => __( 'Carousel Settings', 'powerpack' ),
                'condition'             => [
                    'layout'    => 'carousel'
                ],
            ]
        );

        $this->add_control(
            'animation_speed',
            [
                'label'                 => __( 'Animation Speed', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => 600,
                'frontend_available'    => true,
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
            ]
        );
        
        $this->add_control(
            'dots',
            [
                'label'                 => __( 'Dots', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'no',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
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
                'frontend_available'    => true,
            ]
        );

        $this->add_control(
            'autoplay_speed',
            [
                'label'                 => __( 'Autoplay Speed', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => 3000,
                'frontend_available'    => true,
                'condition'             => [
                    'autoplay'  => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'pause_on_hover',
            [
                'label'                 => __( 'Pause on Hover', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'frontend_available'    => true,
                'condition'             => [
                    'autoplay'  => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'infinite_loop',
            [
                'label'                 => __( 'Infinite Loop', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'frontend_available'    => true,
            ]
        );
        
        $this->add_control(
            'adaptive_height',
            [
                'label'                 => __( 'Adaptive Height', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'frontend_available'    => true,
            ]
        );

        $this->add_control(
            'direction',
            [
                'label'                 => __( 'Direction', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'label_block'           => false,
                'toggle'                => false,
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
                'frontend_available'    => true,
            ]
        );

        $this->end_controls_section();
		
		$this->end_controls_section();

        /*-----------------------------------------------------------------------------------*/
        /*	STYLE TAB
        /*-----------------------------------------------------------------------------------*/

        /**
         * Style Tab: Layout
         */
        $this->start_controls_section(
            'section_layout_style',
            [
                'label'                 => __( 'Layout', 'powerpack' ),
                'tab'               => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_responsive_control(
			'columns_gap',
			[
				'label'                 => __( 'Columns Gap', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
                    'size' => 20,
                    'unit' => 'px',
                ],
				'size_units'            => [ 'px', '%' ],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-video-gallery-grid .pp-video-gallery .pp-grid-item-wrap' => 'padding-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-video-gallery-grid .pp-video-gallery' => 'margin-left: -{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .pp-video-gallery-carousel .slick-slide' => 'padding-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .pp-video-gallery-carousel .slick-list'  => 'margin-left: -{{SIZE}}{{UNIT}};',
				],
			]
		);
        
        $this->add_responsive_control(
			'rows_gap',
			[
				'label'                 => __( 'Rows Gap', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
                    'size' => 20,
                    'unit' => 'px',
                ],
				'size_units'            => [ 'px', '%' ],
				'range'                 => [
					'px' => [
						'max' => 100,
					],
				],
				'tablet_default'        => [
					'unit' => 'px',
				],
				'mobile_default'        => [
					'unit' => 'px',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-video-gallery .pp-grid-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'        => 'grid',
                ],
			]
		);

        $this->end_controls_section();

        /**
         * Style Tab: Overlay
         */
        $this->start_controls_section(
            'section_overlay_style',
            [
                'label'                 => __( 'Overlay', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'overlay_blend_mode',
            [
                'label'                 => __( 'Blend Mode', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'normal',
                'options'               => [
                    'normal'		=> __( 'Normal', 'powerpack' ),
                    'multiply'		=> __( 'Multiply', 'powerpack' ),
                    'screen'		=> __( 'Screen', 'powerpack' ),
                    'overlay'		=> __( 'Overlay', 'powerpack' ),
                    'darken'		=> __( 'Darken', 'powerpack' ),
                    'lighten'		=> __( 'Lighten', 'powerpack' ),
                    'color-dodge'   => __( 'Color Dodge', 'powerpack' ),
                    'color'			=> __( 'Color', 'powerpack' ),
                    'hue'			=> __( 'Hue', 'powerpack' ),
                    'hard-light'	=> __( 'Hard Light', 'powerpack' ),
                    'soft-light'	=> __( 'Soft Light', 'powerpack' ),
                    'difference'	=> __( 'Difference', 'powerpack' ),
                    'exclusion'		=> __( 'Exclusion', 'powerpack' ),
                    'saturation'	=> __( 'Saturation', 'powerpack' ),
                    'luminosity'	=> __( 'Luminosity', 'powerpack' ),
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-gallery-overlay' => 'mix-blend-mode: {{VALUE}};',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_overlay_style' );

        $this->start_controls_tab(
            'tab_overlay_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
            ]
        );

        $this->add_control(
            'overlay_background_color_normal',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-gallery-overlay' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
			'overlay_margin_normal',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
                        'min'   => 0,
                        'max'   => 50,
                        'step'  => 1,
                    ],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-video-gallery-overlay' => 'top: {{SIZE}}px; bottom: {{SIZE}}px; left: {{SIZE}}px; right: {{SIZE}}px;',
				],
			]
		);
        
        $this->add_control(
			'overlay_opacity_normal',
			[
				'label'                 => __( 'Opacity', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
                        'min'   => 0,
                        'max'   => 1,
                        'step'  => 0.1,
                    ],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-video-gallery-overlay' => 'opacity: {{SIZE}};',
				],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_overlay_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
            ]
        );

        $this->add_control(
            'overlay_background_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-grid-item:hover .pp-video-gallery-overlay' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_control(
			'overlay_margin_hover',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
                        'min'   => 0,
                        'max'   => 50,
                        'step'  => 1,
                    ],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-grid-item:hover .pp-video-gallery-overlay' => 'top: {{SIZE}}px; bottom: {{SIZE}}px; left: {{SIZE}}px; right: {{SIZE}}px;',
				],
			]
		);
        
        $this->add_control(
			'overlay_opacity_hover',
			[
				'label'                 => __( 'Opacity', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
                        'min'   => 0,
                        'max'   => 1,
                        'step'  => 0.1,
                    ],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-grid-item:hover .pp-video-gallery-overlay' => 'opacity: {{SIZE}};',
				],
			]
		);
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
        
        /**
         * Style Tab: Play Icon
         */
        $this->start_controls_section(
			'section_play_icon_style',
			[
				'label'                 => __( 'Play Icon', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'play_icon_type!'   => 'none',
                ],
			]
		);

        $this->add_responsive_control(
            'play_icon_size',
            [
                'label'                 => __( 'Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min' => 10,
                        'max' => 400,
                    ],
                ],
                'default'               => [
                    'size' => 80,
                    'unit' => 'px',
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-play-icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                ],
                'condition'             => [
                    'play_icon_type!'   => 'none',
                ],
            ]
        );

		$this->add_control(
			'play_icon_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-video-play-icon img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'play_icon_type'    => 'image',
                ],
			]
		);

        $this->start_controls_tabs( 'tabs_play_icon_style' );

        $this->start_controls_tab(
            'tab_play_icon_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
                    'play_icon_type'    => 'icon',
                    'select_play_icon[value]!' => '',
                ],
            ]
        );

        $this->add_control(
            'play_icon_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-play-icon' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-video-play-icon svg' => 'fill: {{VALUE}}',
                ],
                'condition'             => [
                    'play_icon_type'    => 'icon',
                    'select_play_icon[value]!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name'                  => 'play_icon_text_shadow',
                'label'                 => __( 'Shadow', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-video-play-icon',
                'condition'             => [
                    'play_icon_type'    => 'icon',
                    'select_play_icon[value]!' => '',
                ],
            ]
        );

        $this->add_control(
            'play_icon_opacity',
            [
                'label'                 => __( 'Opacity', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'max'  => 1,
                        'min'  => 0,
                        'step' => 0.01,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-play-icon' => 'opacity: {{SIZE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_play_icon_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'play_icon_type'    => 'icon',
                    'select_play_icon[value]!' => '',
                ],
            ]
        );

        $this->add_control(
            'play_icon_hover_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-container:hover .pp-video-play-icon' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-video-container:hover .pp-video-play-icon svg' => 'fill: {{VALUE}}',
                ],
                'condition'             => [
                    'play_icon_type'    => 'icon',
                    'select_play_icon[value]!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name'                  => 'play_icon_hover_text_shadow',
                'selector'              => '{{WRAPPER}} .pp-video-container:hover .pp-video-play-icon',
                'condition'             => [
                    'play_icon_type'    => 'icon',
                    'select_play_icon[value]!' => '',
                ],
            ]
        );

        $this->add_control(
            'play_icon_hover_opacity',
            [
                'label'                 => __( 'Opacity', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'max'  => 1,
                        'min'  => 0,
                        'step' => 0.01,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-container:hover .pp-video-play-icon' => 'opacity: {{SIZE}}',
                ],
                'condition'             => [
                    'play_icon_type!'   => 'none',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

		$this->end_controls_section();
        
        /**
         * Style Tab: Video Title
         */
        $this->start_controls_section(
            'section_video_title_style',
            [
                'label'                 => __( 'Video Title', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

			$this->add_responsive_control(
				'video_title_text_align',
				[
					'label' 		=> __( 'Text Align', 'powerpack' ),
					'type' 			=> Controls_Manager::CHOOSE,
					'default' 		=> '',
					'options' 		=> [
						'left' 			=> [
							'title' 	=> __( 'Left', 'powerpack' ),
							'icon' 		=> 'fa fa-align-left',
						],
						'center' 		=> [
							'title' 	=> __( 'Center', 'powerpack' ),
							'icon' 		=> 'fa fa-align-center',
						],
						'right' 		=> [
							'title' 	=> __( 'Right', 'powerpack' ),
							'icon' 		=> 'fa fa-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .pp-video-title' => 'text-align: {{VALUE}};',
					],
				]
			);
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'video_title_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-video-title',
            ]
        );

        $this->add_control(
            'video_title_background_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-title' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'video_title_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-title' => 'color: {{VALUE}};',
                ],
            ]
        );

		$this->add_responsive_control(
			'video_title_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-video-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
        
        /**
         * Style Tab: Filter
         */
        $this->start_controls_section(
            'section_filter_style',
            [
                'label'                 => __( 'Filter', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'filter_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-gallery-filters .pp-gallery-filter-text',
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
            ]
        );
        
        $this->add_responsive_control(
            'filters_margin_bottom',
            [
                'label'                 => __( 'Filters Bottom Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 80,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-gallery-filters .pp-gallery-filter' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
            ]
        );

        $this->start_controls_tabs( 'tabs_filter_style' );

        $this->start_controls_tab(
            'tab_filter_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
            ]
        );

        $this->add_control(
            'filter_color_normal',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-gallery-filters .pp-gallery-filter' => 'color: {{VALUE}};',
                ],
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
            ]
        );

        $this->add_control(
            'filter_background_color_normal',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-gallery-filters .pp-gallery-filter' => 'background-color: {{VALUE}};',
                ],
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'filter_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-gallery-filters .pp-gallery-filter',
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
			]
		);

		$this->add_control(
			'filter_border_radius_normal',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-gallery-filters .pp-gallery-filter' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
			]
		);

		$this->add_responsive_control(
			'filter_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'placeholder'           => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-gallery-filters .pp-gallery-filter' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'filter_box_shadow',
				'selector'          => '{{WRAPPER}} .pp-gallery-filters .pp-gallery-filter',
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_filter_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
            ]
        );

        $this->add_control(
            'filter_color_hover',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-gallery-filters .pp-gallery-filter:hover' => 'color: {{VALUE}};',
                ],
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
            ]
        );

        $this->add_control(
            'filter_background_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-gallery-filters .pp-gallery-filter:hover' => 'background-color: {{VALUE}};',
                ],
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
            ]
        );

        $this->add_control(
            'filter_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-gallery-filters .pp-gallery-filter:hover' => 'border-color: {{VALUE}};',
                ],
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'filter_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-gallery-filters .pp-gallery-filter:hover',
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_filter_active',
            [
                'label'                 => __( 'Active', 'powerpack' ),
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
            ]
        );

        $this->add_control(
            'filter_color_active',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-gallery-filters .pp-gallery-filter.pp-active' => 'color: {{VALUE}};',
                ],
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
            ]
        );

        $this->add_control(
            'filter_background_color_active',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-gallery-filters .pp-gallery-filter.pp-active' => 'background-color: {{VALUE}};',
                ],
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
            ]
        );

        $this->add_control(
            'filter_border_color_active',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-gallery-filters .pp-gallery-filter.pp-active' => 'border-color: {{VALUE}};',
                ],
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'filter_box_shadow_active',
				'selector'              => '{{WRAPPER}} .pp-gallery-filters .pp-gallery-filter.pp-active',
                'condition'             => [
					'layout'            => 'grid',
					'filter_enable'     => 'yes',
                ]
			]
		);
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();

        /**
         * Style Tab: Arrows
         */
        $this->start_controls_section(
            'section_arrows_style',
            [
                'label'                 => __( 'Arrows', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'layout'        => 'carousel',
                    'arrows'        => 'yes',
                ],
            ]
        );
        
        $this->add_control(
			'arrow',
			[
				'label'                 => __( 'Choose Arrow', 'powerpack' ),
				'type'                  => Controls_Manager::ICON,
				'include'               => [
					'fa fa-angle-right',
                    'fa fa-angle-double-right',
                    'fa fa-chevron-right',
                    'fa fa-chevron-circle-right',
                    'fa fa-arrow-right',
                    'fa fa-long-arrow-right',
                    'fa fa-caret-right',
                    'fa fa-caret-square-o-right',
                    'fa fa-arrow-circle-right',
                    'fa fa-arrow-circle-o-right',
                    'fa fa-toggle-right',
                    'fa fa-hand-o-right',
				],
				'default'               => 'fa fa-angle-right',
				'frontend_available'    => true,
                'condition'             => [
                    'layout'        => 'carousel',
                    'arrows'        => 'yes',
                ],
			]
		);
        
        $this->add_responsive_control(
            'arrows_size',
            [
                'label'                 => __( 'Arrows Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [ 'size' => '22' ],
                'range'                 => [
                    'px' => [
                        'min'   => 15,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-slider-arrow' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'        => 'carousel',
                    'arrows'        => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'arrows_position',
            [
                'label'                 => __( 'Align Arrows', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => -100,
                        'max'   => 50,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-arrow-next' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-arrow-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'        => 'carousel',
                    'arrows'        => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_arrows_style' );

        $this->start_controls_tab(
            'tab_arrows_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
                    'layout'        => 'carousel',
                    'arrows'        => 'yes',
                ],
            ]
        );

        $this->add_control(
            'arrows_bg_color_normal',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slider-arrow' => 'background-color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'        => 'carousel',
                    'arrows'        => 'yes',
                ],
            ]
        );

        $this->add_control(
            'arrows_color_normal',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slider-arrow' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'        => 'carousel',
                    'arrows'        => 'yes',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'arrows_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-slider-arrow',
                'condition'             => [
                    'layout'        => 'carousel',
                    'arrows'        => 'yes',
                ],
			]
		);

		$this->add_control(
			'arrows_border_radius_normal',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-slider-arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'        => 'carousel',
                    'arrows'        => 'yes',
                ],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_arrows_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'layout'        => 'carousel',
                    'arrows'        => 'yes',
                ],
            ]
        );

        $this->add_control(
            'arrows_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slider-arrow:hover' => 'background-color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'        => 'carousel',
                    'arrows'        => 'yes',
                ],
            ]
        );

        $this->add_control(
            'arrows_color_hover',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slider-arrow:hover' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'        => 'carousel',
                    'arrows'        => 'yes',
                ],
            ]
        );

        $this->add_control(
            'arrows_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-slider-arrow:hover',
                ],
                'condition'             => [
                    'layout'        => 'carousel',
                    'arrows'        => 'yes',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

		$this->add_responsive_control(
			'arrows_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-slider-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'separator'             => 'before',
                'condition'             => [
                    'layout'        => 'carousel',
                    'arrows'        => 'yes',
                ],
			]
		);
        
        $this->end_controls_section();
        
        /**
         * Style Tab: Dots
         */
        $this->start_controls_section(
            'section_dots_style',
            [
                'label'                 => __( 'Dots', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
            ]
        );

        $this->add_control(
            'dots_position',
            [
                'label'                 => __( 'Position', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'options'               => [
                   'inside'     => __( 'Inside', 'powerpack' ),
                   'outside'    => __( 'Outside', 'powerpack' ),
                ],
                'default'               => 'outside',
				'prefix_class'          => 'pp-video-gallery-dots-',
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'dots_size',
            [
                'label'                 => __( 'Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 2,
                        'max'   => 40,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-gallery .slick-dots li button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'dots_spacing',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 30,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-gallery .slick-dots li' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_dots_style' );

        $this->start_controls_tab(
            'tab_dots_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
            ]
        );

        $this->add_control(
            'dots_color_normal',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-gallery .slick-dots li' => 'background: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
            ]
        );

        $this->add_control(
            'active_dot_color_normal',
            [
                'label'                 => __( 'Active Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-gallery .slick-dots li.slick-active' => 'background: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'dots_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-video-gallery .slick-dots li',
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
			]
		);

		$this->add_control(
			'dots_border_radius_normal',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-video-gallery .slick-dots li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
			]
		);

		$this->add_responsive_control(
			'dots_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
                'allowed_dimensions'    => 'vertical',
				'placeholder'           => [
					'top'      => '',
					'right'    => 'auto',
					'bottom'   => '',
					'left'     => 'auto',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-video-gallery .slick-dots' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_dots_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
            ]
        );

        $this->add_control(
            'dots_color_hover',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-gallery .slick-dots li:hover' => 'background: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
            ]
        );

        $this->add_control(
            'dots_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-gallery .slick-dots li:hover' => 'border-color: {{VALUE}};',
                ],
                'condition'             => [
                    'layout'    => 'carousel',
                    'dots'      => 'yes',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $classes = [
            'pp-video-gallery',
        ];
        
		$this->add_render_attribute( 'gallery-container', 'class', 'pp-video-gallery-container' );
		$this->add_render_attribute( 'gallery-container', 'class', 'pp-video-gallery-' . $settings['layout'] );
        
		$this->add_render_attribute( 'gallery-wrap', 'class', 'pp-video-gallery-wrapper' );
        
        if ( $settings['layout'] == 'grid' ) {
            $classes[] = 'pp-elementor-grid';
        }
        
        if ( $settings['layout'] == 'grid' && $settings['filter_enable'] == 'yes' ) {
            $classes[] = 'pp-video-gallery-filter-enabled';
        }
        
        if ( $settings['layout'] == 'carousel' ) {
            if ( $settings['dots_position'] ) {
                $this->add_render_attribute( 'gallery-container', 'class', 'swiper-container-wrap-dots-' . $settings['dots_position'] );
            }
        }
        
		$this->add_render_attribute( 'gallery', 'class', $classes );
        
        $this->carousel_settings();
        
        $this->add_render_attribute( 'gallery', 'data-action', $settings['click_action'] );
        ?>
        <div <?php echo $this->get_render_attribute_string( 'gallery-container' ); ?>>
            <div <?php echo $this->get_render_attribute_string( 'gallery-wrap' ); ?>>
                <?php $this->render_filters(); ?>
                <div <?php echo $this->get_render_attribute_string( 'gallery' ); ?>>
                    <?php $this->render_videos(); ?>
                </div>
            </div>
        </div>
        <?php
        
        if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {

			if ( ( 'grid' === $settings['layout'] && 'yes' === $settings['filter_enable'] ) ) {
				$this->render_editor_script();
			}
		}
    }

	/**
	 * Carousel Settings.
	 *
	 * @access public
	 */
	public function carousel_settings() {
        $settings = $this->get_settings();
        
        if ( $settings['layout'] == 'carousel' ) {
            
            $slider_options = [
                'slidesToShow'           => ( $settings['columns'] !== '' ) ? absint( $settings['columns'] ) : 3,
                'autoplay'               => ( $settings['autoplay'] === 'yes' ),
                'direction'              => 'horizontal',
                'autoplaySpeed'          => ( $settings['autoplay_speed'] !== '' ) ? $settings['autoplay_speed'] : 3000,
                'infinite'               => ( $settings['infinite_loop'] === 'yes' ),
                'pauseOnHover'           => ( $settings['pause_on_hover'] === 'yes' ),
                'dots'                   => ( $settings['dots'] === 'yes' ),
                'adaptiveHeight'         => true,
                'arrows'				 => false,
            ];
            
            if ( $settings['arrows'] == 'yes' ) {
				$slider_options['arrows'] = true;

                if ( $settings['arrow'] ) {
                    $pa_next_arrow = $settings['arrow'];
                    $pa_prev_arrow = str_replace("right","left",$settings['arrow']);
                }
                else {
                    $pa_next_arrow = 'fa fa-angle-right';
                    $pa_prev_arrow = 'fa fa-angle-left';
                }
                
                $slider_options['prevArrow'] = '<div class="pp-slider-arrow pp-arrow pp-arrow-prev"><i class="' . $pa_prev_arrow . '"></i></div>';
                $slider_options['nextArrow'] = '<div class="pp-slider-arrow pp-arrow pp-arrow-next"><i class="' . $pa_next_arrow . '"></i></div>';
            }
			
			$elementor_bp_tablet	= get_option( 'elementor_viewport_lg' );
			$elementor_bp_mobile	= get_option( 'elementor_viewport_md' );
			$bp_tablet				= !empty($elementor_bp_tablet) ? $elementor_bp_tablet : 1025;
			$bp_mobile				= !empty($elementor_bp_mobile) ? $elementor_bp_mobile : 768;

            $slider_options['responsive'] = [
                [
                    'breakpoint' => $bp_tablet,
                    'settings' => [
                        'slidesToShow'      => ( $settings['columns_tablet'] !== '' ) ? absint( $settings['columns_tablet'] ) : 2,
                    ],
                ],
                [
                    'breakpoint' => $bp_mobile,
                    'settings' => [
                        'slidesToShow'      => ( $settings['columns_mobile'] !== '' ) ? absint( $settings['columns_mobile'] ) : 2,
                    ]
                ]
            ];

            $this->add_render_attribute(
                'gallery-wrap',
                [
                    'data-slider-settings' => wp_json_encode( $slider_options ),
                ]
            );

        }
    }
    
	protected function render_filters() {
		$settings = $this->get_settings_for_display();

		if ( $settings['layout'] == 'grid' && $settings['filter_enable'] == 'yes' ) {
			$all_text 	= ( $settings['filter_all_label'] != '' ) ? $settings['filter_all_label'] : esc_html__('All', 'powerpack');
			$gallery	= $this->get_filter_values();
			?>
			<div class="pp-gallery-filters">
                <div class="pp-gallery-filter pp-active" data-filter="*">
                    <span class="pp-gallery-filter-text">
                        <?php echo $all_text; ?>
                    </span>
                </div>
				<?php foreach ( $gallery as $index => $item ) {
					$filter_label = $item;
                    if ( $item ) { ?>
                    <div class="pp-gallery-filter" data-filter=".<?php echo ( $index ); ?>">
                        <span class="pp-gallery-filter-text"><?php echo $filter_label; ?></span>
                    </div>
				    <?php } ?>
				<?php } ?>
			</div>
			<?php
		}
	}
    
    protected function render_videos() {
        $settings         = $this->get_settings_for_display();
		$gallery_videos   = $settings['gallery_videos'];
		$gallery          = array();
        
        if ( $settings['ordering'] == 'random' ) {

			$keys = array_keys( $gallery_videos );
			shuffle( $keys );

			foreach ( $keys as $key ) {
				$gallery[ $key ] = $gallery_videos[ $key ];
			}
		} else {
			$gallery = $gallery_videos;
		}
        
        foreach ( $gallery as $index => $item ) {
            if ( $settings['layout'] == 'carousel' ) {
                $this->add_render_attribute( 'grid-item-wrap' . $index, 'class', ['pp-video-gallery-item-wrap'] );
            } else {
                $tags = $this->get_filter_keys( $item );

                $this->add_render_attribute( 'grid-item-wrap' . $index, 'class', ['pp-grid-item-wrap', 'pp-video-gallery-item-wrap'] );

                $this->add_render_attribute( 'grid-item-wrap' . $index, 'class', array_keys( $tags ) );
            }
            ?>
            <div <?php echo $this->get_render_attribute_string( 'grid-item-wrap' . $index ); ?>>
                <div class="pp-grid-item pp-video-gallery-item">
					<div class="pp-video-title-wrap">
						<?php
							$video_url_src = '';
							$thumb_size = '';
							if ( $item['video_source'] == 'youtube' ) {
								$video_url_src = $item['youtube_url'];
								$thumb_size = $item['thumbnail_size'];
							} elseif ( $item['video_source'] == 'vimeo' ) {
								$video_url_src = $item['vimeo_url'];
							} elseif ( $item['video_source'] == 'dailymotion' ) {
								$video_url_src = $item['dailymotion_url'];
							}

							$this->add_render_attribute( 'video-container' . $index, 'class', ['pp-video-container', 'elementor-fit-aspect-ratio'] );
							$this->add_render_attribute( 'video-play' . $index, 'class', 'pp-video-play' );

							if ( $settings['click_action'] == 'inline' ) {
								$embed_params = $this->get_embed_params( $item );
								$video_url = Embed::get_embed_url( $video_url_src, $embed_params, [] );

								$this->add_render_attribute( 'video-play' . $index, 'data-src', $video_url );
							} else {
								$video_url = $video_url_src;

								$this->add_render_attribute( 'video-play' . $index, 'data-fancybox', 'video-gallery-' . $this->get_id() );
							}

							$this->add_render_attribute( 'video-play' . $index, 'href', $video_url );
							?>
							<div <?php echo $this->get_render_attribute_string( 'video-container' . $index ); ?>>
								<div <?php echo $this->get_render_attribute_string( 'video-play' . $index ); ?>>
									<div class="pp-video-player">
										<img class="pp-video-thumb" src="<?php echo esc_url( $this->get_video_thumbnail( $item, $thumb_size ) ); ?>" alt="<?php echo esc_attr( $item['filter_label'] ); ?>">
										<?php $this->render_play_icon(); ?>
									</div>
								</div>
							</div>
							<?php

							// Video Overlay
							echo $this->render_video_overlay();
						?>
					</div>
					<?php if ( $item['video_title'] ) { ?>
						<div class="pp-video-title">
							<?php echo $item['video_title']; ?>
						</div>
					<?php } ?>
                </div>
            </div>
            <?php
        }
    }

	/**
	 * Returns Video Thumbnail.
	 *
	 * @access protected
	 */
	protected function get_video_thumbnail( $item, $thumb_size ) {
        
        $thumb_url  = '';
        $video_id   = $this->get_video_id( $item );
        
        if ( $item['custom_thumbnail'] == 'yes' ) {
            
            if ( $item['custom_image']['url'] ) {
                $thumb_url = $item['custom_image']['url'];
            }
            
        } elseif ( $item['video_source'] == 'youtube' ) {

            if ( $video_id != '' ) {
                $thumb_url = 'https://i.ytimg.com/vi/' . $video_id . '/' . $thumb_size . '.jpg';
            }

        } elseif ( $item['video_source'] == 'vimeo' ) {

            if ( $video_id != '' ) {
                $vimeo = unserialize( file_get_contents( "https://vimeo.com/api/v2/video/$video_id.php" ) );
                $thumb_url = $vimeo[0]['thumbnail_large'];
            }
            
        } elseif ( $item['video_source'] == 'dailymotion' ) {

            if ( $video_id != '' ) {
                $dailymotion = 'https://api.dailymotion.com/video/'.$video_id.'?fields=thumbnail_url';
                $get_thumbnail = json_decode( file_get_contents( $dailymotion ), TRUE );
                $thumb_url = $get_thumbnail['thumbnail_url'];
            }
        }
        
        return $thumb_url;

    }

	/**
	 * Returns Video ID.
	 *
	 * @access protected
	 */
	protected function get_video_id( $item ) {

		$video_id = '';

		if ( $item['video_source'] == 'youtube' ) {
            $url = $item['youtube_url'];
            
			if ( preg_match( "#(?<=v=|v\/|vi=|vi\/|youtu.be\/)[a-zA-Z0-9_-]{11}#", $url, $matches ) ) {
				$video_id = $matches[0];
			}

		} elseif ( $item['video_source'] == 'vimeo' ) {
            $url = $item['vimeo_url'];

			$video_id = preg_replace( '/[^\/]+[^0-9]|(\/)/', '', rtrim( $url, '/' ) );

		} elseif ( $item['video_source'] == 'dailymotion' ) {
            $url = $item['dailymotion_url'];
            
            if ( preg_match('/^.+dailymotion.com\/(?:video|swf\/video|embed\/video|hub|swf)\/([^&?]+)/', $url, $matches) ) {
				$video_id = $matches[1];
			}

		}

		return $video_id;

	}

	/**
	 * Get embed params.
	 *
	 * Retrieve video widget embed parameters.
	 *
	 * @access public
	 *
	 * @return array Video embed parameters.
	 */
	public function get_embed_params( $item ) {
		$settings = $this->get_settings_for_display();

		$params = [];

		$params_dictionary = [];

		if ( 'youtube' === $item['video_source'] ) {
            
            $params_dictionary = [
				'mute',
			];

			$params['autoplay'] = 1;

			$params['wmode'] = 'opaque';
		} elseif ( 'vimeo' === $item['video_source'] ) {
            
            $params_dictionary = [
				'mute' => 'muted',
			];

            $params['autopause'] = '0';
			$params['autoplay'] = '1';
		} elseif ( 'dailymotion' === $item['video_source'] ) {
            
            $params_dictionary = [
				'mute',
			];
            
			$params['endscreen-enable'] = '0';
			$params['autoplay'] = 1;

		}

		foreach ( $params_dictionary as $key => $param_name ) {
			$setting_name = $param_name;

			if ( is_string( $key ) ) {
				$setting_name = $key;
			}

			$setting_value = $settings[ $setting_name ] ? '1' : '0';

			$params[ $param_name ] = $setting_value;
		}

		return $params;
	}
    
    protected function render_video_overlay() {
        $this->add_render_attribute( 'overlay', 'class', [
            'pp-media-overlay',
            'pp-video-gallery-overlay',
		] );
		
        return '<div ' . $this->get_render_attribute_string( 'overlay' ) . '></div>';
	}
    
    /**
	 * Render play icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_play_icon() {
        $settings = $this->get_settings_for_display();
        
        if ( $settings['play_icon_type'] == 'none' ) {
            return;
        }

        $this->add_render_attribute( 'play-icon', 'class', 'pp-video-play-icon' );
        
        if ( $settings['play_icon_type'] == 'icon' ) {
			$this->add_render_attribute( 'play-icon', 'class', 'pp-icon' );
			
			if ( ! isset( $settings['play_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
				// add old default
				$settings['play_icon'] = 'fa fa-play-circle';
			}

			$has_icon = ! empty( $settings['play_icon'] );

			if ( $has_icon ) {
				$this->add_render_attribute( 'play-icon-i', 'class', $settings['play_icon'] );
				$this->add_render_attribute( 'play-icon-i', 'aria-hidden', 'true' );
			}

			if ( ! $has_icon && ! empty( $settings['select_play_icon']['value'] ) ) {
				$has_icon = true;
			}
			$migrated = isset( $settings['__fa4_migrated']['select_play_icon'] );
			$is_new = ! isset( $settings['play_icon'] ) && Icons_Manager::is_migration_allowed();
            ?>
			<span <?php echo $this->get_render_attribute_string( 'play-icon' ); ?>>
				<?php
				if ( $is_new || $migrated ) {
					Icons_Manager::render_icon( $settings['select_play_icon'], [ 'aria-hidden' => 'true' ] );
				} elseif ( ! empty( $settings['play_icon'] ) ) {
					?><i <?php echo $this->get_render_attribute_string( 'play-icon-i' ); ?>></i><?php
				}
				?>
            </span>
            <?php

        } elseif ( $settings['play_icon_type'] == 'image' ) {
            
            if ( $settings['play_icon_image']['url'] != '' ) {
                ?>
                <span <?php echo $this->get_render_attribute_string( 'play-icon' ); ?>>
                    <img src="<?php echo esc_url( $settings['play_icon_image']['url'] ); ?>">
                </span>
                <?php
            }

        }
    }

	/**
	 * Clean string - Removes spaces and special chars.
	 *
	 * @param String $string String to be cleaned.
	 * @return array Google Map languages List.
	 */
	public function clean( $string ) {

		// Replaces all spaces with hyphens.
		$string = str_replace( ' ', '-', $string );

		// Removes special chars.
		$string = preg_replace( '/[^A-Za-z0-9\-]/', '', $string );

		// Turn into lower case characters.
		return strtolower( $string );
	}

	/**
	 * Render filter keys array.
	 *
	 * @access public
	 */
	public function get_filter_keys( $item ) {

		$filters = explode( ',', $item['filter_label'] );
		$filters = array_map( 'trim', $filters );

		$filters_array = [];

		foreach ( $filters as $key => $value ) {
			$filters_array[ $this->clean( $value ) ] = $value;
		}

		return $filters_array;
	}

	/**
	 * Get Filter values array.
	 *
	 * Returns the Filter array of objects.
	 *
	 * @access public
	 */
	public function get_filter_values() {

		$settings = $this->get_settings_for_display();

		$filters = array();

		if ( ! empty( $settings['gallery_videos'] ) ) {

			foreach ( $settings['gallery_videos'] as $key => $value ) {

				$filter_keys = $this->get_filter_keys( $value );

				if ( ! empty( $filter_keys ) ) {

					$filters = array_unique( array_merge( $filters, $filter_keys ) );
				}
			}
		}

		return $filters;
	}

	/**
	 * Render isotope script
	 *
	 * @access protected
	 */
	protected function render_editor_script() {

		?><script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
                $( '.pp-video-gallery' ).each( function() {
                    
                    var $node_id 	= '<?php echo $this->get_id(); ?>',
                        $scope 		= $( '[data-id="' + $node_id + '"]' ),
                        $gallery 	= $(this);
                    
                    if ( $gallery.closest( $scope ).length < 1 ) {
        				return;
        			}
                    
                    var $layout_mode = 'fitRows';

                    var $isotope_args = {
                        itemSelector:   '.pp-grid-item-wrap',
                        layoutMode		: $layout_mode,
                        percentPosition : true,
                    },
                        $isotope_gallery = {};

                    $gallery.imagesLoaded( function(e) {
                        $isotope_gallery = $gallery.isotope( $isotope_args );
                        
                        $gallery.find('.pp-grid-item-wrap').resize( function() {
							$gallery.isotope( 'layout' );
						});
                    });

                    $('.pp-gallery-filters').on( 'click', '.pp-gallery-filter', function() {
						var $this = $(this),
                            filterValue = $this.attr('data-filter');

                        $this.siblings().removeClass('pp-active');
                        $this.addClass('pp-active');
                        $isotope_gallery.isotope({ filter: filterValue });
                    });
                });
			});
		</script>
		<?php
	}

    protected function _content_template() {
    }
}