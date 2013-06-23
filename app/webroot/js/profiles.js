var stashAccount = function() {

	function submitSuccess(data) {
		$('#stash-submit').button('reset');
		if (data.success.isSuccess) {
			$('.alert-success', '#privacy').show().html('<p>' + data.success.message + '</p>');
		} else {
			if (data.isTimeOut) {
				window.location = "/users/login";
			} else {
				$('.alert-error', '#privacy').show().html('<p>There was a problem updating your privacy settings.</p>');
			}
		}
	}

	function beforeSubmit() {
		$('.alert', '#privacy').hide().html('');
		$('#stash-submit').button('loading');
		return true;
	}

	return {
		init : function() {
			$('#stash-form').ajaxForm({
				// dataType identifies the expected content type of the server response
				dataType : 'json',
				url : '/stashs/updateProfileSettings.json',
				beforeSubmit : beforeSubmit,
				// success identifies the function to invoke when the server response
				// has been received
				success : submitSuccess
			});
			$('#stash-submit').click(function(event) {
				$('#stash-form').submit();
				event.preventDefault();
			});
		},
		update : function() {

		},
		close : function() {
			_close();
		}
	};
}();

$(function() {
	stashAccount.init();
});
