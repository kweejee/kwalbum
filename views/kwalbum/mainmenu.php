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
<div class='kwalbumMenu'>
	<?php
	echo html::anchor($kwalbum_url, 'main page').' - ';
	echo html::anchor($kwalbum_url.'/~map', 'map').' - ';
	echo html::anchor($kwalbum_url.'/~user/list', 'contributors').' - ';
	if ($user->can_edit)
	{
		echo html::anchor($kwalbum_url.'/~user/upload', 'upload').' - ';
		echo html::anchor($kwalbum_url.'/~user/edit', 'edit').' - ';
	}
	if ($user->is_admin)
	{
		echo html::anchor($kwalbum_url.'/~admin', 'admin').' - ';
	}
	if ($user->is_logged_in)
	{
		echo html::anchor($kwalbum_url.'/~user/logout', 'logout');
	}
	else
	{
		echo html::anchor($kwalbum_url.'/~user/login', 'login');
	}
	?>
</div>