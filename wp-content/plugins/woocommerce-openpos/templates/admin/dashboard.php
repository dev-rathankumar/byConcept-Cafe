<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php
/**
 * Created by PhpStorm.
 * User: anhvnit
 * Date: 12/4/16
 * Time: 23:40
 */

?>
<script type="text/javascript">
    (function($) {
        $('body').on('click','#reset-balance',function () {
            if(confirm('<?php echo __('This function to reset cash balance on your cash drawer to 0. Are you sure ?','openpos'); ?>'))
            {
                $.ajax({
                    url: openpos_admin.ajax_url,
                    type: 'post',
                    dataType: 'json',
                    data:{action:'admin_openpos_reset_balance'},
                    success:function(data){
                        $('#openpos-cash-balance').text(0);
                    }
                })
            }
        })

    }(jQuery));
	window.onload = function(){
        <?php
             $label = array();
             $sale_data = array();
             $transaction_data = array();
             $commision_data = array();
             foreach($chart_data as $index =>  $c)
             {
                 if($index == 0)
                 {
                     continue;
                 }
                $label[] = $c[0];
                $sale_data[] = round($c[1],wc_get_price_decimals());;
                $transaction_data[] = $c[2];
                $commision_data[] = round($c[3],wc_get_price_decimals());
             }
        ?>
        var ctx = document.getElementById("myChart").getContext("2d");
        var   sale_data = <?php echo json_encode($sale_data) ?>;
        var   commission_data = <?php echo json_encode($commision_data) ?>;

        var   transaction_data = <?php echo json_encode($transaction_data) ?>;;
        var labels =  <?php echo json_encode($label) ?>;
        
		var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                 {
                    label: '<?php echo __('Sales','openpos'); ?>',
                    data: sale_data,
                },
                {
                    label: '<?php echo __('Commision','openpos'); ?>',
                    data: commission_data,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                }
                /*
                {
                    label: '<?php echo __('Cash Transactions','openpos'); ?>',
                    data: transaction_data,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                }
                */
            ]
            },
            options: {
                    title: {
                        display: true,
                        text: '<?php echo __('All Sales','openpos'); ?>'
                    }
                }
        });
        <?php if(!empty($pie_data)): $pie_type = 'register'; ?>
            var data = {
                datasets: [{
                    data: [
                        <?php foreach($pie_data as $p): ?>
                        <?php echo round($p['sale'],2);  $pie_type = $p['type']; ?>, 
                        <?php endforeach; ?>
                    ],
                    backgroundColor: [
                    <?php foreach($pie_data as $p): ?>
                    getRandomColor(),
                    <?php endforeach; ?>
                    ],
                }],

                // These labels appear in the legend and in the tooltips when hovering different arcs
                labels: [
                    <?php foreach($pie_data as $p): ?>
                        '<?php echo $p['label']; ?>', 
                    <?php endforeach; ?>
                ]
            };

            var ctx_pie = document.getElementById("myChart-pie").getContext("2d");
            var myPieChart = new Chart(ctx_pie, {
                type: 'pie',
                data: data,
                options: {
                    title: {
                        display: true,
                        text: '<?php echo ($pie_type == 'register' ) ? __('Sale by Register','openpos') : __('Sale by Outlet','openpos'); ?>'
                    }
                }
            });
            
        <?php endif ; ?>
        <?php if(!empty($seller_sales)): ?>
        
            var ctx_seller = document.getElementById("myChart-seller").getContext("2d");
            var mySellerChart = new Chart(ctx_seller, {
                type: 'horizontalBar',
                data: {
                    labels: [
                        <?php foreach($seller_sales as $p): ?>
                        '<?php echo $p['name'];   ?>', 
                        <?php endforeach; ?>
                    ],
                    datasets: [ 
                        {
                            label: 'Sales',
                            data: [
                                <?php foreach($seller_sales as $p): ?>
                                    <?php echo round($p['total'],2);   ?>, 
                                <?php endforeach; ?>
                            ],
                            backgroundColor: [
                            <?php foreach($payment_sales as $p): ?>
                            getRandomColor(),
                            <?php endforeach; ?>
                            ]
                        },
                        
                    ]
                },
                options: {
                        title: {
                            display: true,
                            text: '<?php echo __('Sales by Seller','openpos'); ?>'
                        }
                    }
            });
        <?php endif ; ?>
        <?php if(!empty($payment_sales)): ?>
        var payment_data = {
                datasets: [{
                    data: [
                        <?php foreach($payment_sales as $p): ?>
                        <?php echo round($p['total'],2);   ?>, 
                        <?php endforeach; ?>
                    ],
                    backgroundColor: [
                    <?php foreach($payment_sales as $p): ?>
                    getRandomColor(),
                    <?php endforeach; ?>
                    ],
                }],

                // These labels appear in the legend and in the tooltips when hovering different arcs
                labels: [
                    <?php foreach($payment_sales as $p): ?>
                        '<?php echo $p['name']; ?>', 
                    <?php endforeach; ?>
                ]
            };
        var ctx_payment = document.getElementById("myChart-payment").getContext("2d");
        var myPaymentChart = new Chart(ctx_payment, {
            type: 'pie',
            data: payment_data,
            options: {
                    title: {
                        display: true,
                        text: '<?php echo __('Sales by Payment','openpos'); ?>'
                    }
                }
        });
        <?php endif; ?>
        
	}
