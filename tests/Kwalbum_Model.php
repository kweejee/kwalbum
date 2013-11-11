<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Kwalbum Test
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jul 13, 2009
 * @package kwalbum_test
 * @since 3.0 Jul 13, 2009
 */
class UnitTest_Kwalbum_Model extends UnitTest_Case
{
	const DISABLED = FALSE;
	protected $test_id = 1;

	public function setup()
	{
	}

	public function test_add_and_delete_user()
	{
		$user = Model::factory('kwalbum_user');
		$user->login_name = 'loginname';
		$user->name = 'Test Name';
		$user->email = 'testid@example.com';
		$user->password = 'password';
		$this->assert_empty($user->id);
		$this->assert_false($user->loaded);
		$user->save();
		$this->assert_not_empty($user->id);
		$this->assert_true($user->loaded);

		$user->reload();
		$this->assert_equal($user->name, 'Test Name');
		$this->assert_equal($user->login_name, 'loginname');
		$this->assert_equal($user->email, 'testid@example.com');
		$this->assert_true($user->password_equals('password'));
		$this->assert_equal($user->permission_level, 1);
		$this->assert_not_empty($user->visit_date);
		$this->assert_equal($user->visit_date, '0000-00-00 00:00:00');

		$user->permission_level = 2;
		$user->save();

		$user->load('Test Name', 'name');
		$this->assert_equal($user->name, 'Test Name');
		$this->assert_equal($user->email, 'testid@example.com');
		$this->assert_true($user->password_equals('password'));
		$this->assert_equal($user->permission_level, 2);
		$this->assert_not_empty($user->visit_date);
		$this->assert_equal($user->visit_date, '0000-00-00 00:00:00');

		$user->delete();
		$this->assert_empty($user->id);
	}

	public function test_invalid_user()
	{
		$user = Model::factory('kwalbum_user');
		$this->assert_false($user->load(999999)->loaded);
		$this->assert_false($user->load(-1)->loaded);
		$this->assert_false($user->load('hi')->loaded);
		$this->assert_true($user->load(1)->loaded);
	}

	public function test_user_permissions()
	{
		$user = Model::factory('kwalbum_user');
		$user->name = 'Test Name';
		$user->email = 'testid@example.com';
		$user->save();

		$item = Model::factory('kwalbum_item');
		$item->type = 'png';
		$item->user_id = 1;
		$item->description = 'd escription';
		$item->path = 'p ath';
		$item->filename = 'f ilename';
		$item->save();

		// default can not edit
		$this->assert_false($user->can_edit);

		// 4 can edit all
		$user->permission_level = 4;
		$this->assert_true($user->can_edit);
		$this->assert_true($user->can_edit_item($item));

		// 3 can edit only what they own
		$user->permission_level = 3;
		$this->assert_true($user->can_edit);
		$this->assert_false($user->can_edit_item($item));
		$item->user_id = $user->id;
		$this->assert_true($user->can_edit);
		$this->assert_true($user->can_edit_item($item));

		// 2 can not edit
		$user->permission_level = 2;
		$this->assert_false($user->can_edit);
		$this->assert_false($user->can_edit_item($item));
	}

	public function test_add_and_delete_location()
	{
		$location = Model::factory('kwalbum_location');
		$location->name = 'Here';
		$this->assert_empty($location->id);
		$location->save();
		$this->assert_not_empty($location->id);

		$location->reload();
		$this->assert_equal($location->name, 'Here');
		$this->assert_similar($location->latitude, 0.0);
		$this->assert_similar($location->longitude, 0.0);
		$this->assert_equal($location->count, 0);

		$location->latitude = 1.234567;
		$location->longitude = 1.234567;
		$location->save();

		$location->reload();
		$this->assert_similar($location->latitude, 1.234567);
		$this->assert_similar($location->longitude, 1.234567);

		$location->delete();
		$this->assert_empty($location->id);
	}

	public function test_add_and_delete_person()
	{
		$person = Model::factory('kwalbum_person');
		$person->name = 'Me';
		$this->assert_empty($person->id);
		$person->save();
		$this->assert_not_empty($person->id);

		$person->reload();
		$this->assert_equal($person->name, 'Me');
		$this->assert_equal($person->count, 0);

		$person->delete();
		$this->assert_empty($person->id);
	}

