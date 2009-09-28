// Kwalbum 3.0
$(document).ready(function(){ 
	$("#loc").focus().autocomplete('KWALBUM_URL/~ajax/getInputLocations',{
		max:10,cacheLength:1,matchSubset:false,autoFill:true
	});
	$("#tags").autocomplete('KWALBUM_URL/~ajax/getInputTags',{
		max:10,cacheLength:1,matchSubset:false,autoFill:true
	});
});