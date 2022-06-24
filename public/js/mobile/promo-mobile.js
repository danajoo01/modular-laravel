function checkbox_action(elm, keyword) {
    segment     = urlSegment();
    url         = keyword +'='+ elm;
    full_uri    = url;

    return full_uri;
}


/* get all checked value based on choosen element */
function getCheckedVal(elm) {
    var valUrl;
    var segment         = urlGetSegment();
    var segment_count   = segment.length - 1;
    var checked         = $('#ul-'+ elm +' input:checkbox:checked').length;

    if(segment_count < 3) {
        separator = '?';
    } else {
        separator = '&';
    }

    if(checked > 0) {
        var checkedUrl = $('#ul-'+ elm +' input:checkbox:checked').map(function() {              
            if(elm == 'color' || elm == 'brand'){
                var checkedVal = '-'+ this.value +'-'
            } else {
                var checkedVal = this.value +'-'
            }
            
            return checkedVal;
        }).get();

        if(elm == 'color' || elm == 'brand'){
            valUrl = elm +'='+ checkedUrl.join('').slice(1, -1);
        } else {
            valUrl = elm +'='+ checkedUrl.join('').slice(0, -1);
        }

    } else {
        valUrl = "";
    }

    return valUrl;
}

$("#sizes").click(function() {
    $(this).data('clicked', true);
});

$("input[name='categories']").click(function() {
    $(this).data('clicked', true);
});

$("#submit-filter").on("click", function(e){
    e.preventDefault();
    // var segment = urlGetSegment();
    var segment           = [];
    var gender            = $('.filter-gender .display').data('value');
    var color             = $('.filter-color .display').data('value');
    var brand             = $('.filter-brand .display').data('value');
    var category          = $('.filter-kategori .display').data('value');
    var checked_color     = $('.filter-color  input:checkbox:checked').length;
    var checked_brand     = $('.filter-brand  input:checkbox:checked').length;
    var checked_category  = $('input[name=categories]:checked').val();
    var size              = $("#sizes");
    var url               = "";
    var brand_elm         = $('.filter-brand .display').length;
    var sort              = $('#sort').val();

    segment.push(window.location.pathname);    
    if(typeof gender != 'undefined') {        
        if(gender != 'all' && gender != null){
          url = 'gender=' + gender;
          segment.push(url);
        }        
    }

    if(typeof color != 'undefined') {
        var color_join = color.join('').slice(1, -1);
        url = checkbox_action(color_join, 'color');

        if(checked_color != 0) {
            segment.push(url);    
        }
    }

    if(brand_elm) {
        if(typeof brand != 'undefined') {
            var brand_join = brand.join('').slice(0, -2);
            url = checkbox_action(brand_join, 'brand');
            
            if(checked_brand != 0) {
                segment.push(url);
            }
        }
    }

    if(typeof size.data('clicked') != 'undefined') {
        url = 'size='+ size.val();

        if(size.val() != null) {
            segment.push(url);    
        }
    }

    if(typeof category != 'undefined') {
        url =  'cat=' + category;
        segment.push(url);
    }
    
    //current sort mobile
    if(typeof sort != 'undefined'){
        url =  sort;        
        segment.push(url);
    }
    
    full_uri = window.location.origin + create_uri(segment);
    
    window.location.replace(full_uri);
});

function genderAction() {
  var segment = [];
  var gender  = $('input[name=gender]:checked').val();

  segment.push(window.location.pathname);

  if(typeof gender != 'undefined') {
    var gender_split  = gender.split("|");
    var gender_url    = gender_split[1];
    var url           = "";
    
    if(gender_url != 'all'){
      url = 'gender=' + gender_url;
    }
    
    console.log('gender_split = ' + JSON.stringify(gender_split));
    console.log('gender_url = ' + JSON.stringify(gender_url));
    console.log('url = ' + JSON.stringify(url));
    
    segment.push(url);
  }

  full_uri = create_uri(segment);

  actionFullAjax(full_uri);
}

function brandAction(elm) {
    var segment     = [];
    var url         = getCheckedVal('brand');

    segment.push(window.location.pathname);
    
    if(url != "") segment.push(url);
    
    full_uri = create_uri(segment);

    actionAjax(full_uri);
}

