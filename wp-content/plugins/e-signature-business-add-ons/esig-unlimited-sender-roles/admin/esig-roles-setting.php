<?php

class Esig_Roles {

    const ESIG_ROLES_OPTION = "esig_unlimited_roles_option";
    const ESIG_ROLES_USER_OPTION = "esig_unlimited_users_option";
    const ESIG_DOC_UNLIMITED_ROLES = 'esig_unlimited_roles_';
    const ESIG_DOC_UNLIMTED_USERS = 'esig_unlimited_users_';

    public static function save_unlimited_roles_option($roles) {
        WP_E_Sig()->setting->set_generic(self::ESIG_ROLES_OPTION, json_encode($roles));
    }

    public static function save_unlimited_users_option($users) {
        WP_E_Sig()->setting->set_generic(self::ESIG_ROLES_USER_OPTION, json_encode($users));
    }

    public static function get_unlimited_roles_option() {
        return json_decode(WP_E_Sig()->setting->get_generic(self::ESIG_ROLES_OPTION));
    }

    public static function get_unlimited_uesrs_option() {
        return json_decode(WP_E_Sig()->setting->get_generic(self::ESIG_ROLES_USER_OPTION));
    }
    
    public static function is_roles_enabled(){
         $roles = self::get_unlimited_roles_option();
         if(!is_object($roles)){
             return false; 
         }
         return true ; 
    }
    
    public static function is_users_enabled(){
        $users = self::get_unlimited_uesrs_option();
        if(!is_object($users)){
            return false ;
        }
        return true ; 
    }
    
    public static function saveDocumentRoles($docId,$value){
        WP_E_Sig()->meta->add($docId,  self::ESIG_DOC_UNLIMITED_ROLES,  json_encode($value));
        
    }
    
    public static function getDocumentRoles($docId){
        $roles = WP_E_Sig()->meta->get($docId,  self::ESIG_DOC_UNLIMITED_ROLES);
        if($roles){
            return $roles;
        }
        return WP_E_Sig()->setting->get_generic(self::ESIG_DOC_UNLIMITED_ROLES.$docId);
    }
    
    public static function saveDocumentUsers($docId,$value){
        WP_E_Sig()->meta->add($docId,  self::ESIG_DOC_UNLIMTED_USERS,json_encode($value));
    }
    
    public static function getDcoumentUsers($docId){
        $users = WP_E_Sig()->meta->get($docId,  self::ESIG_DOC_UNLIMTED_USERS);
        if($users){
            return $users;
        }
        return WP_E_Sig()->setting->get_generic(self::ESIG_DOC_UNLIMTED_USERS.$docId);
    }

}
