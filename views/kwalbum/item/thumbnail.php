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
$classes = 'kwalbumBox kwalbumThumbnailBox';
if ($in_edit_mode) {
    $classes .= ' kwalbumEditMode';
}
?>
<div class="<?=$classes?>" id="kwalbum_thumbnail_box_<?=$item->id ?>">

<?php
if ($in_edit_mode) {
    echo "<label class='kwalbumMassInclude'>
        <input type='checkbox' name='kwalbum_mass_check[]' value='{$item->id}' "
        .($user->can_edit_item($item) ? '' : 'disabled ')." />
        Include In Update
    </label>
    <br/>Location: {$item->location}"
    .'<br/>Tags: '.implode(', ', $item->tags)
    .'<br/>People: '.implode(', ', $item->persons).'<br/>';
}
echo $item->pretty_date.'<br/>';
// show thumbnail based on file type
echo Kwalbum_Helper::getThumbnailLink($item, $kwalbum_url, $kwalbum_url_params);
?>
</div>
