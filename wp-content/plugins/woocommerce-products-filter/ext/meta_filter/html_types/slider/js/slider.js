function woof_init_meta_slider() {

    jQuery.each(jQuery('.woof_metarange_slider'), function (index, input) {
        try {
            jQuery(input).ionRangeSlider({
                min: jQuery(input).data('min'),
                max: jQuery(input).data('max'),
                from: jQuery(input).data('min-now'),
                to: jQuery(input).data('max-now'),
                type: 'double',
                prefix: jQuery(input).data('slider-prefix'),
                postfix: jQuery(input).data('slider-postfix'),
                prettify: true,
                hideMinMax: false,
                hideFromTo: false,
                grid: true,
                step: jQuery(input).data('step'),
                onFinish: function (ui) {
                    woof_current_values[jQuery(input).attr('name')] = parseFloat(ui.from, 10) + "-" + parseFloat(ui.to, 10);
                    //***
                    woof_ajax_page_num = 1;
                    //jQuery(input).within('.woof').length -> if slider is as shortcode
                    if (woof_autosubmit || jQuery(input).within('.woof').length == 0) {
                        woof_submit_link(woof_get_submit_link());
                    }
                    return false;
                }
            });
        } catch (e) {

        }
    });
}
