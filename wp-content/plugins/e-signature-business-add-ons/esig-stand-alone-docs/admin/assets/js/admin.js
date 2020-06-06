(function ($) {

    //"use strict";

    //$(function(){


    // Show or hide the stand alone console when the box is checked.
    $('input[name="stand_alone"]').on('change', function () {
        if ($('input[name="stand_alone"]').attr('checked')) {
            $.fn.show_SAD_controls();

            $("#postimagediv").css("margin-top", "15px");
        } else {
            $('#stand_alone_options').hide(100);
            $('#stand_alone_page').val('');
            $('#submit_send').show();
            $('#submit_save').val('Save as draft');
            //$('.esign-form-panel').slideDown(500);
        }
    });



    $.fn.show_SAD_controls = function () {
        $('#stand_alone_options').show(100);


        $('#submit_send').hide();
        $('#submit_save').hide();

        $('#esig_submit_section').append('<input type="submit" value="Publish Document"  class="button button-primary button-large" id="submit_send_stand"  name="send_sad">');
        $('#esig_submit_section').append('<input type="submit" value="Save as Draft"  class="button button-secondary button-large" id="submit_save_stand"  name="save_sad">');
    }

    // Use chosen js for select menu
    if ($('input[name="stand_alone"]').attr('checked')) {

        $.fn.show_SAD_controls();
        $('#stand_alone_style').hide();
        $('.basic_esign').hide();
    }

    // Modal dialog box for the #stand_alone_page select menu.
    var $overwrite = $("#esig-sad-overwrite-modal");
    $overwrite.dialog({
        'dialogClass': 'wp-dialog esig-sad-dialog',
        'title': 'Whoah there',
        'modal': true,
        'autoOpen': false,
        'closeOnEscape': true,
        'buttons': {
            "Overwrite": function () {
                $(this).dialog('close');
            },
            "Cancel": function () {
                var old_val = $('#stand_alone_page').data('original');
                $('#stand_alone_page').val(old_val);
                $('#stand_alone_page').trigger('chosen:updated');
                $(this).dialog('close');
            }
        }
    });

    // On-change event for #stand_alone_page select menu.
    $('#stand_alone_page').change(function () {
        var selected = $('option:selected', this);
        var this_doc_id = $('input[name="document_id"]').val();

        // If we're overwriting a page used by another document id
        if ($(selected).data('used') && $(selected).data('used') != this_doc_id) {
            $overwrite.dialog('open'); // Popup a dialog
        }
    });


    // sad doucument submission validation 
    $("#submit_send_stand").on("click", function () {
        var sadpage = $("#stand_alone_page").val();
        if (sadpage == "none") {
            alert("Easy Tiger, a Stand Alone Document lives on your website and needs you to assign a page for it. You can assign a page below or save this document as a draft and come back to it later");
            return false;
        } else {
            return true;
        }
    });


    // Action link for document index page
    $('.send_stand_alone_invite a').click(function () {

        var sad_url = $(this).data('url');
        $('#esig_sad_popup_hidden .invite_url').val(sad_url);

        var doc_id = $(this).data('document');
        $('.invite_form input[name="document_id"]').val(doc_id);

        $('.invite_form input[name="url"]').val(sad_url);

        $('form.invite_form .loader').hide();

        var copy_msg = (getOs() == 'MacOS') ? 'Press ⌘-C to copy' : 'Press Ctrl-C to copy';
        $('.invite_box .copy-msg').html(copy_msg);


        var doc_title = $(this).data('title');
        $('.document_title_caption').show();
        $('.document_title_caption').html(doc_title);
        // removing previous input 
        if ($('#sad-invite-submit').hasClass('disabled'))
        {
            $('#sad-invite-submit').removeClass('disabled');
            $('#sad-invite-name').val('');
            $('#sad-invite-email').val('');
        }


        tb_show('+ Send An Invite', '#TB_inline?height=460&inlineId=esig_sad_popup_hidden');
        esign.tbResize();

        $('.esig_sad_popup .invite_url').select();
        return false;
    });


    /* if (sadmyAjax.doc_preview_id != undefined) {
     $("#sad_document_" + sadmyAjax.doc_preview_id).trigger("click");
     }*/

    // Select the page url upon popup
    $('.esig_sad_popup .invite_url').click(function () {
        $(this).select();
    });

 $('body').on('keyup', "#sad-invite-name", function () {
     
     $("#legal-name-validation").remove();
     
 });

    // Invite Button click event
    $('.esig_sad_popup .invite_form input[type="submit"]').click(function () {

        // validation start here 
        var name = $('input[name="name"]').val();
        var email = $('input[name="email"]').val();
        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;


       // var regexp = new RegExp(/^[a-z]([-']?[a-z]+)*( [a-z]([-']?[a-z]+)*)+$/i);
        if (!esign.isFullName(name)) {
            $('input[name="name"]').focus().css('border', '1px solid red');
            if (!$("#legal-name-validation").hasClass('validation')) {
                $("#sad-invite-name").parent().after("<div id='legal-name-validation' class='validation' style='color:red;margin-bottom:10px'>A full name including your first and last name is required to sign this document. Spaces after last name will prevent submission.</div>");
            }
            return false;
        }

        if (name == '')
        {
            $('input[name="name"]').focus().css('border', '1px solid red');
            return false;
        }
        else
        {
            $("#sad-invite-name").parent().next(".validation").remove(); // remove it
            $('input[name="name"]').css('border', '0px solid red');
        }

        if (email == '')
        {
            $('input[name="email"]').focus().css('border', '1px solid red');
            return false;
        } else if (!esign.is_valid_email(email))
        {
            $('input[name="email"]').focus().css('border', '1px solid red');
            return false;
        } else
        {

            $('input[name="email"]').css('border', '0px solid red');
        }
        // validation end here 

        var serialized_vars = $('form.invite_form').serialize();

        var doc_id = $(this).data('document');

        if ($(this).hasClass('disabled'))
        {

            return false;
        } else
        {
            $(this).addClass('disabled');
        }


        // Send invitation
        $.ajax({
            type: "post",
            dataType: "json",
            url: myAjax.ajaxurl,
            data: serialized_vars + "&action=esig_sad_invite_user",
            success: function (response) {
                if (response.success) {
                    $(this).removeClass('disabled');
                    tb_remove();
                } else {
                    alert('There was a problem. Your invitation could not be sent.');
                }
            },
            beforeSend: function () {
                $('form.invite_form .loader').show();
            },
            error: function (jqXHR, status, error) {
                console.log('invite ajax error:' + error)
            },
        })

        return false;
    });

    //});

    function getOs() {
        var OSName = "Unknown OS";
        if (navigator.appVersion.indexOf("Win") != -1)
            OSName = "Windows";
        else if (navigator.appVersion.indexOf("Mac") != -1)
            OSName = "MacOS";
        else if (navigator.appVersion.indexOf("X11") != -1)
            OSName = "UNIX";
        else if (navigator.appVersion.indexOf("Linux") != -1)
            OSName = "Linux";
        return OSName;
    }

}(jQuery));

// load sad document preview popups
document.addEventListener('DOMContentLoaded', function () {
    //alert("Ready!");
    jQuery("#sad_document_" + sadmyAjax.doc_preview_id).trigger("click");
}, false);