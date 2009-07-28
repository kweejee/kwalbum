<?php
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

class Model_Kwalbum_Item extends Kwalbum_Model
{
	protected $belongs_to = array('user' => 'kwalbum_user', 'location' => 'kwalbum_location');
	protected $has_many = array('kwalbum_comments', 'kwalbum_items_sites');
	protected $has_and_belongs_to_many = array('kwalbum_tags', 'kwalbum_persons');

	public $id, $type, $user_id, $location,
		$visible_date, $sort_date, $update_date, $create_date,
		$description, $latitude, $longitude, $path, $filename,
		$has_comments, $hide_level, $count, $is_external;
	private $_user_name, $_original_location, $_original_user_id,
		 $_location_id, $_tags, $_persons, $_comments;
	protected $types = array(
			0 => 'unknown',
			1 => 'gif', 2 => 'jpg', 3 => 'png',
			40 => 'wmv',
			41 => 'txt',
			42 => 'mp3',
			43 => 'zip',
			44 => 'html',
			45 => 'divx',
			46 => 'ogg',
			47 => 'wav',
			48 => 'xml', 49 => 'gpx',
			50 => 'ods', 51 => 'odt',
			52 => 'flv',
			53 => 'doc',
			54 => 'mpeg',
			55 => 'mp4',
			255 => 'description only'
		);

