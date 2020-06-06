(function ($) {

    function get_tinymce_content() {
        if ($("#wp-document_content-wrap").hasClass("tmce-active")) {
            return tinyMCE.activeEditor.getContent();
        }
        else {
            return $('#document_content').val();
        }
    }

    function autosave() {

        jQuery('#document_form').each(function () {

            var document_title = $('#document-title').val();
            var document_content = get_tinymce_content();

            if (document_title == "" && document_content == "")
            {

                return false;
            }

            $('#esig-preview-document').show();
            jQuery.ajax({
                url: autosaveAjax.ajaxurl + "?action=esig_auto_save",
                data: {
                    'autosave': true,
                    'esig_type': autosaveAjax.doc_type,
                    'document_content': get_tinymce_content(),
                    'formData': $(this).serialize()
                },
                type: 'POST',
                success: function (data) {
                    // alert(get_tinymce_content());
                    if (data) {
                        if (!isNaN(data)) {

                            var docId = $('#document_id').val();
                            if (!docId) {
                                $('#document_id').val(data);
                                var previewLink = $("#esig-preview-link").attr('href');
                                if (previewLink) {
                                    var newLink = updateQueryStringParameter(previewLink, 'document_id', data);

                                    $("#esig-preview-link").attr('href', newLink);
                                }
                            }


                        }

                    } else {
                        // alert("Oh no!");
                    }
                } // end successful POST function
            }); // end jQuery ajax call
        }); // end setting up the autosave on every form on the page
    } // end function autosave()

    var interval = setInterval(autosave, 10 * 1000);
    //alert('test');
    $("form input[type=submit]").click(function () {

        clearInterval(interval); // stop the interval
    });


})(jQuery);


function updateQueryStringParameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
        return uri.replace(re, '$1' + key + "=" + value + '$2');
    }
    else {
        return uri + separator + key + "=" + value;
    }
}