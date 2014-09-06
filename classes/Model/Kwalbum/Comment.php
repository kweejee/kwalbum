<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jul 6, 2009
 * @package kwalbum
 * @since 3.0 Jul 6, 2009
 */

class Model_Kwalbum_Comment extends Kwalbum_Model
{
	public $id, $name, $text, $item_id, $date;
	private $_ip;
	static private $_where = array();
	static private $_sort_field = 'kwalbum_comments.create_dt';
	static private $_sort_direction = 'DESC';
	static private $_gtlt = '>';

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

	public function load($value = null)
	{
        $this->clear();
		if (is_null($value)) {
			return $this;
		}

		$result = DB::query(Database::SELECT,
			"SELECT id, item_id, name, text, create_dt, ip
			FROM kwalbum_comments
			WHERE id = :value
			LIMIT 1")
			->param(':value', $value)
			->execute();
		if ($result->count() == 0) {
			return $this;
		}

		$row = $result[0];
		$this->id = (int)$row['id'];
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

	public function delete()
	{
		// Update item->has_comments if needed
		$result = DB::query(Database::SELECT,
			"SELECT count(*)
			FROM kwalbum_comments
			WHERE item_id = :item_id")
			->param(':item_id', $this->item_id)
			->execute();

		if (0 == $result[0]['count(*)']) {
			DB::query(Database::UPDATE,
				"UPDATE kwalbum_items
				SET has_comments = 0
				WHERE id = :item_id")
				->param(':item_id', $this->item_id)
				->execute();
		}

		// Delete comment
		$query = DB::query(Database::DELETE,
			"DELETE FROM kwalbum_comments
			WHERE id = :id")
			->param(':id', $this->id);
        $query->execute();

        $this->clear();

        return true;
	}

	static public function set_sort_field($sort_field)
	{
		switch ($sort_field)
		{
			case 'create':
			default: $sort_field = 'kwalbum_comments.create_dt';
		}
		Model_Kwalbum_Comment :: $_sort_field = $sort_field;
	}

	static public function set_sort_direction($sort_direction)
	{
		if ($sort_direction == 'ASC')
		{
			Model_Kwalbum_Comment :: $_sort_direction = 'ASC';
			Model_Kwalbum_Comment :: $_gtlt = '<';
		}
		else
		{
			Model_Kwalbum_Comment :: $_sort_direction = 'DESC';
			Model_Kwalbum_Comment :: $_gtlt = '>';
		}
	}

	static public function get_thumbnails($page_number = 1)
	{
		$sort_field = Model_Kwalbum_Comment :: $_sort_field;
		$sort_direction = Model_Kwalbum_Comment :: $_sort_direction;
		$query = "SELECT kwalbum_items.id AS item_id, kwalbum_comments.id AS comment_id
			FROM kwalbum_items
			JOIN kwalbum_comments
			WHERE kwalbum_items.id = kwalbum_comments.item_id
			ORDER BY $sort_field $sort_direction
			LIMIT :offset,:limit";

		$limit = self::get_config('items_per_page');
		$offset = ($page_number-1)*$limit;
		$result = DB::query(Database::SELECT, $query)
			->param(':offset', $offset)
			->param(':limit', $limit)
			->execute();

		$thumbnails = array();
		$i = 0;
		foreach ($result as $row)
		{
			$thumbnails[$i]['item'] = Model::factory('Kwalbum_Item')->load($row['item_id']);
			$thumbnails[$i]['comment'] = Model::factory('Kwalbum_Comment')->load($row['comment_id']);
			$i++;
		}

		return $thumbnails;
	}

	public function clear()
	{
		$this->id = $this->item_id = $this->_ip = 0;
		$this->name = $this->text = $this->date = '';
		$this->loaded = false;
	}
}
