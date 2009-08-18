<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jul 6, 2009
 * @package kwalbum
 * @since 3.0 Jul 6, 2009
 */

class Model_Kwalbum_User extends Kwalbum_Model
{
	public $id, $name, $openid, $visit_date, $permission_level;

	public function load($id = null, $field = 'id')
	{
		$this->clear();
		if ($id === null)
		{
			return $this;
		}

		$result = DB::query(Database::SELECT,
			"SELECT id, name, openid, visit_dt, permission_level
			FROM kwalbum_users
			WHERE $field = :id
			LIMIT 1")
			->param(':id', $id)
			->execute();
		if ($result->count() == 0)
		{
			return $this;
		}

		$row = $result[0];

		$this->id = (int)$row['id'];
		$this->name = $row['name'];
		$this->openid = $row['openid'];
		$this->visit_date = $row['visit_dt'];
		$this->permission_level = (int)$row['permission_level'];
		$this->loaded = true;

		return $this;
	}

	public function save()
	{
		if ($this->loaded == false)
		{
			$query = DB::query(Database::INSERT,
				"INSERT INTO kwalbum_users
				(name, openid, visit_dt, permission_level)
				VALUES (:name, :openid, :visit_dt, :permission_level)");
			if ( ! $this->permission_level)
			{
				$this->permission_level = 1;
			}
			// Creating a new user does not mean they are visiting.
			$this->visit_date = '0000-00-00 00:00:00';
		}
		else
		{
			$query = DB::query(Database::UPDATE,
				"UPDATE kwalbum_users
				SET name = :name, openid = :openid, visit_dt = :visit_dt, permission_level= :permission_level
				WHERE id = :id")
				->param(':id', $this->id);
		}
		$query
			->param(':name', $this->name)
			->param(':openid', $this->openid)
			->param(':visit_dt', $this->visit_date)
			->param(':permission_level', $this->permission_level);

		$result = $query->execute();
		if ($this->loaded == false)
		{
			$this->id = $result[0];
			$this->loaded = true;
		}
	}

	public function delete($id = NULL)
	{
		if ($id === NULL)
		{
			$id = $this->id;
		}

		// do not delete main admin user or default "deleted user" account
		if ($id < 3)
		{
			return false;
		}

		// Change ownership of items from the user to the default "deleted user" account
		DB::query(Database::UPDATE, "UPDATE kwalbum_items
			SET user_id=2, hide_level=100
			WHERE user_id = :id")
			->param(':id', $id)
			->execute();

		// Delete favorites of the user
		DB::query(Database::DELETE, "DELETE FROM kwalbum_favorites
			WHERE user_id = :id")
			->param(':id', $id)
			->execute();

		// Delete the user
		DB::query(Database::DELETE, "DELETE FROM kwalbum_users
			WHERE id = :id")
			->param(':id', $id)
			->execute();

		if ($id == $this->id)
		{
			$this->clear();
		}
	}

	/**
	 * Check if a user can edit an item
	 *
	 * @param kwalbum_item object of item to check about editing
	 * @return true/false if user can edit the item
	 */
	public function can_edit_item($item = null)
	{
		// User can edit any item
		if ($this->permission_level >= 4)
			return true;

		if ($item === null)
			return false;

		// User can only edit items they added
		if ($this->permission_level == 3)
			return ($this->id == $item->user_id);

		return false;
	}

	public function clear()
	{
		$this->id = $this->permission_level = 0;
		$this->name = $this->openid = $this->visit_date = '';
		$this->loaded = false;
	}

	public function __toString()
	{
		return $this->name;
	}

	public function __get($key)
	{
		switch ($key)
		{
			case 'is_logged_in': return ($this->permission_level > 0);
			case 'can_see_all': return ($this->permission_level >= 2);
			case 'can_edit': return ($this->permission_level >= 3);
			case 'is_admin': return ($this->permission_level == 5);
		}
	}
}
