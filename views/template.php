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

define('URL', Kohana::config('kwalbum.url', TRUE));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php echo html::specialchars(isset($title) ? $title.Kohana::config('kwalbum.title_separator').Kohana::config('kwalbum.title') : Kohana::config('kwalbum.title')) ?></title>

	<?php echo html::stylesheet(Kohana::config('kwalbum.css_url').'/default')?>

</head>
<body>
	<p>
		<a href='<?php echo URL?>'>main page</a> -
		<a href='<?php echo URL?>item/1'>single item</a> -
		<a href='<?php echo URL?>admin'>admin</a>
		<br/>
		browse by:
		<a href='<?php echo URL?>2005'>single year</a> -
		<a href='<?php echo URL?>2009/6/20'>full date</a> -
		<a href='<?php echo URL?>tag/test'>tag</a> -
		<a href='<?php echo URL?>Home'>location</a> -
	</p>

	<?php echo $content ?>

	<p class="copyright">
		Rendered in {execution_time} seconds, using {memory_usage} of memory<br />
		Powered by Kwalbum and Kohana
	</p>

</body>
</html>