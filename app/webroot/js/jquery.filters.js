( function($) {

		$.widget("cs.filters", {

			// These options will be used as defaults

			options : {
				// Need to add configuration to indicate which filters might
				// reset others
				// Or if we only allow one per
			},

			// Set up the widget
			_create : function() {
				//Do some initial setup
				var self = this;

				// First onload we need to see if any of the filters are selected
				this.selectedFilters = {};

				this.element.find('.filter').each(function() {
					var type = $(this).attr('data-type');
					if (!self.selectedFilters.hasOwnProperty(type)) {
						self.selectedFilters[type] = [];
					}

					$(this).children('.filter-list-container').children('.filter-list').children('ol').children('li').each(function() {
						if ($(this).hasClass('selected')) {
							self.selectedFilters[type].push($(this).children('a').attr('data-filter'));
						}
					});
				});

				//This is for clicking and opening up the filter box
				this.element.find('.filter').not('.lock').click(function(e) {
					if ($(e.target).hasClass('ui-icon-close')) {
						var selectedType = $(e.target).closest('.filter').attr('data-type');

						if (!self.selectedFilters.hasOwnProperty(selectedType)) {
							self.selectedFilters[selectedType] = [];
						} else {
							self.selectedFilters[selectedType] = [];
						}

						var queryString = self._buildQueryString();

						window.location.href = searchUrl + "?" + queryString;

					} else {
						$('#filters').children('.filter').children('.filter-list-container').hide();
						var $node = $(e.target);
						if ($(e.target).hasClass('name') || $(e.target).hasClass('ui-icon')) {
							$node = $(e.target).parent('.filter-name').parent('.filter');
						}

						$node.find('.filter-list-container').show();
					}
				});

				//This is for clicking anywhere else but the filter box and closing them
				$('body').bind('click', function(e) {
					if (!$(e.target).parent().is('.filter-name') && !$(e.target).is('div.filter') && !$(e.target).is('.filter-list-container') && !$(e.target).is('.filter-list') && !$(e.target).is('ol', '.filter-list') && !$(e.target).is('li', '.filter-list ol')) {
						$('#filters').children('.filter').children('.filter-list-container').hide();
					}
				});

				//This is for clicking a specific filter
				this.element.children('.filter').children('.filter-list-container').children('.filter-list').children('ol').children('li').children('.filter-links').click(function() {
					var selectedType = $(this).closest('.filter').attr('data-type');
					var selectedFilter = $(this).attr('data-filter');

					if (!self.selectedFilters.hasOwnProperty(selectedType)) {
						self.selectedFilters[selectedType] = [];
					}

					if ($(this).parent().hasClass('selected')) {
						// remove it
						self.selectedFilters[selectedType].splice($.inArray(selectedFilter, self.selectedFilters[selectedType]), 1);
					} else {
						self.selectedFilters[selectedType].push(selectedFilter);
					}

					// This will allow for multiples
					var queryString = self._buildQueryString();

					window.location.href = searchUrl + "?" + queryString;

				});

			},
			/**
			 *
			 */
			_buildQueryString : function() {
				var queryString = '';

				$.each(this.selectedFilters, function(index, filterGroup) {
					if (filterGroup.length > 0) {
						var length = filterGroup.length;
						var filterQuery = index + '=';
						$.each(filterGroup, function(index, filter) {
							filterQuery += filter;

							if (index !== length - 1) {
								filterQuery += ',';
							}
						});
						queryString += '&' + filterQuery;
					}
				});

				return queryString;

			},
			// Use the destroy method to clean up any modifications your widget has made to the DOM

			destroy : function() {

				// In jQuery UI 1.8, you must invoke the destroy method from the base widget

				$.Widget.prototype.destroy.call(this);
				// In jQuery UI 1.9 and above, you would define _destroy instead of destroy and not call the base method

			}
		});

	}(jQuery) );
