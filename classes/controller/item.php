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
	function action_index()
	{
		$view = new View('kwalbum/item/single');
		$view->item = $this->item;
		$this->template->content = $view;
		$this->template->title = 'single item';

		$view->description = 'description stuff';

	}

	function action_thumbnail()
	{
		$this->auto_render = false;
		$this->_send_file($this->item->path.'t/'.$this->item->filename, '_thumbnail');
	}

	function action_resize()
	{
		$this->action_resized();
	}

	function action_resized()
	{
		$this->auto_render = false;
		$this->_send_file($this->item->path.'r/'.$this->item->filename, '_resized');
	}

	function action_original()
	{
		$this->auto_render = false;
		$this->_send_file($this->item->path.$this->item->filename);
	}

	function action_download()
	{
		$this->auto_render = false;
		$this->_send_file($this->item->path.$this->item->filename, '', true);
	}

	private function _send_file($filepath, $filename_addition = '', $download = false)
	{
		$request = $this->request;

		// Get the complete file path
		$filepath = realpath($filepath);

		// Use the file name as the download file name
		$filename = pathinfo($filepath, PATHINFO_FILENAME);

		if ( ! is_file($filepath))
		{
			// Return a 404 status
			$this->request->status = 404;
			return;
		}

		// Get the file size
		$size = filesize($filepath);

		// Get the file extension
		$extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

		// Guess the mime using the file extension
		$mime = Kohana::config('mimes');
		$mime = $mime[$extension][0];

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
