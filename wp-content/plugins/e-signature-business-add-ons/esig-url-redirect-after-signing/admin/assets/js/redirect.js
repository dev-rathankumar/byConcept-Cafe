(function ($) {

   

    $('#redirectForm').click(function () {

        var esig_active_tag = $('input[name="esig_redirect_url"]');

        var errorMsg ='<label id="redirect-errormsg" class="error">Please enter a valid URL.</label>';
         $("#redirect-errormsg").remove();
         
        if (!esig_active_tag.val()) {
            $(this).after(errorMsg);
            return false;
        }
        
        if (!esign.isUrl(esig_active_tag.val())) {
            $(this).after(errorMsg);
            return false;
        }
        
        var esig_url_tag_id = $('input[name="esig_url_id"]');


        jQuery.ajax({
            type: "POST",
            url: ajax_script.ajaxurl,
            data: {
                esig_redirect_url: esig_active_tag.val(),
                esig_url_id: esig_url_tag_id.val(),
            },
            success: function (data, status, jqXHR) {
                $('input[name="esig_redirect_url"]').val('');
                jQuery("#esig_url_redirect").html(data);
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText);
            }
        });
        return false;
    });

})(jQuery);

