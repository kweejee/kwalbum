<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kohana
 * @since Jul 21, 2009
 */

class Kwalbum_Items_Tag_Model extends ORM
{
	protected $belongs_to = array('item' => 'kwalbum_items', 'tag' => 'kwalbum_tags');

	public function __construct($id = null)
	{
		$this->foreign_key = array('kwalbum_items' => 'item_id');
		parent::__construct($id);
	}
}