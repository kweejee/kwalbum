<?php
$config = Kohana::$config->load('kwalbum');
$all_locations = Model_Kwalbum_Location::getAllArray();
$locations = array();
foreach ($all_locations as $loc) {
	$locations[$loc['id']] = $loc;
}
$all_locations = $locations;
$locations = array();
foreach ($all_locations as $loc) {
    $loc_parent = '';
    if ($loc['parent_location_id']) {
        $loc_parent = $all_locations[$loc['parent_location_id']]['name'].$config->location_separator_1;
    }
	$locations[$loc_parent.$loc['name']] = $loc;
}
uksort($locations, 'strnatcasecmp');

echo html::script($kwalbum_url.'/media/ajax/jquery.jeditable.mini.js')
    .html::script($kwalbum_url.'/media/ajax/admin/locations.js');
?>
<div class="box">
	<big><b>Editing Locations</b></big>

    <table border="1">
        <tr><th>Count</th><th style="width:255px;">Name</th><th>Delete?</th><th>Mapping Coordinates</th></tr>
<?php
foreach ($locations as $full_name => $loc) {
    $count_link = html::anchor(
        $kwalbum_url.'/'.$full_name,
        '<span title="Items with this exact location">'.$loc['count'].'</span> / <span title="Total including child locations">'.($loc['count']+$loc['child_count']).'</span>'
    );
    $delete_content = "<a href='#' onClick='deleteLocation({$loc['id']});return false;'>[X]</a>";
    if ($loc['id'] == 1 or $loc['child_count']) {
		$delete_content = '<small><small>'
            .($loc['id'] == 1 ? 'default location' : 'has child locations')
            .'</small></small>';
    }
	echo <<<HTML
        <tr id='row{$loc['id']}'>
            <td>{$count_link}</td>
            <td><span class="kwalbumLocationName" id='location_{$loc['id']}'>{$full_name}</span></td>
            <td style='text-align:center'>{$delete_content}</td>
            <td>
                <span id='coord{$loc['id']}' onClick='window.open(\"{$kwalbum_url}/~admin/locationmap?id={$loc['id']}\")'>
                    {$loc['longitude']},{$loc['latitude']}
                </span>
            </td>
        </tr>
HTML;
}
?>
</table></div>
