<?php 
/*
 * Welcome Page Class
 *
 * Displays system status of users for support and bug diagnosis.
 *
 * Adapted from code in Woo Commerce (Copyright (c) 2014).
 *
 * @author 		ApproveMe
 * @category 	Admin
 * @package 	views/about/systeminfo.php
 * @version     1.0.7
*/
?>
<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

   $current= (isset($_GET['tab']))? $_GET['tab'] : 'system' ; 
   
   
   $tabs = array( 'system' => __('System Status', 'esig') , 'logs' => __('Logs', 'esig'),"tools"=>__('Tools','esig'),"import_info"=>__('Import Info','esig'));
    
    echo '<h2 class="nav-tab-wrapper esig-nav-tab-wrapper">';
    
    foreach( $tabs as $tab => $name ){
        
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        
        echo "<a class='nav-tab $class' href='admin.php?page=esign-systeminfo-about&tab=$tab'>$name</a>";

    }
    
    echo '</h2>';
          
?>

 

	
<br/>

<?php 

   if($current == "system"){
       
        include ESIGN_PLUGIN_PATH . "/views/about/system.php";
       
   }
   elseif($current == "logs"){
       
       include ESIGN_PLUGIN_PATH . "/views/about/logs.php";
       
   }
   elseif($current == "tools"){
       
       include ESIGN_PLUGIN_PATH . "/views/about/tools.php";
       
   }
   elseif($current == "import_info"){
       
       include ESIGN_PLUGIN_PATH . "/views/about/import-tool.php";
       
   }


?>

