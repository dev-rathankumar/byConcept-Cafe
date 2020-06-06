<?php defined("ABSPATH") or die("");
	function duplicator_pro_header($title) 
	{
		//Entity item is not updating
		//$global = DUP_PRO_Global_Entity::get_instance();
		echo "<h1>".esc_html($title)."</h1>";
	} 
?>