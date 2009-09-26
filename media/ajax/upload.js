// Kwalbum 3.0
$(document).ready(function(){ 
	$("#loc").focus().autocomplete('KWALBUM_URL/~ajax/getInputLocations',{
		max:10,cacheLength:10
	});
	$("#tags").autocomplete('KWALBUM_URL/~ajax/getInputTags',{
		max:10,cacheLength:10
	});
	$('#fileInput').uploadify({
		'uploader':'KWALBUM_URL/media/ajax/uploadify/uploadify.swf',
		'cancelImg':'KWALBUM_URL/media/ajax/uploadify/cancel.png',
		'script':'KWALBUM_URL/~ajax/upload.php',
		'multi':true,
		'buttonText':'Browse Files',
		'fileExt':'*.jpg;*.JPG;*.jpe;*.JPE;*.jpeg;*.JPEG;*.png;*.PNG;*.gif;*.GIF',
		'fileDesc':'Image Files Only'
	});
});
function kwalbumUpload()
{
	$('#fileInput').uploadifySettings('scriptData',{
		'loc':$("#loc").val(),
		'tags':$("#tags").val(),
		'vis':$('#vis').val(),
		'date':$('#date').val(),
		'session_id':'SESSION_ID'
	});
	$('#fileInput').uploadifyUpload();
}
