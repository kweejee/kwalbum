<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jul 6, 2009
 * @package kwalbum
 * @since 3.0 Jul 6, 2009
 */

class Model_Kwalbum_Site extends Kwalbum_Model
{
	public $id, $url, $key, $import_date, $loaded;

	public function load($id = null, $field = 'id')
	{
		if ($id === null)
		{
			$this->clear();
			return $this;
		}

		$result = DB::query(Database::SELECT,
			"SELECT id, url, site_key, import_dt
			FROM kwalbum_sites
			WHERE $field = :id
			LIMIT 1")
			->param(':id', $id)
			->execute();
		if ($result->count() == 0)
		{
			$this->clear();
			return $this;
		}

		$row = $result[0];

		$this->id = (int)$row['id'];
		$this->url = $row['url'];
		$this->key = $row['site_key'];
		$this->import_date = $row['import_dt'];

		$this->loaded = true;
		return $this;
	}

	public function save()
	{
		$id = $this->id;

		if (empty($this->import_date))
		{
			$this->import_date = '0000-00-00 00:00:00';
		}

		if ($this->loaded === false)
		{
			$result = DB::query(Database::SELECT,
				"SELECT id, url, site_key, import_dt
				FROM kwalbum_sites
				WHERE url = :url
				LIMIT 1")
				->param(':url', $this->url)
				->execute();
			if ($result->count() == 0)
			{
				$result = DB::query(Database::INSERT,
					"INSERT INTO kwalbum_sites
					(url, site_key, import_dt)
					VALUES (:url, :key, :import_date)")
					->param(':url', $this->url)
					->param(':key', $this->key)
					->param(':import_date', $this->import_date)
					->execute();
				$this->id = $result[0];
				$this->loaded = true;
				return;
			}

			$this->id = $id = (int)$result[0]['id'];
			$this->url = $result[0]['url'];
			$this->key = $result[0]['site_key'];
			$this->import_date = $result[0]['import_dt'];
			$this->loaded = true;
		}

		DB::query(Database::UPDATE,
			"UPDATE kwalbum_sites
			SET url = :url, site_key = :key, import_dt = :import_date
			WHERE id = :id")
			->param(':id', $id)
			->param(':url', $this->url)
			->param(':key', $this->key)
			->param(':import_date', $this->import_date)
			->execute();
	}

	public function delete()
	{
		// Delete the site
		DB::query(Database::DELETE,
			"DELETE FROM kwalbum_sites
			WHERE id = :id")
			->param(':id', $this->id)
			->execute();
        $this->clear();
        return true;
	}

	public function clear()
	{
		$this->id = 0;
		$this->url = $this->key = $this->import_date = '';
		$this->loaded = false;
	}
}
