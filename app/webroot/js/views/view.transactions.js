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
		this.errors = [];
	},
	render : function() {
		var self = this;

		var activeListings = false;
		var completedTransactions = false;

		this.collection.each(function(listing) {
			if (!listing.get('processed')) {
				activeListings = true;
			}

			if (listing.get('Transaction').length > 0) {
				completedTransactions = true;
			}
		});

		var data = {
			listings : this.collection.toJSON(),
			errors : this.errors,
			activeListings : activeListings,
			completedTransactions : completedTransactions
		};

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		//once we are done rendering clear errors
		this.errors = [];

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
				if (response.response.isSuccess) {
					self.collection.add(model);
				} else {
					self.errors = response.response.errors;
					self.render();
				}
			},
			error : function() {
				self.errors = [{
					message : 'There was an issue with the request.',
					inline : 'false'
				}];
				self.render();
			}
		})

	}
});
