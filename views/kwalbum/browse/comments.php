<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jun 30, 2009
 * @package kwalbum
 * @since 3.0 Jun 30, 2009
 */

// create page links
$page_links = '';
for($i = 1; $i <= $total_pages; $i++)
{
	if ($i == $page_number)
		$page_links .= "<span class='kwalbumCurrentIndex'>$i</span> ";
	else
		$page_links .= HTML::anchor($kwalbum_url.'/~browse/comments/page/'.$i,
			$i).' ';
}
$page_links = "<div class='kwalbumPageNumbers'>pages: $page_links</div>";


// show page

//echo $page_links;

if (count($items) == 0)
	echo '<div class="kwalbumThumbnailBox"><h2>No items were found that have comments.</h2></div>';

foreach ($items as $item)
{
	$item['item']->hide_if_needed($user);
	//$thumbview = new View('kwalbum/item/thumbnail');
	echo "<div class='kwalbumThumbnailBox'>";

	// show thumbnail based on file type
	echo '<table><tr><td>';
	echo Kwalbum_Helper::getThumbnailLink($item['item'], $kwalbum_url, $kwalbum_url_params);
	echo '</td><td style="vertical-align:text-top;text-align:left;width:600px;">';
	echo '<h2>'.$item['comment']->date.'<small> : '.$item['comment']->name.'</small></h2>'.$item['comment']->text;
	echo '</td></tr></table>';
	echo "</div>";
}

//echo $page_links;