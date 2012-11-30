! function($) {"use strict";// jshint ;_;

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
							'background-color' : '#DFF0D8',
							border : '1px solid #D6E9C6',
							'border-radius' : '4px 4px 4px 4px',
							color : '#468847',
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
										'background-color' : '#EED3D7',
										border : '1px solid #D6E9C6',
										'border-radius' : '4px 4px 4px 4px',
										color : '#B94A48',
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

	var StashRemove = function(element, options) {
		this.$element = $(element);
		this.$stashItem = this.$element.closest('.stash-item');
		this.options = $.extend({}, $.fn.stashadd.defaults, options);
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
							'background-color' : '#DFF0D8',
							border : '1px solid #D6E9C6',
							'border-radius' : '4px 4px 4px 4px',
							color : '#468847',
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
										'background-color' : '#EED3D7',
										border : '1px solid #D6E9C6',
										'border-radius' : '4px 4px 4px 4px',
										color : '#B94A48',
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
			var $this = $(this), data = $this.data('stashremove');
			if (!data) {
				$this.data('stashremove', ( data = new StashRemove(this, options)));
			}

			data.remove();

		});
	}

	$.fn.stashremove.defaults = {

	}

	$.fn.stashremove.Constructor = StashRemove

	/* DATA-API
	 * =============== */

	$(function() {
		var tile = false;
		if ($('.stashable').hasClass('tiles')) {
			tile = true;
		}

		$('.stashable').on('click', 'a.remove-from-stash', function(e) {
			var $anchor = $(e.currentTarget)
			$anchor.stashremove({
				tiles : tile
			});
			e.preventDefault();
		});
	});
}(window.jQuery);

