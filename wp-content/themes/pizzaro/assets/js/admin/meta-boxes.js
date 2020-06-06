jQuery( function ( $ ) {

	// Tabbed Panels
	$( document.body ).on( 'pz-init-tabbed-panels', function() {
		$( 'ul.pz-tabs' ).show();
		$( 'ul.pz-tabs a' ).click( function( e ) {
			e.preventDefault();
			var panel_wrap = $( this ).closest( 'div.panel-wrap' );
			$( 'ul.pz-tabs li', panel_wrap ).removeClass( 'active' );
			$( this ).parent().addClass( 'active' );
			$( 'div.panel', panel_wrap ).hide();
			$( $( this ).attr( 'href' ) ).show();
		});
		$( 'div.panel-wrap' ).each( function() {
			$( this ).find( 'ul.pz-tabs li' ).eq( 0 ).find( 'a' ).click();
		});
	}).trigger( 'pz-init-tabbed-panels' );

	$( '.show_hide_select' ).on( 'change', function() {
		
		var shortcode_select = $(this).val(), 
			$pizzaro_wc_shortcode = $(this).parents( '.options_group' );

		$pizzaro_wc_shortcode.find( '.hide' ).hide();
		$pizzaro_wc_shortcode.find( '.show_if_' + shortcode_select ).show();
	}).change();

	// Uploading Media
	if ( ! $('.upload_image_id').val() ) {
		$('.pz_remove_image_button').hide();
	}

	$(document).on( 'click', '.pz_upload_image_button', function( event ){
		var current_block = $(this).parent('.form-field').attr('id');

		if( typeof file_frame == 'undefined' ) {
			var file_frame;
		}

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.downloadable_file = wp.media({
			title: 'Choose an image',
			button: {
				text: 'Use image',
			},
			multiple: false
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			attachment = file_frame.state().get('selection').first().toJSON();

			$('#'+current_block+' .upload_image_id').val( attachment.id );
			$('#'+current_block+' img.upload_image_preview').attr('src', attachment.url );
			$('#'+current_block+' .pz_remove_image_button').show();
		});

		// Finally, open the modal.
		file_frame.open();
	});

	$(document).on( 'click', '.pz_remove_image_button', function( event ){
		var current_block = $(this).parent('.form-field').attr('id');

		$('#'+current_block+' img.upload_image_preview').attr('src', $('#'+current_block+' img.upload_image_preview').data('placeholder-src'));
		$('#'+current_block+' .upload_image_id').val('');
		$('#'+current_block+' .pz_remove_image_button').hide();
		return false;
	});
});