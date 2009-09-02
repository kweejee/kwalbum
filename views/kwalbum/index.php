<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jun 30, 2009
 * @package kwalbum
 * @since 3.0 Jun 30, 2009
 */

?>
<p>
From here you can <?php echo html::anchor($kwalbum_url.'/~browse', 'browse all the pictures')?> or search below to find something specific.
</p>

<p>
<form method='get' action='<?php echo $kwalbum_url;?>'>
<table><tr>
<td>
Location:<br/>
<select name="location" size="3">
<?php
$locations = Model_Kwalbum_Location::getNameArray();
foreach ($locations as $name)
	echo "<option value='$name'>$name</option>";
?>
</td>
<td>
Tags:<br/>
<select name="tags" multiple size="3">
<?php
$tags = Model_Kwalbum_Tag::getNameArray();
foreach ($tags as $name)
	echo "<option value='$name'>$name</option>";
?>
</select>
</td>
</tr></table>
<br/>
<input type='submit' value='Search'/>
</form>
</p>