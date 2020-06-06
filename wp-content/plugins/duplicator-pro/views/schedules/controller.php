<?php
defined("ABSPATH") or die("");

DUP_PRO_Handler::init_error_handler();
DUP_PRO_U::hasCapability('manage_options');

global $wpdb;

//COMMON HEADER DISPLAY
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/assets/js/javascript.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/views/inc.header.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/classes/ui/class.ui.dialog.php');

$current_tab = isset($_REQUEST['tab']) ? sanitize_text_field($_REQUEST['tab']) : 'schedules';
?>

<div class="wrap">

    <?php duplicator_pro_header(DUP_PRO_U::__("Schedules")) ?>

    <!-- FUTURE SUPPORT FOR TABS HERE: 
	 See /packages/controller for sample code --> 

    <?php
    switch ($current_tab) {
        case 'schedules': include('schedule.controller.php');
            break;	
    }
    ?>
</div>
