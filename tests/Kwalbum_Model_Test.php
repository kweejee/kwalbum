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
	public function add_and_delete_person_test()
	{
		$person = new Kwalbum_Person_Model();
		$person->name = 'Me';
		$this->assert_empty($person->id);
		$person->save();
		$this->assert_not_empty($person->id);
		$person->delete();
		$this->assert_empty($person->id);
	}
	public function add_and_delete_tag_test()
	{
		$tag = new Kwalbum_Tag_Model();
		$tag->name = 'blue sky';
		$this->assert_empty($tag->id);
		$tag->save();
		$this->assert_not_empty($tag->id);
		$tag->delete();
		$this->assert_empty($tag->id);
	}

	public function add_and_delete_item_test()
	{
		// create location if does not exist yet
		$location = ORM::factory('kwalbum_location')->where('name', 'Item Is Here')->find();
		if (!$location->loaded)
		{
			$location->name = 'Item Is Here';
			$location->save();
		}
		$this->assert_not_empty($location->id);
		$count = $location->count;
		$item = new Kwalbum_Item_Model();
		$item->user_id = 1;
		$item->location_id = $location->id;
		$this->assert_empty($item->id);
		$item->save();
		$location->reload();
		$this->assert_same($count+1, $location->count);
		$this->assert_not_empty($item->id);
		$item->delete();
		$location->reload();
		$this->assert_same($count, $location->count);
		$this->assert_empty($item->id);
	}

	public function item_tag_test()
	{
		$item = ORM::factory('kwalbum_item');
		$item->user_id = 1;
		$item->location_id = 1;
		$item->save();
		$this->assert_not_empty($item->id);

		$tag = ORM::factory('kwalbum_tag');
		$tag->name = 'tree tag';
		$tag->save();
		$this->assert_equal(0,$tag->count);
		$item->add($tag);

		$tag2 = ORM::factory('kwalbum_tag');
		$tag2->name = 'second tag thing';
		$tag2->save();
		$item->add($tag2);
		$item->save();

		$this->assert_equal($tag->name, $item->tags[0]->name);
		$this->assert_not_empty($item->tags[1]);
		$tag->reload();
		$tag2->reload();
		$this->assert_equal(1, $tag->count);
		$this->assert_equal(1, $tag2->count);

		$item->remove($tag);
		$item->save();
		$item->add($tag2);
		$item->save();
		$tag->reload();
		$tag2->reload();
		$this->assert_equal(0,$tag->count);
		$this->assert_equal(1,$tag2->count);
		$this->assert_empty($item->tags[1]);
	}

	public function delete_tag_of_item_test()
	{
		$item = ORM::factory('kwalbum_item');
		$item->user_id = 1;
		$item->location_id = 1;
		$item->save();
		$tag = ORM::factory('kwalbum_tag');
		$tag->name = 'tree tag';
		$tag->save();
		$item->add($tag);
		$tag2 = ORM::factory('kwalbum_tag');
		$tag2->name = 'second tag thing';
		$tag2->save();
		$item->add($tag2);
		$item->save();
		$this->assert_not_empty($item->tags[0]);
		$this->assert_not_empty($item->tags[1]);

		$tag->delete();
		$item->reload();
		$this->assert_not_empty($item->tags[0]);
		$this->assert_empty($item->tags[1]);
	}

	public function delete_item_with_tag_test()
	{
		$tag = ORM::factory('kwalbum_tag');
		$tag->name = 'tree tag';
		$tag->save();
		$item = ORM::factory('kwalbum_item');
		$item->user_id = 1;
		$item->location_id = 1;
		$item->add($tag);
		$item->save();
		$item2 = ORM::factory('kwalbum_item');
		$item2->user_id = 1;
		$item2->location_id = 1;
		$item2->add($tag);
		$item2->save();
		$tag->reload();
		$this->assert_equal(2, $tag->count);

		$item->delete();
		$tag->reload();
		$this->assert_equal(1, $tag->count);
		$item2->delete();
		$tag->reload();
		$this->assert_equal(0, $tag->count);
	}

	public function item_person_test()
	{
		$item = ORM::factory('kwalbum_item');
		$item->user_id = 1;
		$item->location_id = 1;
		$item->save();
		$this->assert_not_empty($item->id);

		$person1 = ORM::factory('kwalbum_person');
		$person1->name = 'person 1';
		$person1->save();
		$this->assert_equal(0,$person1->count);
		$item->add($person1);

		$person2 = ORM::factory('kwalbum_person');
		$person2->name = 'second person';
		$person2->save();
		$item->add($person2);
		$item->save();

		$this->assert_equal($person1->name, $item->persons[0]->name);
		$this->assert_not_empty($item->persons[1]);
		$person1->reload();
		$person2->reload();
		$this->assert_equal(1, $person1->count);
		$this->assert_equal(1, $person2->count);

		$item->remove($person1);
		$item->save();
		$item->add($person2);
		$item->save();
		$person1->reload();
		$person2->reload();
		$this->assert_equal(0,$person1->count);
		$this->assert_equal(1,$person2->count);
		$this->assert_empty($item->persons[1]);

		$item->add($person1);
		$item->save();
		$person1->reload();
		$this->assert_equal(1, $person1->count);
	}

	public function delete_person_of_item_test()
	{
		$item = ORM::factory('kwalbum_item');
		$item->user_id = 1;
		$item->location_id = 1;
		$item->save();
		$person1 = ORM::factory('kwalbum_person');
		$person1->name = 'person 1';
		$person1->save();
		$item->add($person1);
		$person2 = ORM::factory('kwalbum_person');
		$person2->name = 'second person';
		$person2->save();
		$item->add($person2);
		$item->save();
		$this->assert_not_empty($item->persons[0]);
		$this->assert_not_empty($item->persons[1]);

		$person2->delete();
		$item->reload();
		$person1->reload();
		$this->assert_not_empty($item->persons[0]);
		$this->assert_empty($item->persons[1]);
		$this->assert_equal(1, $person1->count);

		$item->remove($person1);
		$item->save();
		$person1->reload();
		$this->assert_empty($item->persons[0]);
		$this->assert_equal(0, $person1->count);
	}

	public function delete_item_with_person_test()
	{
		$person1 = ORM::factory('kwalbum_person');
		$person1->name = 'person 1';
		$person1->save();
		$item = ORM::factory('kwalbum_item');
		$item->user_id = 1;
		$item->location_id = 1;
		$item->add($person1);
		$item->save();
		$item2 = ORM::factory('kwalbum_item');
		$item2->user_id = 1;
		$item2->location_id = 1;
		$item2->add($person1);
		$item2->save();
		$person1->reload();
		$this->assert_equal(2, $person1->count);

		$item->delete();
		$person1->reload();
		$this->assert_equal(1, $person1->count);
		$item2->delete();
		$person1->reload();
		$this->assert_equal(0, $person1->count);
	}

	public function delete_location_of_item_test()
	{
		$location = new Kwalbum_Location_Model();
		$location->name = 'Here';
		$location->save();
		$item = ORM::factory('kwalbum_item');
		$item->user_id = 1;
		$item->location_id = $location->id;
		$item->save();
		$item2 = ORM::factory('kwalbum_item');
		$item2->user_id = 1;
		$item2->location_id = $location->id;
		$item2->save();

		$default_location = ORM::factory('kwalbum_location', 1);
		// sanity check that previous tests did not change the count
		$this->assert_equal(0, $default_location->count);

		$location->delete();

		$item->reload();
		$this->assert_equal(1, $item->location_id);
		$default_location->reload();
		$this->assert_equal(2, $default_location->count);
	}

	public function delete_user_of_item_test()
	{
		$user = new Kwalbum_User_Model();
		$user->name = 'Tester Personer';
		$user->save();
		$item = new Kwalbum_Item_Model();
		$item->user_id = $user->id;
		$item->location_id = 1;
		$item->save();
		$user->delete();

		$item->reload();
		$this->assert_equal(100, $item->hide_level);
	}


	public function external_site_test()
	{
		$site = new Kwalbum_Site_Model;
		$site->url = 'http://example.com';
		$site->key = 'k3y';
		$site->save();
		$id = $site->id;
		$site->clear();

		$site = ORM::factory('kwalbum_site',$id);
		$this->assert_equal('k3y', $site->key);
	}

	public function external_item_test()
	{
		$site = new Kwalbum_Site_Model;
		$site->url = 'http://example.com';
		$site->key = 'k3y';
		$site->save();
		$item = new Kwalbum_Item_Model;
		$item->user_id = 1;
		$item->location_id = 1;
		$item->save();
		$this->assert_false($item->is_external);

		$itemsite = new Kwalbum_Items_Site_Model;
		$itemsite->site_id=$site->id;
		//$itemsite->save();

		$item->add($itemsite);
		$item->save();
exit;
		$this->assert_true(false);
	}

	public function add_and_delete_comment_test()
	{
		$item = new Kwalbum_Item_Model;
		$item->user_id = 1;
		$item->location_id = 1;
		$item->save();
		$this->assert_false($item->has_comments);

		$comment = new Kwalbum_Comment_Model;
		$comment->item_id = $item->id;
		$comment->name = 'Test Name';
		$comment->text = 'Test Text';
		$comment->ip = '192.168.0.1';
		$comment->save();

		$comment2 = new Kwalbum_Comment_Model;
		$comment2->item_id = $item->id;
		$comment2->name = 'Test Name 2';
		$comment2->text = 'Test Text 2';
		$comment2->ip = '192.168.0.2';
		$comment2->save();

		$item->reload();
		$this->assert_true($item->has_comments);
		$this->assert_equal('Test Name', $item->comments[0]->name);
		$this->assert_equal('Test Text', $item->comments[0]->text);
		$this->assert_equal('192.168.0.1', $item->comments[0]->ip);
		$this->assert_equal('Test Name 2', $item->comments[1]->name);
		$this->assert_equal('Test Text 2', $item->comments[1]->text);
		$this->assert_equal('192.168.0.2', $item->comments[1]->ip);

		$item->comments[0]->delete();
		$item->reload();
		$this->assert_true($item->has_comments);
		$this->assert_equal('Test Name 2', $item->comments[0]->name);
		$this->assert_equal('Test Text 2', $item->comments[0]->text);
		$this->assert_equal('192.168.0.2', $item->comments[0]->ip);

		$comment2->delete();
		$item->reload();
		$this->assert_false($item->has_comments);

	}
	public function always_pass_test()
	{

	}

	public function teardown()
	{
		$db = Database::instance();
		$sql = 'DELETE FROM `kwalbum_comments`';
		$db->query($sql);
		$sql = 'DELETE FROM `kwalbum_favorites`';
		$db->query($sql);
		$sql = 'DELETE FROM `kwalbum_items_kwalbum_tags`';
		$db->query($sql);
		$sql = 'DELETE FROM `kwalbum_items_kwalbum_persons`';
		$db->query($sql);
		$sql = 'DELETE FROM `kwalbum_items_kwalbum_sites`';
		$db->query($sql);
		$sql = 'DELETE FROM `kwalbum_tags`';
		$db->query($sql);
		$sql = 'DELETE FROM `kwalbum_persons`';
		$db->query($sql);
		$sql = 'DELETE FROM `kwalbum_sites`';
		$db->query($sql);
		$sql = 'DELETE FROM `kwalbum_items`';
		$db->query($sql);
		$sql = 'DELETE FROM `kwalbum_users` WHERE id > 2';
		$db->query($sql);
		$sql = 'DELETE FROM `kwalbum_locations` WHERE id > 1';
		$db->query($sql);
		$location = ORM::factory('kwalbum_location', 1);
		$location->count = 0;
		$location->save();
	}
}