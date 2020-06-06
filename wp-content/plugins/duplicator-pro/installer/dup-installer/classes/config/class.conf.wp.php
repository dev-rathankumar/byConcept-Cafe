<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * Class used to update and edit web server configuration files
 * for both Apache and IIS files .htaccess and web.config
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\WPConfig
 *
 */
class DUPX_WPConfig
{

    const ADMIN_SERIALIZED_SECURITY_STRING = 'a:1:{s:13:"administrator";b:1;}';
    const ADMIN_LEVEL                      = 10;

    /**
     * get wp-config default path (not relative to orig file manger)
     * 
     * @return string
     */
    public static function getWpConfigDeafultPath()
    {
        return DUPX_Paramas_Manager::getInstance()->getValue(DUPX_Paramas_Manager::PARAM_PATH_NEW).'/wp-config.php';
    }

    /**
     *
     * @return bool|string false if fail
     */
    public static function getWpConfigPath()
    {
        return DUPX_Orig_File_Manager::getInstance()->getEntryTargetPath('wpconfig', self::getWpConfigDeafultPath());
    }

    /**
     * 
     * @staticvar boolean|WPConfigTransformer $confTransformer
     * @return boolean|WPConfigTransformer
     */
    public static function getLocalConfigTransformer()
    {
        static $confTransformer = null;

        if (is_null($confTransformer)) {
            try {
                if (($wpConfigPath = DUPX_ServerConfig::getWpConfigLocalStoredPath()) === false) {
                    $wpConfigPath = DUPX_WPConfig::getWpConfigPath();
                }
                if (is_readable($wpConfigPath)) {
                    $confTransformer = new WPConfigTransformer($wpConfigPath);
                } else {
                    $confTransformer = false;
                }
            }
            catch (Exception $e) {
                $confTransformer = false;
            }
        }

        return $confTransformer;
    }

    /**
     * 
     * @param string $name
     * @param string $type  // constant | variable
     * @param mixed $default
     * @return mixed 
     */
    public static function getValueFromLocalWpConfig($name, $type = 'constant', $default = '')
    {
        if (($confTransformer = self::getLocalConfigTransformer()) !== false) {
            return $confTransformer->exists($type, $name) ? $confTransformer->get_value($type, $name) : $default;
        } else {
            return null;
        }
    }

    /**
     * Generates a random password drawn from the defined set of characters.
     * Copy of the wp_generate_password() function from wp-includes/pluggable.php with minor tweaks
     *
     * @since 2.5.0
     *
     * @param int  $length              Optional. The length of password to generate. Default 12.
     * @param bool $special_chars       Optional. Whether to include standard special characters.
     *                                  Default true.
     * @param bool $extra_special_chars Optional. Whether to include other special characters.
     *                                  Used when generating secret keys and salts. Default false.
     * @return string The random password.
     */
    public static function generatePassword($length = 12, $special_chars = true, $extra_special_chars = false)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        if ($special_chars) {
            $chars .= '!@#$%^&*()';
        }
        if ($extra_special_chars) {
            $chars .= '-_ []{}<>~`+=,.;:/?|';
        }

        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= substr($chars, self::rand(0, strlen($chars) - 1), 1);
        }

        return $password;
    }

    /**
     * Generates a random number
     * * Copy of the wp_rand() function from wp-includes/pluggable.php with minor tweaks
     *
     * @since 2.6.2
     * @since 4.4.0 Uses PHP7 random_int() or the random_compat library if available.
     *
     * @global string $rnd_value
     * @staticvar string $seed
     * @staticvar bool $external_rand_source_available
     *
     * @param int $min Lower limit for the generated number
     * @param int $max Upper limit for the generated number
     * @return int A random number between min and max
     */
    private static function rand($min = 0, $max = 0)
    {
        global $rnd_value;

        // Some misconfigured 32bit environments (Entropy PHP, for example) truncate integers larger than PHP_INT_MAX to PHP_INT_MAX rather than overflowing them to floats.
        $max_random_number = 3000000000 === 2147483647 ? (float) "4294967295" : 4294967295; // 4294967295 = 0xffffffff
        // We only handle Ints, floats are truncated to their integer value.
        $min               = (int) $min;
        $max               = (int) $max;

        // Use PHP's CSPRNG, or a compatible method
        static $use_random_int_functionality = true;
        if ($use_random_int_functionality) {
            try {
                $_max = ( 0 != $max ) ? $max : $max_random_number;
                // rand() can accept arguments in either order, PHP cannot.
                $_max = max($min, $_max);
                $_min = min($min, $_max);

                $val = random_int($_min, $_max);
                if (false !== $val) {
                    return abs(intval($val));
                } else {
                    $use_random_int_functionality = false;
                }
            }
            catch (Error $e) {
                $use_random_int_functionality = false;
            }
            catch (Exception $e) {
                $use_random_int_functionality = false;
            }
        }

        // Reset $rnd_value after 14 uses
        // 32(md5) + 40(sha1) + 40(sha1) / 8 = 14 random numbers from $rnd_value
        if (strlen($rnd_value) < 8) {
            static $seed = '';

            $rnd_value = md5(uniqid(microtime().mt_rand(), true).$seed);
            $rnd_value .= sha1($rnd_value);
            $rnd_value .= sha1($rnd_value.$seed);
            $seed      = md5($seed.$rnd_value);
        }

        // Take the first 8 digits for our value
        $value = substr($rnd_value, 0, 8);

        // Strip the first eight, leaving the remainder for the next call to rand().
        $rnd_value = substr($rnd_value, 8);

        $value = abs(hexdec($value));

        // Reduce the value to be within the min - max range
        if ($max != 0)
            $value = $min + ( $max - $min + 1 ) * $value / ( $max_random_number + 1 );

        return abs(intval($value));
    }
}