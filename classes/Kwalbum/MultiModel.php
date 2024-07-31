<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * A standard way for all many-to-many Kwalbum model objects to behave
 *
 * @author Tim Redmond
 * @copyright Copyright 2023 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Nov 26, 2023
 */

abstract class Kwalbum_MultiModel extends Kwalbum_Model
{
    public int $id = 0;
    public string $name = '';
    public int $count = 0;

    /**
     * @todo Stop clearing and just create new instances so this can be removed
     * @return void
     */
    public function clear(): void
    {
        $this->id = $this->count = 0;
        $this->name = '';
        $this->loaded = false;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * MultiModels should never be empty or contain html so escape on the way in
     * @return void
     * @throws Exception
     */
    protected function escape_name(): void
    {
        if (empty(trim($this->name ?? ''))) {
            throw new Exception("Empty names are not allowed and should be validated before saving");
        }
        $this->name = self::htmlspecialchars($this->name);
    }

    protected function hydrate_from_result(Database_Result_Cached $result): void
    {
        if ($result->count() == 0) {
            return;
        }

        $row = $result[0];

        $this->id = (int)$row['id'];
        $this->name = $row['name'];
        $this->count = (int)$row['count'];
        $this->loaded = true;
    }
}
