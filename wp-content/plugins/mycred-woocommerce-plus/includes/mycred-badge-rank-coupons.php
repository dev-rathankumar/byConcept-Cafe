<?php
if ( ! class_exists( 'MyCred_woo_badge_rank_copouns' ) ) :
class MyCred_woo_badge_rank_copouns {
  

    public function __construct() {
		
		
		if(get_option( 'mycred_wooplus_show_ranks' ) == 'yes' || 
		  (get_option( 'mycred_wooplus_show_badges' ) == 'yes' )) {
		      
		add_action( 'init',							  array( $this , 'run_badges_and_ranks_copouns'));
		add_action( 'init',							  array( $this , 'my_account_copouns_url_rewrite') );
		add_action( 'admin_init',					  array( $this , 'mycred_add_metabox'));   
		add_action( 'save_post', 					  array( $this , 'mycred_add_badge_rank_fields'),10,2);  
		add_action( 'admin_enqueue_scripts',	      array( $this , 'mycred_badge_rank_coupons_script'));
		add_action( 'woocommerce_account_my-coupons_endpoint', array( $this , 'copouns_shortcode_call') ); 
		add_filter( 'the_title',					  array( $this , 'copouns_tab_endpoint_title'), 10, 2 );
		add_filter( 'woocommerce_account_menu_items', array( $this , 'add_copouns_tab') );  
		add_filter( 'query_vars', 					  array( $this , 'copouns_query_vars'), 0 );
		add_shortcode( 'mycred_badges_ranks_coupons', array( $this , 'mycred_badges_ranks_coupons_lists') );

		}
 
 }
 

	public function copouns_tab_endpoint_title( $title, $id ) {
		
		global $wp_query;
		
			if ( isset( $wp_query->query_vars['my-coupons'] ) && in_the_loop() ) {
				
				return 'My Coupons';
				
			}

		return $title;
	}

	
	public function mycred_badge_rank_coupons_script() {
	
	//if (  'mycred_badge' != get_post_type() ) return;
	
	wp_register_style('mycred_badge_rank_coupons_style',
	plugins_url( 'assets/css/badge_rank_coupons_style.css', MYCRED_WOOPLUS_THIS ));
	
	wp_enqueue_script('mycred_badge_rank_coupons', 
	plugins_url( 'assets/js/mycred-badge-rank-coupons.js', MYCRED_WOOPLUS_THIS ), array('jquery')); 
	 
	wp_enqueue_script( 'mycred_badge_rank_coupons' );
	wp_enqueue_style( 'mycred_badge_rank_coupons_style' );
	
	}


     
	// mycode for rank metabox
	public function mycred_add_metabox() {
	
	
	// ranks meta box add
	if(get_option( 'mycred_wooplus_show_ranks' ) == 'yes'){	
		add_meta_box(	
					'mycred_ranks_coupons',
					'Reward Coupon', 
					array( $this, 'mycred_ranks_meta_callback' ),
					'mycred_rank',
					'normal', 
					'low'
		);
	}

	// badges meta box add
	if(get_option( 'mycred_wooplus_show_badges' ) == 'yes'){
		add_meta_box(	
					'mycred_badge_coupons',
					'Reward Coupon', 
					array( $this, 'mycred_badges_meta_callback' ),
					'mycred_badge',
					'side', 
					'low'
		);
		
	}	
	
	
	
	
}

