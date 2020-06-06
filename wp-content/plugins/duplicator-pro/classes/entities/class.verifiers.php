<?php
/**
 * @copyright 2016 Snap Creek LLC
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUP_PRO_Verifier_Base
{
    protected $error_text = '';

    function __construct($error_text = '')
    {
        $this->error_text = $error_text;
    }

    // Returns an error string if succeeded or empty string if failed.
    public function Verify($value)
    {
        return "";
    }
}

/**
 * @copyright 2016 Snap Creek LLC
 */
class DUP_PRO_Range_Verifier extends DUP_PRO_Verifier_Base
{
    protected $min = 0;
    protected $max = 0;

    function __construct($min = 0, $max = 0, $error_text = '')
    {
        parent::__construct($error_text);

        $this->min = $min;
        $this->max = $max;
    }

    // Returns an error string if succeeded or empty string if failed.
    public function Verify($value)
    {
        if (($value < $this->min) || ($value > $this->max)) {

            return $this->error_text;
        } else {

            return "";
        }
    }
}

/**
 * @copyright 2016 Snap Creek LLC
 */
class DUP_PRO_Length_Verifier extends DUP_PRO_Verifier_Base
{
    protected $max_length = 0;

    function __construct($max_length = 0, $error_text = '')
    {
        parent::__construct($error_text);
        $this->max_length = $max_length;
    }

    // Returns an error string if succeeded or empty string if failed.
    public function Verify($value)
    {
        if (strlen($value) > $this->max_length) {
            return $this->error_text;
        } else {

            return '';
        }
    }
}

/**
 * @copyright 2016 Snap Creek LLC
 */
class DUP_PRO_Email_Verifier extends DUP_PRO_Verifier_Base
{
    protected $allow_blank = false;

    function __construct($allow_blank = false, $error_text = '')
    {
        parent::__construct($error_text);
        $this->allow_blank = $allow_blank;
    }

    // Returns an error string if succeeded or empty string if failed.
    public function Verify($value)
    {
        if ($this->allow_blank) {
            if (trim($value) == '') {
                return '';
            }
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return $this->error_text;
        } else {
            return '';
        }
    }
}

/**
 * @copyright 2016 Snap Creek LLC
 */
class DUP_PRO_Required_Verifier extends DUP_PRO_Verifier_Base
{

    function __construct($error_text = '')
    {
        parent::__construct($error_text);
    }

    // Returns an error string if succeeded or empty string if failed.
    public function Verify($value)
    {
        if (trim($value) == '') {
            return $this->error_text;
        } else {
            return '';
        }
    }
}

/**
 * @copyright 2016 Snap Creek LLC
 */
class DUP_PRO_Regex_Verifier extends DUP_PRO_Verifier_Base
{
    private $regex       = '';
    private $allow_blank = false;

    function __construct($regex = '', $error_text = '', $allow_blank = false)
    {
        parent::__construct($error_text);
        $this->regex       = $regex;
        $this->allow_blank = $allow_blank;
    }

    // Returns an error string if succeeded or empty string if failed.
    public function Verify($value)
    {
        if ((trim($value) == '') && ($this->allow_blank)) {
            return '';
        }

        if (preg_match($this->regex, $value) != 1) {
            return $this->error_text;
        } else {
            return "";
        }
    }
}
