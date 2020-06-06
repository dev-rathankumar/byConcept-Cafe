<?php
defined("ABSPATH") or die("");
/* @var $global DUP_PRO_Brand_Entity */

// let's setup active brand
if (isset($_GET['made_active']) && isset($_GET['id']) && $_GET['made_active'] == 'true') {

    $set_default_id = ($_GET['id'] > 0 ? (int)$_GET['id'] : -1);

    if ($set_default_id > 0) {
		$brand = DUP_PRO_Brand_Entity::get_by_id($set_default_id);
	} else {
		$brand = DUP_PRO_Brand_Entity::get_active();
	}

	$save_active                = new DUP_PRO_Brand_Entity();
    $save_active->id            = $brand->id;
    $save_active->name          = $brand->name;
    $save_active->active        = $set_default_id > 0 ? (!$brand->active ? true : false) : false;
    $save_active->attachments   = $brand->attachments;
    $save_active->notes         = $brand->notes;
    $save_active->logo          = $brand->logo;
    $save_active->save();

    if(is_multisite()) {
        $redirect_url = network_admin_url("admin.php?page=duplicator-pro-settings&tab=package&sub=brand", (DUP_PRO_U::is_ssl() ? 'https' : 'http'));
    } else {
        $redirect_url = get_admin_url(null, "admin.php?page=duplicator-pro-settings&tab=package&sub=brand" );
    }

    exit('
        <h1>' . DUP_PRO_U::__('Please wait...') . '</h1>
        <meta http-equiv="refresh" content="0; url='.$redirect_url.'">
        <script type="text/javascript">
            window.location.href = "'.$redirect_url.'"
        </script>
    ');
}


if (isset($_REQUEST['action'])) {
	//check_admin_referer($nonce_action);

	$action = $_REQUEST['action'];
	switch ($action) {

		case 'bulk-delete':
			$brand_ids = $_REQUEST['selected_id'];
			foreach ($brand_ids as $brand_id) {
				DUP_PRO_Brand_Entity::delete_by_id($brand_id);
			}
			break;

		case 'delete':
			$brand_id = (int) $_REQUEST['brand_id'];
			DUP_PRO_Brand_Entity::delete_by_id($brand_id);
			break;

	}
}

$brands = DUP_PRO_Brand_Entity::get_all();
$brand_count = count($brands);
$is_freelancer_plus = (DUP_PRO_License_U::getLicenseType() >= DUP_PRO_License_Type::Freelancer);
?>

<style>
    /*Detail Tables */
    table.brand-tbl td {height: 45px}
    table.brand-tbl a.name {font-weight: bold}
    table.brand-tbl input[type='checkbox'] {margin-left: 5px}
    table.brand-tbl div.sub-menu {margin: 5px 0 0 2px; display: none}
    table tr.brand-detail {display:none; margin: 0;}
    table tr.brand-detail td { padding: 3px 0 5px 20px}
    table tr.brand-detail div {line-height: 20px; padding: 2px 2px 2px 15px}
    table tr.brand-detail td button {margin:5px 0 5px 0 !important; display: block}
    tr.brand-detail label {min-width: 150px; display: inline-block; font-weight: bold}
	form#dup-brand-form {padding:0}
</style>

<div <?php echo ($is_freelancer_plus) ? "style='display:none'" : ""; ?>>
	<h2><?php DUP_PRO_U::esc_html_e("Installer Branding") ?></h2>
	<hr size="1"/>
	
	<div style="width:850px">
		<?php
			DUP_PRO_U::esc_html_e("Create your own WordPress distribution by adding a custom name and logo to the installer!  "
				. "Installer branding lets you create multiple brands for your installers and then choose which one you want when the package is built (example shown below).");
		?>
		<br/><br/>
		<b>
			<?php DUP_PRO_U::esc_html_e("This feature is only available in Freelancer, Business or Gold licenses. For details on how to upgrade your license "); ?>
		</b>
		<a href="https://snapcreek.com/duplicator/docs/faqs/#faq-presale-035-q" target="_blank"><?php DUP_PRO_U::esc_html_e("click here"); ?></a>. <br/>
	</div>

	<div style="border:0px solid #999; padding: 5px; margin: 5px; border-radius: 5px; width:700px">
		<img src="<?php echo DUPLICATOR_PRO_IMG_URL ?>/dpro-brand.png" style='' />
	</div>
	<br/><br/>
</div>

<!-- ====================
TOOL-BAR -->
<div <?php echo ($is_freelancer_plus) ? "" : "style='display:none'"; ?>>
<table class="dpro-edit-toolbar">
    <tr>
        <td>
            <select id="bulk_action">
                <option value="-1" selected="selected"><?php _e("Bulk Actions"); ?></option>
                <option value="delete" title="<?php DUP_PRO_U::esc_attr_e('Delete selected brand endpoint(s)'); ?>"><?php _e("Delete"); ?></option>
            </select>
            <input type="button" class="button action" value="<?php DUP_PRO_U::esc_html_e("Apply") ?>" onclick="DupPro.Settings.Brand.BulkAction()">
        </td>
        <td>
			<div class="btnnav">
				<a href="javascript:void(0)" class="button disabled"><i class="far fa-image fa-lg"></i> <?php DUP_PRO_U::esc_html_e("Brands"); ?></a>
				<a href="javascript:void(0)" onclick="DupPro.Settings.Brand.AddNew()" class="button"><?php DUP_PRO_U::esc_html_e('Add New'); ?></a>
			</div>
        </td>
    </tr>
</table>

<form id="dup-brand-form" action="<?php echo $brand_list_url; ?>" method="post">
<?php wp_nonce_field($nonce_action); ?>
<input type="hidden" id="dup-brand-form-action" name="action" value=""/>
<input type="hidden" id="dup-selected-brand" name="brand_id" value="-1"/>

<!-- ====================
LIST ALL STORAGE -->
<table class="widefat brand-tbl">
	<thead>
		<tr>
			<th style='width:10px;'><input type="checkbox" id="dpro-chk-all" title="Select all brand endpoints" onclick="DupPro.Settings.Brand.SetAll(this)"></th>
			<th style='width:300px;'><?php DUP_PRO_U::esc_html_e('Name'); ?></th>
			<th><?php DUP_PRO_U::esc_html_e('Active'); ?></th>
		</tr>
	</thead>
	<tbody>
        <?php
        ob_start(); // Must transfer data after default brand item
		$i = 0;
        $is_default_active = true;
		foreach ($brands as $x=>$brand) :
            if($x === 0) continue; // remove default item in list because is defined out of loop below
			$i++;

            if($brand->active) $is_default_active = false;

			//$brand_type = $brand->get_mode_text();
			?>
			<tr id='main-view-<?php echo $brand->id ?>' class="brand-row<?php echo ($i % 2) ? ' alternate' : ''; ?>">
				<td>
					<?php if ($brand->editable) : ?>
						<input name="selected_id[]" type="checkbox" value="<?php echo $brand->id; ?>" class="item-chk" />
					<?php else : ?>
						<input type="checkbox" disabled="disabled" />
					<?php endif; ?>
				</td>
				<td>
                    <a href="javascript:void(0);" onclick="DupPro.Settings.Brand.Edit('<?php echo $brand->id; ?>')"><b><?php echo $brand->name; ?></b></a>
					<?php if ($brand->editable) : ?>
						<div class="sub-menu">
							<a href="javascript:void(0);" onclick="DupPro.Settings.Brand.Edit('<?php echo $brand->id; ?>')"><?php DUP_PRO_U::esc_html_e('Edit') ?></a> |
							<a href="javascript:void(0);" onclick="DupPro.Settings.Brand.View('<?php echo $brand->id; ?>');"><?php DUP_PRO_U::esc_html_e('Quick View') ?></a>
                            <?php if(!$brand->active) :
                                if(is_multisite()) {
                                    $activeUrl = network_admin_url("admin.php?page=duplicator-pro-settings&tab=package&sub=brand&made_active=true&id=".$brand->id, (DUP_PRO_U::is_ssl() ? 'https' : 'http'));
                                } else {
                                    $activeUrl = get_admin_url(null, "admin.php?page=duplicator-pro-settings&tab=package&sub=brand&made_active=true&id=".$brand->id );
                                }
                            ?>|
                            <a href="<?php echo esc_attr($activeUrl); ?>" style="color:orangered;">
                                <?php DUP_PRO_U::esc_html_e('Set active'); ?>
                            </a>
                            <?php endif; ?> |
							<a href="javascript:void(0);" onclick="DupPro.Settings.Brand.Delete('<?php echo $brand->id; ?>');"><?php DUP_PRO_U::esc_html_e('Delete') ?></a>
						</div>
					<?php else : ?>
						<div class="sub-menu">
							<a href="javascript:void(0);" onclick="DupPro.Settings.Brand.Edit(0)"><?php DUP_PRO_U::esc_html_e('View') ?></a> |
							<a href="javascript:void(0);" onclick="DupPro.Settings.Brand.View('<?php echo $brand->id; ?>');"><?php DUP_PRO_U::esc_html_e('Quick View') ?></a>
						</div>
					<?php endif; ?>
				</td>
				<td>
					<?php if ($brand->active) : ?>
						<span class="fa fa-bolt fa-xs" style="color:green;"></span> <?php echo DUP_PRO_U::esc_html_e('Enabled'); ?>
					<?php endif; ?>

				</td>
			</tr>
			<tr id='quick-view-<?php echo $brand->id ?>' class='<?php echo ($i % 2) ? 'alternate ' : ''; ?>brand-detail'>
				<td colspan="3">
					<b><?php DUP_PRO_U::esc_html_e('QUICK VIEW') ?></b> <br/>
					<div>
						<label><?php DUP_PRO_U::esc_html_e('Name') ?>:</label>
						<?php echo $brand->name ?>
					</div>
					<div>
						<label><?php DUP_PRO_U::esc_html_e('Notes') ?>:</label>
						<?php echo (strlen($brand->notes)) ? $brand->notes : DUP_PRO_U::__('(no notes)'); ?>
					</div>
					<div>
						<label><?php DUP_PRO_U::esc_html_e('Logo') ?>:</label>
						<?php echo $brand->logo ?>
					</div>
					<button type="button" class="button" onclick="DupPro.Settings.Brand.View('<?php echo $brand->id; ?>');"><?php DUP_PRO_U::esc_html_e('Close') ?></button>
				</td>
			</tr>
		<?php
            endforeach;
            $display_brand_list = ob_get_clean(); // save generated list into string
        ?>
        <!-- DEFAULT BRAND ITEM -->
        <tr id='main-view-<?php echo $brands[0]->id; ?>' class="brand-row">
            <td>
                <input type="checkbox" disabled="disabled" />
            </td>
            <td>
                <a href="javascript:void(0);" onclick="DupPro.Settings.Brand.Edit(0)"><b><?php DUP_PRO_U::esc_html_e('Default'); ?></b></a>
                <div class="sub-menu">
                    <a href="javascript:void(0);" onclick="DupPro.Settings.Brand.Edit(0)"><?php DUP_PRO_U::esc_html_e('View'); ?></a> |
                    <a href="javascript:void(0);" onclick="DupPro.Settings.Brand.View('<?php echo $brands[0]->id; ?>');"><?php DUP_PRO_U::esc_html_e('Quick View'); ?></a>
                    <?php  if(!$is_default_active) :
                        if(is_multisite()) {
                            $activeUrl = network_admin_url("admin.php?page=duplicator-pro-settings&tab=package&sub=brand&made_active=true&id=" . $brands[0]->id, (DUP_PRO_U::is_ssl() ? 'https' : 'http'));
                        } else {
                            $activeUrl = get_admin_url(null, "admin.php?page=duplicator-pro-settings&tab=package&sub=brand&made_active=true&id=" . $brands[0]->id );
                        }
                    ?>|
                    <a href="<?php echo esc_attr($activeUrl); ?>" style="color:orangered;">
                        <?php DUP_PRO_U::esc_html_e('Set active'); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </td>
            <td>
                <?php if($is_default_active) echo '<span class="fa fa-bolt fa-xs" style="color:green;"></span> '.DUP_PRO_U::__('Enabled'); ?>
            </td>
        </tr>
        <tr id="quick-view-<?php echo $brands[0]->id ?>" class="brand-detail">
            <td colspan="3">
                <b><?php DUP_PRO_U::esc_html_e('QUICK VIEW') ?></b> <br/>
                <div>
                    <label><?php DUP_PRO_U::esc_html_e('Name') ?>:</label>
                    <?php echo $brands[0]->name ?>
                </div>
                <div>
                    <label><?php DUP_PRO_U::esc_html_e('Notes') ?>:</label>
                    <?php echo (strlen($brands[0]->notes)) ? $brands[0]->notes : DUP_PRO_U::__('(no notes)'); ?>
                </div>
                <div>
                    <label><?php DUP_PRO_U::esc_html_e('Logo') ?>:</label>
                    <?php echo $brands[0]->logo ?>
                </div>
                <button type="button" class="button" onclick="DupPro.Settings.Brand.View('<?php echo $brands[0]->id; ?>');"><?php DUP_PRO_U::esc_html_e('Close') ?></button>
            </td>
        </tr>
        <!-- END DEFAULT BRAND ITEM -->

        <!-- DYNAMIC BRAND ITEMS -->
        <?php echo $display_brand_list; ?>
        <!-- END DYNAMIC BRAND ITEMS -->

	</tbody>
	<tfoot>
		<tr>
			<th colspan="8" style="text-align:right; font-size:12px">
				<?php echo DUP_PRO_U::__('Total') . ': ' . $brand_count; ?>
			</th>
		</tr>
	</tfoot>
</table>
</form>
</div>
<!-- ==========================================
THICK-BOX DIALOGS: -->
<?php
	$alert1 = new DUP_PRO_UI_Dialog();
	$alert1->title		= DUP_PRO_U::__('Bulk Action Required');
	$alert1->message	= DUP_PRO_U::__('Please select an action from the "Bulk Actions" drop down menu!');
	$alert1->initAlert();

	$alert2 = new DUP_PRO_UI_Dialog();
	$alert2->title		= DUP_PRO_U::__('Selection Required');
	$alert2->message	= DUP_PRO_U::__('Please select at least one brand to delete!');
	$alert2->initAlert();

	$confirm1 = new DUP_PRO_UI_Dialog();
	$confirm1->title			 = DUP_PRO_U::__('Delete Brand?');
	$confirm1->message			 = DUP_PRO_U::__('Are you sure you want to delete the selected brand(s)?');
	$confirm1->message			.= '<br/>';
	$confirm1->message			.= DUP_PRO_U::__('<small><i>Note: This action removes all brands.</i></small>');
	$confirm1->progressText      = DUP_PRO_U::__('Removing Brands, Please Wait...');
	$confirm1->jsCallback		 = 'DupPro.Settings.Brand.BulkDelete()';
	$confirm1->initConfirm();

    $confirm2 = new DUP_PRO_UI_Dialog();
	$confirm2->title			 = DUP_PRO_U::__('Delete Brand?');
	$confirm2->message			 = DUP_PRO_U::__('Are you sure you want to delete the selected brand(s)?');
	$confirm2->progressText      = DUP_PRO_U::__('Removing Brands, Please Wait...');
	$confirm2->jsCallback		 = 'DupPro.Settings.Brand.DeleteThis(this)';
	$confirm2->initConfirm();

    $delete_nonce = wp_create_nonce('duplicator_pro_brand_delete');
?>
<script>
jQuery(document).ready(function ($) {

	//Shows detail view
	DupPro.Settings.Brand.AddNew = function()
	{
		document.location.href = '<?php echo "{$brand_edit_url}&action=new"; ?>';
	}

	DupPro.Settings.Brand.Edit = function (id)
	{
		if (id == 0) {
			document.location.href = '<?php echo "{$brand_edit_url}&action=default&id="; ?>' + id;
		} else {
			document.location.href = '<?php echo "{$brand_edit_url}&action=edit&id="; ?>' + id;
		}
	}

	//Shows detail view
	DupPro.Settings.Brand.View = function (id)
	{
		$('#quick-view-' + id).toggle();
		$('#main-view-' + id).toggle();
	}

	//Delets a single record
	DupPro.Settings.Brand.Delete = function (id)
	{
        <?php $confirm2->showConfirm(); ?>
        $("#<?php echo $confirm2->getID(); ?>-confirm").attr('data-id', id);
	}

    DupPro.Settings.Brand.DeleteThis = function(e)
    {
        var id = $(e).attr('data-id');
        jQuery("#dup-brand-form-action").val('delete');
        jQuery("#dup-selected-brand").val(id);
        jQuery("#dup-brand-form").submit()
    }
    
    //	Creats a comma seperate list of all selected package ids
    DupPro.Settings.Brand.DeleteList = function ()
    {
        var arr = [];

        $("input[name^='selected_id[]']").each(function(i, index) {
            var $this = $(index);
            
            if ($this.is(':checked')==true) {
                arr[i] = $this.val();
            }
        });

        return arr.join(',');
    }

    // Bulk delete
    DupPro.Settings.Brand.BulkDelete = function ()
    {
        var list = DupPro.Settings.Brand.DeleteList();
        var pageCount = $('#current-page-selector').val();
        var pageItems = $("input[name^='selected_id[]']");

        $.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: "json",
            data: {action: 'duplicator_pro_brand_delete', duplicator_pro_delid: list, nonce: '<?php echo $delete_nonce; ?>'},
        }).done(function(data) {
            $('#dup-brand-form').submit();
        });
    }

    // Confirm bulk action
	DupPro.Settings.Brand.BulkAction = function () 
	{
        var list = DupPro.Settings.Brand.DeleteList();

        if (list.length == 0) {
            <?php $alert2->showAlert(); ?>
            return;
        }
        
		var action = $('#bulk_action').val();
		var checked = ($('.item-chk:checked').length > 0);

        if (action != "delete") {
            <?php $alert1->showAlert(); ?>
            return;
        }

		if (checked) {
			switch (action) {
                default:
                    <?php $alert2->showAlert(); ?>
                    break;
				case 'delete':
                    <?php $confirm1->showConfirm(); ?>
					break;
			}
		}
	}

	//Sets all for deletion
	DupPro.Settings.Brand.SetAll = function (chkbox) {
		$('.item-chk').each(function () {
			this.checked = chkbox.checked;
		});
	}

	//Name hover show menu
	$("tr.brand-row").hover(
			function () {
				$(this).find(".sub-menu").show();
			},
			function () {
				$(this).find(".sub-menu").hide();
			}
	);
});
</script>
