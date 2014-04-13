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

class Controller_AjaxAdmin extends Controller_Kwalbum
{
	function action_EditLocationName()
	{
		$this->_testPermission();
		$loc = Model :: factory('kwalbum_location')->load((int)$_POST['id']);
		if ( ! empty($_POST['value']))
		{
			$loc->display_name = htmlspecialchars($_POST['value']);
			$loc->save();
		}
		echo $loc->display_name;
		exit;
	}

	function action_DeleteLocation()
	{
		$this->_testPermission();
		Model :: factory('kwalbum_location')
			->load((int)$_POST['id'])
			->delete();
		exit;
	}

	function action_EditPersonName()
	{
		$this->_testPermission();
		$person = Model :: factory('kwalbum_person')->load((int)$_POST['id']);
		if ( ! empty($_POST['value']))
		{
			$person->name = htmlspecialchars(trim($_POST['value']));
			$person->save();
		}
		echo $person->name;
		exit;
	}

	function action_DeletePerson()
	{
		$this->_testPermission();
		Model :: factory('kwalbum_person')
			->load((int)$_POST['id'])
			->delete();
		exit;
	}

	function action_EditTagName()
	{
		$this->_testPermission();
		$tag = Model :: factory('kwalbum_tag')->load((int)$_POST['id']);
		if ( ! empty($_POST['value']))
		{
			$tag->name = htmlspecialchars(trim($_POST['value']));
			$tag->save();
		}
		echo $tag->name;
		exit;
	}

	function action_DeleteTag()
	{
		$this->_testPermission();
		Model :: factory('kwalbum_tag')
			->load((int)$_POST['id'])
			->delete();
		exit;
	}

	function action_GetUserPermission()
	{
        if (!empty($_GET['id'])) {
            $id = explode('_', $_GET['id']);
        }
        if (empty($id[1])) {
            echo 'Invalid id';
            exit;
        }
		$this->_testPermission();
		$perms = Model_Kwalbum_User::$permission_names;
		$user = Model :: factory('kwalbum_user')->load((int)$id[1]);
		$perms['selected'] = $user->permission_level;
		echo json_encode($perms);
		exit;
	}


	function action_EditUserPermission()
	{
        $this->_testPermission();
        if (!empty($_POST['id'])) {
            $id = explode('_', $_POST['id']);
        }
        if (empty($id[1])) {
            echo 'Invalid id';
            exit;
        }
        $user = Model::factory('kwalbum_user')->load((int)$id[1]);
        if (isset($_POST['value'])) {
            if ($user->id > 2 and $user->id != $this->user->id) {
                $user->permission_level = (int)$_POST['value'];
                $user->save();
            }
        }
        echo $user->permission_description;
        exit;
	}

	function action_DeleteUser()
	{
		$this->_testPermission();
		Model :: factory('kwalbum_user')
			->load((int)$_POST['userid'])
			->delete();
		exit;
	}

	function action_SaveMapLocation()
	{
		$this->_testPermission();
		$loc = new Model_Kwalbum_Location;
		$loc->load($_POST['id']);
		$loc->latitude = (float)$_POST['lat'];
		$loc->longitude = (float)$_POST['lon'];
		$loc->save();
		echo 1;
		exit;
	}

	private function _testPermission()
	{
		if ($this->user->is_admin)
			return;
		exit;
	}
}
