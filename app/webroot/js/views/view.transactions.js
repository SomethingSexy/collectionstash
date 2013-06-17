// at some point we should probably break this out into transaction views
var TransactionsView = Backbone.View.extend({
	template : 'transactions',
	className : '',
	events : {
		'click .add-transaction' : 'submit',
		'click .flag' : 'flag',
		'click .delete' : 'deleteListing'

	},
	initialize : function(options) {
		options.allowEdit ? this.allowEdit = true : this.allowEdit = false;
		this.allowMaintenance = options.allowDeleteListing;
		this.allowAdd = options.allowAddListing;
		this.collectible = options.collectible;
		this.collection.on('add', this.render, this);
		this.collection.on('remove', this.render, this);
		this.collection.on('change:flagged', this.render, this);
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
			completedTransactions : completedTransactions,
			allowMaintenance : this.allowMaintenance,
			allowAdd : this.allowAdd
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

		$('.add-transaction', this.el).button('loading');

		model.save({}, {
			success : function(model, response, options) {
				$('.add-transaction', self.el).button('reset');
				if (response.response.isSuccess) {
					self.collection.add(model);
				} else {
					self.errors = response.response.errors;
					self.render();
				}
			},
			error : function(model, xhr, options) {
				$('.add-transaction', self.el).button('reset');
				if (xhr && xhr.status) {
					self.errors = [{
						message : 'You must be logged in to submit listings.',
						inline : 'false'
					}];
				} else {
					self.errors = [{
						message : 'There was an issue with the request.',
						inline : 'false'
					}];
				}

				self.render();
			}
		})

	},
	deleteListing : function(event) {
		var modelId = $(event.currentTarget, this.el).attr('data-id');
		var model = this.collection.get(modelId);
		model.destroy();
	},
	flag : function(event) {
		var modelId = $(event.currentTarget, this.el).attr('data-id');

		if (!$(event.currentTarget, this.el).hasClass('disabled')) {
			var model = this.collection.get(modelId);

			if (model.get('flagged')) {
				model.set({
					flagged : false
				});
			} else {
				model.set({
					flagged : true
				});
			}

			model.save();

		}

	}
});
