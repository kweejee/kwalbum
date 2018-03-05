<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jun 30, 2009
 * @package kwalbum
 * @since 3.0 Jun 30, 2009
 */
//var_export($errors);
?>
<div class="box">
	<h2>setup stage 1</h2>
<?php
	echo (empty($errors['db'])) ? '' : '<div class="errors">'.$errors['db'].'</div>';

	echo Form::open($kwalbum_url.'/~install');

	echo Form::label('login_name', 'Login Name for main account (administrator)');
	echo Form::input('login_name', ($form['login_name']));
	echo (empty($errors['login_name'])) ? '' : '<div class="errors">'.$errors['login_name'].'</div>';
	echo '<br/>';

	echo Form::label('name', 'Name to Display on comments');
	echo Form::input('name', ($form['name']));
	echo (empty($errors['name'])) ? '' : '<div class="errors">'.$errors['name'].'</div>';
	echo '<br/>';

	echo Form::label('password', 'Password');
	echo Form::password('password', ($form['password']));
	echo (empty($errors['password'])) ? '' : '<div class="errors">'.$errors['password'].'</div>';
	echo '<br/>';

	echo Form::label('email', 'Email Address in case of lost password');
	echo Form::input('email', ($form['email']));
	echo (empty($errors['email'])) ? '' : '<div class="errors">'.$errors['email'].'</div>';
	echo '<br/>';

	echo Form::submit('submit', 'Add Kwalbum to Database');
	echo '<br />';
	echo Form::close();
?>
</div>
