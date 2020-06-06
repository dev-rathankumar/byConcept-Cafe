<?php

class WP_E_Esigscripts extends WP_E_Model {
	
	// setting a private variable
	private $scripts;
	
	public function __construct(){
		parent::__construct();
		
		$this->settings = new WP_E_Setting();
		$this->user = new WP_E_User();
		// adding action 	
	}

	/**
	* Set Alert
	*	Set scripts to load
	* 
	* @since 1.1.6
	* @param Array in format: array('type'=>'error|updated|warning', 'title' => 'error title', 'message' => 'error message')
	* @return Bool
	*/
	public function setAlert($script)
	{
		$this->alerts[] = $script;
		return 1;
	}

	public function display_ui_scripts($scripts)
	{
		
		// define scripts folder 
		
		if(array($scripts))
		{
			foreach($scripts as $js)
			{
				
				if (file_exists(ABSPATH . WPINC ."/js/jquery/ui/". $js .".js")) 
				{
					echo "<script type='text/javascript' src='". includes_url() ."js/jquery/ui/". $js .".js'></script>";
				}
				else
				{
					echo "<script type='text/javascript' src='". includes_url() ."js/jquery/ui/jquery.ui.". $js .".js'></script>";
				}
			}
		}
	}
	
}