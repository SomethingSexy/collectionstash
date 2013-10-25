var CollectibleModel = Backbone.Model.extend({});
var EditModel = Backbone.Model.extend({});

var PaginatedActivityCollection = Backbone.Paginator.requestPager.extend({
	paginator_core : {
		// the type of the request (GET by default)
		type : 'GET',

		// the type of reply (jsonp by default)
		dataType : 'json',

		// the URL (or base URL) for the service
		url : function() {
			var url = '/activities/index/page:' + this.currentPage;

			return url;
		}
	},
	paginator_ui : {
		// the lowest page index your API allows to be accessed
		firstPage : 1,

		// which page should the paginator start from
		// (also, the actual page the paginator is on)
		currentPage : 1,

		// how many items per page should be shown
		perPage : 10,

		// a default number of total pages to query in case the API or
		// service you are using does not support providing the total
		// number of pages for us.
		// 10 as a default in case your service doesn't return the total
		totalPages : totalActivityPages,
		total : totalActivity
	},
	server_api : {

		// how many results the request should skip ahead to
		// customize as needed. For the Netflix API, skipping ahead based on
		// page * number of results per page was necessary.
		'page' : function() {
			return this.currentPage;
		}
	},
	parse : function(response) {
		// Be sure to change this based on how your results
		// are structured (e.g d.results is Netflix specific)
		var tags = response.results;
		//Normally this.totalPages would equal response.d.__count
		//but as this particular NetFlix request only returns a
		//total count of items for the search, we divide.
		this.totalPages = response.metadata.paging.pageCount;
		return tags;
	}
});

var PaginatedWorkCollection = Backbone.Paginator.requestPager.extend({
	model : CollectibleModel,
	paginator_core : {
		// the type of the request (GET by default)
		type : 'GET',

		// the type of reply (jsonp by default)
		dataType : 'json',

		// the URL (or base URL) for the service
		url : function() {
			var url = '/collectibles/userHistory/true/' + userId + '/page:' + this.currentPage;

			if (this.selectedSort) {
				url = url + '/sort:' + this.selectedSort + '/direction:' + this.sortDirection;
			}

			return url;
		}
	},
	paginator_ui : {
		// the lowest page index your API allows to be accessed
		firstPage : 1,

		// which page should the paginator start from
		// (also, the actual page the paginator is on)
		currentPage : 1,

		// how many items per page should be shown
		perPage : 10,

		// a default number of total pages to query in case the API or
		// service you are using does not support providing the total
		// number of pages for us.
		// 10 as a default in case your service doesn't return the total
		totalPages : totalWorkPages,
		total : totalWorks
	},
	server_api : {

		// how many results the request should skip ahead to
		// customize as needed. For the Netflix API, skipping ahead based on
		// page * number of results per page was necessary.
		'page' : function() {
			return this.currentPage;
		}
	},
	parse : function(response) {
		// Be sure to change this based on how your results
		// are structured (e.g d.results is Netflix specific)
		var tags = response.results;
		//Normally this.totalPages would equal response.d.__count
		//but as this particular NetFlix request only returns a
		//total count of items for the search, we divide.
		this.totalPages = response.metadata.paging.pageCount;
		return tags;
	}
});

var PaginatedPending = Backbone.Paginator.requestPager.extend({
	model : CollectibleModel,
	paginator_core : {
		// the type of the request (GET by default)
		type : 'GET',

		// the type of reply (jsonp by default)
		dataType : 'json',

		// the URL (or base URL) for the service
		url : function() {
			var url = '/collectibles/pending/page:' + this.currentPage;

			return url;
		}
	},
	paginator_ui : {
		// the lowest page index your API allows to be accessed
		firstPage : 1,

		// which page should the paginator start from
		// (also, the actual page the paginator is on)
		currentPage : 1,

		// how many items per page should be shown
		perPage : 10,

		// a default number of total pages to query in case the API or
		// service you are using does not support providing the total
		// number of pages for us.
		// 10 as a default in case your service doesn't return the total
		totalPages : totalPendingPages,
		total : totalPending
	},
	server_api : {

		// how many results the request should skip ahead to
		// customize as needed. For the Netflix API, skipping ahead based on
		// page * number of results per page was necessary.
		'page' : function() {
			return this.currentPage;
		}
	},
	parse : function(response) {
		// Be sure to change this based on how your results
		// are structured (e.g d.results is Netflix specific)
		var tags = response.results;
		//Normally this.totalPages would equal response.d.__count
		//but as this particular NetFlix request only returns a
		//total count of items for the search, we divide.
		this.totalPages = response.metadata.paging.pageCount;
		return tags;
	}
});

