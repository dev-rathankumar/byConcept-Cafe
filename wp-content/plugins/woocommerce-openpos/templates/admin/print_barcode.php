<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php
global $OPENPOS_SETTING;

$sheet_width = $OPENPOS_SETTING->get_option('sheet_width','openpos_label');
$sheet_height = $OPENPOS_SETTING->get_option('sheet_height','openpos_label');
$sheet_padding_top = $OPENPOS_SETTING->get_option('sheet_margin_top','openpos_label');
$sheet_padding_right = $OPENPOS_SETTING->get_option('sheet_margin_right','openpos_label');
$sheet_padding_bottom = $OPENPOS_SETTING->get_option('sheet_margin_bottom','openpos_label');
$sheet_padding_left = $OPENPOS_SETTING->get_option('sheet_margin_left','openpos_label');
$vertical_space = $OPENPOS_SETTING->get_option('sheet_vertical_space','openpos_label');
$horizontal_space = $OPENPOS_SETTING->get_option('sheet_horizontal_space','openpos_label');
$label_width = $OPENPOS_SETTING->get_option('barcode_label_width','openpos_label');
$label_height = $OPENPOS_SETTING->get_option('barcode_label_height','openpos_label');

$label_padding_top = $OPENPOS_SETTING->get_option('barcode_label_padding_top','openpos_label');
$label_padding_right = $OPENPOS_SETTING->get_option('barcode_label_padding_right','openpos_label');
$label_padding_bottom = $OPENPOS_SETTING->get_option('barcode_label_padding_bottom','openpos_label');
$label_padding_left = $OPENPOS_SETTING->get_option('barcode_label_padding_left','openpos_label');

$barcode_width = $OPENPOS_SETTING->get_option('barcode_width','openpos_label');
$barcode_height = $OPENPOS_SETTING->get_option('barcode_height','openpos_label');



$unit = $OPENPOS_SETTING->get_option('unit','openpos_label');


?>
<head>
    <title><?php echo __( 'Sheet Print Information', 'openpos' ); ?></title>
    <?php
        $handes = array(
            'jquery',
        );
        wp_print_scripts($handes);
    ?>
</head>
<body>
    <p  class="page-label"> <?php echo __( 'Sheet Print Information', 'openpos' ); ?></p>
