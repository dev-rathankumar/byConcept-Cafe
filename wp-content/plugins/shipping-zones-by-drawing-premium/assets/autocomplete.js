(function($) {

    // Prevent Chrome autocomplete dropdown on the address line 1 fields
    $(document).on('focus click tap', 'input#billing_address_1, input#shipping_address_1', function() {
        $(this).attr("autocomplete", 'new-password');
    });

})(jQuery);

var RpCheckoutAutocomplete = RpCheckoutAutocomplete || {};
var RpCheckoutAutocomplete_shipping = RpCheckoutAutocomplete_shipping || {};
RpCheckoutAutocomplete.event = {};
RpCheckoutAutocomplete_shipping.event = {};
RpCheckoutAutocomplete.method = {
    placeSearch: "",
    IdSeparator: "",
    autocomplete : "",
    streetNumber : "",
    formFields : {
        'billing_address_1': '',
        'billing_address_2': '',
        'billing_city': '',
        'billing_state': '',
        'billing_postcode': '',
        'billing_country' : ''
    },
    formFieldsValue : {
        'billing_address_1': '',
        'billing_address_2': '',
        'billing_city': '',
        'billing_state': '',
        'billing_postcode': '',
        'billing_country' : ''
    },
    component_form : "",

    initialize: function(){
        this.getIdSeparator();
        this.initFormFields();

        this.autocomplete = new google.maps.places.Autocomplete(
            (document.getElementById('billing_address_1')),
            {
                types: ['address']
            });
        google.maps.event.addListener(this.autocomplete, 'place_changed', function( event ) {
            RpCheckoutAutocomplete.method.fillInAddress()
        });
        var billing_address = document.getElementById("billing_address_1");
        if(billing_address != null){
            billing_address.addEventListener("focus", function( event ) {
                RpCheckoutAutocomplete.method.setAutocompleteCountry()
            }, true);
        }

        var billing_country = document.getElementById("billing_country");
        if(billing_country != null){
            billing_country.addEventListener("change", function( event ) {
                RpCheckoutAutocomplete.method.setAutocompleteCountry()
            }, true);
        }


    },
    getIdSeparator : function() {
        if (!document.getElementById('billing_address_1')) {
            this.IdSeparator = "_";
            return "_";
        }
        this.IdSeparator = ":";
        return ":";
    },
    initFormFields: function ()
    {
        console.log('-------------');
        for (var field in this.formFields) {
            this.formFields[field] = (field);
        }
        this.component_form = {};
        this.component_form_standard =
        {
            'country': ['billing_country', 'long_name'],
            'street_number': ['billing_address_1', 'short_name'],
            'route': ['billing_address_1', 'long_name'],
            'locality': ['billing_city', 'long_name'],
            'postal_town': ['billing_city', 'long_name'],
            'sublocality_level_1': ['billing_city', 'long_name'],
            'administrative_area_level_2': ['billing_city', 'long_name'],
            'administrative_area_level_3': ['billing_city', 'long_name'],
            'administrative_area_level_1': ['billing_state', 'short_name'],
            'postal_code': ['billing_postcode', 'short_name']
        };
        this.component_form_alt =
        {
            'country': ['billing_country', 'long_name'],
            'street_number': ['billing_address_1', 'short_name'],
            'route': ['billing_address_1', 'long_name'],
            'locality': ['billing_city', 'long_name'],
            'postal_town': ['billing_city', 'long_name'],
            'sublocality_level_1': ['billing_city', 'long_name'],
            'administrative_area_level_2': ['billing_state', 'long_name'],
            'postal_code': ['billing_postcode', 'short_name']
        };
        this.alt_countries = ['ES', 'GB'];
    },

    fillInAddress : function () {
        this.clearFormValues();
        var place = this.autocomplete.getPlace();
		new_address = place;
        this.resetForm();
        console.log(place.address_components);
        var current_country = document.getElementById("billing_country").value;
        if(this.alt_countries.includes(current_country)){
            this.component_form = this.component_form_alt;
        }else{
            this.component_form = this.component_form_standard;
        }
        for (var f in this.component_form) {
            loop1:
            for (var field in place.address_components) {
                var types = place.address_components[field].types;
                for (var t in types) {

                    if(f == types[t]) {
                        var field_id = this.component_form[f][0];
                        var prop = this.component_form[f][1];
                        // If it's empty so far, then fill it
                        if(this.formFieldsValue[field_id] == '') {
                            if(place.address_components[field].hasOwnProperty(prop)){
                                this.formFieldsValue[field_id] = place.address_components[field][prop];
                                console.log(field_id + " = " + place.address_components[field][prop]);
                                break loop1;
                            }
                        }
                    }

                }
            }
        }
        this.streetNumber = place.name;
        var street_addr_type = place.types.indexOf('street_address');
        if(street_addr_type == -1 && typeof precise_address_alert !== 'undefined' && precise_address_alert != 'disabled') {
            alert(precise_address_alert);
        }

        this.appendStreetNumber();
        this.fillForm();

        //update checkout
        jQuery("#billing_state").trigger("change");
        jQuery(document.body).trigger("update_checkout");
        if(typeof  FireCheckout !== 'undefined')
        {
            checkout.update(checkout.urls.billing_address);
        }
    },

    clearFormValues: function ()
    {
        for (var f in this.formFieldsValue) {
            this.formFieldsValue[f] = '';
        }
    },
    appendStreetNumber : function ()
    {

        if(this.streetNumber != '')
        {
            this.formFieldsValue['billing_address_1'] =  this.streetNumber
        }
    },
    fillForm : function()
    {
        for (var f in this.formFieldsValue) {
            if(f == 'billing_country' )
            {
                this.selectRegion( f,this.formFieldsValue[f]);
            }
            else
            {
                if(document.getElementById((f)) === null){
                    continue;
                }
                else
                {
                    var autoc_value = this.formFieldsValue[f];
                    if(f == 'billing_state' && jQuery('select#' + f).length > 0 && jQuery('select#' + f + ' option[value="' + autoc_value +'"]').length == 0) {
                        // State dropdown without a matching value... try the text
                        autoc_value = jQuery('select#' + f + ' option').filter(function () { return jQuery(this).html() == autoc_value; }).val()
                    }
                    document.getElementById((f)).value = autoc_value;
                    jQuery('#'+f).trigger('input');
                }

            }
        }
    },
    selectRegion:function (id,regionText)
    {
        if(document.getElementById((id)) == null){
            return false;
        }
        var el = document.getElementById((id));
        if(el.tagName == 'select') {
            for(var i=0; i<el.options.length; i++) {
                if ( el.options[i].text == regionText ) {
                    el.selectedIndex = i;
                    break;
                }
            }
        }
    },
    resetForm :function ()
    {
        if(document.getElementById(('billing_address_2')) !== null){
            document.getElementById(('billing_address_2')).value = '';
        }
    },


    setAutocompleteCountry : function () {

        if(document.getElementById('billing_country') === null){
            country = 'US';
        }
        else
        {
            var country = document.getElementById('billing_country').value;
        }
        this.autocomplete.setComponentRestrictions({
            'country': country
        });
    }


}


