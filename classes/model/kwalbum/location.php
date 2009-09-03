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

class Model_Kwalbum_Location extends Kwalbum_Model
{
	public $id, $name, $latitude, $longitude, $count;

	public function load($id = null, $field = 'id')
	{
		if ($id === null)
		{
			$this->clear();
			return $this;
		}

		$query = DB::query(Database::SELECT,
			"SELECT id, name, latitude, longitude, count
			FROM kwalbum_locations
			WHERE $field = :id
			LIMIT 1")
			->param(':id', $id);

		$result = $query->execute();

		if ($result->count() == 0)
		{
			$this->clear();
			return $this;
		}

		$row = $result[0];

		$this->id = (int)$row['id'];
		$this->name = $row['name'];
		$this->latitude = $row['latitude'];
		$this->longitude = $row['longitude'];
		$this->count = (int)$row['count'];
		$this->loaded = true;

		return $this;
	}

	public function save()
	{
		$id = $this->id;

		if ($id == 0)
		{
			$result = DB::query(Database::SELECT,
				"SELECT id, latitude, longitude, count
				FROM kwalbum_locations
				WHERE name = :name
				LIMIT 1")
				->param(':name', $this->name)
				->execute();
			if ($result->count() == 0)
			{
				$result = DB::query(Database::INSERT,
					"INSERT INTO kwalbum_locations
					(name, latitude, longitude, count)
					VALUES (:name, :latitude, :longitude, :count)")
					->param(':name', $this->name)
					->param(':latitude', $this->latitude)
					->param(':longitude', $this->longitude)
					->param(':count', $this->count)
					->execute();
				$this->id = $result[0];
				return;
			}
			$this->id = $id = (int)$result[0]['id'];
			if ($this->latitude == 0)
				$this->latitude = $result[0]['latitude'];
			if ($this->longitude == 0)
				$this->longitude = $result[0]['longitude'];
			if ($this->count == 0)
				$this->count = (int)$result[0]['count'];
		}
		else
		{
			$query = DB::query(Database::UPDATE,
				"UPDATE kwalbum_locations
				SET name = :name, latitude = :latitude, longitude = :longitude, count= :count
				WHERE id = :id")
				->param(':id', $id)
			->param(':name', $this->name)
			->param(':latitude', $this->latitude)
			->param(':longitude', $this->longitude)
			->param(':count', $this->count);
			$query->execute();
		}
	}

	public function delete($id = NULL)
	{
		if ($id === null)
		{
			$id = $this->id;
		}

		// do not delete the "unknown" location
		if ($id == 1)
		{
			return $this;
		}

		$count = DB::query(Database::UPDATE,
			"UPDATE kwalbum_items
			SET location_id = 1
			WHERE location_id = :id")
			->param(':id', $id)
			->execute();
		DB::query(Database::UPDATE,
			"UPDATE kwalbum_locations
			SET count = count+:count
			WHERE id = 1")
			->param(':count', $count)
			->execute();
		DB::query(Database::DELETE,
			"DELETE FROM kwalbum_locations
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
		$this->id = $this->latitude = $this->longitude = $this->count = 0;
		$this->name = '';
		$this->loaded = false;
	}

	public function __toString()
	{
		return $this->name;
	}

	static public function getNameArray($min_count = 1, $limit = null, $offset = 0, $name = '', $order = 'name ASC')
	{
		$locations = array();

		$name = trim($name);

		if ( ! empty($name))
		{
			// Select almost exact (not case sensitive) match first
			$result = DB::query(Database::SELECT,
				'SELECT name
				FROM kwalbum_locations
				WHERE name = :name')
				->param(':name', $name)
				->execute();
			if ($result->count() == 1)
			{
				$locations[] = $result[0]['name'];
				$limit--;
			}

			// Select from starting matches if searching by name or select from all
			$partName = "$name%";
			$query = 'AND name != :name';
			$result = DB::query(Database::SELECT,
				"SELECT name
				FROM kwalbum_locations
				WHERE name LIKE :partName $query AND count >= :min_count
				ORDER BY $order"
				.($limit ? ' LIMIT :limit' : null))
				->param(':partName', $partName)
				->param(':name', $name)
				->param(':min_count', $min_count)
				->param(':limit', $limit)
				->execute();

			if ($result->count() > 0)
			{
				foreach($result as $row)
				{
					$locations[] = $row['name'];
					$query .= " AND name != '$row[name]'";
				}
				$limit -= $result->count();
			}

			// Select from any partial matches if the result limit hasn't been reached yet
			if ($limit > 0)
			{
				$partName = "%$name%";
				$result = DB::query(Database::SELECT,
					"SELECT name
					FROM kwalbum_locations
					WHERE name LIKE :partName $query AND count >= :min_count
					ORDER BY $order"
					.($limit ? ' LIMIT :limit' : null))
					->param(':partName', $partName)
					->param(':name', $name)
					->param(':min_count', $min_count)
					->param(':limit', $limit)
					->execute();

				foreach($result as $row)
				{
					$locations[] = $row['name'];
				}
			}
		}
		else
		{
			$result = DB::query(Database::SELECT,
				"SELECT name
				FROM kwalbum_locations
				WHERE count >= :min_count
				ORDER BY $order"
				.($limit ? ' LIMIT :offset,:limit' : null))
				->param(':offset', $offset)
				->param(':min_count', $min_count)
				->param(':limit', $limit)
				->execute();

			foreach ($result as $row)
			{
				$locations[] = $row['name'];
			}
		}

		return $locations;
	}
}
