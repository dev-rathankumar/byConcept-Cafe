jQuery(document).ready(function(){
    //alert(1);
    var val = jQuery('#reward_points_global_type').val();
    if (val === 'exchange') {
        jQuery('#reward_points_exchange_rate').removeAttr('disabled');
        jQuery('#reward_points_global_type_val').attr('disabled', true);
    } else {
        jQuery('#reward_points_global_type_val').removeAttr('disabled');
        jQuery('#reward_points_exchange_rate').attr('disabled', true);
    }

    jQuery('#reward_points_global_type').on('change', function(){
        var val = jQuery(this).val();
        if (val === 'exchange') {
            jQuery('#reward_points_exchange_rate').removeAttr('disabled');
            jQuery('#reward_points_global_type_val').attr('disabled', true);
        } else {
            jQuery('#reward_points_global_type_val').removeAttr('disabled');
            jQuery('#reward_points_exchange_rate').attr('disabled', true);
        }
    });

    if ( jQuery('#reward_points_global').is(':checked') ) {
        var parent = jQuery('#reward_points_global').parents('tr');
        parent.siblings('tr').css('display', 'table-row');
    } else {
        var parent = jQuery('#reward_points_global').parents('tr');
        parent.siblings('tr').css('display', 'none');
    }

    jQuery('#reward_points_global').on('click', function(){
        var parent = jQuery(this).parents('tr');
        if ( jQuery(this).is(':checked') ) {
            parent.siblings('tr').css('display', 'table-row');
        } else {
            parent.siblings('tr').css('display', 'none');
        }
    });
});