<div class="label-container">
<div class="label-setting">
    <form method="post" id="label-setting-frm">
        <input type="hidden" name="product_id" value="<?php echo $_GET['id']; ?>">
        <div class="form-row" style="width: 100%;margin: 0 auto;">
            <table>
                <tr>
                    <th><?php echo __( 'Unit:', 'openpos' ); ?></th>
                    <td>
                        <select name="unit">
                            <option value="in" <?php echo ($unit == 'in')? 'selected':''; ?>><?php echo __( 'Inch', 'openpos' ); ?></option>
                            <option value="mm" <?php echo ($unit == 'mm')? 'selected':''; ?>><?php echo __( 'Millimeter', 'openpos' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php echo __( 'Sheet Width x Height:', 'openpos' ); ?></th>
                    <td><input type="text" value="<?php echo $sheet_width; ?>" name="sheet_width"> x <input name="sheet_height" type="text" value="<?php echo $sheet_height; ?>"></td>
                </tr>
                <tr>
                    <th><?php echo __( 'Vertical Spacing:', 'openpos' ); ?></th>
                    <td><input type="text" name="sheet_vertical_space" value="<?php echo $vertical_space; ?>"></td>
                </tr>
                <tr>
                    <th><?php echo __( 'Horizontal Spacing:', 'openpos' ); ?></th>
                    <td><input type="text" name="sheet_horisontal_space"  value="<?php echo $horizontal_space; ?>"></td>
                </tr>


                <tr>
                    <th><?php echo __( 'Sheet Margin (top x right x bottom x left):', 'openpos' ); ?></th>
                    <td>
                        <input type="text" name="sheet_margin_top"   value="<?php echo $sheet_padding_top; ?>"> x
                        <input type="text" name="sheet_margin_right" value="<?php echo $sheet_padding_right; ?>"> x
                        <input type="text" name="sheet_margin_bottom" value="<?php echo $sheet_padding_bottom; ?>"> x
                        <input type="text" name="sheet_margin_left" value="<?php echo $sheet_padding_left; ?>">
                    </td>
                </tr>



                <tr>
                    <th><?php echo __( 'Label Size (w x h):', 'openpos' ); ?></th>
                    <td>
                        <input type="text" name="label_width" value="<?php echo $label_width; ?>"> x <input name="label_height" type="text" value="<?php echo $label_height; ?>">
                    </td>
                </tr>
                <tr>
                    <th><?php echo __( 'Label Padding (top x right x bottom x left):', 'openpos' ); ?></th>
                    <td>
                        <input type="text" name="label_margin_top"   value="<?php echo $label_padding_top; ?>"> x
                        <input type="text" name="label_margin_right" value="<?php echo $label_padding_right; ?>"> x
                        <input type="text" name="label_margin_bottom" value="<?php echo $label_padding_bottom; ?>"> x
                        <input type="text" name="label_margin_left" value="<?php echo $label_padding_left; ?>">
                    </td>
                </tr>

                <tr>
                    <th><?php echo __( 'Barcode Image Size ( w x h ):', 'openpos' ); ?></th>
                    <td>
                        <input type="text" name="barcode_width" value="<?php echo $barcode_width; ?>"> x <input name="barcode_height" type="text" value="<?php echo $barcode_height; ?>">
                    </td>
                </tr>

                <tr>
                    <th><?php echo __( 'Number Of Label:', 'openpos' ); ?></th>
                    <td><input type="number" name="total" value="30"></td>
                </tr>
                <tr>
                    <th></th>
                    <td><button type="button" id="print-label-btn" name="print" style="
    border: solid 1px #000;
    padding:  5px 7px;
    text-transform: uppercase;
    margin-top: 15px;
    background:  #000;
    color: #fff;"><?php echo __( 'Print', 'openpos' ); ?></button>
                    <button type="button" style="
    border: solid 1px blue;
    padding:  5px 7px;
    text-transform: uppercase;
    margin-top: 15px;
    background:  blue;
    color: #fff;" id="preview-label-btn">Preview</button>
                </td>
                </tr>
            </table>

        </div>
    </form>
</div>
<div class="preview-live">
    <iframe id="preview-frame" style="width:calc(100% - 1px);height:100%;min-height:490px;    background: #fff;
    border: none;" src="">Preview</iframe>
</div>
</div>
<style>
    form{
        margin-top: 100px;
    }
    form input{
        width: 100px;
        text-align: right;
        padding: 5px 2px;
    }
    .form-row th{
        text-align: left;
        font-size:10px;
    }
    .label-setting{
        width: calc(50% - 2px);
        float: left;
        overflow: auto;
        background: #ccc;
        padding: 5px 0;
        min-height: 500px;
        border:solid 1px #ccc;
    }
    .preview-live{
        float: left;
        height: fit-content;
        display: block;
        width: calc(50% - 2px);
        overflow: auto;
        min-height: 500px;
        border:solid 1px #00BCD4;
        padding: 5px 0;
        background: #00BCD4;
    }
    .page-label{
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        padding: 20px 0;
        text-transform: uppercase;
    }
</style>
<script type="text/javascript">
    (function($) {
        var form_values = $('#label-setting-frm').serialize();
            form_values += "&is_preview=1&is_print=0&action=print_barcode" ;
            var frame_url = '<?php echo admin_url('admin-ajax.php'); ?>?'+form_values;
            $('#preview-frame').attr('src',frame_url);


        $('body').on('click','#preview-label-btn',function () {
                var form_values = $('#label-setting-frm').serialize();
                form_values += "&is_preview=1&is_print=0&action=print_barcode" ;
                var frame_url = '<?php echo admin_url('admin-ajax.php'); ?>?'+form_values;
                $('#preview-frame').attr('src',frame_url);
        })

        $('body').on('click','#print-label-btn',function () {
                var form_values = $('#label-setting-frm').serialize();
                form_values += "&is_preview=0&is_print=1&action=print_barcode" ;
                var frame_url = '<?php echo admin_url('admin-ajax.php'); ?>?'+form_values;
                window.open(frame_url);
        })

    }(jQuery));
</script>
</body>