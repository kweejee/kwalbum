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
//	$('#perm<?php echo $u->id; ?>').editable('<?php echo $kwalbum_url; ?>/~ajaxAdmin/EditUserPermission',{
//		type:"select",tooltip:"Click to edit...",indicator:"Saving...",
//		onblur:"submit",submitdata:{userid:<?php echo $u->id; ?>},
//		loadurl:'<?php echo $kwalbum_url; ?>/~ajaxAdmin/GetUserPermission?userid=<?php echo $u->id; ?>'
//	});
var deleteUser = function (id) {
    if (confirm('You are about to permanently delete "'+$('#user'+id).text()+'"')) {
        $.post("KWALBUM_URL/~ajaxAdmin/DeleteUser", {userid:id},function(){$('#row'+id).hide();});
        $('#user'+id).text('deleting...');
    }
}
