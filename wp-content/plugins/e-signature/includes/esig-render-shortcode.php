<?php

/*
 *  Render e-sigature shortcode content on signing and save it to database. 
 *  
 */

function esig_render_shortcode($doc_id) {

    $docType = WP_E_Sig()->document->getDocumentType($doc_id);
    if ($docType != "stand_alone") {
        return false;
    }
    $documentContentUnfilter = WP_E_Sig()->document->esig_do_shortcode($doc_id);

    $document_content = WP_E_Sig()->signature->encrypt(ENCRYPTION_KEY, $documentContentUnfilter);
    $document_checksum = sha1($doc_id . $documentContentUnfilter);
    Esign_Query::_update("documents", array("document_content" => $document_content, "document_checksum" => $document_checksum), array("document_id" => $doc_id), array("%s", "%s"), array("%d"));
    //Esign_Query::_update("documents",array("document_content"=>$document_content),array("document_id"=>$doc_id),array("%s"),array("%d"));
}

//add_action("esig_agreement_cloned_from_stand_alone", "esig_render_shortcode", 1, 1);

if (!function_exists('esig_replace_image')) {

    function esig_replace_image($contentToReplace) {

        if (empty($contentToReplace)) {

            return $contentToReplace;
        }

        $xpath = simplexml_import_dom(@DOMDocument::loadHTML($contentToReplace));
        $images = $xpath->xpath('//img');
        if (empty($images) || !is_array($images)) {
            return $contentToReplace;
        }



        foreach ($images as $img) {

            $imagePath = $img['src'];
            if (is_base64($imagePath)) {
                
                continue;
            }
           
            // grab image content here 
            /* $imageContent = WP_E_Sig()->signature->esig_get_contents($imagePath);
              $imageType = $audit_trail_helper->get_image_type($imageContent, $imagePath);
              $newImage = "data:image/" . $imageType . ";base64," . base64_encode($imageContent); */
            $newImage = esig_encoded_image($imagePath);

            $contentToReplace = str_replace($imagePath, $newImage, $contentToReplace);
        }


        return $contentToReplace;
    }

}


if (!function_exists("esig_encoded_image")) {

    function esig_encoded_image($imagePath) {

        $relativePath = wp_make_link_relative($imagePath);
        // echo file_get_contents($imagePath);

        $imageContent = WP_E_Sig()->signature->esig_get_contents($imagePath);
        

        if (empty($imageContent)) {
            $imageContent = WP_E_Sig()->signature->esig_get_contents(ABSPATH . $relativePath);
        }

        if (empty($imageContent)) {
            $wpcontentDir = basename(WP_CONTENT_DIR);
            list($firstPart, $secondPart) = explode($wpcontentDir, $imagePath);
            $imagePath = content_url() . $secondPart;
            $imageContent = WP_E_Sig()->signature->esig_get_contents($imagePath);
        }

        $audit_trail_helper = new WP_E_AuditTrail();
        $imageType = $audit_trail_helper->get_image_type($imageContent, $imagePath);
       
        $newImage = "data:image/" . $imageType . ";base64," . base64_encode($imageContent);
        return $newImage;
    }

}


if (!function_exists('is_base64')) {

    function is_base64($s) {
        
       
        //return (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s);
       /* if (strpos($s,"data:image/") !== false) {
           
            return true;
              
        }
        return false;*/
        return (bool) preg_match('/^data:image/', $s);
    }

}