</script>

<div class="op-dashboard-content container">
    <div class="row goto-pos-container">
        <div class="col-md-4 pull-right"><a href="<?php echo $pos_url; ?>"class="button-primary" target="_blank"><?php echo __('Goto POS','openpos'); ?></a></div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        <a class="btn <?php  echo (!isset($_GET['duration']) || esc_attr($_GET['duration']) == 'last_7_days') ? 'btn-success':'btn-default' ?>" href="<?php echo admin_url('admin.php?page=openpos-dasboard&duration=last_7_days'); ?>" role="button"><?php echo __('Last 7 Days','openpos'); ?></a>
        <a class="btn <?php  echo (isset($_GET['duration']) && esc_attr($_GET['duration']) == 'last_30_days') ? 'btn-success':'btn-default' ?>" href="<?php echo admin_url('admin.php?page=openpos-dasboard&duration=last_30_days'); ?>" role="button"><?php echo __('Last 30 days','openpos'); ?></a>
        <a class="btn <?php  echo (isset($_GET['duration']) && esc_attr($_GET['duration']) == 'this_week') ? 'btn-success':'btn-default' ?>" href="<?php echo admin_url('admin.php?page=openpos-dasboard&duration=this_week'); ?>" role="button"><?php echo __('This Week','openpos'); ?></a>
        <a class="btn <?php  echo (isset($_GET['duration']) && esc_attr($_GET['duration']) == 'this_month') ? 'btn-success':'btn-default' ?>" href="<?php echo admin_url('admin.php?page=openpos-dasboard&duration=this_month'); ?>" role="button"><?php echo __('This Month','openpos'); ?></a>
        <a class="btn <?php  echo (isset($_GET['duration']) && esc_attr($_GET['duration']) == 'today') ? 'btn-success':'btn-default' ?> " href="<?php echo admin_url('admin.php?page=openpos-dasboard&duration=today'); ?>" role="button"><?php echo __('Today','openpos'); ?></a>
        </div>
    </div>
    <div class="row">
        <?php if(!empty($pie_data)): ?>
            <div class="col-md-8 col-sm-8 col-xs-12 col-lg-8">
                <canvas id="myChart" height="250" width="800"></canvas>
            </div>
            
            <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                <canvas id="myChart-pie"></canvas>
            </div>
        <?php else : ?>
            <div class="col-md-12 col-sm-12 col-xs-12">
                <canvas id="myChart" height="250" width="800"></canvas>
            </div>
        <?php endif ; ?>
    </div>
    <div class="row">
            <div class="col-md-8 col-sm-8 col-xs-12 col-lg-8">
                <canvas id="myChart-seller"></canvas>
            </div>
            
            <div class="col-md-4 col-sm-4 col-xs-12 col-lg-4">
                <canvas id="myChart-payment"></canvas>
            </div>
       
    </div>
    <div class="real-content-container row">
        <div class="last-orders col-md-8 col-sm-8 col-xs-12 col-lg-8" >
            <div class="title"><label><?php echo __('Last Orders','openpos'); ?></label></div>
            <div id="table_div_latest_orders">
            <table class="table table-bordered" style="width: 100%;" id="lastest-order">
                <thead>
                    <tr>
                    <th><?php echo __('#','openpos'); ?></th>
                    <th><?php echo __('Customer','openpos'); ?></th>
                    <th><?php echo __('Grand Total','openpos'); ?></th>
                    <th><?php echo __('Sale By','openpos'); ?></th>
                    <th><?php echo __('Created At','openpos'); ?></th>
                    <th><?php echo __('Status','openpos'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($dashboard_data['order'] as $order): ?>
                    <tr>
                        <td><?php echo $order['view']; ?></td>
                        <td><?php echo $order['customer_name']; ?></td>
                        <td><?php echo $order['total']; ?></td>
                        <td><?php echo $order['cashier']; ?></td>
                        <td><?php echo $order['created_at']; ?></td>
                        <td class="order_status"><?php echo $order['status']; ?></td>
                    </tr>
                    <?php endforeach;   ?>
                </tbody>
            </table>
            </div>
        </div>
        <div class="total col-md-4 col-sm-4 col-xs-12 col-lg-4">
            <div class="title"><label><?php echo __('Cash Balance','openpos'); ?></label></div>
            <ul id="total-details">

                <li>
                    <div class="field-title" style="text-align: center;">
                       <span id="openpos-cash-balance"><?php echo $dashboard_data['cash_balance']; ?></span>
                        <a href="javascript:void(0);" id="reset-balance" style="outline: none;display: block;border:none;" title="Reset Balance">
                            <img src="<?php echo OPENPOS_URL; ?>/assets/images/reset.png" height="34px" />
                        </a>
                    </div>

                </li>
            </ul>

        </div>
    </div>
</div>
