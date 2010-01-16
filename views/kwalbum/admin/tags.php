<h2><?php echo html::anchor($kwalbum_url.'/~admin', 'Admin Options'); ?>: Editing Tags</h2>
<table border="1">
	<tr><th>Count</th><th style="width:255px;">Click to Edit Name</th><th>Delete?</th></tr>
<?php
$tags = Model_Kwalbum_Tag::getAllArray();
foreach ($tags as $tag)
{
	echo "	<tr id='row{$tag['id']}'><td>{$tag['count']}</td><td><span id='tag{$tag['id']}'>{$tag['name']}</span></td><td>"
		."<input type='button' onClick='deleteTag({$tag['id']})' value='Delete'/></td></tr>";
}
echo "</table>";

echo html::script($kwalbum_url.'/media/ajax/jquery.jeditable.mini.js')
?>
<script type="text/javascript">
function deleteTag(id){
	if (confirm('You are about to permanently delete "'+$('#tag'+id).text()+'"')){
			$.post("<?php echo $kwalbum_url; ?>/~ajaxAdmin/DeleteTag", {id:id},function(){$('#row'+id).hide();});
			$('#tag'+id).text('deleting...');
	}
}
<?php
foreach ($tags as $tag)
{
?>
	$('#tag<?php echo $tag['id']; ?>').editable('<?php echo $kwalbum_url; ?>/~ajaxAdmin/EditTagName',{
		type:"text",tooltip:"Click to edit...",indicator:"Saving...",
		onblur:"submit",width:250,submitdata:{id:<?php echo $tag['id']; ?>}
	});
<?php
}
?>
</script>