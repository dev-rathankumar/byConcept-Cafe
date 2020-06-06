<?php defined("ABSPATH") or die(""); ?>
<div class="section-hdr">PACKAGE CTRLS</div>

<form>
	<?php
		$CTRL['Title']   = 'duplicator_pro_package_scan';
		$CTRL['Action']  = 'duplicator_pro_package_scan';
		$CTRL['Test']	 = false;
		DUP_PRO_DEBUG_TestSetup($CTRL);
	?>
	<div class="params">
		No Params
	</div>
</form>

<!-- METHOD TEST -->
<form>
	<?php
		$CTRL['Title']   = 'DUP_PRO_CTRL_Package_addQuickFilters';
		$CTRL['Action']  = 'DUP_PRO_CTRL_Package_addQuickFilters';
		$CTRL['Test']	 = true;
		DUP_PRO_DEBUG_TestSetup($CTRL);
	?>
	<div class="params">
		<textarea style="width:200px; height: 50px" name="dir_paths">D:/path1/;
D:/path2/path/;
		</textarea>
		<textarea style="width:200px; height: 50px" name="file_paths">D:/path1/test.txt;
D:/path2/path/test2.txt;
		</textarea>
	</div>
</form>

<!-- METHOD TEST -->
<form>
	<?php
		$CTRL['Title']   = 'DUP_PRO_CTRL_Package_switchDupArchiveNotice';
		$CTRL['Action']  = 'DUP_PRO_CTRL_Package_switchDupArchiveNotice';
		$CTRL['nonce']  = wp_create_nonce('DUP_PRO_CTRL_Package_switchDupArchiveNotice');
		$CTRL['Test']	 = true;
		DUP_PRO_DEBUG_TestSetup($CTRL);
	?>
	<div class="params">

		<label>Enable DupArchive:</label>
		<input type="text" name="enable_duparchive" value="true" /> <br/>
	</div>
</form>

<!-- METHOD TEST -->
<form>
	<?php
		$CTRL['Title']   = 'DUP_PRO_CTRL_Package_toggleGiftFeatureButton';
		$CTRL['Action']  = 'DUP_PRO_CTRL_Package_toggleGiftFeatureButton';
		$CTRL['nonce']   = wp_create_nonce('DUP_PRO_CTRL_Package_toggleGiftFeatureButton');
		$CTRL['Test']	 = true;
		DUP_PRO_DEBUG_TestSetup($CTRL);
	?>
	<div class="params">

		<label>Disable Gift Icon:</label>
		<input type="text" name="dupHidePackagesGiftFeatures" value="true" /> <br/>
	</div>
</form>

<!-- METHOD TEST -->
<form>
	<?php
		$CTRL['Title']   = 'DUP_PRO_CTRL_Package_getPackageFile';
		$CTRL['Action']  = 'DUP_PRO_CTRL_Package_getPackageFile';
		$CTRL['Test']	 = false;
		DUP_PRO_DEBUG_TestSetup($CTRL);
		$qryResult	= $wpdb->get_row("SELECT ID FROM `{$wpdb->base_prefix}duplicator_pro_packages` ORDER BY id DESC LIMIT 1", ARRAY_A);
		$last_id = (! empty($qryResult['ID'])) ? $qryResult['ID'] : 0;
		
	?>
	<div class="params">
		<!--  0=installer, 1=archive, 2=sql file, 3=log -->
		<label>Type:</label>
		<select name="which">
			<option value="0">installer</option>
			<option value="1">archive</option>
			<option value="2">sql file</option>
			<option value="3">log</option>
		</select><br/>
		<label>Package ID:</label>
		<input type="text" name="package_id" value="<?php echo esc_attr($last_id); ?>" style="width:100px" /> <i>last id</i><br/>
		<small>Note: only the log type will allow for the JSON response to be seen</small>
	</div>
</form>

