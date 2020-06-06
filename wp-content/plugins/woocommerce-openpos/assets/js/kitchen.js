(function ($) {
    $.extend({
        playSound: function () {
            return $(
                '<audio class="sound-player" autoplay="autoplay" style="display:none;">'
                + '<source src="' + arguments[0] + '" />'
                + '<embed src="' + arguments[0] + '" hidden="true" autostart="true" loop="false"/>'
                + '</audio>'
            ).appendTo('body');
        },
        stopSound: function () {
            $(".sound-player").remove();
        }
    });
})(jQuery);

(function($) {
    var total_item = 0;

    function getDataInit(callback){
        var time_data_url = data_url + '?t='+ Date.now();
        if($('body').hasClass('processing'))
        {
            callback();
        }else {
            $.ajax({
                url : time_data_url,
                type: 'post',
                dataType: 'json',
                data: {action: 'get_data',warehouse: data_warehouse_id,type: kitchen_type},
                beforeSend:function(){
                    $('body').addClass('processing');
                },
                success: function(response){
                    $('#kitchen-table-body').empty();

                    if(response.length > total_item)
                    {
                        $('body').trigger('new-dish-come');
                    }
                    total_item = response.length;

                    if(response.length > 0)
                    {


                        for(var i =0; i< response.length; i++)
                        {
                            var template = ejs.compile(data_template, {});
                            var html = template(response[i]);

                            $('#kitchen-table-body').append(html);
                        }
                    }
                    $('body').removeClass('processing');
                    callback();
                },
                error: function(){
                    $('body').removeClass('processing');
                    callback();
                }
            });
        }

    }
    function getData(){
        getDataInit(function(){

            setTimeout(function() {
                getData();
            }, 3000);

        });
    }

    $(document).ready(function(){

        $('select[name="kitchen_type"]').on('change',function(){
            window.location.href = $(this).val();
        });

        getData();

        $(document).on('click','.is_cook_ready',function(){
            var current = $(this);
            var time_data_url = data_url + '?t='+ Date.now();
            $.ajax({
                url : time_data_url,
                type: 'post',
                dataType: 'json',
                data: {action: 'update_ready',id: $(this).data('id'), type: kitchen_type},
                beforeSend:function(){
                    $('body').addClass('processing');
                },
                success: function(response){
                    //$('#kitchen-table-body').empty();
                    current.hide();
                    $('body').removeClass('processing');

                },
                error: function(){
                    $('body').removeClass('processing');
                }
            });
        });

        $(document).on('click','#refresh-kitchen',function(){
            var time_data_url = data_url + '?t='+ Date.now();
            $.ajax({
                url : time_data_url,
                type: 'post',
                dataType: 'json',
                data: {action: 'clear_data',warehouse: data_warehouse_id,type: kitchen_type},
                beforeSend:function(){
                    $('body').addClass('processing');
                },
                success: function(response){
                    $('body').removeClass('processing');
                },
                error: function(){
                    $('body').removeClass('processing');
                }
            });
        })

    });



}(jQuery));