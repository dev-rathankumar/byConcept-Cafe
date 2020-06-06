/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

(function ($) {

    $("#esig-expired-email-send").click(function (e) {
        e.preventDefault();

        var toEmail = $("#esig_admin_email").val();
        var signerEmail = $("#esig_signer_email").val();
        var signerMessage = $("#esig_admin_message").val();
        var signerName = $("#esig_signer_name").val();
        
        if (esign.isEmpty(signerName)) {
            $("#esig_signer_name").addClass("esig-invalid");
            return;
        }
        if (!esign.is_valid_email(toEmail)) {
            $("#esig_admin_email").addClass("esig-invalid");
            return;
        }

        if (!esign.is_valid_email(signerEmail)) {
            $("#esig_signer_email").addClass("esig-invalid");
            return;
        }
        if (esign.isEmpty(signerMessage)) {
            $("#esig_admin_message").addClass("esig-invalid");
            return;
        }
        
       
        jQuery.post(esigAjaxData.ajaxurl, {action: "esig_send_expired_notification",esig_admin_email:toEmail,esig_signer_email:signerEmail,esig_signer_message:signerMessage,esig_signer_name:signerName,esig_nonce:esigAjaxData.esigNonce}, function (data) {
             
              $('#esigModal').modal('hide');
            
        }, "html");

    });

})(jQuery);
