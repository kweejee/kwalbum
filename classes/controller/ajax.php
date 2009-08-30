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
		$userInput = trim(@$_GET['q']);
		$locations = Model_Kwalbum_Location::getNameArray(10, 0, $userInput, 'count DESC');

		foreach($locations as $location)
		{
			echo "$location\n";
		}
	}
	function action_GetInputTags()
	{
		$this->auto_render = false;
		$userInput = trim(@$_GET['q']);
		$tags = Model_Kwalbum_Tag::getTagArray(10, 0, $userInput, 'count DESC');

		foreach($tags as $tag)
		{
			echo "$tag\n";
		}
	}

	function action_upload()
	{
		$user = $this->user;
		if ( ! $user->can_add)
		{
			$this->template->content = new View('kwalbum/invalidpermission');
			return;
		}

		$this->auto_render = false;
		if ( ! empty($_FILES))
		{
			$adder = new Kwalbum_ItemAdder($user);
			if ($adder->save_upload())
				echo 1;
			else
				echo 'ItemAdder failed to save the new item';
		}
		echo 'empty FILES sent';
	}
}
