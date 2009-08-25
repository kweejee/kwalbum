<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Aug 2, 2009
 */

class Controller_User extends Controller_Kwalbum
{
	// allow to run in production
	const ALLOW_PRODUCTION = true;

	function action_login()
	{
		$this->template->content = new View('kwalbum/user/login');
		$this->template->title = 'Logging In';

	}

	function action_upload()
	{
		$user = $this->user;
		if ( ! $user->can_add)
		{
			$this->template->content = new View('kwalbum/invalidpermission');
			return;
		}

		$hidden = (int)@$_POST['hidden'];
		$location = $this->location;
		$tags = $this->tags;
		if (!$date = $this->date)
			$date = date('Y-m-d');


		$content = new View('kwalbum/user/upload');
		$content->user_is_admin = $user->is_admin;
		$content->hidden = $hidden;
		$content->location = $location;
		$content->tags = $tags;
		$content->date = $date;

		$this->template->content = $content;
		$this->template->title = 'Upload';
		$this->template->head = html::style('kwalbum/media/ajax/jqueryautocomplete/jquery.autocomplete.css');
	}
}