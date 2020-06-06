<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

?>

	<div class="col-sm-6"  <?php if (array_key_exists('esig-tooltip', $data)) { echo $data['esig-tooltip']; } ?>>
	<div style="pointer-events: none;" class="signature-wrapper-displayonly-signed" id="signature-<?php echo $data['user_id']; ?>">
		
		
		<div class="signed rtl-signed">
		
		<?php 
		
		$esignature = new WP_E_Signature();
                $class ='';
		
		if(isset($data['signature']) && $data['signature']=="yes")
		{
			$width = $esignature->getScreenWidth($data['user_id'],$data['signed_doc_id']);
                        $input_name  = esigget("esig_aasm",$data);
                       
                        if($input_name=="auto_signature"){
                          
                           $signature_image = $esignature->display_signature($data['user_id'],$data['signed_doc_id'],$data['esig_sig_nonce'],false,"admin_signature"); 
                        }
                        else {
                            $signature_image = $esignature->display_signature($data['user_id'],$data['signed_doc_id'],$data['esig_sig_nonce']);
                            $class ='esig-user-signature-meta';
                        }
                        
			//echo '<img class="signature-image" src="'. $signature_image . '" width="'. $width .'" height="100px">';
                        if(wp_is_mobile()){
                            $style ='style="margin-left:25px;"';
                        }
                        else {
                            $style ='style="margin-top:50px;"';
                        }
                        echo '<img class="signature-image" src="'. $signature_image . '" width="auto" height="100px">';
			
		}
		elseif(isset($data['signature']) && $data['signature']=="old-aams")
		{
                    
                     $input_name  = esigget("esig_aasm",$data);
                       
                        if($input_name=="auto_signature"){
                          
                           $signature_image = $esignature->display_signature($data['user_id'],$data['signed_doc_id'],$data['esig_sig_nonce'],false,"admin_signature"); 
                        } else {
                            $esignature->esign_set_json($data['user_id'],$data['signature'],true);
                            $signature_image = ESIGN_DIRECTORY_URI .'lib/sigtoimage.php?uid='. $data['user_id'] .'&owner_id='. $data['user_id'] .'&doc_id='. $data['signature'] .'&esig_verify='. $data['esig_sig_nonce'];
                        }
		    
		    echo '<img class="signature-image" src="'. $signature_image .'" width="320px" height="100px">';
		}
		
		
		?>
		
		
		</div>
		<input type="hidden" name="esignature_in_text_signed"  maxlength="64" class="esignature-in-text-signed" value="<?php if (array_key_exists('output_type', $data)) { echo $data['output_type']; } ?>"  placeholder="Type signature">
		<input type="hidden" name="font_type_signed" class="font-type-signed" value="<?php if (array_key_exists('font_type', $data)) { echo $data['font_type']; } ?>">
		
			
	</div>
	<div class="signature-meta <?php echo $class ; ?> rtl-signature-meta">
		<p >
			<?php if (array_key_exists('esig-awaiting-sig', $data)){ echo $data['esig-awaiting-sig'];} ?>
			<?php if (array_key_exists('by_line', $data)){   echo $data['by_line'] . ' ' . $data['user_name']; } ?><br/>
			
			<?php if (array_key_exists('sign_date', $data)){ echo $data['sign_date'];} ?>
			
			
			
		</p>
	</div>
</div>

