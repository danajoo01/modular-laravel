$(document).ready(function () {
  var ajax_url = $('#ajax_url').val();
  var url = $('#img_path').val();

  //Function

  //End Function

  //User Action
  $(document).off('change', '.sel-qty');
  $(document).on('change', '.sel-qty', function (event) {
    event.preventDefault();
    var sku = $(this).attr("SKU");
    $('#is-delete-'+sku).val('0');
    $('#form-cart-'+sku).submit();
  });

  $(document).off('click', '.del-cart');
  $(document).on('click', '.del-cart', function (event) {
    event.preventDefault();
    var sku = $(this).attr("SKU");
    $('.del-confirm').hide();
    $('#del-'+sku).show();
  });

  $(document).off('click', '.del-btn-yes');
  $(document).on('click', '.del-btn-yes', function (event) {
    event.preventDefault();
    var sku = $(this).attr("SKU");
    $('#is-delete-'+sku).val('1');
    $('#form-cart-'+sku).submit();
  });

  $(document).off('click', '.del-btn-no');
  $(document).on('click', '.del-btn-no', function (event) {
    event.preventDefault();
    var sku = $(this).attr("SKU");
    $('#del-'+sku).hide();
  });
  //End User Action

  //Initial State

  //End Initial State
});
