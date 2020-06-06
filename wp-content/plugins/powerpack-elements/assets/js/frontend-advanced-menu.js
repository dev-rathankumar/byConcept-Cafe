;(function($) {

	PPAdvancedMenu = function( $scope ) {
		
		this.node = $scope;
		this.wrap = $scope.find('.pp-advanced-menu__container');
		this.menu = $scope.find('.pp-advanced-menu');
		this.dropdownMenu = $scope.find('.pp-advanced-menu__container.pp-advanced-menu--dropdown');
		this.menuToggle = $scope.find('.pp-menu-toggle'); // hamburger icon
		this.settings = $scope.find('.pp-advanced-menu__container').data('settings');
		this.menuId = this.settings.menu_id;
		this.menuType = this.settings.menu_type;
		this.menuLayout = this.settings.menu_layout;
		this.onepage_menu = this.settings.onepage_menu;
		this.duration = 400;

		this.init();
	};

	PPAdvancedMenu.prototype = {
		stretchElement: null,

		init: function () {

			if ( ! this.menu.length ) {
				return;
			}
            
            if ( jQuery.fn.smartmenus ) {
                // Override the default stupid detection
                jQuery.SmartMenus.prototype.isCSSOn = function() {
                    return true;
                };

                if ( elementorFrontend.config.is_rtl  ) {
                    jQuery.fn.smartmenus.defaults.rightToLeftSubMenus = true;
                }
            }

			if ( 'horizontal' === this.menuLayout ) {
				if ('undefined' !== typeof $.fn.smartmenus) {
					this.menu.smartmenus({
						subIndicatorsText: '',
						subIndicatorsPos: 'append',
						subMenusMaxWidth: '1000px',
						subMenusMinWidth: ''
					});
				}
			}

			if ( 'vertical' === this.menuLayout ) {
				if ('undefined' !== typeof $.fn.smartmenus) {
					this.menu.smartmenus({
						subIndicatorsText: '',
						subIndicatorsPos: 'append',
					});
				}
			}

			if ( 'default' === this.menuType ) {
				this.initStretchElement();
				this.stretchMenu();
			}

			if ('off-canvas' === this.menuType) {
				this.initOffCanvas();
			}

			if ('full-screen' === this.menuType) {
				this.initFullScreen();
			}

			this.bindEvents();
			
			$(window).on('load', $.proxy(this.resetDimensions, this))
			
			this.menu.smartmenus('refresh');
		},

		getElementSettings: function( setting ) {
			if ( 'undefined' !== typeof this.settings[setting] ) {
				return this.settings[setting];
			}

			return false;
		},

		bindEvents: function () {
			var self = this;

			if ( ! this.menu.length ) {
				return;
			}

			this.menuToggle.on('click', $.proxy( this.toggleMenu, this ));

			if ( 'yes' === this.onepage_menu ) {
				this.menu.on( 'click', '.menu-item > a[href*="#"]', function(e) {
					var $href = $(this).attr('href'),
						$targetID = '';

					if ( $href !== '#' ) {
						$targetID = $href.split('#')[1];

						if ( $('body').find('#' +  $targetID).length > 0 ) {
							e.preventDefault();
							$( this ).toggleClass( 'pp-active' );
							setTimeout(function() {
								$('html, body').animate({
									scrollTop: $('#'+ $targetID).offset().top
								}, 200, function() {
									window.location.hash = $targetID;
								});
							}, 500);
						}
					}

					self.closeMenu();
				});
			}

			if ('default' === this.menuType) {
				elementorFrontend.addListenerOnce(this.node.data('model-cid'), 'resize', $.proxy( this.stretchMenu, this) );
			}

			//self.panelUpdate();

			this.closeMenuESC();
		},

		panelUpdate: function() {
			var self = this;

			if ('undefined' !== typeof elementor && $('body').hasClass('elementor-editor-active')) {
				elementor.hooks.addAction('panel/open_editor/widget/pp-advanced-menu', function (panel, model, view) {
					panel.$el.find('select[data-setting="dropdown"]').on('change', function () {
						if (model.attributes.id === self.menuId) {
							if ($(this).val() === 'all') {
								self.node.find('.pp-advanced-menu--main').hide();
							}
							if ($(this).val() !== 'all') {
								self.node.find('.pp-advanced-menu--main').show();
							}
						}
					});

					if (model.attributes.id === self.menuId && 'all' === self.settings.breakpoint) {
						self.toggleMenu();
					}
				});
			}
		},

		initStretchElement: function () {
			this.stretchElement = new elementorFrontend.modules.StretchElement({ element: this.dropdownMenu });
		},

		stretchMenu: function () {
			if (this.getElementSettings('full_width')) { 
				this.stretchElement.stretch();

				this.dropdownMenu.css('top', this.menuToggle.outerHeight());
			} else {
				this.stretchElement.reset();
			}
		},

		initOffCanvas: function () {
			$('.pp-menu-' + this.settings.menu_id).each(function(id, el) {
				if ($(el).parent().is('body') ) {
					$(el).remove();
				}
			});

			$('.pp-menu-clear').fadeOut(400, function() {
				$(this).remove();
			});
				
			$('body').prepend(this.node.find( '.pp-menu-' + this.settings.menu_id ) );
			$('.pp-menu-' + this.settings.menu_id).css('height', window.innerHeight + 'px');
			$('.pp-menu-' + this.settings.menu_id).find('.pp-menu-close').on( 'click', $.proxy( this.closeMenu, this ));
		},

		initFullScreen: function () {
			$('body').addClass('pp-menu--full-screen');
			$('.pp-menu-' + this.settings.menu_id).css('height', window.innerHeight + 'px');
			$('.pp-menu-' + this.settings.menu_id).find('.pp-menu-close').on('click', $.proxy(this.closeMenu, this));
			//$('.pp-menu-' + this.settings.menu_id).find('.menu-item a').on('click', $.proxy(this.closeMenu, this));
		},
		
		resetDimensions: function() {
			if ( 'full-screen' === this.menuType ) {
				$('.pp-menu-' + this.settings.menu_id).css('height', window.innerHeight + 'px');
			}
		},

		toggleMenu: function () {
			this.menuToggle.toggleClass('pp-active');

			var menuType = this.getElementSettings('menu_type');
			var isActive = this.menuToggle.hasClass('pp-active');
			
			$('html').removeClass('pp-menu-toggle-open');

			if ( isActive ) {
				$('html').addClass('pp-menu-toggle-open');
			}

			if ('default' === menuType) {
				var $dropdownMenu = this.dropdownMenu;

				if (isActive) {
					$dropdownMenu.hide().slideDown(250, function () {
						$dropdownMenu.css('display', '');
					});
					
					this.stretchMenu();
				} else {
					$dropdownMenu.show().slideUp(250, function () {
						$dropdownMenu.css('display', '');
					});
				}
			}

			if ('off-canvas' === menuType) {
				this.toggleOffCanvas();
			}
			if ('full-screen' === menuType) {
				this.toggleFullScreen();
			}
		},

		toggleOffCanvas: function()
		{
			var isActive = this.menuToggle.hasClass('pp-active'),
				element = $('body').find('.pp-menu-' + this.menuId),
				time = this.duration,
				self = this;

			$('html').removeClass('pp-menu-toggle-open');

			if ( isActive ) {
				$('body').addClass('pp-menu--off-canvas');
				$('html').addClass('pp-menu-toggle-open');
				time = 0;
			} else {
				time = this.duration;
			}

			$('.pp-menu-open').removeClass('pp-menu-open');
			$('.pp-advanced-menu--toggle .pp-menu-toggle').not(this.menuToggle).removeClass('pp-active');

			setTimeout(function() {
				$('.pp-menu-off-canvas').removeAttr('style');

				if (isActive) {
					$('body').addClass('pp-menu-open');
					element.addClass('pp-menu-open').css('z-index', '999999');
					if ( $('.pp-menu-clear').length === 0 ) {
						$('body').append('<div class="pp-menu-clear" style="transition: none !important;"></div>');
					}
					$('.pp-menu-clear').off('click').on('click', $.proxy(self.closeMenu, self));
					$('.pp-menu-clear').fadeIn();
				} else {
					$('.pp-menu-open').removeClass('pp-menu-open');
					$('body').removeClass('pp-menu--off-canvas');
					$('html').removeClass('pp-menu-toggle-open');
					$('.pp-menu-clear').fadeOut();
				}
			}, time);
		},

		toggleFullScreen: function() {
			var isActive = this.menuToggle.hasClass('pp-active'),
				element = $('body').find('.pp-menu-' + this.menuId);

			$('html').removeClass('pp-menu-toggle-open');

			if ( isActive ) {
				$('html').addClass('pp-menu-toggle-open');
				this.node.find('.pp-menu-full-screen').addClass('pp-menu-open');
				this.node.find('.pp-menu-full-screen').attr('data-scroll', $(window).scrollTop());
				$(window).scrollTop(0);
			}
		},

		closeMenu: function() {
			if ( 'default' !== this.menuType ) {
				$('.pp-menu-open').removeClass('pp-menu-open');
				this.menuToggle.removeClass('pp-active');

				$('html').removeClass('pp-menu-toggle-open');

				if ( 'full-screen' === this.menuType ) {
					var scrollTop = this.node.find('.pp-menu-full-screen').data('scroll');
					$(window).scrollTop(scrollTop);
				}

				$('.pp-menu-clear').fadeOut();
			}
		},

		closeMenuESC: function() {
			var self = this;

			// menu close on ESC key
			$(document).on('keydown', function (e) {
				if (e.keyCode === 27) { // ESC
					self.closeMenu();
				}
			});
		}

	};

})(jQuery);