var PaginatedNew = Backbone.Paginator.requestPager.extend({
	model : CollectibleModel,
	paginator_core : {
		// the type of the request (GET by default)
		type : 'GET',

		// the type of reply (jsonp by default)
		dataType : 'json',

		// the URL (or base URL) for the service
		url : function() {
			var url = '/collectibles/newCollectibles/page:' + this.currentPage;

			return url;
		}
	},
	paginator_ui : {
		// the lowest page index your API allows to be accessed
		firstPage : 1,

		// which page should the paginator start from
		// (also, the actual page the paginator is on)
		currentPage : 1,

		// how many items per page should be shown
		perPage : 5,

		// a default number of total pages to query in case the API or
		// service you are using does not support providing the total
		// number of pages for us.
		// 10 as a default in case your service doesn't return the total
		totalPages : totalNewPages,
		total : totalNew
	},
	server_api : {

		// how many results the request should skip ahead to
		// customize as needed. For the Netflix API, skipping ahead based on
		// page * number of results per page was necessary.
		'page' : function() {
			return this.currentPage;
		}
	},
	parse : function(response) {
		// Be sure to change this based on how your results
		// are structured (e.g d.results is Netflix specific)
		var tags = response.results;
		//Normally this.totalPages would equal response.d.__count
		//but as this particular NetFlix request only returns a
		//total count of items for the search, we divide.
		this.totalPages = response.metadata.paging.pageCount;
		return tags;
	}
});

