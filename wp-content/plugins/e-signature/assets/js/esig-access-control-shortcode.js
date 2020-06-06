

function esig_print(data)
{
    
    var mywindow = window.open(data, 'esigprint', 'height=500,width=500');
    
    mywindow.onload = function() { mywindow.print(); mywindow.close(); }

    return true;
}

