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

		if (isset($_POST['act']))
		{
			$user = Model::factory('kwalbum_user')
				->load($_POST['name'], 'login_name');

			if ($user->password_equals($_POST['password']))
			{
				$loginLength = (int)$_POST['length'];
				$user->visit_date = date('Y-m-d H:i:s');
				$token = '';
				$length = mt_rand(50,100);
				for ($i = 0; $i < $length; $i++)
					$token = chr(mt_rand(0,122));
				$user->token = sha1($token);
				$user->save();

				if ($loginLength != 0)
					setcookie('kwalbum',
						$user->id.':'.$user->token,
						time() + $loginLength,
						'/');

				session_start();
				$_SESSION['kwalbum_id'] = $user->id;
				session_write_close();

				$this->template->content->success = true;
				$this->user = $user;
				$this->template->set_global('user', $this->user);
			}
			else
			{
				session_start();
				unset($_SESSION['kwalbum_id']);
				unset($_SESSION['kwalbum_edit']);
				setcookie('kwalbum', '', time() - 36000, '/');
				session_write_close();
				$this->template->content->error = '<p class="error">You\'re login name or password was wrong.</p>';
			}
		}

		$this->template->title = 'Logging In';
	}

	function action_logout()
	{
		/* Cookie and session clearing is done in Model_Kwalbum_User, mostly
		 * in_clear_cookies(). This is so that all session checking and clearing
		 * can be taken care of in one place as the user is being loaded.
		 */
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
			$date = date('Y-m-d H:i');

		$content = new View('kwalbum/user/upload');
		$content->user_is_admin = $user->is_admin;
		$content->location = $this->location;
		if (isset($this->tags))
			$content->tags = implode(',', $this->tags);
		$content->date = $date;

		$template = $this->template;
		$template->content = $content;
		$template->title = 'Upload';
		$template->head .= html::style($this->url.'/media/ajax/jqueryautocomplete/jquery.autocomplete.min.css')
			.html::script($this->url.'/media/ajax/jqueryautocomplete/jquery.autocomplete.pack.js')
			.html::script($this->url.'/media/ajax/uploadify/swfobject.js')
		//	.html::script('http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject_src.js')
			.html::style($this->url.'/media/ajax/uploadify/uploadify.min.css')
			.html::script($this->url.'/media/ajax/uploadify/jquery.uploadify.v2.1.0.min.js')
			.html::script($this->url.'/media/ajax/upload.js')
		;
	}

	function action_write()
	{
		$user = $this->user;
		if ( ! $user->can_add)
		{
			$this->template->content = new View('kwalbum/invalidpermission');
			return;
		}

		$url = $this->url;

		if (!$date = $this->date)
			$date = date('Y-m-d H:i');

		$content = new View('kwalbum/user/write');
		$content->user_is_admin = $user->is_admin;
		$content->location = $this->location;
		$content->tags = 'news,';
		if (isset($this->tags))
			$content->tags .= implode(',', $this->tags);
		$content->date = $date;

		if (isset($_POST['act']))
		{
			$adder = new Kwalbum_ItemAdder($this->user);
			if ($id = $adder->save_write())
			{
				$content->message = "There has been success in saving your words!<br/><a href='$this->url/~$id'>Go read them now to make sure they are correct.</a>";
			}
			else
			{
				Kohana::$log->add('~user/write', 'ItemAdder failed to save_write item');
				$content->message = 'Your words were not saved.  Try again or report the error and save your message somewhere else for now.';
				$content->location = $_POST['loc'];
				$content->tags = $_POST['tags'];
				$content->date = $_POST['date'];
				$content->description = $_POST['description'];
			}
		}

		$template = $this->template;
		$template->content = $content;
		$template->title = 'Write';
		$template->head .= html::style('kwalbum/media/ajax/jqueryautocomplete/jquery.autocomplete.min.css')
			.html::script('kwalbum/media/ajax/jqueryautocomplete/jquery.autocomplete.pack.js')
			.html::script('kwalbum/media/ajax/write.js')
		;
	}
}