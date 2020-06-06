<?php
namespace PowerpackElements\Modules\Gallery\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Modules\Gallery\Module;
use PowerpackElements\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Control_Media;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Scheme_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Image Slider Widget
 */
class Image_Slider extends Powerpack_Widget {
    
    /**
	 * Retrieve image slider widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return parent::get_widget_name( 'Image_Slider' );
    }

    /**
	 * Retrieve image slider widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
    public function get_title() {
        return parent::get_widget_title( 'Image_Slider' );
    }

    /**
	 * Retrieve the list of categories the image slider widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
    public function get_categories() {
        return parent::get_widget_categories( 'Image_Slider' );
    }

    /**
	 * Retrieve image slider widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
    public function get_icon() {
        return parent::get_widget_icon( 'Image_Slider' );
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
		return parent::get_widget_keywords( 'Image_Slider' );
	}
    
    /**
	 * Retrieve the list of scripts the image slider widget depended on.
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
            'jquery-resize',
			'jquery-slick',
            'powerpack-frontend'
        ];
    }
    
    /**
	 * Retrieve the list of styles the image slider widget depended on.
	 *
	 * Used to set styles dependencies required to run the widget.
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
	 * Register image slider widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
    protected function _register_controls() {
		
		/* Content Tab */
		$this->register_content_gallery_controls();
		$this->register_content_thumbnails_controls();
		$this->register_content_feature_image_controls();
		$this->register_content_additional_options_controls();
		$this->register_content_help_docs_controls();
		
		/* Style Tab */
		$this->register_style_feature_image_controls();
		$this->register_style_image_captions_controls();
		$this->register_style_thumbnails_controls();
		$this->register_style_thumbnails_captions_controls();
		$this->register_style_arrows_controls();
		$this->register_style_dots_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/
        
	protected function register_content_gallery_controls() {
        /**
         * Content Tab: Gallery
         */
        $this->start_controls_section(
            'section_gallery',
            [
                'label'                 => __( 'Gallery', 'powerpack' ),
            ]
        );
        
        $this->add_control(
            'gallery_images',
            [
                'label'                 => __( 'Add Images', 'powerpack' ),
                'type'                  => Controls_Manager::GALLERY,
                'dynamic'               => [
                    'active' => true
                ],
            ]
        );

		$this->add_control(
			'effect',
			[
				'type'                  => Controls_Manager::SELECT,
				'label'                 => __( 'Effect', 'powerpack' ),
				'default'               => 'slide',
				'options'               => [
					'slide'    => __( 'Slide', 'powerpack' ),
					'fade'     => __( 'Fade', 'powerpack' ),
				],
				'separator'             => 'before',
				'frontend_available'    => true,
			]
		);

		$this->add_control(
			'skin',
			[
				'label'                 => __( 'Layout', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'slideshow',
				'options'               => [
					'slideshow'    => __( 'Slideshow', 'powerpack' ),
					'carousel'     => __( 'Carousel', 'powerpack' ),
				],
				'prefix_class'          => 'pp-image-slider-',
				'render_type'           => 'template',
				'frontend_available'    => true,
			]
		);

		$slides_per_view = range( 1, 10 );
		$slides_per_view = array_combine( $slides_per_view, $slides_per_view );

		$this->add_responsive_control(
			'slides_per_view',
			[
				'type'                  => Controls_Manager::SELECT,
				'label'                 => __( 'Slides Per View', 'powerpack' ),
				'options'               => $slides_per_view,
				'default'               => '3',
				'tablet_default'        => '2',
				'mobile_default'        => '2',
				'condition'             => [
					'effect'   => 'slide',
					'skin!'    => 'slideshow',
				],
				'frontend_available'    => true,
			]
		);

		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'type'                  => Controls_Manager::SELECT,
				'label'                 => __( 'Slides to Scroll', 'powerpack' ),
				'description'           => __( 'Set how many slides are scrolled per swipe.', 'powerpack' ),
				'options'               => $slides_per_view,
				'default'               => 1,
				'tablet_default'        => 1,
				'mobile_default'        => 1,
				'condition'             => [
					'effect'   => 'slide',
					'skin!'    => 'slideshow',
				],
				'frontend_available'    => true,
			]
		);

