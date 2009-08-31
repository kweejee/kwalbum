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

	function action_index()
	{
		//echo Kohana::debug($this);
		$view = new View('kwalbum/browse');
		$this->template->content = $view;
		$this->template->title = 'browsing all';
	}
}
