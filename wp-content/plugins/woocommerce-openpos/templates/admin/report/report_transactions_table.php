<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-log-12 col-sm-12 col-xs-12">
            <table class="table table-bordered" id="my-table">
                <thead>
                <tr>
                    <th><?php echo __( 'ID', 'openpos' ); ?></th>
                    <th><?php echo __( 'Ref', 'openpos' ); ?></th>
                    <th><?php echo __( 'IN', 'openpos' ); ?></th>
                    <th><?php echo __( 'OUT', 'openpos' ); ?></th>
                    <th><?php echo __( 'Method', 'openpos' ); ?></th>
                    <th><?php echo __( 'Create At', 'openpos' ); ?></th>
                    <th><?php echo __( 'By', 'openpos' ); ?></th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
            <button id="export" data-export="export">Export</button>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        "use strict";
        var datatable = new DataTable(document.querySelector('#my-table'), {
            filters: [true,false, false,false,false,'select'],
            data: <?php echo json_encode($orders_table_data); ?>,
            pageSize:  <?php echo count($orders_table_data); ?>,

        });
        $("#export").click(function(){
            $("#my-table").tableToCSV();
        });

    })( jQuery );

</script>