<?php
/*
 * recipientsController
 * @since 1.0.1
 * @author Micah Blu
 */

class WP_E_recipientsController extends WP_E_appController {

	public function __construct(){
		parent::__construct();
		$this->enqueueScripts();
	
		//include ESIGN_PLUGIN_PATH . DS . "models" . DS . "Recipient.php";
		$this->model = new WP_E_Recipient();
	}

	public function calling_class(){
		return get_class();
	}

	private function enqueueScripts(){
		//wp_enqueue_style('tabs', ESIGN_ASSETS_DIR_URI . DS . "css/jquery.tabs.css");
	}

	public function index(){

	}

	public function add(){

	}

	public function edit(){
	}
}
