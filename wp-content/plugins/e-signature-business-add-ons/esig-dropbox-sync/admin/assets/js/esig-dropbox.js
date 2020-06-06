

(function ($) {


    $('.signature-dropbox-displayonly').each(function (i, e) {

        // alert('you can');
        /*var sigpad = $(e).signaturePad(display_opts);
         var input = $(e).find('input.output');
         if(input && $(input).val()){
         sig = $(input).val();
         sigpad.regenerate(sig);
         } */
    });

    // show dialog if esig pdf is not active 
    //alert(esig_dropbox.folder_url);
    $('input[name="esig_dropbox"]').on('change', function () {

        if ($('input[name="esig_dropbox"]').attr('checked')) {

            var url = esig_dropbox.folder_url + "pdf-error-dialog.php";

            var parent = $(this).attr('data-parent');

            if (parent == "active")
            {
                return true;
            }
            // not active parent addon 
            $('#esig-dialog-content').load(url);
            // show esig dialog 
            $("#esig-dialog-content").dialog({
                dialogClass: 'esig-dialog',
                height: 500,
                width: 600,
                modal: true,
            });

            $('input[name="esig_dropbox"]').prop('checked', false);
            return false;
        } else
        {
            $('input[name="esig_dropbox"]').prop('checked', false);
        }

    });

    // click on authorize
    $('#esig-dropbox-authorize-link').on('click', function (e) {

        e.preventDefault();
        $("#esig-ds-access-coode-container").show();
        var Url = $(this).attr('href');
        PopupCenter(Url, "Name", 600, 600);

    });
    
    
     $('#esig-dropbox-authorize-required').on('click', function (e) {

        e.preventDefault();
        $("#esig-php-required-msg").dialog({
                dialogClass: 'esig-dialog',
                height: 500,
                width: 600,
                modal: true,
            });

    });
    
    $("#esig-dropbox-access-code").bind("paste", function(e){
           $("#esig-dropbox-authorize-link").hide();
           $("#esig-ds-description").show();
    });

})(jQuery);

function PopupCenter(url, title, w, h) {
    // Fixes dual-screen position                         Most browsers      Firefox
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    var width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

    // Puts focus on the newWindow
    if (window.focus) {
        newWindow.focus();
    }
}
