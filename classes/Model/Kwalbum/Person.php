<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2023 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since 3.0 Jul 6, 2009
 */
class Model_Kwalbum_Person extends Kwalbum_MultiModel
{
    public function load($value = null): Model_Kwalbum_Person
    {
        $this->clear();
        if (is_null($value)) {
            return $this;
        }

        $result = DB::query(Database::SELECT,
            "SELECT id, name, count
			FROM kwalbum_persons
			WHERE id = :value
			LIMIT 1")
            ->param(':value', $value)
            ->execute();
        self::hydrate_from_result($result);

        return $this;
    }

    /**
     * @throws Exception
     */
    public function insert_if_needed(): void
    {
        $this->escape_name();

        $result = DB::query(Database::SELECT,
            "SELECT id, count
				FROM kwalbum_persons
				WHERE name = :name
				LIMIT 1")
            ->param(':name', $this->name)
            ->execute();
        if ($result->count() == 0) {
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

        $this->id = (int)$result[0]['id'];
        $this->count = (int)$result[0]['count'];
        $this->loaded = true;
    }

    /**
     * @throws Exception
     */
    public function save()
    {
        if ($this->loaded === false) {
            $this->insert_if_needed();
        } else {
            $this->escape_name();
        }

        DB::query(Database::UPDATE,
            "UPDATE kwalbum_persons
			SET name = :name, count = :count
			WHERE id = :id")
            ->param(':id', $this->id)
            ->param(':name', $this->name)
            ->param(':count', $this->count)
            ->execute();
    }

    public function delete(): bool
    {
        // Delete relations between the person and items
        DB::query(Database::DELETE,
            "DELETE FROM kwalbum_items_persons
			WHERE person_id = :id")
            ->param(':id', $this->id)
            ->execute();

        // Delete the person
        DB::query(Database::DELETE,
            "DELETE FROM kwalbum_persons
			WHERE id = :id")
            ->param(':id', $this->id)
            ->execute();

        $this->clear();
    }

    static public function get_all_array($order = 'name ASC'): Database_Result_Cached
    {
        $result = DB::query(Database::SELECT,
            "SELECT *
			FROM kwalbum_persons
			ORDER BY $order")
            ->execute();
        return $result;
    }

    static public function get_name_array($min_count = 1, $limit = null, $offset = 0,
                                          $name = '', $order = 'name ASC', $not_included = array()): array
    {
        $name = trim($name);
        $tags = array();
        $query = '';
        $db = Database::instance();

        if (count($not_included) > 0) {
            foreach ($not_included as $word) {
                $query .= " AND name != " . $db->escape(self::htmlspecialchars($word));
            }
        }

        if ($name) {
            // Select almost exact (not case sensitive) match first
            $result = DB::query(Database::SELECT,
                'SELECT name
				FROM kwalbum_persons
				WHERE name = :name ' . $query)
                ->param(':name', self::htmlspecialchars($name))
                ->execute();
            if ($result->count() == 1) {
                $tags[] = $result[0]['name'];
                $limit--;
            }

            // Select from starting matches
            $partName = "$name%";
            $query .= ' AND name != :name';
            $result = DB::query(Database::SELECT,
                "SELECT name
				FROM kwalbum_persons
				WHERE name LIKE :partName AND count >= :min_count $query
				ORDER BY $order"
                . ($limit ? ' LIMIT :limit' : null))
                ->param(':partName', $partName)
                ->param(':name', $name)
                ->param(':min_count', $min_count)
                ->param(':limit', $limit)
                ->execute();

            if ($result->count() > 0) {
                foreach ($result as $row) {
                    $tags[] = $row['name'];
                    $query .= " AND name != " . $db->escape($row['name']);
                }
                $limit -= $result->count();
            }

            // Select from any partial matches if the result limit hasn't been reached yet
            if ($limit > 0) {
                $partName = "%$name%";
                $result = DB::query(Database::SELECT,
                    "SELECT name
					FROM kwalbum_persons
					WHERE name LIKE :partName AND count >= :min_count $query"
                    . " ORDER BY $order"
                    . ($limit ? ' LIMIT :limit' : null))
                    ->param(':partName', $partName)
                    ->param(':name', $name)
                    ->param(':min_count', $min_count)
                    ->param(':limit', $limit)
                    ->execute();

                foreach ($result as $row) {
                    $tags[] = $row['name'];
                }
            }
        } else {
            $result = DB::query(Database::SELECT,
                "SELECT name
				FROM kwalbum_persons
				WHERE count >= :min_count
				ORDER BY $order"
                . ($limit ? ' LIMIT :offset,:limit' : null))
                ->param(':offset', $offset)
                ->param(':min_count', $min_count)
                ->param(':limit', $limit)
                ->execute();

            foreach ($result as $row) {
                $tags[] = $row['name'];
            }
        }

        return $tags;
    }
}
