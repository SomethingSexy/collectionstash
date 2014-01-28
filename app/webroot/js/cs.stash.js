// TODO: Lot's of duplicate code in here
! function($) {"use strict";// jshint ;_;

	/* PUBLIC CLASS DEFINITION
	 *
	 * Add in here later whether or not we quick add or not - TODO
	 *
	 * This requires, Backbone, dust, stash model, stash view
	 ** ============================== */

	var StashFullAdd = function() {
	};

	StashFullAdd.prototype.initialize = function() {
		dust.loadSource(dust.compile($('#template-stash-add').html(), 'stash.add'));
		var self = this;
		this.stashAddView = null;
		this.collectibleUser = null;

		$('#stash-add-dialog', 'body').on('hidden', function() {
			self.stashAddView.remove();
		});

		$('#stash-add-dialog').on('click', '.save', function() {
			var $button = $(this);
			$button.button('loading');
			self.collectibleUser.save({}, {
				success : function(model, response, options) {
					$button.button('reset');
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
					$button.button('reset');
				}
			});

		});

	};

	StashFullAdd.prototype.add = function(collectibleModel) {
		this.collectibleUser = new CollectibleUserModel({
			'collectible_id' : collectibleModel.get('id')
		});

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

	$.fn.stashfulladd = function(option, model) {
		return this.each(function() {
			var $this = $(this);

			if (option == 'add') {
				stashFullAdd.add(model);
			}
		});
	};

	$.fn.stashfulladd.defaults = {

	};

	// only want one created really
	var stashFullAdd = new StashFullAdd();

	//$.fn.stashfulladd.Constructor = StashFullAdd

	/* DATA-API
	 * =============== */

	$(function() {
		stashFullAdd.initialize();
		$('.stashable').on('click', '.add-full-to-stash', function(e) {
			var $anchor = $(e.currentTarget);

			var collectibleModel = new Backbone.Model(JSON.parse($anchor.attr('data-collectible')));

			$anchor.stashfulladd('add', collectibleModel, 'Default');
			e.preventDefault();
		});
	});
}(window.jQuery);
! function($) {"use strict";// jshint ;_;

	/* PUBLIC CLASS DEFINITION
	 *
	 * Add in here later whether or not we quick add or not - TODO
	 ** ============================== */

	var StashAdd = function(element, options) {
		this.$element = $(element);
		this.options = $.extend({}, $.fn.stashadd.defaults, options);
		this.collectibleId = this.$element.attr('data-collectible-id');
	};

	StashAdd.prototype.add = function() {
		var self = this;
		$.ajax({
			dataType : 'json',
			type : 'post',
			data : {
				'_method' : 'POST'
			},
			url : '/collectibles_users/quickAdd/' + this.collectibleId,
			beforeSend : function(formData, jqForm, options) {

			},
			// success identifies the function to invoke when the server response
			// has been received
			success : function(data, textStatus, jqXHR) {
				if (data.response.isSuccess) {
					var message = 'The collectible has been added to your Stash!';
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
			var $this = $(this), data = $this.data('stashadd'), options = typeof option == 'object' && option;
			if (!data) {
				$this.data('stashadd', ( data = new StashAdd(this, options)));
			}

			if (option == 'add') {
				data.add();
			}
		});
	};

	$.fn.stashadd.defaults = {

	};

	$.fn.stashadd.Constructor = StashAdd;

	/* DATA-API
	 * =============== */

	$(function() {
		$('.stashable').on('click', '.add-to-stash', function(e) {
			var $anchor = $(e.currentTarget);
			$anchor.stashadd('add');
			e.preventDefault();
		});
	});
}(window.jQuery);

// Add to Wishlist
! function($) {"use strict";// jshint ;_;
	/* PUBLIC CLASS DEFINITION
	 *
	 * Add in here later whether or not we quick add or not - TODO
	 ** ============================== */

	var WishListAdd = function(element, options) {
		this.$element = $(element);
		this.options = $.extend({}, $.fn.wishlistadd.defaults, options);
		this.collectibleId = this.$element.attr('data-collectible-id');
	};

	WishListAdd.prototype.add = function() {
		var self = this;
		$.ajax({
			dataType : 'json',
			type : 'post',
			data : {
				'_method' : 'POST'
			},
			url : '/collectibles_wish_lists/collectible/' + this.collectibleId,
			beforeSend : function(formData, jqForm, options) {

			},
			// success identifies the function to invoke when the server response
			// has been received
			success : function(data, textStatus, jqXHR) {
				var message = 'The collectible has been added to your Wish List!';
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
			},
			error : function(jqXHR, textStatus, errorThrown) {
				var errorMessage = 'Oops! Something went terribly wrong!';
				if (jqXHR.status === 400) {
					$.each(jqXHR.responseJSON.response.errors, function(index, value) {
						errorMessage = value.message;
					});
				}

				$.blockUI({
					message : '<button class="close" data-dismiss="alert" type="button">×</button>' + errorMessage,
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
	};
	/* BUTTON PLUGIN DEFINITION
	 * ======================== */

	$.fn.wishlistadd = function(option) {
		return this.each(function() {
			var $this = $(this), data = $this.data('wishlistadd'), options = typeof option == 'object' && option;
			if (!data) {
				$this.data('wishlistadd', ( data = new WishListAdd(this, options)));
			}

			if (option == 'add') {
				data.add();
			}
		});
	};

	$.fn.wishlistadd.defaults = {

	};

	$.fn.wishlistadd.Constructor = WishListAdd;

	/* DATA-API
	 * =============== */

	$(function() {
		$('.stashable').on('click', '.add-to-wishlist', function(e) {
			var $anchor = $(e.currentTarget);
			$anchor.wishlistadd('add');
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
			var $button = $(this);
			$button.button('loading');
			// TODO: The business logic here should know if this is an update or destroy
			// if we are removing because it is sold, it should be an update, we are not actually
			// remove the model.
			self.collectibleUser.destroy({
				url : self.collectibleUser.url('delete'),
				success : function(model, response, options) {
					$button.button('reset');
					if (response.response.isSuccess) {
						$('#stash-remove-dialog').modal('hide');
						var message = 'You have successfully removed the collectible from your stash!';

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

						// checking for array here is pretty dumb
						if (response.response.data && !_.isArray(response.response.data) && self.historyView) {
							// should update the view to indicate the values that were set
							self.$stashItem.find('.bought-sold-icon').html('<i class="icon-minus"></i>');
						} else {
							if (self.redirect) {
								window.location.href = self.redirect;
							} else if (self.$tiles) {
								self.$tiles.masonry('remove', self.$stashItem);
								self.$tiles.masonry('reload');
							} else {
								self.$stashItem.remove();
							}
						}

					} else {
						if (response.response.errors) {
							self.stashRemoveView.errors = response.response.errors;
							self.stashRemoveView.render();
						}
					}
				},
				error : function(model, xhr, options) {
					$button.button('reset');
				}
			});

		});

	};

	StashPromptRemove.prototype.remove = function(options) {
		if (options.tiles) {
			this.$tiles = $('.tiles');
		}

		if (options.redirect) {
			this.redirect = options.redirect;
		}

		if (options.historyView) {
			this.historyView = options.historyView;
		}

		this.$stashItem = options.$stashItem;
		this.collectibleUser = options.collectibleUserModel;

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

	/* BUTTON PLUGIN DEFINITION
	 * ======================== */

	$.fn.stashremove = function(options) {
		return this.each(function() {
			var $this = $(this);
			stashPromptRemove.remove(options);

		});
	};

	$.fn.stashremove.defaults = {

	};

	var stashPromptRemove = new StashPromptRemove();

	/* DATA-API
	 * =============== */

	$(function() {
		stashPromptRemove.initialize();
		var reasonsCollection = null;
		if ( typeof reasons !== 'undefined') {
			reasonsCollection = new Backbone.Collection(JSON.parse(reasons));
		} else {
			reasonsCollection = new Backbone.Collection();
		}

		var tile = false;
		if ($('.stashable').hasClass('tiles')) {
			tile = true;
		}

		$('.stashable').on('click', '.remove-from-stash', function(e) {
			var $anchor = $(e.currentTarget);

			var historyView = $anchor.closest('.stashable').attr('data-history');

			var collectibleModel = new Backbone.Model(JSON.parse($anchor.attr('data-collectible')));
			var collectibleUserModel = new CollectibleUserModel(JSON.parse($anchor.attr('data-collectible-user')));
			var collectibleUserId = $anchor.attr('data-collectible-user-id');
			var $stashItem = $anchor.closest('.stash-item');
			var redirect = $anchor.attr('data-remove-redirect');
			$anchor.stashremove({
				$stashItem : $stashItem,
				tiles : tile,
				collectibleModel : collectibleModel,
				collectibleUserModel : collectibleUserModel,
				reasons : reasonsCollection,
				collectibleUserId : collectibleUserId,
				redirect : redirect,
				historyView : historyView
			});
			e.preventDefault();
		});
	});
}(window.jQuery);
! function($) {"use strict";// jshint ;_;

	/* PUBLIC CLASS DEFINITION
	 ** ============================== */

	var WishListRemove = function(element, options) {
		this.$element = $(element);
		this.$stashItem = this.$element.closest('.stash-item');
		this.options = $.extend({}, $.fn.stashremove.defaults, options);
		this.collectibleUserId = this.$element.attr('data-collectible-user-id');
		if (options.tiles) {
			this.$tiles = $('.tiles');
		}
	};

	WishListRemove.prototype.remove = function() {

		var self = this;
		$.ajax({
			dataType : 'json',
			type : 'delete',
			data : {
				'_method' : 'DELETE'
			},
			url : '/collectibles_wish_lists/collectible/' + this.collectibleUserId,
			beforeSend : function(formData, jqForm, options) {

			},
			// success identifies the function to invoke when the server response
			// has been received
			success : function(data, textStatus, jqXHR) {
				var message = 'The collectible has been removed from your Wish List!';
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
			},
			error : function(jqXHR, textStatus, errorThrown) {
				var errorMessage = 'Oops! Something went terribly wrong!';
				if (jqXHR.status === 400) {
					$.each(jqXHR.responseJSON.response.errors, function(index, value) {
						errorMessage = value.message;
					});
				}

				$.blockUI({
					message : '<button class="close" data-dismiss="alert" type="button">×</button>' + errorMessage,
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

	};
	/* BUTTON PLUGIN DEFINITION
	 * ======================== */

	$.fn.wishlistremove = function(options) {
		return this.each(function() {
			var $this = $(this);

			var data = $this.data('wishlistremove');
			if (!data) {
				$this.data('wishlistremove', ( data = new WishListRemove(this, options)));
			}

			data.remove();
		});
	};

	$.fn.wishlistremove.defaults = {

	};

	$.fn.wishlistremove.Constructor = WishListRemove;

	/* DATA-API
	 * =============== */

	$(function() {
		var tile = false;
		if ($('.stashable').hasClass('tiles')) {
			tile = true;
		}

		$('.stashable').on('click', '.remove-from-wishlist', function(e) {
			var $anchor = $(e.currentTarget);
			var collectibleModel = new Backbone.Model(JSON.parse($anchor.attr('data-collectible')));
			var collectibleUserModel = new CollectibleUserModel(JSON.parse($anchor.attr('data-collectible-user')));
			var collectibleUserId = $anchor.attr('data-collectible-user-id');
			var $stashItem = $anchor.closest('.stash-item');
			$anchor.wishlistremove({
				$stashItem : $stashItem,
				tiles : tile,
				collectibleModel : collectibleModel,
				collectibleUserModel : collectibleUserModel,
				collectibleUserId : collectibleUserId
			});
			e.preventDefault();
		});
	});
}(window.jQuery); ! function($) {"use strict";// jshint ;_;

	/* PUBLIC CLASS DEFINITION
	 *
	 * Add in here later whether or not we quick add or not - TODO
	 *
	 * This requires, Backbone, dust, stash model, stash view
	 ** ============================== */

	var StashSell = function() {
	};

	StashSell.prototype.initialize = function() {
		dust.loadSource(dust.compile($('#template-stash-sell').html(), 'stash.sell'));
		var self = this;
		this.stashSellView = null;
		this.collectibleUser = null;

		$('#stash-sell-dialog', 'body').on('hidden', function() {
			self.stashSellView.remove();
		});

		$('#stash-sell-dialog').on('click', '.save', function() {
			var $button = $(this);
			$button.button('loading');
			self.collectibleUser.save({}, {
				success : function(model, response, options) {
					$button.button('reset');
					if (response.response.isSuccess) {
						$('#stash-sell-dialog').modal('hide');
						var message = 'You have successfully added the collectible to your sale/trade list!';

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
							self.stashSellView.errors = response.response.errors;
							self.stashSellView.render();
						}
					}
				},
				error : function(model, xhr, options) {
					$button.button('reset');

					if (xhr.status === 500) {
						self.stashSellView.errors = xhr.responseJSON.response.errors;
						self.stashSellView.render();
					}
				}
			});

		});

	};

	StashSell.prototype.sell = function(collectibleModel, collectibleUserModel) {
		this.collectibleUser = collectibleUserModel;

		// mark that we are selling this guy
		this.collectibleUser.set({
			'sale' : true
		}, {
			silent : true
		});

		if (this.stashSellView) {
			this.stashSellView.remove();
			delete this.stashSellView;
		}

		this.stashSellView = new StashSellView({
			collectible : collectibleModel,
			model : this.collectibleUser
		});

		$('.modal-body', '#stash-sell-dialog').html(this.stashSellView.render().el);

		$('#stash-sell-dialog').modal();
	};

	/* BUTTON PLUGIN DEFINITION
	 * ======================== */

	$.fn.stashsell = function(model, collectibleUserModel) {
		return this.each(function() {
			var $this = $(this);

			stashSell.sell(model, collectibleUserModel);
		});
	};

	$.fn.stashsell.defaults = {

	};

	// only want one created really
	var stashSell = new StashSell();

	//$.fn.stashfulladd.Constructor = StashFullAdd

	/* DATA-API
	 * =============== */

	$(function() {
		stashSell.initialize();
		$('.stashable').on('click', '.stash-sell', function(e) {
			var $anchor = $(e.currentTarget);

			var collectibleModel = new Backbone.Model(JSON.parse($anchor.attr('data-collectible')));
			var collectibleUserData = JSON.parse($anchor.attr('data-collectible-user'));

			var collectibleUserModel = new CollectibleUserModel(collectibleUserData);

			$anchor.stashsell(collectibleModel, collectibleUserModel);
			e.preventDefault();
		});
	});
}(window.jQuery);
! function($) {"use strict";// jshint ;_;

	/* PUBLIC CLASS DEFINITION
	 ** ============================== */

	var SaleRemove = function(element, options) {
		this.$element = $(element);
		this.$stashItem = this.$element.closest('.stash-item');
		this.options = $.extend({}, $.fn.saleremove.defaults, options);
		this.collectibleUserId = this.$element.attr('data-collectible-user-id');
		if (options.tiles) {
			this.$tiles = $('.tiles');
		}
		this.collectibleUser = options.collectibleUserModel;
	};

	SaleRemove.prototype.remove = function() {
		var self = this;
		this.collectibleUser.set({
			sale : false
		});

		this.collectibleUser.save({}, {
			success : function(model, response, options) {
				var message = 'The collectible has been removed from your sale/trade list!';
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
			},
			error : function(model, xhr, options) {
				var errorMessage = 'Oops! Something went terribly wrong!';
				if (xhr.status === 400) {
					$.each(xhr.responseJSON.response.errors, function(index, value) {
						errorMessage = value.message;
					});
				} else if (xhr.status === 401) {
					errorMessage = 'You are not authorized to do that!';
				}

				$.blockUI({
					message : '<button class="close" data-dismiss="alert" type="button">×</button>' + errorMessage,
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
	};
	/* BUTTON PLUGIN DEFINITION
	 * ======================== */

	$.fn.saleremove = function(options) {
		return this.each(function() {
			var $this = $(this);

			var data = $this.data('saleremove');
			if (!data) {
				$this.data('saleremove', ( data = new SaleRemove(this, options)));
			}

			data.remove();
		});
	};

	$.fn.saleremove.defaults = {

	};

	$.fn.saleremove.Constructor = SaleRemove;

	/* DATA-API
	 * =============== */

	$(function() {
		var tile = false;
		if ($('.stashable').hasClass('tiles')) {
			tile = true;
		}

		$('.stashable').on('click', '.stash-remove-listing', function(e) {
			var $anchor = $(e.currentTarget);
			var collectibleModel = new Backbone.Model(JSON.parse($anchor.attr('data-collectible')));
			var collectibleUserModel = new CollectibleUserModel(JSON.parse($anchor.attr('data-collectible-user')));
			var collectibleUserId = $anchor.attr('data-collectible-user-id');
			var $stashItem = $anchor.closest('.stash-item');
			$anchor.saleremove({
				$stashItem : $stashItem,
				tiles : tile,
				collectibleModel : collectibleModel,
				collectibleUserModel : collectibleUserModel,
				collectibleUserId : collectibleUserId
			});
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
	var StashMarkSold = function() {

	};

	StashMarkSold.prototype.initialize = function() {
		dust.loadSource(dust.compile($('#template-stash-remove').html(), 'stash.remove'));
		var self = this;
		this.stashRemoveView = null;
		this.collectibleUser = null;

		$('#stash-remove-sold-dialog', 'body').on('hidden', function() {
			self.stashRemoveView.remove();
		});

		$('#stash-remove-sold-dialog').on('click', '.save', function() {
			var $button = $(this);
			$button.button('loading');
			// TODO: The business logic here should know if this is an update or destroy
			// if we are removing because it is sold, it should be an update, we are not actually
			// remove the model.
			self.collectibleUser.destroy({
				url : self.collectibleUser.url('delete'),
				success : function(model, response, options) {
					$button.button('reset');
					if (response.response.isSuccess) {
						$('#stash-remove-sold-dialog').modal('hide');
						var message = 'You have successfully removed the collectible from your stash!';

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

						// checking for array here is pretty dumb
						if (response.response.data && !_.isArray(response.response.data) && self.historyView) {
							// should update the view to indicate the values that were set
							self.$stashItem.find('.bought-sold-icon').html('<i class="icon-minus"></i>');
						} else {
							if (self.redirect) {
								window.location.href = self.redirect;
							} else if (self.$tiles) {
								self.$tiles.masonry('remove', self.$stashItem);
								self.$tiles.masonry('reload');
							} else {
								self.$stashItem.remove();
							}
						}

					} else {
						if (response.response.errors) {
							self.stashRemoveView.errors = response.response.errors;
							self.stashRemoveView.render();
						}
					}
				},
				error : function(model, xhr, options) {
					$button.button('reset');
				}
			});

		});

	};

	StashMarkSold.prototype.remove = function(options) {
		if (options.tiles) {
			this.$tiles = $('.tiles');
		}

		this.$stashItem = options.$stashItem;
		this.collectibleUser = options.collectibleUserModel;

		if (this.stashRemoveView) {
			this.stashRemoveView.remove();
			delete this.stashRemoveView;
		}

		this.collectibleUser.set({
			collectible_user_remove_reason_id : options.listingModel.get('collectible_user_remove_reason_id')
		});

		this.stashRemoveView = new StashRemoveView({
			model : this.collectibleUser,
			collectible : options.collectibleModel,
			reasons : options.reasons,
			changeReason : false
		});

		$('.modal-body', '#stash-remove-sold-dialog').html(this.stashRemoveView.render().el);

		$('#stash-remove-sold-dialog').modal();
	};

	/* BUTTON PLUGIN DEFINITION
	 * ======================== */

	$.fn.stashmarksold = function(options) {
		return this.each(function() {
			var $this = $(this);
			stashMarkSold.remove(options);

		});
	};

	$.fn.stashmarksold.defaults = {

	};

	var stashMarkSold = new StashMarkSold();

	/* DATA-API
	 * =============== */

	$(function() {
		stashMarkSold.initialize();
		var reasonsCollection = null;
		if ( typeof reasons !== 'undefined') {
			reasonsCollection = new Backbone.Collection(JSON.parse(reasons));
		} else {
			reasonsCollection = new Backbone.Collection();
		}

		var tile = false;
		if ($('.stashable').hasClass('tiles')) {
			tile = true;
		}

		$('.stashable').on('click', '.stash-mark-as-sold', function(e) {
			var $anchor = $(e.currentTarget);

			var historyView = $anchor.closest('.stashable').attr('data-history');

			var collectibleModel = new Backbone.Model(JSON.parse($anchor.attr('data-collectible')));
			var collectibleUserModel = new CollectibleUserModel(JSON.parse($anchor.attr('data-collectible-user')));
			var listingModel = new Backbone.Model(JSON.parse($anchor.attr('data-listing')));
			var collectibleUserId = $anchor.attr('data-collectible-user-id');
			var $stashItem = $anchor.closest('.stash-item');
			var redirect = $anchor.attr('data-remove-redirect');
			$anchor.stashmarksold({
				$stashItem : $stashItem,
				tiles : tile,
				collectibleModel : collectibleModel,
				collectibleUserModel : collectibleUserModel,
				listingModel : listingModel,
				reasons : reasonsCollection,
				collectibleUserId : collectibleUserId
			});
			e.preventDefault();
		});
	});
}(window.jQuery);
