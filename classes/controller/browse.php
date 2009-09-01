<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Sep 1, 2009
 */


class Controller_Browse extends Controller_Kwalbum
{
	public function action_index()
	{

		if ( ! $this->location and ! $this->date and ! $this->tags[0] and ! $this->people[0])
		{
			$this->template->content = new View('kwalbum/index');
			return;
		}

		//echo $this->request->route->uri(array('tags' => 'a,b', 'location' => 'd'));
		//echo Kohana::debug($this);
		$view = new View('kwalbum/browse/index');
		$view->items = Model_Kwalbum_Item::get_thumbnails();
		$this->template->content = $view;
		$this->template->title = 'browsing all';

	}
	}