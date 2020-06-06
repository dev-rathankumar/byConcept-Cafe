<?php
/**
 * Created by PhpStorm.
 * User: anhvnit
 * Date: 10/21/18
 * Time: 12:05
 */
global $op_in_kitchen_screen;
$op_in_kitchen_screen = true;
$base_dir = dirname(dirname(dirname(dirname(__DIR__))));
require_once ($base_dir.'/wp-load.php');
global $op_table;
global $op_woo;
$id = isset($_GET['id']) ? esc_attr($_GET['id']) : 0;

if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'clear_data')
{
    $op_table->clear_takeaway();
    exit;
}
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'update_ready')
{
    $id_str = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
    $tmp = explode('-',$id_str);
    if(count($tmp) >= 2)
    {
        $table_id = $tmp[1]; //end($tmp);
        $item_id = $tmp[0];
        $table_type = isset($tmp[2]) ? $tmp[2]: 'dine_in';
        $table_data = $op_table->bill_screen_data($table_id,$table_type);
        $ver = $table_data['ver'];
        $online_ver = $table_data['online_ver'];
        if($online_ver > $ver)
        {
            $ver = $online_ver;
        }
        $table_data['ver'] = $ver + 10;
        $table_data['online_ver'] = $ver + 20;
        $items = array();
        foreach($table_data['items'] as $item)
        {
            if($item['id'] == $item_id)
            {
                $item['done'] = 'ready';
            }
            $items[] = $item;
        }
        $table_data['items'] = $items;
        $op_table->update_table_bill_screen($table_id,$table_data,$table_type);

    }

    echo json_encode(array());exit;
}
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'get_data')
{
    $warehouse_id = isset($_REQUEST['warehouse']) ? $_REQUEST['warehouse'] : -1;
    $view_type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'all';
    $result = array();
    $result_formated = array();
    if($warehouse_id >= 0)
    {
        $off_tables = $op_table->tables((int)$warehouse_id);
        $takeaway_tables = $op_table->takeawayTables((int)$warehouse_id);
        $tables = array_merge($off_tables,$takeaway_tables);

        foreach($tables as $table)
        {

            $table_type = isset($table['dine_type'])? $table['dine_type'] :'dine_in';
           
            $table_data = $op_table->bill_screen_data($table['id'],$table_type);
          

            if(isset($table_data['parent']) && $table_data['parent'] == 0 && isset($table_data['items'])  && count($table_data['items']) > 0)
            {
                $items = $table_data['items'];
                foreach($items as $item)
                {

                    if(isset($item['done']) && ($item['done'] == 'done' || $item['done'] == 'done_all'))
                    {

                        continue;
                    }
                    $id = (int)$item['id'];
                    if($view_type != 'all')
                    {
                        $product_id = isset($item['product_id']) ? $item['product_id'] : 0;
                        if(!$product_id)
                        {
                            continue;
                        }
                        if(!$op_woo->check_product_kitchen_op_type($view_type,$product_id)){
                            continue;
                        }
                    }

                    $timestamp = (int)($item['id'] / 1000);
                    $timestamp += wc_timezone_offset();

                    $order_time = '--:--';
                    if($timestamp)
                    {
                        $order_time = date('h:i',$timestamp);
                    }
                    $dish_id = $id.'-'.$table['id'];
                    if($table_type && $table_type != 'dine_in')
                    {
                        $dish_id.= '-'.$table_type;
                    }
                    $tmp = array(
                        'id' => $dish_id,
                        'priority' => 1,
                        'item' => $item['name'],
                        'qty' => $item['qty'],
                        'table' => $table['name'],
                        'order_time' => $order_time,
                        'note' => $item['sub_name'],
                        'dining' => isset($item['dining']) ? $item['dining'] : '',
                        'done' => isset($item['done']) ? $item['done'] : ''
                    );
                    $result[$id] = $tmp;
                }
            }
        }


    }
    
    if(!empty($result))
    {
        $i = 1;

        foreach($result as $r)
        {
            $r['priority'] = $i;
            $result_formated[] = $r;
            $i++;
        }

    }
    echo json_encode($result_formated);exit;

}


