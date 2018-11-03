$(document).ready(function() {

	$('.main-content').show();

	$('#loader').hide();

	$('.select2-basic').select2({ width: '99%'});

    $( "#accordion" ).accordion();

    $(function() {

		var index = $('li.current_ancestor').index();

		index<0 ? index=false : index;

		$( '#sidebar-menu' ).accordion({
			heightStyle: 'content',
			icons: false,
			collapsible: true,
			active: index
		});
	});

	$('.dropdown').dropit();

	$(".close").click(function(event) {

		event.preventDefault();

		$(this).parents('.alert-large').remove();
	});

	$('.confirm-delete-modal').click(function(event){

		event.preventDefault();

		window.clickedURL = $(this).attr("href");

		var buttons = [
			{
				text: "No",
				class: 'btn btn-red',
				click: function() {
					$( this ).dialog( "close" );
				}
			},
			{
				text: "Yes",
				class: 'btn btn-green',
				click: function() {
					window.open(window.clickedURL,'_parent');
				}
			}
		];

		dialog('Confirm Action',"Are you sure you want to <a href='#'>delete</a> this record !",buttons);

	});

	$('.btn-disabled').click(function(event){

		event.preventDefault();

		window.clickedURL = $(this).attr("href");

		dialog('Action not allowed',"Action is <a href='#'>not</a> allowed for this record !",getCommonActions('confirm'));

	});


    $('.multi-select').multiSelect({
        selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Search...'>",
        selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Search...'>",
        afterInit: function(ms){
            var that = this,
                $selectableSearch = that.$selectableUl.prev(),
                $selectionSearch = that.$selectionUl.prev(),
                selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

            that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                .on('keydown', function(e){
                    if (e.which === 40){
                        that.$selectableUl.focus();
                        return false;
                    }
                });

            that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                .on('keydown', function(e){
                    if (e.which == 40){
                        that.$selectionUl.focus();
                        return false;
                    }
                });
        },
        afterSelect: function(){
            this.qs1.cache();
            this.qs2.cache();
        },
        afterDeselect: function(){
            this.qs1.cache();
            this.qs2.cache();
        }
    });


    $('.date').datepicker({
			dateFormat: 'yy-mm-dd'
	});

	$('.datetime').datetimepicker({
		hourText: 'Hr',
		minuteText: 'Min',
		dateFormat:'yy-mm-dd'
	});

	if ($(".markdown").length > 0)
	{
		var simplemde = new SimpleMDE({
			element: $(".markdown")[0],
            /*toolbar: ["bold", "italic","|",
                "heading-1","heading-2","heading-3","|",
                "quote","unordered-list","ordered-list","|",
                "link","image","|","preview","guide"
            ]*/
        });
	}

	$(':file').filer({limit:1});

	$(".form-wizard").formwizard({
			formPluginEnabled: false,
			validationEnabled: false,
			focusFirstInput : false,
			disableInputFields: true,
			textSubmit:'Save'
	});
	
	if ($("#imageUploadBox").length > 0) {

		//noinspection JSUnresolvedVariable
		var options = {
			thumbBox: '.thumbBox',
			spinner: '.spinner',
			imgSrc: imageFile
		}

		var cropper = $('.imageBox').cropbox(options);

		$('#file').on('change', function () {
			var reader = new FileReader();
			reader.onload = function (e) {
				options.imgSrc = e.target.result;
				cropper = $('.imageBox').cropbox(options);
			}
			reader.readAsDataURL(this.files[0]);
			if (!this.files[0].type.match("^image")) {
				dialog('Action Failed', 'File type not supported', getCommonActions('confirm'));
			}

		})

		$('#btnCrop').on('click', function () {
			var img = cropper.getDataURL();

			$('#loader').show();

			//noinspection JSUnresolvedVariable
			$.ajax({
				type: 'POST',
				url: imageUploadPath,
				data: {image: img, primaryIdentifier: primaryIdentifier},
				dataType: 'json',
				success: function (data) {
					$('#loader').hide();
					if (data.status == 'success') {
						dialog('Action Complete', data.message, getCommonActions('confirm'));
					} else {
						dialog('Action Failed', data.message, getCommonActions('confirm'));
					}
				},
				error: function () {
					$('#loader').hide();
					dialog('Action Failed', "Internal server error", getCommonActions('confirm'));
				}
			});
		})

		$('#btnZoomIn').on('click', function () {
			cropper.zoomIn();
		})

		$('#btnZoomOut').on('click', function () {
			cropper.zoomOut();
		})
	}

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

$('.open-map').on('click', function () {
    $("#dialog-message").dialog({
        modal: true,
        minWidth: 620

    });
});
	
});

function hideBasedOnChoice(target,affected){

	if($(target+':checked').val()!='1')
	{
		$(affected).hide();
	}

	$(target).change(function(){

		var value=$(target+':checked').val();

		if (value =='1') {
			$(affected).show('slow');
		}
		else {
			$(affected).hide('fast');
		}
	});

}

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