	public function test_add_and_delete_tag()
	{
		$tag = Model::factory('kwalbum_tag');
		$tag->name = 'blue sky';
		$this->assert_empty($tag->id);
		$tag->save();
		$this->assert_not_empty($tag->id);

		$tag->reload();
		$this->assert_equal($tag->name, 'blue sky');
		$this->assert_equal($tag->count, 0);

		$tag->delete();
		$this->assert_empty($tag->id);
	}

	public function test_add_and_delete_item()
	{
		$location = Model::factory('kwalbum_location');
		$location->name = 'Item Is Here';
		$location->save();
		$this->assert_not_empty($location->id);
		$count = $location->count;
		$unknown_location = Model::factory('kwalbum_location')->load(1);
		$unknown_count = $unknown_location->count;

		$item = Model::factory('kwalbum_item');
		$item->type = 'jpeg';
		$item->user_id = 1;
		$item->description = 'd escription';
		$item->path = 'p ath';
		$item->filename = 'f ilename';
		$this->assert_empty($item->id);
		$item->save();

		$create_date = $item->create_date;

		$location->reload();
		$unknown_location->reload();
		$this->assert_equal($location->count, $count);
		$this->assert_equal($unknown_location->count, $unknown_count+1);

		$item->reload();
		$this->assert_not_empty($item->id);
		$this->assert_equal($item->location, $unknown_location->name);
		$this->assert_equal($item->count, 0);
		$this->assert_equal($item->user_id, 1);
		$this->assert_equal($item->type, 'jpeg');
		$this->assert_equal($item->user_id, 1);
		$this->assert_equal($item->description, 'd escription');
		$this->assert_equal($item->path, Kwalbum_Model::get_config('item_path').'p ath');
		$this->assert_equal($item->filename, 'f ilename');
		$this->assert_similar($item->latitude, 0.0);
		$this->assert_similar($item->longitude, 0.0);
		$this->assert_equal($item->create_date, $create_date);
		$this->assert_equal($item->update_date, $create_date);
		$this->assert_equal($item->visible_date, $create_date);
		$this->assert_equal($item->sort_date, $create_date);
		$this->assert_similar($item->latitude, 0);
		$this->assert_similar($item->longitude, 0);
		$this->assert_false($item->has_comments);
		$this->assert_equal($item->hide_level, 0);

		sleep(1); // make sure create_date and update_date can be different
		$item->location = $location->name;
		$item->count++;
		$item->visible_date = '2008-09-09 09:09:09';
		$item->sort_date = '2008-09-09 00:00:00';
		$item->latitude = 1.234567;
		$item->longitude = 1.234567;
		$item->hide_level = 1;
		$item->save();

		$item->reload();
		$this->assert_equal($item->location, $location->name);
		$this->assert_equal($item->count, 1);
		$this->assert_equal($item->create_date, $create_date);
		$this->assert_not_equal($item->update_date, $create_date);
		$this->assert_equal($item->visible_date, '2008-09-09 09:09:09');
		$this->assert_equal($item->sort_date, '2008-09-09 00:00:00');
		$this->assert_similar($item->latitude, 1.234567);
		$this->assert_similar($item->longitude, 1.234567);
		$this->assert_equal($item->hide_level, 1);

		$location->reload();
		$unknown_location->reload();
		$this->assert_equal($location->count, $count+1);
		$this->assert_equal($unknown_location->count, $unknown_count);

		$item->delete();
		$location->reload();
		$unknown_location->reload();
		$this->assert_empty($item->id);
		$this->assert_similar($location->count, $count);
		$this->assert_equal($unknown_location->count, $unknown_count);
	}

	public function test_item_tag()
	{
		$item = Model::factory('kwalbum_item');
		$item->user_id = 1;
		$item->location = 'Here';
		$item->save();
		$this->assert_not_empty($item->id);

		$tag = Model::factory('kwalbum_tag');
		$tag->name = 'tree tag';
		$tag->save();

		$this->assert_similar($tag->count, 0);

		$tag2 = Model::factory('kwalbum_tag');
		$tag2->name = 'second tag thing';
		$tag2->save();

		$item->tags = $tag;
		$item->tags = $tag2;
		$item->save();

		$t = $item->tags[0];
		$this->assert_similar($t, $tag->name);
		$t = $item->tags[1];
		$this->assert_not_empty($t);
		$this->assert_similar($t, $tag2->name);
		$tag->reload();
		$tag2->reload();

		$this->assert_similar($tag->count, 1);
		$this->assert_similar($tag2->count, 1);

		$item->tags = array($tag, $tag);
		$item->tags = $tag;
		$item->save();
		$tag->reload();
		$tag2->reload();
		$this->assert_similar($tag->count, 1);
		$this->assert_similar($tag2->count, 0);
	}

