<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Aug 24, 2009
 */

class Controller_Ajax extends Controller_Kwalbum
{
	function action_GetInputLocations()
	{
		$this->auto_render = false;
		$locations = Model_Kwalbum_Location::getNameArray(10, 0, $_GET['q'], 'count DESC');

		foreach($locations as $location)
		{
			echo "$location\n";
		}
	}
	function action_GetInputTags()
	{
		$this->auto_render = false;
		$tags = Model_Kwalbum_Tag::getTagArray(10, 0, $_GET['q'], 'count DESC');

		foreach($tags as $tag)
		{
			echo "$tag\n";
		}
	}
}
