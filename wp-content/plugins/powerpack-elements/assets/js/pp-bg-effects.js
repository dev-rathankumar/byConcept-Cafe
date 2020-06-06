(function ($) {
    "use strict";

    var BgHandler = function ($scope, $) {

		if ( ! $scope.hasClass( 'pp-bg-effects-yes' ) ) {
			return;
		}

		var bg_row_elem    = elementorFrontend.isEditMode() ? $scope.find('.pp-background-wrapper') : $scope,
			section_id     = $scope.data('id'),
			animation_type = bg_row_elem.data('animation-type'),
			style          = '',
			hideMaxWidth   = bg_row_elem.data('hide-max-width'),
			hideMinWidth   = bg_row_elem.data('hide-min-width'),
			winWidth       = $( window ).width(),
			canvas_opacity = 1;

		var prepend_content  = function( canvas_opacity ) {
			if ( ! elementorFrontend.isEditMode() ) {
				var style   = ' style="opacity: ' + canvas_opacity + ';"';
				var content = $( '<div class="pp-background-wrapper" id="pp-background-' + section_id + '"' + style + '></div>' );
				content.insertBefore( bg_row_elem.find('.elementor-container') );
			} else {
				if ( 'particles' !== animation_type || 'nasa' !== animation_type || 'bubble' !== animation_type || 'snow' !== animation_type || 'custom' !== animation_type ) {
					bg_row_elem.css('opacity', canvas_opacity);
				}
			}
		}
		var animation_birds  = function() {
			var	birdBgColor       = bg_row_elem.data( 'bird-bg-color' ),
				birdColor1        = bg_row_elem.data( 'bird-color-1' ),
				birdColor2        = bg_row_elem.data( 'bird-color-2' ),
				birdColorMode     = bg_row_elem.data( 'bird-color-mode' ),
				birdQuantity      = bg_row_elem.data( 'bird-quantity' ),
				birdSize          = bg_row_elem.data( 'bird-size' ),
				birdWingSpan      = bg_row_elem.data( 'bird-wing-span' ),
				birdSpeedLimit    = bg_row_elem.data( 'bird-speed-limit' ),
				birdSeparation    = bg_row_elem.data( 'bird-separation' ),
				birdAlignment     = bg_row_elem.data( 'bird-alignment' ),
				birdCohesion      = bg_row_elem.data( 'bird-cohesion' );

			VANTA.BIRDS({
				el: '#pp-background-' + section_id,
				backgroundColor: new THREE.Color( parseInt ( birdBgColor ) ),
				color1: new THREE.Color( parseInt ( birdColor1 ) ),
				color2: new THREE.Color( parseInt ( birdColor2 ) ),
				colorMode: birdColorMode,
				quantity: birdQuantity,
				birdSize: birdSize,
				wingSpan: birdWingSpan,
				speedLimit: birdSpeedLimit,
				separation: birdSeparation,
				alignment: birdAlignment,
				cohesion: birdCohesion,
			});
		}
		var animation_fog    = function() {
			var	fogHighLightColor = bg_row_elem.data( 'fog-highlight-color' ),
				fogMidtoneColor   = bg_row_elem.data( 'fog-midtone-color' ),
				fogLowlightColor  = bg_row_elem.data( 'fog-lowlight-color' ),
				fogBaseColor      = bg_row_elem.data( 'fog-base-color' ),
				fogBlurFactor     = bg_row_elem.data( 'fog-blur-factor' ),
				fogZoom           = bg_row_elem.data( 'fog-zoom' ),
				fogSpeed          = bg_row_elem.data( 'fog-speed' );

			VANTA.FOG({
				el: '#pp-background-' + section_id,
				highlightColor: new THREE.Color( parseInt ( fogHighLightColor ) ),
				midtoneColor: new THREE.Color( parseInt ( fogMidtoneColor ) ),
				lowlightColor: new THREE.Color( parseInt ( fogLowlightColor ) ),
				baseColor: new THREE.Color( parseInt ( fogBaseColor ) ),
				blurFactor: fogBlurFactor,
				zoom: fogZoom,
				speed: fogSpeed,
			});
		}
		var animation_waves  = function() {
			var	wavesColor = bg_row_elem.data( 'waves-color' ),
				wavesShininess = bg_row_elem.data( 'waves-shininess' ),
				wavesHeight = bg_row_elem.data( 'waves-height' ),
				wavesZoom = bg_row_elem.data( 'waves-zoom' ),
				wavesSpeed = bg_row_elem.data( 'waves-speed' );

			VANTA.WAVES({
				el: '#pp-background-' + section_id,
				shininess: wavesShininess,
				color: new THREE.Color( parseInt ( wavesColor ) ),
				waveHeight: wavesHeight,
				zoom: wavesZoom,
				waveSpeed: wavesSpeed,
			});	
		}
		var animation_net    = function() {
			var netColor       = bg_row_elem.data( 'net-color' ),
				netBgColor     = bg_row_elem.data( 'net-bg-color' ),
				netPoints      = bg_row_elem.data( 'net-points' ),
				netMaxDistance = bg_row_elem.data( 'net-max-distance' ),
				netSpacing     = bg_row_elem.data( 'net-spacing' ),
				netShowDot     = bg_row_elem.data( 'net-show-dot' );

			VANTA.NET({
				el: '#pp-background-' + section_id,
				color: new THREE.Color( parseInt ( netColor ) ),
				backgroundColor: new THREE.Color( parseInt ( netBgColor ) ),
				points: netPoints,
				maxDistance: netMaxDistance,
				spacing: netSpacing,
				showDots: netShowDot,
			});
		}
		var animation_dots   = function() {
			var dotsColor1  = bg_row_elem.data( 'dots-color-1' ),
				dotsColor2  = bg_row_elem.data( 'dots-color-2' ),
				dotsBgColor = bg_row_elem.data( 'dots-bg-color' ),
				dotsSize    = bg_row_elem.data( 'dots-size' ),
				dotsSpacing = bg_row_elem.data( 'dots-spacing' );

			VANTA.DOTS({
				el: '#pp-background-' + section_id,
				color: new THREE.Color( parseInt ( dotsColor1 ) ),
				color2: new THREE.Color( parseInt ( dotsColor2 ) ),
				backgroundColor: new THREE.Color( parseInt ( dotsBgColor ) ),
				size: dotsSize,
				spacing: dotsSpacing,
			});	
		}

		var animation_custom = function() {
			var part_custom_code = bg_row_elem.data( 'custom-code' );
			if ( '' !== part_custom_code ) {
				particlesJS( 'pp-background-' + section_id, part_custom_code );
			}
		}
		var animation_all_particles = function() {
			var partColor         = ( '' !== bg_row_elem.data( 'part-color' ) ) ? bg_row_elem.data( 'part-color' ) : '#ffffff',
				part_opacity      = bg_row_elem.data( 'part-opacity' ),
				part_rand_opacity = bg_row_elem.data( 'rand-opacity' ),
				part_quantity     = bg_row_elem.data( 'quantity' ),
				part_size         = bg_row_elem.data( 'part-size' ),
				part_speed        = bg_row_elem.data( 'part-speed' ),
				part_direction    = bg_row_elem.data( 'part-direction' ),
				part_hover_effect = bg_row_elem.data( 'hover-effect' ),
				part_hover_size   = bg_row_elem.data( 'hover-size' );

			if ( 'particles' === animation_type ) {
				var lineLinked 		= true,
					partShapeType	= 'circle',
					partMoveRand 	= false,
					partSizeRandom	= true,
					lineColor       = ( '' !== bg_row_elem.data( 'line-color' ) ) ? bg_row_elem.data( 'line-color' ) : '#ffffff',
					partQuantity    = ( '' !== part_quantity ) ? part_quantity : 80,
					partOpacity     = ( '' !== part_opacity ) ? part_opacity : 0.5,
					partRandOpacity = ( '' !== part_rand_opacity ) ? part_rand_opacity : false,
					partDirection   = ( '' !== part_direction ) ? part_direction : 'none',
					partSpeed       = ( '' !== part_speed ) ? part_speed : 3,
					partSize        = ( '' !== part_size ) ? part_size : 3,
					showHoverEffect	= ( 'noeffect' === part_hover_effect ) ? false : true,
					partHoverEffect	= ( 'none' !== part_hover_effect ) ? part_hover_effect : 'repulse',
					partHoverSize	= ( '' !== part_hover_size ) ? part_hover_size : 0;
			}
			if ( 'nasa' === animation_type ) {
				var lineLinked		= false,
					partShapeType 	= 'star',
					partMoveRand 	= true,
					partSizeRandom	= true,
					partQuantity    = ( '' !== part_quantity ) ? part_quantity : 110,
					partOpacity     = ( '' !== part_opacity ) ? part_opacity : 1,
					partRandOpacity = ( '' !== part_rand_opacity ) ? part_rand_opacity : true,
					partDirection   = ( '' !== part_direction ) ? part_direction : 'none',
					partSpeed       = ( '' !== part_speed ) ? part_speed : 1,
					partSize        = ( '' !== part_size ) ? part_size : 3,
					showHoverEffect	= ( 'noeffect' === part_hover_effect ) ? false : true,
					partHoverEffect	= ( 'none' !== part_hover_effect ) ? part_hover_effect : 'bubble',
					lineColor       = ( 'grab' === part_hover_effect && '' !== bg_row_elem.data( 'line-h-color' ) ) ? bg_row_elem.data( 'line-h-color' ) : '#ffffff',
					partHoverSize	= ( '' !== part_hover_size ) ? part_hover_size : 0;
			}
			if ( 'bubble' === animation_type ) {
				var lineLinked		= false,
					partShapeType	= 'circle',
					partMoveRand 	= true,
					partSizeRandom 	= false,
					partQuantity    = ( '' !== part_quantity ) ? part_quantity : 6,
					partOpacity     = ( '' !== part_opacity ) ? part_opacity : 0.6,
					partRandOpacity = ( '' !== part_rand_opacity ) ? part_rand_opacity : false,
					partDirection   = ( '' !== part_direction ) ? part_direction : 'none',
					partSpeed       = ( '' !== part_speed ) ? part_speed : 10,
					partSize        = ( '' !== part_size ) ? part_size : 160,
					showHoverEffect	= ( 'noeffect' === part_hover_effect ) ? false : true,
					partHoverEffect	= ( 'none' !== part_hover_effect ) ? part_hover_effect : 'none',
					lineColor       = ( 'grab' === part_hover_effect && '' !== bg_row_elem.data( 'line-h-color' ) ) ? bg_row_elem.data( 'line-h-color' ) : '#ffffff',
					partHoverSize	= ( '' !== part_hover_size ) ? part_hover_size : 0;
			}
			if ( 'snow' === animation_type ) {
				var lineLinked		= false,
					partShapeType	= 'circle',
					partMoveRand 	= false,
					partSizeRandom	= true,
					partQuantity    = ( '' !== part_quantity ) ? part_quantity : 300,
					partOpacity     = ( '' !== part_opacity ) ? part_opacity : 0.5,
					partRandOpacity = ( '' !== part_rand_opacity ) ? part_rand_opacity : true,
					partDirection   = ( '' !== part_direction ) ? part_direction : 'bottom',
					partSpeed       = ( '' !== part_speed ) ? part_speed : 5,
					partSpeed       = ( '' !== part_speed ) ? part_speed : 5,
					partSize        = ( '' !== part_size ) ? part_size : 10,
					showHoverEffect	= ( 'noeffect' === part_hover_effect ) ? false : true,
					partHoverEffect	= ( 'none' !== part_hover_effect ) ? part_hover_effect : 'bubble',
					lineColor       = ( 'grab' === part_hover_effect && '' !== bg_row_elem.data( 'line-h-color' ) ) ? bg_row_elem.data( 'line-h-color' ) : '#ffffff',
					partHoverSize	= ( '' !== part_hover_size ) ? part_hover_size : 4;
			}

			var particlesData = {
				"particles": {
					"number": {
						"value": partQuantity,
						"density": {
							"enable": true,
							"value_area": 800
						}
					},
					"color": {
						"value": partColor,
					},
					"shape": {
						"type": partShapeType,
						"stroke": {
							"width": 0,
							"color": "#000000"
						},
						"polygon": {
							"nb_sides": 5
						},
						"image": {
							"src": "",
							"width": 0,
							"height": 0
						}
					},
					"opacity": {
						"value": partOpacity,
						"random": partRandOpacity,
						"anim": {
							"enable": true,
							"speed": 1,
							"opacity_min": 0.1,
							"sync": false
						}
					},
					"size": {
						"value": partSize,
						"random": partSizeRandom,
						"anim": {
							"enable": false,
							"speed": 40,
							"size_min": 0.1,
							"sync": false
						}
					},
					"line_linked": {
						"enable": lineLinked,
						"distance": 150,
						"color": lineColor,
						"opacity": 0.4,
						"width": 1,
					},
					"move": {
						"enable": true,
						"speed": partSpeed,
						"direction": partDirection,
						"random": partMoveRand,
						"straight": false,
						"out_mode": "out",
						"bounce": false,
						"attract": {
							"enable": false,
							"rotateX": 600,
							"rotateY": 1200
						}
					}
				},
				"interactivity": {
					"detect_on": "canvas",
					"events": {
						"onhover": {
							"enable": showHoverEffect,
							"mode": partHoverEffect,
						},
						"onclick": {
							"enable": true,
							"mode": "repulse"
						},
						"resize": true
					},
					"modes": {
						"grab": {
							"distance": 400,
							"line_linked": {
							"opacity": 0.5
							}
						},
						"bubble": {
							"distance": 400,
							"size": partHoverSize,
							"duration": 0.3,
							"opacity": 1,
							"speed": 3
						},
						"repulse": {
							"distance": 200,
							"duration": 0.4
						},
						"push": {
							"particles_nb": 4
						},
						"remove": {
							"particles_nb": 2
						}
					}
				},
				"retina_detect": true
			}
			particlesJS( 'pp-background-' + section_id, particlesData );
		}
		setTimeout(function(){
			if ( winWidth > hideMaxWidth || winWidth < hideMinWidth || 'none' == hideMaxWidth ) {
				if ( 'particles' === animation_type || 'nasa' === animation_type || 'bubble' === animation_type || 'snow' === animation_type ) {
					var canvas_opacity = '';
					prepend_content( canvas_opacity );
					animation_all_particles();
				} else {
					var canvas_opacity = bg_row_elem.data( 'canvas-opacity' );

					prepend_content( canvas_opacity );
					eval( 'animation_' + animation_type )();
				}
			}
		}, 500);

    };

    $( window ).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/global', BgHandler);
    });
    
}(jQuery));
