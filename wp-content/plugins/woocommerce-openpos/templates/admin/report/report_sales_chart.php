<div class="container-fluid">
    <div class="row" id="summary-list">
        <div class="col-md-3 col-log-3 col-sm-3 col-xs-3">
            <div class="summary-block">
                <dl>
                    <dt><?php echo __('Total Orders','openpos'); ?></dt>
                    <dd><?php echo $summaries['total_order'];?></dd>
                </dl>
            </div>
        </div>
        <div class="col-md-3 col-log-3 col-sm-3 col-xs-3">
            <div class="summary-block">
                <dl>
                    <dt><?php echo __('Total Sales','openpos'); ?></dt>
                    <dd><?php echo wc_price($summaries['total_sale']);?></dd>
                </dl>
            </div>
        </div>
        <div class="col-md-3 col-log-3 col-sm-3 col-xs-3">
            <div class="summary-block">
                <dl>
                    <dt><?php echo __('Total Commision','openpos'); ?></dt>
                    <dd><?php echo wc_price($summaries['total_commision']);?></dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-log-12 col-sm-12 col-xs-12 report-chart">
            <!-- <div id="curve_chart"></div> -->
            <canvas id="myChart" height="250" width="800"></canvas>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        <?php
             
             $label = array();
             $sale_data = array();
             $commision_data = array();
             
             foreach($chart_data as $index =>  $c)
             {
                 if($index == 0)
                 {
                     continue;
                 }
                $label[] = $c[0];
                $sale_data[] = $c[1];
                $commision_data[] = $c[2];
                
             }
        ?>
        var ctx = document.getElementById("myChart").getContext("2d");
        var   sale_data = <?php echo json_encode($sale_data) ?>;
        var   commission_data = <?php echo json_encode($commision_data) ?>;
        var labels =  <?php echo json_encode($label) ?>;
        
		var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                 {
                    label: 'Data',
                    data: sale_data,
                },
                {
                    label: '<?php echo __('Commision','openpos'); ?>',
                    data: commission_data,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                }
            ]
            },
        });

    }(jQuery));
    window.onload = function(){
        
    }
</script>