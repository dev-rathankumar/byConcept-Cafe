<?php

if(!class_exists('Eig_Dropbox_Settings')):
    class Esig_Dropbox_Settings {
        
        const ESIG_DBOX_SETTINGS ='esig_dropbox';
        
        public static function save_dropbox_settings($document_id,$value){
            WP_E_Sig()->meta->add($document_id,  self::ESIG_DBOX_SETTINGS,$value);
        }
        
        public static function get_dropbox_settings($document_id){
             $dbox_settings= WP_E_Sig()->meta->get($document_id,  self::ESIG_DBOX_SETTINGS);
             if($dbox_settings){
                 return $dbox_settings;
             }
             return WP_E_Sig()->setting->get_generic(self::ESIG_DBOX_SETTINGS . $document_id);
        }
        
        public static function is_dropbox_enabled($document_id){
            if(self::get_dropbox_settings($document_id)){
                return true;
            }
            return false; 
        }
        
        public static function clone_dropbox_settings($document_id,$old_doc_id){
              if(self::is_dropbox_enabled($old_doc_id)){
                  WP_E_Sig()->meta->add($document_id,  self::ESIG_DBOX_SETTINGS,  self::get_dropbox_settings($old_doc_id));
              }
        }
        
        public static function get_sad_document_id(){
             $new_sad = new esig_sad_document();
             return $new_sad->get_sad_document_id();
        }
        
        public static function get_default_dbox_settings(){
            return WP_E_Sig()->setting->get_generic('esig_dropbox_default');
        }
        
        public static function save_default_dbox_settings($value){
            WP_E_Sig()->setting->set_generic('esig_dropbox_default',$value);
        }
        
        public static function is_dbox_default_enabled(){
            if(self::get_default_dbox_settings()){
                return true;
            }
            return false;
        }
    
    }
endif;

