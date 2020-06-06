<?php
/**
 * godaddy custom hosting class
 *
 * Standard: PSR-2
 *
 * @package SC\DUPX\DB
 * @link http://www.php-fig.org/psr/psr-2/
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * class for wordpress.com managed hosting
 * 
 * @todo not yet implemneted
 * 
 */
class DUPX_WordpressCom_Host implements DUPX_Host_interface
{

    /**
     * return the current host itentifier
     *
     * @return string
     */
    public static function getIdentifier()
    {
        return DUPX_Custom_Host_Manager::HOST_WORDPRESSCOM;
    }

    /**
     * @return bool true if is current host
     */
    public function isHosting()
    {
        $testFile = DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_PATH_MUPLUGINS_NEW).'/wpcomsh-loader.php';
        if (!file_exists($testFile)) {
            return false;
        }

        $wpConfigFile = dirname(DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW)).'/wp-config.php';
        return file_exists($wpConfigFile);
    }

    /**
     * the init function.
     * is called only if isHosting is true
     *
     * @return void
     */
    public function init()
    {
        
    }

    /**
     * 
     * @return string
     */
    public function getLabel()
    {
        return 'Wordpress.com';
    }

    /**
     * this function is called if current hosting is this
     */
    public function setCustomParams()
    {
        
    }
}