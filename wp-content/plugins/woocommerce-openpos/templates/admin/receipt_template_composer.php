<div class="wrap">
    <div id="wrap-loading">
        <div class="lds-ellipsis"><div></div><div></div><div></div><div></div></div>
    </div>
    <h1><?php echo __( 'Composer Receipt', 'openpos' ); ?></h1>
    <br class="clear" />
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-lg-6 col-xl-6 col-sm-6 col-xs-12" style="    background: #ccc;padding-top: 20px;padding-bottom: 15px;">
                <form class="form-horizontal" id="template-frm">
                    <input type="hidden" name="temp_id" value="<?php echo $default['id']; ?>">
                    <input type="hidden" name="id" value="<?php echo $default['id']; ?>">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo __( 'Width', 'openpos' ); ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="paper_width" value="<?php echo $default['paper_width']; ?>">
                            <span id="helpBlock2" class="help-block">Inch</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo __( 'Padding (Inch)', 'openpos' ); ?></label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="padding_top" value="<?php echo $default['padding_top']; ?>">
                            <span id="helpBlock2" class="help-block"><?php echo __( 'Top', 'openpos' ); ?></span>
                        </div>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="padding_right" value="<?php echo $default['padding_right']; ?>">
                            <span id="helpBlock2" class="help-block"><?php echo __( 'Right', 'openpos' ); ?></span>
                        </div>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="padding_bottom" value="<?php echo $default['padding_bottom']; ?>">
                            <span id="helpBlock2" class="help-block"><?php echo __( 'Bottom', 'openpos' ); ?></span>
                        </div>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="padding_left" value="<?php echo $default['padding_left']; ?>">
                            <span id="helpBlock2" class="help-block"><?php echo __( 'Left', 'openpos' ); ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo __( 'Template', 'openpos' ); ?></label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="content" rows="3" id="receipt-template-content"><?php echo $default['content']; ?></textarea>
                            <span id="helpBlock2" class="help-block"><a href="javascript:void(0)" id="load-sample"><?php echo __( 'Load Sample', 'openpos' ); ?></a></span>

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label"><?php echo __( 'CSS', 'openpos' ); ?></label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="custom_css" rows="3" id="receipt-template-css"><?php echo $default['custom_css']; ?></textarea>
                            <span id="helpBlock2" class="help-block"><a href="javascript:void(0)" id="load-sample-css"><?php echo __( 'Load Sample', 'openpos' ); ?></a></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-8 col-sm-4 text-right">
                            <button type="submit"  class="btn btn-primary"><?php echo __( 'Update', 'openpos' ); ?></button>
                            <!-- <button type="button" id="preview-receipt" class="btn btn-warning"><?php echo __( 'Preview', 'openpos' ); ?></button> -->
                        </div>
                    </div>
                </form>

            </div>
            <div class="col-md-6 col-lg-6 col-xl-6 col-sm-6 col-xs-12">
                <div class="preview-live">
                    <iframe id="preview-frame" src="<?php echo admin_url('admin-ajax.php?action=openpos_update_receipt_preview&id='.$default['id']); ?>" style="width:calc(100% - 1px);height:100%;min-height:490px;    background: #fff;
                    border: none;" src="">Preview</iframe>
                </div>
            </div>
        </div>
    </div>
</div>
<style>

.preview-live{
        float: left;
        height: fit-content;
        display: block;
        width: calc(100% - 2px);
        
        min-height: 500px;
        border:solid 1px #00BCD4;
        padding: 5px 0;
        background: #00BCD4;
    }

</style>
<script type="text/javascript">
    (function($) {
        "use strict";
        
        var frame_url = '<?php echo admin_url('admin-ajax.php?action=openpos_update_receipt_preview&id='.$default['id']); ?>' ;
        $(document).ready(function(){
            var receipt_content = CodeMirror.fromTextArea(document.getElementById("receipt-template-content"), {
                    mode: "text/html",
                    styleActiveLine: true,
                    lineNumbers: true,
                    lineWrapping: true,
                    autoRefresh: true
                });
            var receipt_css = CodeMirror.fromTextArea(document.getElementById("receipt-template-css"), {
                mode: "text/css",
                styleActiveLine: true,
                lineNumbers: true,
                lineWrapping: true,
                autoRefresh: true
            });


            $('#template-frm').on('submit',function(){
                var data = $(this).serialize();
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php?action=openpos_update_receipt_content'); ?>',
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    beforeSend:function(){
                        $('body').addClass('op_loading');
                    },
                    success:function(data){
                        if(data.status == 1)
                        {
                            
                            $('body').removeClass('op_loading');

                            var tmp_t = new Date().getTime();
                            $('#preview-frame').attr('src',frame_url+'&t='+tmp_t);
                        }else {
                            alert(data.message);
                            $('body').removeClass('op_loading');
                        }
                    },
                    error:function(){
                        $('body').removeClass('op_loading');
                    }
                });
              
               return false;
            });

            $(document).on('click','#load-sample',function(){
                $.ajax({
                    url: '<?php echo OPENPOS_URL.'/default/receipt_template_sample.txt'; ?>',
                    type: 'get',
                    dataType: 'text',
                    beforeSend:function(){
                        $('body').addClass('op_loading');
                    },
                    success:function(data){
                        
                        receipt_content.getDoc().setValue(data);
                       

                        
                       $('body').removeClass('op_loading');
                       
                        
                    },
                    error:function(){
                        $('body').removeClass('op_loading');
                    }
                });
            });

            $(document).on('click','#load-sample-css',function(){
                $.ajax({
                    url: '<?php echo OPENPOS_URL.'/default/receipt_template_css_sample.txt'; ?>',
                    type: 'get',
                    dataType: 'text',
                    beforeSend:function(){
                        $('body').addClass('op_loading');
                    },
                    success:function(data){
                        
                        receipt_css.getDoc().setValue(data);
                       $('body').removeClass('op_loading');
                       
                        
                    },
                    error:function(){
                        $('body').removeClass('op_loading');
                    }
                });
            });

            $(document).on('click','#preview-receipt',function(){
                var tmp_t = new Date().getTime();
                $('input[name="temp_id"]').val(tmp_t);
                var data = $('#template-frm').serialize();
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php?action=openpos_update_receipt_draft'); ?>',
                    type: 'post',
                    dataType: 'json',
                    data: data,
                    beforeSend:function(){
                        $('body').addClass('op_loading');
                    },
                    success:function(data){
                        if(data.status == 1)
                        {
                            
                            $('body').removeClass('op_loading');
                            var tmp_t = new Date().getTime();
                            $('#preview-frame').attr('src',frame_url+'&t='+tmp_t);

                        }else {
                            alert(data.message);
                            $('body').removeClass('op_loading');
                        }
                    },
                    error:function(){
                        $('body').removeClass('op_loading');
                    }
                });

                var form_values = $('#template-frm').serialize();
                
                console.log('click');
            });

            var form_height = $('#template-frm').height();
            if(form_height > 500)
            {
                $('.preview-live').css('height',form_height +'px');
            }

        });



    })( jQuery );
</script>