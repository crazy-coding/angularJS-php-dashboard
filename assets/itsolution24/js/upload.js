// UPLOAD LOGO

window.$(document).ready(function ($) {

	var imageIsLoaded = function (e) {
		$("#file").css("color","green");
		$('#logo_preview').css("display", "inline-block");
		$('#logo').attr('src', e.target.result);
		$('#logo').attr('width', '110px');
		$('#logo').attr('height', '110px');
	};
	
	$("#uploadlogo").on('submit',(function(e) {
		e.preventDefault();
		var $btn = $(".btn-logo-upload").button("loading");
		$("#selectImage .message").hide().empty();
		$('.logo-loader').show();
		$.ajax({
			url: window.baseUrl + "/_inc/upload_logo.php", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data)   // A function to be called if request succeeds
			{
				$('.logo-loader').hide();
				$("#selectImage .message").show().html(data);
				$btn.button("reset");
			}
		});
	}));

	// Function to preview image after validation
	$(function() {
		$("#file").change(function() {
			$("#selectImage .message").fadeOut('fast').empty(); // To remove the previous error message
			var file = this.files[0];
			var imagefile = file.type;
			var match= ["image/jpeg","image/png","image/jpg"];
			if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
			{
				$('#logo').attr('src',window.baseUrl+'/assets/itsolution24/img/logo-favicons/nologo.png');
				$("#selectImage .message").show()
					.html("<p>Please Select A valid Image File</p>"+"<span class='error_message'><strong>Note: </strong> Only jpeg, jpg and png Images type allowed</span>");
				return false;
			} else {
				var reader = new FileReader();
				reader.onload = imageIsLoaded;
				reader.readAsDataURL(this.files[0]);
			}
		});
	});
});


// UPLOAD FAVICON

window.$(document).ready(function ($) {

	var faviconIsLoaded = function (e) {
		$("#faviconFile").css("color","green");
		$('#favicon_preview').css("display", "inline-block");
		$('#favicon').attr('src', e.target.result);
		$('#favicon').attr('width', '32px');
		$('#favicon').attr('height', '32px');
	};

	$("#uploadFavicon").on('submit',(function(e) {
		e.preventDefault();
		var $btn = $(".btn-favicon-upload").button("loading");
		$("#selectFavicon .message").hide().empty();
		$('.favicon-loader').show();
		$.ajax({
			url: window.baseUrl + "/_inc/upload_favicon.php", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data: new FormData(this), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data)   // A function to be called if request succeeds
			{
				$('.favicon-loader').hide();
				$("#selectFavicon .message").show().html(data);
				$btn.button("reset");
			}
		});
	}));

	// Function to preview image after validation
	$(function() {
		$("#faviconFile").change(function() {
			$("#selectFavicon .message").fadeOut('fast').empty(); // To remove the previous error message
			var file = this.files[0];
			var imagefile = file.type;
			var match= ["image/ico","image/png"];
			if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
			{
				$('#favicon').attr('src',window.baseUrl+'/assets/itsolution24/img/logo-favicons/nofavicon.png');
				$("#selectFavicon .message").show()
					.html("<p>Please Select A valid Image File</p>"+"<span class='error_message'><strong>Note: </strong> Only ico and png Images type allowed</span>");
				return false;
			} else {
				var reader = new FileReader();
				reader.onload = faviconIsLoaded;
				reader.readAsDataURL(this.files[0]);
			}
		});
	});
});