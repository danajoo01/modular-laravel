$( "#tabs" ).tabs({
  hide: { effect: "slide", duration: 200 }
});

$(window).scroll(function () {
    if ($(this).scrollTop() > 0)
    {
        $('.top-header').fadeOut(0);
        $('.bottom-header').addClass('nav-header');
    } else
    {
        $('.top-header').fadeIn(0);
        $('.bottom-header').removeClass('nav-header');
    }
});

//$('html').click(function () {
//    $('.nav-checkout').hide();
//    $('.user-wrappers').hide();
//    $('.q-login').hide()
//})

$(document).click(function (event) {
    if (!$(event.target).closest('.top-right-header').length) {
        $('.q-login').hide();
        $('.nav-checkout').hide();
        $('.user-wrappers').hide();
    }
});

//$('.top-right-header,.right-item').click(function (e) {
$(document).on("click touchstart", ".top-right-header,.right-item", function (e) {
    e.stopPropagation();
});

//$('.user-dd a').click(function(){
$(document).on("click touchstart", ".user-dd a", function () {
    $('.user-wrappers').toggle('fast');
});

//$('.q-log-triger').click(function(){
$(document).on("click touchstart", ".q-log-triger", function () {
    $('.q-login').toggle('fast');
});

//$(".chekout-arrow").click(function () {
$(document).on("click touchstart", ".chekout-arrow", function () {
    $(".nav-checkout").toggle().toggleClass('show-content');
});

// $(".checkout-dd").click(function(){
$(document).on("click touchstart", ".checkout-dd", function () {
    $(".nav-checkout").toggle().toggleClass('show-content');
});

 //$(".lengkap").click(function(){
$(document).on("click touchstart", ".lengkap", function () {
    $(this).toggleClass('lengkap tutup');
    $('.prod-desc').toggleClass('detail-expand');
});

//$(function() {
//    $( "#slider-range" ).slider({
//      range: true,
//      min: 0,
//      max: 6000,
//      values: [ 0, 6000 ],
//      slide: function( event, ui ) {
//        $( "#low-amount" ).val( "Rp" + " " + ui.values[ 0 ] + "K");
//		$("#high-amount").val( "Rp"  + " " + ui.values[ 1 ] + "K");
//      }
//    });
//    $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
//      " - $" + $( "#slider-range" ).slider( "values", 1 ) );
//  });
  
$('.brand-scroll').scrollable();

$('.cart-list-small').scrollable();
$('#sidebar').hcSticky();
//$('#sticky-cart').hcSticky();
//  $(".addtowish a i").click(function(){
//     $(this).removeClass("fa-heart-o");
// 	$(this).addClass("fa-heart");
// });
// $(".add2wish").click(function(){
// 	$(this).css('background','#333');
//     $(".add2wish i").addClass("heart-white");
// });


//$(".detail-col").click(function(){
$(document).on("click touchstart", ".detail-col", function () {
    $(this).toggleClass("collapsing");
});

$(document).ready(function() {
    setTimeout(function(){ $('#loading').hide(); }, 500);
    $('.fancybox').fancybox();
    $(".fancybox-effects-c").fancybox({
        wrapCSS: 'fancybox-custom',
        closeClick: true,

        openEffect: 'none',

        helpers: {
            title: {
                type: 'inside'
            },
            overlay: {
                css: {
                    'background': 'rgba(238,238,238,0.85)'
                }
            }
        }
    });
});
$(function () {
    $(".catalog-img img").lazyload({effect: "fadeIn"});
});
if ($(".menu-list ul li").hasClass("has-child")) {
    $(".menu-list ul li a i").addClass("fa fa-angle-down");
};

//$(".filter-category input").on('click', function() {
//  var $box = $(this);
//  if ($box.is(":checked")) {
//    var group = "input[name='" + $box.attr("name") + "']";
//    $(group).prop("checked", false);
//    $box.prop("checked", true);
//  } else {
//    $box.prop("checked", false);
//  }
//  $('input[data-level=parent]').change(function() {
//$('input[data-level=child]').prop('checked', false); 
//});
//});


//$('.detail-spec-detail .filter-color input:checkbox').click(function() {
$(document).on("click touchstart", ".detail-spec-detail .filter-color input:checkbox", function () {
    $('.detail-spec-detail .filter-color input:checkbox').not(this).prop('checked', false);
});

