<?php
/**
 * 
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * In this class all the utility functions related to the wordpress configuration and the package are defined.
 * 
 */
class DUPX_Conf_Utils
{

    /**
     * 
     * @return bool
     */
    public static function showMultisite()
    {
        $archive_config = DUPX_ArchiveConfig::getInstance();

        return ($archive_config->mu_mode !== 0 && count($archive_config->subsites) > 0);
    }

    /**
     * 
     * @staticvar null|bool $present
     * @return bool
     */
    public static function isConfArkPresent()
    {
        static $present = null;
        if (is_null($present)) {
            $present = file_exists(DUPX_Package::getWpconfigArkPath());
        }
        return $present;
    }

    public static function isManualExtractFilePresent()
    {
        static $present = null;
        if (is_null($present)) {
            $present = file_exists(DUPX_Package::getManualExtractFile());
        }
        return $present;
    }

    /**
     * 
     * @staticvar null|bool $muEnabled
     * @return bool
     */
    public static function multisitePlusEnabled()
    {
        static $muEnabled = null;
        if (is_null($muEnabled)) {
            $archive_config = DUPX_ArchiveConfig::getInstance();
            $muEnabled      = ($archive_config->getLicenseType() == DUPX_LicenseType::BusinessGold);
        }

        return $muEnabled;
    }

    /**
     * 
     * @staticvar null|bool $enable
     * @return bool
     */
    public static function shellExecUnzipEnable()
    {
        static $enable = null;
        if (is_null($enable)) {
            $enable = DUPX_Server::get_unzip_filepath() != null;
        }
        return $enable;
    }

    /**
     * 
     * @return bool
     */
    public static function classZipArchiveEnable()
    {
        return class_exists('ZipArchive');
    }

    /**
     * 
     * @staticvar bool $exists
     * @return bool
     */
    public static function archiveExists()
    {
        static $exists = null;
        if (is_null($exists)) {
            $exists = file_exists(DUPX_Security::getInstance()->getArchivePath());
        }
        return $exists;
    }

    /**
     * 
     * @staticvar bool $arcSize
     * @return bool
     */
    public static function archiveSize()
    {
        static $arcSize = null;
        if (is_null($arcSize)) {
            $archivePath = DUPX_Security::getInstance()->getArchivePath();
            $arcSize     = file_exists($archivePath) ? (int) @filesize($archivePath) : 0;
        }
        return $arcSize;
    }

    /**
     * 
     * @staticvar type $arcCheck
     * @return string
     */
    public static function archiveCheck()
    {
        static $arcCheck = null;

        if (is_null($arcCheck)) {
            //ARCHIVE FILE
            if (DUPX_Conf_Utils::archiveExists()) {
                $arcCheck = 'Pass';
            } else {
                if (DUPX_Conf_Utils::isConfArkPresent()) {
                    $arcCheck = 'Warn';
                } else {
                    $arcCheck = 'Fail';
                }
            }
        }

        return $arcCheck;
    }
}