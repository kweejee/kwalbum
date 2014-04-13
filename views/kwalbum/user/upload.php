<?php
/**
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2014 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Aug 24, 2009
 */

    $hide_level_options = '';
    foreach (Model_Kwalbum_Item::$hide_level_names as $level => $name) {
        if ($user->permission_level >= $level) {
            $hide_level_options .= "<option value='{$level}'>{$name}</option>";
        }
    }
?>
<div class="box">
<form id="upload_form"
      action="<?php echo $kwalbum_url.'/'.$kwalbum_url_params ?>~user/upload"
      method="post"
      enctype="multipart/form-data"
      autocomplete="off">
<input type='hidden' name='overLimit' value='no'/>
	<table>
	<tr>
		<td colspan='2' class="inputs">
			<b>Information for all pictures being uploaded</b>
		</td>
		<td class="inputs">Visibility: <select name='vis' id='vis'>
            <?=$hide_level_options?>
		</td>
	</tr>
	<tr>
		<td class="inputs">
			<label>Location:</label>
			<input type="text" class="text" name="loc" id="loc" value="<?php echo $location?>" size="15" />
		</td>
		<td class="inputs">
			<label>Tags:</label>
			<input type="text" class="text" name="tags" id="tags" value="<?php echo $tags?>" size="20" />
		</td>
		<td class="inputs">
			Date if not on picture:
			<input type="text" class="text" name="date" id="date" value="<?php echo $date ?>" size="10" />
			<input type="text" class="text" name="time" id="time" value="<?php echo $time ?>" size="3" />
		</td>
	</tr>

	<tr>
		<td colspan="2" class="inputs">
			<small>Allowed filetypes are jpg, gif, and png<?php /*, wmv,
			<a href="http://filext.com/file-extension/divx" target='_blank'>divx</a>, mpeg,
			<a href="http://filext.com/file-extension/mp4" target='_blank'>mp4</a>,txt, html,
			<a href="http://filext.com/file-extension/gpx" target='_blank'>gpx</a>, xml, zip, mp3,
			wav, <a href="http://filext.com/file-extension/odt" target='_blank'>odt</a>, ods,
			<a href="http://filext.com/file-extension/ogg">ogg</a>, doc, and <a href="http://filext.com/file-extension/flv">flv</a>*/?>
			. Files larger than <?php echo floor(ini_get('memory_limit') / 8); ?>M may not upload correctly.</small>
		</td>
		<td class="inputs">
			<select name="group_option" id="group_option">
				<option value="new" selected>Upload to new update group</option>
				<option value="existing">Upload to last group</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="file" name="files[]" id="files" />
			<a href="javascript:kwalbum_upload();">Upload Files</a>
			| <a href="<?php echo $kwalbum_url ?>/~user/write">Ignore Queue and Write Text Item</a>
		</td>
		<td rowspan="2" style="vertical-align:top;" class="inputs">
			<input type="checkbox" name="import_caption" id="import_caption">Copy IPTC Caption to Description</input><br/>
			<input type="checkbox" name="import_keywords" id="import_keywords">Copy IPTC Keywords to Tags</input>
		</td>
	</tr>
	</table>
</form>
</div>