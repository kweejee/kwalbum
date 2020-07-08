<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jun 30, 2009
 * @package kwalbum
 * @since 3.0 Jun 30, 2009
 */

use Google\Cloud\Core\Timestamp;


class Controller_Item extends Controller_Kwalbum
{
	function before()
	{
		$this->auto_render = false;
		parent::before();
		if ($this->request->action() != 'index' and
		    $this->item->hide_level > $this->user->permission_level)
		{
			$this->request->action('hidden');
		}
	}

	function action_index()
	{
		$this->item->increase_count();
		$this->auto_render = true;
		if ($this->in_edit_mode)
		{
			$view = new View('kwalbum/item/single.edit');
		}
		else
		{
			$view = new View('kwalbum/item/single.view');
		}
		$view->item = $this->item;
		$this->template->content = $view;
		//$this->template->title = 'single item';
	}

	function action_hidden()
	{
		$this->_send_file($this->item->real_path.$this->item->filename);
	}

	function action_thumbnail()
	{
		$this->_send_file($this->item->real_path.'t/'.$this->item->filename, '_thumbnail');
	}

	function action_resize()
	{
		$this->action_resized();
	}

	function action_resized()
	{
		$this->item->increase_count();
		$this->_send_file($this->item->real_path.'r/'.$this->item->filename, '_resized');
	}

	function action_original()
	{
		$this->item->increase_count();
		$this->_send_file($this->item->real_path.$this->item->filename);
	}

	function action_download()
	{
		$this->item->increase_count();
        $bucket = Kwalbum_Helper::getGoogleBucket();
        if (!$bucket or file_exists($this->item->real_path.$this->item->filename)) {
            $this->_send_file($this->item->real_path.$this->item->filename, '', true);
        }
        $bucket = Kwalbum_Helper::getGoogleBucket();
        $object = $bucket->object($this->path.$this->filename);
        $download_url = $object->signedUrl(new Timestamp(new DateTime('tomorrow')), ['saveAsName' => $item->filename]);
        header('Location: '.$download_url);
        exit;
	}

	private function _send_file($filepathname, $filename_addition = '', $download = false)
	{
		if ( ! $filepath = realpath($filepathname))
		{
			// Return a 404 status
			$this->response->status(404);
			Kohana::$log->add('~item/_send_file', '404: '.$filepathname);
			return;
		}

		// Use the file name as the download file name
		$filename = pathinfo($filepath, PATHINFO_FILENAME);

		// Get the file size
		$size = filesize($filepath);

		// Get the file extension
		$extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

		// Guess the mime using the file extension
		$mimes = Kohana::$config->load('mimes');
		$mime = $mimes[$extension][0];
		if (!$this->user->can_see_all) {
			$watermark = Kwalbum_Model::get_config('watermark_filename');
			if ($watermark and $size < Kwalbum_Model::get_config('watermark_filesize_limit'))
			{
				$watermark = Kwalbum_Model::get_config('item_path').$watermark;
				$watermark = @imagecreatefrompng($watermark);
			} else {
				$watermark = null;
			}
			if ($watermark)
			{
				switch ($mime)
				{
					case $mimes['jpg'][0]:
					$picture = imagecreatefromjpeg($filepath);
					if ($picture)
					{
						$width_p = imagesx($picture);
						$height_p = imagesy($picture);
						$width_w = imagesx($watermark);
						$height_w = imagesy($watermark);
						$width_percent = Kwalbum_Model::get_config('watermark_width_percent');
						$height_percent = Kwalbum_Model::get_config('watermark_height_percent');
						if ($width_p < $height_p) {
							$height_r = $height_p * $height_percent;
							$width_r = $height_r * $width_w/$height_w;
							if ($width_r > $width_p * $width_percent) {
								$width_r = $width_p * $width_percent;
								$height_r = $width_r * $height_w/$width_w;
							}
						} else {
							$width_r = $width_p * $width_percent;
							$height_r = $width_r * $height_w/$width_w;
							if ($height_r > $height_p * $height_percent) {
								$height_r = $height_p * $height_percent;
								$width_r = $height_r * $width_w/$height_w;
							}
						}
						imagecopyresampled($picture, $watermark, 0, $height_p-$height_r, 0, 0, $width_r, $height_r, $width_w, $height_w);
						//imagecopy($picture, $watermark, 0, $height_p-$height_w, 0, 0, $width_w, $height_w);
						header("Content-Type: image/jpeg");
						imagejpeg($picture, null, 95);
						exit;
					}
					break;
				}
			}
		}

		// Open the file for reading
		$file = fopen($filepath, 'rb');

		// Set the headers for a download
		$this->response->headers('Content-Disposition', ( $download ? 'attachment; ' : null)
			.'filename="'.$filename.$filename_addition
			.'.'.$extension.'"');
		$this->response->headers('Content-Type', $mime);
		$this->response->headers('Content-Length', $size);

		// Send all headers now
		$this->response->send_headers();

		while (ob_get_level())
		{
			// Flush all output buffers
			ob_end_flush();
		}

		// Manually stop execution
		ignore_user_abort(TRUE);

		// Keep the script running forever
		set_time_limit(0);

		// Send data in 16kb blocks
		$block = 1024 * 16;

		while ( ! feof($file))
		{
			if (connection_aborted())
				break;

			// Output a block of the file
			echo fread($file, $block);

			// Send the data now
			flush();
		}

		// Close the file
		fclose($file);

		// Stop execution
		exit;
	}
}
