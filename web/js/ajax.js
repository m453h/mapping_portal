$(document).ready(function() {
	

	handleSelectBox('regionId','districtId',Routing.generate('api_get_districts'));
	handleSelectBox('districtId','wardId',Routing.generate('api_get_wards'));

    $(".court-data-ajax").select2({
        ajax: {
            url: Routing.generate('api_get_courts'),
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term, // search term
                    page: params.page || 1
                };
            },
            processResults: function (data) {
                return {
                    results: data.results,
                    pagination: {
                        'more': data.pagination.more
                    }
                }
            },
            cache: true
        }
    });





});


function handleSelectBox(sourceId,targetId,link)
{

	$('#'+sourceId).change(function(){

		$('#loader').show();

		$('#'+targetId).empty();

		$.ajax({
			type: 'POST',
			url: link,
			data: {value:$('#'+sourceId).val() },
			dataType: 'json',
			success: function (data) {
				loadList(targetId, data);
			},
			error: function(){
				$('#loader').hide();
			}
		});

		return false;
	});

}

function loadList(id, response){
	$('#'+id).append($("<option></option>").attr("value",'').text(''));
	$.each(response, function(index, element) {
		$('#'+id).append($("<option></option>").attr("value",element.value).text(element.name));
	});

	$('#loader').hide();
}