	public function test_delete_tag_of_item()
	{
		$item = Model::factory('kwalbum_item');
		$item->user_id = 1;
		$item->location_id = 1;
		$item->save();

		$tag = Model::factory('kwalbum_tag');
		$tag->name = 'tree tag';
		$tag->save();
		$item->tags = $tag;
		$this->assert_equal(sizeof($item->tags), 1);

		$tag2 = Model::factory('kwalbum_tag');
		$tag2->name = 'second tag thing';
		$item->tags = $tag2;
		$item->save();
		$this->assert_equal(sizeof($item->tags), 2);

		$tag->delete();
		$item->reload();
		$this->assert_equal(sizeof($item->tags), 1);
		$this->assert_equal($item->tags[0], $tag2->name);
	}

	public function test_delete_item_with_tag()
	{
		$tag = Model::factory('kwalbum_tag');
		$tag->name = 'tree tag';
		$tag->save();
		$item = Model::factory('kwalbum_item');
		$item->user_id = 1;
		$item->location_id = 1;
		$item->tags = $tag;
		$item->save();
		$item2 = Model::factory('kwalbum_item');
		$item2->user_id = 1;
		$item2->location_id = 1;
		$item2->tags = $tag;
		$item2->save();
		$tag->reload();
		$this->assert_equal($tag->count, 2);

		$item->delete();
		$tag->reload();
		$this->assert_equal($tag->count, 1);
		$item2->delete();
		$tag->reload();
		$this->assert_equal($tag->count, 0);
	}

	public function test_item_person()
	{
		$item = Model::factory('kwalbum_item');
		$item->user_id = 1;
		$item->location_id = 1;
		$item->save();
		$this->assert_not_empty($item->id);

		$person1 = Model::factory('kwalbum_person');
		$person1->name = 'person 1';
		$person1->save();
		$this->assert_equal($person1->count, 0);
		$item->persons = array($person1);
		$item->save();

		$person2 = Model::factory('kwalbum_person');
		$person2->name = 'second person';
		$person2->save();

		$this->assert_equal($item->persons[0], $person1->name);
		$this->assert_equal(sizeof($item->persons), 2);
		$person1->reload();
		$person2->reload();
		$this->assert_equal($person1->count, 1);
		$this->assert_equal($person2->count, 1);

		$item->persons = array($person2, $person2);
		$item->save();
		$person1->reload();
		$person2->reload();
		$this->assert_equal($person1->count, 0);
		$this->assert_equal($person2->count, 1);
		$this->assert_equal(sizeof($item->persons), 1);

		$item->persons = $person1;
		$item->save();
		$person1->reload();
		$this->assert_equal($person1->count, 1);
		$this->assert_equal(sizeof($item->persons), 2);
	}

	public function test_delete_person_of_item()
	{
		$item = Model::factory('kwalbum_item');
		$item->user_id = 1;
		$item->location_id = 1;
		$item->save();
		$person1 = Model::factory('kwalbum_person');
		$person1->name = 'person 1';
		$person1->save();
		$person2 = Model::factory('kwalbum_person');
		$person2->name = 'second person';
		$person2->save();
		$item->persons = array($person1, $person2);
		$item->save();
		$this->assert_equal(sizeof($item->persons), 2);

		$person1->delete();
		$item->reload();
		$this->assert_equal(sizeof($item->persons), 1);
		$this->assert_equal($item->persons[0], $person2->name);
	}

	public function test_delete_item_with_person()
	{
		$person1 = Model::factory('kwalbum_person');
		$person1->name = 'person 1';
		$person1->save();
		$item = Model::factory('kwalbum_item');
		$item->user_id = 1;
		$item->location_id = 1;
		$item->persons = $person1;
		$item->save();
		$item2 = Model::factory('kwalbum_item');
		$item2->user_id = 1;
		$item2->location_id = 1;
		$item2->persons = $person1;
		$item2->save();
		$person1->reload();
		$this->assert_equal($person1->count, 2);

		$item->delete();
		$person1->reload();
		$this->assert_equal($person1->count, 1);
		$item2->delete();
		$person1->reload();
		$this->assert_equal($person1->count, 0);
	}

