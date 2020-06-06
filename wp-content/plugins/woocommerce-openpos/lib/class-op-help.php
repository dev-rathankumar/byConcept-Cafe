<?php
class OpHelpApp extends OP_App_Abstract implements OP_App {
    public $key = 'op_help_app'; // unique
    public $name = 'Help';
    public $thumb = OPENPOS_URL.'/assets/images/help.png';
    public function render()
    {
        
        header('X-Frame-Options: allow-from *');
        $session = $this->get_session();
        require_once OPENPOS_DIR.'/templates/help.php';
    }

}