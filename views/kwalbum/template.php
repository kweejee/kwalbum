<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Main layout of every Kwalbum page.
 *
 * @author Tim Redmond
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since 3.0 Jul 1, 2009
 */

$config = Kohana::$config->load('kwalbum');

// create summary of location, tags, and people being searched for
$summary = '';
if ($date) {
    $summary .= $date->format('j M \'y').' to '.$date2->format('j M \'y');
}
if ($location)
{
	if ($summary)
		$summary .= $config->title_separator;
	$summary .= $location;
}
if ($tags)
{
	if ($summary)
		$summary .= $config->title_separator;
	$summary .= implode(' + ', $tags);
}
if ($people)
{
	if ($summary)
		$summary .= $config->title_separator;
	$summary .= implode(' + ', $people);
}

if ($summary)
	$title = $summary;

$title = isset($title) ? $title.$config->title_separator.$config->title : $config->title;

if (isset($content->item) and $content->item instanceof Model_Kwalbum_Item)
{
	$description = ($content->item->description
		? $content->item->description
		: ($date
			? ($location
				? $content->item->filename
				: $content->item->location
			)
			:$content->item->pretty_date
		)
	);
	if ($item->type == 'jpeg' or $item->type == 'gif' or $item->type == 'png')
		$thumbnail_image = $item->getThumbnailURL($kwalbum_url);
}

$meta_description = isset($description) ? '<meta property="og:description" content="'.htmlspecialchars($description).'" />' : '';
$meta_image = isset($thumbnail_image) ? '<meta property="og:image" content="'.$thumbnail_image.'" />' : '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta property="og:title" content="<?php echo $title ?>" />
	<?php echo $meta_description ?>
	<?php echo $meta_image ?>

	<title><?php echo $title ?></title>

	<?php
		echo HTML::style('https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css');
		echo HTML::style($kwalbum_url.'/media/css/default.css');
		echo HTML::script('https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js');
		echo HTML::script('https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js');
		echo isset($head) ? $head : null;
	?>

</head>
<body>
<?php
echo View::factory('kwalbum/mainmenu')->render();
echo '<hr/>';
echo $content
?>

</body>
</html>
