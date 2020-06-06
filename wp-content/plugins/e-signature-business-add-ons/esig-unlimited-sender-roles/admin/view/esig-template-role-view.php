
<table class="form-table">
	<tbody>
		
		<tr>
			<td width="20px"><a href="#" class="tooltip">

					<img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/help.png" height="20px" width="20px" align="left" />

					<span>
					<?php _e('This option lets you easily give document sending permissions to all users of an entire WordPress user role.','esig-usr'); ?>
					</span>
					</a></td>
			<td>
					
					<p class="esig-chosen-drop">
					
					
					<label><b><?php _e('Select one (or multiple) ROLES that can send documents','esig-usr'); ?> </b></span></label>

						<select name="esig_roles_option[]" style="width:500px;" tabindex="9" data-placeholder="Choose a Option..." multiple class="esig-select2">

							<?php echo $template_data['roles_option'] ; ?>

							
						</select> 	
					</p>	
				
	   			
			</td>
		</tr>

		<tr>
			<td width="20px"><a href="#" class="tooltip">

					<img src="<?php echo $data['ESIGN_ASSETS_DIR_URI']; ?>/images/help.png" height="20px" width="20px" align="left" />

					<span>
					<?php _e('Want to give document sending access to one single user?  This is space to turn that dream into reality!','esig-usr') ?>
					</span>
					</a></td>
					<td>
					
					<p class="esig-chosen-drop">
					<label><b><?php _e('Select one (or multiple) USERS that can send documents') ?> </b></span></label>

						<select name="esig_roles_user_option[]" style="width:500px;" tabindex="9" data-placeholder="<?php __('Choose a Option...','esig-usr') ?>" multiple class="esig-select2">
									  

							<?php echo $template_data['user_roles_option']; ?>

							
						</select> 	
					</p>	
				
	   			
			</td>
		</tr>
		
		
	</tbody>
</table>

