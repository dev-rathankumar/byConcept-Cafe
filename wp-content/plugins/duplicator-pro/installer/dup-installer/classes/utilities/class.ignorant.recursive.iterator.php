<?php
/**
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\U
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class IgnorantRecursiveDirectoryIterator extends RecursiveDirectoryIterator
{

    function getChildren()
    {
        try {
            return new IgnorantRecursiveDirectoryIterator($this->getPathname());
        }
        catch (UnexpectedValueException $e) {
            return new RecursiveArrayIterator(array());
        }
    }
}