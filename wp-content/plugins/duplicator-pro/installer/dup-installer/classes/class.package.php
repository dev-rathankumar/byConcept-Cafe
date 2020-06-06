<?php
/**
 * Class used to update and edit web server configuration files
 * for .htaccess, web.config and user.ini
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\Crypt
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * Package related functions
 *
 */
final class DUPX_Package
{

    /**
     *
     * @staticvar bool|string $packageHash
     * @return bool|string false if fail
     * @throws Exception
     */
    public static function getPackageHash()
    {
        static $packageHash = null;
        if (is_null($packageHash)) {
            if (($packageHash = DUPX_Boot::getPackageHash()) === false) {
                throw new Exception('PACKAGE ERROR: can\'t find package hash');
            }
        }
        return $packageHash;
    }

    /**
     * 
     * @staticvar string $fileHash
     * @return string
     */
    public static function getArchiveFileHash()
    {
        static $fileHash = null;

        if (is_null($fileHash)) {
            $fileHash = preg_replace('/^.+_([a-z0-9]+)_[0-9]{14}_archive\.(?:daf|zip)$/', '$1', DUPX_Security::getInstance()->getArchivePath());
        }

        return $fileHash;
    }

    /**
     * 
     * @staticvar string $archivePath
     * @return bool|string false if fail
     * @throws Exception
     */
    public static function getPackageArchivePath()
    {
        static $archivePath = null;
        if (is_null($archivePath)) {
            $path = DUPX_INIT.'/'.DUPX_Boot::ARCHIVE_PREFIX.self::getPackageHash().DUPX_Boot::ARCHIVE_EXTENSION;
            if (!file_exists($path)) {
                throw new Exception('PACKAGE ERROR: can\'t read package path: '.$path);
            } else {
                $archivePath = $path;
            }
        }
        return $archivePath;
    }

    /**
     * Returns a save-to-edit wp-config file
     *
     * @return string
     * @throws Exception
     */
    public static function getWpconfigArkPath()
    {
        return DUPX_Orig_File_Manager::getInstance()->getEntryStoredPath('wpconfig');
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public static function getManualExtractFile()
    {
        return DUPX_INIT.'/dup-manual-extract__'.self::getPackageHash();
    }

    /**
     * 
     * @staticvar type $path
     * @return string
     */
    public static function getWpconfigSamplePath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT.'/assets/wp-config-sample.php';
        }
        return $path;
    }

    /**
     *
     * @staticvar string $path
     * @return string
     */
    public static function getSqlFilePath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT.'/dup-database__'.self::getPackageHash().'.sql';
        }
        return $path;
    }

    /**
     *
     * @return int
     */
    public static function getSqlFileSize()
    {
        return (is_readable(self::getSqlFilePath())) ? (int) filesize(self::getSqlFilePath()) : 0;
    }
}
