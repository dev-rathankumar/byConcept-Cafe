

(function($){
        

        // next step click from sif pop
        $( "#esig-wpform-create" ).click(function() {
        
        
 
                   var form_id= $('select[name="esig-wpform-id"]').val();
                  
                 
                   $("#esig-wpform-form-first-step").hide();
                   
                   // jquery ajax to get form field . 
                   jQuery.post(esigAjax.ajaxurl,{ action:"esig_wpform_fields",form_id:form_id},function( data ){ 
                                $("#esig-wpform-field-option").html(data);
				},"html");
                   
                   $("#esig-wpform-second-step").show();                        
  
        });
 
        // contact for 7 add to document button clicked 
        $( "#esig-wpform-insert" ).click(function() {
         
 
                 var formid= $('select[name="esig-wpform-id"]').val();
                   
                 var field_id =$('select[name="esig_wpform_field_id"]').val();
                 var displayType =$('select[name="esig_wpform_value_display_type"]').val();
                  
                  var return_text = '[esigwpform formid="'+ formid +'" field_id="'+ field_id +'" display="'+ displayType +'" ] ';
		  esig_sif_admin_controls.insertContent(return_text);
            
             tb_remove();
                     
                   
        });
        
        
        //if overflow
        $('#select-wpform-form-list').click(function(){
            
            
          
            $(".chosen-drop").show(0, function () { 
				$(this).parents("div").css("overflow", "visible");
				});
            
            
            
        });
	
})(jQuery);



