(function ($) {

    $(".document-sign-page").tooltip({ position: {
        my: "center bottom-10",
        at: "center-20 top",
        using: function (position, feedback) {
            $(this).css(position);
            $("<div>")
            .addClass("esign-arrow")
            .addClass(feedback.vertical)
            .addClass(feedback.horizontal)
            .appendTo(this);
        } 
    }
});

$(".doc_page").tooltip({ position: {
    my: "center bottom-10",
    at: "center-20 top",
    using: function (position, feedback) {
        $(this).css(position);
        $("<div>")
            .addClass("esign-arrow")
            .addClass(feedback.vertical)
            .addClass(feedback.horizontal)
            .appendTo(this);
    }
}
});


   
    
})(jQuery);
