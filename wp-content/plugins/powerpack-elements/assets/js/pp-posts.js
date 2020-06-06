( function( $ ) {

	var loadStatus = true;
	var count = 1;
	var loader = '';
	var total = 0;
	
	function equalHeight( slider_wrapper ) {
		var slickSlider = slider_wrapper.find('.pp-posts-carousel'),
            equalHeight = slickSlider.data( 'equal-height' );
		
		if ( 'yes' != equalHeight ) {
        	return;
        }
		
		slickSlider.find('.slick-slide').height('auto');

		var slickTrack = slickSlider.find('.slick-track'),
			slickTrackHeight = $(slickTrack).height();

		slickSlider.find('.slick-slide').css('height', slickTrackHeight + 'px');
	}
	
	var PostsHandler = function( $scope, $ ) {
		
		var container = $scope.find( '.pp-posts-container' ),
			selector = $scope.find( '.pp-posts-grid' ),
			layout = $scope.find( '.pp-posts' ).data( 'layout' ),
			search = $scope.find( '.pp-post-filters-container' ).data( 'search-form' ),
			$search_input = $scope.find( '.pp-search-form-input' ),
			search_action = $scope.find( '.pp-post-filters-container' ).data( 'search-action' ),
			loader = $scope.find( '.pp-posts-loader' );

		if ( 'masonry' == layout ) {

			$scope.imagesLoaded( function(e) {

				selector.isotope({
					layoutMode: layout,
					itemSelector: '.pp-grid-item-wrap',
				});

			});
		}

		set_posts_count( $scope, 1 );
		
		$scope.find( '.pp-post-filter' ).off( 'click' ).on( 'click', function() {
			$( this ).siblings().removeClass( 'pp-filter-current' );
			$( this ).addClass( 'pp-filter-current' );
			count = 1;

			_postsFilterAjax( $scope, $( this ) );

		});
		
		if ( 'show' == search ) {
			$search_input.on({
                focus: function focus() {
                    $scope.find( '.pp-search-form' ).addClass('pp-search-form-focus');
                },
                blur: function blur() {
                    $scope.find( '.pp-search-form' ).removeClass('pp-search-form-focus');
                }
            });
			
			if ( 'instant' == search_action ) {
				$scope.find('.pp-search-form-input').keyup(debounce(function () {
					_postsSearchAjax( $scope, $( this ) );
				}));      

				// debounce so filtering doesn't happen every millisecond
				function debounce(pp, threshold) {
					var timeout;
					threshold = threshold || 100;
					return function debounced() {
						clearTimeout(timeout);
						var args = arguments;
						var _this = this;

						function delayed() {
							pp.apply(_this, args);
						}
						timeout = setTimeout(delayed, threshold);
					};
				}
			} else if ( 'button-click' == search_action ) {
				$scope.find( '.pp-search-form-submit' ).on( 'click', function() {

					_postsSearchAjax( $scope, $( this ) );

				});
			}
		}
		
		if ( container.find('.pp-posts').hasClass( 'pp-posts-skin-checkerboard' ) && $scope.hasClass( 'pp-equal-height-yes' ) ) {
			function setEqualHeight() {
				var elementorBreakpoints = elementorFrontend.config.breakpoints,
					maxHeight = 0,
					$height = 'auto';
				
				$scope.find('.pp-post-wrap').each( function() {
					if( $(this).find('.pp-post-content').outerHeight() > maxHeight ){
						maxHeight = $(this).find('.pp-post-content').outerHeight();
					}
				});
				if ($(window).width() >= elementorBreakpoints.md) {
					$height = maxHeight;
				}
				$scope.find('.pp-post-wrap').css('height',$height);
			}

			if ( container.find('.pp-posts').hasClass( 'pp-posts-height-auto' ) ) {
				$(setEqualHeight);
				$(window).resize(setEqualHeight);
			}
		}

		if ( container.hasClass( 'pp-posts-infinite-scroll' ) ) {

			var windowHeight50 = jQuery( window ).outerHeight() / 1.25;

			$( window ).scroll( function () {

				if ( elementorFrontend.isEditMode() ) {
					loader.show();
					return false;
				}

				if ( ( $( window ).scrollTop() + windowHeight50 ) >= ( $scope.find( '.pp-post:last' ).offset().top ) ) {

					var $args = {
						'page_id':		$scope.find( '.pp-posts-grid' ).data('page'),
						'widget_id':	$scope.data( 'id' ),
						'filter':		$scope.find( '.pp-filter-current' ).data( 'filter' ),
						'taxonomy':		$scope.find( '.pp-filter-current' ).data( 'taxonomy' ),
						'skin':			$scope.find( '.pp-posts-grid' ).data( 'skin' ),
						'page_number':	$scope.find( '.pp-posts-pagination .current' ).next( 'a' ).html()
					};

					total = $scope.find( '.pp-posts-pagination-wrap' ).data( 'total' );

					if( true == loadStatus ) {

						if ( count < total ) {
							loader.show();
							_callAjax( $scope, $args, true );
							count++;
							loadStatus = false;
						}

					}
				}
			} );
		}
		
		if ( 'carousel' == layout ) {
			var $carousel		= $scope.find( '.pp-posts-carousel' ).eq( 0 ),
				$slider_options	= JSON.parse( $carousel.attr('data-slider-settings') );

			if ( $carousel.length > 0 ) {
				$scope.imagesLoaded( function() {
					$carousel.slick($slider_options);
				});
			}

			$($carousel).on('setPosition', function () {
				equalHeight($scope);
			});
		}
	}

	$( document ).on( 'click', '.pp-post-load-more', function( e ) {

		$scope = $( this ).closest( '.elementor-widget-pp-posts' );
		loader = $scope.find( '.pp-posts-loader' );

		e.preventDefault();

		if( elementorFrontend.isEditMode() ) {
			loader.show();
			return false;
		}

		var page_count = get_posts_count( $scope ),
			category = $scope.find( '.pp-filter-current' ).data( 'filter' ),
			taxonomy = $scope.find( '.pp-filter-current' ).data( 'taxonomy' );

		var $args = {
			'page_id':		$scope.find( '.pp-posts-grid' ).data('page'),
			'widget_id':	$scope.data( 'id' ),
			'filter':		( category ) ? category : '',
			'taxonomy':		( taxonomy ) ? taxonomy : '',
			'skin':			$scope.find( '.pp-posts-grid' ).data( 'skin' ),
			'page_number':	( page_count + 1 )
		};

		total = $scope.find( '.pp-posts-pagination-wrap' ).data( 'total' );

		if( true == loadStatus ) {

			if ( page_count < total ) {
				loader.show();
				$( this ).hide();
				_callAjax( $scope, $args, true, page_count );
				page_count++;
				loadStatus = false;
			}

		}
	} );

	function get_posts_count( $scope ) {
		return $scope.find('.pp-post-load-more').data('count');
	}
	function set_posts_count( $scope, count ) {
		$scope.find('.pp-post-load-more').attr('data-count', count);
	}

	$( 'body' ).delegate( '.pp-posts-pagination-ajax .page-numbers', 'click', function( e ) {

		$scope = $( this ).closest( '.elementor-widget-pp-posts' );
		
		if ( 'main' == $scope.find( '.pp-posts-grid' ).data( 'query-type' ) ) {
			return;
		}

		e.preventDefault();

		$scope.find( '.pp-posts-grid .pp-post' ).last().after( '<div class="pp-post-loader"><div class="pp-loader"></div><div class="pp-loader-overlay"></div></div>' );

		var page_number = 1;
		var curr = parseInt( $scope.find( '.pp-posts-pagination .page-numbers.current' ).html() );

		if ( $( this ).hasClass( 'next' ) ) {
			page_number = curr + 1;
		} else if ( $( this ).hasClass( 'prev' ) ) {
			page_number = curr - 1;
		} else {
			page_number = $( this ).html();
		}

		$scope.find( '.pp-posts-grid .pp-post' ).last().after( '<div class="pp-post-loader"><div class="pp-loader"></div><div class="pp-loader-overlay"></div></div>' );

		var $args = {
			'page_id':		$scope.find( '.pp-posts-grid' ).data('page'),
			'widget_id':	$scope.data( 'id' ),
			'filter':		$scope.find( '.pp-filter-current' ).data( 'filter' ),
			'taxonomy':		$scope.find( '.pp-filter-current' ).data( 'taxonomy' ),
			'skin':			$scope.find( '.pp-posts-grid' ).data( 'skin' ),
			'page_number':	page_number
		};

		$('html, body').animate({
			scrollTop: ( ( $scope.find( '.pp-posts-container' ).offset().top ) - 30 )
		}, 'slow');

		_callAjax( $scope, $args );

	} );

	var _postsFilterAjax = function( $scope, $this ) {

		$scope.find( '.pp-posts-grid .pp-grid-item-wrap' ).last().after( '<div class="pp-posts-loader-wrap"><div class="pp-loader"></div><div class="pp-loader-overlay"></div></div>' );

		var $args = {
			'page_id':		$scope.find( '.pp-posts-grid' ).data('page'),
			'widget_id':	$scope.data( 'id' ),
			'filter':		$this.data( 'filter' ),
			'taxonomy':		$this.data( 'taxonomy' ),
			'skin':			$scope.find( '.pp-posts-grid' ).data( 'skin' ),
			'page_number':	1
		};

		_callAjax( $scope, $args );
	}
    
    var _postsSearchAjax = function( $scope, $this ) {

		$scope.find( '.pp-posts-grid .pp-grid-item-wrap' ).last().after( '<div class="pp-posts-loader-wrap"><div class="pp-loader"></div><div class="pp-loader-overlay"></div></div>' );

		var $args = {
			'page_id':		$scope.find( '.pp-posts-grid' ).data('page'),
			'widget_id':	$scope.data( 'id' ),
			'search':		$scope.find( '.pp-search-form-input' ).val(),
			'page_number':	1
		};
        
		_callAjax( $scope, $args );
	}

	var _callAjax = function( $scope, $obj, $append, $count ) {

		var loader = $scope.find( '.pp-posts-loader' );
		
		$.ajax({
			url: pp.ajax_url,
			data: {
				action:			'pp_get_post',
				page_id:		$obj.page_id,
				widget_id:		$obj.widget_id,
				category:		$obj.filter,
				search:			$obj.search,
				taxonomy:		$obj.taxonomy,
				skin:			$obj.skin,
				page_number:	$obj.page_number,
				nonce:			pp_posts_script.posts_nonce,
			},
			dataType: 'json',
			type: 'POST',
			success: function( data ) {

				var sel = $scope.find( '.pp-posts-grid' );

				if ( true == $append ) {

					var html_str = data.data.html;

					sel.append( html_str );
				} else {
					sel.html( data.data.html );
				}

				$scope.find( '.pp-posts-pagination-wrap' ).html( data.data.pagination );

				var layout = $scope.find( '.pp-posts-grid' ).data( 'layout' ),
					selector = $scope.find( '.pp-posts-grid' );

				if ( 'masonry' == layout ) {

					$scope.imagesLoaded( function() {
						selector.isotope( 'destroy' );
						selector.isotope({
							layoutMode: layout,
							itemSelector: '.pp-grid-item-wrap',
						});
					});
				}

				//	Complete the process 'loadStatus'
				loadStatus = true;
				if ( true == $append ) {
					loader.hide();
					$scope.find( '.pp-post-load-more' ).show();
				}

				set_posts_count( $scope, $obj.page_number );
				
				$count = $count + 1;
				
				if( $count == total ) {
					$scope.find( '.pp-post-load-more' ).hide();
				}
			}
		});
	}

	$( window ).on( 'elementor/frontend/init', function () {

		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.classic', PostsHandler );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.card', PostsHandler );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.checkerboard', PostsHandler );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.creative', PostsHandler );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.event', PostsHandler );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.news', PostsHandler );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.portfolio', PostsHandler );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.overlap', PostsHandler );
		
		elementorFrontend.hooks.addAction( 'frontend/element_ready/pp-posts.template', PostsHandler );

	});

} )( jQuery );
