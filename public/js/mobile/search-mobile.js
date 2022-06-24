

$.extend({
    redirectPost: function(location, args)
    {
        var form = '';

        $.each( args, function( key, value ) {
            form += '<input type="hidden" name="'+key+'" value="'+value+'">';
        });
        var token = $('meta[name=_token]').attr('content');
        form += '<input type="hidden" name="_token" value="'+token+'">';
        $('<form action="'+location+'" method="POST">'+form+'</form>').appendTo("body").submit();
    }
});

function getSearchData(elm) {
    var datas       = "";
    var url_pagenum = "";
    var word        = $('.keyword-search').val();
    var datas       = $(elm).val();

    if(typeof $(elm).data('page') != 'undefined') {
        var page_num    = ($(elm).data('page') - 1) * 48;
        var url_pagenum = (page_num == 0) ? '' : '/'+ page_num;
    }else{
        var currentPage = 1;
    }

    var uri = '/search'+url_pagenum+'?s='+word+ '&' + datas ;

    $.redirectPost(uri,{"keyword":word,"searchData":datas});
    return false;
}