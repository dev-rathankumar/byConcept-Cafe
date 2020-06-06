<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(!class_exists('ESIGN_SIGNER_ORDER_SETTING')):
    
    class ESIGN_SIGNER_ORDER_SETTING {
            const SIGNER_ORDER_META = 'esig_assign_signer_order';
            const SIGNER_ORDER_ACTIVE = 'esig_assign_signer_order_active';
            
            public static function save_assign_signer_order($document_id,$value){
                WP_E_Sig()->meta->add($document_id,  self::SIGNER_ORDER_META,  json_encode($value));
            }
            
            public static function get_assign_signer_order($document_id){
                $signer_order = WP_E_Sig()->meta->get($document_id,  self::SIGNER_ORDER_META);
                if($signer_order){
                    return json_decode($signer_order) ; 
                }
                else {
                    $signer_order_settings = WP_E_Sig()->setting->get_generic(self::SIGNER_ORDER_META . $document_id);
                    return $signer_order_settings;
                }
            }
            
            public static function save_signer_order_active($document_id,$value){
                WP_E_Sig()->meta->add($document_id,self::SIGNER_ORDER_ACTIVE,$value);
            }
            
            
            public static function get_signer_order_active($document_id){
                 $order_active = WP_E_Sig()->meta->get($document_id,  self::SIGNER_ORDER_ACTIVE);
                 if($order_active){
                     return $order_active;
                 }
                 else {
                     $signer_order_active = WP_E_Sig()->setting->get_generic(self::SIGNER_ORDER_ACTIVE . $document_id);
                     if($signer_order_active){
                         return $signer_order_active;
                     }
                 }
                 return false;
            }


            public static function is_signer_order_active($document_id){
                
                 if(self::get_signer_order_active($document_id)){
                     return true ; 
                 }
                 else {
                     return false;
                 }
            }
            
            public static function get_approval_invitation($document_id,$invitation_id){
                
                return WP_E_Sig()->meta->get($document_id,'approval_invitation_'.$invitation_id);        
            }
    }
     
     
    
endif;