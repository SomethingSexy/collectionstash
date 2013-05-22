var TransactionModel = Backbone.Model.extend({
	url : function() {
		return '/transactions/transaction';
	},
	parse : function(resp, xhr) {
		return resp.response.data;
	}
});
