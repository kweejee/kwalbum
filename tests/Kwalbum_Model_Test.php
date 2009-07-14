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
class Kwalbum_Model_Test extends Unit_Test_Case
{
	const DISABLED = FALSE;
	protected $test_id = 1;

	public function setup()
	{
	}

	public function add_and_delete_user_test()
	{
		$user = new Kwalbum_User_Model();
		$user->name = 'Test Name';
		$user->openid = 'testid@example.com';
		$user->permission_level = 1;
		$this->assert_empty($user->id);
		$user->save();
		$this->assert_not_empty($user->id);
		$user->delete();
		$this->assert_empty($user->id);
	}

	public function invalid_user_test()
	{
		$user = new Kwalbum_User_Model();
		$this->assert_false_strict($user->find(999999)->loaded)
			->assert_false_strict($user->find(-1)->loaded)
			->assert_false_strict($user->find('hi')->loaded)
			->assert_true_strict($user->find(1)->loaded);
	}

	public function add_and_delete_location_test()
	{
		$location = new Kwalbum_Location_Model();
		$location->name = 'Here';
		$this->assert_empty($location->id);
		$location->save();
		$this->assert_not_empty($location->id);
		$location->delete();
		$this->assert_empty($location->id);
	}

	public function add_and_delete_item_test()
	{
		$user = new Kwalbum_User_Model();
		$user->name = 'Item Tester';
		$user->openid = 'testid@example.com';
		$user->permission_level = 4;
		$user->save();

		$location = new Kwalbum_Location_Model();
		$location->name = 'Item Is Here';
		$location->save();

		$item = new Kwalbum_Item_Model();
		$item->user_id = $user->id;
		$item->location_id = $location->id;
		$this->assert_empty($item->id);
		$item->save();
		$this->assert_not_empty($item->id);
		$item->delete();
		$this->assert_empty($item->id);

		$location->delete();
		$user->delete();
	}

	public function teardown()
	{

	}
}