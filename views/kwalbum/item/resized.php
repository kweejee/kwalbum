<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Sep 1, 2009
 */
if ($item->hide_level == 100)
{
	echo "<img src='$kwalbum_url/~$item->id/~item/resized' title='$item->filename'/>";
}
else if ($item->type == 'jpeg' or $item->type == 'gif' or $item->type == 'png')
{
	echo html::anchor($kwalbum_url.'/~'.$item->id.'/~item/original',
		"<img src='$kwalbum_url/~$item->id/~item/resized' title='$item->filename'/>")
		.'<br/>'.html::anchor($kwalbum_url.'/~'.$item->id.'/~item/download', 'download');
}
else if ($item->type == 'description only')
{
	echo "<div id='large_description'>$item->description</div>";
}