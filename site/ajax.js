
$("#my_form").submit(function(event){
    event.preventDefault(); //prevent default action 
    var post_url = $(this).attr("action"); //get form action url
    var request_method = $(this).attr("method"); //get form GET/POST method
    var data = new FormData(this); //Encode form elements for submission
    $.ajax({
        url : post_url,
        type: "POST",		
        enctype: 'multipart/form-data',
        data : data,
		contentType: false,
		processData:false,
        cache: false,

		xhr: function(){
		//upload Progress
		var xhr = $.ajaxSettings.xhr();
		if (xhr.upload) {
			xhr.upload.addEventListener('progress', function(event) {
				var percent = 0;
				var position = event.loaded || event.position;
				var total = event.total;
				if (event.lengthComputable) {
					percent = Math.ceil(position / total * 100);
				}
				console.log("input:" + event.loaded);
				//update progressbar
				$("#php-progress .progress-bar").css("width", + percent +"%");
			}, true);
		}
		
		
		return xhr;
	}
	
    }).done(function(response){ //
        $("#server-results").html(response);
    });
	

	 t = setTimeout("updateStatus()", 1000);
});

function updateStatus(){ 
	$.getJSON("status.json", function(data){ 
		$.each(data, function(type, val) { 
				console.log(type + ":" + val)
				$("#" + type + "-ftp-progress .progress-bar").css("width", + val +"%");  
			  

			if(type=="end" && val == 0) {   
				t = setTimeout("updateStatus()", 1000);
            }  
        });   
    });  
}  