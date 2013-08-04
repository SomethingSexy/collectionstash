var PaginatedNotifications = Backbone.Paginator.requestPager.extend({
	//model : CollectibleModel,
	paginator_core : {
		// the type of the request (GET by default)
		type : 'GET',

		// the type of reply (jsonp by default)
		dataType : 'json',

		// the URL (or base URL) for the service
		url : function() {
			var url = '/notifications/notifications/page:' + this.currentPage;

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
		totalPages : totalNotificationPages,
		total : totalNotifications
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

var NotificationsView = Backbone.View.extend({
	template : 'notifications',
	events : {
		'click a.page' : 'gotoPage',
		//'click a.sort' : 'sort',
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
			}
		} else {
			data['paginator'] = this.collection.paginator_ui;
		}

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		this.renderNotifications();

		return this;
	},
	renderNotifications : function() {
		var self = this;
		if (this.collection.size() > 0) {
			this.collection.each(function(model) {
				$('.messages', self.el).append(new NotificationView({
					model : model
				}).render().el);
			});
		} else {
			$('.messages', this.el).html('<p>There are no new notifications.</p>');
		}
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
				direction = 'desc'
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

var NotificationView = Backbone.View.extend({
	className : 'message',
	template : 'notification',
	render : function() {
		var self = this;
		var data = this.model.toJSON();
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	}
});

$(function() {

	$.when($.get('/templates/notifications/notifications.dust'), $.get('/templates/notifications/notification.dust')).done(function(notificationsTemplate, notificationTemplate) {
		dust.loadSource(dust.compile(notificationsTemplate[0], 'notifications'));
		dust.loadSource(dust.compile(notificationTemplate[0], 'notification'));

		$('.widget-content').append(new NotificationsView({
			collection : notifications
		}).render().el);
	});

});
