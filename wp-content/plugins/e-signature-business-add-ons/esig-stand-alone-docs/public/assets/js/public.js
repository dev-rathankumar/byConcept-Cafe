(function ( $ ) {
	"use strict";

	$(function () {
		
		
		// Hide audit trail for public document page
		try{
			if(esigSad && esigSad.is_unsigned == '1'){
				$('.audit-wrapper').hide();
			}			
		} catch (err){
			console.log(err);
		}

		
	});

}(jQuery));