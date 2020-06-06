(function($){
    
    "use strict";

    //Validate Woocommerce Checkout Forms

    //Overwrite Validation Engine Defaults
    $.validationEngine.defaults.validationEventTrigger = "blur";
    $.validationEngine.defaults.scroll = true;
    $.validationEngine.defaults.focusFirstField = true;
    $.validationEngine.defaults.showPrompts = false;
    $.validationEngine.defaults.scrollOffset = 200;

    //Overwrite Validation Engine Prompts
    $('.argmc-wrapper form').bind('jqv.field.result', function (event, field, errorFound, prompText) {
        var formGroup = field.parents('.form-row').first();
        if (errorFound) {
            formGroup.addClass('has-error');
            $("label.error", formGroup).last().remove();
            formGroup.append('<label class="error">' + prompText + '</label>');
        }
        else {
            formGroup.removeClass("has-error");
            $("label.error", formGroup).last().remove();
        }
    });
	
    //Validate forms
    $('.argmc-wrapper').find('form').validationEngine({
        maxErrorsPerField : 1,
	prettySelect: true,
	usePrefix: 's2id_'
    });       
	
    //Validate login form
    $('.argmc-wrapper #username').addClass('validate[required]');
    $('.argmc-wrapper #password').addClass('validate[required]');
    
	
    $('.argmc-wrapper').on('submit', '.login', function(e) {
        e.preventDefault();

        $.ajax({
            url: argmcJsVars.ajaxURL,
            dataType: 'json',
            type: 'POST',
            data: {
                action: 'login',
                username: $('#username').val(),
                password: $('#password').val(),
                rememberme: $('#rememberme').is(':checked'),
                security: argmcJsVars.loginNonce                  
            }
        }) 
        .done(function(data) {
            if (data['success'] === true) {
                location.reload();
            } else {
                if ($('.login-errors').length > 0) {
                    $('.login-errors').html(data['error']);
                } else {	
                    $('.login .form-row-first').before('<ul class="woocommerce-error login-errors"><li>' + data['error'] + '</li></ul>');
                }
            }
        });
    });


    //Validate input
    $('.validate-required :input').addClass('validate[required]');

    //Validate email
    if ($('input[type="email"]').parent().hasClass('validate-required')) {
        $('.validate-required :input[type="email"]').removeClass('validate[required]').addClass('validate[required,custom[email]]');
    } else {
        $('.validate-required :input[type="email"]').addClass('validate[custom[email]]');
    }

    //Billing and shipping forms

    //Validate postcode
    if ($('#billing_postcode').parent().hasClass('validate-required')) {
        $('#billing_postcode').removeClass('validate[required]')
                              .addClass('validate[required,funcCall[checkPostcode]')
                              .data('fieldset-key', 'billing');
    } else {
        $('#billing_postcode').addClass('validate[funcCall[checkPostcode]')
                              .data('fieldset-key', 'billing');	
    }


    if ($('#shipping_postcode').parent().hasClass('validate-required')) {						  
        $('#shipping_postcode').removeClass('validate[required]')
                               .addClass('validate[required,funcCall[checkPostcode]')
                               .data('fieldset-key', 'shipping');
    } else {
        $('#shipping_postcode').addClass('validate[funcCall[checkPostcode]')
                               .data('fieldset-key', 'shipping');	
    }

    //Validate phone
    if ($('#billing_phone').parent().hasClass('validate-required')) {
            $('#billing_phone').removeClass('validate[required]')
                               .addClass('validate[required,funcCall[validatePhone]')
                               .data('fieldset-key', 'billing');
    } else {
            $('#billing_phone').addClass('validate[funcCall[validatePhone]')
                               .data('fieldset-key', 'billing');					   			   
    }
	
    //Validate state
    $('.argmc-wrapper').on('change', '#billing_country', function() {
		
        if (!$(this).parent().hasClass('woocommerce-validated') && !$(this).hasClass('validate[required]')) {

            $(this).addClass('validate[required]')
                   .siblings('select2-container')
                   .addClass('validate[required]');
        }

        setTimeout(function() {
            if ($('#billing_state').parent().hasClass('validate-required')) {
                if (!$('#billing_state').hasClass('validate[required]')) {

                $('#billing_state').addClass('validate[required]')
                                   .siblings('select2-container')
                                   .addClass('validate[required]');
                }
            } else {
                $('#billing_state').removeClass('validate[required]')
                                   .siblings('select2-container')
                                   .removeClass('validate[required]');


                //Remove state error on country change				   
                $('#billing_state').validationEngine('hide');
                $('#billing_state').next('.error').remove();
            }

        }, 500);
    });
		
    $('.argmc-wrapper').on('change', '#shipping_country', function() {
		
        if (!$(this).parent().hasClass('woocommerce-validated') && !$(this).hasClass('validate[required]')) {

            $(this).addClass('validate[required]')
                       .siblings('select2-container')
                       .addClass('validate[required]');
        }

        setTimeout(function() {
            if ($('#shipping_state').parent().hasClass('validate-required')) {
                if (!$('#shipping_state').hasClass('validate[required]')) {

                    $('#shipping_state').addClass('validate[required]')
                                        .siblings('select2-container').addClass('validate[required]');
                }
            } else {
                $('#shipping_state').removeClass('validate[required]')
                                    .siblings('select2-container').removeClass('validate[required]');
            }

            //Remove state error on country change
            $('#shipping_state').validationEngine('hide');
            $('#shipping_state').next('.error').remove();			
        }, 500);
		
    });
	
	
    $('.argmc-wrapper').on('change', '#billing_state', function() {

        if ($('#billing_state').parent().hasClass('validate-required')) {

            if($('#billing_state').val() == '' ) {

                if (!$('#billing_state').hasClass('validate[required]')) {

                    $('#billing_state').addClass('validate[required]')
                                       .siblings('select2-container')
                                       .addClass('validate[required]');
                }
            } else {
                $('#billing_state').removeClass('validate[required]')
                                   .siblings('select2-container')
                                   .removeClass('validate[required]');


                //Remove state error on country change
                $('#billing_state').parent().removeClass('has-error');
                $('#billing_state').validationEngine('hide');
                $('#billing_state').next('.error').remove();
            }
        }

    });


    $('.argmc-wrapper').on('change', '#shipping_state', function() {

        if ($('#shipping_state').parent().hasClass('validate-required')) {

            if($('#shipping_state').val() == '' ) {

                if (!$('#shipping_state').hasClass('validate[required]')) {

                    $('#shipping_state').addClass('validate[required]')
                                        .siblings('select2-container')
                                        .addClass('validate[required]');
                }
            } else {
                $('#shipping_state').removeClass('validate[required]')
                                    .siblings('select2-container')
                                    .removeClass('validate[required]');


                //Remove state error on country change
                $('#shipping_state').parent().removeClass('has-error');
                $('#shipping_state').validationEngine('hide');
                $('#shipping_state').next('.error').remove();
            }
        }	
    });
	

    //Navigation	
    var sections    = $('.argmc-form-steps'),
        tabs        = $('.argmc-tab-item');
		
    function navigateTo(index) {
            
        var atTheEnd = index >= sections.length - 1;
        
		
        //it's always better to know the previous step
        tabs.removeClass('previous').filter('.current').addClass('previous');
        sections.removeClass('previous').filter('.current').addClass('previous');


        //don't animate the first section twice
        if (index == 0 && !sections.eq(0).hasClass('animate')) {
            sections.eq(0).addClass('animate');
        }
        
		
        //Navigate to next step after scrollTop is 0
        var animationTopTiming = 0,
            animationSwitchSectionsDelay = 0;
        
        if ($(window).scrollTop() > 10) {
            animationTopTiming = 300;
            animationSwitchSectionsDelay = 100;
        } 
        
        var prevStep = curIndex()+1;
        var nextStep = index+1;
        
        var wizardTopPos = $('.argmc-wrapper').offset();
        
        //Event triggered before step changes
        $('.argmc-wrapper').trigger('argmcBeforeStepChange', [prevStep, nextStep]);
		
            //Mobile Devices Animation - Because jQuery scrollTop not working on Mobile Devices		
            if (navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/)) {           

                window.scrollTo(wizardTopPos.left,wizardTopPos.top - 30 ); //first value for left offset, second value for top offset

                //Mark the current section with the class 'current'
                sections
                    .removeClass('current')
                    .eq(index)
                    .addClass('current');

                tabs
                    .removeClass('current')
                    .eq(index)
                    .addClass('current visited');


                //Show only the navigation buttons that make sense for the current section:
                $('.argmc-nav .argmc-previous').toggle(index > 0);
                $('.argmc-nav #argmc-next').toggle((!atTheEnd) && !$('.argmc-form-steps.current').hasClass('argmc-login-step'));
                $('.argmc-nav #argmc-skip-login').toggle($('.argmc-form-steps.current').hasClass('argmc-login-step'));
                $('.argmc-nav .argmc-submit').toggle(atTheEnd);

                //Event triggered after step changes
                $('.argmc-wrapper').trigger('argmcAfterStepChange', [prevStep, nextStep]);
				
				$('.argmc-nav-buttons .button').blur();

            //end mobile devices animation
            } else {

                //Desktop Animation
                $('html, body').animate({scrollTop: wizardTopPos.top - 60 },
                   animationTopTiming, function() {

                        setTimeout(function() {

                            //due to the html/body animation(triggered twice for mozzila/chrome) execute the below code only once
                            if (!tabs.eq(index).hasClass('current')) {

                                //Mark the current section with the class 'current'
                                sections
                                    .removeClass('current')
                                    .eq(index)
                                    .addClass('current');

                                tabs
                                    .removeClass('current')
                                    .eq(index)
                                    .addClass('current visited');


                                //Show only the navigation buttons that make sense for the current section:
                                $('.argmc-nav .argmc-previous').toggle(index > 0);
                                $('.argmc-nav #argmc-next').toggle((!atTheEnd) && !$('.argmc-form-steps.current').hasClass('argmc-login-step'));
                                $('.argmc-nav #argmc-skip-login').toggle($('.argmc-form-steps.current').hasClass('argmc-login-step'));
                                $('.argmc-nav .argmc-submit').toggle(atTheEnd);

                                //Event triggered after step changes
                                $('.argmc-wrapper').trigger('argmcAfterStepChange', [prevStep, nextStep]);
								
								$('.argmc-nav-buttons .button').blur();
                            }

                        }, animationSwitchSectionsDelay);
                });
                //end animate
            }
            //end desktop animation
    }
    //end navigateTo
	
    //Return the current index by looking at which section has the class 'current'	
    function curIndex() {
        return sections.index(sections.filter('.current'));
    }
    
	
    //Previous button is easy, just go back
    $('.argmc-nav .argmc-previous').on('click', function() {
        navigateTo(curIndex() - 1);        
    });
	
	
    //Validate current step and navigate to that step
    function validateStepAndNavigateTo(thatStep) {
	
        //remove validation on select2 if is validated by woocommerce
        $('.woocommerce-validated .select2-container').removeClass('validate[required]');
        
        //if current step doesn't need to be validated navigate to next step
        if ($('.argmc-form-steps.current').hasClass('argmc-skip-validation')) {
		
            if (!tabs.eq(curIndex()).hasClass('completed')) {
                tabs.eq(curIndex()).addClass('completed');
            }
            navigateTo(thatStep);

        } else {
			
            if (!$('.argmc-wrapper').hasClass('select2Loaded')) {
                $('.argmc-wrapper').addClass('select2Loaded');
            }

            //if current step has inputs fields
            if ($('.argmc-form-steps.current').find(':input').length > 0) {

                //validate input fields on current step
                if ($('.argmc-form-steps.current').find(':input').parents('form').first().validationEngine('validate') === true) {

                    if (!tabs.eq(curIndex()).hasClass('completed')) {
                        tabs.eq(curIndex()).addClass('completed');
                    }

                    navigateTo(thatStep);
                }

            } else {
                if (!tabs.eq(curIndex()).hasClass('completed')) {
                    tabs.eq(curIndex()).addClass('completed');
                }
                navigateTo(thatStep);
            }
        }
        
        //Add rule to validate terms and conditions checkbox
        $('#terms').removeClass('validate[required]').addClass('validate[required]');    	
    }
       
        
    //Next button goes forward if current block validates
    $('.argmc-nav .argmc-next').on('click', function() {
        validateStepAndNavigateTo(curIndex() + 1);
    });
    
    
    //Navigate to next/visited step by clicking on tabs
    tabs.on('click', function() {
        
        var thatTab = $(this),
            thatIndex = thatTab.index();
        
        if (thatTab.hasClass('current')) {
            return;
        }
		
		//Navigate between visited steps
        if (thatTab.hasClass('visited')) {
			
            if (curIndex() < thatIndex) {

                //Navigate Forward - Validate current step and navigate to that step
                validateStepAndNavigateTo(thatIndex);

            } else {

                //Navigate backward to that step
                navigateTo(thatIndex);
            }
			
        }

        //Validate current step and navigate to next step
        if (thatTab.prev().hasClass('current') && !thatTab.hasClass('visited')) {
            validateStepAndNavigateTo(thatIndex);
        }
    });

    
    //Custom login checkbox - used for styling the "remember me" checkbox
    var loginCheckbox =  $('.woocommerce-checkout .woocommerce .argmc-wrapper form.login label[for="rememberme"] input[type="checkbox"]');
    
    if (loginCheckbox.is(':checked')) {
        loginCheckbox.parent().addClass('checked');
    } else {
        loginCheckbox.parent().removeClass('checked');
    }  
    
    loginCheckbox.on('change', function () {
        
        var that = $(this); 
        
        if (that.is(':checked')) {
            that.parent().addClass('checked');
        } else {
            that.parent().removeClass('checked');
        }  
    });	
	
	
    //Custom ship to different address checkbox - used for styling the "ship to different address" checkbox
    if ($('#ship-to-different-address-checkbox').length) {
        if ($('#ship-to-different-address-checkbox').prev().hasClass('checkbox')) {
            $('#ship-to-different-address-checkbox').insertBefore($('#ship-to-different-address-checkbox').prev());
        }
    }
	
	
    //Place order
    $('#argmc-submit').on('click', function() {
        if ($('.checkout').validationEngine('validate') === true) {
            $("#place_order").trigger("click");
        }
    });

    
    //Disable form submit on enter
    $('.argmc-wrapper .checkout').on('keypress', function(e) {

        if (e.which === 13) { 
            e.preventDefault();
            return false;
        }
        
        return true;
    });
    
})(jQuery);

//Validate postcode
function checkPostcode(field, rules, i, options) {
    data = jQuery.ajax({
        url: argmcJsVars.ajaxURL,
        async: false,
        dataType: 'json',
        type: 'POST',
        data: {
            action: 'validate_fields',
            rule: 'postcode',
            fieldset_key: jQuery(field).data('fieldset-key'),
            country: jQuery('#' + jQuery(field).data('fieldset-key') + '_country').val(),
            postcode: jQuery(field).val()                   
        }				

    });

    data = jQuery.parseJSON(data.responseText);

    if (data.success === false) {
        return data.error;
    }

    return true;
}

//Validate phone
function validatePhone(field, rules, i, options) { 
    data = jQuery.ajax({
        url: argmcJsVars.ajaxURL,
        async: false,
        dataType: 'json',
        type: 'POST',
        data: {
            action: 'validate_fields',
            rule: 'phone',
            fieldset_key: jQuery(field).data('fieldset-key'),
            phone: jQuery(field).val()                  
        }			
    });

    data = jQuery.parseJSON(data.responseText);

    if (data.success === false) {
        return data.error;
    }

    return true;
}