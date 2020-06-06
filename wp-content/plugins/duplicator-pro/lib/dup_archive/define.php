<?php
if (!defined('ABSPATH') && !defined("DUPXABSPATH")) {
    die("");
}
//Prevent directly browsing to the file
if(!defined('DUPARCHIVE_VERSION')) {
    // Should always match the version of Duplicator Pro that includes the library
    define('DUPARCHIVE_VERSION', '3.8.9');
}
