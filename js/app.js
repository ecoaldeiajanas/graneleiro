$(document).ready(function() {

$(window).scroll(function() {
	if ( $(this).scrollTop() > 800 ) {
        $('.scrollup-inside').removeClass('hidden');
	} else {
        $('.scrollup-inside').addClass('hidden');
	}
});

$('.scrollup-link').click(function(e) {
	e.preventDefault();
	$('html, body').animate({
        scrollTop: $('#motherContainer').offset().top
    }, 500);
});

});