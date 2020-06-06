(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	 $( document ).ready( function() {
		jQuery('.mwb_wpra_visit_link').click(function () {
		  	var points = $(this).next('.mwb_wpra_visit_link_points').val();
		  	var user_id = $(this).data('userid');
		  	var href = $(this).attr('href');
		  	var data = {
				action:'mwb_wpra_add_point_on_visiting_link',
				mwb_nonce:mwb_wpr_addon.mwb_wpra_nonce,
				user_id:user_id,
				points:points,
				href:href,
			};

			$.ajax(
				{
					url:mwb_wpr_addon.ajaxurl,
					type:'POST',
					data:data,
					success:function(response){
						
					}
				}
			);		  	
		});
	});
})( jQuery );
