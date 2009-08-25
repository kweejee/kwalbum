<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Aug 24, 2009
 */

	echo html::script('kwalbum/media/ajax/jquery.js');
	echo html::script('kwalbum/media/ajax/jqueryautocomplete/jquery.autocomplete.js');
	//echo html::script('kwalbum/media/ajax/jqueryautocomplete/lib/jquery.bgiframe.min.js');
	//echo html::script('kwalbum/media/ajax/jqueryautocomplete/lib/jquery.ajaxQueue.js');
	//echo html::script('kwalbum/media/ajax/jqueryautocomplete/lib/jquery.thickbox-compressed.js');
	echo html::script('kwalbum/media/ajax/upload.js');
?>
<form action="<?php echo URL ?>~user/upload" method="post" enctype="multipart/form-data" autocomplete="off">
<input type='hidden' name='overLimit' value='no'/>
	<p>
		<table>
		<tr>
		<td colspan='2'><b>Information for all pictures</b></td>
		<td>Visibility: <select name='hidden'>
			<option value='0'>Public</option>
			<option value='1'<?=(1==$hidden?' selected':null)?>>Members Only</option>
			<option value='2'<?=(2==$hidden?' selected':null)?>>Privileged Only</option>
			<?=($user_is_admin ? "<option value='3'".(3==$hidden?' selected':null).">Admin Only</option>" : null)?></select>
		</td>
		</tr>
		<tr>
		<td>
			<label>Location:</label>
			<input type="text" class="text" name="loc" id="loc" value="<?=$location?>" size="15" />
		</td>
		<td>
			<label>Tags:</label>
			<input type="text" class="text" name="tags" id="tags" value="<?=$tags?>" size="20" />
		</td>
		<td>Date if not found on picture: <input type="text" class="text" name="date" id="date" value="<?=$date?>" size="8" /></td>
	</tr></table>
	</p>
	<p>
		<b>Files to upload</b> <small>(Files larger than <?=ini_get('upload_max_filesize')?> can not be uploaded and the total upload amount can not be above <?=ini_get('post_max_size')?>)</small><br/>
		<small>Allowed filetypes are jpg, gif, and png<?php /*, wmv,
		<a href="http://filext.com/file-extension/divx" target='_blank'>divx</a>, mpeg,
		<a href="http://filext.com/file-extension/mp4" target='_blank'>mp4</a>,txt, html,
		<a href="http://filext.com/file-extension/gpx" target='_blank'>gpx</a>, xml, zip, mp3,
		wav, <a href="http://filext.com/file-extension/odt" target='_blank'>odt</a>, ods,
		<a href="http://filext.com/file-extension/ogg">ogg</a>, doc, and <a href="http://filext.com/file-extension/flv">flv</a>*/?></small><br/>
		<span><text>1 </text><input size="50" name="0" type="file"><br></span>
		<span><text>2 </text><input size="50" name="1" type="file"><br></span>
		<span><text>3 </text><input size="50" name="2" type="file"><br></span>
		<span><text>4 </text><input size="50" name="3" type="file"><br></span>
		<span><text>5 </text><input size="50" name="4" type="file"><br></span>
		<span id='add_row_span'></span>
	</p>
	<input type="submit" name="act" value="Upload" />
</form>
