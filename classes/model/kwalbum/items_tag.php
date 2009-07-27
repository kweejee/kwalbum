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

class Model_Kwalbum_Items_Tag extends ORM
{
	protected $belongs_to = array('item' => 'kwalbum_items', 'tag' => 'kwalbum_tags');
	protected $foreign_key = array('kwalbum_items' => 'item_id');
}