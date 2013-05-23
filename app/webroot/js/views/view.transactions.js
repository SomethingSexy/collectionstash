var TransactionsView = Backbone.View.extend({
	template : 'transactions',
	className : 'well',
	events : {
		'click .add-transaction' : 'submit'
	},
	initialize : function(options) {
		options.allowEdit ? this.allowEdit = true : this.allowEdit = false;
		this.collectible = options.collectible;
		this.collection.on('add', this.render, this);
	},
	render : function() {
		var self = this;

		var data = {
			listings : this.collection.toJSON()
		};

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	},
	submit : function() {
		var self = this;
		var model = new ListingModel({
			'ext_item_id' : $('#inputListingItem', this.el).val(),
			'collectible_id' : this.collectible.get('id')
		});

		model.save({}, {
			success : function(model, response, options) {
				self.collection.add(model);
			},
			error : function() {

			}
		})

	}
});
