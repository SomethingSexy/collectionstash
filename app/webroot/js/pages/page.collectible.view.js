$(function() {
	if (showStatus) {
		// Get all of the data here
		$.when($.get('/templates/collectibles/status.dust')).done(function(statusTemplate) {
			// since I am only loading one, don't need to index
			// TODO: This page should use a different view template for different text
			dust.loadSource(dust.compile(statusTemplate, 'status.edit'));

			var status = new Status();
			status.set(collectibleStatus, {
				silent : true
			});

			$('#status-container').html(new StatusView({
				model : status,
				allowEdit : allowStatusEdit,
				collectible : new Backbone.Model(collectible)
			}).render().el);

			// If the status has changed and I am on the view
			//page and they change the status and it is a draft
			// go to the edit page
			status.on('sync', function() {
				if (this.toJSON().status.id === '1') {
					window.location.href = '/collectibles/edit/' + this.id
				}
			}, status);

		});
	} else {
		$('#status-container').remove();
	}

});
