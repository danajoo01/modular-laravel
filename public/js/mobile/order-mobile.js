

$.extend({
    redirectGet: function(location, args)
    {
        var form = '';

        $.each( args, function( key, value ) {
            form += '<input type="hidden" name="'+key+'" value="'+value+'">';
        });

        $('<form action="'+location+'" method="GET">'+form+'</form>').appendTo("body").submit();
    }
});

function getOrderData(elm) {
    var url_pagenum = ""

    if(typeof $(elm).data('page') != 'undefined') {
        var page_num    = $(elm).data('page');
        var url_pagenum = (page_num == 0) ? '' : page_num;
    }else{
        var currentPage = 1;
    }

    var uri = '/user/order_history';

    $.redirectGet(uri,{"page":url_pagenum});
    return false;
}