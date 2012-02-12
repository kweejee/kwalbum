<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 March 7, 2010
 * @package kwalbum
 * @since 3.0 March 7, 2010
 */

// create page links
$page_links = '';
for($i = 1; $i <= $total_pages; $i++)
{
	if ($i == $page_number)
		$page_links .= "<span class='kwalbumCurrentIndex'>$i</span> ";
	else
		$page_links .= html::anchor($kwalbum_url.'/~browse/popular/page/'.$i,
			$i).' ';
}
$page_links = "<div class='kwalbumPageNumbers'>pages: $page_links</div>";


// show page

echo $page_links;

if (count($items) == 0)
	echo '<div class="kwalbumBox"><h2>No items were found that have view counts.</h2></div>';

foreach ($items as $item)
{
	$item->hide_if_needed($user);
	echo "<div class='kwalbumBox' style='height:250px;'>";

	// show thumbnail based on file type
	echo '<h2>'.$item->count.' <small>views</small></h2>';
	echo $item->pretty_date.'<br/>';
	echo Kwalbum_Helper :: getThumbnailLink($item, $kwalbum_url, $kwalbum_url_params);
	echo "</div>";
}

echo $page_links;