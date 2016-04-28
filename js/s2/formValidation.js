function displayError( o, t ) {
	var obj = $("<span class='formError'>"+t+"</span>")
			.addClass( "ui-state-highlight" )
			.css('margin-left','8px');
			
	//if(o.is("select")) o = o.next();
	o.addClass( "ui-state-error" );
	o.after(obj);
	//setTimeout(function() {
		//obj.removeClass( "ui-state-highlight", 1500 );
	//}, 500 );
}

function checkLength( o, min, max ) {
	if( o.is("select") ){
		console.log(o.find("option:selected"));
		if(!o.find("option:selected") || o.find("option:selected").val()==-1){
			displayError( o, "Não selecionou nenhum valor." );
			return false;
		}
		return true;
	}
	if ( o.val().length > max || o.val().length < min || o.val()==o.attr('title')) {
		displayError( o, "Tem de ter entre " +min+ " e " +max+ " carateres." );
		return false;
	} else {
		return true;
	}
}

function checkSelected(o){
	var o=$('#id_produtor');  
	if(o.val()==""){
		displayError( $('#id_produtor'), "Não selecionou nenhum produtor." );
		return false;
	}else{
		return true;
	}
}

function checkRegexp( o, regexp, n ) {
	var reg = XRegExp(regexp);
	if ( !reg.test(o.val()) ) {
		displayError( o, n );
		return false;
	} else {
		return true;
	}
}

function checkRegexp_old( o, regexp, n ) {
	if ( !( regexp.test( o.val() ) ) ) {
		o.addClass( "ui-state-error" );
		displayError( o, n );
		return false;
	} else {
		return true;
	}
}

function validateForm(v){
	var bValid=true;
	v.find("input, select").each(function(){
		if($(this).hasClass("err_mandatory"))
			bValid = bValid & checkLength( $(this), 1, 80 );
		if($(this).hasClass("err_email"))
			bValid = bValid & checkEmail( $(this) );
		if($(this).hasClass("err_name"))
			bValid = bValid & checkName( $(this) );
		if($(this).hasClass("err_number"))
			bValid = bValid & checkNumber( $(this) );
		if($(this).hasClass("err_selected"))
			bValid = bValid & CheckSelect( $(this) );
		if($(this).hasClass("err_selected_prod"))
			bValid = bValid & checkSelected( $(this) );
	});
	return bValid;
}

function cleanErrorsOnForm(f){
	f.find('.formError').remove();
	f.find("*").each(function(){
		$(this).removeClass( "ui-state-error" );
	});
}

function checkName(o){
	return checkRegexp( o , "^[ \\p{L}]+$", "Apenas pode ter espaços e letras." );		
}

function checkEmail(o){
	return checkRegexp_old( o , /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "Este e-mail está mal formado." );
}

function checkNumber(o){
	o.val(o.val().replace(",","."));
	return checkRegexp( o , "^[0-9.]+$", "Apenas pode ter números, virgulas ou pontos." );
}


function CheckSelect(o)  
{
var o=$('#id_category');  
if(o.val() == "")  
{
displayError( $('#id_category'), "Não selecionou nenhuma categoria." );	  
return false;  
}  
else  
{  
return true;  
}  
}  
