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

	function action_login()
	{
		$this->template->content = new View('kwalbum/user/logout');
		$this->template->title = 'Logged Out';
	}

	function action_upload()
	{
		$user = $this->user;
		if ( ! $user->can_add)
		{
			$this->template->content = new View('kwalbum/invalidpermission');
			return;
		}

		$url = $this->url;

		if (!$date = $this->date)
			$date = date('Y-m-d');

		$content = new View('kwalbum/user/upload');
		$content->user_is_admin = $user->is_admin;
		$content->location = $this->location;
		$content->tags = implode(',', $this->tags);
		$content->date = $date;

		$template = $this->template;
		$template->content = $content;
		$template->title = 'Upload';
		$template->head = html::script('kwalbum/media/ajax/jquery.js')
			.html::style('kwalbum/media/ajax/jqueryautocomplete/jquery.autocomplete.min.css')
			.html::script('kwalbum/media/ajax/jqueryautocomplete/jquery.autocomplete.pack.js')
			.html::script('kwalbum/media/ajax/uploadify/swfobject.js')
		//	.html::script('http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject_src.js')
			.html::style('kwalbum/media/ajax/uploadify/uploadify.min.css')
			.html::script('kwalbum/media/ajax/uploadify/jquery.uploadify.v2.1.0.min.js')
			.html::script('kwalbum/media/ajax/upload.js')
		;
	}

}