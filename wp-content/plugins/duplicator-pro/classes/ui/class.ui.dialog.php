<?php
defined("ABSPATH") or die("");

/**
 * Used to generate a thick box inline dialog such as an alert or confirm pop-up
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DUP_PRO
 * @subpackage classes/ui
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 * @since 3.3.0
 *
 */
class DUP_PRO_UI_Dialog
{

    /**
     * if not empty set class on wrapper buttons div
     * 
     * @var string 
     */
    public $wrapperClassButtons = null;

    /**
     * The title that shows up in the dialog
     */
    public $title;

    /**
     * The message displayed in the body of the dialog
     */
    public $message;

    /**
     * The width of the dialog the default is used if not set
     * Alert = 475px (default) |  Confirm = 500px (default)
     */
    public $width;

    /**
     * The height of the dialog the default is used if not set
     * Alert = 125px (default) |  Confirm = 150px (default)
     */
    public $height;

    /**
     * When the progress meter is running show this text
     * Available only on confirm dialogs
     */
    public $progressText;

    /**
     * When true a progress meter will run until page is reloaded
     * Available only on confirm dialogs
     */
    public $progressOn = true;

    /**
     * The javascript call back method to call when the 'Yes' or 'Ok' button is clicked 
     */
    public $jsCallback = null;
    public $okText;
    public $cancelText;

    /**
     * If true close dialog on confirm
     * @var bool
     */
    public $closeOnConfirm = false;

    /**
     * The id given to the full dialog
     */
    private $id;

    /**
     * A unique id that is added to all id elements
     */
    private $uniqid;

    /**
     *  Init this object when created
     */
    public function __construct()
    {
        add_thickbox();
        $this->progressText = DUP_PRO_U::__('Processing please wait...');
        $this->uniqid       = substr(uniqid('', true), 0, 14).mt_rand();
        $this->id           = 'dpro-dlg-'.$this->uniqid;
        $this->okText       = DUP_PRO_U::__('OK');
        $this->cancelText   = DUP_PRO_U::__('Cancel');
    }

    /**
     * Gets the unique id that is assigned to each instance of a dialog
     *
     * @return int      The unique ID of this dialog
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * Gets the unique id that is assigned to each instance of a dialogs message text
     *
     * @return int      The unique ID of the message
     */
    public function getMessageID()
    {
        return "{$this->id}_message";
    }

    /**
     * Initialize the alert base html code used to display when needed
     *
     * @return string	The html content used for the alert dialog
     */
    public function initAlert()
    {
        $onClickClose = '';
        if (!is_null($this->jsCallback)) {
            $onClickClose .= $this->jsCallback.';';
        }
        $onClickClose .= 'tb_remove();';

        $html = '
		<div id="'.esc_attr($this->id).'" style="display:none">
			<div class="dpro-dlg-alert-txt" id="'.esc_attr($this->id).'-alert-txt">
				<span id="'.esc_attr($this->id).'_message">'.$this->message.'</span>
			</div>
			<div class="dpro-dlg-alert-btns '.esc_attr($this->wrapperClassButtons).'">
				<input id="'.esc_attr($this->id).'-confirm" type="button" class="button button-large" value="'.esc_attr($this->okText).'" onclick="'.$onClickClose.'" />
			</div>
		</div>';

        echo $html;
    }

    /**
     * Shows the alert base JS code used to display when needed
     *
     * @return string	The JS content used for the alert dialog
     */
    public function showAlert()
    {
        $this->width  = is_numeric($this->width) ? $this->width : 500;
        $this->height = is_numeric($this->height) ? $this->height : 175;

        $html = "tb_show('".esc_js($this->title)."', '#TB_inline?width=".esc_js($this->width)."&height=".esc_js($this->height)."&inlineId=".esc_js($this->id)."');\n".
            "var styleData = jQuery('#TB_window').attr('style') + 'height: ".esc_js($this->height)."px !important';\n".
            "jQuery('#TB_window').attr('style', styleData);";

        echo $html;
    }

    /**
     * js code to update html message content from js var name
     * 
     * @param string $jsVarName
     */
    public function updateMessage($jsVarName)
    {
        $js = '$("#'.$this->getID().'_message").html('.$jsVarName.');';
        echo $js;
    }

    /**
     * Shows the confirm base JS code used to display when needed
     *
     * @return string	The JS content used for the confirm dialog
     */
    public function initConfirm()
    {
        $progress_data  = '';
        $progress_func2 = '';

        $onClickConfirm = '';
        if (!is_null($this->jsCallback)) {
            $onClickConfirm .= $this->jsCallback.';';
        }

        //Enable the progress spinner
        if ($this->progressOn) {
            $progress_func1 = "__dpro_dialog_".$this->uniqid;
            $progress_func2 = ";{$progress_func1}(this)";
            $progress_data  = <<<HTML
				<div class='dpro-dlg-confirm-progress' id="{$this->id}-progress"><i class='fa fa-circle-notch fa-spin fa-lg fa-fw'></i> {$this->progressText}</div>
				<script> 
					function {$progress_func1}(obj) 
					{
                        (function($,obj){
                            console.log($('#{$this->id}'));
                            // Set object for reuse
                            var e = $(obj);
                            // Check and set progress
                            if($('#{$this->id}-progress'))  $('#{$this->id}-progress').show();
                            // Check and set confirm button
                            if($('#{$this->id}-confirm'))   $('#{$this->id}-confirm').attr('disabled', 'true');
                            // Check and set cancel button
                            if($('#{$this->id}-cancel'))    $('#{$this->id}-cancel').attr('disabled', 'true');
                        }(window.jQuery, obj));
					}
				</script>
HTML;
            $onClickConfirm .= $progress_func2.';';
        }

        if ($this->closeOnConfirm) {
            $onClickConfirm .= 'tb_remove();';
        }

        $html = '
			<div id="'.esc_attr($this->id).'" style="display:none">
				<div class="dpro-dlg-confirm-txt" id="'.esc_attr($this->id).'-confirm-txt">
					<span id="'.esc_attr($this->id).'_message">'.$this->message.'</span>
					<br/><br/>
					'.$progress_data.'
				</div>
				<div class="dpro-dlg-confirm-btns '.esc_attr($this->wrapperClassButtons).'">
					<input id="'.esc_attr($this->id).'-confirm" type="button" class="button button-large" value="'.esc_attr($this->okText).'" onclick="'.$onClickConfirm.'" />
					<input id="'.esc_attr($this->id).'-cancel" type="button" class="button button-large" value="'.esc_attr($this->cancelText).'" onclick="tb_remove()" />
				</div>
			</div>';

        echo $html;
    }

    /**
     * Shows the confirm base JS code used to display when needed
     *
     * @return string	The JS content used for the confirm dialog
     */
    public function showConfirm()
    {
        $this->width  = is_numeric($this->width) ? $this->width : 500;
        $this->height = is_numeric($this->height) ? $this->height : 225;
        $html         = "tb_show('".esc_js($this->title)."', '#TB_inline?width=".esc_js($this->width)."&height=".esc_js($this->height)."&inlineId=".esc_js($this->id)."');\n".
            "var styleData = jQuery('#TB_window').attr('style') + 'height: ".esc_js($this->height)."px !important';\n".
            "jQuery('#TB_window').attr('style', styleData);";

        echo $html;
    }
}