(function ($) {


    var dialog_setup = '#installation-setup-check-dialog';
    var dialog_check = '#checking-installation-dialog';



//transition effect
    $('#esig-installation-check-overlay').fadeIn(500);
    $('#esig-installation-check-overlay').fadeTo("slow", 0.9);
    $('#esig-installation-check-overlay').css("width",$( window ).width());




//transition effect
    $(dialog_setup).fadeIn(2000);

    $('#esig-system-check-close').click(function () {
          $(dialog_setup).fadeOut(300);
          $('#esig-installation-check-overlay').fadeOut(300);
    });
//if close button is clicked
    $('#esig-system-checklist-letsgo').click(function (e) {

        e.preventDefault();

        //$('#esig-installation-check-overlay').hide();
        $(dialog_setup).hide();

        $(dialog_check).show();

        // starting checking here 
        var is_false = esig_system_requirement.count ;
        var number =0; 
        while(is_false > 0){
            
            var result = $.fn.esig_system_check(number);
                result.success(function (data) {
                    
                    if(data.display == "success"){
                       $('#checking-installation-header').hide(); 
                       $(dialog_check).append(data.content);
                    }
                    else {
                       $("#checking-installation-footer").html(data.content); 
                    }
                    
                });
                
            number++ ; 
            is_false--;
        }
         
    });
    
    $('body').on('click', '#esig-system-checklist-retry', function () {
  
        $('#checking-installation-header').show();
        $('#installation-check-fail').remove();
        $(dialog_check).hide();
        $(dialog_setup).show();
        $('#esig-system-checklist-letsgo').trigger('click');
       // $('.installation-setup-check-window').hide();
    });

    $('body').on('click', '#esig-checklist-success-continue', function () {
   
        $('#esig-installation-check-overlay').hide();
        $(dialog_check).hide();
       
    });


//if mask is clicked
    $('#esig-installation-check-overlay').click(function () {
       // $(this).hide();
      //  $('.installation-setup-check-window').hide();
    });

    $.fn.esig_system_check = function (number) {
        
        //return $.post(esigAjax.ajaxurl + "?action=wp_e_signature_ajax&className=WP_E_aboutsController&method=esig_requirement_checking",{ esig_system_index: number});
       return $.ajax({
            type      : 'POST', //Method type
            url       : esigAjax.ajaxurl + "?action=wp_e_signature_ajax&className=WP_E_aboutsController&method=esig_requirement_checking", //Your form processing file URL
            data      : { esig_system_index: number}, //Forms name
            dataType  : 'json'
        });
    }

})(jQuery);
