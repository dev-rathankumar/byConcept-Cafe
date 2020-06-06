(function($, window, document, undefined) {



    'use strict';



    function polygonConfig(element, options, map_data) {



        this.options = options;

        this.map_data = map_data;

        this.bounds = new google.maps.LatLngBounds();

        this.geocoder = new google.maps.Geocoder();

        this.search_area = '';

        this.unique_form_container='';



        this.init();



    }



    polygonConfig.prototype = {



        init: function() {



            var polyobj = this;

            var map_data = this.options.map_data;

            this.mapSetup(this.options);

            var enable_marker = this.options.mapsettings.enable_markers_on_map;

            var enable_polygon = this.options.mapsettings.enable_polygon_on_map;

            var exclude_country = (this.options.exclude_countries.length > 0) ? true : false;

            var exclude_countries = this.options.exclude_countries;

            var from_tab = polyobj.map_data.from_tab;

            var icon_url = polyobj.map_data.icon_url;





            if (from_tab !== "undefined" && from_tab == 'yes') {

                var icon_url = polyobj.map_data.icon_url

            }else{

                var icon_url = polyobj.options.mapsettings.sicon_url

            }



            if (enable_marker !== "undefined" && enable_marker !== 'no') {



                var allzipcode = this.map_data.allzipcodes;



                for (var i = 0; i < allzipcode.length; ++i) {

                    if (this.options.mapsettings.enable_restrict && this.options.marker_country_restrict) {

                        var tr = {

                            componentRestrictions: {

                                country: this.options.mapsettings.restrict_country,

                                postalCode: allzipcode[i]

                            }

                        };

                    } else {

                        var tr = {

                            address: allzipcode[i]

                        };

                    }

                    polyobj.geocoder.geocode(tr, function(result, status) {

                        if (status == 'OK' && result.length > 0) {



                            for (var f = 0; f < result.length; f++) {



                                var address = result[f].address_components;

                                var need_to_skip = true;



                                for (var t = 0; t < address.length; t++) {

                                    if (jQuery.inArray('country', address[t].types) == 0) {

                                        if (jQuery.inArray(address[t].short_name, exclude_countries) == 0) {

                                            need_to_skip = false;

                                        }

                                    }

                                }

                                if (need_to_skip) {

                                    var marker = new google.maps.Marker({

                                        position: result[f].geometry.location,

                                        map: polyobj.map,

                                        icon: icon_url

                                    });

                                    polyobj.update_bounds(marker.getPosition());

                                }



                            }

                        }

                    });





                }



                this.bounds = new google.maps.LatLngBounds();

                var allstorelocations = this.map_data.allstorelocations;

                for (var i = 0; i < allstorelocations.length; ++i) {



                    if(allstorelocations[i].lat =='' && allstorelocations[i].lng =='') 

                        continue;



                    var storeLatLng = {

                            lat: allstorelocations[i].lat,

                            lng: allstorelocations[i].lng

                        };





                  var storeCircle = new google.maps.Circle({

                    strokeColor: this.options.range_circle_ui.strokeColor,

                    strokeOpacity: this.options.range_circle_ui.strokeOpacity,

                    strokeWeight: this.options.range_circle_ui.strokeWeight,

                    fillColor: this.options.range_circle_ui.fillColor,

                    fillOpacity: this.options.range_circle_ui.fillOpacity,

                    map: polyobj.map,

                    center: storeLatLng,

                    radius: (allstorelocations[i].range) * 100

                  });



                    var include_location = true;

                    if (this.options.mapsettings.enable_restrict && this.options.marker_country_restrict) {

                        if (allstorelocations[i].place_country_name != this.options.mapsettings.restrict_country) {

                            include_location = false;

                        }

                    }



                    if (include_location) {

                        

                        var marker = new google.maps.Marker({

                            position: storeLatLng,

                            map: polyobj.map,

                            icon: icon_url

                        });

                        polyobj.update_bounds(marker.getPosition());



                    }



                }



            }





            if (enable_polygon !== "undefined" && enable_polygon !== 'no') {

                this.drawPolygon();

            }

            this.setBoundsofMap(this.bounds);



        },

        update_bounds: function(location) {

            var polyobj = this;

            var enable_bound = this.options.mapsettings.enable_bound;

            if (enable_bound !== "undefined" && enable_bound !== 'no') {

                this.bounds.extend(location);

                polyobj.map.fitBounds(this.bounds);

            }

        },

        mapSetup: function(options) {



            var mapObj = this;

            var mapsettings;

            var map_data = this.map_data;

            var from_tab = map_data.from_tab;

            if (from_tab != undefined && from_tab == 'yes') {

                mapsettings = options.mapsettings;

            } else {

                mapsettings = options.shortcode_map;

            }

            var centerlat = mapsettings.centerlat.length > 0 ? parseFloat(mapsettings.centerlat) : 40.73061;

            var centerlng = mapsettings.centerlng.length > 0 ? parseFloat(mapsettings.centerlng) : -73.935242;

            var centerLatLng =   {lat:centerlat, lng:centerlng};

            var zoom = mapsettings.zoom.length > 0 ? parseInt(mapsettings.zoom) : 5;

            var style = mapsettings.style;

            var mapOptions = {

                center: centerLatLng,

                zoom: zoom

            };

            

            mapObj.map = new google.maps.Map(document.getElementById(map_data.map_id), mapOptions);

            if ($("#pac-input" + map_data.map_id).length > 0) {

                this.autoSuggestSearch(options, "pac-input" + map_data.map_id);

            }

            if (style != '') {

                mapObj.map.setOptions({

                    styles: eval(style)

                });

            }





        },

        autoSuggestSearch: function(options, id) {



            var polyobj = this;

             var from_tab = polyobj.map_data.from_tab;

            var icon_url = polyobj.map_data.icon_url;



            if (from_tab !== "undefined" && from_tab == 'yes') {

                var icon_url = polyobj.map_data.icon_url

            }else{

                var icon_url = polyobj.options.mapsettings.sicon_url

            }



            var input = (document.getElementById(id));

            polyobj.map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);

            var autocomplete = new google.maps.places.Autocomplete(input);

            autocomplete.bindTo('bounds', polyobj.map);

            var infowindow = new google.maps.InfoWindow();

            var marker = new google.maps.Marker({

                map: polyobj.map,

                anchorPoint: new google.maps.Point(0, -29),

                icon: icon_url

            });

            autocomplete.addListener('place_changed', function() {

                infowindow.close();

                marker.setVisible(false);

                var place = autocomplete.getPlace();

                if (!place.geometry) {

                    return;

                }

                if (place.geometry.viewport) {

                    polyobj.map.fitBounds(place.geometry.viewport);

                } else {

                    polyobj.map.setCenter(place.geometry.location);

                    polyobj.map.setZoom(17);

                }

                marker.setPosition(place.geometry.location);

                marker.setVisible(true);

                var address = '';

                if (place.address_components) {

                    address = [

                        (place.address_components[0] && place.address_components[0].short_name || ''),

                        (place.address_components[1] && place.address_components[1].short_name || ''),

                        (place.address_components[2] && place.address_components[2].short_name || '')

                    ].join(' ');

                }

                infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);

                infowindow.open(polyobj.map, marker);

            });

        },

        drawPolygon: function() {



            var polyobj = this;

            var allpolygonscoordinate = polyobj.map_data.allpolycoordinates;



            for (var j = 0; j < allpolygonscoordinate.length; j++) {

                var onecollectioncoordinate = allpolygonscoordinate[j];

                var singlepolygon = new Array();


                for (var i = 0, l = onecollectioncoordinate.length; i < l; i++) {

                    singlepolygon[i] = new google.maps.Polygon({

                        paths: onecollectioncoordinate[i].coordinate,

                        strokeColor: onecollectioncoordinate[i].format.strokeColor,

                        strokeOpacity: onecollectioncoordinate[i].format.strokeOpacity,

                        strokeWeight: onecollectioncoordinate[i].format.strokeWeight,

                        fillColor: onecollectioncoordinate[i].format.fillColor,

                        fillOpacity: onecollectioncoordinate[i].format.fillOpacity,

                        id: onecollectioncoordinate[i].id

                    });

                    var mynewpoly = onecollectioncoordinate[i].coordinate;

                    var testarr = [];

                    for (var g = 0; g < mynewpoly.length; g++) {

                        testarr.push(new google.maps.LatLng(mynewpoly[g].lat, mynewpoly[g].lng));

                        var enable_bound = this.options.mapsettings.enable_bound;

                        if (enable_bound !== "undefined" && enable_bound !== 'no') {

                            this.bounds.extend(testarr[g]);

                        }

                    }

                    var center = polyobj.getCenterOfPolygon(singlepolygon[i]);

                    var url = '';

                    var infomessage = '';

                    if (onecollectioncoordinate[i].format.redirectUrl) {

                        url = onecollectioncoordinate[i].format.redirectUrl;

                    }

                    if (onecollectioncoordinate[i].format.infoWindow) {

                        infomessage = onecollectioncoordinate[i].format.infoWindow;

                    }



                    if ($('#tab-avalibility_map').length > 0) {

                        $('#tab-avalibility_map').append("<input type='hidden' id='" + onecollectioncoordinate[i].id + "' data-strokecolor='" + onecollectioncoordinate[i].format.strokeColor + "' data-strokeOpacity='" + onecollectioncoordinate[i].format.strokeOpacity + "' data-fillColor='" + onecollectioncoordinate[i].format.fillColor + "' data-fillopacity='" + onecollectioncoordinate[i].format.fillOpacity + "' data-strokeweight='" + onecollectioncoordinate[i].format.strokeWeight + "'data-redirecturl='" + url + "'data-infomessage='" + infomessage + "' >");

                    } else {

                        if ($('#' + this.map_data.map_id).length > 0) {

                            $('#' + this.map_data.map_id).append("<input type='hidden' id='" + onecollectioncoordinate[i].id + "' data-strokecolor='" + onecollectioncoordinate[i].format.strokeColor + "' data-strokeOpacity='" + onecollectioncoordinate[i].format.strokeOpacity + "' data-fillColor='" + onecollectioncoordinate[i].format.fillColor + "' data-fillopacity='" + onecollectioncoordinate[i].format.fillOpacity + "' data-strokeweight='" + onecollectioncoordinate[i].format.strokeWeight + "'data-redirecturl='" + url + "'data-infomessage='" + infomessage + "' >");

                        }

                    }

                    singlepolygon[i].setMap(polyobj.map);

                }

                for (var i = 0, l = singlepolygon.length; i < l; i++) {

                    google.maps.event.addListener(singlepolygon[i], 'click', function(event) {

                        var contents = [];

                        var infowindows = [];

                        if ($('#' + this.id).data('redirecturl') != '') {

                            window.location.href = $('#' + this.id).data('redirecturl');

                        }

                        if ($('#' + this.id).data('infomessage') != '') {

                            var pt = polyobj.getCenterOfPolygon(this);

                            var lat = pt.lat();

                            var lng = pt.lng();

                            contents[i] = $('#' + this.id).data('infomessage');

                            if (contents[i])

                                contents[i] = window.atob(contents[i]);

                            var latLng = new google.maps.LatLng(lat, lng);

                            infowindows[i] = new google.maps.InfoWindow({

                                'position': latLng,

                                content: contents[i],

                                maxWidth: 300

                            });

                            infowindows[i].open(polyobj.map, this);

                        }

                    });

                }

            }

            if (enable_bound !== "undefined" && enable_bound !== 'no' && allpolygonscoordinate.length>0) {

                setTimeout(function() {

                        google.maps.event.trigger(polyobj.map, 'resize');

                        polyobj.map.fitBounds(polyobj.bounds);

                }, 50);

           }



        },

        getCenterOfPolygon: function(polygon) {



            var PI = 22 / 7

            var X = 0;

            var Y = 0;

            var Z = 0;



            polygon.getPath().forEach(function(vertex, inex) {

                var lat1 = vertex.lat();

                var lon1 = vertex.lng();

                var lat1 = lat1 * PI / 180;

                var lon1 = lon1 * PI / 180;

                X += Math.cos(lat1) * Math.cos(lon1);

                Y += Math.cos(lat1) * Math.sin(lon1);

                Z += Math.sin(lat1);

            })

            var Lon = Math.atan2(Y, X);

            var Hyp = Math.sqrt(X * X + Y * Y);

            var Lat = Math.atan2(Z, Hyp);

            var Lat = Lat * 180 / PI;

            var Lon = Lon * 180 / PI;

            return new google.maps.LatLng(Lat, Lon);

        },

        setBoundsofMap: function(bounds) {

            var polyobj = this;

            jQuery('.avalibility_map_tab').on('click', function() {

                setTimeout(function() {

                    google.maps.event.trigger(polyobj.map, 'resize');

                    polyobj.map.fitBounds(bounds);

                }, 50);

            });

        }

    };



    function zipcode_testing(element, options) {





        this.options = options;

        this.placeSearch = "",

            this.IdSeparator = "",

            this.autocomplete = [],

            this.shortcodeautocomplete = [],

            this.shortcodeplace = [];

        this.streetNumber = "",

            this.formFields = [],

            this.formFieldsValue = [],

            this.component_form = [],

            this.checkoutaddress = ['shipping', 'billing'];

        this.checkoutPlace = {

            shipping: '',

            billing: ''

        };

        this.hiddenresult = [];

        this.enableApi = (typeof window.google !== "undefined" && wdap_settings_obj.is_api_key !== "undefined") ? true : '';

        this.shortcode_settings = wdap_settings_obj.shortcode_settings;

        this.Serror_container = $(".wdap_product_availity_form").find(".error-container");

        this.Ssuccess_msg_color = this.shortcode_settings.form_success_msg_color;

        this.Serror_msg_color = this.shortcode_settings.form_error_msg_color;

        this.init();

    }



    zipcode_testing.prototype = {



        init: function() {



            var zip_obj = this;

            var match = false;

            zip_obj.initConfig();



            if ((zip_obj.enableApi) && (wdap_settings_obj.enable_autosuggest_checkout)) {



                var shippingaddr = [

                    '_address_1',

                    '_address_2',

                    '_city',

                    '_state',

                    '_postcode',

                    '_country'

                ];



                for (var i = 0; i < zip_obj.checkoutaddress.length; i++) {



                    var checkPrefix = zip_obj.checkoutaddress[i];

                    zip_obj.formFields[checkPrefix] = zip_obj.formFieldsValue[checkPrefix] = [];

                    for (var j = 0; j < shippingaddr.length; j++) {

                        zip_obj.formFields[checkPrefix].push(checkPrefix + shippingaddr[j]);

                    }

                    zip_obj.component_form[checkPrefix] = {

                        'street_number': [checkPrefix + '_address_1', 'short_name'],

                        'route': [checkPrefix + '_address_1', 'long_name'],

                        'locality': [checkPrefix + '_city', 'long_name'],

                        'postal_town': [checkPrefix + '_city', 'long_name'],

                        'sublocality_level_1': [checkPrefix + '_city', 'long_name'],

                        'administrative_area_level_1': [checkPrefix + '_state', 'short_name'],

                        'administrative_area_level_2': [checkPrefix + '_state', 'short_name'],

                        'country': [checkPrefix + '_country', 'long_name'],

                        'postal_code': [checkPrefix + '_postcode', 'short_name']

                    };

                    zip_obj.getIdSeparator(checkPrefix);

                    zip_obj.autosuggestaddress(checkPrefix);



                    var billing_address = document.getElementById(checkPrefix + "_address_1");

                    if (billing_address != null) {

                        billing_address.addEventListener("focus", function(event) {

                            zip_obj.setAutocompleteCountry(checkPrefix)

                        }, true);

                    }

                    var billing_country = document.getElementById(checkPrefix + "_country");

                    if (billing_country != null) {

                        billing_country.addEventListener("change", function(event) {

                            zip_obj.setAutocompleteCountry(checkPrefix)

                        }, true);

                    }

                }

            }

            if (this.order_restriction !== "undefined") {

                jQuery(document).on('click', '.new_submit', function(e) {

                    $('form.checkout').addClass('processing').block({
                        message: "",
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });

                    $(".wdapzipsumit").trigger("click");


                });



            }





            if (jQuery('.check_availability').length > 0 && (zip_obj.enableApi) && !(enable_filter_by_zipcode)) {





                var input = document.getElementsByClassName('type-location');

                if (typeof zip_obj.options.autosuggest_country_restrict !== typeof undefined) {



                    var restrictOptions = {

                        componentRestrictions: {

                            country: zip_obj.options.autosuggest_country_restrict.toLowerCase()

                        }

                    };

                    for (i = 0; i < input.length; i++) {



                        var form_id  = $(input[i]).parent().find('.unique_form_id').val();

                        var autocomplete = new google.maps.places.Autocomplete(input[i], restrictOptions);

                        autocomplete.inputId = form_id;

                        autocomplete.addListener('place_changed', ShortcodefillInAddress);

                        zip_obj.shortcodeautocomplete.push(autocomplete);



                        

                    }



                } else {

                    for (i = 0; i < input.length; i++) {

                        var form_id  = $(input[i]).parent().find('.unique_form_id').val();

                        var autocomplete = new google.maps.places.Autocomplete(input[i]);

                            autocomplete.inputId = form_id;

                            autocomplete.addListener('place_changed', ShortcodefillInAddress);

                            zip_obj.shortcodeautocomplete.push(autocomplete);



                    }

                }



            }



            function ShortcodefillInAddress(){

                var place = this.getPlace();

                zip_obj.shortcodeplace[0] = zip_obj.setCustomResponse(place);

            }



            if (jQuery('.locate-me').length > 0) {

                zip_obj.locateMe();

            }

            // Woopages Testing

            zip_obj.Woopages_zip_testing();

            zip_obj.Shortcode_zip_testing();



        },

        autosuggestaddress: function(checkPrefix) {

            var zip_obj = this;

            if (!(document.getElementById(checkPrefix + '_address_1') === null)) {



                var shipaddr = document.getElementById(checkPrefix + '_address_1');

                google.maps.event.addDomListener(shipaddr, 'keydown', function(e) {

                    if (e.keyCode == 13) {

                        e.preventDefault();

                    }

                });



                var input = document.getElementById(checkPrefix + '_address_1');



                if (typeof zip_obj.options.autosuggest_country_restrict_checkout !== typeof undefined) {



                    var restrictOptions = {

                        componentRestrictions: {

                            country: zip_obj.options.autosuggest_country_restrict_checkout.toLowerCase()

                        }

                    };

                    zip_obj.autocomplete[checkPrefix] = new google.maps.places.Autocomplete(input, restrictOptions);



                } else {



                    zip_obj.autocomplete[checkPrefix] = new google.maps.places.Autocomplete(input);

                }

                google.maps.event.addListener(zip_obj.autocomplete[checkPrefix], 'place_changed', function(event) {

                    zip_obj.fillInAddress(checkPrefix)

                });

            }

        },

        toRad: function(Value) {

            return Value * Math.PI / 180;

        },

        calculateDistance: function(lat1, lon1, lat2, lon2) {



            var zip_obj = this;

            var R = 6371; // km

            var dLat = zip_obj.toRad(lat2 - lat1);

            var dLon = zip_obj.toRad(lon2 - lon1);

            var lat1 = zip_obj.toRad(lat1);

            var lat2 = zip_obj.toRad(lat2);



            var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +

                Math.sin(dLon / 2) * Math.sin(dLon / 2) * Math.cos(lat1) * Math.cos(lat2);

            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            var d = R * c;

            return d;

        },

        initConfig: function() {

            $('.wdap_zip').click(function() {

                var data = $(this).text();

                $("#wdapziptextbox").val(data);

            });

            $('#wdap_type_location').keyup(function() {

                var data = $(this).val();

                if (data) {

                    $(".error-container").hide();

                }

            });

            $('.wdapziptextbox').blur(function() {

                var data = $(this).val();

                if (data === '') {

                    $(".wdap_notification_message").hide();

                    $(this).parent().find(".wdapzipsumit").removeAttr('disabled');

                }

            });

        },

        getDefaultValue: function(address_fields) {

            var zipcode = '';

            var user_address_details = [];

            var default_Values = [];



            for (var i = 0; i < address_fields.length; i++) {

                var address_field_value = $("#" + address_fields[i]).val();



                if (address_field_value) {

                    var key = address_fields[i];

                    var address_field = {};

                    user_address_details[key] = address_field_value;



                    if (i == address_fields.length - 1){

                        zipcode = zipcode + address_field_value;

                    }

                    else{

                        zipcode = zipcode + address_field_value + ', ';

                    }

                }

            }

            default_Values['components'] = user_address_details;

            default_Values['zip_string'] = zipcode;

            return default_Values;

        },

        wdap_display_result: function(button_obj, final_result) {



            var zip_obj = this;

            var notification_color;

            var notification_text;

            var check_zip_result;



            if (final_result == 1) {

                var notification_color = wdap_settings_obj.errormessages.success_msg_color;

                var notification_text = wdap_settings_obj.errormessages.a;

                var check_zip_result = 'YES';

            } else {

                notification_color = wdap_settings_obj.errormessages.error_msg_color;

                notification_text = wdap_settings_obj.errormessages.na;

                check_zip_result = 'NO';

            }

            zip_obj.remove_ajax_loader(button_obj);

            var display_result_div = $(button_obj).parent().parent().find(".wdap_notification_message").show();

            display_result_div.css("color", notification_color).text(notification_text);

            $("#Chkziptestresult").val(check_zip_result);

            jQuery(button_obj).parent().find(".wdap_start").val('yes');



        },

        setup_cart_checkout_tbl_for_notifications: function(products) {



            if (!$(".shop_table thead tr").find('th:last').hasClass('avalitystatus')) {

                $(".shop_table thead tr").append("<th class='avalitystatus'>" + wdap_settings_obj.errormessages.th + "</th>");

            }

            $(".shop_table.cart tbody tr").not(':last').each(function(j) {

                var cart_loading_image = '<img src="' + wdap_settings_obj.loader_image + '" name="cart_loading_image" class="cart_loading_image">';

                var classes = $(this).attr('class').split(' ');

                if(classes.length>2){

                     if (!$(this).find('td:last').hasClass('product_avalibility_tab')) {

                        $(this).append('<td class="product_avalibility_tab">' + cart_loading_image + '</td>');

                    } else {

                        $(this).find('.product_avalibility_tab').html(cart_loading_image);

                    }



                }

            });



        },

        is_product_available_in_store_locations: function(user_location, product_id) {



            var can_deliver = false;



            if ((typeof store_locations != undefined) && (typeof user_location != undefined) && (typeof user_location != 'undefined')) {





                var current_lat = user_location.geometry.location.lat().toFixed(2);

                var current_lng = user_location.geometry.location.lng().toFixed(2);

                var _coordinates = user_location.geometry.location;



                $.each(store_locations, function(key, value) {



                    var need_to_check_location = false;

                    if (typeof product_id == "undefined") {

                        need_to_check_location = true;

                    } else {

                        var collection_product = value.product_id;

                        if (collection_product == "all") {

                            need_to_check_location = true;

                        } else {

                            if (collection_product.length > 0) {

                                var without_string = JSON.parse("[" + collection_product.join() + "]");

                                if (jQuery.inArray(parseInt(product_id), without_string) !== -1)

                                    need_to_check_location = true;

                            }

                        }

                    }

                    if (need_to_check_location) {

                        var storelat = value.lat.toFixed(2);

                        var storelng = value.lng.toFixed(2);



                        var delivery_allow_distance = parseFloat(value.range);

                        var _store_cord_obj = new google.maps.LatLng(parseFloat(storelat), parseFloat(storelng));

                        var computed_distance = parseFloat((google.maps.geometry.spherical.computeDistanceBetween(_store_cord_obj, _coordinates)) / 1000);

                        

                        if (computed_distance < delivery_allow_distance) {

                            can_deliver = true;

                        }

                    }



                });



            }

            return can_deliver;



        },

        Shortcode_zip_testing: function() {



            var zip_obj = this;

            if (jQuery('.check_availability').length > 0 && !(enable_filter_by_zipcode)) {

                jQuery(document).on('click', '.check_availability', function(e) {

                    var unique_form_id = $(this).parent().find('.unique_form_id').val();

                    

                    if(unique_form_id.length>0)

                     var unique_form_container = $(".wdap_product_availity_form."+unique_form_id);

                    else

                     var unique_form_container = $(".wdap_product_availity_form");



                    zip_obj.unique_form_container = unique_form_container;

                    unique_form_container.find(".error-container").hide();

                    var productid;

                    var submit_btn = this;

                    var validation = true;

                    var checkproduct = wdap_settings_obj.shortcode_settings.check_product;

                    var converted_zipcode =unique_form_container.find(".convertedzipcode").val();

                    var txt_address =unique_form_container.find("#wdap_type_location").val();

                    if (unique_form_container.find('.form_product_list').length > 0)

                        productid = unique_form_container.find(".form_product_list").val();



                    if (checkproduct && !(productid)) {

                        zip_obj.showshortcode_notification('select_product');

                        validation = false;

                        return false;

                    }



                    if ((!converted_zipcode) && !(txt_address)) {

                        zip_obj.showshortcode_notification('empty');

                        validation = false;

                        return false;

                    }



                    if ((!converted_zipcode) && (txt_address)) {

                        converted_zipcode = txt_address;

                    }

                    zip_obj.shortcode_loader(submit_btn);                    

                    var can_deliver = zip_obj.is_product_available_in_store_locations(zip_obj.shortcodeplace[0], productid);

                    if (can_deliver) {

                        zip_obj.showshortcode_notification('yes');

                        return false;



                    } else {

                        var zip_data = {

                            action: 'wdap_ajax_call',

                            operation: 'Check_for_zipmatch',

                            noncevalue: wdap_settings_obj.nonce,

                            zipcode: converted_zipcode,

                            shortcode: 'yes',

                            productid: productid

                        };

                        if (typeof window.google !== "undefined") {

                            var zip_response2 = {

                                zip_response: JSON.stringify(zip_obj.shortcodeplace)

                            };

                            jQuery.extend(zip_data, zip_response2);

                            zip_obj.shortcodeAjax(zip_data);

                        } else {

                            zip_obj.shortcodeAjax(zip_data);

                        }



                    }



                });

            }

        },

        Woopages_zip_testing: function() {

            var hiddenresult = [];

            var zip_obj = this;

            if (!disable_zipcode_checking) {

                $(".wdapzipsumit").click(function(event) {

                    hiddenresult = [];

                    var button_obj = this

                    var restrict = true;

                    var checkPrefix = '';

                    var productid = JSON.parse("[" + $(this).parent().find(".checkproductid").val() + "]");

                    var pagetype = $("#checkproductid").data('pagetype');




                    if (pagetype == 'checkout' || pagetype == 'cart') {

                        zip_obj.setup_cart_checkout_tbl_for_notifications(productid);

                    }



                    var start = jQuery(button_obj).parent().find(".wdap_start").val();

                    if (pagetype == 'checkout') {



                        var method = zip_obj.options.wdap_checkout_avality_method;

                        var address_string = '';

                        var zipcode = '';

                        if (method != undefined) {

                            var is_need_check_billing = false;

                            if ($("#ship-to-different-address-checkbox").length > 0) {

                                var is_different_shipping = document.getElementById("ship-to-different-address-checkbox").checked;

                                if (is_different_shipping) {

                                    checkPrefix = 'shipping';

                                    zipcode = $('input:text[name=shipping_postcode]').val();

                                    if (method == 'via_zipcode') {

                                        zipcode = $('input:text[name=shipping_postcode]').val();

                                    }

                                    if (method == 'via_address' && zip_obj.enableApi) {

                                        if ((zip_obj.checkoutPlace[checkPrefix].formatted_address != undefined)) {

                                            zipcode = zip_obj.checkoutPlace[checkPrefix].formatted_address;

                                        } else {

                                           var default_values = zip_obj.getDefaultValue(zip_obj.formFields[checkPrefix]);

                                           zipcode = default_values['components'].shipping_postcode;

                                        }

                                    }

                                } else {

                                    is_need_check_billing = true;

                                }

                            } else {

                                is_need_check_billing = true;

                            }



                            if (is_need_check_billing) {

                                checkPrefix = 'billing';

                                zipcode = $('input:text[name=billing_postcode]').val();

                                if (method == 'via_zipcode') {

                                    zipcode = $('input:text[name=billing_postcode]').val();

                                }

                                if (method == 'via_address' && zip_obj.enableApi) {

                                    if ((zip_obj.checkoutPlace[checkPrefix].formatted_address != undefined)) {

                                        zipcode = zip_obj.checkoutPlace[checkPrefix].formatted_address;

                                    } else {

                                       var default_values = zip_obj.getDefaultValue(zip_obj.formFields[checkPrefix]);

                                       zipcode = default_values['components'].billing_postcode;



                                    }

                                }

                            }

                        }

                    } else {

                        zipcode = $(this).parent().find(".wdapziptextbox").val();

                    }


                    if (!zipcode) {



                        $(this).parent().parent().find(".wdap_notification_message").show().css("color", wdap_settings_obj.errormessages.error_msg_color).text(wdap_settings_obj.errormessages.empty);



                        if (pagetype == 'checkout' || pagetype == 'cart') {



                            $.each(productid, function(index, single_product) {



                                var msg = '<span class="notavilable">' + wdap_settings_obj.errormessages.empty + '</span>';

                                zip_obj.showresultcartcheckout(single_product, msg);

                            });



                        }



                        if ($('.new_submit').length > 0) {

                            $('form.checkout').removeClass('processing').unblock().submit();

                        }



                    } else {



                        if (wdap_settings_obj.order_restriction != undefined && pagetype == 'checkout') {

                            restrict = false;

                        }

                        if (start == 'yes') {

                            jQuery(button_obj).parent().find(".wdap_start").val('no');

                            zip_obj.ajax_loader(this);

                            var mapsetting = zip_obj.options.mapsettings;

                            if (mapsetting.enable_restrict) {

                                var t = {

                                    componentRestrictions: {

                                        country: mapsetting.restrict_country,

                                        postalCode: zipcode

                                    }

                                };

                            } else {

                                var t = {

                                    address: zipcode

                                };

                            }

                            var raw_data = {

                                action: 'wdap_ajax_call',

                                operation: 'Check_for_zipmatch',

                                noncevalue: wdap_settings_obj.nonce,

                                'productid': productid,

                                'pagetype': pagetype,

                                'zipcode': zipcode

                            };

                            if (zip_obj.enableApi) {
                                if (checkPrefix && Object.keys(zip_obj.checkoutPlace[checkPrefix]).length > 0) {
                                    var zip_response2 = {
                                        zip_response: JSON.stringify(zip_obj.checkoutPlace[checkPrefix])
                                    };
                                    jQuery.extend(raw_data, zip_response2);
                                    var geocode_reseponse = [zip_obj.checkoutPlace[checkPrefix]];
                                    var need_to_fire_ajax = zip_obj.check_product_in_store(geocode_reseponse, productid, pagetype);
                                    if (need_to_fire_ajax.status) {
                                        if (pagetype == "checkout") {
                                            var found_products = zip_obj.skipfoundproducts(raw_data.productid, need_to_fire_ajax.result);
                                            raw_data.productid = found_products;
                                        }
                                        zip_obj.zipcodeAjax(raw_data, restrict, button_obj);
                                    } else {

                                        if (pagetype == "checkout") {
                                            zip_obj.cartMessage(button_obj);
                                        }
                                        zip_obj.wdap_display_result(button_obj, 1);
                                    }

                                } else {

                                    var geocoder = new google.maps.Geocoder;
                                    geocoder.geocode(t, function(results, status) {

                                        if (status === 'OK') {

                                            if (results[0]) {



                                                var zip_response1 = {

                                                    zip_response: JSON.stringify(results)

                                                };

                                                jQuery.extend(raw_data, zip_response1);



                                                var need_to_fire_ajax = zip_obj.check_product_in_store(results, productid, pagetype);

                                                if (need_to_fire_ajax.status) {



                                                    if (pagetype == "cart" || pagetype == "checkout") {

                                                        var found_products = zip_obj.skipfoundproducts(raw_data.productid, need_to_fire_ajax.result);

                                                        raw_data.productid = found_products;

                                                    }

                                                    zip_obj.zipcodeAjax(raw_data, restrict, button_obj);



                                                } else {



                                                    if (pagetype == "cart" || pagetype == "checkout") {

                                                        $(".shop_table thead tr").each(function(j) {

                                                            if (!$(this).find('th:last').hasClass('avalitystatus')) {

                                                                $(this).append("<th class='avalitystatus'>" + wdap_settings_obj.errormessages.th + "</th>");

                                                            }

                                                        });

                                                        zip_obj.cartMessage(button_obj);
                                                    }
                                                    zip_obj.wdap_display_result(button_obj, 1);
                                                }
                                            }
                                        } else {
                                            zip_obj.zipcodeAjax(raw_data, restrict, button_obj);
                                        }

                                    });

                                }

                            } else {

                                zip_obj.zipcodeAjax(raw_data, restrict, button_obj);

                            }

                        }

                    }



                });





            }

        },

        skipfoundproducts: function(raw_products, store_result) {



            var zip_obj = this;

            var need_to_skip_products = raw_products;

            var backup_products = raw_products;

            for (var i = 0; i < need_to_skip_products.length; i++) {

                if (store_result[i].value == "YES") {

                    backup_products[i] = '';

                }

            }

            var backup_products = backup_products.filter(zip_obj.isempty);

            return backup_products;

        },

        isempty: function(x) {

            if (x !== "")

                return true;

        },

        ajax_loader: function(button_obj) {

            jQuery(button_obj).removeClass('wdap_arrow').addClass('loadinggif');

        },
        remove_ajax_loader: function(button_obj) {

            jQuery(button_obj).removeClass('loadinggif').addClass('wdap_arrow');

        },
        check_product_in_store: function(results, product_id, pagetype) {

            var zip_obj = this;

            var need_to_fire_ajax = {

                status: false

            };

            var total_result = [];

            $.each(product_id, function(index, single_product) {

                var can_deliver = zip_obj.is_product_available_in_store_locations(results[0], single_product);

                if (can_deliver) {

                    var msg = '<span class="avilable">' + wdap_settings_obj.errormessages.a + '</span>';

                    zip_obj.showresultcartcheckout(single_product, msg);

                    var singlresult = {

                        id: single_product,

                        value: "YES"

                    };

                    zip_obj.hiddenresult.push(singlresult);

                    total_result.push(singlresult);

                } else {

                    var singlresult = {

                        id: single_product,

                        value: "NO"

                    };

                    total_result.push(singlresult);

                }

            });



            for (var i = 0; i < total_result.length; i++) {

                if (total_result[i].value == "NO") {

                    need_to_fire_ajax.status = true;

                }

            }

            need_to_fire_ajax.result = total_result;

            return need_to_fire_ajax;

        },



        zipcodeAjax: function(zip_data, restrict, button_obj) {

            
            var temp_Zipcode = zip_data.zipcode;
            if(temp_Zipcode){
                zip_data.zipcode = temp_Zipcode.trim();
            }

            var zip_obj = this;

            jQuery.ajax({

                type: "POST",

                url: wdap_settings_obj.ajax_url,

                datatype: 'json',

                data: zip_data,

                async: restrict,

                success: function(data) {


                    var response = JSON.parse(data);

                    if ((response.pagetype == 'cart') || (response.pagetype == 'checkout')) {


                    zip_obj.cart_and_checkout_response(response, button_obj);

                    if ($('.new_submit').length > 0) {
                        setTimeout(function(){
                            $('form.checkout').removeClass('processing').unblock().submit();
                        },50);

                    }

                    }

                    if ((response.pagetype == 'single') || (response.pagetype == 'shop') || (response.pagetype == 'category') ) {

                        zip_obj.checkcoordinateandshowresult(response, button_obj);

                    }

                    this.hiddenresult = [];

                    this.checkoutPlace = [];

                }

            });

        },



        locateMe: function() {



            var zip_obj = this;

            jQuery(document).on('click', '.locate-me', function(e) {

                var unique_form_id = $(this).parent().find('.unique_form_id').val();

                    

                    if(unique_form_id.length>0)

                     var unique_form_container = $(".wdap_product_availity_form."+unique_form_id);

                    else

                     var unique_form_container = $(".wdap_product_availity_form");



                    zip_obj.unique_form_container = unique_form_container;



                unique_form_container.find(".error-container").hide();

                if (navigator.geolocation) {

                    navigator.geolocation.getCurrentPosition(function(position, showError) {

                        var lat = position.coords.latitude;

                        var lng = position.coords.longitude;

                        if (zip_obj.enableApi) {

                            var location = new google.maps.LatLng(lat, lng);

                            var locate = "yes";

                            zip_obj.convert_latlng_address(location, locate, 'locate-me');

                        } else {

                            zip_obj.showshortcode_notification('notsupport');

                        }

                    }, function(showError) {

                        zip_obj.showshortcode_notification('browser_error', showError.message);



                    });

                } else {

                    zip_obj.showshortcode_notification('notsupport');

                }

            });

        },



        shortcode_loader: function(button) {

            jQuery(button).addClass('loadinggif');

        },



        shortcodeAjax: function(zip_data) {

            var temp_Zipcode = zip_data.zipcode;
            if(temp_Zipcode){
                zip_data.zipcode = temp_Zipcode.trim();
            }

            var zip_obj = this;

            jQuery.ajax({

                type: "POST",

                url: wdap_settings_obj.ajax_url,

                datatype: 'json',

                data: zip_data,

                success: function(data) {

                    var response = JSON.parse(data);

                    if (response.status == "found") {

                        zip_obj.showshortcode_notification('yes');

                    }

                    if (response.status == "notfound") {

                        zip_obj.shortcode_response(response);

                    }

                }

            });

        },



        shortcode_response: function(response) {

            var coordinatelen = Object.keys(response.coordinatematch).length;

            if (coordinatelen > 0) {

                this.checkcoordinateandshowresult(response, '');

            } else {

                this.showshortcode_notification('no');

            }

        },



        cart_and_checkout_response: function(response, button_obj) {

            var zip_obj = this;

            var cartdata = response.cartdata;

            for (var i = 0; i < cartdata.length; i++) {

                if (cartdata[i].status == 'found') {

                    var msg = '<span class="avilable">' + wdap_settings_obj.errormessages.a + '</span>';

                    zip_obj.showresultcartcheckout(cartdata[i].id, msg);

                    var id = cartdata[i].id;

                    var singlresult = {

                        id: id,

                        value: "YES"

                    };

                    zip_obj.hiddenresult.push(singlresult);

                    jQuery(button_obj).parent().find(".wdap_start").val('yes');

                } else {

                    if (response.zipcodestring.length > 0) {

                        if (cartdata[i].status == 'notfound' && (zip_obj.enableApi)) {

                            var coordinatelen = Object.keys(cartdata[i].coordinatematch).length;

                            if ((coordinatelen > 0)) {

                                var result = zip_obj.cartcheckcoordinates(cartdata[i].coordinatematch, response.zipcodestring, cartdata[i].id);

                                zip_obj.showresultcartcheckout(cartdata[i].id, result.markup);

                                jQuery(button_obj).parent().find(".wdap_start").val('yes');

                            } else {

                                var msg = '<span class="notavilable">' + wdap_settings_obj.errormessages.na + '</span>';

                                zip_obj.showresultcartcheckout(cartdata[i].id, msg);

                                var singlresult = {

                                    id: id,

                                    value: "NO"

                                };

                                zip_obj.hiddenresult.push(singlresult);

                                jQuery(button_obj).parent().find(".wdap_start").val('yes');

                            }

                        } else {

                            var msg = '<span class="notavilable">' + wdap_settings_obj.errormessages.na + '</span>';

                            zip_obj.showresultcartcheckout(cartdata[i].id, msg);

                            var singlresult = {

                                id: id,

                                value: "NO"

                            };

                            zip_obj.hiddenresult.push(singlresult);

                            jQuery(button_obj).parent().find(".wdap_start").val('yes');

                        }

                    } else {

                        var msg = '<span class="notavilable">' + wdap_settings_obj.errormessages.na + '</span>';

                        zip_obj.showresultcartcheckout(cartdata[i].id, msg);

                        var singlresult = {

                            id: id,

                            value: "NO"

                        };

                        zip_obj.hiddenresult.push(singlresult);



                        zip_obj.remove_ajax_loader('.wdapzipsumit');

                        jQuery(button_obj).parent().find(".wdap_start").val('yes');

                    }

                }

            } //End of for loop 

            zip_obj.cartMessage(button_obj);

        },

        cartMessage: function(button_obj) {



            var zip_obj = this;

            var found = 0;

            var notfound = 0;



            for (var i = 0; i < zip_obj.hiddenresult.length; i++) {



                if (zip_obj.hiddenresult[i].value == 'YES')

                    found++;

                if (zip_obj.hiddenresult[i].value == 'NO')

                    notfound++;



            }



            $("#Chkziptestresult").val('');

            $("#Chkziptestresult").val(JSON.stringify(zip_obj.hiddenresult));

            if ($('.new_submit').length > 0) {

                $('form.checkout').removeClass('processing').unblock().submit();

            }

            zip_obj.hiddenresult = [];

            if (zip_obj.hiddenresult) {

                var warningmessage = '';

                if (found == 0)

                    warningmessage = '<span style="color:' + wdap_settings_obj.errormessages.error_msg_color + ';">' + wdap_settings_obj.errormessages.na + '</span>';

                if (notfound == 0)

                    warningmessage = '<span style="color:' + wdap_settings_obj.errormessages.success_msg_color + ';">' + wdap_settings_obj.errormessages.a + '</span>';

                if (found != 0 && notfound != 0){
                     var replaceData = {
                            "{no_products_available}": found,
                            "{no_products_unavailable}": notfound
                        };

                        var temp_listing_placeholder = wdap_settings_obj.errormessages.summary;        

                        temp_listing_placeholder = temp_listing_placeholder.replace(/{[^{}]+}/g, function(match) {
                            if (match in replaceData) {
                                return (replaceData[match]);
                            } else {
                                return ("");
                            }
                        });
                      warningmessage = '<span style="color:' + wdap_settings_obj.errormessages.error_msg_color + ';">'+temp_listing_placeholder+'.</span>';
                }

                $(button_obj).parent().parent().find(".wdap_notification_message").show().html(warningmessage);

            }



            jQuery(button_obj).parent().find(".wdap_start").val('yes');

        },

        convert_latlng_address: function(location, locate, action) {



            var zip_obj = this;

            var converted_zipcode = zip_obj.unique_form_container.find(".convertedzipcode").val();

            var locate, zipcode;

            var geocoder = new google.maps.Geocoder();

            geocoder.geocode({

                'latLng': location

            }, function(results, status) {

                if (status == google.maps.GeocoderStatus.OK) {

                    if (locate == "yes") {

                        if (results[1] != undefined) {

                            zip_obj.unique_form_container.find("#wdap_type_location").val(results[1].formatted_address);

                        }

                    }



                    if (results[1] != undefined) {

                        var address_components = results[results.length - 1].address_components;

                        for (var i in address_components) {

                            zipcode = address_components[i].long_name;

                        }

                        if (zipcode) {

                            zip_obj.unique_form_container.find(".convertedzipcode").val(zipcode);

                        }



                    }

                }

            });

        },

        showshortcode_notification: function(response, errors){



            var shortcode_settings = wdap_settings_obj.shortcode_settings;

            if (wdap_settings_obj.can_be_delivered_redirect_url != '' && wdap_settings_obj.can_be_delivered_redirect_url != null && response == 'yes') {

                window.location.href = wdap_settings_obj.can_be_delivered_redirect_url;

                return false;

            }



            if (wdap_settings_obj.cannot_be_delivered_redirect_url !== '' && wdap_settings_obj.cannot_be_delivered_redirect_url !== null && response == 'no') {

                window.location.href = wdap_settings_obj.cannot_be_delivered_redirect_url;

                return false;

            }



            jQuery('.check_availability').removeClass('loadinggif');

            var error_container = $(".wdap_product_availity_form").find(".error-container");

            var success_msg_color = shortcode_settings.form_success_msg_color;

            var error_msg_color = shortcode_settings.form_error_msg_color;



            if(this.unique_form_container.length>0){

                this.Serror_container = this.unique_form_container.find(".error-container");

            } 



            switch (response) {

                case 'yes':

                    this.Serror_container.css("background", success_msg_color).text(shortcode_settings.address_shipable).show('medium');

                    break;

                case 'browser_error':

                    this.Serror_container.css("background", error_msg_color).text(errors).show('medium');

                    break;

                case 'no':

                    this.Serror_container.css("background", error_msg_color).text(shortcode_settings.address_not_shipable).show('medium');

                    break;

                case 'empty':

                    this.Serror_container.css("background", error_msg_color).text(shortcode_settings.wdap_address_empty).show('medium');

                    break;

                case 'select_product':

                    this.Serror_container.css("background", error_msg_color).text(shortcode_settings.prlist_error).show('medium');

                    break;

                case 'notsupport':

                    this.Serror_container.css("background", error_msg_color).text("Geolocation is not supported by this browser.").show('medium');

                    break;

                default:

            }

        },

        checkcoordinateandshowresult: function(response, button_obj) {



            var zip_obj = this;

            var show_error = $(button_obj).parent().parent().find(".wdap_notification_message").show();

            var success_msg_color = wdap_settings_obj.errormessages.success_msg_color;

            var error_msg_color = wdap_settings_obj.errormessages.error_msg_color;

            if (response.status == 'found') {



                zip_obj.wdap_display_result(button_obj, 1);

            }

            if (response.coordinatematch) {

                var coordinatelen = Object.keys(response.coordinatematch).length;



                if (response.status == 'notfound' && (coordinatelen > 0)) {

                    if (response.zipcodestring.length == 0) {



                        zip_obj.wdap_display_result(button_obj, 0);

                        zip_obj.showshortcode_notification('no');



                    } else {

                        if (coordinatelen > 0) { //If atleast one polygon is drawn

                            if (zip_obj.enableApi) {

                                zip_obj.checkcoordinates(response.coordinatematch, response.zipcodestring, button_obj);

                            } else {



                                zip_obj.wdap_display_result(button_obj, 0);



                            }

                        } else {

                            zip_obj.wdap_display_result(button_obj, 0);



                        }

                    }

                }

            }

            if (response.status == 'notfound' && (coordinatelen == 0)) {



                zip_obj.remove_ajax_loader(button_obj);

                show_error.css("color", error_msg_color).text(wdap_settings_obj.errormessages.na);

                jQuery(button_obj).parent().find(".wdap_start").val('yes');

            }

        },

        checkcoordinates: function(coordinates, zipcode, button_obj) {



            var zip_obj = this;

            var result;

            var latitude;

            var longitude;

            var ziparray = jQuery.makeArray(zipcode);

            var geocoder = new google.maps.Geocoder();



            for (var i = 0; i < ziparray.length; i++) {



                latitude = ziparray[i].lat;

                longitude = ziparray[i].lng;



                for (var j = 0; j < coordinates.length; j++) {



                    var singlepolygon = new google.maps.Polygon({

                        paths: coordinates[j]

                    });

                    result = google.maps.geometry.poly.containsLocation(new google.maps.LatLng(latitude, longitude), singlepolygon);

                    if (result) {



                        zip_obj.wdap_display_result(button_obj, 1);

                        if (jQuery('.check_availability').length > 0) {

                            zip_obj.showshortcode_notification('yes');

                        }

                        jQuery(button_obj).parent().find(".wdap_start").val('yes');

                        return false;

                    } else {

                        zip_obj.wdap_display_result(button_obj, 0);

                    }



                }

            }

            if (jQuery('.check_availability').length > 0) {

                zip_obj.showshortcode_notification('no');

            }

        },



        showresultcartcheckout: function(id, msg) {



            var zip_obj = this;

            $(".shop_table tbody tr").each(function(j) {

                if (typeof $(this).attr('class') != 'undefined') {

                    var classes = $(this).attr('class').split(' ');

                    id = id.toString();

                    if ($.inArray(id, classes) !== -1) {

                        zip_obj.remove_ajax_loader('.wdapzipsumit');

                        if ($(this).find('td:last').hasClass('product_avalibility_tab')) {

                            $(this).find('td:last').html(msg);

                        } else {

                            $(this).append('<td class="product_avalibility_tab">' + msg + '</td>');

                        }

                    }

                } else {

                    if (!$(this).find('td:last').hasClass('placeholdertd')) {

                        $(this).append('<td class="placeholdertd"></td>');

                    }

                }

            });

            $(".shop_table tfoot tr").each(function(j) {

                if (!$(this).find('td:last').hasClass('placeholdertd')) {

                    $(this).append('<td class="placeholdertd"></td>');

                }

            });

        },

        cartcheckcoordinates: function(coordinates, zipcode, id) {



            var zip_obj = this;

            var found = "";

            var result;

            var latitude;

            var longitude;

            var responseobj;

            var ziparray = jQuery.makeArray(zipcode);

            var geocoder = new google.maps.Geocoder();



            for (var i = 0; i < ziparray.length; i++) {

                latitude = ziparray[i].lat;

                longitude = ziparray[i].lng;





                for (var j = 0; j < coordinates.length; j++) {



                    var singlepolygon = new google.maps.Polygon({

                        paths: coordinates[j]

                    });

                    result = google.maps.geometry.poly.containsLocation(new google.maps.LatLng(latitude, longitude), singlepolygon);

                    if (result) {

                        zip_obj.remove_ajax_loader('.wdapzipsumit');

                        $("#Chkziptestresult").val("YES");

                        found = "<span class='avilable'>" + wdap_settings_obj.errormessages.a + "</span>";

                        var singlresult = {

                            id: id,

                            value: "YES"

                        };

                        this.hiddenresult.push(singlresult);

                        responseobj = {

                            'markup': found

                        };

                        return responseobj;

                    } else {



                        zip_obj.remove_ajax_loader('.wdapzipsumit');

                        $("#Chkziptestresult").val("NO");

                        found = "<span class='notavilable'>" + wdap_settings_obj.errormessages.na + "</span>";

                    }



                }

            }



            var singlresult = {

                id: id,

                value: "NO"

            };

            this.hiddenresult.push(singlresult);

            responseobj = {

                'markup': found

            };

            return responseobj;

        },



        getIdSeparator: function(checkPrefix) {



            if (!document.getElementById(checkPrefix + '_address_1')) {

                this.IdSeparator = "_";

                return "_";

            }

            this.IdSeparator = ":";

            return ":";



        },

        setCustomResponse: function(place) {



            var custom_place = {

                address_components: '',

                formatted_address: '',

                geometry: ''

            };



            var address_components =  place.address_components;



            for (var i = 0; i < address_components.length; i++) {



                var long_name = address_components[i].long_name;

                var short_name = address_components[i].short_name;

                long_name =  long_name.replace(/"/g, ""); 

                long_name =  long_name.replace(/'/g, ""); 

                short_name =  short_name.replace(/"/g, ""); 

                short_name =  short_name.replace(/'/g, ""); 



                address_components[i].long_name = long_name;

                address_components[i].short_name = short_name;



            }



            var formatted_address = place.formatted_address;

            formatted_address =  formatted_address.replace(/"/g, ""); 

            formatted_address =  formatted_address.replace(/'/g, ""); 

                



            if (place != undefined) {

                custom_place.address_components = address_components;

                custom_place.formatted_address = formatted_address;

                custom_place.geometry = place.geometry;

            }

            return custom_place;



        },



        fillInAddress: function(checkPrefix) {



            var zip_obj = this;

            zip_obj.clearFormValues(checkPrefix);

            var place = zip_obj.autocomplete[checkPrefix].getPlace();



            zip_obj.checkoutPlace[checkPrefix] = zip_obj.setCustomResponse(place);

            zip_obj.resetForm(checkPrefix);

            var type = '';

            for (var field in place.address_components) {

                for (var t in place.address_components[field].types) {

                    for (var f in zip_obj.component_form[checkPrefix]) {

                        var types = place.address_components[field].types;

                        if (f == types[t]) {

                            if (f == "administrative_area_level_1") {

                                if (document.getElementById(checkPrefix + "_country").value == "GB") {

                                    continue;

                                }

                            }

                            var prop = zip_obj.component_form[checkPrefix][f][1];

                            if (place.address_components[field].hasOwnProperty(prop)) {



                                var replace_space = place.address_components[field][prop];

                                replace_space =  replace_space.replace(/"/g, ""); 

                                zip_obj.formFieldsValue[checkPrefix][zip_obj.component_form[checkPrefix][f][0]] = replace_space;

                            }

                        }

                    }

                }

            }

            zip_obj.streetNumber = place.name;

            zip_obj.appendStreetNumber(checkPrefix);

            zip_obj.fillForm(checkPrefix);

            jQuery("#" + checkPrefix + "_state").trigger("change");

        },



        clearFormValues: function(checkPrefix) {

            for (var f in this.formFieldsValue[checkPrefix]) {

                this.formFieldsValue[checkPrefix][f] = '';

            }

        },



        appendStreetNumber: function(checkPrefix) {

            if (this.streetNumber != '') {

                this.formFieldsValue[checkPrefix][checkPrefix + '_address_1'] = this.streetNumber

            }

        },



        fillForm: function(checkPrefix) {

            for (var f in this.formFieldsValue[checkPrefix]) {

                if (f == checkPrefix + '_country') {

                    this.selectRegion(f, this.formFieldsValue[checkPrefix][f]);

                } else {

                    if (document.getElementById((f)) === null) {

                        continue;

                    } else {

                        document.getElementById((f)).value = this.formFieldsValue[checkPrefix][f];

                    }

                }

            }

        },



        selectRegion: function(id, regionText) {

            if (document.getElementById((id)) == null) {

                return false;

            }

            var el = document.getElementById((id));

            if (el.tagName == 'select') {

                for (var i = 0; i < el.options.length; i++) {

                    if (el.options[i].text == regionText) {

                        el.selectedIndex = i;

                        break;

                    }

                }

            }

        },

        resetForm: function(checkPrefix) {

            if (document.getElementById((checkPrefix + '_address_2')) !== null) {

                document.getElementById((checkPrefix + '_address_2')).value = '';

            }

        },

        setAutocompleteCountry: function(checkPrefix) {



            var mapsetting = this.options.mapsettings;

            var country1 = document.getElementById(checkPrefix + '_country').value;

            var country = '';

            if (mapsetting.enable_restrict) {

                country = mapsetting.restrict_country;

            } else if (country1) {

                country = document.getElementById(checkPrefix + '_country').value;

            } else {

                country = 'US';

            }

            this.autocomplete[checkPrefix].setComponentRestrictions({

                'country': country

            });

        }

    };



    $.fn.zipcode_test = function(options) {

        new zipcode_testing(this, options);

    };



    $.fn.deliveryMap = function(map_data) {

        this.each(function() {

            if (!$.data(this, "wdap_delivery_map")) {

                if (typeof google !== 'undefined') {

                    var plugin_settings = wdap_settings_obj;

                    $.data(this, "wdap_delivery_map", new polygonConfig(this, plugin_settings, map_data));

                }

            }

        });

        // chain jQuery functions

        return this;

    };





    jQuery(document).ready(function($) {



        if (typeof wdap_settings_obj !== "undefined") {

            var options = wdap_settings_obj;

            $(".zipcode_check_params").zipcode_test(options);

            if (jQuery('.form_product_list').length > 0)

                $(".form_product_list").select2({ dropdownCssClass:'wdap_standard_design' });

        }



    });







})(jQuery, window, document);



