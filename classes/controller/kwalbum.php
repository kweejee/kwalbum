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

class Controller_Kwalbum extends Controller_Template
{
	// allow to run in production
	const ALLOW_PRODUCTION = true;

	public $location, $date, $tags, $people, $params;
	public $user, $item;

	public function before()
	{
		$this->template = new View('kwalbum/template');
		$this->url = Kohana::$base_url.'kwalbum';

		// get location from URL
		if ($this->location = Security::xss_clean($this->request->param('location')))
		{
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

		// tags
		$this->tags = explode(',', Security::xss_clean($this->request->param('tags')));
		if ($this->tags[0])
		{
			Model_Kwalbum_Item::append_where('tags', $this->tags);
		}

		// people names
		$this->people = explode(',', Security::xss_clean($this->request->param('people')));
		if ($this->people[0])
		{
			Model_Kwalbum_Item::append_where('people', $this->people);
		}

		// item id
		if (0 < $this->request->param('id'))
		{
			$this->item = Model::factory('kwalbum_item')->load((int)$this->request->param('id'));
		}

		// Set up test user
		$this->user = Model::factory('kwalbum_user')->load(1);


		$this->template->set_global('user', $this->user);
		$this->template->set_global('kwalbum_url', $this->url);
	}

	public function action_media($file)
	{
		$this->auto_render = false;

		// Find the file extension
		$ext = pathinfo($file, PATHINFO_EXTENSION);

		// Remove the extension from the filename
		$file = substr($file, 0, -(strlen($ext) + 1));

		if ($file = Kohana::find_file('media', $file, $ext))
		{
			// Send the file content as the response
			$this->request->response = str_replace('KWALBUM_URL', $this->url, file_get_contents($file));
		}
		else
		{
			// Return a 404 status
			$this->request->status = 404;
		}

		// Set the content type for this extension
		$this->request->headers['Content-Type'] = File::mime_by_ext($ext);
	}
}
