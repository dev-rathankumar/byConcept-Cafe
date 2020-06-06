(function ($) {
    "use strict";
    
    var getElementSettings = function( $element ) {
		var elementSettings = {},
			modelCID 		= $element.data( 'model-cid' );

		if ( isEditMode && modelCID ) {
			var settings 		= elementorFrontend.config.elements.data[ modelCID ],
				settingsKeys 	= elementorFrontend.config.elements.keys[ settings.attributes.widgetType || settings.attributes.elType ];

			jQuery.each( settings.getActiveControls(), function( controlKey ) {
				if ( -1 !== settingsKeys.indexOf( controlKey ) ) {
					elementSettings[ controlKey ] = settings.attributes[ controlKey ];
				}
			} );
		} else {
			elementSettings = $element.data('settings') || {};
		}

		return elementSettings;
	};

    var isEditMode		= false;
    
    var PPSwiperSliderHandler = function ($scope, $) {
        var $carousel                   = $scope.find('.pp-swiper-slider').eq(0),
            $slider_options             = JSON.parse( $carousel.attr('data-slider-settings') );

        var mySwiper = new Swiper($carousel, $slider_options);
			
		PPSliderUpdate($, $carousel, mySwiper);
    };
    
    var PPSliderUpdate = function ($, $carousel, mySwiper, $slider = 'swiper') {
		var $containers = {
			'.pp-advanced-tabs':'.pp-advanced-tabs-title',
			'.pp-toggle-container':'.pp-toggle-switch',
			'.elementor-tabs':'.elementor-tab-title'
		};

		$.each( $containers, function( main_parent, click_element ) {
			if ( $($carousel).closest(main_parent).length > 0 ) {
				$($carousel).closest(main_parent).find(click_element).on('click', function() {
					setTimeout(function() {
						if ( $slider == 'slick' ) {
							$carousel.slick( 'setPosition' );
						} else {
							mySwiper.update();
						}
					}, 100);
				});
			}
		});
	};
	
    var ImageHotspotHandler = function ($scope, $) {
		var id 				= $scope.data('id'),
			elementSettings = getElementSettings( $scope ),
        	$tt_arrow       = elementSettings.tooltip_arrow,
			$tt_trigger     = elementSettings.tooltip_trigger,
			elementorBreakpoints = elementorFrontend.config.breakpoints;
		
        $('.pp-hot-spot-wrap[data-tooltip]').each(function () {
            var $tt_position        = $(this).data('tooltip-position'),
				$tt_template        = '',
				$tt_size            = $(this).data('tooltip-size'),
				$animation_in       = $(this).data('tooltip-animation-in'),
				$animation_out      = $(this).data('tooltip-animation-out');

            // tablet
            if ( window.innerWidth <= elementorBreakpoints.lg && window.innerWidth >= elementorBreakpoints.md ) {
                $tt_position = $scope.find('.pp-hot-spot-wrap[data-tooltip]').data('tooltip-position-tablet');
            }

            // mobile
            if ( window.innerWidth < elementorBreakpoints.md ) {
                $tt_position = $scope.find('.pp-hot-spot-wrap[data-tooltip]').data('tooltip-position-mobile');
            }
            
            if ( $tt_arrow == 'yes' ) {
                $tt_template = '<div class="pp-tooltip pp-tooltip-'+id+' pp-tooltip-'+$tt_size+'"><div class="pp-tooltip-body"><div class="pp-tooltip-content"></div><div class="pp-tooltip-callout"></div></div></div>';
            } else {
                $tt_template = '<div class="pp-tooltip pp-tooltip-'+id+' pp-tooltip-'+$tt_size+'"><div class="pp-tooltip-body"><div class="pp-tooltip-content"></div></div></div>';
			}
			
			var tooltipConfig = {
                template		: $tt_template,
				position		: $tt_position,
				animationIn		: $animation_in,
				animationOut	: $animation_out,
				animDuration	: 400,
                toggleable		: ($tt_trigger === 'click') ? true : false
			};
            
            $(this)._tooltip( tooltipConfig );
        });
    };
    
    var ImageComparisonHandler = function ($scope, $) {
		if ( 'undefined' == typeof $scope )
			return;

        var image_comparison_elem       = $scope.find('.pp-image-comparison').eq(0),
            settings                    = image_comparison_elem.data('settings');
        
		$scope.imagesLoaded( function() {
			image_comparison_elem.twentytwenty({
				default_offset_pct:         settings.visible_ratio,
				orientation:                settings.orientation,
				before_label:               settings.before_label,
				after_label:                settings.after_label,
				move_slider_on_hover:       settings.slider_on_hover,
				move_with_handle_only:      settings.slider_with_handle,
				click_to_move:              settings.slider_with_click,
				no_overlay:                 settings.no_overlay
			});
		} );
    };
    
    var CounterHandler = function ($scope, $) {
        var counter_elem                = $scope.find('.pp-counter').eq(0),
            $target                     = counter_elem.data('target');
        
        $(counter_elem).waypoint(function () {
            $($target).each(function () {
                var v                   = $(this).data("to"),
                    speed               = $(this).data("speed"),
                    od                  = new Odometer({
                        el:             this,
                        value:          0,
                        duration:       speed
                    });
                od.render();
                setInterval(function () {
                    od.update(v);
                });
            });
        },
            {
                offset:             "80%",
                triggerOnce:        true
            });
    };
    
    var InfoBoxCarouselHandler = function ($scope, $) {
		var elementSettings			= getElementSettings( $scope ),
            $carousel               = $scope.find('.pp-info-box-carousel'),
            slider_options          = JSON.parse( $carousel.attr('data-slider-settings') ),
            equal_height			= elementSettings.equal_height_boxes,
			mySwiper				= new Swiper($carousel, slider_options);
		
		if ( equal_height === 'yes' ) {
			function setEqualHeight(){
				var maxHeight = 0;
				$scope.find('.swiper-slide').each( function( currentSlide ) {
					if($(this).height() > maxHeight){
						maxHeight = $(this).height();
					}
				});
				$scope.find('.pp-info-box-content-wrap').css('min-height',maxHeight);
			}

			$(setEqualHeight);
			$(window).resize(setEqualHeight);
		}
		
		PPSliderUpdate($, $carousel, mySwiper);
    };
    
    var InstaFeedPopupHandler = function ($scope, $) {
        var widget_id					= $scope.data('id'),
			instafeed_elem              = $scope.find('.pp-instagram-feed').eq(0),
			elementSettings				= getElementSettings( $scope ),
            settings                    = instafeed_elem.data('settings'),
            taregt_id					= settings.target,
            popup                    	= settings.popup,
            image_link                  = settings.img_link,
            layout                    	= elementSettings.feed_layout,
            likes                    	= elementSettings.insta_likes,
            comments                    = elementSettings.insta_comments,
            icons_style                 = (elementSettings.icons_style === 'outline') ? '-o' : '',
            like_span                   = (likes === 'yes') ? '<span class="likes"><i class="pp-if-icon fa fa-heart' + icons_style + '"></i> {{likes}}</span>' : '',
            comments_span               = (comments === 'yes') ? '<span class="comments"><i class="pp-if-icon fa fa-comment' + icons_style + '"></i> {{comments}}</span>' : '',
            $more_button                = instafeed_elem.find('.pp-load-more-button');
		
		var $slider_options;
		
		if (layout === 'carousel') {
			var $carousel       = $scope.find('.swiper-container').eq(0),
				$slider_options = JSON.parse( $carousel.attr('data-slider-settings') );
		}
		
		if ( elementSettings.use_api === 'yes' ) {
			if ( settings.user_id && settings.access_token ) {
				var feed = new Instafeed({
					get:                    'user',
					userId:                 settings.user_id,
					sortBy:                 settings.sort_by,
					accessToken:            settings.access_token,
					limit:                  settings.images_count,
					target:                 taregt_id,
					resolution:             settings.resolution,
					orientation:            'portrait',
					template:               function () {
						if (popup === '1') {
							if (layout === 'carousel') {
								return '<div class="pp-feed-item swiper-slide"><div class="pp-feed-item-inner"><a href="{{image}}"><div class="pp-if-img"><div class="pp-overlay-container pp-media-overlay">' + like_span + comments_span + '</div><img src="{{image}}" /></div></a></div></div>';
							} else {
								return '<div class="pp-feed-item"><div class="pp-feed-item-inner"><a href="{{image}}"><div class="pp-if-img"><div class="pp-overlay-container pp-media-overlay">' + like_span + comments_span + '</div><img src="{{image}}" /></div></a></div></div>';
							}
						} else {
							if (layout === 'carousel') {
								return '<div class="pp-feed-item swiper-slide"><div class="pp-feed-item-inner">' +
									'<a href="{{link}}">' +
										'<div class="pp-if-img">' +
										'<div class="pp-overlay-container pp-media-overlay">' + like_span + comments_span + '</div>' +
										'<img src="{{image}}" />' +
										'</div>' +
									'</a>' +
									'</div></div>';
							} else {
								return '<div class="pp-feed-item"><div class="pp-feed-item-inner">' +
									'<a href="{{link}}">' +
										'<div class="pp-if-img">' +
										'<div class="pp-overlay-container pp-media-overlay">' + like_span + comments_span + '</div>' +
										'<img src="{{image}}" />' +
										'</div>' +
									'</a>' +
									'</div></div>';
							}
						}
					}(),
					after: function () {
						if (layout === 'carousel') {
							var mySwiper        = new Swiper($carousel, $slider_options);
						}
						if (layout === 'masonry') {
							var grid = $('#pp-instafeed-' + widget_id).imagesLoaded( function() {
								grid.masonry({
									itemSelector: '.pp-feed-item',
									percentPosition: true
								});
							});
						}
						if (!this.hasNext()) {
							$more_button.attr('disabled', 'disabled');
						}
					},
					success: function() {
						$more_button.removeClass( 'pp-button-loading' );
						$more_button.find( '.pp-load-more-button-text' ).html( 'Load More' );
					}
				});
				
				

				$more_button.on('click', function() {
					feed.next();
					$more_button.addClass( 'pp-button-loading' );
					$more_button.find( '.pp-load-more-button-text' ).html( 'Loading...' );
				});

				feed.run();

				if (popup === '1') {
					$(taregt_id).each(function () {
						$(this).magnificPopup({
							delegate: 'div a', // child items selector, by clicking on it popup will open
							gallery: {
								enabled: true,
								navigateByImgClick: true,
								preload: [0, 1]
							},
							type: 'image'
						});
					});
				}
			}
		} else {
			var pp_feed = new PPInstagramFeed({
					id: widget_id,
					username: elementSettings.username,
					layout: layout,
					limit: settings.images_count,
					likes_count: (likes === 'yes'),
					comments_count: (comments === 'yes'),
					carousel: $slider_options,
					popup: popup,
					image_link: image_link,
				});
		}
    };
    
    var ImageSliderHandler = function ( $scope, $ ) {
        var $carousel            = $scope.find( '.pp-image-slider' ).eq( 0 ),
            $slider_id           = $carousel.attr( 'id' ),
            $carousel_settings   = $carousel.data('slider-settings'),
            $slider_wrap         = $scope.find( '.pp-image-slider-wrap' ),
            $thumbs_nav          = $scope.find( '.pp-image-slider-container .pp-image-slider-thumb-item-wrap' ),
            elementSettings      = getElementSettings( $scope );
        
            $carousel.slick( $carousel_settings );

            $carousel.slick( 'setPosition' );

            if ( elementSettings.skin == 'slideshow' ) {
                $thumbs_nav.removeClass('pp-active-slide');
                $thumbs_nav.eq(0).addClass('pp-active-slide');

                $carousel.on('beforeChange', function ( event, slick, currentSlide, nextSlide ) {
                    var currentSlide = nextSlide;
                    $thumbs_nav.removeClass('pp-active-slide');
                    $thumbs_nav.eq( currentSlide ).addClass('pp-active-slide');
                });

                $thumbs_nav.each( function( currentSlide ) {
                    $(this).on( 'click', function ( e ) {
                        e.preventDefault();
                        $carousel.slick( 'slickGoTo', currentSlide );
                    });
                });
            }
		
			PPSliderUpdate($, $carousel, '', 'slick');

            if ( isEditMode ) {
                $slider_wrap.resize( function() {
                    $carousel.slick( 'setPosition' );
                });
            }
        
            var $lightbox_selector = '.slick-slide:not(.slick-cloned) .pp-image-slider-slide-link[data-fancybox="'+$slider_id+'"]';
        
            $($lightbox_selector).fancybox({
                loop:       true,
            });
    };

	var ModalPopupHandler = function ($scope, $) {
		var popup_elem                  = $scope.find('.pp-modal-popup').eq(0),
			widget_id                   = $scope.data('id'),
			elementSettings				= getElementSettings( $scope ),
			overlay                 	= elementSettings.overlay_switch,
			$popup_layout               = 'pp-modal-popup-' + elementSettings.layout_type,
			close_button_pos			= elementSettings.close_button_position,
			$effect                     = 'animated' + ' ' + elementSettings.popup_animation_in,
			$type                       = popup_elem.data('type'),
			$iframe_class               = popup_elem.data('iframe-class'),
			$src                        = popup_elem.data('src'),
			$trigger_element            = popup_elem.data('trigger-element'),
			$delay                      = popup_elem.data('delay'),
			$popup_disable_on           = popup_elem.data('disable-on'),
			$trigger                    = elementSettings.trigger,
			prevent_scroll              = (elementSettings.prevent_scroll === 'yes') ? true : false,
			$popup_id                   = 'popup_' + widget_id,
			$display_after              = popup_elem.data('display-after'),
			$main_class = ' ' + 'pp-modal-popup-' + widget_id + ' ' + $popup_layout + ' ' + close_button_pos + ' ' + $effect,
			popup_args 					= {
				disableOn			: $popup_disable_on,
				showCloseBtn		: (elementSettings.close_button === 'yes') ? true : false,
				enableEscapeKey		: (elementSettings.esc_exit === 'yes') ? true : false,
				closeOnBgClick		: (elementSettings.click_exit === 'yes') ? true : false,
				closeOnContentClick	: (elementSettings.content_close === 'yes') ? true : false,
				closeMarkup			: '<div class="mfp-close">&#215;</div>',
				closeBtnInside		: (close_button_pos === 'win-top-left' || close_button_pos === 'win-top-right') ? false : true,
				removalDelay		: 500,
				callbacks			: {
					open : function() {
						if ( !prevent_scroll ) {
							$('html').css({ 'overflow' : '' });
						}
					},
					close : function() {
						if ( !prevent_scroll ) {
							$('html').css({ 'overflow' : 'hidden' });
						}
					},
				}
			};
		
		if ( overlay !== 'yes' ) {
			$main_class += ' ' + 'pp-no-overlay';
		}

		if ( $trigger == 'exit-intent' ) {
			var flag = true,
				mouseY = 0,
				topValue = 0;

			if ( $display_after === 0 ) {
				$.removeCookie($popup_id, { path: '/' });
			}
			
			popup_args.items = {
				src: $src 
			};
			popup_args.type = $type;
			popup_args.mainClass = 'mfp-fade mfp-fade-side';
			
			$(document).on( 'mouseleave', function( e ) {
				mouseY = e.clientY;
				if (mouseY < topValue && !$.cookie($popup_id) ) {
					$.magnificPopup.open( popup_args );

					if ( $display_after > 0 ) {
						$.cookie($popup_id, $display_after, { expires: $display_after, path: '/' });
					} else {
						$.removeCookie( $popup_id );
					}
				}
			} );
		}
		else if ( $trigger == 'page-load') {
			if ( $display_after === 0 ) {
				$.removeCookie($popup_id, { path: '/' });
			}
			popup_args.items = {
				src: $src 
			};
			popup_args.type = $type;
			if ( !$.cookie($popup_id) ) {
				setTimeout(function() {
					$.magnificPopup.open( popup_args );

					if ( $display_after > 0 ) {
						$.cookie($popup_id, $display_after, { expires: $display_after, path: '/' });
					} else {
						$.removeCookie( $popup_id );
					}
				}, $delay);
			}
		} else {
			if (typeof $trigger_element === 'undefined' || $trigger_element === '') {
				$trigger_element = '.pp-modal-popup-link'
			}
			popup_args.iframe = {
				markup: '<div class="' + $iframe_class + '">'+
						'<div class="modal-popup-window-inner">'+
						'<div class="mfp-iframe-scaler">'+
							'<div class="mfp-close"></div>'+
							'<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
						'</div>'+
						'</div>'+
						'</div>',
			};
			popup_args.items = {
				src: $src,
				type: $type,
			};
			popup_args.mainClass = $main_class;
			$( $trigger_element ).magnificPopup(popup_args);
		};
	};

	var TableHandler = function ($scope, $) {
		var table_elem      = $scope.find('.pp-table').eq(0),
            elementSettings	= getElementSettings( $scope );
		
		if ( elementSettings.table_type == 'responsive' ) {
			
			if ( elementSettings.scrollable == 'yes' && elementSettings.breakpoint > 0 ) {
				if ( jQuery(window).width() >= elementSettings.breakpoint ) {
					console.log(elementSettings.breakpoint);
					jQuery( table_elem ).removeAttr('data-tablesaw-mode');
				}
			}
			
			$( document ).trigger( "enhance.tablesaw" );

		}
	};

	var AdvancedTabsHandler = function ($scope, $) {
		var titleContainer     = $scope.find('.pp-advanced-tabs'),
			titleMain          = $scope.find('.pp-advanced-tabs-wrapper'),
			titleVertical      = $scope.find('.pp-advanced-tabs-wrapper.at-vertical'),
			titleWrap          = titleMain.find('.pp-advanced-tabs-title'),
			titleFirstChild    = titleMain.find(">:first-child").toggleClass(titleWrap ),
			// Content
			contentWrap        = $scope.find('.pp-advanced-tabs-content-wrapper'),
			contentTab         = $scope.find('.pp-advanced-tabs-content-wrapper.at-vertical-content'),
			contentSection     = $scope.find('.pp-advanced-tabs-content'),
			contentFirstChild  = contentWrap.find(".pp-advanced-tabs-content:nth-child(2)"),
			// ResponsiveTab
			responsiveTab      = contentWrap.find(".pp-advanced-tabs-title.pp-tab-responsive"),
			responsiveTabInner = responsiveTab.find(".pp-advanced-tabs-title-inner");
		
		new PPAdvancedTabs( $scope, $ );
	}
	
	var PPCountdownHandler = function ($scope, $) {
		var wrap = $scope.find('.pp-countdown-wrapper'),
			settings = JSON.parse( $scope.find('[name=pp-countdown-settings]').val() );

		new PPCountdown( settings, $scope, $ );
	};

    var ToggleHandler = function ($scope, $) {
        var toggle_elem             	= $scope.find('.pp-toggle-container').eq(0),
			$toggle_switch_container	= $(toggle_elem).find('.pp-toggle-switch-container'),
			$toggle_switch      		= $(toggle_elem).find('.pp-toggle-switch'),
			$label_primary      		= $(toggle_elem).find('.pp-primary-toggle-label'),
			$label_secondary      		= $(toggle_elem).find('.pp-secondary-toggle-label'),
			$section_primary      		= $(toggle_elem).find('.pp-toggle-section-primary'),
			$section_secondary      	= $(toggle_elem).find('.pp-toggle-section-secondary');
		
			$toggle_switch.on('click', function(e){
				$section_primary.toggle(0, 'swing', function() {
					$toggle_switch_container.toggleClass("pp-toggle-switch-on");
				});
				$section_secondary.toggle();
			});

			/* Primary Label Click */
			$label_primary.on('click', function(e){
				$toggle_switch.prop("checked", false);
				$toggle_switch_container.removeClass("pp-toggle-switch-on");
				$section_primary.show();
				$section_secondary.hide();
			});

			/* Secondary Label Click */
			$label_secondary.on('click', function(e){
				$toggle_switch.prop("checked", true);
				$toggle_switch_container.addClass("pp-toggle-switch-on");
				$section_secondary.show();
				$section_primary.hide();
			});
    };

	var AdvancedMenuHandler = function ($scope, $) {

		new PPAdvancedMenu( $scope );
		
	};

    var ImageGalleryHandler = function ($scope, $) {
        
        var $gallery = $scope.find('.pp-image-gallery').eq(0),
			elementSettings = getElementSettings( $scope ),
        	$justified_gallery = $scope.find('.pp-image-gallery-justified').eq(0),
            $widget_id = $scope.data( 'id' ),
            $gallery_id = $gallery.attr( 'id' ),
            $lightbox_library = elementSettings.lightbox_library,
            settings = $gallery.data('settings'),
            cachedItems = [],
            cachedIds = [];
		
        if ( ! isEditMode ) {

            if ( $gallery.hasClass('pp-image-gallery-masonry') || $gallery.hasClass('pp-image-gallery-filter-enabled') || settings.pagination == 'yes' ) {

                var $layout_mode = 'fitRows';

                if ( $gallery.hasClass('pp-image-gallery-masonry') ) {
                    $layout_mode = 'masonry';
                }

                var $isotope_args = {
                    itemSelector:   '.pp-grid-item-wrap',
                    layoutMode		: $layout_mode,
                    percentPosition : true,
                },
                    $isotope_gallery = {};

                $scope.imagesLoaded( function(e) {
                    $isotope_gallery = $gallery.isotope( $isotope_args );
                    $gallery.find('.pp-gallery-slide-image').on('load', function() {
						if ( $(this).hasClass('lazyloaded') ) {
							return;
						}
						setTimeout(function() {
							$gallery.isotope( 'layout' );
						}, 1000);
					});
                });

                $scope.on( 'click', '.pp-gallery-filter', function() {
                    var $this = $(this),
                        filterValue = $this.attr('data-filter'),
						filter_index = $this.attr('data-gallery-index'),
						$gallery_items = $gallery.find(filterValue);

					if ( filterValue === '*' ) {
						$gallery_items = $gallery.find('.pp-grid-item-wrap');
					}

					$($gallery_items).each(function() {
						var $img_link = $(this).find('.pp-image-gallery-item-link');
						if ( $lightbox_library == 'fancybox' ) {
							$img_link.attr('data-fancybox', filter_index + '_' + $widget_id);	
						} else {
							$img_link.attr('data-elementor-lightbox-slideshow', filter_index + '_' + $widget_id);
						}
					});

                    $this.siblings().removeClass('pp-active');
                    $this.addClass('pp-active');

                    $isotope_gallery.isotope({ filter: filterValue });
                });
            }
        }

		var $tilt_enable = (settings.tilt_enable !== undefined) ? settings.tilt_enable : '';

        if ( $tilt_enable == 'yes' ) {
            $( $gallery ).find('.pp-grid-item').tilt({
                disableAxis: settings.tilt_axis,
                maxTilt: settings.tilt_amount,
                scale: settings.tilt_scale,
                speed: settings.tilt_speed,
				perspective: 1000,
            });
		}
		
		if ( $justified_gallery.length > 0 ) {
			$justified_gallery.imagesLoaded( function() {
			})
			.done(function( instance ) {
				$justified_gallery.justifiedGallery({
				    rowHeight : settings.row_height,
				    lastRow : settings.last_row,
				    selector : 'div',
				    waitThumbnailsLoad : true,
				    margins : 0,
				    border : 0,
				});
			});
		}
        
        var $lightbox_selector = '.pp-grid-item-wrap .pp-image-gallery-item-link[data-fancybox="'+$gallery_id+'"]';

        $($lightbox_selector).fancybox({
            loop:       true,
        });
		
		$gallery.find('.pp-grid-item-wrap').each(function() {
			cachedIds.push( $(this).data('item-id') );
		});
		
		// Load More
		$scope.find('.pp-gallery-load-more').on('click', function(e) {
			e.preventDefault();

			var $this = $(this);
			$this.addClass('disabled pp-loading');

			if ( cachedItems.length > 0 ) {
				gallery_render_items();
			} else {

				var data = {
					action: 'pp_gallery_get_images',
					pp_action: 'pp_gallery_get_images',
					settings: settings
				};

				$.ajax({
					type: 'post',
					url: window.location.href.split( '#' ).shift(),
					data: data,
					success: function(response) {
						if ( response.success ) {
							var items = response.data.items;
							if ( items ) {
								$(items).each(function() {
									if ( $(this).hasClass('pp-grid-item-wrap') ) {
										cachedItems.push( this );
									}
								});
							}

							gallery_render_items();
						}
					},
					error: function(xhr, desc) {
						console.log(desc);
					}
				});
			}
		});
	
		function gallery_render_items() {
			$scope.find('.pp-gallery-load-more').removeClass( 'disabled pp-loading' );

			if ( cachedItems.length > 0 ) {
				var count = 1;
				var items = [];

				$(cachedItems).each(function() {
					var id = $(this).data('item-id');

					if ( -1 === $.inArray( id, cachedIds ) ) {
						if ( count <= parseInt( settings.per_page ) ) {
							cachedIds.push( id );
							items.push( this );
							count++;
						} else {
							return false;
						}
					}
				});

				if ( items.length > 0 ) {
					items = $(items);

					items.imagesLoaded( function(e) {
						$gallery.isotope('insert', items);
						setTimeout(function() {
							$gallery.isotope('layout');
						}, 500);
					});
				}
                
                if ( $tilt_enable == 'yes' ) {
                    $( $gallery ).find('.pp-grid-item').tilt({
                        disableAxis: settings.tilt_axis,
                        maxTilt: settings.tilt_amount,
                        scale: settings.tilt_scale,
                        speed: settings.tilt_speed,
                    });
                }

				if ( cachedItems.length === cachedIds.length ) {
					$scope.find('.pp-gallery-pagination').hide();
				}

				var $lightbox_selector = '.pp-grid-item-wrap .pp-image-gallery-item-link[data-fancybox="'+$gallery_id+'"]';

				$($lightbox_selector).fancybox({
					loop:       true,
				});
			}
		}
	};
	
	var OffCanvasContentHandler = function ($scope, $) {
		var content_wrap = $scope.find('.pp-offcanvas-content-wrap');
		
		if ( $(content_wrap).length > 0 ) {
			new PPOffcanvasContent( $scope );
		}
	};

	var PPButtonHandler = function ( $scope, $) {
		var id = $scope.data('id'),
			ttipPosition = $scope.find('.pp-button[data-tooltip]').data('tooltip-position'),
			elementorBreakpoints = elementorFrontend.config.breakpoints;

		// tablet
		if ( window.innerWidth <= elementorBreakpoints.lg && window.innerWidth >= elementorBreakpoints.md ) {
			ttipPosition = $scope.find('.pp-button[data-tooltip]').data('tooltip-position-tablet');
		}
		// mobile
		if ( window.innerWidth < elementorBreakpoints.md ) {
			ttipPosition = $scope.find('.pp-button[data-tooltip]').data('tooltip-position-mobile');
		}
		$scope.find('.pp-button[data-tooltip]')._tooltip( {
			template: '<div class="pp-tooltip pp-tooltip-'+id+'"><div class="pp-tooltip-body"><div class="pp-tooltip-content"></div><div class="pp-tooltip-callout"></div></div></div>',
			position: ttipPosition,
			animDuration: 400
		} );
	};

    var ShowcaseHandler = function ( $scope, $ ) {
        var $carousel            = $scope.find( '.pp-showcase-preview' ).eq( 0 ),
            $showcase_id         = $carousel.attr( 'id' ),
            $rtl     			 = $carousel.data( 'rtl' ),
            $slider_wrap         = $scope.find( '.pp-showcase-preview-wrap' ),
            $nav_wrap            = $scope.find( '.pp-showcase-navigation-items' ),
            $nav                 = $scope.find( '.pp-showcase .pp-showcase-navigation-item-wrap' ),
            $video_wrap          = $scope.find( '.pp-showcase .pp-video-container' ),
            elementSettings      = getElementSettings( $scope ),
            $arrow_next          = elementSettings.arrow,
            $arrow_prev          = ( $arrow_next !== undefined ) ? $arrow_next.replace( "right", "left" ) : '',
            $scrollable_nav      = elementSettings.scrollable_nav,
            $preview_position    = elementSettings.preview_position,
            $stack_on            = elementSettings.preview_stack,
			elementorBreakpoints = elementorFrontend.config.breakpoints;
        
            $carousel.slick({
                slidesToShow:           1,
				slidesToScroll:  		1,
                autoplay:               'yes' === elementSettings.autoplay,
                autoplaySpeed:          elementSettings.autoplay_speed,
                arrows:                 'yes' === elementSettings.arrows,
                prevArrow:              '<div class="pp-slider-arrow pp-arrow pp-arrow-prev"><i class="' + $arrow_prev + '"></i></div>',
				nextArrow:              '<div class="pp-slider-arrow pp-arrow pp-arrow-next"><i class="' + $arrow_next + '"></i></div>',
                dots:                   'yes' === elementSettings.dots,
                fade:                   'fade' === elementSettings.effect,
                speed:                  elementSettings.animation_speed,
                infinite:               'yes' === elementSettings.infinite_loop,
                pauseOnHover:           'yes' === elementSettings.pause_on_hover,
                adaptiveHeight:         'yes' === elementSettings.adaptive_height,
                rtl:                    'yes' === $rtl,
                asNavFor:               ( $scrollable_nav == 'yes' ) ? $nav_wrap : '',
            });

            $carousel.slick( 'setPosition' );
        
            if ( $scrollable_nav == 'yes' ) {
                
                $nav_wrap.slick({
                    slidesToShow:       ( elementSettings.nav_items !== undefined && elementSettings.nav_items !== '' ) ? parseInt( elementSettings.nav_items ) : 5,
                    slidesToScroll:     1,
                    asNavFor:           $carousel,
                    arrows:             false,
                    dots:               false,
                    infinite:           'yes' === elementSettings.infinite_loop,
                    focusOnSelect:      true,
                    vertical:           ($preview_position == 'top' || $preview_position == 'bottom') ? false : true,
                    centerMode:         'yes' === elementSettings.nav_center_mode,
                    centerPadding:      '0px',
                    responsive:         [
                        {
                        breakpoint: elementorBreakpoints.lg,
                            settings: {
                                slidesToShow: ( elementSettings.nav_items_tablet !== undefined && elementSettings.nav_items_tablet !== '' ) ? parseInt( elementSettings.nav_items_tablet ) : 3,
                                slidesToScroll: 1,
                                vertical: ($stack_on == 'tablet') ? false : true,
                            }
                        },
                        {
                        breakpoint: elementorBreakpoints.md,
                            settings: {
                                slidesToShow: ( elementSettings.nav_items_mobile !== undefined && elementSettings.nav_items_mobile !== '' ) ? parseInt( elementSettings.nav_items_mobile ) : 2,
                                slidesToScroll: 1,
                                vertical: false,
                            }
                        },
                    ],
                });
                
            } else {
                
                $nav.removeClass('pp-active-slide');
                $nav.eq(0).addClass('pp-active-slide');

                $carousel.on('beforeChange', function ( event, slick, currentSlide, nextSlide ) {
                    var currentSlide = nextSlide;
                    $nav.removeClass('pp-active-slide');
                    $nav.eq( currentSlide ).addClass('pp-active-slide');
                });

                $nav.each( function( currentSlide ) {
                    $(this).on( 'click', function ( e ) {
                        e.preventDefault();
                        $carousel.slick( 'slickGoTo', currentSlide );
                    });
                });
                
            }
		
			PPSliderUpdate($, $carousel, '', 'slick');

            if ( isEditMode ) {
                $slider_wrap.resize( function() {
                    $carousel.slick( 'setPosition' );
                });
            }
        
            var $lightbox_selector = '.slick-slide:not(.slick-cloned) .pp-showcase-item-link[data-fancybox="'+$showcase_id+'"]';
        
            $($lightbox_selector).fancybox({
                loop:       true,
            });
        
            $video_wrap.off( 'click' ).on( 'click', function( e ) {

                var $iframe = $( "<iframe/>" ),
                    $vid_src = $( this ).data( 'src' ),
                    $player = $( this ).find( '.pp-video-player' );
                
                $iframe.attr( 'src', $vid_src );
				$iframe.attr( 'frameborder', '0' );
				$iframe.attr( 'allowfullscreen', '1' );
				$iframe.attr( 'allow', 'autoplay;encrypted-media;' );

				$player.html( $iframe );

            });
    };
    
    var TimelineHandler = function ( $scope, $ ) {
        var $carousel            = $scope.find( '.pp-timeline-horizontal .pp-timeline-items' ).eq( 0 ),
            $slider_wrap         = $scope.find( '.pp-timeline-wrapper' ),
            $rtl				 = $slider_wrap.data( 'rtl' ),
            $slider_nav          = $scope.find( '.pp-timeline-navigation' ),
            elementSettings      = getElementSettings( $scope ),
            $arrow_next          = elementSettings.arrow,
            $arrow_prev          = ( $arrow_next !== undefined ) ? $arrow_next.replace( "right", "left" ) : '',
			$items               = ( elementSettings.columns !== undefined && elementSettings.columns !== '' ) ? parseInt( elementSettings.columns ) : 3,
			$items_tablet        = ( elementSettings.columns_tablet !== undefined && elementSettings.columns_tablet !== '' ) ? parseInt( elementSettings.columns_tablet ) : 2,
			$items_mobile        = ( elementSettings.columns_mobile !== undefined && elementSettings.columns_mobile !== '' ) ? parseInt( elementSettings.columns_mobile ) : 1,
			elementorBreakpoints = elementorFrontend.config.breakpoints;
        
		if ( elementSettings.layout == 'horizontal' ) {
			var $center_mode = false;
			
			if ( 'yes' === elementSettings.infinite_loop && 'yes' === elementSettings.center_mode ) {
				$center_mode = true;
			}
			
			$carousel.slick({
				slidesToShow:           $items,
				slidesToScroll:  		1,
				autoplay:               'yes' === elementSettings.autoplay,
				autoplaySpeed:          elementSettings.autoplay_speed,
				arrows:                 false,
				centerMode:             $center_mode,
				speed:                  elementSettings.animation_speed,
				infinite:               'yes' === elementSettings.infinite_loop,
				rtl:                    'yes' === $rtl,
				asNavFor:               $slider_nav,
				responsive: [
					{
					breakpoint: elementorBreakpoints.lg,
						settings: {
							slidesToShow: $items_tablet,
						}
					},
					{
					breakpoint: elementorBreakpoints.md,
						settings: {
							slidesToShow: $items_mobile,
						}
					},
				]
			});

			$slider_nav.slick({
				slidesToShow:           $items,
				slidesToScroll:  		1,
				autoplay:               'yes' === elementSettings.autoplay,
				autoplaySpeed:          elementSettings.autoplay_speed,
				asNavFor:               $carousel,
				arrows:                 'yes' === elementSettings.arrows,
				prevArrow:              '<div class="pp-slider-arrow pp-arrow pp-arrow-prev"><i class="' + $arrow_prev + '"></i></div>',
				nextArrow:              '<div class="pp-slider-arrow pp-arrow pp-arrow-next"><i class="' + $arrow_next + '"></i></div>',
				centerMode:             'yes' === elementSettings.center_mode,
				infinite:               'yes' === elementSettings.infinite_loop,
				rtl:                    'yes' === $rtl,
				focusOnSelect:          true,
				responsive: [
					{
					breakpoint: elementorBreakpoints.lg,
						settings: {
							slidesToShow: $items_tablet,
						}
					},
					{
					breakpoint: elementorBreakpoints.md,
						settings: {
							slidesToShow: $items_mobile,
						}
					},
				]
			});

			$carousel.slick( 'setPosition' );

			if ( isEditMode ) {
				$slider_wrap.resize( function() {
					$carousel.slick( 'setPosition' );
				});
			}
			
			var $containers = {
				'.pp-advanced-tabs':'.pp-advanced-tabs-title',
				'.pp-toggle-container':'.pp-toggle-switch'
			};
			
			$.each( $containers, function( main_parent, click_element ) {
				if ( $($slider_wrap).closest(main_parent).length > 0 ) {
					$($slider_wrap).closest(main_parent).find(click_element).on('click', function() {
						setTimeout(function() {
							$carousel.slick( 'setPosition' );
							$slider_nav.slick( 'setPosition' );
						}, 100);
					});
				}
			});
		}

		// PPTimeline
		var settings = {};

		if ( isEditMode ) {
			settings.window = elementor.$previewContents;
		}

		var timeline = new PPTimeline( settings, $scope );
    };
    
    var ImageAccordionHandler = function ($scope, $) {
		var $image_accordion            = $scope.find('.pp-image-accordion').eq(0),
            elementSettings             = getElementSettings( $scope ),
            $action                     = elementSettings.accordion_action,
		    $id                         = $image_accordion.attr( 'id' ),
		    $item                       = $('#'+ $id +' .pp-image-accordion-item');
		   
		if ( 'on-hover' === $action ) {
            $item.hover(
                function ImageAccordionHover() {
                    $item.css('flex', '1');
                    $item.removeClass('pp-image-accordion-active');
                    $(this).addClass('pp-image-accordion-active');
                    $item.find('.pp-image-accordion-content-wrap').removeClass('pp-image-accordion-content-active');
                    $(this).find('.pp-image-accordion-content-wrap').addClass('pp-image-accordion-content-active');
                    $(this).css('flex', '3');
                },
                function() {
                    $item.css('flex', '1');
                    $item.find('.pp-image-accordion-content-wrap').removeClass('pp-image-accordion-content-active');
                    $item.removeClass('pp-image-accordion-active');
                }
            );
        }
		else if ( 'on-click' === $action ) {
            $item.click( function(e) {
                e.stopPropagation(); // when you click the button, it stops the page from seeing it as clicking the body too
                $item.css('flex', '1');
				$item.removeClass('pp-image-accordion-active');
                $(this).addClass('pp-image-accordion-active');
				$item.find('.pp-image-accordion-content-wrap').removeClass('pp-image-accordion-content-active');
				$(this).find('.pp-image-accordion-content-wrap').addClass('pp-image-accordion-content-active');
                $(this).css('flex', '3');
            });

            $('#'+ $id).click( function(e) {
                e.stopPropagation(); // when you click within the content area, it stops the page from seeing it as clicking the body too
            });

            $('body').click( function() {
                $item.css('flex', '1');
				$item.find('.pp-image-accordion-content-wrap').removeClass('pp-image-accordion-content-active');
				$item.removeClass('pp-image-accordion-active');
            });
		}
    };
    
    var AdvancedAccordionHandler = function ($scope, $) {
    	var $advanced_accordion         = $scope.find(".pp-advanced-accordion").eq(0),
            elementSettings             = getElementSettings( $scope ),
        	$accordion_title            = $scope.find(".pp-accordion-tab-title"),
        	$accordion_type             = elementSettings.accordion_type,
        	$accordion_speed            = elementSettings.toggle_speed;
	
        // Open default actived tab
        $accordion_title.each(function(){
            if ( $(this).hasClass('pp-accordion-tab-active-default') ) {
                $(this).addClass('pp-accordion-tab-show pp-accordion-tab-active');
                $(this).next().slideDown($accordion_speed)
            }
        })

        // Remove multiple click event for nested accordion
        $accordion_title.unbind("click");

        $accordion_title.click(function(e) {
            e.preventDefault();

            var $this = $(this);

            if ( $accordion_type === 'accordion' ) {
                if ( $this.hasClass("pp-accordion-tab-show") ) {
                    $this.removeClass("pp-accordion-tab-show pp-accordion-tab-active");
                    $this.next().slideUp($accordion_speed);
                } else {
                    $this.parent().parent().find(".pp-accordion-tab-title").removeClass("pp-accordion-tab-show pp-accordion-tab-active");
                    $this.parent().parent().find(".pp-accordion-tab-title").removeClass("pp-accordion-tab-active-default");
                    $this.parent().parent().find(".pp-accordion-tab-content").slideUp($accordion_speed);
                    $this.toggleClass("pp-accordion-tab-show pp-accordion-tab-active");
                    $this.next().slideToggle($accordion_speed);
                }
            } else {
                // For acccordion type 'toggle'
                if ( $this.hasClass("pp-accordion-tab-show") ) {
                    $this.removeClass("pp-accordion-tab-show pp-accordion-tab-active");
                    $this.next().slideUp($accordion_speed);
                } else {
                    $this.addClass("pp-accordion-tab-show pp-accordion-tab-active");
                    $this.next().slideDown($accordion_speed);
                }
            }
        });
    };

    var MagazineSliderHandler = function ( $scope, $ ) {
        var $carousel            = $scope.find( '.pp-magazine-slider' ).eq( 0 ),
            $slider_settings     = $carousel.data( 'slider-settings' );
        
            $carousel.slick( $slider_settings );
		
			PPSliderUpdate($, $carousel, '', 'slick');
    };

	 var PPVideo = {

		/**
		 * Auto Play Video
		 */

		_play: function( $selector ) {

			var $iframe 		= $( "<iframe/>" );
	        var $vid_src 		= $selector.data( 'src' );

	        if ( 0 == $selector.find( 'iframe' ).length ) {

				$iframe.attr( 'src', $vid_src );
				$iframe.attr( 'frameborder', '0' );
				$iframe.attr( 'allowfullscreen', '1' );
				$iframe.attr( 'allow', 'autoplay;encrypted-media;' );

				$selector.html( $iframe );
	        }
		}
	}

    var VideoHandler = function ($scope, $) {
        var $selector           = $scope.find( '.pp-video' ).eq(0),
            settings            = $selector.data('settings'),
            $video_play         = $scope.find( '.pp-video-play' ),
            elementSettings     = getElementSettings( $scope );
        
            $video_play.off( 'click' ).on( 'click', function( e ) {

                e.preventDefault();
                
                var $selector 	= $( this ).find( '.pp-video-player' );

                PPVideo._play( $selector );

            });

            if ( $video_play.data( 'autoplay' ) == '1' ) {

                PPVideo._play( $scope.find( '.pp-video-player' ) );
                
            }
	};

    var VideoGalleryHandler = function ($scope, $) {
        var $gallery            = $scope.find('.pp-video-gallery').eq(0),
            elementSettings     = getElementSettings( $scope ),
            $video_play         = $scope.find( '.pp-video-play' ),
            $action             = $gallery.data( 'action' );

        if ( $action == 'inline') {
            $video_play.off( 'click' ).on( 'click', function( e ) {

                e.preventDefault();

                var $iframe = $( "<iframe/>" ),
                    $vid_src = $( this ).data( 'src' ),
                    $player = $( this ).find( '.pp-video-player' );

                $iframe.attr( 'src', $vid_src );
                $iframe.attr( 'frameborder', '0' );
                $iframe.attr( 'allowfullscreen', '1' );
                $iframe.attr( 'allow', 'autoplay;encrypted-media;' );

                $player.html( $iframe );
            });
        }
        
        if ( ! isEditMode ) {
            if ( elementSettings.layout == 'grid' ) {
                if ( $gallery.hasClass('pp-video-gallery-filter-enabled') ) {
                    var $isotope_args = {
                            itemSelector    : '.pp-grid-item-wrap',
                            layoutMode		: 'fitRows',
                            percentPosition : true,
                        },
                        $isotope_gallery = {};

                    $scope.imagesLoaded( function(e) {
                        $isotope_gallery = $gallery.isotope( $isotope_args );
                    });

                    $scope.on( 'click', '.pp-gallery-filter', function() {
                        var $this = $(this),
                            filterValue = $this.attr('data-filter');

                        $this.siblings().removeClass('pp-active');
                        $this.addClass('pp-active');

                        $isotope_gallery.isotope({ filter: filterValue });
                    });
                }
            }
        }
        
        if ( elementSettings.layout == 'carousel' ) {
            var carousel_wrap   = $scope.find('.pp-video-gallery-wrapper').eq(0),
                $carousel       = $scope.find('.pp-video-gallery').eq(0),
                slider_options  = JSON.parse( carousel_wrap.attr('data-slider-settings') );

            $carousel.slick(slider_options);
			
			PPSliderUpdate($, $carousel, '', 'slick');
        }
	};

    var AlbumHandler = function ($scope, $) {
        var $album              = $scope.find('.pp-album').eq(0),
            $id                 = $album.data('id'),
            $fancybox_thumbs    = $album.data('fancybox-class'),
            $fancybox_axis		= $album.data('fancybox-axis'),
            elementSettings     = getElementSettings( $scope ),
            $lightbox_selector  = '[data-fancybox="'+$id+'"]';

        if ( elementSettings.lightbox_library == 'fancybox' ) {
            $($lightbox_selector).fancybox({
                loop:               'yes' === elementSettings.loop,
                arrows:             'yes' === elementSettings.arrows,
                infobar:            'yes' === elementSettings.slides_counter,
                keyboard:           'yes' === elementSettings.keyboard,
                toolbar:            'yes' === elementSettings.toolbar,
                buttons:            elementSettings.toolbar_buttons,
                animationEffect:    elementSettings.lightbox_animation,
                transitionEffect:   elementSettings.transition_effect,
				baseClass:			$fancybox_thumbs,
				thumbs: {
					autoStart:	'yes' === elementSettings.thumbs_auto_start,
					axis:		$fancybox_axis
				}
            });
        }
	};
    
    var TestimonialsCarouselHandler = function ( $scope, $ ) {
        var $testimonials           = $scope.find( '.pp-testimonials' ).eq( 0 ),
            $testimonials_wrap      = $scope.find( '.pp-testimonials-wrap' ),
            $testimonials_layout    = $testimonials.data( 'layout' );

            if ( $testimonials_layout == 'carousel' || $testimonials_layout == 'slideshow' ) {
                var $slider_options = JSON.parse( $testimonials.attr('data-slider-settings') ),
                    $thumbs_nav     = $scope.find( '.pp-testimonials-thumb-item-wrap' ),
                    elementSettings = getElementSettings( $scope );
                
                $testimonials.slick( $slider_options );

                if ( $testimonials_layout == 'slideshow' && elementSettings.thumbnail_nav == 'yes' ) {
                    $thumbs_nav.removeClass('pp-active-slide');
                    $thumbs_nav.eq(0).addClass('pp-active-slide');

                    $testimonials.on('beforeChange', function ( event, slick, currentSlide, nextSlide ) {
                        var currentSlide = nextSlide;
                        $thumbs_nav.removeClass('pp-active-slide');
                        $thumbs_nav.eq( currentSlide ).addClass('pp-active-slide');
                    });

                    $thumbs_nav.each( function( currentSlide ) {
                        $(this).on( 'click', function ( e ) {
                            e.preventDefault();
                            $testimonials.slick( 'slickGoTo', currentSlide );
                        });
                    });
                }

                $testimonials.slick( 'setPosition' );
				
				PPSliderUpdate($, $testimonials, '', 'slick');

                if ( isEditMode ) {
                    $testimonials_wrap.resize( function() {
                        $testimonials.slick( 'setPosition' );
                    });
                }

            }
	};
	
    var ImageScrollHandler = function($scope, $) {
        var scrollElement    = $scope.find(".pp-image-scroll-container"),
            scrollOverlay    = scrollElement.find(".pp-image-scroll-overlay"),
            scrollVertical   = scrollElement.find(".pp-image-scroll-vertical"),
			elementSettings  = getElementSettings( $scope ),
            imageScroll      = scrollElement.find('.pp-image-scroll-image img'),
            direction        = elementSettings.direction_type,
            reverse			 = elementSettings.reverse,
            trigger			 = elementSettings.trigger_type,
            transformOffset  = null;
        
        function startTransform() {
            imageScroll.css("transform", (direction == "vertical" ? "translateY" : "translateX") + "( -" +  transformOffset + "px)");
        }
        
        function endTransform() {
            imageScroll.css("transform", (direction == 'vertical' ? "translateY" : "translateX") + "(0px)");
        }
        
        function setTransform() {
            if( direction == "vertical" ) {
                transformOffset = imageScroll.height() - scrollElement.height();
            } else {
                transformOffset = imageScroll.width() - scrollElement.width();
            }
        }
        
        if( trigger == "scroll" ) {
            scrollElement.addClass("pp-container-scroll");
            if ( direction == "vertical" ) {
                scrollVertical.addClass("pp-image-scroll-ver");
            } else {
                scrollElement.imagesLoaded(function() {
                  scrollOverlay.css( { "width": imageScroll.width(), "height": imageScroll.height() } );
                });
            }
        } else {
            if ( reverse === 'yes' ) {
                scrollElement.imagesLoaded(function() {
                    scrollElement.addClass("pp-container-scroll-instant");
                    setTransform();
                    startTransform();
                });
            }
            if ( direction == "vertical" ) {
                scrollVertical.removeClass("pp-image-scroll-ver");
            }
            scrollElement.mouseenter(function() {
                scrollElement.removeClass("pp-container-scroll-instant");
                setTransform();
                reverse === 'yes' ? endTransform() : startTransform();
            });

            scrollElement.mouseleave(function() {
                reverse === 'yes' ? startTransform() : endTransform();
            });
        }
    };

	var TwitterTimelineHandler = function ($scope, $) {
		$(document).ready(function () {
			if ('undefined' !== twttr) {
				twttr.widgets.load();
			}
		});
	};

    var TabbedGalleryHandler = function ( $scope, $ ) {
        var $carousel            = $scope.find( '.pp-tabbed-carousel' ).eq( 0 ),
            $slider_id           = $carousel.attr( 'id' ),
            $carousel_settings   = $carousel.data('slider-settings'),
            $slider_wrap         = $scope.find( '.pp-tabbed-gallery-wrapper' ),
            $tabs_nav            = $scope.find( '.pp-gallery-filters .pp-gallery-filter' ),
            elementSettings      = getElementSettings( $scope );
        
            $carousel.slick( $carousel_settings );

            $carousel.slick( 'setPosition' );

            $tabs_nav.removeClass('pp-active-slide');
            $tabs_nav.eq(0).addClass('pp-active-slide');

            $carousel.on('beforeChange', function ( event, slick, currentSlide, nextSlide ) {
                var $tab_index = $tabs_nav.eq( nextSlide ).data('index'),
					$tab_group_c = $tabs_nav.eq( currentSlide ).data('group'),
					$tab_group_n = $tabs_nav.eq( nextSlide ).data('group');
				
				if ( $tab_group_c != $tab_group_n ) {
					$tabs_nav.removeClass('pp-active-slide');
					var $group = $tabs_nav.eq( nextSlide ).data('group');
					$tabs_nav.filter('[data-group="' + $group + '"]').addClass('pp-active-slide');
				}
            });

            $tabs_nav.each( function( nextSlide ) {
                $(this).on( 'click', function ( e ) {
                    var $current_slide = $(this).data('index');
                    e.preventDefault();
                    $carousel.slick( 'slickGoTo', $current_slide );
                });
            });

            if ( isEditMode ) {
                $slider_wrap.resize( function() {
                    $carousel.slick( 'setPosition' );
                });
            }
        
            var $lightbox_selector = '.slick-slide:not(.slick-cloned) .pp-image-slider-slide-link[data-fancybox="'+$slider_id+'"]';
        
            $($lightbox_selector).fancybox({
                loop:       true,
            });
    };
    
    var TabbedContentCarouselHandler = function ( $scope, $ ) {
        var $carousel            = $scope.find( '.pp-tabbed-carousel' ).eq( 0 ),
            $slider_id           = $carousel.attr( 'id' ),
            $carousel_settings   = $carousel.data('slider-settings'),
            $slider_wrap         = $scope.find( '.pp-image-slider-wrap' ),
            $tabs_nav            = $scope.find( '.pp-gallery-filters .pp-gallery-filter' ),
            elementSettings      = getElementSettings( $scope ),
            $video_play         = $scope.find( '.pp-video-play' ),
            $action             = $carousel.data( 'action' );

        if ( $action == 'inline') {
            $video_play.off( 'click' ).on( 'click', function( e ) {

                e.preventDefault();

                var $iframe = $( "<iframe/>" ),
                    $vid_src = $( this ).data( 'src' ),
                    $player = $( this ).find( '.pp-video-player' );

                $iframe.attr( 'src', $vid_src );
                $iframe.attr( 'frameborder', '0' );
                $iframe.attr( 'allowfullscreen', '1' );
                $iframe.attr( 'allow', 'autoplay;encrypted-media;' );

                $player.html( $iframe );
            });
        }
        
		$carousel.slick( $carousel_settings );

		$carousel.slick( 'setPosition' );

		$tabs_nav.removeClass('pp-active-slide');
		$tabs_nav.eq(0).addClass('pp-active-slide');

		$carousel.on('beforeChange', function ( event, slick, currentSlide, nextSlide ) {
			var currentSlide = nextSlide,
				$tab_index = $tabs_nav.eq( currentSlide ).data('index');

				$tabs_nav.removeClass('pp-active-slide');
				$tabs_nav.eq( currentSlide ).addClass('pp-active-slide');
		});

		$tabs_nav.each( function( currentSlide ) {
			$(this).on( 'click', function ( e ) {
				var $current_slide = $(this).data('index');
				e.preventDefault();
				$carousel.slick( 'slickGoTo', $current_slide );
			});
		});

		if ( isEditMode ) {
			$slider_wrap.resize( function() {
				$carousel.slick( 'setPosition' );
			});
		}

		var $lightbox_selector = '.slick-slide:not(.slick-cloned) .pp-image-slider-slide-link[data-fancybox="'+$slider_id+'"]';

		$($lightbox_selector).fancybox({
			loop:       true,
		});
    };
	
	var WooMiniCartHandler = function ($scope, $) {
		new PPWooMiniCart( $scope );
	};
    
    var CouponsHandler = function ($scope, $) {
        var elementSettings			= getElementSettings( $scope ),
            coupon					= $scope.find('.pp-coupon-wrap');
            
		if ( 'carousel' === elementSettings.layout ) {
        	var carousel_wrap		= $scope.find('.swiper-container-wrap').eq(0),
				carousel			= carousel_wrap.find('.pp-coupons-carousel'),
				slider_options		= JSON.parse( carousel_wrap.attr('data-slider-settings') ),
				mySwiper			= new Swiper(carousel, slider_options);
			
			if ( 'yes' === elementSettings.pause_on_hover ) {
				carousel.on('mouseenter', function(e){
					mySwiper.autoplay.stop();
				});
				carousel.on('mouseleave', function(e){
					mySwiper.autoplay.start();
				});
			}
		}
		
		$scope.find('.pp-coupon-wrap').each(function () {
            var couponCode = $(this).find('.pp-coupon-code').attr('data-coupon-code');

			$(this).find('.pp-coupon-code').not('.pp-copied').on('click', function(){
				var clicked = $(this);
				var tempInput = '<input type="text" value="' + couponCode + '" id="ppCouponInput">';

				clicked.append(tempInput);

				var copyText = document.getElementById("ppCouponInput");
				copyText.select();
				document.execCommand("copy");
				$('#ppCouponInput').remove();

				if ('copy' === elementSettings.coupon_style) {
					clicked.addClass('pp-copied');
					clicked.find('.pp-coupon-copy-text').fadeOut().text('Copied').fadeIn();
				} else {
					clicked.find('.pp-coupon-reveal-wrap').css({
						'transform': 'translate(200px, 0px)',
					});
					setTimeout(function () {
						clicked.find('.pp-coupon-code-text-wrap').removeClass('pp-unreavel');
						clicked.find('.pp-coupon-code-text').text(couponCode);
						clicked.find('.pp-coupon-reveal-wrap').remove();
					}, 150);
					setTimeout(function () {
						clicked.addClass('pp-copied');
						clicked.find('.pp-coupon-copy-text').fadeOut().text('Copied').fadeIn();
					}, 500);
				}
			});
		});
    };
    
    var CategoriesHandler = function ($scope, $) {
        var elementSettings			= getElementSettings( $scope ),
            $cat_box_wrap			= $scope.find('.pp-category-wrap'),
            $cat_box				= $cat_box_wrap.find('.pp-category');
		
		if ( elementSettings.equal_height == 'yes' ) {
			var highestBox = 0;
			$cat_box_wrap.each(function () {
				if ( $( this ).outerHeight() > highestBox) {
					highestBox = $( this ).outerHeight();
				}
			});

			$cat_box.css( 'height', highestBox+'px' );
		}
            
		if ( 'carousel' === elementSettings.layout ) {
        	var carousel_wrap		= $scope.find('.swiper-container-wrap').eq(0),
				carousel			= carousel_wrap.find('.pp-categories-carousel'),
				slider_options		= JSON.parse( carousel_wrap.attr('data-slider-settings') ),
				mySwiper			= new Swiper(carousel, slider_options);
			
			if ( 'yes' === elementSettings.pause_on_hover ) {
				carousel.on('mouseenter', function(e){
					mySwiper.autoplay.stop();
				});
				carousel.on('mouseleave', function(e){
					mySwiper.autoplay.start();
				});
			}
			
			PPSliderUpdate($, $carousel, mySwiper);
		}
    };

	var GFormsHandler = function( $scope, $ ) {
		if ( 'undefined' == typeof $scope )
			return;

		$scope.find('select:not([multiple])').each(function() {
			var	gf_select_field = $( this );
			if( gf_select_field.next().hasClass('chosen-container') ) {
				gf_select_field.next().wrap( "<span class='pp-gf-select-custom'></span>" );
			} else {
				gf_select_field.wrap( "<span class='pp-gf-select-custom'></span>" );
			}
		});
	}

	var BreadcrumbsHandler = function( $scope, $ ) {
		var elementSettings			= getElementSettings( $scope ),
            $breadcrumbs_type		= elementSettings.breadcrumbs_type;

		if ( $breadcrumbs_type != 'powerpack' ) {
			$scope.find('.pp-breadcrumbs a' ).parent().css({'padding' : '0', 'background-color' : 'transparent', 'border' : '0', 'margin' : '0', 'box-shadow' : 'none'});
		}
		if ( $breadcrumbs_type == 'yoast' || $breadcrumbs_type == 'rankmath' ) {
			$scope.find('.pp-breadcrumbs a' ).parent().parent().css({'padding' : '0', 'background-color' : 'transparent', 'border' : '0', 'margin' : '0', 'box-shadow' : 'none'});
		}
	}
    
    $(window).on('elementor/frontend/init', function () {
        if ( elementorFrontend.isEditMode() ) {
			isEditMode = true;
		}
        
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-image-hotspots.default', ImageHotspotHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-image-comparison.default', ImageComparisonHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-counter.default', CounterHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-logo-carousel.default', PPSwiperSliderHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-info-box-carousel.default', InfoBoxCarouselHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-instafeed.default', InstaFeedPopupHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-team-member-carousel.default', PPSwiperSliderHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-modal-popup.default', ModalPopupHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-table.default', TableHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-toggle.default', ToggleHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-countdown.default', PPCountdownHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-image-gallery.default', ImageGalleryHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-image-slider.default', ImageSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-advanced-menu.default', AdvancedMenuHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-advanced-tabs.default', AdvancedTabsHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-offcanvas-content.default', OffCanvasContentHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-buttons.default', PPButtonHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-showcase.default', ShowcaseHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-timeline.default', TimelineHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-card-slider.default', PPSwiperSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-image-accordion.default', ImageAccordionHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-advanced-accordion.default', AdvancedAccordionHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-content-ticker.default', PPSwiperSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-magazine-slider.default', MagazineSliderHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-video.default', VideoHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-video-gallery.default', VideoGalleryHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-testimonials.default', TestimonialsCarouselHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-scroll-image.default', ImageScrollHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-album.default', AlbumHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-twitter-timeline.default', TwitterTimelineHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-twitter-tweet.default', TwitterTimelineHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-tabbed-gallery.default', TabbedGalleryHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-tabbed-content-carousel.default', TabbedContentCarouselHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-woo-mini-cart.default', WooMiniCartHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-faq.default', AdvancedAccordionHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-coupons.default', CouponsHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-categories.default', CategoriesHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-gravity-forms.default', GFormsHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-breadcrumbs.default', BreadcrumbsHandler);
    });
    
}(jQuery));