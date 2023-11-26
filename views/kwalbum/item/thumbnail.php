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
    $tags = implode(', ', $item->get_tags());
    $persons = implode(', ', $item->get_persons());
    echo "<label class='kwalbumMassInclude'>
        <input type='checkbox' name='kwalbum_mass_check[]' value='{$item->id}' "
        .($user->can_edit_item($item) ? '' : 'disabled ')." />
        Include In Update
    </label>"
    .<<<INFO
    <dl class='kwalbumItemInfo'>
        <dt class='kwalbumItemLocationLabel'>Location</dt>
            <dd class='kwalbumItemLocation' title='{$item->location}'>{$item->location}</dd>
        <dt class='kwalbumItemTagsLabel'>Tags</dt>
            <dd class='kwalbumItemTags'>{$tags}</dd>
        <dt class='kwalbumItemPeopleLabel'>People</dt>
            <dd class='kwalbumItemPeople'>{$persons}</dd>
        <dt class='kwalbumItemVisibilityLabel'>Visibility</dt>
            <dd class='kwalbumItemVisibility_{$item->hide_level}'>{$item->hide_level_name}</dd>
    </dl>
INFO;
}
echo $item->pretty_date.'<br/>';
// show thumbnail based on file type
echo Kwalbum_Helper::getThumbnailLink($item, $kwalbum_url, $kwalbum_url_params);
?>
</div>
