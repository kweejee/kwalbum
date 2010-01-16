<h2><?php echo html::anchor($kwalbum_url.'/~admin', 'Admin Options'); ?>: Editing Locations</h2>
<table border="1">
	<tr><th>Count</th><th style="width:255px;">Click to Edit Name</th><th>Delete?</th><th>Click to Edit Map</th></tr>
<?php
$locations = Model_Kwalbum_Location::getAllArray();
foreach ($locations as $loc)
{
	echo "	<tr id='row{$loc['id']}'><td>{$loc['count']}</td><td><span id='loc{$loc['id']}'>{$loc['name']}</span></td><td>"
		.($loc['id'] > 1 ? "<input type='button' onClick='deleteLocation({$loc['id']})' value='Delete'/>" : '&nbsp;')
		.'</td></tr>';
}
echo "</table>";

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