(function ($) {
 
    "use strict";
    
    // signature display option 
    var display_opts = {
		penColour: '#000000',
		displayOnly: true,
		bgColour : 'transparent',
	};
	
    var signaturePadDisplay = $('.signature-wrapper-displayonly-signed').signaturePad(display_opts);
   
    var output = $('input[name="output"]');
	output = output[0];
	var sig = output.value;
   
    if(sig != ""){
		 signaturePadDisplay.regenerate(sig);
	}else {
    // this is common js file . 
    var signature_text = $("input[name='esignature_in_text']").val();
    var font = $('#font-type').val();
  
    if (signature_text) {

        var newSize = signature_text.length;
        newSize = 64 - (1.5 * newSize);

        var htmlcontent = '<div class="sign-here pad signed admin-sig-type esig-signature-type-font' + font + '" height="100"><span class="admin-sig-type-display">' + signature_text + '</span></div>';
        htmlcontent += '<input type="hidden" name="esig_signature_type" value="typed">';
        $('#signatureCanvas2').hide();

        $('.signature-wrapper-displayonly-signed').append(htmlcontent);
        $('.esig-signature-type-font' + font).css("font-size", newSize + "px");


    }

    }// if signature not present 


} (jQuery));