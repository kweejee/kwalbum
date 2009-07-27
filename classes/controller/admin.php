<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jun 30, 2009
 * @package kwalbum
 * @since 3.0 Jun 30, 2009
 */


class Controller_Admin extends Controller_Kwalbum
{
	// allow to run in production
	const ALLOW_PRODUCTION = true;

	function action_index()
	{
		$this->template->content = new View('kwalbum/admin');
		$this->template->title = 'Admin Only';

	}


	function action_test()
	{

		$user = ORM::factory('kwalbum_user')->find(1);

		$this->template->content = $user->name;
		$this->template->title = 'Test';
	}
}
