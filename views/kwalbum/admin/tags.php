<div class="box">
	<big><b><?php echo HTML::anchor($kwalbum_url.'/~admin', 'Admin Options'); ?>: Editing Tags</b></big>

<table border="1">
	<tr><th>Count</th><th style="width:255px;">Name</th><th>Delete?</th></tr>
<?php
$tags = Model_Kwalbum_Tag::get_all_array();
foreach ($tags as $tag)
{
	echo "	<tr id='row{$tag['id']}'><td>"
		.HTML::anchor($kwalbum_url.'/tags/'.rawurlencode($tag['name']), $tag['count'])
		."</td><td><span id='tag{$tag['id']}'>{$tag['name']}</span></td><td style='text-align:center'>"
		."<a href='#' onClick='deleteTag({$tag['id']});return false;'>[X]</a></td></tr>";
}
echo "</table></div>";

echo HTML::script($kwalbum_url.'/media/ajax/jquery.jeditable.mini.js')
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