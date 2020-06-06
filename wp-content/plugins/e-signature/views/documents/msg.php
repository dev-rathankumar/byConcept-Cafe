<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

?>

			
<tr id="post-<?php if (array_key_exists('document_id', $data)) { echo $data['document_id']; } ?>" class="post-<?php if (array_key_exists('document_id', $data)) { echo $data['document_id']; } ?> type-post status-publish format-standard hentry category-uncategorized <?php if (array_key_exists('alternate_class', $data)) { echo $data['alternate_class']; } ?> iedit author-self level-0" valign="top">
	<th scope="row" class="check-column">
		
	</th>
		
	<td class="post-title page-title column-title">
		<?php if (array_key_exists('no_record', $data)) { echo $data['no_record'];} ?>
	</td>

	<td></td>
	<td></td>
	<td></td>
</tr>