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

class Kwalbum_Comment_Model extends ORM
{
	protected $belongs_to = array('item' => 'kwalbum_item');

	public function __set($var, $value = null)
	{
		switch ($var)
		{
			case 'ip':
				$value = ip2long($value);
				break;
		}
		parent::__set($var,$value);
	}

	public function __get($var)
	{
		if ($var == 'ip')
		{
			return long2ip(parent::__get($var));
		}
		return parent::__get($var);
	}

	public function save()
	{
		$this->db->update('kwalbum_items', array('has_comments' => 1), array('id' => $this->item_id));
		parent::save();
	}

	public function delete()
	{
		if (1 == $this->db->count_records('kwalbum_comments', array('item_id' => $this->item_id)))
		{
			$this->db->update('kwalbum_items', array('has_comments' => 0), array('id' => $this->item_id));
		}
		parent::delete();
	}
}
