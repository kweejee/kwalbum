// Kwalbum 3.0
$(document).ready(function(){ 
	$("#loc").focus().autocomplete('KWALBUM_URL/~ajax/getInputLocations',{
		max:10,cacheLength:1,matchSubset:false,autoFill:true,matchCase:true
	});
	$("#tags").autocomplete('KWALBUM_URL/~ajax/getInputTags',{
		max:10,cacheLength:1,matchSubset:false,autoFill:true,matchCase:true
	});
	$('#fileInput').uploadify({
		'uploader':'KWALBUM_URL/media/ajax/uploadify/uploadify.swf',
		'onComplete':kwablum_refresh_upload_data,
		'cancelImg':'KWALBUM_URL/media/ajax/uploadify/cancel.png',
		'script':'KWALBUM_URL/~ajax/upload.php',
		'multi':true,
		'buttonText':'Browse Files',
		'fileExt':'*.jpg;*.JPG;*.jpe;*.JPE;*.jpeg;*.JPEG;*.png;*.PNG;*.gif;*.GIF',
		'fileDesc':'Image Files Only'
	});
});
var kwablum_refresh_upload_data = function()
{
	$('#fileInput').uploadifySettings('scriptData',{
		'loc':$("#loc").val(),
		'tags':$("#tags").val(),
		'vis':$('#vis').val(),
		'date':$('#date').val(),
		'group_option':$('#group_option').val(),
		'session_id':'SESSION_ID'
	});
}
function kwalbum_upload()
{
	kwablum_refresh_upload_data();
	$('#group_option').val('existing');
	$('#fileInput').uploadifyUpload();
}