	public function mycred_badges_meta_callback( $badge ) {
		
	$discount_type  = get_post_meta( $badge->ID, 'mycred_discount_type', true ); 
	
	?><div width="100%" id="mycred_badge_level_meta">
	<span class="description">You can use these settings to reward users on achieving this badge.</span>
	<?php   

	$mycred_badge_level_meta = get_post_meta( get_the_ID(), 'woo_discount', true );

	if(is_array($mycred_badge_level_meta)){
		
	$sno=1; $remove=0;
	
	foreach($mycred_badge_level_meta as $key =>  $badge_level){
		
	$amount 			    	= $badge_level['discount_amount'];
	$discount_type  	    	= $badge_level['discount_type'];
	$mycred_coupon_code_badge   = $badge_level['mycred_coupon_code_badge'];
	?>
	
<table class="mycred_badge_level_meta" width="100%" id="mycred_badge_level_meta<?php echo $remove;?>">
	<tbody>
	<tr id="discount_type_level0">
		<th style="width: 25%"><?php echo __( 'level', 'mycredpartwoo' ); echo $sno; ?></th>
	</tr>
	<tr id="discount_type<?php echo $key;?>">
		<td style="width: 25%"><?php echo __( 'Discount Type', 'mycredpartwoo' ); ?></td>
	</tr>
	<tr>
		<td>
			<select style="width:245px;" name="woo_discount[<?php echo $remove;?>][discount_type]">
				<option value='fixed' <?php if($discount_type=='fixed'){echo "selected";}?>>
					<?php echo __( 'Fixed Discount', 'mycredpartwoo' ); ?>
				</option>
				<option value='percent' <?php if($discount_type=='percent'){echo "selected";}?>>
					<?php echo __( 'Percentage  Discount', 'mycredpartwoo' ); ?>
				</option>
			</select>
		</td>
	</tr>
	<tr id="discount_amount<?php echo $key;?>">
		<td><?php echo __( 'Amount', 'mycredpartwoo' ); ?></td>
	</tr>
	<tr>
		<td>
			<input type="number" style="width:245px;" 
			name="woo_discount[<?php echo $remove;?>][discount_amount]"  value="<?php echo $amount;?>">
		</td>
	</tr>
	<tr id="mycred_discount_coupon_code<?php echo $key;?>">
		<td><?php echo __( 'Coupon Code', 'mycredpartwoo' ); ?></td>
	</tr>
	<tr>
		<td>
			<input type="text" style="width:245px;" name="woo_discount[<?php echo $remove;?>][mycred_coupon_code_badge]" value="<?php echo $mycred_coupon_code_badge; ?>">
		</td>
	</tr>
	</tbody>
</table>
	
	<?php $sno++; $remove++;}}else{ ?>
		
<table class="mycred_badge_level_meta" width="100%" id="mycred_badge_level_meta0">
	<tbody>
	<tr id="discount_type_level0">
		<th style="width: 25%"><?php echo __( 'level 1', 'mycredpartwoo' ); ?></th>
	</tr>
	<tr id="discount_type0">
		<td style="width: 25%"><?php echo __( 'Discount Type', 'mycredpartwoo' ); ?></td>
	</tr>
	<tr>
		<td>
			<select style="width:245px;" name="woo_discount[0][discount_type]">
				<option value='fixed'>
					<?php echo __( 'Fixed Discount', 'mycredpartwoo' ); ?>
				</option>
				<option value='percent'>
					<?php echo __( 'Percentage  Discount', 'mycredpartwoo' ); ?>
				</option>
			</select>
		</td>
	</tr>
	<tr id="discount_amount0">
		<td><?php echo __( 'Amount', 'mycredpartwoo' ); ?></td>
	</tr>
	<tr>
		<td>
			<input type="text" style="width:245px;" name="woo_discount[0][discount_amount]" value="">
		</td>
	</tr>
	<tr id="mycred_discount_coupon_code">
		<td><?php echo __( 'Coupon Code', 'mycredpartwoo' ); ?></td>
	</tr>
	<tr>
		<td>
			<input type="text" style="width:245px;" name="woo_discount[0][mycred_coupon_code_badge]" value="">
		</td>
	</tr>
	</tbody>
</table>
	<?php } ?>
	</div>
	<span class="description">NOTE: Keep amount 0 or empty in order to disable coupons for this badge</span>
	<?php
	}

