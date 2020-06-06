

(function ($) {

    "use strict";

    var popup_contenat_id = 'signer-signature'; //Id of the pop-up content

    var sender_input = $('input[name="sender_signature"]');
    sender_input = sender_input[0];
    var sender_sig = $(sender_input).val();

    // Sigpad Options
    var edit_opts = {
        drawOnly: true,
        validateFields: false,
        penColour: '#000000',
        lineWidth: '0',
        lineColour: 'rgba(255,255,255,0)',
        displayOnly: false, //useful for when re-signing
        bgColour: 'transparent'
    };

    var display_opts = {
        penColour: '#000000',
        displayOnly: true,
        bgColour: 'transparent',
    };

    // remove footer if visiting from mobile . 
    if (esigAjax.esig_mobile == '1')
    {
        $('#esig-footer').hide();
        $('#esig-mobile-footer').hide();
    } else
    {
        $('#esig-footer').show();
        $('#esig-mobile-footer').hide();
    }

    // fill the screen width input 

    $("#esig-screen-width").val(screen.width);
    // tab start here
    $('#tabs').smartTab({autoProgress: false, stopOnFocus: true, transitionEffect: 'vSlide'});

    // If read-only form is present, the doc has been signed. Show signatures

    if (document.forms['readonly']) {

        if (esigAjax.esig_mobile == '1')
        {
            $('#esig-footer').hide();
            $('#esig-mobile-footer').show();
        }

        var sig = "yes";

    } else {


        var recipient_input = $('input[name="recipient_signature"]');

        //console.log('reci:'+$('input[name="recipient_signature"]').val());
        recipient_input = recipient_input[0];

        if (recipient_input) {

            var sig = recipient_input.value;

            var signaturePadEdit = $('.signature-wrapper').signaturePad(edit_opts);

            var signatureDisplayRecipient = $('.signature-wrapper-displayonly.recipient').signaturePad(display_opts);


            if (sig != "") {

                if (signatureDisplayRecipient) {
                    signatureDisplayRecipient.regenerate(sig);
                }
                if (signaturePadEdit) {
                    signaturePadEdit.regenerate(sig);
                }
            }

            // Signature pop-up

            $('.signature-wrapper-displayonly').click(function (e) {

                //if($('#sign-form').valid()){
                //e.preventDefault();
                validator.form();

                if (validator.numberOfInvalids() != 0) {
                    return;
                }

                if ($("#signatureCanvas2").hasClass("esig-signing-disabled")) {
                    return false;
                }


                if (esigAjax.esig_mobile == '1')
                {

                    var fname = $('input[name="recipient_first_name"]').val();

                    if (/<(.*)>/.test(fname))
                    {
                        $('#recipient_first_name').focus();
                        return false;
                    }

                    $('#esignature-in-text').val(fname);

                    $("#esig-mobile-dialog").modal('show');
                    // scrolling top to make signature easy. 
                   // $(document).scrollTop(0);


                } else
                {

                    //var recipient_first_name = $('#recipient_first_name');

                    var fname = $('input[name="recipient_first_name"]').val();

                    if (/<(.*)>/.test(fname))
                    {
                        $('#recipient_first_name').focus();
                        return false;
                    }

                    // fill with value  modal signer anme 
                    $('#esignature-in-text').val(fname);
                    $("#esignature-in-text").css('border', '0px solid #ff0000');
                    $('#esignature-in-text').formError({remove: true});

                    //$('#esig-iam').html(fname);
                    $('#esig-iam-draw').html(fname);
                    $('#esig-iam-type').html(fname);

                    tb_show(Esign_localize.add_signature, '#TB_inline?width=480&height=370&inlineId=signer-signature');
                }

                document.getElementById('page_loader').style.display = 'none';
                //alert('hey hey');			
                //}
            });

            // modal hiding 
            $('#esig-mobile-sig-dismiss').click(function () {
                $('#mobilesigpad').modal('hide')

            });

            // Signature inserted event
            var popup_input = $('.signature-wrapper input[name="output"]');

            $('.signature-wrapper .saveButton').click(function () {

                //if legan name is blank 
                var signature_type = $("input[name='esignature_in_text']").val();

                if (/<(.*)>/.test(signature_type))
                {
                    $("input[name='esignature_in_text']").focus();
                    return false;
                }

                if (!signature_type)
                {
                    $("input[name='esignature_in_text']").focus();
                    $("#esignature-in-text").css('border', '1px solid #ff0000');
                    return false;
                }
                
                if (!esign.isFullName(signature_type) && $("#recipient_first_name").hasClass("esig-no-form-integration"))
                {
                    //alert("A full name including your first and last name is required.");
                    $("#esignature-in-text").formError("A full name including your first and last name is required to sign this document. Spaces after last name will prevent submission.");
                    $("input[name='esignature_in_text']").focus();
                    $("#esignature-in-text").css('border', '1px solid #ff0000');
                    return false;
                }


                $("input[name='recipient_first_name']").val(signature_type);
                // signature adding removing type and enabling draw
                $('#esig-signature-added').show();
                $('.signature-wrapper-displayonly .esig-sig-type').remove();
                var w = $(window).width();
                var canvaswidth = (w / 4) * 3;
                $('#signatureCanvas2').show();
                $("#signatureCanvas2").attr("width", "500");
                signatureDisplayRecipient.regenerate(popup_input.val());
                tb_remove();

                $('.signature-wrapper input[name="output"]').trigger('change');

                $('.signature-wrapper-displayonly .sign-here').removeClass('unsigned').addClass('signed');
                $('.signature-wrapper-displayonly .sign-here').addClass('sigvalid');

                // validation checking here 
                validator.form();

                if (validator.numberOfInvalids() == 0) {
                    $('#esig-print-button').remove();
                    $('#esig-pdf-download').remove();

                    $('#esig-agree-button').removeClass('disabled').trigger('showtip');
                }


            });
        } // undefined checking here. 
    }

    $('.closeButton').click(function () {
        $('.mobile-overlay-bg').hide();
        $('body').removeClass('mobile-overlay-bg-black');
       
    });

    var popup_invite = $('.signatures input[name="invite_hash"]');


    if (!sig)
    {
        if ($('.signature-wrapper-displayonly-signed').hasClass('signed'))
        {
            sig = 'yes';
        }

    }

    // Footer Ajax. Runs afer each page load for dynamic footer
    if (esigAjax.preview || (esigAjax.document_id && sig)) {
        //alert(esigAjax.esig_mode);
        $('.esig-container').hide();
        $.get(esigAjax.ajaxurl,
                {
                    action: "wp_e_signature_ajax",
                    method: "get_footer_ajax",
                    className: "WP_E_Shortcode",
                    inviteCode: popup_invite.val(),
                    url: esigAjax.ajaxurl,
                    preview: esigAjax.preview,
                    document_id: esigAjax.document_id,
                    esig_mode: esigAjax.esig_mode,
                    cc_user_preview: esigAjax.cc_user_preview,
                },
                function (data) {
                    $('#esig-footer').html(data);
                }
        );


    }
    // mobile submit start here 
    $('#esign_click_mobile_submit').click(function () {

        $('#esign_click_submit').trigger('click');
    });

    // Agree button is disabled until document is signed
    $('#esig-agree-button').click(function () {

        validator.form();

        if (validator.numberOfInvalids() > 0)
        {
            return false;
        }

        if ($('#esig-agree-button').hasClass('disabled')) {
            return false;
        }

        $('.mobile-overlay-bg').hide();
        document.getElementById('page_loader').style.display = 'block';
        var overlay = $('<div class="page_loader_overlay"></div>').appendTo('body');
        $(overlay).show();

        // disabling agree and sign but so that uesr can submit only one time 
        $('#esig-agree-button').addClass('disabled').trigger('hidetip');
        $('#esig-agreed').html(Esign_localize.signing);

        $('form[name="sign-form"]').submit();

        return false;
    });

    $('#esig-agree-button').addClass('disabled');


    var validator = $('#sign-form').validate({
        errorClass: 'esig-error',
        invalidHandler: function (event, validator) {
            try {
                var first_error = validator.errorList[0].element;

                var tag = first_error.tagName;
                var field_name = first_error.getAttribute('name');

                $('html, body').animate({
                    scrollTop: $(tag + '[name="' + field_name + '"]').offset().top - 20
                }, 1500);

            } catch (err) {

                console.log('invalidHandler Error' + err)
            }
        },
        errorPlacement: function (error, element) {

            if (element.attr('type') == "checkbox") {

                error.insertAfter('#error-' + element.attr('id'));

            } else if (element.attr('type') == "radio") {

                error.insertAfter('#error-' + element.attr('id'));
            } else {
                error.insertAfter(element);
            }


        }
    });


    // Validate form when user has signed
    $('#esig-type-in-text-accept-signature').click(function () {


        var signature_type = $("input[name='esignature_in_text']").val();


        if (signature_type.replace(/\s+/g, '').length == 0)
        {
            $("input[name='esignature_in_text']").focus();
            $("#esignature-in-text").css('border', '1px solid #ff0000');
            return false;
        }
        if (!signature_type)
        {
            $("input[name='esignature_in_text']").focus();
            return false;
        }
        if (!esign.isFullName(signature_type) && $("#recipient_first_name").hasClass("esig-no-form-integration"))
        {
            //alert("A full name including your first and last name is required.");
            $("#esignature-in-text").formError("A full name including your first and last name is required to sign this document. Spaces after last name will prevent submission.");
            $("input[name='esignature_in_text']").focus();
            $("#esignature-in-text").css('border', '1px solid #ff0000');
            return false;
        }

        validator.form();

        if (validator.numberOfInvalids() == 0) {

            $('#esig-print-button').remove();
            $('#esig-pdf-download').remove();
            $('#esig-agree-button').removeClass('disabled').trigger('showtip');

            var fname = $("input[name='recipient_first_name']").val();
            $('#esig-iam').html(Esign_localize.iam + ' ' + fname + ' ' + Esign_localize.and + ' ');
        }
    });


    $('#esignature-in-text').keypress(function () {
        $(this).formError({remove: true});
    });

    // Validate form when user has signed
    $('.signature-wrapper input[name="output"]', '#sign-form').change(function () {

        validator.form();

        if (validator.numberOfInvalids() == 0) {
            $('#esig-print-button').remove();
            $('#esig-pdf-download').remove();
            $('#esig-agree-button').removeClass('disabled').trigger('showtip');

            var fname = $("input[name='recipient_first_name']").val();
            $('#esig-iam').html(Esign_localize.iam + ' ' + fname + ' ' + Esign_localize.and + ' ');
        }
    });



    // Eager validate after signed
    $('input[type="text"], select, checkbox', '#sign-form').change(function () {

        //get legan name 

        if ($('.signature-wrapper-displayonly .sign-here').hasClass('sigvalid')) {
            validator.form();
            if (validator.numberOfInvalids() == 0) {
                $('#esig-print-button').remove();
                $('#esig-pdf-download').remove();
                $('#esig-agree-button').removeClass('disabled').trigger('showtip');


                $('#esig-iam').html(Esign_localize.iam + ' ' + fname + ' ' + Esign_localize.and + ' ');

            } else {
                $('#esig-agree-button').addClass('disabled').trigger('hidetip');
            }
        }
    });



    // Agree Button Tool Tip
    $.fn.tooltips = function (el) {

        var $tooltip,
                $body = $('body'),
                $el;


        return $("#esign_click_submit").each(function (i, el) {

            $el = $(el).attr("data-tooltip", i);

            // Make DIV and append to page
            var content = $('#agree-button-tip').html();

            var $tooltip = $('<div class="sig-tooltip"  data-tooltip="' + i + '">' +
                    content +
                    '<div class="arrow"></div></div>'
                    ).appendTo(el);


            var overlay = $('<div class="esig-tooltip-overlay"></div>').appendTo('body');

            // Position right away, so first appearance is smooth
            var linkPosition = $el.offset();

            var topOffset = -2; // Offset the top position of the tip

            $tooltip.css({
                top: 0 - $tooltip.outerHeight() - topOffset,
                left: linkPosition.left - ($el.width() / 2)
            });

            $el.on('showtip', function () {

                $el = $("#esign_click_submit");

                if ($el.hasClass('disabled')) {
                    //return;
                }

                $tooltip = $('div[data-tooltip=' + $el.data('tooltip') + ']');

                // Reposition tooltip, in case of page movement e.g. screen resize
                var linkPosition = $el.offset();

                $tooltip.css({
                    top: 0 - $tooltip.outerHeight() - topOffset,
                    left: linkPosition.left - 125
                });

                // Adding class handles animation through CSS
                $tooltip.addClass("active");

                //$(overlay).show();

            });

            $el.on('hidetip', function () {
                $el = $(this);
                $tooltip = $('div[data-tooltip=' + $el.data('tooltip') + ']');
                $tooltip.removeClass('active').addClass('disabled');
            });
        });

    } // End Tool Tip

  $('body').on('click', '.clearButton', function () {
      $('#esig-agree-button').addClass('disabled').trigger('hidetip');
  });

    // Click and show terms and condition 
    $('body').on('click', '.tooltip #esig-terms', function () {

        jQuery.ajax({
            type: "POST",
            url: esigAjax.ajaxurl + "&className=WP_E_Common&method=esig_get_terms_conditions",
            success: function (data, status, jqXHR) {

                $('.esig-terms-modal-lg .modal-body').html(data);
            },
            error: function (xhr, status, error) {
                $('.esig-terms-modal-lg .modal-body').html('<h1>No internet connection</h1>');
            }
        });

    });

    // click terms of service . 
    $('body').on('click', '#esig-terms', function () {

        if (esigAjax.esig_mobile == '1')
        {
            $("#esig-mobile-dialog").modal('hide');
        }

        $.post(esigAjax.ajaxurl + "&className=WP_E_Common&method=esig_get_terms_conditions", function (data) {

            $('.esig-terms-modal-lg .modal-body').html(data);

            // $('.esig-terms-modal-lg .modal-body').append("close<br></br>");

        });


    });





    // inserting signature from mobile
    $("#mobile-adopt-sign").on("tap", function () {
        // $("#mobile-adopt-sign").click(function () {


        var fname = $("input[name='recipient_first_name']").val();
        if (!fname)
        {
            alert("Your legal name can not be empty.");
            return false;
        }

        if (fname.replace(/\s+/g, '').length == 0)
        {
            alert("Your legal name can not be empty.");
            $("input[name='esignature_in_text']").focus();
            return false;
        }

        if (!esign.isFullName(fname) && $("#recipient_first_name").hasClass("esig-no-form-integration"))
        {
            alert("A full name including your first and last name is required to sign this document. Spaces after last name will prevent submission.");
            $("input[name='esignature_in_text']").focus();
            return false;
        }

        if (/<(.*)>/.test(fname))
        {
            $('#recipient_first_name').focus();
            return false;
        }

        if ($(this).hasClass('already-signed')) {
            return false;
        } else {
            $(this).addClass('already-signed');
            $(this).html('Signing...');
        }
        var signature_type = $("input[name='esignature_in_text']").val();

        if (signature_type)
        {
            var font = $('#font-type').val();
            var draw_signature = $("input[name='output']").val();
            var font_type = $("input[name='font_type']").val();

            var htmlcontent = '<div class="sign-here pad signed esig-sig-type esig-signature-type-font' + font + '" width="100%"><span class="esig-sig-type1">' + signature_type + '</span></div>';
            htmlcontent += '<input type="hidden" name="esig_signature_type" value="typed">';
            htmlcontent += '<input type="hidden" name="esignature_in_text" value="' + signature_type + '">';
            htmlcontent += '<input type="hidden" name="font_type" value="' + font_type + '">';
            //  htmlcontent += '<input type="hidden" name="recipient_signature" class="output" value="'+ draw_signature +'"></div>';


            // getting first name value


            if (signature_type != fname) {
                $("input[name='recipient_first_name']").val(signature_type);
            }

            $("input[name='esignature_in_text']").val(signature_type);

            $('#esig-mob-input').html(htmlcontent);

            var newSize = signature_type.length;
            newSize = 64 - (1.5 * newSize);
            $('.esig-signature-type-font' + font).css("font-size", newSize + "px");
        }
        if (popup_input.val())
        {
            // making larger signature in small
            signatureDisplayRecipient.regenerate(popup_input.val());

        }


        validator.form();

        if (validator.numberOfInvalids() > 0)
        {
            return false;
        }

        $('#sign-form')[0].submit();
        return false;

    });



})(jQuery);

jQuery(".esig-template-page .agree-button").tooltips();



/**
 * @author Shawna Culp
 * @description keyboard only, the signature area is keyboard accessible.https://secure.helpscout.net/conversation/356638108/14101/?folderId=471644
 * @
 * @param {type} $
 * @returns {undefined}
 */

(function ($) {

    $(document).ready(function () {
        makeSignatureKeyboardAccessible();
    });

    function makeSignatureKeyboardAccessible() {

        var signaturePopup = $('.signature-wrapper-displayonly');

        if (signaturePopup) {
            signaturePopup.attr('tabindex', 0);
        }

        $(document).on('keypress', signaturePopup, function (e) {
            var code = e.keyCode || e.which;

            if (code == 13) {
                signaturePopup.click();
            }
        });
    }

})(jQuery);








