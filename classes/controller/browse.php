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

		//echo Kohana::debug($this);
		if ( $this->request->uri == 'kwalbum' and ! ($this->location or $this->date or count($this->tags) > 0))
		{
			$this->template->content = new View('kwalbum/index');
			return;
		}

		//echo $this->request->route->uri(array('tags' => 'a,b', 'location' => 'd'));
		$view = new View('kwalbum/browse/index');
		$view->items = Model_Kwalbum_Item::get_thumbnails($this->page_number);
		$this->template->content = $view;
		$this->template->title = 'browsing all';

	}

	public function action_newest()
	{

		//echo Kohana::debug($this);
		if ( $this->request->uri == 'kwalbum' and ! ($this->location or $this->date or count($this->tags) > 0))
		{
			$this->template->content = new View('kwalbum/index');
			return;
		}

		$view = new View('kwalbum/browse/index');
		Model_Kwalbum_Item::set_sort_direction('DESC');
		$view->items = Model_Kwalbum_Item::get_thumbnails($this->page_number);
		$this->template->content = $view;
		$this->template->title = 'browsing newest';

	}
}