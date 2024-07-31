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

    // PHP File Upload error message codes:
    // https://php.net/manual/en/features.file-upload.errors.php
    protected $error_messages = [
        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
    ];

    public function __construct($user = null)
    {
        if ($user === null)
            return;

        $item = new Model_Kwalbum_Item();
        $item->user_id = $user->id;

        $item->hide_level = Kwalbum_ItemAdder:: get_visibility($user);

        $item->location = trim($_POST['loc']);

        $tags = explode(',', @ $_POST['tags']);
        for ($i = 0; $i < count($tags); $i++) {
            $tags[$i] = trim($tags[$i]);
        }
        $item->set_tags($tags);
        $item->visible_date = $item->sort_date = Kwalbum_Helper:: replaceBadDate(@ $_POST['date'] . $_POST['time']);

        $this->_item = $item;
    }

    private function convertCoordinateToDecimal(array $coordinate): float
    {
        $min = explode('/', $coordinate[1]);
        $sec = explode('/', $coordinate[2]);
        return @ ((int)$coordinate[0] + (((int)$min[0] / (int)$min[1]) / 60)
            + (((int)$sec[0] / (int)$sec[1]) / 3600));
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
    private function save(): int
    {
        $item = $this->_item;
        $item->sort_date = Kwalbum_Helper::replaceBadDate($_POST['date'] . ' ' . $_POST['time']);
        $item->visible_date = $item->sort_date;

        // get exif data from jpeg files
        if ($item->type == 'jpeg') {
            $fullpath = $item->real_path . $item->filename;

            $import_caption = !empty($_POST['import_caption']);
            $import_keywords = !empty($_POST['import_keywords']);

            if ($import_caption || $import_keywords) {
                $info = null;
                getimagesize($fullpath, $info);
                if (isset($info['APP13'])) {
                    $iptc = iptcparse($info['APP13']);
                    foreach ($iptc as $key => $data) {
                        if ($key == '2#120' && $import_caption && $data[0] != null) { // https://www.iptc.org/std/photometadata/specification/IPTC-PhotoMetadata#description
                            $item->description = trim($data[0]);
                        }
                        if ($key == '2#025' && $import_keywords) { https://www.iptc.org/std/photometadata/specification/IPTC-PhotoMetadata#keywords
                            $item->add_tags($data);
                        }
                    }
                }
            }


            $exif = @exif_read_data($fullpath);
            if ($exif) {
//                Kohana::$log->add('var', print_r($exif));

                // replace the set date if one is found in the picture's exif data
                if (isset($exif['DateTimeOriginal'])) {
                    $item->sort_date = Kwalbum_Helper::replaceBadDate($exif['DateTimeOriginal']);
                    $item->visible_date = $item->sort_date;
                }

                if (!empty($exif['GPSLatitude'])) {
                    $lat = $this->convertCoordinateToDecimal($exif['GPSLatitude']);
                    if (!empty($exif['GPSLatitudeRef']) && 'S' == $exif['GPSLatitudeRef']) {
                        $lat = -($lat);
                    }
                    $item->latitude = $lat;
                }
                if (!empty($exif['GPSLongitude'])) {
                    $lon = $this->convertCoordinateToDecimal($exif['GPSLongitude']);
                    if (!empty($exif['GPSLongitudeRef']) && 'W' == $exif['GPSLongitudeRef']) {
                        $lon = -($lon);
                    }
                    $item->longitude = $lon;
                }
            }
        }

        if ($item->type == 'jpeg' or $item->type == 'png' or $item->type == 'gif') {
            $this->resizeImage($item->real_path, $item->filename);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 8:
                        $item->rotate(270);
                        break;
                    case 3:
                        $item->rotate(180);
                        break;
                    case 6:
                        $item->rotate(90);
                        break;
                }
            }
        }

        if ($item->type == 'gpx' || $item->type == 'geojson') {
            $item->description = $item->filename;
        }

        if (isset($_POST['group_option']) and $_POST['group_option'] == 'existing') {
            $result = DB::query(Database::SELECT,
                "SELECT create_dt
			FROM kwalbum_items
			ORDER BY create_dt DESC
			LIMIT 1")
                ->execute();
            $item->save(date($result[0]['create_dt']));
        } else {
            $item->save();
        }
        return (int)$item->id;
    }

    /**
     * Clean filename, check/set filetype, create path, move uploaded file, save
     * @param array $file a single file from $_FILES
     * @return string|Model_Kwalbum_Item error string or new item
     * @since 3.0
     */
    public function save_upload($file)
    {
        if (empty($file)) {
            return 'Missing file';
        }
        if ($file['error'] != UPLOAD_ERR_OK) {
            return isset($this->error_messages[$file['error']]) ? $this->error_messages[$file['error']] : $file['error'];
        }

        $item = $this->_item;

        $targetFile = trim($file['name']);
        $item->real_path = $this->makePath();

        $item->filename = $this->replaceAnnoyingFilenameCharacters($targetFile);

        while (!Model_Kwalbum_Item::check_unique_filename($item->real_path, $item->filename)) {
            # TODO: Handle race condition if same filename is uploaded in multiple requests at same time
            if (!isset($name) || !isset($extension)) {
                $i = 0;
                $name = pathinfo($item->filename, PATHINFO_FILENAME);
                $extension = pathinfo($item->filename, PATHINFO_EXTENSION);
            }
            $i++;
            $item->filename = "{$name}_{$i}.{$extension}";
        }
        try {
            $item->type = $this->get_filetype($item->filename);
        } catch (Kohana_Exception $ex) {
            return $ex->getMessage();
        }

        if (!Kohana_Upload::save($file, $item->filename, $item->real_path)) {
            return 'Upload could not be saved';
        }

        $this->save();
        # TODO: upload to google if $this->save returns int and Kwalbum_Helper::getGoogleBucket() returns bucket
        return $item;
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
            return false;
        $item->type = Model_Kwalbum_Item:: $types[255];

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
        if ($type == 'jpg' or $type == 'jpe') {
            $type = 'jpeg';
        }
        if (in_array($type, Model_Kwalbum_Item:: $types)) {
            return $type;
        }

        throw new Kohana_Exception(':type is not an acceptable file type', array(
            ':type' => $type
        ));
    }

    /**
     * Remove characters that may cause errors when resizing and convert file
     * extension to all lowercase
     * @param string $old_name original filename submitted by the user
     * @return string modified filename with characters replaced
     * @version 3.0
     * @since 2.0
     */
    private function replaceAnnoyingFilenameCharacters(string $old_name): string
    {
        return strtr(
            $old_name,
            array(
                ' ' => '_',
                '&' => 'and',
                '+' => 'plus',
                ";" => '_',
                ":" => '_',
                '\''=> '_',
                '"' => '_',
                '<' => '_',
                '>' => '_',
                '$' => '_',
                '!' => '_',
                '?' => '_',
                '*' => '_',
                '(' => '-',
                ')' => '-',
                '[' => '-',
                ']' => '-',
                pathinfo(
                    $old_name,
                    PATHINFO_EXTENSION) => strtolower(pathinfo($old_name, PATHINFO_EXTENSION)
                ),
            )
        );
    }

    /**
     * Create a path if one does not already exist and create
     * any directories in the path that do not already exist.
     *
     * Echo errors labled with css class "error"
     *
     * @return string path if successful
     * @throws Kohana_Exception
     * @version 3.0
     * @since 2.0
     */
    private function makePath(): string
    {
        $path = Kwalbum_Model::get_config('item_path');
        $dirs = explode('-', date('y-m'));

        $path .= Kwalbum_Model::get_config('path_prefix') . $dirs[0];
        if (!file_exists($path) and !mkdir($path)) {
            throw new Kohana_Exception(
                'Directory :dir could not be created',
                array(':dir' => Debug::path($path))
            );
        }
        $path .= '/' . $dirs[1];
        if (!file_exists($path) and !mkdir($path)) {
            throw new Kohana_Exception(
                'Directory :dir could not be created',
                array(':dir' => Debug::path($path))
            );
        }
        $path .= '/';

        if (!file_exists($path . 't') and !@ mkdir($path . 't')) {
            throw new Kohana_Exception(
                'Directory :dir could not be created',
                array(':dir' => Debug::path($path . 't'))
            );
        }
        if (!file_exists($path . 'r') and !@ mkdir($path . 'r')) {
            throw new Kohana_Exception(
                'Directory :dir could not be created',
                array(':dir' => Debug::path($path . 'r'))
            );
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
    private function resizeImage(string $path, string $filename)
    {
        $image = Image::factory($path . $filename);
        $image->resize(null, 480);
        if (!$image->save($path . 'r/' . $filename, 80)) {
            throw new Kohana_Exception('Could not resize image to "resized" version');
        }
        $image->resize(null, 112);
        if (!$image->save($path . 't/' . $filename, 80)) {
            throw new Kohana_Exception('Could not resize image to "thumbnail" version');
        }
    }

    /**
     * Get a level of visibility that is valid for the user and based on
     * $_POST['vis']
     *
     * @param Model_Kwalbum_User $user
     * @return int level of visibility
     * @global int $_POST ['vis']
     */
    static public function get_visibility(Model_Kwalbum_User $user): int
    {

        $visibility = (int)(@ $_POST['vis']);
        if ($visibility < 0) {
            $visibility = 0;
        } else if ($visibility > 2) {
            $visibility = $user->is_admin ? 3 : 2;
        }
        return $visibility;
    }
}

?>
