

(function($){

                    if($('input[name="notify"]').attr('checked')){
				$('#esig-notification-settings').show();
			} 
	// Show or hide the stand alone console when the box is checked.
		$('input[name="notify"]').on('change', function(){
			if($('input[name="notify"]').attr('checked')){
				$('#esig-notification-settings').show();
			} else {
                                 $('#esig_stop_email_after_sign').attr('checked', false); // Unchecks it
				$('#esig-notification-settings').hide();
			}
		});
		
	
	
	
		
})(jQuery);


