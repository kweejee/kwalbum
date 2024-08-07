<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since 3.0 Jun 30, 2009
 */

?>
<div class="box">
<p>
From here you can <?php echo HTML::anchor($kwalbum_url.'/~browse', '<b>browse all the pictures</b>')?> or search below to find something specific.
</p>

<form method='get' action='<?php echo $kwalbum_url.'/~browse/';?>'>
	<table><tr>
	<td>
		Location:<br/>
		<select name="location" size="10" tabindex="1">
		<option value="" selected></option>
		<?php
		$locations = Model_Kwalbum_Location::get_name_array($user);
		foreach ($locations as $name)
			echo "<option value=\"$name\">$name</option>";
		?>
		</select>
	</td>
	<td>
		Tags:<br/>
		<select name="tags[]" multiple size="10" tabindex="2">
		<option value="" selected></option>
		<?php
		$tags = Model_Kwalbum_Tag::get_name_array();
		foreach ($tags as $name)
			echo "<option value=\"$name\">$name</option>";
		?>
		</select>
	</td>
	<td>
		People:<br/>
		<select name="people[]" multiple size="10" tabindex="3">
		<option value="" selected></option>
		<?php
		$people = Model_Kwalbum_Person::get_name_array();
		if ( ! $user->can_see_all)
		{
			$persons = array();
			foreach ($people as $name)
			{
				$length = strpos($name, ' ');
				if ($length > 0)
					$persons[] = substr($name, 0, $length);
				else
					$persons[] = $name;
			}
			$people = array_unique($persons);
		}

		foreach ($people as $name)
		{
			echo "<option value=\"$name\">$name</option>";
		}
		?>
		</select>
	</td>
	</tr></table>
    Start Date: <input type="date" name="date" tabindex="4" />
    End Date: <input type="date" name="date2" tabindex="5" />
	<br/>
	<input type='submit' value='Search' tabindex="6" />
</form>
</div>
