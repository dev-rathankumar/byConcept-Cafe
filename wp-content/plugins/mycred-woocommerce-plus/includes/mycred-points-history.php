<?php

if ( ! class_exists( 'MyCred_woo_points_history' ) ) :
class MyCred_woo_points_history {
		
		public function __construct() {
			
			if(get_option( 'wooplus_points_history' ) != 'yes') return;
			
			add_action('init',							  array( $this , 'point_type_history_url_rewrite') , 0);	
			add_filter('query_vars', 					  array( $this , 'point_type_history_query_vars'), 0 ); 
			add_filter('woocommerce_account_menu_items',  array( $this , 'add_point_type_history_tab') );
			add_filter('get_pagenum_link',				  array( $this , 'get_pagenum_link'),10,2);
			add_action('wp_head', 						  array( $this ,'my_custom_styles'), 100);
			add_filter('the_title',						  array( $this , 'copouns_tab_endpoint_title'), 10, 2 );
		}
		
		public function copouns_tab_endpoint_title( $title, $id ) {
		
		global $wp_query;
		$mycred_get_types = mycred_get_types();
		
			if ( isset( $wp_query->query_vars['my-coupons'] ) && in_the_loop() ) {
				
				return 'My Coupons';
				
			}
			
			foreach($mycred_get_types as $mycred_get_type => $mycred_get_type_name)
			{
					if ( isset( $wp_query->query_vars[$mycred_get_type] ) && in_the_loop() ) {

					return $mycred_get_type_name ." History";

					}
			}

		return $title;
		}
		
		public function point_type_history_url_rewrite() {
		
		$mycred_get_types = mycred_get_types();
		
		foreach($mycred_get_types as $mycred_get_type => $mycred_get_type_name)
		{
			add_action('woocommerce_account_'.$mycred_get_type.'_endpoint', 
			
			function() use ($mycred_get_type) {
			
				echo "<div class='mycred_point_history'>";
				echo $this->mycred_history_data($mycred_get_type);
				echo "</div>";

			}, 100);
			
			add_rewrite_endpoint( $mycred_get_type, EP_ROOT | EP_PAGES  );	
		}
		
		 	
	}

	public function my_custom_styles()
	{	
		$mycred_get_types = mycred_get_types();
		 foreach($mycred_get_types as $mycred_get_type => $mycred_get_type_name)
			{
				?><style>
				.woocommerce-MyAccount-navigation .woocommerce-MyAccount-navigation-link--<?php echo $mycred_get_type;?> a:before{
				content: "\f005";
				}</style>
				<?php 
			}
	}
	
	public function mycred_history_data($mycred_get_type) {
		
		extract( shortcode_atts( array(
			'user_id'    =>  get_current_user_id(),
			'number'     => 10,
			'time'       => '',
			'ref'        => '',
			'order'      => '',
			'show_user'  => 0,
			'show_nav'   => 1,
			'login'      => '',
			'type'       => $mycred_get_type,
			'pagination' => 10,
			'inlinenav'  => 0
		), $atts, MYCRED_SLUG . '_history' ) );

		// If we are not logged in
		if ( ! is_user_logged_in() && $login != '' )
			return $login . $content;

		if ( ! MYCRED_ENABLE_LOGGING ) return '';

		$user_id = mycred_get_user_id( $user_id );

		if ( ! mycred_point_type_exists( $type ) )
			$type = MYCRED_DEFAULT_TYPE_KEY;

		$args    = array( 'ctype' => $type );
		
		if ( $user_id != 0 && $user_id != '' )
			$args['user_id'] = absint( $user_id );

		if ( absint( $number ) > 0 )
			$args['number'] = absint( $number );

		if ( $time != '' )
			$args['time'] = $time;

		if ( $ref != '' )
			$args['ref'] = $ref;

		if ( $order != '' )
			$args['order'] = $order;

		$log = new myCRED_Query_Log( apply_filters( 'mycred_front_history_args', $args, $atts ) );

		ob_start();

		if ( $inlinenav ) echo '<style type="text/css">.mycred-history-wrapper ul li { list-style-type: none; display: inline; padding: 0 6px; }</style>';

		do_action( 'mycred_front_history', $user_id );


		$big = 999999999; // need an unlikely integer
		$current_page = $this->get_url_segment(2);
	 
		if(get_option('permalink_structure')=='/archives/%post_id%'){
			 
		$current_page = explode("?", $this->get_url_segment(1));
		$current_page =  $current_page[0];
		}
		if (!is_numeric($current_page)) {
		   $current_page = 1;
		} 
		
	?><div class="mycred-history-wrapper">
		<form class="form-inline" role="form" method="get" action="">
			<div class="mycred-point-history-pagination">
			<?php 

			if (get_option('permalink_structure') == '' ) {  

				if ( $show_nav == 1 ) $log->front_navigation( 'top', $pagination ); 

			}else { 
					if ( $show_nav == 1 ) 
					{
						echo paginate_links( array(
						'base' 		=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
						'format' 	=> '',
						'show_all'  => true, 
						'prev_next' => true,
						'prev_text' => __('« Previous', 'mycredpartwoo'),
						'next_text' => __('Next »', 'mycredpartwoo'),
						'current'	=> $current_page,
						'total' 	=> $log->max_num_pages
						) );
					} 
			}?>
			</div>
			<?php $log->display(); ?>
			<div class="mycred-point-history-pagination">
			 <?php 

			if (get_option('permalink_structure') == '' ) {  

				if ( $show_nav == 1 ) $log->front_navigation( 'bottom', $pagination ); 

			}else { 
					if ( $show_nav == 1 ) 
					{
						echo paginate_links( array(
						'base' 		=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
						'format' 	=> '',
						'show_all'  => true, 
						'prev_next' => true,
						'prev_text' => __('« Previous', 'mycredpartwoo'),
						'next_text' => __('Next »', 'mycredpartwoo'),
						'current'	=> $current_page,
						'total' 	=> $log->max_num_pages
						) );
					} 
			}?>
			</div>
		</form>
	</div> <?php
	
		$content = ob_get_contents();
		ob_end_clean();
		$log->reset_query();
		
		return $content;
	}
	
