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

if (count($items) == 0)
	echo '<div class="kwalbumBox"><h2>No items were found that match your search.</h2></div>';
foreach ($items as $item)
{
	$thumbview = new View('kwalbum/item/thumbnail');
	$thumbview->item = $item;
	echo $thumbview->render();
}