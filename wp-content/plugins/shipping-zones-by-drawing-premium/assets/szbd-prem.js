jQuery(document).ready(function($) {
	var min_array = [];
	var min_message = [];
	var error_type = [];
	var the_response;
	init_updated();

	function init_updated() {
		jQuery('body').off('updated_checkout.my2').on('updated_checkout.my2', function() {

			update();
			jQuery('.szbd_message').remove();
		});
	}

	function end(address_string) {


		if (jQuery('#shipping_method li[szbd="true"]').size() === 0 && jQuery('#shipping_method li').not('#shipping_method li[szbd="true"]').not('#shipping_method li[szbd="false"]').size() === 0) {

			if (error_type.length !== 0) {
				jQuery('#shipping_method').append('<span class="szbd_message">' + error_type[0] + '</>');
			} else {
				jQuery('#shipping_method').append('<span class="szbd_message">' + szbd.checkout_string_1 + '</>');
			}
			jQuery('#place_order').prop('disabled', true);
		} else {
			jQuery('#place_order').prop('disabled', false);
		}
		// To develop in future versions
		/*
		var selected_method = jQuery('input[type=radio]:checked', '#shipping_method').val();
		 if(selected_method.includes('szbd'))
		 {
			jQuery('.woocommerce-shipping-totals.shipping').after('<tr><th>' + 'Shipping Address' + '</th><td>'+ address_string + '</td></tr>');
		 }*/
	}

	function set_min(min) {
		min_message.push(szbd.checkout_string_2 + ' ' + min[0]);
		min_array.push(min[1]);
	}

	function indexOfMax(arr) {
		if (arr.length === 0) {
			return -1;
		}
		var max = arr[0];
		var maxIndex = 0;
		for (var i = 1; i > arr.length; i++) {
			if (arr[i] < max) {
				maxIndex = i;
				max = arr[i];
			}
		}
		return maxIndex;
	}

	function update() {
		jQuery('.szbd-debug').remove();
		//jQuery('#shipping_method').fadeOut();
		jQuery('table.woocommerce-checkout-review-order-table').addClass('processing').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			'action': 'check_address_2',
		};
		$.post(
			woocommerce_params.ajax_url,
			data,
			function(response) {
				the_response = response;
				if ((the_response.status === true) && !(the_response.szbd_zones === null || the_response.szbd_zones === undefined || the_response.szbd_zones.length == 0)) {
				var ok_types = [];
					var country = $('#billing_country').val();
					 var streetnumber ='';
                    if ($('#billing_number').val()) {
                        streetnumber = $('input#billing_number').val();
                    }
					var state = '';
					var state_text = '';
					if ($('#billing_state').val()) {
						state = $('#billing_state').val();
						state_text = $("#billing_state option:selected").text();
					}
					var postcode = $('input#billing_postcode').val();
					var s_company = $('input#billing_company').val() + ',';
					var city = $('#billing_city').val();
					var address = $('input#billing_address_1').val();
					var address_2 = $('input#billing_address_2').val();
					var s_country = country;
					var s_streetnumber = streetnumber;
					var s_state = state;
					var s_state_text = state_text;
					var s_postcode = postcode;
					var s_city = city;
					var s_address = address;
					var s_address_2 = address_2;
					if ($('#ship-to-different-address').find('input').is(':checked')) {
						s_country = $('#shipping_country').val();
						if ($('#shipping_state').val()) {
							s_state = $('#shipping_state').val();
							s_state_text = $("#shipping_state option:selected").text();
						}
						 if ($('#shipping_number').val()) {
                        s_streetnumber = $('input#shipping_number').val();
                    }
						s_postcode = $('input#shipping_postcode').val();
						s_company = $('input#shipping_company').val() + ',';
						s_city = $('#shipping_city').val();
						s_address = $('input#shipping_address_1').val();
						s_address_2 = $('input#shipping_address_2').val();
					}
					var comp;

				var postcode_ = s_postcode !== undefined ? s_postcode.replace(" ", ""):'';
					if (s_country == 'IL') {
						s_address = s_address + ',' + s_address_2 + ',' + s_city + ' ' + s_postcode;
						comp = {
							country: s_country,
							administrativeArea: s_city,
							locality: s_city,
						};

					} else if (s_country == 'CA') {
						s_address = s_address + ',' + s_address_2 + ',' + s_city + ' ' + s_postcode + ',' + s_state;
						comp = {
							country: s_country,
							administrativeArea: s_state
						};
						if(s_state === undefined){
								delete comp.administrativeArea;
							}
					} else if (s_country == 'RO') {
						s_address = s_address + ',' + s_address_2 + ',' + s_city + ' ' + s_postcode + ',' + s_state;
						comp = {
							country: s_country,
							administrativeArea: s_state,


						};
						if(s_state === undefined){
								delete comp.administrativeArea;
							}
					}else if (s_country == 'RU') {


						s_address = s_address + ',' + s_address_2 + ',' + s_city + ',' + s_state;
						comp = {
							country: s_country,
							administrativeArea: s_state,
							locality: s_city,
						};

							if(s_state === undefined){
								delete comp.administrativeArea;
							}
					}else if (s_country == 'AO') {


						s_address = s_address + ',' + s_address_2 + ',' + s_city + ',' + s_state;
						comp = {
							country: s_country,
							administrativeArea: s_state,
							locality: s_city,
						};

							if(s_state === undefined){
								delete comp.administrativeArea;
							}

					}
					else if (s_country == 'ES' ) {


						s_address = s_address + ',' + s_address_2 + ',' + s_city + ',' + s_state;
						comp = {
							country: s_country,

							locality: s_city,
							postalCode: postcode_,
						};




					}
					else if (s_country == 'IE' ) {


							s_address = s_address + ',' + s_address_2 + ',' + s_city + ' ' + postcode_ ;
						comp = {
							country: s_country,


							postalCode: postcode_,
						};

				ok_types = ["street_address", "subpremise", "premise",  "postal_code", "establishment"];

					} else if (s_country == 'BR' ) {

                                s_address = s_address + ' ' + s_streetnumber + ', ' + postcode_ + ',' + s_city + ' , ' + s_state_text;
                        comp = {

                            country: s_country,
                            locality: s_city,
                        };

						}  else if (s_country == 'PL' ) {

                         s_address = s_address + ',' + s_address_2 + ',' + s_city + ' ' + s_country;
                        comp = {
                            locality: s_city,

                            country: s_country
                        };

                        } else {

						s_address = s_address + ',' + s_address_2 + ',' + s_city + ' ' + postcode_ + ',' + s_state;
						comp = {
							postalCode: postcode_,

							country: s_country
						};


							if(s_postcode === undefined){
								delete comp.postalCode;
							}

					}
					if(szbd.deactivate_postcode == 1){
								delete comp.postalCode;
							}
					if (the_response.delivery_address !== false) {
						do_geolocation(the_response.delivery_address, 'OK', 'OK', true);

					} else {
						// Geocode the address
						var geocoder = new google.maps.Geocoder();
						geocoder.geocode({
							'address': /* s_company + */ s_address,
							'componentRestrictions': comp
						}, function(results, status) {
							if (szbd.debug == 1) {

								jQuery('.woocommerce-notices-wrapper:first-child').html('<div class="woocommerce-info szbd-debug"><h4>GEOCODE:</h4><br>' + JSON.stringify(results) + '</div>');
							}
							do_geolocation(results, status, google.maps.GeocoderStatus.OK, false, ok_types);
						});
					}
				} else {
					jQuery('table.woocommerce-checkout-review-order-table').removeClass('processing').unblock();
					jQuery('#shipping_method').fadeIn();
				}
			}).then(function() {

		});
	}

	function do_geolocation(results, status, ok_status, has_address, ok_types) {

		var delivery_address;
		var drive_time_car_Promise;
		var drive_time_bike_Promise;
		var drive_dist_Promise;
		var bicycle_dist_Promise;
		var radius_Promise;
		var latitude;
		var longitude;
		if (has_address === false) {
			 ok_types = (!Array.isArray(ok_types) || !ok_types.length) ? ["street_address", "subpremise", "premise", "establishment", "route"] : ok_types;
			if (status === ok_status && findCommonElements(results[0].types, ok_types)) {
				latitude = results[0].geometry.location.lat();
				longitude = results[0].geometry.location.lng();
				delivery_address = results[0].geometry.location;
			} else {
				latitude = null;
				longitude = null;
				delivery_address = null;
			}
		} else {
			delivery_address = results;
			latitude = results.lat;
			longitude = results.lng;
		}
		if (the_response.store_address !== false) {
			radius_Promise = the_response.do_radius && delivery_address !== null ? calcRadius(delivery_address, the_response.store_address, true) : false;
		} else {
			radius_Promise = the_response.do_radius && delivery_address !== null ? calcRadius(delivery_address, szbd.store_address, false) : false;
		}
		if (the_response.delivery_duration_driving !== false || the_response.distance_driving !== false) {
			drive_time_car_Promise = drive_dist_Promise = [the_response.delivery_duration_driving, the_response.distance_driving];
		} else {
			drive_time_car_Promise = drive_dist_Promise = (the_response.do_driving_time_car || the_response.do_driving_dist  || the_response.do_dynamic_rate_car) && delivery_address !== null ? calcRoute(latitude, longitude, szbd.store_address, 'DRIVING') : false;
		}
		if (the_response.delivery_duration_bicycle !== false || the_response.distance_bicycle !== false) {
			drive_time_bike_Promise = bicycle_dist_Promise = [the_response.delivery_duration_bicycle, the_response.distance_bicycle];
		} else {
			drive_time_bike_Promise = bicycle_dist_Promise = (the_response.do_driving_time_bike || the_response.do_bike_dist || the_response.do_dynamic_rate_bike) && delivery_address !== null ? calcRoute(latitude, longitude, szbd.store_address, 'BICYCLING') : false;
		}
		$.when(drive_time_car_Promise, drive_time_bike_Promise, drive_dist_Promise, bicycle_dist_Promise, radius_Promise).then(function(driving_car, driving_bike, driving_dist, bicycling_dist, radius) {
			// Check if the custom delivery method is applicable
			if ((the_response.status === true) && !(the_response.szbd_zones === null || the_response.szbd_zones === undefined || the_response.szbd_zones.length == 0)) {
				var ok_methods = [];
				the_response.szbd_zones.forEach(function(element, index) {
					if (element.drawn_map !== false) {
						var path = [];
						for (i = 0; element.geo_coordinates !== null && i < (element.geo_coordinates).length; i++) {
							path.push(new google.maps.LatLng(element.geo_coordinates[i][0], element.geo_coordinates[i][1]));
						}
						var polygon = new google.maps.Polygon({
							paths: path
						});
						var location = new google.maps.LatLng((latitude), (longitude));
						var address_is_in_zone = google.maps.geometry.poly.containsLocation(location, polygon);
					} else if (element.max_radius !== false) {
						var max_radius = element.distance_unit == 'miles' ? element.max_radius.radius *  1609.344 : element.max_radius.radius * 1000 ;
						var max_ok = max_radius > radius && radius !== false;
						if (element.max_radius.bool && !max_ok) {
							error_type.push(szbd.checkout_string_3 + ' ' + element.max_radius.radius + element.distance_unit);
						}
					}
					if (element.max_driving_distance !== false) {
						var max_dist = element.distance_unit == 'miles' ? element.max_driving_distance.distance *  1609.344 : element.max_driving_distance.distance * 1000 ;
						var max_driving_distance_ok = max_dist > driving_dist[1] || max_dist > driving_car[1] || max_dist > driving_bike[1];
					}
					if (element.max_driving_time_car !== false) {
						var max_driving_time_car = element.max_driving_time_car.time * 60 > driving_car[0];
					} else if (element.max_driving_time_bike !== false) {
						var max_driving_time_bike = element.max_driving_time_bike.time * 60 > driving_bike[0];
					}
					var condition_0 = (typeof address_is_in_zone == 'undefined' || address_is_in_zone);
					var condition_1 = (typeof max_ok == 'undefined' || max_ok);
					var condition_2 = typeof max_driving_distance_ok == 'undefined' || max_driving_distance_ok;
					var condition_3 = typeof max_driving_time_car == 'undefined' || max_driving_time_car;
					var condition_4 = typeof max_driving_time_bike == 'undefined' || max_driving_time_bike;
					var ok;
					if (element.drawn_map.bool && !condition_0) {
						ok = false;
					} else if (element.max_radius.bool && !condition_1) {
						ok = false;
					} else if (element.max_driving_distance.bool && !condition_2) {
						ok = false;
					} else if (element.max_driving_time_car.bool && !condition_3) {
						ok = false;
					} else if (element.max_driving_time_bike.bool && !condition_4) {
						ok = false;
					} else if ((typeof address_is_in_zone !== 'undefined' && address_is_in_zone) ||
						(typeof max_ok !== 'undefined' && max_ok) ||
						(typeof max_driving_distance_ok !== 'undefined' && max_driving_distance_ok) ||
						(typeof max_driving_time_car !== 'undefined' && max_driving_time_car) ||
						(typeof max_driving_time_bike !== 'undefined' && max_driving_time_bike)
					) {
						ok = true;
					} else if (typeof address_is_in_zone == 'undefined' &&
						typeof max_ok == 'undefined' &&
						typeof max_driving_distance_ok == 'undefined' &&
						typeof max_driving_time_car == 'undefined' &&
						typeof max_driving_time_bike == 'undefined'
					) {
						ok = true;
					} else {
						ok = false;
					}



					var min_amount_ok = parseFloat(element.min_amount) <= (the_response.tot_amount);
					if (!min_amount_ok && ok) {
						set_min([element.min_amount_formatted, element.min_amount]);
					}
					if (!ok || !min_amount_ok) {
						jQuery('#shipping_method li :input').filter(function() {
							return this.value == element.value_id;
						}).closest('li').attr('szbd',false).hide();

					} else {
					jQuery('#shipping_method li :input').filter(function() {
							return this.value == element.value_id;
						}).closest('li').attr('szbd',true).show();
						//below to get lowest cost method only

						if (the_response.exclude == 'yes') {
							if (element.rate_mode == 'fixed_and_distance') {
								var unit_converter = element.distance_unit == 'miles' ? 1 / 1.6093 : 1;
								element.cost_changed = true;
								element.cost = element.transport_mode == 'car' ? (driving_dist[1] / 1000) * parseFloat(element.rate_distance) * unit_converter + parseFloat(element.rate_fixed) : (bicycling_dist[1] / 1000) * parseFloat(element.rate_distance) * unit_converter + parseFloat(element.rate_fixed) ;
							} else if (element.rate_mode == 'distance') {
								var unit_converter = element.distance_unit == 'miles' ? 1 / 1.6093 : 1;
								element.cost = element.transport_mode == 'car' ? (driving_dist[1] / 1000) * parseFloat(element.rate_distance) * unit_converter : (bicycling_dist[1] / 1000) * parseFloat(element.rate_distance) * unit_converter;
								element.cost_changed = true;
							}
							ok_methods.push(element);

							var max = ok_methods.reduce((max, p, index, arr) =>  parseFloat(p.cost) >  parseFloat(max.cost) ? p : max, ok_methods[0]);

							if (ok_methods.length > 1) {
								jQuery('#shipping_method li :input').filter(function() {
									return this.value == max.value_id;
								}).closest('li').attr('szbd',false).hide();

								var min = ok_methods.reduce((min, p, index, arr) =>  parseFloat(p.cost) <  parseFloat(min.cost) ? p : min, ok_methods[0]);
								ok_methods = [min];
							}
						}
					}
					if (index >= the_response.szbd_zones.length - 1) {
						jQuery('#shipping_method').fadeIn();
						jQuery('table.woocommerce-checkout-review-order-table').removeClass('processing').unblock();
						var adr = the_response.delivery_address_string ;

						end(adr);
					}
				});
			} else {
				jQuery('table.woocommerce-checkout-review-order-table').removeClass('processing').unblock();
				jQuery('#shipping_method').fadeIn();
				var adr = the_response.delivery_address_string ;

				end(adr);
			}
		}).done(function() {
			if ((  jQuery('#shipping_method li :input').not('#shipping_method li[szbd="false"] :input').is(":checked") !== true  ) && jQuery('#shipping_method li').length !== 1) {
				jQuery('#shipping_method li').not('#shipping_method li[szbd="false"]').first().find('input').prop('checked', true).change();
			}
		});
	}

	function calcRoute(lati, longi, store_address, mode) {
		var N = 0;
		var time_def = $.Deferred();
		var request = {
			origin: store_address.store_address + ',' + store_address.store_postcode + ',' + store_address.store_city + ',' + store_address.store_state + ',' + store_address.store_country,
			destination: {
				lat: lati,
				lng: longi
			},
			travelMode: mode,
			drivingOptions: {
				departureTime: new Date(Date.now() + N), // for the time N milliseconds from now.
				trafficModel: 'bestguess'
			}
		};
		var directionsService = new google.maps.DirectionsService();
		directionsService.route(request, function(response, status) {
			if (szbd.debug == 1) {
				jQuery('.woocommerce-notices-wrapper:first-child').append('<div class="woocommerce-info szbd-debug"><h4>CALC ROUTE::</h4><br>' + JSON.stringify(response) + '</div>');
			}
			if (status == 'OK') {
				var time = (typeof response.routes[0].legs[0].duration_in_traffic !== 'undefined') ? response.routes[0].legs[0].duration_in_traffic.value : response.routes[0].legs[0].duration.value;
				var dist = response.routes[0].legs[0].distance.value;
				var del_address = response.routes[0].legs[0].end_address;
				time_def.resolve([time, dist, del_address]);
			} else {
				time_def.resolve('error');
			}
		});
		return time_def.promise();
	}

	function calcRadius(delivery_address, store_address, has_address) {
		var radius = $.Deferred();
		if (has_address) {

			store_address = new google.maps.LatLng(store_address.lat, store_address.lng);
			delivery_address = new google.maps.LatLng(delivery_address.lat, delivery_address.lng);
			var r = compute_radius(store_address, delivery_address);
			if (szbd.debug == 1) {

				jQuery('.woocommerce-notices-wrapper:first-child').append('<div class="woocommerce-info szbd-debug"><h4>CALC RADIUS:</h4><br>Radius:' + JSON.stringify(r) + '</div>');
			}
			radius.resolve(r);
		} else {
			var geocode_storeaddress = new google.maps.Geocoder();
			geocode_storeaddress.geocode({
					'address': store_address.store_address + ',' + store_address.store_postcode + ',' + store_address.store_city + ',' + store_address.store_state + ',' + store_address.store_country
				},
				function(results, status) {

					if (szbd.debug == 1) {

						jQuery('.woocommerce-notices-wrapper:first-child').append('<div class="woocommerce-info szbd-debug"><h4>CALC RADIUS::</h4><br>Radius:' + JSON.stringify(compute_radius(results[0].geometry.location, delivery_address)) + '<br>STORE ADDRESS:' + JSON.stringify(results) + '<br>DELIVERY ADDRESS:' + JSON.stringify(delivery_address) + '</div>');
					}
					if (status == 'OK') {
						var r = compute_radius(results[0].geometry.location, delivery_address);
						radius.resolve(r);
					} else {
						radius.resolve('error');
					}
				});
		}
		return radius.promise();
	}

	function compute_radius(s, d) {
		return google.maps.geometry.spherical.computeDistanceBetween(s, d);
	}
	// Helping methods
	function findCommonElements(arr1, arr2) {
		return arr1.some(item => arr2.includes(item));
	}
});
