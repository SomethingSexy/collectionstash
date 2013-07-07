$(function() {

	$('#myTab a').click(function(e) {
		e.preventDefault();
		$(this).tab('show');
	});
	// Get all of the data here
	$.when($.get('/templates/collectibles/status.dust'), $.get('/templates/transactions/transactions.dust')).done(function(statusTemplate, transactionsTemplate) {
		dust.loadSource(dust.compile(transactionsTemplate[0], 'transactions'));
		// grab the template-stash-add
		var collectibleModel = new Backbone.Model(collectible);
		var listingsList = new Listings(listings);

		// global variable that comes from the page, status is only for new collectibles
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

		// currently the other sections on the detail page are
		// not backbone (not sure they ever will be), so we are going to have
		// to do some hiding/showing

		$('#transactions').html(new TransactionsView({
			collectible : collectibleModel,
			collection : listingsList,
			allowDeleteListing : allowDeleteListing,
			allowAddListing : allowAddListing
		}).render().el);

		$.plot("#holder", [transactionsGraphData], {
			xaxis : {
				mode : "time",
				timeformat : "%m/%d/%y",
			},
			yaxes : [{
				min : 0
			}, {
				// align if we are to the right
				alignTicksWithAxis : "right",
				position : "right",
			}],
		});

	});

});
