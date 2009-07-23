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

class Kwalbum_Location_Model extends ORM
{
	protected $has_many = array('items' => 'kwalbum_items');

	public function __construct($id = null)
	{
		$this->table_name = Kohana::config('kwalbum.dbtables.locations');
		parent::__construct($id);
	}

	public function unique_key($id = null)
	{
		if (is_string($id))
		{
			return 'name';
		}
		return parent::unique_key($id);
	}

	public function delete()
	{
		$result = $this->db->query("UPDATE kwalbum_items SET location_id = 1 WHERE location_id=$this->id");
		$count = $result->count();
		$this->db->query("UPDATE kwalbum_locations SET count = count+$count WHERE id=1");
		parent::delete();
	}
}
