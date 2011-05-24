var stash = function() {

	var $addDialog;
	var $editDialog;

	function showAddRequest(formData, jqForm, options) {
		$('#add-stash-form').children().hide();
		$('#add-stash-form').append("<img id='ajax-loader' src='/img/ajax-loader.gif'/>");
	
		return true;
	}
	
	function showEditRequest(formData, jqForm, options) {
		$('#edit-stash-form').children().hide();
		$('#edit-stash-form').append("<img id='ajax-loader' src='/img/ajax-loader.gif'/>");
	
		return true;
	}
	
	function processEditJson(data) {
		$('#edit-stash-form').children('#ajax-loader').remove();
		// 'data' is the json object returned from the server
		var success = data.success.isSuccess;
		
		if(success) {
			//Remove any messages first before we add a new one.
			$('#my-stashes-component > .inside > .component-title').next('.component-message').remove();
			$('#my-stashes-component > .inside > .component-title').after('<div class="component-message success"><span>' + data.success.message +'</span></div>');
			$( "#edit-stash-dialog" ).dialog('close');
			stash.update();
		} else {
			//remove any previous errors first
			$('#editDialogStashName').next('.error-message').remove();
	
			if(data.isTimeOut) {
				window.location = "/users/login";
			} else {
				if(data.errors[0]['totalAllowed']) {
					$('#my-stashes-component > .inside > .component-title').next('.component-message').remove();
					$('#my-stashes-component > .inside > .component-title').after('<div class="component-message error"><span>' + data.errors[0]['totalAllowed'] +'</span></div>')
					$( "#edit-stash-dialog" ).dialog('close');
				} else {
					if(data.errors[0]['name']) {
						$('#editDialogStashName').after('<div class="error-message">'+ data.errors[0]['name'] +'</div>');
					}
				}
	
				//$( "#add-stash-dialog" ).dialog({ height: 500 });
			}
	
		}
	
		$('#edit-stash-form').children().show();
	}
	
	function processAddJson(data) {
		$('#add-stash-form').children('#ajax-loader').remove();
		// 'data' is the json object returned from the server
		var success = data.success.isSuccess;
	
		if(success) {
			//Remove any messages first before we add a new one.
			$('#my-stashes-component > .inside > .component-title').next('.component-message').remove();
			$('#my-stashes-component > .inside > .component-title').after('<div class="component-message success"><span>' + data.success.message +'</span></div>');
			$( "#add-stash-dialog" ).dialog('close');
			stash.update();
		} else {
			//remove any previous errors first
			$('#addDialogStashName').next('.error-message').remove();
	
			if(data.isTimeOut) {
				window.location = "/users/login";
			} else {
				if(data.errors[0]['totalAllowed']) {
					$('#my-stashes-component > .inside > .component-title').next('.component-message').remove();
					$('#my-stashes-component > .inside > .component-title').after('<div class="component-message error"><span>' + data.errors[0]['totalAllowed'] +'</span></div>')
					$( "#add-stash-dialog" ).dialog('close');
				} else {
					if(data.errors[0]['name']) {
						$('#addDialogStashName').after('<div class="error-message">'+ data.errors[0]['name'] +'</div>');
					}
				}
	
				//$( "#add-stash-dialog" ).dialog({ height: 500 });
			}
	
		}
	
		$('#add-stash-form').children().show();
	}



	function initStashEdit() {
		$('#edit-stash-form').ajaxForm({
			// dataType identifies the expected content type of the server response
			dataType:  'json',
			url: '/stashs/edit.json',
			beforeSubmit:  showEditRequest,
			// success identifies the function to invoke when the server response
			// has been received
			success: processEditJson
		});
	
		$editDialog = $( "#edit-stash-dialog" ).dialog({
				'autoOpen' : false,
				'width' : 500,
				'height': 300,
				'resizable' : false,
				'modal': true,
				'buttons': {
					"Submit": function() {
						$('#edit-stash-form').submit();
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
			});
			
		$('.edit-stash').click( function() {
				//Edit specific setup
				var stashId = $(this).parent('div').parent('div').children('.stashId').val();
				var stashName = $(this).parent('div').parent('div').prev('h3').children('a').text();
				$('#editDialogStashId').val(stashId);
				$('#editDialogStashName').val(stashName);
				resetForm($('#edit-stash-form'));
				$( "#edit-stash-dialog" ).dialog('open')

			});	
	}
	
	function initStashAdd() {
		
		$('#add-stash-form').ajaxForm({
			// dataType identifies the expected content type of the server response
			dataType:  'json',
			url: '/stashs/add.json',
			beforeSubmit:  showAddRequest,
			// success identifies the function to invoke when the server response
			// has been received
			success: processAddJson
		});
	
		
		$addDialog = $( "#add-stash-dialog" ).dialog({
				'autoOpen' : false,
				'width' : 500,
				'height': 300,
				'modal': true,
				'resizable' : false,
				'buttons': {
					"Submit": function() {
						$('#add-stash-form').submit();
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
			});		
		$('.add-stash').click( function() {
				$('#flashMessage').remove();
				
				resetForm($('#add-stash-form'));
				$('#addDialogStashName').val('');

				$( "#add-stash-dialog" ).dialog({ height: 300 });
				//$('#addDialogStashName').next('.error-message').remove();
				$( "#add-stash-dialog" ).dialog('open');
			});		
	}
	
	function resetForm($form) {
		$form.children('#ajax-loader').remove();	
		$form.find('.error-message').remove();	
		$form.children().show();
	}

	return {
		init : function() {
			$('#stash-list-container').accordion({autoHeight: "true"});
			initStashEdit();
			initStashAdd();
			$( "#remove-stash-dialog" ).dialog({
				'autoOpen' : false,
				'width' : 300,
				'height': 250,
				'resizable': false,
				'modal': true,
				'buttons': {
					"Submit": function() {
						$('#remove-stash-form').submit();
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
			});			
			

			$('.remove-stash').click( function() {
				var stashId = $(this).parent('div').parent('div').children('.stashId').val();
				$('#removeDialogStashId').val(stashId);
				$( "#remove-stash-dialog" ).dialog('open')

			});

		},
		update : function() {
			$('#stash-list-container').accordion('destroy').children().remove();

			$.ajax({
				dataType:  'json',
				url: '/stashs/stashList.json',
				success: function(data) {
					var $stashListContainer = $('#stash-list-container');
					$.each(data, function(key, value) {
						//<h3><a href="#"><?php echo $details['Stash']['name']; ?></a></h3>
						var $h3 = $('<h3></h3>');
						var $h3A = $('<a></a>').text(value.Stash.name);
						$h3.append($h3A);
						$stashListContainer.append($h3);
						var $detailsList = $('<div></div>').addClass('stash-list-details');
						var $divTotal = $('<div></div>').text('There are ' + value.Stash.count + ' collectibles in this stash.');
						$detailsList.append($divTotal);
						$stashListContainer.append($detailsList);

						var $stashActions = $('<div></div>').addClass('stash-actions');

						var $viewAction = $('<a></a>').attr('href','/collections/index/' + value.Stash.id).text('View');
						var $addAction = $('<a></a>').attr('href','/collections/addSearch/stashId:' + value.Stash.id + '/initial:yes').text('Add');
						var $editAction = $('<a></a>').addClass('edit-stash').addClass('link').text('Edit');
						var $removeAction = $('<a></a>').addClass('remove-stash').addClass('link').text('Remove');
						$stashActions.append($viewAction).append(' | ').append($addAction).append(' | ').append($editAction).append(' | ').append($removeAction);
						var $stashId = $('<input/>').addClass('stashId').attr('type','hidden').val(value.Stash.id);

						$detailsList.append($stashActions).append($stashId)

						$stashListContainer.append($detailsList);
					});
					stash.init();
				}
			});
		}
	};
}();