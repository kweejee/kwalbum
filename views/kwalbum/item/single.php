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
echo '<br>';
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