	public function mycred_ranks_meta_callback( $rank ) { ?>
		
	
<table width="100%">
	
	<tr>
		<td colspan="10" >
			<p style="font-weight: 600;">
				
			<?php echo __( 'You can use these settings to reward users on achieving this rank.', 'mycredpartwoo' ); ?>
			</p>
		</td>
	</tr>
	
	<tr>
		<td style="width: 25%"><?php echo __( 'Discount Type', 'mycredpartwoo' ); ?></td>
		<td>
		<?php $discount_type  = get_post_meta( $rank->ID, 'mycred_discount_type', true ); ?>
		<select style="width:425px;" name="discount[mycred_discount_type]" >
			<option value='fixed' <?php if($discount_type=='fixed'){echo "selected";}?>>
				<?php echo __( 'Fixed Discount', 'mycredpartwoo' ); ?>
			</option>
			<option value='percent' <?php if($discount_type=='percent'){echo "selected";}?>>
				<?php echo __( 'Percentage  Discount', 'mycredpartwoo' ); ?>
			</option>
		</select>
		</td>
	</tr>
	<tr>
		<td><?php echo __( 'Coupon code', 'mycredpartwoo' ); ?></td>
		<td><input type="text" style="width:425px;" name="discount[mycred_coupon_code_rank]" value="<?php echo esc_html( get_post_meta( $rank->ID, 'mycred_coupon_code_rank', true ) );?>" />
		</td>
	</tr>
	<tr>
		<td><?php echo __( 'Amount', 'mycredpartwoo' ); ?></td>
		<td><input type="number" style="width:425px;" name="discount[mycred_discount_bagde_rank]" value="<?php echo esc_html( get_post_meta( $rank->ID, 'mycred_discount_bagde_rank', true ) );?>" />
		</td>
	</tr>
	
	
	<tr>
		<td colspan="10">
			<p style="font-size: 11px;">
			<?php echo __( 'NOTE: Keep amount 0 or empty in order to disable coupons for this rank.', 'mycredpartwoo' ); ?>
			</p>
		</td>
	</tr>
	
	
</table>
<?php }

	public function mycred_add_badge_rank_fields( $save_badge_rank_id, $post ) {
	
		if ( $post->post_type == 'mycred_rank' ) {
			if ( isset( $_POST['discount'] ) ) {
				foreach( $_POST['discount'] as $key => $value ){
					update_post_meta( $save_badge_rank_id, $key, $value );
				}
			}
		}
	 
		if ( $post->post_type == 'mycred_badge' ) {
			if ( isset( $_POST['woo_discount'] ) ) {
				  
					update_post_meta( $save_badge_rank_id,'woo_discount', $_POST['woo_discount'] );
				 
			}
		}

	}

 
 
