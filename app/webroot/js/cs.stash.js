! function($) {"use strict";// jshint ;_;

	/* PUBLIC CLASS DEFINITION
	 *
	 * Add in here later whether or not we quick add or not - TODO
	 *
	 * This requires, Backbone, dust, stash model, stash view
	 ** ============================== */

	var StashFullAdd = function() {
	}

	StashFullAdd.prototype.initialize = function() {
		dust.loadSource(dust.compile($('#template-stash-add').html(), 'stash.add'));
		var self = this;
		this.stashAddView = null;
		this.collectibleUser = null;

		$('#stash-add-dialog', 'body').on('hidden', function() {
			self.stashAddView.remove();
		});

		$('#stash-add-dialog').on('click', '.save', function() {
			self.collectibleUser.save({}, {
				success : function(model, response, options) {
					if (response.response.isSuccess) {
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
					} else {
						if (response.response.errors) {
							self.stashAddView.errors = response.response.errors;
							self.stashAddView.render();
						}
					}
				},
				error : function(model, xhr, options) {

				}
			});

		});

	};

	StashFullAdd.prototype.add = function(collectibleModel, stashType) {
		this.stashType = stashType;

		this.collectibleUser = new CollectibleUserModel({
			'collectible_id' : collectibleModel.get('id')
		});

		this.collectibleUser.stashType = this.stashType;

		if (this.stashAddView) {
			this.stashAddView.remove();
			delete this.stashAddView;
		}

		this.stashAddView = new StashAddView({
			collectible : collectibleModel,
			model : this.collectibleUser
		});

		$('.modal-body', '#stash-add-dialog').html(this.stashAddView.render().el);

		$('#stash-add-dialog').modal();
	};

	/* BUTTON PLUGIN DEFINITION
	 * ======================== */

	$.fn.stashfulladd = function(option, model, stashType) {
		return this.each(function() {
			var $this = $(this);

			if (option == 'add') {
				stashFullAdd.add(model, stashType);
			}
		});
	}

	$.fn.stashfulladd.defaults = {

	}

	// only want one created really
	var stashFullAdd = new StashFullAdd();

	//$.fn.stashfulladd.Constructor = StashFullAdd

	/* DATA-API
	 * =============== */

	$(function() {
		stashFullAdd.initialize();
		$('.stashable').on('click', 'a.add-full-to-stash', function(e) {
			var $anchor = $(e.currentTarget)

			var collectibleModel = new Backbone.Model(JSON.parse($anchor.attr('data-collectible')));

			$anchor.stashfulladd('add', collectibleModel, 'Default');
			e.preventDefault();
		});
	});
}(window.jQuery); ! function($) {"use strict";// jshint ;_;

	/* PUBLIC CLASS DEFINITION
	 *
	 * Add in here later whether or not we quick add or not - TODO
	 ** ============================== */

	var StashAdd = function(element, options) {
		this.$element = $(element);
		this.options = $.extend({}, $.fn.stashadd.defaults, options);
		this.collectibleId = this.$element.attr('data-collectible-id');
		this.stashType = this.$element.attr('data-stash-type');
	}

	StashAdd.prototype.add = function() {
		var self = this;
		$.ajax({
			dataType : 'json',
			type : 'post',
			data : {
				'_method' : 'POST'
			},
			url : '/collectibles_users/quickAdd/' + this.collectibleId + '/' + this.stashType,
			beforeSend : function(formData, jqForm, options) {

			},
			// success identifies the function to invoke when the server response
			// has been received
			success : function(data, textStatus, jqXHR) {
				if (data.response.isSuccess) {
					var message = (self.stashType === 'Default') ? 'Collectible has been added to your Stash!' : 'Collectible has been added to your Wishlist!'
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

				} else {
					if (data.response.errors) {
						$.each(data.response.errors, function(index, value) {
							if (value.inline) {
								$(':input[name="data[' + value.model + '][' + value.name + ']"]', '#AttributeRemoveForm').after('<div class="error-message">' + value.message + '</div>');
							} else {
								$.blockUI({
									message : '<button class="close" data-dismiss="alert" type="button">×</button>' + value.message,
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
							}

						});
					}
				}
			}
		});
	};
	/* BUTTON PLUGIN DEFINITION
	 * ======================== */

	$.fn.stashadd = function(option) {
		return this.each(function() {
			var $this = $(this), data = $this.data('stashadd'), options = typeof option == 'object' && option
			if (!data) {
				$this.data('stashadd', ( data = new StashAdd(this, options)));
			}

			if (option == 'add') {
				data.add();
			}
		});
	}

	$.fn.stashadd.defaults = {

	}

	$.fn.stashadd.Constructor = StashAdd

	/* DATA-API
	 * =============== */

	$(function() {
		$('.stashable').on('click', 'a.add-to-stash', function(e) {
			var $anchor = $(e.currentTarget)
			$anchor.stashadd('add');
			e.preventDefault();
		});
	});
}(window.jQuery);

// Stash remove
! function($) {"use strict";// jshint ;_;

	/* PUBLIC CLASS DEFINITION
	 *
	 * Add in here later whether or not we quick add or not - TODO
	 ** ============================== */
	var StashPromptRemove = function() {

	};

	StashPromptRemove.prototype.initialize = function() {
		dust.loadSource(dust.compile($('#template-stash-remove').html(), 'stash.remove'));
		var self = this;
		this.stashRemoveView = null;
		this.collectibleUser = null;

		$('#stash-remove-dialog', 'body').on('hidden', function() {
			self.stashRemoveView.remove();
		});

		$('#stash-remove-dialog').on('click', '.save', function() {
			self.collectibleUser.destroy({
				url : self.collectibleUser.url('delete'),
				success : function(model, response, options) {
					if (response.response.isSuccess) {
						$('#stash-remove-dialog').modal('hide');
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
					} else {
						if (response.response.errors) {
							self.stashAddView.errors = response.response.errors;
							self.stashAddView.render();
						}
					}
				},
				error : function(model, xhr, options) {

				}
			});

		});

	};

	StashPromptRemove.prototype.remove = function(options) {
		this.collectibleUser = new CollectibleUserModel({
			id : options.collectibleUserId
		});

		if (this.stashRemoveView) {
			this.stashRemoveView.remove();
			delete this.stashRemoveView;
		}

		this.stashRemoveView = new StashRemoveView({
			model : this.collectibleUser,
			collectible : options.collectibleModel,
			reasons : options.reasons
		});

		$('.modal-body', '#stash-remove-dialog').html(this.stashRemoveView.render().el);

		$('#stash-remove-dialog').modal();
	};

	var StashRemove = function(element, options) {
		this.$element = $(element);
		this.$stashItem = this.$element.closest('.stash-item');
		this.options = $.extend({}, $.fn.stashremove.defaults, options);
		this.collectibleUserId = this.$element.attr('data-collectible-user-id');
		this.stashType = this.$element.attr('data-stash-type');
		if (options.tiles) {
			this.$tiles = $('.tiles');
		}
	}

	StashRemove.prototype.remove = function() {

		var self = this;
		$.ajax({
			dataType : 'json',
			type : 'post',
			data : {
				'_method' : 'POST'
			},
			url : '/collectibles_users/remove/' + this.collectibleUserId,
			beforeSend : function(formData, jqForm, options) {

			},
			// success identifies the function to invoke when the server response
			// has been received
			success : function(data, textStatus, jqXHR) {
				if (data.response.isSuccess) {
					var message = (self.stashType.toLowerCase() === 'default') ? 'Collectible has been removed from your Stash!' : 'Collectible has been removed from your Wishlist!'
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
					if (self.$tiles) {
						self.$tiles.masonry('remove', self.$stashItem);
						self.$tiles.masonry('reload');
					} else {
						self.$stashItem.remove();
					}

				} else {
					if (data.response.errors) {
						$.each(data.response.errors, function(index, value) {
							if (value.inline) {
								$(':input[name="data[' + value.model + '][' + value.name + ']"]', '#AttributeRemoveForm').after('<div class="error-message">' + value.message + '</div>');
							} else {
								$.blockUI({
									message : '<button class="close" data-dismiss="alert" type="button">×</button>' + value.message,
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
							}

						});
					}
				}
			}
		});

	};
	/* BUTTON PLUGIN DEFINITION
	 * ======================== */

	$.fn.stashremove = function(options) {
		return this.each(function() {
			var $this = $(this);
			if (options.prompt) {
				stashPromptRemove.remove(options);
			} else {

				var data = $this.data('stashremove');
				if (!data) {
					$this.data('stashremove', ( data = new StashRemove(this, options)));
				}

				data.remove();
			}

		});
	}

	$.fn.stashremove.defaults = {

	}

	$.fn.stashremove.Constructor = StashRemove;

	var stashPromptRemove = new StashPromptRemove();

	/* DATA-API
	 * =============== */

	$(function() {
		stashPromptRemove.initialize();
		var reasonsCollection = new Backbone.Collection(JSON.parse(reasons))

		var tile = false;
		if ($('.stashable').hasClass('tiles')) {
			tile = true;
		}

		$('.stashable').on('click', 'a.remove-from-stash', function(e) {
			var $anchor = $(e.currentTarget);
			var prompt = $anchor.attr('data-prompt');
			var collectibleModel = new Backbone.Model(JSON.parse($anchor.attr('data-collectible')));
			var collectibleUserId = $anchor.attr('data-collectible-user-id');
			$anchor.stashremove({
				tiles : tile,
				prompt : prompt,
				collectibleModel : collectibleModel,
				reasons : reasonsCollection,
				collectibleUserId : collectibleUserId
			});
			e.preventDefault();
		});
	});
}(window.jQuery);

