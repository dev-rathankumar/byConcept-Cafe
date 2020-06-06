<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $op_warehouse;
$warehouse_id = isset($_REQUEST['warehouse_id']) ? intval($_REQUEST['warehouse_id']) : -1;
$warehouses = $op_warehouse->warehouses();
?>
<div class="wrap">
    <h1><?php echo __( 'POS Stock Overview', 'openpos' ); ?></h1>
    <div style="display: block; width: 100%">
        <div style="width: 500px;margin: 0 auto;">
            <div class="row">
                <div class="col-md-12">
                    <form class="form-horizontal" type="get" action="<?php echo admin_url( 'admin.php' ); ?>">
                        <input type="hidden" name="page" value="op-stock">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-4 control-label"><?php echo __( 'Choose Warehouse', 'openpos' ); ?></label>
                            <div class="col-sm-6">
                                <select name="warehouse_id" class="form-control">
                                    <option value="-1" <?php echo ($warehouse_id == -1) ? 'selected':''; ?> ><?php echo __( 'All Warehouse', 'openpos' ); ?></option>
                                    <?php foreach($warehouses as $warehouse): ?>
                                    <option value="<?php echo $warehouse['id']; ?>" <?php echo ($warehouse_id == $warehouse['id']) ? 'selected':''; ?>  ><?php echo $warehouse['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-2"><input type="submit" class="btn btn-success" value="<?php echo __( 'Choose', 'openpos' ); ?>" ></div>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
    <form id="op-product-list"  onsubmit="return false;">
        <input type="hidden" name="action" value="admin_openpos_update_product_grid">
        <table id="grid-selection" class="table table-condensed table-hover table-striped op-product-grid">
            <thead>
            <tr>
                <th data-column-id="id" data-identifier="true" data-type="numeric"><?php echo __( 'ID', 'openpos' ); ?></th>
                <th data-column-id="barcode" data-sortable="false" data-identifier="true" data-type="numeric"><?php echo __( 'Barcode', 'openpos' ); ?></th>
                <th data-column-id="product_thumb" data-sortable="false"><?php echo __( 'Thumbnail', 'openpos' ); ?></th>
                <th data-column-id="post_title" data-sortable="false"><?php echo __( 'Product Name', 'openpos' ); ?></th>
                <th data-column-id="formatted_price" data-sortable="false"><?php echo __( 'Price', 'openpos' ); ?></th>
                <th data-column-id="qty_html" data-sortable="false"><?php echo __( 'Qty', 'openpos' ); ?></th>
                <th data-column-id="action"  data-sortable="false" style="text-align: center"><?php echo __( 'Action', 'openpos' ); ?></th>
            </tr>
            </thead>
        </table>
    </form>
    <br class="clear">
</div>
<form enctype="multipart/form-data"  style="display: none;">
    <input type="file" id="product_image" name="product_image">
    <input type="hidden" id="product_image_id" name="product_image_id">
</form>

<script type="text/javascript">
    (function($) {
        "use strict";
        var grid = $("#grid-selection").bootgrid({
            ajax: true,
            post: function ()
            {
                /* To accumulate custom parameter with the request object */
                return {
                    warehouse_id: '<?php echo $warehouse_id; ?>',
                    action: "op_stock_products"
                };
            },
            url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
            selection: false,
            multiSelect: false,
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
                header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-12 actionBar\"><p class=\"{{css.search}}\"></p><p class=\"{{css.actions}}\"></p></div></div></div>"
            }
        }).on("loaded.rs.jquery.bootgrid", function()
        {

            grid.find(".update-row").on("click", function(e)
            {
                var id = 'product-row-'+$(this).data("id");
                var current_obj = $(this);
                var form_data = grid.find('#'+id).serialize();
                $.ajax({
                    url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                    type: 'post',
                    dataType: 'json',
                    data: form_data+'&action=op_stock_products_update',
                    beforeSend:function(){
                        current_obj.addClass('loading');

                    },
                    success:function(data){
                        current_obj.removeClass('loading');
                       
                    }
                });

            });

            grid.find('.click-edit-price-a').on('click',function(){
                var parent_div = $(this).closest('.vna-row-price');
                var id = $(this).data("id");
                if(parent_div.hasClass('active'))
                {
                    var input_price = parent_div.find('input').first().val();
                    if(input_price.length > 0)
                    {
                        $.ajax({
                            url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                            type: 'post',
                            dataType: 'json',
                            data: 'action=op_stock_products_update&field=price&id='+id+'&field_value='+input_price,
                            beforeSend:function(){

                                parent_div.find('.row-price-input').prop('disabled',true);
                            },
                            success:function(data){
                                parent_div.find('.row-price-input').prop('disabled',false);
                               
                            }
                        });
                    }else {
                        alert('Please enter value');
                    }

                }else {
                    parent_div.addClass('active');
                }
                console.log($(this));
            });

            grid.find('.upload-a').on('click',function(){
                var parent_div = $(this).closest('.vna-cell-image');
                var id = $(this).data("id");
                var input_file = parent_div.find('input').first();
                var img_form = parent_div.find('form').first();
                $('input[name="product_image_id"]').val(id);
                $('#product_image').trigger('click');
            });
            grid.find('.product-allow-warehouse').on('click',function(){
                     var checked = $(this).prop('checked');
                     var input_qty = $(this).closest('p').find('.product-qty-warehouse').first();


                     if(checked)
                     {

                         input_qty.prop('readonly',false);
                     }else {
                         input_qty.prop('readonly',true);
                     }
            });
        });

        $('input#product_image').on('change',function(){

            var files = new FormData();

            files.append('field_value', $('#product_image')[0].files[0]);
            files.append('id', $('input[name="product_image_id"]').val());
            files.append('field', 'image');
            files.append('action', 'op_upload_product_image');

            $.ajax({
                type: 'post',
                url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                processData: false,
                contentType: false,
                data: files,
                success: function (response) {
                    $("#grid-selection").bootgrid('reload');
                },
                error: function (err) {
                    console.log(err);
                }
            });
        })

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
    .row-price-input,
    .glyphicon-saved,
    .vna-row-price.active .glyphicon-pencil,
    .vna-row-price.active .row-price-span{
        display: none;
    }
    .vna-row-price.active .glyphicon-saved,
    .vna-row-price.active input.row-price-input{
        display: block;
    }
    .vna-cell-image{
        position: relative;
    }
    .upload-a{
        position: absolute;
        right: 0;
        top:0;
        outline: none;
        text-outline: none;
    }
</style>