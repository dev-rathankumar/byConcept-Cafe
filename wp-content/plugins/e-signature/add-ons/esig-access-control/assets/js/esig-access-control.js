/**
 * Callback function for the 'click' event of the 'Set Footer Image'
 * anchor in its meta box.
 *
 * Displays the media uploader for selecting an image.
 *
 * @param    object    $    A reference to the jQuery object
 * @since    0.1.0
 */
function renderMediaUploader($) {
    'use strict';

    var file_frame, image_data, json;

    /**
     * If an instance of file_frame already exists, then we can open it
     * rather than creating a new instance.
     */
    if (undefined !== file_frame) {

        file_frame.open();
        return;

    }

    /**
     * If we're this far, then an instance does not exist, so we need to
     * create our own.
     *
     * Here, use the wp.media library to define the settings of the Media
     * Uploader. We're opting to use the 'post' frame which is a template
     * defined in WordPress core and are initializing the file frame
     * with the 'insert' state.
     *
     * We're also not allowing the user to select more than one image.
     */
    file_frame = wp.media.frames.file_frame = wp.media({
        frame: 'post',
        state: 'insert',
        multiple: false
    });

    /**
     * Setup an event handler for what to do when an image has been
     * selected.
     *
     * Since we're using the 'view' state when initializing
     * the file_frame, we need to make sure that the handler is attached
     * to the insert event.
     */
    file_frame.on('insert', function () {

        // Read the JSON data returned from the Media Uploader
        json = file_frame.state().get('selection').first().toJSON();

        // First, make sure that we have the URL of an image to display
        if (0 > $.trim(json.url.length)) {
            return;
        }

        // After that, set the properties of the image and display it
        $('#esig-featured-image-container')
                .children('img')
                .attr('src', json.url)
                .show()
                .parent()
                .removeClass('hidden');

        // Next, hide the anchor responsible for allowing the user to select an image
        $('#esig-featured-image-container').prev().hide();

        // Display the anchor for the removing the featured image
        $('#esig-featured-image-container').next().show();

        // Store the image's information into the meta data fields
        $('#esig-image-thumbnail-src').val(json.url);

        $('#esig-set-image-thumbnail').parent().hide();

        $('#esig-remove-image-thumbnail').parent().show();

    });

    // Now display the actual file_frame
    file_frame.open();

}

/**
 * Callback function for the 'click' event of the 'Remove Footer Image'
 * anchor in its meta box.
 *
 * Resets the meta box by hiding the image and by hiding the 'Remove
 * Footer Image' container.
 *
 * @param    object    $    A reference to the jQuery object
 * @since    0.2.0
 */
function resetUploadForm($) {
    'use strict';

    // First, we'll hide the image
    $('#esig-featured-image-container').children('img').hide();

    // Then display the previous container
    $('#esig-featured-image-container').next().show();

    // We add the 'hidden' class back to this anchor's parent
    //$('#esig-featured-image-container').next().hide().addClass('hidden');

    // Finally, we reset the meta data input fields
    $('#esig-featured-image-info').children().val('');

    $('#esig-set-image-thumbnail').parent().show();

    $('#esig-remove-image-thumbnail').parent().hide();


}

/**
 * Checks to see if the input field for the thumbnail source has a value.
 * If so, then the image and the 'Remove featured image' anchor are displayed.
 *
 * Otherwise, the standard anchor is rendered.
 *
 * @param    object    $    A reference to the jQuery object
 * @since    1.0.0
 */
function renderFeaturedImage($) {

    /* If a thumbnail URL has been associated with this image
     * Then we need to display the image and the reset link.
     */
    //alert($.trim ( $( '#esig-image-thumbnail-src' ).val() ));
    if ('' !== $.trim($('#esig-image-thumbnail-src').val())) {

        $('#esig-featured-image-container').removeClass('hidden');

        $('#esig-set-image-thumbnail').parent().hide();

        //$( '#esig-remove-image-thumbnail' ).parent().hide();

    }
    else
    {
        $('#esig-featured-image-container').removeClass('hidden');

        //$( '#esig-set-image-thumbnail' ).parent().hide();

        $('#esig-remove-image-thumbnail').parent().hide();
    }

}






(function ($) {
    'use strict';

    $(function () {




        $('input[name="esig_required_wpmember"]').on('change', function ()
        {
            if ($('input[name="esig_required_wpmember"]').attr('checked'))
            {
                $('#esig_wpaccess_control_role').show();
            }
            else
            {
                $('#esig_wpaccess_control_role').hide();
            }

        });




        renderFeaturedImage($);
        $('#esig-set-image-thumbnail').on('click', function (evt) {

            // Stop the anchor's default behavior
            evt.preventDefault();

            // Display the media uploader
            renderMediaUploader($);

        });



        $('#esig-remove-image-thumbnail').on('click', function (evt) {

            // Stop the anchor's default behavior
            evt.preventDefault();

            // Remove the image, toggle the anchors
            resetUploadForm($);

        });

        //

        // description auto fill 
        $('#document_content').bind('input propertychange', function () {

            var doc_content = $("#document_content").val();

            var string_limit = 75;

            var short_description = doc_content.substring(0, string_limit);

            $("#esig_document_description").val(short_description);
        });

        // adding cusotmer function for its own updaes 
        $('#esig_document_description').on('input', function (e) {

            var ac_document_desc = $("#esig_document_description").val();

            var count = 75 - ac_document_desc.length;
            if (ac_document_desc.length >= 75)
            {
                $("#esig-char-count").html(0);
            }
            else {
                $("#esig-char-count").html(count);
            }

        });



    });

    // role section error msg hide 
    $('input[name="esig_access_control_role[]"]').on('change', function ()
    {
        $("#esig-valid-message").hide();

    });

    

})(jQuery);

// access control function 
    var access_role_validation = function () {

        if (jQuery('input[name="esig_required_wpmember"]').attr('checked'))
        {
            
             var user_selection = $("#esig_access_roles_option").val();
           // alert(user_selection);
            if(user_selection>0){
                 return false;
            }
            var access_roles = jQuery("input[name='esig_access_control_role\\[\\]']").map(function () {
                
                if(jQuery(this).attr('checked')){
                    return "checked" ;
                }
                else {
                    return "unchecked" ; 
                }
            });
            
            var error_count = 0 ;
            
             for (var i = 0; i < access_roles.length; i++) {

                
                if (access_roles[i]  == 'unchecked')
                {
                    error_count++ ; 
                }
             }
           
           
          if (access_roles.length == error_count)
            {

                jQuery("#esig-valid-message").show();

                return true;
            }
            else
            {
                return false;
            }

        }

        return false;
    }


