$(document).ready(function() {

	$('.main-content').show();

	$('#loader').hide();

    $(function(){
        $('#menu').slicknav({
            prependTo:'.navigation-bar',
            label: ''
        });
    });

    $('.select2-basic').select2({ width: '99%'});

	$('.dropdown').dropit();

	$(".close").click(function(event) {

		event.preventDefault();

		$(this).parents('.alert-large').remove();
	});

	$('.image-slider').slick({
		dots: true,
		infinite: true,
        autoplay:true,
        autoplaySpeed:1500,
		speed: 300,
		slidesToShow: 1,
		adaptiveHeight: true,
        prevArrow: false,
        nextArrow: false
	});

});

function dialog(heading,message,buttons) {

	$( ".dialog-message" ).html(message);

	$( "#dialog" ).attr('title', heading).dialog({
		draggable: false,
		hide: 'fade',
		show: 'fade',
		modal:true,
		buttons:buttons
	});

}

function getCommonActions(type){
	if(type=='confirm')
	{
		 return [
			{
				text: "OK",
				class: 'btn btn-blue',
				click: function() {
					$( this ).dialog( "close" );
				}
			}
		];
	}
}