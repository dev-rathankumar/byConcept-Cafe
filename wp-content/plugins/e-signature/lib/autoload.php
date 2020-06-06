<?php
/**
 * Autoload
 * Auto loads classes from their respective directories
 * @since 0.1.0
 */

spl_autoload_register( 'esig_autoload' );

if(!function_exists('lcfirst')) {
    function lcfirst($str) {
        $str[0] = strtolower($str[0]);
        return $str;
    }
}

function esig_autoload($classname) {

	$dirs = array("models", "views", "controllers", "lib","vendors", "vendors".ESIG_DS."whiskers");

	foreach($dirs as $dir){
		
		$classname = preg_replace('/^WP_E_/', '', $classname);
		
		
		// If classname ends in 'Controller'
		
		if(strlen($classname) > strlen('Controller')){
			if(substr_compare($classname, 'Controller', -strlen('Controller'), strlen('Controller')) === 0){
				$classname = lcfirst($classname);  // Controllers have lc first first letter filename
			}
		}
          //echo "<center>" . $classname . "</center><br>"; 
		
		$filename = ESIGN_PLUGIN_PATH . ESIG_DS . $dir . ESIG_DS . $classname .".php";
	
		if(file_exists($filename)){
			include_once($filename);
                       
			break;
		}
	}
}


