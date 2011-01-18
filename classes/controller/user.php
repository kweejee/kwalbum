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
				$user->token = Kwalbum_Helper::getRandomHash();
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
		{
			$date = date('Y-m-d H:i');
		}

		$content = new View('kwalbum/user/write');
		$content->user_is_admin = $user->is_admin;
		$content->location = $this->location;
		$content->tags = 'news,';
		if (isset($_POST['group_option']))
		{
			$content->same_group = ($_POST['group_option'] == 'existing');
		}
		else
		{
			$content->same_group = false;
		}
		
		if (isset($this->tags))
		{
			$content->tags .= implode(',', $this->tags);
		}
		$content->date = $date;

		if (isset($_POST['act']))
		{
			$adder = new Kwalbum_ItemAdder($this->user);
			$id = $adder->save_write();
			if ($id)
			{
				$content->message = "There has been success in saving your words!<br/><a href='$this->url/~$id'>Go read them now to make sure they are correct.</a>";
				$content->same_group = true;
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

	function action_register()
	{

		$this->template->content = new View('kwalbum/user/register');
		$this->template->title = 'Register';
		$form = array
		(
			'name' => '',
			'login_name' => '',
			'email' => '',
			'password' => ''
		);

		// Copy the form as errors so the errors will be stored with keys
		// matching the form field names
		$errors = $form;

		if ($_POST)
		{
			$post = Validate::factory($_POST)
				->filter(true, 'trim')
				->filter(true, 'htmlspecialchars')
				->rule('name', 'not_empty')
				->rule('login_name', 'not_empty')
				->rule('email', 'not_empty')
				->rule('email', 'email')
				->rule('password', 'not_empty')
				->rule('name', 'min_length', array(2))
				->rule('name', 'max_length', array(40));

			if ($post->check())
			{
				$data = $post->as_array();
				$name = Security :: xss_clean($data['name']);
				$login_name = Security :: xss_clean($data['login_name']);
				$email = Security :: xss_clean($data['email']);
				$password = $data['password'];

				$user = Model::factory('kwalbum_user');

				// TODO: extend Validate to include custom error checking and clean this part up
				$has_errors = false;
				$temp = $user->load($login_name, 'login_name');
				if ($temp->id)
				{
					$has_errors = true;
					$errors['login_name'] = 'This login name is already being used.';
				}
				$temp = $user->load($name, 'name');
				if ($temp->id)
				{
					$has_errors = true;
					$errors['name'] = 'This name is already being used.';
				}

				if ($has_errors)
				{
					// Repopulate the form fields
					$form = arr::overwrite($form, $post->as_array());
				}
				else
				{


					// create user
					$user->name = $name;
					$user->login_name = $login_name;
					$user->email = $email;
					$user->password = $password;
					$user->permission_level = 1;
					$user->save();
					$errors = false;
				}
			}
			else // Did not validate
			{
				// Repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// Populate the error fields, if any
				// Pass the error message file name to the errors() method
				// Default error message file is in i18n/en_US/
				$errors = arr::overwrite($errors, $post->errors('install_form/errors'));
			}
		}
		$this->template->set_global('form', $form);
		$this->template->set_global('errors', $errors);
	}

	public function action_resetpassword()
	{
		$this->template->content = new View('kwalbum/user/resetpassword');
		$this->template->title = 'Reset Password';

		if (isset($_GET['h']))
		{
			$temp = explode('.', $_GET['h']);
			if ( ! isset($temp[1]))
			{
				$this->template->content->message = '<span class="errors">This address is no longer valid for changing your password.</span>';
				return;
			}

			$hash = $temp[0];
			$id = (int)$temp[1];
			$user = Model::factory('kwalbum_user')->load($id);

			if ( ! $user->reset_code or $user->reset_code != $hash)
			{
				$user->reset_code = '';
				$user->save();
				$this->template->content->message = '<span class="errors">This address is no longer valid for changing your password.</span>';
			}
			else if (isset($_POST['act']))
			{
				$pw = $_POST['pw'];
				if (strlen($pw) > 5)
				{
					$user->password = $pw;
					$user->reset_code = '';
					$user->save();
					$this->template->content->message = 'Your password has been changed and you can now <a href="'.$this->url.'/~user/login">log in</a>.';
				}
				else
				{
					$this->template->content->message2 = '<div class="errors">New password must be at least 6 characters long.</div>';
				}
			}
			$user->permission_level = 0;
			$this->template->set_global('user', $user);
		}
		elseif (isset($_POST['act']))
		{
			$login = $_POST['name'];
			$email = $_POST['email'];
			$user = Model::factory('kwalbum_user')->load($login, 'login_name');
			if ($user->email == $email)
			{
				if ( ! $user->reset_code)
				{
					$user->reset_code = Kwalbum_Helper::getRandomHash();
					$user->save();
				}
				$host = $_SERVER['SERVER_NAME'];
				$emailMessage = "A password change has been requested for $login at $host.  To change it go to\n$this->url/~user/resetpassword/?h=$user->reset_code.$user->id\n\nAutomatic email from\nKwalbum \n\n";
				if ( ! mail($email, 'Lost Password on '.$host, $emailMessage, 'From: "do_not_reply.'.$host.'" <kwalbum@'.$host.'>'))
				{
					$this->template->content->message = '<span class="errors">Email with further instructions was not sent.  Please contact the website administrator.</span>';
				}
			}
			$this->template->content->message = 'If the login name and email address match, then an email has been sent with further instructions.  If you do not recieve the email within a few hours, check your junk mail folder then contact the website administrator if you still can not find it.  If you are unsure which email address or name you registered with, try them all until you get an email or contact the administrator and ask for help.';
		}
	}
}