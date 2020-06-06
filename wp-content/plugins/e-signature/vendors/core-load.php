<?php

/***
 *  Load add-ons 
 */

 function wp_esignature_loaded(){
    Esig_Addons::init();
   
     // E-signature loaded action runs 
     do_action( 'wp_esignature_loaded' );
 }
 
 add_action( 'plugins_loaded', 'wp_esignature_loaded', 100 );
 

/**
*  core extra funcitons 
*/

function esig_total_addons_installed()
{
			$array_Plugins = get_plugins();
			$i = 0 ; 	
			if(!empty($array_Plugins))
			{
				foreach($array_Plugins as $plugin_file => $plugin_data) 
				 {
				   if(is_plugin_active($plugin_file)) 
				   {
				        $plugin_name=$plugin_data['Name'] ; 
						
						// if($plugin_name!="WP E-Signature")
						// {  
						   if(preg_match("/WP E-Signature/",$plugin_name))
						   {  
						      if($plugin_name!="WP E-Signature")
						 	  { 
						      		$i++ ; 
							  }
						   }
					}
				}
			}
			
			return $i ; 			 
}

// esignature update addons notificaitons 

function esig_update_count_bubble ($update_data)
{
    
    if (!Esig_Addons::is_updates_available()) {
        return $update_data;
    }
    
    $plugin_list=get_transient('esign-auto-downloads');	
    
        $count = 0 ; 	
	    if($plugin_list)
		{
			
	    $count = count($plugin_list);
        }
        else
        {
            return $update_data ; 
        }
        
        $c = $update_data['counts']['plugins'];
        
    $esig_count = $c + $count ; 
    
    $update_data['counts']['plugins'] = $esig_count ; 
    
    return $update_data ; 
    
}
add_filter('wp_get_update_data','esig_update_count_bubble',10,2) ;

/* access control feature has been loaded here */ 

function esig_js_localize_text(){
   echo "<script type='text/javascript'>";
        echo '/* <![CDATA[ */
				var Esign_localize = {"signing":"'. esc_html__( 'Signing....', 'esig' ) .'","add_signature":"'. esc_html__('+ Add signature','esig') .'","iam":"'. esc_html__('I am','esig') .'","and":"'. esc_html__('and','esig') .'"};
	/* ]]> */
			</script>';
	
 }

add_action("esig_head","esig_js_localize_text");

function esignature_error_dialog(){
    
        if(!WP_E_Notice::get_error_dialog()){
            return ; 
        }
        
        include ESIGN_PLUGIN_PATH . "/views/errors/" . WP_E_Notice::get_error_dialog() . ".php";
        
        WP_E_Notice::remove_error_dialog();
    
}

add_action( 'esig_notices', 'esignature_error_dialog' );


 
 





