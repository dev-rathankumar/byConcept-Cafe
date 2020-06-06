<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php
    global $OPENPOS_SETTING;
    global $OPENPOS_CORE;
    global $barcode_generator;
    global $unit;
    global $mode;

    global $_barcode_width;
    global $_barcode_height;
    $is_preview = isset($_REQUEST['is_preview']) && $_REQUEST['is_preview'] == 1 ? true : false;
    $product_id_str = isset($_REQUEST['product_id']) ? $_REQUEST['product_id'] : '';
    $product_ids = explode(',',$product_id_str);
    //sheet
    $sheet_width = floatval($_REQUEST['sheet_width']);
    $sheet_height = floatval($_REQUEST['sheet_height']);
    $sheet_padding_top = floatval($_REQUEST['sheet_margin_top']);
    $sheet_padding_right = floatval($_REQUEST['sheet_margin_right']);
    $sheet_padding_bottom = floatval($_REQUEST['sheet_margin_bottom']);
    $sheet_padding_left = floatval($_REQUEST['sheet_margin_left']);
    $vertical_space = floatval($_REQUEST['sheet_vertical_space']);
    $horizontal_space = floatval($_REQUEST['sheet_horisontal_space']);

    //label
    $label_width = floatval($_REQUEST['label_width']);
    $label_height = floatval($_REQUEST['label_height']);
    $label_margin_top = floatval($_REQUEST['label_margin_top']);
    $label_margin_right = floatval($_REQUEST['label_margin_right']);
    $label_margin_bottom = floatval($_REQUEST['label_margin_bottom']);
    $label_margin_left = floatval($_REQUEST['label_margin_left']);

    $barcode_width = floatval($_REQUEST['barcode_width']);
    $barcode_height = floatval($_REQUEST['barcode_height']);
    //other
    $unit = sanitize_text_field($_REQUEST['unit']);
    $total = intval($_REQUEST['total']);
    //calc

    $sheet_space_width = $sheet_width - $sheet_padding_left - $sheet_padding_right + $horizontal_space ;
    $sheet_space_height = $sheet_height - $sheet_padding_top - $sheet_padding_bottom + $vertical_space ;
    $columns = floor($sheet_space_width / ($label_width + $horizontal_space));
    $rows = floor($sheet_space_height / ($label_height + $vertical_space));

    $truth_label_width = $label_width;
    $truth_label_height = $label_height;

    if($rows == 0){ $rows = 1; }
    if($columns == 0){$columns = 1;}
    $label_per_sheet = $rows * $columns;
    $page = ceil($total / $label_per_sheet);
    $count = 0;

    $_barcode_width = $barcode_width;
    $_barcode_height = $barcode_height;

    $templates = array();
    foreach($product_ids as $product_id)
    {
        if($product_id)
        {
            $_op_product = wc_get_product(intval($product_id));
            $templates[] = balanceTags(do_shortcode($OPENPOS_SETTING->get_option('barcode_label_template','openpos_label')),true);
            //$template = balanceTags(do_shortcode($OPENPOS_SETTING->get_option('barcode_label_template','openpos_label')),true);
        }
    }

    $template_count = 0;

?>
<?php ob_start(); ?>
<body style="background-color: transparent;padding:0;margin:0;">
    <?php for($k = 1;$k <= $page;$k++): ?>
    <div style="width: <?php echo $sheet_width.$unit;?>;height:<?php echo $sheet_height.$unit; ?>;  display: block; overflow: hidden; background-color: transparent;" class="sheet">
        <div style="display: block; overflow: hidden;background-color: transparent; margin-left:<?php echo $sheet_padding_left.$unit; ?>;margin-right:<?php echo $sheet_padding_right.$unit; ?>;margin-top:<?php echo $sheet_padding_top.$unit; ?>;margin-bottom:<?php echo $sheet_padding_bottom.$unit; ?>;">
        <?php for($i = 0; $i < $rows; $i++): ?>
            <div class="label-row" style="margin-bottom: <?php echo ($i != ($rows - 1)) ? $horizontal_space.$unit:0;?>; display: block;width: 100%;">
                <?php for($j = 0; $j < $columns; $j++): $count++; ?>
                    <?php
                        $template = $templates[$template_count];
                        if($template_count == (count($templates) - 1 ))
                        {
                            $template_count = 0;
                        }else{
                            $template_count ++;

                        }
                    ?>
                    <div class="label  <?php echo $count; ?>"  style=" text-align: center;overflow: hidden; width: <?php echo $truth_label_width.$unit; ?>;height: <?php echo $truth_label_height.$unit; ?>; display: inline-block;overflow: hidden; <?php echo ($j != ($columns - 1))? 'margin-right:'.$horizontal_space.$unit:'';?> " >
                        <div class="label-element-container">
                        <?php echo $template; ?>
                        </div>
                    </div>
                    <?php if($j == 0 && $j < ($columns - 1)): ?>
                    <div class="label-vertical-space" style="width: <?php echo $vertical_space.$unit; ?>;height: <?php echo $truth_label_height.$unit; ?>; display: inline-block;overflow: hidden;"></div>
                    <?php endif; ?>
                <?php if($count == $total){ break; }  endfor; ?>
            </div>
        <?php if($count == $total){ break; }  endfor; ?>
        </div>
    </div>

    <?php if($count == $total){ break; }  endfor; ?>
</body>
<?php
$out2 = ob_get_contents();

ob_end_clean();
$buffer = preg_replace('/\s+/', ' ', $out2);


$search = array(
    '/\>[^\S ]+/s',
    '/[^\S ]+\</s',
    '/(\s)+/s'
);
$replace = array(
    '>',
    '<',
    '\\1'
);
if (preg_match("/\<html/i",$buffer) == 1 && preg_match("/\<\/html\>/i",$buffer) == 1) {
    $buffer = preg_replace($search, $replace, $buffer);
}
$buffer = str_replace('> <', '><', $buffer);
?>
<html>
<head>
    <title>barcode</title>
    <?php if(!$is_preview): ?>
        <script type="application/javascript">
            window.print();
        </script>
        <style media="print">
            @page {
                size: <?php echo $sheet_width.$unit;?> <?php echo $sheet_height.$unit; ?>;
                padding:0;
                

            }
            .sheet{
                width: 100%;
            }
            .label{
                overflow: hidden;
            }
        </style>
    <?php else: ?>
    
        <style media="all">
            @page {
                size: <?php echo $sheet_width.$unit;?> <?php echo $sheet_height.$unit; ?>;
                padding:0;
                overflow: hidden;
            }
            .sheet{
                width: 100%;
                background-color: #FFEB3B!important;
            }
            .label{
                overflow: hidden;
                background-color:#ccc;
                border-radius: 5px;
            }

        </style>
    <?php endif; ?>
    
</head>
<?php echo $buffer; ?>
</html>
