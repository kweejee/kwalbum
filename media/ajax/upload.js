// Kwalbum 3.0
$(document).ready(function(){ 
	$("#loc").focus().autocomplete('/kwalbum/~ajax/getInputLocations',{
		max:10,cacheLength:10
	});
	$("#tags").focus().autocomplete('/kwalbum/~ajax/getInputTags',{
		max:10,cacheLength:10
	});
	$('#fileInput').uploadify({
		'uploader':'../media/ajax/uploadify/uploadify.swf',
		'cancelImg':'../media/ajax/uploadify/cancel.png',
		'script':'../~ajax/upload.php',
		'multi':true,
		'buttonText':'Browse Files',
		'fileExt':'*.jpg;*.png;*.gif','fileDesc':'Image Files Only'
	});
});
function kwalbumUpload()
{
	$('#fileInput').uploadifySettings('scriptData',{
		'loc':$("#loc").val(),
		'tags':$("#tags").val(),
		'vis':$('#vis').val(),
		'date':$('#date').val()
	});
	$('#fileInput').uploadifyUpload();
}
