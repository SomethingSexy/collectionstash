// at some point we should probably break this out into transaction views
// I also put the price data in this view so I didn't have to create more files
// performance vs standards
var TransactionsView = Backbone.View.extend({
	el : '#transactions',
	className : '',
	events : {
		'click .add-transaction' : 'submit',
		'click .flag' : 'flag',
		'click .delete' : 'deleteListing'

	},
	initialize : function(options) {
		this.collectible = options.collectible;
	},
	submit : function() {
		var self = this;
		var model = new ListingModel({
			'ext_item_id' : $('#inputListingItem', this.el).val(),
			'collectible_id' : this.collectible.get('id'),
			'listing_type_id' : 1
		});

		$('.add-transaction', this.el).button('loading');

		model.save({}, {
			success : function(model, response, options) {
				$('#inputListingItem', this.el).val(''),
				$('.add-transaction', self.el).button('reset');
				if (response.response.isSuccess) {

					// if it not processed then it is active
					if (!model.get('processed')) {
						// render as active listing
						dust.render('transaction.active', model.toJSON(), function(error, output) {
							$('.active-listings tbody', self.el).append(output);
						});

						$('.active-listings', self.el).show();
					} else if (model.get('status') === 'completed' && model.get('quantity_sold') === '0') {
						// render as unsold listing
						dust.render('transaction.unsold', model.toJSON(), function(error, output) {
							$('.unsold-listings tbody', self.el).append(output);
						});

						$('.unsold-listings', self.el).show();
					}

					if (model.get('Transactions') && model.get('Transactions').length > 0) {
						// if it has any transactions render them
						dust.render('transaction.completed', model.toJSON(), function(error, output) {
							$('.completed-listings tbody', self.el).append(output);
						});

						$('.completed-listings', self.el).show();
					}
				} else {
					self.errors = response.response.errors;
					// render message
					//self.render();
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

				//self.render();
			}
		});

	},
	deleteListing : function(event) {
		event.preventDefault();
		var modelId = $(event.currentTarget, this.el).attr('data-id');
		var model = new ListingModel({
			id : modelId
		});
		// delete
		model.destroy();
		// don't wait, just remove
		$(event.currentTarget).closest('.listing').remove();
	},
	flag : function(event) {
		event.preventDefault();

		if (!$(event.currentTarget).hasClass('disabled')) {
			var $tr = $(event.currentTarget).closest('.listing');

			var listingData = JSON.parse($tr.attr('data-listing'));

			var model = new ListingModel(listingData);

			if (model.get('flagged')) {
				model.set({
					flagged : false
				});
				$(event.currentTarget).removeClass('btn-danger');
			} else {
				model.set({
					flagged : true
				});
				$(event.currentTarget).addClass('btn-danger');
			}

			// reset the data in case it gets clicked again
			$tr.attr('data-listing', JSON.stringify(model.toJSON()));

			model.save();
		}

	}
});
