<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

?>
<div id="ajaxerr-area" class="no-display">
    <p>
        <b>ERROR:</b> <span class="message"></span><br>
        <i>Please try again an issue has occurred.</i>
    </p>
    <div>Please see the <?php DUPX_View_Funcs::installerLogLink(); ?> file for more details.</div>
    <div id="ajaxerr-data">
        <div class="html-content" ></div>
        <pre class="pre-content"></pre>
    </div>
    <p>
        <b>Additional Resources:</b><br/>
        &raquo; <a target='_blank' href='https://snapcreek.com/duplicator/docs/'>Help Resources</a><br/>
        &raquo; <a target='_blank' href='https://snapcreek.com/duplicator/docs/faqs-tech/'>Technical FAQ</a>
    </p>
    <div style="text-align:center; margin:10px auto 0px auto">
        <input id="ajax-error-try-again" type="button" class="default-btn" value="&laquo; Try Again" /><br/><br/>
        <i style='font-size:11px'>See online help for more details at <a href='https://snapcreek.com/ticket' target='_blank'>snapcreek.com</a></i>
    </div>
</div>