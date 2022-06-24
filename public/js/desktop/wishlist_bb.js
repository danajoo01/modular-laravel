function set_wishlist(el) {
    var fullPathUrl = window.location.pathname + window.location.search;
    var link_login = "/login?continue=" + encodeURIComponent(fullPathUrl);
    var product_id = $(el).attr('rel');
    var dataString = 'product_id=' + product_id;
    if ($("#wish_" + product_id).hasClass('heart-red')) {
        var type = 2;
        dataString = dataString + '&type=' + type;
    }else{
        var type = 1;
        dataString = dataString + '&type=' + type;
    }
    $.ajax({
        type: "GET",
        url: "/product/set_wishlist",
        data: dataString,
        dataType: "json",
        beforeSend: function () {
            $('#add2wish-' + product_id).hide();
            $('#wish-loading-' + product_id).show();
        },
        success: function (data) {
            link_login = "/user/wishlist";
            if (data == 'success' && type == 2) {
                $('#wishlist-info').empty().append('<span class="success-msg"><i aria-hidden="true" class="fa fa-times"></i> Anda Berhasil Menghapus Produk Yang Ada Di Dalam Wishlist Anda. <a class="text-underline" href="' + link_login + '">Klik Disini</a> Untuk Melihat Wishlist Anda.</span>');
                $("#wish_" + product_id).removeClass('heart-red');
                $(el).removeAttr('style');
            } else if (data == 'not-exist' && type == 2) {
                $('#wishlist-info').empty().append('<span class="error-msg-login"><i aria-hidden="true" class="fa fa-bell"></i><i aria-hidden="true" class="fa fa-times"></i> Error!! Produk Sudah Dihapus Sebelumnya Dari Wishlist Anda.</span>');
            } else if (data == 'success' && type == 1) {
                $('#wishlist-info').empty().append('<span class="success-msg"><i aria-hidden="true" class="fa fa-times"></i> Anda Berhasil Menambahkan Produk Ke Dalam Wishlist Anda. <a class="text-underline" href="' + link_login + '">Klik Disini</a> Untuk Melihat Wishlist Anda.</span>');
                $("#wish_" + product_id).addClass('heart-red');
                $(el).css('background', '#333');
            } else if (data == 'exist' && type == 1) {
                $('#wishlist-info').empty().append('<span class="error-msg-login"><i aria-hidden="true" class="fa fa-bell"></i><i aria-hidden="true" class="fa fa-times"></i> Error!! Produk Anda Sudah Disimpan Sebelumnya.</span>');
            } else if (data == 'not-login') {
                $('#wishlist-info').empty().append('<span class="error-msg-login"><i aria-hidden="true" class="fa fa-bell"></i><i aria-hidden="true" class="fa fa-times"></i> Silakan <a class="text-underline" href="'+ link_login +'"> Klik Disini</a> Untuk Login Terlebih Dahulu Sebelum Menggunakan Wishlist.</span>');
            } else {
                $('#wishlist-info').empty().append('<span class="error-msg-login"><i aria-hidden="true" class="fa fa-bell"></i><i aria-hidden="true" class="fa fa-times"></i> Error!! Produk Tidak Bisa Disimpan. Coba Ulangi Kembali.</span>');
            }

            $('#add2wish-' + product_id).show();
            $('#wish-loading-' + product_id).hide();
        }
    });
    $("html, body").animate({ scrollTop: 0 }, 300);
    //alert(product_id + " was clicked.");
}

function changePage(data)
{
	if(typeof $(data).data('page') != 'undefined')
	{
		var page_num = ($(data).data('page') - 1) * 8;

		var page_num = ($(data).data('page') > 1) ? page_num : 0;

		var url = window.location.pathname;
        url_pagenum = (page_num == 0) ? url : url+'?page='+ page_num;

        location.href = url_pagenum;
	}
}
$(document).ready(function(){
	$(document).off('click', '.modal-remove');
	$(document).on('click', '.modal-remove', function (event) {
	  event.preventDefault();
	  $('.remove-box').hide('fast');
	  $(this).hide('fast');
	  var pid = $(this).attr("pid");
	  $('#remove-box-' + pid).show('fast');
	});

	$(document).off('click', '.remove-cart');
	$(document).on('click', '.remove-cart', function (event) {
	  event.preventDefault();
	  var pid = $(this).attr("pid");
	  remove_wish(pid);
	});

	$(document).off('click', '.close-box');
	$(document).on('click', '.close-box', function (event) {
	  event.preventDefault();
	  var pid = $(this).attr("pid");
	  $('.remove-box').hide('fast');
	  $('.modal-remove').show('fast');
	});
});