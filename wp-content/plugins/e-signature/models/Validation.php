<?php

class WP_E_Validation extends WP_E_Model {

    public $esig_valid = false;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Retrn sanitize string . 
     * @param unknown $string
     */
    public function esig_clean($string) {

        return sanitize_text_field($string);
    }

    /**
     * Sanitize a string destined to be a tooltip. Prevents XSS.
     * @param string $var
     * @return string
     */
    public function esig_sanitize_tooltip($var) {
        return wp_kses(html_entity_decode($var), array(
            'br' => array(),
            'em' => array(),
            'strong' => array(),
            'span' => array(),
            'ul' => array(),
            'li' => array(),
            'ol' => array(),
            'p' => array(),
        ));
    }

    /**
     * check the value is int 
     * @param int $var
     * @return bool
     */
    public function esig_valid_int($var) {
        return filter_var($var, FILTER_VALIDATE_INT);
    }

    /**
     * 
     * @param unknown $var
     * @return mixed|boolean
     */
    public function esig_valid_string($var) {


        $string = $this->esig_clean($var);

        $string = esc_js($string);

        if (!$this->esig_valid_int($string)) {

            return filter_var($string, FILTER_SANITIZE_STRING);
        } else {
            $this->esig_valid = true;

            return false;
        }
    }

    public function esig_valid_fullName($var) {
        $string = $this->esig_clean($var);
        $valid = false;
        // $string = esc_js($string);
        if (preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.()'-]([-']?[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.()'-]+)*( [a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.()'-]([-']?[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.()'-]+))+$/i", $string)) {
           $valid = true;
        }
        elseif(preg_match("/\p{Han}+/u", $string)){
            $valid= true;
        }
        elseif(preg_match("/\p{L}+/u", $string)){
            $valid= true;
        }
        return $valid;
        /* if (!preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]([-']?[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]+)*( [a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]([-']?[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]+))+$/i", $string)) {
          return false;
          } */
        
    }

    public function esig_valid_email($var) {
        $string = $this->esig_clean($var);
        if (is_email($string)) {
            return true;
        }
        return false;
    }

    public function valid_sif($var) {

        if (is_array($var)) {
            return $var;
        }
        if (seems_utf8($var)) {
            return $var;
        }
        $string = $this->esig_clean($var);
        return $string;
    }

    public function valid_json($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

}
