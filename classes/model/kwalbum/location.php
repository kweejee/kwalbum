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
	public $id = 0, $name = '', $latitude = 0.0, $longitude = 0.0, $count = 0;

	public function load($id = null)
	{
		if ($id === null)
		{
			$this->clear();
			return $this;
		}

		$result = DB::query(Database::SELECT,
			"SELECT name, latitude, longitude, count
			FROM kwalbum_locations
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
		$this->latitude = $row['latitude'];
		$this->longitude = $row['longitude'];
		$this->count = $row['count'];
		$this->loaded = true;

		return $this;
	}

	public function save()
	{
		$id = $this->id;

		if ($id == 0)
		{
			$query = DB::query(Database::INSERT, "INSERT INTO kwalbum_locations
				(name, latitude, longitude, count)
				VALUES (:name, :latitude, :longitude, :count)");
		}
		else
		{
			$query = DB::query(Database::UPDATE, "UPDATE kwalbum_locations
				SET name = :name, latitude = :latitude, longitude = :longitude, count= :count
				WHERE id = :id")
				->param(':id', $id);
		}
		$query
			->param(':name', $this->name)
			->param(':latitude', $this->latitude)
			->param(':longitude', $this->longitude)
			->param(':count', $this->count);

		$result = $query->execute();
		if ($id == 0)
		{
			$this->id = $result[0];
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

		$db = $this->_db;
		$count = $db->query(Database::UPDATE, "UPDATE kwalbum_items SET location_id = 1 WHERE location_id=$id");
		$db->query(Database::UPDATE, "UPDATE kwalbum_locations SET count = count+$count WHERE id=1");
		$db->query(Database::DELETE, "DELETE FROM kwalbum_locations WHERE id=$id");

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

}
