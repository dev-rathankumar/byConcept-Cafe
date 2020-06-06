<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-log-12 col-sm-12 col-xs-12">
            <div id="curve_chart"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {

        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable(<?php echo json_encode($chart_data); ?>);

            var options = {
                title: '',
                curveType: 'function',
                legend: { position: 'bottom' }
            };

            var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

            chart.draw(data, options);
        }


    }(jQuery));
</script>