RpCheckoutAutocomplete_shipping.method = {
    placeSearch: "",
    IdSeparator: "",
    autocomplete : "",
    streetNumber : "",
    formFields : {
        'shipping_address_1': '',
        'shipping_address_2': '',
        'shipping_city': '',
        'shipping_state': '',
        'shipping_postcode': '',
        'shipping_country' : ''
    },
    formFieldsValue : {
        'shipping_address_1': '',
        'shipping_address_2': '',
        'shipping_city': '',
        'shipping_state': '',
        'shipping_postcode': '',
        'shipping_country' : ''
    },
    component_form : "",

    initialize: function(){
        this.getIdSeparator();
        this.initFormFields();

        this.autocomplete = new google.maps.places.Autocomplete(
            (document.getElementById('shipping_address_1')),
            {
                types: ['address']
            });
        google.maps.event.addListener(this.autocomplete, 'place_changed', function( event ) {
            RpCheckoutAutocomplete_shipping.method.fillInAddress()
        });
        var shipping_address = document.getElementById("shipping_address_1");
        if(shipping_address != null){
            shipping_address.addEventListener("focus", function( event ) {
                RpCheckoutAutocomplete_shipping.method.setAutocompleteCountry()
            }, true);
        }

        var shipping_country = document.getElementById("shipping_country");
        if(shipping_country != null){
            shipping_country.addEventListener("change", function( event ) {
                RpCheckoutAutocomplete_shipping.method.setAutocompleteCountry()
            }, true);
        }


    },
    getIdSeparator : function() {
        if (!document.getElementById('shipping_address_1')) {
            this.IdSeparator = "_";
            return "_";
        }
        this.IdSeparator = ":";
        return ":";
    },
    initFormFields: function ()
    {
        console.log('-------------');
        for (var field in this.formFields) {
            this.formFields[field] = (field);
        }
        this.component_form = {};
        this.component_form_standard =
        {
            'country': ['shipping_country', 'long_name'],
            'street_number': ['shipping_address_1', 'short_name'],
            'route': ['shipping_address_1', 'long_name'],
            'locality': ['shipping_city', 'long_name'],
            'postal_town': ['shipping_city', 'long_name'],
            'sublocality_level_1': ['shipping_city', 'long_name'],
            'administrative_area_level_2': ['shipping_city', 'long_name'],
            'administrative_area_level_3': ['shipping_city', 'long_name'],
            'administrative_area_level_1': ['shipping_state', 'short_name'],
            'postal_code': ['shipping_postcode', 'short_name']
        };
        this.component_form_alt =
        {
            'country': ['shipping_country', 'long_name'],
            'street_number': ['shipping_address_1', 'short_name'],
            'route': ['shipping_address_1', 'long_name'],
            'locality': ['shipping_city', 'long_name'],
            'postal_town': ['shipping_city', 'long_name'],
            'sublocality_level_1': ['shipping_city', 'long_name'],
            'administrative_area_level_2': ['shipping_state', 'long_name'],
            'postal_code': ['billing_postcode', 'short_name']
        };
        this.alt_countries = ['ES', 'GB'];
    },

    fillInAddress : function () {
        this.clearFormValues();
        var place = this.autocomplete.getPlace();
        this.resetForm();
        console.log(place.address_components);
        var current_country = document.getElementById("shipping_country").value;
        if(this.alt_countries.includes(current_country)){
            this.component_form = this.component_form_alt;
        }else{
            this.component_form = this.component_form_standard;
        }
        for (var f in this.component_form) {
            loop1:
            for (var field in place.address_components) {
                var types = place.address_components[field].types;
                for (var t in types) {

                    if(f == types[t]) {
                        var field_id = this.component_form[f][0];
                        var prop = this.component_form[f][1];
                        // If it's empty so far, then fill it
                        if(this.formFieldsValue[field_id] == '') {
                            if(place.address_components[field].hasOwnProperty(prop)){
                                this.formFieldsValue[field_id] = place.address_components[field][prop];
                                console.log(field_id + " = " + place.address_components[field][prop]);
                                break loop1;
                            }
                        }
                    }

                }
            }
        }
        this.streetNumber = place.name;

        if(place.types.indexOf('street_address') == -1 && typeof precise_address_alert !== 'undefined' && precise_address_alert != 'disabled') {
            alert(precise_address_alert);
        }

        this.appendStreetNumber();
        this.fillForm();


        //update checkout
        jQuery("#shipping_state").trigger("change");
        jQuery(document.body).trigger("update_checkout");
        if(typeof  FireCheckout !== 'undefined')
        {
            checkout.update(checkout.urls.shipping_address);
        }
    },

    clearFormValues: function ()
    {
        for (var f in this.formFieldsValue) {
            this.formFieldsValue[f] = '';
        }
    },
    appendStreetNumber : function ()
    {
        if(this.streetNumber != '')
        {
            this.formFieldsValue['shipping_address_1'] =  this.streetNumber;
        }
    },
    fillForm : function()
    {
        for (var f in this.formFieldsValue) {
            if(f == 'shipping_country' )
            {
                this.selectRegion( f,this.formFieldsValue[f]);
            }
            else
            {
                if(document.getElementById((f)) === null){
                    continue;
                }
                else
                {
                    var autoc_value = this.formFieldsValue[f];
                    if(f == 'shipping_state' && jQuery('select#' + f).length > 0 && jQuery('select#' + f + ' option[value="' + autoc_value +'"]').length == 0) {
                        // State dropdown without a matching value... try the text
                        autoc_value = jQuery('select#' + f + ' option').filter(function () { return jQuery(this).html() == autoc_value; }).val()
                    }
                    document.getElementById((f)).value = autoc_value;
                    jQuery('#'+f).trigger('input');
                }

            }
        }
    },
    selectRegion:function (id,regionText)
    {
        if(document.getElementById((id)) == null){
            return false;
        }
        var el = document.getElementById((id));
        if(el.tagName == 'select') {
            for(var i=0; i<el.options.length; i++) {
                if ( el.options[i].text == regionText ) {
                    el.selectedIndex = i;
                    break;
                }
            }
        }
    },
    resetForm :function ()
    {
        if(document.getElementById(('shipping_address_2')) !== null){
            document.getElementById(('shipping_address_2')).value = '';
        }
    },


    setAutocompleteCountry : function () {

        if(document.getElementById('shipping_country') === null){
            country = 'US';
        }
        else
        {
            var country = document.getElementById('shipping_country').value;
        }
        this.autocomplete.setComponentRestrictions({
            'country': country
        });
    }


}


window.addEventListener('load', function(){
    if(!(document.getElementById('billing_address_1') === null)){
		  //    RpCheckoutAutocomplete.method.initialize();
	}

    if(!(document.getElementById('shipping_address_1') === null)){
		  //    RpCheckoutAutocomplete_shipping.method.initialize();
	}

});

if(!(document.getElementById('billing_address_1') === null)){
    var billaddr = document.getElementById('billing_address_1');
    google.maps.event.addDomListener(billaddr, 'keydown', function(e) {
        if (e.keyCode == 13) {
            e.preventDefault();
        }
    });
}

if(!(document.getElementById('shipping_address_1') === null)){
    var shipaddr = document.getElementById('shipping_address_1');
    google.maps.event.addDomListener(shipaddr, 'keydown', function(e) {
        if (e.keyCode == 13) {
            e.preventDefault();
        }
    });
}
