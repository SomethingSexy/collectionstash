$( function() {
	$('#add-collection-form').ajaxForm({
		// dataType identifies the expected content type of the server response
		dataType:  'json',
		url: '/collections/add.json',
		beforeSubmit:  showRequest,
		// success identifies the function to invoke when the server response
		// has been received
		success: processJson
	});

	$( "#add-collection-dialog" ).dialog({
		'autoOpen' : false,
		'width' : 500,
		'height': 425,
		'resizable': false,
		'modal': true,
		'buttons': {
			"Submit": function() {
				$('#add-collection-form').submit();
			},
			Cancel: function() {
				$( this ).dialog( "close" );
			}
		}

	});
	$('.add-to-collection').click( function() {
		var collectibleId = $(this).parent('.links').children('.collectibleId').val();
		//TODO make this Ajax
		var showEditionSize = $(this).parent('.links').children('.showEditionSize').val();
		if(!showEditionSize) {
			$('#dialogEditionSize').parent('li').hide();
			//$('#dialogEditionSize').hide();
		} else {
			$('#dialogEditionSize').parent('li').show();
			//$('#dialogEditionSize').show();
		}
		$('#dialogEditionSize').val('');
		$('#dialogCost').val('');
		$('#collectiblesStashId').val($('#stashId').val());
		$('#collectiblesStashCollectibleId').val(collectibleId);

		//Reset the select
		var condition = $('#CollectiblesUserConditionId');
		condition.val($('option:first', condition).val());
		var merchant = $('#CollectiblesUserMerchantId');
		merchant.val($('option:first', merchant).val());

		//remove any errors that might be in the dialog before opening.
		$( "#add-collection-form > fieldset > ul.form-fields > li" ).children().remove('.error-message');
		$('#add-collection-dialog').children('#ajax-loader').remove();
		$('#collectibles-list-component > .inside').children('.component-message').remove();
		$('#add-collection-dialog').children().show();
		//reset height
		$( "#add-collection-dialog" ).dialog({
			height: 425
		});

		$( "#add-collection-dialog" ).dialog('open');
	});
	$('#dialogCost').blur( function() {
		$('#dialogCost').formatCurrency();
	});
});
function showRequest(formData, jqForm, options) {
	$('#add-collection-dialog').children().hide();
	$('#add-collection-dialog').append("<img id='ajax-loader' src='/img/ajax-loader.gif'/>");

	return true;
}

function processJson(data) {
	$('#dialogEditionSize').next('.error-message').remove();
	$('#dialogCost').next('.error-message').remove();
	$('#add-collection-dialog').children('#ajax-loader').remove();
	// 'data' is the json object returned from the server
	var success = data.success.isSuccess;
	//TODO need to handle here grabbing the success message
	if(success) {
		$('#collectibles-list-component > .inside > .component-title').after('<div class="component-message success"><span>' + data.success.message +'</span></div>')
		$( "#add-collection-dialog" ).dialog('close');
	} else {
		if(data.isTimeOut) {
			window.location = "/users/login";
		} else {
			if(data.errors[0]['edition_size']) {
				$('#dialogEditionSize').after('<div class="error-message">'+ data.errors[0]['edition_size'] +'</div>');
			}

			if(data.errors[0]['cost']) {
				$('#dialogCost').after('<div class="error-message">'+ data.errors[0]['cost'] +'</div>');
			}

			$( "#add-collection-dialog" ).dialog({
				height: 500
			});
		}

	}

	$('#add-collection-dialog').children().show();
}