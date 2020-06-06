<?php
/**
 * Restore only package
 *
 * Standard: PSR-2
 *
 * @package SC\DUPX\
 * @link http://www.php-fig.org/psr/psr-2/
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUP_PRO_RestoreOnly_Package
{

    /**
     *
     * @var DUP_PRO_RestoreOnly_Package
     */
    protected static $instance = null;

    private function __construct()
    {
        
    }

    /**
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function init()
    {
        if (!self::canBeMigrate()) {
            add_filter('duplicator_pro_overwrite_params_data', array(__CLASS__, 'forceSkipReplace'));
        }
    }

    public static function isRestoreOnly()
    {
        $overwriteInstallerParams = apply_filters('duplicator_pro_overwrite_params_data', array());
        return (
            isset($overwriteInstallerParams['mode_chunking']['value']) &&
            $overwriteInstallerParams['mode_chunking']['value'] == 3 &&
            isset($overwriteInstallerParams['mode_chunking']['formStatus']) &&
            $overwriteInstallerParams['mode_chunking']['formStatus'] == 'st_infoonly'
            );
    }

    public static function forceSkipReplace($data)
    {
        $data['mode_chunking'] = array(
            'value'      => 3,
            'formStatus' => 'st_infoonly'
        );
        return $data;
    }

    private static function canBeMigrate()
    {
        $homePath = DUP_PRO_Archive::getArchiveListPaths('home');
        return apply_filters('duplicator_pro_package_can_be_migrate', (strlen($homePath) > 2));
    }
}