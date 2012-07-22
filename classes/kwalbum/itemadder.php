<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @package kwalbum
 * @version 2.1.2
 * @since 2.0
 */

//$current_dir = pathinfo(__FILE__, PATHINFO_DIRNAME);
//require_once $current_dir.'/../pjmt/EXIF.php';
//require_once $current_dir.'/../pjmt/Photoshop_File_Info.php';

/**
 * Work with the filesystem and database to add a new item.
 * @since 2.0
 */
class Kwalbum_ItemAdder
{
	private $_item;

	public function __construct($user = null)
	{
		if ($user === null)
			return;

		$item = Model :: factory('kwalbum_item');
		$item->user_id = $user->id;

		$item->hide_level = Kwalbum_ItemAdder :: get_visibility($user);

		$item->location = trim(htmlspecialchars(@ $_POST['loc']));

		$tags = explode(',', htmlspecialchars(@ $_POST['tags']));
		for ($i = 0; $i < count($tags); $i++)
		{
			$tags[$i] = trim($tags[$i]);
		}
		$item->tags = $tags;
		$item->visible_date = $item->sort_date = Kwalbum_Helper :: replaceBadDate(@ $_POST['date'].$_POST['time']);

		$this->_item = $item;
	}

	/**
	* Save file in proper location and save information to database
	*
	* Check if it has an acceptable file extension, determine the filetype
	* based on the extension, move the file into the necessary directory,
	* create thumbnail and resized versions if necessary, then insert into the
	* database if there have not been any errors.
	*
	* Return the id of the new item if successful
	* @since 3.0
	*/
	private function save()
	{
		$item = $this->_item;

		// get exif data from jpeg files
		if ($item->type == 'jpeg')
		{
			$fullpath = $item->path.$item->filename;

			$import_caption = false;
			$import_keywords = false;
			if (isset($_POST['import_caption']) && $_POST['import_caption'] == 1)
				$import_caption = true;
			if (isset($_POST['import_keywords']) && $_POST['import_keywords'] == 1)
				$import_keywords = true;
			
			if ($import_caption || $import_keywords)
			{
				$info = null;
				$size = getimagesize($fullpath, $info);
				if (isset($info['APP13']))
				{
					$iptc = iptcparse($info['APP13']);
					foreach ($iptc as $key=>$data)
					{
						if ($key == '2#120' && $import_caption)
							$item->description = trim($data[0]);
						if ($key == '2#025' && $import_keywords)
						{
							if (!is_array($data))
								$data = array($data);
							foreach ($data as $keyword)
							{
								$item->tags = htmlspecialchars(trim($keyword));
							}
						}
					}
				}
			}

			$data = @exif_read_data($fullpath);
			if ($data)
			{
				//Kohana::$log->add('var', Kohana::debug($data));
				/*
				if ($irb = get_Photoshop_IRB($jpeg_header_data))
				{
				    $xmp = Read_XMP_array_from_text(get_XMP_text($jpeg_header_data));
				    $pinfo = get_photoshop_file_info($data, $xmp, $irb);
				    foreach ($pinfo['keywords'] as $keyword)
				    {
					$item->tags = trim($keyword);
				    }
				    //echo '<pre>'.Kohana::debug( $pinfo );exit;
				}*/

				// replace the set date if one is found in the picture's exif data
				if (isset($data['DateTimeOriginal']))
				{
					$item->visible_date = $item->sort_date = Kwalbum_Helper :: replaceBadDate($data['DateTimeOriginal']);
				}

				$latitude = @ $data['GPSLatitude'];
				if ($latitude)
				{
					$sec = explode('/', $latitude[2]);
					$lat = @ ((int)$latitude[0] + ((int)$latitude[1] / 60)
					       + (((int)$sec[0] / (int)$sec[1]) / 3600));
					if ('S' == $data['GPSLatitudeRef'])
					{
						$lat = - ($lat);
					}
					$item->latitude = $lat;
				}
				$longitude = @ $data['GPSLongitude'];
				if ($longitude)
				{
					$sec = explode('/', $longitude[2]);
					$lon = @ ((int)$longitude[0] + ((int)$longitude[1] / 60)
					       + (((int)$sec[0] / (int)$sec[1]) / 3600));
					if ('W' == $data['GPSLongitudeRef'])
					{
						$lon = - ($lon);
					}
					$item->longitude = $lon;
				}
			}
		}

		if ($item->type == 'jpeg' or $item->type == 'png' or $item->type == 'gif')
		{
			$this->ResizeImage($item->path, $item->filename);
		}

		if (isset($_POST['group_option']) && $_POST['group_option'] == 'existing')
		{
			$result = DB::query(Database::SELECT,
			"SELECT update_dt
			FROM kwalbum_items
			ORDER BY id DESC
			LIMIT 1")
			->execute();
			$item->update_date = $result[0]['update_dt'];
			$item->save(false);
		} 
		else
		{
			$item->save();
		}
		return $item->id;
	}

	/**
	 * Clean filename, check/set filetype, create path, move uploaded file, save
	 * @since 3.0
	 */
	public function save_upload()
	{
		$item = $this->_item;

		$targetFile = trim($_FILES['Filedata']['name']);
		$item->path = $this->make_path();

		$item->filename = $this->replace_bad_filename_characters($targetFile);

		while ( ! Model_Kwalbum_Item::check_unique_filename($item->path, $item->filename))
		{
			if (!isset($name))
			{
				$i = 0;
				$name = pathinfo($item->filename, PATHINFO_FILENAME);
				$extension = pathinfo($item->filename, PATHINFO_EXTENSION);
			}
			$i++;
			$item->filename = "{$name}_{$i}.{$extension}";
		}
		$item->type = $this->get_filetype($item->filename);

		if ( $_FILES['Filedata']['error'] == 1) {
			throw new Kohana_Exception('File not uploaded.  Is the file too large?');
		}
		if ( ! Upload :: save($_FILES['Filedata'], $item->filename, $item->path))
		{
			throw new Kohana_Exception('upload could not be saved');
		}

		return $this->save();
	}

	/**
	 * Create description and save
	 * @since 3.0
	 */
	public function save_write()
	{
		$item = $this->_item;

		$item->description = trim($_POST['description']);
		if (empty($item->description))
		{
			return false;
		}
		$item->type = Model_Kwalbum_Item :: $types[255];

		return $this->save();
	}

	/**
	 * Check filename, check/set filetype, save
	 */
	public function save_existing_file()
	{
		$item = $this->_item;

		// TODO: get path
		// $item->path = ???

		// Do not add/save again if the file is already in the database
		if ( ! Model_Kwalbum_Item::check_unique_filename($item->path, $item->filename))
		{
			return false;
		}

		$item->type = $this->get_filetype($item->filename);


		return $this->save();
	}

	/**
	 * Use the file extension to get the filetype and check if it is allowed.
	 *
	 * @return file type or throw exception if extension is not allowed
	 * @since 3.0
	 */
	private function get_filetype($filename)
	{
		$type = pathinfo($filename, PATHINFO_EXTENSION);
		if ($type == 'jpg' or $type == 'jpe')
		{
			$type = 'jpeg';
		}
		if (in_array($type, Model_Kwalbum_Item :: $types))
		{
			return $type;
		}

		throw new Kohana_Exception(':type is not an acceptable file type', array (
		    ':type' => Kohana :: debug($type)
		));
	}

	/**
	* Remove characters that may cause errors when resizing.
	*
	* Also convert file extension to all lowercase.
	* @param string $oldName original filename submitted by the
	* user
	* @return string modified filename with characters replaced
	* @since 2.0
	*/
	private function replace_bad_filename_characters($oldName)
	{
		if (get_magic_quotes_gpc())
		{
			$oldName = stripslashes($oldName);
		}
		return strtr($oldName, array (
			' ' => '_',
			'&' => 'and',
			'+' => 'plus',
			'\''=> '_',
			'"' => '_',
			'<' => '_',
			'>' => '_',
			'$' => '_',
			'!' => '_',
			'*' => '_',
			'(' => '-',
			')' => '-',
			pathinfo($oldName, PATHINFO_EXTENSION) => strtolower(pathinfo($oldName, PATHINFO_EXTENSION))
		));
	}

	/**
	* Create a path if one does not already exist and create
	* any directories in the path that do not already exist.
	*
	* Echo errors labled with css class "error"
	*
	* @param string $existingPath optional predetermined directory
	* @return string path if successful, empty string if not
	* @version 3.0
	* @since 2.0
	*/
	private function make_path($existingPath = '')
	{
		if (!$existingPath)
		{
			$path = Kwalbum_Model::get_config('item_path');
			$dirs = explode('-', date('y-m'));
			$pathYear = $dirs[0];
			$pathMonth = $dirs[1];

			$path = $path.$pathYear;
			if ( ! file_exists($path)
					and ! mkdir($path))
			{
				throw new Kohana_Exception('Directory :dir could not be created', array (
					':dir' => Kohana :: debug_path($path)
				));
			}
			$path .= '/'.$pathMonth;
			if ( ! file_exists($path)
					and ! mkdir($path))
			{
				throw new Kohana_Exception('Directory :dir could not be created', array (
					':dir' => Kohana :: debug_path($path)
				));
			}
			$path .= '/';
		}
		else
		{
			$path = $existingPath;
		}

		if ( ! file_exists($path.'t') and ! @ mkdir($path.'t'))
		{
			throw new Kohana_Exception('Directory :dir could not be created', array (
				':dir' => Kohana :: debug_path($path.'t')
			));
		}
		if ( ! file_exists($path.'r') and ! @ mkdir($path.'r'))
		{
			throw new Kohana_Exception('Directory :dir could not be created', array (
				':dir' => Kohana :: debug_path($path.'r')
			));
		}

		return $path;
	}

	/**
	* Check if thumbnail and resized versions of an image file
	* exist and create them if not.
	*
	* @param string $path
	* @param string $filename
	* @since 2.1.1
	*/
	private function ResizeImage($path, $filename)
	{
		$image = Image::factory($path.$filename);
		$image->resize(null, 480);
		if ( ! $image->save($path.'r/'.$filename, 80))
		{
			throw new Kohana_Exception('Could not resize image to "resized" version');
		}
		$image->resize(null, 112);
		if ( ! $image->save($path.'t/'.$filename, 80))
		{
			throw new Kohana_Exception('Could not resize image to "thumbnail" version');
		}
	}

	/**
	 * Get a level of visibility that is valid for the user and based on
	 * $_POST['vis']
	 *
	 * @param Model_Kwalbum_User $user
	 * @global int $_POST['vis']
	 * @return int level of visibility
	 */
	static public function get_visibility($user)
	{

		$visibility = (int) (@ $_POST['vis']);
		if ($visibility < 0)
		{
			$visibility = 0;
		}
		else if ($visibility > 2)
		{
			$visibility = $user->is_admin ? 3 : 2;
		}
		return $visibility;
	}
}
?>
