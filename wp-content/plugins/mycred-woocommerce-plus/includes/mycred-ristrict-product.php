<?php
if ( ! class_exists( 'MyCred_woo_ristrict_product' ) ) :
class MyCred_woo_ristrict_product {
  

    public function __construct() {
		
	add_action( 'add_meta_boxes',					array( $this , 'mycred_woo_badge_rank_custom'));	
	add_action( 'pre_get_posts',						array( $this ,'remove_products'));
	add_action( 'woocommerce_process_product_meta', array( $this ,'mycred_woo_product_meta'), 10 ); 
	
	}
 
	public function mycred_woo_product_meta() {
	
		update_post_meta( 
		
		$_POST['post_ID'],'mycred_woo_badges_ranks',$_POST['mycred_woo_badges_ranks']
		
		); 
	}

	public	function mycred_woo_badge_rank_custom() {
		
		if(get_option( 'wooplus_ristrict_product' ) == 'yes'){
			
			add_meta_box( 'mycred_woo_badge_rank_custom', 
				esc_html__( 'Restrict Product', 'myCred' ),
				array( $this ,'mycred_woo_badge_rank_custom_callback'), 
				'product',
				'side', 
				'high' 
			);
			
		}

	}	
	
	public function mycred_woo_badge_rank_custom_callback( $meta_id ) {
		
		?><div class="mycred_categorydiv"><?php

		$mycred_woo_badges_ranks = get_post_meta( get_the_ID(), 'mycred_woo_badges_ranks', true );
		 
		 
		if( isset($mycred_woo_badges_ranks['selected']) && 
			isset($mycred_woo_badges_ranks[$mycred_woo_badges_ranks['selected']])){

				$array_ids = $mycred_woo_badges_ranks[$mycred_woo_badges_ranks['selected']];	

		}else{

				$array_ids = array();
		}
	 
	 
		if(  isset( $mycred_woo_badges_ranks['selected'] ) ){
			
				$mycred_selected =	$mycred_woo_badges_ranks['selected'];
			
		}else{
			
				$mycred_selected ='';
		}
  ?><div id="mycred_woo_badges_ranks_option">
			 
		<label class="selectit">None 
			<input type="radio" name="mycred_woo_badges_ranks[selected]" placeholder="Search" id="mycred_woo_select_none" class="mycred_woo_select_radio" value="none"
			<?php if($mycred_selected == 'none'){echo"checked";} ?> />
		</label> 

		<label class="selectit">Badges 
			<input type="radio" name="mycred_woo_badges_ranks[selected]" placeholder="Search" id="mycred_woo_select_badges" class="mycred_woo_select_radio" value="badge"
			<?php if($mycred_selected == 'badge'){echo"checked";} ?> />
		</label> 

		<label class="selectit">Ranks 
			<input type="radio" name="mycred_woo_badges_ranks[selected]" placeholder="Search" id="mycred_woo_select_ranks" class="mycred_woo_select_radio" value="rank" 
			<?php if($mycred_selected == 'rank'){echo"checked";} ?>  />
		</label> 
	 
	</div>
	 
	<!-----Badges panel------->
	  
	<div class="mycred_badges_searchbox">
	 
		<div class="mycred_woo_search_block">
	 
			<input type="text" placeholder="Search Badges"  id="mycred_badges_searchbox_field" class="title_field" value="" style="width: 100%;margin-bottom: 10px;"/><hr>
	 
		</div>
	 
	<div class="tabs-panel">
			 
		<ul class="categorychecklist form-no-clear">
						
		<?php 

		$args = array(
			'post_type' => 'mycred_badge',
			'post_status' => 'publish',
			'posts_per_page' => -1
		);

		$lastposts = get_posts( $args );

		if ( $lastposts ) {
			
			foreach ( $lastposts as $post ) :
			 
			if (in_array($post->ID, $array_ids)){ 
			
				$selected='checked';
			
			}else{ 
			
				$selected='';
			
			} 
			
			?>
			
			<li id="product_cat-<?php echo $post->ID;?>" class="popular-category">
			
				<label class="selectit">
					<input value="<?php echo $post->ID;?>" type="checkbox" name="mycred_woo_badges_ranks[badge][]" <?php echo $selected;?>>
						<span class="mycred_filter"><?php echo $post->post_title; ?></span>
				</label>
				
			</li>
			
			<?php 
			
			endforeach; 
			wp_reset_postdata();
		}
		
		?>
		
		</ul>
			 
	</div>
	 
	 </div>
	 
	<!-----rank panel------->
	 
	<div class="mycred_rank_searchbox">
		<div class="mycred_woo_search_block">
			<input type="text" placeholder="Search Ranks"  id="mycred_ranks_searchbox_field" class="title_field" value="" style="width: 100%;margin-bottom: 10px;"/><hr>
		</div>
		
		<div class="tabs-panel">
			 
			<ul class="categorychecklist form-no-clear">
						
	<?php 
		$args = array(
			'post_type' => 'mycred_rank',
			'post_status' => 'publish',
			'posts_per_page' => -1
		);

		$lastposts = get_posts( $args );
		
		if ( $lastposts ) {
			foreach ( $lastposts as $post ) :
			
			if (in_array($post->ID, $array_ids)){
				
				$selected='checked';
				
			}else{
				
				$selected='';
			}
			?>
			<li id="product_cat-<?php echo $post->ID;?>" class="popular-category">
				<label class="selectit">
					<input value="<?php echo $post->ID;?>" type="checkbox" name="mycred_woo_badges_ranks[rank][]" <?php echo $selected;?>>
						<span class="mycred_filter"><?php echo $post->post_title; ?></span>
				</label>
			</li>
<?php 		endforeach; 
			wp_reset_postdata();
		} ?> 
			</ul>
			 
		</div>
	 
	</div>
	<div class="description_rank">This product will be visible to users with selected ranks only.</div>
	<div class="description_badge">This product will be visible to users with selected badges only.</div>
	 </div> <!------main container------->
	 
	 <script type="text/javascript">
	 
		jQuery(document).ready(function($){
		 
<?php 	if($mycred_selected=='rank'){
			?>  jQuery('.mycred_rank_searchbox').show();
			    jQuery('#mycred_woo_badge_rank_custom .description_rank').show();
			    jQuery('#mycred_woo_badge_rank_custom .description_badge').hide(); 
				jQuery('.mycred_badges_searchbox').hide(); 
			<?php 	
		}elseif($mycred_selected=='badge')
		{
			?>  jQuery('.mycred_rank_searchbox').hide();
			jQuery('#mycred_woo_badge_rank_custom .description_rank').hide();
			jQuery('#mycred_woo_badge_rank_custom .description_badge').show();
				jQuery('.mycred_badges_searchbox').show(); 
			<?php 		
		}else{
			?>  jQuery('.mycred_rank_searchbox').hide();
				jQuery('#mycred_woo_badge_rank_custom .description_rank').hide();
				jQuery('#mycred_woo_badge_rank_custom .description_badge').hide();
				jQuery('.mycred_badges_searchbox').hide(); 
			<?php 	
		}?>	 
	 
		jQuery("#mycred_badges_searchbox_field").on("keyup", function() {
		var value = jQuery(this).val().toLowerCase();
		
		jQuery(".mycred_badges_searchbox span.mycred_filter").filter(function() {
			  
		  jQuery(this).parents( "li" ).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1);
		});
	  });
	 
	  jQuery("#mycred_ranks_searchbox_field").on("keyup", function() {
		var value = jQuery(this).val().toLowerCase();
		
		jQuery(".mycred_rank_searchbox span.mycred_filter").filter(function() {
			  
		  jQuery(this).parents( "li" ).toggle(jQuery(this).text().toLowerCase().indexOf(value) > -1);
		});
	  }); 
		 
		 
		  

	jQuery(".mycred_woo_select_radio").change(function () {
		
		  
			if (jQuery("#mycred_woo_select_badges").is(":checked")) {
				jQuery('.mycred_rank_searchbox').hide();
				jQuery('#mycred_woo_badge_rank_custom .description_badge').show(); 
				jQuery('#mycred_woo_badge_rank_custom .description_rank').hide(); 
				jQuery('.mycred_badges_searchbox').show();
			}
			 else if (jQuery("#mycred_woo_select_ranks").is(":checked")) {
				jQuery('.mycred_rank_searchbox').show();
				jQuery('.mycred_badges_searchbox').hide();
				jQuery('#mycred_woo_badge_rank_custom .description_badge').hide(); 
				jQuery('#mycred_woo_badge_rank_custom .description_rank').show(); 
			}
			else {
				jQuery('.mycred_rank_searchbox').hide();
				jQuery('.mycred_badges_searchbox').hide();
				jQuery('#mycred_woo_badge_rank_custom .description_badge').hide(); 
				jQuery('#mycred_woo_badge_rank_custom .description_rank').hide(); 
			}
			
			
			
		});      

	});

