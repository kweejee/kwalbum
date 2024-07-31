<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 */

// create page links
$page_links = '';
$interval = ceil($total_pages / 20);
for ($i = 1; $i <= $total_pages; $i++) {
    if ($i == $page_number) {
        $page_links .= "<span class='kwalbumPageCurrent'>{$i}</span> ";
    } else {
        $class = '';
        if ($i == 1 or $i == $total_pages) {
            $class = 'kwalbumPageEnds';
        } elseif (abs($i - $page_number) <=5 ) {
            $class = 'kwalbumPageNear';
        } elseif (!($i % $interval)) {
            $class = 'kwalbumPageBetween';
        }
        if ($class) {
            $page_links .= HTML::anchor(
                "{$kwalbum_url}/{$kwalbum_url_params}page/{$i}",
                $i,
                array('class' => $class)
            ).' ';
        }
    }
}
$page_links_div = "<div class='kwalbumPageNumbers'><span class='kwalbumPageLabel'>pages</span>{$page_links}</div>";


// show page
?>
<div id="kwalbumResizePopup">
    Press Esc to <a href="javascript:kwalbum.hideResizePopup();">close</a> or use the arrow keys to move
    <a href="javascript:kwalbum.goToNextImage();">forward</a> and <a href="javascript:kwalbum.goToNextImage(true);">back</a>.
    <div id="kwalbumResizeBox">
        <div id="kwalbumResizeMessage">Loading...</div>
    </div>
</div>
<?php
if ($in_edit_mode) {
    echo HTML::script($kwalbum_url.'/media/ajax/mass_edit.js');

    $hide_level_options = '<option value="">Unchanged</option>'
        .'<option value="-1">Public</option>';
    foreach (Model_Kwalbum_Item::$hide_level_names as $level => $name) {
        if (!$level) {
            continue; // public was already added above
        }
        if ($user->permission_level >= $level) {
            $hide_level_options .= "<option value='{$level}'>{$name}</option>";
        }
    }
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
                <?=$hide_level_options?>
            </select>
        </div>
    </div>
    <div class="kwalbumMassEditInputs">
        <input type="submit" class="kwalbumMassEditSave" disabled="disabled" value="Save" />
        <div class="kwalbumMassEditCheckAlls">
            <input type="button" id="kwalbumMassEditCheckAll" value="Check All" />
            <input type="button" id="kwalbumMassEditUncheckAll" value="Uncheck All" />
        </div>
    </div>
<?php
} else { // in view mode
    echo HTML::script($kwalbum_url.'/media/ajax/view.js');

}

echo $page_links_div;
echo '<div class="kwalbumThumbnails">';
if (empty($items)) {
    echo '<div class="kwalbumThumbnailBox"><h2>No items were found that match your search.</h2></div>';
} else {
    foreach ($items as $item) {
        $item->hide_if_needed($user);
        $thumbview = new View('kwalbum/item/thumbnail');
        $thumbview->item = $item;
        echo $thumbview->render();
    }
}
echo '</div>';
echo $page_links_div;

if ($in_edit_mode) {
?>
    <div class="kwalbumMassEditInputs">
        <input type="submit" class="kwalbumMassEditSave kwalbumMassEditInputs" disabled="disabled" value="Save" />
    </div>
</form>
<?php
}
