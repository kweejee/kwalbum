<?php
/**
 *
 *
 * @author Tim Redmond
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Aug 24, 2009
 */

class Controller_Ajax extends Controller_Kwalbum
{
    public function before(): void
    {
        if ($this->request->action() == 'upload' and
            isset($_POST['session_id'])) {
            session_id($_POST['session_id']);
        }

        $this->auto_render = false;

        parent::before();
    }

    public function action_GetInputLocations(): void
    {
        $this->_testPermission();
        $userInput = trim(@$_GET['term']);
        $locations = Model_Kwalbum_Location::get_name_array($this->user, 0, 20, 0, $userInput, '(loc.count+loc.child_count) DESC, p.name ASC, loc.name ASC');
        echo json_encode($locations);
    }

    public function action_SetLocation(): void
    {
        $item = (new Model_Kwalbum_Item)->load((int)$_POST['item']);
        $this->_testPermission($item);
        $item->location = trim($_POST['value'] ?? '');
        $item->save();
        echo $item->location;
    }

    public function action_SetDate(): void
    {
        $item = Model::factory('Kwalbum_Item')->load((int)$_POST['item']);
        $this->_testPermission($item);
        $date = Kwalbum_Helper::replaceBadDate($_POST['value'] . ' ' . $item->time);
        if ($item->visible_date == $item->sort_date)
            $item->sort_date = $date;
        $item->visible_date = $date;
        $item->save();
        echo $item->date;
    }

    public function action_SetTime(): void
    {
        $item = Model::factory('Kwalbum_Item')->load((int)$_POST['item']);
        $this->_testPermission($item);
        $date = Kwalbum_Helper::replaceBadDate($item->date . ' ' . $_POST['value']);
        if ($item->visible_date == $item->sort_date)
            $item->sort_date = $date;
        $item->visible_date = $date;
        $item->save();
        echo $item->time;
    }

    public function action_SetSortDate(): void
    {
        $item = Model::factory('Kwalbum_Item')->load((int)$_POST['item']);
        $this->_testPermission($item);
        $date = Kwalbum_Helper:: replaceBadDate($_POST['value']);
        $item->sort_date = $date;
        $item->save();
        echo $item->sort_date;
    }

    public function action_GetRawDescription(): void
    {
        $item = Model::factory('Kwalbum_Item')->load((int)$_GET['item']);
        $this->_testPermission($item);
        echo $item->description;
    }

    public function action_SetDescription(): void
    {
        $item = Model::factory('Kwalbum_Item')->load((int)$_POST['item']);
        $this->_testPermission($item);
        $item->description = trim($_POST['value'] ?? '');
        $item->save();
        echo $item->description;
    }

    public function action_GetVisibility(): void
    {
        $item = Model::factory('Kwalbum_Item')->load((int)$_GET['item']);
        $this->_testPermission($item);
        $vis = array();
        foreach (Model_Kwalbum_Item::$hide_level_names as $level => $name) {
            if ($this->user->permission_level >= $level) {
                $vis[] = $name;
            }
        }
        $vis['selected'] = $item->hide_level;
        echo json_encode($vis);
    }

    public function action_SetVisibility(): void
    {
        $item = Model::factory('Kwalbum_Item')->load((int)$_POST['item']);
        $this->_testPermission($item);
        $visibility = (int)(@ $_POST['value']);
        if ($visibility < 0) {
            $visibility = 0;
        } else
            if ($visibility > 3) {
                if ($this->user->is_admin)
                    $visibility = 5;
                else
                    $visibility = 3;
            }
        $item->hide_level = $visibility;
        $item->save();
        echo $item->hide_level_name;
    }

    public function action_GetInputTags(): void
    {
        $this->_getInputList(Model_Kwalbum_Tag::class);
    }

    public function action_SetTags(): void
    {
        $item = (new Model_Kwalbum_Item)->load((int)$_POST['item']);
        $this->_testPermission($item);
        $tags = explode(',', $_POST['value']);
        $item->set_tags($tags);
        $item->save();
        echo implode(',', $item->get_tags());
    }

    public function action_SetPersons(): void
    {
        $item = (new Model_Kwalbum_Item)->load((int)$_POST['item']);
        $this->_testPermission($item);
        $persons = explode(',', $_POST['value']);
        $item->set_persons($persons);
        $item->save();
        echo implode(',', $item->get_persons());
    }

    public function action_GetInputPersons(): void
    {
        $this->_getInputList(Model_Kwalbum_Person::class);
    }

    public function action_SetEditMode(): void
    {
        if (!$this->user->can_edit)
            $_POST['edit'] = false;
        session_start();
        $_SESSION['kwalbum_edit'] = (bool)$_POST['edit'];
        session_write_close();
        echo 1;
    }

