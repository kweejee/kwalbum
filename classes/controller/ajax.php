<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Aug 24, 2009
 */

class Controller_Ajax extends Controller_Kwalbum
{
	public function before()
	{
		if ($this->request->action() == 'upload')
			session_id($_POST['session_id']);
		//session_name(Kohana::$config->load('session.name'));

		$this->auto_render = false;

		parent::before();
	}

	public function action_GetInputLocations()
	{
		$this->_testPermission();
		$userInput = trim(@$_GET['term']);
		$locations = Model_Kwalbum_Location::getNameArray($this->user, 0, 10, 0, $userInput, '(loc.count+loc.child_count) DESC, p.name ASC, loc.name ASC');
		echo json_encode($locations);
	}

	public function action_SetLocation()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_POST['item']);
		$this->_testPermission($item);
		$item->location = htmlspecialchars(trim($_POST['value']));
		$item->save();
		echo $item->location;
	}

	public function action_SetDate()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_POST['item']);
		$this->_testPermission($item);
		$date = Kwalbum_Helper::replaceBadDate($_POST['value'].' '.$item->time);
		if ($item->visible_date == $item->sort_date)
			$item->sort_date = $date;
		$item->visible_date = $date;
		$item->save();
		echo $item->date;
	}

	public function action_SetTime()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_POST['item']);
		$this->_testPermission($item);
		$date = Kwalbum_Helper::replaceBadDate($item->date.' '.$_POST['value']);
		if ($item->visible_date == $item->sort_date)
			$item->sort_date = $date;
		$item->visible_date = $date;
		$item->save();
		echo $item->time;
	}

	public function action_SetSortDate()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_POST['item']);
		$this->_testPermission($item);
		$date = Kwalbum_Helper :: replaceBadDate($_POST['value']);
		$item->sort_date = $date;
		$item->save();
		echo $item->sort_date;
	}
	public function action_GetRawDescription()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_GET['item']);
		$this->_testPermission($item);
		echo $item->description;
	}

	public function action_SetDescription()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_POST['item']);
		$this->_testPermission($item);
		$item->description = trim($_POST['value']);
		$item->save();
		echo $item->description;
	}

	public function action_GetVisibility()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_GET['item']);
		$this->_testPermission($item);
		$vis = array('Public', 'Members Only', 'Privileged Only', 'Contributors Only');
		if ($this->user->is_admin)
			$vis[5] = 'Admin Only';
		$vis['selected'] = $item->hide_level;
		echo json_encode($vis);
	}

	public function action_SetVisibility()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_POST['item']);
		$this->_testPermission($item);
        $visibility = (int) (@ $_POST['value']);
        if ($visibility < 0)
        {
            $visibility = 0;
        } else
            if ($visibility > 3)
            {
                if ($this->user->is_admin)
                    $visibility = 5;
                else
                    $visibility = 3;
            }
        $item->hide_level = $visibility;
		$item->save();
		$vis = array('Public', 'Members Only', 'Privileged Only', 'Contributors Only', '', 'Admin Only');
		echo $vis[$item->hide_level];
	}

	public function action_GetInputTags()
	{
		$this->_getInputList('Model_Kwalbum_Tag', 'getNameArray');
	}

	public function action_SetTags()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_POST['item']);
		$this->_testPermission($item);
		$tags = explode(',', htmlspecialchars($_POST['value']));
		for ($i = 0; $i < count($tags); $i++)
		{
			$tags[$i] = trim($tags[$i]);
		}
		sort($tags, SORT_LOCALE_STRING);
		$item->tags = $tags;
		$item->save();
		echo implode(',',$item->tags);
	}

	public function action_SetPersons()
	{
		$item = Model :: factory('kwalbum_item')->load((int)$_POST['item']);
		$this->_testPermission($item);
		$persons = explode(',', htmlspecialchars($_POST['value']));
		for ($i = 0; $i < count($persons); $i++)
		{
			$persons[$i] = trim($persons[$i]);
		}
		$item->persons = $persons;
		$item->save();
		echo implode(',',$item->persons);
	}

	public function action_GetInputPersons()
	{
		$this->_getInputList('Model_Kwalbum_Person', 'getNameArray');
	}

	public function action_SetEditMode()
	{
		if ( ! $this->user->can_edit)
			$_POST['edit'] = false;
		session_start();
		$_SESSION['kwalbum_edit'] = (bool)$_POST['edit'];
		session_write_close();
		echo 1;
	}

	public function action_AddComment()
	{
		$item = Model::factory('kwalbum_item')->load((int)$_POST['item']);
		if ( ! $this->user->can_view_item($item))
		{
			echo 'no commenting for you';
			return;
		}
		$comment = new Model_Kwalbum_Comment();
		$comment->name = $this->user->name;
		$comment->text = htmlspecialchars(trim($_POST['comment']));
		$comment->item_id = $item->id;
		$comment->save();
		echo $comment->name.' : '.$comment->date.'<br/>'.$comment->text.'<hr/>';
	}

	public function action_DeleteItem()
	{
		$item = Model::factory('kwalbum_item')->load((int)$_POST['item']);
		if ( ! $this->user->can_edit_item($item))
		{
			echo 0;
			return;
		}
		$item->delete();
		echo 1;
	}

	public function action_upload()
	{
		if ( ! $this->user->can_add)
		{
			$this->request->response()->status(400);
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
				$this->request->response()->status(400);
				Kohana::$log->add('~ajax/upload', 'ItemAdder failed to save_upload item');
				return;
			}
		}
		$this->request->response()->status(400);
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

	private function _getInputList($class, $function)
	{
		$this->_testPermission();
		$tags = explode(',', @$_GET['term']);
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

		$tags = call_user_func_array(array($class, $function), array(0, 10, 0, $tag, 'count DESC', $not_included));

		$output_tags = array();
		foreach($tags as $tag)
		{
			$output_tags[] = $old_tags.$tag;
		}
		echo json_encode($output_tags);
	}

	public function action_SetItemMapLocation()
	{
		$item = new Model_Kwalbum_Item;
		$item->load($_POST['id']);

		$this->_testPermission($item);

		$item->latitude = (float)$_POST['lat'];
		$item->longitude = (float)$_POST['lon'];
		$item->save();
		echo 1;
		exit;
	}
	
	public function action_GetMapLocations()
	{
		//$zoom = (int)$_GET["z"];
		$data = array();
		Model_Kwalbum_Location::getMarkers((float)$_GET["l"], (float)$_GET["r"], (float)$_GET["t"], (float)$_GET["b"], $data);

		echo "point	type	title	description";
		foreach($data as $d) {
			echo "\n{$d['lon']},{$d['lat']}	l	{$d['name']}	{$this->url}/".urlencode($d['name']);
		}
	}
	
	public function action_GetMapItems()
	{
		$zoom = (int)$_GET["z"];
		if($zoom == 17)
			$limit = 20;
		else
			$limit = 10;

		$where = Model_Kwalbum_Item::get_where_query();
		if ( ! $where)
			$where = ' WHERE 1=1 ';
		$data = array();
		Model_Kwalbum_Item::getMarkers((float)$_GET["l"], (float)$_GET["r"], (float)$_GET["t"], (float)$_GET["b"], $where, $data, $limit, 1);
				
		echo "point	type	title	description";
		foreach($data as $d) {
			echo "\n{$d['lon']},{$d['lat']}	";
			if ($d['group'])
				echo "g	{$d['count']}	{$zoom}";
			else
				echo "i	{$d['id']}	{$d['date']}";
		}
	}
}
