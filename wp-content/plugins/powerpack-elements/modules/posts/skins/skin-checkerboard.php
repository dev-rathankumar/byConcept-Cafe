<?php
namespace PowerpackElements\Modules\Posts\Skins;

use PowerpackElements\Base\Powerpack_Widget;
use PowerpackElements\Modules\Posts\Module;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Checkerboard Skin for Posts widget
 */
class Skin_Checkerboard extends Skin_Base {
    
    /**
	 * Retrieve Skin ID.
	 *
	 * @access public
	 *
	 * @return string Skin ID.
	 */
    public function get_id() {
        return 'checkerboard';
    }

    /**
	 * Retrieve Skin title.
	 *
	 * @access public
	 *
	 * @return string Skin title.
	 */
    public function get_title() {
        return __( 'Checkerboard', 'powerpack' );
    }

	/**
	 * Register Control Actions.
	 *
	 * @access protected
	 */
	protected function _register_controls_actions() {

		parent::_register_controls_actions();
		
		add_action( 'elementor/element/pp-posts/checkerboard_section_image/before_section_end', [ $this, 'add_checkerboard_image_controls' ] );
		add_action( 'elementor/element/pp-posts/checkerboard_section_layout/before_section_end', [ $this, 'add_checkerboard_layout_controls' ] );
		add_action( 'elementor/element/pp-posts/checkerboard_section_post_content_style/after_section_start', [ $this, 'add_style_content_container_controls' ] );
	}
	
	protected function register_image_controls() {
		parent::register_image_controls();
		
		$this->remove_control('thumbnail_location');
	}
	
	protected function register_content_order() {
		parent::register_content_order();
		
		$this->remove_control('thumbnail_order');
	}
	
	protected function register_layout_content_controls() {
		parent::register_layout_content_controls();
		
		$this->remove_control('layout');
		$this->remove_responsive_control('columns');
        
        $this->update_control(
            'equal_height',
            [
                'label'                 => __( 'Equal Height', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => '',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
                'prefix_class'          => 'pp-equal-height-',
				'render_type'           => 'template',
            ]
        );
	}

	public function add_checkerboard_layout_controls() {
		$this->add_control(
			'height_adjustment',
			[
				'type'                  => Controls_Manager::SELECT,
				'label'                 => __( 'Height Adjustment', 'powerpack' ),
				'options'               => [
					'auto'		=> __( 'Auto', 'powerpack' ),
					'custom'	=> __( 'Custom', 'powerpack' ),
				],
				'default'               => 'auto',
				'frontend_available'    => true,
				'condition'             => [
					$this->get_control_id( 'equal_height' ) => 'yes',
				],
			]
		);
        
        $this->add_responsive_control(
            'height_custom',
            [
                'label'                 => __( 'Custom Height', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' 	=> [
                        'min' => 50,
                        'max' => 1000,
                    ],
                ],
                'default'               => [
                    'size' 	=> 280,
                ],
				'devices'               => [ 'desktop', 'tablet' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-posts-skin-checkerboard .pp-post-wrap' => 'height: {{SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}} .pp-posts-skin-checkerboard .pp-post-wrap' => 'height: auto !important;',
                ],
				'condition'             => [
					$this->get_control_id( 'equal_height' ) => 'yes',
					$this->get_control_id( 'height_adjustment' ) => 'custom',
				],
            ]
        );
	}
	
	protected function register_style_layout_controls() {
		parent::register_style_layout_controls();
		
		$this->remove_control('posts_horizontal_spacing');

        $this->update_control(
            'posts_vertical_spacing',
            [
                'label'                 => __( 'Row Spacing', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' 	=> [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'               => [
                    'size' 	=> 0,
                ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-posts .pp-grid-item-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
	}
	
	protected function register_style_box_controls() {
		parent::register_style_box_controls();
		
		$this->remove_control('post_box_border_border');
		$this->remove_control('post_box_border_radius');
		$this->remove_control('post_box_padding');
		$this->remove_control('post_box_shadow_box_shadow_type');
		$this->remove_control('post_box_border_color_hover');
		$this->remove_control('post_box_shadow_hover_box_shadow_type');
	}
	
	protected function register_style_content_controls() {
		parent::register_style_content_controls();
		
		$this->remove_control('post_content_border_radius');
	}
	
	protected function register_style_image_controls() {
		parent::register_style_image_controls();
		
		$this->remove_control('image_spacing');
	}
	
	public function add_style_content_container_controls() {

        $this->add_responsive_control(
            'content_vertical_align',
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
                'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .pp-posts-skin-checkerboard .pp-post' => 'align-items: {{VALUE}};',
				],
				'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'bottom'   => 'flex-end',
					'middle'   => 'center',
				],
            ]
        );
	}

	public function add_checkerboard_image_controls() {
        
        $this->add_control(
            'image_stack',
            [
                'label'                 => __( 'Stack On', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'mobile',
                'options'               => [
                    'none'		=> __( 'None', 'powerpack' ),
                    'tablet' 	=> __( 'Tablet', 'powerpack' ),
                    'mobile' 	=> __( 'Mobile', 'powerpack' ),
                ],
                'prefix_class'          => 'pp-posts-image-stack-',
            ]
        );
	}
	
	public function get_posts_wrap_classes() {
		$classes = array(
			'pp-posts',
			'pp-posts-skin-' . $this->get_id(),
		);
		
		$equal_height = $this->get_instance_value( 'equal_height' );
		
		if ( $equal_height == 'yes' ) {
			$height_adjustment = $this->get_instance_value( 'height_adjustment' );
			$classes[] = 'pp-posts-height-' . $height_adjustment;
		}
		
		return apply_filters( 'ppe_posts_wrap_classes', $classes );
	}
    
    /**
	 * Render post body output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_post_body() {
        $settings = $this->parent->get_settings_for_display();
        
		$post_meta = $this->get_instance_value( 'post_meta' );
		
		do_action( 'ppe_before_single_post_wrap', get_the_ID(), $settings );
		?>
		<div class="<?php echo $this->get_item_wrap_classes(); ?>">
			<?php do_action( 'ppe_before_single_post', get_the_ID(), $settings ); ?>
			<div class="<?php echo $this->get_item_classes(); ?>">
				<?php 
					$this->render_post_thumbnail();
				?>
				<div class="pp-post-content-wrap">
					<div class="pp-post-content">
						<?php
							$content_parts = $this->get_ordered_items( Module::get_post_parts() );

							foreach ( $content_parts as $part => $index ) {
								if ( $part == 'terms' ) {
									$this->render_terms();
								}

								if ( $part == 'title' ) {
									$this->render_post_title();
								}

								if ( $part == 'meta' ) {
									$this->render_post_meta();
								}

								if ( $part == 'excerpt' ) {
									$this->render_excerpt();
								}

								if ( $part == 'button' ) {
									$this->render_button();
								}
							}
						?>
					</div>
				</div>
			</div>
			<?php do_action( 'ppe_after_single_post', get_the_ID(), $settings ); ?>
		</div>
        <?php
		do_action( 'ppe_after_single_post_wrap', get_the_ID(), $settings );
    }
}