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
				// TODO: Need to handle searches and tags
				this.selectedFilters = {};

				this.element.find('.filter').each(function() {
					var type = $(this).attr('data-type');
					if (!self.selectedFilters.hasOwnProperty(type)) {
						self.selectedFilters[type] = [];
					}

					$(this).children('ul').children('li').children('label').children('.filter-links').each(function() {
						if ($(this).is(':checked')) {
							self.selectedFilters[type].push($(this).attr('data-filter'));
						}
					});
				});

				//This is for clicking a specific filter
				this.element.children('.filter').children('ul').children('li').children('label').children('.filter-links').click(function(event) {
					var selectedType = $(this).closest('.filter').attr('data-type');
					var allowMulitple = $(this).closest('.filter').attr('data-allow-multiple');
					var selectedFilter = $(this).attr('data-filter');

					if (!self.selectedFilters.hasOwnProperty(selectedType)) {
						self.selectedFilters[selectedType] = [];
					}

					if ($(this).is(':checked')) {
						if (allowMulitple === 'false') {
							self.selectedFilters[selectedType] = [];
						}
						self.selectedFilters[selectedType].push(selectedFilter);
					} else {
						// remove it
						self.selectedFilters[selectedType].splice($.inArray(selectedFilter, self.selectedFilters[selectedType]), 1);
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
