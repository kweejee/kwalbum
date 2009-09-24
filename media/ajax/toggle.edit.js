// Kwalbum 3.0
$(document).ready(function(){ 
	$("#kwalbumEditToggleEdit").click(function() {
		$.post("KWALBUM_URL/~ajax/SetEditMode", {edit: '1'},function(){location.reload(true);});
	});
	$("#kwalbumEditToggleView").click(function() {
		$.post("KWALBUM_URL/~ajax/SetEditMode", {edit: '0'},function(){location.reload(true);});
	});
});