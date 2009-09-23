<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Aug 2, 2009
 */
?>
<div class='box'>
<?php
	if ($success)
	{
		echo 'You are logged in.';
		return;
	}
	echo $error;
?>
<fieldset>
<legend>Logging In</legend>
<form method="post" action="<?php echo $kwalbum_url?>/~user/login">
<table border="0">
<tr>
 <td>Login Name:</td>
 <td><input type="text" name="name" size="15"/></td>
</tr>
<tr>
 <td>Password:</td>
 <td><input type="password" name="password" size="15"/></td>
</tr>
<tr>
 <td>Save Cookie For:</td>
 <td><select name="length">
  <option value="session">current session</option>
  <option value="86400">1 day</option>
  <option value="604800">1 week</option>
  <option value="31536000" selected="selected">1 year</option>
 </select></td>
</tr>
<tr>
 <td colspan="2"><input type="submit" name="act" value="Login"></td>
</tr>
</table>
</form>
</div>