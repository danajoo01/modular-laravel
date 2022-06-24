var getUrl = function(){
  var segment         = urlSegment();
  var segment_count   = segment.length - 1;
  var gender          = $('.filter-gender .display').data('value');
  var color           = $('.filter-color .display').data('value');
  var brand           = $('.filter-brand .display').data('value');
  var category        = $('.filter-kategori .display').data('value');
  var checked_color   = $('.filter-color  input:checkbox:checked').length;
  var checked_brand   = $('.filter-brand  input:checkbox:checked').length;
  var size            = $("#sizes");
  var gender_url      = "";
  var color_url       = "";
  var brand_url       = "";
  var category_url    = "";
  var size_url        = "";
  var second_category = "";
  var sort            = $('#sort').val();   
  
  //Define Gender
  if(typeof gender == 'undefined' || gender == null) {
    if(segment[segment_count] == 'men' || segment[segment_count] == 'women') {
      gender_url = segment[segment_count]; //get last url segment / 'gender' keyword uri    
    } else if(segment[segment_count - 1] == 'men' || segment[segment_count - 1] == 'women') {
      gender_url = segment[segment_count - 1]; //get last url segment / 'gender' keyword uri    
    }
  }else{
    gender_url = gender;
  }

  //Define Color
  if(typeof color != 'undefined') {
    color_url = (checked_color > 0) ? '/color/'+ color.join('').slice(1, -1) : '';
  }

  //Define Brand
  if(typeof brand != 'undefined') {
    brand_url = (checked_brand > 0) ? '/brand/'+ brand.join('').slice(0, -2) : '';
  }

  //Define Size
  if(typeof size.data('clicked') != 'undefined') {
    size_url = (size.val() != null) ? '/size/'+ size.val() : ''; 
  }

  //Define Category
  if(typeof category != 'undefined') {
    if(segment.length < 2) {
      category_url = '/' + category;
    } else {
      if(segment[2] != gender && segment[2] != 'size' && segment[2] != 'color') {
        segment.splice(2, 1, category);
      }else{
        segment.splice(2, 0, category);
      }    
    }
  }

  //Define Second Category
  if(typeof gender == 'undefined' || gender == null){
    second_category = (typeof segment[2] != 'undefined' && segment[2] != 'men' && segment[2] != 'women') ? '/' + segment[2] : '';
  }else{
    second_category = (typeof category != 'undefined') ? '/' + category : '';
  }
  
  //current sort
  if(typeof sort != 'undefined'){
      sort = '?' + sort;
  }else{
      sort = '';
  }

  url = window.location.origin + '/'+ segment[1] + second_category + category_url + color_url + brand_url + size_url + '/' + gender_url + sort;
  
  return url;
};

$("#sizes").click(function() {
    $(this).data('clicked', true);
});

$("input[name='categories']").click(function() {
    $(this).data('clicked', true);
});

$("#submit-filter").on("click", function(e){
    e.preventDefault();
    window.location.replace(getUrl());
});

$('#sort').change(function() {
    var getSegment  = urlGetSegment();
    var value       = $(this).val();
    var sort_name   = value.split('='); //split param name and sort param on #sort value

    if(value.indexOf(sort_name[0]) == 0) {
        getSegment.splice(1, 1); 
    }

    getSegment.push(value);        

    url = window.location.origin + create_uri(getSegment);

    window.location.replace(url);
});

function brandAction(elm) {
    var segment         = urlSegment();
    var segment_count   = segment.length - 1;
    var brandUrl        = getCheckedVal('brand');
    var gender          = $('.filter-gender .display').data('value');
    var category        = $('.filter-kategori .display').data('value');
    var url             = "";
    var second_category = "";
    
    /* Define gender segment */
    if(segment[segment_count] == 'men' || segment[segment_count] == 'women') {
        gender = segment[segment_count]; //get last url segment / 'gender' keyword uri    
    } else if(segment[segment_count - 1] == 'men' || segment[segment_count - 1] == 'women') {
        gender = segment[segment_count - 1]; //get last url segment / 'gender' keyword uri    
    }

    //Define Second Category
    if(typeof gender == 'undefined'){
      second_category = (typeof segment[2] != 'undefined' && segment[2] != 'men' && segment[2] != 'women') ? '/' + segment[2] : '';
    }else{
      second_category = (typeof category != 'undefined') ? '/' + category : '';
    }

    url = '/'+ segment[1] + second_category + brandUrl;

    actionAjax(url);
}

function paginate(elm) {

    var segment             = urlSegment();
    var get_segment         = urlGetSegment();
    var get_segment_length  = get_segment.length - 1;
    var seg_count           = segment.length - 1;
    var page_elm            = $('#pagenum');
    var sortUrl             = window.location.search;
    var join_url            = []; //create array variable to join url
    //alert(sortUrl);

    if(typeof $(elm).data('page') != 'undefined') {
        var page_num    = ($(elm).data('page') - 1) * 48;
        var url_pagenum = (page_num == 0) ? '' : '/'+ page_num;
    }else{
        var currentPage = 1;
    }

    if(page_elm.val() != "") {
        segment.splice(seg_count, 1);
    }

    var url_segment = segment.join('/') + url_pagenum;
    // var url_segment = segment.join('/') + url_pagenum + sortUrl;
    var url_get = get_segment[get_segment_length];

    /* push url segment (/) and get segment (?) (&) */
    join_url.push(url_segment);

    if(get_segment.length > 1) {
        join_url.push(url_get);
    }
    url = window.location.origin + create_uri(join_url);

    window.location.replace(url);
}

/* get all checked value based on choosen element */
function getCheckedVal(elm) {
    var valUrl;
    var checked = $('#ul-'+ elm +' input:checkbox:checked').length;

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
            valUrl = '/'+ elm +'/'+ checkedUrl.join('').slice(1, -1);
        } else {
            valUrl = '/'+ elm +'/'+ checkedUrl.join('').slice(0, -1);
        }

    } else {
        valUrl = '';
    }

    return valUrl;
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
        },
        success: function(data){
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
            var url = window.location.origin +'/error_500';
            window.location.replace(url);
        }
    });
}
