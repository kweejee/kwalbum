<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jun 30, 2009
 * @package kwalbum
 * @since 3.0 Jun 30, 2009
 */
class Controller_Admin extends Controller_Kwalbum
{
    // allow to run in production
    const ALLOW_PRODUCTION = true;

    function action_index(): void
    {
        if (!$this->_testAdmin())
            return;
        $this->template->content = new View('kwalbum/admin');
        $this->template->title = 'Admin Options';

    }

    function action_locations(): void
    {
        if (!$this->_testAdmin())
            return;
        $this->template->content = new View('kwalbum/admin/locations');
        $this->template->title = 'Edit Locations';
    }

    function action_tags(): void
    {
        if (!$this->_testAdmin())
            return;
        $this->template->content = new View('kwalbum/admin/tags');
        $this->template->title = 'Edit Tags';
    }

    function action_people(): void
    {
        if (!$this->_testAdmin())
            return;
        $this->template->content = new View('kwalbum/admin/people');
        $this->template->title = 'Edit People';
    }

    function action_users(): void
    {
        if (!$this->_testAdmin())
            return;
        $this->template->content = new View('kwalbum/admin/users');
        $this->template->title = 'Edit Users';
    }

    private function _testAdmin(): bool
    {
        if ($this->user->is_admin)
            return true;

        $this->template->content = 'Admin Only';
        $this->template->title = 'Admin Only';
        return false;
    }
}
