<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-log-12 col-sm-12 col-xs-12">
            
                <table class="table table-bordered" id="report_table">
                    <thead>
                        <tr>
                            <th><?php echo __( '#', 'openpos' ); ?></th>
                            <th><?php echo __( 'Grand Total', 'openpos' ); ?></th>
                            <th><?php echo __( 'Commision Total', 'openpos' ); ?></th>
                            <th><?php echo __( 'Cashier', 'openpos' ); ?></th>
                            <th><?php echo __( 'Created At ', 'openpos' ); ?></th>
                            <th><?php echo __( 'View ', 'openpos' ); ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        "use strict";
        var dataSet = <?php echo json_encode($orders_table_data)?>;
        var datatable = new DataTable(document.querySelector('#report_table'), {
            data: dataSet,
            pageSize:  dataSet.length,
        });
     
    })( jQuery );

</script>