<?php
defined("ABSPATH") or die("");

/**
 * Wordpress utility functions
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DUP_PRO
 * @subpackage classes/utilities
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 * @since 3.8.9
 *
 */

/**
 * Wordpress utility functions
 */
class DUP_PRO_WP_U
{

    public static function getAdminUserLists()
    {
        if (is_multisite()) {
            $superAdmins = get_site_option('site_admins');
            $users       = get_users(array(
                'fields'    => array('id', 'user_login'),
                'blog_id'   => 0,
                'login__in' => $superAdmins
            ));
        } else {
            $users = get_users(array(
                'fields' => array('id', 'user_login'),
                'role'   => 'administrator'
            ));
        }
        return $users;
    }
}