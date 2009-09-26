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
	<?php echo $item->location; ?>
	<br/>
	<?php echo $item->pretty_date; ?>
	<hr/>
	<?php echo ($item->description ? $item->description.'<hr/>' : null); ?>

	<?php
		if (sizeof($item->tags) > 0)
		{
			echo '<strong>Tags:</strong> ';
			$comma = false;
			foreach ($item->tags as $tag)
			{
				if ($comma)
					echo ', ';
				$comma = true;
				echo html::anchor($kwalbum_url.'/tags/'.$tag, $tag);
			}
			echo '<br/>';
		}
		if (sizeof($item->persons) > 0)
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
				echo html::anchor($kwalbum_url.'/people/'.$person, $person);
			}
			echo '<br/>';
		}
	?>
</div>