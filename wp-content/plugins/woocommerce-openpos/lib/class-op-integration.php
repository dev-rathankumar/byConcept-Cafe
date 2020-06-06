<?php
/**
 * Created by PhpStorm.
 * User: anhvnit
 * Date: 9/18/18
 * Time: 17:17
 */
if(!class_exists('OP_Integration'))
{
    class OP_Integration{
    
        public function init(){
            foreach($this->plugins() as $plugin)
            {

                if(is_plugin_active( $plugin))
                {
                    
                    $file = basename($plugin);
                    $file_path = rtrim(OPENPOS_DIR,'/').'/lib/integration/'.$file;
                    $info = pathinfo($file);
                    $class_name = $this->generateClassName($info['filename']);
                        
                    if(file_exists($file_path))
                    {
                        require_once($file_path);
                        if(class_exists($class_name))
                        {
                            $tmp = new $class_name;
                            $tmp->init();
                        }
                    }
                }
            }

        }
        public function generateClassName($string){
            $string = trim($string);
            $str = str_replace('-','_',$string);
            $pieces = explode('_',$str);
            $new_pieces = array();
            foreach($pieces as $p)
            {
                $new_pieces[] = ucfirst(trim($p));
            }
            return 'OP_'.implode('_',$new_pieces);
        }
        public function plugins(){
            $plugins = array(
                'woocommerce-product-addons/woocommerce-product-addons.php',
                'woocommerce-product-bundles/woocommerce-product-bundles.php',
                'gratisfaction-all-in-one-loyalty-contests-referral-program-for-woocommerce/grconnect.php'
            );
            return $plugins;
        }

    }

}
$tmp_integration = new OP_Integration();
$tmp_integration->init();