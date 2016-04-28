(function( $ ){

  $.fn.createPageAlert = function(params) {

  	if(!params || !params.text){
  		return;
  	}

  	var template = 
		'<div class="ui-state-highlight ui-corner-all hidden" style="padding: 10px 16px;"> \
            <p style="float: left;line-height: 1.3em;">\
                <span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>\
                <span class="ttext"></span>\
            </p>\
            <p class="closeTopPageAlert" style="float:right;line-height: 1.3em; text-decoration:underline; cursor:pointer;">Fechar</p>\
            <div style="clear:both"></div>\
        </div>';

	var obj = $(template).clone();
	obj.find(".ttext").html(params.text);
	$(this).first().empty().append(obj);
	obj.show("fast");
	
	$(".closeTopPageAlert").live("click",function(){
		$(this).closest('div')
			.hide('fast',function(){
				$(this).remove();
			});
	});	

  };
})( jQuery );