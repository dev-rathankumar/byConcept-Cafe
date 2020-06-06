<?php
/**
 * View for the Help Sidebar on Manufacturing Central page
 *
 * @since 1.0.1
 */

defined( 'ABSPATH' ) || die;

use Atum\Addons\Addons;
?>
<br/>
<p><a href="https://www.stockmanagementlabs.com" target="_blank"><?php esc_html_e( 'Visit our Website for More', ATUM_LEVELS_TEXT_DOMAIN ) ?></a></p>
<p><a href="https://forum.stockmanagementlabs.com/t/atum-documentation" target="_blank"><?php esc_html_e( 'Read the Docs', ATUM_LEVELS_TEXT_DOMAIN ) ?></a></p>
<p><a href="https://www.youtube.com/channel/UCcTNwTCU4X_UrIj_5TUkweA" target="_blank"><?php esc_html_e( 'Watch our Videos', ATUM_LEVELS_TEXT_DOMAIN ) ?></a></p>
<p><a href="https://forum.stockmanagementlabs.com/t/premium-add-ons" target="_blank"><?php esc_html_e( 'Get Support', ATUM_LEVELS_TEXT_DOMAIN ) ?></a></p>

<?php if ( Addons::has_valid_key() ) : ?>
<p><a href="https://stockmanagementlabs.ticksy.com/" target="_blank"><?php esc_html_e( 'Open Support Ticket', ATUM_LEVELS_TEXT_DOMAIN ) ?></a></p>
<?php endif;
