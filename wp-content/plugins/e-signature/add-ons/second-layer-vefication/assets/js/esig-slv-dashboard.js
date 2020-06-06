
(function ($) {


    /************ populate login ****************/
    $("#esig-access-code-login").click(function (e) {

        //validating data . 
        if (!esig_validation.is_email($("#esig-email-address").val())) {
            document.getElementById("esig-access-code").style.borderColor = "red";
            document.getElementById("access-error-text").className = 'text-error';
            $("#esig-show-error").show().html("<span class='icon-esig-alert invalid-email'></span><span class='error-msg' id='error-access-code'>Invalid E-mail address</span>");
            return false;
        }

        // checking valid access code input 
        if (!esig_validation.is_string($("#esig-access-code").val())) {
            document.getElementById("esig-access-code").style.borderColor = "red";
            document.getElementById("access-error-text").className = 'text-error';
            $("#esig-show-error").show().html("<span class='icon-esig-alert invalid-email'></span><span class='error-msg' id='error-access-code'>Invalid Access Code/Password</span>");
            return false;
        }


        var data = {
            "esig_email_address": $("#esig-email-address").val(),
            "esig_access_code": $("#esig-access-code").val(),
            "invite_hash": $("#esig-invite-hash").val(),
            "checksum": $("#esig-document-checksum").val(),
            "sender_name": $("#esig-document-sender_name").val()
        };

        $.post(esigAjax.ajaxurl + "&className=Esig_Slv_Dashboard&method=esig_verify_access_code", data).done(function (response) {

            if (response == "verified") {
                window.location.reload();
            } else if (response == "display") {
                $("#esig-login-form").hide();
                $("#esig-password-set-form").show();
            } else {
                $("#esig-show-error").show().html(response);

            }
        });

        // alert();

    });

    // setting password 
    $("#esig-slv-set-password").click(function () {

        // checking valid access code input 
        if (!esig_validation.is_string($("#esig-slv-password").val())) {
            $("#esig-set-error").show().html("<span class='icon-esig-alert invalid-email'></span><span class='error-msg' id='error-access-code'>Please enter your password</span>");
            return false;
        }

        if (!esig_validation.is_string($("#esig-slv-confirm-password").val())) {
            $("#esig-set-error").show().html("<span class='icon-esig-alert invalid-email'></span><span class='error-msg' id='error-access-code'>Please enter your confirm password</span>");
            return false;
        }

        var data = {
            "esig_slv_password": $("#esig-slv-password").val(),
            "esig_slv_confirm_password": $("#esig-slv-confirm-password").val(),
            "invite_hash": $("#esig-invite-hash").val(),
            "checksum": $("#esig-document-checksum").val()
        };

        // pass to server through Ajax 
        $.post(esigAjax.ajaxurl + "&className=Esig_Slv_Dashboard&method=slv_set_password", data).done(function (response) {

            if (response == "done") {
                window.location.reload();
            }
            else {
                $("#esig-set-error").show().html(response);
                document.getElementById("esig-slv-confirm-password").style.borderColor = "red",
                document.getElementById("esig-slv-password").style.borderColor = "red",
                document.getElementById("access-error-textt").className = 'text-error';
            }
        });

    });

    // password reset popups 
    $("#forget_access_password").click(function () {

        $("#slv-login-form").hide();
        $("#reset-password-popup").show();
        // hide login form 
    });

    // go back button here 
    $("#slv-go-back").click(function () {

        $("#slv-login-form").show();
        $("#reset-password-popup").hide();
        // hide login form 
    });

    // Re-setting password 
    $("#esig-slv-reset-password").click(function () {

        if (!esig_validation.is_email($("#esig-slv-reset-address").val())) {
            document.getElementById("esig-slv-reset-address").style.borderColor = "red";
            document.getElementById("access-error-texttt").className = 'text-error';
            $("#esig-confirm-error").show().html("<span class='icon-esig-alert invalid-email'></span><span class='error-msg' id='error-access-code'>Invalid E-mail address</span>");
            return false;
        }

        var data = {
            "esig_slv_reset_address": $("#esig-slv-reset-address").val(),
            "invite_hash": $("#esig-invite-hash").val(),
            "checksum": $("#esig-document-checksum").val()
        };

        // pass to server through Ajax 
        $.post(esigAjax.ajaxurl + "&className=Esig_Slv_Dashboard&method=slv_reset_password", data).done(function (response) {

            if (response == "done") {
                $("#reset-password-popup").hide();
                $("#slv_reset_confirmation").show();
            }
            else {

                //$("#esig-confirm-error").show().html(response);
                document.getElementById("esig-slv-reset-address").style.borderColor = "red";
                document.getElementById("access-error-texttt").className = 'text-error';
                 $("#esig-confirm-error").show().html("<span class='icon-esig-alert invalid-email'></span><span class='error-msg' id='error-access-code'>E-mail address is not correct</span>");

            }
        });

    });


})(jQuery);
