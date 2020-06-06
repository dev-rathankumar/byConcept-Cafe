<?php
defined("ABSPATH") or die("");
// DUP_PRO_U::hasCapability('read');
DUP_PRO_U::hasCapability('export');

global $wpdb;
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/assets/js/javascript.php');
require_once(DUPLICATOR_PRO_PLUGIN_PATH . '/views/inc.header.php');


function DUP_PRO_DEBUG_TestSetup($CTRL)
{
	$title	= $CTRL['Title'];
	$action = $CTRL['Action'];
	$testable  = $CTRL['Test'] ? 1 : 0;
	$test_css  = $testable ? '' : 'style="display:none"';
	$nonce = isset($CTRL['nonce']) ? $CTRL['nonce'] :wp_create_nonce($action);
	
	$html = <<<EOT
		<div class="keys">
			<input type="hidden" name="testable" value="{$testable}" />
			<input type="hidden" name="action" value="{$action}" />
			<input type="hidden" name="nonce" value="{$nonce}" />
			<span class="result"><i class="fa fa-cube  fa-lg"></i></span>
			<input type='checkbox' id='{$action}' name='{$action}' {$test_css} /> 
			<label for='{$action}'>{$title}</label> &nbsp;
			<a href="javascript:void(0)" onclick="jQuery(this).closest('form').find('div.params').toggle()">Params</a> |
			<a href="javascript:void(0)" onclick="jQuery(this).closest('form').submit()">Test</a>
		</div>
EOT;
	echo $html;
}

?>
<style>
	div.debug-area {line-height: 26px}
	table.debug-toolbar {width:100%; margin: 3px 0 -10px -5px; }
	table.debug-toolbar td {padding:3px; white-space: nowrap}
	table.debug-toolbar td:last-child {width: 100%}
	
	div.debug-area form {margin: 15px 0 0 0; border-top: 1px solid #dfdfdf; padding-top: 5px}
	div.debug-area div.keys label {font-weight: bold; font-size: 14px; padding-right: 5px }
	div.debug-area div.params label {width:150px; display:inline-block}
	div.debug-area input[type=text] {width:400px}
	
	div.section-hdr {margin:35px 0 0 0; font-size: 16px; font-weight: bold; border:1px solid silver; border-radius: 3px; padding:1px 5px 1px 5px; background: #dfdfdf;}
	div.params {display:none}
	i.result-pass {color:green}
	i.result-fail {color:red}
</style>

<script>	
	var UNIT_TEST_FORMS;
	var UNIT_TEST_CHKBOXES;
	var UNIT_TEST_PASSED;
	var UNIT_TEST_COUNTER;
	var UNIT_TEST_RUNNING = false;
</script>

<div class="wrap dup-wrap dup-support-all">
	<h1><?php DUP_PRO_U::esc_html_e('Testing Interface'); ?></h1>
    <hr size="1" />
	
	<table class="debug-toolbar">
		<tr>
			<td>
				<span id="results-all"><i class="fa fa-cube fa-lg"></i></span> 
				<input id="test-checkall" type="checkbox" onclick="DupPro.Debug.CheckAllTests()">
			</td>
			<td>
				<input type="button" class="button button-small" value="<?php DUP_PRO_U::esc_attr_e('Run Tests'); ?>" onclick="DupPro.Debug.RunTests()" />
				<input type="button" class="button button-small" value="<?php DUP_PRO_U::esc_attr_e('Refresh Page'); ?>" onclick="window.location.reload();" />
			</td>
			<td> <input type="checkbox" id="test-openwindow" onchange="DupPro.Debug.TestNewWindow()" /> <label for="test-openwindow">Tests in new window</label> </td>
		</tr>
	</table>

	<div class="debug-area">
		<?php
			include_once 'tst.tools.php';
			include_once 'tst.packages.php';	
		?>
	</div>
</div>

<script>	
jQuery(document).ready(function($) 
{
	//Run test on all checked options
	DupPro.Debug.RunTests = function()
	{
		try 
		{
			UNIT_TEST_RUNNING	= true;
			UNIT_TEST_PASSED	= true;
			UNIT_TEST_COUNTER	= 0;
			UNIT_TEST_CHKBOXES	= $("div.keys input[type='checkbox']:checked").length;
			UNIT_TEST_FORMS		= $("div.keys input[name='testable'][value='1']").closest('form');

			$(UNIT_TEST_FORMS).each(function(index) 
			{
				var $form = $(this);
				var $result = $form.find('span.result');
				var $check  = $form.find('div.keys input[type="checkbox"]');
				var input;

				if ($check.is(':checked')) 
				{
					$('#results-all').html('<i class="fa fa-cog fa-sm fa-spin fa-fw fa-lg"></i>');
					$result.html('<i class="fas fa-circle-notch fa-spin fa-fw fa-lg"></i>');

					//Run any callbacks if defined
					if ($form.attr("onsubmit") != undefined) {
						$form.submit();
					}
					input	= $form.serialize();

					$.ajax({
						type: "POST",
						url: ajaxurl,
						data: input,
						success: function(respData) {
							try {
								var data = DupPro.parseJSON(respData);								
							} catch(err) {
								console.error(err.message);
								console.error('JSON parse failed for response data: ' + respData);
								return false;
							}
							DupPro.Debug.ProcessResult(data, $result);
						},
						error: function(data) {},
						done: function(data) {}
					});
				}
			});
		} 
		catch (e) {
			console.log(e);
		} finally {
			UNIT_TEST_RUNNING = false;
		}			
	}
	
	//Call back used to test the status of a result
	DupPro.Debug.ProcessResult = function(data, result)
	{	
		UNIT_TEST_COUNTER++;
		var status = data.report.status || 0;
		console.log('Processing Results' + UNIT_TEST_COUNTER)
		console.log(data);
		
		if (status > 0) {
			result.html('<i class="fa fa-check-circle fa-lg result-pass"></i>');
		} else {
			UNIT_TEST_PASSED = false;
			result.html('<i class="fa fa-check-circle fa-lg result-fail"></i>');
		}
		
		//Set after all tests have ran
		if (UNIT_TEST_COUNTER >= UNIT_TEST_CHKBOXES) {
			(UNIT_TEST_PASSED)
				? $('#results-all').html('<i class="fa fa-check-circle fa-lg result-pass"></i>')
				: $('#results-all').html('<i class="fa fa-check-circle fa-lg result-fail"></i>');
		}
	}
	
	//Check all of the check boxes
	DupPro.Debug.CheckAllTests = function()
	{
		var checkAll = $('#test-checkall').is(':checked');
		$("div.keys input[type='checkbox']:visible").each(function() {
			(checkAll) 
				? $(this).attr('checked', '1')
				: $(this).removeAttr('checked');
		});
	}
	
	//Test links will open in seperate window if checked
	DupPro.Debug.TestNewWindow = function()
	{
		var check = $('#test-openwindow').is(':checked');
		var count = 0;
		$("form").each(function(index) 
		{	
			count++;
			(check) 
				? $(this).attr('target', 'dup_debug' + count)
				: $(this).attr('target', 'dup_debug');
		});
	}
	
	//INIT
	$("form").each(function(index) 
	{	
		var $form = $(this);
		$form.attr('action', 'admin-ajax.php');
		$form.attr('target', 'dup_debug');
		$form.attr('method', 'post');
	});
	
});	
</script>
