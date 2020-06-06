jQuery(document).ready(function($) {
	jQuery(document).on('wc_backbone_modal_loaded', function() {
			$('#woocommerce_szbd-shipping-method_title').parents('tr').hide();
		if ($('#woocommerce_szbd-shipping-method_rate_mode').find('option:selected').attr("value") == 'flat') {
			$('#woocommerce_szbd-shipping-method_rate_fixed').parents('tr').hide();
			$('#woocommerce_szbd-shipping-method_rate_distance').parents('tr').hide();
		}else if($('#woocommerce_szbd-shipping-method_rate_mode').find('option:selected').attr("value") == 'distance'){
			$('#woocommerce_szbd-shipping-method_rate').parents('tr').hide();
			$('#woocommerce_szbd-shipping-method_rate_fixed').parents('tr').hide();

		}else{
			$('#woocommerce_szbd-shipping-method_rate').parents('tr').hide();

		}
		$('#woocommerce_szbd-shipping-method_rate_mode').change(function() {
			if ($(this).find('option:selected').attr("value") == 'flat') {
				$('#woocommerce_szbd-shipping-method_rate_fixed').parents('tr').fadeOut();
			$('#woocommerce_szbd-shipping-method_rate_distance').parents('tr').fadeOut();
			$('#woocommerce_szbd-shipping-method_rate').parents('tr').fadeIn();
			} else if ($(this).find('option:selected').attr("value") == 'distance') {
				$('#woocommerce_szbd-shipping-method_rate').parents('tr').fadeOut();
			$('#woocommerce_szbd-shipping-method_rate_fixed').parents('tr').fadeOut();
			$('#woocommerce_szbd-shipping-method_rate_distance').parents('tr').fadeIn();
			} else {
				$('#woocommerce_szbd-shipping-method_rate').parents('tr').fadeOut();
				$('#woocommerce_szbd-shipping-method_rate_fixed').parents('tr').fadeIn();
			$('#woocommerce_szbd-shipping-method_rate_distance').parents('tr').fadeIn();
			}
		});







		if ($('#woocommerce_szbd-shipping-method_map').find('option:selected').attr("value") !== 'radius') {
			$('#woocommerce_szbd-shipping-method_max_radius').parents('tr').hide();
		}
		if ($('#woocommerce_szbd-shipping-method_map').find('option:selected').attr("value") == 'none') {
			$('#woocommerce_szbd-shipping-method_zone_critical').parents('tr').hide();
		}
		$('#woocommerce_szbd-shipping-method_map').change(function() {
			if ($(this).find('option:selected').attr("value") == 'radius') {
				$('#woocommerce_szbd-shipping-method_zone_critical').parents('tr').fadeIn();
				$('#woocommerce_szbd-shipping-method_max_radius').parents('tr').fadeIn();
			} else if ($(this).find('option:selected').attr("value") == 'none') {
				$('#woocommerce_szbd-shipping-method_max_radius').parents('tr').fadeOut();
				$('#woocommerce_szbd-shipping-method_zone_critical').parents('tr').fadeOut();
			} else {
				$('#woocommerce_szbd-shipping-method_max_radius').parents('tr').fadeOut();
				$('#woocommerce_szbd-shipping-method_zone_critical').parents('tr').fadeIn();
			}
		});
		var test3 = parseFloat($('#woocommerce_szbd-shipping-method_max_driving_distance').val());
		if (test3 === 0 || isNaN(test3)) {
			$('#woocommerce_szbd-shipping-method_distance_critical').parents('tr').fadeOut();
		}
		$('#woocommerce_szbd-shipping-method_max_driving_distance').each(function() {
			var elem2 = $(this);
			elem2.data('oldVal', elem2.val());
			elem2.bind("propertychange change click keyup input paste", function(event) {
				if (elem2.data('oldVal') != elem2.val()) {
					elem2.data('oldVal', elem2.val());
					test3 = parseFloat(elem2.val());
					if (test3 === 0 || isNaN(test3)) {
						$('#woocommerce_szbd-shipping-method_distance_critical').parents('tr').fadeOut();
					} else {
						$('#woocommerce_szbd-shipping-method_distance_critical').parents('tr').fadeIn();
					}
				}
			});
		});
		var test = parseFloat($('#woocommerce_szbd-shipping-method_max_driving_time').val());
		if (test === 0 || isNaN(test)) {
			//$('#woocommerce_szbd-shipping-method_driving_mode').parents('tr').fadeTo(1, 0.01);
			$('#woocommerce_szbd-shipping-method_time_critical').parents('tr').fadeTo(1, 0.01);
		}
		$('#woocommerce_szbd-shipping-method_max_driving_time').each(function() {
			var elem = $(this);
			elem.data('oldVal', elem.val());
			elem.bind("propertychange change click keyup input paste", function(event) {
				if (elem.data('oldVal') != elem.val()) {
					elem.data('oldVal', elem.val());
					test = parseFloat(elem.val());
					if (test === 0 || isNaN(test)) {
					//	$('#woocommerce_szbd-shipping-method_driving_mode').parents('tr').fadeTo("slow", 0.01);
						$('#woocommerce_szbd-shipping-method_time_critical').parents('tr').fadeTo("slow", 0.01);
					} else {
					//	$('#woocommerce_szbd-shipping-method_driving_mode').parents('tr').fadeTo("slow", 1);
						$('#woocommerce_szbd-shipping-method_time_critical').parents('tr').fadeTo("slow", 1);
					}
				}
			});
		});
	});
});
