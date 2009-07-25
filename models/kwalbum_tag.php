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

class Kwalbum_Tag_Model extends ORM
{
	protected $has_and_belongs_to_many = array('items' => 'kwalbum_item_tag');
	protected $foreign_key = array('' => 'tag_id');

	public function unique_key($id = null)
	{
		if (is_string($id))
		{
			return 'name';
		}
		return parent::unique_key($id);
	}
}
