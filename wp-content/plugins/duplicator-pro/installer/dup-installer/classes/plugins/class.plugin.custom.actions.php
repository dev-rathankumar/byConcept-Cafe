<?php
/**
 * plugin custom actions
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\U
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUPX_Plugin_custom_actions
{

    const BY_DEFAULT_AUTO     = 'auto';
    const BY_DEFAULT_DISABLED = 'disabled';
    const BY_DEFAULT_ENABLED  = 'enabled';

    /**
     *
     * @var string 
     */
    protected $slug = null;

    /**
     *
     * @var bool|callable 
     */
    protected $byDefaultStatus = self::BY_DEFAULT_AUTO;

    /**
     *
     * @var bool|callable 
     */
    protected $enableAfterLogin = false;

    /**
     *
     * @var string
     */
    protected $byDefaultMessage = '';

    /**
     * 
     * @param string $slug
     * @param bool|callable $disablebyDefault
     * @param bool|callable $enableAfterLogin
     */
    public function __construct($slug, $byDefaultStatus, $enableAfterLogin, $byDefaultMessage)
    {
        $this->slug             = $slug;
        $this->byDefaultStatus  = $byDefaultStatus;
        $this->enableAfterLogin = $enableAfterLogin;
        $this->byDefaultMessage = $byDefaultMessage;
    }

    public function byDefaultStatus()
    {
        if (is_callable($this->byDefaultStatus)) {
            $callable = $this->byDefaultStatus;
            return $callable($this->slug);
        } else {
            return $this->byDefaultStatus;
        }
    }

    public function isEnableAfterLogin()
    {
        if (is_callable($this->enableAfterLogin)) {
            $callable = $this->enableAfterLogin;
            return $callable($this->slug);
        } else {
            return $this->enableAfterLogin;
        }
    }

    public function byDefaultMessage()
    {
        return $this->byDefaultMessage;
    }
}