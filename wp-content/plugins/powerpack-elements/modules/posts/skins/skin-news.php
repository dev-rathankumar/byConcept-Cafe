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
 * News Skin for Posts widget
 */
class Skin_News extends Skin_Base {
    
    /**
	 * Retrieve Skin ID.
	 *
	 * @access public
	 *
	 * @return string Skin ID.
	 */
    public function get_id() {
        return 'news';
    }

    /**
	 * Retrieve Skin title.
	 *
	 * @access public
	 *
	 * @return string Skin title.
	 */
    public function get_title() {
        return __( 'News', 'powerpack' );
    }

	/**
	 * Register Control Actions.
	 *
	 * @access protected
	 */
	protected function _register_controls_actions() {

		parent::_register_controls_actions();
		
		add_action( 'elementor/element/pp-posts/news_section_image/before_section_end', [ $this, 'add_news_image_controls' ] );
		add_action( 'elementor/element/pp-posts/news_section_post_box_style/after_section_start', [ $this, 'add_style_box_controls' ] );
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

        $this->update_responsive_control(
            'columns',
            [
                'label'                 => __( 'Columns', 'powerpack' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => '2',
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
                'frontend_available'    => true,
            ]
        );
	}
	
	protected function register_style_image_controls() {
		parent::register_style_image_controls();
		
		$this->update_responsive_control(
			'image_spacing',
			[
				'label'					=> __( 'Spacing', 'powerpack' ),
				'type'					=> Controls_Manager::SLIDER,
				'range'					=> [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'				=> [
					'{{WRAPPER}}.pp-post-thumbnail-align-left .pp-post-thumbnail' => 'margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.pp-post-thumbnail-align-right .pp-post-thumbnail' => 'margin-left: {{SIZE}}{{UNIT}}',
                    '(tablet){{WRAPPER}}.pp-posts-image-stack-tablet .pp-posts .pp-post-thumbnail' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-left: 0; margin-right: 0;',
                    '(mobile){{WRAPPER}}.pp-posts-image-stack-mobile .pp-posts .pp-post-thumbnail' => 'margin-bottom: {{SIZE}}{{UNIT}}; margin-left: 0; margin-right: 0;',
				],
				'default'				=> [
					'size' => 20,
				],
				'condition'				=> [
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
				],
			]
		);
	}
	
	public function add_style_box_controls() {

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
					'{{WRAPPER}} .pp-posts-skin-news .pp-post' => 'align-items: {{VALUE}};',
				],
				'selectors_dictionary'  => [
					'top'      => 'flex-start',
					'bottom'   => 'flex-end',
					'middle'   => 'center',
				],
            ]
        );
	}

	public function add_news_image_controls() {
        
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
		
		$this->add_control(
			'image_position',
			[
				'label'                 => __( 'Image Position', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'			=> false,
				'options'               => [
					'left'		=> [
						'title' => __( 'Left', 'powerpack' ),
						'icon'	=> 'eicon-h-align-left',
					],
					'right'		=> [
						'title' => __( 'Right', 'powerpack' ),
						'icon'	=> 'eicon-h-align-right',
					],
				],
				'default'               => 'left',
				'prefix_class'			=> 'pp-post-thumbnail-align-',
				'selectors'				=> [
					'{{WRAPPER}} .pp-post-thumbnail'   => 'float: {{VALUE}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'image_width',
			[
				'label'					=> __( 'Width', 'powerpack' ),
				'type'					=> Controls_Manager::SLIDER,
				'range'					=> [
					'px' => [
						'max' => 500,
					],
				],
                'size_units'            => [ 'px', '%' ],
				'selectors'				=> [
					'{{WRAPPER}} .pp-post-thumbnail' => 'flex-basis: {{SIZE}}{{UNIT}}',
				],
				'condition'				=> [
					$this->get_control_id( 'show_thumbnail' ) => 'yes',
				],
			]
		);
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
			<?php do_action( 'ppe_after_single_post', get_the_ID(), $settings ); ?>
		</div>
        <?php
		do_action( 'ppe_after_single_post_wrap', get_the_ID(), $settings );
    }
}