$('#sort').change(function() {
    var getSegment   = urlGetSegment();
    var last_segment = getSegment.length - 1;
    var value        = $(this).val();

    for(var i = 0; i < getSegment.length; i++) {
        if(getSegment[i].indexOf('recommended') != -1 || getSegment[i].indexOf('pn') != -1 || getSegment[i].indexOf('price') != -1 || getSegment[i].indexOf('discount') != -1 || getSegment[i].indexOf('popular') != -1) {
            getSegment.splice(last_segment, 1);
        }
    }
    getSegment.push(value);

    url = window.location.origin + create_uri(getSegment);

    window.location.replace(url);
});


function paginate(elm) {
    var get_segment = urlGetSegment();
    var currentPage;

    if(typeof $(elm).data('page') != 'undefined') {
        var page_num    = ($(elm).data('page') - 1) * 48;
        var url_pagenum = (page_num == 0) ? '' : 'page='+ page_num;
    }else{
        var currentPage = 1;
    }

    /* Search segment that exist "page" key */
    var segment_key = find_key('page', get_segment);

    /* if keyword "page" exist, then remove "page" element */
    if(typeof segment_key != 'undefined') {
        get_segment.splice(segment_key, 1);
    }
    

    get_segment.push(url_pagenum);
    
    url = window.location.origin +  create_uri(get_segment);

    window.location.replace(url);
}

function actionFullAjax(url) {
  $.ajax({
    dataType: "json",
    url: url,
    type: "get",
    cache: false,
    beforeSend: function () {
      $('#ul-category input[type=radio]').prop('disabled', true);
      $('#ul-color input[type=checkbox]').prop('disabled', true);
      $('#sizes').prop('disabled', true);
      $('#loading').show();
    },
    success: function (data) {
      $('#loading').hide();
      
      $('#ul-category').html('');
      $.each(data.category, function (index, value) {
        html = '<li>';
        html += '<input type="radio" name="categories" value="' + value.type_name_bahasa + '|' + value.type_url + '" id="RadioGroup-' + value.type_url + '">';
        html += '<label for="RadioGroup-' + value.type_url + '">' + value.type_name_bahasa + '</label>';
        html += '</li>';

        $('#ul-category').append(html);
      });
      
      $('#ul-category input[type=radio]').prop('disabled', false);

      /* Call all necessary function */
      $('#ul-color').html('');

      $.each(data.color, function (index, value) {
        var brand_url = "";

        html = '<li>';
        html += '<input type="checkbox" name="colors" value="' + value.color_name + '" id="RadioGroup-' + value.color_name + '" data-url="' + value.color_name + '">';
        html += '<label class="color-merah" for="RadioGroup-' + value.color_name + '">' + value.color_name + '<span style="background-color:#' + value.color_hex + '"></span></label>';
        html += '</li>';

        $('#ul-color').append(html);
      });

      option = '<option disabled="disabled" selected="selected" name="size">Ukuran</option>';
      $.each(data.size, function (index, value) {
        option += '<option value="' + value.product_size_url + '">' + value.product_size_url + '</option>';
      });

      $('#sizes').empty().append(option);

      $('#ul-color input[type=checkbox]').prop('disabled', false);
      $('#sizes').prop('disabled', false);
    },
    error: function (data) {
      // console.log(data);
      var url = window.location.origin + '/error_500';
      window.location.replace(url);
    }
  });
}

function actionAjax(url) {

    $.ajax({
        dataType: "json",
        url: url,
        type: "get",
        cache: false,
        beforeSend: function() {
            $('#ul-color input[type=checkbox]').prop('disabled', true);
            $('#sizes').prop('disabled',true);
            $('#loading').show();
        },
        success: function(data){
            $('#loading').hide();
          
            /* Call all necessary function */
            $('#ul-color').html('');

            $.each(data.color, function(index, value) {
                var brand_url = "";

                html = '<li>';
                html += '<input type="checkbox" name="colors" value="'+ value.color_name +'" id="RadioGroup-'+ value.color_name +'" data-url="'+ value.color_name +'">';
                html += '<label class="color-merah" for="RadioGroup-'+ value.color_name +'">'+ value.color_name +'<span style="background-color:#'+ value.color_hex +'"></span></label>';
                html += '</li>';

                $('#ul-color').append(html);
            });            

            option = '<option disabled="disabled" selected="selected" name="size">Ukuran</option>';
            $.each(data.size, function(index, value) {
                option += '<option value="'+ value.product_size_url +'">'+ value.product_size_url +'</option>';
            });

            $('#sizes').empty().append(option);

            $('#ul-color input[type=checkbox]').prop('disabled', false);
            $('#sizes').prop('disabled', false);
        },
        error: function(data){
            // console.log(data);
            var url = window.location.origin +'/error_500';
            window.location.replace(url);
        }
    });
}