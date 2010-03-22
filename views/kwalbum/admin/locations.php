<div class="box">
	<big><b><?php echo html::anchor($kwalbum_url.'/~admin', 'Admin Options'); ?>: Editing Locations</b></big>

<table border="1">
	<tr><th>Count</th><th style="width:255px;">Name</th><th>Delete?</th><th>Mapping Coordinates</th></tr>
<?php
$locations = Model_Kwalbum_Location::getAllArray();
foreach ($locations as $loc)
{
	echo "	<tr id='row{$loc['id']}'><td>"
		.html::anchor($kwalbum_url.'/'.$loc['name'], $loc['count'])
		."</td><td><span id='loc{$loc['id']}'>{$loc['name']}</span></td><td style='text-align:center'>"
		.($loc['id'] > 1 ? "<a href='#' onClick='deleteLocation({$loc['id']});return false;'>[X]</a>" : '&nbsp;')
		."</td><td><span id='coord{$loc['id']}' onClick='window.open(\"{$kwalbum_url}/~admin/locationmap?id={$loc['id']}\")'>{$loc['longitude']},{$loc['latitude']}</span></td></tr>";
}
echo "</table></div>";

echo html::script($kwalbum_url.'/media/ajax/jquery.jeditable.mini.js')
?>
<script type="text/javascript">
function deleteLocation(id){
	if (confirm('You are about to permanently delete "'+$('#loc'+id).text()+'"')){
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