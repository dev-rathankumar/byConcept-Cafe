
<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

?>


<div class="esig-container">
	
	<div class="navbar-header navbar-left agree-container">
	
		<span class="agree-text"> <a href="<?php echo home_url(); ?>" class="esig-sitename"><?php _e('Back to Main Site', 'esig' );?></a></span>
		
	</div>

   <?php 
   if(!wp_is_mobile())
   {
   	?>
  
	<div class="nav navbar-nav navbar-right footer-btn">
	
		<?php if (array_key_exists('print_button', $data)) { echo $data['print_button']; } ?>
		<?php if (array_key_exists('pdf_button', $data)) { echo $data['pdf_button'];} ?>
		
		</div>
	<?php } ?>
</div>