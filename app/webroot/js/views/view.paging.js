var PagingView = Backbone.View.extend({
	template : 'paging',
	className : 'paging',
	events : {
		'click a.page' : 'gotoPage',
		'click a.sort' : 'sort',
		'click a.next' : 'next',
		'click a.previous' : 'previous'
	},
	initialize : function() {
		this.collection.on('change', this.render, this);
		this.collection.on('reset', this.render, this);
		this.pagesArray = [];
		// ya fuck you dust
		for (var i = 1; i <= this.collection.paginator_ui.totalPages; i++) {
			this.pagesArray.push(i);
		}
	},
	render : function() {
		var self = this;
		var data = {
			pages : this.pagesArray
		};

		if (this.collection.currentPage) {
			data['paginator'] = {
				currentPage : this.collection.currentPage,
				firstPage : this.collection.firstPage,
				perPage : this.collection.perPage,
				totalPages : this.collection.totalPages,
				total : this.collection.paginator_ui.total
			};
		} else {
			data['paginator'] = this.collection.paginator_ui;
		}

		data.sort = {
			sort : this.collection.selectedSort,
			direction : this.collection.sortDirection
		};

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	},
	gotoPage : function(e) {
		e.preventDefault();
		var page = $(e.target).text();
		this.collection.goTo(page);
	},
	sort : function(e) {
		e.preventDefault();
		var direction = 'asc';
		if ($(e.target).attr('data-direction')) {
			if ($(e.target).attr('data-direction') === 'asc') {
				$(e.target).attr('data-direction', 'desc');
				direction = 'desc';
			} else {
				$(e.target).attr('data-direction', 'asc');
			}
		} else {
			$(e.target).attr('data-direction', 'asc');
		}

		this.collection.selectedSort = $(e.target).attr('data-sort');
		this.collection.sortDirection = direction;
		this.collection.fetch();
	},
	next : function(e) {
		e.preventDefault();
		if ( typeof this.collection.currentPage === 'undefined') {
			this.collection.currentPage = 1;
		}
		this.collection.requestNextPage();
	},
	previous : function(e) {
		e.preventDefault();
		this.collection.requestPreviousPage();
	}
});