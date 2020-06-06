(function ($) {

    
    // this is common js file . 
    $('.esig-pro-pack h3 a,.esig-pro-pack .esig-dismiss').on('click', function (e) {
        e.preventDefault();
        $('.esig-pro-pack').toggleClass('open');
        $('.esig-pro-pack p').slideToggle('fast');
        $('.esig-pro-pack h3 span').fadeToggle('fast');
        $('.esig-dismiss').slideToggle('fast');
    });

    $('.esig-add-on-actions .esig-add-on-enabled a').hover(function () {
        $(this).text($(this).attr('data-text-disable'));
    }, function () {

        $(this).text($(this).attr('data-text-enabled'));

    });

    $('.esig-add-on-actions .esig-add-on-disabled a').hover(function () {
        $(this).text($(this).attr('data-text-enable'));
    }, function () {
        $(this).text($(this).attr('data-text-disabled'));
    });

    //progress bar start here 
    $("#esig-install-alladdons").click(function () {

        var overlay = $('<div class="page-loader-overlay"></div>').appendTo('body');
        $(overlay).show();

        $(".esig-addon-devbox").show();

        $.fx.interval = 3000;

        $(".progress").animate({ width: "100%" }, {
            duration: 90000,
            step: function (now, fx) {
                if (fx.prop == 'width') {
                    var countup = Math.round((now / 100) * 100) + '%';
                    $(".countup").html(countup);
                }
            },

            start: function () { $(this).before("<div class='load'><p>Installing...</p></div>"); },

            complete: function () { $(this).after("<div class='logo'></div>"); },

            done: function () { $("div.load").html("<p>Successfully Installed</p>"); }

        });

    });
	
	
	// delete confirmation 
	  $(".esig-add-on-delete a").click(function () {
		  
		  var add_url =  $(this).data('url');
		  
		  var data_name =  $(this).data('name');
		  
		  $('#esig-addon-name').html(data_name);
		  
		  $('#esig-addon-agree').html(data_name);
		  
		  $( "#esig-addon-dialog-confirm" ).dialog({
			  dialogClass: 'esig-dialog',
			  height:300,
			  width:300,
			  modal: true,
			  buttons:[ {
				  text:"YES, DELETE FOREVER",
				  "ID": 'esig-primary-dgr-btn',
				  click: function() {
					 
					  var error='You must agree with the statements above to delete this add-on forever';
					  if(!$('#esig-addon-agree-one').attr('checked'))
					  {
						  	$('#esig-addon-error').slideDown( "slow" );
						  	$('#esig-addon-error').html(error);
						     return false;
					  }
					  if(!$('#esig-addon-agree-two').attr('checked'))
					  {
						  $('#esig-addon-error').slideDown( "slow" );
						  $('#esig-addon-error').html(error);
						     return false;
					  }
					  if(!$('#esig-addon-agree-three').attr('checked'))
					  {
						  $('#esig-addon-error').slideDown( "slow" );
						  $('#esig-addon-error').html(error);
						     return false;
					  }
					  $('#esig-addon-error').hide();
					  window.location =add_url ; 
					  return true ; 
				  }
				},
				{
				 text:"CANCEL, KEEP MY ADD-ON",
				 "id":"esig-secondary-btn",
				 click: function() {
				  $( this ).dialog( "close" );
				  return false ;
					}
				}]
			  
			});
		  return false ;
	  });
	  // hiding tooltip when cancel delete 
	  $("body").on("click","#esig-secondary-btn",function() {
		    $(".ui-tooltip-content").parents('div').hide();
	  });
	  
	  // tooltip
	  $(".esig-add-ons-wrapper").tooltip({ position: {
	        my: "right-110 bottom-10",
	        at: "right center",
	        using: function (position, feedback) {
	            $(this).css(position);
	            $("<div>")
	            .addClass("esign-arrow")
	            .addClass(feedback.vertical)
	            .addClass(feedback.horizontal)
	            .appendTo(this);
	        } 
	    }
	});

})(jQuery);
