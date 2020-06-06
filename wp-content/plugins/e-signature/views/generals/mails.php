<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

?>

<?php 
include($this->rootDir . ESIG_DS . 'partials/_tab-nav.php'); 

// To default a var, add it to an array
	$vars = array(
		'other_form_element', // will default $data['other_form_element']
		'pdf_options', 
		'active_campaign_options'
	);
	$this->default_vals($data, $vars);
?>
<div class="esign-main-tab">

 <a class="mails_link <?php echo $data['link_active']; ?>" href="admin.php?page=esign-mails-general"><?php _e('General Option', 'esig'); ?></a> 
 
 <?php if(is_esig_super_admin()){?>
 | <a class="mails_link" href="admin.php?page=esign-email-general"><?php _e('E-mail Sending Options', 'esig'); ?></a>  
 <?php } ?>
 


</div>	

 <?php echo $data['message']; ?>

<form name="settings_form" class="settings-form" method="post" action="<?php echo $data['post_action']; ?>">
    
<table class="form-table">
	<tbody>
		
		<tr>
			<td> 
				<?php echo $data['other_form_element']; ?> 
			</td>
		</tr>
	
            <?php  if(array_key_exists('mails_extra_content',$data)){ echo $data['mails_extra_content'];} ?>
                
                
           
                
	</tbody>
</table>
    
 <?php do_action("esig_mails_general_options"); ?>		

	<p>
		<input type="submit" name="mails-submit" class="button-appme button" value="Save Settings" />
	</p>
</form>
