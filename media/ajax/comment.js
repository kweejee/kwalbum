// Kwalbum 3.0
$(document).ready(function(){ 
	$('#comment_save').click(function(){
		$.post('KWALBUM_URL/~ajax/AddComment',
			{comment:$('#comment_text').val(),item:item_id},
			function(data){
				$('#new_comment').html(data);
			}
		);
	});
});