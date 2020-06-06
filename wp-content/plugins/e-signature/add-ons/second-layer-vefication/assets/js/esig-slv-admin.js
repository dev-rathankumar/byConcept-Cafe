(function ($) {

    // $('#tabs-access-code').smartTab({autoProgress: false, stopOnFocus: true, transitionEffect: 'fade in'});

    $("#access_cssmenu a").click(function (event) {
        event.preventDefault();
        $('.esig-error-box').remove();
        // $(this).parent().siblings().removeClass("current");
        $("#access_cssmenu a").removeClass("current");
        $(this).addClass("current");
        var tab = $(this).attr("href");
        $(".tab-content").not(tab).css("display", "none");
        $(tab).fadeIn();
    });


    $('body').on('click', '#recipient_emails .second-layer', function () {


        // select default none in dialog. 
        if (!$("#access_cssmenu a").hasClass("current")) {
            $("#esig-access-none").addClass("current");
        }

        var email_address = $(this).closest('div').parent().find('input[name="recipient_emails\\[\\]"]').val();

        if (esig_validation.is_email(email_address)) {

            // passing email address to dialog 
            $("#esig-slv-email-address").val(email_address);

            var accessCode = $(this).data('accesscode');

            if (accessCode) {
                $('#esig_access_code').val(accessCode);
            } else {
                //show dialog
                var code = slv_meta_get(email_address);
                if (code) {
                    $('#esig_access_code').val(code);
                } else {
                    $('#esig_access_code').val("");
                }
            }

            $("#esig-access-code-verification").dialog({
                dialogClass: 'esig-access-dialog',
                height: 400,
                width: 495,
                modal: true,
            });

        } else {

            $('.esig-error-box').remove();
            $(this).closest('div').parent().after('<div class="row esig-error-box"><div class="col-md-12">*You must fill e-mail address field.</div></div>');
        }

    });

    $('body').on('click', '#recipient_emails .edit-second-layer', function () {

        // select default none in dialog. 
        $("#esig-access-none").addClass("current");
        var email_address = $(this).closest('div').parent().find('input[name="recipient_emails\\[\\]"]').val();
        if (esig_validation.is_email(email_address)) {

            // passing email address to dialog 
            $("#esig-slv-email-address").val(email_address);
            //show dialog
            $("#esig-access-code-verification").dialog({
                dialogClass: 'esig-access-dialog',
                height: 400,
                width: 500,
                modal: true,
            });

        } else {
            $('.esig-error-box').remove();
            $(this).closest('div').parent().after('<div class="row esig-error-box"><div class="col-md-12">*You must fill e-mail address field.</div></div>');
        }

    });




    $("#submit_send_layer").click(function (e) {

        e.preventDefault();
        var email_address = $("#esig-slv-email-address").val();
        var access_security_code = $("#access_code").find('input[name="esig_access_code"]').val(); //get the value..

        if (esign.isEmpty(access_security_code)) {
            $('.esig-error-box').remove();
            $("#esig_access_code").parent().parent().parent().append('<div class="esig-error-box" style="margin:40px;">*You must fill valid access code.</div>');
            return false;
        }
        if (/\s/.test(access_security_code)) {
            $('.esig-error-box').remove();
           
            $("#esig_access_code").parent().parent().parent().append('<div class="esig-error-box" style="margin:40px;">*Space is not allowed.</div>');
            return false;
        }

        slv_meta_save(email_address, access_security_code);

        $("#esig-access-code-verification").dialog("close");


    });


    $('body').on('input', "#esig_access_code", function (e) {
        // $('#esig_access_code').bind('keyup', function () {
        e.preventDefault();
        var access_security_code = $(this).val(); //get the value..

        if (!esign.isEmpty(access_security_code)) {
            $('.esig-error-box').remove();
            return false;
        }
    });


    ////cancel second layer verification
    $("#submit_send_cancel").click(function () {

        $("#esig-access-code-verification").dialog("close");

    });

    ///second layer for temp
    $('body').on('click', '#standard_view_popup_bottom .second-layer', function () {


        // select default none in dialog. 
        if (!$("#access_cssmenu a").hasClass("current")) {
            $("#esig-access-none").addClass("current");
        }

        var email_address = $(this).closest('div').parent().find('input[name="recipient_emails\\[\\]"]').val();

        if (esig_validation.is_email(email_address)) {

            // passing email address to dialog 
            $("#esig-slv-email-address").val(email_address);
            // $('#esig_access_code').val("");
            //show dialog
           
                //show dialog
                var code = slv_meta_get(email_address);
                if (code) {
                    $('#esig_access_code').val(code);
                } else {
                    $('#esig_access_code').val("");
                }
           
            
            $("#esig-access-code-verification").dialog({
                dialogClass: 'esig-access-dialog',
                height: 400,
                width: 495,
                modal: true,
            });

        } else {

            $('.esig-error-box').remove();
            // $('.af-inner').append('<span class="esig-error-box">*You must fill e-mail address field.</span>');
            $(this).closest('div').parent().after('<div class="row esig-error-box"><div class="col-md-12" align="center">*You must fill e-mail address field.</div></div>');
        }

    });



    $("#access_code_login").click(function (e) {
        e.preventDefault();
        var access_security_code = $("#access_code").find('input[name="esig_access_code"]').val(); //get the value..

        $.post(esigAjax.ajaxurl + "?action=esig_access_code_verification", {access_code: access_security_code}).done(function (data) {
            // alert("Data Loaded: " + data);
            if (data == "success") {
                $("#esig-access-code-verification").dialog("close");
            } else {
                alert('dfbhdchfb');
            }
        });
    });


    // for sad document 
    // Show or hide the stand alone console when the box is checked.
    $('input[name="esig_second_layer_verification"]').on('change', function () {
        if ($('input[name="esig_second_layer_verification"]').attr('checked')) {
            //show dialog
            // passing email address to dialog 
            $("#esig-slv-email-address").val('stand-alone');

            $("#esig-access-code-verification").dialog({
                dialogClass: 'esig-access-dialog',
                height: 400,
                width: 510,
                modal: true,
            });

        }
    });



})(jQuery);

/**
 *  slv meta saving. 
 */
function slv_meta_save(email, access_code) {

    var slv_settings = esign.getCookie("esig-slv-settings");

    if (slv_settings) {
        var slv_json = JSON.parse(slv_settings);
        slv_json[urlFriendly(email)] = access_code;

        esign.setCookie("esig-slv-settings", JSON.stringify(slv_json), 1 * 60 * 60);

    } else {

        var slv_settings = {};
        slv_settings[urlFriendly(email)] = access_code;
        esign.setCookie("esig-slv-settings", JSON.stringify(slv_settings), 1 * 60 * 60);
    }
}

function slv_meta_get(email) {
    var json = esign.getCookie("esig-slv-settings");
    if (!json) {
        return false;
    }
    var slv_settings = JSON.parse(json);
    if (slv_settings) {
        return slv_settings[urlFriendly(email)];
    }
    return false;
}

function urlFriendly(emailAddress) {
    var str = btoa(emailAddress);
    return str.replace(/\+/g, '-').replace(/\//g, '_').replace(/\=+$/, '');
}
