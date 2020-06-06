<?php
/**
 * PowerPack WooCommerce Skin Grid - Classic.
 *
 * @package PowerPack
 */

namespace PowerpackElements\Modules\Woocommerce\Skins;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Grid_Skin_4
 *
 * @property Products $parent
 */
class Skin_Grid_Skin_4 extends Skin_Grid_Base {

	/**
	 * Get ID.
	 *
	 * @since 1.3.3
	 * @access public
	 */
	public function get_id() {
		return 'skin-4';
	}

	/**
	 * Get title.
	 *
	 * @since 1.3.3
	 * @access public
	 */
	public function get_title() {
		return __( 'Skin 4', 'powerpack' );
	}

	/**
	 * Loop Template.
	 *
	 * @since 1.3.3
	 * @access public
	 */
	public function render_woo_loop_template() {

		$settings = $this->parent->get_settings();

		include POWERPACK_ELEMENTS_PATH . 'modules/woocommerce/templates/content-product-skin-4.php';
	}

	/**
	 * View Cart.
	 *
	 * @since 1.3.3
	 * @access public
	 */
	public function view_cart() {
		echo '<div class="pp-notice-cart-outer">';
			echo '<div class="pp-notice-cart">';
				echo '<span class="pp-close-notice"></span>';
				echo '<div class="pp-text-notice">';
					echo '<div><b>"Modern Blazer"</b> has been added to your cart.</div>';
					echo '<a href="#cart-url" class="pp-forward">View Cart</a>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}

	/**
	 * Render.
	 *
	 * @since 1.3.3
	 * @access public
	 */
	public function render() {
		parent::render();
	}
}
