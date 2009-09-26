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
}
