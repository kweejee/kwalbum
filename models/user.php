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

class User_Model extends Model
{
	public function __construct($id = NULL)
	{
		parent::__construct($id);
		$this->table = Kohana::config('kwalbum.dbtables.users');
	}

	//protected $has_many = array('item');
	private $table;

	public function get_row($userId = 0)
	{
		$this->db->where('id', $userId);
		return $this->db->get($this->table, $userId);
	}
	public function get_name($userId)
	{
		$result = $this->get_row($userId);
		if ( ! $result->valid())
			return '';
		foreach($result as $row)
			return $row->name;
	}

	public function insert($name, $openid)
	{
		if (empty($name) or empty($openid))
			return false;
		$this->db->insert($this->table, array('name' => $name, 'openid' => $openid));
	}

	public function delete($name)
	{
		if (empty($name))
			return false;
		$this->db->delete($this->table, array('name =' => $name));
	}
}