        $this->end_controls_section();
	}

	protected function register_content_thumbnails_controls() {
        /**
         * Content Tab: Thumbnails
         */
        $this->start_controls_section(
            'section_thumbnails_settings',
            [
                'label'                 => __( 'Thumbnails', 'powerpack' ),
            ]
        );
        
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'                  => 'thumbnail',
                'label'                 => __( 'Image Size', 'powerpack' ),
                'default'               => 'thumbnail',
                'exclude'               => [ 'custom' ],
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label'                 => __( 'Columns', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => '3',
                'tablet_default'        => '6',
                'mobile_default'        => '4',
                'options'               => [
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                    '7' => '7',
                    '8' => '8',
                    '9' => '9',
                    '10' => '10',
                    '11' => '11',
                    '12' => '12',
                ],
                'prefix_class'          => 'elementor-grid%s-',
                'frontend_available'    => true,
                'condition'             => [
					'skin'     => 'slideshow',
				],
            ]
        );

		$this->add_control(
			'thumbnails_caption',
			[
				'type'                  => Controls_Manager::SELECT,
				'label'                 => __( 'Caption', 'powerpack' ),
				'default'               => '',
				'options'               => [
					''				=> __( 'None', 'powerpack' ),
					'caption'		=> __( 'Caption', 'powerpack' ),
					'title'			=> __( 'Title', 'powerpack' ),
                    'description'	=> __( 'Description', 'powerpack' ),
				],
			]
		);

        $this->add_control(
            'carousel_link_to',
            [
                'label'                 => __( 'Link to', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'none',
                'options'               => [
                    'none' 		=> __( 'None', 'powerpack' ),
                    'file' 		=> __( 'Media File', 'powerpack' ),
                    'custom' 	=> __( 'Custom URL', 'powerpack' ),
                ],
                'condition'             => [
					'skin'      => 'carousel',
                ],
            ]
        );
		
		$this->add_control(
			'carousel_link_important_note',
			[
				'label'					=> '',
				'type'					=> Controls_Manager::RAW_HTML,
				'raw'					=> __( 'Add custom link in media uploader.', 'powerpack' ),
				'content_classes'		=> 'pp-editor-info',
                'condition'				=> [
					'skin'              => 'carousel',
                    'carousel_link_to'  => 'custom',
                ],
			]
		);

        $this->add_control(
            'carousel_link_target',
            [
                'label'                 => __( 'Link Target', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => '_blank',
                'options'               => [
                    '_self' 		=> __( 'Same Window', 'powerpack' ),
                    '_blank'		=> __( 'New Window', 'powerpack' ),
                ],
				'conditions'			=> [
					'relation'	=> 'and',
					'terms'		=> [
						[
							'name'		=> 'skin',
							'operator' 	=> '==',
							'value'		=> 'carousel',
						],
						[
							'relation'	=> 'or',
							'terms'		=> [
								[
									'name'		=> 'carousel_link_to',
									'operator' 	=> '==',
									'value'		=> 'custom',
								],
								[
									'relation'	=> 'and',
									'terms'		=> [
										[
											'name'		=> 'carousel_link_to',
											'operator' 	=> '==',
											'value'		=> 'file',
										],
										[
											'name'		=> 'carousel_open_lightbox',
											'operator' 	=> '==',
											'value'		=> 'no',
										],
									],
								]
							]
						]
					]
				]
            ]
        );

        $this->add_control(
            'carousel_open_lightbox',
            [
                'label'                 => __( 'Lightbox', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'default',
                'options'               => [
                    'default' 	=> __( 'Default', 'powerpack' ),
                    'yes' 		=> __( 'Yes', 'powerpack' ),
                    'no' 		=> __( 'No', 'powerpack' ),
                ],
                'separator'             => 'before',
                'condition'             => [
					'skin'              => 'carousel',
                    'carousel_link_to'  => 'file',
                ],
            ]
        );

        $this->add_control(
            'carousel_lightbox_library',
            [
                'label'                 => __( 'Lightbox Library', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => '',
                'options'               => [
                    ''          => __( 'Elementor', 'powerpack' ),
                    'fancybox'  => __( 'Fancybox', 'powerpack' ),
                ],
                'condition'             => [
					'skin'                      => 'carousel',
                    'carousel_link_to'          => 'file',
                    'carousel_open_lightbox!'   => 'no',
                ],
            ]
        );

		$this->add_control(
			'thumbnails_lightbox_caption',
			[
				'type'                  => Controls_Manager::SELECT,
				'label'                 => __( 'Lightbox Caption', 'powerpack' ),
				'default'               => '',
				'options'               => [
					''         => __( 'None', 'powerpack' ),
					'caption'  => __( 'Caption', 'powerpack' ),
					'title'    => __( 'Title', 'powerpack' ),
				],
                'condition'             => [
					'skin'                      => 'carousel',
                    'carousel_link_to'          => 'file',
                    'carousel_open_lightbox!'   => 'no',
                    'carousel_lightbox_library' => 'fancybox',
				],
			]
		);
        
        $this->end_controls_section();
	}

	protected function register_content_feature_image_controls() {
        /**
         * Content Tab: Feature Image
         */
        $this->start_controls_section(
            'section_feature_image',
            [
                'label'                 => __( 'Feature Image', 'powerpack' ),
                'condition'             => [
					'skin'     => 'slideshow',
				],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'                  => 'image',
                'label'                 => __( 'Image Size', 'powerpack' ),
                'default'               => 'full',
                'exclude'               => [ 'custom' ],
                'condition'             => [
					'skin'     => 'slideshow',
				],
            ]
        );

		$this->add_control(
			'feature_image_caption',
			[
				'type'                  => Controls_Manager::SELECT,
				'label'                 => __( 'Caption', 'powerpack' ),
				'default'               => '',
				'options'               => [
					''				=> __( 'None', 'powerpack' ),
					'caption'		=> __( 'Caption', 'powerpack' ),
					'title'			=> __( 'Title', 'powerpack' ),
                    'description'	=> __( 'Description', 'powerpack' ),
				],
                'condition'             => [
					'skin'     => 'slideshow',
				],
			]
		);

        $this->add_control(
            'link_to',
            [
                'label'                 => __( 'Link to', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'none',
                'options'               => [
                    'none' 		=> __( 'None', 'powerpack' ),
                    'file' 		=> __( 'Media File', 'powerpack' ),
                    'custom' 	=> __( 'Custom URL', 'powerpack' ),
                ],
                'condition'             => [
					'skin'      => 'slideshow',
				],
            ]
        );
		
		$this->add_control(
			'link_important_note',
			[
				'label'					=> '',
				'type'					=> Controls_Manager::RAW_HTML,
				'raw'					=> __( 'Add custom link in media uploader.', 'powerpack' ),
				'content_classes'		=> 'pp-editor-info',
                'condition'				=> [
					'skin'		=> 'slideshow',
                    'link_to'	=> 'custom',
                ],
			]
		);

        $this->add_control(
            'link_target',
            [
                'label'                 => __( 'Link Target', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => '_blank',
                'options'               => [
                    '_self' 		=> __( 'Same Window', 'powerpack' ),
                    '_blank'		=> __( 'New Window', 'powerpack' ),
                ],
				'conditions'			=> [
					'relation'	=> 'and',
					'terms'		=> [
						[
							'name'		=> 'skin',
							'operator' 	=> '==',
							'value'		=> 'slideshow',
						],
						[
							'relation'	=> 'or',
							'terms'		=> [
								[
									'name'		=> 'link_to',
									'operator' 	=> '==',
									'value'		=> 'custom',
								],
								[
									'relation'	=> 'and',
									'terms'		=> [
										[
											'name'		=> 'link_to',
											'operator' 	=> '==',
											'value'		=> 'file',
										],
										[
											'name'		=> 'open_lightbox',
											'operator' 	=> '==',
											'value'		=> 'no',
										],
									],
								]
							]
						]
					]
				]
            ]
        );

        $this->add_control(
            'open_lightbox',
            [
                'label'                 => __( 'Lightbox', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'default',
                'options'               => [
                    'default' 	=> __( 'Default', 'powerpack' ),
                    'yes' 		=> __( 'Yes', 'powerpack' ),
                    'no' 		=> __( 'No', 'powerpack' ),
                ],
				'separator'             => 'before',
                'condition'             => [
					'skin'      => 'slideshow',
                    'link_to'   => 'file',
                ],
            ]
        );

        $this->add_control(
            'lightbox_library',
            [
                'label'                 => __( 'Lightbox Library', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => '',
                'options'               => [
                    ''          => __( 'Elementor', 'powerpack' ),
                    'fancybox'  => __( 'Fancybox', 'powerpack' ),
                ],
                'condition'             => [
					'skin'              => 'slideshow',
                    'link_to'           => 'file',
                    'open_lightbox!'    => 'no',
                ],
            ]
        );

		$this->add_control(
			'feature_image_lightbox_caption',
			[
				'type'                  => Controls_Manager::SELECT,
				'label'                 => __( 'Lightbox Caption', 'powerpack' ),
				'default'               => '',
				'options'               => [
					''         => __( 'None', 'powerpack' ),
					'caption'  => __( 'Caption', 'powerpack' ),
					'title'    => __( 'Title', 'powerpack' ),
				],
                'condition'             => [
					'skin'              => 'slideshow',
                    'link_to'           => 'file',
                    'open_lightbox!'    => 'no',
                    'lightbox_library'  => 'fancybox',
				],
			]
		);
        
        $this->end_controls_section();
	}

	protected function register_content_additional_options_controls() {
        /**
         * Content Tab: Additional Options
         */
        $this->start_controls_section(
            'section_additional_options',
            [
                'label'                 => __( 'Additional Options', 'powerpack' ),
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

		$this->end_controls_section();
	}

	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links('Image_Slider');

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

	protected function register_style_feature_image_controls() {
        /**
         * Style Tab: Feature Image
         */
        $this->start_controls_section(
            'section_feature_image_style',
            [
                'label'                 => __( 'Feature Image', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'skin'     => 'slideshow',
                ],
            ]
        );
        
        $this->add_control(
			'feature_image_align',
			[
                'label'                 => __( 'Align', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'label_block'           => false,
                'toggle'                => false,
                'default'               => 'left',
                'options'               => [
                    'left'          => [
                        'title'     => __( 'Left', 'powerpack' ),
                        'icon'      => 'eicon-h-align-left',
                    ],
                    'top'           => [
                        'title'     => __( 'Top', 'powerpack' ),
                        'icon'      => 'eicon-v-align-top',
                    ],
                    'right'         => [
                        'title'     => __( 'Right', 'powerpack' ),
                        'icon'      => 'eicon-h-align-right',
                    ],
                ],
                'prefix_class'          => 'pp-image-slider-align-',
                'frontend_available'    => true,
                'condition'             => [
					'skin'     => 'slideshow',
				],
			]
		);
        
        $this->add_control(
            'feature_image_stack',
            [
                'label'                 => __( 'Stack On', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'tablet',
                'options'               => [
                    'tablet' 	=> __( 'Tablet', 'powerpack' ),
                    'mobile' 	=> __( 'Mobile', 'powerpack' ),
                ],
                'prefix_class'          => 'pp-image-slider-stack-',
                'condition'             => [
					'skin'                 => 'slideshow',
					'feature_image_align!' => 'top',
				],
            ]
        );

        $this->add_responsive_control(
            'feature_image_width',
            [
                'label'                 => __( 'Width', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'size_units'            => [ '%' ],
                'range'                 => [
                    '%' 	=> [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'               => [
                    'size' 	=> 70,
                ],
                'selectors'             => [
                    '{{WRAPPER}}.pp-image-slider-align-left .pp-image-slider-wrap' => 'width: {{SIZE}}%',
                    '{{WRAPPER}}.pp-image-slider-align-right .pp-image-slider-wrap' => 'width: {{SIZE}}%',
                    '{{WRAPPER}}.pp-image-slider-align-right .pp-image-slider-thumb-pagination' => 'width: calc(100% - {{SIZE}}%)',
                    '{{WRAPPER}}.pp-image-slider-align-left .pp-image-slider-thumb-pagination' => 'width: calc(100% - {{SIZE}}%)',
                ],
                'condition'             => [
                    'skin'     => 'slideshow',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'feature_image_spacing',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' 	=> [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'default'               => [
                    'size' 	=> 20,
                ],
                'selectors'             => [
                    '{{WRAPPER}}.pp-image-slider-align-left .pp-image-slider-container,
                    {{WRAPPER}}.pp-image-slider-align-right .pp-image-slider-container' => 'margin-left: -{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.pp-image-slider-align-left .pp-image-slider-container > *,
                    {{WRAPPER}}.pp-image-slider-align-right .pp-image-slider-container > *' => 'padding-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.pp-image-slider-align-top .pp-image-slider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.pp-image-slider-stack-tablet .pp-image-slider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.pp-image-slider-stack-mobile .pp-image-slider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition'             => [
                    'skin'     => 'slideshow',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'feature_image_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-image-slider',
				'separator'             => 'before',
                'condition'             => [
                    'skin'     => 'slideshow',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'feature_image_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-image-slider',
				'separator'             => 'before',
                'condition'             => [
                    'skin'     => 'slideshow',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'                  => 'feature_image_css_filters',
				'selector'              => '{{WRAPPER}} .pp-image-slider img',
                'condition'             => [
                    'skin'     => 'slideshow',
                ],
			]
		);

        $this->end_controls_section();
	}

	protected function register_style_image_captions_controls() {
        /**
         * Style Tab: Feature Image Captions
         */
        $this->start_controls_section(
            'section_feature_image_captions_style',
            [
                'label'                 => __( 'Feature Image Captions', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'feature_image_captions_vertical_align',
            [
                'label'                 => __( 'Vertical Align', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
                    'top' 	=> [
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
                ],
                'default'               => 'bottom',
				'selectors' => [
					'{{WRAPPER}} .pp-image-slider-slide .pp-image-slider-content' => 'justify-content: {{VALUE}};',
				],
				'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'bottom'   => 'flex-end',
					'middle'   => 'center',
				],
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'feature_image_captions_horizontal_align',
            [
                'label'                 => __( 'Horizontal Align', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
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
                    'justify' 		=> [
                        'title' 	=> __( 'Justify', 'powerpack' ),
                        'icon' 		=> 'eicon-h-align-stretch',
                    ],
                ],
                'default'               => 'left',
				'selectors' => [
					'{{WRAPPER}} .pp-image-slider-slide .pp-image-slider-content' => 'align-items: {{VALUE}};',
				],
				'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'right'    => 'flex-end',
					'center'   => 'center',
					'justify'  => 'stretch',
				],
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'feature_image_captions_align',
            [
                'label'                 => __( 'Text Align', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
                    'left' 	=> [
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
                'default'               => 'center',
                'selectors'             => [
                    '{{WRAPPER}} .pp-image-slider-slide .pp-image-slider-caption' => 'text-align: {{VALUE}};',
                ],
                'condition'             => [
                    'skin'                                      => 'slideshow',
                    'feature_image_captions_horizontal_align'   => 'justify',
                    'feature_image_caption!'                    => '',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'feature_image_captions_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-image-slider-slide .pp-image-slider-caption',
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_feature_image_captions_style' );

        $this->start_controls_tab(
            'tab_feature_image_captions_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'feature_image_captions_background',
				'types'            	    => [ 'classic','gradient' ],
				'selector'              => '{{WRAPPER}} .pp-image-slider-slide .pp-image-slider-caption',
                'exclude'               => [
                    'image',
                ],
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
			]
		);

        $this->add_control(
            'feature_image_captions_text_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-image-slider-slide .pp-image-slider-caption' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'feature_image_captions_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-image-slider-slide .pp-image-slider-caption',
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
			]
		);

		$this->add_control(
			'feature_image_captions_border_radius_normal',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-image-slider-slide .pp-image-slider-caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
			]
		);

		$this->add_responsive_control(
			'feature_image_captions_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-image-slider-slide .pp-image-slider-caption' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
			]
		);

		$this->add_responsive_control(
			'feature_image_captions_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-image-slider-slide .pp-image-slider-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
			]
		);

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name'                  => 'feature_image_text_shadow',
                'selector' 	            => '{{WRAPPER}} .pp-image-slider-slide .pp-image-slider-caption',
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
            ]
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_feature_image_captions_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'feature_image_captions_background_hover',
				'types'            	    => [ 'classic','gradient' ],
				'selector'              => '{{WRAPPER}} .pp-image-slider-slide:hover .pp-image-slider-caption',
                'exclude'               => [
                    'image',
                ],
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
			]
		);

        $this->add_control(
            'feature_image_captions_text_color_hover',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-image-slider-slide:hover .pp-image-slider-caption' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
            ]
        );

        $this->add_control(
            'feature_image_captions_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-image-slider-slide:hover .pp-image-slider-caption' => 'border-color: {{VALUE}}',
                ],
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name'                  => 'feature_image_text_shadow_hover',
                'selector' 	            => '{{WRAPPER}} .pp-image-slider-slide:hover .pp-image-slider-caption',
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->add_control(
			'feature_image_captions_blend_mode',
			[
				'label'                 => __( 'Blend Mode', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					''             => __( 'Normal', 'powerpack' ),
					'multiply'     => 'Multiply',
					'screen'       => 'Screen',
					'overlay'      => 'Overlay',
					'darken'       => 'Darken',
					'lighten'      => 'Lighten',
					'color-dodge'  => 'Color Dodge',
					'saturation'   => 'Saturation',
					'color'        => 'Color',
					'difference'   => 'Difference',
					'exclusion'    => 'Exclusion',
					'hue'          => 'Hue',
					'luminosity'   => 'Luminosity',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-image-slider-slide .pp-image-slider-caption' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator'             => 'before',
                'condition'             => [
                    'skin'                      => 'slideshow',
                    'feature_image_caption!'    => '',
                ],
			]
		);

        $this->end_controls_section();
	}

	protected function register_style_thumbnails_controls() {
        /**
         * Style Tab: Thumbnails
         */
        $this->start_controls_section(
            'section_thumbnails_style',
            [
                'label'                 => __( 'Thumbnails', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'thumbnails_alignment',
            [
                'label'                 => __( 'Alignment', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
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
                'default'               => 'left',
				'selectors' => [
					'{{WRAPPER}} .pp-image-slider-thumb-pagination' => 'justify-content: {{VALUE}};',
				],
				'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'right'    => 'flex-end',
					'center'   => 'center',
				],
                'condition'             => [
                    'skin'     => 'slideshow',
                ],
            ]
        );
        
        $this->add_control(
            'thumbnail_images_heading',
            [
                'label'                 => __( 'Images', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
            ]
        );
        
        $this->add_responsive_control(
            'thumbnails_horizontal_spacing',
            [
                'label'                 => __( 'Column Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' 	=> [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'default'               => [
                    'size' 	=> '',
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-image-slider-thumb-item-wrap,
                    {{WRAPPER}}.pp-image-slider-carousel .pp-image-slider-slide' => 'padding-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.pp-image-slider-align-top .pp-image-slider-thumb-pagination'  => 'width: calc(100% + {{SIZE}}{{UNIT}});',
                    '{{WRAPPER}} .pp-image-slider-thumb-pagination,
                    {{WRAPPER}}.pp-image-slider-carousel .slick-list'  => 'margin-left: -{{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'thumbnails_vertical_spacing',
            [
                'label'                 => __( 'Row Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' 	=> [
                        'min' => 0,
                        'max' => 200,
                    ],
                ],
                'default'               => [
                    'size' 	=> '',
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-image-slider-thumb-item-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition'             => [
					'skin'     => 'slideshow',
				],
            ]
        );

        $this->start_controls_tabs( 'tabs_thumbnails_style' );

        $this->start_controls_tab(
            'tab_thumbnails_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'thumbnails_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-image-slider-thumb-item',
			]
		);

		$this->add_control(
			'thumbnails_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-image-slider-thumb-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'thumbnails_scale',
            [
                'label'                 => __( 'Scale', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px'        => [
                        'min'   => 1,
                        'max'   => 2,
                        'step'  => 0.01,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-image-slider-thumb-image img' => 'transform: scale({{SIZE}});',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'thumbnails_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-image-slider-thumb-item',
				'condition'             => [
					'skin'     => 'slideshow',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'                  => 'thumbnails_css_filters',
				'selector'              => '{{WRAPPER}} .pp-image-slider-thumb-image img',
			]
		);

        $this->add_control(
            'thumbnails_image_filter',
            [
                'label'                 => __( 'Image Filter', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'normal',
                'options'               => Module::get_image_filters(),
				'prefix_class'          => 'pp-ins-',
            ]
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_thumbnails_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
            ]
        );

        $this->add_control(
            'thumbnails_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-image-slider-thumb-item:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'thumbnails_scale_hover',
            [
                'label'                 => __( 'Scale', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px'        => [
                        'min'   => 1,
                        'max'   => 2,
                        'step'  => 0.01,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-image-slider-thumb-item:hover .pp-image-slider-thumb-image img' => 'transform: scale({{SIZE}});',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'thumbnails_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-image-slider-thumb-item:hover',
				'condition'             => [
					'skin'     => 'slideshow',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'                  => 'thumbnails_css_filters_hover',
				'selector'              => '{{WRAPPER}} .pp-image-slider-thumb-item:hover .pp-image-slider-thumb-image img',
			]
		);

        $this->add_control(
            'thumbnails_image_filter_hover',
            [
                'label'                 => __( 'Image Filter', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'normal',
                'options'               => Module::get_image_filters(),
				'prefix_class'          => 'pp-ins-hover-',
            ]
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_thumbnails_active',
            [
                'label'                 => __( 'Active', 'powerpack' ),
            ]
        );

        $this->add_control(
            'thumbnails_border_color_active',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-active-slide .pp-image-slider-thumb-item' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'thumbnails_scale_active',
            [
                'label'                 => __( 'Scale', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px'        => [
                        'min'   => 1,
                        'max'   => 2,
                        'step'  => 0.01,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-active-slide .pp-image-slider-thumb-image img' => 'transform: scale({{SIZE}});',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'thumbnails_box_shadow_active',
				'selector'              => '{{WRAPPER}} .pp-active-slide .pp-image-slider-thumb-item',
				'condition'             => [
					'skin'     => 'slideshow',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'                  => 'thumbnails_css_filters_active',
				'selector'              => '{{WRAPPER}} .pp-active-slide .pp-image-slider-thumb-image img',
			]
		);
        
        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->add_control(
            'thumbnail_overlay_heading',
            [
                'label'                 => __( 'Overlay', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
            ]
        );

        $this->start_controls_tabs( 'tabs_thumbnails_overlay_style' );

        $this->start_controls_tab(
            'tab_thumbnails_overlay_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'thumbnails_overlay_background',
				'types'            	    => [ 'classic','gradient' ],
				'selector'              => '{{WRAPPER}} .pp-image-slider-thumb-overlay',
                'exclude'               => [
                    'image',
                ],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_thumbnails_overlay_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'thumbnails_overlay_background_hover',
				'types'            	    => [ 'classic','gradient' ],
				'selector'              => '{{WRAPPER}} .pp-image-slider-thumb-item:hover .pp-image-slider-thumb-overlay',
                'exclude'               => [
                    'image',
                ],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_thumbnails_overlay_active',
            [
                'label'                 => __( 'Active', 'powerpack' ),
                'condition'             => [
                    'skin'  => 'slideshow',
                ],
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'thumbnails_overlay_background_active',
				'types'            	    => [ 'classic','gradient' ],
				'selector'              => '{{WRAPPER}} .pp-active-slide .pp-image-slider-thumb-overlay',
                'exclude'               => [
                    'image',
                ],
                'condition'             => [
                    'skin'  => 'slideshow',
                ],
			]
		);
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->add_control(
			'feature_image_overlay_blend_mode',
			[
				'label'                 => __( 'Blend Mode', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					''             => __( 'Normal', 'powerpack' ),
					'multiply'     => 'Multiply',
					'screen'       => 'Screen',
					'overlay'      => 'Overlay',
					'darken'       => 'Darken',
					'lighten'      => 'Lighten',
					'color-dodge'  => 'Color Dodge',
					'saturation'   => 'Saturation',
					'color'        => 'Color',
					'difference'   => 'Difference',
					'exclusion'    => 'Exclusion',
					'hue'          => 'Hue',
					'luminosity'   => 'Luminosity',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-image-slider-thumb-overlay' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator'             => 'before',
			]
		);

        $this->end_controls_section();
	}

	protected function register_style_thumbnails_captions_controls() {
        /**
         * Style Tab: Thumbnails Captions
         */
        $this->start_controls_section(
            'section_thumbnails_captions_style',
            [
                'label'                 => __( 'Thumbnails Captions', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'thumbnails_captions_vertical_align',
            [
                'label'                 => __( 'Vertical Align', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
                    'top' 	=> [
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
                ],
                'default'               => 'bottom',
				'selectors' => [
					'{{WRAPPER}} .pp-image-slider-thumb-item-wrap .pp-image-slider-content' => 'justify-content: {{VALUE}};',
				],
				'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'bottom'   => 'flex-end',
					'middle'   => 'center',
				],
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'thumbnails_captions_horizontal_align',
            [
                'label'                 => __( 'Horizontal Align', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
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
                    'justify' 		=> [
                        'title' 	=> __( 'Justify', 'powerpack' ),
                        'icon' 		=> 'eicon-h-align-stretch',
                    ],
                ],
                'default'               => 'left',
				'selectors' => [
					'{{WRAPPER}} .pp-image-slider-thumb-item-wrap .pp-image-slider-content' => 'align-items: {{VALUE}};',
				],
				'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'right'    => 'flex-end',
					'center'   => 'center',
					'justify'  => 'stretch',
				],
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'thumbnails_captions_align',
            [
                'label'                 => __( 'Text Align', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
                    'left' 	=> [
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
                'default'               => 'center',
                'selectors'             => [
                    '{{WRAPPER}} .pp-image-slider-thumb-item-wrap .pp-image-slider-caption' => 'text-align: {{VALUE}};',
                ],
                'condition'             => [
                    'thumbnails_captions_horizontal_align'  => 'justify',
                    'thumbnails_caption!'                   => '',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'thumbnails_captions_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-image-slider-thumb-item-wrap .pp-image-slider-caption',
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_thumbnails_captions_style' );

        $this->start_controls_tab(
            'tab_thumbnails_captions_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'thumbnails_captions_background',
				'types'            	    => [ 'classic','gradient' ],
				'selector'              => '{{WRAPPER}} .pp-image-slider-thumb-item-wrap .pp-image-slider-caption',
                'exclude'               => [
                    'image',
                ],
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
			]
		);

        $this->add_control(
            'thumbnails_captions_text_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-image-slider-thumb-item-wrap .pp-image-slider-caption' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'thumbnails_captions_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-image-slider-thumb-item-wrap .pp-image-slider-caption',
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
			]
		);

		$this->add_control(
			'thumbnails_captions_border_radius_normal',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-image-slider-thumb-item-wrap .pp-image-slider-caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
			]
		);

		$this->add_responsive_control(
			'thumbnails_captions_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-image-slider-thumb-item-wrap .pp-image-slider-caption' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
			]
		);

		$this->add_responsive_control(
			'thumbnails_captions_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-image-slider-thumb-item-wrap .pp-image-slider-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
			]
		);

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name'                  => 'thumbnails_text_shadow',
                'selector' 	            => '{{WRAPPER}} .pp-image-slider-thumb-item-wrap .pp-image-slider-caption',
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
            ]
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_thumbnails_captions_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'thumbnails_captions_background_hover',
				'types'            	    => [ 'classic','gradient' ],
				'selector'              => '{{WRAPPER}} .pp-image-slider-thumb-item-wrap:hover .pp-image-slider-caption',
                'exclude'               => [
                    'image',
                ],
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
			]
		);

        $this->add_control(
            'thumbnails_captions_text_color_hover',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-image-slider-thumb-item-wrap:hover .pp-image-slider-caption' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
            ]
        );

        $this->add_control(
            'thumbnails_captions_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-image-slider-thumb-item-wrap:hover .pp-image-slider-caption' => 'border-color: {{VALUE}}',
                ],
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name'                  => 'thumbnails_text_shadow_hover',
                'selector' 	            => '{{WRAPPER}} .pp-image-slider-thumb-item-wrap:hover .pp-image-slider-caption',
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->add_control(
			'thumbnails_captions_blend_mode',
			[
				'label'                 => __( 'Blend Mode', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					''             => __( 'Normal', 'powerpack' ),
					'multiply'     => 'Multiply',
					'screen'       => 'Screen',
					'overlay'      => 'Overlay',
					'darken'       => 'Darken',
					'lighten'      => 'Lighten',
					'color-dodge'  => 'Color Dodge',
					'saturation'   => 'Saturation',
					'color'        => 'Color',
					'difference'   => 'Difference',
					'exclusion'    => 'Exclusion',
					'hue'          => 'Hue',
					'luminosity'   => 'Luminosity',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-image-slider-thumb-item-wrap .pp-image-slider-caption' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator'             => 'before',
                'condition'             => [
                    'thumbnails_caption!'   => '',
                ],
			]
		);

        $this->end_controls_section();
	}

	protected function register_style_arrows_controls() {
        /**
         * Style Tab: Arrows
         */
        $this->start_controls_section(
            'section_arrows_style',
            [
                'label'                 => __( 'Arrows', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
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
                    'arrows'        => 'yes',
                ],
			]
		);
        
        $this->end_controls_section();
	}

	protected function register_style_dots_controls() {
        /**
         * Style Tab: Dots
         */
        $this->start_controls_section(
            'section_dots_style',
            [
                'label'                 => __( 'Dots', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
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
				'prefix_class'          => 'pp-slick-slider-dots-',
                'condition'             => [
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
                    '{{WRAPPER}} .pp-image-slider .slick-dots li button' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition'             => [
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
                    '{{WRAPPER}} .pp-image-slider .slick-dots li' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
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
                    '{{WRAPPER}} .pp-image-slider .slick-dots li' => 'background: {{VALUE}};',
                ],
                'condition'             => [
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
                    '{{WRAPPER}} .pp-image-slider .slick-dots li.slick-active' => 'background: {{VALUE}};',
                ],
                'condition'             => [
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
				'selector'              => '{{WRAPPER}} .pp-image-slider .slick-dots li',
                'condition'             => [
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
					'{{WRAPPER}} .pp-image-slider .slick-dots li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
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
					'{{WRAPPER}} .pp-image-slider .slick-dots' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
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
                    '{{WRAPPER}} .pp-image-slider .slick-dots li:hover' => 'background: {{VALUE}};',
                ],
                'condition'             => [
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
                    '{{WRAPPER}} .pp-image-slider .slick-dots li:hover' => 'border-color: {{VALUE}};',
                ],
                'condition'             => [
                    'dots'      => 'yes',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
    }

	/**
	 * Carousel Settings.
	 *
	 * @access public
	 */
	public function slider_settings() {
        $settings = $this->get_settings();

        if ( $settings['effect'] == 'slide' && $settings['skin'] != 'slideshow'  ) {
            $slides_to_show = ( $settings['slides_per_view'] !== '' ) ? absint( $settings['slides_per_view'] ) : 3;
            $slides_to_show_tablet = ( $settings['slides_per_view_tablet'] !== '' ) ? absint( $settings['slides_per_view_tablet'] ) : 2;
            $slides_to_show_mobile = ( $settings['slides_per_view_mobile'] !== '' ) ? absint( $settings['slides_per_view_mobile'] ) : 2;
            $slides_to_scroll = ( $settings['slides_to_scroll'] !== '' ) ? absint( $settings['slides_to_scroll'] ) : 1;
            $slides_to_scroll_tablet = ( $settings['slides_to_scroll_tablet'] !== '' ) ? absint( $settings['slides_to_scroll_tablet'] ) : 1;
            $slides_to_scroll_mobile = ( $settings['slides_to_scroll_mobile'] !== '' ) ? absint( $settings['slides_to_scroll_mobile'] ) : 1;
        } else {
            $slides_to_show = 1;
            $slides_to_show_tablet = 1;
            $slides_to_show_mobile = 1;
            $slides_to_scroll = 1;
            $slides_to_scroll_tablet = 1;
            $slides_to_scroll_mobile = 1;
        }
        
        $slider_options = [
            'slidesToShow'           => $slides_to_show,
            'slidesToScroll'         => $slides_to_scroll,
            'autoplay'               => ( $settings['autoplay'] === 'yes' ),
            'autoplaySpeed'          => ( $settings['autoplay_speed'] !== '' ) ? $settings['autoplay_speed'] : 3000,
            'arrows'                 => ( $settings['arrows'] === 'yes' ),
            'dots'                   => ( $settings['dots'] === 'yes' ),
            'fade'                   => ( $settings['effect'] === 'fade' ),
            'speed'                  => ( $settings['animation_speed'] !== '' ) ? $settings['animation_speed'] : 600,
            'infinite'               => ( $settings['infinite_loop'] === 'yes' ),
            'pauseOnHover'           => ( $settings['pause_on_hover'] === 'yes' ),
            'adaptiveHeight'         => ( $settings['adaptive_height'] === 'yes' ),
        ];

        if ( is_rtl() ) {
			$slider_options['rtl'] = true;
		}

        if ( $settings['arrows'] == 'yes' ) {
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
                    'slidesToShow'      => $slides_to_show_tablet,
                    'slidesToScroll'    => $slides_to_scroll_tablet,
                ],
            ],
            [
                'breakpoint' => $bp_mobile,
                'settings' => [
                    'slidesToShow'      => $slides_to_show_mobile,
                    'slidesToScroll'    => $slides_to_scroll_mobile,
                ]
            ]
        ];

        $this->add_render_attribute(
            'slider',
            [
                'data-slider-settings' => wp_json_encode( $slider_options ),
            ]
        );
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'slider-wrap', 'class', 'pp-image-slider-wrap' );
        
        $this->add_render_attribute( 'slider', [
            'class' => ['pp-image-slider','pp-slick-slider'],
            'id'    => 'pp-image-slider-'.esc_attr( $this->get_id() )
        ] );
        
        if ( is_rtl() ) {
            $this->add_render_attribute( 'slider', 'dir', 'rtl' );
        }
        
        $this->slider_settings();
		$gallery = $settings['gallery_images'];
        ?>
		<?php if ( !empty( $gallery ) ) { ?>
        <div class="pp-image-slider-container">
            <div <?php echo $this->get_render_attribute_string( 'slider-wrap' ); ?>>
                <div <?php echo $this->get_render_attribute_string( 'slider' ); ?>>
                    <?php
                        if ( $settings['skin'] == 'slideshow' ) {
                            $this->render_slideshow();
                        } else {
                            $this->render_carousel();
                        }
                    ?>
                </div>
            </div>
            <?php
                if ( $settings['skin'] == 'slideshow' ) {
                    // Slideshow Thumbnails
                    $this->render_thumbnails();
                }
            ?>
        </div>
		<?php } else {
			$placeholder = sprintf( 'Click here to edit the "%1$s" settings and choose some images.', esc_attr( $this->get_title() ) );

			echo $this->render_editor_placeholder( [
				'title' => __( 'Gallery is empty!', 'powerpack' ),
				'body' => $placeholder,
			] );
		}
    }
    
    protected function render_slideshow() {
        $settings = $this->get_settings_for_display();
		$gallery = $settings['gallery_images'];
        
        foreach ( $gallery as $index => $item ) {
            ?>
            <div class="pp-image-slider-slide">
                <?php
                    $image_url = Group_Control_Image_Size::get_attachment_image_src( $item['id'], 'image', $settings );
                    $image_html = '<div class="pp-image-slider-image-wrap">';
                    $image_html .= '<img class="pp-image-slider-image" src="' . esc_url( $image_url ) . '" alt="' . esc_attr( Control_Media::get_image_alt( $item ) ) . '" />';
                    $image_html .= '</div>';
            
                    $caption = '';
                    $caption_rendered = '';
            
                    if ( $settings['feature_image_caption'] != '' ) {
                        $caption_rendered = $this->render_image_caption( $item['id'], $settings['feature_image_caption'] );
                        $image_html .= '<div class="pp-image-slider-content pp-media-content">';
                            $image_html .= $caption_rendered;
                        $image_html .= '</div>';
                    }
            
                    if ( $settings['feature_image_lightbox_caption'] != '' ) {
                        $caption = Module::get_image_caption( $item['id'], $settings['feature_image_lightbox_caption'] );
                    }

                    if ( $settings['link_to'] != 'none' ) {
            
                        $image_html = $this->get_slide_link_atts('slideshow', $index, $item, $image_html, $caption);
                        
                    }

                    echo $image_html;
                ?>
            </div>
            <?php
        }
    }
    
    protected function render_thumbnails() {
        $settings = $this->get_settings_for_display();
		$gallery = $settings['gallery_images'];
        ?>
        <div class="pp-image-slider-thumb-pagination pp-elementor-grid <?php echo 'pp-' . $settings['thumbnails_image_filter']; ?>">
            <?php
                foreach ( $gallery as $index => $item ) {
                    $image_url = Group_Control_Image_Size::get_attachment_image_src( $item['id'], 'thumbnail', $settings );
                    ?>
                    <div class="pp-image-slider-thumb-item-wrap pp-grid-item-wrap">
                        <div class="pp-grid-item pp-image-slider-thumb-item pp-ins-filter-hover">
                            <div class="pp-image-slider-thumb-image pp-ins-filter-target">
                                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( Control_Media::get_image_alt( $item ) ); ?>" />
                            </div>
                            <?php echo $this->render_image_overlay(); ?>
                            <?php if ( $settings['thumbnails_caption'] != '' ) { ?>
                                <div class="pp-image-slider-content pp-media-content">
                                    <?php
                                        echo $this->render_image_caption( $item['id'], $settings['thumbnails_caption'] );
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php
                }
            ?>
        </div>
        <?php
    }
    
    protected function render_carousel() {
        $settings = $this->get_settings_for_display();
		$gallery = $settings['gallery_images'];
        
        foreach ( $gallery as $index => $item ) {
            $image_url = Group_Control_Image_Size::get_attachment_image_src( $item['id'], 'thumbnail', $settings );
            ?>
            <div class="pp-image-slider-thumb-item-wrap">
                <div class="pp-image-slider-thumb-item pp-ins-filter-hover">
                    <?php
                        $image_html = '<div class="pp-image-slider-thumb-image pp-ins-filter-target">';
                        $image_html .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( Control_Media::get_image_alt( $item ) ) . '" />';
                        $image_html .= '</div>';
            
                        $image_html .= $this->render_image_overlay();
            
                        $caption = '';
                        $caption_rendered = '';

                        if ( $settings['thumbnails_caption'] != '' ) {
                            $caption_rendered = $this->render_image_caption( $item['id'], $settings['thumbnails_caption'] );
                            $image_html .= '<div class="pp-image-slider-content pp-media-content">';
                            $image_html .= $caption_rendered;
                            $image_html .= '</div>';
                        }

                        if ( $settings['thumbnails_lightbox_caption'] != '' ) {
                            $caption = Module::get_image_caption( $item['id'], $settings['thumbnails_lightbox_caption'] );
                        }

                        if ( $settings['carousel_link_to'] != 'none' ) {
            
                            $image_html = $this->get_slide_link_atts('carousel', $index, $item, $image_html, $caption);
                            
                        }

                        echo $image_html;
                    ?>
                </div>
            </div>
            <?php
        }
    }
    
    protected function get_slide_link_atts( $layout = '', $index = '', $item = '', $image_html, $caption = '' ) {
        $settings = $this->get_settings_for_display();
        
        if ( $layout == 'slideshow' ) {
            $link_to = $settings['link_to'];
            $custom_link = get_post_meta( $item['id'], 'pp-custom-link', true );
			$link_target = $settings['link_target'];
            $lightbox_library = $settings['lightbox_library'];
            $lightbox_caption = $settings['feature_image_lightbox_caption'];
            $link_key = $this->get_repeater_setting_key( 'link', 'gallery_images', $index );
        } elseif ( $layout == 'carousel' ) {
            $link_to = $settings['carousel_link_to'];
            $custom_link = get_post_meta( $item['id'], 'pp-custom-link', true );
			$link_target = $settings['carousel_link_target'];
            $lightbox_library = $settings['carousel_lightbox_library'];
            $lightbox_caption = $settings['thumbnails_lightbox_caption'];
            $link_key = $this->get_repeater_setting_key( 'carousel_link', 'gallery_images', $index );
        }

        if ( $link_to == 'file' ) {
            $link = wp_get_attachment_url( $item['id'] );

            if ( $lightbox_library == 'fancybox' ) {
                $this->add_render_attribute( $link_key, [
                    'data-elementor-open-lightbox'      => 'no',
                    'data-fancybox'                     => 'pp-image-slider-' . $this->get_id(),
                ] );
                
                if ( $lightbox_caption != '' ) {
                    $this->add_render_attribute( $link_key, [
                        'data-caption'                  => $caption
                    ] );
                }

                $this->add_render_attribute( $link_key, [
                    'data-src' 						    => $link,
                ] );
            } else {
                $this->add_render_attribute( $link_key, [
                    'data-elementor-open-lightbox' 		=> $settings['open_lightbox'],
                    'data-elementor-lightbox-slideshow' => $this->get_id(),
                    'data-elementor-lightbox-index' 	=> $index,
                ] );

                $this->add_render_attribute( $link_key, [
                    'href' 								=> $link,
                    'class' 							=> 'elementor-clickable',
                ] );
            }
        } elseif ( $link_to == 'custom' && $custom_link != '' ) {
            $link = $custom_link;

			$this->add_render_attribute( $link_key, 'target', $link_target );

            $this->add_render_attribute( $link_key, [
                'href' 								=> $link,
            ] );
        }

        $this->add_render_attribute( $link_key, [
            'class' 							=> 'pp-image-slider-slide-link',
        ] );

        return '<a ' . $this->get_render_attribute_string( $link_key ) . '>' . $image_html . '</a>';
    }
    
    protected function render_image_overlay() {
        return '<div class="pp-image-slider-thumb-overlay pp-media-overlay"></div>';
    }
    
    protected function render_image_caption( $id, $caption_type = 'caption' ) {
        $settings = $this->get_settings_for_display();
        
        $caption = Module::get_image_caption( $id, $caption_type );
        
        if ( $caption == '' ) {
			return '';
		}
        
        ob_start();
        ?>
        <div class="pp-image-slider-caption">
            <?php echo $caption; ?>
        </div>
        <?php
        $html = ob_get_contents();
		ob_end_clean();
        return $html;
    }

    protected function _content_template() {
    }
}