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
		if (isset($new[0]))
			$newDate = $new[0];
		else
			$newDate = '';
		if (isset($new[1]))
			$newTime = $new[1];
		else
			$newTime = '';
		if ($newDate)
		{
			$new = explode('-', $newDate);
			if (count($new) != 3)
				$new = explode(':', $newDate);

			if (isset($new[0]))
				$newYear = (int)$new[0];
			else
				$newYear = 0;
			if (isset($new[1]))
				$newMonth = (int)$new[1];
			else
				$newMonth = 0;
			if (isset($new[2]))
				$newDay = (int)$new[2];
			else
				$newDay = 0;
			$newNewDate = "$newYear-$newMonth-$newDay";
		}
		else
			$newNewDate = '0-0-0';
		if ($newTime)
		{
			$new = explode(':', $newTime);
			if (isset($new[0]))
				$newHour = (int)$new[0];
			else
				$newHour = 0;
			if (isset($new[1]))
				$newMinute = (int)$new[1];
			else
				$newMinute = 0;
			if (isset($new[2]))
				$newSecond = (int)$new[2];
			else
				$newSecond = 0;
			$newNewTime = "$newHour:$newMinute:$newSecond";
		}
		else
			$newNewTime = '0:0:0';

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
    public static function getThumbnailLink($item, $kwalbum_url, $kwalbum_url_params = '')
    {
    	$cleaned_description = strip_tags($item->description,'<br><br/>');

		if ($item->type == 'jpeg' or $item->type == 'gif' or $item->type == 'png')
		{
			$item->type = ($item->type == 'jpeg' ? 'jpg' : $item->type);
			$link_text = "<img src='$kwalbum_url/~$item->id/~item/thumbnail.$item->type' title='$item->filename'/>";
			$description = '';
			if (strlen($cleaned_description) > 30)
				$description .= '<div class="box-thumbnail-description">';
			$description .= substr($cleaned_description, 0, 50)
				.(strlen($cleaned_description) > 50 ? '...' : null);
			if (strlen($cleaned_description) > 30)
				$description .= '</div>';
		}
		else if ($item->type == 'description only')
		{
			$link_text = '<div class="box-thumbnail-description">'.substr($cleaned_description, 0, 200)
				.(strlen($cleaned_description) > 200 ? '...' : null).'</div>';
			$description = '';
		}
		else
		{
			$link_text = 'Unknown Filetype';
		}

		return html::anchor($kwalbum_url.'/~'.$item->id.'/'.$kwalbum_url_params, $link_text)
			."<br/>\n"
			.$description
			.($item->has_comments ? '<span class="kwalbumHasComments">*has comments</span>' : null);
    }

    static public function getRandomHash()
    {
		$token = '';
		$length = mt_rand(50,100);
		for ($i = 0; $i < $length; $i++)
			$token = chr(mt_rand(0,122));
		return sha1($token);
    }
}
