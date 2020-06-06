<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<html>
<head>

    <?php
        $handes = array(
            'openpos.admin.receipt.ejs'
        );
        wp_print_scripts($handes);
        
    ?>
    <link rel="stylesheet" media="print,screen" href="<?php echo OPENPOS_URL.'/pos/font.css'; ?>"/>
    <?php echo $data['html_header']; ?>
</head>
<body style="margin:0;">

<script type="text/javascript">
    (function($) {

        $(document).ready(function(){
            <?php if(!$data['order_json'] || $data['order_json'] == ''): ?>
            var  order = <?php echo file_get_contents(OPENPOS_DIR.'default/order_formatted.txt'); ?>;
            <?php else: ?>
            var  order = <?php echo $data['order_json']; ?>;
            <?php endif; ?>
            var template = '<?php echo $data['html_body']; ?>';

            var html = ejs.render(template, order);

            $('body').html(html);
            <?php if(!isset($is_review) || !$is_review): ?>
                window.print();
            <?php endif; ?>
        });

    }(jQuery));

</script>
</body>
</html>