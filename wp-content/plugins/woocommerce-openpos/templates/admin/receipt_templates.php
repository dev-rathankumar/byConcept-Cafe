<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<?php
global $op_warehouse;
global $op_receipt;
global $op_woo;
global $OPENPOS_SETTING;

$templates = $op_receipt->templates();
$default = array(
    'id' => 0,
    'name' => '',
    'status' => ''
);
$is_new = true;
if(isset($_GET['id']) && $id = $_GET['id'])
{
    $current_register = $op_receipt->get($id);
    if(!empty($current_register))
    {
        $default = $current_register;
        $is_new = false;
    }
}

?>
<style type="text/css">
    .register-name ul{
        list-style: none;
        display: block;
        margin:0;
        padding:0;
    }
    .register-name ul li{
        float:left;
        padding:3px;
        display: inline-block;
    }
    .register-frm{
        background-color: #ccccccb3;
    }
    
    .template-list .status{
        color: red;
    }
    .template-list  .status-publish{
        color: green;
    }
    .template-list{
        width: 100%;
    }
    .template-list tr td{
        padding: 5px;
    }
    .template-list tr td .text-center{
        text-align: center;
    }
    .template-list tr th{
        padding: 15px 5px;
        text-transform: uppercase;
    }
    .template-list tr:nth-child(odd){
        background-color: #ccc;
    }
    .new-template:active,
    .new-template:focus,
    .new-template{
        text-decoration: none;
        color: #fff;
        background: #009688;
        padding: 5px 10px;
        float: right;
        margin-right: 3px;
        text-transform: uppercase;
        font-weight: bold;
        outline: none;
    }
</style>
<div class="wrap">
    <div id="wrap-loading">
        <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
    </div>
    <h1><?php echo __( 'Receipt templates', 'openpos' ); ?></h1>
    <br class="clear" />
    <div class="container-fluid">
        
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-4 register-frm">

            <h4><?php echo ($is_new) ?  __( 'New template', 'openpos' ) : __( 'Edit template', 'openpos' ); ?></h4>
                <form class="form-horizontal" id="register-frm">
                    <input type="hidden" name="action" value="openpos_update_receipt_template">
                    <input type="hidden" name="id" value="<?php echo $default['id']; ?>">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo __( 'Name', 'openpos' ); ?></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" value="<?php echo $default['name']; ?>" placeholder="Template Name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo __( 'Status', 'openpos' ); ?></label>
                        <div class="col-sm-4">
                            <select class="form-control" name="status">
                                    <option <?php echo ($default['status'] == 'publish') ? 'selected':''; ?> value="publish"><?php echo __('Active','openpos'); ?></option>
                                    <option <?php echo ($default['status'] == 'draft') ? 'selected':''; ?> value="draft"><?php echo __('Inactive','openpos'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-8 col-sm-4 text-right">
                            <button type="submit" class="btn btn-default"><?php echo __( 'Save', 'openpos' ); ?></button>
                        </div>
                    </div>
                </form>

            
            </div>
            <div class="col-xs-12 col-sm-12 col-md-8">
                
                <div class="table-responsive">
                    <table class="table template-list">
                        <tr>
                            <th><?php echo __( 'Receipt Name', 'openpos' ); ?></th>
                            <th><?php echo __( 'Created By', 'openpos' ); ?></th>
                            <th><?php echo __( 'Created At', 'openpos' ); ?></th>
                            <th><?php echo __( 'Status', 'openpos' ); ?></th>
                        </tr>
                        <?php foreach($templates as $template): ?>
                        <tr>
                            <td class="register-name">
                                <p><span style="color: #fff;background: #009688;padding: 2px 6px;margin-right: 3px;"><?php echo $template['id']; ?></span><?php echo $template['name']; ?></p>
                                <ul>
                                    <li><a href="<?php echo admin_url('admin.php?page=op-receipt-template&op-action=composer&id='.esc_attr($template['id'])); ?>"><?php echo __('Composer','openpos'); ?></a></li>
                                    <li>|</li>
                                    <li><a href="<?php echo admin_url('admin.php?page=op-receipt-template&id='.esc_attr($template['id'])); ?>"><?php echo __('Edit','openpos'); ?></a></li>
                                    <li>|</li>
                                    <li><a href="javascript:void(0);" class="delete-register-btn" data-id="<?php echo $template['id']; ?>"><?php echo __('Delete','openpos'); ?></a></li>
                                    <li>|</li>
                                    <!-- <li><a href="javascript:void(0);" class="duplicate-register-btn" data-id="<?php echo $template['id']; ?>"><?php echo __('Duplicate','openpos'); ?></a></li>
                                    <li>|</li> -->
                                    <li><a target="_blank" href="<?php echo admin_url('admin-ajax.php?action=print_receipt&template_id='.esc_attr($template['id'])); ?>"><?php echo __('Print Sample','openpos'); ?></a></li>
                                </ul>
                            </td>
                            <td class="cashiers">
                                <p class="text-center"><?php echo $template['created_by']; ?></p>
                            </td>
                            <td>
                                <p class="text-center"><?php echo $template['created_at']; ?></p>
                            </td>
                           
                            <td>
                                <p class="text-center"><span class="status status-<?php echo esc_attr($template['status']); ?>"><?php echo $template['status'] == 'publish' ? 'Active' : 'Inactive'; ?></span></p>
                            </td>
                        </tr>
                        <?php endforeach ?>
                        <?php if(count($templates) == 0): ?>
                            <tr>
                                <td colspan="4"><?php echo __('No template found','openpos'); ?></td>
                            </tr>
                        <?php endif; ?>
                        
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        "use strict";
        $(document).ready(function(){
            $('#register-frm').on('submit',function(){
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
                            window.location.href = '<?php echo admin_url('admin.php?page=op-receipt-template'); ?>';


                        }else {
                            alert(data.message);
                            $('body').removeClass('op_loading');
                        }
                    },
                    error:function(){
                        $('body').removeClass('op_loading');
                    }
                });
               console.log(data);
               return false;
            });

            $(document).on('click','.delete-register-btn',function(){
                var id = $(this).data('id');

                if(confirm('Are you sure ? '))
                {
                    $.ajax({
                        url: openpos_admin.ajax_url,
                        type: 'post',
                        dataType: 'json',
                        //data:$('form#op-product-list').serialize(),
                        data: {action: 'openpos_delete_receipt',id:id},
                        beforeSend:function(){
                            $('body').addClass('op_loading');
                        },
                        success:function(data){
                            if(data.status == 1)
                            {
                                location.reload();
                            }else {
                                alert(data.message);
                                $('body').removeClass('op_loading');
                            }
                        },
                        error:function(){
                            $('body').removeClass('op_loading');
                        }
                    });
                }
            });

        });



    })( jQuery );
</script>