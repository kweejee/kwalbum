// Kwalbum 3.0
var loc = {
	lastXhr: null,
	cache: {}
};
var tags = {
	lastXhr: null,
	cache: {}
};
$(document).ready(function(){
	$("#loc").focus().autocomplete({
		minLength: 2,
		source: function( request, response ) {
			var term = request.term;
			if ( term in loc.cache ) {
				response( loc.cache[ term ] );
				return;
			}

			loc.lastXhr = $.getJSON( "KWALBUM_URL/~ajax/getInputLocations", request, function( data, status, xhr ) {
				loc.cache[ term ] = data;
				if ( xhr === loc.lastXhr ) {
					response( data );
				}
			});
		}
	});
	$("#tags").autocomplete({
		minLength: 2,
		source: function( request, response ) {
			var term = request.term;
			if ( term in tags.cache ) {
				response( tags.cache[ term ] );
				return;
			}

			tags.lastXhr = $.getJSON( "KWALBUM_URL/~ajax/getInputTags", request, function( data, status, xhr ) {
				tags.cache[ term ] = data;
				if ( xhr === tags.lastXhr ) {
					response( data );
				}
			});
		}
	});
//	$('#files').uploadify({
//		'uploader':'KWALBUM_URL/media/ajax/uploadify/uploadify.swf',
//		'onComplete':function(event, ID, fileObj, response, data){
//			if (console && console.log) {
//				console.log(response);
//				console.log(data);
//			}
//			kwablum_refresh_upload_data();
//		},
//		'onError':function(event,ID,fileObj,errorObj){
//			if (console && console.log) {
//				console.log(errorObj);
//			}
//		},
//		'cancelImg':'KWALBUM_URL/media/ajax/uploadify/cancel.png',
//		'script':'KWALBUM_URL/~ajax/upload.php',
//		'multi':true,
//		'buttonText':'Browse Files',
//		'fileDataName':'userfile',
//		'fileExt':'*.jpg;*.JPG;*.jpe;*.JPE;*.jpeg;*.JPEG;*.png;*.PNG;*.gif;*.GIF',
//		'fileDesc':'Image Files Only'
//	});
    $("#date").datepicker({dateFormat: "yy-mm-dd"});
});

$(function () {
	$("#fileupload").fileupload({
		sequentialUploads: true, // todo: UI toggle option
		dataType: "json",
		add: function(e, data) {
			data.context = $('<p class="file"></p>')
				.append($('<span>').text(data.files[0].name))
				.appendTo($("#progress"));
			data.submit();
		},
		progressall: function (e, data) {
			let progress = parseInt(data.loaded / data.total * 100, 10);
			let progressElement = $('#progress');
			let totalCount = progressElement.children().length - 1;
			let uploadedCount = progressElement.children('.done').length + 1;
			progressElement.children('.bar')
				.css('width', progress + '%')
				.addClass('fileupload-processing')
				.text("Uploading " + uploadedCount + " of " + totalCount);
		},
		progress: function(e, data) {
			let progress = parseInt((data.loaded / data.total) * 100, 10);
			data.context.css("background-position-x", 100 - progress + "%");
		},
		done: function(e, data) {
			// if (data.result.errorCode)
			let file = data.result.files[0];
			data.context
				.addClass("done")
				.find("span")
				.replaceWith('<a href="' + file.url + '" target="_blank">' +
					'<table><tr><td><img src="' + file.thumbnailUrl + '"></td>' +
					'<td>' + file.name + '<br>' + file.visibleDate + '</td></tr></table>' +
					'</a>');
		},
		fail: function(e, data) {
			let response = data.jqXHR.responseJSON;
			if (response.errors != undefined && response.errors[0] != undefined) {
				data.context
					.addClass("error")
					.find("span")
					.text('"' + response.errors[0] + '" ' + data.files[0].name);
			}
		},
		stop: function(e) {
			$('#progress .bar')
				.removeClass('fileupload-processing')
				.text('');
		}
	});
});
