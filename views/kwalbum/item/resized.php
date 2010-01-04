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

if ($item->type == 'jpeg' or $item->type == 'gif' or $item->type == 'png')
{
	$item->type = ($item->type == 'jpeg' ? 'jpg' : $item->type);
	echo html::anchor($kwalbum_url.'/~'.$item->id.'/~item/original.'.$item->filename,
		"<img src='$kwalbum_url/~$item->id/~item/resized.$item->filename' title='$item->filename' alt='$item->filename' />")
		.'<br/>'.html::anchor($kwalbum_url.'/~'.$item->id.'/~item/download.'.$item->filename, 'download');
}
else if ($item->type == 'description only')
{
	echo "<div id='large_description'>$item->description</div>";
}