	public function load($id = null)
	{
		if ($id === null)
		{
			$this->clear();
			return $this;
		}

		$result = DB::query(Database::SELECT,
			"SELECT *
			FROM kwalbum_items
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
		$this->type = $this->types[$row['type_id']];
		$this->user_id = $this->_original_user_id = $row['user_id'];
		$this->_location_id = $row['location_id'];
		$this->visible_date = $row['visible_dt'];
		$this->sort_date = $row['sort_dt'];
		$this->update_date = $row['update_dt'];
		$this->create_date = $row['create_dt'];
		$this->description = $row['description'];
		$this->latitude = $row['latitude'];
		$this->longitude = $row['longitude'];
		$this->path = $row['path'];
		$this->filename = $row['filename'];
		$this->has_comments = $row['has_comments'];
		$this->hide_level = $row['hide_level'];
		$this->count = $row['count'];
		$this->is_external = $row['is_external'];
		$this->_tags = $this->_persons = $this->_comments = $this->_location = $this->_user_name = null;
		$this->loaded = true;

		$result = DB::query(Database::SELECT,
			"SELECT name
			FROM kwalbum_locations
			WHERE id = :id
			LIMIT 1")
			->param(':id', $this->_location_id)
			->execute();
		$this->location = $this->_original_location = $result[0]['name'];

		return $this;
	}

	public function save()
	{
		// Set type

		$types = array_flip($this->types);
		$type_id = $types[$this->type];

		// Set location

		// Item has an original location so check for name changes
		if ($this->_location_id)
		{
			// Update original location's item count if the name is different
			if ($this->location != $this->_original_location)
			{
				DB::query(Database::UPDATE, "UPDATE kwalbum_locations
					SET count = count-1
					WHERE name = :id")
					->param(':id', $this->_location_id)
					->execute();
			}

			// Use the original id if there are no changes
			else
			{
				$location_id = $this->_location_id;
			}
		}

		// If there is no location id set then get id for new location name
		if (!isset($location_id))
		{
			// The location name is unknown so use default unknown id and name
			if (empty($this->location))
			{
				$location_id = 1;
				$result = DB::query(Database::SELECT,
					"SELECT name
					FROM kwalbum_locations
					WHERE id = 1
					LIMIT 1")
					->execute();
				$this->location = $this->_original_location = $result[0]['name'];
			}

			// Get new location id for known name
			else
			{
				// Get id if new location already exists
				$result = DB::query(Database::SELECT,
					"SELECT id
					FROM kwalbum_locations
					WHERE name = :name
					LIMIT 1")
					->param(':name', $this->location)
					->execute();

				// If new location does not exist then create it
				if ($result->count() == 0)
				{
					$result = DB::query(Database::INSERT, "INSERT INTO kwalbum_locations
						(name)
						VALUES (:name)")
						->param(':name', $this->location)
						->execute();
				}
				$location_id = $result[0]['id'];
			}

			// Update count on new location
			DB::query(Database::UPDATE, "UPDATE kwalbum_locations
				SET count = count+1
				WHERE name = :id")
				->param(':id', $location_id)
				->execute();
		}

		// Save any changes to location id and original name
		$this->_location_id = $location_id;
		$this->_original_location_name = $this->location;


		if ($this->loaded == false)
		{
			$query = DB::query(Database::INSERT, "INSERT INTO kwalbum_items
				(type_id, location_id)
				VALUES (:type_id, :location_id)");
		}
		else
		{
			$query = DB::query(Database::UPDATE, "UPDATE kwalbum_items
				SET type_id = :type_id, location_id = :location_id
				WHERE id = :id")
				->param(':id', $this->id);
		}
		$query
			->param(':type_id', $type_id)
			->param(':location_id', $location_id);


		$result = $query->execute();
		if ($this->loaded == false)
		{
			$this->id = $result[0];
			$this->loaded = true;
		}
	}
//	public function save()
//	{
//		if (isset($this->changed['location_id']))
//		{
//			if ($this->id)
//			{
//				$this->db->query(Database::UPDATE, "UPDATE kwalbum_locations SET count=count-1 WHERE id=(SELECT location_id FROM kwalbum_items WHERE id=$this->id) AND count>0");
//			}
//			$this->db->query(Database::UPDATE, "UPDATE kwalbum_locations SET count=count+1 WHERE id=$this->location_id");
//		}
//
//		// add new tags
//		if (isset($this->changed_relations['kwalbum_tags']))
//		{
//			if (isset($this->object_relations['kwalbum_tags']))
//				$new_tags = array_diff($this->changed_relations['kwalbum_tags'],$this->object_relations['kwalbum_tags']);
//			else
//				$new_tags = $this->changed_relations['kwalbum_tags'];
//			foreach ($new_tags as $tag)
//				$this->db->query(Database::UPDATE, "UPDATE kwalbum_tags SET count=count+1 WHERE id=$tag");
//		}
//		// remove old tags
//		if (isset($this->object_relations['kwalbum_tags']))
//		{
//			if (isset($this->changed_relations['kwalbum_tags']))
//				$new_tags = array_diff($this->object_relations['kwalbum_tags'],$this->changed_relations['kwalbum_tags']);
//			else
//				$new_tags = $this->object_relations['kwalbum_tags'];
//			foreach ($new_tags as $tag)
//				$this->db->query(Database::UPDATE, "UPDATE kwalbum_tags SET count=count-1 WHERE id=$tag AND count>0");
//		}
//
//		// add new persons
//		if (isset($this->changed_relations['kwalbum_persons']))
//		{
//			if (isset($this->object_relations['kwalbum_persons']))
//				$new_persons = array_diff($this->changed_relations['kwalbum_persons'],$this->object_relations['kwalbum_persons']);
//			else
//				$new_persons = $this->changed_relations['kwalbum_persons'];
//			foreach ($new_persons as $person)
//				$this->db->query(Database::UPDATE, "UPDATE kwalbum_persons SET count=count+1 WHERE id=$person");
//		}
//		// remove old persons
//		if (isset($this->object_relations['kwalbum_persons']))
//		{
//			if (isset($this->changed_relations['kwalbum_persons']))
//				$new_persons = array_diff($this->object_relations['kwalbum_persons'],$this->changed_relations['kwalbum_persons']);
//			else
//				$new_persons = $this->object_relations['kwalbum_persons'];
//			foreach ($new_persons as $person)
//				$this->db->query(Database::UPDATE, "UPDATE kwalbum_persons SET count=count-1 WHERE id=$person AND count>0");
//		}
//		parent::save();
//	}
//	public function delete($id = null)
//	{
//		if ($id === null)
//		{
//			$id = $this->primary_key;
//		}
//
//		$this->db->query(Database::UPDATE, "UPDATE kwalbum_locations SET count=count-1 WHERE id=$this->location_id AND count>0");
//
//			foreach ($this->kwalbum_tags as $tag)
//			{
//				if (is_object($tag))
//				{//		echo Kohana::debug($this->kwalbum_tags);
//				$this->remove($tag);
////					$tag_id = $tag->id;
////					$this->db->query(Database::UPDATE, "UPDATE kwalbum_tags SET count=count-1 WHERE id=$tag_id AND count>0");
//				}
//			}
//		foreach ($this->kwalbum_persons as $person)
//		{
//			$person_id = $person->id;
//			$this->db->query(Database::UPDATE, "UPDATE kwalbum_persons SET count=count-1 WHERE id=$person_id AND count>0");
//		}
//		parent::delete($id);
//	}
	public function delete($id = NULL)
	{
		if ($id === NULL)
		{
			$id = $this->id;
		}

		// Remove item from location count
		if ($id == $this->id)
		{
			$location_id = $this->_location_id;
			$persons = $this->persons;
			$tags = $this->tags;
		}
		else
		{
			$result = DB::query(Database::SELECT, 'SELECT location_id
				FROM kwalbum_items
				WHERE item_id = :id')
				->param(':id', $id)
				->execute();
			$location_id = $result[0]['location_id'];
		}
		DB::query(Database::UPDATE, 'UPDATE kwalbum_locations
			SET count = count-1
			WHERE id = :location_id AND count > 0')
			->param(':location_id', $location_id)
			->execute();

		// Delete favorites of the item
		DB::query(Database::DELETE, 'DELETE FROM kwalbum_favorites
			WHERE item_id = :id')
			->param(':id', $id)
			->execute();

		// Delete relation between item and external site if it exists
		DB::query(Database::DELETE, "DELETE FROM kwalbum_items_sites
			WHERE item_id = :id")
			->param(':id', $id)
			->execute();

		// Remove item from person counts


		// Delete relations between item and persons
		DB::query(Database::DELETE, "DELETE FROM kwalbum_items_persons
			WHERE item_id = :id")
			->param(':id', $id)
			->execute();

		// Remove item from tag counts


		// Delete relations between item and tags
		DB::query(Database::DELETE, "DELETE FROM kwalbum_items_tags
			WHERE item_id = :id")
			->param(':id', $id)
			->execute();

		// Delete the item
		DB::query(Database::DELETE, "DELETE FROM kwalbum_items
			WHERE id = :id")
			->param(':id', $id)
			->execute();

		if ($id == $this->id)
		{
			$this->clear();
		}
	}

	public function __get($id)
	{
		if ($id == 'tags')
		{
			if ($this->_tags === null)

				$this->_tags = array();

			return $this->_tags;
		}
		else if ($id == 'persons')
		{
			return array();
		}
		else if ($id == 'comments')
		{
			return array();
		}
		else if ($id == 'user_name')
		{
			if ($this->_user_name === null)
			{
				$result = DB::query(Database::SELECT,
					"SELECT name
					FROM kwalbum_users
					WHERE id = :id
					LIMIT 1")
					->param(':id', $this->user_id)
					->execute();
				$this->_user_name = $result[0]['name'];
			}
			return $this->_user_name;
		}
	}

	public function __set($id, $value)
	{
		if ($id == 'location')
		{
			$this->_location = $value;
		}
	}

	public function clear()
	{
		$this->id = $this->user_id = $this->location_id = $this->latitude
			= $this->longitude = $this->hide_level = $this->count
			= $this->has_comments = $this->is_external = 0;
		$this->description = $this->visible_date = $this->sort_date = $this->update_date
			= $this->create_date = $this->path = $this->filename = '';
		$this->_tags = $this->_persons = $this->_comments = $this->_location = $this->_user_name
			= $this->_original_location = $this->_original_user_id = null;
		$this->type = $this->types[0];
		$this->loaded = false;
	}
}
