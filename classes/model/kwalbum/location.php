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

class Model_Kwalbum_Location extends ORM
{
	protected $has_many = array('items' => 'kwalbum_items');

	public function delete($id = NULL)
	{
		if ($id === NULL)
		{
			// Use the the primary key value
			$id = $this->id;
		}

		// do not delete the "unknown" location
		if ($id == 1)
		{
			return $this;
		}

		$count = $this->db->query(Database::UPDATE, "UPDATE kwalbum_items SET location_id = 1 WHERE location_id=$id");
		$this->db->query(Database::UPDATE, "UPDATE kwalbum_locations SET count = count+$count WHERE id=1");
		return parent::delete($id);
	}
}
