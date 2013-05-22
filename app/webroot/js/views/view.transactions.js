var TransactionsView = Backbone.View.extend({
	template : 'transactions',
	className : 'well',
	events : {
		'click .add-transaction' : 'submit'
	},
	initialize : function(options) {
		options.allowEdit ? this.allowEdit = true : this.allowEdit = false;
		this.collectible = options.collectible;
	},
	render : function() {
		var self = this;

		var data = {
			transactions : this.collection.toJSON()
		};

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	},
	submit : function() {
		var model = new TransactionModel({
			'ext_transaction_id' : $('#inputTransactionItem', this.el).val(),
			'collectible_id' : this.collectible.get('id')
		});

		model.save({}, {
			success : function() {

			},
			error : function() {

			}
		})

	}
});
