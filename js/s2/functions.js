(function( $ ){
  $.fn.selectElement = function(s) {
  	$(this).find("option").each(function(){
  		if($(this).html()==s){
  			$(this).attr("selected","true");
  		}
  	});
  	$(this).selectBox('destroy').selectBox();
  };
})( jQuery );