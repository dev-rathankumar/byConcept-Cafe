<?php
/**
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package dup_archive
 * @copyright (c) year, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

if (!class_exists('DupArchiveConstants')) {

    class DupArchiveConstants
    {

        public static $DARoot;
        public static $LibRoot;
        public static $MaxFilesizeForHashing;

        public static function init()
        {
            self::$LibRoot               = dirname(__FILE__).'/../../';
            self::$DARoot                = dirname(__FILE__).'/../';
            self::$MaxFilesizeForHashing = 1000000000;
        }
    }
    DupArchiveConstants::init();
}

if (!class_exists('DupArchiveExceptionCodes')) {

    class DupArchiveExceptionCodes
    {

        const NonFatal = 0;
        const Fatal    = 1;

    }
}

