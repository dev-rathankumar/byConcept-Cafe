
var esig_sif_admin_controls = null;

(function ($)
{
    //"use strict";
    $(function ()
    {
        // Clone btn
        $('.esig-sif-main-panels .clone-btn').click(function ()
        {
            var target = $(this).data('target');
            if ($(target).length)
            {
                $(this).before($(target).html());
            }
            return false;
        });



        /* ################################## textbox action start here #####################################################*/

        $('body').on('click', '.esig-sif-panel-textfield .insert-btn', function ()
        {
            var name = 'esig-sif-' + Date.now();
            var verifysigner = $(".sif_text_signer_info #sif_invite_select option:selected").val();
            if (verifysigner == 'undefined')
            {
                $('.esig-sif-panel-textfield #esign-sif-signer-msg').show();
                return false;
            }

            var maxsize = $("input[name='maxsize']").val();

            if (typeof (maxsize) != "undefined" && !$.isNumeric(maxsize))
            {
                $('.esig-sif-panel-textfield #esign-sif-size-msg').show();
                return false;
            }
            var required = $('.esig-sif-panel-textfield input.required').prop('checked') ? 'required="1"' : '';

            var label = $(".esig-sif-panel-textfield input[name='textbox']").val();
            var display_type = $("#text_field_display_type option:selected").val();

            var return_text = ' [esigtextfield name="' + name + '" verifysigner="' + verifysigner + '" size="' + maxsize + '" label="' + label + '" displaytype="' + display_type + '" ' + required + ' ] ';

            esig_sif_admin_controls.insertContent(return_text);
            tb_remove();
            // clear text sif input . 
            $('.esig-sif-panel-textfield').find('input[type=text]').val('');
            // make textbox default siz_e. 
            var text_default = 'Enter your placeholder text<br> <input type="text"  name="textbox" value="" placeholder="Placeholder Text">';

            $('.esig-sif-panel-textfield .sif_text_placeholder_Text').html(text_default);

            return false;
        });

        // sif signer select msg changeing 
        $('body').on('change', '.esig-sif-panel-textfield #sif_invite_select', function () {

            $('.esig-sif-panel-textfield #esign-sif-signer-msg').hide();

        });

        $('body').on('change keyup paste', '.popover #maxsize', function () {

            var maxsize = Math.abs($("body .popover input[name='textbox_width']").val());
            if (!esign.isValidNumber(maxsize))
            {
                $(this).css('border', '1px solid red');
                var htmltext1 = '<input type="hidden" name="maxsize" value="' + maxsize + '">';

                $('.esig-sif-panel-textfield .sif_text_placeholder_Text').append(htmltext1);
                return false;
            } else {

                $(this).css('border', '0px solid red');
            }
            var label = $(".esig-sif-panel-textfield input[name='textbox']").val();

            var htmltext = 'Enter your placeholder text <br> <input type="text" name="textbox" style="width:' + maxsize + 'px;" value="' + label + '" placeholder="' + label + '"><input type="hidden" name="maxsize" value="' + maxsize + '">';

            $('.esig-sif-panel-textfield .sif_text_placeholder_Text').html(htmltext);
            $('.esig-sif-panel-textfield #esign-sif-size-msg').hide();
        });


        $("#sif_textbox_advanced_button").popover(
                {
                    placement: 'bottom',
                    html: 'true',
                    title: '<span><strong>Advanced Settings</strong></span>' +
                            '<span class="close">&times;</span>',
                    content: $('.sif_textbox_advanced_content').html(),
                    template: '<div class="popover" role="tooltip"><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
                });

        $('body').on('click', '.popover .close', function ()
        {
            $("#sif_textbox_advanced_button").popover('hide');
        });

        /* ********************************* textbox action end here ******************************************************/

        /* ##################################  textarea action start here ################################################ */
        // textarea 
        $('body').on('click', '.esig-sif-panel-textarea .insert-btn', function ()
        {
            var name = 'esig-sif-' + Date.now();

            var verifysigner = $(".sif_textarea_signer_info #sif_invite_select option:selected").val();

            if (verifysigner == 'undefined')
            {
                $('.esig-sif-panel-textarea #esign-sif-signer-msg').show();
                return false;
            }

            var size = $("#text-area-size-temp").val();

            var required = $('.esig-sif-panel-textarea input.required').prop('checked') ? 'required="1"' : '';
            var label = $("textarea#esig-textarea-input").val();
            var display_type = $("#text_area_display_type option:selected").val();
            var return_text = ' [esigtextarea name="' + name + '" verifysigner="' + verifysigner + '" size="' + size + '" label="' + label + '" displaytype="' + display_type + '" ' + required + ' ] ';
            esig_sif_admin_controls.insertContent(return_text);

            tb_remove();

            $('#esig-textarea-input').val('');

            return false;
        });

        // textarea popover size change event start here 
        $('body').on('change', '.popover #esig-textarea-size', function () {

            var size = $(".popover #esig-textarea-size option:selected").val();
            $("#esig-textarea-input").removeClass();
            $("#esig-textarea-input").addClass("area-" + size);
            $('#text-area-size-temp').attr('value', size);

        });

        // textarea advaned option start here 
        $("#sif_textarea_advanced_button").popover(
                {
                    placement: 'bottom',
                    html: 'true',
                    title: '<span><strong>Advanced Settings</strong></span>' +
                            '<span class="close">&times;</span>',
                    content: $('.sif_textarea_advanced_content').html(),
                    template: '<div class="popover" role="tooltip"><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
                });

        $('body').on('click', '.popover .close', function ()
        {
            var content = $('#esig-textarea-size option:selected').val();
            $('#esig-textarea-size option[value="' + content + '"]').attr('selected', true);
            $('#text-area-size-temp').attr('value', content);
            $('#sif_textarea_advanced_button').data('bs.popover').options.content = $('.sif_textarea_advanced_content').html();
            $("#sif_textarea_advanced_button").popover('hide');

            //$('#sif_textarea_advanced_button').attr('data-content','Cannot proceed with Save while Editing a row.');
            //
        });

        // sif signer select msg changeing 
        $('body').on('change', '.esig-sif-panel-textarea #sif_invite_select', function () {

            $('.esig-sif-panel-textarea #esign-sif-signer-msg').hide();

        });

        /*********************************** textarea action end here *****************************************************/


        /* ################################# date picker actions start here ###############################3############### */

        // date picket
        $('body').on('click', '.esig-sif-panel-datepicker .insert-date', function () {

            var name = 'esig-sif-picker-' + Date.now();

            var picker_label = $("input[name='datepickerlabel']").val();

            var verifysigner_picker = $(".sif_popup_main_datepicker #sif_invite_select option:selected").val();

            if (verifysigner_picker == 'undefined')
            {
                $('.esig-sif-panel-datepicker #esign-sif-signer-msg').show();
                return false;
            }

            var startDate = $("input[name='datepickerstartdate']").val();
            var endDate = $("input[name='datepickerenddate']").val();

            var pattern = /^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/;
            var validStartDate = new Date(startDate);
            if (startDate != "" && validStartDate == "Invalid Date") {

                BootstrapDialog.alert({
                    title: 'WARNING',
                    message: 'Date format is not correct. Please insert date as correct format. Allowed format is mm/dd/yyyy.',
                    type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                    closable: true, // <-- Default value is false
                    buttonLabel: 'Ok', // <-- Default value is 'OK',

                });

                return  false;

            }
            
            var validEndDate = new Date(endDate);
            if (endDate != "" && validEndDate == "Invalid Date") {

                BootstrapDialog.alert({
                    title: 'WARNING',
                    message: 'Date format is not correct. Please insert date as correct format. Allowed format is mm/dd/yyyy.',
                    type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                    closable: true, // <-- Default value is false
                    buttonLabel: 'Ok', // <-- Default value is 'OK',

                });

                return  false;

            }
            

            if (startDate != "" && !pattern.test(startDate) || endDate != "" && !pattern.test(endDate)) {

                BootstrapDialog.alert({
                    title: 'WARNING',
                    message: 'Date format is not correct. Please insert date as correct format. Allowed format is mm/dd/yyyy.',
                    type: BootstrapDialog.TYPE_WARNING, // <-- Default value is BootstrapDialog.TYPE_PRIMARY
                    closable: true, // <-- Default value is false
                    buttonLabel: 'Ok', // <-- Default value is 'OK',

                });

                return  false;
            }


            var required = $('.esig-sif-panel-datepicker input.required').prop('checked') ? ' required="1"' : '';
            var dtreadonly = $('.esig-sif-panel-datepicker input.esig-picker-readonly').prop('checked') ? ' readonly="1"' : 'readonly="0"';

            var display_type = $("#datepicker_display_type option:selected").val();

            var return_text = '[esigdatepicker name="' + name + '" label="' + picker_label + '" verifysigner="' + verifysigner_picker + '" mindate="' + startDate + '" maxdate="' + endDate + '" displaytype="' + display_type + '"  ' + dtreadonly + '   ' + required + ']';
            esig_sif_admin_controls.insertContent(return_text);

            tb_remove();
            // clear datepciker  sif input . 
            $('.esig-sif-panel-datepicker').find('input[type=text]').val('');
            return false;
        });

        $('body').on('change', '.esig-sif-panel-datepicker #sif_invite_select', function () {

            $('.esig-sif-panel-datepicker #esign-sif-signer-msg').hide();

        });

        /********************************** date picker action end here ***************************************************/

        /* ################################# today date actions start here ###############################3############### */

        // date picket
        $('body').on('click', '.esig-sif-panel-todaydate .insert-todaydate', function () {

            var name = 'esig-sif-today-' + Date.now();

            // var picker_label = $("input[name='datepickerlabel']").val();

            var verifysigner_picker = $(".sif_popup_main_todaydate #sif_invite_select option:selected").val();

            if (verifysigner_picker == 'undefined')
            {
                $('.esig-sif-panel-todaydate #esign-sif-signer-msg').show();
                return false;
            }
            var required = $('.esig-sif-panel-todaydate input.required').prop('checked') ? ' required="1"' : '';
            var display_type = $("#todaydate_display_type option:selected").val();

            var return_text = '[esigtodaydate name="' + name + '" verifysigner="' + verifysigner_picker + '" displaytype="' + display_type + '"]';
            esig_sif_admin_controls.insertContent(return_text);

            tb_remove();

            return false;
        });

        $('body').on('change', '.esig-sif-panel-todaydate #sif_invite_select', function () {

            $('.esig-sif-panel-datepicker #esign-sif-signer-msg').hide();

        });

        /********************************** Today date  action end here ***************************************************/

        /* ################################## file action start here ####################################################### */

        // files
        $('body').on('click', '.esig-sif-panel-file .insert-file', function () {

            var name = 'esig-sif-file-' + Date.now();

            var file_label = $("input[name='filelabel']").val();

            var file_extension = $("input[name='file_extension']").val();
            var file_size = $("input[name='max_file_size']").val();
            if (typeof (file_size) != "undefined" && !$.isNumeric(file_size))
            {
                $('.esig-sif-panel-textfield #esign-sif-size-msg').show();
                return false;
            }
            var verifysigner_file = $(".sif_popup_main_file #sif_invite_select option:selected").val();
            if (!file_extension)
            {
                $('.esig-sif-panel-file #esign-sif-extension-msg').show();
                return false;
            }
            if (verifysigner_file == 'undefined')
            {
                $('.esig-sif-panel-file #esign-sif-signer-msg').show();
                return false;
            }
            var required = $('.esig-sif-panel-file input.required').prop('checked') ? ' required="1"' : '';
            //var display_type = $("#upload_display_type").val();


            var return_text = '[esigfile name="' + name + '" label="' + file_label + '"  verifysigner="' + verifysigner_file + '" extensions="' + file_extension + '" filesize="' + file_size + '"   ' + required + ']';

            esig_sif_admin_controls.insertContent(return_text);

            $('.esig-sif-panel-file #esign-sif-extension-msg').hide();

            tb_remove();
            // clear file  sif input . 
            $('.esig-sif-panel-file').find('input[name=filelabel],input[name=file_extension]').val('');
            return false;
        });

        // sif signer select msg changeing 
        $('body').on('change', '.esig-sif-panel-file #sif_invite_select', function () {

            $('.esig-sif-panel-file #esign-sif-signer-msg').hide();

        });

        // file upload option advanced tab start hre 
        $("#sif_file_advanced_button").popover({
            placement: 'bottom',
            html: 'true',
            title: '<span><strong>Advanced Settings</strong></span>' +
                    '<span class="close">&times;</span>',
            content: function () {

                return $('.sif_file_advanced_content').html()
            },
            template: '<div class="popover" role="tooltip"><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
        });

        /* changing file popover content */
        $('#sif-file-advanced-button').on('click', '.popover .close', function ()
        {

            var content = $('#max-file-size').val();
            if (!esign.isValidNumber(content))
            {
                $('.sif_file_advanced_content #max-file-size').css('border', '1px solid red');
            }
            $('.sif_file_advanced_content #max-file-size').attr('value', content);

            $("#sif_file_advanced_button").popover('toggle');
        });

        $('body').on('change keyup paste', '.popover #max-file-size', function () {

            var maxsize = Math.abs($("body .popover input[name='max_file_size']").val());
            if (!esign.isValidNumber(maxsize))
            {
                $(this).css('border', '1px solid red');
                return false;
            } else {

                $(this).css('border', '0px solid red');
            }
        });


        /*********************************** file action end here ***********************************************************/


        /* ################################### radio action start here #################################################### */
        var sif_radio_display = 'vertical';
        // Radios
        $('body').on('click', '.esig-sif-panel-radio .insert-btn', function ()
        {
            var name = 'esig-sif-' + Date.now();

            var radio_label = $(".esig-sif-panel-radio input[name='radiolabel']").val();



            if ($('#radio_vertical').is(':checked'))
            {
                sif_radio_display = 'vertical';
            }
            if ($('#radio_horizontal').is(':checked'))
            {
                sif_radio_display = 'horizontal';
            }

            var verifysigner_radio = $(".sif_radio_signer_info #sif_invite_select option:selected").val();

            if (verifysigner_radio == 'undefined')
            {
                $('.esig-sif-panel-radio #esign-sif-signer-msg').show();
                return false;
            }

            var required = $('.esig-sif-panel-radio input.required').prop('checked') ? 'required="1"' : '';
            var radios = $('.esig-sif-panel-radio .hidden_radio').serialize();
            //var display_type = $("#radio_display_type").val();
            var return_text = ' [esigradio name="' + name + '" label="' + radio_label + '" display="' + sif_radio_display + '" verifysigner="' + verifysigner_radio + '" labels="' + radios + '"  ' + required + ' ] ';
            esig_sif_admin_controls.insertContent(return_text);
            //sif advanced radio pophover hide
            //$('#radio_vertical').attr("checked", "checked");
            $("#sif_radio_advanced_button").popover('hide');
            tb_remove();
            // clear radio  sif input . 
            $('.esig-sif-panel-radio').find('input[type=text]').val('');
            $('#removeradio').remove();
            return false;
        });

        // sif signer select msg changeing 
        $('body').on('change', '.esig-sif-panel-radio #sif_invite_select', function () {

            $('.esig-sif-panel-radio #esign-sif-signer-msg').hide();

        });


        $('body').on('change keyup paste', '.popover #radiocheck', function () {


            var sif_display = $("body .popover input[name='sif_radio_position']:checked").val();

            var htmltext = '<input type="hidden" name="display_position" value="' + sif_display + '">';

            $('#radio_html').append(htmltext);
        });

        $("body").on("click", ".esig-sif-panel-radio ul li #addRadio", function ()
        {
            $("#radio_html").append("<li id=\"removeradio\">" +
                    "<input type=\"radio\" name=\"esig-radio-sif\"/>" +
                    "<span style=\"margin-left:3px;\"><input type=\"text\" class=\"deletablesif\" name=\"label[]\" placeholder=\"Label\" value=\"\" /></span>" +
                    "<input type=\"hidden\" class=\"hidden_radio\" name=\"\" value=\"\">" +
                    "<span class=\"icon-plus\" id=\"addRadio\"></span><span class=\"icon-minus\" id=\"minusRadio\"></span></span>" +
                    "</li>");
        });

        $('ul li #minusRadio').live('click', function ()
        {
            $(this).parent().remove();
            return false;
        });

        // Enter user's label into name attribute of radio
        $('.esig-sif-panel-radio').on('change', 'input:text', function () {
            var name = $(this).val();
            var box = $(this).closest('li').find('.hidden_radio');
            if (box.length) {
                $(box).attr('name', name);
            }
        });

        // Enter checked into value of hidden radio
        $('.esig-sif-panel-radio').on('change', 'input:radio', function () {
            var box = $(this).closest('li').find('.hidden_radio');
            if (box.length) {
                var checked = $(this).attr('checked') ? '1' : '0';
                $(box).val(checked);
            }
        });

        //advanced settings of radio button 
        $("#sif_radio_advanced_button").popover(
                {
                    placement: 'bottom',
                    html: 'true',
                    title: '<span><strong>Advanced Settings</strong></span>' +
                            '<span class="close">&times;</span>',
                    content: $('.sif_radio_advanced_content').html(),
                    template: '<div class="popover" role="tooltip"><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
                });


        $('#sif_radio_advanced_button').on('shown.bs.popover', function () {
            if (sif_radio_display == "vertical") {
                $("#radio_vertical").prop("checked", true);
            } else if (sif_radio_display == "horizontal") {
                $("#radio_horizontal").prop("checked", true);
            }

        });

        $('#sif_radio_advanced_button').on('hide.bs.popover', function () {

            if ($('#radio_vertical').is(':checked'))
            {
                sif_radio_display = 'vertical';
            }
            if ($('#radio_horizontal').is(':checked'))
            {
                sif_radio_display = 'horizontal';
            }

        });

        $('.sif_advanced_button_area').on('click', '.popover .close', function ()
        {

            if ($('#radio_vertical').is(':checked'))
            {
                sif_radio_display = 'vertical';
            }
            if ($('#radio_horizontal').is(':checked'))
            {
                sif_radio_display = 'horizontal';
            }
            $("#sif_radio_advanced_button").popover('toggle');
        });

        /************************************** radio action end here ******************************************************/


        /* ###################################### checkbox action start here ############################################## */

        var sif_display = 'vertical';
        // Checkboxes
        $('body').on('click', '.esig-sif-panel-checkbox .insert-btn', function ()
        {
            var name = 'esig-sif-' + Date.now();
            var checkbox_label = $("input[name='checkboxlabel']").val();



            if ($('#box-vertical').is(':checked'))
            {
                sif_display = 'vertical';
            }

            if ($('#box-horizontal').is(':checked'))
            {
                sif_display = 'horizontal';
            }

            var verifysigner_check = $(".sif_checkbox_signer_info #sif_invite_select option:selected").val();
            // showing message not signer select
            if (verifysigner_check == 'undefined')
            {
                $('.esig-sif-panel-checkbox #esign-sif-signer-msg').show();
                return false;
            }

            var required = $('.esig-sif-panel-checkbox input.required').prop('checked') ? ' required="1"' : '';
            var boxes = $('.esig-sif-panel-checkbox .hidden_checkbox').serialize();
            //var display_type = $("#checkbox_display_type").val();
            var return_text = ' [esigcheckbox name="' + name + '" label="' + checkbox_label + '" display="' + sif_display + '" verifysigner="' + verifysigner_check + '" boxes="' + boxes + '" ' + required + ' ] ';

            esig_sif_admin_controls.insertContent(return_text);
            //pophover is hide 
            // $('#box-vertical').attr("checked", "checked");
            $("#sif_radio_advanced_button").popover('hide');
            tb_remove();
            // clear checkbox  sif input . 
            $('.esig-sif-panel-checkbox').find('input[type=text]').val('');
            $('#removecheckbox').remove();
            return false;
        });

        // sif signer select msg changeing 
        $('body').on('change', '.esig-sif-panel-checkbox #sif_invite_select', function () {

            $('.esig-sif-panel-checkbox #esign-sif-signer-msg').hide();

        });

        $('body').on('change keyup paste', '.popover #checkboxcheck', function ()
        {
            var sif_display = $("body .popover input[name='sif_checkbox_position']:checked").val();

            var htmltext = '<input type="hidden" name="display_position" value="' + sif_display + '">';

            $('#checkbox_html').append(htmltext);
        });

        // Enter user's label into name attribute of hidden checkbox
        $('.esig-sif-panel-checkbox').on('change', 'input:text', function () {
            var name = $(this).val();
            var box = $(this).closest('li').find('.hidden_checkbox');
            if (box.length) {
                $(box).attr('name', name);
            }
        });

        // Enter checked into value of hidden checkbox
        $('.esig-sif-panel-checkbox').on('change', 'input:checkbox', function () {
            var box = $(this).closest('li').find('.hidden_checkbox');
            if (box.length) {
                var checked = $(this).attr('checked') ? '1' : '0';
                $(box).val(checked);
            }
        });

        $("body").on("click", ".esig-sif-panel-checkbox ul li #addCheckbox", function ()
        {
            $("#checkbox_html-rupom").append("<li id=\"removecheckbox\">" +
                    "<input type=\"checkbox\" name=\"\"/>" +
                    "<span style=\"margin-left:3px;\"><input type=\"text\" name=\"label[]\" placeholder=\"Label\" value=\"\" /></span>" +
                    "<input type=\"hidden\" class=\"hidden_checkbox\" name=\"\" value=\"\">" +
                    "<span class=\"icon-plus\" id=\"addCheckbox\"></span><span class=\"icon-minus\" id=\"minusCheckbox\"></span></span>" +
                    "</li>");
        });

        $('ul li #minusCheckbox').live('click', function ()
        {
            $(this).parent().remove();
            return false;
        });

        $("#sif_checkbox_advanced_button").popover({
            placement: 'bottom',
            html: 'true',
            title: '<span><strong>Advanced Settings</strong></span>' +
                    '<span class="close">&times;</span>',
            content: function () {
                return $('.sif_checkbox_advanced_content').html();
            },
            template: '<div class="popover" role="tooltip"><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
        });



        $('#sif_checkbox_advanced_button').on('shown.bs.popover', function () {
            if (sif_display == "vertical") {
                $("#box-vertical").prop("checked", true);
            } else if (sif_display == "horizontal") {
                $("#box-horizontal").prop("checked", true);
            }

        });

        $('#sif-checkbox-advanced-button').on('hide.bs.popover', function () {

            if ($('#box-vertical').is(':checked'))
            {
                sif_display = 'vertical';
            }

            if ($('#box-horizontal').is(':checked'))
            {
                sif_display = 'horizontal';
            }


        });

        /* changing checkbox popover content */
        $('#sif-checkbox-advanced-button').on('click', '.popover .close', function ()
        {
            if ($('#box-vertical').is(':checked'))
            {
                sif_display = 'vertical';
            }

            if ($('#box-horizontal').is(':checked'))
            {
                sif_display = 'horizontal';
            }

            $("#sif_checkbox_advanced_button").popover('toggle');
        });





        /*************************************** checkbox action end here ***************************************************/


        /* ####################################### dropdown action start here ############################################### */

        // Checkboxes
        $('body').on('click', '.esig-sif-panel-dropdown .insert-btn', function ()
        {
            var name = 'esig-sif-' + Date.now();
            var dropdown_label = $("input[name='dropdownlabel']").val();
            var sif_display = '';


            var verifysigner_check = $(".sif_dropdown_signer_info #sif_invite_select option:selected").val();
            // showing message not signer select
            if (verifysigner_check == 'undefined')
            {
                $('.esig-sif-panel-dropdown #esign-sif-signer-msg').show();
                return false;
            }

            var required = $('.esig-sif-panel-dropdown input.required').prop('checked') ? ' required="1"' : '';
            var boxes = $('.esig-sif-panel-dropdown .hidden_dropdown').serialize();
            if (!boxes) {
                alert("Dropdown option can not be empty");
                return false;
            }
            // var display_type = $("#dropdown_display_type").val();
            var return_text = ' [esigdropdown name="' + name + '" label="' + dropdown_label + '" verifysigner="' + verifysigner_check + '" boxes="' + boxes + '"  ' + required + ' ] ';

            esig_sif_admin_controls.insertContent(return_text);
            //pophover is hide 
            tb_remove();
            // clear checkbox  sif input . 
            $('.esig-sif-panel-dropdown').find('input[type=text]').val('');
            $('#removedropdown').remove();
            return false;
        });

        // sif signer select msg changeing 
        $('body').on('change', '.esig-sif-panel-dropdown #sif_invite_select', function () {

            $('.esig-sif-panel-dropdown #esign-sif-signer-msg').hide();

        });

        // Enter user's label into name attribute of hidden checkbox
        $('.esig-sif-panel-dropdown').on('change', 'input:text', function () {
            var name = $(this).val();
            var box = $(this).closest('li').find('.hidden_dropdown');
            if (box.length) {
                $(box).attr('name', name);
            }
        });

        // Enter checked into value of hidden checkbox
        $('.esig-sif-panel-checkbox').on('change', 'input:checkbox', function () {
            var box = $(this).closest('li').find('.hidden_checkbox');
            if (box.length) {
                var checked = $(this).attr('checked') ? '1' : '0';
                $(box).val(checked);
            }
        });

        $("body").on("click", ".esig-sif-panel-dropdown ul li #addDropdown", function ()
        {
            $("#dropdown_html-rupom").append("<li id=\"removedropdown\">" +
                    "<span style=\"margin-left:3px;\"><input type=\"text\" name=\"label[]\" placeholder=\"Label\" value=\"\" /></span>" +
                    "<input type=\"hidden\" class=\"hidden_dropdown\" name=\"\" value=\"\">" +
                    "<span class=\"icon-plus\" id=\"addDropdown\"></span><span class=\"icon-minus\" id=\"minusDropdown\"></span></span>" +
                    "</li>");
        });

        $('ul li #minusDropdown').live('click', function ()
        {
            $(this).parent().remove();
            return false;
        });

        /***************************************** dropdown action end here **************************************************/

    });


    /*
     Main Class for admin controls
     */
    esig_sif_admin_controls = {
        menu_class: "mce-esig-sif-adminMainMenu",
        initialized: false,
        menu_timer: null,
        editor: null,
        mode: 'mce', // mce or quicktag mode
        quicktag: null, // wp quicktag for text-only mode
        canvas: null, // wp post textarea
        element: null, // the button the user clicked to open this menu

        // Initializes the main menu
        // canvas and element are only used for quicktags
        mainMenuInit: function (editor) {
            var self = this;
            self.editor = editor;

            var commands = esignTextInputSif;

            var buttons = '';
            $.each(commands, function (key, command) {
                buttons = buttons + '<li class="esigbtn" data-label="' + command.label + '" data-cmd="' + key + '">' + command.label + "</li>\n";
            });
            var ul = '<ul style="display:none;" class="' + self.menu_class + '">' + buttons + '</ul>';
            $('.mceIcon.mce_esig_sif').append(ul); // Add menu html to mce

            // Add wrapper around quicktag
            $('#qt_document_content_esig_1').wrap('<span id="qt_document_content_esig_1_wrap"></span>');

            // Add menu html to quicktag wrapper
            $('#qt_document_content_esig_1_wrap').append(ul);

            $('.' + self.menu_class).mouseout(function () {
                var menu = this;
                self.menu_timer = setTimeout(function () {
                    $('.' + self.menu_class).hide();
                }, 200);
            }).mouseover(function () {
                if (self.menu_timer) {
                    clearTimeout(self.menu_timer);
                }
            });

            $('.' + self.menu_class + ' > li.esigbtn').click(function () {

                var cmd = $(this).data('cmd');
                var label = $(this).data('label');

                if (cmd == 'textfield') {

                    self.popupMenuShow(cmd);

                } else if (cmd == 'todaydate') {
                    self.popupMenuShow(cmd);
                    //self.insertContent('[esigtodaydate]');

                } else if (cmd == 'page_break') {

                    self.insertContent('[esig-page-break]');

                } else if (cmd == 'radio') {

                    self.popupMenuShow(cmd);

                } else if (cmd == 'checkbox') {

                    self.popupMenuShow(cmd);

                } else if (cmd == 'dropdown') {

                    self.popupMenuShow(cmd);

                } else if (cmd == 'textarea') {

                    self.popupMenuShow(cmd);

                } else if (cmd == 'datepicker') {

                    self.popupMenuShow(cmd);

                } else if (cmd == 'file') {

                    self.popupMenuShow(cmd);

                } else if (cmd == 'Contact') {
                    $(".chosen-container").css("min-width", "250px");
                    tb_show("+ Contact form 7 option", "#TB_inline?width=450&height=300&inlineId=esig-contact-option");
                } else {
                    $(".chosen-container").css("min-width", "250px");
                    tb_show("+ " + label + "", "#TB_inline?width=450&height=300&inlineId=esig-" + cmd.toLowerCase() + "-option");
                }

            });
            this.initialized = true;
        },
        // Show the main menu attached to element
        // mode = 'mce' or 'quicktag'
        mainMenuShow: function (mode, element) {
            this.mode = (mode == 'mce') ? 'mce' : 'quicktag';
            $('.' + this.menu_class, element).show();
        },
        // Shows the pop-up modal window
        popupMenuShow: function (cmd) {

            var width = jQuery(window).width();

            if (mysifAjax.document_id) {

                jQuery.ajax({
                    type: "POST",
                    url: mysifAjax.ajaxurl + "?action=signerdefine",
                    data: {
                        esig_sif_document_id: mysifAjax.document_id,
                        sif_signer: mysifAjax.sif_signer,
                    },
                    success: function (data, status, jqXHR) {

                        //if ($("#signer_display").length == 0){

                        if (cmd == 'textfield') {
                            $("#sif_text_advanced_button").show();
                            jQuery(".sif_text_signer_info").html(data);
                        } else if (cmd == 'textarea') {
                            //$("#sif_text_advanced_button").show();
                            jQuery(".sif_textarea_signer_info").html(data);
                        } else if (cmd == 'radio')
                        {
                            $("#sif_radio_advanced_button").show();
                            jQuery(".sif_radio_signer_info").html(data);
                        } else if (cmd == 'checkbox')
                        {
                            jQuery(".sif_checkbox_signer_info").html(data);
                        } else if (cmd == 'dropdown')
                        {
                            jQuery(".sif_dropdown_signer_info").html(data);
                        } else if (cmd == 'datepicker')
                        {
                            // jQuery(".sif_popup_main_datepicker").
                            jQuery(".sif_popup_main_datepicker").html(data);
                        } else if (cmd == 'todaydate')
                        {
                            // jQuery(".sif_popup_main_todaydate").
                            jQuery(".sif_popup_main_todaydate").html(data);
                        } else if (cmd == 'file')
                        {
                            // jQuery(".sif_popup_main_datepicker").
                            jQuery(".sif_file_signer_info").html(data);
                        }

                        //}
                    },
                    error: function (xhr, status, error) {
                        alert(xhr.responseText);
                    }
                });

            }

            /* if (cmd == 'todaydate')
             {
             esig_sif_admin_controls.insertContent('[esigtodaydate]');
             return;
             }*/
            if (cmd == 'page_break')
            {
                esig_sif_admin_controls.insertContent('[esig-page-break]');
                return;
            }

            $('.esig-sif-main-panels .esigpanel').hide();
            //hide all the signer error msg 
            $('#esign-sif-signer-msg').hide();
            $('#esign-sif-extension-msg').hide();

            $('.esig-sif-panel-' + cmd).show();




            tb_show('+ Signer input fields', '#TB_inline?&inlineId=esig-sif-admin-panel');



        },
        // Inserts content into the post canvas
        insertContent: function (content) {
            // Visual mode
            if (this.mode == 'mce') {
                this.editor.execCommand('mceInsertContent', 0, content);

                // Quicktag
            } else {
                this.quicktag.tagStart = content;
                QTags.TagButton.prototype.callback.call(this.quicktag, this.element, this.canvas, this.editor);

            }
        },
        // Settings required for quicktag
        initQuicktag: function (quicktag, element, canvas) {
            this.quicktag = quicktag;
            this.element = element;
            this.canvas = canvas;
        }
    }


}(jQuery));


function esig_sif_quicktag(element, canvas, editor)
{
    if (!esig_sif_admin_controls.initialized) {
        esig_sif_admin_controls.mainMenuInit(editor);
    }
    if (!esig_sif_admin_controls.quicktag) {
        esig_sif_admin_controls.initQuicktag(this, element, canvas);
    }
    esig_sif_admin_controls.mainMenuShow('quicktag', jQuery('#qt_document_content_esig_1_wrap'));
}
