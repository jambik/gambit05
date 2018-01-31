jQuery(document).ready(function($){
	var isLateralNavAnimating = false;
	var isLateralCartAnimating = false;
	
	//open/close lateral navigation
	$('.cd-nav-trigger').on('click', function(event){
		event.preventDefault();
		//stop if nav animation is running 
		if( !isLateralNavAnimating ) {
			if($(this).parents('.csstransitions').length > 0 ) isLateralNavAnimating = true; 
			
			$('body').toggleClass('navigation-is-open');
			$('.cd-navigation-wrapper').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(){
				//animation is over
				isLateralNavAnimating = false;
			});
		}
	});
	//open/close lateral cart
	$('#cart-button').on('click', function(event){
		event.preventDefault();
		//stop if cart animation is running 
		if( !isLateralCartAnimating ) {
			if($(this).parents('.csstransitions').length > 0 ) isLateralCartAnimating = true; 
			
			$('body').toggleClass('cart-is-open');
			$('.cart-wrapper').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function(){
				//animation is over
				isLateralCartAnimating = false;
			});
		}
	});
	$('#close-cart').on('click', function(event){
		console.log('ok');
		$('body').removeClass('cart-is-open');
		isLateralCartAnimating = false;
	});
});