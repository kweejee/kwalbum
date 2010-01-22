<div class="box">
	<big><b><?php echo html::anchor($kwalbum_url.'/~admin', 'Admin Options'); ?>: Editing User Accounts</b></big>

<table border="1">
	<tr><th style="width:255px;">Displayed Name</th><th>Login Name</th><th>Email</th><th>Last Visit</th><th style="width:250px;">Permission</th><th>Delete?</th></tr>
<?php
$users = Model_Kwalbum_User::getAllArray();

foreach ($users as $u)
{
	echo "	<tr id='row{$u->id}'><td><span id='user{$user->id}'>{$u->name}</span></td><td>{$u->login_name}</td>"
		."<td>{$u->email}</td><td>{$u->visit_date}</td><td><span id='perm{$user->id}'>$u->permission_description</span></td>"
		."<td style='text-align:center'>".($u->id > 2 ? "<a href='#' onClick='deleteUser({$u->id});return false;'>[X]</a>" : "&nbsp;")."</td></tr>";
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
foreach ($users as $u)
{
	if ($u->id > 1 and $u->id != $user->id)
	{
?>
	$('#perm<?php echo $u->id; ?>').editable('<?php echo $kwalbum_url; ?>/~ajaxAdmin/EditUserPermission',{
		type:"select",tooltip:"Click to edit...",indicator:"Saving...",
		onblur:"submit",submitdata:{userid:<?php echo $u->id; ?>},
		loadurl:'<?php echo $kwalbum_url; ?>/~ajaxAdmin/GetUserPermission?userid=<?php echo $u->id; ?>'
	});
<?php
	}
}
?>
</script>