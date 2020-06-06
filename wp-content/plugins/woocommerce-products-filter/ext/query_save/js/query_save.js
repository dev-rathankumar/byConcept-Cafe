var woof_save_query_init = 0;
function woof_init_save_query() {   
    if (woof_save_query_init > 0) {
	return;
    }
    woof_save_query_init++;
    jQuery('.woof_add_query_save').attr('data-href', location.href);

    jQuery('.woof_add_query_save').life('click', function () {
        var title = jQuery('.woof_save_query_title').val();

        if(!title){
            woof_save_query_check_title();
            return false;
        }
	var data = {
	    action: "woof_save_query_add_query",
            query_title:title,
	    user_id: jQuery(this).attr('data-user'),
            get_var: woof_current_values,
	    link: location.href
	};
	jQuery.post(woof_ajaxurl, data, function (content) {
	    // alert(jQuery.parseJSON(content));
	    if (content) {
		var req = content;
		woof_redraw_save_query(req);
                jQuery('.woof_save_query_title').val("");
	    }
	});

	return false;
    });
    jQuery('.woof_save_query_title').life('keyup', function () {
        var value=jQuery(this).val();
        jQuery('.woof_save_query_title').val(value);
        woof_save_query_check_title();
    });
    jQuery('.woof_remove_query_save').life('click', function () {
	if (!confirm(woof_confirm_lang)) {
	    return false;
	}
	var data = {
	    action: "woof_save_query_remove_query",
	    user_id: jQuery(this).attr('data-user'),
	    key: jQuery(this).attr('data-key')
	};
	// console.log(data);
	jQuery.post(woof_ajaxurl, data, function (content) {

	    var req = jQuery.parseJSON(content);
	    //console.log(req); 
	    jQuery('.woof_query_save_item_' + req.key).remove();
	    woof_check_count_save_query();

	});

	return false;
    });
    function woof_redraw_save_query(data) {
	jQuery('.woof_query_save_list ul').append(data);
	woof_check_count_save_query();
    }

    function woof_check_count_save_query() {
	var count_li = jQuery('.woof_query_save_list ul:first').find('li').length;
	var max_count = jQuery('.woof_add_query_count input[name="add_query_save"]').attr('data-count');
	if (count_li >= max_count) {
	    jQuery('.woof_add_query_count').hide();
	} else {
	    jQuery('.woof_add_query_count').show();

	}

    }
     
    function woof_save_query_check_title(){
        var inputs = jQuery('.woof_save_query_title');
        var has=false;
        jQuery.each(inputs,function(i,input){
            if(jQuery(input).val()){
                has=true;
            }            
        });
        if(has){
            jQuery('.woof_save_query_title').removeClass("woof_save_query_error");
            jQuery('.woof_query_save_title_error').hide();            
        }else{
            jQuery('.woof_save_query_title').addClass("woof_save_query_error");
            jQuery('.woof_query_save_title_error').show();           
        }
    } 
}
