

(function ($) {


    $('.esig_reminders_setting a').click(function () {
        var doc_remind = $(this).data('reminder');
        var doc_title = decodeURIComponent($(this).data('title'));
        $('.document_title_caption').show();
        $('.instructions').html(doc_title);
        var document_id = $(this).data('document');
        // getting invited signers and reminders details 
        $('#esig_reminder_invite_row').html('<div class="esig-ajax-loader"></div>');

        jQuery.ajax({
            type: "POST",
            url: reminderAjax.ajaxurl + "?action=esig_reminders_settings",
            data: {
                document_id: document_id,
            },
            success: function (data, status, jqXHR) {
                $('#esig_reminder_invite_row').html(data);
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText);
            }
        });

        $('#esig_pause_reminders').html(doc_remind);

        // ajax end here 
        tb_show("", '#TB_inline?width=620&height=475&inlineId=esig_reminder_popup_hidden');

    });
    // update reminder settings clicked 
    $("#esig_update_reminders").live("click", function () {
        // getting varibale from popup . 
        var document_id = $('input[name="document_id_no"]').val();
        var reminder_for = $('input[name="esig_reminder_for"]').val();
        var reminder_repeat = $('input[name="esig_reminder_repeat"]').val();
        var reminder_expire = $('input[name="esig_reminder_expire"]').val();

        if (reminder_for == "" || isNaN(reminder_for))
        {
            $('#reminder_for').css('border', '1px solid #ff0000');
            return false;
        }
        else
        {
            $('#reminder_for').css('border', '0px solid #ff0000');
        }

        if (reminder_repeat == "" || isNaN(reminder_repeat))
        {
            $('#reminder_repeat').css('border', '1px solid #ff0000');
            return false;
        }
        else
        {
            $('#reminder_repeat').css('border', '0px solid #ff0000');
        }

        if (reminder_expire == "" || isNaN(reminder_expire))
        {
            $('#reminder_expire').css('border', '1px solid #ff0000');
            return false;
        }
        else
        {
            $('#reminder_expire').css('border', '0px solid #ff0000');
        }

       
        // setting ajax content . 
        jQuery.ajax({
            type: "POST",
            url: reminderAjax.ajaxurl + "?action=esig_reminders_update",
            data: {
                document_id: document_id,
                reminder_for: reminder_for,
                reminder_repeat: reminder_repeat,
                reminder_expire: reminder_expire,
            },
            success: function (data, status, jqXHR) {
                alert('Successfully updated');

            },
            error: function (xhr, status, error) {
                alert(xhr.responseText);
            }
        });

    });

    // pause reminder settings 
    $('#esig_pause_reminders').click(function () {

        var document_id = $('input[name="document_id_no"]').val();

        var reminder_for = $('input[name="esig_reminder_for"]').val();
        var reminder_repeat = $('input[name="esig_reminder_repeat"]').val();
        var reminder_expire = $('input[name="esig_reminder_expire"]').val();

        if (reminder_for == "" || isNaN(reminder_for))
        {
            $('#reminder_for').css('border', '1px solid #ff0000');
            return false;
        }
        else
        {
            $('#reminder_for').css('border', '0px solid #ff0000');
        }

        if (reminder_repeat == "" || isNaN(reminder_repeat))
        {
            $('#reminder_repeat').css('border', '1px solid #ff0000');
            return false;
        }
        else
        {
            $('#reminder_repeat').css('border', '0px solid #ff0000');
        }

        if (reminder_expire == "" || isNaN(reminder_expire))
        {
            $('#reminder_expire').css('border', '1px solid #ff0000');
            return false;
        }
        else
        {
            $('#reminder_expire').css('border', '0px solid #ff0000');
        }

        jQuery.ajax({
            type: "POST",
            url: reminderAjax.ajaxurl + "?action=esig_reminders_start_pause",
            data: {
                document_id: document_id,
            },
            success: function (data, status, jqXHR) {
                alert('Successfully updated');
                $('#esig_pause_reminders').html(data);
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText);
            }
        });

    });
    // onload show if checked esig reminders 
    if ($('input[name="esig_reminders"]').attr('checked')) {
        $('#esig_reminders_input').show();
    }
    // Show or hide the stand alone console when the box is checked.
    $('input[name="esig_reminders"]').on('change', function () {
        if ($('input[name="esig_reminders"]').attr('checked')) {
            $('#esig_reminders_input').show();
        } else {
            $('#esig_reminder_for').val('');
            $('#esig_reminder_repeat').val('');
            $('#esig_reminder_expire').val('');
            $('#esig_reminders_input').hide();
        }
    });

    // send_instant_reminder_email 
    $('#send_instant_reminder_email').click(function () {




        var esig_signer_email = "";
        if ($('#reminder_checkbox').is(':checked')) {
            esig_signer_email = $("input[name='reminder_email\\[\\]']").map(function () {
                return $(this).val();
            }).get();
        } else {
            return alert("Please check email to send reminder");
        }
        $(this).html('Sending reminder....');


        var document_id = $('input[name="document_id_no"]').val();

        jQuery.ajax({
            type: "POST",
            url: reminderAjax.ajaxurl + "?action=esig_reminders_instant_email",
            data: {
                document_id: document_id,
                esig_reminder_email: esig_signer_email,
            },
            success: function (data, status, jqXHR) {
                //alert(data);
                $('#send_instant_reminder_email').html('Send Reminder Now');
                tb_remove();
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText);
            }
        });

    });

    $("#esig_reminder_for").focusout(function () {

        var remindfor = $('input[name="esig_reminder_for"]').val();

        if (isNaN(remindfor)) {
            alert("Must be numbers");
            $("#esig_reminder_for").focus();
            return false;
        }
    });

    // esig_reminder_repeat 
    $("#esig_reminder_repeat").focusout(function () {

        var remindfor = $('input[name="esig_reminder_repeat"]').val();

        if (isNaN(remindfor)) {
            alert("Must be numbers");
            $("#esig_reminder_repeat").focus();
            return false;
        }
    });

    //esig_reminder_expire
    $("#esig_reminder_expire").focusout(function () {

        var remindfor = $('input[name="esig_reminder_expire"]').val();

        if (isNaN(remindfor)) {
            alert("Must be numbers");
            $("#esig_reminder_expire").focus();
            return false;
        }
    });


})(jQuery);
