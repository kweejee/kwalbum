// Kwalbum 2.0
var fileid = 5;

function add_rows()
{
	var i;
	for (i=0;i<5;i++)
		add_row();
}

function add_row()
{
	var add_row_span = document.getElementById('add_row_span');

	var new_span = document.createElement('span');
	var new_input_file = document.createElement('input');
	new_input_file.type = 'file';
	new_input_file.name = fileid;
	new_input_file.size= 50;
	
	fileid++;

	var new_text = document.createElement('text');
	new_text.innerHTML = fileid+' ';
	
	new_span.appendChild(new_text);
	new_span.appendChild(new_input_file);
	new_span.appendChild(document.createElement('br'));
	add_row_span.parentNode.insertBefore(new_span, add_row_span);
}

$(document).ready(function(){
	//add_rows();
	
	var add_row_span = document.getElementById('add_row_span');
	add_row_span.innerHTML = "<a href='javascript:void(0);' onClick=add_rows();>Add more files...</a>";var new_row_link = document.createElement('a');
	
	$("#loc").focus().autocomplete('/kwalbum/~ajax/getInputLocations', {
		max: 10,
		cacheLength: 10,
	});
	$("#tags").focus().autocomplete('/kwalbum/~ajax/getInputTags', {
		max: 10,
		cacheLength: 10,
	});
	
});
