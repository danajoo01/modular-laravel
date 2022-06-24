var action = {
  hit:[]
};

var input = document.getElementById("keyword");
input.addEventListener("keypress", function(e) {
    if(e.which == 13) {
        var input = document.createElement("input");
        input.setAttribute("type", "hidden");
        input.setAttribute("name", "isEnter");
        input.setAttribute("value", true);
        document.getElementById("searching").appendChild(input);
    }
});

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

$(document).ready(function(){
  $.ajaxSetup({
    headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') },
//    timeout: 10000,
    error: function(jqXHR, textStatus){
      if(textStatus === 'timeout'){
        alert('Request timed out. Mohon refresh browser anda.');
      }
    },
    statusCode: {
      404: function() {
        location.reload();
      },
      500: function() {
        window.location.href = '/checkout/cart/';
      },
      302: function() {
        window.location.href = '/checkout/cart/';
      }
    }
  });

//  $('a[href="#"]').click(function(event){
//    event.preventDefault();
//  });
  $(document).on("click", "a[href='#']", function (event) {
    event.preventDefault();
  });

  number_format = function(number, decimals, dec_point, thousands_sep) {
    //  discuss at: http://phpjs.org/functions/number_format/
    // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: davook
    // improved by: Brett Zamir (http://brett-zamir.me)
    // improved by: Brett Zamir (http://brett-zamir.me)
    // improved by: Theriault
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: Michael White (http://getsprink.com)
    // bugfixed by: Benjamin Lupton
    // bugfixed by: Allan Jensen (http://www.winternet.no)
    // bugfixed by: Howard Yeend
    // bugfixed by: Diogo Resende
    // bugfixed by: Rival
    // bugfixed by: Brett Zamir (http://brett-zamir.me)
    //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    //  revised by: Luke Smith (http://lucassmith.name)
    //    input by: Kheang Hok Chin (http://www.distantia.ca/)
    //    input by: Jay Klehr
    //    input by: Amir Habibi (http://www.residence-mixte.com/)
    //    input by: Amirouche
    //   example 1: number_format(1234.56);
    //   returns 1: '1,235'
    //   example 2: number_format(1234.56, 2, ',', ' ');
    //   returns 2: '1 234,56'
    //   example 3: number_format(1234.5678, 2, '.', '');
    //   returns 3: '1234.57'
    //   example 4: number_format(67, 2, ',', '.');
    //   returns 4: '67,00'
    //   example 5: number_format(1000);
    //   returns 5: '1,000'
    //   example 6: number_format(67.311, 2);
    //   returns 6: '67.31'
    //   example 7: number_format(1000.55, 1);
    //   returns 7: '1,000.6'
    //   example 8: number_format(67000, 5, ',', '.');
    //   returns 8: '67.000,00000'
    //   example 9: number_format(0.9, 0);
    //   returns 9: '1'
    //  example 10: number_format('1.20', 2);
    //  returns 10: '1.20'
    //  example 11: number_format('1.20', 4);
    //  returns 11: '1.2000'
    //  example 12: number_format('1.2000', 3);
    //  returns 12: '1.200'
    //  example 13: number_format('1 000,50', 2, '.', ' ');
    //  returns 13: '100 050.00'
    //  example 14: number_format(1e-8, 8, '.', '');
    //  returns 14: '0.00000001'

    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + (Math.round(n * k) / k)
        .toFixed(prec);
    };

    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
      s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }

    if ((s[1] || '').length < prec) {
      s[1] = s[1] || '';
      s[1] += new Array(prec - s[1].length + 1).join('0');
    }

    return s.join(dec);
  };

  ucfirst = function(str) {
    //  discuss at: http://phpjs.org/functions/ucfirst/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: Onno Marsman
    // improved by: Brett Zamir (http://brett-zamir.me)
    //   example 1: ucfirst('kevin van zonneveld');
    //   returns 1: 'Kevin van zonneveld'

    str += '';
    var f = str.charAt(0).toUpperCase();
    return f + str.substr(1);
  };

  strstr = function(haystack, needle, bool) {
    //  discuss at: http://phpjs.org/functions/strstr/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: Onno Marsman
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    //   example 1: strstr('Kevin van Zonneveld', 'van');
    //   returns 1: 'van Zonneveld'
    //   example 2: strstr('Kevin van Zonneveld', 'van', true);
    //   returns 2: 'Kevin '
    //   example 3: strstr('name@example.com', '@');
    //   returns 3: '@example.com'
    //   example 4: strstr('name@example.com', '@', true);
    //   returns 4: 'name'

    var pos = 0;

    haystack += '';
    pos = haystack.indexOf(needle);
    if (pos == -1) {
      return false;
    } else {
      if (bool) {
        return haystack.substr(0, pos);
      } else {
        return haystack.slice(pos);
      }
    }
  };});

function toSlug(Text)
{
  return Text
      .toLowerCase()
      .replace(/[^\w ]+/g,'')
      .replace(/ +/g,'-');
}

