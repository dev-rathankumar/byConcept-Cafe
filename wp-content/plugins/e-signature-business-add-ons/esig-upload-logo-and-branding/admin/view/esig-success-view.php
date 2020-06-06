

 <!--<div style="margin: 10px;"><h3>  <?php _e('This section lets you customise the WP E-Signature Success Page','esig') ?></h3></div>-->

	
<table id="esign-success-view" class="form-table esig-settings-form esig-settings-wrap" >
    
	<tbody>
        <tr>
			<th><label for="success_paragraph_text"><?php _e('Success Paragraph Text','esig') ?></label></th>
				<td><span class="esig-description"> <?php _e('The text to appear in the header of signer success page.','esig') ?></span>
				<textarea id="esig_success_paragraph_text" name="esig_success_paragraph_text"  rows="5" cols="100%"><?php if(!isset($data['esig_success_page_paragraph_text']) && $data['esig_success_page_paragraph_text'] == false ){ 
				echo "Excellent work! You signed {document_title} like a boss.";
				} else {echo htmlspecialchars(stripslashes($data['esig_success_page_paragraph_text']));  }?></textarea></td>
		</tr>
    	<tr>
			<th><label for="esig_success_image" id="esig_success_image_label"><?php _e('Success Image','esig') ?></label></th>
			<td><input type="text" name="esig_branding_success_image" id="esig_branding_success_image" value="<?php echo $data['esig_success_page_image']; ?>" class="regular-text" /><br />
			<span class="description"><?php _e('Enter a URL to an image you want to show in the success page header . Upload your image using the','esig') ?> <a href="#" id="esig_success_image_upload"><?php _e('Media Uploader','esig') ?></a></span></td>
		</tr>
		
		 <tr>
		    <th>&nbsp;</th>
			<td>
            <?php
                 $alignment=!empty($data['esig_success_img_alignment'])?$data['esig_success_img_alignment']:'center';
            ?>
            <input type="radio" name="esig_success_img_alignment" value="left" <?php if($alignment=='left'){ echo 'checked';} ?>> Align Left
            <input type="radio" name="esig_success_img_alignment" value="center" <?php if($alignment=='center'){ echo 'checked';} ?>> Align Center
            <input type="radio" name="esig_success_img_alignment" value="right" <?php if($alignment=='right'){ echo 'checked';} ?>> Align Right
			</td>
    	</tr>
    	
		<tr>
		    <th>&nbsp;</th>
			<td><label for="">
					<input name="esig_success_image_show" id="esig_success_image_show" type="checkbox" value="1" <?php echo $data['esig_success_page_image_disable']; ?>> 
                    <?php _e('Do not display a success image (or icon) when a document is signed','esig') ?> </label>
					
			</td>
    	</tr>
		

	</tbody>
</table>
