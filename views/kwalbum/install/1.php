<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jun 30, 2009
 * @package kwalbum
 * @since 3.0 Jun 30, 2009
 */

?>
<div class="box">
	<h2>setup stage 1</h2>
<?php
	echo (empty($errors['db'])) ? '' : '<div class="errors">'.$errors['db'].'</div>';

	echo form::open();
	echo form::label('login_name', 'Login Name for main account (administrator)');
	echo form::input('login_name', ($form['login_name']));
	echo (empty($errors['login_name'])) ? '' : $errors['login_name'];
	echo '<br/>';

	echo form::label('password', 'Password');
	echo form::input('password', ($form['password']));
	echo (empty($errors['password'])) ? '' : $errors['password'];
	echo '<br/>';

	echo form::label('email', 'Email Address in case of lost password');
	echo form::input('email', ($form['login_name']));
	echo (empty($errors['email'])) ? '' : $errors['email'];
	echo '<br/>';

	echo form::label('name', 'Name to Display');
	echo form::input('name', ($form['name']));
	echo $_user['minNameLength'].' to '.$_user['maxNameLength'].' characters long';
	echo (empty($errors['name'])) ? '' : $errors['name'];
	echo '<br/>';

	echo form::submit('submit', 'Add Kwalbum to Database');
	echo '<br />';
	echo form::close();
?>
</div>