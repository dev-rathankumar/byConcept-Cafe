<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php
    $register_id = isset($_GET['register']) ? (int)$_GET['register'] : 0;
    $warehouse_id = isset($_GET['warehouse']) ? (int)$_GET['warehouse'] : 0;
?>
<div class="wrap">
    <div id="wrap-loading">
            <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
    </div>
    <h1><?php echo __( 'Cash Transactions', 'openpos' ); ?></h1>
    <form id="op-product-list" onsubmit="return false;">
        <table id="grid-selection" class="table table-condensed table-hover table-striped op-product-grid">
            <thead>
            <tr>
                <th data-column-id="id" data-identifier="true" data-type="numeric"><?php echo __( 'ID', 'openpos' ); ?></th>
                <th data-column-id="title" data-identifier="true" data-type="numeric"><?php echo __( 'Ref', 'openpos' ); ?></th>
                <th data-column-id="in_amount" data-sortable="false"><?php echo __( 'IN', 'openpos' ); ?></th>
                <th data-column-id="out_amount" data-sortable="false"><?php echo __( 'OUT', 'openpos' ); ?></th>
                <th data-column-id="payment_name" data-sortable="false"><?php echo __( 'Method', 'openpos' ); ?></th>
                <th data-column-id="register" data-sortable="false"><?php echo __( 'Register', 'openpos' ); ?></th>
                <th data-column-id="created_by" data-sortable="false"><?php echo __( 'By', 'openpos' ); ?></th>
                <th data-column-id="created_at" data-sortable="false" data-order="desc"><?php echo __( 'Created At', 'openpos' ); ?></th>

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
                    action: "op_transactions",
                    register: <?php echo $register_id; ?>,
                    warehouse: <?php echo $warehouse_id ; ?>
                };
            },
            url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
            selection: true,
            multiSelect: true,
            identifier: true,
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
               header: "<div id=\"{{ctx.id}}\" class=\"{{css.header}}\"><div class=\"row\"><div class=\"col-sm-12 actionBar\"><p class=\"{{css.search}}\"></p><p class=\"{{css.actions}}\"></p><button type=\"button\" class=\"btn vna-action btn-default\" data-action=\"delete\"><span class=\" icon glyphicon glyphicon-trash\"></span></button></div></div></div>"
           }
        }).on("initialized.rs.jquery.bootgrid",function(){

        }).on("selected.rs.jquery.bootgrid", function(e, rows)
        {


           // alert("xxSelect: " + rowIds.join(","));
        }).on("deselected.rs.jquery.bootgrid", function(e, rows)
        {

        });

        $('.vna-action').click(function(){
            var selected = $("#grid-selection").bootgrid("getSelectedRows");
            var action = $(this).data('action');
            if(selected.length == 0)
            {
                alert('Please choose row to continue.');
            }else{

                if(confirm('Are you sure ? '))
                {
                    $.ajax({
                        url: openpos_admin.ajax_url,
                        type: 'post',
                        dataType: 'json',
                        //data:$('form#op-product-list').serialize(),
                        data: {action: 'admin_openpos_update_transaction_grid',data:selected},
                        beforeSend:function(){
                            $('body').addClass('op_loading');
                        },
                        success:function(data){
                            $('body').removeClass('op_loading');
                            $("#grid-selection").bootgrid("reload");
                        }
                    });
                }


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