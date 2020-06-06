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
 * Event Skin for Posts widget
 */
class Skin_Event extends Skin_Base {
    
    /**
	 * Retrieve Skin ID.
	 *
	 * @access public
	 *
	 * @return string Skin ID.
	 */
    public function get_id() {
        return 'event';
    }

    /**
	 * Retrieve Skin title.
	 *
	 * @access public
	 *
	 * @return string Skin title.
	 */
    public function get_title() {
        return __( 'Event', 'powerpack' );
    }

	/**
	 * Register Control Actions.
	 *
	 * @access protected
	 */
	protected function _register_controls_actions() {

		parent::_register_controls_actions();
		
		add_action( 'elementor/element/pp-posts/event_section_post_content_style/after_section_end', [ $this, 'add_event_date_controls' ] );
	}
	
	protected function register_image_controls() {
		parent::register_image_controls();
		
		$this->remove_control('thumbnail_location');
	}
	
	protected function register_content_order() {
		parent::register_content_order();
		
		$this->remove_control('thumbnail_order');
	}
	
	protected function register_style_content_controls() {
		parent::register_style_content_controls();

		$this->update_control(
			'post_content_align',
			[
				'label'					=> __( 'Alignment', 'powerpack' ),
				'type'					=> Controls_Manager::CHOOSE,
				'label_block'			=> false,
				'options'			=> [
					'left'		=> [
						'title'	=> __( 'Left', 'powerpack' ),
						'icon'	=> 'fa fa-align-left',
					],
					'center'	=> [
						'title' => __( 'Center', 'powerpack' ),
						'icon'	=> 'fa fa-align-center',
					],
					'right'		=> [
						'title' => __( 'Right', 'powerpack' ),
						'icon'	=> 'fa fa-align-right',
					],
				],
				'default'				=> 'center',
				'selectors'			=> [
					'{{WRAPPER}} .pp-post-content' => 'text-align: {{VALUE}};',
				],
			]
		);
		
	}

	public function add_event_date_controls() {
		
		$this->start_controls_section(
            'section_date_style',
            [
                'label'                 => __( 'Date', 'powerpack' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'date_color',
            [
                'label'                 => __( 'Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-post-event-date' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'date_background_color',
            [
                'label'                 => __( 'Background Color', 'powerpack' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .pp-post-event-date' => 'background-color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'date_typography',
                'label'                 => __( 'Typography', 'powerpack' ),
                'selector'              => '{{WRAPPER}} .pp-post-event-date',
            ]
        );
        
        $this->add_responsive_control(
            'date_box_size',
            [
                'label'                 => __( 'Box Size', 'powerpack' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .pp-post-event-date' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; margin-top: calc(-{{SIZE}}{{UNIT}} / 2);',
                ],
            ]
        );

		$this->end_controls_section();
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
		$thumbnail_html = $this->get_post_thumbnail();

		if ( empty( $thumbnail_html ) ) {
			return;
		}
		
		if ( $image_link == 'yes' ) {
			
			$thumbnail_html = '<a href="' . get_the_permalink() . '">' . $thumbnail_html . '</a>';
			
		}
		?>
		<div class="pp-post-thumbnail">
			<?php echo $thumbnail_html; ?>
			<div class="pp-post-event-date">
				<span class="pp-post-month">
					<?php echo date_i18n( 'M', strtotime( get_the_date() ) ); ?>
				</span>
				<span class="pp-post-day">
					<?php echo date_i18n( 'd', strtotime( get_the_date() ) ); ?>
				</span>
			</div>
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
		$thumbnail_location = $this->get_instance_value( 'thumbnail_location' );
		
		do_action( 'ppe_before_single_post_wrap', get_the_ID(), $settings );
		?>
		<div class="<?php echo $this->get_item_wrap_classes(); ?>">
			<?php do_action( 'ppe_before_single_post', get_the_ID(), $settings ); ?>
			<div class="<?php echo $this->get_item_classes(); ?>">
				<?php
					$this->render_post_thumbnail();
				?>

				<?php do_action( 'ppe_before_single_post_content', get_the_ID(), $settings ); ?>

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
				
				<?php do_action( 'ppe_after_single_post_content', get_the_ID(), $settings ); ?>
			</div>
			<?php do_action( 'ppe_after_single_post', get_the_ID(), $settings ); ?>
		</div>
        <?php
		do_action( 'ppe_after_single_post_wrap', get_the_ID(), $settings );
    }
}