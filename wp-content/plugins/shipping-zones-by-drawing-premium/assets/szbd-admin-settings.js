jQuery(document).ready(function($) {
	test_store_address();
	// Helping methods
	function findCommonElements(arr1, arr2) {
		return arr1.some(item => arr2.includes(item));
	}

	function initMap(uluru) {

		jQuery('#szbd_map').height(400);
		var map = new google.maps.Map(
			document.getElementById('szbd_map'), {
				zoom: 18,
				center: uluru
			});

		var marker = new google.maps.Marker({
			position: uluru,
			map: map
		});
	}

	function test_store_address() {
		$('#szbd-test-address').off('click').on('click', function(event, ui) {
			jQuery('.szbd-admin-map').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				'action': 'test_store_address',
			};
			$.post(
				szbd_settings.ajax_url,
				data,
				function(response) {
					var store_address = response.store_address;
					var geocode_storeaddress = new google.maps.Geocoder();
					geocode_storeaddress.geocode({
							'address': store_address.store_address + ',' + store_address.store_postcode + ',' + store_address.store_city + ',' + store_address.store_state + ',' + store_address.store_country
						},
						function(results, status) {
							var ok_types = ["street_address", "subpremise", "premise","establishment","route"];
							if (status === 'OK' && findCommonElements(results[0].types, ok_types)) {
								initMap(results[0].geometry.location);
                               jQuery('#szbd-test-result').html('<div class=""> <br><span class="szbd-heading">STORE ADDRESS OK!</span> <br>' + (results[0].formatted_address) + '</div>');

							} else {
								jQuery('#szbd-test-result').html('<div class=""> <br><span class="szbd-heading-fail">STORE ADDRESS NOT OK!</span> <br>' + JSON.stringify(results) + '</div>');
							}
						});
				}).always(function() {
				jQuery('.szbd-admin-map').unblock();
			});
		});
	}
});
