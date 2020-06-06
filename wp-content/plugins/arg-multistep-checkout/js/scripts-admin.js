(function($){
    "use strict";
	
    $('.color-field').wpColorPicker();

    ShowCheckoutStyleOptions($('.arg-checkout-option-button:checked'));

    $('.argmc-table-style').on('click', '.arg-checkout-option-button', function() {
        ShowCheckoutStyleOptions($(this));
    });

    function ShowCheckoutStyleOptions(elem) {
        if (elem.data('style') == 'theme') {
            $('.argmc-table-style').find('.checkout-form-options').hide();
        } else {
            $('.argmc-table-style').find('.checkout-form-options').show();
        }
    }
	
	
	ShowWizardButtonOptions($('.overwrite-wizard-buttons:checked'));

    $('.argmc-table-style').on('click', '.overwrite-wizard-buttons', function() {
        ShowWizardButtonOptions($(this));
    });

    function ShowWizardButtonOptions(elem) {
        if (elem.data('style') == 'overwrite-buttons-no') {
            $('.argmc-table-style').find('.wizard-overwrite-buttons-option').hide();
        } else {
            $('.argmc-table-style').find('.wizard-overwrite-buttons-option').show();
        }
    }
	
    
	ShowTabsLayoutOptions($('.argmc-tabs-layout'));

    $('.argmc-table-style').on('change', '.argmc-tabs-layout', function() {
    	ShowTabsLayoutOptions($(this));
    });

    function ShowTabsLayoutOptions(elem) {
    	var layout = elem.val();

        $('.argmc-tab-style').hide();  
        
        if (layout == 'tabs-arrow-alt') {       	
        	$('.argmc-tab-arrow-alt-style').show();
        } else if (layout == 'tabs-progress-bar') {      	
        	$('.argmc-tab-progress-bar-style').show(); 
		} else if (layout == 'tabs-outline') {      	
        	$('.argmc-tab-outline-style').show();
        } else {	
        	$('.argmc-tab-default-style').show();
        }
        
    }    
})(jQuery);