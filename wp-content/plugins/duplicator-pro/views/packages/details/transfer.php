<?php
defined("ABSPATH") or die("");
DUP_PRO_U::hasCapability('export');
/* @var $package DUP_PRO_Package */
$storage_list = DUP_PRO_Storage_Entity::get_all();
$storage_list_count = count($storage_list);

$transfer_occurring = (($package->Status >= DUP_PRO_PackageStatus::STORAGE_PROCESSING) && ($package->Status < DUP_PRO_PackageStatus::COMPLETE));

$view_state = DUP_PRO_UI_ViewState::getArray();
$ui_css_transfer_log = (isset($view_state['dup-transfer-transfer-log']) && $view_state['dup-transfer-transfer-log']) ? 'display:block' : 'display:none';
?>


<style>
	h3 {margin:10px 0 5px 0}
	div.transfer-panel {padding: 20px 5px 10px 10px;}
	div.transfer-hdr { border-bottom: 1px solid #dfdfdf; margin: 0 0 0 0}

	div#step1-section {margin: 5px 0 40px 0}
	div#step1-section label {font-weight: bold; padding-right: 20px}

	div#step2-section {margin: 5px 0 40px 0}
	div#location-quick-opts {display:none}
	div#location-quick-opts input[type=text] {width:300px}

	div#step3-section {margin: 5px 0 40px 0}
	div#dpro-progress-bar-area {width:300px; margin:5px auto 0 auto; ext-align: center}
	div.dpro-active-status-area { display: none; }
	
	#dup-pro-stop-transfer-btn { margin-top: 10px; }
	button.dpro-btn-stop {width:150px !important}	
</style>


<div class="transfer-panel">

	<div class="transfer-hdr">
		<h2><i class="fa fa-arrow-circle-right"></i> <?php DUP_PRO_U::esc_html_e('Manual Transfer'); ?></h2>
	</div>

	<!-- ===================
	STEP 1 Old -->
	<div id="step1-section" style="display:none">
		<h3><?php DUP_PRO_U::esc_html_e('1: Select Files') ?></h3>
		<input type="checkbox" checked="checked" id="files-installer" /> <i class="fa fa-bolt fa-sm"></i> 
		<label for="files-installer"><?php echo DUP_PRO_U::__('Installer') . ' ' . DUP_PRO_U::byteSize($package->Installer->Size); ?> </label>

		<input type="checkbox" checked="checked" id="files-archive" /> <i class="fa fa-archive fa-sm"></i>
		<label for="files-archive"><?php echo DUP_PRO_U::__('Archive') . ' ' . $package->ZipSize ?></label>

		<input type="checkbox" id="files-database" /> <i class="fas fa-database fa-sm"></i> 
		<label for="files-database"><?php echo DUP_PRO_U::__('Database') . ' ' . DUP_PRO_U::byteSize($package->Database->Size); ?></label> 
	</div>


	<!-- ===================
	STEP 1 -->
	<div id="step2-section">
		<div style="margin:0px 0 0px 0">
			<h3><?php DUP_PRO_U::esc_html_e('1: Choose Location') ?></h3>
			<input style="display:none" type="radio" name="location" id="location-storage" checked="checked" onclick="DupPro.Pack.Transfer.ToggleLocation()" /> 
			<label style="display:none" for="location-storage"><?php DUP_PRO_U::esc_html_e('Storage'); ?></label>
			<input style="display:none" type="radio" name="location" id="location-quick" onclick="DupPro.Pack.Transfer.ToggleLocation()" /> 
			<label style="display:none" for="location-quick"><?php DUP_PRO_U::esc_html_e('Quick FTP Connect'); ?></label>
		</div>

		<!-- STEP 1: STORAGE -->
		<table id="location-storage-opts" class="widefat">
			<thead>
				<tr>
					<th style='white-space: nowrap; width:10px;'></th>
					<th style='width:275px'><?php DUP_PRO_U::esc_html_e('Name') ?></th>
					<th style='width:100px'><?php DUP_PRO_U::esc_html_e('Type') ?></th>
					<th style="white-space: nowrap"><?php DUP_PRO_U::esc_html_e('Location') ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$i = 0;

				foreach ($storage_list as $store) :

					/* @var $store DUP_PRO_Storage_Entity */
					if ($store->id != DUP_PRO_Virtual_Storage_IDs::Default_Local)
					{
						$i++;

						$store_type = $store->get_storage_type_string();
						$store_location = $store->get_storage_location_string();
						$is_valid = $store->is_valid();

						$mincheck = ($i == 1) ? 'data-parsley-mincheck="1" data-parsley-required="true"' : '';
						$row_style = ($i % 2) ? 'alternate' : '';
						$row_style .= ($is_valid) ? '' : ' storage-missing';
						?>
						<tr class="package-row <?php echo $row_style ?>">
							<td>
								<input class="duppro-storage-input" <?php echo DUP_PRO_UI::echoDisabled($is_valid == false); ?> name="_storage_ids[]" data-parsley-errors-container="#storage_error_container" <?php echo $mincheck; ?> type="checkbox" value="<?php echo $store->id; ?>" />
								<input name="edit_id" type="hidden" value="<?php echo $i ?>" />
							</td>
							<td>
								<a href="?page=duplicator-pro-storage&tab=storage&inner_page=edit&storage_id=<?php echo intval($store->id); ?>" target="_blank">
									<?php
									echo ($is_valid == false) ? '<i class="fa fa-exclamation-triangle fa-sm"></i>' : (($store_type == 'Local') ? '<i class="fa fa-server"></i>' : '<i class="fa fa-cloud"></i>');
									echo " {$store->name}";
									?>
								</a>
							</td>
							<td><?php echo $store_type ?></td>
							<td><?php
								echo (($store_type == 'Local') || ($store_type == 'Google Drive') || ($store_type == 'Amazon S3')) ? $store_location : "<a href='{$store_location}' target='_blank'>" . urldecode($store_location) . "</a>";
								?>
							</td>
						</tr>

					<?php } endforeach; ?>
					
					<?php if ($i == 0) : ?>
						<tr class="package-row">
							<td colspan="4" style="text-align: center">- <?php DUP_PRO_U::esc_html_e('No Storage Items Found') ?> -</td>
						</tr>
					<?php  endif; ?>
			</tbody>
			<tr style="background: #F1F1F1">
				<td colspan="4" style="text-align:right; padding:3px">
					<a href="admin.php?page=duplicator-pro-storage&tab=storage&inner_page=edit" target="_blank">
						[<?php DUP_PRO_U::esc_html_e('Create New Storage') ?>]
					</a>
				</td>
			</tr>
		</table>

		<!-- STEP 1: QUICK -->
		<div style="display:none" id="location-quick-opts">
			<table>
				<tr>
					<td><label><?php DUP_PRO_U::esc_html_e('Host'); ?>:</label></td>
					<td><input type="text" id="quick-host" name="quick-host" /></td>
					<td>
						<label><?php DUP_PRO_U::esc_html_e('Port'); ?>:</label>
						<input type="text" id="quick-port" name="quick-port" style="width:50px" />
					</td>
				</tr>
				<tr>
					<td><label><?php DUP_PRO_U::esc_html_e('Username'); ?>:</label></td>
					<td colspan="2"><input type="text" id="quick-user" name="quick-user" /></td>
				</tr>
				<tr>
					<td><label><?php DUP_PRO_U::esc_html_e('Password'); ?>:</label></td>
					<td colspan="2"><input type="text" id="quick-pass" name="quick-pass" /></td>
				</tr>
			</table>
			<input type="button" class="button button-small" value="<?php DUP_PRO_U::esc_attr_e('Test Connection') ?>"/>
		</div>
	</div>

	<!-- ===================
	STEP 2 -->
	<div id="step3-section">
		<h3><?php DUP_PRO_U::esc_html_e('2: Transfer Files') ?></h3>
		<input style="display: <?php echo ($transfer_occurring ? 'none' : 'default'); ?>" id="dup-pro-transfer-btn" type="button" class="button button-large button-primary" value="<?php DUP_PRO_U::esc_attr_e('Start Transfer') ?>" onclick="DupPro.Pack.Transfer.StartTransfer();"/> 
		<br/>

		<div style="width:700px; text-align: center; margin-left: auto; margin-right: auto" class="dpro-active-status-area">
			<div style="display:none; font-size:20px; font-weight:bold" id="dpro-progress-bar-percent"></div>
			<div style="font-size:14px" id="dpro-progress-bar-text"><?php DUP_PRO_U::esc_html_e('Processing') ?></div>			
			<div id="dpro-progress-bar-percent-help">
					<small><?php DUP_PRO_U::esc_html_e('Full package percentage shown on packages screen'); ?></small>
			</div>
		</div>
		
		<div style="margin-left:auto; margin-right:auto; text-align: center;">
			<div id="dpro-progress-bar-area" class="dpro-active-status-area">
				<div id="dpro-progress-bar"></div>
				<button disabled id="dup-pro-stop-transfer-btn" type="button" class="button button-large button-primarybutton dpro-btn-stop" value="" onclick="DupPro.Pack.Transfer.StopBuild();">
									<i class="fa fa-times fa-sm"></i> &nbsp; <?php DUP_PRO_U::esc_html_e('Stop Transfer'); ?>
				</button>
			</div>			
		</div>				
	</div>

	<!-- ===============================
	TRANSFER LOG -->
	<div class="dup-box">
		<div class="dup-box-title">
			<i class="fas fa-database fa-sm"></i> <?php DUP_PRO_U::esc_html_e('Transfer Log') ?>
			<div class="dup-box-arrow"></div>
		</div>			
		<div class="dup-box-panel" id="dup-transfer-transfer-log" style="<?php echo $ui_css_transfer_log ?>">
			<table class="widefat package-tbl">
				<thead>
					<tr>
						<th style='width:150px'><?php DUP_PRO_U::esc_html_e('Started') ?></th>
						<th style='width:100px'><?php DUP_PRO_U::esc_html_e('Stopped') ?></th>						
						<th style="white-space: nowrap"><?php DUP_PRO_U::esc_html_e('Status') ?></th>
						<th style="white-space: nowrap"><?php DUP_PRO_U::esc_html_e('Type') ?></th>
						<th style="width: 50%; white-space: nowrap"><?php DUP_PRO_U::esc_html_e('Description') ?></th>
					</tr>
				</thead>
				<tbody>
					
				</tbody>
			</table>
		</div>
	</div>


</div>

<?php
$alert1 = new DUP_PRO_UI_Dialog();
$alert1->title		= DUP_PRO_U::__('Storage Warning!');
$alert1->message	= DUP_PRO_U::__('At least one storage location must be checked.');
$alert1->initAlert();

$alert2 = new DUP_PRO_UI_Dialog();
$alert2->title		= DUP_PRO_U::__('Transfer Failure!');
$alert2->message	= DUP_PRO_U::__('Transfer failure when calling duplicator_pro_manual_transfer_storage.');
$alert2->initAlert();

$alert3 = new DUP_PRO_UI_Dialog();
$alert3->title		= DUP_PRO_U::__('Build Error');
$alert3->message	= DUP_PRO_U::__('Failed to stop build');
$alert3->initAlert();

$alert4 = new DUP_PRO_UI_Dialog();
$alert4->title		= $alert3->title;
$alert4->message	= DUP_PRO_U::__('Failed to stop build due to ajax error.');
$alert4->initAlert();

$alert5 = new DUP_PRO_UI_Dialog();
$alert5->title		= 'INFO!';
$alert5->message	= '';  // javascript inserted message
$alert5->initAlert();

$alert6 = new DUP_PRO_UI_Dialog();
$alert6->title		= 'INFO!';
$alert6->message	= '';  // javascript inserted message
$alert6->initAlert();

?>
<script>
	DupPro.Pack.Transfer = { };
    jQuery(document).ready(function ($) {

		var transferRequestedTimestamp = 0;
		var activePackageId = -1;
	
		DupPro.Pack.Transfer.GetTimeStamp = function() {
			return Math.floor(Date.now() / 1000);
		}
						
        /*	METHOD: Starts the data transfer */
        DupPro.Pack.Transfer.StartTransfer = function () {
			
			if(jQuery('#location-storage-opts input[type=checkbox]:checked').length == 0)
			{
				<?php $alert1->showAlert(); ?>
			}
			else
			{
				$(".dpro-active-status-area").show(500);
				DupPro.UI.AnimateProgressBar('dpro-progress-bar');
				var selected_storage_ids = $.map($(':checkbox[name=_storage_ids\\[\\]]:checked'), function (n, i) {
					return n.value;
				});
				var data = {
				    action: 'duplicator_pro_manual_transfer_storage',
                    package_id: <?php echo absint($package_id); ?>,
                    storage_ids: selected_storage_ids,
                    nonce: '<?php echo wp_create_nonce('duplicator_pro_manual_transfer_storage'); ?>'
				}

				console.log("sending to selected storages " + selected_storage_ids);

				transferRequestedTimestamp = DupPro.Pack.Transfer.GetTimeStamp();

				$("#dpro-progress-bar-text").text("<?php echo DUP_PRO_U::__('Initiating transfer. Please wait.') ?>");
				$("#dpro-progress-bar-percent").text('');
				DupPro.Pack.Transfer.SetUIState(true);

				$.ajax({
					type: "POST",
					url: ajaxurl,
					cache: false,
					timeout: 10000000,
					data: data,
					success: function (respData) {
						try {  
							var parsedData = DupPro.parseJSON(respData);
						} catch(err) {
							console.error(err);
                    		console.error('JSON parse failed for response data: ' + respData);
							<?php $alert2->showAlert(); ?>
							transferRequestedTimestamp = 0;
							DupPro.Pack.Transfer.SetUIState(false);
							console.log(respData);
							return false;
						}
						if(!parsedData.success)
						{
							if(parsedData.message != '') {
                                <?php $alert5->showAlert(); ?>
                                $("#<?php echo $alert5->getID(); ?>_message").html(parsedData.message);
							}
							transferRequestedTimestamp = 0;
							DupPro.Pack.Transfer.SetUIState(false);
							DupPro.Pack.Transfer.GetPackageState();
						}
					},
					error: function (respData) {
                        <?php $alert2->showAlert(); ?>
						transferRequestedTimestamp = 0;
						DupPro.Pack.Transfer.SetUIState(false);
						console.log(respData);
					}
				});
			}
        };
		
		 /*	METHOD: Starts the data transfer */
        DupPro.Pack.Transfer.StopBuild = function () {
			
            var data = {action: 'duplicator_pro_package_stop_build', package_id: activePackageId, nonce: '<?php echo wp_create_nonce('duplicator_pro_package_stop_build'); ?>'}
			$("#dup-pro-stop-transfer-btn").prop("disabled", true);
			
            $.ajax({
                type: "POST",
                url: ajaxurl,
                timeout: 10000000,
                data: data,
                success: function (respData) {
					try {
						var data = DupPro.parseJSON(respData);
					} catch(err) {
						console.error(err);
                    	console.error('JSON parse failed for response data: ' + respData);
						<?php $alert4->showAlert(); ?>
						$("#dup-pro-stop-transfer-btn").prop("disabled", false);
						return false;
					}
					if(! data.succeeded) {
                        <?php $alert3->showAlert(); ?>
						$("#dup-pro-stop-transfer-btn").prop("disabled", false);
					}						
                },
                error: function (data) {
                    <?php $alert4->showAlert(); ?>
					$("#dup-pro-stop-transfer-btn").prop("disabled", false);
                }
            });
        };
		
		/*	METHOD: Progress bar display state*/
		DupPro.Pack.Transfer.SetUIState = function (activeProcessing) {
			
			if(activeProcessing) 
			{
				$(".dpro-active-status-area").show(500);
				$("#dup-pro-transfer-btn").hide();				
				$("#location-storage input").prop("disabled", true);
				$("#location-storage-opts input").prop("disabled", true);
				DupPro.UI.AnimateProgressBar('dpro-progress-bar');
			} 
			else 
			{
				$("#dup-pro-stop-transfer-btn").prop("disabled", true);
				// Only allow to revert after enough time has past since the last transfer request
				currentTimestamp = DupPro.Pack.Transfer.GetTimeStamp();
				if((currentTimestamp - transferRequestedTimestamp) > 10) 
				{
					$("#location-storage input").prop("disabled", false);
					$("#location-storage-opts input").prop("disabled", false);
					$("#dup-pro-transfer-btn").show();
					$(".dpro-active-status-area").hide();
				}
			}
		}

		/*	METHOD: Retreive package state */
        DupPro.Pack.Transfer.GetPackageState = function () {

			var package_id = <?php echo $package_id; ?>;
            var data = {
                action: 'duplicator_pro_packages_details_transfer_get_package_vm',
                package_id: package_id,
                nonce: '<?php echo wp_create_nonce('duplicator_pro_packages_details_transfer_get_package_vm'); ?>'
            };

            $.ajax({
                type: "POST",
                url: ajaxurl,
                timeout: 10000000,
                data: data,
                success: function (respData) {
					try {
						var parsedData = DupPro.parseJSON(respData);
					} catch(err) {
                        console.error(err);
						console.error('JSON parse failed for response data: ' + respData);
						console.log("Transfer failure.");
						DupPro.Pack.Transfer.SetUIState(false);
						console.log(respData);
						return false;
                    }

					console.log(parsedData);
					if(parsedData.success)
					{
						var vm = parsedData.vm;
						
						// vm - view model for this screen
						// vm.active_package_id: Active package id (-1 for none)
						// vm.percent_text: Percent through the current transfer
						// vm.text: Text to display
						// vm.transfer_logs: array of transfer request vms (start, stop, status, message)

						if(activePackageId != vm.active_package_id)
						{
							// Once we have an active package ID allow the stop button to be clicked
							$("#dup-pro-stop-transfer-btn").prop("disabled", false);
						}
						
						activePackageId = vm.active_package_id;
						if (vm.active_package_id == -1) 
						{
							// No packages are running
							DupPro.Pack.Transfer.SetUIState(false);																									
						
						} else if (vm.active_package_id == package_id) {
							
							// This package is running
							if(vm.percent_text != '')
							{
								$("#dpro-progress-bar-percent").text(vm.percent_text);
							}
							else
							{
								$("#dpro-progress-bar-percent").text('');
							}
							
							$("#dpro-progress-bar-text").text(vm.text);
							DupPro.Pack.Transfer.SetUIState(true);		
						} else {
							
							// A package other than this one is running
							$("#dpro-progress-bar-text").text(vm.text);
							DupPro.Pack.Transfer.SetUIState(true);		
						}
						DupPro.Pack.Transfer.UpdateTransferLog(vm);						
					}
					else {
						if(parsedData.message != '') {
                            <?php $alert6->showAlert(); ?>
                            $("#<?php echo $alert6->getID(); ?>_message").html(parsedData.message);
						}
						DupPro.Pack.Transfer.SetUIState(false);
						console.log(data);
					}				
                },
                error: function (data) {
                    console.log("Transfer failure.");
					DupPro.Pack.Transfer.SetUIState(false);
					console.log(data);
                }
            });
        };

		/*	METHOD: Updates the transfer log with the information from the view model */
        DupPro.Pack.Transfer.UpdateTransferLog = function(vm) {
														
            $("#dup-transfer-transfer-log table tbody").empty();
			var i;
			for(i = 0; i < vm.transfer_logs.length; i++) 
			{
				var transfer_log = vm.transfer_logs[i];
				var row_style = (i % 2) ? ' alternate' : '';
				var row_html = '<tr class="package-row"' + row_style + '">';
				row_html += '<td style="width:16%">' + transfer_log.started + '</td>';
				row_html += '<td style="width:16%">' + transfer_log.stopped + '</td>';				
				row_html += '<td style="width:10%">' + transfer_log.status_text + '</td>';
				row_html += '<td style="width:12%">' + transfer_log.storage_type_text + '</td>';				
				row_html += '<td style="width:46%">' + transfer_log.message + '</td>';
				row_html += '</tr>';
				
				$("#dup-transfer-transfer-log table tbody").append(row_html);
			}
			
			if (i == 0) 
			{
				var row_html = '<tr><td colspan="5" style="text-align:center"><?php DUP_PRO_U::esc_html_e('- No transactions found for this package -'); ?></td></tr>';
				$("#dup-transfer-transfer-log table tbody").append(row_html);
			}
        };

		//INIT
        DupPro.Pack.Transfer.GetPackageState();
        setInterval(DupPro.Pack.Transfer.GetPackageState, 8000);
    });
</script>