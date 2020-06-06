<?php

/* Starting "Points" Callbacks */

function MCRA_PointsCallbackHandling( $request ){

    if(isset($_POST) && isset($_POST['access_key']) && $_POST['access_key'] !== "" && isset($_POST['user_id']) && $_POST['user_id'] !== "" && isset($_POST['type']) && $_POST['type'] !== ""){

         MCRA_CheckForApiKey($_POST['access_key']);

          $request_type = $_POST['type'];

          switch($request_type){

               case 'get':
                   return MCRA_GetPointsByUserID();
               case 'add':
                   return MCRA_AddPointsByUserID();
               default:
                   return "No request type defined";

          } 

     }else{

            return array('Error' => "Access Key, User ID and Request Type Required");

     }  

}

function MCRA_GetPointsByUserID(){

    if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] !== NULL){

        $user_id = $_REQUEST['user_id'];
        $ctype   = ( empty( $_REQUEST['ctype'] ) ? MYCRED_DEFAULT_TYPE_KEY : $_REQUEST['ctype'] );
        $balance = mycred_get_users_balance( $user_id, $ctype );

        return $balance;

    }else{

        return array('Error' => "User ID not found");

    }
     
 }
  
function MCRA_AddPointsByUserID() {

    if( isset($_REQUEST['user_id']) ) {
        $exists = user_exist( $_REQUEST['user_id'] );
    }

     if( $exists ) {

        if( !isset($_REQUEST['reference']) || empty($_REQUEST['reference']) ) {

            return array('Error' => "No Refrence Found");

        }
        else if( !isset($_REQUEST['user_id']) || empty($_REQUEST['user_id']) ) {

            return array('Error' => "No User ID Found");

        }
        else if(!isset($_REQUEST['amount']) || empty($_REQUEST['amount']) ) {

            return array('Error' => "No Amount Found");

        }
        else if( !isset($_REQUEST['entry']) || empty($_REQUEST['entry']) ) {

            return array('Error' => "No Entry Found");

        }
        else {

            $ctype = ( empty( $_REQUEST['ctype'] ) ? MYCRED_DEFAULT_TYPE_KEY : $_REQUEST['ctype'] );
            return array(
                'status' => mycred_add(
                    $_REQUEST['reference'], 
                    $_REQUEST['user_id'], 
                    $_REQUEST['amount'], 
                    $_REQUEST['entry'],
                    0,
                    '',
                    $ctype
                )
            );

        }

    }
    else {

        return array('Error' => "No user found with provided User ID");

    }

 }

 /* Ending "Points" Callbacks */


 /* Starting "Ranks" Callbacks */

function MCRA_RanksCallbackHandling($request){

    if( isset($_POST) && isset($_POST['access_key']) ) {

        MCRA_CheckForApiKey($_POST['access_key']);

        $request_type = $_POST['type'];

        switch( $request_type ) {

                case 'get':
                    if(!isset($_POST['user_id']) || $_POST['user_id'] == 0 || $_POST['user_id'] == '') {

                        return MCRA_GetAllRanks();

                    }
                    else {

                        return MCRA_GetRanksByUserID();
                    }

                default:
                    return MCRA_GetRanksByUserID();
        }

    }
    else {

        return array('Error' => "Access Key and User ID required");

    }  

}

function MCRA_GetAllRanks(){

    $ctype     = ( empty( $_REQUEST['ctype'] ) ? MYCRED_DEFAULT_TYPE_KEY : $_REQUEST['ctype'] );
    $all_ranks = mycred_get_ranks( 'publish', -1, 'ASC', $ctype );
    $ranks_to_return = [];

    $count = 0;
    foreach ( $all_ranks as $rank ) {
        $rank_data = array(

            'id' =>  $rank->post_id,
            'title' => $rank->title,
            'max' => $rank->maximum,
            'min' => $rank->minimum,
            'logo_url' => $rank->logo_url,
            'point_type' => $rank->point_type->singular

        );

        array_push($ranks_to_return, $rank_data);
        $count++;
    }

    return $ranks_to_return;
}

function MCRA_GetRanksByUserID(){

    if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] !== NULL){

        try {

            $user_id = $_REQUEST['user_id'];
            $ctype = ( empty( $_REQUEST['ctype'] ) ? MYCRED_DEFAULT_TYPE_KEY : $_REQUEST['ctype'] );
            $ranks = mycred_get_users_rank( $user_id, $ctype );

            $ranks_to_return = array(

                    'id' =>  $ranks->post_id,
                    'title' => $ranks->title,
                    'max' => $ranks->maximum,
                    'min' => $ranks->minimum,
                    'logo_url' => $ranks->logo_url,
                    'point_type' => $ranks->point_type->singular
            );

            return $ranks_to_return;

        } catch (\Error $ex) {

            return array('Error' => "Please activate Ranks from add-ons. Refer to MyCred documentation for more information");

        }

    }else{

        return array('Error' => "User ID not found");

    }
    
}

/* Ending "Ranks" Callbacks */


/* Starting "Badges" Callbacks */

