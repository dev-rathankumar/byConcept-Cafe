(function ($) {

    $('#esigactivecampaign').click(function () {
        var esig_active_tag = $('input[name="esig_active_campaign_tag"]');
        if (!esig_active_tag.val()) {
            return false;
        }
        var esig_active_tag_id = $('input[name="esig_active_document_id"]');
        var esig_campaign_list = '';
        esig_campaign_list = $("input[name='esig_active\\[\\]']").map(function () {
            if ($(this).is(':checked')) {
                return $(this).val();
            }
        }).get();

        jQuery.ajax({
            type: "POST",
            url: active_campaign_script.ajaxurl,
            data: {
                esig_active_campaign_tag: esig_active_tag.val(),
                esig_active_document_id: esig_active_tag_id.val(),
                esig_campaign_list: esig_campaign_list
            },
            success: function (data, status, jqXHR) {
                jQuery("#esig_active_campaign").html(data);
                $('input[name="esig_active_campaign_tag"]').val('');
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText);
            }
        });
        return false;
    });

})(jQuery);

