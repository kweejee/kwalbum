<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jun 30, 2009
 * @package kwalbum
 * @since 3.0 Jun 30, 2009
 */

?>
<div class="box">
	<?php
		if ($item->type != 'description only')
		{
			$resizedview = new View('kwalbum/item/resized');
			$resizedview->item = $item;
			echo $resizedview->render();
			if ($item->type == 'jpeg') {
				echo '<span id="kwalbumRotateOptions" style="float:right" title="rotates thumbnail and resized only">rotate '
					.'<a href="javascript:void(rotate(90))">90&deg;</a> '
					.'<a href="javascript:void(rotate(180))">180&deg;</a> '
					.'<a href="javascript:void(rotate(270))">270&deg;</a>'
					.'</span>';
			}
		}
		else
			echo "<div id='large_description'>$item->description</div>";
	?>
</div>

<div class="box-right">
	<div class="box">
<?php
echo ' <span class="kwalbumPageNumbers">(item '.$item_index.' of '.$total_items.') - '
	.HTML::anchor($kwalbum_url.'/'
	.($kwalbum_url_params ? $kwalbum_url_params : null)
	.($page_number > 1 ? 'page/'.$page_number.'/' : null)
	.(($kwalbum_url_params or $page_number > 1) ? null : '~browse/'),
	'back to browsing').'</span><br/>';
?>

	<?php
		echo "<script type='text/javascript'>var item_id=$item->id</script>";

		echo '<strong id="location_label">Location:</strong> ';
		echo '<span id="location">'.$item->location.'</span>';

		echo '<br/><strong id="date_label">Date:</strong> ';
		echo '<span id="date">'.$item->date.'</span>';

		echo '<br/><strong id="time_label">Time:</strong> ';
		echo '<span id="time">'.$item->time.'</span>';

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

		echo '<br/><strong id="visibility_label">Visibility:</strong> ';
		echo '<span id="visibility">'.Model_Kwalbum_Item::$hide_level_names[$item->hide_level].'</span>';

		echo '<br/><strong id="sortdate_label">Sorting Datetime:</strong> ';
		echo '<span id="sortdate">'.$item->sort_date.'</span>';

		echo '<br/><span id="delete"><input type="button" id="delete_button" value="Delete Item"/></span><br/><br/>';

	echo HTML::script($kwalbum_url.'/media/ajax/jquery.jeditable.mini.js')
		.HTML::script($kwalbum_url.'/media/ajax/edit.js');

	?>
	<strong>Views:</strong> <?php echo $item->count; ?><br/>

	<div>
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

</div></div>

<div class="box box-comments">
<a name='comments'><b>Comments:</b></a><br/>
<?php
foreach ($item->comments as $comment)
{
	echo $comment->name.' : '.$comment->date.' : <b>'.$comment->ip.'</b><br/>'.$comment->text.'<hr/>';
}
?>
</div>
