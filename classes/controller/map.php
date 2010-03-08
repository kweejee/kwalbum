<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Sep 21, 2009
 */


class Controller_Map extends Controller_Kwalbum
{
	public function action_index()
	{
		$this->template->content = new View('kwalbum/map');
		$this->template->title = 'Map';
	}
}