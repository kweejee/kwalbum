<?php
/**
 *
 *
 * @author Tim Redmond
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kohana
 * @since Jul 21, 2009
 */

?>
<div class='kwalbumMenu'>
	<?php
//	echo HTML::anchor($kwalbum_url, 'main page')
//		.' - '.HTML::anchor($kwalbum_url.'/~map', 'map')
//		.' | ';
	if ($user->is_logged_in)
	{
		if ($user->can_edit)
		{
			if ($user->can_add)
				echo HTML::anchor($kwalbum_url.'/~user/upload', 'Upload').' - ';
			echo '<a href="#" id="kwalbumEditToggle'.($in_edit_mode ? 'View">View' : 'Edit">Edit').'</a> - ';
		}
		if ($user->is_admin)
		{
			echo HTML::anchor($kwalbum_url.'/~admin', 'Admin').' - ';
		}
		echo HTML::anchor($kwalbum_url.'/~user/logout', 'Logout');

		if ($in_edit_mode)
			echo '&nbsp;&nbsp;&nbsp;<strong>!!! In Edit Mode !!!</strong>&nbsp;&nbsp;&nbsp;';
	}
	else
	{
		echo HTML::anchor($kwalbum_url.'/~user/login', 'Login')
			.' - '.HTML::anchor($kwalbum_url.'/~user/register', 'Register');
	}
	?>
</div>
