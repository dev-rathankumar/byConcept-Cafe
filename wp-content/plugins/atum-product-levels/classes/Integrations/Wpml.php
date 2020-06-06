<?php
/**
 * WPML multilingual integration class for Product Levels
 *
 * @package        AtumLevels
 * @subpackage     Integrations
 * @author         Be Rebel - https://berebel.io
 * @copyright      ©2020 Stock Management Labs™
 *
 * @since          1.1.8
 */

namespace AtumLevels\Integrations;

defined( 'ABSPATH' ) || die;

use Atum\Integrations\Wpml as AtumWpml;
use AtumLevels\Models\BOMModel;


class Wpml extends AtumWpml {
	
	/**
	 * Register the WPML Atum Product Levels hooks
	 *
	 * @since 1.1.8
	 */
	public function register_hooks() {
		
		if ( is_admin() ) {

			// replace the BOM Panel.
			add_filter( 'atum/product_levels/can_add_bom_panel', array( $this, 'maybe_remove_bom_panel' ), 10, 3 );
			
			// Filter product parts shown in product json search.
			add_filter( 'atum/product_levels/ajax/json_search/select', array( $this, 'select_add_icl_translations' ), 10, 3 );
			add_filter( 'atum/product_levels/ajax/json_search/where', array( $this, 'where_add_icl_translations' ), 10, 3 );
			
			// Change the product loaded in get_all_bom_children.
			add_filter( 'atum/product_levels/get_all_bom_children/product', array( $this, 'current_product_language' ) );
			
		}
		
		// Get original boms and products ids.
		add_filter( 'atum/product_levels/args_save_linked_bom', array( $this, 'set_original_linked_bom' ) );
		add_filter( 'atum/product_levels/cols_delete_linked_bom', array( $this, 'delete_original_linked_bom' ) );
		
		add_filter( 'atum/product_levels/bom_id', array( $this, 'set_original_product_id' ) );
		// TODO: Is this one the only function needed in the FrontEnd?
		add_filter( 'atum/product_levels/product_id', array( $this, 'set_original_product_id' ) );
		
	}
	
	
	/**
	 * Change the bom id to original product. Only original products can be linked.
	 *
	 * @since 1.1.8
	 *
	 * @param array $bom_data
	 *
	 * @return array
	 */
	public function set_original_linked_bom( $bom_data ) {
		
		$bom_data['product_id'] = self::get_original_product_id( $bom_data['product_id'] );
		$bom_data['bom_id']     = self::get_original_product_id( $bom_data['bom_id'] );
		
		return $bom_data;
		
	}

	/**
	 * Ensure original and translated linked BOMs are deleted. Event though only original products can be linked.
	 *
	 * @since 1.4.3
	 *
	 * @param array $bom_data
	 *
	 * @return array
	 */
	public function delete_original_linked_bom( $bom_data ) {

		// Ensure translations have no linked BOMs.
		remove_filter( 'atum/product_levels/cols_delete_linked_bom', array( $this, 'delete_original_linked_bom' ) );

		$translated_product_id = $bom_data['product_id'];
		$translated_bom_id     = $bom_data['bom_id'];

		$bom_data['product_id'] = self::get_original_product_id( $bom_data['product_id'] );
		$bom_data['bom_id']     = self::get_original_product_id( $bom_data['bom_id'] );

		if ( $bom_data['product_id'] !== $translated_product_id ) {

			// Remove all translated product's linked BOM.
			BOMModel::clean_linked_bom( $translated_product_id, [] );
		}

		if ( $bom_data['bom_id'] !== $translated_bom_id ) {

			// Remove translated BOM FROM original product.
			BOMModel::delete_linked_bom( $bom_data['product_id'], $translated_bom_id );
		}

		add_filter( 'atum/product_levels/cols_delete_linked_bom', array( $this, 'delete_original_linked_bom' ) );

		$bom_data['product_id'] = self::get_original_product_id( $bom_data['product_id'] );
		$bom_data['bom_id']     = self::get_original_product_id( $bom_data['bom_id'] );

		return $bom_data;

	}
	
	/**
	 * Return the original translation bom id
	 *
	 * @since 1.1.8
	 *
	 * @param int $bom_id
	 *
	 * @return int
	 */
	public function set_original_product_id( $bom_id ) {
		
		return self::get_original_product_id( $bom_id );
	}
	
	/**
	 * Return product in current language
	 *
	 * @since 1.1.8
	 *
	 * @param \WC_Product $product
	 *
	 * @return \WC_Product
	 */
	public function current_product_language( $product ) {

		/* @noinspection PhpUndefinedMethodInspection */
		$current_language = self::$sitepress->get_current_language();
		/* @noinspection PhpUndefinedMethodInspection */
		if ( $current_language !== self::$sitepress->get_default_language() ) {
			
			$product_id   = $product->get_id();
			$translations = self::get_product_translations_ids( $product_id );
			
			if ( isset( $translations[ $current_language ] ) && $translations[ $current_language ] !== $product_id ) {
				$product = wc_get_product( $translations[ $current_language ] );
			}
		}
		
		return $product;
	}

	/**
	 * Prevent BOM Panel to be shown if the product is a translation
	 *
	 * #since 1.4.3
	 *
	 * @param bool $show
	 * @param int  $product_id
	 * @param bool $is_variation
	 *
	 * @return bool
	 */
	public function maybe_remove_bom_panel( $show, $product_id, $is_variation ) {

		$product_id = (int) $product_id;

		if ( self::get_original_product_id( $product_id ) !== $product_id ) {

			$show = FALSE;

			?>
			<?php if ( ! $is_variation ) : ?>
				<div id="bom_product_data" class="panel woocommerce_options_panel hidden">
			<?php endif ?>
			<div class="options-group translated-pl-product">
				<div class="alert alert-warning">
					<h3>
						<i class="atum-icon atmi-warning"></i>
						<?php esc_html_e( 'BOMs can not be edited within translations', ATUM_LEVELS_TEXT_DOMAIN ) ?>
					</h3>

					<p><?php esc_html_e( 'You must edit original product instead.', ATUM_LEVELS_TEXT_DOMAIN ) ?></p>
				</div>
			</div>
			<?php if ( ! $is_variation ) : ?>
				</div>
			<?php endif ?>
			<?php
		}

		return $show;

	}

}
