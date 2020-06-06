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

    //Validate input
    $('.validate-required :input').addClass('validate[required]');

    //Validate email
	$('.validate-email').each(function() {
		if ($(this).hasClass('validate-required')) { 
			$(this).find('input').removeClass('validate[required]').addClass('validate[required,custom[email]]');
		} else {
			$(this).find('input').addClass('validate[custom[email]]');
		}
	});
	

    //Billing and shipping forms

    //Validate postcode
    if ($('#billing_postcode').parent().hasClass('validate-required')) {
        $('#billing_postcode').removeClass('validate[required]')
                              .addClass('validate[required,custom[postcode]')
                              .data('fieldset-key', 'billing');
    } else {
        $('#billing_postcode').addClass('validate[custom[postcode]')
                              .data('fieldset-key', 'billing');	
    }


    if ($('#shipping_postcode').parent().hasClass('validate-required')) {						  
        $('#shipping_postcode').removeClass('validate[required]')
                               .addClass('validate[required,custom[postcode]')
                               .data('fieldset-key', 'shipping');
    } else {
        $('#shipping_postcode').addClass('validate[custom[postcode]')
                               .data('fieldset-key', 'shipping');	
    }

    //Validate phone
    if ($('#billing_phone').parent().hasClass('validate-required')) {
            $('#billing_phone').removeClass('validate[required]')
                               .addClass('validate[required,custom[phone]')
                               .data('fieldset-key', 'billing');
    } else {
            $('#billing_phone').addClass('validate[custom[phone]')
                               .data('fieldset-key', 'billing');					   			   
    }
	
    //Validate state
    $('.argmc-wrapper').on('change', '#billing_country', function() {
		
        if (!$(this).parent().hasClass('woocommerce-validated') && !$(this).hasClass('validate[required]')) {

            $(this).addClass('validate[required]')
                   .siblings('select2-container')
                   .addClass('validate[required]');
        }
		
		if ($(this).parent().hasClass('woocommerce-validated')) { 
			$(this).removeClass('validate[required]')
                   .siblings('select2-container')
                   .removeClass('validate[required]');
				   
			//Remove country error on country change
			$('#billing_country').parent().removeClass('has-error');
			$('#billing_country').validationEngine('hide');
			$('#billing_country').siblings('.error').remove();
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
		
		if ($(this).parent().hasClass('woocommerce-validated')) { 
			$(this).removeClass('validate[required]')
                   .siblings('select2-container')
                   .removeClass('validate[required]');
				   
			//Remove country error on country change
			$('#shipping_country').parent().removeClass('has-error');
			$('#shipping_country').validationEngine('hide');
			$('#shipping_country').siblings('.error').remove();
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
	
	window.navigateTo = function(index) {	
    //function navigateTo(index) {
            
        var atTheEnd = index >= sections.length - 1;
        
		
        //It's always better to know the previous step
        tabs.removeClass('previous').filter('.current').addClass('previous');
        sections.removeClass('previous').filter('.current').addClass('previous');


        //Don't animate the first section twice
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
		
		
		
		//Scroll Top Position - Page top(default) or Wizard Top 
		var scrollTopDesktops = 0;
		var scrollTopMobiles = 0;
		var scrollLeftMobiles = 0;
		var wizardTopPos = $('.argmc-wrapper').offset();
        
		if ($('.argmc-wrapper').data('scrolltopdesktops') > 0) {
			scrollTopDesktops = wizardTopPos.top - $('.argmc-wrapper').data('scrolltopdesktops');
			
			if ( $(window).scrollTop() < scrollTopDesktops ) {
				scrollTopDesktops = $(window).scrollTop();
				animationTopTiming = 0;
				animationSwitchSectionsDelay = 0;
			}
		}
		
		if ($('.argmc-wrapper').data('scrolltopmobiles') > 0) {
			scrollTopMobiles = wizardTopPos.top - $('.argmc-wrapper').data('scrolltopmobiles');
			scrollLeftMobiles = wizardTopPos.left;
		}
		 
        
        var prevStep = curIndex()+1;
        var nextStep = index+1;
        
        //Event triggered before step changes
        $('.argmc-wrapper').trigger('argmcBeforeStepChange', [prevStep, nextStep]);
		
            //Mobile Devices Animation - Because jQuery scrollTop not working on Mobile Devices		
            if (navigator.userAgent.match(/(iPod|iPhone|iPad|Android)/)) {           

                window.scrollTo(scrollLeftMobiles,scrollTopMobiles); //first value for left offset, second value for top offset
				
				//Remove from argmc-wrapper the prev step class
				if ($('.argmc-form-steps.current').attr('data-step')) {
					var prevStep = $('.argmc-form-steps.current').data('step');
					$('.argmc-wrapper').removeClass(prevStep);
				}
				
                //Mark the current section with the class 'current'
                sections
                    .removeClass('current')
                    .eq(index)
                    .addClass('current');

                tabs
                    .removeClass('current')
                    .eq(index)
                    .addClass('current visited');
					
				//Add to argmc-wrapper the next step class
				if ($('.argmc-form-steps.current').attr('data-step')) {
					var nextStep = $('.argmc-form-steps.current').data('step'); 
					$('.argmc-wrapper').addClass(nextStep);
				}

                //Show only the navigation buttons that make sense for the current section:
				if (index > 0) {
					$('.argmc-nav .argmc-previous').removeClass('hide-button').addClass('show-button');
				} else {
					$('.argmc-nav .argmc-previous').removeClass('show-button').addClass('hide-button');
				}
				
				if ((!atTheEnd) && !$('.argmc-form-steps.current').hasClass('argmc-login-step')) {
					$('.argmc-nav #argmc-next').removeClass('hide-button').addClass('show-button');
				} else { 
					$('.argmc-nav #argmc-next').removeClass('show-button').addClass('hide-button');
				}
				
				if ($('.argmc-form-steps.current').hasClass('argmc-login-step')) {
					$('.argmc-nav #argmc-skip-login').removeClass('hide-button').addClass('show-button');
				} else {
					$('.argmc-nav #argmc-skip-login').removeClass('show-button').addClass('hide-button');
				}
				
				if(atTheEnd) {                                
					$('.argmc-nav .argmc-submit').removeClass('hide-button').addClass('show-button');
				}else {
					$('.argmc-nav .argmc-submit').removeClass('show-button').addClass('hide-button');
				}
				

                //Event triggered after step changes
                $('.argmc-wrapper').trigger('argmcAfterStepChange', [prevStep, nextStep]);
				
				$('.argmc-nav-buttons .button').blur();
				
				//last visisted step
				tabs.removeClass('last').filter('.visited').last().addClass('last');

            //end mobile devices animation
            } else {

                //Desktop Animation
                $('html, body').animate({scrollTop: scrollTopDesktops },
                   animationTopTiming, function() {

                        setTimeout(function() {

                            //due to the html/body animation(triggered twice for mozzila/chrome) execute the below code only once
                            if (!tabs.eq(index).hasClass('current')) {
								
								//Remove from argmc-wrapper the prev step class
								if ($('.argmc-form-steps.current').attr('data-step')) {
									var prevStep = $('.argmc-form-steps.current').data('step');
									$('.argmc-wrapper').removeClass(prevStep);
								}
									
									
                                //Mark the current section with the class 'current'
                                sections
                                    .removeClass('current')
                                    .eq(index)
                                    .addClass('current');

                                tabs
                                    .removeClass('current')
                                    .eq(index)
                                    .addClass('current visited');
									
									
								//Add to argmc-wrapper the next step class
								if ($('.argmc-form-steps.current').attr('data-step')) {
									var nextStep = $('.argmc-form-steps.current').data('step'); 
									$('.argmc-wrapper').addClass(nextStep);
								}
								
									
                                //Show only the navigation buttons that make sense for the current section:
								
								if (index > 0) {
									$('.argmc-nav .argmc-previous').removeClass('hide-button').addClass('show-button');
								} else {
									$('.argmc-nav .argmc-previous').removeClass('show-button').addClass('hide-button');
								}
								
								if ((!atTheEnd) && !$('.argmc-form-steps.current').hasClass('argmc-login-step')) {
									$('.argmc-nav #argmc-next').removeClass('hide-button').addClass('show-button');
								} else { 
									$('.argmc-nav #argmc-next').removeClass('show-button').addClass('hide-button');
								}
								
								if ($('.argmc-form-steps.current').hasClass('argmc-login-step')) {
									$('.argmc-nav #argmc-skip-login').removeClass('hide-button').addClass('show-button');
								} else {
									$('.argmc-nav #argmc-skip-login').removeClass('show-button').addClass('hide-button');
								}
								
								if(atTheEnd) {                                
									$('.argmc-nav .argmc-submit').removeClass('hide-button').addClass('show-button');
								}else {
									$('.argmc-nav .argmc-submit').removeClass('show-button').addClass('hide-button');
								}								

                                //Event triggered after step changes
                                $('.argmc-wrapper').trigger('argmcAfterStepChange', [prevStep, nextStep]);
								
								$('.argmc-nav-buttons .button').blur();
								
								//last visisted step
								tabs.removeClass('last').filter('.visited').last().addClass('last');
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
	
        //remove validation on select2 if there is value for the select fields
		$('.argmc-form-steps.current select').each(function() {
			if ($(this).val() !='') {
				$(this).parent().find('.select2-container').removeClass('validate[required]');
			}
		})
		
		//remove validation on select2 if is validated by woocommerce
        $('.woocommerce-validated .select2-container').removeClass('validate[required]');
		
        //Add rule to validate terms and conditions checkbox - check again due to the woo order review on the fly update 
        $('#terms').removeClass('validate[required]').addClass('validate[required]');
        
        //if current step doesn't need to be validated navigate to next step
        if ($('.argmc-form-steps.current').hasClass('argmc-skip-validation')) {
		    
			//Login Step - Force User Login Message 
		    if ($('.argmc-form-steps.current').hasClass('argmc-login-step') && $('.argmc-form-steps.current').attr('data-forcelogin')) {
				
				if ($('.argmc-login-step').find('.force-login-error').length == 0) {
					$('.argmc-login-step').prepend('<ul class="woocommerce-error force-login-error"><li>' + $('.argmc-form-steps.current').data('forceloginmessage') + '</li></ul>')
				}
				
				  var forceLoginErrorTopPos = $('.force-login-error').offset();
				  
				  if ($(window).width() < 1024) {
					window.scrollTo(forceLoginErrorTopPos.left,forceLoginErrorTopPos.top - 100 ); //first value for left offset, second value for top offset
				  } else {
					$('html, body').animate({scrollTop: forceLoginErrorTopPos.top - 160 },300);
				  }
				
			} else {
		 
				if (!tabs.eq(curIndex()).hasClass('completed')) {
					tabs.eq(curIndex()).addClass('completed');
				}
				navigateTo(thatStep);
				
			} 

        } else {
			
            if (!$('.argmc-wrapper').hasClass('select2Loaded')) {
                $('.argmc-wrapper').addClass('select2Loaded');
            }

            //if current step has inputs fields
            if ($('.argmc-form-steps.current').find(':input').length > 0) {
                
                //validate input fields on current step
                if ($('.argmc-form-steps.current').find(':input').parents('form:not('+ coupon_form_class +')').first().validationEngine('validate') === true) {
               
                    if (!tabs.eq(curIndex()).hasClass('completed')) {
                        tabs.eq(curIndex()).addClass('completed');
                    }
                    $('.argmc-wrapper').removeClass("step-has-errors");
                    navigateTo(thatStep);
                } else {
                    $('.argmc-wrapper').addClass("step-has-errors");
                }

            } else {
                if (!tabs.eq(curIndex()).hasClass('completed')) {
                    tabs.eq(curIndex()).addClass('completed');
                }
                navigateTo(thatStep);
            }
        }
        	
    }
       
        
    //Next button goes forward if current block validates
    $('.argmc-nav .argmc-next, .argmc-skip-step').on('click', function() {
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
        
        //Add rule to validate terms and conditions checkbox - check again due to the woo order review on the fly update 
        $('#terms').removeClass('validate[required]').addClass('validate[required]'); 
        
        if ($('.checkout').validationEngine('validate') === true) {
            $('.argmc-wrapper').removeClass("step-has-errors");
            if ($("#place_order").length) {
				$("#place_order").trigger("click"); 
			} else {
				$('.woocommerce-checkout').submit(); 
			}
        } else {
            $('.argmc-wrapper').addClass("step-has-errors");
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

	

	
	//Set a default coupon class	
	var coupon_form_class = '.outside_the_checkout';

	$(document).ready( function() {
		

		//Validate login form
		$('.argmc-wrapper #username').addClass('validate[required]');
		$('.argmc-wrapper #password').addClass('validate[required]');
		
		
		$('.argmc-wrapper').on('submit', '.login', function(e) {
			e.preventDefault();
	
			var form = $(this);	
			
			var data = form.serializeArray();			
			data.push({name: 'action', value: 'arg_mc_login'}, {name: 'rememberme', value: $('#rememberme').is(':checked')}, {name: 'security', value: argmcJsVars.loginNonce});
			
			$.ajax({
				url: argmcJsVars.ajaxURL,
				dataType: 'json',
				type: 'POST',
				data: data
			}) 
			.done(function(data) {
				if (data['success'] === true) {
					location.reload();
				} else {
					if ($('.argmc-login').find('.woocommerce-error').length > 0) {
						$('.argmc-login').find('.woocommerce-error li').html(data.error);
					} else {	
						//form.find('.form-row').first().before('<ul class="woocommerce-error login-errors"><li>' + data['error'] + '</li></ul>');
						if ($('.register-visible').length) { 
							$('.argmc-login').find('.login-headings').after('<ul class="woocommerce-error login-form-errors"><li>' + data['error'] + '</li></ul>');
						} else {
							$('.argmc-login').prepend('<ul class="woocommerce-error login-form-errors"><li>' + data['error'] + '</li></ul>');
						}
					}
					
					var loginErrorsTopPos = $('.login-form-errors').offset();
				  
					if ($(window).width() < 1024) {
					  window.scrollTo(loginErrorsTopPos.left,loginErrorsTopPos.top - 100 ); //first value for left offset, second value for top offset
					} else {
					  $('html, body').animate({scrollTop: loginErrorsTopPos.top - 130 },300);
					}
					
				}
			});
		});
	
		
		//Validate register form
		$('.argmc-wrapper #reg_username').addClass('validate[required]');	
		$('.argmc-wrapper #reg_email').addClass('validate[required,custom[email]]');
		$('.argmc-wrapper #reg_password').addClass('validate[required]');
			
		$('.argmc-wrapper').on('submit', '.register', function(e) {
			e.preventDefault();
			
			var form = $(this);
	
			var data = form.serializeArray();			
			data.push({name: 'action', value: 'arg_mc_register'}, {name: 'security', value: argmcJsVars.registerNonce});
			
			$.ajax({
				url: argmcJsVars.ajaxURL,
				dataType: 'json',
				type: 'POST',
				data: data
			}) 
			.done(function(data) {
				if (data['success'] === true) {
					location.reload();
				} else {
					if ($('.argmc-register').find('.woocommerce-error').length > 0) {
						$('.argmc-register').find('.woocommerce-error li').html(data.error);
					} else {	
						//form.find('.form-row').first().before('<ul class="woocommerce-error register-errors"><li>' + data['error'] + '</li></ul>');
						
						if ($('.register-visible').length) { 
							$('.argmc-register').find('.login-headings').after('<ul class="woocommerce-error register-form-errors"><li>' + data['error'] + '</li></ul>');
						} else {
							$('.argmc-register').prepend('<ul class="woocommerce-error register-form-errors"><li>' + data['error'] + '</li></ul>');
						}
					}
					
					var registerErrorsTopPos = $('.register-form-errors').offset();
				  
					if ($(window).width() < 1024) {
					  window.scrollTo(registerErrorsTopPos.left,registerErrorsTopPos.top - 100 ); //first value for left offset, second value for top offset
					} else {
					  $('html, body').animate({scrollTop: registerErrorsTopPos.top - 130 },300);
					}
				}
			});
		});	
		
		//Move coupon form to another step
		if ( $('.coupon-placeholder form').length > 0 ) {
			
			var moved_coupon_wrapper = $('.coupon-placeholder .coupon-wrapper');
			var couponPosition = $('.argmc-wrapper').data('coupon-position');
			
			if (couponPosition != 'default') {
				
				//The coupon is moved in the checkout form and we want to avoid validating the step
				coupon_form_class = '.'+$('.coupon-placeholder form').attr('class').split(' ')[0];
			
				switch (couponPosition) {
					case 'before-billing-section':
						if ($('.woocommerce-billing-fields').length) {
							moved_coupon_wrapper.addClass('before-billing').insertBefore($('.woocommerce-billing-fields'));
						}				
						break;
					
					case 'after-shipping-section':
						if ($('.woocommerce-shipping-fields').length) {
							moved_coupon_wrapper.addClass('after-shipping').insertAfter($('.woocommerce-shipping-fields'));
						}							
						break;
					
					case 'after-additional-fields':
						if ($('.woocommerce-additional-fields').length) {
							moved_coupon_wrapper.addClass('after-additional').insertAfter($('.woocommerce-additional-fields'));
						}				
						break;
					
					case 'before-order-review-table':
						if ($('.woocommerce-checkout-review-order-table').length) {
							moved_coupon_wrapper.addClass('before-order').insertBefore($('.woocommerce-checkout-review-order-table'));
						}				
						break;
						
					case 'after-order-review-table':
						if ($('.woocommerce-checkout-review-order-table').length) {
							moved_coupon_wrapper.addClass('after-order').insertAfter($('.woocommerce-checkout-review-order-table'));
						}
						break;
						
					case 'before-payment':
						if ($('#payment').length) {
							moved_coupon_wrapper.addClass('before-payment').insertBefore($('#payment'));
						}
						break;
					case 'after-payment':
						if ($('#payment').length) {
							moved_coupon_wrapper.addClass('after-payment').insertAfter($('#payment'));
						}
						break;		
				}
			}
		} //end if coupon placeholder
		
		
		//Remove browser validation
		
		if ($('.argmc-register').length) {
			$('.argmc-register').find('form').attr('novalidate', 'novalidate');
		}
		
		//Show Register on Login Step
		
		$('.argmc-login-tabs').on('click', '.tab-item', function() {
			var that = $(this);
			
			if (that.hasClass('current')) {
				return
			} 
			
			that.addClass('current').siblings().removeClass('current');
			
			if (that.data("target")=="argmc-login") {
				$('.argmc-register').fadeOut(150,function(){
					$('.argmc-login').fadeIn(150);
				});
			}
			
			if (that.data("target")=="argmc-register") {
					$('.argmc-login').fadeOut(150,function(){
					$('.argmc-register').fadeIn(150);
				});
			}
		
		})//end show register on login step
		
		
		//Fix Tabs Progress Bars on Mobiles (need to be fixed the tabs class)
		
		function fixProgressBarLayout() {
			if ( $('.argmc-wrapper').hasClass('tabs-progress-bar') && $(window).width() < 768 ) {
				var firstTab = $('.argmc-tabs-list li').first();
				var lastTab = $('.argmc-tabs-list li').last();
				
				if (firstTab.find('.argmc-tab-item-outer').width() > firstTab.find('.argmc-tab-text').width() + 16 ) {
					firstTab.addClass('text-centered'); 
				} else {
					firstTab.removeClass('text-centered'); 
				}
				
				if (lastTab.find('.argmc-tab-item-outer').width() > lastTab.find('.argmc-tab-text').width() + 16 ) {
					lastTab.addClass('text-centered'); 
				} else {
					lastTab.removeClass('text-centered'); 
				}
			}
		}
		
		fixProgressBarLayout()
		
		
		//Change Place Order Text on Payment Methods Change
		
		function placeOrderText() {
			
			var placeOrderCurrentText;
			
			if ($('#place_order').is('button')) {
				placeOrderCurrentText = $('#place_order').text();
			} else {
				placeOrderCurrentText = $('#place_order').val();
			}
			
			$('#argmc-submit span').text(placeOrderCurrentText);
		}
		
		setTimeout(function(){
			placeOrderText();
		},300); 
		
		$(".argmc-wrapper #order_review").on('click', 'li', function() { 
			setTimeout(function(){ 
				placeOrderText();
			},100)
		});
		
		
		$(window).resize(function() {
			fixProgressBarLayout()
		});
		
	}); //end dom Ready
	
})(jQuery);