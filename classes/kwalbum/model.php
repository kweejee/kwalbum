<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Jul 28, 2009
 */

abstract class Kwalbum_Model extends Model
{
	public $loaded = false;

	public function __construct($db = NULL)
	{
		$this->clear();
		parent::__construct($db);
	}

	public function reload()
	{
		$id = $this->id;
		$this->clear();
		return $this->load($id);
	}

	abstract public function load($id = null);
	//abstract public function find($val = null);
	abstract public function save();
	abstract public function delete($id = null);
	abstract public function clear();
}

