'use strict';

(function ($) {
  $.fn.unveil = function (threshold, callback) {
    var $w = $(window),
        th = threshold || 0,
        retina = window.devicePixelRatio > 1,
        attrib = retina ? "data-src-retina" : "data-src",
        images = this,
        loaded;
    this.one("unveil", function () {
      var source = this.getAttribute(attrib);
      source = source || this.getAttribute("data-src");

      if (source) {
        this.setAttribute("src", source);
        if (typeof callback === "function") callback.call(this);
      }
    });

    function unveil() {
      var inview = images.filter(function () {
        var $e = $(this),
            wt = $w.scrollTop(),
            wb = wt + $w.height(),
            et = $e.offset().top,
            eb = et + $e.height();
        return eb >= wt - th && et <= wb + th;
      });
      loaded = inview.trigger("unveil");
      images = images.not(loaded);
    }

    $w.on("scroll.unveil resize.unveil lookup.unveil", unveil);
    unveil();
    return this;
  };
})(window.jQuery || window.Zepto);

let intImageLoad = (childClass, parentClass) => {
  var $images = $(childClass);

  if ($images.length) {
    $images.unveil(1, function () {
      $(this).load(function () {
        $(this).parents(parentClass).first().addClass('image-loaded');
        $(this).removeAttr('data-src');
        $(this).removeAttr('data-srcset');
        $(this).removeAttr('data-sizes');
      });
    });
  }
};

let initImageProduct = () => {
  var $images = $('.product-image:not(.image-loaded) .unveil-image, .tbay-gallery-varible:not(.image-loaded) .unveil-image');

  if ($images.length) {
    $images.unveil(1, function () {
      $(this).load(function () {
        $(this).parents('.product-image, .tbay-gallery-varible').first().addClass('image-loaded');
        $(this).removeAttr('data-src');
      });
    });
  }
};

let layzyLoadImage = () => {
  jQuery(window).off('scroll.unveil resize.unveil lookup.unveil');
  intImageLoad('.tbay-image-loaded:not(.image-loaded) .unveil-image', '.tbay-image-loaded');
  initImageProduct();
};

jQuery(document).ready(function ($) {
  setTimeout(function () {
    layzyLoadImage();
  }, 200);
  $(document.body).on('puca_load_more', () => {
    layzyLoadImage();
  });
  $(document.body).on('puca_tabs_carousel', () => {
    layzyLoadImage();
  });
  $(document.body).on('reset_image', () => {
    layzyLoadImage();
  });
  $(document.body).on('reset_data', () => {
    layzyLoadImage();
  });
});

var CustomlayzyLoadImage = function ($scope, $) {
  setTimeout(function () {
    layzyLoadImage();
  }, 200);
};

jQuery(window).on('elementor/frontend/init', function () {
  if (jQuery.isArray(puca_settings.elements_ready.layzyloadimage)) {
    $.each(puca_settings.elements_ready.layzyloadimage, function (index, value) {
      elementorFrontend.hooks.addAction('frontend/element_ready/tbay-' + value + '.default', CustomlayzyLoadImage);
    });
  }
});
