'use strict';

class StickyHeader {
  constructor() {
    this.$tbayHeader = $('#tbay-header');
    this.$isExistedEventMiniCartClick = false;

    if (this.$tbayHeader.hasClass('main-sticky-header')) {
      this._initStickyHeader();
    }

    this._widgetProducts();

    $(".search-horizontal .btn-search-totop").on("click", function () {
      $(".container-search-horizontal").toggleClass('active');
    });

    this._clickHorizontalSearch();
  }

  _initStickyHeader() {
    var _this = this;

    var tbay_width = $(window).width();

    var header_height = this._getHeaderHeight();

    $(window).scroll(function () {
      var cart_height_v1 = $('#tbay-top-cart').height() > 0 ? $('#tbay-top-cart.tbay-top-cart.v1').height() : 0;

      if (tbay_width >= 1024) {
        if ($(this).scrollTop() > header_height) {
          if (_this.$tbayHeader.hasClass('sticky-header1')) return;

          _this._stickyHeaderOnDesktop(header_height);
        } else {
          _this.$tbayHeader.next().css('margin-top', 0);

          _this.$tbayHeader.css("top", 0).removeClass('sticky-header1');

          $("#tbay-header.header-v8 .search-full").slideToggle(500);
        }
      }

      if (tbay_width <= 767) {
        _this._fixedHeaderOnMobile();
      }
    });
  }

  _fixedHeaderOnMobile() {
    if ($('body').hasClass('post-type-archive-product')) {
      var NextScroll = $('.archive-shop .tbay-filter + .products').offset().top - $(window).scrollTop();

      if (NextScroll < 99) {
        $('.archive-shop .tbay-filter').next().css('margin-top', $('.archive-shop .tbay-filter').height() + 20);
        $('.archive-shop .tbay-filter').addClass('fixed').css("top", 36 + $('#wpadminbar').outerHeight());
      } else {
        $('.archive-shop .tbay-filter').css("top", 0);
        $('.archive-shop .tbay-filter').removeClass('fixed');
        $('.archive-shop .tbay-filter').next().css('margin-top', 0);
      }
    }
  }

  _stickyHeaderOnDesktop(header_height) {
    if (this.$tbayHeader.hasClass('sticky-header1')) {
      return;
    }

    this.$tbayHeader.addClass('sticky-header1').css("top", $('#wpadminbar').outerHeight());
    $("#tbay-top-cart").slideUp(500, function () {});
    $("#tbay-header.header-v8 .search-full").slideUp(500, function () {});
    this.$tbayHeader.next().css('margin-top', header_height);

    if (this.$isExistedEventMiniCartClick) {
      return;
    }

    $('#tbay-header.sticky-header1 .tbay-topcart a.mini-cart.v1').on('click', () => {
      this.$isExistedEventMiniCartClick = true;
      $('html, body').scrollTop(0);
    });
  }

  _getHeaderHeight() {
    var header_height = this.$tbayHeader.height();
    return header_height;
  }

  _widgetProducts() {
    if ($(".widget-products").hasClass("carousel-blur")) {
      $('.widget-products.carousel-blur').parents('.vc_row[data-vc-full-width="true"]').addClass('tbay-product-carousel-blur');
      $('.widget-products.carousel-blur').parents('.elementor-section-stretched.elementor-section-full_width').addClass('elementor-product-carousel-blur');
    }

    if ($(".widget-products").hasClass("special-home5")) {
      $('.widget-products.special-home5').parents('.vc_row[data-vc-full-width="true"]').addClass('tbay-product-special-home5');
      $('.widget-products.special-home5').parents('.elementor-section-stretched.elementor-section-full_width').addClass('elementor-product-special-home5');
    }
  }

  _clickHorizontalSearch() {
    var $box_search = $('.search-horizontal .btn-search-totop, .container-search-horizontal,.ui-autocomplete.ui-widget-content.style1.horizontal');
    $(window).on("click.Bst", function (event) {
      if ($box_search.has(event.target).length == 0 && !$box_search.is(event.target)) {
        $(".container-search-horizontal").removeClass('active');
      }
    });
  }

}

class AutoComplete {
  constructor() {
    if (jQuery(window).width() >= 1024) {
      this._callAjaxSearch();
    }
  }

