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
 * Overlap Skin for Posts widget
 */
class Skin_Overlap extends Skin_Base {
    
    /**
	 * Retrieve Skin ID.
	 *
	 * @access public
	 *
	 * @return string Skin ID.
	 */
    public function get_id() {
        return 'overlap';
    }

    /**
	 * Retrieve Skin title.
	 *
	 * @access public
	 *
	 * @return string Skin title.
	 */
    public function get_title() {
        return __( 'Overlap', 'powerpack' );
    }

	/**
	 * Register Control Actions.
	 *
	 * @access protected
	 */
	protected function _register_controls_actions() {

		parent::_register_controls_actions();
		
		add_action( 'elementor/element/pp-posts/overlap_section_post_content_style/before_section_end', [ $this, 'add_overlap_content_controls' ] );
		add_action( 'elementor/element/pp-posts/overlap_section_terms_style/after_section_start', [ $this, 'add_overlap_terms_controls' ] );
	}
	
	protected function register_image_controls() {
		parent::register_image_controls();
		
		$this->remove_control('thumbnail_location');
	}
	
	protected function register_excerpt_controls() {
		parent::register_excerpt_controls();
        
        $this->update_control(
            'show_excerpt',
            [
                'label'                 => __( 'Show Content', 'powerpack' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'yes',
                'label_on'              => __( 'Yes', 'powerpack' ),
                'label_off'             => __( 'No', 'powerpack' ),
                'return_value'          => 'yes',
            ]
        );
	}
	
	protected function register_content_order() {
		parent::register_content_order();
		
		$this->remove_control('terms_order');
		$this->remove_control('thumbnail_order');
	}
	
	protected function register_style_box_controls() {
		parent::register_style_box_controls();

        $this->update_control(
            'post_box_bg',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '#f6f6f6',
                'selectors'             => [
                    '{{WRAPPER}} .pp-post' => 'background-color: {{VALUE}};',
                ],
            ]
        );
	}
	
	protected function register_style_content_controls() {
		parent::register_style_content_controls();

        $this->update_control(
            'post_content_bg',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '#ffffff',
                'selectors'             => [
                    '{{WRAPPER}} .pp-post-content' => 'background-color: {{VALUE}};',
                ],
            ]
        );
		
