<div class="box">
	<big><b><?php echo html::anchor($kwalbum_url.'/~admin', 'Admin Options'); ?>: Editing People</b></big>

<table border="1">
	<tr><th>Count</th><th style="width:255px;">Name</th><th>Delete?</th></tr>
<?php
$people = Model_Kwalbum_Person::getAllArray();
foreach ($people as $person)
{
	echo "	<tr id='row{$person['id']}'><td>"
		.html::anchor($kwalbum_url.'/people/'.$person['name'], $person['count'])
		."</td><td><span id='per{$person['id']}'>{$person['name']}</span></td><td style='text-align:center'>"
		."<a href='#' onClick='deletePerson({$person['id']});return false;'>[X]</a></td></tr>";
}
echo "</table></div>";

echo html::script($kwalbum_url.'/media/ajax/jquery.jeditable.mini.js')
?>
<script type="text/javascript">
function deletePerson(id){
	if (confirm('You are about to permanently delete "'+$('#per'+id).text()+'"')){
			$.post("<?php echo $kwalbum_url; ?>/~ajaxAdmin/DeletePerson", {id:id},function(){$('#row'+id).hide();});
			$('#per'+id).text('deleting...');
	}
}
<?php
foreach ($people as $person)
{
?>
	$('#per<?php echo $person['id']; ?>').editable('<?php echo $kwalbum_url; ?>/~ajaxAdmin/EditPersonName',{
		type:"text",tooltip:"Click to edit...",indicator:"Saving...",
		onblur:"submit",width:250,submitdata:{id:<?php echo $person['id']; ?>}
	});
<?php
}
?>
</script>