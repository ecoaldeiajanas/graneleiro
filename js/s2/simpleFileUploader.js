(function( $ ){

  $.fn.SimpleFileUploader = function(params) {

  	if(!params){
  		params = {n:"0"};
  	}

  	var template = 
		'<div class="qq-uploader">\
			<div class="qq-upload-drop-area">\
				<span class="uploadText">Arraste a imagem para aqui</span>\
				<span class="uploadText">ou</span>\
				<button class="qq-upload-button">Carregue Aqui<input type="file" style="position:absolute; right: 0; top: 0; font-size:1px; margin: 0; padding: 0; cursor: \'pointer\'; opacity: 0" /></button>\
			</div><div class="upload-zone"></div>\
		 </div>';

	
	var self = $(this).first(),
		button = $(this).append($(template)) // the button
  			.find(".qq-upload-button").button({
        		icons: {
            		primary: "ui-icon-arrowthickstop-1-n"
		        }
			}),
		inputbutton = button.find("input"); // the hidden input

	// pressed the button to load
	inputbutton
		.change(function(){
			var file = this.files[0];
			uploadFile(file);
		});

// Region: Drag and drop
	$(".qq-upload-drop-area").live('dragover',function(e){
		$(this).css("background","#CCC");
	});
	$(".qq-upload-drop-area").live('dragleave',function(e){
		$(this).css("background","#FFF");
	});
	jQuery.event.props.push('dataTransfer'); // jquery needs this in order for data transfer to work
	$('.qq-upload-drop-area').bind('drop',function(e){
		e.stopPropagation();
		e.preventDefault();
		if(e.dataTransfer.files.length){
			uploadFile(e.dataTransfer.files[0]);
		}
	});
//
	

	function progress(e){
		$('.upload-zone b').first().html(Math.round((e.loaded / e.total) * 100));
	}

	function uploadFile(file){
		if(params.onBeforeStart) params.onBeforeStart();
		if (!!file.type.match(/image.*/)) {  
			// generate a quick preview of the image
			if ( window.FileReader ) {  
			    reader = new FileReader();  
			    reader.onloadend = function (e) {  
			        self.find(".qq-upload-drop-area").remove();
			        img  = document.createElement("img");
			        img.src = this.result;
			        self.find(".upload-zone")
			        	.append(img)
			        	.append("<div>A Enviar<br><img class='loading' src='css/images/loading.gif'/><b>0</b><b>%</b></div>")
			        	.css("height","100px");
			    };
			    reader.readAsDataURL(file);
			}else{
	    		//console.log("Cannot preview uploaded image because FileReader is not available in this browser.");
	    	} 

	    	var formdata = new FormData();
		    formdata.append("images[]",file);
		    var xhr = new XMLHttpRequest();
			xhr.open("POST","upload.php",true);
			xhr.upload.addEventListener('progress', progress, false);
			xhr.onreadystatechange = function(){
				if(xhr.readyState == 4){
					if(params.onComplete) params.onComplete(xhr.responseText);
					self.find(".upload-zone img.loading").replaceWith("<span style='position: relative; top: -3px' class='loading ui-icon-good ui-icon-circle-check'>&nbsp;</span>");
				}
			}
			xhr.send(formdata); 

    	}
	}

  };
})( jQuery );