<div id="dialog-message" title="">
	<p>
		<span style="float:left; margin:0 7px 50px 0;"></span>
		<p id="dialog-message-text"></p>
	</p>
</div>

<div id="dialog-query" title="">
	<p>
		<span style="float:left; margin:0 7px 50px 0;"></span>
		<p id="dialog-query-text"></p>
	</p>
</div>

<script>

// ui-icon-circle-close
 
$( "#dialog-message" ).dialog({
	autoOpen: false,
	modal: true,
	width:600,
	resizable: false,
	buttons: {
		Ok: function() {
			$( this ).dialog( "close" );
		}
	}
});

$( "#dialog-query" ).dialog({
	autoOpen: false,
	modal: true,
	width:400,
	resizable: false,
	
});

function popupDialog(message, type, afterFn){
	var icon;
	if(type=="error") icon="ui-icon-error ui-icon-circle-close";
	if(type=="good")  icon="ui-icon-good  ui-icon-circle-check";
	$("#dialog-message")
		.dialog({dialogClass: "noTitleDialog"})
		.dialog( "open" )
		.find("#dialog-message-text").html(message).parents("#dialog-message")
		.find("span").attr("class","").addClass(icon);
	if(afterFn) afterFn();
}

function queryDialog(message, fns, fnn){
	var icon;
	icon = "ui-icon-error ui-icon-alert";
	with($("#dialog-query")){
		dialog({
			buttons: {
				"Sim": function() {
					if(fns) fns();
					$( this ).dialog( "close" );
				},
				"Não": function() {
					if(fnn) fnn();
					$( this ).dialog( "close" );
				}
			},
			dialogClass: "noTitleDialog"
		});
		dialog( "open" );
		find("#dialog-query-text").html(message);
		find("span").attr("class","").addClass(icon);
	}
}

</script>
