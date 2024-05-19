// Kwalbum 3.0
$(document).ready(function(){
	$('.kwalbumPermission').editable('KWALBUM_URL/~ajaxAdmin/EditUserPermission',
	{
		type:"select",
		tooltip:"Click to edit...",
		indicator:"Saving...",
		onblur:"submit",
		style:"inherit",
		loadurl:'KWALBUM_URL/~ajaxAdmin/GetUserPermission'
	});
});

const deleteUser = function (id) {
    if (confirm('You are about to permanently delete "'+$('#user'+id).text()+'"')) {
        $.post("KWALBUM_URL/~ajaxAdmin/DeleteUser", {userid:id},function(){$('#row'+id).hide();});
        $('#user'+id).text('deleting...');
    }
}
