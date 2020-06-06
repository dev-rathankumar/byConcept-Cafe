<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

?>
<p><a href="admin.php?page=esign-docs"><?php _e('Back to my documents', 'esig'); ?></a></p>

<div class="wp-esign-document-page">
	<h1><?php echo $data['[document_title']; ?>]</h1>
	<br />
	<?php echo $data['document_body']; ?>
</div>