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
   $('#fileInput').change(function () {
        $('#btnUpload').show();
        $('#divFiles').html('');
        for (var i = 0; i < this.files.length; i++) { //Progress bar and status label's for each file genarate dynamically
            var fileId = i;
            $("#divFiles").append('<div class="col-md-12">' +
                '<div class="progress-bar progress-bar-striped active" id="progressbar_' + fileId + '" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%"></div>' +
                '</div>' +
                '<div class="col-md-12">' +
                    '<div class="col-md-6">' +
                       '<input type="button" class="btn btn-danger" style="display:none;line-height:6px;height:25px" id="cancel_' + fileId + '" value="cancel">' +
                    '</div>' +
                    '<div class="col-md-6">' +
                       '<p class="progress-status" style="text-align: right;margin-right:-15px;font-weight:bold;color:saddlebrown" id="status_' + fileId + '"></p>' +
                    '</div>' +
                '</div>' +
                '<div class="col-md-12">' +
                     '<p id="notify_' + fileId + '" style="text-align: right;"></p>' +
                '</div>');
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
var kwablum_refresh_upload_data = function ()
{
//	$('#files').uploadifySettings('scriptData',{
//		'loc':$("#loc").val(),
//		'tags':$("#tags").val(),
//		'vis':$('#vis').val(),
//		'date':$('#date').val(),
//		'time':$('#time').val(),
//		'group_option':$('#group_option').val(),
//		'import_caption':($('#import_caption').attr('checked') ? 1 : 0),
//		'import_keywords':($('#import_keywords').attr('checked') ? 1 : 0),
//		'session_id':'SESSION_ID'
//	});
};
function uploadSingleFile(file, i) {
    var fileId = i;
    var ajax = new XMLHttpRequest();
    //Progress Listener
    ajax.upload.addEventListener("progress", function (e) {
        var percent = (e.loaded / e.total) * 100;
        $("#status_" + fileId).text(Math.round(percent) + "% uploaded, please wait...");
        $('#progressbar_' + fileId).css("width", percent + "%")
        $("#notify_" + fileId).text("Uploaded " + (e.loaded / 1048576).toFixed(2) + " MB of " + (e.total / 1048576).toFixed(2) + " MB ");
    }, false);
    //Load Listener
    ajax.addEventListener("load", function (e) {
        $("#status_" + fileId).text(event.target.responseText);
        $('#progressbar_' + fileId).css("width", "100%")

        //Hide cancel button
        var _cancel = $('#cancel_' + fileId);
        _cancel.hide();
    }, false);
    //Error Listener
    ajax.addEventListener("error", function (e) {
        $("#status_" + fileId).text("Upload Failed");
    }, false);
    //Abort Listener
    ajax.addEventListener("abort", function (e) {
        $("#status_" + fileId).text("Upload Aborted");
    }, false);

    ajax.open("POST", "/api/upload/UploadFiles"); // Your API .net, php

    var uploaderForm = new FormData(); // Create new FormData
    uploaderForm.append("file", file); // append the next file for upload
    ajax.send(uploaderForm);

    //Cancel button
    var _cancel = $('#cancel_' + fileId);
    _cancel.show();

    _cancel.on('click', function () {
        ajax.abort();
    })
}
var kwalbum_upload = function ()
{
	kwablum_refresh_upload_data();
	$('#group_option').val('existing');
    var file = document.getElementById("fileInput") //All files
    for (var i = 0; i < file.files.length; i++) {
           uploadSingleFile(file.files[i], i);
    }
};
