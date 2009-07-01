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


class Item_Controller extends Kwalbum_Controller
{
	function single()
	{
		$view = new View('item/single');
		$this->template->content = $view;
		$this->template->title = 'single item';

		$view->description = 'description stuff';

	}
}
