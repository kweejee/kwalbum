<div class="box">
	<big><b><?php echo html::anchor($kwalbum_url.'/~admin', 'Admin Options'); ?>: Editing User Accounts</b></big>

<table border="1" cellspacing="0">
	<tr>
        <th style="width:255px;">Displayed Name</th>
        <th>Login Name</th>
        <th>Email</th>
        <th>Last Visit</th>
        <th style="width:250px;">Permission</th>
        <th>Delete?</th>
    </tr>
<?php
$users = Model_Kwalbum_User::getAllArray();

foreach ($users as $u) {
    $delete_link = $u->id > 2 ? "<a href='#' onClick='deleteUser({$u->id});return false;'>[X]</a>" : "&nbsp;";
    $permission_class = 'kwalbumPermission';
    if ($u->id == $user->id or $u->id <= 2) {
        $permission_class = 'kwalbumPermissionFixed';
    }
	echo <<<ROW
    <tr id='row{$u->id}'>
        <td><span id='user{$user->id}'>{$u->name}</span></td>
        <td>{$u->login_name}</td>
        <td>{$u->email}</td>
        <td>{$u->visit_date}</td>
        <td>
            <span class="{$permission_class}" id="kwalbumPermission_{$u->id}">{$u->permission_description}</span>
        </td>
        <td style='text-align:center'>{$delete_link}</td>
    </tr>
ROW;
}
echo "</table></div>";

echo html::script($kwalbum_url.'/media/ajax/jquery.jeditable.mini.js')
    .html::script($kwalbum_url.'/media/ajax/admin/users.js');
?>
