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

class Model_Kwalbum_Items_Site extends ORM
{
	protected $belongs_to = array('item' => 'kwalbum_item');
	protected $has_one = array('site' => 'kwalbum_site');
	protected $table_name = 'kwalbum_items_kwalbum_sites';
	protected $primary_key = 'item_id';

	public function save()
	{
		$this->db->update('kwalbum_items', array('external_item' => 0), array('id' => $this->item_id));
		parent::save();
	}

	public function delete($id)
	{
		parent::delete($id);
	}
}

