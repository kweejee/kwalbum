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
	echo form::label('openid', 'OpenId for Administrator');
	echo form::input('openid', ($form['openid']));
	echo (empty($errors['openid'])) ? '' : $errors['openid'];
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