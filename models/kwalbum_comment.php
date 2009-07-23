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

class Kwalbum_Comment_Model extends ORM
{
	protected $belongs_to = array('item');

	public function __construct($id = null)
	{
		$this->table_name = Kohana::config('kwalbum.dbtables.comments');
		parent::__construct($id);
	}
}