function sendVal(val,type,filter,name,productParent,genders){
  if(typeof(name)=="undefined"){var value = val;}else{var value = name;}
  var gender = $('.nav .gender-tab tabs > li > a').text().toLowerCase().replace(' ','');

  if(gender=="wanita")
  {
    gender = "women";
  }
  else if(gender=="pria")
  {
    gender = "men";
  }

  if(typeof(type)!="undefined" && type !="")
  {
    if(type=="brand" || type=="category" || type=="terms" )
    {
      if(type=="category")
      {
        var act_gender = genders;
      }
      else
      {
        var act_gender = gender
      }

      $.redirectPost($("#searching").attr("action"),{"keyword":val,"type":type,"gender":act_gender});
    }
    else if(type=="filter" && typeof(filter)!="undefined" && filter !="")
    {
      $.redirectPost('<?php echo current_url(); ?>',{"keyword":val,"type":type,"filter":filter});
    }
    else if(type=="push_promo" || type=="tags")
    {
      $.redirectPost($("#searching").attr("action"),{"keyword":name,"type":type,"gender":gender,"url":val});
    }
  }
  else
  {
    if(typeof(productParent)=="undefined" && productParent=="")
    {
      $.redirectPost($("#searching").attr("action"),{"keyword":val,"gender":gender,"type":"product","parent":""});
    }
    else
    {
      $.redirectPost($("#searching").attr("action"),{"keyword":value,"gender":gender,"type":"product","parent":productParent,"url":val});
    }
  }
}

function searchSolr(id,form){
  var gender = $('.nav .gender-tab tabs > li > a').text().toLowerCase().replace(' ','');
  if(gender=="wanita"){
    gender = "women";
  }else if(gender=="pria"){
    gender = "men";
  }

  if(typeof(form)=='undefined'){var formid=""; }else{ var formid = "#"+form;}
  $(formid+" #"+id).autocomplete($(formid+" #"+id).attr('url'), {maxdata:50, selectFirst: false, width: "100%", tops: "40px", lefts: "0px", scrollHeight: "100%", delay:false, divResults:".search", resultsClass:"search-sugestion", extraParams:{"gender":gender}});
  $(formid+" #"+id).result(function(event, data, formatted){
    if(data){
      $('#keyword').val(data[1]);

      $('#type').val(data[3]);
      $('#filter').val(data[4]);
      $('#names').val(data[5]);
      $('#parent').val(data[6]);
      $('#gender').val(data[7]);

      if(data[3]=="product"){
        $('#url').val(data[2]);
        $('#keywords').val(data[5]);
      }else if(data[3]=="push_promo" || data[3]=="tags"){
        $('#url').val(data[2]);
      }else{
        $('#keywords').val(data[2]);
      }
    }
  });
}

function search_bb(keyword){
  var nkey = keyword.replace(" ", "+");

  $("#searching").attr('action','');
  $("#searching").attr('action','/search?s='+ nkey);
}

/* find a char in array and return a key */
function find_key(key, array) {
    // The variable results needs var in this case (without 'var' a global variable is created)
    var results = [];
    var key_index;

    for (var i = 0; i < array.length; i++) {
        if (array[i].indexOf(key) == 0) {
            results.push(array[i]); //show selected array
            key_index = i; //show index array
        }
    }
    return key_index;
}

/* combine all separated array to be a path URL */
function create_uri(array) {
    var result = '';
    var separator;

    for(var i=0; i < array.length; i++) {
        if(i == 0) {
            separator = '?';
        } else {
            separator = '&';
        }

        result += array[i] + separator;
    }

    result = result.slice(0, -1);

    return result;
}

/* Generate uri segment into an array */
function urlSegment() {
    var newURL = window.location.protocol + "://" + window.location.host + "/" + window.location.pathname;
    var pathArray = window.location.pathname.split( '/' );
    var segment = pathArray;

    return segment;
}

/* Separate full uri to segment by & or ? */
function urlGetSegment() {
    var fullPathUrl = window.location.pathname + window.location.search;
    var separators = ['\\\&', '\\\?'];
    var segment = fullPathUrl.split(new RegExp(separators.join('|'), 'g'));

    return segment;
}

/* Get separator between ? or & */
function get_separator() {
  var fullPathUrl = window.location.pathname + window.location.search;
  var separator

  if(fullPathUrl.indexOf('?') > -1) {
      separator = '&';
  } else if(fullPathUrl.indexOf('?') > -1 && fullPathUrl.indexOf('&') > -1) {
      separator = '?';
  }

  return separator;
}

function set_separator() {
    var fullPathUrl = window.location.pathname + window.location.search;
    var separator

    if(fullPathUrl.indexOf('?') === -1) {
        separator = '?';
    } else {
        separator = '&';
    }

    return separator;
}

 /* get last url */
function last_url() {
    var fullPathUrl = window.location.pathname + window.location.search;

    var separator = get_separator();
    var url = fullPathUrl.substring(fullPathUrl.lastIndexOf(separator) + 1);

    return url;
}

function findUriSegment(word) {
  var segment = urlSegment();
  var val;

  switch(word) {
    case 'cat_parent' :
      result = (typeof segment[2] != 'undefined') ? segment[2] : [segment[2]];
    break;
    case 'cat_children' :
      result = (typeof segment[3] != 'undefined') ? segment[3] : [segment[3]];
    break;
    case 'color' :
      var get_val = segment.indexOf('color') + 1;
      var value = (typeof segment[get_val] != 'undefined') ? segment[get_val] : segment[get_val];
      result = value.split('--');
    break;
    default:
      var get_val = segment.indexOf(word) + 1;
      var value = (typeof segment[get_val] != 'undefined') ? segment[get_val] : segment[get_val];
      result = value.split('-');
    break;
  }

  return result;
}

function findUriKey(word, separator) {
  var url = window.location.search.substr(1);
  var exp_uri = url.split('&');
  var arr_param = [];

  $.each(exp_uri, function(index, value) {
    if(value.search(word) > -1) {
      var param = value;
      var exp_uri_key = value.split('=');

      if(exp_uri_key[1].search(separator) > -1) {
        var exp_multi_filter = exp_uri_key[1].split(separator);
        arr_param = exp_multi_filter;
      } else {
        arr_param = [exp_uri_key[1]];
      }
    }
  });

  return arr_param;  
}