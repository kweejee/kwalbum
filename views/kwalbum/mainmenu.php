<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kohana
 * @since Jul 21, 2009
 */

?>
<p>
	<?php
	echo html::anchor(URL, 'main page').' - ';
	echo html::anchor(URL.'~map', 'map').' - ';
	echo html::anchor(URL.'~user/list', 'contributors').' - ';
	if ($user->can_edit)
	{
		echo html::anchor(URL.'~user/upload', 'upload').' - ';
		echo html::anchor(URL.'~user/edit', 'edit').' - ';
	}
	if ($user->is_admin)
	{
		echo html::anchor(URL.'~admin', 'admin').' - ';
	}
	if ($user->is_logged_in)
	{
		echo html::anchor(URL.'~user/logout', 'logout');
	}
	else
	{
		echo html::anchor(URL.'~user/login', 'login');
	}
	?>
</p>