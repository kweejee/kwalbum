<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Sep 28, 2009
 */
?>
<div class="box">
<?php
if ( ! empty($message))
	echo $message.'<br/>';
?>
<form action="<?php echo $kwalbum_url.'/'.$kwalbum_url_params ?>/~user/write" method="post" autocomplete="off">
	<p>
		<table>
		<tr>
		<td colspan='2'><b>What are we writing about today?</b></td>
		<td>Visibility: <select name='vis' id='vis'>
			<option value='0'>Public</option>
			<option value='1'>Members Only</option>
			<option value='2'>Privileged Only</option>
			<option value='3'>Contributors Only</option>
			<?php echo ($user->is_admin ? "<option value='5'>Admin Only</option>" : null) ?></select>
		</td>
		</tr>
		<tr>
		<td>
			<label>Location:</label>
			<input type="text" class="text" name="loc" id="loc" value="<?php echo $location?>" size="15" />
		</td>
		<td>
			<label>Tags:</label>
			<input type="text" class="text" name="tags" id="tags" value="<?php echo $tags?>" size="20" />
		</td>
		<td>Date of entry: <input type="text" class="text" name="date" id="date" value="<?php echo $date?>" size="15" /></td>
	</tr></table>
	</p>
	<p>
		<textarea cols="60" rows="20" name="description" id="description"><?php echo (isset($description) ? $description : null)?></textarea>
		<br/>
		<input type="submit" name="act" value="Save Entry" />
	</p>
</form>
</div>