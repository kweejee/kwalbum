<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kohana
 * @since Jul 21, 2009
 */

?>
<div class='kwalbumMenu'>
	<?php
	echo html::anchor($kwalbum_url, 'main page')
		.' - '.html::anchor($kwalbum_url.'/~map', 'map')
		.' | ';
	if ($user->is_logged_in)
	{
		if ($user->can_edit)
		{
			if ($user->can_add)
				echo html::anchor($kwalbum_url.'/~user/upload', 'upload').' - ';
			echo '<a href="#" id="kwalbumEditToggle'.($in_edit_mode ? 'View">view' : 'Edit">edit').'</a> - ';
		}
		if ($user->is_admin)
		{
			echo html::anchor($kwalbum_url.'/~admin', 'admin').' - ';
		}
		echo html::anchor($kwalbum_url.'/~user/logout', 'logout');

		if ($in_edit_mode)
			echo '&nbsp;&nbsp;&nbsp;<strong>!!! In Edit Mode !!!</strong>&nbsp;&nbsp;&nbsp;';
	}
	else
	{
		echo html::anchor($kwalbum_url.'/~user/login', 'login')
			.' - '.html::anchor($kwalbum_url.'/~user/register', 'register');
	}
	?>
</div>