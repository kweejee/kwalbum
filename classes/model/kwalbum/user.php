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

class Model_Kwalbum_User extends ORM
{
	protected $has_many = array('items' => 'kwalbum_items');

	public function delete($id = NULL)
	{
		if ($id === NULL)
		{
			$id = $this->id;
		}

		// do not delete main admin user or default deleted user
		if ($id < 3)
		{
			return $this;
		}

		$this->db->query(Database::UPDATE, "UPDATE kwalbum_items SET user_id=2, hide_level=100 WHERE user_id=$id");
		$this->db->query(Database::DELETE, "DELETE FROM kwalbum_favorites WHERE user_id=$id");
		return parent::delete($id);
	}
}