	public function run_badges_and_ranks_copouns() {
   
    if (!is_user_logged_in()) return;
    if ( is_admin() ) return;
	 
	
	//User id and user email. 
	$user_id	= get_current_user_id();
	$user_email = get_userdata($user_id)->user_email;
	
	
	if ( function_exists( 'mycred_get_users_badges' ) ){
		
		//All badge ids	
		$badge_ids  = array_keys(mycred_get_users_badges( $user_id ));
	
	}else{
		
		$badge_ids = array();
		
	}
	
	if ( function_exists( 'mycred_get_users_badges' ) ){
		
		//Rank ids  
		$rank_ids   = mycred_get_users_rank_id( $user_id );
	
	}else {
		$rank_ids   = array();
	}
	//Rank title
	$rank_title = get_the_title( $rank_ids );
	 
	if(get_option( 'mycred_wooplus_show_ranks' ) == 'yes'){
		
	if(!empty($rank_ids)){
		
		$args = array();
		$discount_type 	     = get_post_meta( $rank_ids , 'mycred_discount_type', true );
		$amount				 = get_post_meta( $rank_ids , 'mycred_discount_bagde_rank', true );
		$coupon_code	 	 = get_post_meta( $rank_ids  , 'mycred_coupon_code_rank', true );
		
		if(!empty($discount_type) &&  !empty($amount) &&  !empty($coupon_code) && $amount != 0){
			
		
			
			
		if($discount_type =='percent'){

		$d_amount =  $amount."%";

		}else{

		$currency = get_woocommerce_currency_symbol();
		$d_amount = $currency.$amount;

		}
		
		 
	 	$description = 'You have Won a coupon for '.$d_amount.' off for achieving '. $rank_title .' Rank';
			
			
			$mycred_ranks_coupons = get_user_meta( get_current_user_id(),'mycred_ranks_coupons', true );
			
			// General info check validation copoun create.
			$create_coupons_run  = true;
			 
			$args = array(
				'ranks_or_badges_ids' => $rank_ids,
				'coupon_code'		  => $coupon_code,
				'amount'			  => $amount,
				'discount_type'		  => $discount_type,
				'customer_email'	  => $user_email,
				'description'		  => $description,
				'type'				  => 'rank',
				'level'				  =>  null,
				'individual_use'	  => 'no',
				'product_ids'		  => '',
				'exclude_product_ids' => '',
				'usage_limit' 		  => '1',
				'expiry_date' 		  => '',
				'apply_before_tax' 	  => 'yes',
				'free_shipping' 	  => 'no'
			); 
			 
			if($mycred_ranks_coupons){
				
				foreach($mycred_ranks_coupons as $mycred_ranks_coupons_ids){
					
					if (  in_array( $rank_ids , $mycred_ranks_coupons_ids ) ) {
						
					$create_coupons_run = false;	
					
					}
				}
				
					if($create_coupons_run===true){
									 
						// create coupons
						$coupon_id = $this->create_coupons_badges_ranks($args);	
			
					}
				
			}else{ 
		
			// create coupons
			$coupon_id = $this->create_coupons_badges_ranks($args);	
			
			}
		 
  
		} 
		
	}
	
	}
				
	if(get_option( 'mycred_wooplus_show_badges' ) == 'yes'){
		
	if(count($badge_ids) != 0){
		
	$args = array();	
	
	foreach($badge_ids as $badge_id){
	
	// get badge discount array
	$mycred_discount_bagde		= get_post_meta( $badge_id , 'woo_discount', true );
	
	if(!$mycred_discount_bagde) return;
	
	// get user badge level 
	$user_badge_level_reached   = get_user_meta(get_current_user_id(), 'mycred_badge'.$badge_id , true );
	
	// badge object
	$badge    					= mycred_get_badge( $badge_id );
 
	// badge level number
	$level_number = $user_badge_level_reached;
	$level_number = $level_number+1;
	// sort user badge level discount
	
	$mycred_discount_bagde = array_slice($mycred_discount_bagde,$user_badge_level_reached, 1, true);

	if(!empty($mycred_discount_bagde)){
		
		// General info check validation copoun create.
		$create_coupons_run  = true;
			
		foreach ($mycred_discount_bagde as $bagde_value)
		{
			$coupon_code		 = $bagde_value["mycred_coupon_code_badge"];
			$amount				 = $bagde_value["discount_amount"];
			$discount_type		 = $bagde_value["discount_type"];
		}
		
		if( empty( $amount ) or empty( $coupon_code ) && $amount == 0){ continue ; } 
		 
		
		
		if($discount_type =='percent'){
		
		 $d_amount =  $amount."%";
		
		}else{
			
		$currency = get_woocommerce_currency_symbol();
		$d_amount = $currency.$amount;
		
		}
		
		 
		$description = 'You have Won a coupon for '.$d_amount.' off for achieving level '. $level_number .' '. $badge->title .' Badge';
		
		$mycred_badges_coupons = get_user_meta(get_current_user_id(),'mycred_badges_coupons', true );
	
		$args = array(
			'ranks_or_badges_ids' => $badge_id,
			'coupon_code'		  => $coupon_code,
			'amount'			  => $amount,
			'discount_type'		  => $discount_type,
			'customer_email'	  => $user_email,
			'description'		  => $description,
			'type'				  => 'badge',
			'level'				  => $user_badge_level_reached,
			'individual_use'	  => 'no',
			'product_ids'		  => '',
			'exclude_product_ids' => '',
			'usage_limit' 		  => '1',
			'expiry_date' 		  => '',
			'apply_before_tax' 	  => 'yes',
			'free_shipping' 	  => 'no'
		);
		
  
	if($mycred_badges_coupons){
		 
			foreach($mycred_badges_coupons as $mycred_badges_coupons_ids){
				
				if ($badge_id == $mycred_badges_coupons_ids['badge_id']  &&  
				$user_badge_level_reached == $mycred_badges_coupons_ids['level'] ){
		
				$create_coupons_run = false;

				}
			} 
			if($create_coupons_run===true){

			// create_coupons			
			$coupon_id = $this->create_coupons_badges_ranks($args);	
		 
			}
		}else{
			
			// create_coupons 
			$coupon_id = $this->create_coupons_badges_ranks($args);	
		
		}
	}
		
		
		
	}
		 
    }
    }
	
}
 	
 
	public function create_coupons_badges_ranks($args){	
	
	$args = apply_filters('mycred_wooplus_modify_coupon',$args);
	
	$ranks_or_badges_ids = $args['ranks_or_badges_ids'];
	$coupon_code 		 = $args['coupon_code'];
	$amount 			 = $args['amount'];
	$discount_type 		 = $args['discount_type'];
	$customer_email 	 = $args['customer_email'];
	$description 		 = $args['description'];
	$type 			 	 = $args['type'];
	$level 			 	 = $args['level'];
	$individual_use 	 = $args['individual_use'];
	$product_ids 		 = $args['product_ids'];
	$exclude_product_ids = $args['exclude_product_ids'];
	$usage_limit         = $args['usage_limit'];
	$expiry_date		 = $args['expiry_date'];
	$apply_before_tax 	 = $args['apply_before_tax'];
	$free_shipping 		 = $args['free_shipping'];
	
	
	 
	
	global $woocommerce;
  

	$coupon = array(
		'post_title'   => $coupon_code."_".get_current_user_id(),
		'post_content' => '',
		'post_excerpt' => $description,  
		'post_status'  => 'publish',
		'post_author'  => 1,
		'post_type'    => 'shop_coupon'
	);    

	$new_coupon_id = wp_insert_post( $coupon );

	// Add meta coupons
	update_post_meta( $new_coupon_id, 'discount_type',       $discount_type );
	update_post_meta( $new_coupon_id, 'coupon_amount',       $amount );
	update_post_meta( $new_coupon_id, 'individual_use',      $individual_use );
	update_post_meta( $new_coupon_id, 'product_ids',         $product_ids );
	update_post_meta( $new_coupon_id, 'exclude_product_ids', $exclude_product_ids );
	update_post_meta( $new_coupon_id, 'usage_limit',         $usage_limit );
	update_post_meta( $new_coupon_id, 'date_expires', 	     $expiry_date );
	update_post_meta( $new_coupon_id, 'apply_before_tax',    $apply_before_tax );
	update_post_meta( $new_coupon_id, 'free_shipping',       $free_shipping );
	update_post_meta( $new_coupon_id, 'customer_email',      $customer_email );	
	update_post_meta( $new_coupon_id, 'reference_type',      $type );	
	update_post_meta( $new_coupon_id, 'user_id',     		 get_current_user_id() );	
 
	if( $type == 'rank' ){ 
	 
	$mycred_ranks_coupons = get_user_meta(get_current_user_id(),'mycred_ranks_coupons', true );

	if(get_user_meta(get_current_user_id(),'mycred_ranks_coupons', true ) == ''){
		
		$mycred_ranks_coupons = array(); 
	}

	array_push( $mycred_ranks_coupons , 
									array(
											'rank_id'	 => $ranks_or_badges_ids,
											'coupon_id'  => $new_coupon_id
										  ) 
			   );
			 
	update_user_meta( get_current_user_id() , 'mycred_ranks_coupons', $mycred_ranks_coupons);
		
	$response_coupon_code = get_the_title( $new_coupon_id );
	
	 
	}	


	if($type == 'badge'){ 

		$mycred_badges_coupons = get_user_meta( get_current_user_id() ,'mycred_badges_coupons', true );

		if( !$mycred_badges_coupons ){
			 $mycred_badges_coupons = array(); 
		}

		 array_push( $mycred_badges_coupons , 
										array(
											  'badge_id'  => $ranks_or_badges_ids,
											  'coupon_id' => $new_coupon_id,
											  'level'     => $level
											  ) 
				    );
				 
		update_user_meta(get_current_user_id(), 'mycred_badges_coupons', $mycred_badges_coupons);
		 

	}
		wc_add_notice( $description, 'notice' );

	
/// return coupon id
return $new_coupon_id; 


	
	
}
  
  
  
