<div class="box">
	<big><b><?php echo html::anchor($kwalbum_url.'/~admin', 'Admin Options'); ?>: Editing Locations</b></big>

<table border="1">
	<tr><th>Count</th><th style="width:255px;">Name</th><th>Delete?</th><th>Mapping Coordinates</th></tr><pre>
<?php
$all_locations = Model_Kwalbum_Location::getAllArray();
$locations = array();
foreach ($all_locations as $loc)
	$locations[$loc['id']] = $loc;
$all_locations = $locations;
$locations = array();
foreach ($all_locations as $loc)
	$locations[($loc['parent_location_id'] ? $all_locations[$loc['parent_location_id']]['name'].': ' : '').$loc['name']] = $loc;
ksort($locations);
foreach ($locations as $full_name => $loc)
{
	echo "	<tr id='row{$loc['id']}'><td>"
		.html::anchor($kwalbum_url.'/'.$full_name, '<span title="Items with this exact location">'.$loc['count'].'</span> / <span title="Total including child locations">'.($loc['count']+$loc['child_count']).'</span>')
		."</td><td><span id='loc{$loc['id']}'>{$full_name}</span></td><td style='text-align:center'>"
		.(($loc['id'] > 1 && !$loc['child_count']) ? "<a href='#' onClick='deleteLocation({$loc['id']});return false;'>[X]</a>" : ($loc['child_count'] ? '<small><small>has child locations</small></small>' : '&nbsp;'))
		."</td><td><span id='coord{$loc['id']}' onClick='window.open(\"{$kwalbum_url}/~admin/locationmap?id={$loc['id']}\")'>{$loc['longitude']},{$loc['latitude']}</span></td></tr>";
}
echo "</table></div>";

echo html::script($kwalbum_url.'/media/ajax/jquery.jeditable.mini.js')
?>
<script type="text/javascript">
function deleteLocation(id){
	if (confirm('You are about to permanently delete "'+$('#loc'+id).text()+'".')){
			$.post("<?php echo $kwalbum_url; ?>/~ajaxAdmin/DeleteLocation", {id:id},function(){$('#row'+id).hide();});
			$('#loc'+id).text('deleting...');
	}
}
<?php
foreach ($locations as $loc)
{
?>
	$('#loc<?php echo $loc['id']; ?>').editable('<?php echo $kwalbum_url; ?>/~ajaxAdmin/EditLocationName',{
		type:"text",tooltip:"Click to edit...",indicator:"Saving...",
		onblur:"submit",width:250,submitdata:{id:<?php echo $loc['id']; ?>}
	});
<?php
}
?>
</script>