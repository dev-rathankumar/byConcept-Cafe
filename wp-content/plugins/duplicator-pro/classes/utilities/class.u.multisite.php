<?php
defined("ABSPATH") or die("");

class DUP_PRO_MU_Generations
{

    const NotMultisite  = 0;
    const PreThreeFive  = 1;
    const ThreeFivePlus = 2;

}

class DUP_PRO_MU
{

    public static function networkMenuPageUrl($menu_slug, $echo = true)
    {
        global $_parent_pages;

        if (isset($_parent_pages[$menu_slug])) {
            $parent_slug = $_parent_pages[$menu_slug];
            if ($parent_slug && !isset($_parent_pages[$parent_slug])) {
                $url = network_admin_url(add_query_arg('page', $menu_slug, $parent_slug));
            } else {
                $url = network_admin_url('admin.php?page='.$menu_slug);
            }
        } else {
            $url = '';
        }

        $url = esc_url($url);

        if ($echo) {
            echo esc_url($url);
        }

        return $url;
    }

    public static function isMultisite()
    {
        return self::getMode() > 0;
    }

    // 0 = single site; 1 = multisite subdomain; 2 = multisite subdirectory
    public static function getMode()
    {

        if (is_multisite()) {
            if (defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL) {
                return 1;
            } else {
                return 2;
            }
        } else {
            return 0;
        }
    }

    /**
     * This function is wrong because it assumes that if the folder sites exist, blogs.dir cannot exist.
     * This is not true because if the network is old but a new site is created after the WordPress update both blogs.dir and sites folders exist.
     * 
     * @deprecated since version 3.8.4
     *
     * @return int
     */
    public static function getGeneration()
    {
        if (self::getMode() == 0) {
            return DUP_PRO_MU_Generations::NotMultisite;
        } else {
            $sitesDir = WP_CONTENT_DIR.'/uploads/sites';

            if (file_exists($sitesDir)) {
                return DUP_PRO_MU_Generations::ThreeFivePlus;
            } else {
                return DUP_PRO_MU_Generations::PreThreeFive;
            }
        }
    }

    /**
     * 
     * @param int[] $filter_sites
     * @return array
     */
    public static function getSubsites($filter_sites = array())
    {
        $site_array = array();
        $mu_mode    = DUP_PRO_MU::getMode();

        if ($mu_mode !== 0) {
            DUP_PRO_LOG::trace("NETWORK SITES");

            if (function_exists('get_sites')) {
                $sites = get_sites(array('number' => 2000));
            } else {
                $sites = wp_get_sites(array('limit' => 2000));
            }
            foreach ($sites as $site) {
                $site = (object) $site;
                if (in_array($site->blog_id, $filter_sites)) {
                    continue;
                }
                $site_details = get_blog_details($site->blog_id);

                $site_info              = new stdClass();
                $site_info->id          = $site->blog_id;
                $site_info->domain      = $site_details->domain;
                $site_info->path        = $site_details->path;
                $site_info->blogname    = $site_details->blogname;
                $site_info->blog_prefix = $GLOBALS['wpdb']->get_blog_prefix($site->blog_id);

                array_push($site_array, $site_info);
                DUP_PRO_LOG::trace("Multisite subsite detected. ID={$site_info->id} Domain={$site_info->domain} Path={$site_info->path} Blogname={$site_info->blogname}");
            }
        }

        return $site_array;
    }

    /**
     * Returns the main site ID for the network.
     *
     * Copied from the source of the get_main_site_id() except first line in https://developer.wordpress.org/reference/functions/get_main_site_id/
     * get_main_site_id() is introduced in WP 4.9.0. It is for backward compatibility
     * 
     * @param int|null network id
     * @return int The ID of the main site.
     */
    public static function get_main_site_id($network_id = null)
    {
        // For > WP 4.9.0  
        if (function_exists('get_main_site_id')) {
            return get_main_site_id($network_id);
        }

        if (!is_multisite()) {
            return get_current_blog_id();
        }

        $network = function_exists('get_network') ? get_network($network_id) : wp_get_network($network_id);
        if (!$network) {
            return 0;
        }

        return $network->site_id;
    }
}