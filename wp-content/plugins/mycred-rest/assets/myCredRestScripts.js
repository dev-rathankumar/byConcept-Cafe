jQuery('document').ready(function(){

    jQuery('#mycred-generate-api-key').click(function(){

        var key_in_hex = MCRA_CREDENTIALS.user + MCRA_CREDENTIALS.pass;
        var access_key = window.btoa(key_in_hex);
        jQuery('#generalmycredrestapiapikey').val(access_key);

    });

}); 