<?php
namespace PowerpackElements\Modules\Table\Widgets;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Table Widget
 */
class Table extends Powerpack_Widget {
    
    public function get_name() {
        return parent::get_widget_name( 'Table' );
    }

    public function get_title() {
        return parent::get_widget_title( 'Table' );
    }

    public function get_categories() {
        return parent::get_widget_categories( 'Table' );
    }

    public function get_icon() {
        return parent::get_widget_icon( 'Table' );
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
		return parent::get_widget_keywords( 'Table' );
	}
    
    public function get_script_depends() {
        return [
            'tablesaw',
            'powerpack-frontend',
        ];
    }
    
    public function get_style_depends() {
        return [
            'tablesaw',
        ];
    }

    protected function _register_controls() {
		
		/* Content Tab */
		$this->register_content_general_controls();
		$this->register_content_header_controls();
		$this->register_content_body_controls();
		$this->register_content_footer_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_table_controls();
		$this->register_style_header_controls();
		$this->register_style_rows_controls();
		$this->register_style_cells_controls();
		$this->register_style_footer_controls();
		$this->register_style_icon_controls();
		$this->register_style_columns_controls();
	}
	
	protected function register_content_general_controls() {
        $this->start_controls_section(
            'section_general',
            [
                'label'                 => __( 'General', 'powerpack' ),
            ]
        );

        $this->add_control(
            'source',
            [
                'label'                 => __( 'Source', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'manual',
                'options'               => [
                    'manual' => __( 'Manual', 'powerpack' ),
                    'file'   => __( 'CSV File', 'powerpack' ),
                ],
            ]
        );

        $this->add_control(
            'file',
            [
                'label'                 => __( 'Upload a CSV File', 'powerpack' ),
                'type'                  => Controls_Manager::MEDIA,
                'dynamic'               => [
                    'active' => true,
					'categories' => [
						TagsModule::MEDIA_CATEGORY,
					],
                ],
                'condition'             => [
                    'source' => 'file',
                ],
            ]
        );

        $this->add_control(
            'table_type',
            [
                'label'                 => __( 'Table Type', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'responsive',
                'options'               => [
                    'normal'		=> __( 'Normal', 'powerpack' ),
                    'responsive'	=> __( 'Responsive', 'powerpack' ),
                ],
				'frontend_available'    => true,
                'separator'             => 'before'
            ]
        );
        
        $this->add_control(
            'sortable',
            [
                'label'					=> __( 'Sortable Table', 'powerpack' ),
                'description'			=> __( 'Enable sorting the table data by clicking on the table headers', 'powerpack' ),
                'type'					=> Controls_Manager::SWITCHER,
                'default'				=> 'no',
                'label_on'				=> __( 'On', 'powerpack' ),
                'label_off'				=> __( 'Off', 'powerpack' ),
                'return_value'			=> 'yes',
				'condition'             => [
					'table_type' => 'responsive',
				],
            ]
        );
        
        $this->add_control(
            'sortable_dropdown',
            [
                'label'					=> __( 'Sortable Dropdown', 'powerpack' ),
                'description'			=> __( 'This will show dropdown menu to sort the table by columns', 'powerpack' ),
                'type'					=> Controls_Manager::SWITCHER,
                'default'				=> 'show',
                'label_on'				=> __( 'Show', 'powerpack' ),
                'label_off'				=> __( 'Hide', 'powerpack' ),
                'return_value'			=> 'show',
				'condition'             => [
					'table_type'	=> 'responsive',
					'sortable'		=> 'yes',
				],
            ]
        );
        
        $this->add_control(
            'scrollable',
            [
                'label'					=> __( 'Scrollable Table', 'powerpack' ),
                'description'			=> __( 'This will disable stacking and enable swipe/scroll when below breakpoint', 'powerpack' ),
                'type'					=> Controls_Manager::SWITCHER,
                'default'				=> 'no',
                'label_on'				=> __( 'On', 'powerpack' ),
                'label_off'				=> __( 'Off', 'powerpack' ),
                'return_value'			=> 'yes',
				'frontend_available'    => true,
				'condition'             => [
					'table_type' => 'responsive',
				],
            ]
        );
        
        $this->add_control(
            'breakpoint',
            [
                'label'                 => __( 'Breakpoint', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
                'placeholder'           => '',
				'default'               => '',
				'frontend_available'    => true,
				'condition'             => [
					'table_type' => 'responsive',
					'scrollable' => 'yes',
				],
            ]
        );
        
        $this->end_controls_section();
	}
	
	protected function register_content_header_controls() {

        $this->start_controls_section(
            'section_headers',
            [
                'label'                 => __( 'Header', 'powerpack' ),
				'condition'             => [
					'source' => 'manual',
				],
            ]
        );
        
        $repeater_header = new Repeater();
        
        $repeater_header->start_controls_tabs( 'table_header_tabs' );

        $repeater_header->start_controls_tab( 'table_header_tab_content', [ 'label' => __( 'Content', 'powerpack' ) ] );
        
        $repeater_header->add_control(
            'table_header_col',
            [
                'label'                 => __( 'Text', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
                'placeholder'           => __( 'Table Header', 'powerpack' ),
				'default'               => __( 'Table Header', 'powerpack' ),
            ]
        );
        
        $repeater_header->end_controls_tab();
        
        $repeater_header->start_controls_tab( 'table_header_tab_icon', [ 'label' => __( 'Icon', 'powerpack' ) ] );
        
        $repeater_header->add_control(
			'cell_icon_type',
			[
				'label'                 => esc_html__( 'Icon Type', 'powerpack' ),
				'label_block'           => false,
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'none'        => [
						'title'   => esc_html__( 'None', 'powerpack' ),
						'icon'    => 'fa fa-ban',
					],
					'icon'        => [
						'title'   => esc_html__( 'Icon', 'powerpack' ),
						'icon'    => 'fa fa-star',
					],
					'image'       => [
						'title'   => esc_html__( 'Image', 'powerpack' ),
						'icon'    => 'fa fa-picture-o',
					],
				],
				'default'               => 'none',
			]
		);

		$repeater_header->add_control(
			'select_cell_icon',
			[
				'label'					=> __( 'Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'label_block'			=> true,
				'fa4compatibility'		=> 'cell_icon',
				'condition'				=> [
					'cell_icon_type' => 'icon',
				],
			]
		);
        
        $repeater_header->add_control(
            'cell_icon_image',
            [
                'label'                 => __( 'Image', 'powerpack' ),
				'label_block'           => true,
                'type'                  => Controls_Manager::MEDIA,
				'dynamic'               => [
					'active'   => true,
				],
                'default'               => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
				'condition'             => [
					'cell_icon_type' => 'image',
				],
            ]
        );
        
        $repeater_header->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'                  => 'cell_icon_image',
                'label'                 => __( 'Image Size', 'powerpack' ),
                'default'               => 'full',
                'exclude'               => [ 'custom' ],
				'condition'             => [
					'cell_icon_type' => 'image',
				],
            ]
        );
        
        $repeater_header->add_control(
            'cell_icon_position',
            [
                'label'                 => __( 'Icon Position', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'before',
                'options'               => [
                    'before'    => __( 'Before', 'powerpack' ),
                    'after'     => __( 'After', 'powerpack' ),
                ],
				'condition'             => [
					'cell_icon_type!'   => 'none',
				],
            ]
        );
        
        $repeater_header->end_controls_tab();
        
        $repeater_header->start_controls_tab( 'table_header_tab_advanced', [ 'label' => __( 'Advanced', 'powerpack' ) ] );
        
        $repeater_header->add_control(
            'col_span',
            [
                'label'                 => __( 'Column Span', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'default'               => '',
            ]
        );
        
        $repeater_header->add_control(
            'row_span',
            [
                'label'                 => __( 'Row Span', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'default'               => '',
            ]
        );
        
        $repeater_header->add_control(
            'css_id',
            [
                'label'                 => __( 'CSS ID', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'default'               => '',
            ]
        );
        
        $repeater_header->add_control(
            'css_classes',
            [
                'label'                 => __( 'CSS Classes', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'default'               => '',
            ]
        );
        
        $repeater_header->end_controls_tab();
        
        $repeater_header->end_controls_tabs();

		$this->add_control(
			'table_headers',
			[
				'label'                 => '',
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[
						'table_header_col' => __( 'Header Column #1', 'powerpack' ),
					],
					[
						'table_header_col' => __( 'Header Column #2', 'powerpack' ),
					],
					[
						'table_header_col' => __( 'Header Column #3', 'powerpack' ),
					],
				],
				'fields'                => array_values( $repeater_header->get_controls() ),
				'title_field'           => '{{{ table_header_col }}}',
			]
		);
        
        $this->end_controls_section();
	}
	
	protected function register_content_body_controls() {

        $this->start_controls_section(
            'section_body',
            [
                'label'                 => __( 'Body', 'powerpack' ),
				'condition'             => [
					'source' => 'manual',
				],
            ]
        );
        
        $repeater_rows = new Repeater();
        
        $repeater_rows->add_control(
            'table_body_element',
            [
                'label'                 => __( 'Element Type', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'cell',
                'options'               => [
                    'row'       => __( 'Row', 'powerpack' ),
                    'cell'      => __( 'Cell', 'powerpack' ),
                ],
            ]
        );
        
        $repeater_rows->start_controls_tabs( 'table_body_tabs' );

        $repeater_rows->start_controls_tab( 'table_body_tab_content',
            [
                'label'                 => __( 'Content', 'powerpack' ),
				'condition'             => [
					'table_body_element' => 'cell',
				],
            ]
        );
        
        $repeater_rows->add_control(
            'cell_text',
            [
                'label'                 => __( 'Text', 'powerpack' ),
                'type'                  => Controls_Manager::TEXTAREA,
				'dynamic'               => [
					'active'   => true,
				],
                'placeholder'           => '',
				'default'               => '',
				'condition'             => [
					'table_body_element' => 'cell',
				],
            ]
        );

		$repeater_rows->add_control(
			'link',
			[
				'label'					=> __( 'Link', 'powerpack' ),
				'type'					=> Controls_Manager::URL,
				'placeholder'			=> '#',
				'dynamic'				=> [
					'active' => true,
				],
				'default'				=> [
					'url' => '',
				],
				'condition'				=> [
					'table_body_element' => 'cell',
				],
			]
		);
        
        $repeater_rows->end_controls_tab();
        
        $repeater_rows->start_controls_tab( 'table_body_tab_icon',
            [
                'label'                 => __( 'Icon', 'powerpack' ),
				'condition'             => [
					'table_body_element' => 'cell',
				],
            ]
        );
        
        $repeater_rows->add_control(
			'cell_icon_type',
			[
				'label'                 => esc_html__( 'Icon Type', 'powerpack' ),
				'label_block'           => false,
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'none' => [
						'title' => esc_html__( 'None', 'powerpack' ),
						'icon' => 'fa fa-ban',
					],
					'icon' => [
						'title' => esc_html__( 'Icon', 'powerpack' ),
						'icon' => 'fa fa-star',
					],
					'image' => [
						'title' => esc_html__( 'Image', 'powerpack' ),
						'icon' => 'fa fa-picture-o',
					],
				],
				'default'               => 'none',
				'condition'             => [
					'table_body_element' => 'cell',
				],
			]
		);

		$repeater_rows->add_control(
			'select_cell_icon',
			[
				'label'					=> __( 'Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'label_block'			=> true,
				'fa4compatibility'		=> 'cell_icon',
				'condition'				=> [
					'table_body_element'   => 'cell',
					'cell_icon_type'       => 'icon',
				],
			]
		);
        
        $repeater_rows->add_control(
            'cell_icon_image',
            [
                'label'                 => __( 'Image', 'powerpack' ),
				'label_block'           => true,
                'type'                  => Controls_Manager::MEDIA,
				'dynamic'               => [
					'active'   => true,
				],
                'default'               => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
				'condition'             => [
					'table_body_element'   => 'cell',
					'cell_icon_type'       => 'image',
				],
            ]
        );
        
        $repeater_rows->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'                  => 'cell_icon_image',
                'label'                 => __( 'Image Size', 'powerpack' ),
                'default'               => 'full',
                'exclude'               => [ 'custom' ],
				'condition'             => [
					'table_body_element'   => 'cell',
					'cell_icon_type'       => 'image',
				],
            ]
        );
        
        $repeater_rows->add_control(
            'cell_icon_position',
            [
                'label'                 => __( 'Icon Position', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'before',
                'options'               => [
                    'before'    => __( 'Before', 'powerpack' ),
                    'after'     => __( 'After', 'powerpack' ),
                ],
				'condition'             => [
					'table_body_element'   => 'cell',
					'cell_icon_type!'      => 'none',
				],
            ]
        );
        
        $repeater_rows->end_controls_tab();
        
        $repeater_rows->start_controls_tab( 'table_body_tab_advanced',
            [
                'label'                 => __( 'Advanced', 'powerpack' ),
				'condition'             => [
					'table_body_element' => 'cell',
				],
            ]
        );
        
        $repeater_rows->add_control(
            'col_span',
            [
                'label'                 => __( 'Column Span', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'default'               => '',
				'condition'             => [
					'table_body_element' => 'cell',
				],
            ]
        );
        
        $repeater_rows->add_control(
            'row_span',
            [
                'label'                 => __( 'Row Span', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'default'               => '',
				'condition'             => [
					'table_body_element' => 'cell',
				],
            ]
        );
        
        $repeater_rows->add_control(
            'css_id',
            [
                'label'                 => __( 'CSS ID', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'default'               => '',
            ]
        );
        
        $repeater_rows->add_control(
            'css_classes',
            [
                'label'                 => __( 'CSS Classes', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'default'               => '',
            ]
        );
        
        $repeater_rows->end_controls_tab();
        
        $repeater_rows->end_controls_tabs();

		$this->add_control(
			'table_body_content',
			[
				'label'                 => '',
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[
						'table_body_element'  => 'row',
					],
					[
						'table_body_element'  => 'cell',
						'cell_text'           => __( 'Column #1', 'powerpack' ),
					],
					[
						'table_body_element'  => 'cell',
						'cell_text'           => __( 'Column #2', 'powerpack' ),
					],
					[
						'table_body_element'  => 'cell',
						'cell_text'           => __( 'Column #3', 'powerpack' ),
					],
					[
						'table_body_element'  => 'row',
					],
					[
						'table_body_element'  => 'cell',
						'cell_text'           => __( 'Column #1', 'powerpack' ),
					],
					[
						'table_body_element'  => 'cell',
						'cell_text'           => __( 'Column #2', 'powerpack' ),
					],
					[
						'table_body_element'  => 'cell',
						'cell_text'           => __( 'Column #3', 'powerpack' ),
					],
				],
				'fields'                => array_values( $repeater_rows->get_controls() ),
				'title_field'           => __( 'Table', 'powerpack' ) . ' {{{ table_body_element }}}: {{{ cell_text }}}',
			]
		);
        
        $this->end_controls_section();
	}
	
	protected function register_content_footer_controls() {

        $this->start_controls_section(
            'section_footer',
            [
                'label'                 => __( 'Footer', 'powerpack' ),
				'condition'             => [
					'source' => 'manual',
				],
            ]
        );
        
        $repeater_footer_rows = new Repeater();
        
        $repeater_footer_rows->add_control(
            'table_footer_element',
            [
                'label'                 => __( 'Element Type', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'cell',
                'options'               => [
                    'row'       => __( 'Row', 'powerpack' ),
                    'cell'      => __( 'Cell', 'powerpack' ),
                ],
            ]
        );
        
        $repeater_footer_rows->start_controls_tabs( 'table_footer_tabs' );

        $repeater_footer_rows->start_controls_tab( 'table_footer_tab_content',
            [
                'label'                 => __( 'Content', 'powerpack' ),
				'condition'             => [
					'table_footer_element' => 'cell',
				],
            ]
        );
        
        $repeater_footer_rows->add_control(
            'cell_text',
            [
                'label'                 => __( 'Text', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
                'placeholder'           => '',
				'default'               => '',
				'condition'             => [
					'table_footer_element' => 'cell',
				],
            ]
        );
        
        $repeater_footer_rows->end_controls_tab();

        $repeater_footer_rows->start_controls_tab( 'table_footer_tab_icon',
            [
                'label'                 => __( 'Icon', 'powerpack' ),
				'condition'             => [
					'table_footer_element' => 'cell',
				],
            ]
        );
        
        $repeater_footer_rows->add_control(
			'cell_icon_type',
			[
				'label'                 => esc_html__( 'Icon Type', 'powerpack' ),
				'label_block'       => false,
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'none' => [
						'title' => esc_html__( 'None', 'powerpack' ),
						'icon' => 'fa fa-ban',
					],
					'icon' => [
						'title' => esc_html__( 'Icon', 'powerpack' ),
						'icon' => 'fa fa-star',
					],
					'image' => [
						'title' => esc_html__( 'Image', 'powerpack' ),
						'icon' => 'fa fa-picture-o',
					],
				],
				'default'               => 'none',
				'condition'             => [
					'table_footer_element' => 'cell',
				],
			]
		);

		$repeater_footer_rows->add_control(
			'select_cell_icon',
			[
				'label'					=> __( 'Icon', 'powerpack' ),
				'type'					=> Controls_Manager::ICONS,
				'label_block'			=> true,
				'fa4compatibility'		=> 'cell_icon',
				'condition'				=> [
					'table_footer_element' => 'cell',
					'cell_icon_type' => 'icon',
				],
			]
		);
        
        $repeater_footer_rows->add_control(
            'cell_icon_image',
            [
                'label'                 => __( 'Image', 'powerpack' ),
				'label_block'       	=> true,
                'type'                  => Controls_Manager::MEDIA,
				'dynamic'               => [
					'active'   => true,
				],
                'default'               => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
				'condition'             => [
					'table_footer_element' => 'cell',
					'cell_icon_type' => 'image',
				],
            ]
        );
        
        $repeater_footer_rows->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'                  => 'cell_icon_image',
                'label'                 => __( 'Image Size', 'powerpack' ),
                'default'               => 'full',
                'exclude'               => [ 'custom' ],
				'condition'             => [
					'table_footer_element' => 'cell',
					'cell_icon_type' => 'image',
				],
            ]
        );
        
        $repeater_footer_rows->add_control(
            'cell_icon_position',
            [
                'label'                 => __( 'Icon Position', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'before',
                'options'               => [
                    'before'    => __( 'Before', 'powerpack' ),
                    'after'     => __( 'After', 'powerpack' ),
                ],
				'condition'             => [
					'table_footer_element' => 'cell',
					'cell_icon_type!' => 'none',
				],
            ]
        );
        
        $repeater_footer_rows->end_controls_tab();

        $repeater_footer_rows->start_controls_tab( 'table_footer_tab_advanced',
            [
                'label'                 => __( 'Advanced', 'powerpack' ),
				'condition'             => [
					'table_footer_element' => 'cell',
				],
            ]
        );
        
        $repeater_footer_rows->add_control(
            'col_span',
            [
                'label'                 => __( 'Column Span', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'default'               => '',
				'condition'             => [
					'table_footer_element' => 'cell',
				],
            ]
        );
        
        $repeater_footer_rows->add_control(
            'row_span',
            [
                'label'                 => __( 'Row Span', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'default'               => '',
				'condition'             => [
					'table_footer_element' => 'cell',
				],
            ]
        );
        
        $repeater_footer_rows->add_control(
            'css_id',
            [
                'label'                 => __( 'CSS ID', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'default'               => '',
            ]
        );
        
        $repeater_footer_rows->add_control(
            'css_classes',
            [
                'label'                 => __( 'CSS Classes', 'powerpack' ),
                'type'                  => Controls_Manager::TEXT,
				'default'               => '',
            ]
        );
        
        $repeater_footer_rows->end_controls_tab();

        $repeater_footer_rows->end_controls_tabs();

		$this->add_control(
			'table_footer_content',
			[
				'label'                 => '',
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[
						'col_span' => '',
					],
				],
				'fields'                => array_values( $repeater_footer_rows->get_controls() ),
				'title_field'           => 'Table {{{ table_footer_element }}}: {{{ cell_text }}}',
			]
		);
        
        $this->end_controls_section();
	}
	
	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links('pp-table');
		if ( !empty($help_docs) ) {
			/**
			 * Content Tab: Docs Links
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
	protected function register_style_table_controls() {
        
        $this->start_controls_section(
            'section_table_style',
            [
                'label'                 => __( 'Table', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'table_width',
            [
                'label'                 => __( 'Width', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'size_units'            => [ '%', 'px' ],
                'range'                 => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1200,
                    ],
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-table' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'table_align',
            [
                'label'                 => __( 'Alignment', 'powerpack' ),
                'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
                'default'               => 'center',
                'options'               => [
                    'left' 		=> [
                        'title' => __( 'Left', 'powerpack' ),
                        'icon' 	=> 'eicon-h-align-left',
                    ],
                    'center' 	=> [
                        'title' => __( 'Center', 'powerpack' ),
                        'icon' 	=> 'eicon-h-align-center',
                    ],
                    'right' 	=> [
                        'title' => __( 'Right', 'powerpack' ),
                        'icon' 	=> 'eicon-h-align-right',
                    ],
                ],
                'prefix_class'           => 'pp-table-',
            ]
        );

		$this->add_control(
			'table_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-table-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->end_controls_section();
	}
	
	protected function register_style_header_controls() {
        
        $this->start_controls_section(
            'section_table_header_style',
            [
                'label'                 => __( 'Header', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'table_header_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-table thead th.pp-table-cell',
            ]
        );

        $this->start_controls_tabs( 'tabs_header_style' );

        $this->start_controls_tab(
            'tab_header_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
            ]
        );

        $this->add_control(
            'table_header_text_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table thead th.pp-table-cell .pp-table-cell-content' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-table thead th.pp-table-cell .pp-icon svg' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'table_header_sortable_icon_color',
            [
                'label'                 => __( 'Sortable Icon Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table thead th.pp-table-cell .tablesaw-sortable-arrow' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'table_header_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '#e6e6e6',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table thead th.pp-table-cell' => 'background-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'section_table_header_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-table thead th.pp-table-cell',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_header_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
            ]
        );

        $this->add_control(
            'table_header_text_color_hover',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table thead th.pp-table-cell:hover .pp-table-cell-content' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-table thead th.pp-table-cell:hover .pp-icon svg' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'table_header_sortable_icon_color_hover',
            [
                'label'                 => __( 'Sortable Icon Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table thead th.pp-table-cell:hover .tablesaw-sortable-arrow' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'table_header_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table thead th.pp-table-cell:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->start_controls_tabs( 'tabs_header_default_style' );

        $this->start_controls_tab(
            'tab_header_default',
            [
                'label'                 => __( 'Default', 'powerpack' ),
            ]
        );
        
        $this->add_control(
			'table_header_align',
			[
				'label'                 => __( 'Horizontal Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
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
                'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'center'   => 'center',
					'right'    => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table thead .pp-table-cell-content'   => 'justify-content: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_header_text_vertical_align',
			[
				'label'                 => __( 'Text Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table thead th'   => 'vertical-align: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_header_vertical_align',
			[
				'label'                 => __( 'Icon Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
                'default'				=> 'middle',
                'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'middle'   => 'center',
					'bottom'   => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table thead .pp-table-cell-content'   => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'table_header_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-table thead th.pp-table-cell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'table_header_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-table thead',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_header_col_first',
            [
                'label'                 => __( 'First', 'powerpack' ),
            ]
        );
        
        $this->add_control(
			'table_header_col_first_align',
			[
				'label'                 => __( 'Horizontal Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
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
                'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'center'   => 'center',
					'right'    => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table thead .pp-table-cell:first-child .pp-table-cell-content'   => 'justify-content: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_header_col_first_text_vertical_align',
			[
				'label'                 => __( 'Text Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table thead .pp-table-cell:first-child'   => 'vertical-align: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_header_col_first_vertical_align',
			[
				'label'                 => __( 'Icon Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
                'default'				=> 'middle',
                'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'middle'   => 'center',
					'bottom'   => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table thead .pp-table-cell:first-child .pp-table-cell-content'   => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'table_header_col_first_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-table thead .pp-table-cell:first-child' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'table_header_col_first_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-table thead .pp-table-cell:first-child',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_header_col_last',
            [
                'label'                 => __( 'Last', 'powerpack' ),
            ]
        );
        
        $this->add_control(
			'table_header_col_last_align',
			[
				'label'                 => __( 'Horizontal Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
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
                'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'center'   => 'center',
					'right'    => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table thead .pp-table-cell:last-child .pp-table-cell-content'   => 'justify-content: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_header_col_last_text_vertical_align',
			[
				'label'                 => __( 'Text Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table thead .pp-table-cell:last-child'   => 'vertical-align: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_header_col_last_vertical_align',
			[
				'label'                 => __( 'Icon Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
                'default'				=> 'middle',
                'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'middle'   => 'center',
					'bottom'   => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table thead .pp-table-cell:last-child .pp-table-cell-content'   => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'table_header_col_last_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-table thead .pp-table-cell:last-child' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'table_header_col_last_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-table thead .pp-table-cell:last-child',
			]
		);

        $this->end_controls_tab();

        $this->end_controls_tabs();
        
        $this->end_controls_section();
	}
	
	protected function register_style_rows_controls() {
        $this->start_controls_section(
            'section_table_rows_style',
            [
                'label'                 => __( 'Rows', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_control(
            'table_striped_rows',
            [
                'label'             => __( 'Striped Rows', 'powerpack' ),
                'type'              => Controls_Manager::SWITCHER,
                'default'           => 'no',
                'label_on'          => __( 'On', 'powerpack' ),
                'label_off'         => __( 'Off', 'powerpack' ),
                'return_value'      => 'yes',
            ]
        );

        $this->add_control(
            'table_rows_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tbody tr' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
					'table_striped_rows!' => 'yes',
				],
            ]
        );

        $this->add_control(
            'table_rows_text_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tbody tr .pp-table-cell-content' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'table_striped_rows!' => 'yes',
				],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'section_table_rowsborder',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-table tr',
				'condition'             => [
					'table_striped_rows!' => 'yes',
				],
			]
		);

        $this->start_controls_tabs( 'tabs_rows_alternate_style' );

        $this->start_controls_tab(
            'tab_row_even',
            [
                'label'                 => __( 'Even', 'powerpack' ),
				'condition'             => [
					'table_striped_rows' => 'yes',
				],
            ]
        );

        $this->add_control(
            'table_even_rows_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tr:nth-child(even)' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
					'table_striped_rows' => 'yes',
				],
            ]
        );

        $this->add_control(
            'table_even_rows_text_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tr:nth-child(even) .pp-table-cell-content' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'table_striped_rows' => 'yes',
				],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'section_table_even_rows_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
                'separator'             => 'before',
				'selector'              => '{{WRAPPER}} .pp-table tr:nth-child(even)',
				'condition'             => [
					'table_striped_rows' => 'yes',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_row_odd',
            [
                'label'                 => __( 'Odd', 'powerpack' ),
				'condition'             => [
					'table_striped_rows' => 'yes',
				],
            ]
        );

        $this->add_control(
            'table_odd_rows_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tr:nth-child(odd)' => 'background-color: {{VALUE}}',
                ],
				'condition'             => [
					'table_striped_rows' => 'yes',
				],
            ]
        );

        $this->add_control(
            'table_odd_rows_text_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tr:nth-child(odd) .pp-table-cell-content' => 'color: {{VALUE}}',
                ],
				'condition'             => [
					'table_striped_rows' => 'yes',
				],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'section_table_odd_rows_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
                'separator'             => 'before',
				'selector'              => '{{WRAPPER}} .pp-table tr:nth-child(odd)',
				'condition'             => [
					'table_striped_rows' => 'yes',
				],
			]
		);

        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
	}
	
	protected function register_style_cells_controls() {
        
        $this->start_controls_section(
            'section_table_cells_style',
            [
                'label'                 => __( 'Cells', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'table_cells_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-table tr .pp-table-cell',
            ]
        );

        $this->start_controls_tabs( 'tabs_cell_style' );

        $this->start_controls_tab(
            'tab_cell_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
            ]
        );

        $this->add_control(
            'table_cell_text_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table .pp-table-cell .pp-table-cell-content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'table_cell_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table .pp-table-cell' => 'background-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'section_cell_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-table .pp-table-cell',
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_cell_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
            ]
        );

        $this->add_control(
            'table_cell_text_color_hover',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table .pp-table-cell:hover .pp-table-cell-content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'table_cell_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table .pp-table-cell:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();

        $this->start_controls_tabs( 'tabs_cell_default_style' );

        $this->start_controls_tab(
            'tab_cell_default',
            [
                'label'                 => __( 'Default', 'powerpack' ),
            ]
        );
        
        $this->add_control(
			'table_cells_horizontal_align',
			[
				'label'                 => __( 'Horizontal Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center'           => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'            => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'               => '',
                'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'center'   => 'center',
					'right'    => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tbody .pp-table-cell-content'   => 'justify-content: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_cells_text_vertical_align',
			[
				'label'                 => __( 'Text Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tbody td'   => 'vertical-align: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_cells_vertical_align',
			[
				'label'                 => __( 'Icon Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
                'default'				=> 'middle',
                'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'middle'   => 'center',
					'bottom'   => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tbody .pp-table-cell-content'   => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'table_cell_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tbody td.pp-table-cell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_cell_first',
            [
                'label'                 => __( 'First', 'powerpack' ),
            ]
        );
        
        $this->add_control(
			'table_cell_first_horizontal_align',
			[
				'label'                 => __( 'Horizontal Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center'           => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'            => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'               => '',
                'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'center'   => 'center',
					'right'    => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tbody .pp-table-cell:first-child .pp-table-cell-content'   => 'justify-content: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_cell_first_text_vertical_align',
			[
				'label'                 => __( 'Text Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tbody .pp-table-cell:first-child td'   => 'vertical-align: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_cell_first_vertical_align',
			[
				'label'                 => __( 'Icon Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
                'default'				=> 'middle',
                'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'middle'   => 'center',
					'bottom'   => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tbody .pp-table-cell:first-child .pp-table-cell-content'   => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'table_cell_first_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tbody .pp-table-cell:first-child' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_cell_last',
            [
                'label'                 => __( 'Last', 'powerpack' ),
            ]
        );
        
        $this->add_control(
			'table_cell_last_horizontal_align',
			[
				'label'                 => __( 'Horizontal Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'fa fa-align-left',
					],
					'center'           => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'            => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'               => '',
                'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'center'   => 'center',
					'right'    => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tbody .pp-table-cell:last-child .pp-table-cell-content'   => 'justify-content: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_cell_last_text_vertical_align',
			[
				'label'                 => __( 'Text Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tbody .pp-table-cell:last-child td'   => 'vertical-align: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_cell_last_vertical_align',
			[
				'label'                 => __( 'Icon Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
                'default'				=> 'middle',
                'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'middle'   => 'center',
					'bottom'   => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tbody .pp-table-cell:last-child .pp-table-cell-content'   => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'table_cell_last_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tbody .pp-table-cell:last-child' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->end_controls_tab();
		
        $this->end_controls_tabs();
        
        $this->end_controls_section();
	}
	
	protected function register_style_footer_controls() {
        
        $this->start_controls_section(
            'section_table_footer_style',
            [
                'label'                 => __( 'Footer', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'table_footer_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
                'selector'              => '{{WRAPPER}} .pp-table tfoot td.pp-table-cell',
            ]
        );

        $this->start_controls_tabs( 'tabs_footer_style' );

        $this->start_controls_tab(
            'tab_footer_normal',
            [
                'label'                 => __( 'Normal', 'powerpack' ),
            ]
        );

        $this->add_control(
            'table_footer_text_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tfoot td.pp-table-cell .pp-table-cell-content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'table_footer_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tfoot td.pp-table-cell' => 'background-color: {{VALUE}}',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'section_table_footer_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-table tfoot td.pp-table-cell',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_footer_hover',
            [
                'label'                 => __( 'Hover', 'powerpack' ),
            ]
        );

        $this->add_control(
            'table_footer_text_color_hover',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tfoot td.pp-table-cell:hover .pp-table-cell-content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'table_footer_bg_color_hover',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tfoot td.pp-table-cell:hover' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->start_controls_tabs( 'tabs_footer_default_cell_style' );

        $this->start_controls_tab(
            'tab_footer_default_cell',
            [
                'label'                 => __( 'Default', 'powerpack' ),
            ]
        );
        
        $this->add_control(
			'table_footer_align',
			[
				'label'                 => __( 'Horizontal Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'label_block'           => false,
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
                'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'center'   => 'center',
					'right'    => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tfoot .pp-table-cell-content'   => 'justify-content: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_footer_text_vertical_align',
			[
				'label'                 => __( 'Text Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tfoot td'   => 'vertical-align: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_footer_vertical_align',
			[
				'label'                 => __( 'Icon Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
                'default'				=> 'middle',
                'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'middle'   => 'center',
					'bottom'   => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tfoot .pp-table-cell-content'   => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'table_footer_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tfoot td.pp-table-cell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_footer_first_cell',
            [
                'label'                 => __( 'First', 'powerpack' ),
            ]
        );
        
        $this->add_control(
			'table_footer_first_cell_align',
			[
				'label'                 => __( 'Horizontal Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'label_block'           => false,
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
                'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'center'   => 'center',
					'right'    => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tfoot .pp-table-cell:first-child .pp-table-cell-content'   => 'justify-content: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_footer_first_cell_text_vertical_align',
			[
				'label'                 => __( 'Text Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tfoot .pp-table-cell:first-child'   => 'vertical-align: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_footer_first_cell_vertical_align',
			[
				'label'                 => __( 'Icon Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
                'default'				=> 'middle',
                'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'middle'   => 'center',
					'bottom'   => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tfoot .pp-table-cell:first-child .pp-table-cell-content'   => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'table_footer_first_cell_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tfoot .pp-table-cell:first-child' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_footer_last_cell',
            [
                'label'                 => __( 'Last', 'powerpack' ),
            ]
        );
        
        $this->add_control(
			'table_footer_last_cell_align',
			[
				'label'                 => __( 'Horizontal Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'label_block'           => false,
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
                'selectors_dictionary'  => [
					'left'     => 'flex-start',
					'center'   => 'center',
					'right'    => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tfoot .pp-table-cell:last-child .pp-table-cell-content'   => 'justify-content: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_footer_last_cell_text_vertical_align',
			[
				'label'                 => __( 'Text Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tfoot .pp-table-cell:last-child'   => 'vertical-align: {{VALUE}};',
				],
			]
		);
        
        $this->add_control(
			'table_footer_last_cell_vertical_align',
			[
				'label'                 => __( 'Icon Vertical Align', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'top'      => [
						'title'   => __( 'Top', 'powerpack' ),
						'icon'    => 'eicon-v-align-top',
					],
					'middle'   => [
						'title'   => __( 'Middle', 'powerpack' ),
						'icon'    => 'eicon-v-align-middle',
					],
					'bottom'   => [
						'title'   => __( 'Bottom', 'powerpack' ),
						'icon'    => 'eicon-v-align-bottom',
					],
				],
                'default'				=> 'middle',
                'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'middle'   => 'center',
					'bottom'   => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tfoot .pp-table-cell:last-child .pp-table-cell-content'   => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'table_footer_last_cell_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-table tfoot .pp-table-cell:last-child' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_tab();

        $this->end_controls_tabs();
        
        $this->end_controls_section();
	}
	
	protected function register_style_icon_controls() {
        
        $this->start_controls_section(
            'section_table_icon_style',
            [
                'label'                 => __( 'Icon', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'icon_spacing',
            [
                'label'                 => __( 'Icon Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'size_units'            => [ 'px' ],
                'range'                 => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
				'selectors'             => [
					'{{WRAPPER}} .pp-table-cell-icon-before' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .pp-table-cell-icon-after' => 'margin-left: {{SIZE}}px;',
				],
            ]
        );
        
        $this->add_control(
            'table_icon_heading',
            [
                'label'                 => __( 'Icon', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label'                 => __( 'Icon Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table-cell .pp-table-cell-icon' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .pp-table-cell .pp-table-cell-icon svg' => 'fill: {{VALUE}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'table_icon_size',
            [
                'label'                 => __( 'Icon Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'size_units'            => [ 'px' ],
                'range'                 => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
				'selectors'             => [
					'{{WRAPPER}} .pp-table-cell-icon' => 'font-size: {{SIZE}}px; line-height: {{SIZE}}px;',
				],
            ]
        );
        
        $this->add_control(
            'table_img_heading',
            [
                'label'                 => __( 'Image', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
            ]
        );

        $this->add_responsive_control(
            'table_img_size',
            [
                'label'                 => __( 'Image Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'size_units'            => [ '%', 'px' ],
                'default'               => [
                    'size' => 100,
                    'unit' => 'px',
                ],
                'range'                 => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 600,
                    ],
                ],
				'selectors'             => [
					'{{WRAPPER}} .pp-table-cell-icon img' => 'width: {{SIZE}}px;',
				],
            ]
        );

		$this->add_control(
			'table_img_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-table-cell-icon img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
        
        $this->end_controls_section();
	}
	
	protected function register_style_columns_controls() {
        
        $this->start_controls_section(
            'section_table_columns_style',
            [
                'label'                 => __( 'Columns', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $repeater_columns = new Repeater();
        
        $repeater_columns->add_control(
            'table_col_span',
            [
                'label'                 => __( 'Span', 'powerpack' ),
                'type'                  => Controls_Manager::NUMBER,
                'default'               => 1,
                 'min'                  => 0,
                 'max'                  => 999,
                 'step'                 => 1,
            ]
        );
        
        $repeater_columns->add_control(
            'table_col_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
            ]
        );

        $repeater_columns->add_control(
            'table_col_width',
            [
                'label'                 => __( 'Width', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'size_units'            => [ '%', 'px' ],
                'range'                 => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1200,
                    ],
                ],
            ]
        );
        
        $this->add_control(
			'table_column_styles',
			[
				'label'                 => __( 'Column Styles', 'powerpack' ),
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[
						'table_col_span' => '1',
					],
				],
				'fields'                => array_values( $repeater_columns->get_controls() ),
				'title_field'           => 'Column Span {{{ table_col_span }}}',
			]
		);
        

        $this->start_controls_tabs( 'tabs_columns_style' );

        $this->start_controls_tab(
            'tab_columns_even',
            [
                'label'                 => __( 'Even', 'powerpack' ),
            ]
        );
        
        $this->add_control(
            'table_columns_body',
            [
                'label'                 => __( 'Body', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'table_columns_text_color_body_even',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tbody tr td:nth-child(even) .pp-table-cell-content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'table_columns_bg_color_body_even',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tbody tr td:nth-child(even)' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'table_columns_header_even',
            [
                'label'                 => __( 'Header', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'table_columns_text_color_header_even',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table thead tr th:nth-child(even) .pp-table-cell-content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'table_columns_bg_color_header_even',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table thead tr th:nth-child(even)' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'table_columns_footer_even',
            [
                'label'                 => __( 'Footer', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'table_columns_text_color_footer_even',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tfoot tr td:nth-child(even) .pp-table-cell-content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'table_columns_bg_color_footer_even',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tfoot tr td:nth-child(even)' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_columns_odd',
            [
                'label'                 => __( 'Odd', 'powerpack' ),
            ]
        );
        
        $this->add_control(
            'table_columns_body_odd',
            [
                'label'                 => __( 'Body', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'table_columns_text_color_body_odd',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tbody tr td:nth-child(odd) .pp-table-cell-content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'table_columns_bg_color_body_odd',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tbody tr td:nth-child(odd)' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'table_columns_header_odd',
            [
                'label'                 => __( 'Header', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'table_columns_text_color_header_odd',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table thead tr th:nth-child(odd) .pp-table-cell-content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'table_columns_bg_color_header_odd',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table thead tr th:nth-child(odd)' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->add_control(
            'table_columns_footer_odd',
            [
                'label'                 => __( 'Footer', 'powerpack' ),
                'type'                  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'table_columns_text_color_footer_odd',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tfoot tr td:nth-child(odd) .pp-table-cell-content' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'table_columns_bg_color_footer_odd',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-table tfoot tr td:nth-child(odd)' => 'background-color: {{VALUE}}',
                ],
            ]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();

    }
    
    protected function render_column_styles() {
        $settings = $this->get_settings_for_display();

        if ( $settings['table_column_styles'] ) { ?>
            <colgroup>
                <?php foreach( $settings['table_column_styles'] as $col_style ) { ?>
                <col
                     span="<?php echo $col_style['table_col_span']; ?>"
                     class="elementor-repeater-item-<?php echo $col_style['_id']; ?>"
                     <?php if ( $col_style['table_col_bg_color'] || $col_style['table_col_width'] ) { ?>
                        style="
                        <?php if ( $col_style['table_col_bg_color'] ) { ?>
                        background-color:<?php echo $col_style['table_col_bg_color']; ?>;
                        <?php } ?>
                        <?php if ( $col_style['table_col_width']['size'] ) { ?>
                        width:<?php echo $col_style['table_col_width']['size'] . $col_style['table_col_width']['unit']; ?>
                        <?php } ?>
                        "
                     <?php } ?>
                     >
                <?php } ?>
            </colgroup>
            <?php
        }
    }
    
    protected function clean( $string ) {
		$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
		$string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

		return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }
    
    protected function render_header() {
        $settings = $this->get_settings_for_display();
        $i = 1;
        $this->add_render_attribute( 'row', 'class', 'pp-table-row' );
        ?>
        <thead>
            <tr <?php echo $this->get_render_attribute_string( 'row' ); ?>>
                <?php
                    $pp_output = '';
					ob_start();
                    foreach ( $settings['table_headers'] as $index => $item ) :

                        $header_cell_key = $this->get_repeater_setting_key( 'table_header_col', 'table_headers', $index );
                        $this->add_render_attribute( $header_cell_key, 'class', 'pp-table-cell-text' );
                        $this->add_inline_editing_attributes( $header_cell_key, 'basic' );
		
						$th_key = 'header-' . $i;
						$th_icon_key = 'header-icon-' . $i;

                        $this->add_render_attribute( $th_key, 'class', 'pp-table-cell' );
                        $this->add_render_attribute( $th_key, 'class', 'pp-table-cell-' . $item['_id'] );

                        if ( $item['css_id'] != '' ) {
                            $this->add_render_attribute( $th_key, 'id', $item['css_id'] );
                        }

                        if ( $item['css_classes'] != '' ) {
                            $this->add_render_attribute( $th_key, 'class', $item['css_classes'] );
                        }

                        if ( $item['cell_icon_type'] != 'none' ) {
                            $this->add_render_attribute( $th_icon_key, 'class', 'pp-table-cell-icon' );
                            $this->add_render_attribute( $th_icon_key, 'class', 'pp-table-cell-icon-' . $item['cell_icon_position'] );
							
							$migration_allowed = Icons_Manager::is_migration_allowed();
							
							// add old default
							if ( ! isset( $item['cell_icon'] ) && ! $migration_allowed ) {
								$item['cell_icon'] = '';
							}

							$migrated = isset( $item['__fa4_migrated']['select_cell_icon'] );
							$is_new = ! isset( $item['cell_icon'] ) && $migration_allowed;
							
							if ( ! empty( $item['cell_icon'] ) || ( ! empty( $item['select_cell_icon']['value'] ) && $is_new ) ) {
								$this->add_render_attribute( $th_icon_key, 'class', 'pp-icon' );
							}
                        }

                        if ( $item['col_span'] > 1 ) {
                            $this->add_render_attribute( $th_key, 'colspan', $item['col_span'] );
                        }

                        if ( $item['row_span'] > 1 ) {
                            $this->add_render_attribute( $th_key, 'rowspan', $item['row_span'] );
                        }

                        if ( $settings['table_type'] == 'responsive'&& $settings['sortable'] == 'yes' ) {
                            $pp_sortable_header = ' data-tablesaw-sortable-col';
                        } else {
                            $pp_sortable_header = '';
                        }
                        
                        if ( $item['cell_icon_type'] == 'image' && $item['cell_icon_image']['url'] != '' ) {
							$image_url = Group_Control_Image_Size::get_attachment_image_src( $item['cell_icon_image']['id'], 'cell_icon_image', $item );

							if ( ! $image_url ) {
								$image_url = $item['cell_icon_image']['url'];
							}
							
                            $this->add_render_attribute( 'header_col_img-' . $i, 'src', $image_url );
                            $this->add_render_attribute( 'header_col_img-' . $i, 'title', get_the_title( $item['cell_icon_image']['id'] ) );
                            $this->add_render_attribute( 'header_col_img-' . $i, 'alt', Control_Media::get_image_alt( $item['cell_icon_image'] ) );
                        }

                        echo '<th ' . $this->get_render_attribute_string( $th_key ) . $pp_sortable_header . '>';
                        echo '<span class="pp-table-cell-content">';
                        if ( $item['cell_icon_type'] != 'none' ) {
                            echo '<span ' . $this->get_render_attribute_string( $th_icon_key ) .'>';
                            if ( $item['cell_icon_type'] == 'icon' ) {
								if ( ! empty( $item['cell_icon'] ) || ( ! empty( $item['select_cell_icon']['value'] ) && $is_new ) ) {
									if ( $is_new || $migrated ) {
										Icons_Manager::render_icon( $item['select_cell_icon'], [ 'aria-hidden' => 'true' ] );
									} else { ?>
										<i class="<?php echo esc_attr( $item['cell_icon'] ); ?>" aria-hidden="true"></i>
									<?php }
								}
                            }
                            elseif ( $item['cell_icon_type'] == 'image' && $item['cell_icon_image']['url'] != '' ) {
                                echo '<img ' . $this->get_render_attribute_string( 'header_col_img-' . $i ) . '>';
                            }
                            echo '</span>';
                        }
                        echo '<span ' . $this->get_render_attribute_string( $header_cell_key ) . '>';
                        echo $item['table_header_col'];
                        echo '</span>';
                        echo '</span>';
                        echo '</th>';
                    $i++;
                    endforeach;
					$html = ob_get_contents();
					ob_end_clean();
                    echo $html;
                ?>
            </tr>
        </thead>
        <?php
    }
    
    protected function render_footer() {
        $settings = $this->get_settings_for_display();
        $i = 1;
        ?>
        <tfoot>
            <?php
				ob_start();
                if ( !empty( $settings['table_footer_content'] ) ) {
                    foreach ( $settings['table_footer_content'] as $index => $item ) {
                        if ( $item['table_footer_element'] == 'cell' ) {

                            $footer_cell_key = $this->get_repeater_setting_key( 'cell_text', 'table_footer_content', $index );
                            $this->add_render_attribute( $footer_cell_key, 'class', 'pp-table-cell-text' );
                            $this->add_inline_editing_attributes( $footer_cell_key, 'basic' );

                            $this->add_render_attribute( 'footer-' . $i, 'class', 'pp-table-cell' );
                            $this->add_render_attribute( 'footer-' . $i, 'class', 'pp-table-cell-' . $item['_id'] );

                            if ( $item['css_id'] != '' ) {
                                $this->add_render_attribute( 'footer-' . $i, 'id', $item['css_id'] );
                            }

                            if ( $item['css_classes'] != '' ) {
                                $this->add_render_attribute( 'footer-' . $i, 'class', $item['css_classes'] );
                            }

                            if ( $item['cell_icon_type'] != 'none' ) {
                                $this->add_render_attribute( 'footer-icon-' . $i, 'class', 'pp-table-cell-icon' );
                                $this->add_render_attribute( 'footer-icon-' . $i, 'class', 'pp-table-cell-icon-' . $item['cell_icon_position'] );
							
								$migration_allowed = Icons_Manager::is_migration_allowed();

								// add old default
								if ( ! isset( $item['cell_icon'] ) && ! $migration_allowed ) {
									$item['cell_icon'] = '';
								}

								$migrated = isset( $item['__fa4_migrated']['select_cell_icon'] );
								$is_new = ! isset( $item['cell_icon'] ) && $migration_allowed;

								if ( ! empty( $item['cell_icon'] ) || ( ! empty( $item['select_cell_icon']['value'] ) && $is_new ) ) {
									$this->add_render_attribute( 'footer-icon-' . $i, 'class', 'pp-icon' );
								}
                            }

                            if ( $item['col_span'] > 1 ) {
                                $this->add_render_attribute( 'footer-' . $i, 'colspan', $item['col_span'] );
                            }

                            if ( $item['row_span'] > 1 ) {
                                $this->add_render_attribute( 'footer-' . $i, 'rowspan', $item['row_span'] );
                            }
                        
                            if ( $item['cell_icon_type'] == 'image' && $item['cell_icon_image']['url'] != '' ) {
								$image_url = Group_Control_Image_Size::get_attachment_image_src( $item['cell_icon_image']['id'], 'cell_icon_image', $item );

								if ( ! $image_url ) {
									$image_url = $item['cell_icon_image']['url'];
								}
								
                                $this->add_render_attribute( 'footer_col_img-' . $i, 'src', $image_url );
                                $this->add_render_attribute( 'footer_col_img-' . $i, 'title', get_the_title( $item['cell_icon_image']['id'] ) );
                                $this->add_render_attribute( 'footer_col_img-' . $i, 'alt', Control_Media::get_image_alt( $item['cell_icon_image'] ) );
                            }

                            if ( $item['cell_text'] != '' || $item['cell_icon_type'] != 'none' ) {
                                echo '<td ' . $this->get_render_attribute_string( 'footer-' . $i ) . '">';
                                echo '<span class="pp-table-cell-content">';
                                if ( $item['cell_icon_type'] != 'none' ) {
                                    echo '<span ' . $this->get_render_attribute_string( 'footer-icon-' . $i ) .'>';
                                    if ( $item['cell_icon_type'] == 'icon' ) {
                                        if ( ! empty( $item['cell_icon'] ) || ( ! empty( $item['select_cell_icon']['value'] ) && $is_new ) ) {
											if ( $is_new || $migrated ) {
												Icons_Manager::render_icon( $item['select_cell_icon'], [ 'aria-hidden' => 'true' ] );
											} else { ?>
												<i class="<?php echo esc_attr( $item['cell_icon'] ); ?>" aria-hidden="true"></i>
											<?php }
										}
                                    }
                                    elseif ( $item['cell_icon_type'] == 'image' && $item['cell_icon_image']['url'] != '' ) {
                                        echo '<img ' . $this->get_render_attribute_string( 'footer_col_img-' . $i ) . '>';
                                    }
                                    echo '</span>';
                                }
                                echo '<span ' . $this->get_render_attribute_string( $footer_cell_key ) . '>';
                                echo $item['cell_text'];
                                echo '</span>';
                                echo '</span>';
                                echo '</td>';
                            }
                        }
                        else {
                            if ( $i === 1 && $item['table_footer_element'] == 'row' ) {
                                echo '<tr>';
                            }
                            else if ( $i > 1 ) {
                                echo '</tr><tr>';
                            }
                        }
                        $i++;
                    }
                }
				$html = ob_get_contents();
				ob_end_clean();
				echo $html;
                echo '</tr>';
            ?>
        </tfoot>
        <?php
    }
    
    protected function render_body() {
        $settings = $this->get_settings_for_display();
        $i = 1;
        ?>
        <tbody>
            <?php
				ob_start();
                foreach ( $settings['table_body_content'] as $index => $item ) {
                    if ( $item['table_body_element'] == 'cell' ) {

                        $body_cell_key = $this->get_repeater_setting_key( 'cell_text', 'table_body_content', $index );
                        $this->add_render_attribute( $body_cell_key, 'class', 'pp-table-cell-text' );
                        $this->add_inline_editing_attributes( $body_cell_key, 'basic' );
						
						$body_icon_key = 'body-icon-' . $i;
                        
                        $this->add_render_attribute( 'row-' . $i, 'class', 'pp-table-row' );
                        $this->add_render_attribute( 'row-' . $i, 'class', 'pp-table-row-' . $item['_id'] );
                        $this->add_render_attribute( 'body-' . $i, 'class', 'pp-table-cell' );
                        $this->add_render_attribute( 'body-' . $i, 'class', 'pp-table-cell-' . $item['_id'] );

                        if ( $item['css_id'] != '' ) {
                            $this->add_render_attribute( 'body-' . $i, 'id', $item['css_id'] );
                        }

                        if ( $item['css_classes'] != '' ) {
                            $this->add_render_attribute( 'body-' . $i, 'class', $item['css_classes'] );
                        }

                        if ( $item['cell_icon_type'] != 'none' ) {
                            $this->add_render_attribute( $body_icon_key, 'class', 'pp-table-cell-icon' );
                            $this->add_render_attribute( $body_icon_key, 'class', 'pp-table-cell-icon-' . $item['cell_icon_position'] );
							
							$migration_allowed = Icons_Manager::is_migration_allowed();
							
							// add old default
							if ( ! isset( $item['cell_icon'] ) && ! $migration_allowed ) {
								$item['cell_icon'] = '';
							}

							$migrated = isset( $item['__fa4_migrated']['select_cell_icon'] );
							$is_new = ! isset( $item['cell_icon'] ) && $migration_allowed;
							
							if ( ! empty( $item['cell_icon'] ) || ( ! empty( $item['select_cell_icon']['value'] ) && $is_new ) ) {
								$this->add_render_attribute( $body_icon_key, 'class', 'pp-icon' );
							}
                        }

                        if ( $item['col_span'] > 1 ) {
                            $this->add_render_attribute( 'body-' . $i, 'colspan', $item['col_span'] );
                        }

                        if ( $item['row_span'] > 1 ) {
                            $this->add_render_attribute( 'body-' . $i, 'rowspan', $item['row_span'] );
                        }
                        
                        if ( $item['cell_icon_type'] == 'image' && $item['cell_icon_image']['url'] != '' ) {
							$image_url = Group_Control_Image_Size::get_attachment_image_src( $item['cell_icon_image']['id'], 'cell_icon_image', $item );

							if ( ! $image_url ) {
								$image_url = $item['cell_icon_image']['url'];
							}
							
                            $this->add_render_attribute( 'col_img-' . $i, 'src', $image_url );
                            $this->add_render_attribute( 'col_img-' . $i, 'title', get_the_title( $item['cell_icon_image']['id'] ) );
                            $this->add_render_attribute( 'col_img-' . $i, 'alt', Control_Media::get_image_alt( $item['cell_icon_image'] ) );
                        }
						
						if ( ! empty( $item['link']['url'] ) ) {
							$this->add_link_attributes( 'col-link-' . $i, $item['link'] );
						}

                        if ( $item['cell_text'] != '' || $item['cell_icon_type'] != 'none' ) {
                            echo '<td ' . $this->get_render_attribute_string( 'body-' . $i ) . '>';
							if ( ! empty( $item['link']['url'] ) ) { ?>
							<a <?php echo $this->get_render_attribute_string( 'col-link-' . $i ); ?>>
							<?php }
                            echo '<span class="pp-table-cell-content">';
                            if ( $item['cell_icon_type'] != 'none' ) {
                                echo '<span ' . $this->get_render_attribute_string( $body_icon_key ) .'>';
                                if ( $item['cell_icon_type'] == 'icon' ) {
                                    if ( ! empty( $item['cell_icon'] ) || ( ! empty( $item['select_cell_icon']['value'] ) && $is_new ) ) {
										if ( $is_new || $migrated ) {
											Icons_Manager::render_icon( $item['select_cell_icon'], [ 'aria-hidden' => 'true' ] );
										} else { ?>
											<i class="<?php echo esc_attr( $item['cell_icon'] ); ?>" aria-hidden="true"></i>
										<?php }
									}
                                }
                                elseif ( $item['cell_icon_type'] == 'image' && $item['cell_icon_image']['url'] != '' ) {
                                    echo '<img ' . $this->get_render_attribute_string( 'col_img-' . $i ) . '>';
                                }
                                echo '</span>';
                            }
                            echo '<span ' . $this->get_render_attribute_string( $body_cell_key ) . '>';
                            echo $item['cell_text'];
                            echo '</span>';
                            echo '</span>';
							if ( ! empty( $item['link']['url'] ) ) { ?>
							</a>
							<?php }
                            echo '</td>';
                        }
                    }
                    else {
                        if ( $i === 1 && $item['table_body_element'] == 'row' ) {
                            echo '<tr ' . $this->get_render_attribute_string( 'row-' . $i ) . '>';
                        }
                        else if ( $i > 1 ) {
                            echo '</tr><tr ' . $this->get_render_attribute_string( 'row-' . $i ) . '>';
                        }
                    }
                    $i++;
                }
				$html = ob_get_contents();
				ob_end_clean();
				echo $html;
                echo '</tr>';
            ?>
        </tbody>
        <?php
    }

	/**
	 * Function to get table HTML from csv file.
	 *
	 * Parse CSV to Table
	 *
	 * @access protected
	 */
	protected function parse_csv() {

		$settings = $this->get_settings_for_display();

		if ( 'file' != $settings['source'] ) {
			return [
				'html' => '',
				'rows' => '',
			];
		}
		$response = wp_remote_get(
			$settings['file']['url'],
			array(
				'sslverify' => false,
			)
		);

		if ( '' == $settings['file']['url'] || is_wp_error( $response ) || 200 != $response['response']['code'] || '.csv' !== substr( $settings['file']['url'], -4 )
		) {
			return [
				'html' => __( '<p>Please provide a valid CSV file.</p>', 'powerpack' ),
				'rows' => '',
			];
		}

		$rows       = [];
		$rows_count = [];
		$upload_dir = wp_upload_dir();
		$file_url   = str_replace( $upload_dir['baseurl'], '', $settings['file']['url'] );

		$file = $upload_dir['basedir'] . $file_url;

		// Attempt to change permissions if not readable.
		if ( ! is_readable( $file ) ) {
			chmod( $file, 0744 );
		}

		// Check if file is writable, then open it in 'read only' mode.
		if ( is_readable( $file ) ) {

			$_file = fopen( $file, 'r' );

			if ( ! $_file ) {
				return [
					'html' => __( "File could not be opened. Check the file's permissions to make sure it's readable by your server.", 'powerpack' ),
					'rows' => '',
				];
			}

			// To sum this part up, all it really does is go row by row.
			// Column by column, saving all the data.
			$file_data = array();

			// Get first row in CSV, which is of course the headers.
			$header = fgetcsv( $_file );

			while ( $row = fgetcsv( $_file ) ) {

				foreach ( $header as $i => $key ) {
					$file_data[ $key ] = $row[ $i ];
				}

				$data[] = $file_data;
			}

			fclose( $_file );

		} else {
			return [
				'html' => __( "File could not be opened. Check the file's permissions to make sure it's readable by your server.", 'powerpack' ),
				'rows' => '',
			];
		}

		if ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				$rows[ $key ]       = $value;
				$rows_count[ $key ] = count( $value );
			}
		}
		$header_val = array_keys( $rows[0] );

		$return['rows'] = $rows_count;

		$heading_count = 0;

		ob_start();
		?>
			<thead>
				<?php
				$counter_h      = 1;
				$cell_counter_h = 0;
				$inline_count   = 0;
				$header_text    = array();

				if ( $header_val ) {
                    $this->add_render_attribute( 'row', 'class', 'pp-table-row' );
                    ?>
                    <tr <?php echo $this->get_render_attribute_string( 'row' ); ?>>
					<?php
					foreach ( $header_val as $hkey => $head ) {
                        $header_cell_key = $this->get_repeater_setting_key( 'table_header_col', 'table_headers', $inline_count );
                        $this->add_render_attribute( $header_cell_key, 'class', 'pp-table-cell-text' );
						$this->add_render_attribute( 'header-' . $hkey, 'class', 'pp-table-cell' );
						$this->add_render_attribute( 'header-' . $hkey, 'class', 'pp-table-cell-' . $hkey );
						
						if ( $settings['table_type'] == 'responsive'&& $settings['sortable'] == 'yes' ) {
                            $pp_sortable_header = ' data-tablesaw-sortable-col';
                        } else {
                            $pp_sortable_header = '';
                        }
						?>
                        <th <?php echo $this->get_render_attribute_string( 'header-' . $hkey ) . $pp_sortable_header; ?>>
                            <span class="pp-table-cell-content">
                                <span <?php echo $this->get_render_attribute_string( $header_cell_key ); ?>>
                                    <?php echo $head; ?>
                                </span>
                            </span>
                        </th>
                        <?php
                        $header_text[ $cell_counter_h ] = $head;
                        $cell_counter_h++;
                        $counter_h++;
                        $inline_count++;
					}
					?>
					</tr>
					<?php
				}
				?>
			</thead>
			<tbody>
				<?php
				$counter           = 1;
				$cell_counter      = 0;
				$cell_inline_count = 0;

				foreach ( $rows as $row_key => $row ) {
                    $this->add_render_attribute( 'row-' . $row_key, 'class', 'pp-table-row' );
					?>
                    <tr <?php echo $this->get_render_attribute_string( 'row-' . $row_key ); ?>>
                        <?php
                        foreach ( $row as $bkey => $col ) {
                            $body_cell_key = $this->get_repeater_setting_key( 'cell_text', 'table_body_content', $cell_inline_count );
                            $this->add_render_attribute( $body_cell_key, 'class', 'pp-table-cell-text' );
							$bkey = $this->clean( $bkey );
                            $this->add_render_attribute( 'body-' . $cell_counter, 'class', 'pp-table-cell' );
                            $this->add_render_attribute( 'body-' . $cell_counter, 'class', 'pp-table-cell-' . $bkey );
                            ?>
                            <td <?php echo $this->get_render_attribute_string( 'body-' . $cell_counter ); ?>>
                                <span class="pp-table-cell-content">
                                    <span <?php echo $this->get_render_attribute_string( $body_cell_key ); ?>>
                                        <?php echo $col; ?>
                                    </span>
                                </span>
                            </td>
                            <?php
                            // Increment to next cell.
                            $cell_counter++;
                            $counter++;
                            $cell_inline_count++;
                        }
                        ?>
                    </tr>
					<?php
				}
				?>
			</tbody>
		<?php
		$html           = ob_get_clean();
		$return['html'] = $html;
		return $return;
	}

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $this->add_render_attribute( 'table-container', 'class', 'pp-table-container' );
		
        $this->add_render_attribute( 'table', 'class', ['pp-table', 'tablesaw'] );
		
		if ( $settings['sortable'] == 'yes' && $settings['sortable_dropdown'] != 'show' ) {
			$this->add_render_attribute( 'table-container', 'class', 'pp-table-sortable-dd-hide' );
		}
		
		if ( $settings['table_type'] == 'responsive' ) {
			if ( $settings['scrollable'] == 'yes' ) {
				$this->add_render_attribute( 'table', 'data-tablesaw-mode', 'swipe' );
			} else {
				$this->add_render_attribute( 'table', 'data-tablesaw-mode', 'stack' );
			}
		}
        ?>
        <div <?php echo $this->get_render_attribute_string( 'table-container' );?>>
            <table <?php echo $this->get_render_attribute_string( 'table' ); if ( $settings['table_type'] == 'responsive' && $settings['sortable'] == 'yes' ) { echo ' data-tablesaw-sortable data-tablesaw-sortable-switch'; } ?>>
                <?php
                    if ( 'file' == $settings['source'] ) {
                        $csv = $this->parse_csv();
                        echo $csv['html'];
                    } else {
                        $this->render_column_styles();

                        $this->render_header();

                        $this->render_footer();

                        $this->render_body();
                    }
                ?>
            </table>
        </div>
        <?php
    }
	
    protected function _content_template() {
    }
}