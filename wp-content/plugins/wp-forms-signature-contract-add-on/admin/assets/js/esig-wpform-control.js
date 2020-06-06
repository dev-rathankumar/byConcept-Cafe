(function($){

	 
       //almost done modal dialog here 
       $( "#esig-wpform-almost-done" ).dialog({
			  dialogClass: 'esig-dialog',
			  height:350,
			  width:350,
			  modal: true,
			});
            
      // do later button click 
       $( "#esig-wpform-setting-later" ).click(function() {
          $( '#esig-wpform-almost-done' ).dialog( "close" );
        });
      
     
		
})(jQuery);


