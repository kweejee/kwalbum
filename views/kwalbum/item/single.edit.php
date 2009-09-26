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

echo 'item '.$item_index.' of '.$total_items;
echo ' - ';
echo html::anchor($kwalbum_url.'/'
	.$kwalbum_url_params
	.($page_number > 1 ? 'page/'.$page_number.'/' : null),
	'back to browsing');
?>
<div class="box">
	<?php
		$resizedview = new View('kwalbum/item/resized');
		$resizedview->item = $item;
		echo $resizedview->render();
	?>
</div>

<div class="box box-right">
	<div class="box box-thumbnail">
	Previous Item:<br/>
	<?php
	if ($previous_item->id)
		echo Kwalbum_Helper::getThumbnailLink($previous_item, $kwalbum_url, $kwalbum_url_params);
	else
		echo 'Viewing First Item';
	?>
	</div>
	<div class="box box-thumbnail">
	Next Item:<br/>
	<?php

	if ($next_item->id)
		echo Kwalbum_Helper::getThumbnailLink($next_item, $kwalbum_url, $kwalbum_url_params);
	else
		echo 'Viewing Last Item';
	?>
	</div>
</div>

<div class="box box-right">
	<?php
		echo "<strong>Item #</strong><span id='item_id'>$item->id</span>";
		echo '<br/><strong id="location_label">Location:</strong> ';
		echo '<span id="location">'.$item->location.'</span>';

		echo '<br/><strong id="date_label">Date &amp; Time:</strong> ';
		echo '<span id="date">'.$item->visible_date.'</span>';

		echo "<br/><strong id='description_label'>Description:</strong>
			<div id='description'>$item->description</div>";

		echo '<strong id="tags_label">Tags:</strong> ';
		echo '<span id="tags">';
		$tags = $item->tags;
		echo implode(',', $tags);
		echo '</span>';

		echo '<br/><strong id="persons_label">People:</strong> ';
		echo '<span id="persons">';
		$persons = $item->persons;
		echo implode(',', $persons);
		echo '</span>';

	echo html::style('kwalbum/media/ajax/jqueryautocomplete/jquery.autocomplete.min.css')
		.html::script('kwalbum/media/ajax/jqueryautocomplete/jquery.autocomplete.pack.js')
		.html::script('kwalbum/media/ajax/jquery.jeditable.mini.js')
		.html::script('kwalbum/media/ajax/edit.js');

	?>
</div>