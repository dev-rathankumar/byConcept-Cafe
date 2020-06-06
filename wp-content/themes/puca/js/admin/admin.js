'use strict';

!function ($) {
  $("body").on("click", ".tbay-checkbox", function () {
    jQuery('.' + this.id).toggle();
  });
  $('.tbay-wpcolorpicker').each(function () {
    $(this).wpColorPicker();
  });
}(window.jQuery);
