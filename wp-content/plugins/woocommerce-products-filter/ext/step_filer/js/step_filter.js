var woof_step_autosubmit=0;
var woof_step_pre_autosubmit={};
function woof_step_filter_html_items(){
    var step_filter= jQuery('.woof_step');
    
    if(step_filter.length){
        //woof_step_filter_check_items();
        var type =step_filter.data('filter_type');
        woof_step_filter_select_fix();
        
        if(type==2){
            woof_autosubmit=1;
            woof_ajax_redraw = 1;
        }
        
        woof_step_filter_check_items(type);

        if(type==1){
            check_next_prev_btn();
        }
        
        step_filter.slideDown('100');

        if(type==1){
          //  var reset_btn=jQuery('.woof_step .woof_reset_search_form');
          //  if(reset_btn.length){
              //var btn = reset_btn.detach(); 
             // jQuery('.woof_step_filter_prev').after(btn);
           // }
            jQuery('.woof_step_filter_next').unbind('click');
            jQuery('.woof_step_filter_next').click(function(){
                woof_submit_link_locked = false;
                woof_ajax_redraw = 1;
               // woof_is_ajax = 1;    
                woof_submit_link(woof_get_submit_link());
               // check_next_prev_btn();
            });
            jQuery('.woof_step_filter_prev').unbind('click');
            jQuery('.woof_step_filter_prev').click(function(){
                
                var element = jQuery('.woof_step_filter_current').prev();
                if(!element.length){
                    element=jQuery(this).parents('.woof_step').find('.woof_container.woof_step_hide').last();                    
                }     
                
                var current_filter=woof_step_filter_delete_filter_data(element);

                jQuery.each(current_filter,function(i,item){
                    delete woof_current_values[item];
                });
                woof_submit_link_locked = false;
                woof_ajax_redraw = 1;
                //woof_is_ajax = 1;    
                woof_submit_link(woof_get_submit_link());
               // check_next_prev_btn();
            });
        }
    } 



}

