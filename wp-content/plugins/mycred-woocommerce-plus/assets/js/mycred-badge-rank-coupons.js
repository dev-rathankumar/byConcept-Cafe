jQuery(function($){
	
	$( '#mycred-badge-setup' ).on( 'click', 'button.remove-badge-level', function(e){

		var leveltoremove = $(this).data( 'level' );

		if ( $( '#mycred-badge-level' + leveltoremove ) === undefined ) return false;

		// if ( ! confirm( myCREDBadge.remove ) ) return false;

		$( '#mycred_badge_level_meta' + leveltoremove ).slideUp().remove();
		 

	});

var el = document.getElementById('badges-add-new-level');
	if(el){
	el.addEventListener("click", function(event) { 
	  
	console.log( 'Add new level coupons' );
	
	TotalBadgeLevels  = $( '#mycred-badge-setup #badge-levels .badge-level' ).length;
	 
	TotalRequirements = $( '#mycred-badge-setup .level-requirements .row-narrow' ).length;

	template  =  '<table class="mycred_badge_level_meta" width="100%" id="mycred_badge_level_meta'+TotalBadgeLevels+'">';	  	  

	template +=  '<tr id="discount_type_level'+TotalBadgeLevels+'"><th style="width: 25%">level '+(TotalBadgeLevels+1)+'</th></tr>'; 


	template +=  '<tr id="discount_type'+TotalBadgeLevels+'">';
	template +=  '<td style="width: 25%">Discount Type</td>';
	template +=  '</tr>';

	template +=  '<tr>';
	template +=  '<td>';
	template +=  '<select style="width:245px;" name="woo_discount['+TotalBadgeLevels+'][discount_type]" >';
	template +=  '<option value="fixed">Fixed Discount</option>';
	template +=  '<option value="percent">Percentage  Discount</option>';
	template +=  '</select>';
	template +=  '</td>';
	template += '</tr>';


	template +=  '<tr id="discount_amount0">';
	template +=  '<td>Amount</td>';
	template +=  '</tr>';

	template +=  '<tr>';
	template +=  '<td><input type="text" style="width:245px;"name="woo_discount['+TotalBadgeLevels+'][discount_amount]" value="" />';
	template +=  '</td>';
	template +=  '</tr>';

	template +=  '<tr id="mycred_discount_coupon_code">';
	template +=  '<td>Coupon Code</td>';
	template +=  '</tr>';

	template +=  '<tr>';
	template +=  '<td><input type="text" style="width:245px;"name="woo_discount['+TotalBadgeLevels+'][mycred_coupon_code_badge]" value="" />';
	template +=  '</td>';
	template +=  '</tr>';

	template +=  '</table>';
	
	jQuery( '#mycred_badge_level_meta' ).append( template );
	   
	});
	}	
});
	 