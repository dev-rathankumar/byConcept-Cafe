<?php
global $op_warehouse;
global $op_woo;
$warehouses = $op_warehouse->warehouses();
$openpos_type = $this->settings_api->get_option('openpos_type','openpos_pos');



$default = array(
    'id' => 0,
    'name' => '',
    'address' => '',
    'city' => '',
    'postal_code' => '',
    'country' => '',
    'status' => 'publish',
    'email' => '',
    'phone' => '',
    'facebook' =>''
);
$is_new = true;
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($id)
{
    $default = $op_warehouse->get($id);
    $is_new = false;
}
$countries = $op_woo->get_countries_and_states();
?>
<style type="text/css">
    .warehouse-name ul{
        list-style: none;
        display: block;
        margin:0;
        padding:0;
    }
    .warehouse-name ul li{
        float:left;
        padding:3px;
        display: inline-block;
    }
    .register-frm{
        background-color: #ccccccb3;
    }
    .status-draft{
        color: red;
    }
    .status-publish{
        color: green;
    }
</style>
<div class="wrap warehouse-list">
    <div id="wrap-loading">
        <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
    </div>
    <h1><?php echo __( 'Outlets', 'openpos' ); ?></h1>
    <br class="clear" />
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-lg-6 col-md-4">
                <h4><?php echo ($is_new) ?  __( 'New Outlet', 'openpos' ) : __( 'Edit Outlet', 'openpos' ); ?></h4>
            </div>
            <div class="col-xs-6 col-sm-6 col-lg-6 col-md-8" style="margin-bottom: 5px;">
                <?php if(!$is_new): ?>
                <a type="button" href="<?php echo admin_url('admin.php?page=op-warehouses'); ?>" class="btn btn-primary pull-right"><?php echo __('Add New Outlet','openpos');?></a>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">

            <div class="col-xs-12 col-sm-12 col-md-4 warehouse-frm">
                <form class="form-horizontal" id="warehouse-frm">
                    <input type="hidden" name="action" value="openpos_update_warehouse" />
                    <input type="hidden" name="id" value="<?php echo $default['id'];?>" />
                    <h4 class="text-center"><?php echo __('General Information','openpos');?></h4>
                    <div class="form-group">
                        <label for="input_name" class="col-sm-4 control-label required "><?php echo __('Outlet Name','openpos');?></label>
                        <div class="col-sm-8">
                            <input type="text" name="name" value="<?php echo $default['name'];?>"  class="form-control" id="input_name" placeholder="Name">

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_address" class="col-sm-4 control-label"><?php echo __('Address line 1','openpos');?></label>
                        <div class="col-sm-8">
                            <input type="text" name="address" value="<?php echo $default['address'];?>"  class="form-control" id="input_address" placeholder="Address">
                            <p class="help-block"><?php echo __( 'The street address for your business location.', 'openpos' ); ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_address" class="col-sm-4 control-label"><?php echo __('Address line 2','openpos');?></label>
                        <div class="col-sm-8">
                            <input type="text" name="address_2" value="<?php echo isset($default['address_2']) ? $default['address_2'] : '';?>"  class="form-control" id="input_address" placeholder="Address 2">
                            <p class="help-block"><?php echo __( 'An additional, optional address line for your business location.', 'openpos' ); ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_city" class="col-sm-4 control-label"><?php echo __('City','openpos');?></label>
                        <div class="col-sm-8">
                            <input type="text" name="city" value="<?php echo $default['city'];?>"  class="form-control" id="input_city" placeholder="City">
                            <p class="help-block"><?php echo __( 'The city in which your business is located.', 'openpos' ); ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_country" class="col-sm-4 control-label"><?php echo __('Country / State','openpos');?></label>
                        <div class="col-sm-8">
                            <select class="form-control" name="country">
                                <option value=""><?php echo __('Default store','openpos');?></option>
                                <?php foreach($countries as $value => $label): ?>
                                    <option value="<?php echo $value; ?>" <?php echo $value == $default['country'] ? 'selected':'';?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="help-block"><?php echo __( 'The country and state or province, if any, in which your business is located.', 'openpos' ); ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_zip" class="col-sm-4 control-label"><?php echo __('Postcode / ZIP','openpos');?></label>
                        <div class="col-sm-8">
                            <input type="text" name="postal_code" value="<?php echo $default['postal_code'];?>"  class="form-control" id="input_zip" placeholder="Postcode code">
                            <p class="help-block"><?php echo __( 'The postal code, if any, in which your business is located.', 'openpos' ); ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_status" class="col-sm-4 control-label"><?php echo __('Status','openpos');?></label>
                        <div class="col-sm-4">
                            <select name="status" class="form-control">
                                <option <?php echo $default['status'] == 'publish' ? 'selected':''; ?> value="publish"><?php echo __('Active','openpos');?></option>
                                <option <?php echo $default['status'] == 'draft' ? 'selected':''; ?> value="draft"><?php echo __('Inactive','openpos');?></option>
                            </select>
                        </div>
                    </div>
                    <h4 class="text-center"><?php echo __('Contact Information','openpos');?></h4>
                    <div class="form-group">
                        <label for="inputEmail3"  class="col-sm-4 control-label"><?php echo __('Email','openpos');?></label>
                        <div class="col-sm-8">
                            <input type="email" value="<?php echo $default['email'];?>"  class="form-control" id="inputEmail3" name="email" placeholder="Email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_phone" class="col-sm-4 control-label"><?php echo __('Phone','openpos');?></label>
                        <div class="col-sm-8">
                            <input type="text" value="<?php echo $default['phone'];?>"  class="form-control" id="input_phone" name="phone" placeholder="Phone">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="input_fb" class="col-sm-4 control-label"><?php echo __('Facebook','openpos');?></label>
                        <div class="col-sm-8">
                            <input type="text" value="<?php echo $default['facebook'];?>"  class="form-control" id="input_fb" name="facebook" placeholder="Facebook">
                        </div>
                    </div>
                    <?php do_action('op_warehouse_form_end',$default); ?>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-default pull-right"><?php echo __('Save','openpos');?></button>
                        </div>
                    </div>
                </form>
                <?php do_action('op_warehouse_form_after',$default); ?>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-8 warehouse-list">

                <div class="table-responsive">
                    <table class="table register-list">
                        <tr>
                            <th><?php echo __( 'Name', 'openpos' ); ?></th>
                            <th><?php echo __( 'Address', 'openpos' ); ?></th>
                            <th><?php echo __( 'Status', 'openpos' ); ?></th>