function woof_step_filter_select_fix(){
    var mselect = jQuery('.woof_container_mselect');
    var select = jQuery('.woof_container_select');
    var select_h = jQuery('.woof_container_select_hierarchy');

    if(jQuery(mselect).length){
        jQuery.each(jQuery(mselect),function(i,item){
            if(jQuery(item).find('select option').length<1){
                jQuery(item).remove();
            }else if(jQuery(item).find('select option').length==1 && jQuery(item).find('select option').val()==0){
				jQuery(item).remove();
			}
        });
    }
    if(jQuery(select).length){
		           
        jQuery.each(jQuery(select),function(i,item){
			 
            if(jQuery(item).find('select option').length<=1){
                jQuery(item).remove();
            }
        });
    }
    if(jQuery(select_h).length){
        jQuery.each(jQuery(select_h),function(i,item){
            if(jQuery(item).find('select option').length<=1){
                jQuery(item).remove();
            }
        });
    }
}
function check_next_prev_btn(){

    var curr_el=jQuery('.woof_step_filter_current');
    jQuery('.woof_step .woof_container_inner input').on('ifChecked', function (event) {
        if(woof_step_filter_check_state(jQuery(this).parents(".woof_container")).has){
            jQuery('.woof_step_filter_next').prop( "disabled", false ); 
        }else{
            jQuery('.woof_step_filter_next').prop( "disabled", true );
        }        
    });
    jQuery('.woof_step .woof_container_inner input').on('ifUnchecked', function (event) {
        if(woof_step_filter_check_state(jQuery(this).parents(".woof_container")).has){
            jQuery('.woof_step_filter_next').prop( "disabled", false ); 
        }else{
            jQuery('.woof_step_filter_next').prop( "disabled", true );
        }        
    });
    jQuery('.woof_step .woof_container_inner a').on('click', function (event) {
        if(woof_step_filter_check_state(jQuery(this).parents(".woof_container")).has){
            jQuery('.woof_step_filter_next').prop( "disabled", false ); 
        }else{
            jQuery('.woof_step_filter_next').prop( "disabled", true );
        }        
    });
    jQuery('.woof_step .price_slider_wrapper .price_slider').on('click', function (event) {
        if(woof_step_filter_check_state(jQuery(this).parents(".woof_container")).has){
            jQuery('.woof_step_filter_next').prop( "disabled", false ); 
        }else{
            jQuery('.woof_step_filter_next').prop( "disabled", true );
        }        
    });
     jQuery('.woof_step .woof_price_filter_txt').life('change', function () {
        if(woof_step_filter_check_state(jQuery(this).parents(".woof_step_filter_current")).has){
            jQuery('.woof_step_filter_next').prop( "disabled", false ); 
        }else{
            jQuery('.woof_step_filter_next').prop( "disabled", true );
        }        
    });
    jQuery('.woof_step .woof_meta_filter_textinput').keyup(function (e) {
        if(woof_step_filter_check_state(jQuery(this).parents(".woof_step_filter_current")).has){
            jQuery('.woof_step_filter_next').prop( "disabled", false ); 
        }else{
            jQuery('.woof_step_filter_next').prop( "disabled", true );
        }        
    });
    jQuery('.woof_step .woof_show_sku_search').keyup(function (e) {
        if(woof_step_filter_check_state(jQuery(this).parents(".woof_step_filter_current")).has){
            jQuery('.woof_step_filter_next').prop( "disabled", false ); 
        }else{
            jQuery('.woof_step_filter_next').prop( "disabled", true );
        }        
    });
    jQuery('.woof_step .woof_show_text_search').keyup(function (e) {
        if(woof_step_filter_check_state(jQuery(this).parents(".woof_step_filter_current")).has){
            jQuery('.woof_step_filter_next').prop( "disabled", false ); 
        }else{
            jQuery('.woof_step_filter_next').prop( "disabled", true );
        }        
    });
    
    
    
//    jQuery('.woof_step .woof_checkbox_instock').on('ifChecked', function (event) {
//        
//        if(woof_step_filter_check_state(jQuery(this).parents(".woof_container")).has){
//            jQuery('.woof_step_filter_next').prop( "disabled", false ); 
//        }else{
//            jQuery('.woof_step_filter_next').prop( "disabled", true );
//        }        
//    });
//    jQuery('.woof_step .woof_checkbox_instock').on('ifUnchecked', function (event) {
//        if(woof_step_filter_check_state(jQuery(this).parents(".woof_container")).has){
//            jQuery('.woof_step_filter_next').prop( "disabled", false ); 
//        }else{
//            jQuery('.woof_step_filter_next').prop( "disabled", true );
//        }        
//    });
//    jQuery('.woof_step .woof_checkbox_term').on('ifChecked', function (event) {
//        if(woof_step_filter_check_state(jQuery(this).parents(".woof_container")).has){
//            jQuery('.woof_step_filter_next').prop( "disabled", false ); 
//        }else{
//            jQuery('.woof_step_filter_next').prop( "disabled", true );
//        }        
//    });
//    jQuery('.woof_step .woof_checkbox_term').on('ifUnchecked', function (event) {
//
//        if(woof_step_filter_check_state(jQuery(this).parents(".woof_container")).has){
//            jQuery('.woof_step_filter_next').prop( "disabled", false ); 
//        }else{
//            jQuery('.woof_step_filter_next').prop( "disabled", true );
//        }        
//    });
//    jQuery('.woof_step .woof_radio_term').on('ifChecked', function (event) {
//        if(woof_step_filter_check_state(jQuery(this).parents(".woof_container")).has){
//            jQuery('.woof_step_filter_next').prop( "disabled", false ); 
//        }else{
//            jQuery('.woof_step_filter_next').prop( "disabled", true );
//        }        
//    });
    
    jQuery('.woof_step_filter_current').click(function(){
        if(woof_step_filter_check_state(this).has){
            jQuery('.woof_step_filter_next').prop( "disabled", false ); 
        }else{
            jQuery('.woof_step_filter_next').prop( "disabled", true );
        }
    });

	jQuery('.woof_step_filter_current [type="checkbox"]').on('change', function (event) {
        if(woof_step_filter_check_state(jQuery(this).parents(".woof_step_filter_current")).has){
            jQuery('.woof_step_filter_next').prop( "disabled", false ); 
        }else{
            jQuery('.woof_step_filter_next').prop( "disabled", true );
        }  
    });
    jQuery('.woof_step_filter_current [type="radio"]').on('change', function (event) {
        if(woof_step_filter_check_state(jQuery(this).parents(".woof_step_filter_current")).has){
            jQuery('.woof_step_filter_next').prop( "disabled", false ); 
        }else{
            jQuery('.woof_step_filter_next').prop( "disabled", true );
        }  
    });
    
    if(woof_step_filter_check_state(curr_el).has){
        jQuery('.woof_step_filter_next').prop( "disabled", false );
    }else{
        jQuery('.woof_step_filter_next').prop( "disabled", true ); 
    }

    if(jQuery(curr_el).next('.woof_submit_search_form_container').length){
        jQuery('.woof_step_filter_next').prop( "disabled", true );
        woof_autosubmit=1;
        woof_ajax_redraw = 1;

        if(jQuery('.woof_step').data('autosubmit')){
            woof_ajax_redraw = 0; 
        }
                
    }else{
        woof_autosubmit=0;  
    }


    
    if(jQuery(curr_el).is(":first-child")){
        jQuery('.woof_step_filter_prev').prop( "disabled", true );
    }else{
        jQuery('.woof_step_filter_prev').prop( "disabled", false );
    }

    if(!curr_el.length){       
        //jQuery('.woof_container.woof_step_hide').last().show();
        var prev_btn = jQuery('.woof_step .woof_step_filter_prev').clone();
        prev_btn.prependTo('.woof_step .woof_submit_search_form_container');
        
        jQuery('.woof_step_next_back_btn').hide();
    }else{
        jQuery('.woof_step .woof .woof_submit_search_form_container').hide();
        jQuery('.woof_step_next_back_btn').show(); 
        jQuery('.woof_step .woof_submit_search_form_container .woof_step_filter_prev').remove();
    }
    
}

