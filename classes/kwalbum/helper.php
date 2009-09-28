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
     * @return properly formatted string that can be inserted into a database
     * @since 2.0
     */
    public static function replaceBadDate($date)
    {
		$new = explode(' ', $date);
		$newDate = $new[0];
		$newTime = $new[1];
		$new = explode('-', $newDate);
		$newYear = (int)$new[0];
		$newMonth = (int)$new[1];
		$newDay = (int)$new[2];
		$new = explode(':', $newTime);
		$newHour = (int)$new[0];
		$newMinute = (int)$new[1];
		$newSecond = (int)$new[2];
		$newNewDate = "$newYear-$newMonth-$newDay";
		$newNewTime = "$newHour:$newMinute:$newSecond";

		if ('0-0-0' == $newNewDate and '' != $newDate and '0000-00-00' != $newDate)
			$badDate = true;
		else
		{
			$newDate = $newNewDate;
			if ('0:0:0' == $newNewTime and '' != $newTime and '00:00:00' != $newTime)
				$badDate = true;
			else
			{
				$badDate = false;
				$newTime = $newNewTime;
			}
		}
//        if (empty ($date) or ($time = @ strtotime($date)) < 1)
//            $date = '0000-00-00 00:00:00';
//        else
//            $date = date('Y-m-d H:i:s', $time);
        return $newDate.' '.$newTime;
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
    	$description = strip_tags($item->description,'<br><br/>');
		if ($item->type == 'jpeg' or $item->type == 'gif' or $item->type == 'png')
		{
			$link_text = "<img src='$kwalbum_url/~$item->id/~item/thumbnail' title='$item->filename'/>";
			$description = substr($description, 0, 50)
				.(strlen($item->description) > 50 ? '...' : null);
		}
		else if ($item->type == 'description only')
		{
			$link_text = substr($description, 0, 200)
				.(strlen($item->description) > 200 ? '...' : null);
			$description = '';
		}

		return html::anchor($kwalbum_url.'/~'.$item->id.'/'.$kwalbum_url_params, $link_text)
			."<br/>\n"
			.$description;

    }
}
