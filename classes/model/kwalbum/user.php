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
	public $id, $name, $login_name, $email, $token, $visit_date, $permission_level, $reset_code;
	private $_password;

	public function load($id = null, $field = 'id')
	{
		$this->clear();
		if ($id === null)
		{
			return $this;
		}

		try {
			$result = DB::query(Database::SELECT,
				"SELECT id, name, login_name, email, password, token, visit_dt, permission_level, reset_code
				FROM kwalbum_users
				WHERE $field = :id
				LIMIT 1")
				->param(':id', $id)
				->execute();
		}
		catch (Exception $e){ return $this;}

		if ($result->count() == 0)
		{
			return $this;
		}

		$row = $result[0];

		$this->id = (int)$row['id'];
		$this->name = $row['name'];
		$this->login_name = $row['login_name'];
		$this->email = $row['email'];
		$this->_password = $row['password'];
		$this->token = $row['token'];
		$this->visit_date = $row['visit_dt'];
		$this->permission_level = (int)$row['permission_level'];
		$this->reset_code = $row['reset_code'];
		$this->loaded = true;

		return $this;
	}

	public function save()
	{
		if ($this->loaded == false)
		{
			$query = DB::query(Database::INSERT,
				"INSERT INTO kwalbum_users
				(name, login_name, email, password, token, visit_dt, permission_level, reset_code)
				VALUES (:name, :login_name, :email, :password, :token, :visit_dt, :permission_level, :reset_code)");
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
				SET name = :name, login_name = :login_name, email = :email, password = :password,
					token = :token, visit_dt = :visit_dt, permission_level= :permission_level
				WHERE id = :id")
				->param(':id', $this->id);
		}
		$query
			->param(':name', $this->name)
			->param(':login_name', $this->login_name)
			->param(':email', $this->email)
			->param(':password', $this->_password)
			->param(':token', $this->token)
			->param(':visit_dt', $this->visit_date)
			->param(':permission_level', $this->permission_level)
			->param(':reset_code', $this->reset_code);

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

	/**
	 * Check if a user can see an item
	 *
	 * @param kwalbum_item object of item to check about editing
	 * @return true/false if user can view the item
	 */
	public function can_view_item($item = null)
	{
		// User can view any at the same level or lower
		if ($this->permission_level >= $item->hide_level)
			return true;

		return false;
	}

	public function password_equals($password_to_check)
	{
		return (sha1($password_to_check) === $this->_password);
	}

	public function load_from_cookie($action)
	{
		session_start();
		if ($action == 'logout')
		{
			if ( ! empty($_SESSION['kwalbum_id']))
			{
				$user = $this->load((int)$_SESSION['kwalbum_id']);
				if ($this->token)
				{
					$this->token = '';
					$this->save;
				}
			}
			unset($_SESSION['kwalbum_id']);
			unset($_SESSION['kwalbum_edit']);
			setcookie('kwalbum', '', time() - 36000, '/');
			session_write_close();
			$this->clear();
			return $this;
		}

		if ( ! empty($_SESSION['kwalbum_id']))
		{
			$id = (int)$_SESSION['kwalbum_id'];
			$this->load($id);
			if ($this->reset_code)
			{
				$this->reset_code = '';
				$this->save();
			}
		}
		elseif (isset ($_COOKIE['kwalbum']))
		{
			$temp = array ();
			$temp = explode(':', $_COOKIE['kwalbum']);
			$this->load((int)($temp[0]));
			if ($this->token == $temp[1])
			{
				$this->visit_date = date('Y-m-d H:i:s');
				$this->save();
			}
			else
			{
				if ( ! empty($_SESSION['kwalbum_id']))
				{
					$user = $this->load((int)$_SESSION['kwalbum_id']);
					if ($this->token)
					{
						$this->token = '';
						$this->save;
					}
				}
				unset($_SESSION['kwalbum_id']);
				unset($_SESSION['kwalbum_edit']);
				setcookie('kwalbum', '', time() - 36000, '/');
				return $this->clear();
			}
		}
		$_SESSION['kwalbum_id'] = $this->id;
		session_write_close();

		return $this;
	}

	public function clear()
	{
		$this->id = $this->permission_level = 0;
		$this->name = $this->login_name = $this->email = $this->_password
			= $this->token = $this->visit_date = $this->reset_code = '';
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
			case 'can_edit':
			case 'can_add': return ($this->permission_level >= 3);
			//case 'can_edit_all': return ($this->permission_level >= 4); // see can_edit_item($item)
			case 'is_admin': return ($this->permission_level == 5);
		}
	}

	public function __set($key, $value)
	{
		if ($key == 'password')
		{
			$this->_password = sha1($value);
		}
	}
}
