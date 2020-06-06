jQuery(document).ready(function () {
    jQuery('.mycred-woo-product-referral').on('change', function () {
        jQuery('.mycred_product_url').removeAttr('value');
        jQuery('.mycred_product_referral_url').removeAttr('value');
    });

});
