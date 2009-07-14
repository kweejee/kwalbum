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

class Kwalbum_User_Model extends ORM
{
	protected $has_many = array('item');
/*	private $_table;

	public function __construct($id = NULL)
	{
		parent::__construct($id);
		$this->_table = Kohana::config('kwalbum.dbtables.users');
	}

	public function __get($var)
	{
		if ($var == 'total')
			return $this->db->count_records($this->_table);
	}

	public function insert($name, $openid, $level = 0)
	{
		$level = (int)$level;
		if (empty($name) or empty($openid))
			return false;
		$result = $this->db->insert($this->_table, array('name' => $name, 'openid' => $openid, 'permission_level' => $level));
		return $result->insert_id();
	}

	public function delete($id)
	{
		$id = (int)$id;
		if (empty($id))
			return false;
		$result = $this->db->delete($this->_table, array('id =' => $id));
		if ($result->count() == 1)
			return true;
		return false;
	}

	public function get_row($id = 0)
	{
		$id = (int)$id;
		$this->db->where('id', $id);
		$result = $this->db->get($this->_table, 1);
		if ( ! $result->valid())
			return false;
		return $result[0];
		$result->upd
	}

	public function get_name($id)
	{
		$row = $this->get_row($id);
		if ( ! $row)
			return false;
		return $row->name;
	}

	public function get_openid($id)
	{
		$row = $this->get_row($id);
		if ( ! $row)
			return false;
		return $row->openid;
	}

	public function get_permission_level($id)
	{
		$row = $this->get_row($id);
		if ( ! $row)
			return false;
		return (int)$row->permission_level;
	}


	public function change_permission_to($newLevel)
	{
		$newLevel = (int)$newLevel;
		if ($newLevel < 0 or $newLevel > 5)
			return false;
		$this->db->update($this->_table, array('permission_level' => $newLevel), array());
	}*/
}
