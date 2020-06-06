<?php
namespace PowerpackElements\Modules\Posts\Skins;

use PowerpackElements\Base\Powerpack_Widget;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Skin_Base as Elementor_Skin_Base;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Portfolio Skin for Posts widget
 */
class Skin_Template extends Skin_Base {
    
    /**
	 * Retrieve Skin ID.
	 *
	 * @access public
	 *
	 * @return string Skin ID.
	 */
    public function get_id() {
        return 'template';
    }

    /**
	 * Retrieve Skin title.
	 *
	 * @access public
	 *
	 * @return string Skin title.
	 */
    public function get_title() {
        return __( 'Saved Template', 'powerpack' );
    }

	/**
	 * Register Control Actions.
	 *
	 * @access protected
	 */
	protected function _register_controls_actions() {

		//parent::_register_controls_actions();
		
		add_action( 'elementor/element/pp-posts/section_skin_field/after_section_end', [ $this, 'register_layout_controls' ] );
		//add_action( 'elementor/element/pp-posts/template_section_skin_field/after_section_end', [ $this, 'register_filter_section_controls' ] );
		add_action( 'elementor/element/pp-posts/section_query/after_section_end', [ $this, 'register_template_controls' ] );
	}

	public function register_template_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->register_slider_controls();
		$this->register_filter_section_controls();
		$this->register_pagination_controls();
		$this->register_content_help_docs();

		$this->register_style_layout_controls();
		$this->register_style_filter_controls();
		$this->register_style_pagination_controls();
		$this->register_style_arrows_controls();
		$this->register_style_dots_controls();
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
		
		do_action( 'ppe_before_single_post_wrap', get_the_ID(), $settings );
		?>
		<div class="<?php echo $this->get_item_wrap_classes(); ?>">
			<?php do_action( 'ppe_before_single_post', get_the_ID(), $settings ); ?>
			<div class="<?php echo $this->get_item_classes(); ?>">
				<?php
					if ( !empty( $settings['templates'] ) ) {
						$template_id = $settings['templates'];
						
						$this->parent->render_template_content( $template_id, $this->parent );
						
					} else {
						$placeholder = __( 'Choose a post template that you want to use as post skin in widget settings.', 'powerpack' );
					
						echo $this->parent->render_editor_placeholder( [
							'title' => __( 'No template selected!', 'powerpack' ),
							'body' => $placeholder,
						] );
					}
				?>
			</div>
			<?php do_action( 'ppe_after_single_post', get_the_ID(), $settings ); ?>
		</div>
        <?php
		do_action( 'ppe_after_single_post_wrap', get_the_ID(), $settings );
    }
}