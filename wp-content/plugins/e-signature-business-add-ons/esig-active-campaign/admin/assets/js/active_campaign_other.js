(function($){

$('body').on('click', '#esig_active_campaign .campagin-tag-del', function (e) {
	//$("").click(function(e) {
           // alert();
           e.preventDefault();
          var documentId = $("#esig-active-campaign-document-id").val();
          
    jQuery.ajax({  
        type:"GET",  
        url: esig_active_campaign_ajax_script.ajaxurl,   
        data:{
				esigdocid: documentId,
			},  
        success:function(data, status, jqXHR){   
            if(data !="none"){
               jQuery("#esig_active_campaign").html(data);  
            }
             
        },  
        error: function(xhr, status, error){  
            alert(xhr.responseText); 
        }  
    });  

  });

})(jQuery);

