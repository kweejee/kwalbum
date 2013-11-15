<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@gmail.com>
 * @copyright Copyright 2009-2013 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 */

// create page links
$page_links = '';
for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page_number) {
        $page_links .= "<span class='kwalbumCurrentIndex'>{$i}</span> ";
    } else {
        $page_links .= html::anchor(
            "{$kwalbum_url}/{$kwalbum_url_params}page/{$i}",
            $i
        ).' ';
    }
}
$page_links_div = "<div class='kwalbumPageNumbers'>pages: {$page_links}</div>";


// show page

if ($in_edit_mode) {
    echo html::script($kwalbum_url.'/media/ajax/mass_edit.js');
?>
<form method="post">
    <div id="kwalbumMassEditFields" class="kwalbumMassEditInputs kwalbumBox">
        <div class="kwalbumMassEditField">
            <label for="kwalbum_me_add_tags">Add Tags:</label>
            <input type="text" id="kwalbum_me_add_tags" name="tags_add" />
        </div>
        <div class="kwalbumMassEditField">
            <label for="kwalbum_me_add_people">Add People:</label>
            <input type="text" id="kwalbum_me_add_people" name="persons_add" />
        </div>
        <div class="kwalbumMassEditField">
            <label for="kwalbum_me_location">New Location:</label>
            <input type="text" id="kwalbum_me_location" name="loc" />
        </div>
        <div class="kwalbumMassEditField">
            <label for="kwalbum_me_rem_tags">Remove Tags:</label>
            <input type="text" id="kwalbum_me_rem_tags" name="tags_rem" />
        </div>
        <div class="kwalbumMassEditField">
            <label for="kwalbum_me_rem_people">Remove People:</label>
            <input type="text" id="kwalbum_me_rem_people" name="persons_rem" />
        </div>
        <div class="kwalbumMassEditField">
            <label for="kwalbum_me_rem_people">New Visibility:</label>
            <select name="vis" id="kwalbum_me_visibility">
                <option value=''>Unchanged</option>
                <option value='-1'>Public</option>
                <option value='1'>Members Only</option>
                <option value='2'>Privileged Only</option>
                <option value='3'>Contributors Only</option>
                <?php echo ($user->is_admin ? "<option value='5'>Admin Only</option>" : null) ?>
            </select>
        </div>
    </div>
    <div class="kwalbumMassEditInputs">
        <input type="submit" class="kwalbumMassEditSave" disabled="disabled" value="Save" />
    </div>
<?php
}

echo $page_links_div;

if (empty($items)) {
    echo '<div class="kwalbumBox"><h2>No items were found that match your search.</h2></div>';
} else {
    foreach ($items as $item) {
        $item->hide_if_needed($user);
        $thumbview = new View('kwalbum/item/thumbnail');
        $thumbview->item = $item;
        echo $thumbview->render();
    }
}

echo $page_links_div;

if ($in_edit_mode) {
?>
    <div class="kwalbumMassEditInputs">
        <input type="submit" class="kwalbumMassEditSave kwalbumMassEditInputs" disabled="disabled" value="Save" />
    </div>
</form>
<?php
}
