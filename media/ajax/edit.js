// Kwalbum 3.0
$(document).ready(function(){ 	
	$.editable.addInputType('autocomplete', {
		element:$.editable.types.text.element,
		plugin:function(settings, original) {
			$('input', this).autocomplete(settings.autocomplete.data, {
				max:10,cacheLength:1,matchSubset:false,matchCase:true,
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
		submitdata:{item:item_id},
		autocomplete: {data: 'KWALBUM_URL/~ajax/GetInputLocations'}
	});
	$('#description_label').click(function(){$('#description').click();});
	$('#description').editable( 'KWALBUM_URL/~ajax/SetDescription',{
		loadurl:'KWALBUM_URL/~ajax/GetRawDescription?item='+item_id,
		type:"textarea",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		submit:'Save',
		onblur:"submit",
		cols:35,
		rows:20, 
		submitdata:{item:item_id},
	});
	$('#large_description').editable( 'KWALBUM_URL/~ajax/SetDescription',{
		loadurl:'KWALBUM_URL/~ajax/GetRawDescription?item='+item_id,
		type:"textarea",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		submit:'Save',
		onblur:"submit",
		cols:60,
		rows:20, 
		submitdata:{item:item_id},
	});
	$('#tags_label').click(function(){$('#tags').click();});
	$('#tags').editable( 'KWALBUM_URL/~ajax/SetTags',{
		type:"autocomplete",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		onblur:"submit",
		width:200, 
		submitdata:{item:item_id},
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
		submitdata:{item:item_id},
		autocomplete: {data: 'KWALBUM_URL/~ajax/GetInputPersons'}
	});
	$('#date_label').click(function(){$('#date').click();});
	$('#date').editable('KWALBUM_URL/~ajax/SetDate',
	{
		type:"text",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		onblur:"submit",
		width:150, 
		submitdata:{item:item_id},
	});
	$('#sortdate_label').click(function(){$('#sortdate').click();});
	$('#sortdate').editable('KWALBUM_URL/~ajax/SetSortDate',
	{
		type:"text",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		onblur:"submit",
		width:150, 
		submitdata:{item:item_id},
	});
	$('#visibility_label').click(function(){$('#visibility').click();});
	$('#visibility').editable('KWALBUM_URL/~ajax/SetVisibility',
	{
		type:"select",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		onblur:"submit",
		submitdata:{item:item_id},
		loadurl:'KWALBUM_URL/~ajax/GetVisibility?item='+$('#item_id').text()
	});
});