$kitchen_type = isset($_REQUEST['type']) ? esc_attr($_REQUEST['type']) : 'all';
$all_area = $op_woo->getListRestaurantArea();
?>
<html lang="en" style="height: calc(100% - 0px);">
<head>
    <meta charset="utf-8">
    <title>Kitchen Screen</title>
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        var  kitchen_type = '<?php echo $kitchen_type; ?>';
        var data_url = '<?php echo OPENPOS_URL.'/kitchen/index.php' ?>';
        var data_warehouse_id = '<?php echo $id; ?>';
        var data_template= '<tr><td class="text-center"><%= priority %></td><td class="item-name"><span class="dining <%- dining %>"><%- dining %></span><%= item %><p class="item-note"><%- note %></p></td><td class="text-center"><%= qty %></td><td><%= order_time %></td><td><%= table %></td><td class="text-center"><% if (done != "ready" && done != "done" ) { %> <a data-id="<%- id %>" href="javascript:void(0);" class="is_cook_ready"> <span class="glyphicon glyphicon-bell" aria-hidden="true"></span> </a> <% } else { %> <span class="glyphicon glyphicon-ok" aria-hidden="true"></span> <% } %></td></tr>';
    </script>
    <?php
    $handes = array(
        'openpos.kitchen.style'
    );
    wp_print_styles($handes);
    ?>

</head>
<body>

<div class="container">

    <div class="row">
        <div class="col-md-12 text-center">
            <h3><?php echo __('KitChen View','openpos'); ?></h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <form class="form-horizontal">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label"><?php echo __('View Type','openpos'); ?></label>
                    <div class="col-sm-8">
                        <select class="form-control" name="kitchen_type">
                            <option value="<?php echo OPENPOS_URL.'/kitchen/index.php?type=all&id='.$id; ?>" <?php echo ($kitchen_type == 'all') ? 'selected':'';?> > <?php echo __('All','openpos'); ?></option>
                            <?php foreach($all_area as $a_code => $area): ?>
                                <option value="<?php echo OPENPOS_URL.'/kitchen/index.php?type='.esc_attr($a_code).'&id='.$id; ?>" <?php echo ($kitchen_type == $a_code ) ? 'selected':'';?> ><?php echo $area['label']; ?></option>
                            <?php endforeach; ?>

                        </select>

                    </div>
                    <div class="col-sm-1 pull-left">
                        <p><a href="javascript:void(0);" data-id="<?php echo $id; ?>" id="refresh-kitchen"> <span class="glyphicon glyphicon-retweet" aria-hidden="true"></span> </a></p>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <div  id="bill-content">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="text-center">#</th>
                <th><?php echo __('Item','openpos'); ?></th>
                <th class="text-center"><?php echo __('Qty','openpos'); ?></th>
                <th><?php echo __('Order Time','openpos'); ?></th>
                <th><?php echo __('Table','openpos'); ?></th>
                <th class="text-center"><?php echo __('Ready ?','openpos'); ?></th>
            </tr>
            </thead>
            <tbody id="kitchen-table-body">

            </tbody>
        </table>
    </div>

</div>


<?php
$handes = array(
    'openpos.kitchen.script'
);
wp_print_scripts($handes);
?>

<button id="button-notification" style="display: none;"  type="button"></button>
<script type="text/javascript">

    (function($) {

        $(document).ready(function(){
            $('#button-notification').on('click',function(){
                $.playSound("<?php echo OPENPOS_URL.'/assets/sound/helium.mp3' ?>");
            });
            $('body').on('new-dish-come',function(){
                $('#button-notification').trigger('click');
            })

        });
    }(jQuery));


</script>

<style  type="text/css">
    .item-name{
        position: relative;
    }
    span.dining{
       display: none;
    }
    span.dining.takeaway{
        display: block;
        position: absolute;
        top: 2px;
        right: 2px;
        border:none;
        padding: 2px 10px;
        font-size: 12px;
        color: #fff;
        background: #005724;
        border-radius: 10px;
    }
</style>
</body>
</html>