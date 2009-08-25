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

class Model_Kwalbum_Tag extends Kwalbum_Model
{
	public $id, $name, $count, $loaded;

	public function load($id = null, $field = 'id')
	{
		if ($id === null)
		{
			$this->clear();
			return $this;
		}

		$result = DB::query(Database::SELECT,
			"SELECT id, name, count
			FROM kwalbum_tags
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
				FROM kwalbum_tags
				WHERE name = :name
				LIMIT 1")
				->param(':name', $this->name)
				->execute();
			if ($result->count() == 0)
			{
				$result = DB::query(Database::INSERT,
					"INSERT INTO kwalbum_tags
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
			"UPDATE kwalbum_tags
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

		// Delete relations between the tag and items
		DB::query(Database::DELETE,
			"DELETE FROM kwalbum_items_tags
			WHERE tag_id = :id")
			->param(':id', $id)
			->execute();

		// Delete the tag
		DB::query(Database::DELETE,
			"DELETE FROM kwalbum_tags
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


	static public function getTagArray($limit = 10, $offset = 0, $name = '', $order = 'name ASC')
	{
		$tags = array();

		$name = trim($name);

		// Select almost exact (not case sensitive) match first if searching by name
		if ( ! empty($name))
		{
			$result = DB::query(Database::SELECT,
				'SELECT name
				FROM kwalbum_tags
				WHERE name = :name')
				->param(':name', $name)
				->execute();
			if ($result->count() == 1)
			{
				$tags[] = $result[0]['name'];
				$limit--;
			}
		}

		// Select from starting matches if searching by name or select from all
		$partName = "$name%";
		$query = 'AND name != :name';
		$result = DB::query(Database::SELECT,
			"SELECT name
			FROM kwalbum_tags"
			.( ! empty($name) ? " WHERE name LIKE :partName $query" : null)
			." ORDER BY $order
			LIMIT :offset,:limit")
			->param(':partName', $partName)
			->param(':name', $name)
			->param(':limit', $limit)
			->param(':offset', $offset)
			->execute();

		if ($result->count() > 0)
		{
			foreach($result as $row)
			{
				$tags[] = $row['name'];
				$query .= " AND name != '$row[name]'";
			}
			$limit -= $result->count();
		}

		// Select from any partial matches if searching by name
		if ( ! empty($name) and $limit > 0)
		{
			$partName = "%$name%";
			$result = DB::query(Database::SELECT,
				"SELECT name
				FROM kwalbum_tags
				WHERE name LIKE :partName $query"
				." ORDER BY $order
				LIMIT :limit")
				->param(':partName', $partName)
				->param(':name', $name)
				->param(':limit', $limit)
				->execute();

			foreach($result as $row)
			{
				$tags[] = $row['name'];
			}
		}
		return $tags;
	}
}