function woof_step_filter_check_items(type){
    var items=jQuery('.woof_step .woof .woof_container');
    var woof_current_values_temp={};
    var hide=false;
    var first=0;
    jQuery.each(items, function(i,item){
        if(type==1){
            jQuery(item).addClass('woof_step_hide');
        }else if(type==2){
            if(i!=0 && hide){
                jQuery(item).addClass('woof_step_hide');
            }else{
                jQuery(item).removeClass('woof_step_hide');
            }                       
        }
  
        var stat = woof_step_filter_check_state(item);      
        if(!stat.has){
            hide=true;
            first++;
            if(first==1){
                jQuery(item).addClass('woof_step_filter_current');
                jQuery(item).removeClass('woof_step_hide');
            }
            
        }  
        
        if(!hide && stat.key){
            jQuery.each(stat.key,function(i,val){
                if(woof_current_values[val]){
                    woof_current_values_temp[val] = woof_current_values[val];
                }
            });
            if(woof_current_values['page']!=undefined){
               woof_current_values_temp['page'] = woof_current_values['page']; 
            }
            if(woof_current_values['paged']!=undefined){
               woof_current_values_temp['paged'] = woof_current_values['paged']; 
            }
            if(woof_current_values['orderby']!=undefined){
               woof_current_values_temp['orderby'] = woof_current_values['orderby'];
            }
            if(woof_current_values['order']!=undefined){
               woof_current_values_temp['order'] = woof_current_values['order']; 
            }
                 
        }

        if(type==2){
            if(jQuery('.woof_step_filter_current').next('.woof_submit_search_form_container').length){
                //woof_ajax_redraw = 1;
                 jQuery('.woof_step_filter_current').click(function(){

                   woof_step_autosubmit=1;
                 }); 
            }
        }
		
		var curr_el = jQuery('.woof_step_filter_current');
		if(jQuery(curr_el).length==0){	
			if(jQuery(woof_step_pre_autosubmit).data('css-class')==jQuery(".woof_step.woof_autosubmit").find(".woof_container").last().data('css-class')){
				woof_step_autosubmit=1;
				woof_step_filter_submit();
			}				
		}else{			
			woof_step_pre_autosubmit=curr_el
		}		
		
        if(!hide && i == items.length-1){           
            woof_step_filter_submit();
        }

                
    });
    woof_current_values=woof_current_values_temp;    

}

