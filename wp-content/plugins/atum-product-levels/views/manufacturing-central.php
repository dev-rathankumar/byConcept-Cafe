<?php
/**
 * View for the Manufacturing Central page
 *
 * @since 0.0.5
 *
 * @var bool   $is_uncontrolled_list
 * @var string $mc_url
 * @var \AtumLevels\ManufacturingCentral\Lists\ListTable $list
 * @var string $ajax
 */

defined( 'ABSPATH' ) || die;

?>
<div class="wrap">
	<h1 class="wp-heading-inline extend-list-table">
		<?php esc_html_e( 'Manufacturing Central', ATUM_LEVELS_TEXT_DOMAIN ) ?>

		<?php if ( $is_uncontrolled_list ) : ?>
			<?php esc_html_e( '(Uncontrolled)', ATUM_LEVELS_TEXT_DOMAIN ) ?>
		<?php endif; ?>

		<a href="<?php echo esc_url( $mc_url ) ?>" class="toggle-managed page-title-action extend-list-table">
			<?php echo $is_uncontrolled_list ? esc_html__( 'Show Controlled', ATUM_LEVELS_TEXT_DOMAIN ) : esc_html__( 'Show Uncontrolled', ATUM_LEVELS_TEXT_DOMAIN ) ?>
		</a>
	</h1>

	<hr class="wp-header-end">

	<div class="atum-list-wrapper" data-action="atum_fetch_manufacturing_central_list" data-screen="<?php echo esc_attr( $list->screen->id ) ?>">
		<div class="list-table-header">
			<div id="scroll-stock_central_nav" class="nav-container-box nav-mc">
				<div class="overflow-opacity-effect-right"></div>
				<div class="overflow-opacity-effect-left"></div>

				<nav id="stock_central_nav" class="nav-with-scroll-effect dragscroll">
					<?php $list->views(); ?>
				</nav>
			</div>

			<div class="search-box extend-list-table search-mc">

				<div class="input-group input-group-sm">
					<div class="input-group-append">
						<button class="btn btn-outline-secondary dropdown-toggle dropdown-toggle-mc tips" id="search_column_btn" type="button"
								data-value="" title="<?php esc_html_e( 'Search in Column', ATUM_LEVELS_TEXT_DOMAIN ) ?>"
								aria-haspopup="true" aria-expanded="false" data-toggle="dropdown">
							<?php esc_html_e( 'Search In', ATUM_LEVELS_TEXT_DOMAIN ) ?>
						</button>

						<div class="search_column_dropdown dropdown-menu" id="search_column_dropdown"
								data-product-title="<?php esc_attr_e( 'Product Name', ATUM_LEVELS_TEXT_DOMAIN ) ?>"
								data-no-option="<?php esc_attr_e( 'Search In', ATUM_LEVELS_TEXT_DOMAIN ) ?>">
						</div>
					</div>
					<input type="search" class="form-control atum-post-search atum-post-search-mc atum-post-search-with-dropdown" data-value=""
							autocomplete="off" placeholder="<?php esc_attr_e( 'Search...', ATUM_LEVELS_TEXT_DOMAIN ) ?>">

					<?php if ( 'no' === $ajax ) : ?>
						<input type="submit" class="button search-submit" value="<?php esc_attr_e( 'Search', ATUM_LEVELS_TEXT_DOMAIN ) ?>">
					<?php endif; ?>

				</div>

			</div>
		</div>
		
		<?php $list->display(); ?>
		
	</div>
</div>
