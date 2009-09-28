// Kwalbum 3.0
$(document).ready(function(){ 	
	$.editable.addInputType('autocomplete', {
		element:$.editable.types.text.element,
		plugin:function(settings, original) {
			$('input', this).autocomplete(settings.autocomplete.data, {
				max:10,cacheLength:1,matchSubset:false,
				autoFill:true});
		}
	});
  
  	$('#location_label').click(function(){$('#location').click();});
	$('#location').editable( 'KWALBUM_URL/~ajax/SetLocation',{
		type:"autocomplete",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		onblur:"submit",
		width:200, 
		submitdata:{item:$('#item_id').text()},
		autocomplete: {data: 'KWALBUM_URL/~ajax/GetInputLocations'}
	});
	
  	$('#description_label').click(function(){$('#description').click();});
	$('#description').editable( 'KWALBUM_URL/~ajax/SetDescription',{
		loadurl:'KWALBUM_URL/~ajax/GetRawDescription?item='+$('#item_id').text(),
		type:"textarea",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		submit:'Save',
		onblur:"submit",
		cols:30,
		rows:15, 
 submitdata:{item:$('#item_id').text()},
	});
	
  	$('#tags_label').click(function(){$('#tags').click();});
	$('#tags').editable( 'KWALBUM_URL/~ajax/SetTags',{
		type:"autocomplete",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		onblur:"submit",
		width:200, 
		submitdata:{item:$('#item_id').text()},
		autocomplete: {data: 'KWALBUM_URL/~ajax/GetInputTags'}
	});
	
	$('#persons_label').click(function(){$('#persons').click();});
	$('#persons').editable('KWALBUM_URL/~ajax/SetPersons',
	{
		type:"autocomplete",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		onblur:"submit",
		width:200, 
		submitdata:{item:$('#item_id').text()},
		autocomplete: {data: 'KWALBUM_URL/~ajax/GetInputPersons'}
	});
	
	$('#visibility_label').click(function(){$('#visibility').click();});
	$('#visibility').editable('KWALBUM_URL/~ajax/SetVisibility',
	{
		type:"select",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		onblur:"submit",
		submitdata:{item:$('#item_id').text()},
		loadurl:'KWALBUM_URL/~ajax/GetVisibility?item='+$('#item_id').text()
	});
});