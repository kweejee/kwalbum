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

class Kwalbum_User_Model extends ORM
{
	protected $has_many = array('items');

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
		// do not delete main admin user or default deleted user
		if ($this->id < 3)
			return $this;
		$this->db->query("UPDATE kwalbum_items SET user_id=2, hide_level=100 WHERE user_id=$this->id");
		$this->db->query("DELETE FROM kwalbum_favorites WHERE user_id=$this->id");
		parent::delete();
	}
}
