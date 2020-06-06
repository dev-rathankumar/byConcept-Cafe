<?php
/**
 * Created by PhpStorm.
 * User: anhvnit
 * Date: 10/21/18
 * Time: 12:05
 */
global $op_in_bill_screen;
$op_in_bill_screen = true;
$base_dir = dirname(dirname(dirname(dirname(__DIR__))));
require_once ($base_dir.'/wp-load.php');
global $op_register;
$id = esc_attr($_GET['id']);
$register = $op_register->get((int)$id);

?>
<?php if(!empty($register)):  ?>
<html lang="en" style="height: calc(100% - 0px);">
<head>
    <meta charset="utf-8">
    <title>Bill Screen - <?php echo $register['name']; ?></title>
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <script>
        var data_url = '<?php echo $op_register->bill_screen_file_url($register['id']); ?>';
        var data_template= <?php echo json_encode(array('template' => $op_register->bill_template()));?>;
    </script>
    <?php
    $handes = array(
        'openpos.bill.style'
    );
    wp_print_styles($handes);
    ?>

</head>
<body>
<div  id="bill-content"></div>

<?php
$handes = array(
    'openpos.bill.script'
);
wp_print_scripts($handes);
?>

</body>
</html>
<?php else: ?>
    <h1>Opppos !!!!</h1>
<?php endif; ?>

