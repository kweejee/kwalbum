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
		$resizedview = new View('kwalbum/item/resized');
		$resizedview->item = $item;
		echo $resizedview->render();
	?>
</div>

<div class="box box-right">
<?php
echo ' <span class="kwalbumPageNumbers">(item '.$item_index.' of '.$total_items.') - '
	.HTML::anchor($kwalbum_url.'/'
	.($kwalbum_url_params ? $kwalbum_url_params : null)
	.($page_number > 1 ? 'page/'.$page_number.'/' : null)
	.(($kwalbum_url_params or $page_number > 1) ? null : '~browse/'),
	'back to browsing').'</span>'
	.'<span class="kwalbumShareIcons"><a name="fb_share" type="button"></a></span>'
	.'<br/>';
?>

	<?=($item->location ? HTML::anchor($kwalbum_url.'/'.$item->location, $item->location).'<br />' : '') ?>
	<?php echo $item->pretty_date; ?>
	<hr/>
	<?php echo (
			($item->description and $item->type != 'description only')
			? $item->description.'<hr/>'
			: null
		); ?>

	<?php
		if (count($item->tags) > 0)
		{
			echo '<strong>Tags:</strong> ';
			$comma = false;
			foreach ($item->tags as $tag)
			{
				if ($comma)
					echo ', ';
				$comma = true;
				echo HTML::anchor($kwalbum_url.'/tags/'.$tag, $tag);
			}
			echo '<br/>';
		}
		if (count($item->persons) > 0)
		{
			echo '<strong>People:</strong> ';
			$comma = false;
			foreach ($item->persons as $person)
			{
				if ($comma)
					echo ', ';
				$comma = true;
				if ( ! $user->can_see_all and $length = strpos($person,' '))
				{
					$person = substr($person, 0, $length);
				}
				echo HTML::anchor($kwalbum_url.'/people/'.$person, $person);
			}
			echo '<br/>';
		}
	?>
	<strong>Views:</strong> <?php echo $item->count; ?><br/>

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

<script type="text/javascript">var item_id=<?php echo $item->id?></script>
<?php
echo HTML::script($kwalbum_url.'/media/ajax/comment.js');
?>
<div class="box box-comments">
<a name='comments'><b>Comments:</b></a><br/>
<?php
foreach ($item->comments as $comment)
{
	echo $comment->name.' : '.$comment->date.'<br/>'.$comment->text.'<hr/>';
}
if ($user->can_view_item($item))
{
	if ($user->is_logged_in)
	{
	?>

		<div id="new_comment">
			Add a comment.
			<form action="#">
			<textarea id="comment_text" cols="45" rows="10"></textarea><br/>
			<input type="button" id="comment_save" value="Add Your Comment" />
			</form>
		</div>
	<?php
	}
	else
	{
		echo HTML::anchor($kwalbum_url.'/~user/login', 'Log in to add a comment.');
	}
}
else
{
	echo 'no commenting allowed';
}
?>
</div>
