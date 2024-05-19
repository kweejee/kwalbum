// Kwalbum 3.0
$(document).ready(function(){
    $('.kwalbumLocationName').editable('KWALBUM_URL/~ajaxAdmin/EditLocationName',
    {
        type:"text",
        tooltip:"Click to edit...",
        indicator:"Saving...",
        onblur:"submit",
        width:250
    });
    $('.kwalbumLocationNameHideLevel').editable('KWALBUM_URL/~ajaxAdmin/EditLocationNameHideLevel',
    {
        type:"select",
        tooltip:"Click to edit...",
        indicator:"Saving...",
        onblur:"submit",
        style:"inherit",
        loadurl:'KWALBUM_URL/~ajaxAdmin/GetLocationNameHideLevel'
    });
    $('.kwalbumLocationCoordinateHideLevel').editable('KWALBUM_URL/~ajaxAdmin/EditLocationCoordinateHideLevel',
    {
        type:"select",
        tooltip:"Click to edit...",
        indicator:"Saving...",
        onblur:"submit",
        style:"inherit",
        loadurl:'KWALBUM_URL/~ajaxAdmin/GetLocationCoordinateHideLevel'
    });
});
const deleteLocation = function (id) {
    if (confirm('You are about to permanently delete "'+$('#location_'+id).text()+'".')) {
        $.post(
            "KWALBUM_URL/~ajaxAdmin/DeleteLocation",
            {id: id},
            function () {
                $('#row'+id).hide();
            }
        );
        $('#loc'+id).text('deleting...');
    }
};
