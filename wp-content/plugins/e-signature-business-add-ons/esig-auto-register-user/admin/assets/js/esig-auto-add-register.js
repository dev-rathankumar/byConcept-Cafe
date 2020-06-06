(function ($) {

    
    $('input[name="esig_auto_add_register"]').on('change', function ()
    {
        if ($('input[name="esig_auto_add_register"]').attr('checked'))
        {
            $('#esig_auto_register_setting').show();
        }
        else
        {
            $('#esig_auto_register_setting').hide();
        }

    });

    /***
     *  The dynamic checkbox that hides/activates the select field was not added
     */
    $('#auto-register-signer-wp-user-show').on('click', function ()
    {
            $('#auto-register-sender-permission').show(); 
            $(this).hide();
            $('#auto-register-signer-wp-user-hide').show();
    });
    $('#auto-register-signer-wp-user-hide').on('click', function ()
    {
            $('#auto-register-sender-permission').hide();
            $(this).hide();
            $('#auto-register-signer-wp-user-show').show();
    });


//
 $('input[name="esig_misc_content"]').on('change', function () 
				{
					if ($('input[name="esig_misc_content"]').attr('checked')) 
					{
						 $('#misc_content_role').show(); 
					}
					else 
					{
						$('#misc_content_role').hide();
					}
                                        
			  
				});


})(jQuery);
