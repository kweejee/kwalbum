<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jun 30, 2009
 * @package kwalbum
 * @since 3.0 Jun 30, 2009
 */


class Controller_Item extends Controller_Kwalbum
{
	function before()
	{
		$this->auto_render = false;
		parent::before();
		if ($this->request->action != 'index' and $this->item->hide_level > $this->user->permission_level)
		{
			$this->request->action = 'hidden';
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
		$this->_send_file($this->item->path.$this->item->filename);
	}

	function action_thumbnail()
	{
		$this->_send_file($this->item->path.'t/'.$this->item->filename, '_thumbnail');
	}

	function action_resize()
	{
		$this->action_resized();
	}

	function action_resized()
	{
		$this->item->increase_count();
		$this->_send_file($this->item->path.'r/'.$this->item->filename, '_resized');
	}

	function action_original()
	{
		$this->item->increase_count();
		$this->_send_file($this->item->path.$this->item->filename);
	}

	function action_download()
	{
		$this->item->increase_count();
		$this->_send_file($this->item->path.$this->item->filename, '', true);
	}

	private function _send_file($filepathname, $filename_addition = '', $download = false)
	{
		$request = $this->request;

		if ( ! $filepath = realpath($filepathname))
		{
			// Return a 404 status
			$request->status = 404;
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
		$mimes = Kohana::config('mimes');
		$mime = $mimes[$extension][0];
		if (!$this->user->can_see_all) {
			$watermark = Kohana::config('kwalbum.watermark_filename');
			if ($watermark && $size < Kohana::config('kwalbum.watermark_filesize_limit'))
			{
				$watermark = Kohana::config('kwalbum.item_path').$watermark;
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
						$width_percent = Kohana::config('kwalbum.watermark_width_percent');
						$height_percent = Kohana::config('kwalbum.watermark_height_percent');
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
		$request->headers['Content-Disposition'] = ( $download ? 'attachment; ' : null)
			.'filename="'.$filename.$filename_addition
			.'.'.$extension.'"';
		$request->headers['Content-Type']  = $mime;
		$request->headers['Content-Length'] = $size;

		// Send all headers now
		$request->send_headers();

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
