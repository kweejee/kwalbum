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

class Item_Model extends ORM
{
	protected $belongs_to = array('user');
	protected $has_many = array('comments');
	protected $has_and_belongs_to_many = array('locations', 'tags', 'persons');
	protected $types = array(
		1 => 'gif', 2 => 'jpg', 3 => 'png',
		40 => 'wmv',
		41 => 'txt',
		42 => 'mp3',
		43 => 'zip',
		44 => 'html',
		45 => 'divx',
		46 => 'ogg',
		47 => 'wav',
		48 => 'xml', 49 => 'gpx',
		50 => 'ods', 51 => 'odt',
		52 => 'flv',
		53 => 'doc',
		54 => 'mpeg',
		55 => 'mp4',
		255 => 'description only'
	);

}
