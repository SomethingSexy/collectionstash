var inviteAccount = function() {

	function _viewSuccess(data) {
		stashAccount.close();
		$('#account-invites').children('div.body').children('.account.detail.view').children('div.standard-list').children('ul').children().remove();
		$('#account-invites').children('div.body').children('.account.detail.view').children('div.standard-list').removeClass('empty');	
		if(!isNaN(parseFloat(data.responseData.remaining)) && isFinite(data.responseData.remaining)) {
			$('#account-invites-left').text('You have ' + data.responseData.remaining + ' invites remaining.');
		}
		
		if(data.responseData.remaining <= 0){
			$('#invite-user').hide();
		} else {
			$('#invite-user').show();
		}

		if(data.responseData.Invites) {
			if(data.responseData.Invites.length > 0) {
										// echo '<li class="title">';
						// echo '<span class="attribute-name">'.__('Part', true).'</span>';
						// echo '<span class="attribute-description">'.__('Description', true).'</span>';
						// echo '</li>';
				$titleLi = $('<li></li>').addClass('title');
				$titleLi.append($('<span></span>').text('Email')).append($('<span></span>').text('Status'));
				$('#account-invites').children('div.body').children('.account.detail.view').children('div.standard-list').children('ul').append($titleLi);
				var $li;
				$.each(data.responseData.Invites, function(key, value){
					$li = $('<li></li>').append($('<span></spann').text(value.Invite.email));
					var status = '';
					if(value.Invite.registered === '0'){
						status = 'Not registered';
					} else {
						status = 'Registered';
					}
					$li.append($('<span></spann').text(status));
					$('#account-invites').children('div.body').children('.account.detail.view').children('div.standard-list').children('ul').append($li)
				});
					
				
			} else {
				$('#account-invites').children('div.body').children('.account.detail.view').children('div.standard-list').addClass('empty');
				var $li = $('<li></li>').text('You have not invited anyone to Collection Stash.');
				$('#account-invites').children('div.body').children('.account.detail.view').children('div.standard-list').children('ul').append($li);
			}
		}
		$('#account-invites').children('div.body').children('.account.detail.view').show();
		$('#account-invites').children('div.body').children('.account.detail.update').hide();
		$('#account-invites').children('div.body').show();
		$('#account-invites').children('.header').children('span.action').children('a').text('Close');
		$('#account-invites').addClass('selected');
	}

	function invite() {
		$('#account-invites').children('div.body').children('.account.detail.update').children('#ajax-loader').remove();
		$('#account-invites').children('div.body').children('.account.detail.update').children().show();
		$('#invite-email').next('.error-message').remove();
		$('#account-invites').children('div.body').children('.account.detail.view').hide();
		$('#account-invites').children('div.body').children('.account.detail.update').show();
		$('#invite-email').val('').focus();
	}

	function viewInvites() {
	
		$.ajax({
			type: "POST",
			dataType:  'json',
			url: '/invites/view.json',
			//data: "name=John&location=Boston",
			success: function(data, textStatus, XMLHttpRequest) {
				if(data.success.isSuccess) {
					_viewSuccess(data);
				} else {
					if(data.isTimeOut) {
						window.location = "/users/login";
					} else {
						if(data.errors[0]['email']) {
							$('#invite-email').after('<div class="error-message">'+ data.errors[0]['email'] +'</div>')
						}
					}
				}

			},
			error: function(jqXHR, textStatus, errorThrown) {

			}
		});
	}

	function submitSuccess(data) {
		$('#invite-email').next('.error-message').remove();
		if(data.success.isSuccess) {
			
			viewInvites();
		} else {
			if(data.isTimeOut) {
				window.location = "/users/login";
			} else {
				$('#account-invites').children('div.body').children('.account.detail.update').children('#ajax-loader').remove();
				$('#account-invites').children('div.body').children('.account.detail.update').children().show();
				if(data.errors[0]['email']) {
					$('#invite-email').after('<div class="error-message">'+ data.errors[0]['email'] +'</div>');
				}
			}
		}
	}
	
	function beforeSubmit(formData, jqForm, options) {
		$('#account-invites').children('div.body').children('.account.detail.update').children().hide();
		$('#account-invites').children('div.body').children('.account.detail.update').append("<img id='ajax-loader' src='/img/ajax-loader.gif'/>");
	
		return true;
	}
	
	function _close() {
		$('#account-invites').children('div.body').children('.account.detail.view').hide();
		$('#account-invites').children('div.body').children('.account.detail.update').hide();
		$('#account-invites').children('div.body').hide();
		$('#account-invites').children('.header').children('span.action').children('a').text('View');
		$('#account-invites').removeClass('selected');			
	}

	return {
		init : function() {
			$('#account-invites').children('.header').children('span.action').children('a').click( function() {
				if($('#account-invites').hasClass('selected')){
					_close();
				} else {
					viewInvites();	
				}
			});
			$('#invite-user').click( function() {
				invite();
			});
			$('#invite-form').ajaxForm({
				// dataType identifies the expected content type of the server response
				dataType:  'json',
				url: '/invites/add.json',
				beforeSubmit:  beforeSubmit,
				// success identifies the function to invoke when the server response
				// has been received
				success: submitSuccess
			});

			$('#invite-submit').click( function() {
				$('#invite-form').submit();
			})
			// $('#account').children('.inside').children('.account.page').children('.title').children('.link').children('a').click( function() {
			// invite(this);
			// });
			// $('#invite-cancel').click(function(){
			//
			// $('#account').children('.inside').children('.account.page').children('.account.detail').children('.account.detail.view').show();
			// $('#account').children('.inside').children('.account.page').children('.account.detail').children('.account.detail.update').hide();
			// $('#account').children('.inside').children('.account.page').children('.title').children('.link').children('a').show();
			// });
		},
		update : function() {

		},
		close : function() {
			_close();
		}
	};
}();

var stashAccount = function() {
	
	function _viewSuccess(data) {
		inviteAccount.close();
		
		if(data.responseData.privacy) {
			$('#stash-privacy').attr('checked', true);	
		} else {
			$('#stash-privacy').attr('checked', false);
		}
		
		
		$('#account-stash').children('div.body').children('.account.detail.update').show();
		$('#account-stash').children('div.body').show();
		$('#account-stash').children('.header').children('span.action').children('a').text('Close');
		$('#account-stash').addClass('selected');		
	}
	
	function viewStashDetails() { 
		
		$.ajax({
			type: "POST",
			dataType:  'json',
			url: '/stashs/profileSettings.json',
			//data: "name=John&location=Boston",
			success: function(data, textStatus, XMLHttpRequest) {
				if(data.success.isSuccess) {
					_viewSuccess(data);
				} else {
					if(data.isTimeOut) {
						window.location = "/users/login";
					} else {
			
					}
				}

			},
			error: function(jqXHR, textStatus, errorThrown) {

			}
		});		

	}
	
	function _close(){
		$('#account-stash').children('div.body').children('.account.detail.update').hide();
		$('#account-stash').children('div.body').hide();	
		$('#account-stash').children('.header').children('span.action').children('a').text('View');
		$('#account-stash').removeClass('selected');	
	}
	
	return {
		init : function() {
			$('#account-stash').children('.header').children('span.action').children('a').click( function() {
				if($('#account-stash').hasClass('selected')){
					_close();
				} else {
					viewStashDetails();	
				}				
			});
		},
		update : function() {

		},
		close : function() {
			_close();
		}
	};
}();

$( function() {
	inviteAccount.init();
	stashAccount.init();
});