function MCRA_BadgesCallbackHandling($request){

    if( isset($_POST) && isset($_POST['access_key']) && isset($_POST['type']) ) {

        MCRA_CheckForApiKey($_POST['access_key']);

        $request_type = $_POST['type'];

        switch($request_type){

            case 'get':

                if( !isset($_POST['user_id']) || $_POST['user_id'] == 0 || $_POST['user_id'] == '' ) {

                    return MCRA_GetAllBadges();

                }
                else {

                    return MCRA_GetBadgesByUserID();

                }

            case 'assign':
                return MCRA_AssignBadgesByUserID();
            case 'unassign':
                return MCRA_UnAssignBadgesByUserID();
            default:
                return MCRA_GetBadgesByUserID();

        }

    }
    else {

        return array('Error' => 'Access Key, User ID and Request Type required');

    }  

}

function MCRA_GetBadgesByUserID(){

    if( isset($_REQUEST['user_id']) && $_REQUEST['user_id'] !== NULL ){

        try {

            $user_id = $_REQUEST['user_id'];
            $badge_ids = mycred_get_users_badges( $user_id );
            $badges_to_return = [];
            foreach ( $badge_ids as $key => $value ) {
                $badge = mycred_get_badge( $key, 0 );
                $main_image = MCRA_GetSrc($badge->main_image);
                $level_image = MCRA_GetSrc($badge->level_image);
                unset($badge->main_image);
                unset($badge->level_image);
                $badge->main_image = $main_image;
                $badge->level_image = $level_image;
                array_push( $badges_to_return, $badge );
            }

            return $badges_to_return;

        } 
        catch (\Error $ex) {

            return array('Error' => "Please activate Badges from add-ons. Refer to MyCred documentation for more information");

        }

    }
    else {

        return array('Error' => "User ID not found");

    }
    
}

function MCRA_GetAllBadges(){

    $badge_ids =  mycred_get_badge_ids();
    $badges_to_return = [];
    foreach ($badge_ids as $badge_id){

            $badge_details = mycred_get_badge( $badge_id, 0 );
            $main_image = MCRA_GetSrc($badge_details->main_image);
            $level_image = MCRA_GetSrc($badge_details->level_image);
            unset($badge_details->main_image);
            unset($badge_details->level_image);
            $badge_details->main_image = $main_image;
            $badge_details->level_image = $level_image;

            array_push($badges_to_return, $badge_details);
        
    }

    return $badges_to_return;

}

function MCRA_AssignBadgesByUserID(){

    if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] !== NULL && isset($_REQUEST['badge_id']) && $_REQUEST['badge_id'] !== NULL && isset($_REQUEST['badge_level']) && $_REQUEST['badge_level'] !== NULL) {

        $level = absint($_REQUEST['badge_level']);
        if (mycred_assign_badge_to_user($_REQUEST['user_id'], $_REQUEST['badge_id'], $level) == 'true'){

            return array('Response' => "The Badge ".$_REQUEST['badge_id']."has been assigned to user ".$_REQUEST['user_id']);

        }

    }else{

        return array('Error' => "User ID, Badge ID and Badge Level are required. Refer to MyCred documentation for more information");

    }

}

function MCRA_UnAssignBadgesByUserID(){

    if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] !== NULL && isset($_REQUEST['badge_id']) && $_REQUEST['badge_id'] !== NULL && isset($_REQUEST['badge_level']) && $_REQUEST['badge_level'] !== NULL) {

        $badge = mycred_get_badge($_REQUEST['badge_id']);
        if($badge->divest($_REQUEST['user_id']) == 'true'){

            return array('Response' => "The Badge ".$_REQUEST['badge_id']."has been unassigned. User ".$_REQUEST['user_id']);

        }

    }else{

        return array('Error' => "User ID, Badge ID are required. Refer to MyCred documentation for more information");

    }

}

/* Ending "Badges" Callbacks */


/* Start "References" Callbacks */

function MCRA_ReferencesCallbackHandling(){

    if(isset($_POST) && isset($_POST['access_key']) && $_POST['access_key'] !== "" && isset($_POST['type']) && $_POST['type'] !== ""){

        MCRA_CheckForApiKey($_POST['access_key']);
        $request_type = $_POST['type'];

        switch($request_type){

            case 'get':
                return MCRA_GetAllReferences();
            default:
                return MCRA_GetAllReferences();

        }

    }else{

        return array('Error' => "Access Key and Request Type required");

    }

}


function MCRA_GetAllReferences(){

    return array('Response' => mycred_get_all_references());

}

/* Ending "References" Callbacks */

function MCRA_CheckForApiKey($Apikey){

    $rest_api_settings = get_option('mycred_rest_api');
    $api_key_stored = $rest_api_settings['api_key'];

    if($api_key_stored !== $Apikey){

        return array('Error' => 'Return valid Access Key');
        exit;

    }

}

function user_exist( $user_id = '' ) {

    if ( $user_id instanceof WP_User ) {
        $user_id = $user_id->ID;
    }
    return (bool) get_user_by( 'id', $user_id );

}

function MCRA_GetSrc($html){

    $doc = new DOMDocument();
    $doc->loadHTML($html);
    $xpath = new DOMXPath($doc);
    return $xpath->evaluate("string(//img/@src)");

}
