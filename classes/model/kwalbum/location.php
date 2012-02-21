<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since 3.0 Jul 6, 2009
 */

class Model_Kwalbum_Location extends Kwalbum_Model
{
	public $id, $name, $latitude, $longitude, $count, $child_count, $thumbnail_item_id, $parent_name,
		$name_hide_level, $coordinate_hide_level, $description;
	private $_display_name;

	public function load($id = null, $field = 'id')
	{
		if ($id === null)
		{
			$this->clear();
			return $this;
		}

		$field = mysql_escape_string($field);
		$query = DB::query(Database::SELECT,
			"SELECT loc.*, p.name AS parent_name
			FROM kwalbum_locations loc
			LEFT JOIN kwalbum_locations p ON (p.id = loc.parent_location_id)
			WHERE loc.{$field} = :id
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
		$this->parent_name = $row['parent_name'];
		$this->_display_name = ($row['parent_name'] ? $row['parent_name'].': ' : '').$row['name'];
		$this->latitude = (float)$row['latitude'];
		$this->longitude = (float)$row['longitude'];
		$this->count = (int)$row['count'];
		$this->child_count = (int)$row['child_count'];
		$this->thumbnail_item_id = (int)$row['thumbnail_item_id'];
		$this->name_hide_level = (int)$row['name_hide_level'];
		$this->coordinate_hide_level = (int)$row['coordinate_hide_level'];
		$this->description = $row['description'];
		$this->loaded = true;

		return $this;
	}

	/**
	 * Save location changes by either inserting or updating the location table in the database
	 *
	 * @return Model_Kwalbum_Location
	 */
	public function save()
	{
		$id = $this->id;

		$parent_id = 0;
		if ($this->parent_name)
		{
			$result = DB::query(Database::SELECT,
				"SELECT id
				FROM kwalbum_locations
				WHERE name = :name")
				->param(':name', $this->parent_name)
				->execute();
			if (count($result) == 1)
			{
				$parent_id = $result[0]['id'];
			}
			else
			{
				$parent = clone $this;
				$parent->id = 0;
				$parent->display_name = $this->parent_name;
				$parent_id = $parent->save()->id;
			}
		}
		if ($id == 0)
		{
			$result = DB::query(Database::SELECT,
				"SELECT id, latitude, longitude, count, child_count
				FROM kwalbum_locations
				WHERE name = :name AND parent_location_id = :parent_id
				LIMIT 1")
				->param(':name', $this->name)
				->param(':parent_id', (int)$parent_id)
				->execute();
			if ($result->count() == 0)
			{
				$result = DB::query(Database::INSERT,
					"INSERT INTO kwalbum_locations
					(name, latitude, longitude, count, child_count, thumbnail_item_id, parent_location_id,
						name_hide_level, coordinate_hide_level, description)
					VALUES (:name, :latitude, :longitude, :count, :child_count, :thumbnail_item_id, :parent_id,
						:name_hide_level, :coordinate_hide_level, :description)")
					->param(':name', $this->name)
					->param(':latitude', $this->latitude ? $this->latitude : 0)
					->param(':longitude', $this->longitude ? $this->longitude : 0)
					->param(':count', (int)$this->count)
					->param(':child_count', (int)$this->child_count)
					->param(':thumbnail_item_id', $this->thumbnail_item_id ? $this->thumbnail_item_id : 0)
					->param(':parent_id', (int)$parent_id)
					->param(':name_hide_level', (int)$this->name_hide_level)
					->param(':coordinate_hide_level', (int)$this->coordinate_hide_level)
					->param(':description', $this->description ? $this->description : '')
					->execute();
				$this->id = $result[0];

				Model_Kwalbum_Location::updateCounts();
				return $this;
			}

			$row = $result[0];
			$this->id = $id = (int)$row['id'];
			if (!$this->latitude)
				$this->latitude = (float)$row['latitude'];
			if (!$this->longitude)
				$this->longitude = (float)$row['longitude'];
			if (!$this->count)
				$this->count = (int)$row['count'];
			if (!$this->child_count)
				$this->child_count = (int)$row['child_count'];
		}
		else
		{
			$query = DB::query(Database::UPDATE,
				"UPDATE kwalbum_locations
				SET name = :name, latitude = :latitude, longitude = :longitude, count = :count, child_count = :child_count,
					thumbnail_item_id = :thumbnail_item_id, parent_location_id = :parent_id,
					name_hide_level = :name_hide_level, coordinate_hide_level = :coordinate_hide_level,
					description = :description
				WHERE id = :id")
				->param(':id', $id)
				->param(':name', $this->name)
				->param(':latitude', $this->latitude ? $this->latitude : 0)
				->param(':longitude', $this->longitude ? $this->longitude : 0)
				->param(':count', (int)$this->count)
				->param(':child_count', (int)$this->child_count)
				->param(':thumbnail_item_id', $this->thumbnail_item_id ? $this->thumbnail_item_id : 0)
				->param(':parent_id', (int)$parent_id)
				->param(':name_hide_level', $this->name_hide_level ? $this->name_hide_level : 0)
				->param(':coordinate_hide_level', $this->coordinate_hide_level ? $this->coordinate_hide_level : 0)
				->param(':description', $this->description ? $this->description : '');
			$query->execute();
		}

		Model_Kwalbum_Location::updateCounts();
		return $this;
	}

	/**
	 * Delete a location and all connections it has to items.
	 * 
	 * @param int $id
	 * @return boolean
	 */
	public function delete($id = null)
	{
		if ($id === null)
		{
			$id = $this->id;
		}

		// do not delete the "unknown" location
		if ($id == 1)
		{
			return false;
		}

		$parent_id = 0;
		$result = DB::query(Database::SELECT,
			"SELECT parent_location_id
			FROM kwalbum_locations
			WHERE id = :id")
			->param(':id', $id)
			->execute();
		if (count($result) == 1)
		{
			$parent_id = $result[0]['parent_location_id'];
		}
		$new_location_id = $parent_id ? $parent_id : 1;
		$count = DB::query(Database::UPDATE,
			"UPDATE kwalbum_items
			SET location_id = :new_id
			WHERE location_id = :id")
			->param(':id', $id)
			->param(':new_id', $new_location_id)
			->execute();
		DB::query(Database::UPDATE,
			"UPDATE kwalbum_locations
			SET count = count+:count
			WHERE id = :new_id")
			->param(':count', $count)
			->param(':new_id', $new_location_id)
			->execute();
		if ($parent_id)
		{
			DB::query(Database::UPDATE,
				"UPDATE kwalbum_locations
				SET child_count = child_count-:count
				WHERE id = :id")
				->param(':id', $parent_id)
				->param(':count', $count)
				->execute();
		}
		DB::query(Database::DELETE,
			"DELETE FROM kwalbum_locations
			WHERE id = :id")
			->param(':id', $id)
			->execute();

		if ($id == $this->id)
		{
			$this->clear();
		}

		return true;
	}

	public function clear()
	{
		$this->id = $this->latitude = $this->longitude = $this->count = $this->child_count = $this->thumbnail_item_id
			= $this->name_hide_level = $this->coordinate_hide_level = 0;
		$this->name = $this->parent_name = $this->_display_name = $this->description = '';
		$this->loaded = false;
	}

	public function __toString()
	{
		return $this->_display_name;
	}

	public function  __get($key) {
		if ($key == 'display_name')
			return $this->_display_name;
	}
	public function  __set($key, $value) {
		if ($key == 'display_name') {
			$names = explode(':', $value);
			if (count($names) > 1) {
				$parent_name = trim($names[0]);
				array_shift($names);
				foreach ($names as &$n)
					$n = trim($n);
				$this->name = implode(': ', $names);
				$this->parent_name = $parent_name;
				$this->_display_name = "{$this->parent_name}: {$this->name}";
			} else {
				$this->name = trim($value);
				$this->parent_name = '';
				$this->_display_name = $this->name;
			}
		}
	}

	static public function getAllArray($order_by = '')
	{
		$result = DB::query(Database::SELECT,
			"SELECT *
			FROM kwalbum_locations
			{$order_by}")
			->execute();
		return $result;
	}

	/**
	 *
	 * @param Model_Kwalbum_User $user
	 * @param int $min_count
	 * @param int $limit
	 * @param int $offset
	 * @param string $name
	 * @param string $order
	 * @return array
	 */
	static public function getNameArray(&$user, $min_count = 1, $limit = null, $offset = 0, $name = '', $order = '')
	{
		$name = trim($name);
		if ($order)
			$order = 'ORDER BY '.$order;

		$locations = array();
		if ( ! empty($name))
		{
			// Split the parent location name from the specific location name
			$parent_name = '';
			$parent_query = '';
			$names = explode(':', $name);
			if (count($names) > 1) {
				$parent_name = mysql_escape_string(trim($names[0]));
				array_shift($names);
				foreach ($names as &$n)
					$n = trim($n);
				$name = implode(': ', $names);
			}
			$partName = "{$name}%";
			if ($parent_name)
				$parent_query = "OR p.name LIKE '{$parent_name}%'";
			$not_query = 'loc.name != :name';

			// Select almost exact (not case sensitive) match first
			$result = DB::query(Database::SELECT,
				'SELECT loc.name AS name, p.id, p.name AS parent
				FROM kwalbum_locations loc
				LEFT JOIN kwalbum_locations p ON (p.id = loc.parent_location_id)
				WHERE loc.name = :name
				  AND loc.name_hide_level <= :permission_level')
				->param(':name', $name)
				->param(':permission_level', (int)$user->permission_level)
				->execute();
			if ($result->count() > 0)
			{
				foreach ($result as $row)
				{
					$locations[] = ($row['parent'] ? $row['parent'].': ' : '').$row['name'];
					$not_query .= " AND loc.name != '{$row['name']}'";
				}
				$limit -= $result->count();
			}

			// Select from starting matches if searching by name or select from all
			$result = DB::query(Database::SELECT,
				"SELECT loc.name AS name, p.id, p.name AS parent
				FROM kwalbum_locations loc
				LEFT JOIN kwalbum_locations p ON (p.id = loc.parent_location_id)
				WHERE {$not_query} AND (loc.name LIKE :partName {$parent_query}) AND (loc.count >= :min_count OR (!loc.parent_location_id AND loc.child_count >= :min_count))
				  AND loc.name_hide_level <= :permission_level
				{$order}"
				.($limit ? ' LIMIT :limit' : null))
				->param(':partName', $partName)
				->param(':name', $name)
				->param(':min_count', (int)$min_count)
				->param(':permission_level', (int)$user->permission_level)
				->param(':limit', $limit)
				->execute();

			if ($result->count() > 0)
			{
				foreach ($result as $row)
				{
					$locations[] = ($row['parent'] ? $row['parent'].': ' : '').$row['name'];
					$not_query .= " AND loc.name != '{$row['name']}'";
				}
				$limit -= $result->count();
			}

			// Select from any partial matches if the result limit hasn't been reached yet
			if ($limit > 0)
			{
				$partName = "%{$name}%";
				if ($parent_name)
					$parent_query = "OR p.name LIKE '%{$parent_name}%'";
				$result = DB::query(Database::SELECT,
					"SELECT loc.name AS name, p.id, p.name AS parent
					FROM kwalbum_locations loc
					LEFT JOIN kwalbum_locations p ON (p.id = loc.parent_location_id)
					WHERE {$not_query} AND (loc.name LIKE :partName {$parent_query}) AND (loc.count >= :min_count OR (!loc.parent_location_id AND loc.child_count >= :min_count))
					  AND loc.name_hide_level <= :permission_level
					{$order}"
					.($limit ? ' LIMIT :limit' : null))
					->param(':partName', $partName)
					->param(':name', $name)
					->param(':min_count', (int)$min_count)
					->param(':permission_level', (int)$user->permission_level)
					->param(':limit', $limit)
					->execute();

				foreach ($result as $row)
				{
					$locations[] = ($row['parent'] ? $row['parent'].': ' : '').$row['name'];
				}
			}
		}
		else
		{
			$result = DB::query(Database::SELECT,
				"SELECT loc.name AS name, p.id, p.name AS parent
				FROM kwalbum_locations loc
				LEFT JOIN kwalbum_locations p ON (p.id = loc.parent_location_id)
				WHERE (loc.count >= :min_count OR (!loc.parent_location_id AND loc.child_count >= :min_count))
				  AND loc.name_hide_level <= :permission_level
				{$order}"
				.($limit ? ' LIMIT :offset,:limit' : null))
				->param(':offset', $offset)
				->param(':min_count', (int)$min_count)
				->param(':permission_level', (int)$user->permission_level)
				->param(':limit', $limit)
				->execute();

			foreach ($result as $row)
			{
				$locations[] = ($row['parent'] ? $row['parent'].': ' : '').$row['name'];
			}
		}

		if (!$order)
			sort($locations);

		return $locations;
	}

	static public function getMarkers($left, $right, $top, $bottom, &$data) {
		$where_query = " WHERE latitude IS NOT NULL AND latitude != 0"
			." AND latitude >= '$bottom' AND latitude <= '$top'"
			.($left>$right
				? " AND (longitude >= '$left' OR longitude <= '$right')"
				: " AND longitude >= '$left' AND longitude <= '$right'");
		$query = 'SELECT id, name, latitude as lat, longitude as lon, (count+child_count AS count), thumbnail_item_id, description'
			.' FROM kwalbum_locations'
			.$where_query
			.' ORDER BY count DESC'
			.' LIMIT 10';
		$result = DB::query(Database::SELECT, $query)
			->execute();
		foreach($result as $row) {
			$data[] = $row;
		}
		return;
	}

	static public function updateCounts() {
		DB::query(Database::UPDATE, "
			UPDATE kwalbum_locations loc
			SET count = (
				SELECT count(*)
				FROM kwalbum_items i
				WHERE i.location_id = loc.id
			)
		")->execute();
		DB::query(Database::UPDATE, "
			UPDATE kwalbum_locations p
			LEFT JOIN kwalbum_locations loc ON (loc.parent_location_id = p.id)
			SET p.child_count = 0
			WHERE loc.parent_location_id IS NULL
		")->execute();
		$result = DB::query(Database::SELECT, "
			SELECT parent_location_id, count(*)
			FROM kwalbum_items
			JOIN kwalbum_locations loc ON (loc.id = location_id)
			WHERE parent_location_id
			GROUP BY parent_location_id
			")->execute();
		foreach ($result as $row) {
			DB::query(Database::UPDATE,
				"UPDATE kwalbum_locations a SET child_count = :count WHERE id=:id")
				->param(':count',$row['count(*)'])
				->param(':id',$row['parent_location_id'])
				->execute();
		}
	}
}
