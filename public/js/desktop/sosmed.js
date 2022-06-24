	(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));

    function fbShare() {
    	var url = $('#fb_share').data('url');
    	var title = $('#product_name').val();
    	var descr = document.getElementById('product_description').innerHTML;
    	var image = document.getElementById("default_image").src;
    	var winWidth = 520;
    	var winHeight = 350;
    	var winTop = (screen.height / 2) - (winHeight / 2);
        var winLeft = (screen.width / 2) - (winWidth / 2);
        var images = image.replace(/\./g,'%2E');
        window.open('https://www.facebook.com/sharer.php?s=100&p[title]=' + title + '&p[summary]=' + descr + '&p[url]=' + url + '&p[images][0]=' + images, 'sharer', 'top=' + winTop + ',left=' + winLeft + ',toolbar=0,status=0,width=' + winWidth + ',height=' + winHeight);
    }

	$('.popup').click(function(event) {
	    var width  = 575,
	        height = 400,
	        left   = ($(window).width()  - width)  / 2,
	        top    = ($(window).height() - height) / 2,
	        url    = this.href,
	        opts   = 'status=1' +
	                 ',width='  + width  +
	                 ',height=' + height +
	                 ',top='    + top    +
	                 ',left='   + left;
	    
	    window.open(url, 'twitter', opts);
	 
	    return false;
	});

  	function twitterShare() {
    	var url = $('#twitter_share').data('url');
    	var title = $('#product_name').val();
  		var url_tw = "https://twitter.com/intent/tweet?url="+ url +"&text="+ title + "";

  		$('#twitter_share').attr("href", url_tw);
  	}

	$('#pinterest_share').click(function() {
	    var url = $('#pinterest_share').data('url');
	    var media = document.getElementById("default_image").src;
	    var desc = $('#product_name').val();
	    window.open("//www.pinterest.com/pin/create/button/"+
	    "?url="+url+
	    "&media="+media+
	    "&description="+desc,"_blank");
	    return false;
	});

	$('#gplus_share').click(function() {
	    var url = $('#gplus_share').data('url');
	    var media = document.getElementById("default_image").src;
	    var desc = $('#product_name').val();
	    window.open("https://plus.google.com/share?url=" + url + "",
	    			'','menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
	    return false;
	});

