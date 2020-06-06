<?php 

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

?>

	<?php 
	// To default a var, add it to an array
	$vars = array(
		'awaiting_class' // will default $data['awaiting_class']
	);
	$this->default_vals($data, $vars);
	
	include($this->rootDir . ESIG_DS . 'partials/_tab-nav.php'); ?>

	<div style="padding:12px 0;">
    <div class="header_left">
    <p>
	<a class="add-new-h2" href="admin.php?page=esign-view-document"><?php _e('Add New Document','esig'); ?></a>
	</p>
    </div>
    
    <div class="header_right">
	<?php echo $data['esig_document_search_box']; ?>
	</div>
    </div>

	<?php 
        
         echo $data['message']; 
	
	 if(class_exists('WP_E_Notice'))
	 {
	 	$esig_notice = new WP_E_Notice();
   
  		 echo $esig_notice->esig_print_notice();
	 }
         
         echo do_action("esig_display_alert_message");
	 
   ?>
	
	<?php echo $data['loop_head']; ?>
	
	<div class="header_left">
	<ul class="subsubsub">
		<!--<li class="all"><a class="<?php echo $data['all_class']; ?>" href="<?php echo $data['manage_all_url']; ?>" title="View all documents">Active Documents</a> <span class="count">(<?php echo $data['document_total']; ?>)</span> |</li>-->
		<li class="awaiting"><a class="<?php echo $data['awaiting_class']; ?>" href="<?php echo $data['manage_awaiting_url']; ?>" title="View documents currently awaiting signatures">Awaiting Signatures <span class="count">(<?php echo $data['total_awaiting']; ?>)</span></a> |</li>
		<li class="draft"><a class="<?php echo $data['draft_class']; ?>" href="<?php echo $data['manage_draft_url']; ?>" title="View documents in draft mode">Draft <span class="count">(<?php echo $data['total_draft']; ?>)</span></a> |</li>
		<li class="signed"><a class="<?php echo $data['signed_class']; ?>" href="<?php echo $data['manage_signed_url']; ?>" title="View signed documents">Signed <span class="count">(<?php echo $data['total_signed']; ?>)</span></a> |</li>
		<li class="trash"><a class="<?php echo $data['trash_class']; ?>" href="<?php echo $data['manage_trash_url']; ?>" title="View documents in trash">Trash <span class="count">(<?php echo $data['total_trash']; ?>)</span></a></li>
		<?php echo $data['document_filters']; ?>
		
	</ul>
	</div>

	<div class="header_right">
	
	</div>
	
     <form name="esig_document_form" action="" method="post">
     
	<div class="esig-documents-list-wrap">
		<table cellspacing="0" class="wp-list-table widefat fixed esig-documents-list">
			<thead>
				<tr>
					<th class="check-column"><input name="selectall" type="checkbox" id="selectall" class="selectall" value=""></th>
					<th style="width: 245px;"><?php _e('Title','esig'); ?> </th>
                                        
                                        <?php 
                                          $isArray = array('stand_alone','esig_template');
                                          $docStatus = esigget('document_status');
                                          if(in_array($docStatus, $isArray)){
                                        ?>
                                        <th style="width: 145px;"><?php _e('Created date','esig'); ?></th>
					<th style="width: 160px;"><?php _e('Last modified','esig'); ?></th>
					<th style="width: 100px;"><?php _e('Created by','esig'); ?></th>
                                          <?php } else { ?>
					<th style="width: 145px;"><?php _e('Signer(s)','esig'); ?></th>
					<th style="width: 160px;"><?php _e('Latest Activity','esig'); ?></th>
					<th style="width: 100px;"><?php _e('Date','esig'); ?></th>
                                          <?php } ?>
				</tr>
			</thead>
		
			<tfoot>
				<tr>
					<th class="check-column">
					<input name="selectall1" type="checkbox" id="selectall1" class="selectall" value=""></th>
					<th><?php _e('Title','esig'); ?></th>
                                        
					 <?php 
                                          
                                          if(in_array($docStatus, $isArray)){
                                        ?>
                                        <th style="width: 145px;"><?php _e('Created date','esig'); ?></th>
					<th style="width: 160px;"><?php _e('Last modified','esig'); ?></th>
					<th style="width: 100px;"><?php _e('Created by','esig'); ?></th>
                                          <?php } else { ?>
					<th style="width: 145px;"><?php _e('Signer(s)','esig'); ?></th>
					<th style="width: 160px;"><?php _e('Latest Activity','esig'); ?></th>
					<th style="width: 100px;"><?php _e('Date','esig'); ?></th>
                                          <?php } ?>
				</tr>
			</tfoot>
			<tbody>