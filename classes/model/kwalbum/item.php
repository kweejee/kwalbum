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
	public $id, $type, $user_id, $location,
		$visible_date, $sort_date, $update_date, $create_date,
		$description, $latitude, $longitude, $path, $filename,
		$has_comments, $hide_level, $count, $is_external, $loaded;
	private $_user_name, $_original_location, $_original_user_id,
		 $_location_id, $_tags, $_persons, $_comments,
		 $_external_site, $_external_id;

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
		$this->user_id = $this->_original_user_id = (int)$row['user_id'];
		$this->_location_id = (int)$row['location_id'];
		$this->visible_date = $row['visible_dt'];
		$this->sort_date = $row['sort_dt'];
		$this->update_date = $row['update_dt'];
		$this->create_date = $row['create_dt'];
		$this->description = $row['description'];
		$this->latitude = $row['latitude'];
		$this->longitude = $row['longitude'];
		$this->path = $row['path'];
		$this->filename = $row['filename'];
		$this->has_comments = (bool)$row['has_comments'];
		$this->hide_level = (int)$row['hide_level'];
		$this->count = (int)$row['count'];
		$this->is_external = (bool)$row['is_external'];
		$this->_tags = $this->_persons = $this->_comments = $this->_location = $this->_user_name = null;

		$result = DB::query(Database::SELECT,
			"SELECT name
			FROM kwalbum_locations
			WHERE id = :id
			LIMIT 1")
			->param(':id', $this->_location_id)
			->execute();
		$this->location = $this->_original_location = $result[0]['name'];

		$this->loaded = true;
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
					$location_id = $result[0];
				}
				else
				{
					$location_id = $result[0]['id'];
				}
			}

			// Update count on new location
			$result = DB::query(Database::UPDATE, "UPDATE kwalbum_locations
				SET count = count+1
				WHERE id = :id")
				->param(':id', $location_id)
				->execute();
		}

		// Save any changes to location id and original name
		$this->_location_id = $location_id;
		$this->_original_location_name = $this->location;

		// Set user

		$user_id = $this->user_id;


		// Save actual item

		if ($this->loaded == false)
		{
			$query = DB::query(Database::INSERT,
				"INSERT INTO kwalbum_items
				(type_id, location_id, user_id)
				VALUES (:type_id, :location_id, :user_id)");
		}
		else
		{
			$query = DB::query(Database::UPDATE,
				"UPDATE kwalbum_items
				SET type_id = :type_id, location_id = :location_id, user_id = :user_id
				WHERE id = :id")
				->param(':id', $this->id);
		}
		$query
			->param(':type_id', $type_id)
			->param(':location_id', $location_id)
			->param(':user_id', $user_id);

		$result = $query->execute();

		if ($this->loaded == false)
		{
			$this->id = $result[0];
			$this->loaded = true;
		}



		// Set tags and persons once we know we have an item_id for the relationship

		// Remove old item-person and item-tag relations
		$this->_delete_person_relations();
		$this->_delete_tag_relations();

		// Remove duplicates of new person and tags
		// Use __get to make sure the array exists
		$this->_persons = array_unique($this->persons);
		$this->_tags = array_unique($this->tags);

		// Create new item-person and item-tag relations
		$person = Model::factory('kwalbum_person');
		foreach($this->_persons as $name)
		{
			$person->clear();
			$person->name = $name;
			$person->save();
			DB::query(Database::INSERT, "INSERT INTO kwalbum_items_persons
				(item_id, person_id)
				VALUES (:item_id, :person_id)")
				->param(':item_id', $this->id)
				->param(':person_id', $person->id)
				->execute();;
			$person->count = $person->count+1;
			$person->save();
		}
		$tag = Model::factory('kwalbum_tag');
		foreach($this->_tags as $name)
		{
			$tag->clear();
			$tag->name = $name;
			$tag->save();
			DB::query(Database::INSERT, "INSERT INTO kwalbum_items_tags
				(item_id, tag_id)
				VALUES (:item_id, :tag_id)")
				->param(':item_id', $this->id)
				->param(':tag_id', $tag->id)
				->execute();;
			$tag->count = $tag->count+1;
			$tag->save();
		}

		//echo Kohana::debug($query);exit;
	}
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

		// Remove item-person relations and reduce persons' item counts
		$this->_delete_person_relations($id);

		// Remove item-tag relations and reduce tags' item counts
		$this->_delete_tag_relations($id);

		// Delete the item
		DB::query(Database::DELETE, "DELETE FROM kwalbum_items
			WHERE id = :id")
			->param(':id', $id)
			->execute();

		// Delete comments
		$comments = $this->_load_comments($id);
		foreach ($comments as $comment)
		{
			$comment->delete();
		}

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
			{
				$this->_tags = array();
				$result = DB::query(Database::SELECT,
					"SELECT name
					FROM kwalbum_items_tags
						LEFT JOIN kwalbum_tags ON tag_id = id
					WHERE item_id = :id")
					->param(':id', $this->id)
					->execute();
				foreach($result as $row)
				{
					$this->_tags[] = $row['name'];
				}
			}

			return $this->_tags;
		}
		else if ($id == 'persons')
		{
			if ($this->_persons === null)
			{
				$this->_persons = array();
				$result = DB::query(Database::SELECT,
					"SELECT name
					FROM kwalbum_items_persons
						LEFT JOIN kwalbum_persons ON person_id = id
					WHERE item_id = :id")
					->param(':id', $this->id)
					->execute();
				foreach($result as $row)
				{
					$this->_persons[] = $row['name'];
				}
			}

			return $this->_persons;
		}
		else if ($id == 'comments')
		{
			if ($this->_comments === null)
			{
				$this->_comments = $this->_load_comments($this->id);
			}
			return $this->_comments;
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
		else if ($id == 'external_site' or $id == 'external_id')
		{
			if ($this->_external_site === null)
			{
				$result = DB::query(Database::SELECT,
					"SELECT site_id, external_item_id
					FROM kwalbum_items_sites
					WHERE item_id = :id
					LIMIT 1")
					->param(':id', $this->item_id)
					->execute();
				$site_id = $result[0]['site_id'];
				$this->_external_id = $result[0]['external_item_id'];
				$this->_external_site = Model::factory('kwalbum_site')->load($site_id);
			}
			return $this->_external_site;
		}
	}

	public function __set($key, $value)
	{
		// Overwrite tags if an array is given or add to the existing array
		if ($key == 'tags')
		{
			if (is_array($value))
				$this->_tags = $value;
			else
			{
				if ($this->_tags === null)
				{
					$this->_tags = $this->tags;
				}
				$this->_tags[] = (string)$value;
			}
		}

		// Overwrite persons if an array is given or add to the existing array
		elseif ($key == 'persons')
		{
			if (is_array($value))
				$this->_persons = $value;
			else
			{
				if ($this->_persons === null)
				{
					$this->_persons = $this->persons;
				}
				$this->_persons[] = (string)$value;
			}
		}

		// Overwrite comments if an array is given or add to the existing array
		elseif ($key == 'comments')
		{
			if (is_array($value))
				$this->_comments = $value;
			else
			{
				if ($this->_comments === null)
				{
					$this->_comments = $this->comments;
				}
				$this->_comments[] = (string)$value;
			}
		}
	}

	public function clear()
	{
		$this->id = $this->user_id = $this->location_id = $this->latitude
			= $this->longitude = $this->hide_level = $this->count
			= $this->_external_id = 0;
		$this->has_comments = $this->is_external = false;
		$this->description = $this->visible_date = $this->sort_date = $this->update_date
			= $this->create_date = $this->path = $this->filename = '';
		$this->_tags = $this->_persons = $this->_comments = $this->_location = $this->_user_name
			= $this->_original_location = $this->_original_user_id
			= $this->_external_site = null;
		$this->type = $this->types[0];
		$this->loaded = false;
	}

	private function _delete_person_relations($id = null)
	{
		if ($id === NULL)
		{
			$id = $this->id;
		}

		// Remove item from person counts
		$result = DB::query(Database::UPDATE,
			"UPDATE kwalbum_persons
			SET count = count-1
			WHERE id IN
				(SELECT person_id
					FROM kwalbum_items_persons
					WHERE item_id = :id
				)")
			->param(':id', $id)
			->execute();

		if ($result > 0)
		{
			// Remove relations between item and persons
			$result = DB::query(Database::DELETE,
				"DELETE FROM kwalbum_items_persons
				WHERE item_id = :id")
				->param(':id', $id)
				->execute();
		}
	}

	private function _delete_tag_relations($id = null)
	{
		if ($id === NULL)
		{
			$id = $this->id;
		}

		// Remove item from tag counts
		$result = DB::query(Database::UPDATE,
			"UPDATE kwalbum_tags
			SET count = count-1
			WHERE id IN
				(SELECT tag_id
					FROM kwalbum_items_tags
					WHERE item_id = :id
				)")
			->param(':id', $id)
			->execute();

		if ($result > 0)
		{
			// Remove relations between item and tags
			$result = DB::query(Database::DELETE,
				"DELETE FROM kwalbum_items_tags
				WHERE item_id = :id")
				->param(':id', $id)
				->execute();
		}
	}

	private function _load_comments($id = null)
	{
		if ($id === NULL)
		{
			$id = $this->id;
		}

		$comments = array();

		$result = DB::query(Database::SELECT,
			"SELECT id
			FROM kwalbum_comments
			WHERE item_id = :id")
			->param(':id', $this->id)
			->execute();

		foreach ($result as $row)
		{
			$comments[] = Model::factory('kwalbum_comment')->load($row['id']);
		}

		return $comments;
	}
}
