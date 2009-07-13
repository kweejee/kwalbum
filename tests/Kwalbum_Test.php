<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Kwalbum Test
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jul 13, 2009
 * @package kwalbum_test
 * @since 3.0 Jul 13, 2009
 */
class Kwalbum_Test extends Unit_Test_Case
{
	const DISABLED = FALSE;

	public function setup()
	{

	}

	public function first_user_exists_test()
	{
		$user = new User_Model();
		$this->assert_true($user->get_name(1));
	}
	public function invalid_user_does_not_exist_test()
	{
		$user = new User_Model();
		$this->assert_false($user->get_name(999999))
			 ->assert_false($user->get_name(-1))
			 ->assert_false($user->get_name('hi'));
	}


	public function teardown()
	{

	}
}