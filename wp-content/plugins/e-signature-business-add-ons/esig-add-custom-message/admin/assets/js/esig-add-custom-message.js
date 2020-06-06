

(function($){

    
	// Show or hide the stand alone console when the box is checked.
    $('input[name="esig_custom_message"]').on('change', function () {
        if ($('input[name="esig_custom_message"]').attr('checked')) {
            
            $('#esig-custom-message-input').show();
	}
        else {
            
            $('#esig-custom-message-input textarea').val('');
            $('#esig-custom-message-input').hide();
            
             }
        });


    if ($('input[name="esig_custom_message"]').attr('checked')) {
        $('#esig-custom-message-input').show();
    } else {
        $('#esig-custom-message-input').hide();
    }
	
    /************* Confirmation custom message settings  **************/		
    $('input[name="confirmation_custom_message"]').on('change', function () {
        if ($('input[name="confirmation_custom_message"]').attr('checked')) {
            
            $('#confirmation_custom_message_text').show();
	}
        else {
            
            $('#confirmation_custom_message_text textarea').val('');
            $('#confirmation_custom_message_text').hide();
            
             }
        });


    if ($('input[name="confirmation_custom_message"]').attr('checked')) {
        $('#confirmation_custom_message_text').show();
    } else {
        $('#confirmation_custom_message_text').hide();
    }
	
		
})(jQuery);
