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

class Model_Kwalbum_Person extends Kwalbum_Model
{
	public $id, $name, $count, $loaded;

	public function load($id = null, $field = 'id')
	{
		if ($id == null)
		{
			$this->clear();
			return $this;
		}

		$result = DB::query(Database::SELECT,
			"SELECT id, name, count
			FROM kwalbum_persons
			WHERE $field = :id
			LIMIT 1")
			->param(':id', $id)
			->execute();
		if ($result->count() == 0)
		{
			$this->clear();
			return $this;
		}

		$row = $result[0];

		$this->id = (int)$row['id'];
		$this->name = $row['name'];
		$this->count = (int)$row['count'];
		$this->loaded = true;

		return $this;
	}

	public function save()
	{
		$id = $this->id;

		if ($this->loaded === false)
		{
			$result = DB::query(Database::SELECT,
				"SELECT id, count
				FROM kwalbum_persons
				WHERE name = :name
				LIMIT 1")
				->param(':name', $this->name)
				->execute();
			if ($result->count() == 0)
			{
				$result = DB::query(Database::INSERT,
					"INSERT INTO kwalbum_persons
					(name, count)
					VALUES (:name, :count)")
					->param(':name', $this->name)
					->param(':count', $this->count)
					->execute();
				$this->id = $result[0];
				$this->loaded = true;
				return;
			}

			$this->id = $id = (int)$result[0]['id'];
			$this->count = (int)$result[0]['count'];
			$this->loaded = true;
		}

		DB::query(Database::UPDATE,
			"UPDATE kwalbum_persons
			SET name = :name, count = :count
			WHERE id = :id")
			->param(':id', $id)
			->param(':name', $this->name)
			->param(':count', $this->count)
			->execute();
	}

	public function delete($id = NULL)
	{
		if ($id === NULL)
		{
			$id = $this->id;
		}

		// Delete relations between the person and items
		DB::query(Database::DELETE,
			"DELETE FROM kwalbum_items_persons
			WHERE person_id = :id")
			->param(':id', $id)
			->execute();

		// Delete the person
		DB::query(Database::DELETE,
			"DELETE FROM kwalbum_persons
			WHERE id = :id")
			->param(':id', $id)
			->execute();

		if ($id == $this->id)
		{
			$this->clear();
		}
	}

	public function clear()
	{
		$this->id = $this->count = 0;
		$this->name = '';
		$this->loaded = false;
	}

	public function __toString()
	{
		return $this->name;
	}

	static public function getNameArray($min_count = 1, $limit = 10, $offset = 0,
		$name = '', $order = 'name ASC', $not_included = array())
	{
		$name = trim($name);
		$tags = array();
		$query = '';
		$db = Database::instance();

		if (count($not_included) > 0)
		{
			foreach($not_included as $word)
			{
				$query .= " AND name != ".$db->escape($word);
			}
		}

		if ($name)
		{

			// Select almost exact (not case sensitive) match first
			$result = DB::query(Database::SELECT,
				'SELECT name
				FROM kwalbum_persons
				WHERE name = :name '.$query)
				->param(':name', $name)
				->execute();
			if ($result->count() == 1)
			{
				$tags[] = $result[0]['name'];
				$limit--;
			}

			// Select from starting matches
			$partName = "$name%";
			$query .= ' AND name != :name';
			$result = DB::query(Database::SELECT,
				"SELECT name
				FROM kwalbum_persons
				WHERE name LIKE :partName $query AND count >= :min_count
				ORDER BY $order
				LIMIT :limit")
				->param(':partName', $partName)
				->param(':name', $name)
				->param(':min_count', $min_count)
				->param(':limit', $limit)
				->execute();

			if ($result->count() > 0)
			{
				foreach($result as $row)
				{
					$tags[] = $row['name'];
					$query .= " AND name != ".$db->escape($row['name']);
				}
				$limit -= $result->count();
			}

			// Select from any partial matches if the result limit hasn't been reached yet
			if ($limit > 0)
			{
				$partName = "%$name%";
				$result = DB::query(Database::SELECT,
					"SELECT name
					FROM kwalbum_persons
					WHERE name LIKE :partName $query AND count >= :min_count"
					." ORDER BY $order
					LIMIT :limit")
					->param(':partName', $partName)
					->param(':name', $name)
					->param(':min_count', $min_count)
					->param(':limit', $limit)
					->execute();

				foreach($result as $row)
				{
					$tags[] = $row['name'];
				}
			}
		}
		else
		{
			$result = DB::query(Database::SELECT,
				"SELECT name
				FROM kwalbum_persons
				WHERE count >= :min_count
				ORDER BY $order"
				.($limit ? ' LIMIT :offset,:limit' : null))
				->param(':offset', $offset)
				->param(':min_count', $min_count)
				->param(':limit', $limit)
				->execute();

			foreach ($result as $row)
			{
				$tags[] = $row['name'];
			}
		}

		return $tags;
	}
}
