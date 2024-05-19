// Kwalbum 3.0
$(document).ready(function(){
	$.editable.addInputType('autocomplete', {
		element:$.editable.types.text.element,
		plugin:function(settings, original) {
			const data = {
				minLength: 2,
				source: function( request, response ) {
					let term = request.term;
					if ( term in settings.cache ) {
						response( settings.cache[ term ] );
						return;
					}

					settings.lastXhr = $.getJSON( "KWALBUM_URL/~ajax/"+settings.autocomplete_action, request, function( data, status, xhr ) {
						settings.cache[ term ] = data;
						if ( xhr === settings.lastXhr ) {
							response( data );
						}
					});
				}
			}
			$('input').autocomplete(data);
		}
	});
	$.editable.addInputType('datepicker', {
		element:$.editable.types.text.element,
		plugin : function(settings, original) {
		$("input", this)
		.datepicker({
			defaultDate: settings.data,
			dateFormat: 'yy-mm-dd'
		})
		.bind('dateSelected', function(e, selectedDate, $td) {
		    $(form).submit();
		})
		.trigger('change')
		.click();
		}
	});
	$('#location_label').click(function(){$('#location').click();});
	$('#location').editable( 'KWALBUM_URL/~ajax/SetLocation',{
		type:"autocomplete",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		onblur:"submit",
		width:200,
		style:"inherit",
		submitdata:{item:item_id},
		autocomplete_action: 'GetInputLocations',
		cache: {},
		lastXhr: null
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
		style:"inherit",
		submitdata:{item:item_id}
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
		style:"inherit",
		submitdata:{item:item_id}
	});
	$('#tags_label').click(function(){$('#tags').click();});
	$('#tags').editable( 'KWALBUM_URL/~ajax/SetTags',{
		type:"autocomplete",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		onblur:"submit",
		width:200,
		submitdata:{item:item_id},
		autocomplete_action: 'GetInputTags',
		style:"inherit",
		cache: {},
		lastXhr: null
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
		style:"inherit",
		autocomplete_action: 'GetInputPersons',
		cache: {},
		lastXhr: null
	});
	$('#date_label').click(function(){$('#date').click();});
	$('#date').editable('KWALBUM_URL/~ajax/SetDate',
	{
		type:"datepicker",
		data:$("#date").html(),
		tooltip:"Click to edit...",
		indicator:"Saving...",
		submit:'Save',
		cancel:'Cancel',
		onblur:'ignore',
		width:80,
		style:"inherit",
		submitdata:{item:item_id}
	});
	$('#time_label').click(function(){$('#time').click();});
	$('#time').editable('KWALBUM_URL/~ajax/SetTime',
	{
		type:"text",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		onblur:"submit",
		width:70,
		style:"inherit",
		submitdata:{item:item_id}
	});
	$('#sortdate_label').click(function(){$('#sortdate').click();});
	$('#sortdate').editable('KWALBUM_URL/~ajax/SetSortDate',
	{
		type:"text",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		onblur:"submit",
		width:150,
		style:"inherit",
		submitdata:{item:item_id}
	});
	$('#visibility_label').click(function(){$('#visibility').click();});
	$('#visibility').editable('KWALBUM_URL/~ajax/SetVisibility',
	{
		type:"select",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		onblur:"submit",
		style:"inherit",
		submitdata:{item:item_id},
		loadurl:'KWALBUM_URL/~ajax/GetVisibility?item='+item_id
	});
	$('#delete_button').click(function(){
		if (confirm('You are about to permanently delete an item.')){
			$.post("KWALBUM_URL/~ajax/DeleteItem", {item: item_id},function(){$('#delete').text('DELETED!');});
			$('#delete').text('deleting...');
		}
	});
});
const rotate = function (degrees) {
	$.post(
		"KWALBUM_URL/~ajax/RotateItem",
		{item: item_id, degrees: degrees},
		function () {
			location.reload(true);
		}
	);
	$("#kwalbumRotateOptions").text("rotating...");
};
