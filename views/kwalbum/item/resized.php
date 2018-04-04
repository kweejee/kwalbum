<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Sep 1, 2009
 */

if ($item->type == 'jpeg' or $item->type == 'gif' or $item->type == 'png')
{
	echo HTML::anchor(
        $item->getItemURL($kwalbum_url),
		"<img src='{$item->getResizedURL($kwalbum_url)}'"
        ." title='{$item->filename}'"
        ." alt='{$item->filename}'"
        ." class='kwalbumResizeImg' />"
    )
		.'<br/>'.HTML::anchor($item->getDownloadURL($kwalbum_url), 'download');
}
else if ($item->type == 'description only')
{
	echo "<div id='large_description'>$item->description</div>";
}