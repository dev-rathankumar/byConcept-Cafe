/**
 * The admin-specific js functionlity
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    points-and-rewards-for-wooCommerce
 * @subpackage points-and-rewards-for-wooCommerce/admin
 */

jQuery(document).ready(function ($) {
    console.log( mwb_wpr_activation );
    jQuery('#mwb-wpr-install-lite').click(function (e) {
        e.preventDefault();
        jQuery("#mwb_notice_loader").show();
        var data = {
            action: 'mwb_wpr_activate_lite_plugin',
            // mwb_nonce: mwb_wpr_activation.mwb_wpr_nonce
        };
        console.log( data );
        $.ajax({
            url: mwb_wpr_activation.ajax_url,
            type: 'POST',
            data: data,
            success: function (response) {
                jQuery("#mwb_notice_loader").show();
                if (response == 'success') {
                    window.location.reload();
                }
            }
        });
    });
});