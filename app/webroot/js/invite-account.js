var inviteAccount = function() {

	function invite(element) {
		$(element).hide();
		$('#account').children('.inside').children('.account.page').children('.account.detail').children('.account.detail.view').hide();
		$('#account').children('.inside').children('.account.page').children('.account.detail').children('.account.detail.update').show();
		$('#invite-email').val('').focus();
	}

	return {
		init : function() {
			$('#account').children('.inside').children('.account.page').children('.title').children('.link').children('a').click( function() {
				invite(this);
			});
			$('#invite-cancel').click(function(){
				
				$('#account').children('.inside').children('.account.page').children('.account.detail').children('.account.detail.view').show();
				$('#account').children('.inside').children('.account.page').children('.account.detail').children('.account.detail.update').hide();	
				$('#account').children('.inside').children('.account.page').children('.title').children('.link').children('a').show();				
			});
		},
		update : function() {

		}
	};
}();
$( function() {
	inviteAccount.init();
});