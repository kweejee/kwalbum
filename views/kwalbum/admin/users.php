<div class="box">
	<big><b><?php echo html::anchor($kwalbum_url.'/~admin', 'Admin Options'); ?>: Editing User Accounts</b></big>

<table border="1">
	<tr><th style="width:255px;">Displayed Name</th><th>Login Name</th><th>Email</th><th>Last Visit</th><th style="width:250px;">Permission</th><th>Delete?</th></tr>
<?php
$users = Model_Kwalbum_User::getAllArray();

foreach ($users as $user)
{
	echo "	<tr id='row{$user->id}'><td><span id='user{$user->id}'>{$user->name}</span></td><td>{$user->login_name}</td>"
		."<td>{$user->email}</td><td>{$user->visit_date}</td><td><span id='perm{$user->id}'>$user->permission_description</span></td>"
		."<td style='text-align:center'>".($user->id > 2 ? "<a href='#' onClick='deleteUser({$user->id});return false;'>[X]</a>" : "&nbsp;")."</td></tr>";
}
echo "</table></div>";

echo html::script($kwalbum_url.'/media/ajax/jquery.jeditable.mini.js');
?>
<script type="text/javascript">
function deleteUser(id){
	if (confirm('You are about to permanently delete "'+$('#user'+id).text()+'"')){
			$.post("<?php echo $kwalbum_url; ?>/~ajaxAdmin/DeleteUser", {userid:id},function(){$('#row'+id).hide();});
			$('#user'+id).text('deleting...');
	}
}
<?php
foreach ($users as $user)
{
	if ($user->id > 1 and $user->id != $current_user->id)
	{
?>
	$('#perm<?php echo $user->id; ?>').editable('<?php echo $kwalbum_url; ?>/~ajaxAdmin/EditUserPermission',{
		type:"select",tooltip:"Click to edit...",indicator:"Saving...",
		onblur:"submit",submitdata:{userid:<?php echo $user->id; ?>},
		loadurl:'<?php echo $kwalbum_url; ?>/~ajaxAdmin/GetUserPermission?userid=<?php echo $user->id; ?>'
	});
<?php
	}
}
?>
</script>