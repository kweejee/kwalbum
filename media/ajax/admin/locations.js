// Kwalbum 3.0
$(document).ready(function(){
	$('.kwalbumLocationName').editable('KWALBUM_URL/~ajaxAdmin/EditLocationName',
	{
		type:"text",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		onblur:"submit",
        width:250
	});
});
var deleteLocation = function (id) {
    if (confirm('You are about to permanently delete "'+$('#loc'+id).text()+'".')) {
        $.post(
            "KWALBUM_URL/~ajaxAdmin/DeleteLocation",
            {id: id},
            function () {
                $('#row'+id).hide();
            }
        );
        $('#loc'+id).text('deleting...');
    }
};