<!--                            <th>--><?php //echo __( 'Total Qty', 'openpos' ); ?><!--</th>-->

                        </tr>
                        <?php foreach($warehouses as $warehouse): ?>
                            <tr>
                                <td class="warehouse-name">
                                    <p><span style="color: #fff;background: #009688;padding: 2px 6px;margin-right: 3px;"><?php echo $warehouse['id']; ?></span><?php echo $warehouse['name']; ?></p>
                                    <ul>
                                        <li><a href="<?php echo admin_url('admin.php?page=op-warehouses&op-action=inventory&id='.esc_attr($warehouse['id'])); ?>"><?php echo __('Inventory','openpos'); ?></a></li>
                                        <li>|</li>
                                        <?php if($warehouse['id'] > 0):  ?>

                                            <li><a href="<?php echo admin_url('admin.php?page=op-warehouses&id='.esc_attr($warehouse['id'])); ?>"><?php echo __( 'Edit', 'openpos' ); ?></a></li>
                                            <li>|</li>
                                            <li><a href="javascript:void(0);" class="delete-warehouse-btn" data-id="<?php echo esc_attr($warehouse['id']); ?>"><?php echo __( 'Delete', 'openpos' ); ?></a></li>
                                            <li>|</li>
                                            <li><a href="<?php echo admin_url('admin.php?page=op-transactions&warehouse='.esc_attr($warehouse['id'])); ?>"><?php echo __('Transactions','openpos'); ?></a></li>
                                            <li>|</li>
                                            <li><a href="<?php echo admin_url('edit.php?post_type=shop_order&warehouse='.esc_attr($warehouse['id'])); ?>"><?php echo __('Orders','openpos'); ?></a></li>
                                        <?php else: ?>

                                                <li><a href="<?php echo admin_url('admin.php?page=op-transactions&warehouse='.esc_attr($warehouse['id'])); ?>"><?php echo __('Transactions','openpos'); ?></a></li>
                                                <li>|</li>
                                                <li><a href="<?php echo admin_url('edit.php?post_type=shop_order&warehouse='.esc_attr($warehouse['id'])); ?>"><?php echo __('Orders','openpos'); ?></a></li>
                                        <?php endif; ?>
                                        <?php if($openpos_type =='restaurant'): ?>
                                            <li>|</li>
                                            <li><a target="_blank" href="<?php echo OPENPOS_URL.'/kitchen/index.php?id='.esc_attr($warehouse['id']);  ?>"><?php echo __('Kitchen Screen','openpos'); ?></a></li>
                                        <?php endif; ?>

                                    </ul>
                                </td>
                                <td class="address">
                                    <address>
                                        <?php echo $address = WC()->countries->get_formatted_address( $op_warehouse->getStorePickupAddress( $warehouse['id'] ) ); ?>
                                    </address>
                                    <address>
                                        <?php  echo $warehouse['phone'] ? '<abbr title="Phone">P:</abbr>'.$warehouse['phone'].'<br>':'' ?>
                                        <?php  echo $warehouse['email'] ? '<a href="mailto:#">'.$warehouse['email'].'</a><br>':'' ?>
                                        <?php  echo $warehouse['facebook'] ? '<abbr title="Facebook">Fb:</abbr>'.$warehouse['facebook'].'<br>':'' ?>
                                    </address>
                                </td>

                                <td>
                                    <span class="status-<?php echo esc_attr($warehouse['status']); ?>"><?php echo $warehouse['status'] == 'publish' ? 'Active' : 'Inactive'; ?></span>
                                </td>
<!--                                <td>-->
<!--                                    --><?php //echo $warehouse['total_qty']; ?>
<!--                                </td>-->


                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="6"></td>
                        </tr>

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


            $(document).on('click','.delete-warehouse-btn',function(){
                var id = $(this).data('id');

                if(confirm('Are you sure ? '))
                {
                    $.ajax({
                        url: openpos_admin.ajax_url,
                        type: 'post',
                        dataType: 'json',
                        //data:$('form#op-product-list').serialize(),
                        data: {action: 'openpos_delete_warehouse',id:id},
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
<script type="text/javascript">
    (function($) {
        "use strict";
        $(document).ready(function(){
            $('#warehouse-frm').on('submit',function(){
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
                            window.location.href = '<?php echo admin_url('admin.php?page=op-warehouses&id='); ?>'+data.data['id'];

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


        });



    })( jQuery );
</script>