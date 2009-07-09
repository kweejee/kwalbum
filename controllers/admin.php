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


class Admin_Controller extends Kwalbum_Controller
{
	// allow to run in production
	const ALLOW_PRODUCTION = true;

	function index()
	{
		$this->template->content = new View('admin');
		$this->template->title = 'Admin Only';

	}


	function test()
	{

		$user = new User_Model();

		$this->template->content = $user->get_name(1);
		$this->template->title = 'Test';
	}
}
