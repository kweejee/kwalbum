<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 *
 *
 * @author Tim Redmond
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since 3.0 Jul 6, 2009
 */
class Model_Kwalbum_Location extends Kwalbum_Model
{
    public $id, $name, $latitude, $longitude, $count, $child_count, $thumbnail_item_id, $parent_name,
        $name_hide_level, $coordinate_hide_level, $description;
    private $_original_name;

    /**
     *
     * @param int|string|null $id_or_name
     * @return $this
     */
    public function __construct(int|string $id_or_name = null)
    {
        if (is_int($id_or_name)) {
            $this->load($id_or_name);
            return $this;
        } else {
            $this->clear();
            if ($id_or_name) {
                $id_or_name = self::htmlspecialchars($id_or_name);
            }
        }

        if ($id_or_name !== null) {
            // Cleanup name
            $names = explode(trim(self::get_config('location_separator_1')), $id_or_name);
            foreach ($names as $i => &$name) {
                $name = trim($name);
                if (!$name) {
                    unset($names[$i]);
                }
            }
            if (count($names) > 1) {
                $this->parent_name = $names[0];
                // combine children
                array_shift($names);
                $this->name = implode(self::get_config('location_separator_2'), $names);
            } else {
                $this->name = $names[0];
            }

            // Maybe find existing location by name
            if ($this->parent_name) {
                $query = DB::query(Database::SELECT, "
                    SELECT loc.id
                    FROM kwalbum_locations loc
                    LEFT JOIN kwalbum_locations p ON (p.id = loc.parent_location_id)
                    WHERE loc.name = :name AND p.name = :parent_name
                    LIMIT 1")
                    ->param(':parent_name', $this->parent_name);
            } else {
                $query = DB::query(Database::SELECT, "
                    SELECT id
                    FROM kwalbum_locations
                    WHERE name = :name
                    LIMIT 1");
            }
            $result = $query->param(':name', $this->name)
                ->execute();
            if ($result->count() != 0) {
                $this->load($result[0]['id']);
            }
        }

        return $this;
    }


    /**
     * Load a location where $field equals $value
     *
     * @param int $value id
     * @return Model_Kwalbum_Location
     */
    public function load($value = null): Model_Kwalbum_Location
    {
        $this->clear();
        if (is_null($value)) {
            return $this;
        }
        $query = DB::query(Database::SELECT,
            "SELECT loc.*, p.name AS parent_name
            FROM kwalbum_locations loc
            LEFT JOIN kwalbum_locations p ON (p.id = loc.parent_location_id)
            WHERE loc.id = :value
            LIMIT 1")
            ->param(':value', $value);
        $result = $query->execute();
        if ($result->count() == 0) {
            return $this;
        }
        $row = $result[0];

        $this->id = (int)$row['id'];
        $this->name = $row['name'];
        $this->_original_name = $this->name;
        $this->parent_name = $row['parent_name'];
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
    public function save(): Model_Kwalbum_Location
    {
        $id = $this->id;
        $this->name = self::htmlspecialchars($this->name);

        $parent_id = 0;
        if ($this->parent_name) {
            if ($this->parent_name != $this->_original_name) {
                $result = DB::query(Database::SELECT,
                    "SELECT id
                    FROM kwalbum_locations
                    WHERE name = :name
                        AND parent_location_id = 0
                    ORDER BY id ASC
                   LIMIT 1")
                    ->param(':name', $this->parent_name)
                    ->execute();
            }
            if (isset($result) && $result->count() > 0) {
                $parent_id = $result[0]['id'];
            } else {
                $parent = clone $this;
                $parent->id = 0;
                $parent->name = '_____temporary name_____' . time();
                $parent->parent_name = '';
                $parent_id = $parent->save()->id;
                $parent->name = $this->parent_name;
                $parent->save();
            }
        }

        if ($id == 0) {
            $result = DB::query(Database::SELECT,
                "SELECT id, latitude, longitude, count, child_count
                FROM kwalbum_locations
                WHERE name = :name AND parent_location_id = :parent_id
                LIMIT 1")
                ->param(':name', $this->name)
                ->param(':parent_id', (int)$parent_id)
                ->execute();
            if ($result->count() == 0) {
                $result = DB::query(Database::INSERT,
                    "INSERT INTO kwalbum_locations
                    (name, latitude, longitude, count, child_count, thumbnail_item_id, parent_location_id,
                        name_hide_level, coordinate_hide_level, description)
                    VALUES (:name, :latitude, :longitude, :count, :child_count, :thumbnail_item_id, :parent_id,
                        :name_hide_level, :coordinate_hide_level, :description)")
                    ->param(':name', $this->name)
                    ->param(':latitude', $this->latitude ?: 0)
                    ->param(':longitude', $this->longitude ?: 0)
                    ->param(':count', (int)$this->count)
                    ->param(':child_count', (int)$this->child_count)
                    ->param(':thumbnail_item_id', $this->thumbnail_item_id ?: 0)
                    ->param(':parent_id', (int)$parent_id)
                    ->param(':name_hide_level', (int)$this->name_hide_level)
                    ->param(':coordinate_hide_level', (int)$this->coordinate_hide_level)
                    ->param(':description', $this->description ?: '')
                    ->execute();
                $this->id = $result[0];

                Model_Kwalbum_Location::updateCounts();
                return $this;
            }

            $row = $result[0];
            $this->id = (int)$row['id'];
            if (!$this->latitude) {
                $this->latitude = (float)$row['latitude'];
            }
            if (!$this->longitude) {
                $this->longitude = (float)$row['longitude'];
            }
            if (!$this->count) {
                $this->count = (int)$row['count'];
            }
            if (!$this->child_count) {
                $this->child_count = (int)$row['child_count'];
            }
        } else {
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
                ->param(':thumbnail_item_id', (int)$this->thumbnail_item_id)
                ->param(':parent_id', (int)$parent_id)
                ->param(':name_hide_level', (int)$this->name_hide_level)
                ->param(':coordinate_hide_level', (int)$this->coordinate_hide_level)
                ->param(':description', $this->description ? $this->description : '');
            $query->execute();
        }

        Model_Kwalbum_Location::updateCounts();
        return $this;
    }

    /**
     * Delete a location and all connections it has to items.
     *
     * @return boolean
     */
    public function delete(): bool
    {
        // do not delete the "unknown" location
        if ($this->id === 1) {
            return false;
        }

        $parent_id = 0;
        $result = DB::query(Database::SELECT,
            "SELECT parent_location_id
            FROM kwalbum_locations
            WHERE id = :id")
            ->param(':id', $this->id)
            ->execute();
        if (count($result) == 1) {
            $parent_id = $result[0]['parent_location_id'];
        }
        $new_location_id = $parent_id ? $parent_id : 1;
        $count = DB::query(Database::UPDATE,
            "UPDATE kwalbum_items
            SET location_id = :new_id
            WHERE location_id = :id")
            ->param(':id', $this->id)
            ->param(':new_id', $new_location_id)
            ->execute();
        DB::query(Database::UPDATE,
            "UPDATE kwalbum_locations
            SET count = count+:count
            WHERE id = :new_id")
            ->param(':count', $count)
            ->param(':new_id', $new_location_id)
            ->execute();
        if ($parent_id) {
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
            ->param(':id', $this->id)
            ->execute();

        $this->clear();

        return true;
    }

    public function clear()
    {
        $this->id = $this->latitude = $this->longitude = $this->count = $this->child_count = $this->thumbnail_item_id
            = $this->name_hide_level = $this->coordinate_hide_level = 0;
        $this->name = $this->parent_name = $this->description = '';
        $this->loaded = false;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return self::getFullName($this->parent_name, $this->name);
    }

    /**
     * Get full name including parent name if set
     *
     * @param string|null $parent_name
     * @param string $name
     * @return string
     */
    public static function getFullName(?string $parent_name, string $name): string
    {
        $parent = trim($parent_name ?? '') ? trim($parent_name) . self::get_config('location_separator_1') : '';
        return $parent . trim($name);
    }

    public function __get($key)
    {
        switch ($key) {
            case 'name_hide_level_description':
                return Model_Kwalbum_Item::$hide_level_names[$this->name_hide_level];
            case 'coordinate_hide_level_description':
                return Model_Kwalbum_Item::$hide_level_names[$this->coordinate_hide_level];
        }
    }

    static public function getAllArray($order_by = '')
    {
        $result = DB::query(Database::SELECT,
            "SELECT loc.*, p.name AS parent_name
            FROM kwalbum_locations loc
            LEFT JOIN kwalbum_locations p ON (p.id = loc.parent_location_id)
            {$order_by}")
            ->execute();
        return $result;
    }

    /**
     *
     * @param Model_Kwalbum_User $user
     * @param int $min_count
     * @param int|null $limit
     * @param int $offset
     * @param string $name
     * @param string $order
     * @return array
     */
    static public function get_name_array(Model_Kwalbum_User &$user, int $min_count = 1, int $limit = null, int $offset = 0, string $name = '', string $order = ''): array
    {
        $name = trim($name);
        if ($order) {
            $order = 'ORDER BY ' . $order;
        }

        $locations = array();
        if (!empty($name)) {
            // Split the parent location name from the specific location name
            $parent_name = '';
            $parent_query = '';
            $names = explode(trim(self::get_config('location_separator_1')), $name);
            if (count($names) > 1) {
                $parent_name = trim($names[0]);
                array_shift($names);
                foreach ($names as &$n) {
                    $n = trim($n);
                }
                $name = implode(self::get_config('location_separator_2'), $names);
            }
            $part_name = "{$name}%";
            if ($parent_name) {
                $parent_query = "AND p.name = :parent_name";
            }
            $not_names = [''];

            // Select almost exact (not case sensitive) match first
            if (strlen($name) > 0) {
                $result = DB::query(Database::SELECT,
                    'SELECT loc.name AS name, p.id, p.name AS parent
                    FROM kwalbum_locations loc
                    LEFT JOIN kwalbum_locations p ON (p.id = loc.parent_location_id)
                    WHERE loc.name = :name
                      AND loc.name_hide_level <= :permission_level')
                    ->param(':name', $name)
                    ->param(':permission_level', (int)$user->permission_level)
                    ->execute();
                if ($result->count() > 0) {
                    foreach ($result as $row) {
                        $locations[] = ($row['parent'] ? $row['parent'] . self::get_config('location_separator_1') : '') . $row['name'];
                        $not_names[] = $row['name'];
                    }
                    if ($limit) {
                        $limit -= $result->count();
                    }
                }
            }

            // Select from starting matches if searching by name or select from all
            $query = DB::query(Database::SELECT,
                "SELECT loc.name AS name, p.id, p.name AS parent
                FROM kwalbum_locations loc
                LEFT JOIN kwalbum_locations p ON (p.id = loc.parent_location_id)
                WHERE loc.name != :name AND loc.name NOT IN :not_names
                  AND loc.name LIKE :part_name AND loc.count >= :min_count {$parent_query}
                  AND loc.name_hide_level <= :permission_level
                {$order}"
                . ($limit ? ' LIMIT :limit' : null))
                ->param(':part_name', $part_name)
                ->param(':name', $name)
                ->param(':not_names', $not_names)
                ->param(':min_count', (int)$min_count)
                ->param(':permission_level', (int)$user->permission_level)
                ->param(':limit', $limit);
            if ($parent_name) {
                $query = $query->param(':parent_name', $parent_name);
            }
            $result = $query->execute();

            if ($result->count() > 0) {
                $new_locations = array();
                foreach ($result as $row) {
                    $new_locations[] = ($row['parent'] ? $row['parent'] . self::get_config('location_separator_1') : '') . $row['name'];
                    $not_names[] = $row['name'];
                }
                if (!$order) {
                    usort($new_locations, 'strnatcasecmp');
                }
                $locations = array_merge($locations, $new_locations);
                if ($limit) {
                    $limit -= $result->count();
                }
            }

            // Select from any partial matches if the result limit hasn't been reached yet
            if ($limit > 0) {
                $part_name = "%{$name}%";
                $query = DB::query(Database::SELECT,
                    "SELECT loc.name AS name, p.id, p.name AS parent
                    FROM kwalbum_locations loc
                    LEFT JOIN kwalbum_locations p ON (p.id = loc.parent_location_id)
                    WHERE loc.name != :name AND loc.name NOT IN :not_names
                      AND loc.name LIKE :part_name {$parent_query} AND loc.count >= :min_count
                      AND loc.name_hide_level <= :permission_level
                    {$order}"
                    . ($limit ? ' LIMIT :limit' : null))
                    ->param(':part_name', $part_name)
                    ->param(':name', $name)
                    ->param(':not_names', $not_names)
                    ->param(':min_count', (int)$min_count)
                    ->param(':permission_level', (int)$user->permission_level)
                    ->param(':limit', $limit);
                if ($parent_name) {
                    $query = $query->param(':parent_name', $parent_name);
                }
                $result = $query->execute();

                $new_locations = array();
                foreach ($result as $row) {
                    $new_locations[] = ($row['parent'] ? $row['parent'] . self::get_config('location_separator_1') : '') . $row['name'];
                    $not_names[] = $row['name'];
                }
                if (!$order) {
                    usort($new_locations, 'strnatcasecmp');
                }
                $locations = array_merge($locations, $new_locations);
                if ($limit) {
                    $limit -= $result->count();
                }
            }

            // Select from starting matches of parent if the result limit hasn't been reached yet
            if (!$parent_name) {
                $part_name = "{$name}%";
                $result = DB::query(Database::SELECT,
                    "SELECT loc.name AS name, p.id, p.name AS parent
                    FROM kwalbum_locations loc
                    LEFT JOIN kwalbum_locations p ON (p.id = loc.parent_location_id)
                    WHERE loc.name != :name AND loc.name NOT IN :not_names
                      AND p.name LIKE :part_name AND p.child_count >= :min_count
                      AND loc.name_hide_level <= :permission_level
                    {$order}"
                    . ($limit ? ' LIMIT :limit' : null))
                    ->param(':part_name', $part_name)
                    ->param(':name', $name)
                    ->param(':not_names', $not_names)
                    ->param(':min_count', (int)$min_count)
                    ->param(':permission_level', (int)$user->permission_level)
                    ->param(':limit', $limit)
                    ->execute();

                if ($result->count() > 0) {
                    $new_locations = array();
                    foreach ($result as $row) {
                        $new_locations[] = ($row['parent'] ? $row['parent'] . self::get_config('location_separator_1') : '') . $row['name'];
                    }
                    if (!$order) {
                        usort($new_locations, 'strnatcasecmp');
                    }
                    $locations = array_merge($locations, $new_locations);
                    if ($limit) {
                        $limit -= $result->count();
                    }
                }
            }
        } else {
            $result = DB::query(Database::SELECT,
                "SELECT loc.name AS name, p.id, p.name AS parent
                FROM kwalbum_locations loc
                LEFT JOIN kwalbum_locations p ON (p.id = loc.parent_location_id)
                WHERE (loc.count >= :min_count OR (!loc.parent_location_id AND loc.child_count >= :min_count))
                  AND loc.name_hide_level <= :permission_level
                {$order}"
                . ($limit ? ' LIMIT :offset,:limit' : null))
                ->param(':offset', $offset)
                ->param(':min_count', (int)$min_count)
                ->param(':permission_level', (int)$user->permission_level)
                ->param(':limit', $limit)
                ->execute();

            foreach ($result as $row) {
                $locations[] = ($row['parent'] ? $row['parent'] . self::get_config('location_separator_1') : '') . $row['name'];
            }
            if (!$order) {
                usort($locations, 'strnatcasecmp');
            }
        }

        return $locations;
    }

    static public function getMarkers($left, $right, $top, $bottom, &$data)
    {
        $where_query = " WHERE latitude IS NOT NULL AND latitude != 0"
            . " AND latitude >= '$bottom' AND latitude <= '$top'"
            . ($left > $right
                ? " AND (longitude >= '$left' OR longitude <= '$right')"
                : " AND longitude >= '$left' AND longitude <= '$right'");
        $query = 'SELECT id, name, latitude as lat, longitude as lon, (count+child_count AS count), thumbnail_item_id, description'
            . ' FROM kwalbum_locations'
            . $where_query
            . ' ORDER BY count DESC'
            . ' LIMIT 10';
        $result = DB::query(Database::SELECT, $query)
            ->execute();
        foreach ($result as $row) {
            $data[] = $row;
        }
        return;
    }

    static public function updateCounts()
    {
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
                ->param(':count', $row['count(*)'])
                ->param(':id', $row['parent_location_id'])
                ->execute();
        }
    }
}
