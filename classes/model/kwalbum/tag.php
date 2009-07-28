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

	public function load($id = null)
	{
		if ($id == null)
		{
			$this->clear();
			return $this;
		}

		$result = DB::query(Database::SELECT,
			"SELECT name, count
			FROM kwalbum_tags
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
		$this->name = $row['name'];
		$this->count = $row['count'];
		$this->loaded = true;

		return $this;
	}

	public function save()
	{
		$id = $this->id;

		if ($id == 0)
		{
			$query = DB::query(Database::INSERT, "INSERT INTO kwalbum_tags
				(name, count)
				VALUES (:name, :count)");
		}
		else
		{
			$query = DB::query(Database::UPDATE, "UPDATE kwalbum_tags
				SET name = :name, count = :count
				WHERE id = :id")
				->param(':id', $id);
		}
		$query
			->param(':name', $this->name)
			->param(':count', $this->count);

		$result = $query->execute();
		if ($id == 0)
		{
			$this->id = $result[0];
		}
	}

	public function delete($id = NULL)
	{
		if ($id === NULL)
		{
			$id = $this->id;
		}

		// Delete relations between the tag and items
		DB::query(Database::DELETE, "DELETE FROM kwalbum_items_tags
			WHERE tag_id = :id")
			->param(':id', $id)
			->execute();

		// Delete the tag
		DB::query(Database::DELETE, "DELETE FROM kwalbum_tags
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
}