var NewView = Backbone.View.extend({
	template : 'new',
	className : 'carousel',
	events : {
		'click a.next' : 'next',
		'click a.previous' : 'previous'
	},
	initialize : function() {
		this.collection.on('change', this.render, this);
		this.collection.on('reset', this.render, this);
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

		if (this.collection.size() > 0) {
			this.collection.each(function(model) {
				$('ul', self.el).append(new NewCollectibleView({
					model : model
				}).render().el);
			});
		} else {
			$('ul', this.el).html('<li>There are no new collectibles.</li>');
		}

		return this;
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

var NewCollectibleView = Backbone.View.extend({
	tagName : 'li',
	className : 'col-md-2',
	template : 'new.collectible',
	render : function() {
		var self = this;
		var data = this.model.toJSON();
		data.uploadDirectory = uploadDirectory;
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	}
});
var PendingView = Backbone.View.extend({
	template : 'pending',
	className : 'carousel',
	events : {
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

		if (this.collection.size() > 0) {
			this.collection.each(function(model) {
				$('ul', self.el).append(new PendingCollectibleView({
					model : model
				}).render().el);
			});
		} else {
			$('ul', this.el).html('<li>There are no pending collectibles.</li>');
		}

		return this;
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

var PendingCollectibleView = Backbone.View.extend({
	tagName : 'li',
	className : 'col-md-2',
	template : 'pending.collectible',
	render : function() {
		var self = this;
		var data = this.model.toJSON();
		data.uploadDirectory = uploadDirectory;
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	}
});

var WorksView = Backbone.View.extend({
	template : 'works',
	tagName : 'div',
	className : 'table-responsive',
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
		var data = {};
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		if (!this.collection.isEmpty()) {
			this.collection.each(function(model) {
				$('tbody', self.el).append(new WorkView({
					model : model
				}).render().el);
			});

		} else {
			$('thead', self.el).empty();
			$('tbody', self.el).html('<td>You aren\'t working on anything</td>');
			$('.paging', self.el).remove();
		}

		return this;
	}
});

var WorkView = Backbone.View.extend({
	tagName : 'tr',
	template : 'work',
	events : {
		'click' : 'selectCollectible'
	},
	render : function() {
		var self = this;
		var data = this.model.toJSON();
		data.type = 'Mass-produced';
		if (data.Collectible.original) {
			data.type = 'Original';
		} else if (data.Collectible.custom) {
			data.type = 'Custom';
		} else {

		}

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	},
	selectCollectible : function(event) {
		event.preventDefault();
		var collectible = this.model.toJSON();
		if (collectible.Status.id == 1) {
			window.location.href = '/collectibles/edit/' + collectible.Collectible.id;
		} else if (collectible.Status.id == 2) {
			window.location.href = '/collectibles/view/' + collectible.Collectible.id;
		} else {
			window.location.href = '/collectibles/view/' + collectible.Collectible.id;
		}
	}
});

var ActivitiesView = Backbone.View.extend({
	template : 'activities',
	events : {
		'click .load' : 'next'
	},
	initialize : function() {
		this.collection.on('change', this.renderActivities, this);
		this.collection.on('reset', this.renderActivities, this);
	},
	render : function() {
		var self = this;
		var data = {};

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		if (!this.collection.isEmpty()) {
			this.collection.each(function(activity) {
				$('.activities', self.el).append(new ActivityView({
					model : activity
				}).render().el);
			});
		} else {
			//todo empty view
		}

		return this;
	},
	renderActivities : function() {
		if (!this.collection.isEmpty()) {
			this.collection.each(function(activity) {
				$('.activities', self.el).append(new ActivityView({
					model : activity
				}).render().el);
			});
		}
		$('.btn.load', this.el).button('reset');
		var hideButton = false;
		if (this.collection.currentPage) {
			if (this.collection.currentPage === this.collection.totalPages) {
				hideButton = true;
			}
		} else {
			if (this.collection.paginator_ui.currentPage === this.collection.paginator_ui.totalPages) {
				hideButton = true;
			}
		}

		if (hideButton) {
			$('.btn.load', this.el).hide();
		}

	},
	next : function(e) {
		e.preventDefault();
		if ( typeof this.collection.currentPage === 'undefined') {
			this.collection.currentPage = 1;
		}
		this.collection.requestNextPage();

		$('.btn.load', this.el).button('loading');
	},
});

$(function() {

	$.when($.get('/templates/user/pending.dust'), $.get('/templates/user/pending.collectible.dust'), $.get('/templates/user/new.collectibles.dust'), $.get('/templates/user/new.collectible.dust'), $.get('/templates/user/works.dust'), $.get('/templates/user/work.dust'), $.get('/templates/user/activities.dust'), $.get('/templates/activities/activity.dust'), $.get('/templates/common/paging.dust')).done(function(pendingTemplate, pendingCollectibleTemplate, newTemplate, newCollectibleTemplate, worksTemplate, workTemplate, acitivitesTemplate, activityTemplate, pagingTemplate) {
		dust.loadSource(dust.compile(pendingTemplate[0], 'pending'));
		dust.loadSource(dust.compile(pendingCollectibleTemplate[0], 'pending.collectible'));
		dust.loadSource(dust.compile(newTemplate[0], 'new'));
		dust.loadSource(dust.compile(newCollectibleTemplate[0], 'new.collectible'));
		dust.loadSource(dust.compile(worksTemplate[0], 'works'));
		dust.loadSource(dust.compile(workTemplate[0], 'work'));
		dust.loadSource(dust.compile(acitivitesTemplate[0], 'activities'));
		dust.loadSource(dust.compile(activityTemplate[0], 'activity'));
		dust.loadSource(dust.compile(pagingTemplate[0], 'paging'));

		$('.pending').append(new PendingView({
			collection : pending
		}).render().el);

		$('.new').append(new NewView({
			collection : newCollectibles
		}).render().el);

		$('.work .panel-heading').after(new WorksView({
			collection : works
		}).render().el);

		$('.work .panel-footer').append(new PagingView({
			collection : works
		}).render().el);

		$('.activities-container').append(new ActivitiesView({
			collection : activity
		}).render().el);

	});

});