	 </script>
	<?php 

}

	public function remove_products($query) {
		
	if(get_option( 'wooplus_ristrict_product' ) == 'yes'){
		  
		if(!is_admin() && $query->is_main_query()){

		$post__in = array();
		
		if ( function_exists( 'mycred_get_users_rank_id' ) ){
			
			$rank_ids = mycred_get_users_rank_id( get_current_user_id() );
		}
		else{
			
			$rank_ids =  array();
		}
		
		if ( function_exists( 'mycred_get_users_badges' ) ){
			
			$badge_ids = array_keys(mycred_get_users_badges( get_current_user_id() ));
		}
		else{
			
			$badge_ids =  array();
		}
		
		$args = array(
				 'post_type'                => 'product',
				 'post_status'              => 'publish',
				 'posts_per_page'			 => -1
			  );
		  
		$loop = get_posts($args);
	 
		foreach ( $loop as $post ) {
		  
		$woo_b_r = get_post_meta($post->ID, 'mycred_woo_badges_ranks', true );
		
			if( isset($woo_b_r['selected']) && 	isset($woo_b_r[$woo_b_r['selected']])){
				 
				if (count($woo_b_r[$woo_b_r['selected']]) >= 1){
					 
					if( $woo_b_r['selected']  == 'badge'){
			 
						if ( !array_intersect( $badge_ids, $woo_b_r[$woo_b_r['selected']] ) ) {
						 
						$post__in[] += $post->ID;
						
						}
						
					}
					elseif( $woo_b_r['selected']  == 'rank'){
					
						if (!in_array( $rank_ids, $woo_b_r[$woo_b_r['selected']] ) ) {
						
						$post__in[] += $post->ID;
						
						}
					}
				
					
				
				
				}
			}
		}
		
		$query->set('post__not_in', $post__in);
		 
		}
	}
	}
	 
}
 
$MyCred_woo_ristrict_product = new MyCred_woo_ristrict_product();
endif;