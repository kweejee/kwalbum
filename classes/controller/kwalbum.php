<?php defined('SYSPATH') OR die('No direct access allowed.');
/**set_global
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.1 Feb 12, 2012
 * @package kwalbum
 * @since 3.0 Jun 30, 2009
 */

class Controller_Kwalbum extends Controller_Template
{
	// allow to run in production
	const ALLOW_PRODUCTION = true;

	public $location, $date, $tags, $people, $create_dt, $params;
	public $user, $item, $previous_item, $next_item;
	public $total_items, $total_pages, $item_index, $page_number;
	public $in_edit_mode;

	public function before()
	{
		$this->template = new View('kwalbum/template');
		$this->url = URL::base(true, 'http').'kwalbum';

		// get location from URL
		if ( $this->request->param('location'))
		{
			$this->location = urldecode($this->request->param('location'));
			Model_Kwalbum_Item::append_where('location', $this->location);
		}
		else if ( ! empty($_GET['location']))
		{
			$this->location = $_GET['location'];
			Model_Kwalbum_Item::append_where('location', $this->location);
		}

		// date
		$year = (int)$this->request->param('year');
		$month = (int)$this->request->param('month');
		$day = (int)$this->request->param('day');
		if ($year or $month or $day)
		{
			$this->date = ($year ? abs($year) : '0000').'-'.($month ? abs($month) : '00').'-'.($day ? abs($day) : '00');
			Model_Kwalbum_Item::append_where('date', $this->date);
		}
		else if ( ! empty($_GET['date']))
		{
			$date = explode('-', $_GET['date']);
			$this->date = ((int)@$date[0] ? abs($date[0]) : '0000').'-'.((int)@$date[1] ? abs($date[1]) : '00').'-'.((int)@$date[2] ? abs($date[2]) : '00');
			Model_Kwalbum_Item::append_where('date', $this->date);
		}

		// tags
		$this->tags = explode(',', urldecode($this->request->param('tags')));
		if ($this->tags[0] != '')
		{
			Model_Kwalbum_Item::append_where('tags', $this->tags);
		}
		else if ( ! empty($_GET['tags']))
		{
			$this->tags = explode(',', $_GET['tags']);
			Model_Kwalbum_Item::append_where('tags', $this->tags);
		}
		else
			$this->tags = null;

		// people names
		$this->people = explode(',', urldecode($this->request->param('people')));
		if ($this->people[0] != '')
		{
			Model_Kwalbum_Item::append_where('people', $this->people);
		}
		else if ( ! empty($_GET['people']))
		{
			$this->people = explode(',', $_GET['people']);
			Model_Kwalbum_Item::append_where('people', $this->people);
		}
		else
			$this->people = null;

		// created timestamp
		if ($this->request->param('created_date'))
		{
			$this->create_dt = urldecode($this->request->param('created_date'));
			if ($this->request->param('created_time'))
			{
				$this->create_dt .= ' '.urldecode($this->request->param('created_time'));
				Model_Kwalbum_Item::append_where('create_dt', $this->create_dt);
			}
			else
			{
				Model_Kwalbum_Item::append_where('create_date', $this->create_dt);
			}
		}
		else if ( ! empty($_GET['created_date']))
		{
			$this->create_dt = $_GET['created_date'];
			if ($_GET['created_time'])
			{
				$this->create_dt .= ' '.$_GET['created_time'];
				Model_Kwalbum_Item::append_where('create_dt', $this->create_dt);
			}
			else
			{
				Model_Kwalbum_Item::append_where('create_date', $this->create_dt);
			}
		}
		else
		{
			$this->create_dt = null;
		}

		// Set up user if logged in
		$this->user = Model::factory('kwalbum_user');
		$this->user->load_from_cookie($this->request->action());

		// item id
		if (0 < $this->request->param('id'))
		{
			$this->item = Model::factory('kwalbum_item')
				->load((int)$this->request->param('id'));
			$this->item->hide_if_needed($this->user);
			$this->template->set_global('item', $this->item);
		}

		$this->params =
			($year ? $year.'/' : null)
			.($month ? $month.'/' : null)
			.($day ? $day.'/' : null)
			.($this->location ? $this->location.'/' : null)
			.($this->tags ? 'tags/'.implode(',', $this->tags).'/' : null)
			.($this->people ? 'people/'.implode(',', $this->people).'/' : null)
			.($this->create_dt ? 'created/'.implode('/', explode(' ', $this->create_dt)).'/' : null);

		if ($this->request->action() != 'media' and $this->request->controller() != 'install')
		{
			$this->total_items = Model_Kwalbum_Item::get_total_items();
			$this->total_pages = Model_Kwalbum_Item::get_page_number($this->total_items);
			$this->item_index = 0;
			$page_number = (int)$this->request->param('page');

			if ($page_number < 1 or $page_number > $this->total_pages)
			{
				$page_number = 1;
			}

			if ($this->item)
			{
				$this->item_index = Model_Kwalbum_Item::get_index($this->item->id, $this->item->sort_date);
				$page_number = Model_Kwalbum_Item::get_page_number($this->item_index);
				$this->next_item = Model_Kwalbum_Item::get_next_item($this->item->id, $this->item->sort_date);
				if ($this->next_item->id)
					$this->next_item->hide_if_needed($this->user);
				$this->previous_item = Model_Kwalbum_Item::get_previous_item($this->item->id, $this->item->sort_date);
				if ($this->previous_item->id)
					$this->previous_item->hide_if_needed($this->user);

				$this->template->set_global('previous_item', $this->previous_item);
				$this->template->set_global('next_item', $this->next_item);
			}

			$this->page_number = $page_number;

			$this->template->set_global('location', $this->location);
			$this->template->set_global('date', $this->date);
			$this->template->set_global('tags', $this->tags);
			$this->template->set_global('people', $this->people);
			$this->template->set_global('create_dt', $this->create_dt);
			$this->template->set_global('kwalbum_url_params', $this->params);

			$this->template->set_global('total_items', $this->total_items);
			$this->template->set_global('total_pages', $this->total_pages);
			$this->template->set_global('item_index', $this->item_index);
			$this->template->set_global('page_number', $this->page_number);

			$this->in_edit_mode = !empty($_SESSION['kwalbum_edit']);
			$this->template->set_global('in_edit_mode', $this->in_edit_mode);
			$this->template->set_global('head', html::script($this->url.'/media/ajax/toggle.edit.js'));
		}
		$this->template->set_global('kwalbum_url', $this->url);
		$this->template->set_global('user', $this->user);
	}

	public function action_media()
	{
		$file = $this->request->param('file');
		$this->auto_render = false;

		// Find the file extension
		$ext = pathinfo($file, PATHINFO_EXTENSION);

		// Remove the extension from the filename
		$file = substr($file, 0, -(strlen($ext) + 1));
		
		$file = Kohana::find_file('media', $file, $ext);
		if (is_file($file))
		{
			// Set the content type for this extension
			$response = $this->request->create_response();
			$response->headers('Content-Type', File::mime_by_ext($ext));
			$response->send_headers();

			// Send the file content as the response
			if ($ext == 'css' || $ext == 'js')
			{
				$body = str_replace('KWALBUM_URL', $this->url, file_get_contents($file));
				$body = str_replace('SESSION_ID', session_id(), $body);
				echo $body;
				exit;
			}
			else
			{
				$img = @ fopen($file, 'rb');
				if ($img)
				{
					fpassthru($img);
					exit;
				}
			}
		}
		// Return a 404 status
		$this->request->response()->status(404);
	}
}
