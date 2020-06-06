<?php
defined('ABSPATH') || exit;

function duplicator_pro_get_home_path()
{
    static $homePath = null;
    if (is_null($homePath)) {
        $homePath = DupProSnapLibIOU::safePathUntrailingslashit(DupProSnapLibUtilWp::getHomePath(), true);
    }
    return $homePath;
}
if (!function_exists('wp_doing_ajax')) {

    /**
     * Determines whether the current request is a WordPress Ajax request.
     *
     * @since 4.7.0
     *
     * @return bool True if it's a WordPress Ajax request, false otherwise.
     */
    function wp_doing_ajax()
    {
        /**
         * Filters whether the current request is a WordPress Ajax request.
         *
         * @since 4.7.0
         *
         * @param bool $wp_doing_ajax Whether the current request is a WordPress Ajax request.
         */
        return apply_filters('wp_doing_ajax', defined('DOING_AJAX') && DOING_AJAX);
    }
}

if (!function_exists('sanitize_textarea_field')) {

    /**
     * Sanitizes a multiline string from user input or from the database.
     *
     * The function is like sanitize_text_field(), but preserves
     * new lines (\n) and other whitespace, which are legitimate
     * input in textarea elements.
     *
     * @see sanitize_text_field()
     *
     * @since 4.7.0
     *
     * @param string $str String to sanitize.
     * @return string Sanitized string.
     */
    function sanitize_textarea_field($str)
    {
        $filtered = _sanitize_text_fields($str, true);

        /**
         * Filters a sanitized textarea field string.
         *
         * @since 4.7.0
         *
         * @param string $filtered The sanitized string.
         * @param string $str      The string prior to being sanitized.
         */
        return apply_filters('sanitize_textarea_field', $filtered, $str);
    }
}

if (!function_exists('_sanitize_text_fields')) {

    /**
     * Internal helper function to sanitize a string from user input or from the db
     *
     * @since 4.7.0
     * @access private
     *
     * @param string $str String to sanitize.
     * @param bool $keep_newlines optional Whether to keep newlines. Default: false.
     * @return string Sanitized string.
     */
    function _sanitize_text_fields($str, $keep_newlines = false)
    {
        $filtered = wp_check_invalid_utf8($str);

        if (strpos($filtered, '<') !== false) {
            $filtered = wp_pre_kses_less_than($filtered);
            // This will strip extra whitespace for us.
            $filtered = wp_strip_all_tags($filtered, false);

            // Use html entities in a special case to make sure no later
            // newline stripping stage could lead to a functional tag
            $filtered = str_replace("<\n", "&lt;\n", $filtered);
        }

        if (!$keep_newlines) {
            $filtered = preg_replace('/[\r\n\t ]+/', ' ', $filtered);
        }
        $filtered = trim($filtered);

        $found = false;
        while (preg_match('/%[a-f0-9]{2}/i', $filtered, $match)) {
            $filtered = str_replace($match[0], '', $filtered);
            $found    = true;
        }

        if ($found) {
            // Strip out the whitespace that may now exist after removing the octets.
            $filtered = trim(preg_replace('/ +/', ' ', $filtered));
        }

        return $filtered;
    }
}

if (!function_exists('wp_normalize_path')) {

    /**
     * Normalize a filesystem path.
     *
     * On windows systems, replaces backslashes with forward slashes
     * and forces upper-case drive letters.
     * Allows for two leading slashes for Windows network shares, but
     * ensures that all other duplicate slashes are reduced to a single.
     *
     * @since 3.9.0
     * @since 4.4.0 Ensures upper-case drive letters on Windows systems.
     * @since 4.5.0 Allows for Windows network shares.
     * @since 4.9.7 Allows for PHP file wrappers.
     *
     * @param string $path Path to normalize.
     * @return string Normalized path.
     */
    function wp_normalize_path($path)
    {
        $wrapper = '';
        if (wp_is_stream($path)) {
            list( $wrapper, $path ) = explode('://', $path, 2);
            $wrapper .= '://';
        }

        // Standardise all paths to use /
        $path = str_replace('\\', '/', $path);

        // Replace multiple slashes down to a singular, allowing for network shares having two slashes.
        $path = preg_replace('|(?<=.)/+|', '/', $path);

        // Windows paths should uppercase the drive letter
        if (':' === substr($path, 1, 1)) {
            $path = ucfirst($path);
        }

        return $wrapper.$path;
    }
}
