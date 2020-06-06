<?php
defined("ABSPATH") or die("");
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/entities/class.secure.global.entity.php');

$license = DUP_PRO_License_U::getLicenseType();
$is_freelancer_plus = ($license >= DUP_PRO_License_Type::Freelancer);
$migrate_access = ($license === DUP_PRO_License_Type::BusinessGold || $license === DUP_PRO_License_Type::Freelancer) ? true : false;
$subtab = isset($_REQUEST['subtab']) ? $_REQUEST['subtab'] : 'general';
$txt_general = DUP_PRO_U::__("General Settings");
$txt_profile = DUP_PRO_U::__("Features");
$txt_migrate = DUP_PRO_U::__("Migrate Settings");
$spacer = ' &nbsp;|&nbsp; ';
$url = 'admin.php?page=duplicator-pro-settings';

$link_migrate = "{$spacer}<a href='{$url}&subtab=migrate'>{$txt_migrate}</a>";

switch ($subtab) {

//GENERAL TAB
case 'general':
$html = <<<HTML
<div class='dpro-sub-tabs'>
	<b>{$txt_general}</b>{$spacer}
	<a href='{$url}&subtab=profile'>{$txt_profile}</a>
	{$link_migrate}
 </div>
HTML;
	echo $html;
	include ('inc.general.php');
break;

//PROFILE TAB
case 'profile':
$html = <<<HTML
<div class='dpro-sub-tabs'>
	<a href='{$url}&subtab=general'>{$txt_general}</a>{$spacer}
	<b>{$txt_profile}</b>
	{$link_migrate}
 </div>
HTML;
	echo $html;
	include ('inc.feature.php');
break;

//MIGRATE TAB
case 'migrate':	
$html = <<<HTML
<div class='dpro-sub-tabs'>
	<a href='{$url}&subtab=general'>{$txt_general}</a>{$spacer}
	<a href='{$url}&subtab=profile'>{$txt_profile}</a>{$spacer}
	<b>{$txt_migrate}</b>
 </div>
HTML;
	echo $html;
    	include_once ('inc.migrate.php');
}