		$this->update_control(
			'post_content_padding',
			[
				'label'					=> __( 'Padding', 'powerpack' ),
				'type'					=> Controls_Manager::DIMENSIONS,
				'size_units'			=> [ 'px', 'em', '%' ],
				'default'				=> [
					'top'		=> '20',
					'right'		=> '20',
					'bottom'	=> '20',
					'left'		=> '20',
					'unit'		=> 'px',
				],
				'selectors'				=> [
					'{{WRAPPER}} .pp-post-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	}

	public function add_overlap_content_controls() {
        
        $this->add_responsive_control(
            'content_margin_top',
            [
                'label'                 => __( 'Lift Up Box by', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 80,
                        'step'  => 1,
                    ],
                ],
                'default'               => [
                    'size' 	=> 45,
                ],
                'size_units'            => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-post:not(.pp-post-no-thumb) .pp-post-content' => 'margin-top: -{{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'content_margin',
            [
                'label'                 => __( 'Margin', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 80,
                        'step'  => 1,
                    ],
                ],
                'default'               => [
                    'size' 	=> 15,
                ],
                'size_units'            => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-post-content' => 'width: calc(100% - {{SIZE}}{{UNIT}}*2); margin-bottom: {{SIZE}}{{UNIT}}; margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
		
	}

	public function add_overlap_terms_controls() {
        
        $this->add_control(
			'terms_alignment',
			[
				'label'                 => __( 'Alignment', 'powerpack' ),
                'label_block'           => false,
				'type'                  => Controls_Manager::CHOOSE,
				'default'               => 'left',
				'options'               => [
					'left'          => [
						'title'     => __( 'Left', 'powerpack' ),
						'icon'      => 'eicon-h-align-left',
					],
					'center'         => [
						'title'     => __( 'Center', 'powerpack' ),
						'icon'      => 'eicon-h-align-center',
					],
					'right'         => [
						'title'     => __( 'Right', 'powerpack' ),
						'icon'      => 'eicon-h-align-right',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-post-terms-wrap'   => 'text-align: {{VALUE}};',
				],
				'condition'				=> [
					$this->get_control_id( 'post_terms' ) => 'yes',
				],
			]
		);
	}
	
	protected function register_style_image_controls() {
		parent::register_style_image_controls();
		
		$this->remove_control('image_spacing');
	}
	
	protected function register_style_terms_controls() {
		parent::register_style_terms_controls();
		
		$this->remove_control('terms_margin_bottom');

		$this->update_control(
			'terms_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'default'				=> [
					'top'		=> '4',
					'right'		=> '10',
					'bottom'	=> '4',
					'left'		=> '10',
					'unit'		=> 'px',
					'isLinked'	=> false,
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-post-terms' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    $this->get_control_id( 'post_terms' ) => 'yes',
                ]
			]
		);
		
		$this->update_control(
            'terms_bg_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '#000000',
                'selectors'             => [
                    '{{WRAPPER}} .pp-post-terms' => 'background: {{VALUE}}',
                ],
                'condition'             => [
                    $this->get_control_id( 'post_terms' ) => 'yes',
                ]
            ]
        );

        $this->update_control(
            'terms_text_color',
            [
                'label'                 => __( 'Text Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '#ffffff',
                'selectors'             => [
                    '{{WRAPPER}} .pp-post-terms' => 'color: {{VALUE}}',
                ],
                'condition'             => [
                    $this->get_control_id( 'post_terms' ) => 'yes',
                ]
            ]
        );
	}
    
    /**
	 * Render post thumbnail output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render_post_thumbnail() {

        $image_link = $this->get_instance_value( 'thumbnail_link' );
		$post_terms = $this->get_instance_value( 'post_terms' );
		$thumbnail_html = $this->get_post_thumbnail();

		if ( empty( $thumbnail_html ) ) {
			return;
		}
		
		if ( $image_link == 'yes' ) {
			
			$thumbnail_html = '<a href="' . get_the_permalink() . '">' . $thumbnail_html . '</a>';
			
		}
		?>
		<div class="pp-post-thumbnail">
			<?php
				echo $thumbnail_html;
		
				if ( $post_terms == 'yes' ) {
					$this->render_terms();
				}
			?>
		</div>

		<?php
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
		
		$post_terms = $this->get_instance_value( 'post_terms' );
		$post_meta = $this->get_instance_value( 'post_meta' );
		
		if ( has_post_thumbnail() ) {
			$image_id = get_post_thumbnail_id( get_the_ID() );
			//$pp_thumb_url = Group_Control_Image_Size::get_attachment_image_src( $image_id, 'thumbnail_size', $settings );
		} else {
			$pp_thumb_url = '';
		}
		
		$thumbnail = $this->get_post_thumbnail();
		$no_thumb_class = '';

		if ( !$thumbnail ) {
			$no_thumb_class = ' pp-post-no-thumb';
		}
		
		do_action( 'ppe_before_single_post_wrap', get_the_ID(), $settings );
		?>
		<div class="<?php echo $this->get_item_wrap_classes(); ?>">
			<?php do_action( 'ppe_before_single_post', get_the_ID(), $settings ); ?>
			<div class="<?php echo $this->get_item_classes() . $no_thumb_class; ?>">
				<?php
					$this->render_post_thumbnail();
				?>

				<div class="pp-post-content">
					<?php
						$content_parts = $this->get_ordered_items( Module::get_post_parts() );

						foreach ( $content_parts as $part => $index ) {
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
			<?php do_action( 'ppe_after_single_post', get_the_ID(), $settings ); ?>
		</div>
        <?php
		do_action( 'ppe_after_single_post_wrap', get_the_ID(), $settings );
    }
}