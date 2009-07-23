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

class Kwalbum_Item_Model extends ORM
{
	protected $belongs_to = array('user' => 'kwalbum_user', 'location' => 'kwalbum_location');
	protected $has_one = array('site' => 'kwalbum_site');
	protected $has_many = array('comments' => 'kwalbum_comments');
	protected $has_and_belongs_to_many = array('kwalbum_tags', 'kwalbum_persons');
	//protected $object_relations = array('kwalbum_tags' => array());
	protected $foreign_key = array('' => 'item_id');

	public function save()
	{
		if (isset($this->changed['location_id']))
		{
			$this->db->query("UPDATE kwalbum_locations SET count=count-1 WHERE id=(SELECT location_id FROM kwalbum_items WHERE id=$this->id) AND count>0");
			$this->db->query("UPDATE kwalbum_locations SET count=count+1 WHERE id=$this->location_id");
		}
		// add new tags
		if (isset($this->changed_relations['kwalbum_tags']))
		{
			if (isset($this->object_relations['kwalbum_tags']))
				$new_tags = array_diff($this->changed_relations['kwalbum_tags'],$this->object_relations['kwalbum_tags']);
			else
				$new_tags = $this->changed_relations['kwalbum_tags'];
			foreach ($new_tags as $tag)
				$this->db->query("UPDATE kwalbum_tags SET count=count+1 WHERE id=$tag");
		}
		// remove old tags
		if (isset($this->object_relations['kwalbum_tags']))
		{
			if (isset($this->changed_relations['kwalbum_tags']))
				$new_tags = array_diff($this->object_relations['kwalbum_tags'],$this->changed_relations['kwalbum_tags']);
			else
				$new_tags = $this->object_relations['kwalbum_tags'];
			foreach ($new_tags as $tag)
				$this->db->query("UPDATE kwalbum_tags SET count=count-1 WHERE id=$tag AND count>0");
		}

		// add new persons
		if (isset($this->changed_relations['kwalbum_persons']))
		{
			if (isset($this->object_relations['kwalbum_persons']))
				$new_persons = array_diff($this->changed_relations['kwalbum_persons'],$this->object_relations['kwalbum_persons']);
			else
				$new_persons = $this->changed_relations['kwalbum_persons'];
			foreach ($new_persons as $person)
				$this->db->query("UPDATE kwalbum_persons SET count=count+1 WHERE id=$person");
		}
		// remove old persons
		if (isset($this->object_relations['kwalbum_persons']))
		{
			if (isset($this->changed_relations['kwalbum_persons']))
				$new_persons = array_diff($this->object_relations['kwalbum_persons'],$this->changed_relations['kwalbum_persons']);
			else
				$new_persons = $this->object_relations['kwalbum_persons'];
			foreach ($new_persons as $person)
				$this->db->query("UPDATE kwalbum_persons SET count=count-1 WHERE id=$person AND count>0");
		}
		parent::save();
	}
	public function delete()
	{
		$this->db->query("DELETE FROM kwalbum_comments WHERE item_id=$this->id");
		$this->db->query("UPDATE kwalbum_locations SET count=count-1 WHERE id=$this->location_id AND count>0");
		foreach ($this->kwalbum_tags as $tag)
		{
			$tag_id = $tag->id;
			$this->db->query("UPDATE kwalbum_tags SET count=count-1 WHERE id=$tag_id AND count>0");
		}
		foreach ($this->kwalbum_persons as $person)
		{
			$person_id = $person->id;
			$this->db->query("UPDATE kwalbum_persons SET count=count-1 WHERE id=$person_id AND count>0");
		}
		parent::delete();
	}
	public function __get($id)
	{
		if ($id == 'tags')
			$id = 'kwalbum_tags';
		if ($id == 'persons')
			$id = 'kwalbum_persons';
		return parent::__get($id);
	}
		/*protected $types = array(
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
	);*/
}