  _callAjaxSearch() {
    var acs_action = 'puca_autocomplete_search',
        _this = this,
        jQuerytop = this._getTop(),
        $t = jQuery("input[name=s]:visible");

    $t.autocomplete({
      source: function (req, response) {
        jQuery.ajax({
          url: puca_settings.ajaxurl + '?callback=?&action=' + acs_action,
          dataType: "json",
          data: {
            term: req.term,
            category: this.element.parent().find('.dropdown_product_cat').val(),
            style: this.element.data('style'),
            post_type: this.element.parent().find('.post_type').val()
          },
          success: function (data, event, ui) {
            response(data);
          }
        });
      },
      position: {
        my: 'left+0 top+' + jQuerytop + ''
      },
      minLength: 2,
      autoFocus: true,
      search: function (event) {
        jQuery(event.currentTarget).parents('.tbay-search-form').addClass('load');
      },
      select: function (event, ui) {
        window.location.href = ui.item.link;
      },
      create: function () {
        jQuery(this).data('ui-autocomplete')._renderItem = function (ul, item) {
          var string = '';
          ul.addClass(item.style);

          if (item.image != '') {
            var string = '<a href="' + item.link + '" title="' + item.label + '"><img src="' + item.image + '" ></a>';
          }

          string = string + '<div class="group"><div class="name"><a href="' + item.link + '" title="' + item.label + '">' + item.label + '</a></div>';

          if (item.price != '') {
            string = string + '<div class="price">' + item.price + '</div></div> ';
          }

          var strings = jQuery("<li>").append(string).appendTo(ul);
          return strings;
        };

        jQuery(this).data('ui-autocomplete')._renderMenu = function (ul, items) {
          var that = this;
          jQuery.each(items, function (index, item) {
            that._renderItemData(ul, item);
          });

          if (typeof $t.data('style') !== "undefined" && $t.data('style') != 'search-min') {
            if (items[0].view_all) {
              ul.append('<li class="list-header ui-menu-divider">' + items[0].result + '<a id="search-view-all" data-id="#' + this.element.parents('form').attr('id') + '" href="javascript:void(0)">' + puca_settings.view_all + '</a></li>');
            } else {
              ul.append('<li class="list-header ui-menu-divider">' + items[0].result + '</li>');
            }
          } else {
            if (items[0].view_all) {
              ul.append('<li class="list-header ui-menu-divider">' + items[0].result + '</li>');
              ul.append('<li class="list-bottom ui-menu-divider"><a id="search-view-all" data-id="#' + this.element.parents('form').attr('id') + '" href="javascript:void(0)">' + puca_settings.view_all + '</a></li>');
            } else {
              ul.append('<li class="list-header ui-menu-divider">' + items[0].result + '</li>');
            }
          }

          $(document.body).trigger('puca_search_view_all');
        };
      },
      response: (event, ui) => {
        _this._autoCompeleteResponse(ui.content.length);
      },
      open: (event, ui) => {
        $(event.target).parents('.tbay-search-form').removeClass('load');
        $(event.target).parents('.tbay-search-form').addClass('active');
        var width_ul = $(event.target).parents('form').outerWidth();
        var left = $(event.target).parents('form').offset().left;
        jQuery(event.target).autocomplete("widget").css({
          "width": width_ul,
          "left": left
        });
      },
      close: event => {
        _this._autoCompeleteClose(event);
      }
    });
    $('.tbay-preloader').on("click", function () {
      _this._onClickTbayPreloader(event);
    });
    $(document.body).on('puca_search_view_all', () => {
      $('#search-view-all').on('click', function () {
        $($(this).data('id')).submit();
      });
    });
  }

  _getTop() {
    let jQuerytop = 0;

    switch (jQuery("input[name=s]:visible").data('style')) {
      case 'style1 home5':
        jQuerytop = -47;
        break;

      case 'style1 home6':
        jQuerytop = 20;
        break;

      case 'style1':
        jQuerytop = 24;
        break;

      case 'style1 home5 home8':
        jQuerytop = -45;
        break;
    }

    return jQuerytop;
  }

  _onClickTbayPreloader(event) {
    jQuery(event.currentTarget).parents('.tbay-search-form').removeClass('active');
    jQuery(event.currentTarget).parents('.tbay-search-form').find('input[name=s]').val('');
    jQuery('.tbay-preloader').removeClass('no-results');
  }

  _autoCompeleteResponse(length) {
    let preloader = jQuery(".tbay-preloader");

    if (length === 0) {
      preloader.text(puca_settings.no_results);
      preloader.addClass('no-results');
      preloader.parents('.tbay-search-form').removeClass('load');
      preloader.parents('.tbay-search-form').addClass('active');
    } else {
      preloader.empty();
      preloader.removeClass('no-results');
    }
  }

  _autoCompeleteClose(event) {
    jQuery(event.target).parents('.tbay-search-form').removeClass('load');
    jQuery(event.target).parents('.tbay-search-form').removeClass('active');
  }

}

jQuery(document).ready(() => {
  new StickyHeader();
  new AutoComplete();
});
