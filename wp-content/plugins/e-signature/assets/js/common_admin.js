(function ($) {

"use strict";

    // this is common js file . 
    var signature_text = $("input[name='esignature_in_text']").val();
    var font = $('#font-type').val();

    if (signature_text) {
     
        var newSize = signature_text.length;
        newSize = 64 - (1.5 * newSize);

        var htmlcontent = '<div class="sign-here pad signed esig-sig-type esig-signature-type-font' + font + '" width="500" height="100"><span class="sign-text-display">' + signature_text + '</span></div>';
        htmlcontent += '<input type="hidden" name="esig_signature_type" value="typed">';


        $('.signature-wrapper-displayonly').append(htmlcontent);
        $('.esig-signature-type-font' + font).css("font-size", newSize + "px");

        var htmlcontent1 = '<div class="esig-admin-type"><span class="sign-text-admin">' + signature_text + '</span><br> <input type="hidden" id="font-type" name="font_type" value="' + font + '"></div>';
        $('#esig-type-in-preview').html(htmlcontent1);
        $('#esig-type-in-preview').addClass('esig-signature-type-font' + font).css("font-size", newSize + "px");

    }

    $('body').on('change keyup paste blur', '#esignature-in-text', function () {

        var signature_type = $("input[name='esignature_in_text']").val();
        
        $("#esignature-in-text").css('border','0px solid #ff0000');

        var newSize = signature_type.length;

        newSize = 64 - (1.5 * newSize);
        //var htmltext = 'Enter your placeholder text <br> <input type="text" name="textbox" style="width:' + maxsize + 'px;"  class="sif_input_field label" value="' + label + '" placeholder="' + label + '"><input type="hidden" name="maxsize" value="' + maxsize + '">';
        var htmlcontent = '<span class="sign-text-admin">' + signature_type + '</span><br> <input type="hidden" id="font-type" name="font_type" value="1">';
        $('#esig-type-in-preview').html(htmlcontent);
        $('#esig-type-in-preview').addClass("esig-signature-type-font1").css("font-size", newSize + "px");

    });

    $('#esig-type-in-change-fonts').click(function () {

        var font = $('#font-type').val();
        var currentfont = Number(font) + Number(1);
        if (currentfont > 7) {
            currentfont = 1;
        }
        var presentfont = "esig-signature-type-font" + font;
        var nextfont = 'esig-signature-type-font' + currentfont;
        $('#font-type').val(currentfont);
        $('#esig-type-in-preview').removeClass(presentfont).addClass(nextfont);

    });

    $('#esig-type-in-text-accept-signature').click(function () {

        var signature_type = $("input[name='esignature_in_text']").val();
        
        if (signature_type.replace(/\s+/g, '').length == 0)
        	{
        		$("input[name='esignature_in_text']").focus();
        		$("#esignature-in-text").css('border','1px solid #ff0000');
        		return false ;
        	}	
        	
        var font = $('#font-type').val();
        // $('#signatureCanvas2').hide();
        var htmlcontent = '<div class="sign-here pad signed esig-sig-type esig-signature-type-font' + font + '" width="500" height="100"><span class="sign-text-display">' + signature_type + '</span></div>';
        htmlcontent += '<input type="hidden" name="esig_signature_type" value="typed">';

        $("input[name='esignature_in_text']").val(signature_type);
        $('#signatureCanvas2').hide();
        $('.esig-sig-type').remove();
        $('.signature-wrapper-displayonly').append(htmlcontent);

        $("input[name='output']").val('');

        var newSize = signature_type.length;
        newSize = 64 - (1.5 * newSize);
        $('.esig-signature-type-font' + font).css("font-size", newSize + "px");
        //fixing auto size 

        tb_remove();

    });

    $('#esig-tab-draw').click(function () {

        $('#esig-type-in-change-fonts').fadeOut(1600, "linear");

    });

    $('#esig-tab-type').click(function () {

        $('#esig-type-in-change-fonts').fadeIn(1600, "linear");

    });



})(jQuery);
