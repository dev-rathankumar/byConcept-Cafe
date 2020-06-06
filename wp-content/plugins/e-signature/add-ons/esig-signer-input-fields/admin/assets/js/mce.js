(function ($) {

    //"use strict";

    //$(function () {
    
    $(".chosen-container").css("min-width","250px");
    
    tinymce.PluginManager.add('esig_sif', function (editor, url) {
        editor.addButton('esig_sif',esign_inputs);
        editor.onLoadContent.add(function (editor, o) {
            esig_sif_admin_controls.mainMenuInit(editor);
        });
    });

    tinymce.init({
        plugins: "advlist"
    });

    // });

}(jQuery));