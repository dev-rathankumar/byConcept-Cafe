<?php 
if ( ! defined( 'ABSPATH' ) ) { 

	exit; // Exit if accessed directly

}

?>

<?php include($this->rootDir . ESIG_DS . 'partials/_tab-nav.php'); ?>

<?php

 echo $data['message']; 

 $esig_notice = new WP_E_Notice();

  echo $esig_notice->esig_print_notice();		

 ?>



<div id="esig-settings-container">

    <div id="esig-settings-col2">

<h4><?php _e('E-Signature Admin Settings', 'esig' );?></h4>

<form name="settings_form" class="settings-form" method="post" action="<?php echo $data['post_action']; ?>">	

<table class="form-table esig-settings-form">

	<tbody>

    	<tr>

			<th><label for="first_name" id="first_name_label"><?php _e('Admin First Name<span class="description"> (required)</span>', 'esig' );?></label></th>

			<td><input type="text" name="first_name" id="first_name" value="<?php if (array_key_exists('first_name', $data)) { echo $data['first_name']; } ?>" class="regular-text" /></td>

		</tr>

		

		<tr>

			<th><label for="last_name" id="last_name_label"><?php _e('Admin Last Name<span class="description"> (required)</span>', 'esig' );?></label></th>

			<td><input type="text" name="last_name" id="last_name" value="<?php if (array_key_exists('last_name', $data)) { echo $data['last_name']; } ?>" class="regular-text" /></td>

		</tr>



		<tr>

			<th><label for="adminemail" id="user_email_label"><?php _e('Admin Email<span class="description"> (required)</span>', 'esig' );?></label></th>

			<td><input type="text" name="user_email" id="user_email" size="30" value="<?php if (array_key_exists('user_email', $data)) { echo $data['user_email']; } ?>" class="regular-text" />

			<br />

        	<span class="description"><?php _e('Enter the E-Signature admin email address that you would like all document communications sent.', 'esig' );?></span></td>

		</tr>



		<tr>

			<th><label for="user_title"><?php _e('Organization\'s Name<span class="description"> (required)</span>', 'esig' );?></label></th>

				<td><input type="text" name="user_title" id="user_title" maxlength="64" size="30" value="<?php if (array_key_exists('user_title', $data)) { echo $data['user_title']; } ?>" class="regular-text" /></td>

		</tr>

		

		<tr>

    		<th><?php _e('Draw signature with a mouse (for future documents)<span class="description"> (required)</span>', 'esig' );?></th>

    		<td>

			

				<div class="signature-wrapper-displayonly">

					

					<canvas id="signatureCanvas2" class="sign-here pad <?php echo $data['signature_classes']; ?>" width="420" height="100" ></canvas>

				</div>

				

				<span id="admin-signature" style="display:none">

				

					<!-- -->

					

						

				

					<div id="tabs">

					

						<div class="signature-tab">

								

						

						<article id="adopt">

      

									<header class="ds-title p">

									<label for="full-name"><?php _e('Please Confirm full name and signature.','esig'); ?></label>

									</header>

									<div class="full-name">

									  <div class="wrapper">

										<div class="text-input-wrapper">

										  <input id="esignature-in-text" value="<?php if (array_key_exists('output_type', $data)) { echo $data['output_type']; } ?>" name="esignature_in_text" class="required text-input" maxlength="64" type="text">

										</div>

									  </div>

									</div>

									<a href="#" id="esig-type-in-change-fonts"><?php _e('Change fonts','esig'); ?></a>

							 <div class="clear-float"></div>

						</article>

							

  						<ul>

  							<li><a href="#tabs-1" id="esig-tab-draw" class="selected"><?php _e('Draw Signature','esig') ; ?> <br />	

						</a></li>

  							<li><a href="#tabs-2" id="esig-tab-type"><?php _e('Type In Signature','esig'); ?><br />

						</a></li>

  				

  						</ul>

						</div> <!-- type signature end here -->

  			<div id="tabs-1">

					

					<div class="signature-wrapper">

						

						<span class="instructions"><?php _e('Draw your signature with <strong>your mouse, tablet or smartphone</strong>', 'esig' );?></span>

						<a href="#clear" class="clearButton" style="margin-bottom:25px;"><?php _e('Clear', 'esig' );?></a>

						<canvas  id="signatureCanvas" class="sign-here pad <?php echo $data['signature_classes']; ?>" width="380" height="100" ></canvas>

						<input type="hidden" name="output" class="output" value='<?php if (array_key_exists('output', $data)) { echo $data['output']; } ?>'/>

                        

						<div class="description">

						

						<?php _e('I agree this is a legal representation of my signature for all purposes 

						just the same as a pen-and-paper signature','esig'); ?>

						

						</div>

						

						<button class="button saveButton" data-nonce="<?php echo $data['nonce']; ?>"><?php _e('Insert Signature','esig' );?></button>



				

					</div>

			</div>

  			<div id="tabs-2">

			

			 <div > 

							<!-- type esignature start here -->

					 <div id="type-in-signature">

				

						<div id="esig-type-in-preview" class="pad" width="450px" height="100px">

						

						<?php 

							

								if (array_key_exists('output_type', $data)) 

									{ 

										$wp_user_id = WP_E_Sig()->user->getCurrentUserID();

										

										$font_choice =  WP_E_Sig()->setting->get('esig-signature-type-font'.$wp_user_id);

										

										 echo '<input type="hidden" name="font_type" id="font-type" value="'. $font_choice .'">';

										

									} 

									

						?>

						

						</div>



						<div id="esig-type-in-controls">

							<div>

								<div  id="type-in-text-accept-signature-statement">

									<label for="type-in-text-accept-signature">

										<span class="signature"><?php _e('I understand this is a legal representation of my signature','esig'); ?></span>

									</label>

								</div>

								<div>

									<a id="esig-type-in-text-accept-signature" class="blue-sub alt button-appme button" href="#">

										

										<span class="esig-signature-type-add"><?php _e('Adopt & Sign','esig'); ?></span>

									</a>	

								</div>

							</div>

						</div>

						<div class="clearfix"></div>

					

			</div> 

			

			</div>

			

					</div>



				 </div>    

				</span>

    		</td>

    	</tr>







		<tr>	

  			<th><label for="esig_super_admin" id="esig_super_admin">Super-Admin</label>

							

			</th>

			

			<td>

			

				<a href="#" class="tooltip">

					<img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/help.png" height="20px" align="left" />

					<span>

					<?php _e('A Super Admin is the main document sender/admin user that has executive document sending privileges.  To add additional document senders you will need our Premium "Unlimited Document Senders" Add-On.', 'esig' ); ?>

					</span>

				</a>

				

				<?php echo $data['esig_administrator']; ?>

				<br>



				<div id="esig-confirm-dialog" style="display:none;">

					<div class="esig-confirm-dialog-content"><?php _e('

						Handing over Super-Admin ownership is serious business. 

Once you assign <span id="esig_selected_admin"> </span>as super admin you will no longer be able 

to send and preview documents unless you have the Additional E-Signature Roles Add-On. ', 'esig' ); ?>

					</div>

				</div>



        	</td>	

		</tr>

        <?php 

         

            $wp_user_id = get_current_user_id();

            

            $settings = new WP_E_Setting();

            

            $admin_user_id=  WP_E_Sig()->user->esig_get_super_admin_id();

		

		    if($wp_user_id == $admin_user_id || $admin_user_id==null){

        

        ?>



		<tr>	

  			<th><label for="default_display_page" id="default_display_page">E-Signature Page</label>

							

			</th>

			

			<td>

			

				<a href="#" class="tooltip">

					<img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/help.png" height="20px" align="left" />

					<span>

					<?php _e('WP E-Signature requires one page of your website to host the document signing application.  If a user accesses this page directly they will see an error message. Each document is protected with a randomly generated user specific url that is emailed to each signer.', 'esig' ); ?>

					</span>

				</a>

				

				<?php echo $data['post_select']; ?><br>

        	</td>	

		</tr>

        

        <tr>

		    <th></th>

			<td><label for="">

    		<a href="#" class="tooltip">

					<img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/help.png" height="20px" width="20px" align="left" />

					<span>

					<?php _e('Hide the "E-Signature" page from main navigation menu.', 'esig' ); ?>

					</span>

					</a>

                    <?php $esig_default_page_hide=$data['esig_default_page_hide'] ; if($esig_default_page_hide==1){ $eisg_page = "checked"; } else { $eisg_page = ""; } ?>

					<input name="esig_hide_page" id="esig_hide_page" type="checkbox" value="1" <?php echo $eisg_page ;  ?>> <?php _e('Hide E-Signature default page from main navigation menu', 'esig' ); ?></label>

        

			</td>

    	</tr>

		<!-- timezone settings start here -->
		<tr>

		    <th><?php _e("Timezone Settings","esig");?></th>

			<td><label for="">

    		<a href="#" class="tooltip">

					<img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/help.png" height="20px" width="20px" align="left" />

					<span>

					<?php _e('E-signature timezone settings. ', 'esig' ); ?>

					</span>

					</a>
					
					<select id="esig_timezone_string" name="esig_timezone_string" class="esig-select2"  aria-describedby="timezone-description" style="width: 288px">
<?php  

     echo $data['esig_timezone'] ; 

 ?>
 </select>
			</label>

        <p class="description" id="timezone-description" style="margin-left: 50px;"><?php _e("Choose a city in the same timezone as you.","esig");?></p>

			</td>

    	</tr>
		
		<!-- timezone settings end here -->
                
                <!-- Terms of use settings start here -->
		<tr>

		    <th><?php _e("Terms of use language","esig");?></th>

			<td><label for="">

    		<a href="#" class="tooltip">

					<img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/help.png" height="20px" width="20px" align="left" />

					<span>

					<?php _e('E-signature Terms of use language settings. ', 'esig' ); ?>

					</span>

					</a>
					
					<select id="esig_tou_string" name="esig_tou_string" class="esig-select2"  aria-describedby="tou-description" style="width: 288px">
<?php  

     echo $data['esig_terms_of_use'] ; 

 ?>
 </select>
			</label>

        

			</td>

    	</tr>
		
		<!-- Terms of use settings end here -->

		<tr>

		    <th><?php _e("Dashboard Settings","esig");?></th>

			<td><label for="">

    		<a href="#" class="tooltip">

					<img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/help.png" height="20px" width="20px" align="left" />

					<span>

					<?php _e('Hide the "E-Signature" menu from all users that do not have direct access to WP E-Signature.', 'esig' ); ?>

					</span>

					</a>

					<input name="hide_esign" id="esign_hide" type="checkbox" value="1" <?php echo $data['esign_hide_data']; ?>> <?php _e('Hide E-Signature from dashboard for users without access', 'esig' ); ?></label>

        

			</td>

    	</tr>

		

		<tr>

    		<th><?php _e('SSL Security', 'esig' ); ?></th>

    		<td>

		

			<label for=""><a href="#" class="tooltip">

					<img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/help.png" height="20px" align="left" />

					<span>

					<?php _e('This feature forces SSL (HTTPS) on all WP E-Signature signing pages (a valid SSL Certificate is required).', 'esig' ); ?>

					</span>



				</a> 
                            
                            <input name="force_ssl_enabled" title="hello testing" id="esign_ssl" type="checkbox" value="1" <?php echo $data['ssl_checked']; ?>> Force secure signing. <!--<a href="">Purchase an SSL certificate</a>  from WP E-Signature starting at $7.95 / yr.</label>-->

        <span class="description"><?php _e('Force SSL (HTTPS) on the signing pages (an SSL Certificate is required).', 'esig' ); ?></span> </td>



				</a>

			</td>



    	</tr>

		<?php }  // super admin access end here ?>

		<tr>

    		<th></th>

    		<td><label for=""><i><?php _e('By clicking "Save Settings" you are agreeing to the Approve Me & WP E-Signature <a href="admin.php?page=esign-terms-general">Terms of Service</a> and <a href="admin.php?page=esign-privacy-general">Privacy Policy</a>.', 'esig' ); ?></i></td>

    	</tr>

	</tbody>

</table>



	<p>

		<input type="submit" name="submit"  class="button-appme button" value="Save Settings" />

	</p>

</form>



 

	</div>



    <div id="postbox-container-1" class="esig-postbox-container">

    <?php echo $data['extra_contents']; ?>
        
    </div>>
   

<?php $tail= apply_filters('esig-document-footer-content', '',array()); 

      echo $tail ; 

?>

