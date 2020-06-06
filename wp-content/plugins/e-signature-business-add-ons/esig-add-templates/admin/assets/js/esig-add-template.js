

(function ($) {

    $('#template_view').click(function (event) {
        event.preventDefault();
        // hide other thing . 
        //$('#template_top').show();
        $('#standard_view_popup_bottom').hide();
        $('#create_template').show();
        $('#esig_template_create').show();
        $('#upload_template_button').show();
        $('#template_type').hide();
        $('#upload_template_content').hide();
        $('#insert_template_button').hide();
        $('#no_of_signer').hide();
        $('#create_template_basic_next').hide();
        tb_show("", '#TB_inline?width=460&height=350&inlineId=template-option-step2');
        esign.tbResize();
    });


    $("#addRecipient_temp").on("click", function (e) {

        e.preventDefault();
        var slv = '';
        if (esign.is_slv_active()) {
            slv = '<span id="second_layer_verification" class="icon-doorkey second-layer" ></span>';
        }

        var html = '<div class="row topPadding bottomPadding" id="signer_main_temp">' +
                '<div class="col-sm-5 leftPadding"><input class="form-control esig-input" type="text" name="recipient_fnames[]"  placeholder="Signers Name"  /></div>' +
                '<div class="col-sm-5 noPadding leftPadding-5"><input class="form-control esig-input" type="text" name="recipient_emails[]"  placeholder="email@address.com" value="" /></div>' +
                '<div class="col-sm-2 noPadding text-left">' + slv + '<span id="esig-del-signer" class="deleteIcon"></span></div></div>';

        $("#recipient_emails_temp").append(html);

        // count temp signer emails if it gretar than 2 signer order option will be visible . 
        var signer_email_array = $("#recipient_emails_temp input[name='recipient_emails\\[\\]']").map(function () {
            return $(this).val();
        });

        if (signer_email_array.length > 1) {
            // $('#esign-assign-signer-order-temp').show();
            $('#esign-signer-order-temp').fadeIn(1600, "linear");
            if ($('#esign-signer-order-temp #esign-assign-signer-order-temp').attr('checked')) {
                $('#esign-assign-signer-order-temp').change();
            }
        }

    });
    
    $('body').on('click', '#recipient_emails_temp #template_signer_container .deleteIcon', function (e) {

        // checking if signer only one then hide signer order checkbox 

        $(this).closest('div').parent().parent().remove();

        e.preventDefault();
        //$(this).remove();

        // count temp signer emails if it gretar than 2 signer order option will be visible . 
        var signer_email_array = $("#recipient_emails_temp input[name='recipient_emails\\[\\]']").map(function () {
            return $(this).val();
        });

        if (signer_email_array.length === 1) {
            // $('#esign-assign-signer-order-temp').show();
            $('#esign-assign-signer-order-temp').prop('checked', false).change();
            $('#esign-signer-order-temp').fadeOut(1600, "linear");
        }
    });

    $('body').on('click', '#recipient_emails_temp .deleteIcon', function (e) {

        // checking if signer only one then hide signer order checkbox 

        $(this).closest('div').parent().remove();

        e.preventDefault();
        //$(this).remove();

        // count temp signer emails if it gretar than 2 signer order option will be visible . 
        var signer_email_array = $("#recipient_emails_temp input[name='recipient_emails\\[\\]']").map(function () {
            return $(this).val();
        });

        if (signer_email_array.length === 1) {
            // $('#esign-assign-signer-order-temp').show();
            $('#esign-assign-signer-order-temp').prop('checked', false).change();
            $('#esign-signer-order-temp').fadeOut(1600, "linear");
        }
    });

    $('#esig_template_upload').click(function () {

        jQuery.ajax({
            type: "POST",
            url: esigtemplateAjax.ajaxurl + "?action=esig_templateupload",
            success: function (data, status, jqXHR) {

                $('#create_template').hide();
                $('#upload_template_button').hide();
                $('#template_type').show();
                $('#upload_template_content').show();
                $('#insert_template_button').show();
                $('#rupom').show();
                $('#template_id').empty();

                $('#template_id').append(data);

                // selecting defalut value if todo add template
                if (esigtemplateAjax.esig_add_template != "") {
                    $("#template_id").find('option').each(function (i, opt) {
                        if (opt.value === esigtemplateAjax.esig_add_template)
                            $(opt).attr('selected', 'selected');
                    });
                }
                // tempalte trigger updated 
                $('#template_id').trigger("chosen:updated");

                $(".chosen-container").css("min-width", "250px");


                $(".chosen-drop").show(0, function () {
                    $(this).parents("div").css("overflow", "visible");
                });

            },
            error: function (xhr, status, error) {
                alert('Template Upload Error:' + xhr.responseText);
            }
        });

    });


    // create template button clicked  
    $('#esig_template_create').click(function () {
        $(".chosen-container").css("min-width", "250px");
        $('#esig_template_create').hide();
        $('#no_of_signer').show();
        $('#create_template_basic_next').show();
        $('#upload_template_button').hide();

    });

    // create template next click 
    $('#esig_template_basic_next').click(function () {

        var noofsigner = $('input[name="signerno"]').val();
        if (noofsigner == "") {
            alert('please input how many signer?');
            return;
        } else if (isNaN(noofsigner)) {
            alert('Woah Tiger!  Looks like you\'re entering text in a field that only accepts numbers.  Try and using a number instead');
            return;
        }
        //var noofsigner = $('#no_of_signer option:selected').val();
        var doc_id = $(this).data('document');
        window.location = "edit.php?post_type=esign&page=esign-add-document&esig_type=template&document_id=" + doc_id + "&sif_signer=" + noofsigner;
    });
    // Show or hide the stand alone console when the box is checked.
    $('input[name="esig_template"]').on('change', function () {
        if ($('input[name="esig_template"]').attr('checked')) {
            $('#esig_template_input').show();
        } else {
            $('#esig_template_input input[type="text"]').val('');
            $('#esig_template_input').hide();
        }
    });



    // validation and submit start here 
    $('#template_insert').click(function () {

        var template_id = $('#template_id option:selected').val();

        var template_type = $('#esig_temp_doc_type option:selected').val();

        var error = '';
        if (template_type == 'doctype') {
            error += 'You must select document type.\n\n';
        }
        if (template_id == 'sel_temp_name') {
            error += 'You must select template name.\n\n';
        }
        if (error != '')
        {
            alert(error);
            return false;
        }


        if (template_type == 'sad') {
            $('#esig_select_template').submit();
        }

        if (template_type == 'basic') {

            $('#create_template').hide();
            $('#esig_template_create').hide();
            $('#upload_template_button').hide();
            $('#template_type').hide();
            $('#upload_template_content').hide();
            $('#insert_template_button').hide();
            $('#no_of_signer').hide();
            $('#create_template_basic_next').hide();

            $('#standard_view_popup_bottom').show();

            jQuery.ajax({
                type: "POST",
                url: esigtemplateAjax.ajaxurl + "?action=sifinputfield",
                data: {
                    template_id: template_id,
                },
                success: function (data, status, jqXHR) {

                    if (data != "") {

                        $('#recipient_emails_temp').html(data);
                        var signer_email_array = $("#recipient_emails_temp input[name='recipient_emails\\[\\]']").map(function () {
                            return $(this).val();
                        });

                        if (signer_email_array.length > 1) {
                            // $('#esign-assign-signer-order-temp').show();
                            // $('#esign-assign-signer-order-temp').prop('checked', false).change();
                            $('#esign-signer-order-temp').fadeIn(1600, "linear");
                        }
                    }
                },
                error: function (xhr, status, error) {
                    alert('Template insert error: ' + xhr.responseText + 'Please Try Again');
                    return false;
                }
            });



            $(this).parents("div").css("overflow", "");



            $('input[name="template_id"]').val(template_id);
            $('input[name="esig_temp_document_type"]').val(template_type);
        }

    });


    // esig temp todo start hree 
    if (esigtemplateAjax.esig_add_template != "") {

        $(window).load(function () {
            tb_show("", '#TB_inline?inlineId=template-option-step2');
            esign.tbResize();
        });


        $("#esig_template_upload").trigger("click");

    }
    // esig temp todo end here 
    if (esigtemplateAjax.esig_template_preview != "") {

        $('.basic_esign').hide();
        $('#submit_send').hide();
        $('#submit_save').hide();

        if (esigtemplateAjax.esig_template_edit == '1') {
            $('#esig_submit_section').append('<input type="submit" value="Save Template"  class="button button-primary button-large" id="submit_add_template"  name="add_template">');
        } else {
            $('#esig_submit_section').append('<input type="submit" value="Add Template"  class="button button-primary button-large" id="submit_add_template"  name="add_template">');
        }

        $('#esig_submit_section').append('<input type="submit" value="Save as Draft"  class="button button-secondary button-large" id="submit_save_stand"  name="save_template">');

    }


//check validatioin on input change 
    $('body').on('keyup', "#recipient_emails_temp input[name='recipient_fnames\\[\\]']", function () {
        // checking if signer only one then hide signer order checkbox 
        //$.fn.approval_email_duplicate();
        $(".esig-error-box").remove();
    });

    //template basic document signer info validation 
// email validation checking on basic document add view . 

    $.fn.template_email_duplicate = function () {

        var view_email = $("#recipient_emails_temp input[name='recipient_emails\\[\\]']").map(function () {
            return $(this).val();
        });

        var view_fname = $("#recipient_emails_temp input[name='recipient_fnames\\[\\]']").map(function () {
            return $(this).val();
        });

        var sorted_email = view_email.sort();
        // getting new array 
        var exists = false;
        var blank = false;
        var blank_email = false;
        // if blank signer name is input 
        for (var i = 0; i < view_fname.length; i++) {

            if (view_fname[i] == undefined || view_fname[i] == '')
            {

                blank = true;
            }
            var re = /<(.*)>/
            if (re.test(view_fname[i]))
            {
                blank = true;
            }
            // var regexp = new RegExp(/^[a-z]([-']?[a-z]+)*( [a-z]([-']?[a-z]+)*)+$/i);
            if (!esign.isFullName(view_fname[i])) {
                blank = true;
            }
            if (blank)
            {
                $('#standard_view_popup_bottom .invitations-container .esig-error-box').remove();
                $('#standard_view_popup_bottom .invitations-container').after('<div class="row esig-error-box"><div class="col-md-12">*A full name including your first and last name is required to sign this document. Spaces after last name will prevent submission.</span></div></div>');
                return true;
            }
        }
        // if blank email address is input 
        for (var i = 0; i < view_email.length; i++) {

            // var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

            if (view_email[i] == undefined || view_email[i] == '')
            {
                blank_email = true;
            } else if (!esign.is_valid_email(view_email[i]))
            {
                // remove previous error msg 
                $('#standard_view_popup_bottom .invitations-container .esig-error-box').remove();
                // add new error msg 
                $('#standard_view_popup_bottom .invitations-container').after('<div class="row esig-error-box"><div class="col-md-12">*E-mail address is not valid.</span></div></div>');

                return true;
            }
            if (blank_email)
            {
                // remove previous error msg 
                $('#standard_view_popup_bottom .invitations-container .esig-error-box').remove();
                // add new error msg 
                $('#standard_view_popup_bottom .invitations-container').after('<div class="row esig-error-box"><div class="col-md-12">*You must fill email address.</span></div></div>');

                return true;
            }
        }


        for (var i = 0; i < view_email.length - 1; i++) {

            if (sorted_email[i + 1].toLowerCase() == sorted_email[i].toLowerCase())
            {
                exists = true;
            }
        }

        if (exists)
        {

            $('#standard_view_popup_bottom .invitations-container .esig-error-box').remove();
            $('#standard_view_popup_bottom .invitations-container').after('<div class="row esig-error-box"><div class="col-md-12">*You can not use duplicate email address.</span></div></div>');


            return true;
        } else
        {
            $('#standard_view_popup_bottom .invitations-container .esig-error-box').remove();
            return false;
        }

    }


    // template basic document form has signer info submit 
    $("#temp-basic-signer-form").on("submit", function (e) {

        // validation for same email address . 
        if ($.fn.template_email_duplicate())
        {
            return false;
        }
        return true;
    });




})(jQuery);
