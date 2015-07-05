<?php defined('SYSPATH') or die('No direct script access.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2014 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Sep 1, 2009
 */


class Controller_Browse extends Controller_Kwalbum
{
    public function before()
    {
        parent::before();

        if (!empty($_POST['kwalbum_mass_check'])) {
            if (!empty($_POST['loc'])) {
                $location = trim(htmlspecialchars($_POST['loc']));
            }
            if (!empty($_POST['vis'])) {
                $visibility = Kwalbum_ItemAdder::get_visibility($this->user);
            }
            if (!empty($_POST['tags_add'])) {
                $tags_to_add = explode(',', $_POST['tags_add']);
                foreach ($tags_to_add as $i => $tag) {
                    $tags_to_add[$i] = trim(htmlspecialchars($tag));
                }
            }
            if (!empty($_POST['tags_rem'])) {
                $tags_to_remove = explode(',', $_POST['tags_rem']);
                foreach ($tags_to_remove as $i => $tag) {
                    $tags_to_remove[$i] = trim(htmlspecialchars($tag));
                }
            }
            if (!empty($_POST['persons_add'])) {
                $persons_to_add = explode(',', $_POST['persons_add']);
                foreach ($persons_to_add as $i => $name) {
                    $persons_to_add[$i] = trim(htmlspecialchars($name));
                }
            }
            if (!empty($_POST['persons_rem'])) {
                $persons_to_remove = explode(',', $_POST['persons_rem']);
                foreach ($persons_to_remove as $i => $name) {
                    $persons_to_remove[$i] = trim(htmlspecialchars($name));
                }
            }
            foreach ($_POST['kwalbum_mass_check'] as $item_id) {
                $item = new Model_Kwalbum_Item($item_id);
                if (isset($location)) {
                    $item->location = $location;
                }
                if (isset($visibility)) {
                    $item->hide_level = $visibility;
                }
                if (!empty($tags_to_add)) {
                    $item->tags = array_merge($item->tags, $tags_to_add);
                }
                if (!empty($tags_to_remove)) {
                    $item->tags = array_diff($item->tags, $tags_to_remove);
                }

                if (!empty($persons_to_add)) {
                    $item->persons = array_merge($item->persons, $persons_to_add);
                }
                if (!empty($persons_to_remove)) {
                    $item->persons = array_diff($item->persons, $persons_to_remove);
                }
                $item->save();
            }
        }
    }

    public function action_index()
    {
        //echo Kohana::debug($this);
        if ($this->request->uri() == 'kwalbum' and
            !($this->location or $this->date or count($this->tags) > 0)
        ) {
            $this->template->content = new View('kwalbum/index');
            return;
        }

        //echo $this->request->route->uri(array('tags' => 'a,b', 'location' => 'd'));
        $view = new View('kwalbum/browse/index');
        $view->items = Model_Kwalbum_Item::get_thumbnails($this->page_number, $this->in_edit_mode);
        $this->template->content = $view;
        $this->template->title = 'browsing';
    }

    public function action_comments()
    {
        $view = new View('kwalbum/browse/comments');
        Model_Kwalbum_Comment::set_sort_field('create');
        Model_Kwalbum_Comment::set_sort_direction('DESC');
        $view->items = Model_Kwalbum_Comment::get_thumbnails($this->page_number, $this->in_edit_mode);
        $this->template->content = $view;
        $this->template->title = 'browsing newest comments';

    }

    public function action_popular()
    {
        $view = new View('kwalbum/browse/popular');
        Model_Kwalbum_Item::set_sort_field('count');
        Model_Kwalbum_Item::set_sort_direction('DESC');
        $view->items = Model_Kwalbum_Item::get_thumbnails($this->page_number, $this->in_edit_mode);
        $this->template->content = $view;
        $this->template->title = 'browsing most popular';
    }

    public function action_public()
    {
        $view = new View('kwalbum/browse/index');
        Model_Kwalbum_Item::append_where('hide_level', 0);
        $view->items = Model_Kwalbum_Item::get_thumbnails($this->page_number, $this->in_edit_mode);
        $this->template->content = $view;
        $this->template->title = 'browsing public permissioned';
    }

    public function action_member()
    {
        $view = new View('kwalbum/browse/index');
        Model_Kwalbum_Item::append_where('hide_level', 1);
        $view->items = Model_Kwalbum_Item::get_thumbnails($this->page_number, $this->in_edit_mode);
        $this->template->content = $view;
        $this->template->title = 'browsing member permissioned';
    }

    public function action_privileged()
    {
        $view = new View('kwalbum/browse/index');
        Model_Kwalbum_Item::append_where('hide_level', 2);
        $view->items = Model_Kwalbum_Item::get_thumbnails($this->page_number, $this->in_edit_mode);
        $this->template->content = $view;
        $this->template->title = 'browsing privileged permissioned';
    }

    public function action_contributor()
    {
        $view = new View('kwalbum/browse/index');
        Model_Kwalbum_Item::append_where('hide_level', 3);
        $view->items = Model_Kwalbum_Item::get_thumbnails($this->page_number, $this->in_edit_mode);
        $this->template->content = $view;
        $this->template->title = 'browsing contributor permissioned';
    }

    public function action_admin()
    {
        $view = new View('kwalbum/browse/index');
        Model_Kwalbum_Item::append_where('hide_level', 5);
        $view->items = Model_Kwalbum_Item::get_thumbnails($this->page_number, $this->in_edit_mode);
        $this->template->content = $view;
        $this->template->title = 'browsing admin permissioned';
    }
}