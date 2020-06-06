jQuery(document).ready(function($){
    var source = '';
    var title = '';
    
    if ( $('.tbay-btn-import').data('disabled') ) {
        $(this).attr('disabled', 'disabled');
    }
    
    $('.tbay-btn-import').click(function(event){
        // all
        source = $(event.currentTarget).children("input").val();
        title = $(event.currentTarget).prev().text();
        version = $(event.currentTarget).prev().prev().text();
        if ( confirm('Import all data of "'+ version +'" skin and set "' + title + '" as default') ) {
            
            tbay_open_popup();  
            
            $('.tbay_progress_import_2').hide();
            $('.tbay_progress_import_1').show();

            $(this).attr('disabled', 'disabled');
            $('.tbay-progress-content').show();
            
            $('.first_settings span').hide();
            $('.first_settings .installing').show();
            $('.steps li').removeClass('active');
            $('.first_settings').addClass('active');

            tbay_import_type('first_settings');
        }
    });     

    $('.tbay-btn-config').click(function(event){
        // all
        source = $(event.currentTarget).children("input").val();
        title = $(event.currentTarget).prev().prev().text();
        version = $(event.currentTarget).prev().prev().prev().text();
        if ( confirm('Only active "'+ version +' skin and set  "' + title + '" as default (not import all data)') ) {
            
			tbay_open_popup();	
			
            $('.tbay-complete1').hide();
            $('.tbay_progress_import_1').hide();
            $('.tbay_progress_import_2').show();

            $(this).attr('disabled', 'disabled');
            $('.tbay-progress-content').show();
            
            $('.first_settings span').hide();
            $('.first_settings .installing').show();
            $('.steps2 li').removeClass('active');
            $('.first_settings').addClass('active');

            tbay_import_type2('first_settings2');
        }
    }); 
	
	$('.tbay-close-import-popup').click(function(event){
        tbay_close_popup();
    });

    function tbay_import_type( type ) {
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'tbay_import_sample',
                demo_source: source,
                ajax: 1, 
                import_type: type
            },
            dataType: 'json',
            success: function (res) {
                var next = res.next;

                if ( res.status == false ) {
                    tbay_import_error( res );
                    return false;
                }

                if ( next == 'done' ) {
                    tbay_import_complete( type );
                    $('.tbay-close-import-popup').show();
                    return false;
                }
                
                if ( next == 'error' ) {
                    tbay_import_error( res );
                    $('.tbay-close-import-popup').show();
                    return false;
                }

                tbay_import_complete_step( type, res );
                tbay_import_type( next );
                
            },
            error: function (html) {
                $('.tbay_progress_error_message .tbay-error .content').append('<p>' + html + '</p>');
                $('.tbay_progress_error_message').show();
                return false;
            }
        });

        return false;
    }

    function tbay_import_type2( type ) {
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'tbay_import_sample',
                demo_source: source,
                ajax: 1, 
                import_type: type
            },
            dataType: 'json',
            success: function (res) {
                var next = res.next;

                if ( res.status == false ) {
                    tbay_import_error( res );
                    return false;
                }

                if ( next == 'done' ) {
                    tbay_import_complete2( type );
					$('.tbay-close-import-popup').show();
                    return false;
                }
                
                if ( next == 'error' ) {
                    tbay_import_error( res );
					$('.tbay-close-import-popup').show();
                    return false;
                }

                tbay_import_complete_step2( type, res );
                tbay_import_type2( next );
				
            },
            error: function (html) {
                $('.tbay_progress_error_message .tbay-error .content').append('<p>' + html + '</p>');
                $('.tbay_progress_error_message').show();
                return false;
            }
        });

        return false;
    }

    function tbay_import_complete_step(type, res) {
        $( '.' + type + ' span' ).hide();
        $( '.' + type + ' .installed' ).show();

        var next = res.next;
        if ( next == 'done' ) {
            $('.tbay-complete1').show();
            $('.steps li').removeClass('active');
            $('.tbay-btn-import').removeAttr('disabled');
        } else {
            $('.' + next + ' span').hide();
            $('.' + next + ' .installing').show();
            $('.steps li').removeClass('active');
            $('.' + next).addClass('active');
        }
    }

    function tbay_import_complete_step2(type, res) {
        $( '.' + type + ' span' ).hide();
        $( '.' + type + ' .installed' ).show();

        var next = res.next;
        if ( next == 'done' ) {
            $('.tbay-complete2').show();
            $('.steps li').removeClass('active');
            $('.tbay-btn-import').removeAttr('disabled');
        } else {
            $('.' + next + ' span').hide();
            $('.' + next + ' .installing').show();
            $('.steps li').removeClass('active');
            $('.' + next).addClass('active');
        }
    }

    function tbay_import_complete(type) {
        $( '.' + type + ' span' ).hide();
        $( '.' + type + ' .installed' ).show();
        $('.tbay-complete1').show();
        $('.tbay-btn-import').removeAttr('disabled');
    }    

    function tbay_import_complete2(type) {
        $( '.' + type + ' span' ).hide();
        $( '.' + type + ' .installed' ).show();
        $('.tbay-complete2').show();
        $('.tbay-btn-import').removeAttr('disabled');
        location.reload();
    }

    function tbay_import_error(res) {
        if ( res.msg !== undefined && res.msg != '' ) {
            $('.tbay_progress_error_message .tbay-error .content').append('<p>' + res.msg + '</p>');
            $('.tbay_progress_error_message').show();
        }
    }
	
	function tbay_open_popup() {
        var $body = $('body');
        $body.addClass('open-popup'); 
        $body.append('<div id="TB_overlay" class="TB_overlayBG"></div>');

		$('.tbay-popup').show(); 
        $('.tbay-popup').animate({
            'top': '50%'
        }, 1000);
    }	
	
	function tbay_close_popup() {
        var $body = $('body');
        $body.removeClass('open-popup'); 
        $('#TB_overlay').remove();

        $('.tbay-popup').hide();
    }

});


