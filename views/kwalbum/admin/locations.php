<?php
$config = Kohana::$config->load('kwalbum');
$all_locations_returned = Model_Kwalbum_Location::getAllArray();
$locations = array();
foreach ($all_locations_returned as $loc) {
    $full_name = Model_Kwalbum_Location::getFullName($loc['parent_name'], $loc['name']);
	$locations[$full_name.$loc['id']] = $loc;
}
uksort($locations, 'strnatcasecmp');
$hide_levels = Model_Kwalbum_Item::$hide_level_names;

echo HTML::script($kwalbum_url.'/media/ajax/jquery.jeditable.mini.js')
    .HTML::script($kwalbum_url.'/media/ajax/admin/locations.js');
?>
<div class="box">
	<big><b>Editing Locations</b></big>

    <table border="1">
        <tr>
            <th>Count</th>
            <th style="width:255px;">Name</th>
            <th style="width:150px;">Name Visibility</th>
            <th>Delete?</th>
            <th>Mapping Coordinates</th>
            <th style="width:150px;">Coordinate Visibility</th>
        </tr>
<?php
foreach ($locations as $loc) {
    $full_name = Model_Kwalbum_Location::getFullName($loc['parent_name'], $loc['name']);
    $count_link = HTML::anchor(
        $kwalbum_url.'/'.$full_name,
        '<span title="Items with this exact location">'.$loc['count'].'</span> / <span title="Total including child locations">'.($loc['count']+$loc['child_count']).'</span>'
    );
    $name_permission_class = 'kwalbumLocationNameHideLevel';
    if ($loc['id'] == 1) {
        $name_permission_class = 'kwalbumLocationNameHideLevelFixed';
    }
    $delete_content = "<a href='#' onClick='deleteLocation({$loc['id']});return false;'>[X]</a>";
    if ($loc['id'] == 1 or $loc['child_count']) {
		$delete_content = '<small><small>'
            .($loc['id'] == 1 ? 'default location' : 'has child locations')
            .'</small></small>';
    }
    $coord_permission_class = 'kwalbumLocationCoordinateHideLevel';
    if ($loc['id'] == 1) {
        $coord_permission_class = 'kwalbumLocationCoordinateHideLevelFixed';
    }
	echo <<<HTML
        <tr id='row{$loc['id']}'>
            <td>{$count_link}</td>
            <td><span class="kwalbumLocationName" id='location_{$loc['id']}'>{$full_name}</span></td>
            <td>
                <span class="{$name_permission_class}" id="kwalbumnamePermission_{$loc['id']}">{$hide_levels[$loc['name_hide_level']]}</span>
            </td>
            <td style='text-align:center'>{$delete_content}</td>
            <td>
                <span id='coord{$loc['id']}'>
                    {$loc['longitude']},{$loc['latitude']}
                </span>
            </td>
            <td>
                <span class="{$coord_permission_class}" id="kwalbumCoordPermission_{$loc['id']}">{$hide_levels[$loc['coordinate_hide_level']]}</span>
            </td>
        </tr>
HTML;
}
?>
</table></div>
