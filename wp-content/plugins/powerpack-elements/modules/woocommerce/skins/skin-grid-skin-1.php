<?php
/**
 * PowerPack WooCommerce Skin Grid - Default.
 *
 * @package PowerPack
 */

namespace PowerpackElements\Modules\Woocommerce\Skins;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Skin_Grid_Skin_1
 *
 * @property Products $parent
 */
class Skin_Grid_Skin_1 extends Skin_Grid_Base {

	/**
	 * Get ID.
	 *
	 * @since 1.3.3
	 * @access public
	 */
	public function get_id() {
		return 'skin-1';
	}

	/**
	 * Get title.
	 *
	 * @since 1.3.3
	 * @access public
	 */
	public function get_title() {
		return __( 'Skin 1', 'powerpack' );
	}

}
