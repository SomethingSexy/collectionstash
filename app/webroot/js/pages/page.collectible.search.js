		var stashAddView = null;

		// when it changes, show the modal
		collectibleForStash.on('change', function() {
			var collectibleUser = new CollectibleUserModel();

			if (stashAddView) {
				stashAddView.remove();
				delete stashAddView;
			}

			stashAddView = new StashAddView({
				collectible : collectibleForStash,
				model : collectibleUser
			});

			$('body').append(new stashAddView.render().el);

			$('#stash-add-dialog', 'body').on('hidden', function() {
				self.addNewView.remove();
			});

			$('#stash-add-dialog').modal();

		}, this);

		$('.add-stash-link').click(function(event) {
			var collectibleId = $(event.currentTarget).attr('data-id');

			if (collectibleForStash.isNew()) {
				collectibleForStash.set({
					id : collectibleId
				}, {
					silent : true
				});

				$.blockUI({
					message : 'Loading...',
					css : {
						border : 'none',
						padding : '15px',
						backgroundColor : ' #F1F1F1',
						'-webkit-border-radius' : '10px',
						'-moz-border-radius' : '10px',
						color : '#222',
						background : 'none repeat scroll 0 0 #F1F1F',
						'border-radius' : '5px 5px 5px 5px',
						'box-shadow' : '0 0 10px rgba(0, 0, 0, 0.5)'
					}
				});

				collectibleForStash.fetch({
					success : function() {
						$.unblockUI();
					},
					error : function() {
						$.unblockUI();
					}
				});
			} else {
				// trigger change to open modal
				collectibleForStash.trigger('change');
			}

		});