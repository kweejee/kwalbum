<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Oct 8, 2009
 */

?>
<div class='box'>

<fieldset>
<legend>Lost Password</legend>
<?php
if (isset($message))
{
	echo $message;
}

if ( ! isset($_GET['h']))
{
	?>
<form method="post" action="<?php echo $kwalbum_url?>/~user/resetpassword">
	<table border="0">
	<tr>
		<td>Login Name:</td>
		<td><input type="text" name="name" size="15"/></td>
	</tr>
	<tr>
		<td>Email Address:</td>
		<td><input type="text" name="email" size="15"/></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" name="act" value="Send Email With Instructions"></td>
	</tr>
	</table>
</form>
	<?php
}
else if ( ! isset($message))
{
	if (isset($message2))
		echo $message2;
	?>
	Enter your new password here.<br/>
<form method="post" action="<?php echo $kwalbum_url?>/~user/resetpassword/?h=<?php echo $user->reset_code.'.'.$user->id?>">
	New Password: <input type="password" name="pw" size="15"/> <input type="submit" name="act" value="Change It">
</form>
	<?php
}
?>
</fieldset>

</div>