function woof_step_filter_check_items_(){
    
    var items=jQuery('.woof_step .woof .woof_container');
    var woof_current_values_temp={};
    var hide=false;
    var first=0;
    jQuery.each(items, function(i,item){
        if(i!=0 && hide){
            jQuery(item).addClass('woof_step_hide');
        }else{
            jQuery(item).removeClass('woof_step_hide');
          //  console.log(i);
        }   
       // jQuery(item).removeClass('woof_current');
        var stat = woof_step_filter_check_state(item);
        
        if(!stat.has){
            hide=true;
            first++;
            if(first==1){
                jQuery(item).addClass('woof_step_filter_current');
            }
            
        }  
        
        
        if(!hide && stat.key){
            jQuery.each(stat.key,function(i,val){
                if(woof_current_values[val]){
                    woof_current_values_temp[val] = woof_current_values[val];
                }
            });
            if(woof_current_values['page']!=undefined){
               woof_current_values_temp['page'] = woof_current_values['page']; 
            }
            if(woof_current_values['paged']!=undefined){
               woof_current_values_temp['paged'] = woof_current_values['paged']; 
            }
            if(woof_current_values['orderby']!=undefined){
               woof_current_values_temp['orderby'] = woof_current_values['orderby'];
            }
            if(woof_current_values['order']!=undefined){
               woof_current_values_temp['order'] = woof_current_values['order']; 
            }
                 
        }
          

  
    //console.log(jQuery('.woof_step_filter_current').next('.woof_submit_search_form_container').length);
        
        if(!hide && i == items.length-1){           
            woof_step_filter_submit();
        }

                
    });
    woof_current_values=woof_current_values_temp;
            
}

function woof_step_filter_check_state(_this){
    var stat={
       has:false,
       key:[],
    };
    //console.log(woof_current_values);
    //console.log(jQuery(_this));
    jQuery.each(woof_current_values, function(i,item){
        if(i=='min_price'|| i=='max_price'){
            if(jQuery(_this).hasClass('woof_price_filter')){               
                stat.has = true;
                stat.key = ['min_price','max_price']; 
            }
        }else if(i=='min_rating'|| i=='max_rating'){
            if(jQuery(_this).hasClass('woof_by_rating_container')){               
                stat.has = true;
                stat.key = ['min_rating','max_rating'];
            }
            
        }else if(jQuery(_this).hasClass(('woof_container_'+i))){
                stat.has = true;
                stat.key = [i];
                if(jQuery(_this).hasClass("woof_container_select_hierarchy")){
                    if(jQuery(_this).find(".woof_block_html_items select:last").val()==0){
                        stat.has = false;
                        stat.key = [];
                    }
                }
        }
    });
    return stat;
}
function woof_step_filter_delete_filter_data(_this){
    var key=[];
    if(!jQuery(_this).hasClass('woof_container')){
        _this=jQuery(_this).prev();
    }
    jQuery.each(woof_current_values, function(i,item){
        
        if(i=='min_price'|| i=='max_price'){
            if(jQuery(_this).hasClass('woof_price_filter')){               
                key = ['min_price','max_price'];
            }
            if(jQuery(_this).hasClass('woof_price_filter')){               
                key = ['min_price','max_price'];
            }

        }else if(i=='min_rating'|| i=='max_rating'){
            if(jQuery(_this).hasClass('woof_by_rating_container')){               
                key = ['min_rating','max_rating'];
            }
            
        }else if(jQuery(_this).hasClass(('woof_container_'+i))){
                key = [i];
        }
    });
    return key;
}

function woof_step_filter_submit(){

    if(jQuery('.woof_step').data('autosubmit') && woof_step_autosubmit){
        jQuery('.woof_submit_search_form_container').hide();
       
        woof_submit_link_locked = false;
        //woof_autosubmit=1;
        woof_ajax_redraw = 0; 
        woof_submit_link(woof_get_submit_link());
        woof_step_autosubmit=0;
		woof_step_pre_autosubmit={};
        //  woof_submit_link_locked = true;
        //jQuery('.woof_step .woof .woof_submit_search_form').trigger('click');
        //return false; 
    }else{
        woof_ajax_redraw = 1;
        jQuery('.woof_step .woof .woof_submit_search_form').click(function(){
            woof_ajax_redraw = 0;
        })
        
        jQuery('.woof_submit_search_form_container').show();
        jQuery('.woof_step .woof .woof_submit_search_form').show();
    }
    
    
}