	public function my_account_copouns_url_rewrite() {
		
		add_rewrite_endpoint( 'my-coupons', EP_ROOT | EP_PAGES );
	}
  
	public function copouns_query_vars( $vars ) {
	
		$vars[] = 'my-coupons';
		
		return $vars;
	}
  
  
  
	public function add_copouns_tab( $items ) {
		 
		$new_items = array();
		$new_items['my-coupons'] = __( 'My coupons', 'mycredpartwoo' );

		// Add the new item after `edit-account`.
		return $this->my_custom_insert_after_helper( $items, $new_items, 'edit-account' );
	}
  
  
	public function my_custom_insert_after_helper( $items, $new_items, $after ) {
		// Search for the item position and +1 since is after the selected item key.
		$position = array_search( $after, array_keys( $items ) ) + 1;

		// Insert the new item.
		$array = array_slice( $items, 0, $position, true );
		$array += $new_items;
		$array += array_slice( $items, $position, count( $items ) - $position, true );

		return $array;
	}
  
  
  
	public function copouns_shortcode_call() {

		 
		//echo '<h3>'. __( 'My Coupons', 'mycredpartwoo' ) . '</h3>';
		 
		echo do_shortcode( '[mycred_badges_ranks_coupons type=""]' );
		
	}

	public function mycred_badges_ranks_coupons_lists( $atts, $content = "" ) {
		$coupons_settings = shortcode_atts( array(
			'type' => 'all'
		), $atts );
		 
		return $this->mycred_badges_ranks_coupons_data($coupons_settings);
	}

