<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Sep 25, 2009
 */

class Kwalbum_Helper
{
    /**
     * Replace a date with the current time if it is not real.
     * @param string $date original 'yyyy-mm-dd hh:mm:ss' datetime
     * submitted by the user
     * @return string valid datetime that can be inserted into a
     * database
     * @since 2.0
     */
    public static function replaceBadDate($date)
    {

        if (empty ($date) or ($time = @ strtotime($date)) < 1)
            $date = '0000-00-00 00:00:00';
        else
            $date = date('Y-m-d H:i:s', $time);
        return $date;
    }

    /**
     * get "thumbnail" image/text link to item page
     * @param Model_Kwalbum_Item item to get information from
     * @param string root URL of kwalbum
     * @return string html
     * @since 3.0
     */
    public static function getThumbnailLink($item, $kwalbum_url, $kwalbum_url_params)
    {
		if ($item->type == 'jpeg' or $item->type == 'gif' or $item->type == 'png')
			$text = "<img src='$kwalbum_url/~$item->id/~item/thumbnail' title='$item->filename' width='100px'/>";
		else if ($item->type == 'description only')
			$text = "Item is only text.";

		return html::anchor($kwalbum_url.'/~'.$item->id.'/'.$kwalbum_url_params, $text);
    }
}
