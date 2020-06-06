(function($, window, document, undefined) {

    'use strict';

    function Design_Polygon(element, options) {
        this.options = options;
        this.init();
        this.completepolygonscordinates = [];
        this.drawingManager;
        this.selectedShape;
        this.map;
        this.obj;
        this.savedpolygon = new Array();
    }

    Design_Polygon.prototype = {

        init: function() {

            var poly_obj = this.obj = this;
            poly_obj.mapSetup();
            poly_obj.autosuggestion();
            poly_obj.setupdrawingManager();
            poly_obj.newPolygonConfig();
            poly_obj.configSavedPolygon();
            poly_obj.drawSavedStore();
            poly_obj.drawSavedPolygon();
            var map = poly_obj.map;
            poly_obj.drawingManager.setMap(map);
            poly_obj.drwaNewPolygon();
            poly_obj.polygon_event_handler();
            poly_obj.init_configuration();

        },
        mapSetup: function() {

            var poly_obj = this;
            var map;
            var centerlat = wdap_backend_obj.mapsettings.centerlat > 0 ? parseFloat(wdap_backend_obj.mapsettings.centerlat) : 40.73061;
            var centerlng = wdap_backend_obj.mapsettings.centerlng.length > 0 ? parseFloat(wdap_backend_obj.mapsettings.centerlng) : -73.935242;
            var zoom = wdap_backend_obj.mapsettings.zoom.length > 0 ? parseInt(wdap_backend_obj.mapsettings.zoom) : 5;
            var style = wdap_backend_obj.mapsettings.style;
            poly_obj.map = new google.maps.Map(
                document.getElementById("wdappolygons"), {
                    center: new google.maps.LatLng(centerlat, centerlng),
                    zoom: zoom,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                });

            map = poly_obj.map;
            if (style != '') {
                map.setOptions({
                    styles: eval(style)
                });
            }
        },
        polygon_event_handler: function() {
            var poly_obj = this;
            var map = poly_obj.map;
            google.maps.event.addListener(poly_obj.drawingManager, 'drawingmode_changed', function() {
                poly_obj.clearSelection(poly_obj);
            });
            google.maps.event.addListener(map, 'click', function() {
                poly_obj.clearSelection(poly_obj);
            });
            google.maps.event.addDomListener(document.getElementById('wdap-shape-delete'), 'click', function() {
                poly_obj.deleteSelectedShape(poly_obj);
            });
        },
        drawSavedStore:function(){
            var poly_obj = this;
            var allstorelocations = wdap_backend_obj.store_information;

            if(allstorelocations != undefined &&  allstorelocations.lat !='' && allstorelocations.lng !='' ){

                    var storeLatLng = {
                            lat: allstorelocations.lat,
                            lng: allstorelocations.lng
                        };


                  var storeCircle = new google.maps.Circle({
                    strokeColor: allstorelocations.format.strokeColor,
                    strokeOpacity: allstorelocations.format.strokeOpacity,
                    strokeWeight: allstorelocations.format.strokeWeight,
                    fillColor: allstorelocations.format.fillColor,
                    fillOpacity: allstorelocations.format.fillOpacity,
                    map: poly_obj.map,
                    center: storeLatLng,
                    radius: (allstorelocations.range) * 100
                  });

                                    
                var marker = new google.maps.Marker({
                    position: storeLatLng,
                    map: poly_obj.map,
                   icon: wdap_backend_obj.icon_url,

                });
                poly_obj.map.fitBounds(storeCircle.getBounds());
                poly_obj.drawingManager.set('drawingMode');


            
            }
        },
        drawSavedPolygon: function() {
            var poly_obj = this;
            var onepolygondraw = this.savedpolygon;
            var showpolycoordinate = [];
            
            for (var i = 0, l = onepolygondraw.length; i < l; i++) {


                google.maps.event.addListener(onepolygondraw[i], 'click', function(event) {

                    $('.polygon_property').find('.hiderow').show();
                    $("#wdap_shape_path").val('');
                    poly_obj.setSelection(this);
                    $("#wdap_shape_path").val('');
                    for (var i = 0; i < this.getPath().getLength(); i++) {
                        var values = this.getPath().getAt(i).toUrlValue(6).split(',');
                        var lat = values[0];
                        var lng = values[1];
                        var custLatlng = lat + ',' + lng + "\n";
                        var curVal = $("#wdap_shape_path").val();
                        $("#wdap_shape_path").val(curVal + "" + custLatlng);
                        var singlevertex = {
                            lat: lat,
                            lng: lng
                        };
                        showpolycoordinate.push(singlevertex);
                    }


                    $(".wpdap_fill_color").iris('color', this.format.fillColor);
                    $('.wpdap_stroke_color').iris('color', this.format.strokeColor);

                    $('#wdap_shape_stroke_weight').select2('destroy');
                    $('#wdap_shape_stroke_weight').val(this.format.strokeWeight).select2();


                    $('#wdap_shape_stroke_opacity').select2('destroy');
                    $('#wdap_shape_stroke_opacity').val(this.format.strokeOpacity).select2();

                    $('#wdap_shape_fill_opacity').select2('destroy');
                    $('#wdap_shape_fill_opacity').val(this.format.fillOpacity).select2();


                    if ($('#' + this.id).data('redirecturl')) {
                        $("#wdap_shape_click_url").val($('#' + this.id).data('redirecturl'));
                    } else {
                        $("#wdap_shape_click_url").val('');

                    }
                    if ($('#' + this.id).data('infomessage')) {
                        var savedhtmlmsg = $('#' + this.id).data('infomessage')
                        var decodedsavedhtmlmsg = window.atob(savedhtmlmsg);

                        $("#wdap_shape_click_message").val(decodedsavedhtmlmsg);
                    } else {
                        $("#wdap_shape_click_message").val('');
                    }
                    var setatcoordinate = [];
                    var insertatcoordinate = [];
                    var newshape = this.getPath();
                    google.maps.event.addListener(newshape, 'insert_at', function() {
                        $("#wdap_shape_path").val('');
                        for (var i = 0; i < poly_obj.selectedShape.getPath().getLength(); i++) {
                            var values = poly_obj.selectedShape.getPath().getAt(i).toUrlValue(6).split(',');
                            var lat = values[0];
                            var lng = values[1];
                            var custLatlng = lat + ',' + lng + "\n";
                            var curVal = $("#wdap_shape_path").val();
                            $("#wdap_shape_path").val(curVal + "" + custLatlng);
                            var singlevertex = {
                                lat: lat,
                                lng: lng
                            };
                            insertatcoordinate.push(singlevertex);
                        }
                        poly_obj.checkchangeattribute(poly_obj.selectedShape, 'resize', insertatcoordinate);
                        insertatcoordinate = [];
                    });
                    google.maps.event.addListener(newshape, 'set_at', function() {
                        $("#wdap_shape_path").val('');
                        for (var i = 0; i < poly_obj.selectedShape.getPath().getLength(); i++) {
                            var values = poly_obj.selectedShape.getPath().getAt(i).toUrlValue(6).split(',');
                            var lat = values[0];
                            var lng = values[1];
                            var custLatlng = lat + ',' + lng + "\n";
                            var curVal = $("#wdap_shape_path").val();
                            $("#wdap_shape_path").val(curVal + "" + custLatlng);
                            var singlevertex = {
                                lat: lat,
                                lng: lng
                            };
                            setatcoordinate.push(singlevertex);
                        }
                        poly_obj.checkchangeattribute(poly_obj.selectedShape, 'resize', setatcoordinate);
                        setatcoordinate = [];
                    });
                });
            }
        },
        newPolygonConfig: function(){

            var poly_obj = this;
            google.maps.event.addListener(poly_obj.drawingManager, 'polygoncomplete', function(polygon) {
                var singlepolygoncordinate = [];
                var singleonlycoordinate = [];
                for (var i = 0; i < polygon.getPath().getLength(); i++) {
                    var values = polygon.getPath().getAt(i).toUrlValue(6).split(',');
                    var lat = values[0];
                    var lng = values[1];
                    var singlevertex = {
                        lat: lat,
                        lng: lng
                    };
                    singleonlycoordinate.push(singlevertex);
                }
                var custompolyid = Math.floor((Math.random() * 10000000) + 1);
                var polygonOptions = poly_obj.drawingManager.get('polygonOptions');
                polygonOptions.id = custompolyid;
                var final = {
                    'id': custompolyid,
                    'coordinate': singleonlycoordinate,
                    'polygon_formatting': polygonOptions,
                    'popygon_all_properties': '',
                };
                singlepolygoncordinate.push(final);
                var existingpolygons = $("#polygons_json").val();
                if (existingpolygons.length > 2) {
                    var existingpolygonsArray = eval('(' + existingpolygons + ')');
                    for (var i = 0; i < existingpolygonsArray.length; i++) {
                        poly_obj.completepolygonscordinates.push(existingpolygonsArray[i]);
                    }
                }
                polygon.id = custompolyid;
                poly_obj.completepolygonscordinates.push(singlepolygoncordinate);
                poly_obj.drawingManager.set('drawingMode');
                
                $('#addcollection').append("<input type='hidden' id='" + custompolyid + "' data-strokecolor='" + polygonOptions.strokeColor + "' data-strokeOpacity='" +polygonOptions.strokeOpacity+ "' data-fillColor='" + polygonOptions.fillColor + "' data-fillopacity='" + polygonOptions.fillOpacity + "' data-strokeweight='" + polygonOptions.strokeWeight + "' data-redirecturl=''data-infomessage='' >");
            });
        },
        configSavedPolygon: function() {
            var setting = this.options.polygons;
            var poly_obj = this;
            var map = poly_obj.map;
            var savedpolygon = new Array();
            if (setting) {
                var bounds = new google.maps.LatLngBounds();
                for (var i = 0, l = setting.length; i < l; i++) {

                    savedpolygon[i] = new google.maps.Polygon({
                        paths: $.makeArray(setting[i].coordinate),
                        strokeColor: setting[i].format.strokeColor,
                        strokeOpacity: setting[i].format.strokeOpacity,
                        strokeWeight: setting[i].format.strokeWeight,
                        fillColor: setting[i].format.fillColor,
                        fillOpacity: setting[i].format.fillOpacity,
                        id: setting[i].id
                    });

                    savedpolygon[i].format = setting[i].format;

                    var url = '';
                    var infomessage = '';
                    if (setting[i].format.redirectUrl) {
                        url = setting[i].format.redirectUrl;
                    }
                    if (setting[i].format.infoWindow) {
                        infomessage = setting[i].format.infoWindow;
                    }
                    $('#addcollection').append("<input type='hidden' id='" + setting[i].id + "' data-strokecolor='" + setting[i].format.strokeColor + "' data-strokeOpacity='" + setting[i].format.strokeOpacity + "' data-fillColor='" + setting[i].format.fillColor + "' data-fillopacity='" + setting[i].format.fillOpacity + "' data-strokeweight='" + setting[i].format.strokeWeight + "'data-redirecturl='" + url + "'data-infomessage='" + infomessage + "' >");
                    savedpolygon[i].setMap(map);
                    var mynewpoly = setting[i].coordinate;
                    var testarr = [];
                    for (var g = 0; g < mynewpoly.length; g++) {
                        testarr.push(new google.maps.LatLng(mynewpoly[g].lat, mynewpoly[g].lng));
                        bounds.extend(testarr[testarr.length - 1]);
                    }
                    map.fitBounds(bounds);
                    poly_obj.drawingManager.set('drawingMode');
                }
            }
            this.savedpolygon = savedpolygon;
        },
        drwaNewPolygon: function() {

            var poly_obj = this;
            google.maps.event.addListener(poly_obj.drawingManager, 'overlaycomplete', function(e) {
                var newShape = e.overlay;
                newShape.type = e.type;
                if (e.type !== google.maps.drawing.OverlayType.MARKER) {
                    poly_obj.drawingManager.setDrawingMode(null);
                    var showpolycoordinate = [];
                    var insertatcoordinate = [];
                    var setatcoordinate = [];
                    google.maps.event.addListener(newShape.getPath(), 'set_at', function() {
                        $("#wdap_shape_path").val('');
                        for (var i = 0; i < newShape.getPath().getLength(); i++) {
                            var values = newShape.getPath().getAt(i).toUrlValue(6).split(',');
                            var lat = values[0];
                            var lng = values[1];
                            var custLatlng = lat + ',' + lng + "\n";
                            var curVal = $("#wdap_shape_path").val();
                            $("#wdap_shape_path").val(curVal + "" + custLatlng);
                            var singlevertex = {
                                lat: lat,
                                lng: lng
                            };
                            setatcoordinate.push(singlevertex);
                        }
                        poly_obj.newpolygresize(newShape, poly_obj.completepolygonscordinates, setatcoordinate);
                        setatcoordinate = [];
                    });
                    google.maps.event.addListener(newShape.getPath(), 'insert_at', function() {
                        $("#wdap_shape_path").val('');
                        for (var i = 0; i < newShape.getPath().getLength(); i++) {
                            var values = newShape.getPath().getAt(i).toUrlValue(6).split(',');
                            var lat = values[0];
                            var lng = values[1];
                            var custLatlng = lat + ',' + lng + "\n";
                            var curVal = $("#wdap_shape_path").val();
                            $("#wdap_shape_path").val(curVal + "" + custLatlng);
                            var singlevertex = {
                                lat: lat,
                                lng: lng
                            };
                            insertatcoordinate.push(singlevertex);
                        }
                        poly_obj.newpolygresize(newShape, poly_obj.completepolygonscordinates, insertatcoordinate);
                        insertatcoordinate = [];
                    });
                    google.maps.event.addListener(newShape, 'click', function(e) {
                        poly_obj.setSelection(newShape);
                        $("#wdap_shape_path").val('');
                        for (var i = 0; i < newShape.getPath().getLength(); i++) {
                            var values = newShape.getPath().getAt(i).toUrlValue(6).split(',');
                            var lat = values[0];
                            var lng = values[1];
                            var custLatlng = lat + ',' + lng + "\n";
                            var curVal = $("#wdap_shape_path").val();
                            $("#wdap_shape_path").val(curVal + "" + custLatlng);
                            var singlevertex = {
                                lat: lat,
                                lng: lng
                            };
                            showpolycoordinate.push(singlevertex);
                        }
                        var polygonsoptons = poly_obj.drawingManager.get('polygonOptions');
                        $("#wdap_shape_click_url").val('');
                        $("#wdap_shape_click_message").val('');
                        $(".wpdap_fill_color").iris('color', $('#' + this.id).attr('data-fillcolor'));
                        $('.wpdap_stroke_color').iris('color', $('#' + this.id).attr('data-strokecolor'));
                            
                        
                        
                        $('#wdap_shape_stroke_weight').select2('destroy');
                        $('#wdap_shape_stroke_weight').val($('#' + this.id).attr('data-strokeweight')).select2();
                        
                        $('#wdap_shape_stroke_opacity').select2('destroy');
                        $('#wdap_shape_stroke_opacity').val($('#' + this.id).attr('data-strokeopacity')).select2();
                        
                        $('#wdap_shape_fill_opacity').select2('destroy');
                        $('#wdap_shape_fill_opacity').val($('#' + this.id).attr('data-fillopacity')).select2();



                        $("#wdap_shape_click_url").val($('#' + this.id).attr('data-redirecturl'));
                        var htmlmsg = $('#' + this.id).attr('data-infomessage');
                        if (htmlmsg != '')
                            htmlmsg = window.atob(htmlmsg);
                        $("#wdap_shape_click_message").val(htmlmsg);
                        $('.polygon_property').find('.hiderow').show();
                    });
                } else {
                    google.maps.event.addListener(newShape, 'click', function(e) {
                        poly_obj.setSelection(newShape);
                    });
                    poly_obj.setSelection(newShape);
                }
            });
        },
        setupdrawingManager: function() {

            var poly_obj = this;
            var fillcolor = $('.wpdap_fill_color').val();
            var strolecolor = $('.wpdap_stroke_color').val();
            var strokeWeight = $('#wdap_shape_stroke_weight').val();
            var stroleopactiry = $('#wdap_shape_stroke_opacity').val();
            var fillOpacity = $('#wdap_shape_fill_opacity').val();

            poly_obj.drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [
                        google.maps.drawing.OverlayType.POLYGON
                    ]
                },
                polygonOptions: {
                    fillColor: fillcolor,
                    fillOpacity: fillOpacity,
                    strokeWeight: strokeWeight,
                    strokeColor: strolecolor,
                    strokeOpacity: stroleopactiry,
                    clickable: true,
                    editable: true,
                    draggable: false,
                    zIndex: 1
                }
            });

        },
        autosuggestion: function() {

            var map = this.map;
            var input = (document.getElementById('pac-input'));
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);
            var infowindow = new google.maps.InfoWindow();
            var marker = new google.maps.Marker({
                map: map,
                anchorPoint: new google.maps.Point(0, -29)
            });

            autocomplete.addListener('place_changed', function() {
                infowindow.close();
                marker.setVisible(false);
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    window.alert("No details available for input: '" + place.name + "'");
                    return;
                }

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }
                marker.setIcon(({
                    url: wdap_backend_obj.icon_url,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(35, 35)
                }));
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
                infowindow.open(map, marker);

            });

        },
        newpolygresize: function(newShape, completepolygonscordinates, insertatcoordinate) {
            for (var j = 0; j < completepolygonscordinates.length; j++) {
                if (newShape.id == completepolygonscordinates[j][0].id) {
                    completepolygonscordinates[j][0].coordinate = insertatcoordinate;
                }
            }
        },
        init_configuration: function() {

            var poly_obj = this;
            var checkarea = true;
            var filloptions = {
                defaultColor: 'true',
                change: function(event, ui) {
                    var theColor = ui.color.toString();
                    var polygonOptions = poly_obj.drawingManager.get('polygonOptions');
                    polygonOptions.fillColor = theColor;
                    poly_obj.checkchangeattribute(poly_obj.selectedShape, 'fillcolor', theColor);
                    $('#' + poly_obj.selectedShape.id).attr("data-fillcolor", theColor);
                    poly_obj.selectedShape.set('fillColor', theColor);
                },
                clear: function() {},
                hide: true,
                palettes: true
            };
            if ($('.wpdap_fill_color').length > 0) {
                $('.wpdap_fill_color').wpColorPicker(filloptions);
            }
            /*Stroke settings*/
            var strokeoptions = {
                defaultColor: true,
                change: function(event, ui) {
                    var theColor = ui.color.toString();
                    var polygonOptions = poly_obj.drawingManager.get('polygonOptions');
                    polygonOptions.fillColor = theColor;
                    poly_obj.checkchangeattribute(poly_obj.selectedShape, 'strokecolor', theColor);
                    $('#' + poly_obj.selectedShape.id).attr("data-strokecolor", theColor);
                    poly_obj.selectedShape.set('strokeColor', theColor);
                },
                clear: function() {},
                hide: true,
                palettes: true
            };
            if ($('.wpdap_stroke_color').length > 0) {
                $('.wpdap_stroke_color').wpColorPicker(strokeoptions);
            }
            $('#wdap_shape_stroke_weight').change(function(e) {
                if (poly_obj.selectedShape) {
                    var weight = $(this).val();
                    var polygonOptions = poly_obj.drawingManager.get('polygonOptions');
                    polygonOptions.strokeWeight = weight;
                    poly_obj.checkchangeattribute(poly_obj.selectedShape, 'strokeweight', weight);
                    $('#' + poly_obj.selectedShape.id).attr("data-strokeweight", weight);
                    poly_obj.selectedShape.set('strokeWeight', weight);
                }

            });

            //Stroke Opacity
            $('#wdap_shape_stroke_opacity').change(function(e) {
                if (poly_obj.selectedShape) {
                    var Opacity = $(this).val();
                    var polygonOptions = poly_obj.drawingManager.get('polygonOptions');
                    polygonOptions.strokeOpacity = Opacity;
                    poly_obj.checkchangeattribute(poly_obj.selectedShape, 'strokeopacity', Opacity);
                    $('#' + poly_obj.selectedShape.id).attr("data-strokeopacity", Opacity);
                    poly_obj.selectedShape.set('strokeOpacity', Opacity);
                }
            });

            //Fill Opacity
            $('#wdap_shape_fill_opacity').change(function(e) {
                if (poly_obj.selectedShape) {
                    var fillOpacity = $(this).val();
                    var polygonOptions = poly_obj.drawingManager.get('polygonOptions');
                    polygonOptions.fillOpacity = fillOpacity;
                    poly_obj.checkchangeattribute(poly_obj.selectedShape, 'fillopacity', fillOpacity);
                    $('#' + poly_obj.selectedShape.id).attr("data-fillopacity", fillOpacity);
                    poly_obj.selectedShape.set('fillOpacity', fillOpacity);
                }

            });

            //Fill Opacity
            $('#wdap_shape_path').change(function(e) {
                if (poly_obj.selectedShape) {
                    var singlepolygoncordinate = [];
                    var strVal = $(this).val();
                    strVal = strVal.replace(/\n/g, ",").replace(/\r/g, "");
                    var strArray = strVal.split(',');
                    strArray = strArray.filter(Boolean);
                    var updatedshap = [];
                    for (var i = 0; i < strArray.length; i = i + 2) {
                        var lat = parseFloat(strArray[i]);
                        var lng = parseFloat(strArray[i + 1]);
                        var singlevertex = {
                            lat: lat,
                            lng: lng
                        };
                        updatedshap.push(singlevertex);
                    }
                    var onepolygondraw = new google.maps.Polygon({
                        paths: $.makeArray(updatedshap),
                        strokeColor: $('#' + poly_obj.selectedShape.id).data("strokecolor"),
                        strokeOpacity: $('#' + poly_obj.selectedShape.id).data("strokeopacity"),
                        strokeWeight: $('#' + poly_obj.selectedShape.id).data("strokeweight"),
                        fillColor: $('#' + poly_obj.selectedShape.id).data("fillcolor"),
                        fillOpacity: $('#' + poly_obj.selectedShape.id).data("fillopacity")
                    });
                    poly_obj.selectedShape.setMap(null);
                    var polygonOptions = poly_obj.drawingManager.get('polygonOptions');
                    var final = {
                        'id': poly_obj.selectedShape.id,
                        'coordinate': updatedshap,
                        'polygon_formatting': polygonOptions,
                        'popygon_all_properties': ''
                    };
                    singlepolygoncordinate.push(final);
                    onepolygondraw.id = poly_obj.selectedShape.id;
                    onepolygondraw.setMap(poly_obj.map);
                    poly_obj.selectedShape = onepolygondraw;
                    poly_obj.selectedShape.setEditable(true);
                    for (var j = 0; j < poly_obj.completepolygonscordinates.length; j++) {
                        if (onepolygondraw.id == poly_obj.completepolygonscordinates[j][0].id) {
                            poly_obj.completepolygonscordinates.splice(j, 1);
                            poly_obj.completepolygonscordinates.push(singlepolygoncordinate);
                        }
                    }
                    poly_obj.checkchangeattribute(poly_obj.selectedShape, 'coordinatepath', updatedshap);
                }
            });
            $("#wdap_shape_click_url").focusout(function() {
                if (poly_obj.selectedShape) {
                    $('#' + poly_obj.selectedShape.id).attr("data-redirecturl", $(this).val());
                    poly_obj.checkchangeattribute(poly_obj.selectedShape, 'urlchange', $(this).val());
                }

            });

            $("#wdap_shape_click_message").focusout(function() {
                if (poly_obj.selectedShape) {
                    var htmlMessage = $(this).val();
                    var encodedhtml = window.btoa(htmlMessage); // encode a string
                    $('#' + poly_obj.selectedShape.id).attr("data-infomessage", encodedhtml);
                    poly_obj.checkchangeattribute(poly_obj.selectedShape, 'messagechange', encodedhtml);
                }
            });

            $('#wdap_shape_click_url').focusout(function() {
                var re = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/;

                if ($(this).val()) {
                    if (!re.test($(this).val())) {
                        checkarea = false;
                        $(this).css("border", "2px solid #FF0000");
                    } else {
                        checkarea = true;
                        $(this).css("border", "2px solid #2c3e50");
                    }
                } else {
                    checkarea = true;
                    $(this).css("border", "2px solid #2c3e50");

                }
            });

            $('#addcollection').submit(function(event) {
                var jk = [];

                var complete_coordinate = poly_obj.completepolygonscordinates;

                for (var i = 0; i < complete_coordinate.length; i++) {

                    var polyid = complete_coordinate[i][0].id;
                    var formt = complete_coordinate[i][0].popygon_all_properties;
                    var newformating = {
                        redirectUrl: '',
                        infoWindow: ''
                    };
                    newformating.fillColor = $('#' + polyid).data("fillcolor");
                    newformating.strokeColor = $('#' + polyid).data("strokecolor");
                    newformating.strokeWeight = $('#' + polyid).data("strokeweight");
                    newformating.strokeOpacity = $('#' + polyid).data("strokeopacity");
                    newformating.fillOpacity = $('#' + polyid).data("fillopacity");
                    newformating.redirectUrl = $('#' + polyid).data("redirecturl");
                    var infomessa = $('#' + polyid).data("infomessage");
                    newformating.infoWindow = infomessa;
                    complete_coordinate[i][0].popygon_all_properties = newformating;

                }
                var t = JSON.stringify(poly_obj.completepolygonscordinates);
                var existpoly = $("#polygons_json").val();
                if (t.length == 2 && existpoly.length > 0) {
                    var existingpolygonsArray = eval('(' + existpoly + ')');
                    $("#polygons_json").val(JSON.stringify(existingpolygonsArray));
                } else {
                    $("#polygons_json").val(JSON.stringify(poly_obj.completepolygonscordinates));
                    if (checkarea == false) {
                        event.preventDefault();
                    }
                }
                if (checkarea == false) {
                    event.preventDefault();
                }
                //event.preventDefault();
            });

        },
        setSelection: function(shape) {
            var poly_obj = this;
            if (shape.type !== 'marker') {
                poly_obj.clearSelection(poly_obj);
                poly_obj.selectedShape = shape;
                shape.setEditable(true);
                $('.polygon_property').find('.hiderow').show();
            }
            poly_obj.selectedShape = shape;

        },
        clearSelection: function(poly_obj = '') {
            var poly_obj = this;
            if (poly_obj.selectedShape) {
                if (poly_obj.selectedShape.type !== 'marker') {
                    poly_obj.selectedShape.setEditable(false);
                }
                poly_obj.selectedShape = null;
                $('.polygon_property').find('.hiderow').hide();
            }
        },
        deleteSelectedShape: function(poly_obj = '') {
            var poly_obj = this;
            if (poly_obj.selectedShape) {
                poly_obj.selectedShape.setMap(null);
                for (var i = 0; i < poly_obj.completepolygonscordinates.length; i++) {
                    if (poly_obj.selectedShape.id == poly_obj.completepolygonscordinates[i][0].id) {
                        poly_obj.completepolygonscordinates.splice(i, 1);
                    }
                }
                poly_obj.checkchangeattribute(poly_obj.selectedShape, 'delete', 'nothing');
            }
        },
        checkchangeattribute: function(onepolygondraw, attribute, value) {

            var existingpolygons = $("#polygons_json").val();
            if (existingpolygons) {
                var existingpolygonsArray = eval('(' + existingpolygons + ')');
                for (var j = 0; j < existingpolygonsArray.length; j++) {
                    if (onepolygondraw.id == existingpolygonsArray[j][0].id) {
                        if (attribute == 'fillcolor') {
                            existingpolygonsArray[j][0].popygon_all_properties.fillColor = value;
                        }
                        if (attribute == 'strokecolor') {
                            existingpolygonsArray[j][0].popygon_all_properties.strokeColor = value;
                        }
                        if (attribute == 'strokeweight') {
                            existingpolygonsArray[j][0].popygon_all_properties.strokeWeight = value;
                        }
                        if (attribute == 'strokeopacity') {
                            existingpolygonsArray[j][0].popygon_all_properties.strokeOpacity = value;
                        }
                        if (attribute == 'fillopacity') {
                            existingpolygonsArray[j][0].popygon_all_properties.fillOpacity = value;
                        }
                        if (attribute == 'coordinatepath') {
                            existingpolygonsArray[j][0].coordinate = value;
                        }
                        if (attribute == 'delete') {
                            existingpolygonsArray.splice(j, 1);
                            $('.polygon_property').find('.hiderow').hide();
                        }
                        if (attribute == 'urlchange') {
                            existingpolygonsArray[j][0].popygon_all_properties.redirectUrl = value;
                        }
                        if (attribute == 'messagechange') {
                            existingpolygonsArray[j][0].popygon_all_properties.infoWindow = value;
                        }
                        if (attribute == 'resize') {
                            existingpolygonsArray[j][0].coordinate = value;
                        }
                    }
                }
                $("#polygons_json").val(JSON.stringify(existingpolygonsArray));
            }
        }
    };

    $.fn.Design_Polygon = function(options) {
        if (typeof window.google !== "undefined") {
            new Design_Polygon(this, options);
        }
    };

    jQuery(document).ready(function($) {

        if (typeof wdap_backend_obj !== "undefined") {
            var options = wdap_backend_obj;
            $("#product_avalibility").Design_Polygon(options);
        }

    });



})(jQuery, window, document);