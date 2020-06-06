<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php
    global $op_warehouse;
    $id = isset($_GET['id']) ? $_GET['id'] : 0;
    $warehouse = $op_warehouse->get($id);
    $warehouse_name = isset($warehouse['name']) ? $warehouse['name'] : '';
?>
<div class="wrap">
    <h1><?php echo implode(__(' of ','openpos'),array( __( 'Inventory', 'openpos' ),$warehouse_name ) ); ?></h1>
    <form id="op-product-list" onsubmit="return false;">
        <input type="hidden" name="action" value="admin_openpos_update_inventory_grid">
        <input type="hidden" name="warehouse_id" value="<?php echo $id; ?>">
        <table id="grid-selection" class="table table-condensed table-hover table-striped op-product-grid">
            <thead>
            <tr>
                <th data-column-id="id" data-identifier="true" data-type="numeric"><?php echo __( 'ID', 'openpos' ); ?></th>
                <th data-column-id="barcode" data-identifier="true" data-type="numeric"><?php echo __( 'Barcode', 'openpos' ); ?></th>
                <th data-column-id="product_thumb" data-sortable="false"><?php echo __( 'Thumbnail', 'openpos' ); ?></th>
                <th data-column-id="post_title" data-sortable="false"><?php echo __( 'Product Name', 'openpos' ); ?></th>
                <th data-column-id="price" data-sortable="false"><?php echo __( 'Price', 'openpos' ); ?></th>
                <th data-column-id="qty" data-type="numeric" data-sortable="false"><?php echo __( 'Qty', 'openpos' ); ?></th>
            </tr>
            </thead>
        </table>
    </form>
    <br class="clear">
</div>


<script type="text/javascript">
    (function($) {
        "use strict";
        var grid = $("#grid-selection").bootgrid({
            ajax: true,
            post: function ()
            {
                /* To accumulate custom parameter with the request object */
                return {
                    action: "op_inventory",
                    warehouse_id : <?php echo $id; ?>
                };
            },
            url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
            selection: true,
            multiSelect: true,
            formatters: {
                "link": function(column, row)
                {
                    return "<a href=\"#\">" + column.id + ": " + row.id + "</a>";
                },
                "price": function(column,row){

                    return row.formatted_price;
                }
            },
            templates: {
                header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-6 actionBar\" ><a type=\"button\" href=\"<?php echo admin_url('admin-ajax.php?action=op_export_inventory&warehouse_id='.$id);?>\" class=\"btn pull-left btn-default\" data-action=\"export\">Export</a>&nbsp;<a type=\"button\" href=\"<?php echo admin_url('admin.php?page=op-warehouses&op-action=adjust_stock&warehouse_id=' . esc_attr($id));?>\" class=\"btn pull-left btn-default\" data-action=\"export\">Adjust Stock</a></div><div class=\"col-sm-6 actionBar\"><p class=\"{{css.search}}\"></p><p class=\"{{css.actions}}\"></p><button type=\"button\" class=\"btn vna-action btn-default\" data-action=\"save\"><span class=\" icon glyphicon glyphicon-floppy-save\"></span></button></div></div></div>"
            }
        }).on("initialized.rs.jquery.bootgrid",function(){

        }).on("selected.rs.jquery.bootgrid", function(e, rows)
        {
            var rowIds = [];
            for (var i = 0; i < rows.length; i++)
            {
                rowIds.push(rows[i].id);

                if($('input[name="qty['+rows[i].id+']"]'))
                {
                    $('input[name="qty['+rows[i].id+']"]').prop('disabled',false);
                }
            }

            // alert("xxSelect: " + rowIds.join(","));
        }).on("deselected.rs.jquery.bootgrid", function(e, rows)
        {
            var rowIds = [];
            for (var i = 0; i < rows.length; i++)
            {
                rowIds.push(rows[i].id);

                if($('input[name="qty['+rows[i].id+']"]'))
                {
                    $('input[name="qty['+rows[i].id+']"]').prop('disabled',true);
                }
            }
            //alert("Deselect: " + rowIds.join(","));
        });
        $('.vna-action').click(function(){
            var selected = $("#grid-selection").find('input[type="checkbox"]:checked');
            var action = $(this).data('action');
            if(selected.length == 0)
            {
                alert('Please choose row to continue.');
            }else{
                $.ajax({
                    url: openpos_admin.ajax_url,
                    type: 'post',
                    dataType: 'json',
                    //data:$('form#op-product-list').serialize(),
                    data: {action: 'admin_openpos_update_inventory_grid',warehouse_id:<?php echo $id; ?>,data:$('form#op-product-list').serialize()},
                    success:function(data){
                        alert('Saved');
                    }
                })

            }

        });
    })( jQuery );
</script>

<style>
    .action-row a{
        display: block;
        padding: 3px 4px;
        text-decoration: none;
        border: solid 1px #ccc;
        text-align: center;
        margin: 5px;
    }
    .op-product-grid td{
        vertical-align: middle!important;
    }
</style>