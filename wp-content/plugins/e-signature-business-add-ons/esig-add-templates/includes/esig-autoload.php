<?php
/**
 * Autoload
 * Auto loads classes from their respective directories
 * @since 1.3.0
 */


spl_autoload_register( 'esig_temp_autoload');


function esig_temp_autoload($classname)
{
        
        $dirs = array("models");
        foreach($dirs as $dir){
                
               // replace dash with underscore . 
               $classname = str_replace("_", "-", $classname);
               
               $filename = ESIGN_TEMP_BASE_PATH . "/" .  $dir . "/" . $classname .".php";
               
		if(file_exists($filename)){
			include_once($filename);
			break;
		}
        }
}
