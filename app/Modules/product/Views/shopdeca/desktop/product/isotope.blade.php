<!DOCTYPE html>
<html>
<head>
<title>jQuery Isotope</title>
<link rel="stylesheet" href="{{ asset('shopdeca/theme/css/style.css') }}">
<script src="http://code.jquery.com/jquery-latest.js" type="text/javascript"></script>
<script src="{{ asset('shopdeca/theme/script/jquery.isotope.js') }}"></script>
</head>
<body>

<h1>jQuery Isotope</h1>
 
<div class="portfolioFilter">

	<a href="#" data-filter="*" class="current">All Categories</a>
	<a href="#" data-filter=".people">People</a>
	<a href="#" data-filter=".places">Places</a>
	<a href="#" data-filter=".food">Food</a>
	<a href="#" data-filter=".objects">Objects</a>

</div>

<div class="portfolioContainer">

    <div class="objects">
        <img src="http://www.9bitstudios.com/demos/blog/jquery-isotope/images/watch.jpg" alt="image">
    </div>
    
    <div class="people places">
        <img src="http://www.9bitstudios.com/demos/blog/jquery-isotope/images/surf.jpg" alt="image">
    </div>  

    <div class="food">
        <img src="http://www.9bitstudios.com/demos/blog/jquery-isotope/images/burger.jpg" alt="image">
    </div>
    
    <div class="people places">
        <img src="http://www.9bitstudios.com/demos/blog/jquery-isotope/images/subway.jpg" alt="image">
    </div>

    <div class="places objects">
        <img src="http://www.9bitstudios.com/demos/blog/jquery-isotope/images/trees.jpg" alt="image">
    </div>

    <div class="people food objects">
        <img src="http://www.9bitstudios.com/demos/blog/jquery-isotope/images/coffee.jpg" alt="image">
    </div>

    <div class="food objects">
        <img src="http://www.9bitstudios.com/demos/blog/jquery-isotope/images/wine.jpg" alt="image">
    </div>  
    
    <div class="food">
        <img src="http://www.9bitstudios.com/demos/blog/jquery-isotope/images/salad.jpg" alt="image">
    </div>  
    
</div>
 
<script type="text/javascript">

$(window).load(function(){
    var $container = $('.portfolioContainer');
    $container.isotope({
        filter: '*',
        animationOptions: {
            duration: 750,
            easing: 'linear',
            queue: false
        }
    });
 
    $('.portfolioFilter a').click(function(){
        $('.portfolioFilter .current').removeClass('current');
        $(this).addClass('current');
 
        var selector = $(this).attr('data-filter');
        $container.isotope({
            filter: selector,
            animationOptions: {
                duration: 750,
                easing: 'linear',
                queue: false
            }
         });
         return false;
    }); 
});

</script>
 
 
</body>
</html>