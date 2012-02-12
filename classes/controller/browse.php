<?php defined('SYSPATH') or die('No direct script access.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Sep 1, 2009
 */


class Controller_Browse extends Controller_Kwalbum
{
	public function action_index()
	{

		//echo Kohana::debug($this);
		if ( $this->request->uri() == 'kwalbum' and ! ($this->location or $this->date or count($this->tags) > 0))
		{
			$this->template->content = new View('kwalbum/index');
			return;
		}

		//echo $this->request->route->uri(array('tags' => 'a,b', 'location' => 'd'));
		$view = new View('kwalbum/browse/index');
		$view->items = Model_Kwalbum_Item::get_thumbnails($this->page_number);
		$this->template->content = $view;
		$this->template->title = 'browsing';

	}

	public function action_comments()
	{

		//echo Kohana::debug($this);
//		if ( $this->request->uri == 'kwalbum' and ! ($this->location or $this->date or count($this->tags) > 0))
//		{
//			$this->template->content = new View('kwalbum/index');
//			return;
//		}

		$view = new View('kwalbum/browse/comments');
		Model_Kwalbum_Comment :: set_sort_field('create');
		Model_Kwalbum_Comment :: set_sort_direction('DESC');
		$view->items = Model_Kwalbum_Comment :: get_thumbnails($this->page_number);
		$this->template->content = $view;
		$this->template->title = 'browsing newest comments';

	}

	public function action_popular()
	{
		$view = new View('kwalbum/browse/popular');
		Model_Kwalbum_Item :: set_sort_field('count');
		Model_Kwalbum_Item :: set_sort_direction('DESC');
		$view->items = Model_Kwalbum_Item :: get_thumbnails($this->page_number);
		$this->template->content = $view;
		$this->template->title = 'browsing most popular';

	}
}