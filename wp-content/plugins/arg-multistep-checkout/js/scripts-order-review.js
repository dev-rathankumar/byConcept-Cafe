(function($){
    
    "use strict";
    
    var orderReviewStepHasTable = $('.argmc-order-review-step').find('.woocommerce-checkout-review-order-table').length;
    var paymentHasCustomerReview = $('.argmc-payment-step, .argmc-order-payment-step').find('.argmc-customer-review').length;
    
    $('.argmc-wrapper').on('argmcBeforeStepChange', function(event, currentStep, nextStep) {
        
        var argWizardSteps = $('.argmc-form-steps');
        if (argWizardSteps.eq(nextStep-1).hasClass('argmc-order-review-step')) { 
             
            //if is the default woo order review table, not the cloned one   
            if (!orderReviewStepHasTable) {
                
                //arg multistep order table
                var argmcOrderTable = $('.woocommerce-checkout-review-order-table').first().clone();
                
                $(argmcOrderTable).removeClass('woocommerce-checkout-review-order-table').addClass('review-table');  
                
                //find selected shipping method and add it to the order review table
                var argmcShippingMethod = '';
                if ($(argmcOrderTable).find('tr.shipping').length) { 
                    if ($(argmcOrderTable).find("#shipping_method").length) {
						if ($(argmcOrderTable).find("#shipping_method li").length == 1) {
							argmcShippingMethod = $(argmcOrderTable).find('.shipping_method').siblings('label').text();
						} else {
							argmcShippingMethod = $(argmcOrderTable).find('.shipping_method:checked').siblings('label').text();
						}
                    } else {
                        argmcShippingMethod = $(argmcOrderTable).find('tr.shipping td').text();
                    }
                    
                    $(argmcOrderTable).find('tr.shipping td').empty().text(argmcShippingMethod);
                }
				
				//find remove coupon link and remove it
				if ($(argmcOrderTable).find('a.woocommerce-remove-coupon').length) {
					$(argmcOrderTable).find('a.woocommerce-remove-coupon').remove();
				}
				
    
                $('.argmc-review-order-wrapper').html(argmcOrderTable);            
                
            }
                 
			setCustomerDetailsReview();
        
        }
        
        if (paymentHasCustomerReview) { 
            if (argWizardSteps.eq(nextStep-1).hasClass('argmc-payment-step') || argWizardSteps.eq(nextStep-1).hasClass('argmc-order-payment-step')) {
                setCustomerDetailsReview();
            }
        }
    });

	
	function setCustomerDetailsReview() {
	
		//Set billing details object
		var argmcBillingDetails = {
			firstName: $("#billing_first_name").length ? $("#billing_first_name").val() : '',
			lastName: $("#billing_last_name").length ? $("#billing_last_name").val() : '',
			company : $("#billing_company").length ? $("#billing_company").val() : '',
			email: $("#billing_email").length ? $("#billing_email").val() : '',
			phone: $("#billing_phone").length ? $("#billing_phone").val() : '',
			country: $("#billing_country").length ? $("#billing_country option:selected").text() : '',
            state: $("#billing_state").length ? ($("#billing_state").is("select") ? $("#billing_state option:selected").text() : $("#billing_state").val()) : '',
			city: $("#billing_city").length ? $("#billing_city").val() : '',
			firstAddress: $("#billing_address_1").length ? $("#billing_address_1").val() : '',
			secondAddress: $("#billing_address_2").length ? $("#billing_address_2").val() : '',
			zipcode: $("#billing_postcode").length ? $("#billing_postcode").val() : ''
		};
		
		var argmcBillingAddress = '';
		
		//First Name & Last Name
		if ((argmcBillingDetails.firstName != '') || (argmcBillingDetails.lastName != '')) {
			argmcBillingAddress = argmcBillingDetails.firstName + ' ' + argmcBillingDetails.lastName + '<br>';
		}
			
		// Company	 
		if (argmcBillingDetails.company != '') {
			argmcBillingAddress += argmcBillingDetails.company + '<br>';
		}

		// First Address
		if (argmcBillingDetails.firstAddress != '') {
			argmcBillingAddress += argmcBillingDetails.firstAddress + '<br>';
		}
		
		// Second Address
		if (argmcBillingDetails.secondAddress != '') {
			argmcBillingAddress += argmcBillingDetails.secondAddress + '<br>';
		}
		
		// City
		if (argmcBillingDetails.city != '') {
			argmcBillingAddress += argmcBillingDetails.city + ', ';
		}
		
		// State
		if (argmcBillingDetails.state != '') {
			argmcBillingAddress += argmcBillingDetails.state + ' ';
		}
		
		// Zipcode
		if (argmcBillingDetails.zipcode != '') {
			argmcBillingAddress += argmcBillingDetails.zipcode;
		}
		
		// Country
		if (argmcBillingDetails.country != '') {
			argmcBillingAddress += '<br>' + argmcBillingDetails.country;
		}
		
		
		//Billing Address						  
		$('.argmc-billing-address').html(argmcBillingAddress);
		
		//Email
		$('.argmc-customer-email').html(argmcBillingDetails.email);
		
		//Phone
		$('.argmc-customer-phone').html(argmcBillingDetails.phone);
		
		//If Email is Unset
		if (!$("#billing_email").length) {
			$('.argmc-customer-details li').first().remove();
		}
		
		//If Phone is Unset
		if (!$("#billing_phone").length) {
			$('.argmc-customer-details li').last().remove();
		}
		
		//If Email and Phone are Unset
		if (!$("#billing_email").length && !$("#billing_phone").length) {
			$('.argmc-customer-details').remove();
		}
		
		//Set shipping details object
		if ($("#ship-to-different-address-checkbox").is(":checked")) {
			
			//shipping details
			var argmcSpippingDetails = {
				firstName: $("#shipping_first_name").length ? $("#shipping_first_name").val() : '',
				lastName: $("#shipping_last_name").length ? $("#shipping_last_name").val() : '',
				company : $("#shipping_company").length ? $("#shipping_company").val() : '',
				country: $("#shipping_country").length ? $("#shipping_country option:selected").text() : '',
				state: $("#shipping_state").length ? ($("#shipping_state").is("select") ? $("#shipping_state option:selected").text() : $("#shipping_state").val()) : '',
				city: $("#shipping_city").length ? $("#shipping_city").val() : '',
				firstAddress: $("#shipping_address_1").length ? $("#shipping_address_1").val() : '',
				secondAddress: $("#shipping_address_2").length ? $("#shipping_address_2").val() : '',
				zipcode: $("#shipping_postcode").length ? $("#shipping_postcode").val() : ''
			};			
			
			var argmcShippingAddress = '';
					
			//First Name & Last Name
			if ((argmcSpippingDetails.firstName != '') || (argmcSpippingDetails.lastName != '')) {
				argmcShippingAddress = argmcSpippingDetails.firstName + ' ' + argmcSpippingDetails.lastName + '<br>';
			}
				
			// Company	 
			if (argmcSpippingDetails.company != '') {
				argmcShippingAddress += argmcSpippingDetails.company + '<br>';
			}
	
			// First Address
			if (argmcSpippingDetails.firstAddress != '') {
				argmcShippingAddress += argmcSpippingDetails.firstAddress + '<br>';
			}
			
			// Second Address
			if (argmcSpippingDetails.secondAddress != '') {
				argmcShippingAddress += argmcSpippingDetails.secondAddress + '<br>';
			}
			
			// City
			if (argmcSpippingDetails.city != '') {
				argmcShippingAddress += argmcSpippingDetails.city + ', ';
			}
			
			// State
			if (argmcSpippingDetails.state != '') {
				argmcShippingAddress += argmcSpippingDetails.state + ' ';
			}
			
			// Zipcode
			if (argmcSpippingDetails.zipcode != '') {
				argmcShippingAddress += argmcSpippingDetails.zipcode;
			}
			
			// Country
			if (argmcSpippingDetails.country != '') {
				argmcShippingAddress += '<br>' + argmcSpippingDetails.country;
			}
			
				
			//Shipping Address						  
			$('.argmc-shipping-address').html(argmcShippingAddress);			
                   
		} else {
			//Shipping Address
			$(".argmc-shipping-address").html(argmcBillingAddress);
		}
	}
	
})(jQuery);


