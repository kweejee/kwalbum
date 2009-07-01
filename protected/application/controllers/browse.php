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


class Browse_Controller extends Kwalbum_Controller
{
	function date()
	{
		$view = new View('browse');
		$this->template->content = $view;
		$this->template->title = 'date browsing';

		$args = Router::$arguments;
		$date = $args[0];
		if (isset($args[1]))
			$date .= '-'.$args[1];
		else
			$date .= '-00';
		if (isset($args[2]))
			$date .= '-'.$args[2];
		else
			$date .= '-00';
		$view->tempInfo = "date = $date";

	}
	function tag()
	{
		$view = new View('browse');
		$this->template->content = $view;
		$this->template->title = 'tag browsing';

		$args = Router::$arguments;
		$view->tempInfo = 'tag = '.$args[0];

	}
	function location()
	{
		$view = new View('browse');
		$this->template->content = $view;
		$this->template->title = 'location browsing';

		$args = Router::$arguments;
		$view->tempInfo = 'location = '.$args[0];

	}
}
