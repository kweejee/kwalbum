<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Sep 1, 2009
 */
?>
<div class='kwalbumBox'>

<?php
echo $item->pretty_date.'<br/>';
// show thumbnail based on file type
if ($item->type == 'jpeg' or $item->type == 'gif' or $item->type == 'png')
{
	echo html::anchor($kwalbum_url.'/~'.$item->id.'/'.$kwalbum_url_params,
		"<img src='$kwalbum_url/~$item->id/~item/thumbnail' title='$item->filename'/>")."\n";
	echo substr($item->description, 0, 50)
		.(strlen($item->description) > 50 ? '...' : null);
}
else if ($item->type == 'description only')
{
	echo substr($item->description, 0, 200)
		.(strlen($item->description) > 200 ? '...' : null);
}

?>
</div>