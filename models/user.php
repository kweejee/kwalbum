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
	//protected $has_many = array('item');
	private $_table;

	public function __construct($id = NULL)
	{
		parent::__construct($id);
		$this->_table = Kohana::config('kwalbum.dbtables.users');
	}

	public function get_row($userId = 0)
	{
		$this->db->where('id', $userId);
		return $this->db->get($this->_table, $userId);
	}
	public function get_name($userId)
	{
		$result = $this->get_row($userId);
		if ( ! $result->valid())
			return '';
		foreach($result as $row)
			return $row->name;
	}

	public function __get($var)
	{
		if ($var == 'total')
			return $this->db->count_records($this->_table);
	}

	public function insert($name, $openid)
	{
		if (empty($name) or empty($openid))
			return false;
		$this->db->insert($this->_table, array('name' => $name, 'openid' => $openid));
	}

	public function delete($name)
	{
		if (empty($name))
			return false;
		$this->db->delete($this->_table, array('name =' => $name));
	}
}
