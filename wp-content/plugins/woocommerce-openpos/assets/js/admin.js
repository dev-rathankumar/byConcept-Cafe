function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
}
(function($) {
    function realTime()
    {
        let _time = 60*60*1000;
        $.ajax({
            url: openpos_admin.ajax_url,
            type: 'post',
            dataType: 'json',
            data:{action:'admin_openpos_data'},
            success:function(data){
                $(document).trigger('board_ajax_data',[data]);
                setTimeout(
                    function()
                    {
                        realTime();
                    }, _time);
            },
            error: function(){
                setTimeout(
                    function()
                    {
                        realTime();
                    }, _time);
            }
        })
    }

    

    $(document).ready(function(){
        if($('body').hasClass('toplevel_page_openpos-dasboard'))
        {
            //realTime();
        }


    });


}(jQuery));