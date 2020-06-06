<?php
/**
 * WooCommerce - Quick View Modal
 *
 * @package PowerPack
 */

?>
<div class="pp-quick-view-<?php echo $widget_id; ?>">
	<div class="pp-quick-view-bg"><div class="pp-quick-view-loader"></div></div>
	<div id="pp-quick-view-modal" class="pp-quick-view-modal">
		<div class="pp-content-main-wrapper"><?php /*Don't remove this html comment*/ ?><!--
		--><div class="pp-content-main">
				<div class="pp-lightbox-content">
					<div class="pp-content-main-head">
						<div id="pp-quick-view-close" class="pp-quick-view-close-btn"></div>
					</div>
					<div id="pp-quick-view-content" class="woocommerce single-product"></div>
				</div>
			</div>
		</div>
	</div>
</div>
