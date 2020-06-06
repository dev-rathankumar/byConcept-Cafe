(function ($) {

    "use strict";

    // this is common js file . 

    $('.signature-wrapper-displayonly').click(function () {

        var signature_text_owner = $(".esignature-in-text").val();
        var font_owner = $('#font-type').val();

        if (signature_text_owner != "") {

            if (!font_owner) {
                font_owner = 1;
            }

            var newSize = signature_text_owner.length;
            newSize = 36 - (.5 * newSize);

            var htmlcontent1 = '<span class="sign-text"><a href="#" id="esig-type-in-change-fonts" class="esig-change-font"></a>' + signature_text_owner + '</span><br> <input type="hidden" id="font-type" name="font_type" value="' + font_owner + '">';
            htmlcontent1 += '<input type="hidden" name="esig_signature_type" value="typed">';
            $('#esig-type-in-preview').html(htmlcontent1);
            $('#esig-type-in-preview').addClass('esig-signature-type-font' + font_owner).css("font-size", newSize + "px");

        }
    });

    $('.signature-wrapper-displayonly-signed').each(function (i, e) {


        if ($(e).find('.signature-image')) {

            // if already sign or add type 
        }
        if ($(e).find('.esignature-in-text-signed'))
        {
            var signature_text = $(e).find('input.esignature-in-text-signed').val(); // $("input[name='esignature_in_text']").val();
            var font = $(e).find('input.font-type-signed').val(); // $('#font-type').val();


            if (signature_text) {

                var newSize = signature_text.length;
                newSize = 36 - (.5 * newSize);

                if (!font) {
                    font = "1";
                }
                //var htmlcontent = '<div class="sign-here pad signed esig-sig-type-signed esig-signature-type-font' + font + '" width="400" height="100"><span class="sig-type">' + signature_text + '</span></div>';
                var htmlcontent = '<div class="sign-here pad signed esig-sig-type-signed" width="400" height="100"><span class="sig-type esig-signature-type-font' + font + '">' + signature_text + '</span></div>';
                htmlcontent += '<input type="hidden" name="esig_signature_type" value="typed">';
                // $('#signatureCanvas2').hide();

                $(this).html(htmlcontent);
                $('.esig-signature-type-font' + font).css("font-size", newSize + "px");

                var htmlcontent1 = '<span class="sign-text">' + signature_text + '</span><br> <input type="hidden" id="font-type" name="font_type" value="' + font + '">';

                $(this).find('#esig-type-in-preview').html(htmlcontent1);
                //$('#esig-type-in-preview').addClass('esig-signature-type-font' + font).css("font-size", newSize + "px");

            }
        }
    });
    

    $('.digital-signature-image').each(function (i, e) {
        if ($(e).find('.sign-text-pdf'))
        {
            var spanEl = $(e).find('.sign-text-pdf span');
            var signature_text = spanEl.text();
            if (signature_text) {
                var newSize = signature_text.length;
                newSize = 36 - (.7 * newSize);
                spanEl.css("font-size", newSize + "px");
            }
        }
    });


    $('body').on('change keyup paste', '#esignature-in-text', function () {


        // hiding  tooltip if that already showed
        $('#esig-agree-button').addClass('disabled').trigger('hidetip');

        var signature_type = $("input[name='esignature_in_text']").val();
        $("#esignature-in-text").css('border', '0px solid #ff0000');
        if (/<(.*)>/.test(signature_type))
        {
            $("input[name='esignature_in_text']").focus();
            return false;
        }

        //$("input[name='recipient_first_name']").val(signature_type);

        var newSize = signature_type.length;

        newSize = 36 - (.5 * newSize);

        var font = $('#font-type').val();
        //var htmltext = 'Enter your placeholder text <br> <input type="text" name="textbox" style="width:' + maxsize + 'px;"  class="sif_input_field label" value="' + label + '" placeholder="' + label + '"><input type="hidden" name="maxsize" value="' + maxsize + '">';
        var htmlcontent = '<a href="#" id="esig-type-in-change-fonts" class="esig-change-font"></a><span class="">' + signature_type + '</span><br> <input type="hidden" id="font-type" name="font_type" value="1">';
        htmlcontent += '<input type="hidden" name="esig_signature_type" value="typed">';
        $('#esig-type-in-preview').html(htmlcontent);

        if ($('#esig-type-in-preview').hasClass("esig-signature-type-font" + font)) {

            $('#esig-type-in-preview').removeClass("esig-signature-type-font" + font);
        }
        $('#esig-type-in-preview').addClass("esig-signature-type-font1").css("font-size", newSize + "px");
        $('#esig-iam-type').html(signature_type);
        $('#esig-iam').html(signature_type);
        $('#esig-iam-draw').html(signature_type);


    });


    $('body').on('click', '#esig-type-in-change-fonts', function (e) {

        e.preventDefault();

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

        if (/<(.*)>/.test(signature_type))
        {
            $("input[name='esignature_in_text']").focus();
            return false;
        }

        if (!signature_type)
        {
            $("input[name='esignature_in_text']").focus();
            return false;
        }

        if (signature_type.replace(/\s+/g, '').length == 0)
        {
            $("input[name='esignature_in_text']").focus();
            return false;
        }
        
        if (!esign.isFullName(signature_type) && $("#recipient_first_name").hasClass("esig-no-form-integration"))
        {
            return false;
        }


        var font = $('#font-type').val();

        var htmlcontent = '<div class="sign-here pad signed esig-sig-type esig-signature-type-font' + font + '" width="100%"><span class="esig-sig-type1">' + signature_type + '</span></div>';
        htmlcontent += '<input type="hidden" name="esig_signature_type" value="typed">';

        // getting first name value

        var fname = $("input[name='recipient_first_name']").val();
        if (signature_type != fname) {

            $("input[name='recipient_first_name']").val(signature_type);

            $('#esig-iam').html(fname);
            //$('#esig-iam-type').html(fname);
        }

        $("input[name='esignature_in_text']").val(signature_type);

        $('#esig-signature-added').hide();
        $('#signatureCanvas2').hide();
        $('.signature-wrapper-displayonly .esig-sig-type').remove();

        $('.signature-wrapper-displayonly').append(htmlcontent);

        var newSize = signature_type.length;
        newSize = 36 - (.5 * newSize);
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
