<?php
	
	require_once(dirname(__FILE__).'../../../../wp-config.php');

	if (class_exists('Customerly')){
        if (isset($_POST['email'])){
            header('Content-Type: application/json');
            $result = Customerly::create_leads($_POST['email'], $_POST['name'], $_POST);
            print_r($result);
        }else{
            print_r(array('result' => 'error'));
        }
    }