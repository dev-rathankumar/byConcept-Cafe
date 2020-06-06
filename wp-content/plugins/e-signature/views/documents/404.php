<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

?>
<?php if (array_key_exists('message', $data)) { echo $data['message'];} ?>


<script type="text/javascript">
(function($) {
	$( document ).ready(function() {
	  $('.footer-agree').hide();
	});
})(jQuery);
</script>