//$('.detail-spec-detail .filter-size input:checkbox').click(function() {
$(document).on("click touchstart", ".detail-spec-detail .filter-size input:checkbox", function () {
    $('.detail-spec-detail .filter-size input:checkbox').not(this).prop('checked', false);
}); 
$(document).ready(function(){
    //$('#add-qty').click(function () {
    $(document).on("click touchstart", "#add-qty", function () {
        var counter = $('#qty-value').val();
        counter++;
        $('#qty-value').val(counter);
    });
    //$('#min-qty').click(function () {
    $(document).on("click touchstart", "#min-qty", function () {
        var counter = $('#qty-value').val();
        if (counter > 1) {
            counter--;
        }
        $('#qty-value').val(counter);
    });
});

//$('.bank-promo i').click(function () {
$(document).on("click touchstart", ".bank-promo i", function () {
    $('.bank-promo').hide().addClass('disabled');
});
	
//$('.close-list-alamat').click(function () {
$(document).on("click touchstart", ".close-list-alamat", function () {
    $('.edit-alamat').hide().addClass('disabled');
    $('.fancybox-overlay').hide();
    $('html').removeClass();
});
	
//$('.list-alamat h1 i').click(function(){
//      $('.list-alamat').hide().addClass('disabled');
//	  $('.fancybox-overlay').hide();
//    });

//$('.error-msg-login .fa-times').click(function () {
$(document).on("click touchstart", ".error-msg-login .fa-times", function () {
    $(this).parent().hide().addClass('disabled');
});

//$('.content').click(function () {
$(document).on("click touchstart", ".content", function () {
    $('.show-content').hide().removeClass('show-content');
});

//$('.edit-current-address').click(function () {
$(document).on("click touchstart", ".edit-current-address", function () {
    $('.ubah-list-alamat').hide('.2s');
    $('.close-list-alamat').hide()
    $('.back-list-alamat').show()
    $('.edit-alamat-detail').show('.2s');
});

//$('.back-list-alamat').click(function () {
$(document).on("change", "#tambah-alamat-checkbox", function () {
    if ($(this).prop("checked")) {
        $('.tambah-alamat-penagihan').hide('.2s');
    } else {
        $('.tambah-alamat-penagihan').show('.2s');
    }
});

$(document).on("click touchstart", ".back-list-alamat", function () {
    $('.close-list-alamat').show();
    $('.back-list-alamat').hide();
    $('.ubah-list-alamat').show('.2s');
    $('.edit-alamat-detail ').hide('.2s');
});

//$('#tambah-alamat-checkbox').change(function(){
$(document).on("change", ".back-list-alamat", function () {
    if ($(this).prop("checked")) {
        $('.tambah-alamat-penagihan').hide('.2s');
    } else {
        $('.tambah-alamat-penagihan').show('.2s');
    }
});

 //$('.tambah-alamat-baru').click(function () {
 $(document).on("click touchstart", ".tambah-alamat-baru", function () {
    $('#tambah-alamat-baru').show('.2s');
    $('.ubah-list-alamat ul').hide();
});

//$('.cancel-add-address').click(function () {
$(document).on("click touchstart", ".cancel-add-address", function () {
    $('#tambah-alamat-baru').hide('.2s');
    $('.ubah-list-alamat ul').show();
});

//$('.alamat-detail').click(function(){
$(document).on("click touchstart", ".alamat-detail", function () {
    $('.ubah-list-alamat ul').show();
    $('#tambah-alamat-baru').hide();
});

//$('.close-edit-alamat').click(function () {
$(document).on("click touchstart", ".close-edit-alamat", function () {
    $('.ubah-alamat-edit').hide();
    $('.fancybox-overlay').hide();
    $('.status-pesan-list').hide();
    $('html').removeClass('fancybox-margin fancybox-lock');
});

//$('.erase-address-setting').click(function(){
$(document).on("click touchstart", ".erase-address-setting", function () {
    $(this).parent().hide('fast');
});



//update 8-9-16
  $(document).ready(function($) {
    $('.panel h3').click(function(){

      //Expand or collapse this panel
      $(this).next().slideToggle('fast');

      //Hide the other panels
      $(".collapse").not($(this).next()).slideUp('fast');

    });
  });
  $(document).ready(function($) {
    $('.list-q a').click(function(){

      //Expand or collapse this panel
      $(this).next().slideToggle('fast');

      //Hide the other panels
      $(".list-q div").not($(this).next()).slideUp('fast');

    });
  });
  
$(document).ready(function() {
    $(".tab-menu a").click(function(event) {
        event.preventDefault();
        $(this).parent().addClass("current");
        $(this).parent().siblings().removeClass("current");
        var tab = $(this).attr("href");
        $(".tab-content").not(tab).css("display", "none");
        $(tab).fadeIn();
    });
}); 
 
var clipboard = new Clipboard('.rescode-btn');
clipboard.on('success', function(e) {
	console.log(e);
});
clipboard.on('error', function(e) {
	console.log(e);
});
