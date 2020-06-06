jQuery(document).ready(function($){
	$('.submit-new-preset').click(function(){
		var form = $('#redux-form-wrapper');
		jQuery.ajax(
            {
                type: "post",
                dataType: "json",
                url: ajaxurl,
                data: {
                    action: "tbay_framework_new_preset",
                    nonce: form.data('nonce'),
                    new_preset: $('.new_preset').val()
                },
                error: function( response ) {
                    location.reload();
                },
                success: function( response ) {
                    location.reload();
                }
            }
        );
	});

	$('.submit-preset').click(function(){
		var form = $('#redux-form-wrapper');
		jQuery.ajax(
            {
                type: "post",
                dataType: "json",
                url: ajaxurl,
                data: {
                    action: "tbay_framework_set_default_preset",
                    nonce: form.data('nonce'),
                    default_preset: $('.set_default_preset').val()
                },
                error: function( response ) {
                    location.reload();
                },
                success: function( response ) {
                    location.reload();
                }
            }
        );
	});
    $('.submit-duplicate-preset').click(function(){
        var form = $('#redux-form-wrapper');
        var title = prompt("Please enter preset name", "");
        if ( title ) {
            jQuery.ajax(
                {
                    type: "post",
                    dataType: "json",
                    url: ajaxurl,
                    data: {
                        action: "tbay_framework_duplicate_preset",
                        nonce: form.data('nonce'),
                        default_preset: $('.set_default_preset').val(),
                        title: title
                    },
                    error: function( response ) {
                        location.reload();
                    },
                    success: function( response ) {
                        location.reload();
                    }
                }
            );
        }
    });
    $('.submit-delete-preset').click(function(){
        var form = $('#redux-form-wrapper');
        jQuery.ajax(
            {
                type: "post",
                dataType: "json",
                url: ajaxurl,
                data: {
                    action: "tbay_framework_delete_preset",
                    nonce: form.data('nonce'),
                    default_preset: $('.set_default_preset').val()
                },
                error: function( response ) {
                    location.reload();
                },
                success: function( response ) {
                    location.reload();
                }
            }
        );
    });
	$('.set_default_preset').change(function(){
		$('.preset_des .key').text( $(this).val() );
	});
    $('.preset_des .key').text( $('.set_default_preset').val() );
});