	public function test_delete_location_of_item()
	{
		$location = Model::factory('kwalbum_location');
		$location->name = 'Delete Test Location';
		$location->save();
		$item = Model::factory('kwalbum_item');
		$item->user_id = 1;
		$item->location = $location->name;
		$item->save();
		$item2 = Model::factory('kwalbum_item');
		$item2->user_id = 1;
		$item2->location = $location->name;
		$item2->save();

		$location->reload();
		$this->assert_equal($location->count, 2);

		$default_location = Model::factory('kwalbum_location')->load(1);
		$starting_count = $default_location->count;
		$location->delete();

		$default_location->reload();
		$this->assert_equal($default_location->count, $starting_count+2);

		$item->reload();
		$this->assert_equal($item->location, (string)$default_location);
	}

	public function test_delete_user_of_item()
	{
		$user = Model::factory('kwalbum_user');
		$user->name = 'Tester Personer';
		$user->save();
		$item = Model::factory('kwalbum_item');
		$item->user_id = $user->id;
		$item->location = Model::factory('kwalbum_location')->load(1);
		$item->save();
		$user->delete();

		$item->reload();
		$this->assert_equal($item->user_id, 2);
		$this->assert_equal($item->hide_level, 100);
	}

	public function test_add_and_delete_comment_test()
	{
		$item = Model::factory('kwalbum_item');
		$item->user_id = 1;
		$item->location_id = 1;
		$item->save();
		$this->assert_false($item->has_comments);

		$comment = Model::factory('kwalbum_comment');
		$comment->item_id = $item->id;
		$comment->name = 'Test Name';
		$comment->text = 'Test Text';
		$comment->ip = '192.168.0.1';
		$comment->save();

		$comment2 = Model::factory('kwalbum_comment');
		$comment2->item_id = $item->id;
		$comment2->name = 'Test Name 2';
		$comment2->text = 'Test Text 2';
		$comment2->ip = '192.168.0.2';
		$comment2->save();

		$item->reload();
		$this->assert_true($item->has_comments);
		$this->assert_equal($item->comments[0]->name, 'Test Name');
		$this->assert_equal($item->comments[0]->text, 'Test Text');
		$this->assert_equal($item->comments[0]->ip, '192.168.0.1');
		$this->assert_equal($item->comments[1]->name, 'Test Name 2');
		$this->assert_equal($item->comments[1]->text, 'Test Text 2');
		$this->assert_equal($item->comments[1]->ip, '192.168.0.2');

		$item->comments[0]->delete();
		$item->reload();

		$this->assert_true($item->has_comments);
		$this->assert_equal($item->comments[0]->name, 'Test Name 2');
		$this->assert_equal($item->comments[0]->text, 'Test Text 2');
		$this->assert_equal($item->comments[0]->ip, '192.168.0.2');

		$comment2->delete();
		$item->reload();
		$this->assert_false($item->has_comments);

	}
	public function test_always_pass_test()
	{

	}

	public function teardown()
	{
		$db = Database::instance();
		$sql = 'DELETE FROM `kwalbum_comments`';
		$db->query(Database::DELETE, $sql);
		$sql = 'DELETE FROM `kwalbum_items_tags`';
		$db->query(Database::DELETE, $sql);
		$sql = 'DELETE FROM `kwalbum_items_persons`';
		$db->query(Database::DELETE, $sql);
		$sql = 'DELETE FROM `kwalbum_tags`';
		$db->query(Database::DELETE, $sql);
		$sql = 'DELETE FROM `kwalbum_persons`';
		$db->query(Database::DELETE, $sql);
		$sql = 'DELETE FROM `kwalbum_items`';
		$db->query(Database::DELETE, $sql);
		$sql = 'DELETE FROM `kwalbum_users` WHERE id > 2';
		$db->query(Database::DELETE, $sql);
		$sql = 'DELETE FROM `kwalbum_locations` WHERE id > 1';
		$db->query(Database::DELETE, $sql);
		$location = Model::factory('kwalbum_location')->load(1);
		$location->count = 0;
		$location->save();
	}
}