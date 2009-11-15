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

echo ' <span class="kwalbumPageNumbers">(item '.$item_index.' of '.$total_items.') - '
	.html::anchor($kwalbum_url.'/'
	.($kwalbum_url_params ? $kwalbum_url_params : null)
	.($page_number > 1 ? 'page/'.$page_number.'/' : null)
	.(($kwalbum_url_params or $page_number > 1) ? null : '~browse/'),
	'back to browsing').'</span>';
?>
<div class="box">
	<?php
		if ($item->type != 'description only')
		{
			$resizedview = new View('kwalbum/item/resized');
			$resizedview->item = $item;
			echo $resizedview->render();
		}
		else
			echo "<div id='large_description'>$item->description</div>";
	?>
</div>

<div class="box-right">
<?php
echo ' <span class="kwalbumPageNumbers">(item '.$item_index.' of '.$total_items.') - '
	.html::anchor($kwalbum_url.'/'
	.($kwalbum_url_params ? $kwalbum_url_params : null)
	.($page_number > 1 ? 'page/'.$page_number.'/' : null)
	.(($kwalbum_url_params or $page_number > 1) ? null : '~browse/'),
	'back to browsing').'</span><br/>';
?>
<div class="box box-right">
	<?php
		echo "<script type='text/javascript'>var item_id=$item->id</script>";

		echo '<strong id="location_label">Location:</strong> ';
		echo '<span id="location">'.$item->location.'</span>';

		echo '<br/><strong id="date_label">Date &amp; Time:</strong> ';
		echo '<span id="date">'.$item->visible_date.'</span>';

		if ($item->type != 'description only')
			echo "<br/><strong id='description_label'>Description:</strong>
				<div id='description'>$item->description</div>";
		else
			echo "<br/>";

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

		$vis = array('Public', 'Members Only', 'Privileged Only', 'Contributors Only', '', 'Admin Only');
		echo '<br/><strong id="visibility_label">Visibility:</strong> ';
		echo '<span id="visibility">'.$vis[$item->hide_level].'</span>';

		echo '<br/><strong id="sortdate_label">Sorting Datetime:</strong> ';
		echo '<span id="sortdate">'.$item->sort_date.'</span>';

	echo html::style($kwalbum_url.'/media/ajax/jqueryautocomplete/jquery.autocomplete.min.css')
		.html::script($kwalbum_url.'/media/ajax/jqueryautocomplete/jquery.autocomplete.pack.js')
		.html::script($kwalbum_url.'/media/ajax/jquery.jeditable.mini.js')
		.html::script($kwalbum_url.'/media/ajax/edit.js');

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
</div>

<div class="box box-comments">
<a name='comments'><b>Comments:</b></a><br/>
<?php
foreach ($item->comments as $comment)
{
	echo $comment->name.' : '.$comment->date.' : <b>'.$comment->ip.'</b><br/>'.$comment->text.'<hr/>';
}
?>
</div>