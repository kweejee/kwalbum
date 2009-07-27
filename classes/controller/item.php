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


class Controller_Item extends Controller_Kwalbum
{
	function action_single($id = 0)
	{
		$view = new View('kwalbum/item/single');
		$view->id = $id;
		$this->template->content = $view;
		$this->template->title = 'single item';

		$view->description = 'description stuff';

	}
	function action_edit($id = 0)
	{
		$view = new View('kwalbum/item/edit');
		$view->id = $id;
		$this->template->content = $view;
		$this->template->title = 'edit item';

		$view->description = 'description stuff';

	}
}
