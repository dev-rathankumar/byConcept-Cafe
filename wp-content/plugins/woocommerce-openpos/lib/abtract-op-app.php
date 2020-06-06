<?php
/**
 * Created by PhpStorm.
 * User: anhvnit
 * Date: 3/19/19
 * Time: 23:36
 */
require_once "interface-op-app.php";
abstract class OP_App_Abstract  {
    public $key;
    public $name;
    public $thumb; // 256x256
    public $session;

    public function get_key(){
        return $this->key;
    }
    public function get_name(){
        return $this->name;
    }
    public function get_thumb(){
        return $this->thumb;
    }
    public function set_key($key){
        $this->key = $key;
    }
    public function set_name($name){
        $this->name = $name;
    }
    public function set_thumb($url){
        $this->thumb = $url;
    }
    public function set_session($session)
    {
        $this->session = $session;
    }
    public function get_session(){
        return $this->session;
    }
    public function render(){}
}