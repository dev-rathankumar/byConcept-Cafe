<?php
namespace PowerpackElements\Modules\Video\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Modules\Video\Module;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
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
 * Video Widget
 */
class Video extends Powerpack_Widget {
    
    /**
	 * Retrieve video widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return parent::get_widget_name( 'Video' );
    }

    /**
	 * Retrieve video widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
    public function get_title() {
        return parent::get_widget_title( 'Video' );
    }

    /**
	 * Retrieve the list of categories the video widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Video' );
    }

    /**
	 * Retrieve video widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
    public function get_icon() {
        return parent::get_widget_icon( 'Video' );
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
		return parent::get_widget_keywords( 'Video' );
	}
    
    /**
	 * Retrieve the list of scripts the video widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
    public function get_script_depends() {
        return [
            'fancybox',
            'powerpack-frontend'
        ];
    }

    /**
	 * Register video widget controls.
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
         * Content Tab: Video
         */
        $this->start_controls_section(
            'section_video',
            [
                'label'                 => __( 'Video', 'powerpack' ),
            ]
        );

		$this->add_control(
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

		$this->add_control(
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

		$this->add_control(
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

		$this->add_control(
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

		$this->add_control(
			'loop',
			[
				'label'                 => __( 'Loop', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'condition'             => [
					'video_source!' => 'dailymotion',
				],
			]
		);

		$this->add_control(
			'start_time',
			[
				'label'                 => __( 'Start Time', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'description'           => __( 'Enter start time in seconds', 'powerpack' ),
				'default'               => '',
				'condition'             => [
					'loop'         => '',
				],
			]
		);

		$this->add_control(
			'end_time',
			[
				'label'                 => __( 'End Time', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'description'           => __( 'Enter end time in seconds', 'powerpack' ),
				'default'               => '',
				'condition'             => [
					'loop'         => '',
					'video_source' => [ 'youtube', 'hosted' ],
				],
			]
		);

		$this->add_control(
			'video_options',
			[
				'label'                 => __( 'Video Options', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
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
            'autoplay',
            [
                'label'                 => __( 'Autoplay', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
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
			'controls',
			[
				'label'                 => __( 'Player Controls', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'label_off'             => __( 'Hide', 'powerpack' ),
				'label_on'              => __( 'Show', 'powerpack' ),
				'default'               => 'yes',
				'condition'             => [
					'video_source!'     => 'vimeo',
				],
			]
		);

		$this->add_control(
			'color',
			[
				'label'                 => __( 'Controls Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'condition'             => [
					'video_source' => [ 'vimeo', 'dailymotion' ],
				],
			]
		);

		$this->add_control(
			'modestbranding',
			[
				'label'                 => __( 'Modest Branding', 'powerpack' ),
				'description'           => __( 'Turn on this option to use a YouTube player that does not show a YouTube logo.', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'condition'             => [
					'video_source' => [ 'youtube' ],
					'controls'     => 'yes',
				],
			]
		);
        
		$this->add_control(
			'yt_privacy',
			[
				'label'                 => __( 'Privacy Mode', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'description'           => __( 'When you turn on privacy mode, YouTube won\'t store information about visitors on your website unless they play the video.', 'powerpack' ),
				'condition'             => [
					'video_source' => 'youtube',
				],
			]
		);

		$this->add_control(
			'rel',
			[
				'label'                 => __( 'Suggested Videos', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					''     => __( 'Current Video Channel', 'powerpack' ),
					'yes'  => __( 'Any Video', 'powerpack' ),
				],
				'condition'             => [
					'video_source' => 'youtube',
				],
			]
		);

        // Dailymotion
		$this->add_control(
			'showinfo',
			[
				'label'                 => __( 'Video Info', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'label_off'             => __( 'Hide', 'powerpack' ),
				'label_on'              => __( 'Show', 'powerpack' ),
				'default'               => 'yes',
				'condition'             => [
					'video_source' => [ 'dailymotion' ],
				],
			]
		);

		$this->add_control(
			'logo',
			[
				'label'                 => __( 'Logo', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'label_off'             => __( 'Hide', 'powerpack' ),
				'label_on'              => __( 'Show', 'powerpack' ),
				'default'               => 'yes',
				'condition'             => [
					'video_source' => [ 'dailymotion' ],
				],
			]
		);

		// Vimeo.
		$this->add_control(
			'vimeo_title',
			[
				'label'                 => __( 'Intro Title', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'label_off'             => __( 'Hide', 'powerpack' ),
				'label_on'              => __( 'Show', 'powerpack' ),
				'default'               => 'yes',
				'condition'             => [
					'video_source' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_portrait',
			[
				'label'                 => __( 'Intro Portrait', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'label_off'             => __( 'Hide', 'powerpack' ),
				'label_on'              => __( 'Show', 'powerpack' ),
				'default'               => 'yes',
				'condition'             => [
					'video_source' => 'vimeo',
				],
			]
		);

		$this->add_control(
			'vimeo_byline',
			[
				'label'                 => __( 'Intro Byline', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'label_off'             => __( 'Hide', 'powerpack' ),
				'label_on'              => __( 'Show', 'powerpack' ),
				'default'               => 'yes',
				'condition'             => [
					'video_source' => 'vimeo',
				],
			]
		);

        $this->end_controls_section();

        /**
         * Content Tab: Thumbnail
         */
        $this->start_controls_section(
            'section_thumbnail',
            [
                'label'                 => __( 'Thumbnail', 'powerpack' ),
            ]
        );

		$this->add_control(
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
                'condition'             => [
					'video_source'      => 'youtube',
                ]
			]
		);

		$this->add_control(
			'custom_thumbnail',
			[
				'label'                 => __( 'Custom Thumbnail', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => '',
			]
		);
        
        $this->add_control(
			'custom_image',
            [
                'label'                 => __( 'Image', 'powerpack' ),
                'type'                  => Controls_Manager::MEDIA,
                'default'               => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition'             => [
					'custom_thumbnail'  => 'yes',
                ]
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

        /*-----------------------------------------------------------------------------------*/
        /*	STYLE TAB
        /*-----------------------------------------------------------------------------------*/

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
                    '{{WRAPPER}} .pp-video:hover .pp-video-gallery-overlay' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .pp-video:hover .pp-video-gallery-overlay' => 'top: {{SIZE}}px; bottom: {{SIZE}}px; left: {{SIZE}}px; right: {{SIZE}}px;',
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
					'{{WRAPPER}} .pp-video:hover .pp-video-gallery-overlay' => 'opacity: {{SIZE}};',
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
                    'play_icon_type'	=> 'icon',
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
                    'play_icon_type'	=> 'icon',
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
                    'play_icon_type'	=> 'icon',
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
                'condition'             => [
                    'play_icon_type!'   => 'none',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_play_icon_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'play_icon_type'	=> 'icon',
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
                    'play_icon_type'	=> 'icon',
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
                    'play_icon_type'	=> 'icon',
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
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $classes = [
            'pp-video',
        ];
        
		$this->add_render_attribute( 'video-wrap', 'class', 'pp-video-wrap' );
        
		$this->add_render_attribute( 'video', 'class', $classes );
		
		$pp_gallery_settings = [];

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$pp_gallery_settings['post_id'] = \Elementor\Plugin::$instance->editor->get_post_id();
		} else {
			$pp_gallery_settings['post_id'] = get_the_ID();
		}

		$pp_gallery_settings['widget_id'] = $this->get_id();
        
        $this->add_render_attribute( 'video', 'data-settings', wp_json_encode( $pp_gallery_settings ) );
        
        ?>
        <div <?php echo $this->get_render_attribute_string( 'video-wrap' ); ?>>
            <div <?php echo $this->get_render_attribute_string( 'video' ); ?>>
                <?php $this->render_video(); ?>
            </div>
        </div>
        <?php
    }
    
    protected function render_video() {
        $settings = $this->get_settings_for_display();

        $video_url_src = '';
        $thumb_size = '';
        if ( $settings['video_source'] == 'youtube' ) {
            $video_url_src = $settings['youtube_url'];
            $thumb_size = $settings['thumbnail_size'];
        } elseif ( $settings['video_source'] == 'vimeo' ) {
            $video_url_src = $settings['vimeo_url'];
        } elseif ( $settings['video_source'] == 'dailymotion' ) {
            $video_url_src = $settings['dailymotion_url'];
        }

        $this->add_render_attribute( 'video-container', 'class', ['pp-video-container', 'elementor-fit-aspect-ratio'] );
        $this->add_render_attribute( 'video-play', 'class', 'pp-video-play' );
        $this->add_render_attribute( 'video-player', 'class', 'pp-video-player' );

        $embed_params = $this->get_embed_params();
        $embed_options = $this->get_embed_options();

        $video_url = Embed::get_embed_url( $video_url_src, $embed_params, $embed_options );

        $this->add_render_attribute( 'video-player', 'data-src', $video_url );
        
        $autoplay = ( 'yes' == $settings['autoplay'] ) ? '1' : '0';
        
        $this->add_render_attribute( 'video-play', 'data-autoplay', $autoplay );
        ?>
        <div <?php echo $this->get_render_attribute_string( 'video-container' ); ?>>
            <div <?php echo $this->get_render_attribute_string( 'video-play' ); ?>>
                <div <?php echo $this->get_render_attribute_string( 'video-player' ); ?>>
                    <img class="pp-video-thumb" src="<?php echo esc_url( $this->get_video_thumbnail( $thumb_size ) ); ?>">
                    <?php $this->render_play_icon(); ?>
                </div>
            </div>
        </div>
        <?php

        // Video Overlay
        echo $this->render_video_overlay();
    }

	/**
	 * Returns Video Thumbnail.
	 *
	 * @access protected
	 */
	protected function get_video_thumbnail( $thumb_size ) {
        $settings = $this->get_settings_for_display();
        
        $thumb_url  = '';
        $video_id   = $this->get_video_id();
        
        if ( $settings['custom_thumbnail'] == 'yes' ) {
            
            if ( $settings['custom_image']['url'] ) {
                $thumb_url = $settings['custom_image']['url'];
            }
            
        } elseif ( $settings['video_source'] == 'youtube' ) {

            if ( $video_id != '' ) {
                $thumb_url = 'https://i.ytimg.com/vi/' . $video_id . '/' . $thumb_size . '.jpg';
            }

        } elseif ( $settings['video_source'] == 'vimeo' ) {

            if ( $video_id != '' ) {
                $vimeo = unserialize( file_get_contents( "https://vimeo.com/api/v2/video/$video_id.php" ) );
                $thumb_url = $vimeo[0]['thumbnail_large'];
            }
            
        } elseif ( $settings['video_source'] == 'dailymotion' ) {

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
	protected function get_video_id() {
        $settings = $this->get_settings_for_display();

		$video_id = '';

		if ( $settings['video_source'] == 'youtube' ) {
            $url = $settings['youtube_url'];
            
			if ( preg_match( "#(?<=v=|v\/|vi=|vi\/|youtu.be\/)[a-zA-Z0-9_-]{11}#", $url, $matches ) ) {
				$video_id = $matches[0];
			}

		} elseif ( $settings['video_source'] == 'vimeo' ) {
            $url = $settings['vimeo_url'];

			$video_id = preg_replace( '/[^\/]+[^0-9]|(\/)/', '', rtrim( $url, '/' ) );

		} elseif ( $settings['video_source'] == 'dailymotion' ) {
            $url = $settings['dailymotion_url'];
            
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
	public function get_embed_params() {
		$settings = $this->get_settings_for_display();

		$params = [];

		$params_dictionary = [];

		if ( 'youtube' === $settings['video_source'] ) {
            
            $params_dictionary = [
				'loop',
				'mute',
				'controls',
				'modestbranding',
				'rel',
			];

			if ( $settings['loop'] ) {
				$video_properties = Embed::get_video_properties( $settings['youtube_url'] );

				$params['playlist'] = $video_properties['video_id'];
			}

			$params['autoplay'] = 1;

			$params['wmode'] = 'opaque';

			$params['start'] = $settings['start_time'];

			$params['end'] = $settings['end_time'];
		} elseif ( 'vimeo' === $settings['video_source'] ) {
            
            $params_dictionary = [
				'loop',
				'mute' => 'muted',
				'vimeo_title' => 'title',
				'vimeo_portrait' => 'portrait',
				'vimeo_byline' => 'byline',
			];

			$params['color'] = str_replace( '#', '', $settings['color'] );

            $params['autopause'] = '0';
			$params['autoplay'] = '1';
		} elseif ( 'dailymotion' === $settings['video_source'] ) {
            
            $params_dictionary = [
				'controls',
				'mute',
				'showinfo' => 'ui-start-screen-info',
				'logo' => 'ui-logo',
			];

			$params['ui-highlight'] = str_replace( '#', '', $settings['color'] );

			$params['start'] = $settings['start_time'];
            
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

	
	/**
	 * Get embed options.
	 *
	 * @access private
	 *
	 * @return array Video embed options.
	 */
	private function get_embed_options() {
		$settings = $this->get_settings_for_display();

		$embed_options = [];

		if ( 'youtube' === $settings['video_source'] ) {
			$embed_options['privacy'] = $settings['yt_privacy'];
		} elseif ( 'vimeo' === $settings['video_source'] ) {
			$embed_options['start'] = $settings['start_time'];
		}

		//$embed_options['lazy_load'] = ! empty( $settings['lazy_load'] );

		return $embed_options;
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

    protected function _content_template() {
    }
}