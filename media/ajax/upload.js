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
	$('#files').uploadify({
		'uploader':'KWALBUM_URL/media/ajax/uploadify/uploadify.swf',
		'onComplete':function(event, ID, fileObj, response, data){
			kwablum_refresh_upload_data();
		},
		'onError':function(event,ID,fileObj,errorObj){
		},
		'cancelImg':'KWALBUM_URL/media/ajax/uploadify/cancel.png',
		'script':'KWALBUM_URL/~ajax/upload.php',
		'multi':true,
		'buttonText':'Browse Files',
		'fileDataName':'userfile',
		'fileExt':'*.jpg;*.JPG;*.jpe;*.JPE;*.jpeg;*.JPEG;*.png;*.PNG;*.gif;*.GIF',
		'fileDesc':'Image Files Only'
	});
	$("#date").datepicker();
});
var kwablum_refresh_upload_data = function()
{
	$('#files').uploadifySettings('scriptData',{
		'loc':$("#loc").val(),
		'tags':$("#tags").val(),
		'vis':$('#vis').val(),
		'date':$('#date').val(),
		'time':$('#time').val(),
		'group_option':$('#group_option').val(),
		'import_caption':($('#import_caption').attr('checked') ? 1 : 0),
		'import_keywords':($('#import_keywords').attr('checked') ? 1 : 0),
		'session_id':'SESSION_ID'
	});
}
function kwalbum_upload()
{
	kwablum_refresh_upload_data();
	$('#group_option').val('existing');
	$('#files').uploadifyUpload();
}