    public function action_AddComment(): void
    {
        $item = Model::factory('Kwalbum_Item')->load((int)$_POST['item']);
        if (!$this->user->can_view_item($item)) {
            echo 'no commenting for you';
            return;
        }
        $comment = new Model_Kwalbum_Comment();
        $comment->name = $this->user->name;
        $comment->text = htmlspecialchars(trim($_POST['comment']));
        $comment->item_id = $item->id;
        $comment->save();
        echo $comment->name . ' : ' . $comment->date . '<br/>' . $comment->text . '<hr/>';
    }

    public function action_DeleteItem(): void
    {
        if (empty($_POST['item'])) {
            echo 0;
            return;
        }
        $item = Model::factory('Kwalbum_Item')->load((int)$_POST['item']);
        if (!$this->user->can_edit_item($item)) {
            echo 0;
            return;
        }
        $item->delete();
        echo 1;
    }

    public function action_RotateItem(): void
    {
        if (empty($_POST['item']) || empty($_POST['degrees'])) {
            echo 0;
            return;
        }
        $item = Model::factory('Kwalbum_Item')->load((int)$_POST['item']);
        if (!$this->user->can_edit_item($item)) {
            echo 0;
            return;
        }
        $item->rotate((int)$_POST['degrees']);
        echo 1;
    }

    /**
     * Handle file upload request and echo "success" or json of errors
     *
     * @return null
     */
    public function action_upload(): void
    {
        if (!$this->user->is_logged_in) {
            if (!isset($_SERVER['PHP_AUTH_USER'])) {
                header('WWW-Authenticate: Basic realm="Upload"');
                header('HTTP/1.1 401 Unauthorized');
                die('Invalid login');
            }
            $this->user = Model_Kwalbum_User::login(
                $_SERVER['PHP_AUTH_USER'],
                $_SERVER['PHP_AUTH_PW']);
            if (!$this->user) {
                die('Invalid login');
            }
        }
        if (!$this->user->can_add) {
            $this->response->status(401); // Unauthorized
            die('You do not have permission to add items');
        }

        if (!empty($_FILES)) {
            $adder = new Kwalbum_ItemAdder($this->user);
            $response_body = [];
            try {
                if (isset($_FILES['file'])) {
                    $result = $adder->save_upload($_FILES['file']);
                    if (is_string($result)) {
                        $this->response->status(400);
                        $response_body['errors'] = [$result];
                    } else {
                        $response_body['files'] = [[
                            'visibleDate' => $result->visible_date,
                            'name' => $result->filename,
                            'thumbnailUrl' => $result->getThumbnailURL($this->url),
                            'url' => "{$this->url}/~{$result->id}",
                        ]];
                    }
                }
            } catch (Exception $e) {
                error_log($e);
                $this->response->status(500);
                $response_body['errors'] = [$e->getMessage()];
            }
        } else {
            $this->response->status(400);
            $response_body['errors'] = ['No files sent'];
        }
        $this->response->headers('Content-Type', File::mime_by_ext('json'));
        echo json_encode($response_body);
    }

    private function _testPermission($item = null): void
    {
        if ($item) {
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

    private function _getInputList($class): void
    {
        $this->_testPermission();
        $input_terms = explode(',', Kwalbum_Model::htmlspecialchars(trim($_GET['term'] ?? '')));
        if (!$size = count($input_terms))
            exit;

        $other_input_terms = '';
        $not_included = [];

        // trim all terms passed in and exclude all but the last for searching
        $term = '';
        for ($i = 0; $i < $size; $i++) {
            $term = trim($input_terms[$i]);
            if (!empty($term) && $i < $size - 1) {
                $other_input_terms .= $term . ',';
                $not_included[] = $term;
            }
        }
        if (!$term) // term used for search has nothing so don't search
            return;

        if ($class == Model_Kwalbum_Tag::class) {
            $found_terms = Model_Kwalbum_Tag::get_name_array(0, 10, 0, $term, 'count DESC', $not_included);
        } elseif ($class == Model_Kwalbum_Person::class) {
            $found_terms = Model_Kwalbum_Person::get_name_array(0, 10, 0, $term, 'count DESC', $not_included);
        } else {
            throw new Exception('Unsupported term type');
        }

        $output_terms = [];
        foreach ($found_terms as $found_term) {
            $output_terms[] = $other_input_terms . $found_term;
        }
        echo json_encode($output_terms);
    }

    public function action_GetResizedImage(): void
    {
        if (empty($_GET['id'])) {
            echo 'missing id';
            exit;
        }
        $item = new Model_Kwalbum_Item($_GET['id']);

        $resizedview = new View('kwalbum/item/resized');
        $resizedview->item = $item;
        $data = [
            'id' => $item->id,
            'type' => $item->type,
            'img_html' => $resizedview->render(),
            'description' => $item->description,
            'next_id' => $item->getNextItem()->id,
            'prev_id' => $item->getPreviousItem()->id,
        ];
        echo json_encode($data);
    }
}
