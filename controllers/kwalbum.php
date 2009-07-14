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

class Kwalbum_Controller extends Kwalbum_Template_Controller
{
	// allow to run in production
	const ALLOW_PRODUCTION = true;

	function index()
	{
		$this->template->content = new View('index');

	}
}