'use strict';

require('../chunk-4650308d.js');

class CounterUp {
  constructor() {
    this._intCounterUp();
  }

  _intCounterUp() {
    jQuery(function ($) {
      $(".count-number").data("countToOptions", {
        formatter: function (value, options) {
          return value.toFixed(options.decimals).replace(/\B(?=(?:\d{3})+(?!\d))/g, ",");
        }
      });
      $(".timer").each(count);

      function count(options) {
        var $this = $(this);
        options = $.extend({}, options || {}, $this.data("countToOptions") || {});
        $this.countTo(options);
      }
    });
  }

}

jQuery(document).ready(function ($) {
  new CounterUp();
});
jQuery(document).ready(function ($) {
  new CounterUp();
});

var CounterUpHandler = function ($scope, $) {
  new CounterUp();
};

jQuery(window).on('elementor/frontend/init', function () {
  if (jQuery.isArray(puca_settings.elements_ready.counterup)) {
    $.each(puca_settings.elements_ready.counterup, function (index, value) {
      elementorFrontend.hooks.addAction('frontend/element_ready/tbay-' + value + '.default', CounterUpHandler);
    });
  }
});
