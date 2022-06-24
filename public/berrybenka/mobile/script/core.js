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
$(window).load(function () {
    $('.loading-icon').hide();
});
jQuery(document).ready(function ($) {
    $('.ssm-toggle-nav').click(function () {
        $('nav').toggleClass('visible');
        $('.ssm-overlay').addClass('reveal');
    });
    $('.ssm-overlay').click(function () {
        $('nav').removeClass('visible');
        $('.ssm-overlay').removeClass('reveal');
    });
});


$('.flexslider').flexslider({
    animation: "fade",
    start: function (slider) {
        $('body').removeClass('loading');
    }
});

//$('.search-head').click(function(){
$(document).on("click touchstart", ".search-head", function () {
   $('.search').show('fast');
   $('body').addClass('hide-scroll');
});

$('.search-cancel').click(function (e) {
//$(document).on("click touchstart", ".search-cancel", function () {
    e.preventDefault();
    $('.search').hide('fast');
    $('body').removeClass('hide-scroll');
    $(".search-sugestion").remove();
    $('#keyword').val("");
});

//$('.filter-link').click(function(){
$(document).on("click touchstart", ".filter-link", function () {
    $('#filter-list').show("fast");
    $('.content-catalog').hide();
});

$('.filter-list-outer h1 .fa-times').click(function(){
//$(document).on("click touchstart", ".filter-list-outer h1 .fa-times", function () {
    $('#filter-list').hide("fast");
    $('.content-catalog').show();
    // $('header').removeClass('nav-up').addClass('nav-down');
});

//$('.filter-gender  input:radio').change(function(){
$(document).on("change", ".filter-gender  input:radio", function () {
    var tempValue = '';
    var type_url = '';
    tempValue = $('.filter-gender  input:radio').map(function (n) {
        if (this.checked) {
            split_val = this.value.split('|');
            type_url = split_val[1];

            return  split_val[0];
        }
        ;
    }).get().join(', ');
    $('.filter-gender .display').html(tempValue).css('display','block').data('value', type_url);
});

$(document).on('change', '.filter-kategori input:radio', function(){
    var tempValue = '';
    var type_url = '';
    tempValue = $('.filter-kategori  input:radio').map(function (n) {
        if (this.checked) {
            split_val = this.value.split('|');
            type_url = split_val[1];

            return  split_val[0];
        }
        ;
    }).get().join(', ');
    $('.filter-kategori .display').html(tempValue).css('display','block').data('value', type_url);
})

//$('.filter-brand  input:checkbox').change(function(){
$(document).on('change', '.filter-brand  input:checkbox', function(){
    var tempValue = '';
    var arr_data = [];
    tempValue = $('.filter-brand  input:checkbox').map(function (n) {
        if (this.checked) {
            arr_data.push($(this).data('url') + '--');

            return this.value;
        }
        ;
    }).get().join(', ');

    $('.filter-brand .display').html(tempValue).css('display', 'block').data('value', arr_data);
})

$(document).on('change', '.filter-color input:checkbox', function(){
    var tempValue = '';
    var arr_data = [];
    tempValue = $('.filter-color  input:checkbox').map(function (n) {
        if (this.checked) {
            arr_data.push('-' + $(this).data('url') + '-');

            return  this.value;
        }
        ;
    }).get().join(', ');
    $('.filter-color .display').html(tempValue).css('display','block').data('value', arr_data);
});

//$('.new-cust').click(function () {
$(document).on('click touchstart', '.new-cust', function(){
//  $('.mid-login-left').css('left','-100%');
//  $('.mid-login-right').css('left','0');
    $('.mid-login-left').addClass('slide-left');
    $('.mid-login-right').addClass('slide-right');
});

//$('.b2log').click(function(){
$(document).on('click touchstart', '.b2log', function(){
//  $('.mid-login-left').css('left','-100%');
//  $('.mid-login-right').css('left','0');
    $('.mid-login-left').removeClass('slide-left');
    $('.mid-login-right').removeClass('slide-right');
});
// $('.edit-address').click(function(){
//  $('.list-alamat').show('fast');
//  $('.content-detail').hide('fast');
//  $('.list-alamat ul').show('fast');
//  $('.editing-address').hide('fast');
// });

//$('.list-alamat .fa-times').click(function(){
$(document).on('click', '.list-alamat .fa-times', function () {
    $('.list-alamat').hide('fast');
    $('.content-detail').show('fast');
    // $('header').removeClass('nav-up').addClass('nav-down');
});

//$('.show-edit').click(function(){
$(document).on('click touchstart', '.show-edit', function () {
    $('.editing-address').show('fast');
    $('.list-alamat-wrapper ul').hide('fast');
});
//$('.del-cart').click(function(){
//  $(this).parentsUntil('.cart-list ul').hide();
//});
//$('.del-cart').click(function(){
//  $('.del-confirm').show('fast');
//});

//$('.cart-title').click(function(){
$(document).on('click touchstart', '.cart-title', function () {
    $('.cart-title i').toggleClass('rotate');
    $('.cart-list ul').toggle('fast');
});

//$('.b-gratis').click(function(){
$(document).on('click touchstart', '.b-gratis', function () {
    $('.b-gratis i').toggleClass('rotate');
    $('.hidden').toggle('fast');
    // $('header').removeClass('nav-up');
});

$('.benka-form').focus(function(){
    //$('.info-benka-point').show('fast');
});