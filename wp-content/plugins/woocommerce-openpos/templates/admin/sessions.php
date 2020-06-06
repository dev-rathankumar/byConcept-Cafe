<div class="wrap">
    <h1><?php echo __( 'Active Login Sessions', 'openpos' ); ?></h1>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-log-12 col-sm-12 col-xs-12 ">
                <button type="button" class="btn btn-danger pull-right" id="clear-all-session-btn"><?php echo __( 'Clear All', 'openpos' ); ?></button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-log-12 col-sm-12 col-xs-12">
                <table class="table table-bordered" id="my-table">
                    <thead>
                    <tr>
                        <th><?php echo __( 'ID', 'openpos' ); ?></th>
                        <th><?php echo __( 'User', 'openpos' ); ?></th>
                        <th><?php echo __( 'Login Date', 'openpos' ); ?></th>
                        <th><?php echo __( 'IP ', 'openpos' ); ?></th>
                        <th><?php echo __( 'Register ', 'openpos' ); ?></th>
                        <th><?php echo __( 'Location ', 'openpos' ); ?></th>
                        <th><?php echo __( 'Unlink', 'openpos' ); ?></th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div style="display: block;">
    <!-- Modal -->
    <div class="modal fade" id="mapModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo __('Your Location','openpos'); ?></h4>
                </div>
                <div class="modal-body">
                    <div style="width: 100%">
                        <div class="mapouter"><div class="gmap_canvas"><iframe width="100%" height="500" id="gmap_canvas" src="" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe></div><style>.mapouter{text-align:right;height:500px;width:100%;}.gmap_canvas {overflow:hidden;background:none!important;height:500px;width:100%;}</style></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        "use strict";
        var datatable = new DataTable(document.querySelector('#my-table'), {
            filters: [false,'select', false,true,false,false,false],
            data: <?php echo json_encode($session_data); ?>,
            pageSize:  <?php echo count($session_data); ?>,

        });
        var dialog = $('#mapModal').modal('hide');

        $("#export").click(function(){
            $("#my-table").tableToCSV();
        });
        $(document).on('click','#clear-all-session-btn',function(){
            var ids = new Array();
            $(document).find('.unlink-session').each(function(s){
                ids.push($(this).data('id'))
            });

            if(confirm('Are you sure ?'))
            {
                $.ajax({
                    url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                    type: 'post',
                    dataType: 'json',
                    data: {action: 'admin_openpos_session_unlink',id:ids},
                    success:function(data){
                        location.reload();
                    }
                });
            }
        });
        $(document).on('click','.unlink-session',function(){
            var id = $(this).data('id');
            if(confirm('Are you sure ?'))
            {
                $.ajax({
                    url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                    type: 'post',
                    dataType: 'json',
                    data: {action: 'admin_openpos_session_unlink',id:[id]},
                    success:function(data){
                        location.reload();
                    }
                });
            }
        });
        $(document).ready(function(){
            $(document).on('click','.session-location',function(){
                $('iframe#gmap_canvas').attr('src',$(this).data('url'));
                $('#mapModal').modal('show');
            });
        });


    })( jQuery );

</script>