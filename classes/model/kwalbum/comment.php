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

class Model_Kwalbum_Comment extends Kwalbum_Model
{
	public $id, $name, $text, $item_id, $date;
	private $_ip;

	public function __set($var, $value = null)
	{
		if ($var == 'ip')
		{
			$this->_ip = ip2long($value);
		}
	}

	public function __get($var)
	{
		if ($var == 'ip')
		{
			return long2ip($this->_ip);
		}
	}

	public function load($id = null)
	{
		if ($id === null)
		{
			$id = $this->id;
		}

		$result = DB::query(Database::SELECT,
			"SELECT item_id, name, text, create_dt, ip
			FROM kwalbum_comments
			WHERE id = :id
			LIMIT 1")
			->param(':id', $id)
			->execute();
		if ($result->count() == 0)
		{
			$this->clear();
			return $this;
		}

		$row = $result[0];
		$this->id = $id;
		$this->item_id = $row['item_id'];
		$this->name = $row['name'];
		$this->text = $row['text'];
		$this->date = $row['create_dt'];
		$this->_ip = $row['ip'];
		$this->loaded = true;
		return $this;
	}

	public function save()
	{
		if ($this->loaded === false)
		{
			$query = DB::query(Database::INSERT,
				"INSERT INTO kwalbum_comments
				(item_id, name, text, create_dt, ip)
				VALUES (:item_id, :name, :text, :date, :ip)");
			DB::query(Database::UPDATE,
				"UPDATE kwalbum_items
				SET has_comments = 1
				WHERE id = :item_id")
				->param(':item_id', $this->item_id)
				->execute();
		}
		else
		{
			$query = DB::query(Database::UPDATE,
				"UPDATE kwalbum_comments
				SET name = :name, text = :text, create_dt = :date, ip = :ip
				WHERE id = :id");
		}

		if (empty($this->date))
		{
			$this->date = date('Y-m-d H:i:s');
		}
		if (empty($this->_ip))
		{
			$this->ip = Request::$client_ip;
		}
		$query
			->param(':item_id', $this->item_id)
			->param(':name', $this->name)
			->param(':text', $this->text)
			->param(':date', $this->date)
			->param(':ip', $this->_ip);

		$result = $query->execute();

		if ($this->loaded === false)
		{
			$this->id = $result[0];
			$this->loaded = true;
		}
	}

	public function delete($id = null)
	{
		if ($id === null)
		{
			$id = $this->id;
		}

		// get item_id if needed
		if ($id != $this->id)
		{
			$result = DB::query(Database::SELECT,
				"SELECT item_id
				FROM kwalbum_comments
				WHERE id = :id")
				->param(':id', $id)
				->execute();
			$item_id = $result[0]['item_id'];
		}
		else
		{
			$item_id = $this->item_id;
			$this->clear();
		}

		// Delete comment
		$query = DB::query(Database::DELETE,
			"DELETE FROM kwalbum_comments
			WHERE id = :id")
			->param(':id', $id);
			$query->execute();
		// Update item->has_comments if needed
		$result = DB::query(Database::SELECT,
			"SELECT count(*)
			FROM kwalbum_comments
			WHERE item_id = :item_id")
			->param(':item_id', $item_id)
			->execute();

		if (0 == $result[0]['count(*)'])
		{
			DB::query(Database::UPDATE,
				"UPDATE kwalbum_items
				SET has_comments = 0
				WHERE id = :item_id")
				->param(':item_id', $item_id)
				->execute();
		}
	}

	public function clear()
	{
		$this->id = $this->item_id = $this->_ip = 0;
		$this->name = $this->text = $this->date = '';
		$this->loaded = false;
	}
}
