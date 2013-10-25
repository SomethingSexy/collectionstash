var CollectibleModel = Backbone.Model.extend({});
var EditModel = Backbone.Model.extend({});

var PaginatedCollection = Backbone.Paginator.requestPager.extend({
	model : CollectibleModel,
	paginator_core : {
		// the type of the request (GET by default)
		type : 'GET',

		// the type of reply (jsonp by default)
		dataType : 'json',

		// the URL (or base URL) for the service
		url : function() {
			var url = '/collectibles/userHistory/false/' + userId + '/page:' + this.currentPage;

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
		totalPages : totalSubmissionPages,
		total : totalSubmission
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

var PaginatedEdits = Backbone.Paginator.requestPager.extend({
	model : EditModel,
	paginator_core : {
		// the type of the request (GET by default)
		type : 'GET',

		// the type of reply (jsonp by default)
		dataType : 'json',

		// the URL (or base URL) for the service
		url : function() {
			var url = '/edits/userHistory/' + userId + '/page:' + this.currentPage;

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
		totalPages : totalEditPages,
		total : totalEdit
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

var EditsView = Backbone.View.extend({
	template : 'edits',
	tagName : 'div',
	className : 'table-responsive',
	initialize : function() {
		this.collection.on('change', this.render, this);
		this.collection.on('reset', this.render, this);
	},
	render : function() {
		var self = this;
		var data = {

		};

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		this.collection.each(function(model) {
			$('tbody', self.el).append(new EditView({
				model : model
			}).render().el);
		});

		return this;
	}
});

var CollectiblesView = Backbone.View.extend({
	template : 'collectibles',
	tagName : 'div',
	className : 'table-responsive',
	initialize : function() {
		this.collection.on('change', this.render, this);
		this.collection.on('reset', this.render, this);
	},
	render : function() {
		var self = this;
		var data = {

		};

		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		this.collection.each(function(model) {
			$('tbody', self.el).append(new CollectibleView({
				model : model
			}).render().el);
		});

		return this;
	}
});

var CollectibleView = Backbone.View.extend({
	tagName : 'tr',
	template : 'collectible',
	events : {
		'click' : 'selectCollectible'
	},
	render : function() {
		var self = this;
		var data = this.model.toJSON();
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

var EditView = Backbone.View.extend({
	tagName : 'tr',
	template : 'edit',
	render : function() {
		var self = this;
		var data = this.model.toJSON();
		dust.render(this.template, data, function(error, output) {
			$(self.el).html(output);
		});

		return this;
	}
});
//

$(function() {

	$.when($.get('/templates/user/collectible.dust'), $.get('/templates/user/collectibles.dust'), $.get('/templates/user/edit.dust'), $.get('/templates/user/edits.dust'), $.get('/templates/common/paging.dust')).done(function(collectibleTemplate, collectiblesTemplate, editTemplate, editsTemplate, pagingTemplate) {
		dust.loadSource(dust.compile(collectibleTemplate[0], 'collectible'));
		dust.loadSource(dust.compile(collectiblesTemplate[0], 'collectibles'));
		dust.loadSource(dust.compile(editTemplate[0], 'edit'));
		dust.loadSource(dust.compile(editsTemplate[0], 'edits'));
		dust.loadSource(dust.compile(pagingTemplate[0], 'paging'));

		$('.submissions .panel-heading').after(new CollectiblesView({
			collection : submissions
		}).render().el);

		$('.submissions .panel-footer').append(new PagingView({
			collection : submissions
		}).render().el);

		$('.edits .panel-heading').after(new EditsView({
			collection : edits
		}).render().el);

		$('.edits .panel-footer').append(new PagingView({
			collection : edits
		}).render().el);

	});

});
