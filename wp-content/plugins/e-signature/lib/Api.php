<?php

class WP_E_Api {

    public function __construct() {
        $this->view = new WP_E_View();
        $this->invite = new WP_E_Invite;
        $this->document = new WP_E_Document;
        $this->user = new WP_E_User;
        $this->signer = new WP_E_Signer();
        $this->setting = new WP_E_Setting;
        $this->validation = new WP_E_Validation();
        $this->notice = new WP_E_Notice();
        $this->email = new WP_E_Email();
        $this->meta = new WP_E_Meta;
        $this->common = new WP_E_Common();
        $this->signature = new WP_E_Signature;
    }

}


