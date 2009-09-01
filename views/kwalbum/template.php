<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Main layout of every Kwalbum page.
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jul 1, 2009
 * @package kwalbum
 * @since 3.0 Jul 1, 2009
 */

$config = Kohana::config('kwalbum', true);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php echo htmlspecialchars(isset($title) ? $title.$config->title_separator.$config->title : $config->title) ?></title>

	<?php
		echo html::style('kwalbum/media/css/default.css');
		echo isset($head) ? $head : null;
	?>

</head>
<body>
<?php echo View::factory('kwalbum/mainmenu')->render(); ?>

<div class="box">
	<?php echo $content ?>
</div>

<p class="copyright">
	Rendered in {execution_time} seconds, using {memory_usage} of memory<br />
	Powered by Kwalbum and Kohana
</p>

</body>
</html>