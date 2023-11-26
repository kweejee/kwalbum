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
    function action_EditLocationName(): void
    {
        $this->_testPermission();
        if (!empty($_POST['id'])) {
            $id = explode('_', $_POST['id']);
        }
        if (empty($id[1])) {
            echo 'Invalid id';
            exit;
        }
        $loc = Model::factory('Kwalbum_Location')->load((int)$id[1]);
        if (!empty($_POST['value'])) {
            $names = explode(trim(Kwalbum_Model::get_config('location_separator_1')), htmlspecialchars($_POST['value']));
            $parent_name = '';
            if (count($names) > 1) {
                $parent_name = trim($names[0]);
                array_shift($names);
                foreach ($names as $i => $name) {
                    $names[$i] = trim($name);
                    if (!$name) {
                        unset($names[$i]);
                    }
                }
                $loc->name = implode(Kwalbum_Model::get_config('location_separator_2'), $names);
            } else {
                $loc->name = trim($names[0]);
            }
            if ($parent_name) {
                if (!$loc->name) {
                    $loc->name = $parent_name;
                    $loc->parent_name = '';
                } else {
                    $loc->parent_name = $parent_name;
                }
            } else {
                $loc->parent_name = '';
            }
            $loc->save();
        }
        echo (string)$loc;
        exit;
    }

    protected function getHideLevelOptions($field): void
    {
        if (!empty($_GET['id'])) {
            $id = explode('_', $_GET['id']);
        }
        if (empty($id[1])) {
            echo 'Invalid id';
            exit;
        }
        $this->_testPermission();
        $levels = array_slice(Model_Kwalbum_Item::$hide_level_names, 0, 3);
        $loc = Model::factory('Kwalbum_Location')->load((int)$id[1]);
        $levels['selected'] = $field == 'name' ? $loc->name_hide_level : $loc->coordinate_hide_level;
        echo json_encode($levels);
        exit;
    }

    /**
     * @param string $field
     * @return Model_Kwalbum_Location
     */
    protected function setLocationHideLevel(string $field): Model_Kwalbum_Location
    {
        $this->_testPermission();
        if (!empty($_POST['id'])) {
            $id = explode('_', $_POST['id']);
        }
        if (empty($id[1])) {
            echo 'Invalid id';
            exit;
        }
        $loc = Model::factory('Kwalbum_Location')->load((int)$id[1]);
        if ($loc->id > 1 and isset($_POST['value']) and $_POST['value'] >= 0 and $_POST['value'] <= 2) {
            if ($field == 'name') {
                $loc->name_hide_level = (int)$_POST['value'];
            } else {
                $loc->coordinate_hide_level = (int)$_POST['value'];
            }
            $loc->save();
        }
        return $loc;
    }

    public function action_GetLocationNameHideLevel(): void
    {
        $this->getHidelevelOptions('name');
    }

    public function action_EditLocationNameHideLevel(): void
    {
        $loc = $this->setLocationHideLevel('name');
        echo $loc->name_hide_level_description;
        exit;
    }

    function action_GetLocationCoordinateHideLevel(): void
    {
        $this->getHidelevelOptions('coordinate');
    }

    public function action_EditLocationCoordinateHideLevel(): void
    {
        $loc = $this->setLocationHideLevel('coordinate');
        echo $loc->coordinate_hide_level_description;
        exit;
    }

    function action_DeleteLocation(): void
    {
        $this->_testPermission();
        Model::factory('Kwalbum_Location')
            ->load((int)$_POST['id'])
            ->delete();
        exit;
    }

    function action_EditPersonName(): void
    {
        $this->_testPermission();
        $person = (new Model_Kwalbum_Person)->load((int)$_POST['id']);
        if (!empty($_POST['value'])) {
            $person->name = trim($_POST['value']);
            $person->save();
        }
        echo $person->name;
        exit;
    }

    function action_DeletePerson(): void
    {
        $this->_testPermission();
        Model::factory('Kwalbum_Person')
            ->load((int)$_POST['id'])
            ->delete();
        exit;
    }

    function action_EditTagName(): void
    {
        $this->_testPermission();
        $tag = (new Model_Kwalbum_Tag)->load((int)$_POST['id']);
        if (!empty(trim($_POST['value']))) {
            $tag->name = trim($_POST['value']);
            $tag->save();
        }
        echo $tag->name;
        exit;
    }

    function action_DeleteTag(): void
    {
        $this->_testPermission();
        Model::factory('Kwalbum_Tag')
            ->load((int)$_POST['id'])
            ->delete();
        exit;
    }

    function action_GetUserPermission(): void
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
        $user = Model::factory('Kwalbum_User')->load((int)$id[1]);
        $perms['selected'] = $user->permission_level;
        echo json_encode($perms);
        exit;
    }

    function action_EditUserPermission(): void
    {
        $this->_testPermission();
        if (!empty($_POST['id'])) {
            $id = explode('_', $_POST['id']);
        }
        if (empty($id[1])) {
            echo 'Invalid id';
            exit;
        }
        $user = Model::factory('Kwalbum_User')->load((int)$id[1]);
        if (isset($_POST['value'])) {
            if ($user->id > 2 and $user->id != $this->user->id) {
                $user->permission_level = (int)$_POST['value'];
                $user->save();
            }
        }
        echo $user->permission_description;
        exit;
    }

    function action_DeleteUser(): void
    {
        $this->_testPermission();
        Model::factory('Kwalbum_User')
            ->load((int)$_POST['userid'])
            ->delete();
        exit;
    }

    private function _testPermission(): void
    {
        if ($this->user->is_admin) {
            return;
        }
        exit;
    }
}
