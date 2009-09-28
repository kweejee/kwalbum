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
		if ($this->request->action == 'upload')
			session_id($_POST['session_id']);
		//session_name(Kohana::config('session.name'));

		$this->auto_render = false;

		parent::before();
	}

	function action_GetInputLocations()
	{
		$this->_testPermission();
		$userInput = trim(@$_GET['q']);
		$locations = Model_Kwalbum_Location::getNameArray(0, 10, 0, $userInput, 'count DESC');

		foreach($locations as $location)
		{
			echo "$location\n";
		}
	}

	function action_SetLocation()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_POST['item']);
		$this->_testPermission($item);
		$item->location = Security :: xss_clean($_POST['value']);
		$item->save();
		echo $item->location;
	}

	function action_GetRawDescription()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_GET['item']);
		$this->_testPermission($item);
		echo $item->description;
	}

	function action_SetDescription()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_POST['item']);
		$this->_testPermission($item);
		$item->description = Security :: xss_clean($_POST['value']);
		$item->save();
		echo $item->description;
	}

	function action_GetVisibility()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_GET['item']);
		$this->_testPermission($item);
		$vis = array('Public', 'Members Only', 'Privileged Only');
		if ($this->user->is_admin)
			$vis[] = 'Admin Only';
		$vis['selected'] = $item->hide_level;
		echo json_encode($vis);
	}

	function action_SetVisibility()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_POST['item']);
		$this->_testPermission($item);
        $visibility = (int) (@ $_POST['value']);
//        if ($visibility < 0)
//        {
//            $visibility = 0;
//        } else
//            if ($visibility > 2)
//            {
//                if ($this->user->is_admin)
//                    $visibility = 3;
//                else
//                    $visibility = 2;
//            }
        $item->hide_level = $visibility;
		$item->save();
		$vis = array('Public', 'Members Only', 'Privileged Only', 'Admin Only');
		echo $vis[$item->hide_level];
	}

	function action_GetInputTags()
	{
		$this->_getInputList('Model_Kwalbum_Tag::getNameArray');
	}

	function action_SetTags()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_POST['item']);
		$this->_testPermission($item);
		$tags = explode(',', Security :: xss_clean($_POST['value']));
		for ($i = 0; $i < count($tags); $i++)
		{
			$tags[$i] = trim($tags[$i]);
		}
		sort($tags, SORT_LOCALE_STRING);
		$item->tags = $tags;
		$item->save();
		echo implode(',',$item->tags);
	}

	function action_SetPersons()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_POST['item']);
		$this->_testPermission($item);
		$persons = explode(',', Security :: xss_clean($_POST['value']));
		for ($i = 0; $i < count($persons); $i++)
		{
			$persons[$i] = trim($persons[$i]);
		}
		$item->persons = $persons;
		$item->save();
		echo implode(',',$item->persons);
	}

	function action_GetInputPersons()
	{
		$this->_getInputList('Model_Kwalbum_Person::getNameArray');
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
			Kohana::$log->add('~ajax/upload', 'invalid permission for user id '.$this->user->id);
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

	private function _testPermission($item =  null)
	{
		if ($item)
		{
			if ($this->user->can_edit_item($item))
				return;
			echo 'You do not have permission to change this.';
			exit;
		}

		if ($this->user->can_edit)
			return;

		// User has no reason to be getting ajax lists
		// if they do not have permission to edit.
		exit;
	}

	private function _getInputList($function)
	{
		$this->_testPermission();
		$tags = explode(',', @$_GET['q']);
		if (!$size = count($tags))
			exit;

		$old_tags = '';
		$not_included = array();

		for($i = 0; $i < $size; $i++)
		{
			$tag = trim($tags[$i]);
			if ($tag)
			{
				if ($i < $size-1)
				{
					$old_tags .= $tag.',';
					$not_included[] = $tag;
				}
			}
		}
		if (!$tag)
			exit;

		$tags = call_user_func_array($function, array(0, 10, 0, $tag, 'count DESC', $not_included));

		foreach($tags as $tag)
		{
			echo "$old_tags$tag\n";
		}
	}

}
