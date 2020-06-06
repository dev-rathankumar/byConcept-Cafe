<?php
namespace PowerpackElements\Modules\Album\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Modules\Album\Module;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
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
class Album extends Powerpack_Widget {
    
    /**
	 * Retrieve image slider widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
    public function get_name() {
        return parent::get_widget_name( 'Album' );
    }

    /**
	 * Retrieve image slider widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
    public function get_title() {
        return parent::get_widget_title( 'Album' );
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
        return parent::get_widget_categories( 'Album' );
    }

    /**
	 * Retrieve image slider widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
    public function get_icon() {
        return parent::get_widget_icon( 'Album' );
    }

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.4.13.1
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Album' );
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

        /*-----------------------------------------------------------------------------------*/
        /*	CONTENT TAB
        /*-----------------------------------------------------------------------------------*/
        
        /**
         * Content Tab: Album
         */
        $this->start_controls_section(
            'section_album',
            [
                'label'                 => __( 'Album', 'powerpack' ),
            ]
        );
        
        $this->add_control(
            'album_images',
            [
                'label'                 => __( 'Add Images', 'powerpack' ),
                'type'                  => Controls_Manager::GALLERY,
                'dynamic'               => [
                    'active' => true
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Content Tab: Trigger
         */
        $this->start_controls_section(
            'section_album_cover_settings',
            [
                'label'                 => __( 'Trigger', 'powerpack' ),
            ]
        );
        
        $this->add_control(
            'trigger',
            [
                'label'                 => __( 'Trigger', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'cover',
                'options'               => [
                    'cover'     => __( 'Album Cover', 'powerpack' ),
                    'button'    => __( 'Button', 'powerpack' ),
                ],
            ]
        );

		$this->add_control(
			'album_cover_type',
			[
				'label'                 => __( 'Cover Image', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'first_img',
				'options'               => [
					'custom_img'   => __( 'Custom', 'powerpack' ),
					'first_img'    => __( 'First Image of Album', 'powerpack' ),
				],
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
			]
		);

		$this->add_control(
			'album_cover',
			[
				'label'                 => __( 'Add Cover Image', 'powerpack' ),
				'type'                  => Controls_Manager::MEDIA,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => [
					'url' => Utils::get_placeholder_image_src(),
				],
                'condition'             => [
                    'trigger'			=> 'cover',
                    'album_cover_type'  => 'custom_img',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'album_cover', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default'               => 'full',
				'separator'             => 'none',
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
			]
		);
        
        $this->add_responsive_control(
            'album_height',
            [
                'label'                 => __( 'Album Cover Height', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => 300,
                    'unit'      => 'px',
                ],
                'range'                 => [
                    'px'        => [
                        'min'   => 50,
                        'max'   => 1000,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-cover-wrap' => 'height: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'trigger'	=> 'cover',
                ],
            ]
        );

        $this->add_control(
            'album_trigger_button_text',
            [
                'label'                 => __( 'Button Text', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'default'               => __( 'View Album', 'powerpack' ),
				'dynamic'               => [
					'active'   => true,
				],
                'condition'             => [
                    'trigger'			=> 'button',
                ],
            ]
        );
		
		$this->add_control(
			'select_album_trigger_button_icon',
			[
				'label'					=> __( 'Button Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'fa4compatibility'		=> 'album_trigger_button_icon',
                'condition'             => [
                    'trigger'			=> 'button',
                ],
			]
		);
        
        $this->add_control(
            'album_trigger_button_icon_position',
            [
                'label'                 => __( 'Icon Position', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'after',
                'options'               => [
                    'after'     => __( 'After', 'powerpack' ),
                    'before'    => __( 'Before', 'powerpack' ),
                ],
                'condition'             => [
                    'trigger'						=> 'button',
                    'album_trigger_button_text!'	=> '',
                    'select_album_trigger_button_icon[value]!'	=> '',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'album_trigger_button_icon_spacing',
            [
                'label'                 => __( 'Icon Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => 8,
                    'unit'      => 'px',
                ],
                'range'                 => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 50,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-trigger-icon-before .pp-button-icon' => 'margin-right: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .pp-album-trigger-icon-after .pp-button-icon' => 'margin-left: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
					'trigger'						=> 'button',
                    'album_trigger_button_text!'	=> '',
                    'select_album_trigger_button_icon[value]!'	=> '',
                ],
            ]
        );
        
        $this->end_controls_section();
        
        /**
         * Content Tab: Album Cover Content
         */
        $this->start_controls_section(
            'section_album_content',
            [
                'label'                 => __( 'Album Cover Content', 'powerpack' ),
                'condition'             => [
                    'trigger'	=> 'cover',
                ],
            ]
        );
		
		$this->add_control(
			'select_album_icon',
			[
				'label'					=> __( 'Album Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'fa4compatibility'		=> 'album_icon',
                'condition'             => [
                    'trigger'	=> 'cover',
                ],
			]
		);

        $this->add_control(
            'album_title',
            [
                'label'                 => __( 'Title', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'default'               => '',
				'dynamic'               => [
					'active'   => true,
				],
				'separator'             => 'before',
                'condition'             => [
                    'trigger'	=> 'cover',
                ],
            ]
        );
        
        $this->add_control(
            'album_title_html_tag',
            [
                'label'                 => __( 'Title HTML Tag', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'div',
                'options'               => [
                    'h1'     => __( 'H1', 'powerpack' ),
                    'h2'     => __( 'H2', 'powerpack' ),
                    'h3'     => __( 'H3', 'powerpack' ),
                    'h4'     => __( 'H4', 'powerpack' ),
                    'h5'     => __( 'H5', 'powerpack' ),
                    'h6'     => __( 'H6', 'powerpack' ),
                    'div'    => __( 'div', 'powerpack' ),
                    'span'   => __( 'span', 'powerpack' ),
                    'p'      => __( 'p', 'powerpack' ),
                ],
                'condition'             => [
                    'trigger'		=> 'cover',
                    'album_title!'  => '',
                ],
            ]
        );

        $this->add_control(
            'album_subtitle',
            [
                'label'                 => __( 'Subtitle', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'default'               => '',
				'dynamic'               => [
					'active'   => true,
				],
				'separator'             => 'before',
                'condition'             => [
                    'trigger'	=> 'cover',
                ],
            ]
        );
        
        $this->add_control(
            'album_subtitle_html_tag',
            [
                'label'                 => __( 'Subtitle HTML Tag', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'div',
                'options'               => [
                    'h1'     => __( 'H1', 'powerpack' ),
                    'h2'     => __( 'H2', 'powerpack' ),
                    'h3'     => __( 'H3', 'powerpack' ),
                    'h4'     => __( 'H4', 'powerpack' ),
                    'h5'     => __( 'H5', 'powerpack' ),
                    'h6'     => __( 'H6', 'powerpack' ),
                    'div'    => __( 'div', 'powerpack' ),
                    'span'   => __( 'span', 'powerpack' ),
                    'p'      => __( 'p', 'powerpack' ),
                ],
                'condition'             => [
                    'trigger'			=> 'cover',
                    'album_subtitle!'	=> '',
                ],
            ]
        );
        
        $this->add_control(
            'album_cover_button',
            [
                'label'                 => __( 'Show Button', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'return_value'          => 'yes',
				'separator'             => 'before',
                'condition'             => [
                    'trigger'	=> 'cover',
                ],
            ]
        );

        $this->add_control(
            'album_cover_button_text',
            [
                'label'                 => __( 'Button Text', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'default'               => __( 'View More', 'powerpack' ),
				'dynamic'               => [
					'active'   => true,
				],
                'condition'             => [
                    'trigger'				=> 'cover',
                    'album_cover_button'	=> 'yes',
                ],
            ]
        );
		
		$this->add_control(
			'select_album_cover_button_icon',
			[
				'label'					=> __( 'Button Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'fa4compatibility'		=> 'album_cover_button_icon',
                'condition'             => [
                    'trigger'				=> 'cover',
                    'album_cover_button'	=> 'yes',
                ],
			]
		);
        
        $this->add_control(
            'album_cover_button_icon_position',
            [
                'label'                 => __( 'Icon Position', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'after',
                'options'               => [
                    'after'     => __( 'After', 'powerpack' ),
                    'before'    => __( 'Before', 'powerpack' ),
                ],
                'condition'             => [
                    'trigger'					=> 'cover',
                    'album_cover_button'		=> 'yes',
                    'select_album_cover_button_icon[value]!'	=> '',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'album_cover_button_position',
            [
                'label'                 => __( 'Button Position', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'bottom',
                'options'               => [
                    'inline'	=> __( 'Inline', 'powerpack' ),
                    'bottom'	=> __( 'Bottom', 'powerpack' ),
                ],
				'prefix_class'          => 'pp-album-cover-button%s-position-',
                'condition'             => [
                    'trigger'				=> 'cover',
                    'album_cover_button'	=> 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        /**
         * Content Tab: Settings
         */
        $this->start_controls_section(
            'section_general_settings',
            [
                'label'                 => __( 'Settings', 'powerpack' ),
            ]
        );
        
        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'                  => 'image',
                'label'                 => __( 'Image Size', 'powerpack' ),
                'default'               => 'full',
                'exclude'               => [ 'custom' ],
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
				'frontend_available'    => true,
            ]
        );

		$this->add_control(
			'lightbox_options_heading',
			[
				'type'                  => Controls_Manager::HEADING,
				'label'                 => __( 'Lightbox Options', 'powerpack' ),
                'separator'             => 'before',
                'condition'             => [
                    'lightbox_library'  => 'fancybox',
				],
			]
		);

		$this->add_control(
			'lightbox_caption',
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
                    'lightbox_library'  => 'fancybox',
				],
			]
		);
        
        $this->add_control(
            'loop',
            [
                'label'                 => __( 'Loop', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
                'condition'             => [
                    'lightbox_library'  => 'fancybox',
				],
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
                'condition'             => [
                    'lightbox_library'  => 'fancybox',
				],
            ]
        );
        
        $this->add_control(
            'slides_counter',
            [
                'label'                 => __( 'Slides Counter', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
                'condition'             => [
                    'lightbox_library'  => 'fancybox',
				],
            ]
        );
        
        $this->add_control(
            'keyboard',
            [
                'label'                 => __( 'Keyboard Navigation', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
                'condition'             => [
                    'lightbox_library'  => 'fancybox',
				],
            ]
        );
        
        $this->add_control(
            'toolbar',
            [
                'label'                 => __( 'Toolbar', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
                'condition'             => [
                    'lightbox_library'  => 'fancybox',
				],
            ]
        );

        $this->add_control(
            'toolbar_buttons',
            [
                'label'                 => __( 'Toolbar Buttons', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT2,
				'label_block'			=> true,
                'default'               => ['zoom', 'slideShow', 'thumbs', 'close'],
                'options'               => [
                    'zoom'          => __( 'Zoom', 'powerpack' ),
                    'share'         => __( 'Share', 'powerpack' ),
                    'slideShow'     => __( 'SlideShow', 'powerpack' ),
                    'fullScreen'    => __( 'Full Screen', 'powerpack' ),
                    'download'      => __( 'Download', 'powerpack' ),
                    'thumbs'        => __( 'Thumbs', 'powerpack' ),
                    'close'         => __( 'Close', 'powerpack' ),
                ],
				'multiple'              => true,
				'frontend_available'    => true,
                'condition'             => [
                    'lightbox_library'  => 'fancybox',
                    'toolbar'           => 'yes',
				],
            ]
        );
        
        $this->add_control(
            'thumbs_auto_start',
            [
                'label'                 => __( 'Thumbs Auto Start', 'powerpack' ),
                'description'			=> __( 'Display thumbnails on lightbox opening', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
				'frontend_available'    => true,
                'condition'             => [
                    'lightbox_library'  => 'fancybox',
				],
            ]
        );

        $this->add_control(
            'thumbs_position',
            [
                'label'                 => __( 'Thumbs Position', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => '',
                'options'               => [
                    ''				=> __( 'Default', 'powerpack' ),
                    'bottom'		=> __( 'Bottom', 'powerpack' ),
                ],
				'frontend_available'    => true,
                'condition'             => [
                    'lightbox_library'  => 'fancybox',
				],
            ]
        );

        $this->add_control(
            'lightbox_animation',
            [
                'label'                 => __( 'Animation', 'powerpack' ),
                'description'           => __( 'Open/Close animation', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'zoom',
                'options'               => [
                    ''              => __( 'None', 'powerpack' ),
                    'fade'          => __( 'Fade', 'powerpack' ),
                    'zoom'          => __( 'Zoom', 'powerpack' ),
                    'zoom-in-out'   => __( 'Zoom in Out', 'powerpack' ),
                ],
				'frontend_available'    => true,
                'condition'             => [
                    'lightbox_library'  => 'fancybox',
				],
            ]
        );

        $this->add_control(
            'transition_effect',
            [
                'label'                 => __( 'Transition Effect', 'powerpack' ),
                'description'           => __( 'Transition effect between slides', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'fade',
                'options'               => [
                    ''              => __( 'None', 'powerpack' ),
                    'fade'          => __( 'Fade', 'powerpack' ),
                    'slide'         => __( 'Slide', 'powerpack' ),
                    'circular'      => __( 'Circular', 'powerpack' ),
                    'tube'          => __( 'Tube', 'powerpack' ),
                    'zoom-in-out'   => __( 'Zoom in Out', 'powerpack' ),
                    'rotate'        => __( 'Rotate', 'powerpack' ),
                ],
				'frontend_available'    => true,
                'condition'             => [
                    'lightbox_library'  => 'fancybox',
				],
            ]
        );

        $this->end_controls_section();

        /*-----------------------------------------------------------------------------------*/
        /*	STYLE TAB
        /*-----------------------------------------------------------------------------------*/

        /**
         * Style Tab: Album Cover
         */
        $this->start_controls_section(
            'section_cover_style',
            [
                'label'                 => __( 'Album Cover', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_album_cover_style' );

        $this->start_controls_tab(
            'tab_album_cover_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'album_cover_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-album-cover',
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
			]
		);

		$this->add_control(
			'album_cover_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-album-cover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
			]
		);

        $this->add_control(
            'album_cover_image_scale',
            [
                'label'                 => __( 'Image Scale', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'  => 1,
                        'max'  => 2,
                        'step' => 0.01,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-cover img' => 'transform: scale({{SIZE}});',
                ],
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'album_cover_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-album-cover',
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'                  => 'album_cover_css_filters',
				'selector'              => '{{WRAPPER}} .pp-album-cover img',
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
			]
		);

        $this->add_control(
            'album_cover_image_filter',
            [
                'label'                 => __( 'Image Filter', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'normal',
                'options'               => Module::get_image_filters(),
				'prefix_class'          => 'pp-ins-',
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
            ]
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_album_cover_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
            ]
        );

        $this->add_control(
            'album_cover_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-cover:hover' => 'border-color: {{VALUE}}',
                ],
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
            ]
        );

        $this->add_control(
            'album_cover_image_scale_hover',
            [
                'label'                 => __( 'Image Scale', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'  => 1,
                        'max'  => 2,
                        'step' => 0.01,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-cover:hover img' => 'transform: scale({{SIZE}});',
                ],
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'album_cover_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-album-cover:hover',
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'                  => 'album_cover_css_filters_hover',
				'selector'              => '{{WRAPPER}} .pp-album-cover:hover img',
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
			]
		);

        $this->add_control(
            'album_cover_image_filter_hover',
            [
                'label'                 => __( 'Image Filter', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'normal',
                'options'               => Module::get_image_filters(),
				'prefix_class'          => 'pp-ins-hover-',
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
            ]
        );
        
        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->add_control(
            'album_cover_overlay_style_heading',
            [
                'label'                 => __( 'Cover Overlay', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'trigger'		=> 'cover',
                ],
            ]
        );

        $this->start_controls_tabs( 'tabs_album_cover_overlay_style' );

        $this->start_controls_tab(
            'tab_album_cover_overlay_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
                    'trigger'		=> 'cover',
                ],
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'album_cover_overlay_background',
				'types'            	    => [ 'classic','gradient' ],
				'selector'              => '{{WRAPPER}} .pp-album-cover-overlay',
                'exclude'               => [
                    'image',
                ],
                'condition'             => [
                    'trigger'		=> 'cover',
                ],
			]
		);

		$this->add_responsive_control(
			'overlay_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-album-cover-overlay' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'trigger'		=> 'cover',
                ],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_album_cover_overlay_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'album_cover_overlay_background_hover',
				'types'            	    => [ 'classic','gradient' ],
				'selector'              => '{{WRAPPER}} .pp-album-cover:hover .pp-album-cover-overlay',
                'exclude'               => [
                    'image',
                ],
                'condition'             => [
                    'trigger'		=> 'cover',
                ],
			]
		);
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

        $this->end_controls_section();

        /**
         * Style Tab: Album Cover Content
         */
        $this->start_controls_section(
            'section_cover_content_style',
            [
                'label'                 => __( 'Album Cover Content', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
            ]
        );

        $this->add_responsive_control(
            'cover_content_vertical_align',
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
                'default'               => 'middle',
				'selectors' => [
					'{{WRAPPER}} .pp-album-content-wrap' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}}.pp-album-cover-button-position-inline .pp-album-content' => 'align-items: {{VALUE}};',
				],
				'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'bottom'   => 'flex-end',
					'middle'   => 'center',
				],
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
            ]
        );

        $this->add_responsive_control(
            'cover_content_horizontal_align',
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
                'default'               => 'center',
				'selectors' => [
					'{{WRAPPER}} .pp-album-content-wrap' => 'align-items: {{VALUE}};',
				],
				'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'right'    => 'flex-end',
					'center'   => 'center',
					'justify'  => 'stretch',
				],
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
            ]
        );

        $this->add_responsive_control(
            'cover_content_text_align',
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
                    '{{WRAPPER}} .pp-album-content' => 'text-align: {{VALUE}};',
                ],
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
            ]
        );

		$this->add_responsive_control(
			'cover_content_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-album-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'separator'             => 'before',
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
			]
		);

		$this->add_responsive_control(
			'cover_content_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-album-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
			]
		);

        $this->start_controls_tabs( 'tabs_cover_content_style' );

        $this->start_controls_tab(
            'tab_cover_content_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'cover_content_background',
				'types'            	    => [ 'classic','gradient' ],
				'selector'              => '{{WRAPPER}} .pp-album-content',
                'exclude'               => [
                    'image',
                ],
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'cover_content_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-album-content',
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
			]
		);

		$this->add_control(
			'cover_content_border_radius_normal',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-album-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'trigger'			=> 'cover',
                ],
			]
		);
        
        $this->add_control(
            'album_icon_style_heading',
            [
                'label'                 => __( 'Icon', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'trigger'					=> 'cover',
					'select_album_icon[value]!'	=> '',
                ],
            ]
        );

        $this->add_control(
            'album_icon_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-icon' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-album-icon svg' => 'fill: {{VALUE}}',
                ],
                'condition'             => [
                    'trigger'					=> 'cover',
					'select_album_icon[value]!'	=> '',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'album_icon_size',
            [
                'label'                 => __( 'Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => '',
                    'unit'      => 'px',
                ],
                'range'                 => [
                    'px'        => [
                        'min'   => 1,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-icon' => 'font-size: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'trigger'					=> 'cover',
					'select_album_icon[value]!'	=> '',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'icon_spacing',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => '',
                    'unit'      => 'px',
                ],
                'range'                 => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-icon' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'trigger'					=> 'cover',
					'select_album_icon[value]!'	=> '',
                ],
            ]
        );
        
        $this->add_control(
            'album_title_style_heading',
            [
                'label'                 => __( 'Title', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'trigger'		=> 'cover',
					'album_title!'	=> '',
                ],
            ]
        );

        $this->add_control(
            'album_title_text_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-title' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'trigger'		=> 'cover',
					'album_title!'	=> '',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'album_title_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-album-title',
                'condition'             => [
                    'trigger'		=> 'cover',
					'album_title!'	=> '',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'title_spacing',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => '',
                    'unit'      => 'px',
                ],
                'range'                 => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'trigger'		=> 'cover',
					'album_title!'	=> '',
                ],
            ]
        );
        
        $this->add_control(
            'album_subtitle_style_heading',
            [
                'label'                 => __( 'Subtitle', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'trigger'		=> 'cover',
					'album_subtitle!'	=> '',
                ],
            ]
        );

        $this->add_control(
            'album_subtitle_text_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-subtitle' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'trigger'		=> 'cover',
					'album_subtitle!'	=> '',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'album_subtitle_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-album-subtitle',
                'condition'             => [
                    'trigger'			=> 'cover',
					'album_subtitle!'	=> '',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'subtitle_spacing',
            [
                'label'                 => __( 'Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'      => '',
                    'unit'      => 'px',
                ],
                'range'                 => [
                    'px'        => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'trigger'			=> 'cover',
					'album_subtitle!'	=> '',
                ],
            ]
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_cover_content_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
                'condition'             => [
                    'trigger'		=> 'cover',
                ],
            ]
        );
        
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'cover_content_background_hover',
				'types'            	    => [ 'classic','gradient' ],
				'selector'              => '{{WRAPPER}} .pp-album-cover:hover .pp-album-content',
                'exclude'               => [
                    'image',
                ],
                'condition'             => [
                    'trigger'		=> 'cover',
                ],
			]
		);

        $this->add_control(
            'album_icon_color_hover',
            [
                'label'                 => __( 'Icon Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-cover:hover .pp-album-icon' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'trigger'		=> 'cover',
					'album_icon!'	=> '',
                ],
            ]
        );

        $this->add_control(
            'album_title_color_hover',
            [
                'label'                 => __( 'Title Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-cover:hover .pp-album-title' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'trigger'		=> 'cover',
					'album_title!'	=> '',
                ],
            ]
        );

        $this->add_control(
            'album_subtitle_color_hover',
            [
                'label'                 => __( 'Subtitle Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-cover:hover .pp-album-title' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    'trigger'			=> 'cover',
					'album_subtitle!'	=> '',
                ],
            ]
        );

        $this->add_control(
            'cover_content_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-cover:hover .pp-album-content' => 'border-color: {{VALUE}}',
                ],
                'condition'             => [
                    'trigger'		=> 'cover',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->add_control(
			'cover_content_blend_mode',
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
					'{{WRAPPER}} .pp-album-content' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator'             => 'before',
                'condition'             => [
                    'trigger'		=> 'cover',
                ],
			]
		);

        $this->end_controls_section();

        /**
         * Style Tab: Album Cover Button
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_album_cover_button_style',
            [
                'label'                 => __( 'Album Cover Button', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'trigger'				=> 'cover',
                    'album_cover_button'	=> 'yes',
                ],
            ]
        );

		$this->add_control(
			'album_cover_button_size',
			[
				'label'                 => __( 'Size', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'xs',
				'options'               => [
					'xs' => __( 'Extra Small', 'powerpack' ),
					'sm' => __( 'Small', 'powerpack' ),
					'md' => __( 'Medium', 'powerpack' ),
					'lg' => __( 'Large', 'powerpack' ),
					'xl' => __( 'Extra Large', 'powerpack' ),
				],
				'condition'             => [
                    'trigger'				=> 'cover',
					'album_cover_button'	=> 'yes',
				],
			]
		);

        $this->start_controls_tabs( 'tabs_album_cover_button_style' );

        $this->start_controls_tab(
            'tab_album_cover_button_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
				'condition'             => [
                    'trigger'				=> 'cover',
					'album_cover_button'	=> 'yes',
				],
            ]
        );

        $this->add_control(
            'album_cover_button_text_color_normal',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-cover-button' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-album-cover-button svg' => 'fill: {{VALUE}}',
                ],
				'condition'             => [
                    'trigger'				=> 'cover',
					'album_cover_button'	=> 'yes',
				],
            ]
        );

        $this->add_control(
            'album_cover_button_bg_color_normal',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-cover-button' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
                    'trigger'				=> 'cover',
					'album_cover_button'	=> 'yes',
				],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'album_cover_button_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-album-cover-button',
				'condition'             => [
                    'trigger'				=> 'cover',
					'album_cover_button'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'album_cover_button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-album-cover-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
                    'trigger'				=> 'cover',
					'album_cover_button'	=> 'yes',
				],
			]
		);
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'album_cover_button_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-album-cover-button',
				'condition'             => [
                    'trigger'				=> 'cover',
					'album_cover_button'	=> 'yes',
				],
            ]
        );

		$this->add_responsive_control(
			'album_cover_button_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-album-cover-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
                    'trigger'				=> 'cover',
					'album_cover_button'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'album_cover_button_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-album-cover-button',
				'condition'             => [
                    'trigger'				=> 'cover',
					'album_cover_button'	=> 'yes',
				],
			]
		);
        
        $this->add_control(
            'album_cover_button_icon_heading',
            [
                'label'                 => __( 'Button Icon', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'trigger'						=> 'cover',
					'album_cover_button'			=> 'yes',
                    'select_album_cover_button_icon[value]!'	=> '',
                ],
            ]
        );

		$this->add_responsive_control(
			'album_cover_button_icon_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'placeholder'       => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-album-cover-button .pp-button-icon' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
                'condition'             => [
                    'trigger'						=> 'cover',
					'album_cover_button'			=> 'yes',
                    'select_album_cover_button_icon[value]!'	=> '',
                ],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_album_cover_button_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
				'condition'             => [
                    'trigger'				=> 'cover',
					'album_cover_button'	=> 'yes',
				],
            ]
        );

        $this->add_control(
            'album_cover_button_text_color_hover',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-cover-button:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-album-cover-button:hover svg' => 'fill: {{VALUE}}',
                ],
				'condition'             => [
                    'trigger'				=> 'cover',
					'album_cover_button'	=> 'yes',
				],
            ]
        );

        $this->add_control(
            'album_cover_button_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-cover-button:hover' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
                    'trigger'				=> 'cover',
					'album_cover_button'	=> 'yes',
				],
            ]
        );

        $this->add_control(
            'album_cover_button_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-cover-button:hover' => 'border-color: {{VALUE}}',
                ],
				'condition'             => [
                    'trigger'				=> 'cover',
					'album_cover_button'	=> 'yes',
				],
            ]
        );

		$this->add_control(
			'album_cover_button_animation',
			[
				'label'                 => __( 'Animation', 'powerpack' ),
				'type'                  => Controls_Manager::HOVER_ANIMATION,
				'condition'             => [
                    'trigger'				=> 'cover',
					'album_cover_button'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'album_cover_button_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-album-cover-button:hover',
				'condition'             => [
                    'trigger'				=> 'cover',
					'album_cover_button'	=> 'yes',
				],
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->end_controls_section();

        /**
         * Style Tab: Album Trigger Button
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_album_trigger_button_style',
            [
                'label'                 => __( 'Album Trigger Button', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'trigger'	=> 'button',
                ],
            ]
        );

		$this->add_control(
			'album_trigger_button_size',
			[
				'label'                 => __( 'Size', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'md',
				'options'               => [
					'xs' => __( 'Extra Small', 'powerpack' ),
					'sm' => __( 'Small', 'powerpack' ),
					'md' => __( 'Medium', 'powerpack' ),
					'lg' => __( 'Large', 'powerpack' ),
					'xl' => __( 'Extra Large', 'powerpack' ),
				],
				'condition'             => [
					'trigger'	=> 'button',
				],
			]
		);
        
        $this->add_responsive_control(
            'album_trigger_button_alignment',
            [
                'label'                 => __( 'Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center'    => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'     => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-album-trigger-button-wrap' => 'text-align: {{VALUE}};',
				],
				'condition'             => [
					'trigger'	=> 'button',
				],
			]
		);

        $this->start_controls_tabs( 'tabs_album_trigger_button_style' );

        $this->start_controls_tab(
            'tab_album_trigger_button_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
				'condition'             => [
					'trigger'	=> 'button',
				],
            ]
        );

        $this->add_control(
            'album_trigger_button_text_color_normal',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-trigger-button' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-album-trigger-button svg' => 'fill: {{VALUE}}',
                ],
				'condition'             => [
					'trigger'	=> 'button',
				],
            ]
        );

        $this->add_control(
            'album_trigger_button_bg_color_normal',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-trigger-button' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
					'trigger'	=> 'button',
				],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'album_trigger_button_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-album-trigger-button',
				'condition'             => [
					'trigger'	=> 'button',
				],
			]
		);

		$this->add_control(
			'album_trigger_button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-album-trigger-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'trigger'	=> 'button',
				],
			]
		);
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'album_trigger_button_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-album-trigger-button',
				'condition'             => [
					'trigger'	=> 'button',
				],
            ]
        );

		$this->add_responsive_control(
			'album_trigger_button_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-album-trigger-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'trigger'	=> 'button',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'album_trigger_button_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-album-trigger-button',
				'condition'             => [
					'trigger'	=> 'button',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_album_trigger_button_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
				'condition'             => [
					'trigger'	=> 'button',
				],
            ]
        );

        $this->add_control(
            'album_trigger_button_text_color_hover',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-trigger-button:hover' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-album-trigger-button:hover svg' => 'fill: {{VALUE}}',
                ],
				'condition'             => [
					'trigger'	=> 'button',
				],
            ]
        );

        $this->add_control(
            'album_trigger_button_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-trigger-button:hover' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
					'trigger'	=> 'button',
				],
            ]
        );

        $this->add_control(
            'album_trigger_button_border_color_hover',
            [
                'label'                 => __( 'Border Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-album-trigger-button:hover' => 'border-color: {{VALUE}}',
                ],
				'condition'             => [
					'trigger'	=> 'button',
				],
            ]
        );

		$this->add_control(
			'album_trigger_button_animation',
			[
				'label'                 => __( 'Animation', 'powerpack' ),
				'type'                  => Controls_Manager::HOVER_ANIMATION,
				'condition'             => [
					'trigger'	=> 'button',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'album_trigger_button_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-album-trigger-button:hover',
				'condition'             => [
					'trigger'	=> 'button',
				],
			]
		);

        $this->end_controls_tab();
        $this->end_controls_tabs();
        
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'album', [
            'class'         => 'pp-album',
            'data-id'       => 'pp-album-'.esc_attr( $this->get_id() )
        ] );
		
		if ( $settings['trigger'] == 'cover' ) {
			$this->add_render_attribute( 'album', 'class', ['pp-album-cover-wrap', 'pp-ins-filter-hover'] );
		}
		
		if ( $settings['thumbs_position'] == 'bottom' ) {
			$this->add_render_attribute( 'album', [
				'data-fancybox-class'	=> 'pp-fancybox-thumbs-x',
				'data-fancybox-axis'	=> 'x'
			] );
		} else {
			$this->add_render_attribute( 'album', [
				'data-fancybox-class'	=> 'pp-fancybox-thumbs-y',
				'data-fancybox-axis'	=> 'y'
			] );
		}
        
        $this->add_render_attribute( 'album-gallery', 'class', 'pp-album-gallery' );
        ?>
        <div class="pp-album-container">
			<?php if ( !empty($settings['album_images']) ) { ?>
            <div <?php echo $this->get_render_attribute_string( 'album' ); ?>>
                <?php
					if ( $settings['trigger'] == 'cover' ) {
						// Album Cover
						$this->render_album_cover();
					} else {
						// Album Trigger Button
						echo $this->get_album_trigger_button();
					}
                ?>
                <div <?php echo $this->get_render_attribute_string( 'album-gallery' ); ?>>
                    <?php
                        $this->render_album_images();
                    ?>
                </div>
            </div>
			<?php } else {
				$placeholder = __( 'Choose some images for album in widget settings.', 'powerpack' );
					
				echo $this->render_editor_placeholder( [
					'title' => __( 'Album is empty!', 'powerpack' ),
					'body' => $placeholder,
				] );
			}
			?>
        </div>
        <?php
    }
    
    protected function render_album_images() {
        $settings = $this->get_settings_for_display();
		$gallery = $settings['album_images'];
        $isFirst = true;
        foreach ( $gallery as $index => $item ) {
            if ( $isFirst ) {
                $isFirst = false;
                continue;
            }

            $image_key = $this->get_repeater_setting_key('image', 'album_images', $index);

            $image_url = Group_Control_Image_Size::get_attachment_image_src( $item['id'], 'image', $settings );
			
			$thumbs_url = wp_get_attachment_image_src($item['id'], 'thumbnail');

            $this->add_render_attribute( $image_key, [
                'class'             => 'pp-album-image',
            ]);
            
            $this->get_lightbox_atts( $image_key, $item, $image_url, $index );
			
			$thumbs_html = '';
			
			if ( $settings['lightbox_library'] == 'fancybox' ) {
				if ( in_array('thumbs', $settings['toolbar_buttons']) || $settings['thumbs_auto_start'] == 'yes' ) {
					$thumbs_html = '<img src="'.$thumbs_url[0].'">';
				}
			}

            echo '<a ' . $this->get_render_attribute_string( $image_key ) . '>' . $thumbs_html . '</a>';
        }
    }
    
    protected function render_album_cover() {
        $settings = $this->get_settings_for_display();
        
        $this->add_render_attribute( 'album-cover', [
            'class' => ['pp-album-cover', 'pp-media-content', 'pp-ins-filter-target'],
        ] );
        
        $link_key = 'album-cover-link';

        $album = $settings['album_images'];
		
		if ( !empty( $album ) ) {
			$album_first_item = $album[0];
			$album_image_url = Group_Control_Image_Size::get_attachment_image_src( $album_first_item['id'], 'image', $settings );

			$this->get_lightbox_atts( $link_key, $album_first_item, $album_image_url );
			?>
			<a <?php echo $this->get_render_attribute_string( $link_key ); ?>>
				<div <?php echo $this->get_render_attribute_string( 'album-cover' ); ?>>
				<?php
					if ( $settings['album_cover_type'] == 'custom_img' ) {
						$image_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'album_cover', 'album_cover' );
					} else {
						$cover_image_id = $album_first_item['id'];
						$cover_image_url = Group_Control_Image_Size::get_attachment_image_src( $cover_image_id, 'album_cover', $settings );

						$image_html = '<img src="' . $cover_image_url . '" alt="' . esc_attr( Control_Media::get_image_alt( $album_first_item ) ) . '"/>';
					}

					$image_html .= $this->render_image_overlay();

					$image_html .= $this->get_album_content();

					echo $image_html;
				?>
				</div>
			</a>
			<?php
		}
    }
    
    protected function get_album_content() {
        $settings = $this->get_settings_for_display();
		
		ob_start();
		if ( ! isset( $settings['album_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['album_icon'] = '';
		}

		$has_icon = ! empty( $settings['album_icon'] );
		
		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['album_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}
		
		if ( ! $has_icon && ! empty( $settings['select_album_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['select_album_icon'] );
		$is_new = ! isset( $settings['album_icon'] ) && Icons_Manager::is_migration_allowed();
		
		$content_html = '';
		$is_icon = '';

        if ( $has_icon || $settings['album_title'] || $settings['album_subtitle'] || $settings['album_cover_button'] == 'yes' ) { ?>
			<div class="pp-album-content-wrap pp-media-content">
				<div class="pp-album-content">
					<div class="pp-album-content-inner">
						<?php if ( $has_icon ) { ?>
							<div class="pp-icon pp-album-icon">
								<?php
								if ( $is_new || $migrated ) {
									Icons_Manager::render_icon( $settings['select_album_icon'], [ 'aria-hidden' => 'true' ] );
								} elseif ( ! empty( $settings['album_icon'] ) ) {
									?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
								}
								?>
							</div>
						<?php } ?>
						<?php
							if ( $settings['album_title'] ) {
								echo $this->get_album_title();
							}

							if ( $settings['album_subtitle'] ) {
								$content_html .= $this->get_album_subtitle();
							}
						?>
					</div>
					<?php
						if ( $settings['album_cover_button'] == 'yes' ) {
							echo $this->get_album_cover_button();
						}
					?>
            	</div>
            </div>
			<?php
        }

		$html = ob_get_contents();
		ob_end_clean();
		return $html;
    }
	
    protected function get_album_title() {
        $settings = $this->get_settings_for_display();
		
		$title_html = '';
        
        $this->add_render_attribute( 'album_title', 'class', 'pp-album-title' );
		
		$title_html .= sprintf( '<%1$s %2$s>', $settings['album_title_html_tag'], $this->get_render_attribute_string( 'album_title' ) );
			$title_html .= $settings['album_title'];
		$title_html .= sprintf( '</%1$s>', $settings['album_title_html_tag'] );
		
		return $title_html;
    }
	
    protected function get_album_subtitle() {
        $settings = $this->get_settings_for_display();
		
		$subtitle_html = '';
        
        $this->add_render_attribute( 'album_subtitle', 'class', 'pp-album-subtitle' );
		
		$subtitle_html .= sprintf( '<%1$s %2$s>', $settings['album_subtitle_html_tag'], $this->get_render_attribute_string( 'album_subtitle' ) );
			$subtitle_html .= $settings['album_subtitle'];
		$subtitle_html .= sprintf( '</%1$s>', $settings['album_subtitle_html_tag'] );

		return $subtitle_html;
    }
	
    protected function get_album_cover_button() {
        $settings = $this->get_settings_for_display();
		ob_start();
        
        $this->add_render_attribute( 'cover-button', 'class', [
				'pp-album-cover-button',
				'elementor-button',
				'elementor-size-' . $settings['album_cover_button_size'],
			]
		);

		if ( $settings['album_cover_button_animation'] ) {
			$this->add_render_attribute( 'cover-button', 'class', 'elementor-animation-' . $settings['album_cover_button_animation'] );
		}
		
		if ( ! isset( $settings['album_cover_button_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['album_cover_button_icon'] = '';
		}

		$has_icon = ! empty( $settings['album_cover_button_icon'] );
		
		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['album_cover_button_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}

		$icon_attributes = $this->get_render_attribute_string( 'album_cover_button_icon' );
		
		if ( ! $has_icon && ! empty( $settings['select_album_cover_button_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['select_album_cover_button_icon'] );
		$is_new = ! isset( $settings['album_cover_button_icon'] ) && Icons_Manager::is_migration_allowed();
		?>
		<div class="pp-album-cover-button-wrap">
			<div <?php echo $this->get_render_attribute_string( 'cover-button' ) ?>>
				<?php if ( ! empty( $settings['album_cover_button_icon'] ) || ( ! empty( $settings['select_album_cover_button_icon']['value'] ) && $is_new ) ) { ?>
				<?php if ( $settings['album_cover_button_icon_position'] == 'before' ) { ?>
					<span class="pp-button-icon pp-icon pp-no-trans">
						<?php
						if ( $is_new || $migrated ) {
							Icons_Manager::render_icon( $settings['select_album_cover_button_icon'], [ 'aria-hidden' => 'true' ] );
						} elseif ( ! empty( $settings['album_cover_button_icon'] ) ) {
							?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
						}
						?>
					</span>
				<?php } ?>
				<?php } ?>
				<?php if ( ! empty( $settings['album_cover_button_text'] ) ) { ?>
					<span <?php echo $this->get_render_attribute_string( 'album_cover_button_text' ); ?>>
						<?php echo esc_attr( $settings['album_cover_button_text'] ); ?>
					</span>
				<?php } ?>
				<?php if ( ! empty( $settings['album_cover_button_icon'] ) || ( ! empty( $settings['select_album_cover_button_icon']['value'] ) && $is_new ) ) { ?>
				<?php if ( $settings['album_cover_button_icon_position'] == 'after' ) { ?>
					<span class="pp-button-icon pp-icon pp-no-trans">
						<?php
						if ( $is_new || $migrated ) {
							Icons_Manager::render_icon( $settings['select_album_cover_button_icon'], [ 'aria-hidden' => 'true' ] );
						} elseif ( ! empty( $settings['album_cover_button_icon'] ) ) {
							?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
						}
						?>
					</span>
				<?php } ?>
				<?php } ?>
			</div>
		</div>
		<?php

		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	
    protected function get_album_trigger_button() {
        $settings = $this->get_settings_for_display();
		ob_start();
        
        $this->add_render_attribute( 'trigger-button', 'class', [
				'pp-album-trigger-button',
				'elementor-button',
				'elementor-size-' . $settings['album_trigger_button_size'],
			]
		);

		if ( $settings['album_cover_button_animation'] ) {
			$this->add_render_attribute( 'trigger-button', 'class', 'elementor-animation-' . $settings['album_cover_button_animation'] );
		}
		
		$album = $settings['album_images'];
        $album_first_item = $album[0];
        $album_image_url = Group_Control_Image_Size::get_attachment_image_src( $album_first_item['id'], 'image', $settings );
        
        $this->get_lightbox_atts( 'trigger-button', $album_first_item, $album_image_url );
		
		$this->add_render_attribute( 'trigger-button-content', 'class', 'pp-album-button-content' );
		
		if ( ! isset( $settings['album_trigger_button_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['album_trigger_button_icon'] = 'fa fa-star';
		}

		$has_icon = ! empty( $settings['album_trigger_button_icon'] );
		
		if ( $has_icon ) {
			$this->add_render_attribute( 'i', 'class', $settings['album_trigger_button_icon'] );
			$this->add_render_attribute( 'i', 'aria-hidden', 'true' );
		}

		$icon_attributes = $this->get_render_attribute_string( 'album_trigger_button_icon' );
		
		if ( ! $has_icon && ! empty( $settings['select_album_trigger_button_icon']['value'] ) ) {
			$has_icon = true;
		}
		$migrated = isset( $settings['__fa4_migrated']['select_album_trigger_button_icon'] );
		$is_new = ! isset( $settings['album_trigger_button_icon'] ) && Icons_Manager::is_migration_allowed();
		
		if ( ! empty( $settings['album_trigger_button_icon'] ) || ( ! empty( $settings['select_album_trigger_button_icon']['value'] ) && $is_new ) ) {
        	$this->add_render_attribute( 'trigger-button', 'class', 'pp-album-trigger-icon-' . $settings['album_trigger_button_icon_position'] );
		}
		?>
		<div class="pp-album-trigger-button-wrap">
			<a <?php echo $this->get_render_attribute_string( 'trigger-button' ) ?>>
				<span <?php echo $this->get_render_attribute_string( 'trigger-button-content' ) ?>>
					<?php if ( ! empty( $settings['album_trigger_button_text'] ) ) { ?>
						<span <?php echo $this->get_render_attribute_string( 'album_trigger_button_text' ); ?>>
							<?php echo esc_attr( $settings['album_trigger_button_text'] ); ?>
						</span>
					<?php } ?>
					<?php if ( ! empty( $settings['album_trigger_button_icon'] ) || ( ! empty( $settings['select_album_trigger_button_icon']['value'] ) && $is_new ) ) { ?>
						<span class="pp-button-icon pp-icon">
						<?php
						if ( $is_new || $migrated ) {
							Icons_Manager::render_icon( $settings['select_album_trigger_button_icon'], [ 'aria-hidden' => 'true' ] );
						} elseif ( ! empty( $settings['album_trigger_button_icon'] ) ) {
							?><i <?php echo $this->get_render_attribute_string( 'i' ); ?>></i><?php
						}
						?>
						</span>
					<?php } ?>
				</span>
			</a>
		</div>
		<?php

		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
    
    protected function get_lightbox_atts( $key = '', $item = '', $link = '', $index = 0 ) {
        $settings = $this->get_settings_for_display();
        
        if ( $settings['lightbox_library'] == 'fancybox' ) {
            $this->add_render_attribute( $key, [
                'data-elementor-open-lightbox'      => 'no',
                'data-fancybox'     => 'pp-album-' . esc_attr( $this->get_id() ),
            ] );
            
            if ( $settings['lightbox_caption'] != '' ) {
                $caption = Module::get_image_caption( $item['id'], $settings['lightbox_caption'] );

                $this->add_render_attribute( $key, [
                    'data-caption'  => $caption,
                ]);
            }

            $this->add_render_attribute( $key, [
                'data-src'              => esc_url( $link ),
            ] );
        } else {
            $this->add_render_attribute( $key, [
                'data-elementor-open-lightbox' 		=> 'yes',
                'data-elementor-lightbox-slideshow' => $this->get_id(),
                'data-elementor-lightbox-index' 	=> $index,
            ] );

            $this->add_render_attribute( $key, [
                'href' 								=> esc_url( $link ),
                'class' 							=> 'elementor-clickable',
            ] );
        }
    }
    
    protected function render_image_overlay() {
        return '<div class="pp-album-cover-overlay pp-media-overlay"></div>';
    }

    protected function _content_template() {
    }
}