<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * A standard way for all Kwalbum model objects to behave.
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2023 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Jul 28, 2009
 */

abstract class Kwalbum_Model extends Model
{
    public $loaded = false; // TODO: use id instead
    static public $config;

//	public function __construct($db = null)
//	{
//		$this->clear();
//		parent::__construct($db);
//	}

    /**
     * Reload all data for the current object.
     */
    public function reload()
    {
        $id = $this->id;
        $this->clear();
        return $this->load($id);
    }

    static public function get_config($key)
    {
        if (!self::$config) {
            self::$config = Kohana::$config->load('kwalbum');
        }
        return self::$config->$key;
    }

    /**
     * This exists to make sure everything is escaped the same way everywhere
     *
     * @param string $value
     * @return string
     */
    public static function htmlspecialchars(string $value): string
    {
        return htmlspecialchars($value, ENT_COMPAT | ENT_HTML5 | ENT_SUBSTITUTE, 'UTF-8',false);
    }

    /**
     * Load data into $this or clear $this if $id is null or invalid.
     *
     */
    abstract public function load($value = null);

    /**
     * Call load() using an id found where the object's main field, such
     * as "name" or "user_id" equals $val
     */
    //abstract public function find($val = null);

    /**
     * Save data for $this.
     *
     * If $this->id is null it should set defaults and insert; otherwise,
     * update.  After data is saved, it must be manually reloaded to see changes
     * done on the database server. It should not be automatic because most
     * times it is not needed.
     */
    abstract public function save();

    /**
     * Delete the object from the database.
     *
     */
    abstract public function delete();

    /**
     * Set empty values for all fields.
     *
     * Defaults should be set in save() if they are not empty by default.
     */
    abstract public function clear();
}

