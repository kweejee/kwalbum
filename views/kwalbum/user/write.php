<?php
/**
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2014 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Sep 28, 2009
 */
    $hide_level_options = '';
    foreach (Model_Kwalbum_Item::$hide_level_names as $level => $name) {
        if ($user->permission_level >= $level) {
            $hide_level_options .= "<option value='{$level}'>{$name}</option>";
        }
    }
?>
<div class="box">
<?php
if ( ! empty($message))
	echo $message.'<br/>';
echo $kwalbum_url_params;
?>
<form action="<?php echo $kwalbum_url.($kwalbum_url_params ? '/'.$kwalbum_url_params : '')?>/~user/write" method="post" autocomplete="off">
		<table>
		<tr>
			<td colspan="2">
				<b>What are we writing about today?</b>
			</td>
			<td>Visibility: <select name='vis' id='vis'>
                <?=$hide_level_options?>
			</td>
		</tr>
		<tr>
			<td>
				<label for="loc">Location:</label>
				<input type="text" class="text" name="loc" id="loc" value="<?php echo $location?>" size="15" />
			</td>
			<td>
				<label for="tags">Tags:</label>
				<input type="text" class="text" name="tags" id="tags" value="<?php echo $tags?>" size="20" />
			</td>
			<td>
				<label for="date">Date of entry:</label>
				<input type="text" class="text" name="date" id="date" value="<?php echo $date ?>" size="10" />
				<input type="text" class="text" name="time" id="time" value="<?php echo $time ?>" size="3" />
			</td>
		</tr>
		<tr style="vertical-align:top">
		<td colspan="2">
			<textarea cols="60" rows="20" name="description" id="description"><?php echo (isset($description) ? $description : null)?></textarea>
		</td>
		<td>
			<select name="group_option" id="group_option">
				<option value="new" selected>Add to new update group</option>
				<option value="existing"<?php echo ($same_group ? ' selected' : null)?>>Add to last group</option>
			</select>
		</td>
	</tr>
		</table>			<br/>
			<input type="submit" name="act" value="Save Entry" />
</form>
</div>