//header
$(window).scroll(function() {    
    var scroll = $(window).scrollTop();

    if (scroll >= 50) {
        $("header").addClass("active-header");
    } else {
        $("header").removeClass("active-header");
    }
});

//sticky.js
$('.prod-desc-wrapper').hcSticky({
  top: 120,
  bottom: 40
});
$('.filter-outer').hcSticky({
  top: 120,
  bottom: 40
});

$('.filter-trigger').click(function(){
  $('.filter').toggleClass('show-filter');
  $('.catalog-list').toggleClass('catalog-list-mini');
});
$('.sizing h1').click(function(){
  $('.sizing ul').toggleClass('show-content')
});
$('.care h1').click(function(){
  $('.care ul').toggleClass('show-content')
});
$('.menu-mobile').click(function(){
  $('nav.mobile-menu-nav').toggleClass('show-nav');
})

//search
$('.search-trigger').click(function(){
  $('.search-wrapper').toggleClass('show-search');
  $('header').addClass('active-header')
  // $('html').addClass('noscroll')
  // $('body').addClass('noscroll');
});

$('.close-search').click(function(){
  $('.search-wrapper').removeClass('show-search')
  // $('html').removeClass('noscroll')
  // $('body').removeClass('noscroll');
})

$('.search-textfield').keyup(function() {
    if($(this).val() == ''){
        $('.search-result').hide('fast');
    }else{
        $('.search-result').show('fast');
    }
});

//cart
$('.add2cart').click(function(){
  $('.cart-list-wrapper').addClass("show-cart");
  $('html').addClass('noscroll');
  $('body').addClass('noscroll')
});

$('.cart-list-wrapper').click(function(){
  $(this).removeClass("show-cart");
  $('html').removeClass('noscroll');
  $('body').removeClass('noscroll')
});

// $('.cart-list').click(function (e) {
//     e.stopPropagation();
// });

//login
$('.login-trigger').click(function(){
  $('.login-wrapper').addClass('show-login-wrapper');
  $('.login-outer').addClass('show-login');
  $('html').addClass('noscroll')
  $('body').addClass('noscroll');
});

$('.login-wrapper').click(function(){
  $('.login-wrapper').removeClass('show-login-wrapper');
  $('.login-outer').removeClass('show-login');
  $('html').removeClass('noscroll');
  $('body').removeClass('noscroll');
});

$('.login-outer').click(function (e) {
    e.stopPropagation();
});

$('.cart-list-small').scrollable();

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
//    $('.fancybox-overlay').hide();
//    });

//$('.error-msg-login .fa-times').click(function () {
$(document).on("click touchstart", ".error-msg-login .fa-times", function () {
    $(this).parent().hide().addClass('disabled');
});

$(document).on("click touchstart", ".success-msg .fa-times", function () {
    $(this).parent().hide().addClass('disabled');
});

$(document).on("click touchstart", ".success-login .fa-times", function () {
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
 
 $(document).ready(function($) {
  $('.stamp-faq a').click(function(){

    //Expand or collapse this panel
    $(this).next().slideToggle('fast');

    //Hide the other panels
    $(".stamp-faq div").not($(this).next()).slideUp('fast');

  });
});