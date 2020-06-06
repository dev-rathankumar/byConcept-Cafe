<?php
defined("ABSPATH") or die("");
DUP_PRO_U::hasCapability('manage_options');
require_once(DUPLICATOR_PRO_PLUGIN_PATH.'/classes/ui/class.ui.dialog.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/classes/package/class.pack.archive.available.php');
?>

<style>
form#dpro-migration-form { padding:15px; border: 1px solid silver; border-radius: 5px; background:#ffffff; min-height:375px}
div#dpro-dd-target {margin:5px 0; text-align: center }
div.fs-upload-target {border:3px dashed silver !important; border-radius:8px !important;  color:#000 !important; height:200px; padding: 10px !important; }

div.step-state {display:block}
div.step-err {display:none; color:maroon; font-size: 18px; font-style: italic; line-height: 26px}
div#dpro-step-1 {padding:5px;}
input#dpro-step-1-btn	{margin:15px auto; display:block}
div#dpro-step-1-target {font-size:18px; line-height:26px; font-weight: bold; color:#555}
div#dpro-step-2 {display:none;}
div#dpro-step-3 {display:none; font-size:14px; text-align: left; width:800px; margin:auto;}
div#dpro-step-3 h2 {text-align: center; font-size:18px; color:green; line-height: 22px; font-weight: bold}
button#dpro-launch-btn {font-weight: bold; font-size: 16px}
table.dpro-import-tbl {margin:auto}
table.dpro-import-tbl td:first-child{font-weight: bold}

.filelists {margin:0}
.filelists .cancel_all {color: red;	cursor: pointer; clear: both; font-size: 10px; margin: 0; text-transform: uppercase;}
.filelist {margin: 0; padding:0;}
.filelist li {background: #fff;	border-bottom: 1px solid #ECEFF1; font-size: 16px; list-style: none; padding:15px; position: relative; border:1px solid silver; border-radius: 3px}
.filelist li:before {display: none !important;}
.filelist li .bar {background: #CCCCCC; content: ''; height: 100%; left: 0; position: absolute; top: 0;	width: 0; z-index: 0;  transition: width 0.1s linear;}
.filelist li .content {display: block; overflow: hidden; position: relative; z-index: 1; font-weight: bold; color:#000}
.filelist li .file {color: #000;	float: left;display: block;	overflow: hidden; text-overflow: ellipsis; max-width: 85%; white-space: nowrap;}
.filelist li .progress {color: #000;	display: block;	float: right; font-size: 14px;text-transform: uppercase;}
.filelist li .cancel {color: red;cursor: pointer;display: block;float: right;font-size: 14px;margin: 0 0 0 10px;text-transform: uppercase;}
.filelist li.error .file {color: red;}
.filelist li.error .progress {color: red;}
.filelist li.error .cancel {display: none;}
div#migrate-details {margin: 5px 0}
div#migrate-details ol {margin:10px 0 0 30px }
.warn {color:maroon}
div.import-accept {width:700px; margin: auto; padding:20px; font-style: italic; color:maroon}

div#dpro-dd-available-packages table tbody tr:nth-child(odd) {background: #f9f9f9;}
</style>

<h2><i class="fa fa-upload"></i> <?php DUP_PRO_U::esc_html_e("Import Site (Beta)"); ?></h2>

<?php if (! $global->profile_beta) : ?>
	<?php DUP_PRO_U::esc_html_e('Please enable "Beta Features" from the '); ?>
	<a href="admin.php?page=duplicator-pro-settings&subtab=profile"> <?php DUP_PRO_U::esc_html_e('Settings &gt; General &gt; Features Profiles Screen'); ?></a>.
	<?php die(); ?>
<?php endif;?>

<form id="dpro-migration-form">

	<!-- STEP 1: Select File -->
	<div id="dpro-step-1" class="step-state">
		<?php DUP_PRO_U::esc_html_e("Use the box below to upload a duplicator archive file (zip/daf)"); ?>
		<a href="javascript:void(0)" onClick="jQuery('#migrate-details').toggle(300)"><?php DUP_PRO_U::esc_html_e("[More Details]"); ?>...</a>
		<div id="migrate-details" style="display:none">
			<?php DUP_PRO_U::esc_html_e("The import migration tool allows a Duplicator Pro package to be installed over this site.  This process consist  of the following steps:"); ?>

			<ol>
				<li><?php DUP_PRO_U::esc_html_e("Upload a Duplicator zip/daf archive file below."); ?></li>
				<li><?php DUP_PRO_U::esc_html_e("Click the Launch Installer button and proceed with the install wizard."); ?></li>
				<li><?php echo wp_kses(DUP_PRO_U::esc_html__("After install this site will be overwritten with the uploaded archive files contents."), array('u')); ?></li>
			</ol>
            <p style="color:maroon">
            <?php echo wp_kses(DUP_PRO_U::__("<b>Important:</b> This feature is intended for empty or newly installed WordPress sites.  It is not recommend for use on production sites."), array(
                    'b' => array(),
                )
            ); ?>
            </p>
        </div>
		<div id="dpro-dd-target">
			<div id="dpro-step-1-target">

				<i class="fa fa-cloud-upload fa-3x" ></i><br>
				<div id="dpro-step-1-label">
                    <?php DUP_PRO_U::esc_html_e("Drag & Drop to Upload"); ?>
                    <br/>
                    <?php DUP_PRO_U::esc_html_e("Duplicator Archive File"); ?>
				</div>

				<!-- ERROR MESSAGES:  -->
				<div id="dpro-step-10" class="step-state step-err">
                    <i class="fas fa-exclamation-triangle fa-sm"></i>
					<?php DUP_PRO_U::esc_html_e("Only file types .zip &amp; .daf are supported!");?>
                    <br/> 
                    <?php DUP_PRO_U::esc_html_e("Please try again!"); ?>
				</div>

				<div id="dpro-step-11" class="step-state step-err">
                    <i class="fas fa-exclamation-triangle fa-sm"></i>
					<?php DUP_PRO_U::esc_html_e("Upload request aborted by user!");?>
                    <br/> 
                    <?php DUP_PRO_U::esc_html_e("Please try again!"); ?>
				</div>
                
                <div id="dpro-step-12" class="step-state step-err">
                    <i class="fas fa-exclamation-triangle fa-sm"></i>
					<?php DUP_PRO_U::esc_html_e("Error uploading file!"); ?>
                    <br/> 
                    <?php DUP_PRO_U::esc_html_e("Please try again!"); ?>
				</div>
                                
                <div id="dpro-step-20" class="step-state step-err">
                    <i class="fas fa-exclamation-triangle fa-sm"></i>
					<?php printf(wp_kses(DUP_PRO_U::__("Archive you upload is not supported in version %s!<br> Please upload archive from version %s and above.<br> Archive version you upload is %s."), array('br' => array())), esc_html(DUPLICATOR_PRO_VERSION), esc_html(DUPLICATOR_PRO_LIMIT_UPLOAD_VERSION), '<span id="error-archive-version">'.esc_html(DUPLICATOR_PRO_LIMIT_UPLOAD_VERSION).'</span>'); ?>
				</div>
				<input id="dpro-step-1-btn" type="button" class="button button-large" name="dpro-files" id="dpro-daf-upload-btn" value="<?php DUP_PRO_U::esc_attr_e("Select File"); ?>">
			</div>

		</div>

        <?php
        $list_available_archive = array();
        $i = 0;
        foreach(array(
            DUPLICATOR_PRO_PATH_IMPORTS,
            duplicator_pro_get_home_path()
        ) as $archive_path)
        {
            $archive_available = DUP_PRO_Archive_Available::get_list($archive_path);
            if($archive_available->length > 0)
            {
                foreach($archive_available->list as $list_archive)
                {
                    $permalink = preg_replace("/[^a-z0-9\_]/Ui","_",$list_archive->name);
                    $list_available_archive[]='<tr id="dpro-dd-archive-' . $i . '">
                        <td style="text-align: left"><span title="'.esc_attr($list_archive->path).'" style="cursor: default;">'.esc_attr($list_archive->name).'</span></td>
                        <td style="text-align: center"><span title="Total '.esc_attr($list_archive->size).' bytes" style="cursor: default;">'.esc_html($list_archive->size_unit).'</span></td>
                        <td style="text-align: center">'.esc_html($list_archive->date).'</td>
                        <td style="text-align: right">

                            <a href="javascript:void(0);" data-id="#dpro-dd-archive-' . absint($i) . '" data-path="'.esc_attr($list_archive->path).'" data-name="'.esc_attr($list_archive->name).'" data-type="install" class="dpro-dd-archive-action"><i class="fa fa-bolt fa-sm"></i> ' . DUP_PRO_U::esc_html__('Launch Installer') . '</a>&nbsp;&nbsp; | &nbsp;&nbsp;
                            <a href="javascript:void(0);" data-id="#dpro-dd-archive-' . $i . '" data-path="'.esc_attr($list_archive->path).'" data-type="delete" class="dpro-dd-archive-action" style="color:orangered"><i class="fa fa-ban"></i> Remove</a>
                        </td>
                    </tr>';
                    $i++;
                }
            }
        }
        if(count($list_available_archive) > 0) :
        sort($list_available_archive);
        ?>
        <div id="dpro-dd-available-packages">
            <br/>
            <table style="width:100%;" class="widefat">
                <thead>
                    <tr>
                        <th style="width:40%; text-align: left"><strong><?php DUP_PRO_U::esc_html_e("Available archives"); ?></strong></th>
                        <th style="width:15%; text-align: center"><strong><?php DUP_PRO_U::esc_html_e("Size"); ?></strong></th>
                        <th style="width:20%; text-align: center"><strong><?php DUP_PRO_U::esc_html_e("Created"); ?></strong></th>
                        <th style="text-align: center">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo join($list_available_archive); ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
	</div>

	<!-- STEP 2: Progress Bar -->
	<div id="dpro-step-2" class="step-state">
		<div class="filelists">
			<!-- <h5>Complete</h5>
			<ol class="filelist complete"></ol>-->
			<h2><?php DUP_PRO_U::esc_html_e("Uploading Please Wait..."); ?></h2>
			<ol class="filelist queue"></ol>
			<!-- <span class="cancel_all">Cancel All</span>-->
		</div>
	</div>

	<!-- STEP 3: Complete Message -->
	<div id="dpro-step-3" class="step-state">
		<h2>
			<i class="fa fa-check" ></i>
			<?php DUP_PRO_U::esc_html_e("Archive Ready for Install!"); ?></h2>
			<?php echo wp_kses(DUP_PRO_U::__("The archive is fully uploaded and ready to be installed over this site.  This process will <u><b class='warn'>overwrite</b></u> this entire site "
				. "you are currently logged into.  All plugins, themes, content and data will be replaced with the content found in the archive file.  The following database "
				. "credential below will be used for the database overwrite.  The values can be changed at install time if needed."), array(
                    'b' => array(),
                    'u' => array(),
                )); ?> <br/><br/>

		<div style="margin:auto; text-align: center;">
			<div style="text-align: left;">
				<table class="dpro-import-tbl">
					<tr>
						<td colspan="2"><b><u><?php DUP_PRO_U::esc_html_e("Uploaded Archive"); ?></u></b></td>
					</tr>
					<tr>
						<td><?php DUP_PRO_U::esc_html_e("Name:"); ?></td>
						<td id="dpro-import-tbl-archive-name"><?php DUP_PRO_U::esc_html_e("(undefined)"); ?></td>
					</tr>
					<tr>
						<td><?php DUP_PRO_U::esc_html_e("Size:"); ?></td>
						<td id="dpro-import-tbl-archive-size"><?php DUP_PRO_U::esc_html_e("(undefined)"); ?></td>
					</tr>
					<tr>
						<td colspan="2"><b><u><?php DUP_PRO_U::esc_html_e("Database"); ?></u></b></td>
					</tr>
					<tr>
						<td><?php DUP_PRO_U::esc_html_e("Host:"); ?></td>
						<td><?php echo DB_HOST ?></td>
					</tr>
					<tr>
						<td><?php DUP_PRO_U::esc_html_e("Name:"); ?></td>
						<td><?php echo DB_NAME  ?></td>
					</tr>
					<tr>
						<td><?php DUP_PRO_U::esc_html_e("User:"); ?></td>
						<td><?php echo DB_USER ?></td>
					</tr>

				</table>
			</div>

			<div class="import-accept">
				<input type="checkbox" id="enable-installer" onClick="DupPro.Tools.enableLaunchButton()"/>
				<label for="enable-installer">
					<?php  echo wp_kses(DUP_PRO_U::__("The files and database of the site you're logged into will be <u>overwritten</u> with the contents of the uploaded archive.  Check box to confirm you understand this."), array( 'u'  => array())); ?>
				</label><br/><br/>
				<button id="dpro-launch-btn" type="button" class="button button-large button-primary" title="<?php DUP_PRO_U::esc_attr_e("Check box above to enable button."); ?>">
					<i class="fa fa-bolt fa-sm"></i> <?php DUP_PRO_U::esc_html_e("Launch Installer"); ?>
				</button>
			</div>

			<small><a href="javascript:void(0)" onClick="location.reload()">[<?php DUP_PRO_U::esc_html_e("Cancel Import &amp; Refresh"); ?>]</a></small>
		</div>
            
    <!-- STEP 4: Error Message -->
	<div id="dpro-step-4" class="step-state">
		<h2>
			<i class="fa fa-check" ></i>
			<?php DUP_PRO_U::esc_html_e("Error Uploading Archive!"); ?></h2>
			<small><a href="javascript:void(0)" onClick="location.reload()">[<?php DUP_PRO_U::esc_html_e("Reset"); ?>]</a></small>
		</div>
	</div>
</form>


<?php
$global  = DUP_PRO_Global_Entity::get_instance();
$ajax_nonce	= wp_create_nonce('DUP_PRO_CTRL_Tools_migrationUploader');
$min_chunk_size  = 5 * MB_IN_BYTES;
$chunk_mode = 'chunked'; //chunked, direct
$max_size   = 107374182400; //100GB
// DupPro.Tools.prepArchive();

$confirmUpload = new DUP_PRO_UI_Dialog();
$confirmUpload->title			 = DUP_PRO_U::__('WARNING!');
$confirmUpload->message			 = DUP_PRO_U::__('This option cannot be undone without manual intervention! Proceed?');
$confirmUpload->progressText      = DUP_PRO_U::__('Please Wait...');
$confirmUpload->jsCallback		 = 'DupPro.Tools.prepArchive()';
$confirmUpload->initConfirm();

?>

<script>
jQuery(document).ready(function ($)
{
    var DPRO_UPLOAD_STEP = 1;
    var DPRO_UPLOADER;
    var DPRO_DEBUG = true;
    var INTERRUPT = 300 // miliseconds

    $('.dpro-dd-archive-action').on('click touchstart',function(e){
        e.preventDefault();
        var $this = $(this),
            $id = $this.attr('data-id'),
            $type = $this.attr('data-type'),
            $path = $this.attr('data-path'),
            $name = $this.attr('data-name'),
            data = {
                nonce	: '<?php echo $ajax_nonce; ?>',
            };

        if($type == 'delete')
        {
            if(confirm("Are you sure you want to delete this archive?"))
            {
                data.action = 'DUP_PRO_CTRL_Tools_deleteExistingPackage';
                data.path = $path;
                data.nonce = '<?php echo wp_create_nonce('DUP_PRO_CTRL_Tools_deleteExistingPackage'); ?>';

                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    dataType: "json",
                    data: data
                }).done(function() {                    
                    $.when( $($id).remove() ).done(function() {
                        if (0 === $('#dpro-dd-available-packages table tbody tr').length) {
                            $('#dpro-dd-available-packages').remove();
                        }
                    });
                });
            }
        }
        else if($type == 'install')
        {
            if(confirm("The site you're currently logged into will be overwritten with the contents of the uploaded archive. Both the files and database will be overwritten. Continue?"))
            {
                $this.html('<i class="fas fa-circle-notch fa-spin"></i> <?php DUP_PRO_U::esc_html_e('Please Wait...'); ?>');
                DupPro.Tools.lastArchiveUploaded = $name;
                DupPro.Tools.prepArchive();
            }
        }
    });

    DupPro.Tools.compareVersions = function(a_components, b_components) {

        if (a_components === b_components) {
            return 0;
        }

        var partsNumberA = a_components.split(".");
        var partsNumberB = b_components.split(".");

        for (var i = 0; i < partsNumberA.length; i++) {

           var valueA = parseInt(partsNumberA[i]);
           var valueB = parseInt(partsNumberB[i]);

           // A bigger than B
           if (valueA > valueB || isNaN(valueB)) {
              return 1;
           }

           // B bigger than A
           if (valueA < valueB) {
              return -1;
           }
        }
    };

    // Get Cookie information
    DupPro.Tools.getCookie = function(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return null;
    };

    DupPro.Tools.versionController = function(){
        var check = DupPro.Tools.getCookie('wp_duplicator_pro_daf_version');

        if(null!=check)
        {
            if(DupPro.Tools.compareVersions(check, "<?php echo DUPLICATOR_PRO_LIMIT_UPLOAD_VERSION; ?>") === -1)
            {
                $("#error-archive-version").text(check);
                $(".cancel").each(function(){
                    $(this).attr('data-state','interrupt').click();
                }).promise().done(function(){
                    DPRO_UPLOAD_STEP = 20;

                    //$('div.step-state').hide();
                    $('div.step-err').hide();
                    $('#dpro-step-20').show();
                });

                document.cookie = 'wp_duplicator_pro_daf_version=; Max-Age=0; path=<?php echo COOKIEPATH; ?>; domain=<?php echo COOKIE_DOMAIN; ?>';
            }
        }
    };


    DupPro.Tools.bytesCalculator = function(bytes)
    {
            try {
                var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
                if (bytes == 0) return '0 Byte';
                var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
                return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
            } catch ($e) {
                return "n/a";
            }
    }

    DupPro.Tools.lastArchiveUploaded = null;

    DupPro.Tools.initUploader = function()
    {
        var data = {
                    action	: 'DUP_PRO_CTRL_Tools_migrationUploader',
                    nonce	: '<?php echo $ajax_nonce; ?>',
                    chunk_size	: '<?php echo intval(DUPLICATOR_PRO_BUFFER_READ_WRITE_SIZE); ?>', // This is in bytes
                    chunk_mode	: <?php echo "'".esc_js($chunk_mode)."'"; ?>,
                    nonce: '<?php echo wp_create_nonce('DUP_PRO_CTRL_Tools_migrationUploader'); ?>'
                };

        var url = "<?php echo get_admin_url(); ?>admin-ajax.php?action=DUP_PRO_CTRL_Tools_migrationUploader";
        var $steps = $('#dpro-step-1-target');

        //Create uploader
        DPRO_UPLOADER = $("div#dpro-dd-target").upload({
                autoUpload: true,
                multiple: false,
                maxSize: <?php echo esc_js($max_size); ?>,
                maxFiles: 1,
                postData : data,
                chunkSize: <?php echo esc_js(DUP_PRO_U::get_default_chunk_size_in_kb($min_chunk_size)); ?>, // This is in kb
                action:url,
                chunked: <?php echo $chunk_mode == 'chunked' ? 'true' : 'false'; ?>,
                label: $steps.parent().html(),
                beforeSend: DupPro.Tools.onBeforeSend,
                leave: '<?php echo esc_js(DUP_PRO_U::__('You have uploads pending, are you sure you want to leave this page?'));?>'
        });

        //Attach to internal events
        DPRO_UPLOADER
            .on("start.upload", DupPro.Tools.onStart)
            .on("complete.upload", DupPro.Tools.onComplete)
            .on("filestart.upload", DupPro.Tools.onFileStart)
            .on("fileprogress.upload", DupPro.Tools.onFileProgress)
            .on("filecomplete.upload", DupPro.Tools.onFileComplete)
            .on("fileerror.upload", DupPro.Tools.onFileError)
            .on("chunkstart.upload", DupPro.Tools.onChunkStart)
            .on("chunkprogress.upload", DupPro.Tools.onChunkProgress)
            .on("chunkcomplete.upload", DupPro.Tools.onChunkComplete)
            .on("chunkerror.upload", DupPro.Tools.onChunkError)
            .on("queued.upload", DupPro.Tools.onQueued);

        $(".filelist.queue").on("click", ".cancel", DupPro.Tools.onCancel);
        $(".cancel_all").on("click", DupPro.Tools.onCancelAll);

        $steps.detach();
        DupPro.Tools.toggleStep(1);
    };


    DupPro.Tools.toggleStep = function(num)
    {
        DPRO_UPLOAD_STEP = num;

        $('#dpro-step-1-label').show();
        $('div.step-err').hide();
        switch (DPRO_UPLOAD_STEP) {
            case 2:
                DPRO_UPLOADER.upload("disable");
                $('#dpro-step-2').show();
                break;

            case 3:
                $('div.step-state').hide();
                $('#dpro-step-3').show();
                break;

            default:
                DPRO_UPLOADER.upload("enable");
                $('div.step-state').hide();
                $('#dpro-step-1').show();
                break;
        }

        if (DPRO_UPLOAD_STEP >= 10) {
                $('#dpro-step-1-label').hide();
                $('#dpro-step-' + DPRO_UPLOAD_STEP).show(200);

                // Remove file part from file system, otherwise next upload will give error
                var data = {
                    nonce: "<?php echo wp_create_nonce('DUP_PRO_CTRL_Tools_removeUploadedFilePart');?>",
                    upload_file_name: DupPro.Tools.uploadFileName,
                    action: 'DUP_PRO_CTRL_Tools_removeUploadedFilePart',
                    nonce: '<?php echo wp_create_nonce('DUP_PRO_CTRL_Tools_removeUploadedFilePart'); ?>'
                };
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    dataType: "json",
                    data: data
                }).done(function(){
                    console.log('Removed file part');
                });

        }
    };


    DupPro.Tools.onCancel = function(e)
    {
        DupPro.Tools.upDebug(DPRO_DEBUG, "Cancel");
        var index = $(this).parents("li").data("index");
        DPRO_UPLOADER.upload("abort", parseInt(index, 10));

        var state = $(this).attr('data-state');

        if(state && 'interrupt'==state)
        {}
        else
           DupPro.Tools.toggleStep(11);
    };

    DupPro.Tools.onBeforeSend = function (formData, file)
    {
        DupPro.Tools.upDebug(DPRO_DEBUG, "Before Send");
        var patt = /^[0-9]{8}_[a-zA-Z0-9_]+_[a-z0-9]{20}_[0-9]{14}_archive.(zip|daf)$/;
        // 20191002_dup3_bc0eb2f766318abe8616_20191002101550_archive.zip


        if (file.name.match(patt)) {
            formData.append("is_first_chunk_uploading", DupPro.Tools.firstChunkUploading);
            if (DupPro.Tools.firstChunkUploading) {
                DupPro.Tools.firstChunkUploading = 0;
            }
            return  formData;
        } else {
            alert('Invalid archive file!!!');
            DupPro.Tools.toggleStep(10);
            return false;
        }
    };

    DupPro.Tools.onQueued = function (e, files)
    {
        DupPro.Tools.upDebug(DPRO_DEBUG, "Queued");
        var html = '';
        var size = 0;
        for (var i = 0; i < files.length; i++) {
            size = DupPro.humanFileSize(files[i].size)
            html += '<li data-index="' + files[i].index + '"><span class="content">';
            html += '<span class="file">' + files[i].name + ' (' + size + ')' + '</span><span class="cancel">Cancel</span><span class="progress">Queued</span>';
            html += '</span><span class="bar"></span></li>';
        }
        $(this).parents("form").find(".filelist.queue").append(html);
    };

    DupPro.Tools.onStart = function (e, files)
    {
        $('div.step-err').hide();
        DupPro.Tools.upDebug(DPRO_DEBUG, "Start");
        DupPro.Tools.toggleStep(2);
        //$(this).parents("form").find(".filelist.queue").find("li").find(".progress").text("Waiting");
    };

    DupPro.Tools.onFileStart = function (e, file)
    {
        DupPro.Tools.uploadFileName = file.name;
        DupPro.Tools.firstChunkUploading = 1;
        DupPro.Tools.upDebug(DPRO_DEBUG, "File Start");
        $(this).parents("form").find(".filelist.queue").find("li[data-index=" + file.index + "]").find(".progress").text("0%");
    };

    DupPro.Tools.onFileProgress = function (e, file, percent)
    {
        DupPro.Tools.upDebug(DPRO_DEBUG, "File Progress");
        var $file = $(this).parents("form").find(".filelist.queue").find("li[data-index=" + file.index + "]");

        $file.find(".progress").text(percent + "%")
        $file.find(".bar").css("width", percent + "%");
    };

    DupPro.Tools.onFileComplete = function (e, file, response)
    {
        var data_return = JSON.parse(response);
        var version_pass = true;

        if (typeof data_return.payload.version != "undefined") {
            // alert(data_return.payload.version);
            if(DupPro.Tools.compareVersions(data_return.payload.version, "<?php echo DUPLICATOR_PRO_LIMIT_UPLOAD_VERSION; ?>") === -1) {
                version_pass = false;
            }
        }

        if (version_pass) {
            DupPro.Tools.upDebug(DPRO_DEBUG, "File Complete");
            if (response.trim() === "" || response.toLowerCase().indexOf("error") > -1) {
                $(this).parents("form").find(".filelist.queue")
                        .find("li[data-index=" + file.index + "]").addClass("error")
                        .find(".progress").text(response.trim());
            } else {
                var $target = $(this).parents("form").find(".filelist.queue").find("li[data-index=" + file.index + "]");
                $target.find(".file").text(file.name);
                $target.find(".progress").remove();
                $target.find(".cancel").remove();
                $target.appendTo($(this).parents("form").find(".filelist.complete"));

                $('#dpro-import-tbl-archive-name').text(file.file.name);
                $('#dpro-import-tbl-archive-size').text(file.file.size > 1024 ? DupPro.Tools.bytesCalculator(file.file.size) + ' (' + file.file.size + ' Bytes)' : DupPro.Tools.bytesCalculator(file.file.size) );

                DupPro.Tools.lastArchiveUploaded = file.name;
            }
        } else {
            $("#error-archive-version").text(data_return.payload.zip_version);
            $(".cancel").each(function(){
                $(this).attr('data-state','interrupt').click();
            }).promise().done(function(){
                DPRO_UPLOAD_STEP = 20;

                //$('div.step-state').hide();
                $('div.step-err').hide();
                $('#dpro-step-20').show();
            });
        }
    };

    DupPro.Tools.onFileError = function (e, file, error)
    {
        DupPro.Tools.upDebug(DPRO_DEBUG, "File Error");
        var index = $(this).parents("li").data("index");
        DPRO_UPLOADER.upload("abort", parseInt(index, 10));
        var $target = $(this).parents("form").find(".filelist.queue").find("li[data-index=" + file.index + "]");
        $target.remove();
        DupPro.Tools.toggleStep(12);
    };

    DupPro.Tools.onChunkError = function (e, file, error)
    {
        DupPro.Tools.upDebug(DPRO_DEBUG, "Chunk Error Toggle step 4");
    };

    DupPro.Tools.onComplete = function(e)
    {
        DupPro.Tools.upDebug(DPRO_DEBUG, "Complete");
        if (DPRO_UPLOAD_STEP < 10) {
                DupPro.Tools.toggleStep(3);
        }
    };

    //Empty Handles
    DupPro.Tools.onCancelAll = function(e) { DupPro.Tools.upDebug(DPRO_DEBUG, "Cancel All");}
    DupPro.Tools.onChunkStart = function (e, file) {/*alert('Chunk start');*/ DupPro.Tools.upDebug(DPRO_DEBUG, "Chunk Start");}
    DupPro.Tools.onChunkProgress = function (e, file, percent) {DupPro.Tools.upDebug(DPRO_DEBUG, "Chunk Progress");}
    DupPro.Tools.onChunkComplete = function (e, file, response){DupPro.Tools.upDebug(DPRO_DEBUG, "Chunk Complete");}
    DupPro.Tools.upDebug = function (enable, object) { if (enable) console.log(object);}

    DupPro.Tools.prepArchive = function() {
        var data = {
            action : 'DUP_PRO_CTRL_Tools_prepareArchiveForImport',
            nonce: '<?php echo wp_create_nonce('DUP_PRO_CTRL_Tools_prepareArchiveForImport'); ?>', 'archive-filename': DupPro.Tools.lastArchiveUploaded
        };
        
        $.when($(this).html('<i class="fas fa-circle-notch fa-spin fa-fw"></i> <span>Launching Installer, Please wait..</span>').prop('disabled',true)).then(function( x ) {
            var ajax = $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    cache: false,
                    dataType: "json",
                    data: data,
                    success: function(data) { DupPro.Tools.launchInstaller(); },
                    error: function(data) {
                        alert('<?php DUP_PRO_U::__('An error occurred while preparing the installation of the imported archive.'); ?>');
                        console.log(data)
                    },
                    done: function(data) { console.log(data) }
                });

            ajax.fail(function(jqXHR, textStatus, errorThrown) {
                alert('<?php DUP_PRO_U::__('An error occurred while preparing the installation of the imported archive.'); ?>');
                console.log(jqXHR);
                console.log(textStatus);
                console.log(errorThrown);
            });
        });
    };

    DupPro.Tools.launchInstaller = function() {
        // RSR TODO: call archive/install-backup prep
        if(DupPro.Tools.lastArchiveUploaded != null) {
            var installerUrl = "<?php echo DUPLICATOR_PRO_SITE_URL . '/' . DUPLICATOR_PRO_IMPORT_INSTALLER_NAME; ?>";
            var win = window.open(installerUrl, '_self');
            win.focus();
        } else {
            DupPro.Tools.upDebug(DPRO_DEBUG, "Trying to launch installer when last file uploaded is null!");
        }
    };

    DupPro.Tools.enableLaunchButton = function() {
        if ($('input#enable-installer').is(':checked')) {
            $('#dpro-launch-btn').removeAttr('disabled');
        } else {
            $('#dpro-launch-btn').attr('disabled', 'true');
        }
    };

    //Init
    $('#dpro-daf-upload-btn').click(function() {$('.fs-upload-target"').trigger('click');});
   // $('#dpro-launch-btn').click(DupPro.Tools.prepArchive);
   $('#dpro-launch-btn').click(function(){
       <?php $confirmUpload->showConfirm(); ?>
   });

    DupPro.Tools.initUploader();
    DupPro.Tools.enableLaunchButton();

    // Do interrupt for certain rules
    DupPro.Tools.interrupt = setInterval(function(){
        DupPro.Tools.versionController();
    }, 300);
 });
</script>
