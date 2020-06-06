(function($) {

	var Decimals      = parseInt( myCREDPartial.decimals );
	var Exchange      = parseFloat( myCREDPartial.rate );
	var MinPayment    = 0;
	var MaxPayment    = 0;
	var CartTotal     = 0;
	var Template      = myCREDPartial.format;
	var OurUpdate     = false;
	var CouponRemoval = 'no';
	var AmountToPay   = 0;

	var parseBaseValues = function() {

		if ( Decimals > 0 ) {
			MinPayment = parseFloat( myCREDPartial.min ).toFixed(2);
			MaxPayment = parseFloat( myCREDPartial.max ).toFixed(2);
		}
		else {
			MinPayment = parseInt( myCREDPartial.min );
			MaxPayment = parseInt( myCREDPartial.max );
		}

		CartTotal = parseFloat( myCREDPartial.total ).toFixed(2);

		return true;

	};

	$(document).ready(function() {

		parseBaseValues();

		$( 'form.checkout' ).on( 'focusout', '#mycred-range-selector input', function(){

			var selectedAmount = parseFloat( $( this ).val() );
			var maxVal = parseFloat( $( this ).attr( "max" ) );
			if ( selectedAmount > maxVal ) {
				$( this ).val( maxVal );
				$( this ).change();
			}

		});

		$( 'form.checkout' ).on( 'change', '#mycred-range-selector input', function(){

			var selectedamount = $(this).val();

			if ( Decimals > 0 )
				selectedamount = parseFloat( selectedamount ).toFixed(2);
			else
				selectedamount = parseInt( selectedamount );

			AmountToPay = selectedamount;

			if ( selectedamount == 0 )
				$( 'button#mycred-apply-partial-payment' ).attr( 'disabled', 'disabled' );
			else
				$( 'button#mycred-apply-partial-payment' ).removeAttr( 'disabled' );

				$( '#mycred-partial-payment-total h2 span' ).text( selectedamount );
				
				function formatNumber (num) {
					return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
				}
				var newdiscount      = selectedamount * Exchange;
					newdiscount 	 = newdiscount.toFixed(2);
					newdiscount 	 = formatNumber(newdiscount);
				var DiscountFormat   = myCREDPartial.format;
				var DiscountTemplate = DiscountFormat.replace( /COST/i, newdiscount );
				
				$( '#mycred-partial-payment-total p span.amount' ).text( DiscountTemplate );

		});

		var reloadingpartial = false;
		$( 'form.checkout' ).on( 'click', '#mycred-apply-partial-payment', function(e){

			console.log( 'Attempting new partial payment' );

			e.preventDefault();

			$( '.woocommerce-error, .woocommerce-message' ).remove();

			$.ajax({
				type       : "POST",
				data       : {
					action : 'mycred-new-partial-payment',
					token  : myCREDPartial.token,
					amount : AmountToPay
				},
				dataType   : "JSON",
				url        : myCREDPartial.ajaxurl,
				beforeSend : function() {

					$( '#mycred-apply-partial-payment' ).attr( 'disabled', 'disabled' );

				},
				success    : function( response ) {

					if ( response.success === undefined || response.success === false ) {

						alert( response.data );

					}

				},
				complete : function() {

					OurUpdate = true;
					$( document.body ).trigger( 'update_checkout', { update_shipping_method: false } );

				}
			});

		});

		$(document).bind( 'click', '.woocommerce-remove-coupon', function(){

			OurUpdate = true;

		});

		/**
		 * Whenever WooCommerce decides to update the order total
		 * we need to update the partial payment form as well to ensure we can still use it.
		 */
		$( document.body ).bind( 'update_checkout', function(){

			if ( ! OurUpdate ) return false;

			OurUpdate = false;

			console.log( 'Reloading partial payment form' );

			$.ajax({
				type     : "POST",
				data     : {
					action : 'mycred-partial-payment-reload',
					token  : myCREDPartial.reload
				},
				cache    : false,
				dataType : "HTML",
				url      : myCREDPartial.ajaxurl,
				beforeSend : function() {

					$( '#mycred-apply-partial-payment' ).attr( 'disabled', 'disabled' );

				},
				success  : function( response ) {

					// console.log( response );
					$( '#mycred-partial-payment-woo' ).replaceWith( response );

				},
				complete : function() {

					$( '#mycred-apply-partial-payment' ).removeAttr( 'disabled' );

				}
			});

		});

	});

})(jQuery);