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


class Controller_Browse extends Controller_Kwalbum
{
	function action_date($year = '0000', $month = '00', $day = '00')
	{
		$view = new View('kwalbum/browse');
		$this->template->content = $view;
		$this->template->title = 'date browsing';

		$date = $year.'-'.$month.'-'.$day;
		$view->tempInfo = "date = $date";

	}
	function action_tag($tag)
	{
		$view = new View('kwalbum/browse');
		$this->template->content = $view;
		$this->template->title = 'tag browsing';

		$view->tempInfo = 'tag = '.$tag;

	}
	function action_location($location)
	{
		$view = new View('kwalbum/browse');
		$this->template->content = $view;
		$this->template->title = 'location browsing';

		$view->tempInfo = 'location = '.$location;
	}
}
