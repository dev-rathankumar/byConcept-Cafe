<?php
namespace PowerpackElements\Modules\Devices\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Modules\Devices\Module;
use PowerpackElements\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Control_Media;
use Elementor\Icons_Manager;
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
 * Devices Widget
 */
class Devices extends Powerpack_Widget {
    
    /**
	 * Retrieve video widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return parent::get_widget_name( 'Devices' );
    }

    /**
	 * Retrieve video widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
    public function get_title() {
        return parent::get_widget_title( 'Devices' );
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
        return parent::get_widget_categories( 'Devices' );
    }

    /**
	 * Retrieve video widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
    public function get_icon() {
        return parent::get_widget_icon( 'Devices' );
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
		return parent::get_widget_keywords( 'Devices' );
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
            'powerpack-devices',
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
		
		/* Content Tab */
		$this->register_content_device_controls();
		$this->register_content_image_controls();
		$this->register_content_video_controls();
		$this->register_content_video_options_controls();
		$this->register_content_help_docs_controls();
		
		/* Style Tab */
		$this->register_style_device_controls();
		$this->register_style_video_overlay_controls();
		$this->register_style_video_interface_controls();
		$this->register_style_video_buttons_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/
	
	protected function register_content_device_controls() {
        
        /**
         * Content Tab: Device
         */
        $this->start_controls_section(
            'section_device',
            [
                'label'                 => __( 'Device', 'powerpack' ),
            ]
        );

		$this->add_control(
			'device_type',
			[
				'label'                 => __( 'Type', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'phone',
				'options'               => [
					'phone'		=> __( 'Phone', 'powerpack' ),
					'tablet'	=> __( 'Tablet', 'powerpack' ),
					'laptop'	=> __( 'Laptop', 'powerpack' ),
					'desktop'	=> __( 'Desktop', 'powerpack' ),
					'window'	=> __( 'Window', 'powerpack' ),
				],
			]
		);

		$this->add_control(
			'media_type',
			[
				'label'                 => __( 'Media Type', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'image',
				'options'               => [
					'image'		=> __( 'Image', 'powerpack' ),
					'video'		=> __( 'Video', 'powerpack' ),
				],
			]
		);

		$this->add_control(
			'orientation',
			[
				'label'                 => __( 'Orientation', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'portrait',
				'options'               => [
					'portrait'		=> __( 'Portrait', 'powerpack' ),
					'landscape'		=> __( 'Landscape', 'powerpack' ),
				],
				'condition'             => [
					'device_type'	=> ['phone', 'tablet'],
				],
			]
		);

		$this->add_control(
			'orientation_control',
			[
				'label'                 => __( 'Orientation Control', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'label_off'             => __( 'Hide', 'powerpack' ),
				'label_on'              => __( 'Show', 'powerpack' ),
				'default'               => '',
				'condition'             => [
					'device_type'	=> ['phone', 'tablet'],
				],
			]
		);

        $this->add_responsive_control(
            'device_align',
            [
                'label'					=> __( 'Alignment', 'powerpack' ),
                'type'					=> Controls_Manager::CHOOSE,
                'options'				=> [
                    'left' 	=> [
                        'title' 	=> __( 'Left', 'powerpack' ),
                        'icon' 		=> 'eicon-h-align-left',
                    ],
                    'center' 		=> [
                        'title' 	=> __( 'Center', 'powerpack' ),
                        'icon' 		=> 'eicon-h-align-center',
                    ],
                    'right' 		=> [
                        'title' 	=> __( 'Right', 'powerpack' ),
                        'icon' 		=> 'eicon-h-align-right',
                    ],
                ],
                'default'               => 'center',
				'selectors' => [
					'{{WRAPPER}} .pp-device-container' => 'text-align: {{VALUE}};',
				],
            ]
        );

        $this->end_controls_section();
	}

	protected function register_content_image_controls() {
        /**
         * Content Tab: Image
         */
        $this->start_controls_section(
            'section_image',
            [
                'label'                 => __( 'Image', 'powerpack' ),
				'condition'             => [
					'media_type'	=> 'image',
				],
            ]
        );
		
		$this->add_control(
			'image',
			[
				'label'					=> __( 'Choose Image', 'powerpack' ),
				'type'					=> Controls_Manager::MEDIA,
				'dynamic'				=> [
					'active'	=> true
				],
				'default'				=> [
					'url'		=> Utils::get_placeholder_image_src(),
				],
				'condition'             => [
					'media_type'	=> 'image',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'					=> 'image',
				'label'					=> __( 'Image Size', 'powerpack' ),
				'default'				=> 'large',
				'condition'				=> [
					'image[url]!'	=> '',
					'media_type' 	=> 'image',
				]
			]
		);

		$this->add_control(
			'fit_type',
			[
				'label'                 => __( 'Fit Type', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'cover',
				'options'               => [
					'default'	=> __( 'Default', 'powerpack' ),
					'cover'		=> __( 'Cover', 'powerpack' ),
					'fill'		=> __( 'Fill', 'powerpack' ),
				],
				'prefix_class'          => 'pp-device-image-fit-',
				'condition'				=> [
					'media_type'	=> 'image',
				]
			]
		);

		$this->add_control(
			'scrollable',
			[
				'label'                 => __( 'Scrollable', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'label_off'             => __( 'No', 'powerpack' ),
				'label_on'              => __( 'Yes', 'powerpack' ),
				'default'               => '',
				'condition'				=> [
					'media_type'	=> 'image',
				]
			]
		);

		$this->add_control(
			'image_align',
			[
				'label'					=> __( 'Vertical Align', 'powerpack' ),
				'type'					=> Controls_Manager::CHOOSE,
				'label_block'           => false,
				'toggle'				=> false,
				'default'				=> 'top',
				'options'				=> [
                    'top'			=> [
                        'title' 	=> __( 'Top', 'powerpack' ),
                        'icon' 		=> 'eicon-v-align-top',
                    ],
                    'middle' 		=> [
                        'title' 	=> __( 'Middle', 'powerpack' ),
                        'icon' 		=> 'eicon-v-align-middle',
                    ],
                    'bottom' 		=> [
                        'title' 	=> __( 'Bottom', 'powerpack' ),
                        'icon' 		=> 'eicon-v-align-bottom',
                    ],
					'custom'		=> [
						'title' => __( 'Custom', 'powerpack' ),
						'icon' 		=> 'eicon-exchange',
					],
				],
				'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'bottom'   => 'flex-end',
					'middle'   => 'center',
					'custom'   => 'flex-start',
				],
				'selectors' 	=> [
					'{{WRAPPER}} .pp-device-screen-image' => 'align-items: {{VALUE}};',
				],
				'condition'				=> [
					'image[url]!'	=> '',
					'device_type!'	=> 'window',
					'media_type'	=> 'image',
					'fit_type'		=> 'default',
					'scrollable!'	=> 'yes',
				]
			]
		);
        
        $this->add_responsive_control(
			'image_align_custom',
			[
				'label'                 => __( 'Top Offset', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%' ],
				'range'                 => [
					'px' => [
                        'min'   => 0,
                        'max'   => 800,
                        'step'  => 1,
                    ],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-device-screen' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition'				=> [
					'image[url]!'	=> '',
					'device_type!'	=> 'window',
					'media_type'	=> 'image',
					'fit_type'		=> 'default',
					'image_align'	=> 'custom',
					'scrollable!'	=> 'yes',
				]
			]
		);

        $this->end_controls_section();
	}

	protected function register_content_video_controls() {
        /**
         * Content Tab: Video
         */
        $this->start_controls_section(
            'section_video',
            [
                'label'                 => __( 'Video', 'powerpack' ),
				'condition'             => [
					'media_type' => 'video',
				],
            ]
        );

		$this->add_control(
			'video_source',
			[
				'label'                 => __( 'Source', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'youtube',
				'options'               => [
					'youtube'		=> __( 'YouTube', 'powerpack' ),
					'vimeo'			=> __( 'Vimeo', 'powerpack' ),
					'dailymotion'	=> __( 'Dailymotion', 'powerpack' ),
					'hosted'		=> __( 'Self Hosted/URL', 'powerpack' ),
				],
				'frontend_available'    => true,
				'condition'             => [
					'media_type' => 'video',
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
					'media_type' => 'video',
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

		$this->start_controls_tabs(
			'tabs_sources',
			[
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				],	
			]
		);

		$this->start_controls_tab(
			'tab_source_mp4',
			[
				'label'					=> __( 'MP4', 'powerpack' ),
			]
		);

		$this->add_control(
			'video_source_mp4',
			[
				'label'					=> __( 'Source', 'powerpack' ),
				'type'					=> Controls_Manager::SELECT,
				'default'				=> 'url',
				'options'				=> [
					'url'		=> __( 'URL', 'powerpack' ),
					'file'		=> __( 'File', 'powerpack' ),
				],
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				],
			]
		);

		$this->add_control(
			'video_url_mp4',
			[
				'label'					=> __( 'URL', 'powerpack' ),
				'type'					=> Controls_Manager::TEXT,
				'dynamic'				=> [
					'active' 	=> true,
					'categories'   => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'condition'				=> [
					'media_type'		=> 'video',
					'video_source'		=> 'hosted',
					'video_source_mp4'	=> 'url',
				],
			]
		);

		$this->add_control(
			'video_file_mp4',
			[
				'label'					=> __( 'Upload Video', 'powerpack' ),
				'type'					=> Controls_Manager::MEDIA,
				'dynamic'				=> [
					'active' 		=> true,
					'categories'	=> [
						TagsModule::POST_META_CATEGORY,
						TagsModule::MEDIA_CATEGORY,
					],
				],
				'media_type'			=> 'video',
				'condition'				=> [
					'media_type'		=> 'video',
					'video_source'		=> 'hosted',
					'video_source_mp4'	=> 'file',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_source_m4v',
			[
				'label'					=> __( 'M4V', 'powerpack' ),
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				],
			]
		);

		$this->add_control(
			'video_source_m4v',
			[
				'label'					=> __( 'Source', 'powerpack' ),
				'type'					=> Controls_Manager::SELECT,
				'default'				=> 'url',
				'options'				=> [
					'url'		=> __( 'URL', 'powerpack' ),
					'file'		=> __( 'File', 'powerpack' ),
				],
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				],
			]
		);

		$this->add_control(
			'video_url_m4v',
			[
				'label'					=> __( 'URL', 'powerpack' ),
				'type'					=> Controls_Manager::TEXT,
				'dynamic'				=> [
					'active' 	=> true,
					'categories'   => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'condition'				=> [
					'media_type'		=> 'video',
					'video_source'		=> 'hosted',
					'video_source_m4v'	=> 'url',
				],
			]
		);

		$this->add_control(
			'video_file_m4v',
			[
				'label'					=> __( 'Upload Video', 'powerpack' ),
				'type'					=> Controls_Manager::MEDIA,
				'dynamic'				=> [
					'active'		=> true,
					'categories'	=> [
						TagsModule::POST_META_CATEGORY,
						TagsModule::MEDIA_CATEGORY,
					],
				],
				'media_type'			=> 'video',
				'condition'				=> [
					'media_type'		=> 'video',
					'video_source'		=> 'hosted',
					'video_source_m4v'	=> 'file',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_source_ogg',
			[
				'label'					=> __( 'OGG', 'powerpack' ),
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				],
			]
		);

		$this->add_control(
			'video_source_ogg',
			[
				'label'					=> __( 'Source', 'powerpack' ),
				'type'					=> Controls_Manager::SELECT,
				'default'				=> 'url',
				'options'				=> [
					'url'		=> __( 'URL', 'powerpack' ),
					'file'		=> __( 'File', 'powerpack' ),
				],
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				],
			]
		);

		$this->add_control(
			'video_url_ogg',
			[
				'label'					=> __( 'URL', 'powerpack' ),
				'type'					=> Controls_Manager::TEXT,
				'dynamic'				=> [
					'active' 	=> true,
					'categories'   => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'condition'				=> [
					'media_type'		=> 'video',
					'video_source'		=> 'hosted',
					'video_source_ogg'	=> 'url',
				],
			]
		);

		$this->add_control(
			'video_file_ogg',
			[
				'label'					=> __( 'Upload Video', 'powerpack' ),
				'type'					=> Controls_Manager::MEDIA,
				'dynamic'				=> [
					'active'		=> true,
					'categories'	=> [
						TagsModule::POST_META_CATEGORY,
						TagsModule::MEDIA_CATEGORY,
					],
				],
				'media_type'			=> 'video',
				'condition'				=> [
					'media_type'		=> 'video',
					'video_source'		=> 'hosted',
					'video_source_ogg'	=> 'file',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_source_webm',
			[
				'label'					=> __( 'WEBM', 'powerpack' ),
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				],
			]
		);

		$this->add_control(
			'video_source_webm',
			[
				'label'					=> __( 'Source', 'powerpack' ),
				'type'					=> Controls_Manager::SELECT,
				'default'				=> 'url',
				'options'				=> [
					'url'		=> __( 'URL', 'powerpack' ),
					'file'		=> __( 'File', 'powerpack' ),
				],
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				],
			]
		);

		$this->add_control(
			'video_url_webm',
			[
				'label'					=> __( 'URL', 'powerpack' ),
				'type'					=> Controls_Manager::TEXT,
				'dynamic'				=> [
					'active' 	=> true,
					'categories'   => [
						TagsModule::POST_META_CATEGORY,
						TagsModule::URL_CATEGORY,
					],
				],
				'condition'				=> [
					'media_type'		=> 'video',
					'video_source'		=> 'hosted',
					'video_source_webm'	=> 'url',
				],
			]
		);

		$this->add_control(
			'video_file_webm',
			[
				'label'					=> __( 'Upload Video', 'powerpack' ),
				'type'					=> Controls_Manager::MEDIA,
				'dynamic'				=> [
					'active'		=> true,
					'categories'	=> [
						TagsModule::POST_META_CATEGORY,
						TagsModule::MEDIA_CATEGORY,
					],
				],
				'media_type'			=> 'video',
				'condition'				=> [
					'media_type'		=> 'video',
					'video_source'		=> 'hosted',
					'video_source_webm' => 'file',
				],
			]
		);

		$this->end_controls_tab();
		
		$this->end_controls_tabs();

		$this->add_control(
			'thumbnail_size',
			[
				'label'                 => __( 'Thumbnail Size', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'separator'             => 'before',
				'default'               => 'maxresdefault',
				'options'               => [
					'maxresdefault' => __( 'Maximum Resolution', 'powerpack' ),
                    'hqdefault'     => __( 'High Quality', 'powerpack' ),
                    'mqdefault'     => __( 'Medium Quality', 'powerpack' ),
                    'sddefault'     => __( 'Standard Quality', 'powerpack' ),
				],
                'condition'             => [
					'media_type'		=> 'video',
					'video_source'      => 'youtube',
                ]
			]
		);

		$this->add_control(
			'cover_image_show',
			[
				'label'                 => __( 'Show Cover Image', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => '',
				'separator'             => 'before',
				'condition'				=> [
					'media_type'		=> 'video',
				]
			]
		);
        
        $this->add_control(
			'cover_image',
            [
                'label'                 => __( 'Cover Image', 'powerpack' ),
                'type'                  => Controls_Manager::MEDIA,
                'default'               => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
				'dynamic'				=> [
					'active'		=> true,
				],
				'condition'             => [
					'media_type'		=> 'video',
					'cover_image_show'	=> 'yes',
				],
            ]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'					=> 'cover_image',
				'label'					=> __( 'Image Size', 'powerpack' ),
				'default'				=> 'large',
				'condition'				=> [
					'cover_image[url]!'	=> '',
					'media_type'		=> 'video',
					'cover_image_show'	=> 'yes',
				]
			]
		);

        $this->end_controls_section();
	}

	protected function register_content_video_options_controls() {
        /**
         * Content Tab: Video Options
         */
        $this->start_controls_section(
            'section_video_options',
            [
                'label'                 => __( 'Video Options', 'powerpack' ),
				'condition'             => [
					'media_type'	=> 'video',
				],
            ]
        );

		$this->add_control(
			'video_settings',
			[
				'label'                 => __( 'Video Settings', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'condition'				=> [
					'media_type'	=> 'video',
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
					'media_type'	=> 'video',
					'video_source'	=> 'youtube',
					'controls'		=> 'yes',
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
					'media_type'	=> 'video',
					'video_source'	=> 'youtube',
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
					'media_type'	=> 'video',
					'video_source'	=> 'youtube',
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
					'media_type'	=> 'video',
					'video_source'	=> [ 'dailymotion' ],
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
					'media_type'	=> 'video',
					'video_source'	=> [ 'dailymotion' ],
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
					'media_type'	=> 'video',
					'video_source'	=> 'vimeo',
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
					'media_type'	=> 'video',
					'video_source'	=> 'vimeo',
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
					'media_type'	=> 'video',
					'video_source'	=> 'vimeo',
				],
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
				'condition'				=> [
					'media_type'	=> 'video',
				],
            ]
        );

		$this->add_control(
			'stop_others',
			[
				'label'                 => __( 'Stop Others', 'powerpack' ),
				'description'			=> __( 'Stop all other videos on page when this video is played.', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'				=> '',
				'label_on'				=> __( 'Yes', 'powerpack' ),
				'label_off'				=> __( 'No', 'powerpack' ),
				'return_value'			=> 'yes',
				'frontend_available'	=> true,
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				],
			]
		);

		$this->add_control(
			'restart_on_pause',
			[
				'label'                 => __( 'Restart on pause', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'				=> '',
				'label_on'				=> __( 'Yes', 'powerpack' ),
				'label_off'				=> __( 'No', 'powerpack' ),
				'return_value'			=> 'yes',
				'frontend_available'	=> true,
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				],
			]
		);

		$this->add_control(
			'loop',
			[
				'label'                 => __( 'Loop', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'frontend_available'	=> true,
				'condition'				=> [
					'media_type'	=> 'video',
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
					'media_type'	=> 'video',
					'video_source!' => 'hosted',
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
					'media_type'	=> 'video',
					'video_source' => [ 'youtube', 'hosted' ],
					'loop'         => '',
				],
			]
		);

		$this->add_control(
			'end_at_last_frame',
			[
				'label'                 => __( 'End at last frame', 'powerpack' ),
				'description'			=> __( 'End the video at the last frame instead of showing the first one', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'frontend_available'	=> true,
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				],
			]
		);
        
        $this->add_control(
            'mute',
            [
                'label'					=> __( 'Mute', 'powerpack' ),
                'type'					=> Controls_Manager::SWITCHER,
                'default'				=> '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
				'condition'				=> [
					'media_type'	=> 'video',
				],
            ]
        );

		$this->add_control(
			'playback_speed',
			[
				'label'					=> __( 'Playback Speed', 'powerpack' ),
				'type'					=> Controls_Manager::SLIDER,
				'default'				=> [
					'size' 	=> 1,
				],
				'range'					=> [
					'px' 	=> [
						'max' 	=> 5,
						'min' 	=> 0.1,
						'step' 	=> 0.01,
					],
				],
				'frontend_available'	=> true,
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				],
			]
		);

		$this->add_control(
			'video_controls_heading',
			[
				'label'					=> __( 'Controls', 'powerpack' ),
				'type'					=> Controls_Manager::HEADING,
				'separator'				=> 'before',
				'condition'				=> [
					'media_type'	=> 'video',
				],
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
					'media_type'	=> 'video',
					'video_source'	=> [ 'youtube','dailymotion' ],
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
					'media_type'	=> 'video',
					'video_source'	=> [ 'vimeo', 'dailymotion' ],
				],
			]
		);

		$this->add_control(
			'video_show_buttons',
			[
				'label'                 => __( 'Show Buttons', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'				=> 'show',
				'label_on'				=> __( 'Yes', 'powerpack' ),
				'label_off'				=> __( 'No', 'powerpack' ),
				'return_value'			=> 'show',
				'frontend_available'    => true,
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				],
			]
		);

		$this->add_control(
			'show_bar',
			[
				'label'                 => __( 'Show Bar', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'				=> '',
				'label_on'				=> __( 'Yes', 'powerpack' ),
				'label_off'				=> __( 'No', 'powerpack' ),
				'return_value'			=> 'show',
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				],
			]
		);

		$this->add_control(
			'show_rewind',
			[
				'label'                 => __( 'Show Rewind', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'				=> 'show',
				'label_on'				=> __( 'Yes', 'powerpack' ),
				'label_off'				=> __( 'No', 'powerpack' ),
				'return_value'			=> 'show',
				'condition'				=> [
					'media_type'		=> 'video',
					'video_source'		=> 'hosted',
					'show_bar!'			=> '',
					'restart_on_pause!' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_time',
			[
				'label'                 => __( 'Show Time', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'				=> 'show',
				'label_on'				=> __( 'Yes', 'powerpack' ),
				'label_off'				=> __( 'No', 'powerpack' ),
				'return_value'			=> 'show',
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
					'show_bar!'		=> '',
				]
			]
		);

		$this->add_control(
			'show_progress',
			[
				'label'                 => __( 'Show Progress', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'				=> 'show',
				'label_on'				=> __( 'Yes', 'powerpack' ),
				'label_off'				=> __( 'No', 'powerpack' ),
				'return_value'			=> 'show',
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
					'show_bar!'		=> '',
				]
			]
		);

		$this->add_control(
			'show_duration',
			[
				'label'                 => __( 'Show Duration', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'				=> 'show',
				'label_on'				=> __( 'Yes', 'powerpack' ),
				'label_off'				=> __( 'No', 'powerpack' ),
				'return_value'			=> 'show',
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
					'show_bar!'		=> '',
				]
			]
		);

		$this->add_control(
			'show_fs',
			[
				'label'                 => __( 'Show Fullscreen', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'				=> 'show',
				'label_on'				=> __( 'Yes', 'powerpack' ),
				'label_off'				=> __( 'No', 'powerpack' ),
				'return_value'			=> 'show',
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
					'show_bar!'		=> '',
				],
			]
		);

		$this->add_control(
			'volume_heading',
			[
				'label'					=> __( 'Volume', 'powerpack' ),
				'type'					=> Controls_Manager::HEADING,
				'separator'				=> 'before',
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
					'show_bar!'		=> '',
				],
			]
		);

		$this->add_control(
			'show_volume',
			[
				'label'                 => __( 'Show Volume', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'				=> 'show',
				'label_on'				=> __( 'Yes', 'powerpack' ),
				'label_off'				=> __( 'No', 'powerpack' ),
				'return_value'			=> 'show',
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
					'show_bar!'		=> '',
				],
			]
		);

		$this->add_control(
			'show_volume_icon',
			[
				'label'                 => __( 'Show Volume Icon', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'				=> 'show',
				'label_on'				=> __( 'Yes', 'powerpack' ),
				'label_off'				=> __( 'No', 'powerpack' ),
				'return_value'			=> 'show',
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
					'show_bar!'		=> '',
					'show_volume!'	=> '',
				],
			]
		);

		$this->add_control(
			'show_volume_bar',
			[
				'label'                 => __( 'Show Volume Bar', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'				=> 'show',
				'label_on'				=> __( 'Yes', 'powerpack' ),
				'label_off'				=> __( 'No', 'powerpack' ),
				'return_value'			=> 'show',
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
					'show_bar!'		=> '',
					'show_volume!'	=> '',
				],
			]
		);

        $this->end_controls_section();
	}

	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links('Devices');

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

	protected function register_style_device_controls() {
        /**
         * Style Tab: Device
         */
        $this->start_controls_section(
            'section_device_style',
            [
                'label'                 => __( 'Device', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_responsive_control(
			'device_width',
			[
				'label'                 => __( 'Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px', '%' ],
				'range'                 => [
					'px' => [
                        'min'   => 100,
                        'max'   => 1200,
                        'step'  => 1,
                    ],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-device-container .pp-device-wrap' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
            'override_style',
            [
                'label'                 => __( 'Style', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'predefined',
                'options'               => [
					'predefined'	=> __( 'Predefined', 'powerpack' ),
					'custom'		=> __( 'Custom', 'powerpack' ),
                ],
				'separator'             => 'before',
            ]
        );

        $this->add_control(
            'skin',
            [
                'label'                 => __( 'Choose Skin', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'jet_black',
                'options'               => [
					'jet_black' => __( 'Jet black', 'powerpack' ),
					'black'     => __( 'Black', 'powerpack' ),
					'silver'    => __( 'Silver', 'powerpack' ),
					'gold'      => __( 'Gold', 'powerpack' ),
					'rose_gold' => __( 'Rose Gold', 'powerpack' ),
                ],
				'selectors_dictionary'  => [
					'jet_black'	=> '#000000',
					'black'		=> '#343639',
					'silver'	=> '#e4e6e7',
					'gold'		=> '#fbe6cf',
					'rose_gold'	=> '#fde4dc',
				],
				'selectors'				=> [
					'{{WRAPPER}} .pp-device-body svg .side-shape, {{WRAPPER}} .pp-device-body svg .back-shape' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .pp-device-body svg .overlay-shape' => 'fill: #fff;',
				],
                'condition'             => [
                    'override_style'	=> 'predefined',
                ],
            ]
        );

        $this->add_control(
            'device_color',
            [
                'label'                 => __( 'Device Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-device-body svg .side-shape, {{WRAPPER}} .pp-device-body svg .back-shape' => 'fill: {{VALUE}};',
                ],
                'condition'             => [
                    'override_style'	=> 'custom',
                ],
            ]
        );

        $this->add_control(
            'device_bg_color',
            [
                'label'                 => __( 'Device Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-device-media-inner' => 'background-color: {{VALUE}};',
                ],
                'condition'             => [
                    'override_style'	=> 'custom',
                ],
            ]
        );

        $this->add_control(
            'tone',
            [
                'label'                 => __( 'Tone', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'light',
                'options'               => [
					'dark'  => __( 'Dark', 'powerpack' ),
					'light' => __( 'Light', 'powerpack' ),
                ],
				'selectors_dictionary'  => [
					'dark'	=> '#000000',
					'light'	=> '#ffffff',
				],
				'selectors'				=> [
					'{{WRAPPER}} .pp-device-body svg .overlay-shape' => 'fill: {{VALUE}};',
				],
				'prefix_class'          => 'pp-device-tone-',
                'condition'             => [
                    'override_style'	=> 'custom',
                ],
            ]
        );
        
        $this->add_control(
			'opacity',
			[
				'label'                 => __( 'Opacity', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
                        'min'  => 0.1,
						'max'  => 1,
						'step' => 0.01,
                    ],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-device-body svg .overlay-shape' => 'fill-opacity: {{SIZE}};',
				],
                'condition'             => [
                    'override_style'	=> 'custom',
                ],
			]
		);

		$this->add_control(
			'orientation_control_heading',
			[
				'label'                 => __( 'Orientation Control', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'				=> [
					'orientation_control'	=> 'yes',
				],
			]
		);

        $this->add_control(
            'orientation_control_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-device-orientation' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'orientation_control'	=> 'yes',
                ],
            ]
        );

        $this->add_control(
            'orientation_control_color_hover',
            [
                'label'                 => __( 'Hover Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-device-orientation:hover' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'orientation_control'	=> 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
			'orientation_control_size',
			[
				'label'                 => __( 'Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'size_units'            => [ 'px' ],
				'range'                 => [
					'px' => [
                        'min'   => 0,
                        'max'   => 50,
                        'step'  => 1,
                    ],
				],
				'selectors'         => [
					'{{WRAPPER}} .pp-device-orientation' => 'font-size: {{SIZE}}{{UNIT}};',
				],
                'condition'             => [
                    'orientation_control'	=> 'yes',
                ],
			]
		);
        
        $this->end_controls_section();
	}
	
	protected function register_style_video_overlay_controls() {
        /**
         * Style Tab: Video Overlay
         */
        $this->start_controls_section(
            'section_video_overlay_style',
            [
                'label'                 => __( 'Video Overlay', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'				=> [
					'media_type'	=> 'video',
				]
            ]
        );

        $this->add_control(
            'video_overlay_background_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => 'rgba(0,0,0,0.4)',
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-overlay' => 'background-color: {{VALUE}};',
                ],
				'condition'				=> [
					'media_type'	=> 'video',
				]
            ]
        );
        
        $this->add_control(
			'video_overlay_opacity',
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
					'{{WRAPPER}} .pp-video-overlay' => 'opacity: {{SIZE}};',
				],
				'condition'				=> [
					'media_type'	=> 'video',
				]
			]
		);
        
        $this->end_controls_section();
	}
	
	protected function register_style_video_interface_controls() {
        /**
         * Style Tab: Video Interface
         */
        $this->start_controls_section(
            'section_video_interface_style',
            [
                'label'                 => __( 'Video Interface', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				]
            ]
        );

        $this->start_controls_tabs( 'tabs_video_interface_style' );

        $this->start_controls_tab(
            'tab_video_interface_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				]
            ]
        );

        $this->add_control(
            'video_interface_color',
            [
                'label'                 => __( 'Controls Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-player-control' => 'color: {{VALUE}};',
                ],
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				]
            ]
        );

        $this->add_control(
            'video_interface_background_color',
            [
                'label'                 => __( 'Controls Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-player-controls-bar' => 'background-color: {{VALUE}};',
                ],
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				]
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_video_interface_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				]
            ]
        );

        $this->add_control(
            'video_interface_color_hover',
            [
                'label'                 => __( 'Controls Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-player-control:hover' => 'color: {{VALUE}};',
                ],
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				]
            ]
        );

        $this->add_control(
            'video_interface_background_color_hover',
            [
                'label'                 => __( 'Controls Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-player-controls-bar:hover' => 'background-color: {{VALUE}};',
                ],
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				]
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();
        
        $this->end_controls_section();
	}
	
	protected function register_style_video_buttons_controls() {
        /**
         * Style Tab: Video Buttons
         */
        $this->start_controls_section(
            'section_video_buttons_style',
            [
                'label'                 => __( 'Video Buttons', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'				=> [
					'media_type'	=> 'video',
				]
            ]
        );

		$this->add_responsive_control(
			'video_buttons_size',
			[
				'label'					=> __( 'Size', 'powerpack' ),
				'type'					=> Controls_Manager::SLIDER,
				'default'				=> [
					'size' 	=> '',
				],
				'range'					=> [
					'px' 	=> [
						'min' 	=> 10,
						'max' 	=> 50,
						'step' 	=> 1,
					],
				],
				'selectors'         	=> [
					'{{WRAPPER}} .pp-video-button:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'				=> [
					'media_type'	=> 'video',
				],
			]
		);

		$this->add_responsive_control(
			'video_buttons_spacing',
			[
				'label'					=> __( 'Spacing', 'powerpack' ),
				'type'					=> Controls_Manager::SLIDER,
				'default'				=> [
					'size' 	=> '',
				],
				'range'					=> [
					'px' 	=> [
						'min' 	=> 0,
						'max' 	=> 50,
						'step' 	=> 1,
					],
				],
				'selectors'         	=> [
					'{{WRAPPER}} .pp-video-button' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition'				=> [
					'media_type'	=> 'video',
					'video_source'	=> 'hosted',
				],
			]
		);

		$this->start_controls_tabs(
			'tabs_video_buttons',
			[
				'condition'				=> [
					'media_type'	=> 'video',
				],	
			]
		);

		$this->start_controls_tab(
			'tab_video_buttons_normal',
			[
				'label'					=> __( 'Normal', 'powerpack' ),
				'condition'             => [
					'media_type'	=> 'video',
				],
			]
		);

		$this->add_control(
			'video_buttons_color',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-buttons .pp-video-button' => 'color: {{VALUE}};',
                ],
				'condition'             => [
					'media_type'	=> 'video',
				],
			]
		);

		$this->add_control(
			'video_buttons_bg_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-buttons .pp-video-button' => 'background-color: {{VALUE}};',
                ],
				'condition'             => [
					'media_type'	=> 'video',
				],
			]
		);
        
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'					=> 'video_buttons_border',
				'label'					=> __( 'Border', 'powerpack' ),
				'placeholder'			=> '1px',
				'default'				=> '',
				'selector'				=> '{{WRAPPER}} .pp-video-button',
				'condition'				=> [
					'media_type'	=> 'video',
				]
			]
		);
        
        $this->add_control(
			'video_buttons_border_radius',
			[
				'label'					=> __( 'Border Radius', 'powerpack' ),
				'type'					=> Controls_Manager::DIMENSIONS,
				'size_units'			=> [ 'px', '%' ],
				'selectors'				=> [
					'{{WRAPPER}} .pp-video-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'				=> [
					'media_type'	=> 'video',
				]
			]
		);

		$this->add_responsive_control(
			'video_buttons_padding',
			[
				'label'					=> __( 'Padding', 'powerpack' ),
				'type'					=> Controls_Manager::DIMENSIONS,
				'size_units'			=> [ 'px', 'em' ],
				'default'           => [
                    'top'       => '1',
                    'right'     => '1',
                    'bottom'    => '1',
                    'left'      => '1',
                    'unit'      => 'em',
                    'isLinked'  => true,
                ],
				'selectors'				=> [
					'{{WRAPPER}} .pp-video-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'				=> [
					'media_type'	=> 'video',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_video_buttons_hover',
			[
				'label'					=> __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'video_buttons_color_hover',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-buttons .pp-video-button:hover' => 'color: {{VALUE}};',
                ],
				'condition'             => [
					'media_type'	=> 'video',
				],
			]
		);

		$this->add_control(
			'video_buttons_bg_color_hover',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-buttons .pp-video-button:hover' => 'background-color: {{VALUE}};',
                ],
				'condition'             => [
					'media_type'	=> 'video',
				],
			]
		);

		$this->add_control(
			'video_buttons_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-video-buttons .pp-video-button:hover' => 'border-color: {{VALUE}};',
                ],
				'condition'             => [
					'media_type'	=> 'video',
				],
			]
		);

		$this->end_controls_tab();
		
		$this->end_controls_tabs();
        
        $this->end_controls_section();
    }

	protected function render_image() {
		$settings = $this->get_settings_for_display();

		if ( '' !== $settings['image']['url'] ) { ?>
			<figure><?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'image' ); ?></figure>
		<?php }
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
    
    protected function render_video() {
        $settings = $this->get_settings_for_display();

		if ( $settings['video_source'] == 'hosted' ) {
			
			$this->render_video_hosted();
			echo $this->render_video_overlay();
			$this->render_controls();

		} else {
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

			$this->add_render_attribute( 'video-container', 'class', ['pp-video-container'] );
			$this->add_render_attribute( 'video-play', 'class', 'pp-video-play' );
			$this->add_render_attribute( 'video-player', 'class', 'pp-video-player' );
			$this->add_render_attribute( 'video', 'class', 'pp-video' );

			$pp_gallery_settings['widget_id'] = $this->get_id();

			$this->add_render_attribute( 'video', 'data-settings', wp_json_encode( $pp_gallery_settings ) );

			$embed_params = $this->get_embed_params();
			$embed_options = $this->get_embed_options();

			$video_url = Embed::get_embed_url( $video_url_src, $embed_params, $embed_options );

			$this->add_render_attribute( 'video-player', 'data-src', $video_url );

			$autoplay = ( 'yes' == $settings['autoplay'] ) ? '1' : '0';

			$this->add_render_attribute( 'video-play', 'data-autoplay', $autoplay );
			?>
			<div <?php echo $this->get_render_attribute_string( 'video' ); ?>>
				<div <?php echo $this->get_render_attribute_string( 'video-container' ); ?>>
					<div <?php echo $this->get_render_attribute_string( 'video-play' ); ?>>
						<div <?php echo $this->get_render_attribute_string( 'video-player' ); ?>>
							<img class="pp-video-thumb" src="<?php echo esc_url( $this->get_video_thumbnail( $thumb_size ) ); ?>">
							<?php $this->render_video_buttons(); ?>
						</div>
					</div>
				</div>
			</div>
			<?php

			// Video Overlay
			echo $this->render_video_overlay();
		}
    }

	protected function render_video_hosted() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'video', [
			'class' => [
				'pp-video-player-source'
			],
			'playsinline' => 'true',
			'webkit-playsinline' => 'true',
			'width' => '100%',
			'height' => '100%',
		] );

		if ( 'yes' === $settings['autoplay'] ) {
			$this->add_render_attribute( 'video', 'autoplay', 'true' );
		}

		if ( 'yes' === $settings['mute'] ) {
			$this->add_render_attribute( 'video', 'muted', 'true' );
		}

		if ( 'yes' === $settings['loop'] ) {
			$this->add_render_attribute( 'video', 'loop', 'true' );
		}

		if ( ! empty( $settings['cover_image']['url'] ) ) {
			$url = Group_Control_Image_Size::get_attachment_image_src( $settings['cover_image']['id'], 'cover_image', $settings );
			$this->add_render_attribute( 'video', 'poster', $url );
		}
		?>
		<div class="pp-video-player">
			<video <?php echo $this->get_render_attribute_string( 'video' ); ?>><?php

				$video_url = ( 'file' === $settings['video_source_mp4'] ) ? $settings['video_file_mp4']['url'] : $settings['video_url_mp4'];
				$video_url_m4v = ( 'file' === $settings['video_source_m4v'] ) ? $settings['video_file_m4v']['url'] : $settings['video_url_m4v'];
				$video_url_ogg = ( 'file' === $settings['video_source_ogg'] ) ? $settings['video_file_ogg']['url'] : $settings['video_url_ogg'];
				$video_url_webm = ( 'file' === $settings['video_source_webm'] ) ? $settings['video_file_webm']['url'] : $settings['video_url_webm'];

				if ( $video_url ) {
					$this->add_render_attribute( 'source-mp4', [
						'src' => $video_url,
						'type' => 'video/mp4',
					] );
				?><source <?php echo $this->get_render_attribute_string( 'source-mp4' ); ?>><?php } ?>

				<?php if ( $video_url_m4v ) {
					$this->add_render_attribute( 'source-m4v', [
						'src' => $video_url_m4v,
						'type' => 'video/m4v',
					] );
				?><source <?php echo $this->get_render_attribute_string( 'source-m4v' ); ?>><?php } ?>

				<?php if ( $video_url_ogg ) {
					$this->add_render_attribute( 'source-ogg', [
						'src' => $video_url_ogg,
						'type' => 'video/ogg',
					] );
				?><source <?php echo $this->get_render_attribute_string( 'source-wav' ); ?>><?php } ?>

				<?php if ( $video_url_webm ) {
					$this->add_render_attribute( 'source-webm', [
						'src' => $video_url_webm,
						'type' => 'video/webm',
					] );
				?><source <?php echo $this->get_render_attribute_string( 'source-webm' ); ?>><?php } ?>

			</video>
		</div><?php
	}

	protected function render_video_cover() {
		$settings = $this->get_settings_for_display();
		?>
		<div class="pp-video-player-cover pp-player-cover">
		</div>
		<?php
	}

	protected function render_video_buttons() {
		$settings = $this->get_settings_for_display();
		
		$this->add_render_attribute( 'play-icon', [
			'class' => ['pp-video-button', 'pp-player-controls-play', 'fa fa-play', 'pp-play'],
			'title' => __( 'Play / Pause', 'powerpack' )
		] );
		
		if ( $settings['video_source'] == 'hosted' && $settings['video_show_buttons'] == 'show' ) {
			$show_buttons = true;
		} elseif ( $settings['video_source'] != 'hosted' ) {
			$show_buttons = true;
		} else {
			$show_buttons = false;
		}
		
		if ( $show_buttons ) {
		?>
		<div class="pp-player-controls-overlay pp-video-player-overlay">
			<div class="pp-video-buttons">
				<?php if ( $settings['video_source'] == 'hosted' ) { ?>
				<i class="fa fa-redo pp-player-controls-rewind pp-video-button" title="<?php echo __( 'Rewind', 'powerpack' ); ?>"></i>
				<?php } ?>

				<i <?php echo $this->get_render_attribute_string( 'play-icon' ); ?>></i>
			</div>
		</div>
		<?php
		}
	}

	protected function render_controls() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
			'controls' => [
				'class' => [
					'pp-video-player-controls',
				],
			],
			'bar-wrapper' => [
				'class' => [
					'pp-video-player-controls-bar-wrapper',
				],
			],
			'bar' => [
				'class' => [
					'pp-player-controls-bar',
				],
			],
			'control-play' => [
				'class' => [
					'pp-player-control',
					'pp-player-controls-play',
					'pp-player-control-icon',
					'fa',
					'fa-play',
				],
			],
		] );

		?>
		<div <?php echo $this->get_render_attribute_string( 'controls' ); ?>><?php

			$this->render_video_buttons();

			if ( 'show' === $settings['show_bar'] ) {

				?><div <?php echo $this->get_render_attribute_string( 'bar-wrapper' ); ?>>
					<div <?php echo $this->get_render_attribute_string( 'bar' ); ?>>

						<?php if ( 'yes' !== $settings['restart_on_pause'] && 'show' === $settings['show_rewind'] ) {
							$this->add_render_attribute( 'control-rewind', [
								'class' => [
									'pp-player-control',
									'pp-player-controls-rewind',
									'pp-player-control-icon',
									'fa',
									'fa-redo',
								],
							] );
						?><div <?php echo $this->get_render_attribute_string( 'control-rewind' ); ?>></div><?php } ?>
						
						<div <?php echo $this->get_render_attribute_string( 'control-play' ); ?>></div>

						<?php if ( $settings['show_time'] ) {
							$this->add_render_attribute( 'control-time', [
								'class' => [
									'pp-player-control',
									'pp-player-control-indicator',
									'pp-player-controls-time',
								],
							] );
						?><div <?php echo $this->get_render_attribute_string( 'control-time' ); ?>>00:00</div><?php } ?>

						<?php if ( $settings['show_progress'] ) {
							$this->add_render_attribute( [
								'control-progress' => [
									'class' => [
										'pp-player-control',
										'pp-player-controls-progress',
										'pp-player-control-progress',
									],
								],
								'control-progress-time' => [
									'class' => [
										'pp-player-controls-progress-time',
										'pp-player-control-progress-inner',
									],
								],
								'control-progress-track' => [
									'class' => [
										'pp-player-control-progress-inner',
										'pp-player-control-progress-track',
									],
								],
							] );
						?><div <?php echo $this->get_render_attribute_string( 'control-progress' ); ?>>
							<div <?php echo $this->get_render_attribute_string( 'control-progress-time' ); ?>></div>
							<div <?php echo $this->get_render_attribute_string( 'control-progress-track' ); ?>></div>
						</div><?php } ?>

						<?php if ( $settings['show_duration'] ) {
							$this->add_render_attribute( 'control-duration', [
								'class' => [
									'pp-player-control',
									'pp-player-controls-duration',
									'pp-player-control-indicator',
								],
							] );
						?><div <?php echo $this->get_render_attribute_string( 'control-duration' ); ?>>00:00</div><?php } ?>

						<?php if ( $settings['show_volume'] ) {
							$this->add_render_attribute( 'control-volume', [
								'class' => [
									'pp-player-control',
									'pp-player-controls-volume',
								],
							] );
						?><div <?php echo $this->get_render_attribute_string( 'control-volume' ); ?>>

							<?php if ( $settings['show_volume_icon'] ) {
								if ( $settings['mute'] == 'yes' ) {
									$vol_icon = 'fa-volume-mute';
								} else {
									$vol_icon = 'fa-volume-up';
								}
							
								$this->add_render_attribute( 'control-volume-icon', [
									'class' => [
										'pp-player-controls-volume-icon',
										'pp-player-control-icon',
										'fa',
										$vol_icon
									],
								] );
							?><div <?php echo $this->get_render_attribute_string( 'control-volume-icon' ); ?>></div><?php } ?>

							<?php if ( $settings['show_volume_bar'] ) {
								$this->add_render_attribute( [
									'control-volume-bar' => [
										'class' => [
											'pp-player-control',
											'pp-player-controls-volume-bar',
											'pp-player-controls-progress',
										],
									],
									'control-volume-bar-amount' => [
										'class' => [
											'pp-player-controls-volume-bar-amount',
											'pp-player-control-progress-inner',
										],
									],
									'control-volume-bar-track' => [
										'class' => [
											'pp-player-controls-volume-bar-track',
											'pp-player-control-progress-inner',
											'pp-player-controls-progress-track',
										],
									],
								] );
							?><div <?php echo $this->get_render_attribute_string( 'control-volume-bar' ); ?>>
								<div <?php echo $this->get_render_attribute_string( 'control-volume-bar-amount' ); ?>></div>
								<div <?php echo $this->get_render_attribute_string( 'control-volume-bar-track' ); ?>></div>
							</div><?php } ?>

						</div><?php } ?>

						<?php if ( $settings['show_fs'] ) {
							$this->add_render_attribute( 'control-fullscreen', [
								'class' => [
									'pp-player-control',
									'pp-player-controls-fs',
									'pp-player-control-icon',
									'fa',
									'fa-expand',
								],
							] );
						?><div <?php echo $this->get_render_attribute_string( 'control-fullscreen' ); ?>></div><?php } ?>

					</div>
				</div>
			<?php } ?>
		</div>
		<?php
	}
    
    protected function render_video_overlay() {
        $this->add_render_attribute( 'overlay', 'class', [
            'pp-image-overlay',
            'pp-video-overlay',
		] );
		
        return '<div ' . $this->get_render_attribute_string( 'overlay' ) . '></div>';
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
        
        if ( $settings['cover_image_show'] == 'yes' && $settings['cover_image']['url'] ) {

            $thumb_url = $settings['cover_image']['url'];
			
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
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
		$this->add_render_attribute( [
			'device-container' => [
				'class' => [
					'pp-device-container',
					'pp-device-type-' . $settings['device_type']
				],
			],
			'device-wrap' => [
				'class' => [
					'pp-device-wrap'
				],
			],
			'device' => [
				'class' => [
					'pp-device'
				],
			],
			'screen' => [
				'class' => [
					'pp-device-screen',
					'pp-device-screen-' . $settings['media_type']
				],
			],
		] );
		
		if ( 'show' !== $settings['video_show_buttons'] ) {
			$this->add_render_attribute( 'screen', 'class', 'pp-device-screen-play' );
		}
		
		if ( $settings['device_type'] == 'phone' || $settings['device_type'] == 'tablet' ) {
			$this->add_render_attribute( 'device-container', 'class', 'pp-device-orientation-' . $settings['orientation'] );
		}
		
		if ( 'yes' === $settings['orientation_control'] ) {
			$this->add_render_attribute( 'device', 'class', 'pp-has-orientation-control' );
		}
		
		if ( 'yes' === $settings['scrollable'] ) {
			$this->add_render_attribute( 'device', 'class', 'pp-scrollable' );
		}
        ?>
		<div <?php echo $this->get_render_attribute_string( 'device-container' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'device-wrap' ); ?>>
				<div <?php echo $this->get_render_attribute_string( 'device' ); ?>>
					<div class="pp-device-body">
						<?php require POWERPACK_ELEMENTS_PATH . 'assets/images/devices/' . $settings['device_type'] . '.svg'; ?>
					</div>
					<div class="pp-device-media">
						<div class="pp-device-media-inner">
							<div <?php echo $this->get_render_attribute_string( 'screen' ); ?>>
								<?php if ( 'image' === $settings['media_type'] ) { ?>
									<?php $this->render_image(); ?>
								<?php } elseif ( 'video' === $settings['media_type'] ) { ?>
									<?php $this->render_video(); ?>
								<?php } ?>
							</div>
						</div>
					</div>
					<?php if ( 'yes' === $settings['orientation_control'] ) { ?>
						<?php
							$this->add_render_attribute( 'device-icon', [
								'class' => 'fas fa-mobile',
								'aria-hidden' => 'true',
							]);
			
							$this->add_render_attribute( 'device-icon', 'class', 'pp-mobile-icon-' . $settings['orientation'] );
						?>
						<div class="pp-device-orientation">
							<i <?php echo $this->get_render_attribute_string( 'device-icon' ); ?>></i>
						</div>
					<?php } ?>
				</div>
			</div>
        </div>
        <?php
    }

    protected function _content_template() {
    }
}