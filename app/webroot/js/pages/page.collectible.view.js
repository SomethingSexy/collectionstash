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
			var collectibleUser = new CollectibleUserModel({
				'collectible_id' : collectibleModel.get('id')
			});

			collectibleUser.stashType = 'Default';

			if (stashAddView) {
				stashAddView.remove();
				delete stashAddView;
			}

			stashAddView = new StashAddView({
				collectible : collectibleModel,
				model : collectibleUser
			});

			stashAddView.on('add:success', function() {
				$('#stash-add-dialog').modal('hide');
				var message = 'You have successfully added the collectible to your stash!';

				$.blockUI({
					message : '<button class="close" data-dismiss="alert" type="button">×</button>' + message,
					showOverlay : false,
					css : {
						top : '100px',
						'background-color' : '#DDFADE',
						border : '1px solid #93C49F',
						'box-shadow' : '3px 3px 5px rgba(0, 0, 0, 0.5)',
						'border-radius' : '4px 4px 4px 4px',
						color : '#333333',
						'margin-bottom' : '20px',
						padding : '8px 35px 8px 14px',
						'text-shadow' : '0 1px 0 rgba(255, 255, 255, 0.5)',
						'z-index' : 999999
					},
					timeout : 2000
				});
			});

			$('body').append(stashAddView.render().el);

			$('#stash-add-dialog', 'body').on('hidden', function() {
				stashAddView.remove();
			});

			$('#stash-add-dialog').modal();
		});
	});

});