	public function get_url_segment($segment) {
		
		$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$end = explode('/', $url);
		
		$total = count( $end );
		
		return $end[$total - $segment];
	}
	
	public function get_pagenum_link($result,$pagenum) {
		
		
	 	if(get_the_ID() == get_option('woocommerce_myaccount_page_id')) {
		 
		$segment = $this->get_url_segment(2);
		
		if( empty( $_SERVER['QUERY_STRING'] ) ){
			
			if(get_option('permalink_structure')=='/%postname%/'){
				
			return get_permalink( get_option('woocommerce_myaccount_page_id') ) . $pagenum .'?'. $segment;
			
			}elseif(get_option('permalink_structure')=='/archives/%post_id%'){
				
			 $segment = $this->get_url_segment(1);
			
			return get_permalink( get_option('woocommerce_myaccount_page_id') ) .'/'. $pagenum .'/'.'?'. $segment;
			
			}else{
				 
				return get_permalink( get_option('woocommerce_myaccount_page_id') ) .'/'. $pagenum .'/'.'?'. $segment;
			}
			
		}
		else{
			
			if(get_option('permalink_structure')=='/%postname%/'){
				
				return get_permalink( get_option('woocommerce_myaccount_page_id') ) . $pagenum .'?'. $_SERVER['QUERY_STRING'];
			
			}elseif(get_option('permalink_structure') == "/%year%/%monthnum%/%postname%/"){
				
				return get_permalink( get_option('woocommerce_myaccount_page_id') ) .'/'. $pagenum .'/'.'?'. $_SERVER['QUERY_STRING'];
			
			}else{
 
				 
			return get_permalink( get_option('woocommerce_myaccount_page_id') ) .'/'. $pagenum .'/'.'?'. $_SERVER['QUERY_STRING']; 
 
				 
			}

		
		}
		}else{ return $result; }
	}
	
	public function point_type_history_query_vars( $vars ) {
	
		$mycred_get_types = mycred_get_types();
		
		foreach($mycred_get_types as $mycred_get_type => $mycred_get_type_name)
		{
			$vars[] = $mycred_get_type ;
		}
		
		
		return $vars;
	}
  
	public function add_point_type_history_tab( $items ) {
		 
		$new_items = array();
		
		$mycred_get_types = mycred_get_types();
		foreach($mycred_get_types as $mycred_get_type => $mycred_get_type_name)
		{
			 
			$new_items[$mycred_get_type] = __( $mycred_get_type_name, 'mycredpartwoo' );
 
		}
		
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
  
  
    public function mycred_custom_get_types_name($type)
	{
		$mycred_get_types = mycred_get_types();
		return	$mycred_get_types[$type];	
	}
	 
}
 
$MyCred_woo_points_history = new MyCred_woo_points_history();

 
endif;