	public function copouns_status($copoun_id){
		
		
		$usage_limit = get_post_meta( $copoun_id , 'usage_limit', true );
		$usage_count = get_post_meta( $copoun_id , 'usage_count', true );
	 
	 
		if(get_post_meta( $copoun_id, 'date_expires', true )){
			
			$date_expires =  date('d/m/Y', get_post_meta( $copoun_id, 'date_expires', true ));
			
		}else{
			
			$date_expires= date('d/m/Y', strtotime("+1 day"));
			
			}
		
			if($usage_count  <   $usage_limit  && $date_expires > date("d/m/Y")){

				echo __( 'Available', 'mycredpartwoo' );	

			}else{

					if($date_expires > date("d/m/Y") ){
					
						echo __( 'Used', 'mycredpartwoo' );		 	
					
					}else{
					
						echo __( 'Expired', 'mycredpartwoo' );	 
					
					}

			}
	}


	public function mycred_badges_ranks_coupons_data($coupons_settings) {
		
	$coupons_settings = apply_filters('wooplus_badges_ranks_coupons_type',$coupons_settings);
	 
	if(!isset($coupons_settings['type'])) 
	{ $coupons_settings = array('type' => 'all'); }



	switch ($coupons_settings['type']) {
		case "badge":
		   
			$meta_query_args = array(
				array(
					'key'     => 'reference_type',
					'value'   => 'badge',
					'compare' => '='
				),
				array(
					'key'     => 'user_id',
					'value'   => get_current_user_id(),
					'compare' => '='
				)
			);
		   
			break;
		case "rank":
		 
		 $meta_query_args = array(
				array(
					'key'     => 'reference_type',
					'value'   => 'rank',
					'compare' => '='
				),
				array(
					'key'     => 'user_id',
					'value'   => get_current_user_id(),
					'compare' => '='
				)
			);
			
			break;
		default:
			$meta_query_args = array(
				array(
					'key'     => 'user_id',
					'value'   => get_current_user_id(),
					'compare' => '='
				)
			);
	}
		
		
		$args = array(
			'posts_per_page'   => -1,
			'orderby'          => 'ID',
			'order'            => 'DESC',
			'post_type'        => 'shop_coupon',
			'post_status'      => 'publish',
			'meta_query'   	   => array($meta_query_args)
		);


	$coupons = get_posts( $args );
	 
		ob_start();
		 ?>
		 
		<div class="mycred_coupons_badge_rank_container">
		
		<table class="mycred_coupons_badge_rank">
		
			<thead>
			<tr> 
			<th class=""><span><?php echo __( 'Sno', 'mycredpartwoo' ); ?></span></th>
			<th class=""><span><?php echo __( 'Coupon Code', 'mycredpartwoo' ); ?></span></th>
			<th class=""><span><?php echo __( 'Amount', 'mycredpartwoo' ); ?></span></th>
			<th class="coupon_description" ><span><?php echo __( 'Description', 'mycredpartwoo' ); ?></span></th>
			<th class=""><span><?php echo __( 'Expiry date', 'mycredpartwoo' ); ?></span></th>
			<th class=""><span><?php echo __( 'Status', 'mycredpartwoo' ); ?></span></th>
			</tr>
			</thead>

			<tbody>
	<?php   if(count($coupons) >= 1){ $sno=1;

			foreach ( $coupons as $coupon ) { ?>
					<tr class="<?php echo $this->copouns_status($coupon->ID); ?>">
					
						<td class=""><?php echo $sno;?></td>
						
						<td class="coupon_code">
							<span class="copoun_code_style">
								<?php echo $coupon->post_title ; ?>
							</span>
						</td>
						
						<td class="">
							<?php 
							 
							if(get_post_meta( $coupon->ID , 'discount_type', true ) =='percent'){
							
							echo  get_post_meta( $coupon->ID , 'coupon_amount', true )."% Off";
							
							}else{
								
							$currency = get_woocommerce_currency_symbol();
							echo $currency.get_post_meta( $coupon->ID , 'coupon_amount', true )." Off";
							
							}
							 
							?> 
						</td>
						
						<td class="">
							<?php echo $coupon->post_excerpt; ?>
						</td>
						
						<td class="">
						<?php
						
						if(get_post_meta( $coupon->ID, 'date_expires', true ) != ''){

						$date_expires =  date('d/m/Y', get_post_meta( $coupon->ID, 'date_expires', true ));    
							echo  $date_expires;

						}else{
								echo "-"; 
							} 
						?>
						</td>
						
						<td class="">
							<?php echo $this->copouns_status($coupon->ID); ?>
						</td>
						
					</tr>
			<?php $sno++; } } else{ ?>
			
			<tr class="">
				<td class="no_copouns_found" colspan="10">
					<?php echo __( 'No copouns found.', 'mycredpartwoo' ); ?>
				</td>
			</tr>
			
			<?php } ?>
			</tbody>
		</table>
		</div>
		<?php return ob_get_clean();
	}


}

$MyCred_woo_badge_rank_copouns = new MyCred_woo_badge_rank_copouns();
endif;