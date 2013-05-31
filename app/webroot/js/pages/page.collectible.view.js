$(function() {
	// Get all of the data here
	$.when($.get('/templates/collectibles/status.dust'), $.get('/templates/transactions/transactions.dust')).done(function(statusTemplate, transactionsTemplate) {
		dust.loadSource(dust.compile(transactionsTemplate[0], 'transactions'));
		// grab the template-stash-add
		dust.loadSource(dust.compile($('#template-stash-add').html(), 'stash.add'));
		var collectibleModel = new Backbone.Model(collectible);
		var listingsList = new Listings(listings);

		if (showStatus) {

			// since I am only loading one, don't need to index
			// TODO: This page should use a different view template for different text
			dust.loadSource(dust.compile(statusTemplate[0], 'status.edit'));

			var status = new Status();
			status.set(collectibleStatus, {
				silent : true
			});

			$('#status-container').html(new StatusView({
				model : status,
				allowEdit : allowStatusEdit,
				collectible : collectibleModel
			}).render().el);

			// If the status has changed and I am on the view
			//page and they change the status and it is a draft
			// go to the edit page
			status.on('sync', function() {
				if (this.toJSON().status.id === '1') {
					window.location.href = '/collectibles/edit/' + this.id
				}
			}, status);

		} else {
			$('#status-container').remove();
		}

		$('#transactions').html(new TransactionsView({
			collectible : collectibleModel,
			collection : listingsList,
			allowDeleteListing : allowDeleteListing,
			allowAddListing : allowAddListing
		}).render().el);

		var stashAddView = null;
		
		// TODO: We need to do a call to see if the user
		// akready owns this guy
		
		$('.add-stash-link').click(function(event) {
			var collectibleUser = new CollectibleUserModel();

			if (stashAddView) {
				stashAddView.remove();
				delete stashAddView;
			}

			stashAddView = new StashAddView({
				collectible : collectibleModel,
				model : collectibleUser
			});

			$('body').append(stashAddView.render().el);

			$('#stash-add-dialog', 'body').on('hidden', function() {
				stashAddView.remove();
			});

			$('#stash-add-dialog').modal();
		});
	});

});
