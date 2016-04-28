function correctDataTable(){
	$(".dataTables_length").html("").append($(".navBar"));
    $(".dataTables_filter input").appendTo(".dataTables_filter").attr("title","Procurar").addClass("defaultText text ui-widget-content ui-corner-all");
    $(".dataTables_filter label").remove();

    $(".dataTables_wrapper tbody tr").live("click", function( e ) {
    	//IE9-console.log($(this));
    	//$(this).removeClass("gradeA");
	    if ( $(this).hasClass('row_selected') ) {
	        $(this).removeClass('row_selected');
	    }else{
	        $(this).parents(".dataTables_wrapper").find('tr.row_selected').removeClass('row_selected');
	        $(this).addClass('row_selected');
	    }
	});
}

function fnGetSelected( oTableLocal )
{
    return $(".dataTables_wrapper").find('tr.row_selected').first();
}
