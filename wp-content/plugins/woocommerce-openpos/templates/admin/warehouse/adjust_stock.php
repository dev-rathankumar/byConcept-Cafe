<?php
/**
 * Created by PhpStorm.
 * User: anhvnit
 * Date: 10/15/18
 * Time: 12:17
 */
?>
<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php
$warehouse_id = isset($_GET['warehouse_id']) ? $_GET['warehouse_id'] : 0;
?>
<div class="wrap">
    <div id="wrap-loading">
        <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
    </div>
    <h1><?php echo __( 'Adjust Stock', 'openpos' ); ?></h1>
    <br class="clear" />
    <div class="container-fluid">

        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">
                <form style="display: none" enctype="multipart/form-data" id="upload-csv-form">
                    <input type="hidden" name="action" value="op_upload_inventory_csv">
                    <input type="file" id="csv-file-import" name="csv" value="">
                    <input type="submit">
                </form>
                <form class="form-horizontal" id="stock-frm">
                    <input type="hidden" name="action" value="openpos_adjust_stock_finder" />
                    <h4 class="text-center"><?php echo __('Search / Import Product','openpos');?></h4>
                    <div class="form-group">
                        <label for="input_name" class="col-sm-3 control-label required "><?php echo __('Barcode','openpos');?></label>
                        <div class="col-sm-6">
                            <input type="text" name="barcode" value=""  class="form-control" id="input_name" placeholder="Enter product barcode">
                        </div>

                        <button type="submit" class="btn btn-default col-sm-2"><?php echo __('Search','openpos');?></button>
                    </div>
                    <div class="form-group">
                        <label for="input_name" class="col-sm-3 control-label "></label>
                        <div class="col-sm-6">
                            <p><a href="javascript:void(0);" id="link-choose-csv"><?php echo __('Click here to get to import with csv file','openpos'); ?></a></p>
                            <p><a target="_blank" href="<?php echo OPENPOS_URL.'/assets/sample/adjust_stock.csv'; ?>"><?php echo __('Download sample csv file','openpos'); ?></a></p>
                        </div>
                    </div>


                </form>
                <div id="inventory-search-result">
                    <form id="adjust-stock-frm">
                        <input type="hidden" name="action" value="op_upload_inventory_csv">
                        <input type="hidden" name="warehouse_id" value="<?php echo $warehouse_id; ?>">
                    <table class="table table-bordered">
                        <tr id="table-inventory-title">
                            <th>Product</th>
                            <th>Barcode</th>
                            <th>Qty</th>
                            <th>#</th>
                        </tr>

                        <tr id="table-inventory-no-product">
                            <td colspan="4"> <p class="text-center"><?php echo __('No product selected','openpos'); ?></p></td>
                        </tr>
                        <tr id="table-inventory-total">
                            <td colspan="2"><p><?php echo __('Total','openpos'); ?></p></td>
                            <td  colspan="2">
                                <b id="total-row-qty">0</b>
                            </td>
                        </tr>
                        <tr id="table-inventory-update-btn">
                            <td colspan="2"></td>
                            <td  colspan="2">
                                <button type="submit" class="btn btn-danger"><?php echo __('Update','openpos'); ?></button>
                            </td>
                        </tr>
                    </table>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">
    (function($) {
        "use strict";
        $(document).ready(function(){
            var files;
            $('#link-choose-csv').click(function(){
                $('#csv-file-import').trigger('click');
            });
            $('input#csv-file-import').change(function(event) {
                files = event.target.files;
                // select the form and submit

                $('#upload-csv-form').submit();

            });
            $('#upload-csv-form').submit(function(event,data){

                var formData = new FormData();

                formData.append("action", "op_upload_inventory_csv");

                formData.append("file", files[0]);

                files = new Array();
                $('#upload-csv-form').trigger("reset");
                $.ajax({
                    url: openpos_admin.ajax_url,
                    type: 'post',
                    dataType: 'json',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend:function(){
                        $('body').addClass('op_loading');
                    },
                    success:function(data){
                        if(data.status == 1)
                        {
                            if(data['data'].length > 0)
                            {
                                var rows = data['data'];
                                rows.forEach(function(row){
                                    if($('#inventory-search-result').find('#item-'+row.id).length > 0)
                                    {
                                        var current_qty = $('#inventory-search-result').find('input[name="product['+row.id+']"]').first().val();
                                        var new_qty = parseFloat(current_qty) + row.qty;
                                        $('#inventory-search-result').find('input[name="product['+row.id+']"]').first().val(new_qty);
                                    }else {
                                        var html = '';
                                        html += '<tr class="item-row item-row-new" id="item-'+row.id+'">';
                                        html += '<td>'+ row.name +'</td>';
                                        html += '<td>'+ row.barcode +'</td>';
                                        if(row.qty != null)
                                        {
                                            html += '<td><input type="text" class="form-control item-row-qty" name="product['+row.id+']" value="'+row.qty+'" placeholder="qty"></td>';
                                        }else {
                                            html += '<td><input type="text" class="form-control item-row-qty" name="product['+row.id+']" value="" placeholder="no manage"></td>';
                                        }

                                        html += '<td><button type="button" class="btn btn-warning delete-row-adjust"><?php echo __('Delete','openpos'); ?></button></td>';
                                        html += '</tr>';
                                        $('#table-inventory-no-product').before(html);
                                    }

                                });
                            }
                        }
                        $('body').removeClass('op_loading');
                        $(document).trigger('check-inventory-data');
                    },
                    error:function(){
                        $('body').removeClass('op_loading');
                    }
                });
                return false;
            });

            $(document).on('click','.delete-row-adjust',function(){
                $(this).closest('tr').remove();
                $(document).trigger('check-inventory-data');
            });

            $(document).on('change','input.item-row-qty',function(){
                $(document).trigger('check-inventory-data');
            });

            $(document).on('check-inventory-data',function(){
                var total_item = $('#inventory-search-result').find('.item-row').length;
                if(total_item == 0)
                {
                    $('#table-inventory-no-product').show();
                    $('#table-inventory-total').hide();
                    $('#table-inventory-update-btn').hide();
                    $('#total-row-qty').text(0);
                }else {
                    $('#table-inventory-no-product').hide();
                    $('#table-inventory-total').show();
                    $('#table-inventory-update-btn').show();
                    var total_qty = 0;
                    var rows = $('#inventory-search-result').find('.item-row-qty').each(function(){
                        var current_qty = $(this).val();
                        if(current_qty)
                        {
                            total_qty += parseFloat(current_qty);
                        }

                    });
                    $('#total-row-qty').text( 1 * total_qty );
                }
            });
            $(document).trigger('check-inventory-data');


            $('#stock-frm').on('submit',function(){
                var data = $(this).serialize();
                $.ajax({
                    url: openpos_admin.ajax_url,
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    beforeSend:function(){
                        $('body').addClass('op_loading');
                    },
                    success:function(data){
                        if(data.status == 1)
                        {
                            if(data['data'].length > 0)
                            {
                                var rows = data['data'];
                                rows.forEach(function(row){
                                    if($('#inventory-search-result').find('#item-'+row.id).length > 0)
                                    {
                                        var current_qty = $('#inventory-search-result').find('input[name="product['+row.id+']"]').first().val();
                                        var new_qty = parseFloat(current_qty) + row.qty;
                                        $('#inventory-search-result').find('input[name="product['+row.id+']"]').first().val(new_qty);
                                    }else {
                                        var html = '';
                                        html += '<tr class="item-row item-row-new" id="item-'+row.id+'">';
                                        html += '<td>'+ row.name +'</td>';
                                        html += '<td>'+ row.barcode +'</td>';
                                        html += '<td><input type="text" class="form-control item-row-qty" name="product['+row.id+']" value="'+row.qty+'" placeholder="qty"></td>';
                                        html += '<td><button type="button" class="btn btn-warning delete-row-adjust"><?php echo __('Delete','openpos'); ?></button></td>';
                                        html += '</tr>';
                                        $('#table-inventory-no-product').before(html);
                                    }

                                });
                            }
                        }
                        $('body').removeClass('op_loading');
                        $(document).trigger('check-inventory-data');
                    },
                    error:function(){
                        $('body').removeClass('op_loading');
                    }
                });
                return false;
            });
            function adjust_stock(){
                if($('#adjust-stock-frm').find('.item-row-new').length > 0)
                {
                    var row = $('#adjust-stock-frm').find('.item-row-new').first();
                    if(!$('body').hasClass('op_loading'))
                    {
                        var row_name = row.find('input').first().attr('name');
                        var row_qty = row.find('input').first().val();
                        var data = {
                            action:'op_adjust_stock',
                            warehouse_id: <?php echo $warehouse_id; ?>
                        };
                        data[row_name] = row_qty;
                        $.ajax({
                            url: openpos_admin.ajax_url,
                            type: 'post',
                            dataType: 'json',
                            data: data,
                            beforeSend:function(){
                                $('body').addClass('op_loading');
                            },
                            success:function(response){
                                row.removeClass('item-row-new');
                                row.find('input').first().prop('disabled',true);
                                if(response.status == 1)
                                {

                                    row.addClass('item-row-synced');
                                }else{
                                    row.addClass('item-row-error');
                                }
                                $('body').removeClass('op_loading');
                                adjust_stock();
                            },
                            error:function(){
                                $('body').removeClass('op_loading');
                            }
                        });
                    }
                }

            }
            $('#adjust-stock-frm').on('submit',function(e){
                e.preventDefault();
                adjust_stock();
            });
        });
    })( jQuery );
</script>
<style type="text/css">
    .item-row-error{
        color: red;
    }
    .item-row-synced{
        color: green;
    }
</style>