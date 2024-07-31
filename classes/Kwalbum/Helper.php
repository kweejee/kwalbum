<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2018 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Sep 25, 2009
 */


use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;

class Kwalbum_Helper
{
    /** @var Bucket */
    static private $_google_bucket;

    /**
     * @return Bucket|false
     */
    public static function getGoogleBucket()
    {
        if (is_null(self::$_google_bucket)) {
            $google_project_id = Kwalbum_Model::get_config('google_cloud_project_id');
            $google_bucket_name = Kwalbum_Model::get_config('google_cloud_bucket_name');
            if ($google_project_id and $google_bucket_name) {
                # Instantiates a client
                $storage = new StorageClient([
                    'projectId' => $google_project_id
                ]);
                $bucket = $storage->bucket($google_bucket_name);
                self::$_google_bucket = $bucket;
            } else {
                self::$_google_bucket = false;
            }
        }
        return self::$_google_bucket;
    }

    /**
     * Replace a date with '0000-00-00 00:00:00' if it is not real.
     * @param string $datetime the questionable datetime submitted by the user
     * that may be good or bad
     * @return string properly formatted string that can be inserted into a database
     * @since 2.0
     */
    public static function replaceBadDate($datetime): string
    {
        $new = explode(' ', $datetime);
        if (isset($new[0]))
            $newDate = $new[0];
        else
            $newDate = '';
        if (isset($new[1]))
            $newTime = $new[1];
        else
            $newTime = '';
        if ($newDate) {
            $new = explode('-', $newDate);
            if (count($new) != 3)
                $new = explode(':', $newDate);

            if (isset($new[0]))
                $newYear = (int)$new[0];
            else
                $newYear = 0;
            if ($newYear < 1000)
                $newYear = 0;
            if (isset($new[1]))
                $newMonth = (int)$new[1];
            else
                $newMonth = 0;
            if ($newMonth < 0 || $newMonth > 12)
                $newMonth = 0;
            if (isset($new[2]))
                $newDay = (int)$new[2];
            else
                $newDay = 0;
            $newDate = (!$newYear ? '0000' : $newYear) . '-' . ($newMonth < 10 ? 0 : '') . $newMonth . '-' . ($newDay < 10 ? 0 : '') . $newDay;
        } else
            $newDate = '0000-00-00';
        if ($newTime) {
            $new = explode(':', $newTime);
            if (isset($new[0]))
                $newHour = (int)$new[0];
            else
                $newHour = 0;
            if ($newHour < 0 || $newHour > 23)
                $newHour = 0;
            if (isset($new[1]))
                $newMinute = (int)$new[1];
            else
                $newMinute = 0;
            if ($newMinute < 0 || $newMinute > 59)
                $newMinute = 0;
            if (isset($new[2]))
                $newSecond = (int)$new[2];
            else
                $newSecond = 0;
            if ($newSecond < 0 || $newSecond > 59)
                $newSecond = 0;
            $newTime = ($newHour < 10 ? 0 : '') . $newHour . ':' . ($newMinute < 10 ? 0 : '') . $newMinute . ':' . ($newSecond < 10 ? 0 : '') . $newSecond;
        } else
            $newTime = '00:00:00';

        return $newDate . ' ' . $newTime;
    }

    /**
     * get "thumbnail" image/text link to item page
     * @param Model_Kwalbum_Item $item to get information from
     * @param string $kwalbum_url root URL of kwalbum
     * @param string $kwalbum_url_params
     * @return string html
     * @since 3.0
     */
    public static function getThumbnailLink(Model_Kwalbum_Item $item, string $kwalbum_url, string $kwalbum_url_params = ''): string
    {
        $cleaned_description = strip_tags(str_replace(array('<br>', '<br/>'), ' ', $item->description));
        $description = '';
        $description_only = $item->type == 'description only' || $item->type == 'gpx' || $item->type == 'geojson';

        if ($item->type == 'jpeg' or
            $item->type == 'gif' or
            $item->type == 'png') {
            $link_text = "<img src='{$item->getThumbnailURL($kwalbum_url)}'/>";
        } elseif ($description_only) {
            $link_text = '<div class="kwalbumThumbnailDescriptionBox">' . substr($cleaned_description, 0, 200)
                . (strlen($cleaned_description) > 200 ? '...' : null) . '</div>';
        } else {
            $link_text = 'Unknown Filetype';
        }
        if (!$description_only) {
            $description .= substr($cleaned_description, 0, 50)
                . (strlen($cleaned_description) > 50 ? '...' : null);
            if (!$description) {
                $tags = implode(', ', $item->get_tags());
                if (strlen($tags > 53)) {
                    $tags = substr($tags, 0, 50) . '...';
                }
                $description = $tags;
            }
        }

        return HTML::anchor(
                "{$kwalbum_url}/~{$item->id}/{$kwalbum_url_params}",
                $link_text,
                array('class' => 'kwalbumThumbnailLink', 'id' => "kwalbumItem_{$item->id}")
            )
            . "<br/>\n"
            . ($description ? '<div class="kwalbumThumbnailDescriptionBox">' . $description . '</div>' : '')
            . ($item->has_comments ? '<span class="kwalbumHasComments">*has comments</span>' : null);
    }

    static public function getRandomHash()
    {
        $token = '';
        $length = mt_rand(50, 100);
        for ($i = 0; $i < $length; $i++)
            $token = chr(mt_rand(0, 122));
        return sha1($token);
    }
}
