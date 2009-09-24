<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Aug 24, 2009
 */

class Controller_Ajax extends Controller_Kwalbum
{
	function before()
	{
		session_id($_POST['session_id']);
		//session_name(Kohana::config('session.name'));

		$this->auto_render = false;

		parent::before();
	}

	function action_GetInputLocations()
	{
		$userInput = trim(@$_GET['q']);
		$locations = Model_Kwalbum_Location::getNameArray(0, 10, 0, $userInput, 'count DESC');

		foreach($locations as $location)
		{
			echo "$location\n";
		}
	}
	function action_GetInputTags()
	{
		$userInput = trim(@$_GET['q']);
		$tags = Model_Kwalbum_Tag::getNameArray(0, 10, 0, $userInput, 'count DESC');

		foreach($tags as $tag)
		{
			echo "$tag\n";
		}
	}

	function action_SetEditMode()
	{
		if ( ! $this->user->can_edit)
			$_POST['edit'] = false;
		session_start();
		$_SESSION['kwalbum_edit'] = (bool)$_POST['edit'];
		session_write_close();
		echo 1;
	}

	function action_upload()
	{
		if ( ! $this->user->can_add)
		{
			$this->request->status = 400;
			Kohana::$log->add('~ajax/upload', 'invalid permission');
			return;
		}

		if ( ! empty($_FILES))
		{
			$adder = new Kwalbum_ItemAdder($this->user);
			if ($adder->save_upload())
			{
				echo 'success';
				return;
			}
			else
			{
				$this->request->status = 400;
				Kohana::$log->add('~ajax/upload', 'ItemAdder failed to save the new item');
				return;
			}
		}
		$this->request->status = 400;
		Kohana::$log->add('~ajax/upload', 'empty FILES sent');
	}
}
