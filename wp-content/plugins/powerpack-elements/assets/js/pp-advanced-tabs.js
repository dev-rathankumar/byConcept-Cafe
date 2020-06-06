(function($) {

	PPAdvancedTabs = function( $scope )
	{
		//this.settings 	= settings;
		this.elem  = $scope;
		this.elemClass  = $scope.find('.pp-advanced-tabs');
		this.tabs  = $scope.find('.pp-tabs-labels .pp-advanced-tabs-title');
		this.tabsResponsive  = $scope.find('.pp-tabs-panels .pp-advanced-tabs-title');
		this._init();
	};

	PPAdvancedTabs.prototype = {

		settings	: {},
		elemClass   : '',

		_init: function()
		{
			var win = $(window);

			this.tabs.click($.proxy(this._labelClick, this));
			this.tabsResponsive.click($.proxy(this._responsiveLabelClick, this));

			this._responsiveCollapsed();

			if(this.elem.find('.at-vertical').length > 0) {
				this._resize();
				win.off('resize' + this.elemClass);
				win.on('resize' + this.elemClass, $.proxy(this._resize, this));
			}

			this._hashChange();

			$(window).on('hashchange', $.proxy( this._hashChange, this ));
			//$(window).on('resize', $.proxy( this._responsiveCollapsed, this ));
		},

		_hashChange: function()
		{
			if( location.hash && $(location.hash).length > 0 ) {
				var element = $(location.hash + '.pp-tabs-label');
				console.log(element);
				if ( element && element.length > 0 ) {
					location.href = '#';
					$('html, body').animate({
						scrollTop: element.parents('.pp-advanced-tabs').offset().top
					}, 50, function() {
						if ( ! element.hasClass('pp-tab-active') ) {
							element.trigger('click');
						}
					});
				}
			}
		},

		_labelClick: function(e)
		{
			var label       = $(e.target).closest('.pp-tabs-label'),
				index       = label.data('index'),
				wrap        = label.closest('.pp-advanced-tabs'),
				tabs_wrap   = label.closest('.pp-advanced-tabs-wrapper'),
				allIcons    = wrap.find('.pp-tabs-label .fa'),
				icon        = wrap.find('.pp-tabs-label[data-index="' + index + '"] .fa');

			// Toggle the responsive icons.
			//allIcons.addClass('fa-plus');
			//icon.removeClass('fa-plus');

			if ( tabs_wrap.hasClass('at-vertical') ) {
				$('html, body').animate({
					scrollTop: wrap.offset().top
				}, 500 );
			}

			// Toggle the tabs.
			wrap.find('.pp-tabs-labels:first > .pp-tab-active').removeClass('pp-tab-active');
			wrap.find('.pp-tabs-panels:first > .pp-tabs-panel > .pp-tab-active').removeClass('pp-tab-active');
			wrap.find('.pp-tabs-panels:first > .pp-tabs-panel > .pp-tabs-label').removeClass('pp-tab-active');

			wrap.find('.pp-tabs-labels:first > .pp-tabs-label[data-index="' + index + '"]').addClass('pp-tab-active');
			wrap.find('.pp-tabs-panels:first > .pp-tabs-panel > #pp-advanced-tabs-content-' + index ).addClass('pp-tab-active');
			wrap.find('.pp-tabs-panels:first > .pp-tabs-panel > .pp-tabs-label[data-index="' + index + '"]').addClass('pp-tab-active');

			$(document).trigger('pp-tabs-switched', [ wrap.find( '#pp-advanced-tabs-content-' + index ) ]);
		},

		_responsiveLabelClick: function(e)
		{
			var label           = $(e.target).closest('.pp-tabs-label'),
				wrap            = label.closest('.pp-advanced-tabs'),
				index           = label.data('index'),
				content         = label.siblings('.pp-advanced-tabs-content'),
				activeContent   = wrap.find('.pp-advanced-tabs-content.pp-tab-active'),
				activeIndex     = activeContent.data('index'),
				allIcons        = wrap.find('.pp-tabs-label .fa'),
				icon            = label.find('.fa');

			// Should we proceed?
			if(index == activeIndex) {
				activeContent.slideUp('normal');
				activeContent.removeClass('pp-tab-active');
				this.elemClass.find('.pp-tabs-panels .pp-tabs-label').removeClass('pp-tab-active');
				//$(this.elemClass + ' .pp-tabs-panels .pp-tabs-label').removeClass('pp-tab-active');
				wrap.removeClass('pp-tabs-animation');
				return;
			}
			if(wrap.hasClass('pp-tabs-animation')) {
				return;
			}

			// Toggle the icons.
			//allIcons.addClass('fa-plus');
			//icon.removeClass('fa-plus');

			// Run the animations.
			wrap.addClass('pp-tabs-animation');
			activeContent.slideUp('normal');


			content.slideDown('normal', function(){

				wrap.find('.pp-tab-active').removeClass('pp-tab-active');
				wrap.find('.pp-tabs-label[data-index="' + index + '"]').addClass('pp-tab-active');
				content.addClass('pp-tab-active');
				wrap.removeClass('pp-tabs-animation');

				// Content Grid module support.
				if ( 'undefined' !== typeof $.fn.isotope ) {
					content.find('.pp-content-post-grid').isotope('layout');
				}

				if(label.offset().top < $(window).scrollTop() + 100) {
					$('html, body').animate({ scrollTop: label.offset().top - 100 }, 500, 'swing');
				}

				$(document).trigger('pp-tabs-switched', [ content ]);
			});
		},

		_resize: function()
		{
			this.elem.find('.at-vertical').each($.proxy(this._resizeVertical, this));
		},

		_resizeVertical: function(e)
		{
			var wrap    = this.elem.find('.at-vertical'),
				labels  = wrap.find('.pp-tabs-labels'),
				panels  = wrap.find('.pp-tabs-panels');

			panels.css('min-height', labels.height() + 'px');
		},

		_responsiveCollapsed: function()
		{
			if ( $(window).innerWidth() < 769 ) {
				if ( this.settings.responsiveClosed ) {
					this.elemClass.find('.pp-tabs-panels .pp-tabs-label.pp-tab-active').trigger('click');
				}
				this.elemClass.find('.pp-tabs-panels').css('visibility', 'visible');
			}
		}
	};

})(jQuery);
